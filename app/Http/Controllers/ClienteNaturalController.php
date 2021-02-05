<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;

use DB;

use App\Model\TClienteNatural;

class ClienteNaturalController extends Controller
{
	public function actionJSONPorDni(Request $request, SessionManager $sessionManager)
	{
		$tClienteNatural=TClienteNatural::whereRaw("dni=replace(?, ' ', '') and codigoOficina=?", [$request->input('dni'), $sessionManager->get('codigoOficina')])->first();
		$tClienteNatural=$tClienteNatural!=null ? $tClienteNatural : TClienteNatural::whereRaw("dni=replace(?, ' ', '')", [$request->input('dni')])->first();

		return response()->json($tClienteNatural);
	}
}
?>