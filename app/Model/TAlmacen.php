<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TAlmacen extends Model
{
	protected $table='talmacen';
	protected $primaryKey='codigoAlmacen';
	public $incrementing=false;
	public $timestamps=true;

	public function tEmpresa()
	{
		return $this->belongsTo('App\Model\TEmpresa', 'codigoEmpresa');
	}

	public function tPersonalTAlmacen()
	{
		return $this->hasMany('App\Model\TPersonalTAlmacen', 'codigoAlmacen');
	}

	public function tAlmacenProducto()
	{
		return $this->hasMany('App\Model\TAlmacenProducto', 'codigoAlmacen');
	}

	public function tAlmacenProductoRetiro()
	{
		return $this->hasMany('App\Model\TAlmacenProductoRetiro', 'codigoAlmacen');
	}

	public function tReciboCompra()
	{
		return $this->hasMany('App\Model\TReciboCompra', 'codigoAlmacen');
	}

	public function tProductoEnviarStock()
	{
		return $this->hasMany('App\Model\TProductoEnviarStock', 'codigoAlmacen');
	}

	public function tAmbiente()
	{
		return $this->hasMany('App\Model\TAmbiente', 'codigoAlmacen');
	}
}
?>