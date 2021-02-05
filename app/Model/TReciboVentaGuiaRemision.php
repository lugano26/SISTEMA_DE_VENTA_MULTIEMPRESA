<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TReciboVentaGuiaRemision extends Model
{
	protected $table='treciboventaguiaremision';
	protected $primaryKey='codigoReciboVentaGuiaRemision';
	public $incrementing=false;
	public $timestamps=true;

	public function tReciboVenta()
	{
		return $this->belongsTo('App\Model\TReciboVenta', 'codigoReciboVenta');
	}

	public function tUbigeoPartida()
	{
		return $this->belongsTo('App\Model\TUbigeo', 'ubigeoPartida', 'codigo');
	}

	public function tUbigeoLlegada()
	{
		return $this->belongsTo('App\Model\TUbigeo', 'ubigeoLlegada', 'codigo');
	}

	public function tReciboVentaGuiaRemisionDetalle()
	{
		return $this->hasMany('App\Model\TReciboVentaGuiaRemisionDetalle', 'codigoReciboVentaGuiaRemision');
	}
}
?>