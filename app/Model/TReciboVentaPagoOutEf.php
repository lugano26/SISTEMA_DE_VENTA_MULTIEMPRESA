<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TReciboVentaPagoOutEf extends Model
{
	protected $table='treciboventapagooutef';
	protected $primaryKey='codigoReciboVentaPagoOutEf';
	public $incrementing=false;
	public $timestamps=true;

	public function tReciboVentaOutEf()
	{
		return $this->belongsTo('App\Model\TReciboVentaOutEf', 'codigoReciboVentaOutEf');
	}
}
?>