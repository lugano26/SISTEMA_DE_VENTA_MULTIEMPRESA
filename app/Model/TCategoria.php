<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TCategoria extends Model
{
	protected $table='tcategoria';
	protected $primaryKey='codigoCategoria';
	public $incrementing=false;
	public $timestamps=true;

	public function tAlmacenProductoTCategoria()
	{
		return $this->hasMany('App\Model\TAlmacenProductoTCategoria', 'codigoCategoria');
	}
}
?>