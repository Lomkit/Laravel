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

## Traits

#### HasResourcePolicy

Cette classe permet de créer une Policy avec toutes les autorisations de base suivant les conventions Lomkit

#### HasAttachPolicy (Laravel Nova)

Cette classe permet de faire en sorte que les ressources externes d'une ressource Laravel Nova ne soit modifiable que si le parent l'est

## Automatic Translations

Requires https://github.com/optimistdigital/nova-translatable and https://github.com/spatie/laravel-translatable

Add `HasAutomaticTranslations` to the model that need translations

The locales are chosen using `config('nova-translatable.locales')` 

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

### Commands
**@TODO: Expliquer ici commande + job**
**@TODO: Expliquer configuration google translate**

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

### Laravel Nova Helpers