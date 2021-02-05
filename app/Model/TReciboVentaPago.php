<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TReciboVentaPago extends Model
{
	protected $table='treciboventapago';
	protected $primaryKey='codigoReciboVentaPago';
	public $incrementing=false;
	public $timestamps=true;

	public function tReciboVenta()
	{
		return $this->belongsTo('App\Model\TReciboVenta', 'codigoReciboVenta');
	}
}
?>