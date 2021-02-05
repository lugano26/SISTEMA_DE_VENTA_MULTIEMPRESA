<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TPersonalTOficina extends Model
{
	protected $table='tpersonaltoficina';
	protected $primaryKey='codigoPersonalTOficina';
	public $incrementing=false;
	public $timestamps=true;

	public function tPersonal()
	{
		return $this->belongsTo('App\Model\TPersonal', 'codigoPersonal');
	}

	public function tOficina()
	{
		return $this->belongsTo('App\Model\TOficina', 'codigoOficina');
	}
}
?>