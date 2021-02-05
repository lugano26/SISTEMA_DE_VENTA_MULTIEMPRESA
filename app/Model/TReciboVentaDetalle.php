<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TReciboVentaDetalle extends Model
{
	protected $table='treciboventadetalle';
	protected $primaryKey='codigoReciboVentaDetalle';
	public $incrementing=false;
	public $timestamps=true;

	public function tReciboVenta()
	{
		return $this->belongsTo('App\Model\TReciboVenta', 'codigoReciboVenta');
	}
}
?>