<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Domains\Core\Models\Career;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;

class MyCareerPath extends Page implements HasForms
{
    use InteractsWithForms;
    use InteractsWithFormActions;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static string $view = 'filament.app.pages.my-career-path';

    protected static ?string $navigationLabel = 'مسیر شغلی من';

    protected static ?string $title = 'مسیر شغلی من';

    public ?array $data = [];

    public function mount(): void
    {
        $user = auth()->user();
        $this->form->fill([
            'selected_career_id' => $user->selected_career_id,
        ]);
    }

    public function getSelectedCareerProperty()
    {
        $user = auth()->user();
        if (!$user->relationLoaded('selectedCareer')) {
            $user->load('selectedCareer');
        }
        return $user->selectedCareer;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('انتخاب مسیر شغلی')
                    ->description('مسیر حرفه‌ای خود را انتخاب کنید')
                    ->schema([
                        Forms\Components\Select::make('selected_career_id')
                            ->label('مسیر شغلی')
                            ->options(
                                Career::query()
                                    ->where('is_active', true)
                                    ->whereNotNull('published_at')
                                    ->where('published_at', '<=', now())
                                    ->orderBy('title')
                                    ->pluck('title', 'id')
                            )
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->placeholder('یک مسیر شغلی انتخاب کنید')
                            ->helperText('می‌توانید مسیر شغلی مورد نظر خود را از لیست انتخاب کنید'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $user = auth()->user();

        $user->update([
            'selected_career_id' => $data['selected_career_id'] ?? null,
        ]);

        if ($data['selected_career_id']) {
            $career = Career::find($data['selected_career_id']);
            Notification::make()
                ->title('مسیر شغلی با موفقیت انتخاب شد')
                ->body("مسیر شغلی \"{$career->title}\" انتخاب شد.")
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('مسیر شغلی حذف شد')
                ->success()
                ->send();
        }
    }

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('save')
                ->label('ذخیره')
                ->color('primary')
                ->submit('save'),
        ];
    }

    protected function hasFullWidthFormActions(): bool
    {
        return false;
    }
}

