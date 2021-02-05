<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TEmpresa extends Model
{
	protected $table='tempresa';
	protected $primaryKey='codigoEmpresa';
	public $incrementing=false;
	public $timestamps=true;

	public function tEmpresaDeuda()
	{
		return $this->hasMany('App\Model\TEmpresaDeuda', 'codigoEmpresa');
	}

	public function tOficina()
	{
		return $this->hasMany('App\Model\TOficina', 'codigoEmpresa');
	}

	public function tAlmacen()
	{
		return $this->hasMany('App\Model\TAlmacen', 'codigoEmpresa');
	}

	public function tPersonal()
	{
		return $this->hasMany('App\Model\TPersonal', 'codigoEmpresa');
	}

	public function tProveedor()
	{
		return $this->hasMany('App\Model\TProveedor', 'codigoEmpresa');
	}

	public function tDocumentoGeneradoSunat()
	{
		return $this->hasMany('App\Model\TDocumentoGeneradoSunat', 'codigoEmpresa');
	}

	public function tResumenDiario()
	{
		return $this->hasMany('App\Model\TResumenDiario', 'codigoEmpresa');
	}

	public function tCaja()
	{
		return $this->hasMany('App\Model\TCaja', 'codigoEmpresa');
	}

	public function tCategoriaVenta()
	{
		return $this->hasMany('App\Model\TCategoriaVenta', 'codigoEmpresa');
	}
}
?>