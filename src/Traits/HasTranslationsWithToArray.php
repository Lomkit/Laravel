<?php

namespace Lomkit\Laravel\Traits;

use Spatie\Translatable\HasTranslations;

trait HasTranslationsWithToArray
{
    use HasTranslations;
    /**
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        $attributes = parent::toArray();
        foreach ($this->getTranslatableAttributes() as $field) {
            $attributes[$field] = $this->getTranslation($field);
        }
        return $attributes;
    }
}
