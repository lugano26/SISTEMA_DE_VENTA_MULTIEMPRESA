<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;

use DB;

use App\Model\TEgreso;
use App\Model\TCajaDetalle;

class EgresoController extends Controller
{
	public function actionInsertar(Request $request, SessionManager $sessionManager)
	{
		if($_POST)
		{
			try
			{
				DB::beginTransaction();

				if(!($sessionManager->has('codigoOficina')))
				{
					return $this->plataformHelper->redirectError('Debe estar logueado en una oficina para realizar esta operación.', 'egreso/insertar');
				}

				if(!($sessionManager->has('codigoCajaDetalle')))
				{
					return $this->plataformHelper->redirectError('Ocurrio un error, por favor contacte con el administrador del sistema.', '/');
				}

				if(floatval($request->input('txtMonto')) <= 0)
				{
					DB::rollBack();

					$request->flash();

					return $this->plataformHelper->redirectError('Por favor ingrese un monto mayor que 0.', 'egreso/insertar');
				}

				$tCajaDetalle = TCajaDetalle::find($sessionManager->get('codigoCajaDetalle'));

				$tCajaDetalle->egresos = $tCajaDetalle->egresos + $request->input('txtMonto');
				$tCajaDetalle->saldoFinal = ($tCajaDetalle->saldoInicial + $tCajaDetalle->ingresos) - $tCajaDetalle->egresos;

				$tCajaDetalle->save();

				if(
					trim($request->input('txtDescripcion'))==''
				)
				{
					DB::rollBack();

					$request->flash();

					return $this->plataformHelper->redirectError('Datos incorrectos. Por favor no trate de aleterar el comportamiento del sistema.', 'recibocompra/insertar');
				}

				$tEgreso=new TEgreso();

				$tEgreso->codigoOficina=$sessionManager->get('codigoOficina');
				$tEgreso->codigoPersonal=$sessionManager->get('codigoPersonal');
				$tEgreso->descripcion=trim($request->input('txtDescripcion'));
				$tEgreso->monto = $request->input('txtMonto');

				$tEgreso->save();

				DB::commit();

				return $this->plataformHelper->redirectCorrecto('Operación realizada correctamente.', 'egreso/insertar');
			}
			catch(\Exception $e)
			{
				DB::rollback();

				return $this->plataformHelper->capturarExcepcion(__CLASS__, __FUNCTION__, $e->getMessage(), '/');
			}
		}

		return view('egreso/insertar');
	}

	public function actionVer(Request $request, SessionManager $sessionManager, $pagina = 1)
	{
		if($request->input('q'))
		{
			$term = $request->input('q');
			
			$paginationPrepare = $this->plataformHelper->prepararPaginacion(TEgreso::with(['toficina', 'tpersonal'])
			->whereRaw('(compareFind(concat(descripcion), ?, 77)=1 or codigoOficina in (select codigoOficina from toficina where compareFind(concat(descripcion), ?, 77)=1) or codigoPersonal in (select codigoPersonal from tpersonal where compareFind(concat(nombre, apellido, correoElectronico), ?, 77)=1)) and (codigoPersonal=? or ? or ?)', [$term, $term, $term, $sessionManager->get('codigoPersonal'), strpos($sessionManager->get('rol'), 'Súper usuario')!==false ? 1 : 0, strpos($sessionManager->get('rol'), 'Adminisrador')!==false ? 1 : 0])
			->whereHas('toficina', function($query) use ($sessionManager)
			{
				$query->whereRaw('codigoEmpresa=?', [$sessionManager->get('codigoEmpresa')]);
			})
			->orderBy('created_at', 'desc'), null, $pagina);

			$paginationRender = $this->plataformHelper->renderizarPaginacion('egreso/ver', $paginationPrepare["cantidadPaginas"], $pagina, $request->input('q'));
	
			return view('egreso/ver', ['listaTEgreso' => $paginationPrepare["listaRegistros"], "pagination" => $paginationRender, "q" => $request->input('q')]);
		}

		$paginationPrepare = $this->plataformHelper->prepararPaginacion(TEgreso::with(['toficina', 'tpersonal'])
		->whereRaw('(codigoPersonal=? or ? or ?)', [$sessionManager->get('codigoPersonal'), strpos($sessionManager->get('rol'), 'Súper usuario')!==false ? 1 : 0, strpos($sessionManager->get('rol'), 'Adminisrador')!==false ? 1 : 0])
		->whereHas('toficina', function($query) use ($sessionManager)
		{
			$query->whereRaw('codigoEmpresa=?', [$sessionManager->get('codigoEmpresa')]);
		})->orderBy('created_at', 'desc'), null, $pagina);

		$paginationRender = $this->plataformHelper->renderizarPaginacion('egreso/ver', $paginationPrepare["cantidadPaginas"], $pagina);

		return view('egreso/ver', ['listaTEgreso' => $paginationPrepare["listaRegistros"], "pagination" => $paginationRender]);
	}
}
?>