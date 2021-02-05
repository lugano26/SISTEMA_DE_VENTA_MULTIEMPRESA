<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TEmpresaDeuda extends Model
{
	protected $table='tempresadeuda';
	protected $primaryKey='codigoEmpresaDeuda';
	public $incrementing=false;
	public $timestamps=true;

	public function tEmpresa()
	{
		return $this->belongsTo('App\Model\TEmpresa', 'codigoEmpresa');
	}
}
?>