<?php
namespace Dialect\TransEdit\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;

class Translation extends Model{
	use Cachable;
	protected $table = 'transedit_translations';
	protected $guarded = ['id'];

	public function locale(){
		return $this->belongsTo(Locale::class);
	}

	public function key(){
		return $this->belongsTo(key::class);
	}

}