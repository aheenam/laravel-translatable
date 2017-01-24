<?php

namespace Aheenam\Translatable\Test;


use Aheenam\Translatable\Test\Models\TestModel;
use Aheenam\Translatable\Translation;
use Illuminate\Support\Facades\App;

class TranslatableTest extends TestCase
{

    /**
     * @return void
     */
    public function test_is_test_running()
    {
        $this->assertTrue(true);
    }

    /**
     * @return void
     */
    public function test_saves_translation()
    {
        // test model with default value for attribute "name"
        $testModel = factory(TestModel::class)->create([
            'name'  => 'testValue',
            'place' => 'France'
        ]);

        $testModel->translate('de', [
            'name'      => 'TestWert',
            'place'     => 'Frankreich'
        ]);

        $deName = $testModel->translations()->where('translatable_type', TestModel::class)
            ->where('translatable_id', $testModel->id)
            ->where('key', 'name')
            ->where('locale', 'de')
            ->value('translation');

        $this->assertEquals('TestWert', $deName);

    }

    /**
     * @return void
     */
    public function test_is_returning_value_of_current_app_locale()
    {
        // test model with default value for attribute "name"
        $testModel = factory(TestModel::class)->create([
            'name' => 'testValue'
        ]);

        // test model has translation for language "fr"
        $testModel->translations()->save(new Translation([
            'translatable_type' => TestModel::class,
            'translatable_id'   => $testModel->id,
            'key'               => 'name',
            'translation'       => 'testValue_fr',
            'locale'            => 'fr'
        ]));

        // app locale changes to fr
        App::setLocale('fr');

        // assert that the value of attribute "name" is the translated value
        $this->assertEquals($testModel->name, 'testValue_fr');

    }

    public function test_avoid_translating_attribute_if_not_in_translatable_array()
    {
        // test model with default value for attribute "name"
        $testModel = factory(TestModel::class)->create([
            'name'  => 'testValue',
            'place' => 'France'
        ]);

        $testModel->translate('de', [
            'name'      => 'TestWert',
            'place'     => 'Frankreich'
        ]);

        $deName = $testModel->translations()->where('translatable_type', TestModel::class)
            ->where('translatable_id', $testModel->id)
            ->where('key', 'place')
            ->where('locale', 'de')
            ->value('translation');

        $this->assertNotEquals('Frankreich', $deName);
        $this->assertEquals('France', $deName);

    }


}