<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TAlmacenProductoTCategoria extends Model
{
	protected $table='talmacenproductotcategoria';
	protected $primaryKey='codigoAlmacenProductoTCategoria';
	public $incrementing=false;
	public $timestamps=true;

	public function tAlmacenProducto()
	{
		return $this->belongsTo('App\Model\TAlmacenProducto', 'codigoAlmacenProducto');
	}

	public function tCategoria()
	{
		return $this->belongsTo('App\Model\TCategoria', 'codigoCategoria');
	}
}
?>