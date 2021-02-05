<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TAlmacenProductoRetiro extends Model
{
	protected $table='talmacenproductoretiro';
	protected $primaryKey='codigoAlmacenProductoRetiro';
	public $incrementing=false;
	public $timestamps=true;

	public function tAlmacenProducto()
	{
		return $this->belongsTo('App\Model\TAlmacenProducto', 'codigoAlmacenProducto');
	}

	public function tAlmacen()
	{
		return $this->belongsTo('App\Model\TAlmacen', 'codigoAlmacen');
	}
}
?>