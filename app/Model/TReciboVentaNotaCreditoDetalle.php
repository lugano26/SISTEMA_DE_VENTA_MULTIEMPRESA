<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TReciboVentaNotaCreditoDetalle extends Model
{
	protected $table='treciboventanotacreditodetalle';
	protected $primaryKey='codigoReciboVentaNotaCreditoDetalle';
	public $incrementing=false;
	public $timestamps=true;

	public function tReciboVentaNotaCredito()
	{
		return $this->belongsTo('App\Model\TReciboVentaNotaCredito', 'codigoReciboVentaNotaCredito');
	}
}
?>