<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TProductoEnviarStock extends Model
{
	protected $table='tproductoenviarstock';
	protected $primaryKey='codigoProductoEnviarStock';
	public $incrementing=false;
	public $timestamps=true;

	public function tOficina()
	{
		return $this->belongsTo('App\Model\TOficina', 'codigoOficina');
	}

	public function tAlmacen()
	{
		return $this->belongsTo('App\Model\TAlmacen', 'codigoAlmacen');
	}

	public function tProductoEnviarStockDetalle()
	{
		return $this->hasMany('App\Model\TProductoEnviarStockDetalle', 'codigoProductoEnviarStock');
	}
}
?>