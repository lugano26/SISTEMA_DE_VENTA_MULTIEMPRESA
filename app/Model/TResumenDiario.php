<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TResumenDiario extends Model
{
	protected $table='tresumendiario';
	protected $primaryKey='codigoResumenDiario';
	public $incrementing=false;
	public $timestamps=true;

	public function tEmpresa()
	{
		return $this->belongsTo('App\Model\TEmpresa', 'codigoEmpresa');
	}
}
?>