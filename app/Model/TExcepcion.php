<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TExcepcion extends Model
{
	protected $table='texcepcion';
	protected $primaryKey='codigoExcepcion';
	public $incrementing=false;
	public $timestamps=true;

	public function tUsuario()
	{
		return $this->belongsTo('App\Model\TUsuario', 'codigoPersonal');
	}
}
?>