<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TReciboVentaLetra extends Model
{
	protected $table='treciboventaletra';
	protected $primaryKey='codigoReciboVentaLetra';
	public $incrementing=false;
	public $timestamps=true;

	public function tReciboVenta()
	{
		return $this->belongsTo('App\Model\TReciboVenta', 'codigoReciboVenta');
	}
}
?>