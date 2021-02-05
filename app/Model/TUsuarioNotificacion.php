<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TUsuarioNotificacion extends Model
{
	protected $table='tusuarionotificacion';
	protected $primaryKey='codigoUsuarioNotificacion';
	public $incrementing=false;
	public $timestamps=true;

	public function tPersonal()
	{
		return $this->belongsTo('App\Model\TPersonal', 'codigoPersonal');
	}
}
?>