<?php

namespace Dialect\TransEdit\Models;

use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    protected $table = 'transedit_translations';
    protected $guarded = ['id'];

    public function locale()
    {
        return $this->belongsTo(Locale::class);
    }

    public function key()
    {
        return $this->belongsTo(Key::class);
    }
}
