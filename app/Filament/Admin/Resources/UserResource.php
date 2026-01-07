<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Domains\Auth\Models\User;
use App\Enums\UserRole;
use App\Filament\Admin\Resources\UserResource\Pages\CreateUser;
use App\Filament\Admin\Resources\UserResource\Pages\EditUser;
use App\Filament\Admin\Resources\UserResource\Pages\ListUsers;
use App\Filament\Admin\Resources\UserResource\Pages\ViewUser;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'کاربران';

    protected static ?string $modelLabel = 'کاربر';

    protected static ?string $pluralModelLabel = 'کاربران';

    protected static ?string $navigationGroup = 'کاربران';

    protected static ?int $navigationSort = 1;

    /**
     * @return array<string, string>
     */
    private static function roleOptions(): array
    {
        return collect(UserRole::cases())
            ->mapWithKeys(fn (UserRole $case) => [$case->value => $case->value])
            ->all();
    }

    public static function form(Form $form): Form
    {
        $roleOptions = self::roleOptions();

        return $form
            ->schema([
                Forms\Components\Section::make('اطلاعات کاربر')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('نام')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->label('ایمیل')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\TextInput::make('mobile')
                            ->label('موبایل')
                            ->tel()
                            ->unique(ignoreRecord: true)
                            ->maxLength(20),

                        Forms\Components\Select::make('role')
                            ->label('نقش')
                            ->options($roleOptions)
                            ->required()
                            ->native(false)
                            ->default(UserRole::Student->value),

                        Forms\Components\Textarea::make('bio')
                            ->label('بیوگرافی')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('password')
                            ->label('رمز عبور')
                            ->password()
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? Hash::make($state) : null)
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->minLength(8)
                            ->maxLength(255)
                            ->helperText('برای ویرایش، اگر خالی بماند تغییر نمی‌کند.'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        $roleOptions = self::roleOptions();

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('نام')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('ایمیل')
                    ->searchable(),

                Tables\Columns\TextColumn::make('mobile')
                    ->label('موبایل')
                    ->searchable(),

                Tables\Columns\TextColumn::make('role')
                    ->label('نقش')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof UserRole ? $state->value : (string) $state)
                    ->color(function ($state): string {
                        $value = $state instanceof UserRole ? $state->value : (string) $state;

                        return match ($value) {
                            UserRole::Admin->value => 'danger',
                            UserRole::Teacher->value => 'warning',
                            UserRole::Student->value => 'success',
                            UserRole::Customer->value => 'gray',
                            default => 'gray',
                        };
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('ایجاد')
                    ->dateTime('Y/m/d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('نقش')
                    ->options($roleOptions),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'view' => ViewUser::route('/{record}'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }

    public static function canDelete($record): bool
    {
        return false;
    }
}
