<?php

$factory->define(Aheenam\Translatable\Test\Models\TestModel::class, function (Faker\Generator $faker) {
    return [
        'name'  => $faker->name,
        'place' => $faker->city,
        'title' => $faker->title
    ];
});

$factory->define(Aheenam\Translatable\Translation::class, function (Faker\Generator $faker) {
    return [
        'key' => 'name',
        'translation' => $faker->word,
        'locale' => $faker->locale
    ];
});
