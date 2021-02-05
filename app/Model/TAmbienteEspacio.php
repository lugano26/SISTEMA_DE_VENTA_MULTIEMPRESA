<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TAmbienteEspacio extends Model
{
	protected $table='tambienteespacio';
	protected $primaryKey='codigoAmbienteEspacio';
	public $incrementing=false;
	public $timestamps=true;

	public function tAmbiente()
	{
		return $this->belongsTo('App\Model\TAmbiente', 'codigoAmbiente');
	}

	public function tInventario()
	{
		return $this->hasMany('App\Model\TInventario', 'codigoAmbienteEspacio');
	}
}
?>