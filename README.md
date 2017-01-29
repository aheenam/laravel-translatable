A Trait to handle Translations in Laravel
===
This is a Laravel package containing a trait for translatable Eloquent models. This package follows
the approach to have only a single table to maintain all the translations.

This approach may not be perfect for every use case as the table can grow really big. But compared to all the
other packages this approach is the most flexible as it lets you make models and its attributes translatable
without extra configuration.

Alternatives to this package are following packages:

1. [Spatie/laravel-translatable](https://github.com/spatie/laravel-translatable) saves the translatable
attributes as jsons
2. [dimsav/laravel-translatable](https://github.com/dimsav/laravel-translatable) expects a new table for
every new model that has translatable attributes

## Todo

This project has not reached a stable status, there are still some methods that must be implemented before v1.0
can be released

- -[remove an attributes translation]
- -[remove all (or a locale's) translations of an model]
- -[return default value if translation is not present]
- -[make sure that translation is updated on multiple saves]
- -[returns all translations]
- do some refactoring

Installation
---
You can install the package via composer:

    composer require aheenam/laravel-translatable

> Note: Until this packages has no stable version you have to add the git repository manually to the project. 
Therefor add the following lines to your composer.json

```
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/Aheenam/laravel-translatable"
    }
],
```

Then add the service provider must be registered:

```
// config/app.php
'providers' => [
    ...
    Aheenam\Translatable\TranslatableServiceProvider::class,
];
```

Now you can use this Trait on any Eloquent Model of your project

Usage
---

To make your Eloquent Model translatable just add the `Aheenam\Translatable\Translatable` Trait to your model.
Then add a public attribute `$translatable` as an array containing all the attributes that should be translatable.

```php
<?php

use Illuminate\Database\Eloquent\Model;
use Aheenam\Translatable\Translatable;

class MyModel extends Model {
    
    use Translatable;
    
    public $translatable = ['place'];
    
}
```

Methods
---

The simplest version of getting an translation is to simply get the property. This will return the value of
the property in the current language

```php
// assuming $myModel is an instace of MyModel class defined above
// and the translations are set

echo $myModel->place; // returns 'Germany'

App::setLocale('de');

echo $myModel->place; // returns 'Deutschland'
```

You can also use

```php
$myModel->translate('place', 'de'); // returns 'Deutschland'
```

### Getting the translated model
Sometime you want to keep the model properties untouched but get a translated version of your model. You can reach
this by using

```php
$translatedModel = $myModel->in('de');

echo $translatedModel->place; // returns 'Deutschland'

// shorter
echo $myModel->in('de')->place; // returns 'Deutschland'

```

### Translating an Model

You can translate a model using

```php
$myModel->translate('no', [
    'place' => 'Tyskland'
]);
```

> Note: The method translate() can be used to get a translation and to set a translation. The difference is what
type of parameters are used. If you pass an array as the second argument, then translate() will work as a setter
otherwise as a getter

Changelog
---
*soon*

Testing
---
To run tests use

    $ composer test
    
Contributing
---
*soon*

Security
---
If you discover any security related issues, please email rathes@aheenam.com or use the issue tracker of GitHub.

About Aheenam
---
Aheenam is a small company from NRW, Germany creating custom digital solutions. Visit 
[our website](https://aheenam.com) to find out more about us.

License
---
The MIT License (MIT). Please see [License File](https://github.com/Aheenam/laravel-translatable/blob/master/LICENSE)
for more information.