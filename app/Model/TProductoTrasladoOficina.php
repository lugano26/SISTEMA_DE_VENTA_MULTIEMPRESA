<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TProductoTrasladoOficina extends Model
{
	protected $table='tproductotrasladooficina';
	protected $primaryKey='codigoProductoTrasladoOficina';
	public $incrementing=false;
	public $timestamps=true;

	public function tOficina()
	{
		return $this->belongsTo('App\Model\TOficina', 'codigoOficina');
	}

	public function tOficinaLlegada()
	{
		return $this->belongsTo('App\Model\TOficina', 'codigoOficinaLlegada');
	}

	public function tProductoTrasladoOficinaDetalle()
	{
		return $this->hasMany('App\Model\TProductoTrasladoOficinaDetalle', 'codigoProductoTrasladoOficina');
	}
}
?>