<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TOficinaProducto extends Model
{
	protected $table='toficinaproducto';
	protected $primaryKey='codigoOficinaProducto';
	public $incrementing=false;
	public $timestamps=true;

	public function tOficina()
	{
		return $this->belongsTo('App\Model\TOficina', 'codigoOficina');
	}

	public function tOficinaProductoRetiro()
	{
		return $this->hasMany('App\Model\TOficinaProductoRetiro', 'codigoOficinaProducto');
	}

	public function tProductoTrasladoOficinaDetalle()
	{
		return $this->hasMany('App\Model\TProductoTrasladoOficinaDetalle', 'codigoOficinaProducto');
	}
}
?>