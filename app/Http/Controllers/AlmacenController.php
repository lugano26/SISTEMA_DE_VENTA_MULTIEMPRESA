<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;

use App\Validation\AlmacenValidation;

use DB;

use App\Model\TAlmacen;
use App\Model\TPersonal;
use App\Model\TPersonalTAlmacen;

class AlmacenController extends Controller
{
	public function actionInsertar(Request $request, SessionManager $sessionManager)
	{
		if($_POST)
		{
			try
			{
				DB::beginTransaction();

				$this->mensajeGlobal=(new AlmacenValidation())->validationInsertar($request);

				if($this->mensajeGlobal!='')
				{
					DB::rollBack();

					$request->flash();

					return $this->plataformHelper->redirectError($this->mensajeGlobal, 'almacen/insertar');
				}

				$tAlmacen=new TAlmacen();

				$tAlmacen->codigoEmpresa=$sessionManager->get('codigoEmpresa');
				$tAlmacen->descripcion=trim($request->input('txtDescripcion'));
				$tAlmacen->pais=trim($request->input('txtPais'));
				$tAlmacen->departamento=trim($request->input('txtDepartamento'));
				$tAlmacen->provincia=trim($request->input('txtProvincia'));
				$tAlmacen->distrito=trim($request->input('txtDistrito'));
				$tAlmacen->direccion=trim($request->input('txtDireccion'));
				$tAlmacen->manzana='';
				$tAlmacen->lote='';
				$tAlmacen->numeroVivienda=trim($request->input('txtNumeroVivienda'));
				$tAlmacen->numeroInterior='';
				$tAlmacen->telefono=trim($request->input('txtTelefono'));
				$tAlmacen->fechaCreacion='1111-11-11';
				$tAlmacen->estado=true;

				$tAlmacen->save();

				DB::commit();

				return $this->plataformHelper->redirectCorrecto('Operación realizada correctamente.', 'almacen/ver');
			}
			catch(\Exception $e)
			{
				DB::rollback();

				return $this->plataformHelper->capturarExcepcion(__CLASS__, __FUNCTION__, $e->getMessage(), '/');
			}
		}

		return view('almacen/insertar');
	}

	public function actionVer(SessionManager $sessionManager)
	{
		$listaTAlmacen=TAlmacen::whereRaw('codigoEmpresa=?', [$sessionManager->get('codigoEmpresa')])->get();

		return view('almacen/ver', ['listaTAlmacen' => $listaTAlmacen]);
	}

	public function actionEditar(Request $request, SessionManager $sessionManager)
	{
		if($request->has('hdCodigoAlmacen'))
		{
			try
			{
				DB::beginTransaction();

				$tAlmacen=TAlmacen::find($request->input('hdCodigoAlmacen'));

				if(!($this->plataformHelper->verificarExistenciaAutorizacion($tAlmacen, 'codigoEmpresa', $sessionManager->get('codigoEmpresa'), $mensajeOut)))
				{
					return $this->plataformHelper->redirectError($mensajeOut, 'almacen/ver');
				}

				$this->mensajeGlobal=(new AlmacenValidation())->validationEditar($request);

				if($this->mensajeGlobal!='')
				{
					DB::rollBack();

					return $this->plataformHelper->redirectError($this->mensajeGlobal, 'almacen/ver');
				}

				$tAlmacen->descripcion=trim($request->input('txtDescripcion'));
				$tAlmacen->pais=trim($request->input('txtPais'));
				$tAlmacen->departamento=trim($request->input('txtDepartamento'));
				$tAlmacen->provincia=trim($request->input('txtProvincia'));
				$tAlmacen->distrito=trim($request->input('txtDistrito'));
				$tAlmacen->direccion=trim($request->input('txtDireccion'));
				$tAlmacen->numeroVivienda=trim($request->input('txtNumeroVivienda'));
				$tAlmacen->telefono=trim($request->input('txtTelefono'));

				$tAlmacen->save();

				DB::commit();

				return $this->plataformHelper->redirectCorrecto('Operación realizada correctamente.', 'almacen/ver');
			}
			catch(\Exception $e)
			{
				DB::rollback();

				return $this->plataformHelper->capturarExcepcion(__CLASS__, __FUNCTION__, $e->getMessage(), '/');
			}
		}

		$tAlmacen=TAlmacen::find($request->input('codigoAlmacen'));

		if(!($this->plataformHelper->verificarExistenciaAutorizacion($tAlmacen, 'codigoEmpresa', $sessionManager->get('codigoEmpresa'), $mensajeOut)))
		{
			return $this->plataformHelper->redirectError($mensajeOut, 'almacen/ver');
		}

		return view('almacen/editar', ['tAlmacen' => $tAlmacen]);
	}

	public function actionGestionarPersonal(Request $request, SessionManager $sessionManager)
	{
		if($request->has('hdCodigoAlmacen'))
		{
			try
			{
				DB::beginTransaction();

				$tAlmacen=TAlmacen::find($request->input('hdCodigoAlmacen'));

				if(!($this->plataformHelper->verificarExistenciaAutorizacion($tAlmacen, 'codigoEmpresa', $sessionManager->get('codigoEmpresa'), $mensajeOut)))
				{
					return $this->plataformHelper->redirectError($mensajeOut, 'almacen/ver');
				}

				TPersonalTAlmacen::whereRaw('codigoAlmacen=?', [$tAlmacen->codigoAlmacen])->delete();

				if(count($request->input('selectCodigoPersonal'))>0)
				{
					foreach($request->input('selectCodigoPersonal') as $value)
					{
						$tPersonalTAlmacen=new TPersonalTAlmacen();

						$tPersonalTAlmacen->codigoPersonal=$value;
						$tPersonalTAlmacen->codigoAlmacen=$tAlmacen->codigoAlmacen;

						$tPersonalTAlmacen->save();
					}
				}

				DB::commit();

				return $this->plataformHelper->redirectCorrecto('Operación realizada correctamente.', 'almacen/ver');
			}
			catch(\Exception $e)
			{
				DB::rollback();

				return $this->plataformHelper->capturarExcepcion(__CLASS__, __FUNCTION__, $e->getMessage(), '/');
			}
		}

		$tAlmacen=TAlmacen::with(['tpersonaltalmacen'])->whereRaw('codigoAlmacen=?', [$request->input('codigoAlmacen')])->first();

		if(!($this->plataformHelper->verificarExistenciaAutorizacion($tAlmacen, 'codigoEmpresa', $sessionManager->get('codigoEmpresa'), $mensajeOut)))
		{
			return $this->plataformHelper->redirectError($mensajeOut, 'almacen/ver');
		}

		$listaTPersonal=TPersonal::whereRaw('codigoEmpresa=? and cargo!=?', [$sessionManager->get('codigoEmpresa'), 'Súper usuario'])->orderBy('correoElectronico', 'asc')->get();

		return view('almacen/gestionarpersonal', ['tAlmacen' => $tAlmacen, 'listaTPersonal' => $listaTPersonal]);
	}

	public function actionJSONPorDescripcion(Request $request, SessionManager $sessionManager)
	{
		$listTAlmacen=TAlmacen::whereRaw('codigoEmpresa = ? and compareFind(concat(descripcion, pais, departamento, provincia, distrito, direccion), ?, 77)=1 limit 10', [$sessionManager->get('codigoEmpresa'), $request->input('q')])->get();

		$items=[];

		foreach($listTAlmacen as $item)
		{
			$items[]=['id' => $item->codigoAlmacen, 'text' => $item->descripcion, 'row' => $item];
		}

		$result=['items' => $items];

		return response()->json($result);
	}
}
?>