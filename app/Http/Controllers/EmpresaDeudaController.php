<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;

use DateTime;
use DB;

use App\Model\TEmpresaDeuda;
use App\Model\TEmpresa;

class EmpresaDeudaController extends Controller
{
	public function actionGestionar(Request $request, SessionManager $sessionManager, $codigoEmpresa=null)
	{
		if($_POST)
		{
			try
			{
				if(strpos($sessionManager->get('rol'), 'Súper usuario')===false)
				{
					return $this->plataformHelper->redirectError('No se puede proceder. Por favor no trate de alterar el comportamiento del sistema.', 'usuario/login');
				}

				DB::beginTransaction();

				if(
					trim($request->input('txtDescripcion'))==''
				)
				{
					DB::rollBack();

					$request->flash();

					return $this->plataformHelper->redirectError('Datos incorrectos. Por favor no trate de aleterar el comportamiento del sistema.', 'empresadeuda/gestionar/'.$sessionManager->get('codigoEmpresa'));
				}

				$tEmpresaDeuda=new TEmpresaDeuda();

				$tEmpresaDeuda->codigoEmpresa=$request->input('hdCodigoEmpresa');
				$tEmpresaDeuda->descripcion=trim($request->input('txtDescripcion'));
				$tEmpresaDeuda->monto=$request->input('txtMonto');
				$tEmpresaDeuda->fechaPagar=$request->input('dateFechaPagar');
				$tEmpresaDeuda->fechaInicioPeriodo=$request->input('dateFechaInicioPeriodo');
				$tEmpresaDeuda->fechaFinPeriodo=$request->input('dateFechaFinPeriodo');
				$tEmpresaDeuda->incluyeIgv=true;
				$tEmpresaDeuda->facturaEmitida=false;
				$tEmpresaDeuda->estado='Pendiente';

				$tEmpresaDeuda->save();

				DB::commit();

				return $this->plataformHelper->redirectCorrecto('Operación realizada correctamente.', 'empresadeuda/gestionar/'.$request->input('hdCodigoEmpresa'));
			}
			catch(\Exception $e)
			{
				DB::rollback();

				return $this->plataformHelper->capturarExcepcion(__CLASS__, __FUNCTION__, $e->getMessage(), '/');
			}
		}

		$tEmpresa=TEmpresa::find($codigoEmpresa);

		if($tEmpresa==null)
		{
			return $this->plataformHelper->redirectError('Datos inexistentes.', 'empresadeuda/gestionar/'.$sessionManager->get('codigoEmpresa'));
		}

		if(strpos($sessionManager->get('rol'), 'Súper usuario')===false && $tEmpresa->codigoEmpresa!=$sessionManager->get('codigoEmpresa'))
		{
			return $this->plataformHelper->redirectError('No se puede proceder. Por favor no trate de alterar el comportamiento del sistema.', 'usuario/login');
		}

		$listaTEmpresa=TEmpresa::all();
		$listaTEmpresaDeuda=TEmpresaDeuda::whereRaw('codigoEmpresa=?', [$codigoEmpresa])->orderBy('fechaPagar', 'desc')->get();

		foreach($listaTEmpresaDeuda as $value)
		{
			$fecha1=new DateTime(date('Y-m-d'));
			$fecha2=new DateTime($value->fechaPagar);
			$resultado=$fecha2->diff($fecha1);
			$value->diasRetraso=$resultado->format('%R%a');
		}

		return view('empresadeuda/gestionar', ['tEmpresa' => $tEmpresa, 'listaTEmpresa' => $listaTEmpresa, 'listaTEmpresaDeuda' => $listaTEmpresaDeuda]);
	}

	public function actionInclusionIgv(SessionManager $sessionManager, $codigoEmpresaDeuda)
	{
		$tEmpresaDeuda=TEmpresaDeuda::find($codigoEmpresaDeuda);

		if($tEmpresaDeuda==null)
		{
			return $this->plataformHelper->redirectError('Datos inexistentes.', 'empresadeuda/gestionar/'.$sessionManager->get('codigoEmpresa'));
		}

		$tEmpresaDeuda->incluyeIgv=!$tEmpresaDeuda->incluyeIgv;

		$tEmpresaDeuda->save();

		return $this->plataformHelper->redirectCorrecto('Operación realizada correctamente.', 'empresadeuda/gestionar/'.$tEmpresaDeuda->codigoEmpresa);
	}

	public function actionEmisionFactura(SessionManager $sessionManager, $codigoEmpresaDeuda)
	{
		$tEmpresaDeuda=TEmpresaDeuda::find($codigoEmpresaDeuda);

		if($tEmpresaDeuda==null)
		{
			return $this->plataformHelper->redirectError('Datos inexistentes.', 'empresadeuda/gestionar/'.$sessionManager->get('codigoEmpresa'));
		}

		$tEmpresaDeuda->facturaEmitida=!$tEmpresaDeuda->facturaEmitida;

		$tEmpresaDeuda->save();

		return $this->plataformHelper->redirectCorrecto('Operación realizada correctamente.', 'empresadeuda/gestionar/'.$tEmpresaDeuda->codigoEmpresa);
	}
	
	public function actionCambioPago(SessionManager $sessionManager, $codigoEmpresaDeuda)
	{
		$tEmpresaDeuda=TEmpresaDeuda::find($codigoEmpresaDeuda);

		if($tEmpresaDeuda==null)
		{
			return $this->plataformHelper->redirectError('Datos inexistentes.', 'empresadeuda/gestionar/'.$sessionManager->get('codigoEmpresa'));
		}

		$tEmpresaDeuda->fechaPago=date('Y-m-d');
		$tEmpresaDeuda->estado=!$tEmpresaDeuda->estado;

		$tEmpresaDeuda->save();

		return $this->plataformHelper->redirectCorrecto('Operación realizada correctamente.', 'empresadeuda/gestionar/'.$tEmpresaDeuda->codigoEmpresa);
	}

	public function actionEliminar(SessionManager $sessionManager, $codigoEmpresaDeuda)
	{
		$tEmpresaDeuda=TEmpresaDeuda::find($codigoEmpresaDeuda);

		if($tEmpresaDeuda==null)
		{
			return $this->plataformHelper->redirectError('Datos inexistentes.', 'empresadeuda/gestionar/'.$sessionManager->get('codigoEmpresa'));
		}

		$codigoEmpresaTemp=$tEmpresaDeuda->codigoEmpresa;

		$tEmpresaDeuda->delete();

		return $this->plataformHelper->redirectCorrecto('Operación realizada correctamente.', 'empresadeuda/gestionar/'.$codigoEmpresaTemp);
	}
}
?>