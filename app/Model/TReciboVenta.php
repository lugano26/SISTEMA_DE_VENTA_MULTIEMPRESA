<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TReciboVenta extends Model
{
	protected $table='treciboventa';
	protected $primaryKey='codigoReciboVenta';
	public $incrementing=false;
	public $timestamps=true;

	public function tOficina()
	{
		return $this->belongsTo('App\Model\TOficina', 'codigoOficina');
	}

	public function tPersonal()
	{
		return $this->belongsTo('App\Model\TPersonal', 'codigoPersonal');
	}

	public function tCategoriaVenta()
	{
		return $this->belongsTo('App\Model\TCategoriaVenta', 'codigoCategoriaVenta');
	}

	public function tReciboVentaDetalle()
	{
		return $this->hasMany('App\Model\TReciboVentaDetalle', 'codigoReciboVenta');
	}

	public function tReciboVentaGuiaRemision()
	{
		return $this->hasMany('App\Model\TReciboVentaGuiaRemision', 'codigoReciboVenta');
	}

	public function tReciboVentaNotaDebito()
	{
		return $this->hasMany('App\Model\TReciboVentaNotaDebito', 'codigoReciboVenta');
	}

	public function tReciboVentaNotaCredito()
	{
		return $this->hasMany('App\Model\TReciboVentaNotaCredito', 'codigoReciboVenta');
	}

	public function tReciboVentaPago()
	{
		return $this->hasMany('App\Model\TReciboVentaPago', 'codigoReciboVenta');
	}

	public function tReciboVentaLetra()
	{
		return $this->hasMany('App\Model\TReciboVentaLetra', 'codigoReciboVenta');
	}
}
?>