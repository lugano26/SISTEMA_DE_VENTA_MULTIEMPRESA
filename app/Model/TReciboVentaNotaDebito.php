<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TReciboVentaNotaDebito extends Model
{
	protected $table='treciboventanotadebito';
	protected $primaryKey='codigoReciboVentaNotaDebito';
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
		return $this->belongsTo('App\Model\TUsuario', 'codigoPersonal');
	}
	
	public function tPersonal()
	{
		return $this->belongsTo('App\Model\TPersonal', 'codigoPersonal');
	}
	
	public function tReciboVentaNotaDebitoDetalle()
	{
		return $this->hasMany('App\Model\TReciboVentaNotaDebitoDetalle', 'codigoReciboVentaNotaDebito');
	}
}
?>