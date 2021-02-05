<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;

use DB;

use App\Model\TClienteJuridico;

class ClienteJuridicoController extends Controller
{
	public function actionJSONPorRuc(Request $request)
	{
		$tClienteJuridico=TClienteJuridico::whereRaw("ruc=replace(?, ' ', '')", [$request->input('ruc')])->first();

		return response()->json($tClienteJuridico);
	}

	public function actionJSONPorRazonSocialLargaParaVenta(Request $request, SessionManager $sessionManager)
	{
		$listaTClienteJuridico=[];

		if($request->input('searchPerformance')=='Performance')
		{
			$listaTClienteJuridico=TClienteJuridico::whereRaw('codigoOficina=? and replace(razonSocialLarga, \' \', \'\') like replace(?, \' \', \'\') limit 10', [$sessionManager->get('codigoOficina'), '%'.$request->input('q').'%'])->get();
			$listaTClienteJuridico=count($listaTClienteJuridico)>0 ? $listaTClienteJuridico : TClienteJuridico::whereRaw('replace(razonSocialLarga, \' \', \'\') like replace(?, \' \', \'\') limit 10', ['%'.$request->input('q').'%'])->get();
		}
		else
		{
			$listaTClienteJuridico=TClienteJuridico::whereRaw('codigoOficina=? and compareFind(razonSocialLarga, ?, 77)=1 limit 10', [$sessionManager->get('codigoOficina'), $request->input('q')])->get();
			$listaTClienteJuridico=count($listaTClienteJuridico)>0 ? $listaTClienteJuridico : TClienteJuridico::whereRaw('compareFind(razonSocialLarga, ?, 77)=1 limit 10', [$request->input('q')])->get();
		}

		$items=[];

		foreach($listaTClienteJuridico as $item)
		{
			$items[]=['id' => $item->razonSocialLarga, 'text' => $item->razonSocialLarga, 'row' => $item];
		}

		$result=['items' => $items];

		return response()->json($result);
	}
}
?>