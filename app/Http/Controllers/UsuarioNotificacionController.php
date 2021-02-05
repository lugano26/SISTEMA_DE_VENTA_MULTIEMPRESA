<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;

use DB;

use App\Model\TPersonal;
use App\Model\TUsuarioNotificacion;

class UsuarioNotificacionController extends Controller
{
	public function actionInsertar(Request $request, SessionManager $sessionManager)
	{
		if($_POST)
		{
			try
			{
				DB::beginTransaction();

				if(count($request->get('hdPersonalSeleccionado')) < 1)
				{
					return $this->plataformHelper->redirectError('No se puede proceder. Agregue al menos un usuario para enviar las notificaciones.', 'usuarionotificacion/insertar');
				}

				if($request->get('chkPermanente') == "true" && (date_parse($request->get('dateFechaInicioPeriodo')) > date_parse($request->get('dateFechaFinPeriodo'))))
				{
					return $this->plataformHelper->redirectError('No se puede proceder. La fecha de inicio no puede ser mayor a la fecha de fin.', 'usuarionotificacion/insertar');
				}

				$urlImage='';
				
				if($request->hasFile('txtImagen'))
				{
					$pathImage = '/img/usuarionotificacion/';
					$nameImage = uniqid('usuarionotificacion_').".png";
					$urlImage = $pathImage . $nameImage;
					
					if(!file_exists(public_path(). $pathImage))
					{
						mkdir(public_path().$pathImage, 0777, true);
					}
					
					$request->file('txtImagen')->move(public_path().$pathImage, $nameImage);
				}

				foreach($request->get('hdPersonalSeleccionado') as $codigoPersonal)
				{
					$usuarioNotificacion = new TUsuarioNotificacion();
					
					$usuarioNotificacion->codigoPersonal = $codigoPersonal;
					$usuarioNotificacion->descripcion = $request->get('txtDescripcion');
					$usuarioNotificacion->permanente = $request->get('chkPermanente') == "true";
					$usuarioNotificacion->fechaInicioPeriodo = $request->get('dateFechaInicioPeriodo');
					$usuarioNotificacion->fechaFinPeriodo = $request->get('dateFechaFinPeriodo');
					$usuarioNotificacion->url = $urlImage;
					$usuarioNotificacion->estado = false;
					$usuarioNotificacion->save();
				}

				DB::commit();

				return $this->plataformHelper->redirectCorrecto('Operación realizada correctamente.', 'usuarionotificacion/ver');
			}
			catch(\Exception $e)
			{
				DB::rollback();

				return $this->plataformHelper->capturarExcepcion(__CLASS__, __FUNCTION__, $e->getMessage(), '/');
			}
        }
        
        $listaTPersonal = TPersonal::with('tusuario')->whereRaw('codigoEmpresa=?', [$sessionManager->get('codigoEmpresa')])
        ->orderBy('apellido', 'asc')
        ->orderBy('nombre', 'asc')
		->get();
		
		return view('usuarionotificacion/insertar', ['listaGrupoPersonal' => $listaTPersonal->split(2)]);
	}

	public function actionVer(Request $request, SessionManager $sessionManager, $pagina=1)
	{
		$listaTUsuarioNotificacion = TUsuarioNotificacion::with('tpersonal')
		->whereHas('tpersonal', function($q) use($sessionManager){ $q->where('codigoEmpresa', $sessionManager->get('codigoEmpresa')); })
		->get();

		if($request->input('q'))
		{
			$term=$request->input('q');
			
			$paginationPrepare=$this->plataformHelper->prepararPaginacion(TUsuarioNotificacion::with('tpersonal')
			->orWhereHas('tpersonal', function($q) use($term){ 
				$q->whereRaw('(compareFind(concat(dni, nombre, apellido, correoElectronico), ?, 77)=1', [$term]); 
			})
			->orWhereRaw('compareFind(descripcion, ?, 77)=1)', [$term])
			->whereHas('tpersonal', function($q) use($sessionManager){ 
				$q->whereRaw('codigoEmpresa=?', [$sessionManager->get('codigoEmpresa')]); 
			})
			->orderBy('created_at', 'desc'), null, $pagina);

			$paginationRender=$this->plataformHelper->renderizarPaginacion('usuarionotificacion/ver', $paginationPrepare["cantidadPaginas"], $pagina, $request->input('q'));
			
			return view('usuarionotificacion/ver', ['genericHelper' => $this->plataformHelper, 'listaTUsuarioNotificacion' => $paginationPrepare["listaRegistros"], "pagination" => $paginationRender, "q" => $request->input('q')]);
		}

		$paginationPrepare=$this->plataformHelper->prepararPaginacion(TUsuarioNotificacion::with('tpersonal')
		->whereHas('tpersonal', function($q) use($sessionManager){ $q->where('codigoEmpresa', $sessionManager->get('codigoEmpresa')); })->orderBy('created_at', 'desc'), null, $pagina);
		$paginationRender=$this->plataformHelper->renderizarPaginacion('usuarionotificacion/ver', $paginationPrepare["cantidadPaginas"], $pagina);

		return view('usuarionotificacion/ver', ['genericHelper' => $this->plataformHelper, 'listaTUsuarioNotificacion' => $paginationPrepare["listaRegistros"], "pagination" => $paginationRender]);
	}

	public function actionOcultarNotificacion(Request $request, $codigoUsuarioNotificaion)
	{
		try
		{
			DB::beginTransaction();

			$tUsuarioNotificacion = TUsuarioNotificacion::find($codigoUsuarioNotificaion);

			if($tUsuarioNotificacion == null)
			{
				return $this->plataformHelper->redirectError('No se pudo proceder. No se encontró la notificación.', 'usuarionotificacion/ver');
			}

			$tUsuarioNotificacion->permanente = false;
			$tUsuarioNotificacion->estado = true;
			$tUsuarioNotificacion->save();

			DB::commit();

			return $this->plataformHelper->redirectCorrecto('Operación realizada correctamente.', 'usuarionotificacion/ver');
		}
		catch(\Exception $e)
		{
			DB::rollback();

			return $this->plataformHelper->capturarExcepcion(__CLASS__, __FUNCTION__, $e->getMessage(), '/');
		}
	}

	public function actionMarcarTodoLeido(Request $request, SessionManager $sessionManager)
	{
		try
		{
			DB::beginTransaction();

			TUsuarioNotificacion::whereRaw('codigoPersonal=?', $sessionManager->get('codigoPersonal'))
			->update(['estado' => true]);

			DB::commit();

			return $this->plataformHelper->redirectCorrecto('Operación realizada correctamente.', '/');
		}
		catch(\Exception $e)
		{
			DB::rollback();

			return $this->plataformHelper->capturarExcepcion(__CLASS__, __FUNCTION__, $e->getMessage(), '/');
		}
	}

	public function actionDetalle(Request $request)
	{
		$tUsuarioNotificacion=TUsuarioNotificacion::with('tpersonal')->whereRaw('codigoUsuarioNotificacion=?', [$request->input('codigoUsuarioNotificacion')])->first();

		return view('usuarionotificacion/detalle', ['tUsuarioNotificacion' => $tUsuarioNotificacion]);
	}

	public function actionMarcarLeido(Request $request, SessionManager $sessionManager)
	{
		try
		{
			DB::beginTransaction();

			$tUsuarioNotificacion = TUsuarioNotificacion::whereRaw('codigoPersonal=? and codigoUsuarioNotificacion=?', [$sessionManager->get('codigoPersonal'), $request->get('hdCodigoUsuarioNotificacion')])->first();

			if($tUsuarioNotificacion == null)
			{
				return $this->plataformHelper->redirectError('No se pudo encontrar la notificacion.', '/');
			}

			$tUsuarioNotificacion->estado = true;
			$tUsuarioNotificacion->save();

			DB::commit();

			return $this->plataformHelper->redirectCorrecto('Operación realizada correctamente.', '/');
		}
		catch(\Exception $e)
		{
			DB::rollback();

			return $this->plataformHelper->capturarExcepcion(__CLASS__, __FUNCTION__, $e->getMessage(), '/');
		}
	}

	public function actionMarcarLeidoJSON(Request $request, SessionManager $sessionManager)
	{
		try
		{
			DB::beginTransaction();

			$tUsuarioNotificacion = TUsuarioNotificacion::whereRaw('codigoPersonal=? and codigoUsuarioNotificacion=?', [$sessionManager->get('codigoPersonal'), $request->get('hdCodigoUsuarioNotificacion')])->first();

			if($tUsuarioNotificacion == null)
			{
				return $this->plataformHelper->redirectError('No se pudo encontrar la notificacion.', '/');
			}

			$tUsuarioNotificacion->estado = true;
			$tUsuarioNotificacion->save();

			DB::commit();

			return response()->json(array('error' => false));
		}
		catch(\Exception $e)
		{
			DB::rollback();

			return response()->json(array('error' => true));
		}
	}
}
?>