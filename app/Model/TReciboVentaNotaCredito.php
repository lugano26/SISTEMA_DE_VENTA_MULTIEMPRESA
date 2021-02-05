<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TReciboVentaNotaCredito extends Model
{
	protected $table='treciboventanotacredito';
	protected $primaryKey='codigoReciboVentaNotaCredito';
	public $incrementing=false;
	public $timestamps=true;

	public function tReciboVenta()
	{
		return $this->belongsTo('App\Model\TReciboVenta', 'codigoReciboVenta');
	}

	public function tOficina()
	{
		return $this->belongsTo('App\Model\TOficina', 'codigoOficina');
	}

	public function tUsuario()
	{
		return $this->belongsTo('App\Model\TUsuario', 'codigoUsuario');
	}

	public function tPersonal()
	{
		return $this->belongsTo('App\Model\TPersonal', 'codigoPersonal');
	}

	public function tReciboVentaNotaCreditoDetalle()
	{
		return $this->hasMany('App\Model\TReciboVentaNotaCreditoDetalle', 'codigoReciboVentaNotaCredito');
	}
}
?>