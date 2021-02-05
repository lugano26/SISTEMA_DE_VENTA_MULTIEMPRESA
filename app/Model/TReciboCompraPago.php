<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TReciboCompraPago extends Model
{
	protected $table='trecibocomprapago';
	protected $primaryKey='codigoReciboCompraPago';
	public $incrementing=false;
	public $timestamps=true;

	public function tReciboCompra()
	{
		return $this->belongsTo('App\Model\TReciboCompra', 'codigoReciboCompra');
	}
}
?>