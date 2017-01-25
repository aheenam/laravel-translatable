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

        // add some translation
        $testModel->translate('de', [
            'name'      => 'TestWert',
            'place'     => 'Frankreich'
        ]);

        // change language
        App::setLocale('de');

        // expect name to be translated, but not place
        $this->assertEquals('TestWert', $testModel->name);
        $this->assertEquals('France', $testModel->place);

        //expect that there does not even a place translation
        $placeTranslation = $testModel->translations()
            ->where('locale', 'de')
            ->where('key', 'place')
            ->first();
        $this->assertNull($placeTranslation);

    }


    /**
     * @return void
     */
    public function test_translate_whole_model()
    {
        // test model with default value for attribute "name"
        $testModel = factory(TestModel::class)->create([
            'name'  => 'testName',
            'place' => 'testPlace',
        ]);

        // set a translation
        $testModel->translate('de', [
            'name' => 'testName_de'
        ]);

        // make sure that app locale is different from de
        App::setLocale('en');

        // expect name to be testName and testName_de in de
        $this->assertEquals('testName_de', $testModel->in('de')->name);
        $this->assertEquals('testName', $testModel->name);
        $this->assertEquals('testPlace', $testModel->in('de')->place);

    }


    public function test_getting_translation_of_attribute_by_function()
    {
        // test model with default value for attribute "name"
        $testModel = factory(TestModel::class)->create([
            'name'  => 'testName',
            'place' => 'testPlace',
        ]);

        // set a translation
        $testModel->translate('de', [
            'name' => 'testName_de'
        ]);

        $this->assertEquals( 'testName_de', $testModel->translate('name', 'de' ) );
        $this->assertEquals( 'testPlace', $testModel->translate('place', 'de' ) );


    }


}