<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\RedirectResource\Pages;

use App\Filament\Admin\Resources\RedirectResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use Filament\Notifications\Notification;

class ListRedirects extends ListRecords
{
    protected static string $resource = RedirectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('import_csv')
                ->label('وارد کردن CSV')
                ->icon('heroicon-o-arrow-up-tray')
                ->form([
                    \Filament\Forms\Components\FileUpload::make('file')
                        ->label('فایل CSV')
                        ->required()
                        ->acceptedFileTypes(['text/csv', 'text/plain'])
                        ->disk('local')
                        ->directory('temp'),
                ])
                ->action(function (array $data) {
                    $filePath = Storage::disk('local')->path($data['file']);
                    $handle = fopen($filePath, 'r');
                    
                    if ($handle === false) {
                        Notification::make()
                            ->title('خطا در خواندن فایل')
                            ->danger()
                            ->send();
                        return;
                    }

                    $imported = 0;
                    $skipped = 0;
                    
                    // Skip header row
                    fgetcsv($handle);
                    
                    while (($row = fgetcsv($handle)) !== false) {
                        if (count($row) < 2) {
                            $skipped++;
                            continue;
                        }

                        $sourceUrl = trim($row[0]);
                        $targetUrl = trim($row[1]);
                        $statusCode = isset($row[2]) ? (int) trim($row[2]) : 301;

                        if (empty($sourceUrl) || empty($targetUrl)) {
                            $skipped++;
                            continue;
                        }

                        try {
                            \App\Domains\Core\Models\Redirect::updateOrCreate(
                                ['source_url' => $sourceUrl],
                                [
                                    'target_url' => $targetUrl,
                                    'status_code' => $statusCode,
                                ]
                            );
                            $imported++;
                        } catch (\Exception $e) {
                            $skipped++;
                        }
                    }

                    fclose($handle);
                    Storage::disk('local')->delete($data['file']);

                    Notification::make()
                        ->title("وارد شد: {$imported} | رد شد: {$skipped}")
                        ->success()
                        ->send();
                }),
        ];
    }
}