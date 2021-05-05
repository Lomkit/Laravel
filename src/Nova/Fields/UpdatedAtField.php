<?php


namespace Lomkit\Laravel\Nova\Fields;


use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Makeable;

class UpdatedAtField extends DateTime {

    /**
     * Create a new field.
     *
     * @param  string  $name
     * @param  string|callable|null  $attribute
     * @param  callable|null  $resolveCallback
     * @return void
     */
    public function __construct($name = 'Updated At', $attribute = null, callable $resolveCallback = null) {
        parent::__construct($name, $attribute, $resolveCallback);

        $this
            ->onlyOnDetail();
    }

}
