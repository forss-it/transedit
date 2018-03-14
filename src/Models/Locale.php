<?php

namespace Dialect\TransEdit\Models;

use Illuminate\Database\Eloquent\Model;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Locale extends Model
{
    use Cachable;
    protected $table = 'transedit_locales';
    protected $guarded = ['id'];

    public function translations()
    {
        return $this->hasMany(Translation::class);
    }
}
