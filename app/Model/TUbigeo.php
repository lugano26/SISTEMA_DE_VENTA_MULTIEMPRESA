<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TUbigeo extends Model
{
	protected $table='tubigeo';
	protected $primaryKey='codigoUbigeo';
	public $incrementing=false;
	public $timestamps=true;
}
?>