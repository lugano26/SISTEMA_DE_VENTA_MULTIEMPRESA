<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TClienteJuridico extends Model
{
	protected $table='tclientejuridico';
	protected $primaryKey='codigoClienteJuridico';
	public $incrementing=false;
	public $timestamps=true;

	public function tOficina()
	{
		return $this->belongsTo('App\Model\TOficina', 'codigoOficina');
	}

	public function tClienteJuridicoRepresentante()
	{
		return $this->hasMany('App\Model\TClienteJuridicoRepresentante', 'codigoClienteJuridico');
	}
}
?>