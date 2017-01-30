<?php

namespace Aheenam\Translatable;

use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{

    /**
     * @var string
     */
    protected $table = 'translatable_translations';

    protected $fillable = ['key', 'locale', 'translation'];

    /**
     * Get all of the owning translatable models.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function translatable()
    {
        return $this->morphTo();
    }
}
