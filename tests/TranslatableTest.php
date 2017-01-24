<?php

namespace Aheenam\Translatable\Test;


use Aheenam\Translatable\Test\Models\TestModel;

class TranslatableTest extends TestCase
{

    /**
     * @var TestModel
     */
    protected $testModel;

    /**
     * setup the test
     */
    public function setUp()
    {
        parent::setUp();
        $this->testModel = new TestModel();
    }

    /**
     * @return void
     */
    public function test_is_test_running()
    {
        $this->assertTrue(true);
    }


}