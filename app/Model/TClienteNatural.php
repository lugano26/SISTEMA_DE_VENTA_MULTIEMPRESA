<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TClienteNatural extends Model
{
	protected $table='tclientenatural';
	protected $primaryKey='codigoClienteNatural';
	public $incrementing=false;
	public $timestamps=true;

	public function tOficina()
	{
		return $this->belongsTo('App\Model\TOficina', 'codigoOficina');
	}
}
?>