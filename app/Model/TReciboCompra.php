<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TReciboCompra extends Model
{
	protected $table='trecibocompra';
	protected $primaryKey='codigoReciboCompra';
	public $incrementing=false;
	public $timestamps=true;

	public function tProveedor()
	{
		return $this->belongsTo('App\Model\TProveedor', 'codigoProveedor');
	}

	public function tAlmacen()
	{
		return $this->belongsTo('App\Model\TAlmacen', 'codigoAlmacen');
	}

	public function tReciboCompraDetalle()
	{
		return $this->hasMany('App\Model\TReciboCompraDetalle', 'codigoReciboCompra');
	}

	public function tReciboCompraPago()
	{
		return $this->hasMany('App\Model\TReciboCompraPago', 'codigoReciboCompra');
	}
}
?>