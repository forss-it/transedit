<?php

namespace Dialect\TransEdit\Models;

use Illuminate\Database\Eloquent\Model;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Key extends Model
{
    use Cachable;

    protected $table = 'transedit_keys';
    protected $guarded = ['id'];

    public function translations()
    {
        return $this->hasMany(Translation::class);
    }
}
