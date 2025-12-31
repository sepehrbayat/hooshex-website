<?php

declare(strict_types=1);

namespace App\Livewire\Curator;

use Awcodes\Curator\Components\Modals\CuratorPanel as BaseCuratorPanel;
use Awcodes\Curator\Components\Forms\Uploader;
use Awcodes\Curator\CuratorPlugin;
use Awcodes\Curator\Models\Media;
use Awcodes\Curator\Resources\MediaResource;
use Exception;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\View as FormView;
use Filament\Forms\Form;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;

final class CuratorPanel extends BaseCuratorPanel
{
    public function form(Form $form): Form
    {
        if ($this->maxItems) {
            $this->validationRules = array_filter($this->validationRules, function ($value) {
                if ($value === 'array' || str_starts_with($value, 'max:')) {
                    return false;
                }

                return true;
            });
        }

        return $form
            ->schema([
                Uploader::make('files_to_add')
                    ->visible(function () {
                        return count($this->selected) !== 1 &&
                            (
                                is_null(Gate::getPolicyFor($this->mediaClass)) ||
                                Gate::allows('create', $this->mediaClass)
                            );
                    })
                    ->hiddenLabel()
                    ->required()
                    ->multiple()
                    ->label(trans('curator::forms.fields.file'))
                    ->preserveFilenames($this->shouldPreserveFilenames)
                    ->minSize($this->minSize)
                    ->maxSize($this->maxSize)
                    ->rules($this->validationRules)
                    ->acceptedFileTypes($this->acceptedFileTypes)
                    ->disk($this->diskName)
                    ->visibility($this->visibility)
                    ->directory($this->directory)
                    ->pathGenerator($this->pathGenerator)
                    ->imageCropAspectRatio($this->imageCropAspectRatio)
                    ->imageResizeMode($this->imageResizeMode)
                    ->imageResizeTargetWidth($this->imageResizeTargetWidth)
                    ->imageResizeTargetHeight($this->imageResizeTargetHeight)
                    ->storeFileNamesIn('originalFilenames'),

                // Add meta fields directly in the "add/upload" flow when exactly one file is being uploaded.
                // This matches the Edit experience, but before the media record is created.
                Group::make([
                    Forms\Components\TextInput::make('title')
                        ->label('عنوان تصویر')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('alt')
                        ->label('متن جایگزین (alt)')
                        ->maxLength(500),
                    Forms\Components\Textarea::make('caption')
                        ->label('کپشن')
                        ->rows(2),
                    Forms\Components\Textarea::make('description')
                        ->label('توضیحات')
                        ->rows(3),
                ])
                    ->visible(function (Forms\Get $get): bool {
                        if (count($this->selected) === 1) {
                            return false;
                        }

                        $filesToAdd = $get('files_to_add') ?? [];

                        return is_array($filesToAdd) && count($filesToAdd) === 1;
                    }),

                Group::make([
                    FormView::make('preview')
                        ->view('curator::components.forms.edit-preview', [
                            'file' => Arr::first($this->selected),
                            'actions' => [
                                $this->viewAction(),
                                $this->downloadAction(),
                                $this->destroyAction(),
                            ],
                        ]),
                    ...collect(App::make(MediaResource::class)->getAdditionalInformationFormSchema())
                        ->map(function ($field) {
                            return $field->disabled(function () {
                                /** @var \Awcodes\Curator\CuratorPlugin $plugin */
                                $plugin = CuratorPlugin::get();

                                return ! $plugin->authorize('update');
                            });
                        })->toArray(),
                ])->visible(fn () => filled($this->selected) && count($this->selected) === 1),
            ])->statePath('data');
    }

    /**
     * @throws Exception
     */
    protected function createMediaFiles(array $formData): array
    {
        $media = [];

        $titleOverride = $formData['title'] ?? null;
        $meta = [
            'alt' => $formData['alt'] ?? null,
            'caption' => $formData['caption'] ?? null,
            'description' => $formData['description'] ?? null,
        ];

        foreach ($formData['files_to_add'] as $item) {
            $defaultTitle = pathinfo($formData['originalFilenames'][$item['path']] ?? null, PATHINFO_FILENAME);

            $item['title'] = filled($titleOverride) ? $titleOverride : $defaultTitle;

            foreach ($meta as $key => $value) {
                if (filled($value)) {
                    $item[$key] = $value;
                }
            }

            $media[] = tap(
                $this->mediaClass->create($item),
                fn (Media $media) => $media->getPrettyName(),
            )->toArray();
        }

        return $media;
    }
}
