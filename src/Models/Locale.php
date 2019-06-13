<?php

namespace Dialect\TransEdit\Models;

use Illuminate\Database\Eloquent\Model;

class Locale extends Model
{
    protected $table = 'transedit_locales';
    protected $guarded = ['id'];

    public function translations()
    {
        return $this->hasMany(Translation::class);
    }
}
