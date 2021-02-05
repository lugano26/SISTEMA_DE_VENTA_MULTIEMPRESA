<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TAlmacenProducto extends Model
{
	protected $table='talmacenproducto';
	protected $primaryKey='codigoAlmacenProducto';
	public $incrementing=false;
	public $timestamps=true;

	public function tAlmacen()
	{
		return $this->belongsTo('App\Model\TAlmacen', 'codigoAlmacen');
	}

	public function tPresentacion()
	{
		return $this->belongsTo('App\Model\TPresentacion', 'codigoPresentacion');
	}

	public function tUnidadMedida()
	{
		return $this->belongsTo('App\Model\TUnidadMedida', 'codigoUnidadMedida');
	}

	public function tAlmacenProductoTCategoria()
	{
		return $this->hasMany('App\Model\TAlmacenProductoTCategoria', 'codigoAlmacenProducto');
	}

	public function tAlmacenProductoRetiro()
	{
		return $this->hasMany('App\Model\TAlmacenProductoRetiro', 'codigoAlmacenProducto');
	}

	public function tProductoEnviarStockDetalle()
	{
		return $this->hasMany('App\Model\TProductoEnviarStockDetalle', 'codigoAlmacenProducto');
	}
}
?>