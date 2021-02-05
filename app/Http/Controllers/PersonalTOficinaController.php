<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;

use DB;

use App\Model\TPersonalTOficina;

class PersonalTOficinaController extends Controller
{
	public function actionJSONPersonalTOficina(Request $request, SessionManager $sessionManager)
	{
		$listaTPersonalTOficina=TPersonalTOficina::with('tpersonal')->whereRaw('codigoPersonal in (select codigoPersonal from tpersonal where compareFind(concat(dni, nombre, apellido, correoElectronico), ?, 77)=1 and codigoEmpresa=? and cargo!=? ) limit 10', [$request->input('q'), $sessionManager->get('codigoEmpresa'), 'Súper usuario'])->get();

		$items=collect([]);

		foreach($listaTPersonalTOficina as $item)
		{
			if($items->contains('id', $item->tpersonal->codigoPersonal))
			{
				continue;
			}

			$items[]=(object) ['id' => $item->tpersonal->codigoPersonal, 'text' => ($item->tpersonal->nombre . ' '. $item->tpersonal->apellido), 'row' => $item];			
		}

		$result=['items' => $items];

		return response()->json($result);
	}
}
?>