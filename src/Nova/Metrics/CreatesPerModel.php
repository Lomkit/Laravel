<?php


namespace Lomkit\Laravel\Nova\Metrics;


use Carbon\Carbon;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;
use Laravel\Nova\Nova;

class CreatesPerModel extends Partition {

    protected $to;
    protected $from;

    public function automaticHelpText() {
        if ($this->getTo()->isToday()) {
            $this->from("Data from {$this->getFrom()->diffForHumans()}");
        } else {
            $this->help("Data from {$this->getFrom()->format('Y-m-d')} to {$this->getTo()->format('Y-m-d')}");
        }
    }

    public function from($from) {
        $this->from = $from;

        return $this;
    }

    public function to($to) {
        $this->to = $to;

        return $this;
    }

    public function getFrom() {
        return $this->from ?? Carbon::now()->subMonth();
    }

    public function getTo() {
        return $this->to ?? Carbon::now();
    }

    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        $results = [];

        foreach ($this->getModels() as $model) {
            $count = $model::whereBetween($model::CREATED_AT, [$this->getFrom(), $this->getTo()])
                ->count();
            if ($count > 0) {
                $results[Nova::humanize((new $model)->getTable())] = $count;
            }
        }

        return $this->result($results);
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
        return 'creates-per-model';
    }

    protected function getModels() {
        $models = collect(File::allFiles(app_path()))
            ->map(function ($item) {
                $path = $item->getRelativePathName();
                $class = sprintf('\%s%s',
                    Container::getInstance()->getNamespace(),
                    strtr(substr($path, 0, strrpos($path, '.')), '/', '\\'));

                return $class;
            })
            ->filter(function ($class) {
                $valid = false;

                if (class_exists($class)) {
                    $reflection = new \ReflectionClass($class);

                    $valid = $reflection->isSubclassOf(Model::class) &&
                        !$reflection->isAbstract();

                    if ($valid === true) {
                        $instanciated = new $class;
                        $valid = $instanciated->timestamps !== false;
                    }
                }

                return $valid;
            });

        return $models;
    }
}
