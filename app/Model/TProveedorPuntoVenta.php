<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TProveedorPuntoVenta extends Model
{
	protected $table='tproveedorpuntoventa';
	protected $primaryKey='codigoProveedorPuntoVenta';
	public $incrementing=false;
	public $timestamps=true;

	public function tProveedor()
	{
		return $this->belongsTo('App\Model\TProveedor', 'codigoProveedor');
	}
}
?>