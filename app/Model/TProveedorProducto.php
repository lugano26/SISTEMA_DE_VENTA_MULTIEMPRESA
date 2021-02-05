<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TProveedorProducto extends Model
{
	protected $table='tproveedorproducto';
	protected $primaryKey='codigoProveedorProducto';
	public $incrementing=false;
	public $timestamps=true;

	public function tProveedor()
	{
		return $this->belongsTo('App\Model\TProveedor', 'codigoProveedor');
	}
}
?>