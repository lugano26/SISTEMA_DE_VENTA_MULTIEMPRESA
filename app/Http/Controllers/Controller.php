<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use App\Helper\PlataformHelper;
use App\Object\DtoMessage;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	protected $mensajeGlobal;
    protected $plataformHelper;

	protected $_so;

    public function __construct()
    {
    	$this->mensajeGlobal='';
    	$this->plataformHelper=new PlataformHelper();

		$this->_so=new \stdClass();
		
		$this->_so->mo=new DtoMessage();

		$this->_so->dto=null;
		$this->_so->listDto=null;
    }
}
