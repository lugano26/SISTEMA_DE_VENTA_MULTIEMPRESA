<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TOficina extends Model
{
	protected $table='toficina';
	protected $primaryKey='codigoOficina';
	public $incrementing=false;
	public $timestamps=true;

	public function tEmpresa()
	{
		return $this->belongsTo('App\Model\TEmpresa', 'codigoEmpresa');
	}

	public function tPersonalTOficina()
	{
		return $this->hasMany('App\Model\TPersonalTOficina', 'codigoOficina');
	}

	public function tClienteNatural()
	{
		return $this->hasMany('App\Model\TClienteNatural', 'codigoOficina');
	}

	public function tClienteJuridico()
	{
		return $this->hasMany('App\Model\TClienteJuridico', 'codigoOficina');
	}

	public function tOficinaProducto()
	{
		return $this->hasMany('App\Model\TOficinaProducto', 'codigoNombre');
	}

	public function tReciboVenta()
	{
		return $this->hasMany('App\Model\TReciboVenta', 'codigoOficina');
	}

	public function tReciboVentaNotaDebito()
	{
		return $this->hasMany('App\Model\TReciboVentaNotaDebito', 'codigoOficina');
	}

	public function tReciboVentaNotaCredito()
	{
		return $this->hasMany('App\Model\TReciboVentaNotaCredito', 'codigoOficina');
	}

	public function tOficinaProductoRetiro()
	{
		return $this->hasMany('App\Model\TOficinaProductoRetiro', 'codigoOficina');
	}

	public function tProductoEnviarStock()
	{
		return $this->hasMany('App\Model\TProductoEnviarStock', 'codigoOficina');
	}

	public function tProductoTrasladoOficina()
	{
		return $this->hasMany('App\Model\TProductoTrasladoOficina', 'codigoOficina');
	}

	public function tProductoTrasladoOficinaLlegada()
	{
		return $this->hasMany('App\Model\TProductoTrasladoOficinaLlegada', 'codigoOficinaLlegada');
	}

	public function tAmbiente()
	{
		return $this->hasMany('App\Model\TAmbiente', 'codigoOficina');
	}
}
?>