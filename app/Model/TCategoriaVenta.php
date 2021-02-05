<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TCategoriaVenta extends Model
{
	protected $table='tcategoriaventa';
	protected $primaryKey='codigoCategoriaVenta';
	public $incrementing=false;
	public $timestamps=true;

	public function tCategoriaVenta()
	{
		return $this->belongsTo('App\Model\TCategoriaVenta', 'codigoCategoriaVentaPadre', 'codigoCategoriaVenta');
	}

	public function tEmpresa()
	{
		return $this->belongsTo('App\Model\TEmpresa', 'codigoEmpresa');
	}

	public function tCategoriaVentaChild()
	{
		return $this->hasMany('App\Model\TCategoriaVenta', 'codigoCategoriaVentaPadre', 'codigoCategoriaVenta');
	}

	public function tReciboVentaOutEf()
	{
		return $this->hasMany('App\Model\TReciboVentaOutEf', 'codigoCategoriaVenta');
	}

	public function tReciboVenta()
	{
		return $this->hasMany('App\Model\TReciboVenta', 'codigoCategoriaVenta');
	}
}
?>