<?php


namespace Lomkit\Laravel\Traits\AutomaticTranslations;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Query\Expression;

class HasAutomaticTranslationsScope implements Scope {
    /**
     * All of the extensions to be added to the builder.
     *
     * @var string[]
     */
    protected $extensions = ['WaitingTranslation', 'Translating', 'WaitingApproval', 'Translated', 'LaunchTranslation', 'ApproveTranslation', 'WaitTranslation', 'WaitApproveTranslation'];

    /**
     * Extend the query builder with the needed functions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    public function extend(Builder $builder)
    {
        foreach ($this->extensions as $extension) {
            $this->{"add{$extension}"}($builder);
        }

        $builder->onDelete(function (Builder $builder) {
            $column = $this->getDeletedAtColumn($builder);

            return $builder->update([
                $column => $builder->getModel()->freshTimestampString(),
            ]);
        });
    }

    /**
     * Get the "deleted at" column for the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return array
     */
    protected function getTranslatableFields(Builder $builder)
    {
        return $builder->getModel()->getTranslatableFields();
    }

    /**
     * Get the "deleted at" column for the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return mixed
     */
    protected function getWaitingTranslationColumn(Builder $builder)
    {
        return $builder->getModel()->getWaitingTranslationColumn();
    }

    /**
     * Get the "deleted at" column for the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return mixed
     */
    protected function getWaitingApprovalColumn(Builder $builder)
    {
        return $builder->getModel()->getWaitingApprovalColumn();
    }

    /**
     * Get the locales to traduce.
     *
     * @return array
     */
    protected function getLocales()
    {
        return array_keys(config('lomkit.locales'));
    }

    /**
     * Add a "where JSON contains keys" clause to the query.
     *
     * @param Builder $builder
     * @param string $column
     * @param mixed $value
     * @return Builder
     */
    public function whereJsonContainsKeys(Builder $builder, string $column, $keys)
    {
        foreach ($keys as $key) {
            $builder->whereRaw("JSON_EXTRACT({$column}, \"$.{$key}\") IS NOT NULL");
        }

        return $builder;
    }

    /**
     * Add a "where JSON doesn't contains keys" clause to the query.
     *
     * @param Builder $builder
     * @param string $column
     * @param mixed $value
     * @return Builder
     */
    public function whereJsonDoesntContainKeys(Builder $builder, string $column, $keys)
    {
        $builder->where(function ($query) use ($keys, $column) {
            foreach ($keys as $key) {
                $query->orWhereRaw("JSON_EXTRACT({$column}, \"$.{$key}\") IS NULL");
            }
        });

        return $builder;
    }

    /**
     * Add the waiting-translation extension to the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    protected function addWaitingTranslation(Builder $builder)
    {
        $builder->macro('waitingTranslation', function (Builder $builder) {
            foreach ($this->getTranslatableFields($builder) as $translatable) {
                $this->whereJsonDoesntContainKeys($builder, $translatable, $this->getLocales());
            }

            if ($this->getWaitingTranslationColumn($builder) !== false) {
                $builder->where($this->getWaitingTranslationColumn($builder), true);
            }

            return $builder;
        });
    }

    /**
     * Add the translating extension to the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    protected function addTranslating(Builder $builder)
    {
        $builder->macro('translating', function (Builder $builder) {
            foreach ($this->getTranslatableFields($builder) as $translatable) {
                $this->whereJsonDoesntContainKeys($builder, $translatable, $this->getLocales());
            }

            if ($this->getWaitingTranslationColumn($builder) !== false) {
                $builder->where($this->getWaitingTranslationColumn($builder), false);
            }

            return $builder;
        });
    }

    /**
     * Add the waiting-approval extension to the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    protected function addWaitingApproval(Builder $builder)
    {
        $builder->macro('waitingApproval', function (Builder $builder) {
            foreach ($this->getTranslatableFields($builder) as $translatable) {
                $this->whereJsonContainsKeys($builder, $translatable, $this->getLocales());
            }

            if ($this->getWaitingApprovalColumn($builder) !== false) {
                $builder->where($this->getWaitingApprovalColumn($builder), true);
            }

            return $builder;
        });
    }

    /**
     * Add the translated extension to the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    protected function addTranslated(Builder $builder)
    {
        $builder->macro('translated', function (Builder $builder) {
            foreach ($this->getTranslatableFields($builder) as $translatable) {
                $this->whereJsonContainsKeys($builder, $translatable, $this->getLocales());
            }

            if ($this->getWaitingApprovalColumn($builder) !== false) {
                $builder->where($this->getWaitingApprovalColumn($builder), false);
            }

            return $builder;
        });
    }

    /**
     * Add the restore extension to the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    protected function addLaunchTranslation(Builder $builder)
    {
        $builder->macro('launchTranslation', function (Builder $builder) {
            return $builder->update([$builder->getModel()->getWaitingTranslationColumn() => false]);
        });
    }

    /**
     * Add the restore extension to the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    protected function addWaitTranslation(Builder $builder)
    {
        $builder->macro('waitTranslation', function (Builder $builder) {
            return $builder->update([$builder->getModel()->getWaitingTranslationColumn() => true]);
        });
    }

    /**
     * Add the restore extension to the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    protected function addApproveTranslation(Builder $builder)
    {
        $builder->macro('approveTranslation', function (Builder $builder) {
            return $builder->update([$builder->getModel()->getWaitingApprovalColumn() => false]);
        });
    }

    /**
     * Add the restore extension to the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    protected function addWaitApproveTranslation(Builder $builder)
    {
        $builder->macro('waitApproveTranslation', function (Builder $builder) {
            return $builder->update([$builder->getModel()->getWaitingApprovalColumn() => true]);
        });
    }

}
