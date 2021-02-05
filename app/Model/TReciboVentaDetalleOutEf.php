<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TReciboVentaDetalleOutEf extends Model
{
	protected $table='treciboventadetalleoutef';
	protected $primaryKey='codigoReciboVentaDetalleOutEf';
	public $incrementing=false;
	public $timestamps=true;

	public function tReciboVentaOutEf()
	{
		return $this->belongsTo('App\Model\TReciboVentaOutEf', 'codigoReciboVentaOutEf');
	}
}
?>