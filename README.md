# Laravel Lomkit

> Laravel tools for Lomkit.

## Setup
- Setup composer.json :

```php
{
    // ...
    "require": {
        "lomkit/laravel": "*"
    },
    // ...
    "repositories": [
        { "type": "vcs", "url": "https://github.com/Lomkit/Laravel.git" }
    ],
}
```

And then run `composer update`

## Config

You can publish the config using:
```bash
php artisan vendor:publish --tag=lomkit-config
```

More doc about google api key can be found here: https://github.com/googleapis/google-cloud-php/blob/master/AUTHENTICATION.md

## Traits

#### HasResourcePolicy

Cette classe permet de créer une Policy avec toutes les autorisations de base suivant les conventions Lomkit

#### HasAttachPolicy (Laravel Nova)

Cette classe permet de faire en sorte que les ressources externes d'une ressource Laravel Nova ne soit modifiable que si le parent l'est

## Automatic Translations

Requires https://github.com/optimistdigital/nova-translatable and https://github.com/spatie/laravel-translatable

Add `HasAutomaticTranslations` to the model that need translations

The locales are chosen using `config('lomkit.locales')` 

The fields chosen are those in the `$translatable` in the model
```php
public $translatable = ['name'];
```

##### Optional

If you need approval before translating:

```php
public $waitingTranslation = 'waiting_translation';
```

If you need approval after translating:

```php
public $waitingApproval = 'waiting_approval';
```

Migrations example:
```php
$table->boolean('waiting_translation')->default(true);
$table->boolean('waiting_approval')->default(false);
```

### Commands

#### Lomkit Translate

This command translate a model completely including all locales

```bash
php artisan lomkit:translate {model?} {--class= : The class to translate} 
```
If no arguments are given, it will take the first translating status it find

You may want to schedule it:

```php
use Lomkit\Laravel\Console\TranslateCommand;

$schedule->command(TranslateCommand::class)->everyMinute()
    ->environments(['staging', 'production']);
```

### Query Builder Methods
    Please note that if you don't define the required variables,
    the waiting methods will return the same as the other methods

Waiting for translation
```php
use App\Models\Flight;

$flights = Flight::waitingTranslation()
                ->get();
```

Translating
```php
use App\Models\Flight;

$flights = Flight::translating()
                ->get();
```

Waiting for approval
```php
use App\Models\Flight;

$flights = Flight::waitingApproval()
                ->get();
```

Translated
```php
use App\Models\Flight;

$flights = Flight::translated()
                ->get();
```

LaunchTranslation
```php
use App\Models\Flight;

$flights = Flight::launchTranslation();
```

WaitTranslation
```php
use App\Models\Flight;

$flights = Flight::waitTranslation();
```

ApproveTranslation
```php
use App\Models\Flight;

$flights = Flight::approveTranslation();
```

WaitApproveTranslation
```php
use App\Models\Flight;

$flights = Flight::waitApproveTranslation();
```


### Laravel Nova Helpers 
#### Actions
##### Forget All Translations
This action will forget all translations except English for the selected models
```php
use Lomkit\Laravel\Nova\Actions\ForgetAllTranslations;

ForgetAllTranslations::make()
```

##### Forget Translation
This action will ask the user for the locale except english which is required for all translations
```php
use Lomkit\Laravel\Nova\Actions\ForgetTranslation;

ForgetTranslation::make()
```

#### Change Translation Status
This action allow to change the translation status
```php
use Lomkit\Laravel\Nova\Actions\ChangeTranslationStatus;

ChangeTranslationStatus::make(['wait','approve'])
```

#### Change Translation Status
This action approove the status whatever it's state
```php
use Lomkit\Laravel\Nova\Actions\ApproveTranslation;

ApproveTranslation::make()
```

#### Fields
##### Translation Status
This field works the same as a badge with preconfigured labels
```php
use Lomkit\Laravel\Nova\Fields\TranslationStatusField;

TranslationStatusField::make()
```

#### Lenses

By default all lenses inherit from parent, you can change this by extending
##### Waiting For Translation
```php
use Lomkit\Laravel\Nova\Lenses\WaitingTranslationModels;

WaitingTranslationModels::make()
```
##### Translating
```php
use Lomkit\Laravel\Nova\Lenses\TranslatingModels;

TranslatingModels::make()
```

##### Waiting Approval
```php
use Lomkit\Laravel\Nova\Lenses\WaitingApprovalModels;

WaitingApprovalModels::make()
```

##### Translated
```php
use Lomkit\Laravel\Nova\Lenses\TranslatedModels;

TranslatedModels::make()
```
