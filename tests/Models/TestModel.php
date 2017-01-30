<?php

namespace Aheenam\Translatable\Test\Models;

use Aheenam\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model as Eloquent;

class TestModel extends Eloquent
{
    use Translatable;

    protected $table = 'test_models';

    protected $guarded = [];
    public $timestamps = false;

    protected $translatable = ['name', 'title'];
}
