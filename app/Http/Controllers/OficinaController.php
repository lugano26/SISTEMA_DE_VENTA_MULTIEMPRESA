<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;

use App\Validation\OficinaValidation;

use DB;

use App\Model\TOficina;
use App\Model\TPersonal;
use App\Model\TPersonalTOficina;

class OficinaController extends Controller
{
	public function actionInsertar(Request $request, SessionManager $sessionManager)
	{
		if($_POST)
		{
			try
			{
				DB::beginTransaction();

				$this->mensajeGlobal=(new OficinaValidation())->validationInsertar($request);

				if($this->mensajeGlobal!='')
				{
					DB::rollBack();

					$request->flash();

					return $this->plataformHelper->redirectError($this->mensajeGlobal, 'oficina/insertar');
				}

				$tOficina=new TOficina();

				$tOficina->codigoEmpresa=$sessionManager->get('codigoEmpresa');
				$tOficina->descripcion=trim($request->input('txtDescripcion'));
				$tOficina->pais=trim($request->input('txtPais'));
				$tOficina->departamento=trim($request->input('txtDepartamento'));
				$tOficina->provincia=trim($request->input('txtProvincia'));
				$tOficina->distrito=trim($request->input('txtDistrito'));
				$tOficina->direccion=trim($request->input('txtDireccion'));
				$tOficina->descripcionComercialComprobante=trim($request->input('txtDescripcionComercialComprobante'));
				$tOficina->manzana='';
				$tOficina->lote='';
				$tOficina->numeroVivienda=trim($request->input('txtNumeroVivienda'));
				$tOficina->numeroInterior='';
				$tOficina->telefono=trim($request->input('txtTelefono'));
				$tOficina->fechaCreacion='1111-11-11';
				$tOficina->estado=true;

				$tOficina->save();

				DB::commit();

				return $this->plataformHelper->redirectCorrecto('Operación realizada correctamente.', 'oficina/ver');
			}
			catch(\Exception $e)
			{
				DB::rollback();

				return $this->plataformHelper->capturarExcepcion(__CLASS__, __FUNCTION__, $e->getMessage(), '/');
			}
		}

		return view('oficina/insertar');
	}

	public function actionVer(SessionManager $sessionManager)
	{
		$listaTOficina=TOficina::whereRaw('codigoEmpresa=?', [$sessionManager->get('codigoEmpresa')])->get();

		return view('oficina/ver', ['listaTOficina' => $listaTOficina]);
	}

	public function actionEditar(Request $request, SessionManager $sessionManager)
	{
		if($request->has('hdCodigoOficina'))
		{
			try
			{
				DB::beginTransaction();

				$tOficina=TOficina::find($request->input('hdCodigoOficina'));

				if(!($this->plataformHelper->verificarExistenciaAutorizacion($tOficina, 'codigoEmpresa', $sessionManager->get('codigoEmpresa'), $mensajeOut)))
				{
					return $this->plataformHelper->redirectError($mensajeOut, 'oficina/ver');
				}

				$this->mensajeGlobal=(new OficinaValidation())->validationEditar($request);

				if($this->mensajeGlobal!='')
				{
					DB::rollBack();

					return $this->plataformHelper->redirectError($this->mensajeGlobal, 'oficina/ver');
				}

				$tOficina->descripcion=trim($request->input('txtDescripcion'));
				$tOficina->pais=trim($request->input('txtPais'));
				$tOficina->departamento=trim($request->input('txtDepartamento'));
				$tOficina->provincia=trim($request->input('txtProvincia'));
				$tOficina->distrito=trim($request->input('txtDistrito'));
				$tOficina->direccion=trim($request->input('txtDireccion'));
				$tOficina->numeroVivienda=trim($request->input('txtNumeroVivienda'));
				$tOficina->descripcionComercialComprobante=trim($request->input('txtDescripcionComercialComprobante'));
				$tOficina->telefono=trim($request->input('txtTelefono'));

				$tOficina->save();

				DB::commit();

				return $this->plataformHelper->redirectCorrecto('Operación realizada correctamente.', 'oficina/ver');
			}
			catch(\Exception $e)
			{
				DB::rollback();

				return $this->plataformHelper->capturarExcepcion(__CLASS__, __FUNCTION__, $e->getMessage(), '/');
			}
		}

		$tOficina=TOficina::find($request->input('codigoOficina'));

		if(!($this->plataformHelper->verificarExistenciaAutorizacion($tOficina, 'codigoEmpresa', $sessionManager->get('codigoEmpresa'), $mensajeOut)))
		{
			return $this->plataformHelper->redirectError($mensajeOut, 'oficina/ver');
		}

		return view('oficina/editar', ['tOficina' => $tOficina]);
	}

	public function actionGestionarPersonal(Request $request, SessionManager $sessionManager)
	{
		if($request->has('hdCodigoOficina'))
		{
			try
			{
				DB::beginTransaction();

				$tOficina=TOficina::find($request->input('hdCodigoOficina'));

				if(!($this->plataformHelper->verificarExistenciaAutorizacion($tOficina, 'codigoEmpresa', $sessionManager->get('codigoEmpresa'), $mensajeOut)))
				{
					return $this->plataformHelper->redirectError($mensajeOut, 'oficina/ver');
				}

				TPersonalTOficina::whereRaw('codigoOficina=?', [$tOficina->codigoOficina])->delete();

				if(count($request->input('selectCodigoPersonal'))>0)
				{
					foreach($request->input('selectCodigoPersonal') as $value)
					{
						$tPersonalTOficina=new TPersonalTOficina();

						$tPersonalTOficina->codigoPersonal=$value;
						$tPersonalTOficina->codigoOficina=$tOficina->codigoOficina;

						$tPersonalTOficina->save();
					}
				}

				DB::commit();

				return $this->plataformHelper->redirectCorrecto('Operación realizada correctamente.', 'oficina/ver');
			}
			catch(\Exception $e)
			{
				DB::rollback();

				return $this->plataformHelper->capturarExcepcion(__CLASS__, __FUNCTION__, $e->getMessage(), '/');
			}
		}

		$tOficina=TOficina::with(['tpersonaltoficina'])->whereRaw('codigoOficina=?', [$request->input('codigoOficina')])->first();

		if(!($this->plataformHelper->verificarExistenciaAutorizacion($tOficina, 'codigoEmpresa', $sessionManager->get('codigoEmpresa'), $mensajeOut)))
		{
			return $this->plataformHelper->redirectError($mensajeOut, 'oficina/ver');
		}

		$listaTPersonal=TPersonal::whereRaw('codigoEmpresa=? and cargo!=?', [$sessionManager->get('codigoEmpresa'), 'Súper usuario'])->orderBy('correoElectronico', 'asc')->get();

		return view('oficina/gestionarpersonal', ['tOficina' => $tOficina, 'listaTPersonal' => $listaTPersonal]);
	}

	public function actionJSONPorDescripcion(Request $request, SessionManager $sessionManager)
	{
		$listTOficina=TOficina::whereRaw('codigoEmpresa = ? and compareFind(concat(descripcion, pais, departamento, provincia, distrito, direccion), ?, 77)=1 limit 10', [$sessionManager->get('codigoEmpresa'), $request->input('q')])->get();

		$items=[];

		foreach($listTOficina as $item)
		{
			$items[]=['id' => $item->codigoOficina, 'text' => $item->descripcion, 'row' => $item];
		}

		$result=['items' => $items];

		return response()->json($result);
	}
}
?>