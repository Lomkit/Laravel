<?php


namespace Lomkit\Laravel\Nova\Fields;


use Laravel\Nova\Fields\Badge;

class TranslationStatusField extends Badge {

    /**
     * Create a new field.
     *
     * @param  string  $name
     * @param  string|callable|null  $attribute
     * @param  callable|null  $resolveCallback
     * @return void
     */
    public function __construct($name = 'Translation Status', $attribute = null, callable $resolveCallback = null) {
        parent::__construct($name, $attribute, $resolveCallback);

        $this->map([
            config('lomkit.statuses.waiting_translation') => 'danger',
            config('lomkit.statuses.translating') => 'warning',
            config('lomkit.statuses.waiting_approval') => 'info',
            config('lomkit.statuses.translated') => 'success',
        ]);
    }
}
