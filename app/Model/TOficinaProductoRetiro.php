<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TOficinaProductoRetiro extends Model
{
	protected $table='toficinaproductoretiro';
	protected $primaryKey='codigoOficinaProductoRetiro';
	public $incrementing=false;
	public $timestamps=true;

	public function tOficinaProducto()
	{
		return $this->belongsTo('App\Model\TOficinaProducto', 'codigoOficinaProducto');
	}

	public function tOficina()
	{
		return $this->belongsTo('App\Model\TOficina', 'codigoOficina');
	}
}
?>