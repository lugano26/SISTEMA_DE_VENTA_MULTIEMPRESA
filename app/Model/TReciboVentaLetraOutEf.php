<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TReciboVentaLetraOutEf extends Model
{
	protected $table='treciboventaletraoutef';
	protected $primaryKey='codigoReciboVentaLetraOutEf';
	public $incrementing=false;
	public $timestamps=true;

	public function tReciboVentaOutEf()
	{
		return $this->belongsTo('App\Model\TReciboVentaOutEf', 'codigoReciboVentaOutEf');
	}
}
?>