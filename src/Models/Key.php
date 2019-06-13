<?php

namespace Dialect\TransEdit\Models;

use Illuminate\Database\Eloquent\Model;

class Key extends Model
{

    protected $table = 'transedit_keys';
    protected $guarded = ['id'];

    public function translations()
    {
        return $this->hasMany(Translation::class);
    }
}
