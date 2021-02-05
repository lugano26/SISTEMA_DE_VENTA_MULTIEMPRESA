<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TCaja extends Model
{
	protected $table='tcaja';
	protected $primaryKey='codigoCaja';
	public $incrementing=false;
	public $timestamps=true;

	public function tEmpresa()
	{
		return $this->belongsTo('App\Model\TEmpresa', 'codigoEmpresa');
	}

	public function tCajaDetalle()
	{
		return $this->hasMany('App\Model\TCajaDetalle', 'codigoCaja');
	}
}
?>