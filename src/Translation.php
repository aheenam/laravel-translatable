<?php

namespace Aheenam\Translatable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Translation extends Model
{
    /**
     * @var string
     */
    protected $table = 'translatable_translations';

    protected $fillable = ['key', 'locale', 'translation'];

    /**
     * Get all of the owning translatable models.
     */
    public function translatable(): MorphTo
    {
        return $this->morphTo();
    }
}
