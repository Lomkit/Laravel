<?php
namespace Lomkit\Laravel\Traits\AutomaticTranslations;

trait HasAutomaticTranslations {
    /**
     * Column names
     */

    public $waitingTranslation = false;
    public $waitingApproval = false;

    /**
     * Boot the soft deleting trait for a model.
     *
     * @return void
     */
    public static function bootHasAutomaticTranslations()
    {
        static::addGlobalScope(new HasAutomaticTranslationsScope());
    }

    /**
     * Get the name of the "waiting translation" column.
     *
     * @return string
     */
    public function getWaitingTranslationColumn()
    {
        return $this->waitingTranslation;
    }

    /**
     * Get the name of the "waiting approval" column.
     *
     * @return string
     */
    public function getWaitingApprovalColumn()
    {
        return $this->waitingApproval;
    }

    /**
     * Get the name of the "translatable" fields.
     *
     * @return array
     */
    public function getTranslatableFields()
    {
        return $this->translatable ?? [];
    }

    public function getHasAllFieldTranslatedAttribute() {
        foreach ($this->translatable as $translatable) {
            foreach (array_keys(config('lomkit.locales')) as $locale) {
                if ($this->getTranslation($translatable, $locale, false) === '') {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Get the translationStatus.
     *
     * @param  string  $value
     * @return string
     */
    public function getTranslationStatusAttribute($value)
    {
        if (!$this->has_all_field_translated) {
            if ($this->waitingTranslation !== false && $this->{$this->waitingTranslation} === true) {
                return config('lomkit.statuses.waiting_translation');
            }
            return config('lomkit.statuses.translating');
        }

        if ($this->waitingApproval !== false && $this->{$this->waitingApproval} === true) {
            return config('lomkit.statuses.waiting_approval');
        }
        return config('lomkit.statuses.translated');
    }
}
