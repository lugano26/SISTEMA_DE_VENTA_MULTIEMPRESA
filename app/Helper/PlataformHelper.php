<?php
namespace App\Helper;

use Session;
use Mail;
use ZipArchive;
use RecursiveIteratorIterator;

use App\Model\TExcepcion;
use App\Model\TPersonal;
use App\Model\TUsuario;

class PlataformHelper
{
	public function redirectCorrecto($mensaje, $routeRedirect)
	{
		Session::flash('mensajeGlobal', $mensaje);
		Session::flash('tipo', 'success');

		return redirect($routeRedirect);
	}

	public function redirectAlerta($mensaje, $routeRedirect)
	{
		Session::flash('mensajeGlobal', $mensaje);
		Session::flash('tipo', 'notice');

		return redirect($routeRedirect);
	}

	public function redirectError($mensaje, $routeRedirect)
	{
		Session::flash('mensajeGlobal', $mensaje);
		Session::flash('tipo', 'error');

		return redirect($routeRedirect);
	}

	public function capturarExcepcion($controller, $action, $ex, $routeRedirect)
	{
		try
		{
			$tExcepcion=new TExcepcion();

			$tExcepcion->codigoPersonal=Session::has('codigoPersonal') ? Session::get('codigoPersonal') : null;
			$tExcepcion->controlador=$controller;
			$tExcepcion->accion=$action;
			$tExcepcion->error=$ex;
			$tExcepcion->estado='Pendiente';

			$tExcepcion->save();

			$listaTPersonal=TPersonal::whereRaw('cargo=? and codigoEmpresa=?', ['Súper usuario', Session::get('codigoEmpresa')])->get();

			foreach($listaTPersonal as $value)
			{
				Mail::send('email.other.alerta', ['tipo' => 'divAlertaRojo', 'mensaje' => 'Excepción ocurrida en el sistema.'], function($x) use($value)
				{
					$x->from(env('MAIL_USERNAME'), 'sysef.com');
					$x->to($value->correoElectronico, $value->nombre.' '.$value->apellido)->subject('sysef.com: Excepción ocurrida');
				});
			}

			Session::flash('mensajeGlobal', 'Ocurrió un error inesperado. Se está trabajando para solucionar este problema, gracias por su paciencia.');
			Session::flash('tipo', 'error');
		}
		catch(\Exception $ex)
		{
			Session::flash('mensajeGlobal', 'Ocurrió un error inesperado. Se está trabajando para solucionar este problema, gracias por su paciencia.');
			Session::flash('tipo', 'error');
		}

		return redirect($routeRedirect);
	}

	function createZip($source, $destination)
	{
		if(!extension_loaded('zip') || !file_exists($source))
		{
			return false;
		}

		$zip=new ZipArchive();

		if(!$zip->open($destination, (ZIPARCHIVE::CREATE | ZipArchive::OVERWRITE)))
		{
			return false;
		}

		$source=str_replace('\\', '/', realpath($source));

		if(is_dir($source)===true)
		{
			$files=new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source), \RecursiveIteratorIterator::SELF_FIRST);

			foreach($files as $file)
			{
				$file=str_replace('\\', '/', $file);

				if(in_array(substr($file, strrpos($file, '/')+1), array('.', '..')))
				{
					continue;
				}

				if(is_dir($file)===true)
				{
					$zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
				}
				else
				{
					if(is_file($file)===true)
					{
						$zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
					}
				}
			}
		}
		else
		{
			if(is_file($source)===true)
			{
				$zip->addFromString(basename($source), file_get_contents($source));
			}
		}

		return $zip->close();
	}

	public function obtenerRamaCategoriaVenta($ultimoHijo, $separador)
	{
		$returnText='';

		do
		{
			$returnText=($ultimoHijo->tcategoriaventa!=null ? $separador : '').$ultimoHijo->descripcion.$returnText;

			$ultimoHijo=$ultimoHijo->tcategoriaventa;
		} while($ultimoHijo!=null);

		return $returnText;
	}

	public function cadenaAleatoria($length=10)
	{
		$characters='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength=strlen($characters);
		$randomString='';
		
		for ($i=0;$i<$length; $i++)
		{
			$randomString.=$characters[rand(0, $charactersLength-1)];
		}

		return $randomString;
	}

	public function fechaHoraSumar($fechaHora, $tipo, $cantidad)
	{
		$nuevaFechaHora=strtotime( '+'.$cantidad.' '.$tipo , strtotime($fechaHora));
		$nuevaFechaHora=date('Y-m-d H:i:s' , $nuevaFechaHora);

		return $nuevaFechaHora;
	}

	public function verificarSesion()
	{
		$tUsuario=TUsuario::find(Session::get('codigoPersonal'));

		if($tUsuario==null)
		{
			return $this->plataformHelper->redirectError('No existe una sesión iniciada.', '/');
		}

		return $tUsuario;
	}

	public function verificarExistenciaAutorizacion($objeto, $codigoObjeto, $codigoActual, &$mensajeOut)
	{
		if($objeto==null)
		{
			$mensajeOut='Datos inexistentes.';

			if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])=='xmlhttprequest')
			{
				echo '<div class="alert alert-danger"><h4><i class="icon fa fa-ban"></i> Prohibido!</h4>'.$mensajeOut.'</div>';exit;
			}

			return false;
		}

		if($codigoObjeto===true && $codigoActual===true)
		{
			return true;
		}

		if($objeto->$codigoObjeto!=$codigoActual)
		{
			$mensajeOut='Esta información no es de su propiedad. Por favor no trate de alterar el comportamiento del sistema.';

			if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])=='xmlhttprequest')
			{
				echo '<div class="alert alert-danger"><h4><i class="icon fa fa-ban"></i> Prohibido!</h4>'.$mensajeOut.'</div>';exit;
			}

			return false;
		}

		return true;
	}

	public function prepararPaginacion($consulta, $registrosPagina, $paginaActual, $cantidadPages = null)
	{
		$cantidadRegistrosConsiderar=$registrosPagina != null ? $registrosPagina : env('NUMERO_REGISTROS_PAGINACION', 7);
		$paginaActual=$paginaActual<=0 ? 1 : $paginaActual;
		$cantidadPaginas=ceil(($cantidadPages != null ? $cantidadPages : $consulta->count())/$cantidadRegistrosConsiderar);
		$paginaActual=$paginaActual>$cantidadPaginas ? ($cantidadPaginas > 0 ? $cantidadPaginas : 1) : $paginaActual;
		$listaRegistros=$consulta->skip(($paginaActual*$cantidadRegistrosConsiderar)-$cantidadRegistrosConsiderar)->take($cantidadRegistrosConsiderar)->get();
		$cantidadPaginas=($cantidadPaginas==0 ? 1 : $cantidadPaginas);

		return ['listaRegistros' => $listaRegistros, 'paginaActual' => $paginaActual, 'cantidadPaginas' => $cantidadPaginas];
	}

	public function renderizarPaginacion($urlPagina, $cantidadPaginas, $paginaActual, $parametroBusqueda = null, $paginationSm = false)
	{
		$urlPagina = url($urlPagina);
		$urlPaginaAnterior = $urlPagina . ($parametroBusqueda != null && $parametroBusqueda != '' ? '/' . ( $paginaActual > 1 ? $paginaActual - 1 : $paginaActual ) . '?q=' . $parametroBusqueda : '/' . ( $paginaActual > 1 ? $paginaActual - 1 : $paginaActual ));
		$urlPaginaSiguiente = $urlPagina . ($parametroBusqueda != null && $parametroBusqueda != '' ? '/' . ( $paginaActual < $cantidadPaginas ? $paginaActual + 1 : $paginaActual ) . '?q=' . $parametroBusqueda : '/' . ( $paginaActual < $cantidadPaginas ? $paginaActual + 1 : $paginaActual ));
		$urlPagina .= $parametroBusqueda != null && $parametroBusqueda != '' ? '/paginaActual?q=' . $parametroBusqueda : '/paginaActual';

		$seccionPaginacion = '<div class="row">
		<div class="col-sm-12">
			<div class="dataTables_paginate paging_simple_numbers pull-right" id="pagination_generated">
				<ul class="pagination' . ($paginationSm ? ' pagination-sm inline' : '') . '" style="vertical-align:middle">
					<li class="paginate_button previous ' . ( $paginaActual == 1 ? 'disabled' : '' ) . '" id="pagination_generated_previus">
						<a ' . ( $paginaActual == 1 ? '' : 'href="' . $urlPaginaAnterior . '"' ) . ' tabindex="0">' . ($paginationSm ? '«' : 'Anterior') . '</a>
					</li>';

		$nextPage = '<li class="paginate_button next  ' . ( $paginaActual == $cantidadPaginas ? 'disabled' : '' ) . '" id="pagination_generated_next"><a ' . ( $paginaActual == $cantidadPaginas ? '' : 'href="' . $urlPaginaSiguiente . '"' ) . ' tabindex="0">' . ($paginationSm ? '»' : 'Siguiente') . '</a>
		</li>';

		$itemsPagination = '';

		if($cantidadPaginas > 10)
		{
			if($paginaActual < 5 || $paginaActual == $cantidadPaginas || $paginaActual == $cantidadPaginas - 1 )
			{
				for($i = 1; $i <= 5 ; $i ++)
				{
					$itemsPagination .= '<li class="paginate_button ' . ($i == $paginaActual ? 'active' : '') . '"><a style="cursor:pointer;" href="' . ( str_replace('paginaActual', $i, $urlPagina) ) . '" tabindex="0">' . $i . '</a>
					</li>';
				}

				$itemsPagination .= '<li class="paginate_button"><a style="cursor:pointer;" tabindex="0">...</a>
				</li>';

				for($i = $cantidadPaginas - ($paginaActual == $cantidadPaginas - 1 ? 2 : 1) ; $i <= $cantidadPaginas ; $i ++)
				{
					$itemsPagination .= '<li class="paginate_button ' . ($i == $paginaActual ? 'active' : '') . '"><a style="cursor:pointer;" href="' . ( str_replace('paginaActual', $i, $urlPagina) ) . '" tabindex="0">' . $i . '</a>
					</li>';
				}
			}
			else
			{
				$itemsPagination .= '<li class="paginate_button"><a style="cursor:pointer;" href="' . ( str_replace('paginaActual', 1, $urlPagina) ) . '" tabindex="0">1</a></li>';
				$itemsPagination .= '<li class="paginate_button"><a style="cursor:pointer;" tabindex="0">...</a>
				</li>';

				for($i = $paginaActual - 1; $i <= $paginaActual + 1 ; $i ++)
				{
					$itemsPagination .= '<li class="paginate_button ' . ($i == $paginaActual ? 'active' : '') . '"><a style="cursor:pointer;" href="' . ( str_replace('paginaActual', $i, $urlPagina) ) . '" tabindex="0">' . $i . '</a>
					</li>';
				}

				if($paginaActual + 1 != $cantidadPaginas - 1)
				{
					$itemsPagination .= '<li class="paginate_button"><a style="cursor:pointer;" tabindex="0">...</a></li>';
				}

				$itemsPagination .= '<li class="paginate_button"><a style="cursor:pointer;" href="' . ( str_replace('paginaActual', $cantidadPaginas, $urlPagina) ) . '" tabindex="0">' . $cantidadPaginas . '</a></li>';				
			}
		}
		else
		{
			for($i = 1; $i <= $cantidadPaginas ; $i ++)
			{
				$itemsPagination .= '<li class="paginate_button ' . ($i == $paginaActual ? 'active' : '') . '"><a style="cursor:pointer;" href="' . ( str_replace('paginaActual', $i, $urlPagina) ) . '" tabindex="0">' . $i . '</a>
				</li>';
			}
		}

		$seccionPaginacion .= $itemsPagination . $nextPage.'</ul></div></div></div>';

		return $seccionPaginacion;
	}

	public function borrarSaltosCKEditor($contenido)
	{
		$descripcion=trim($contenido);

		while(substr($descripcion, 0, 13)=='<p>&nbsp;</p>')
		{
			$descripcion=substr($descripcion, 13, strlen($descripcion));
		}

		$longitudDescripcion=strlen($descripcion);

		while(substr($descripcion, $longitudDescripcion-13, 13)=='<p>&nbsp;</p>')
		{
			$descripcion=substr($descripcion, 0, $longitudDescripcion-13);

			$longitudDescripcion=strlen($descripcion);
		}

		return $descripcion;
	}

	public function limpiarTextoEntreComas($etiquetas)
	{
		$arrayEtiquetas=explode(',', $etiquetas);

		$etiquetasNuevas='';

		foreach($arrayEtiquetas as $key => $value)
		{
			if(trim($value)!='')
			{		
				$etiquetasNuevas.=','.trim($value);
			}
		}

		$etiquetasNuevas=substr($etiquetasNuevas, 1, strlen($etiquetasNuevas));

		return $etiquetasNuevas;
	}

	public function getTagFromXmlString($tag, $xmlString)
	{
		return explode('</'.$tag.'>', explode('<'.$tag.'>', $xmlString)[1])[0];
	}
}
?>