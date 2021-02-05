<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TClienteJuridicoRepresentante extends Model
{
	protected $table='tclientejuridicorepresentante';
	protected $primaryKey='codigoClienteJuridicoRepresentante';
	public $incrementing=false;
	public $timestamps=true;

	public function tClienteJuridico()
	{
		return $this->belongsTo('App\Model\TClienteJuridico', 'codigoClienteJuridico');
	}
}
?>