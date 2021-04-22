<?php

namespace Lomkit\Laravel\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;

class TranslateModel implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The model instance.
     *
     * @var Model
     */
    protected $model;

    /**
     * The column to translate.
     *
     * @var Model
     */
    protected $column;

    /**
     * The lang to translate to.
     *
     * @var string
     */
    protected $lang;

    /**
     * The number of seconds after which the job's unique lock will be released.
     *
     * @var int
     */
    public $uniqueFor = 900;

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId()
    {
        $className = get_class($this->model);
        return "lomkit.translations.{$this->lang}.{$this->model->id}.{$className}";
    }

    /**
     * Create a new job instance.
     *
     * @param Model $model
     * @param string $lang
     *
     * @return void
     */
    public function __construct(Model $model, $lang, $column)
    {
        $this->model = $model->withoutRelations();
        $this->lang = $lang;
        $this->column = $column;
    }

    /**
     * Get the middleware the job should pass through.
     *
     * @return array
     */
    public function middleware()
    {
        return [new WithoutOverlapping($this->model->id)];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // @TODO TRADUCTION DE LA LANGUE
    }
}
