<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TDocumentoGeneradoSunat extends Model
{
	protected $table='tdocumentogeneradosunat';
	protected $primaryKey='codigoDocumentoGeneradoSunat';
	public $incrementing=false;
	public $timestamps=true;

	public function tEmpresa()
	{
		return $this->belongsTo('App\Model\TEmpresa', 'codigoEmpresa');
	}
}
?>