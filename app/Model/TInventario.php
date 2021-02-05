<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TInventario extends Model
{
	protected $table='tinventario';
	protected $primaryKey='codigoInventario';
	public $incrementing=false;
	public $timestamps=true;

	public function tAmbienteEspacio()
	{
		return $this->belongsTo('App\Model\TAmbienteEspacio', 'codigoAmbienteEspacio');
	}
}
?>