<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use DB;

use App\Model\TUbigeo;

class UbigeoController extends Controller
{
	public function actionJSONPorUbicacion(Request $request)
	{
		$listaTUbigeo=TUbigeo::whereRaw('compareFind(ubicacion, ?, 77)=1 limit 10', [$request->input('q')])->get();

		$items=[];

		foreach($listaTUbigeo as $item)
		{
			$items[]=['id' => $item->codigo, 'text' => $item->ubicacion, 'row' => $item];
		}

		$result=['items' => $items];

		return response()->json($result);
	}
}
?>