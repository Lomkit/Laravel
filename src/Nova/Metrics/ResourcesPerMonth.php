<?php

namespace Lomkit\Laravel\Nova\Metrics;

use Illuminate\Database\Eloquent\Model;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Trend;
use Laravel\Nova\Nova;

class ResourcesPerMonth extends Trend
{

    protected $model;

    public function setModel($model) {
        $this->model = $model;

        return $this;
    }

    /**
     * Get the displayable name of the metric.
     *
     * @return string
     */
    public function name()
    {
        return Nova::humanize((new $this->model)->getTable().' Per Months');
    }

    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        return $this->countByMonths($request, $this->model);
    }

    /**
     * Get the ranges available for the metric.
     *
     * @return array
     */
    public function ranges()
    {
        return [
            6 => __(':months Months', ['months' => 6]),
            12 => __(':months Months', ['months' => 12]),
            24 => __(':months Months', ['months' => 24]),
            48 => __(':months Months', ['months' => 48]),
        ];
    }

    /**
     * Determine for how many minutes the metric should be cached.
     *
     * @return  \DateTimeInterface|\DateInterval|float|int
     */
    public function cacheFor()
    {
        return now()->addHours(20);
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return (new $this->model)->getTable().'-per-month';
    }
}
