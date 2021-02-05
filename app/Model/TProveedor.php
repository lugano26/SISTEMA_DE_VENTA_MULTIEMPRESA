<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TProveedor extends Model
{
	protected $table='tproveedor';
	protected $primaryKey='codigoProveedor';
	public $incrementing=false;
	public $timestamps=true;

	public function tEmpresa()
	{
		return $this->belongsTo('App\Model\TEmpresa', 'codigoEmpresa');
	}

	public function tProveedorPuntoVenta()
	{
		return $this->hasMany('App\Model\TProveedorPuntoVenta', 'codigoProveedor');
	}

	public function tProveedorProducto()
	{
		return $this->hasMany('App\Model\TProveedorProducto', 'codigoProveedor');
	}

	public function tReciboCompra()
	{
		return $this->hasMany('App\Model\TReciboCompra', 'codigoProveedor');
	}
}
?>