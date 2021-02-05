<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TPersonalTAlmacen extends Model
{
	protected $table='tpersonaltalmacen';
	protected $primaryKey='codigoPersonalTAlmacen';
	public $incrementing=false;
	public $timestamps=true;

	public function tPersonal()
	{
		return $this->belongsTo('App\Model\TPersonal', 'codigoPersonal');
	}

	public function tAlmacen()
	{
		return $this->belongsTo('App\Model\TAlmacen', 'codigoAlmacen');
	}
}
?>