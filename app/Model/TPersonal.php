<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TPersonal extends Model
{
	protected $table='tpersonal';
	protected $primaryKey='codigoPersonal';
	public $incrementing=false;
	public $timestamps=true;

	public function tUsuario()
	{
		return $this->hasOne('App\Model\TUsuario', 'codigoPersonal');
	}

	public function tEmpresa()
	{
		return $this->belongsTo('App\Model\TEmpresa', 'codigoEmpresa');
	}

	public function tPersonalTOficina()
	{
		return $this->hasMany('App\Model\TPersonalTOficina', 'codigoPersonal');
	}

	public function tPersonalTAlmacen()
	{
		return $this->hasMany('App\Model\TPersonalTAlmacen', 'codigoPersonal');
	}

	public function tCajaDetalle()
	{
		return $this->hasMany('App\Model\TCajaDetalle', 'codigoPersonal');
	}

	public function tEgreso()
	{
		return $this->hasMany('App\Model\TEgreso', 'codigoPersonal');
	}
}
?>