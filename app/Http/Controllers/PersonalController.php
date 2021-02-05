<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;
use Illuminate\Encryption\Encrypter;

use App\Validation\PersonalValidation;

use DB;

use App\Model\TPersonal;
use App\Model\TUsuario;

class PersonalController extends Controller
{
	public function actionInsertar(Request $request, SessionManager $sessionManager, Encrypter $encrypter)
	{
		if($_POST)
		{
			try
			{
				DB::beginTransaction();

				$this->mensajeGlobal=(new PersonalValidation())->validationInsertar($request);

				if($this->mensajeGlobal!='')
				{
					DB::rollBack();

					$request->flash();

					return $this->plataformHelper->redirectError($this->mensajeGlobal, 'personal/insertar');
				}

				$tPersonal=new TPersonal();

				$tPersonal->codigoEmpresa=$sessionManager->get('codigoEmpresa');
				$tPersonal->dni=trim($request->input('txtDni'));
				$tPersonal->nombre=trim($request->input('txtNombre'));
				$tPersonal->apellido=trim($request->input('txtApellido'));
				$tPersonal->seguridadSocial='';
				$tPersonal->pais='';
				$tPersonal->departamento='';
				$tPersonal->provincia='';
				$tPersonal->distrito='';
				$tPersonal->direccion=trim($request->input('txtDireccion'));
				$tPersonal->manzana='';
				$tPersonal->lote='';
				$tPersonal->numeroVivienda='';
				$tPersonal->numeroInterior='';
				$tPersonal->telefono=trim($request->input('txtTelefono'));
				$tPersonal->estadoCivil='';
				$tPersonal->sexo=$request->input('radioSexo');
				$tPersonal->fechaNacimiento='1111-11-11';
				$tPersonal->correoElectronico=trim($request->input('txtCorreoElectronico'));
				$tPersonal->grupoSanguineo='';
				$tPersonal->tipoEmpleado='';
				$tPersonal->cargo='';
				
				$tPersonal->save();

				$codigoPersonal=TPersonal::max('codigoPersonal');

				$tUsuario=new TUsuario();

				$tUsuario->codigoPersonal=$codigoPersonal;
				$tUsuario->nombreUsuario=explode('@', trim($request->input('txtCorreoElectronico')))[0];
				$tUsuario->contrasenia=$encrypter->encrypt($request->input('passContraseniaUsuario'));
				$tUsuario->rol=$request->input('selectRolUsuario')!=null && $request->input('selectRolUsuario')!='' ? implode(',', $request->input('selectRolUsuario')) : '';

				$tUsuario->save();

				DB::commit();

				return $this->plataformHelper->redirectCorrecto('Operación realizada correctamente.', 'personal/ver');
			}
			catch(\Exception $e)
			{
				DB::rollback();

				return $this->plataformHelper->capturarExcepcion(__CLASS__, __FUNCTION__, $e->getMessage(), '/');
			}
		}

		return view('personal/insertar');
	}

	public function actionVer(Request $request, SessionManager $sessionManager, $pagina = 1)
	{
		if($request->input('q'))
		{
			$term = $request->input('q');
			
			$paginationPrepare = $this->plataformHelper->prepararPaginacion(TPersonal::with('tusuario')
			->whereRaw('compareFind(concat(dni, nombre, apellido, correoElectronico), ?, 77)=1 and codigoEmpresa=? and cargo!=?', [$term, $sessionManager->get('codigoEmpresa'), 'Súper usuario'])
			->orWhereHas('tusuario',function($query) use ($term)
			{
				$query->whereRaw('compareFind(concat(rol), ?, 77)=1', [$term] );
			
			})
			->whereRaw('codigoEmpresa=? and cargo!=?', [$sessionManager->get('codigoEmpresa'), 'Súper usuario']), null, $pagina);

			$paginationRender = $this->plataformHelper->renderizarPaginacion('personal/ver', $paginationPrepare["cantidadPaginas"], $pagina, $request->input('q'));
	
			return view('personal/ver', ['listaTPersonal' => $paginationPrepare["listaRegistros"], "pagination" => $paginationRender, "q" => $request->input('q')]);
		}

		$paginationPrepare = $this->plataformHelper->prepararPaginacion(TPersonal::with('tusuario')->whereRaw('codigoEmpresa=? and cargo!=?', [$sessionManager->get('codigoEmpresa'), 'Súper usuario']), null, $pagina);
		$paginationRender = $this->plataformHelper->renderizarPaginacion('personal/ver', $paginationPrepare["cantidadPaginas"], $pagina);

		return view('personal/ver', ['listaTPersonal' => $paginationPrepare["listaRegistros"], "pagination" => $paginationRender]);
	}

	public function actionEditar(Request $request, SessionManager $sessionManager)
	{
		if($request->has('hdCodigoPersonal'))
		{
			try
			{
				DB::beginTransaction();

				$tPersonal=TPersonal::with(['tusuario'])->whereRaw('codigoPersonal=?', [$request->input('hdCodigoPersonal')])->first();

				if((strpos($sessionManager->get('rol'), 'Administrador')===false && strpos($sessionManager->get('rol'), 'Súper usuario')===false && !($this->plataformHelper->verificarExistenciaAutorizacion($tPersonal, 'codigoPersonal', $sessionManager->get('codigoPersonal'), $mensajeOut))) || !($this->plataformHelper->verificarExistenciaAutorizacion($tPersonal, 'codigoEmpresa', $sessionManager->get('codigoEmpresa'), $mensajeOut)))
				{
					return $this->plataformHelper->redirectError($mensajeOut, 'personal/ver');
				}

				$this->mensajeGlobal=(new PersonalValidation())->validationEditar($request);

				if($this->mensajeGlobal!='')
				{
					DB::rollBack();

					return $this->plataformHelper->redirectError($this->mensajeGlobal, 'personal/ver');
				}

				$tPersonal->dni=trim($request->input('txtDni'));
				$tPersonal->nombre=trim($request->input('txtNombre'));
				$tPersonal->apellido=trim($request->input('txtApellido'));
				$tPersonal->direccion=trim($request->input('txtDireccion'));
				$tPersonal->telefono=trim($request->input('txtTelefono'));
				$tPersonal->sexo=$request->input('radioSexo');
				$tPersonal->correoElectronico=trim($request->input('txtCorreoElectronico'));

				$tPersonal->tusuario->nombreUsuario=explode('@', trim($request->input('txtCorreoElectronico')))[0];
				$tPersonal->tusuario->rol=$request->input('selectRolUsuario')!=null && $request->input('selectRolUsuario')!='' ? implode(',', $request->input('selectRolUsuario')) : '';

				$tPersonal->tusuario->save();
				$tPersonal->save();

				DB::commit();

				return $this->plataformHelper->redirectCorrecto('Operación realizada correctamente.', 'personal/ver');
			}
			catch(\Exception $e)
			{
				DB::rollback();

				return $this->plataformHelper->capturarExcepcion(__CLASS__, __FUNCTION__, $e->getMessage(), '/');
			}
		}

		$tPersonal=TPersonal::with(['tusuario'])->whereRaw('codigoPersonal=?', [$request->input('codigoPersonal')])->first();

		if(strpos($sessionManager->get('rol'), 'Administrador')===false && strpos($sessionManager->get('rol'), 'Súper usuario')===false && !($this->plataformHelper->verificarExistenciaAutorizacion($tPersonal, 'codigoPersonal', $sessionManager->get('codigoPersonal'), $mensajeOut)) || !($this->plataformHelper->verificarExistenciaAutorizacion($tPersonal, 'codigoEmpresa', $sessionManager->get('codigoEmpresa'), $mensajeOut)))
		{
			return $this->plataformHelper->redirectError($mensajeOut, 'personal/ver');
		}

		return view('personal/editar', ['tPersonal' => $tPersonal]);
	}

	public function actionCambiarContrasenia(Request $request, SessionManager $sessionManager, Encrypter $encrypter)
	{
		if($request->has('hdCodigoPersonal'))
		{
			try
			{
				DB::beginTransaction();

				$tPersonal=TPersonal::with(['tusuario'])->whereRaw('codigoPersonal=?', [$request->input('hdCodigoPersonal')])->first();

				if((strpos($sessionManager->get('rol'), 'Administrador')===false && strpos($sessionManager->get('rol'), 'Súper usuario')===false && !($this->plataformHelper->verificarExistenciaAutorizacion($tPersonal, 'codigoPersonal', $sessionManager->get('codigoPersonal'), $mensajeOut))) || !($this->plataformHelper->verificarExistenciaAutorizacion($tPersonal, 'codigoEmpresa', $sessionManager->get('codigoEmpresa'), $mensajeOut)))
				{
					return $this->plataformHelper->redirectError($mensajeOut, 'personal/ver');
				}

				if($encrypter->decrypt($tPersonal->tusuario->contrasenia)!=$request->input('passContraseniaActualUsuario') && strpos($sessionManager->get('rol'), 'Administrador')===false && strpos($sessionManager->get('rol'), 'Súper usuario')===false)
				{
					return $this->plataformHelper->redirectError('La contraseña actual no es la correcta.', 'personal/ver');
				}

				$tPersonal->tusuario->contrasenia=$encrypter->encrypt($request->input('passContraseniaUsuario'));

				$tPersonal->tusuario->save();

				DB::commit();

				return $this->plataformHelper->redirectCorrecto('Operación realizada correctamente.', 'personal/ver');
			}
			catch(\Exception $e)
			{
				DB::rollback();

				return $this->plataformHelper->capturarExcepcion(__CLASS__, __FUNCTION__, $e->getMessage(), '/');
			}
		}

		$tPersonal=TPersonal::with(['tusuario'])->whereRaw('codigoPersonal=?', [$request->input('codigoPersonal')])->first();

		if(strpos($sessionManager->get('rol'), 'Administrador')===false && strpos($sessionManager->get('rol'), 'Súper usuario')===false && !($this->plataformHelper->verificarExistenciaAutorizacion($tPersonal, 'codigoPersonal', $sessionManager->get('codigoPersonal'), $mensajeOut)) || !($this->plataformHelper->verificarExistenciaAutorizacion($tPersonal, 'codigoEmpresa', $sessionManager->get('codigoEmpresa'), $mensajeOut)))
		{
			return $this->plataformHelper->redirectError($mensajeOut, 'personal/ver');
		}

		return view('personal/cambiarcontrasenia', ['tPersonal' => $tPersonal]);
	}
}
?>