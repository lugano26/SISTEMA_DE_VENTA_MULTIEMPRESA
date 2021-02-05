<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TUnidadMedida extends Model
{
	protected $table='tunidadmedida';
	protected $primaryKey='codigoUnidadMedida';
	public $incrementing=false;
	public $timestamps=true;

	public function tReciboCompraDetalle()
	{
		return $this->hasMany('App\Model\TReciboCompraDetalle', 'codigoUnidadMedida');
	}

	public function tProductoEnviarStockDetalle()
	{
		return $this->hasMany('App\Model\TProductoEnviarStockDetalle', 'codigoUnidadMedida');
	}
}
?>