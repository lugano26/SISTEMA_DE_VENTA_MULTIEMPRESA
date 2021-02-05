<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TPresentacion extends Model
{
	protected $table='tpresentacion';
	protected $primaryKey='codigoPresentacion';
	public $incrementing=false;
	public $timestamps=true;

	public function tAlmacenProducto()
	{
		return $this->hasMany('App\Model\TAlmacenProducto', 'codigoPresentacion');
	}

	public function tReciboCompraDetalle()
	{
		return $this->hasMany('App\Model\TReciboCompraDetalle', 'codigoPresentacion');
	}

	public function tProductoEnviarStockDealle()
	{
		return $this->hasMany('App\Model\TProductoEnviarStockDealle', 'codigoPresentacion');
	}
}
?>