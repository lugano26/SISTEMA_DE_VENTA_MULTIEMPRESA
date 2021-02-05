<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TReciboVentaNotaDebitoDetalle extends Model
{
	protected $table='treciboventanotadebitodetalle';
	protected $primaryKey='codigoReciboVentaNotaDebitoDetalle';
	public $incrementing=false;
	public $timestamps=true;

	public function tReciboVentaNotaDebito()
	{
		return $this->belongsTo('App\Model\TReciboVentaNotaDebito', 'codigoReciboVentaNotaDebito');
	}
}
?>