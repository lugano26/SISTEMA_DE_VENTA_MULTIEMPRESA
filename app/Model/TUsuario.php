<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TUsuario extends Model
{
	protected $table='tusuario';
	protected $primaryKey='codigoPersonal';
	public $incrementing=false;
	public $timestamps=true;

	public function tPersonal()
	{
		return $this->belongsTo('App\Model\TPersonal', 'codigoPersonal');
	}

	public function tReciboVenta()
	{
		return $this->hasMany('App\Model\TReciboVenta', 'codigoPersonal');
	}

	public function tReciboVentaNotaDebito()
	{
		return $this->hasMany('App\Model\TReciboVentaNotaDebito', 'codigoPersonal');
	}

	public function tReciboVentaNotaCredito()
	{
		return $this->hasMany('App\Model\TReciboVentaNotaCredito', 'codigoPersonal');
	}

	public function tExcepcion()
	{
		return $this->hasMany('App\Model\TExcepcion', 'codigoPersonal');
	}
}
?>