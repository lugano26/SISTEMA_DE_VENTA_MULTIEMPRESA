<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TCajaDetalle extends Model
{
	protected $table='tcajadetalle';
	protected $primaryKey='codigoCajaDetalle';
	public $incrementing=false;
	public $timestamps=true;

	public function tCaja()
	{
		return $this->belongsTo('App\Model\TCaja', 'codigoCaja');
	}

	public function tPersonal()
	{
		return $this->belongsTo('App\Model\TPersonal', 'codigoPersonal');
	}
}
?>