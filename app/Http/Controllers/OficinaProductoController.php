<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;

use DB;

use App\Model\TOficinaProducto;

class OficinaProductoController extends Controller
{
	public function actionJSONPorCodigoBarrasNombre(Request $request, SessionManager $sessionManager)
	{
		$listaTOficinaProducto=[];

		if($request->input('searchPerformance')=='Performance')
		{
			$listaTOficinaProducto=TOficinaProducto::whereRaw('codigoOficina=? and replace(concat(codigoBarras, nombre), \' \', \'\') like replace(?, \' \', \'\') limit 10', [$sessionManager->get('codigoOficina'), '%'.$request->input('q').'%'])->get();
		}
		else
		{
			$listaTOficinaProducto=TOficinaProducto::whereRaw('codigoOficina=? and compareFind(concat(codigoBarras, nombre), ?, 77)=1 limit 10', [$sessionManager->get('codigoOficina'), $request->input('q')])->get();
		}

		$items=[];

		foreach($listaTOficinaProducto as $item)
		{
			$items[]=['id' => $item->codigoOficinaProducto, 'text' => $item->nombre, 'row' => $item];
		}

		$result=['items' => $items];

		return response()->json($result);
	}

	public function actionJSONPorCodigoBarrasNombreOficina(Request $request)
	{
		$listaTOficinaProducto=TOficinaProducto::whereRaw('compareFind(concat(codigoBarras, nombre), ?, 77)=1 and codigoOficina=? limit 10', [$request->input('q'), $request->input('codigoOficina')])->get();

		$items=[];

		foreach($listaTOficinaProducto as $item)
		{
			$items[]=['id' => $item->codigoOficinaProducto, 'text' => $item->nombre, 'row' => $item];
		}

		$result=['items' => $items];

		return response()->json($result);
	}

	public function actionVerPorCodigoOficina(Request $request, SessionManager $sessionManager, $pagina = 1)
	{
		if(!($sessionManager->has('codigoOficina')))
		{
			return $this->plataformHelper->redirectError('Debe estar logueado en una oficina para realizar esta operación.', '/');
		}
		
		$listaTOficinaProducto=TOficinaProducto::whereRaw('codigoOficina=?', [$sessionManager->get('codigoOficina')])->get();

		$sumaEstimacionCompraTemp=0;
		$sumaEstimacionVentaSubTotalTemp=0;
		$sumaEstimacionVentaTotalTemp=0;

		foreach($listaTOficinaProducto as $key => $value)
		{
			$sumaEstimacionCompraTemp+=($value->cantidad*$value->precioCompraUnitario);
			$sumaEstimacionVentaSubTotalTemp+=($value->cantidad*$value->precioVentaUnitario)/number_format(($value->porcentajeTributacion/100)+1, 2, '.', '');
			$sumaEstimacionVentaTotalTemp+=($value->cantidad*$value->precioVentaUnitario);
		}

		$listaTOficinaProducto->sumaEstimacionCompra=$sumaEstimacionCompraTemp;
		$listaTOficinaProducto->sumaEstimacionVentaSubTotal=$sumaEstimacionVentaSubTotalTemp;
		$listaTOficinaProducto->sumaEstimacionVentaTotal=$sumaEstimacionVentaTotalTemp;

		if($request->input('q'))
		{
			$term = $request->input('q');
			$paginationPrepare = null;

			if($request->input('searchPerformance')=='Performance')
			{
				$paginationPrepare = $this->plataformHelper->prepararPaginacion(TOficinaProducto::whereRaw('replace(concat(codigoBarras, nombre), \' \', \'\') like replace(?, \' \', \'\') and codigoOficina=?', ['%'.$term.'%', $sessionManager->get('codigoOficina')]), null, $pagina);
			}
			else
			{
				$paginationPrepare = $this->plataformHelper->prepararPaginacion(TOficinaProducto::whereRaw('compareFind(concat(codigoBarras, nombre), ?, 77)=1 and codigoOficina=?', [$term, $sessionManager->get('codigoOficina')]), null, $pagina);
			}

			$paginationRender = $this->plataformHelper->renderizarPaginacion('oficinaproducto/verporcodigooficina', $paginationPrepare["cantidadPaginas"], $pagina, $request->input('q'));
	
			return view('oficinaproducto/verporcodigooficina', ['listaTOficinaProducto' => $listaTOficinaProducto, 'listaTRegistrosPaginacion' => $paginationPrepare["listaRegistros"], "pagination" => $paginationRender, "q" => $request->input('q')]);
		}

		$paginationPrepare = $this->plataformHelper->prepararPaginacion(TOficinaProducto::whereRaw('codigoOficina=?', [$sessionManager->get('codigoOficina')]), null, $pagina);

		$paginationRender = $this->plataformHelper->renderizarPaginacion('oficinaproducto/verporcodigooficina', $paginationPrepare["cantidadPaginas"], $pagina);

		return view('oficinaproducto/verporcodigooficina', ['listaTOficinaProducto' => $listaTOficinaProducto, 'listaTRegistrosPaginacion' => $paginationPrepare["listaRegistros"], "pagination" => $paginationRender]);
	}
}
?>