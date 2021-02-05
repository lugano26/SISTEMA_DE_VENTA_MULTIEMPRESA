<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TProductoTrasladoOficinaDetalle extends Model
{
	protected $table='tproductotrasladooficinadetalle';
	protected $primaryKey='codigoProductoTrasladoOficinaDetalle';
	public $incrementing=false;
	public $timestamps=true;

	public function tProductoTrasladoOficina()
	{
		return $this->belongsTo('App\Model\TProductoTrasladoOficina', 'codigoProductoTrasladoOficina');
	}

	public function tOficinaProducto()
	{
		return $this->belongsTo('App\Model\TOficinaProducto', 'codigoOficinaProducto');
	}
}
?>