<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TAmbiente extends Model
{
	protected $table='tambiente';
	protected $primaryKey='codigoAmbiente';
	public $incrementing=false;
	public $timestamps=true;

	public function tOficina()
	{
		return $this->belongsTo('App\Model\TOficina', 'codigoOficina');
	}

	public function tAlmacen()
	{
		return $this->belongsTo('App\Model\TAlmacen', 'codigoAlmacen');
	}

	public function tAmbienteEspacio()
	{
		return $this->hasMany('App\Model\TAmbienteEspacio', 'codigoAmbiente');
	}
}
?>