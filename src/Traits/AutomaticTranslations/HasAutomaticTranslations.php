<?php
namespace Lomkit\Laravel\Traits\AutomaticTranslations;

trait HasAutomaticTranslations {

    /**
     * All statuses
     **/
    public $WAITING_TRANSLATION = 'waiting_translation';
    public $TRANSLATING = 'translating';
    public $WAITING_APPROVAL = 'waiting_approval';
    public $TRANSLATED = 'translated';

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
}