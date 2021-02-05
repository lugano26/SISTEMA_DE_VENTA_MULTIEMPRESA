<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TReciboCompraDetalle extends Model
{
	protected $table='trecibocompradetalle';
	protected $primaryKey='codigoReciboCompraDetalle';
	public $incrementing=false;
	public $timestamps=true;

	public function tPresentacion()
	{
		return $this->belongsTo('App\Model\TPresentacion', 'codigoPresentacion');
	}

	public function tUnidadMedida()
	{
		return $this->belongsTo('App\Model\TUnidadMedida', 'codigoUnidadMedida');
	}

	public function tReciboCompra()
	{
		return $this->belongsTo('App\Model\TReciboCompra', 'codigoReciboCompra');
	}
}
?>