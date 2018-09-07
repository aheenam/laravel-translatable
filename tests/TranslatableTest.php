<?php

namespace Aheenam\Translatable\Test;

use Illuminate\Support\Facades\App;
use Aheenam\Translatable\Translation;
use Aheenam\Translatable\Test\Models\TestModel;

class TranslatableTest extends TestCase
{
    /**
     * @return void
     */
    public function test_saves_translation()
    {
        // test model with default value for attribute "name"
        $testModel = factory(TestModel::class)->create([
            'name'  => 'testValue',
            'place' => 'France',
        ]);

        $testModel->translate('de', [
            'name'      => 'TestWert',
            'place'     => 'Frankreich',
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
            'name' => 'testValue',
        ]);

        // test model has translation for language "fr"
        $testModel->translations()->save(new Translation([
            'key'               => 'name',
            'translation'       => 'testValue_fr',
            'locale'            => 'fr',
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
            'place' => 'France',
        ]);

        // add some translation
        $testModel->translate('de', [
            'name'      => 'TestWert',
            'place'     => 'Frankreich',
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
            'name' => 'testName_de',
        ]);

        // make sure that app locale is different from de
        App::setLocale('en');

        // expect name to be testName and testName_de in de
        $this->assertEquals('testName_de', $testModel->in('de')->name);
        $this->assertEquals('testName', $testModel->name);
        $this->assertEquals('testPlace', $testModel->in('de')->place);
    }

    /**
     * @return void
     */
    public function test_getting_translation_of_attribute_by_function()
    {
        // test model with default value for attribute "name"
        $testModel = factory(TestModel::class)->create([
            'name'  => 'testName',
            'place' => 'testPlace',
        ]);

        // set a translation
        $testModel->translate('de', [
            'name' => 'testName_de',
        ]);

        $this->assertEquals('testName_de', $testModel->translate('name', 'de'));
        $this->assertEquals('testPlace', $testModel->translate('place', 'de'));
    }

    /**
     * @return void
     */
    public function test_it_updates_translation_on_multiple_saves()
    {
        // test model with default value for attribute "name"
        $testModel = factory(TestModel::class)->create([
            'name'  => 'testName',
            'place' => 'testPlace',
        ]);

        // set a translation
        $testModel->translate('de', [
            'name' => 'testName_de',
        ]);

        // update a translation
        $testModel->translate('de', [
            'name' => 'testName_de_update',
        ]);

        $translationsCount = $testModel->translations()
            ->where('locale', 'de')
            ->where('key', 'name')
            ->count();

        $this->assertEquals(1, $translationsCount);
        $this->assertEquals('testName_de_update', $testModel->in('de')->name);
    }

    /**
     * @return void
     */
    public function test_it_returns_all_translations()
    {
        // test model with default value for attribute "name"
        $testModel = factory(TestModel::class)->create([
            'name'  => 'testName',
            'place' => 'testPlace',
        ]);

        // set a translation
        $testModel->translate('de', [
            'name'      => 'testName_de',
            'title'     => 'title_de',
        ]);

        // set another translation
        $testModel->translate('fr', [
            'name' => 'testName_fr',
        ]);

        // set another translation
        $testModel->translate('it', [
            'name' => 'testName_it',
        ]);

        // set another translation
        $testModel->translate('es', [
            'name' => 'testName_es',
        ]);

        $allTranslations = $testModel->allTranslations();

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $allTranslations);
        $this->assertEquals(4, $allTranslations->count());
    }

    /**
     * @return void
     */
    public function test_it_returns_default_value_if_one_attribute_not_present()
    {
        // test model with default value for attribute "name"
        $testModel = factory(TestModel::class)->create([
            'name'      => 'testName',
            'title'     => 'testTitle',
        ]);

        // set another translation
        $testModel->translate('es', [
            'name' => 'testName_es',
        ]);

        $this->assertEquals('testTitle', $testModel->in('es')->title);
    }

    /**
     * @return void
     */
    public function test_it_removes_a_complete_locale()
    {
        $testModel = factory(TestModel::class)->create([
            'name'      => 'testName',
            'title'     => 'testTitle',
        ]);

        $testModel->translate('es', [
            'name'  => 'testName_es',
            'title' => 'testTitle_es',
        ]);

        $testModel->translate('de', [
            'name'  => 'testName_de',
            'title' => 'testTitle_de',
        ]);

        $testModel->removeTranslationIn('es');

        $allTranslations = $testModel->allTranslations();

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $allTranslations);
        $this->assertEquals(1, $allTranslations->count());
    }

    /**
     * @return void
     */
    public function test_it_removes_an_attributes_translation()
    {
        $testModel = factory(TestModel::class)->create([
            'name'      => 'testName',
            'title'     => 'testTitle',
        ]);

        $testModel->translate('es', [
            'name'  => 'testName_es',
            'title' => 'testTitle_es',
        ]);

        $testModel->translate('de', [
            'name'  => 'testName_de',
            'title' => 'testTitle_de',
        ]);

        $testModel->removeTranslation('es', 'name');

        $this->assertEquals('testName', $testModel->in('es')->name);
        $this->assertEquals('testTitle_es', $testModel->in('es')->title);
    }
}
