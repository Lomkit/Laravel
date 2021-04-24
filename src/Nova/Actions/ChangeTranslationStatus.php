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

class ChangeTranslationStatus extends Action implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $allows;

    public function __construct($arguments) {
        $this->allows = $arguments;
    }

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        if (!in_array($fields->action, ['launchTranslation', 'waitTranslation', 'approveTranslation', 'waitApproveTranslation'])) {
            foreach ($models as $model) {
                return $this->markAsFailed($model, 'The user modified the field');
            }
            return Action::danger('Error');
        }

        foreach ($models as $model) {
            $model->{$fields->action}();
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
            Select::make('Action')
                ->options(function () {
                    return array_merge(
                        Arr::where($this->allows, function ($value) { return $value === 'wait'; }) ? [
                            'launchTranslation' => 'Launch Translation',
                            'waitTranslation' => 'Wait Translation',
                        ] : [],
                        Arr::where($this->allows, function ($value) { return $value === 'approve'; }) ? [
                            'approveTranslation' => 'Approve Translation',
                            'waitApproveTranslation' => 'Wait Approve Translation',
                        ] : []
                    );
                })
                ->rules('required')
        ];
    }
}
