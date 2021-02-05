<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use DB;

use App\Model\TExcepcion;

class ExcepcionController extends Controller
{
	public function actionVer(Request $request, $pagina = 1)
	{
		if($request->input('q'))
		{
			$term = $request->input('q');
			
			$paginationPrepare = $this->plataformHelper->prepararPaginacion(TExcepcion::
			with(['tusuario.tpersonal'])
			->whereRaw('compareFind(concat(controlador, accion, error, estado), ?, 77)=1', [ $term ])
			->orWhereHas('tusuario.tpersonal',function($query) use ($term)
			{
				$query->whereRaw('compareFind(concat(dni, nombre, apellido, correoElectronico), ?, 77)=1', [$term] );
			
			})
			->orderBy('codigoExcepcion', 'desc'), null, $pagina);

			$paginationRender = $this->plataformHelper->renderizarPaginacion('excepcion/ver', $paginationPrepare["cantidadPaginas"], $pagina, $request->input('q'));
	
			return view('excepcion/ver', ['listaTExcepcion' => $paginationPrepare["listaRegistros"], "pagination" => $paginationRender, "q" => $request->input('q')]);
		}

		$paginationPrepare = $this->plataformHelper->prepararPaginacion(TExcepcion::with(['tusuario.tpersonal'])->orderBy('codigoExcepcion', 'desc'), null, $pagina);
		$paginationRender = $this->plataformHelper->renderizarPaginacion('excepcion/ver', $paginationPrepare["cantidadPaginas"], $pagina);

		return view('excepcion/ver', ['listaTExcepcion' => $paginationPrepare["listaRegistros"], "pagination" => $paginationRender]);
	}

	public function actionCambiarEstado($codigoExcepcion, $estado)
	{
		$tExcepcion=TExcepcion::find($codigoExcepcion);

		if(!($this->plataformHelper->verificarExistenciaAutorizacion($tExcepcion, true, true, $mensajeOut)))
		{
			return $this->plataformHelper->redirectError($mensajeOut, 'excepcion/ver');
		}

		$tExcepcion->estado=$estado;

		$tExcepcion->save();

		return $this->plataformHelper->redirectCorrecto('Operación realizada correctamente.', 'excepcion/ver');
	}
}
?>