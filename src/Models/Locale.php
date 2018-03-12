<?php
namespace Dialect\TransEdit\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;

class Locale extends Model{
	use Cachable;
	protected $table = 'transedit_locales';
	protected $guarded = ['id'];

	public function translations(){
		return $this->hasMany(Translation::class);
	}
}