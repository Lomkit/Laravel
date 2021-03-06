<?php

namespace Lomkit\Laravel\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Actions\DestructiveAction;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Select;

class ForgetTranslation extends DestructiveAction implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        if(!in_array($fields->locale, array_keys(Arr::except(config('lomkit.locales'), 'en')))) {
            foreach ($models as $model) {
                $this->markAsFailed($model, 'The user modified the field');
            }
            return Action::danger('Error');
        }

        foreach ($models as $model) {
            foreach ($model->translatable as $translatable) {
                $model->forgetTranslation($translatable, $fields->locale);
            }
            $model->save();

            $this->markAsFinished($model);
        }
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields()
    {
        return [
            Select::make('Locale')
                ->options(Arr::except(config('lomkit.locales'), 'en'))
                ->rules('required')
        ];
    }
}
