<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TReciboVentaGuiaRemisionDetalle extends Model
{
	protected $table='treciboventaguiaremisiondetalle';
	protected $primaryKey='codigoReciboVentaGuiaRemisionDetalle';
	public $incrementing=false;
	public $timestamps=true;

	public function tReciboVentaGuiaRemision()
	{
		return $this->belongsTo('App\Model\TReciboVentaGuiaRemision', 'codigoReciboVentaGuiaRemision');
	}
}
?>