<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TProductoEnviarStockDetalle extends Model
{
	protected $table='tproductoenviarstockdetalle';
	protected $primaryKey='codigoProductoEnviarStockDetalle';
	public $incrementing=false;
	public $timestamps=true;

	public function tProductoEnviarStock()
	{
		return $this->belongsTo('App\Model\TProductoEnviarStock', 'codigoProductoEnviarStock');
	}

	public function tAlmacenProducto()
	{
		return $this->belongsTo('App\Model\TAlmacenProducto', 'codigoAlmacenProducto');
	}

	public function tPresentacion()
	{
		return $this->belongsTo('App\Model\TPresentacion', 'codigoPresentacion');
	}

	public function tUnidadMedida()
	{
		return $this->belongsTo('App\Model\TUnidadMedida', 'codigoUnidadMedida');
	}
}
?>