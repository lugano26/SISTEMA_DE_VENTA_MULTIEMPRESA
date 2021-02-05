<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TReciboVentaOutEf extends Model
{
	protected $table='treciboventaoutef';
	protected $primaryKey='codigoReciboVentaOutEf';
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

	public function tReciboVenta()
	{
		return $this->hasOne('App\Model\TReciboVenta', 'codigoReciboVentaOutEf');
	}

	public function tReciboVentaDetalleOutEf()
	{
		return $this->hasMany('App\Model\TReciboVentaDetalleOutEf', 'codigoReciboVentaOutEf');
    }
    
	public function tReciboVentaPagoOutEf()
	{
		return $this->hasMany('App\Model\TReciboVentaPagoOutEf', 'codigoReciboVentaOutEf');
	}

	public function tReciboVentaLetraOutEf()
	{
		return $this->hasMany('App\Model\TReciboVentaLetraOutEf', 'codigoReciboVentaOutEf');
	}
}
?>