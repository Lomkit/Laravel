<?php

namespace Lomkit\Laravel\Jobs;

use Google\Cloud\Translate\V2\TranslateClient;
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
        $translate = new TranslateClient([
            'keyFilePath' => config('lomkit.google_translate_key_path')
        ]);

        $result = $translate->translate($this->model->getTranslation($this->column, 'en', false), [
            'source' => 'en',
            'target' => $this->lang
        ]);

        $this->model->setTranslation($this->column, $this->lang, html_entity_decode($result['text'], ENT_QUOTES));

        if ($this->model->has_all_field_translated && $this->model->getWaitingApprovalColumn() !== false) {
            $this->model->waitApproveTranslation();
        }

        $this->model->save();
    }
}
