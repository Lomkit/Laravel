<?php

namespace Lomkit\Laravel\Console;

use Illuminate\Console\Command;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Lomkit\Laravel\Jobs\TranslateModel;

class TranslateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lomkit:translate {model?} {--class= : The class to translate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Translate an entry';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->argument('model')) {
            if (!$this->option('class')) {
                $this->error('You need to define the class of the model');
                return;
            }
            $model = $this->option('class')::find($this->argument('model'));
        } else {
            $model = $this->getModels()
                ->filter(function($model) {
                    return Arr::has(class_uses($model), 'Lomkit\Laravel\Traits\AutomaticTranslations\HasAutomaticTranslations');
                })->first(function($finalModel) {
                    return $finalModel::translating()->count() > 0;
                });
            if (!is_null($model)) {
                $model = $model::translating()->inRandomOrder()->first();
            }
        }

        if (is_null($model)) {
            $this->info('No model found');
            return;
        }

        foreach (array_keys(config('lomkit.locales')) as $lang) {
            foreach ($model->translatable as $column) {
                TranslateModel::dispatchIf($model->getTranslation($column, $lang, false) === '', $model, $lang, $column);
            }
        }

        $this->info("Model treated: ".get_class($model)." {$model->id}");
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
                }

                return $valid;
            });

        return $models;
    }
}
