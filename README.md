laravel-translatable
===
A Laravel Package to manage translations for models simply using a Trait

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