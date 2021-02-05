<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;
use Illuminate\Encryption\Encrypter;

use App\Model\TUsuario;
use App\Model\TPersonal;
use App\Model\TEmpresa;
use App\Model\TPersonalTOficina;
use App\Model\TPersonalTAlmacen;

class UsuarioController extends Controller
{
	public function actionLogIn(Request $request, SessionManager $sessionManager, Encrypter $encrypter)
	{
		if($_POST)
		{
			$sessionManager->flush();

			if(!(TEmpresa::find($request->input('selectCodigoEmpresa'))->estado))
            {
                return $this->plataformHelper->redirectError('La empresa seleccionada no se encuentra habilitada.', 'usuario/login');
            }

			$listaTUsuario=TUsuario::with(['tpersonal.tempresa.toficina', 'tpersonal.tempresa.talmacen'])->whereRaw('nombreUsuario=?', [explode('@', trim($request->input('txtCorreoElectronico')))[0]])->get();

			if(count($listaTUsuario)>0)
			{
				$tUsuario=null;
				$usuarioContraseniaCorrecto=false;

				foreach($listaTUsuario as $value)
				{
					if($encrypter->decrypt($value->contrasenia)===$request->input('passContrasenia') && $request->input('selectCodigoEmpresa')==$value->tpersonal->codigoEmpresa && trim($request->input('txtCorreoElectronico'))==$value->tpersonal->correoElectronico)
					{
						$usuarioContraseniaCorrecto=true;

						$tUsuario=$value;

						break;
					}
				}

				if($usuarioContraseniaCorrecto)
				{
					if(strpos($tUsuario->rol, 'Súper usuario')===false && !($request->input('selectCodigoEmpresa')==$tUsuario->tpersonal->codigoEmpresa && (($request->input('radioLocal') && TPersonalTOficina::whereRaw('codigoOficina=? and codigoPersonal=?', [$request->input('selectCodigoOficina'), $tUsuario->tpersonal->codigoPersonal])->first()!=null) || (!($request->input('radioLocal')) && TPersonalTAlmacen::whereRaw('codigoAlmacen=? and codigoPersonal=?', [$request->input('selectCodigoAlmacen'), $tUsuario->tpersonal->codigoPersonal])->first()!=null))))
					{
						return $this->plataformHelper->redirectError('El usuario especificado no tiene acceso al local seleccionado.', 'usuario/login');
					}

					$sessionManager->put('codigoPersonal', $tUsuario->codigoPersonal);
					$sessionManager->put('correoElectronico', $tUsuario->tpersonal->correoElectronico);
					$sessionManager->put('nombreCompleto', $tUsuario->tpersonal->nombre);
					$sessionManager->put('rol', $tUsuario->rol);
					$sessionManager->put('codigoEmpresa', $tUsuario->tpersonal->codigoEmpresa);
					$sessionManager->put('razonSocialEmpresa', $tUsuario->tpersonal->tempresa->razonSocial);
					$sessionManager->put('codigoOficina', $request->input('radioLocal') ? $request->input('selectCodigoOficina') : null);
					$sessionManager->put('codigoAlmacen', !($request->input('radioLocal')) ? $request->input('selectCodigoAlmacen') : null);
					$sessionManager->put('rucEmpresa', TEmpresa::find($request->input('selectCodigoEmpresa'))->ruc);
					$sessionManager->put('demo', TEmpresa::find($request->input('selectCodigoEmpresa'))->demo);

					if($request->input('radioLocal'))
					{
						foreach($tUsuario->tpersonal->tempresa->toficina as $value)
						{
							if($value->codigoOficina==$request->input('selectCodigoOficina'))
							{
								$sessionManager->put('descripcionOficina', $value->descripcion);

								break;
							}
						}
					}
					else
					{
						foreach($tUsuario->tpersonal->tempresa->talmacen as $value)
						{
							if($value->codigoAlmacen==$request->input('selectCodigoAlmacen'))
							{
								$sessionManager->put('descripcionAlmacen', $value->descripcion);

								break;
							}
						}
					}

					return $this->plataformHelper->redirectCorrecto('Se bienvenido(a) al sistema, '.$tUsuario->tpersonal->nombre.'.', '/');
				}
			}

			return $this->plataformHelper->redirectError('Usuario y/o contraseña incorrecta o el usuario no tiene acceso a la empresa seleccionada.', 'usuario/login');
		}

		$listaTEmpresa=TEmpresa::with(['toficina', 'talmacen'])->get();

		if(count($listaTEmpresa)==0)
		{
			return $this->plataformHelper->redirectAlerta('Antes de usar el sistema primero registre una empresa.', 'general/configuracionglobal');
		}

		return view('usuario/login', ['listaTEmpresa' => $listaTEmpresa]);
	}

	public function actionLogOut(SessionManager $sessionManager)
	{
		$sessionManager->flush();

		return $this->plataformHelper->redirectCorrecto('Sesión cerrada correctamente.', '/');
	}

	public function actionCambiarLocal(Request $request, SessionManager $sessionManager)
	{
		if($_POST)
		{
			if(strpos($sessionManager->get('rol'), 'Súper usuario') === false && $sessionManager->get('codigoEmpresa') != $request->input('selectCodigoEmpresa'))
			{
				return $this->plataformHelper->redirectError('El usuario especificado no tiene acceso al local seleccionado.', 'general/index');
			}
			
			if(!(TEmpresa::find($request->input('selectCodigoEmpresa'))->estado))
            {
                return $this->plataformHelper->redirectError('La empresa seleccionada no se encuentra habilitada.', 'general/index');
            }

			$tPersonal = TPersonal::with(['tusuario', 'tempresa.toficina', 'tempresa.talmacen'])->whereRaw('correoElectronico=? and codigoEmpresa=?', [
				$sessionManager->get('correoElectronico'),
				(strpos($sessionManager->get('rol'), 'Súper usuario')===false ? $sessionManager->get('codigoEmpresa') : $request->input('selectCodigoEmpresa'))
			])->first();
			
			if(!$tPersonal)
			{
				return $this->plataformHelper->redirectError('No se encontró el usuario especificado.', 'general/index');
			}

			if(strpos($tPersonal->tusuario->rol, 'Súper usuario')===false && !($request->input('selectCodigoEmpresa')==$tPersonal->codigoEmpresa && (($request->input('radioLocal') && TPersonalTOficina::whereRaw('codigoOficina=? and codigoPersonal=?', [$request->input('selectCodigoOficina'), $tPersonal->codigoPersonal])->first()!=null) || (!($request->input('radioLocal')) && TPersonalTAlmacen::whereRaw('codigoAlmacen=? and codigoPersonal=?', [$request->input('selectCodigoAlmacen'), $tPersonal->codigoPersonal])->first()!=null))))
			{
				return $this->plataformHelper->redirectError('El usuario especificado no tiene acceso al local seleccionado.', 'general/index');
			}

			$sessionManager->flush();

			$sessionManager->put('codigoPersonal', $tPersonal->codigoPersonal);
			$sessionManager->put('correoElectronico', $tPersonal->correoElectronico);
			$sessionManager->put('nombreCompleto', $tPersonal->nombre);
			$sessionManager->put('rol', $tPersonal->tUsuario->rol);
			$sessionManager->put('codigoEmpresa', $tPersonal->codigoEmpresa);
			$sessionManager->put('razonSocialEmpresa', $tPersonal->tempresa->razonSocial);
			$sessionManager->put('codigoOficina', $request->input('radioLocal') ? $request->input('selectCodigoOficina') : null);
			$sessionManager->put('codigoAlmacen', !($request->input('radioLocal')) ? $request->input('selectCodigoAlmacen') : null);
			$sessionManager->put('rucEmpresa', TEmpresa::find($request->input('selectCodigoEmpresa'))->ruc);
			$sessionManager->put('demo', TEmpresa::find($request->input('selectCodigoEmpresa'))->demo);
			
			if($request->input('radioLocal'))
			{
				foreach($tPersonal->tempresa->toficina as $value)
				{
					if($value->codigoOficina==$request->input('selectCodigoOficina'))
					{
						$sessionManager->put('descripcionOficina', $value->descripcion);

						break;
					}
				}
			}
			else
			{
				foreach($tPersonal->tempresa->talmacen as $value)
				{
					if($value->codigoAlmacen==$request->input('selectCodigoAlmacen'))
					{
						$sessionManager->put('descripcionAlmacen', $value->descripcion);

						break;
					}
				}
			}

			return $this->plataformHelper->redirectCorrecto('Se bienvenido(a) al sistema, '.$tPersonal->nombre.'.', '/');
		}

		$listaTEmpresa = null;
		$codigoEmpresa = $sessionManager->get('codigoEmpresa');

		if(strpos($sessionManager->get('rol'), 'Súper usuario') !== false)
		{
			$listaTEmpresa=TEmpresa::with(['toficina', 'talmacen'])->get();
		}
		else
		{
			$listaTEmpresa=TEmpresa::with(['toficina', 'talmacen'])->whereRaw('codigoEmpresa=?', [$sessionManager->get('codigoEmpresa')])->get();
		}

		if($listaTEmpresa && count($listaTEmpresa)==0)
		{
			return $this->plataformHelper->redirectAlerta('Antes de usar el sistema primero registre una empresa.', 'general/configuracionglobal');
		}

		return view('usuario/cambiarlocal', ['listaTEmpresa' => $listaTEmpresa, 'codigoEmpresa' => $codigoEmpresa]);
	}
}
?>