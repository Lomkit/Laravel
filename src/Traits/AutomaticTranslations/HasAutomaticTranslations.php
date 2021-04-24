<?php
namespace Lomkit\Laravel\Traits\AutomaticTranslations;

trait HasAutomaticTranslations {

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);

        if ($this->getWaitingTranslationColumn()) {
            $this->mergeCasts([
                $this->getWaitingTranslationColumn() => 'boolean'
            ]);
        }

        if ($this->getWaitingApprovalColumn()) {
            $this->mergeCasts([
                $this->getWaitingApprovalColumn() => 'boolean'
            ]);
        }
    }

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
        return $this->waitingTranslation ?? false;
    }

    /**
     * Get the name of the "waiting approval" column.
     *
     * @return string
     */
    public function getWaitingApprovalColumn()
    {
        return $this->waitingApproval ?? false;
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
            if ($this->getWaitingTranslationColumn() !== false && $this->{$this->getWaitingTranslationColumn()} === true) {
                return config('lomkit.statuses.waiting_translation');
            }
            return config('lomkit.statuses.translating');
        }

        if ($this->getWaitingApprovalColumn() !== false && $this->{$this->getWaitingApprovalColumn()} === true) {
            return config('lomkit.statuses.waiting_approval');
        }
        return config('lomkit.statuses.translated');
    }

    /**
     * Launch translation model instance.
     *
     * @return bool|null
     */
    public function launchTranslation() {
        $this->fireModelEvent('launch-translation');

        $this->{$this->getWaitingTranslationColumn()} = false;

        $result = $this->save();

        $this->fireModelEvent('launched-translation', false);

        return $result;
    }

    /**
     * Launch translation model instance.
     *
     * @return bool|null
     */
    public function waitTranslation() {
        $this->fireModelEvent('wait-translation');

        $this->{$this->getWaitingTranslationColumn()} = true;

        $result = $this->save();

        $this->fireModelEvent('waited-translation', false);

        return $result;
    }

    /**
     * Launch translation model instance.
     *
     * @return bool|null
     */
    public function approveTranslation() {
        $this->fireModelEvent('approve-translation');

        $this->{$this->getWaitingApprovalColumn()} = false;

        $result = $this->save();

        $this->fireModelEvent('approved-translation', false);

        return $result;
    }

    /**
     * Launch translation model instance.
     *
     * @return bool|null
     */
    public function waitApproveTranslation() {
        $this->fireModelEvent('wait-approve-translation');

        $this->{$this->getWaitingApprovalColumn()} = true;

        $result = $this->save();

        $this->fireModelEvent('waited-approved-translation', false);

        return $result;
    }
}
