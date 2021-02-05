<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;

use App\Validation\AlmacenProductoValidation;

use DB;

use App\Model\TAlmacenProducto;
use App\Model\TOficinaProducto;
use App\Model\TUnidadMedida;
use App\Model\TPresentacion;

class AlmacenProductoController extends Controller
{
	public function actionJSONPorCodigoAlmacenNombre(Request $request, SessionManager $sessionManager)
	{
		$listaTAlmacenProducto=TAlmacenProducto::whereRaw('codigoAlmacen=? and compareFind(nombre, ?, 77)=1 limit 20', [$sessionManager->get('codigoAlmacen'), $request->input('q')])->get();

		$items=[];

		foreach($listaTAlmacenProducto as $item)
		{
			$items[]=['id' => $item->codigoAlmacenProducto, 'text' => $item->nombre, 'row' => $item];
		}

		$result=['items' => $items];

		return response()->json($result);
	}

	public function actionJSONPorCodigoEmpresaNombreGroupByNombre(Request $request, SessionManager $sessionManager)
	{
		$listaTAlmacenProducto=[];

		if($request->input('searchPerformance')=='Performance')
		{
			$listaTAlmacenProducto=TAlmacenProducto::whereRaw('(replace(nombre, \' \', \'\') like replace(?, \' \', \'\'))', ['%'.$request->input('q').'%'])->whereHas('talmacen', function($q) use($sessionManager){ $q->where('codigoEmpresa', $sessionManager->get('codigoEmpresa')); })->groupBy('nombre')->take(20)->get();
		}
		else
		{
			$listaTAlmacenProducto=TAlmacenProducto::whereRaw('(compareFind(nombre, ?, 77)=1)', [$request->input('q')])->whereHas('talmacen', function($q) use($sessionManager){ $q->where('codigoEmpresa', $sessionManager->get('codigoEmpresa')); })->groupBy('nombre')->take(20)->get();
		}

		$items=[];

		foreach($listaTAlmacenProducto as $item)
		{
			$items[]=['id' => $item->codigoAlmacenProducto, 'text' => $item->nombre, 'row' => $item];
		}

		$result=['items' => $items];

		return response()->json($result);
	}

	public function actionJSONPorCodigoBarrasNombre(Request $request, SessionManager $sessionManager)
	{
		$listaTAlmacenProducto=TAlmacenProducto::with('tpresentacion', 'tunidadmedida')->whereRaw('compareFind(concat(codigoBarras, nombre), ?, 77)=1 and codigoAlmacen=? limit 10', [ $request->input('q'), $sessionManager->get('codigoAlmacen')])->get();

		$items=[];

		foreach($listaTAlmacenProducto as $item)
		{
			$items[]=['id' => $item->codigoAlmacenProducto, 'text' => $item->nombre, 'row' => $item];
		}

		$result=['items' => $items];

		return response()->json($result);
	}

	public function actionJSONPorCodigoBarrasNombreAlmacen(Request $request)
	{
		$listaTAlmacenProducto=TAlmacenProducto::with('tpresentacion', 'tunidadmedida')->whereRaw('compareFind(concat(codigoBarras, nombre), ?, 77)=1 and codigoAlmacen=? limit 10', [ $request->input('q'), $request->input('codigoAlmacen')])->get();

		$items=[];

		foreach($listaTAlmacenProducto as $item)
		{
			$items[]=['id' => $item->codigoAlmacenProducto, 'text' => $item->nombre, 'row' => $item];
		}

		$result=['items' => $items];

		return response()->json($result);
	}

	public function actionVerPorCodigoAlmacen(Request $request, SessionManager $sessionManager, $pagina = 1)
	{
		if(!($sessionManager->has('codigoAlmacen')))
		{
			return $this->plataformHelper->redirectError('Debe estar logueado en un almacen para realizar esta operación.', '/');
		}

		$listaTAlmacenProducto=TAlmacenProducto::with(['tpresentacion', 'tunidadmedida'])->whereRaw('codigoAlmacen=?', [$sessionManager->get('codigoAlmacen')])->get();

		$sumaEstimacionCompraTemp=0;
		$sumaEstimacionVentaSubTotalTemp=0;
		$sumaEstimacionVentaTotalTemp=0;

		foreach($listaTAlmacenProducto as $key => $value)
		{
			$sumaEstimacionCompraTemp+=($value->cantidad*$value->precioCompraUnitario);
			$sumaEstimacionVentaSubTotalTemp+=($value->cantidad*$value->precioVentaUnitario)/number_format(($value->porcentajeTributacion/100)+1, 2, '.', '');
			$sumaEstimacionVentaTotalTemp+=($value->cantidad*$value->precioVentaUnitario);			
		}

		$listaTAlmacenProducto->sumaEstimacionCompra=$sumaEstimacionCompraTemp;
		$listaTAlmacenProducto->sumaEstimacionVentaSubTotal=$sumaEstimacionVentaSubTotalTemp;
		$listaTAlmacenProducto->sumaEstimacionVentaTotal=$sumaEstimacionVentaTotalTemp;

		if($request->input('q'))
		{
			$term = $request->input('q');
			$paginationPrepare = null;

			if($request->input('searchPerformance')=='Performance')
			{
				$paginationPrepare = $this->plataformHelper->prepararPaginacion(TAlmacenProducto::with(['tpresentacion', 'tunidadmedida'])->whereRaw('replace(concat(codigoBarras, nombre), \' \', \'\') like replace(?, \' \', \'\') and codigoAlmacen=?', ['%'.$term.'%', $sessionManager->get('codigoAlmacen')]), null, $pagina);
			}
			else
			{
				$paginationPrepare = $this->plataformHelper->prepararPaginacion(TAlmacenProducto::with(['tpresentacion', 'tunidadmedida'])->whereRaw('compareFind(concat(codigoBarras, nombre), ?, 77)=1 and codigoAlmacen=?', [$term, $sessionManager->get('codigoAlmacen')]), null, $pagina);
			}	

			foreach($paginationPrepare["listaRegistros"] as $key => $value)
			{
				$value->distribucionProductoOficina=TOficinaProducto::with(['toficina'])->whereRaw("replace(concat(codigoBarras, nombre), ' ', '')=replace(?, ' ', '')", [$value->codigoBarras.$value->nombre])->whereHas('toficina', function($q) use($sessionManager){ $q->where('codigoEmpresa', $sessionManager->get('codigoEmpresa')); })->get();
				$value->distribucionProductoAlmacen=TAlmacenProducto::with(['talmacen'])->whereRaw("replace(concat(codigoBarras, nombre), ' ', '')=replace(?, ' ', '')", [$value->codigoBarras.$value->nombre])->whereHas('talmacen', function($q) use($sessionManager){ $q->where('codigoEmpresa', $sessionManager->get('codigoEmpresa')); })->get();
			}

			$paginationRender = $this->plataformHelper->renderizarPaginacion('almacenproducto/verporcodigoalmacen', $paginationPrepare["cantidadPaginas"], $pagina, $request->input('q'));
	
			return view('almacenproducto/verporcodigoalmacen', ['listaTAlmacenProducto' => $listaTAlmacenProducto, 'listaTRegistrosPaginacion' => $paginationPrepare["listaRegistros"], "pagination" => $paginationRender, "q" => $request->input('q')]);
		}

		$paginationPrepare = $this->plataformHelper->prepararPaginacion(TAlmacenProducto::with(['tpresentacion', 'tunidadmedida'])->whereRaw('codigoAlmacen=?', [$sessionManager->get('codigoAlmacen')]), null, $pagina);

		foreach($paginationPrepare["listaRegistros"] as $key => $value)
		{
			$value->distribucionProductoOficina=TOficinaProducto::with(['toficina'])->whereRaw("replace(concat(codigoBarras, nombre), ' ', '')=replace(?, ' ', '')", [$value->codigoBarras.$value->nombre])->whereHas('toficina', function($q) use($sessionManager){ $q->where('codigoEmpresa', $sessionManager->get('codigoEmpresa')); })->get();
			$value->distribucionProductoAlmacen=TAlmacenProducto::with(['talmacen'])->whereRaw("replace(concat(codigoBarras, nombre), ' ', '')=replace(?, ' ', '')", [$value->codigoBarras.$value->nombre])->whereHas('talmacen', function($q) use($sessionManager){ $q->where('codigoEmpresa', $sessionManager->get('codigoEmpresa')); })->get();
		}

		$paginationRender = $this->plataformHelper->renderizarPaginacion('almacenproducto/verporcodigoalmacen', $paginationPrepare["cantidadPaginas"], $pagina);

		return view('almacenproducto/verporcodigoalmacen', ['listaTAlmacenProducto' => $listaTAlmacenProducto, 'listaTRegistrosPaginacion' => $paginationPrepare["listaRegistros"], "pagination" => $paginationRender]);
	}

	public function actionVerAgrupado(Request $request, SessionManager $sessionManager, $pagina = 1)
	{
		if($request->input('q'))
		{
			$term = $request->input('q');
			$paginationPrepare = null;

			if($request->input('searchPerformance')=='Performance')
			{
				$paginationPrepare = $this->plataformHelper->prepararPaginacion(TAlmacenProducto::with(['tpresentacion', 'tunidadmedida'])->whereHas('talmacen', function($q) use($sessionManager){ $q->where('codigoEmpresa', $sessionManager->get('codigoEmpresa')); })->whereRaw('replace(concat(codigoBarras, nombre), \' \', \'\') like replace(?, \' \', \'\')', ['%'.$term.'%'])->groupBy(['codigoBarras', 'nombre']), null, $pagina, TAlmacenProducto::with(['tpresentacion', 'tunidadmedida'])->whereHas('talmacen', function($q) use($sessionManager){ $q->where('codigoEmpresa', $sessionManager->get('codigoEmpresa')); })->whereRaw('replace(concat(codigoBarras, nombre), \' \', \'\') like replace(?, \' \', \'\')', ['%'.$term.'%'])->groupBy(['codigoBarras', 'nombre'])->get()->count());	
			}
			else
			{
				$paginationPrepare = $this->plataformHelper->prepararPaginacion(TAlmacenProducto::with(['tpresentacion', 'tunidadmedida'])->whereHas('talmacen', function($q) use($sessionManager){ $q->where('codigoEmpresa', $sessionManager->get('codigoEmpresa')); })->whereRaw('compareFind(concat(codigoBarras, nombre), ?, 77)=1', [$term])->groupBy(['codigoBarras', 'nombre']), null, $pagina, TAlmacenProducto::with(['tpresentacion', 'tunidadmedida'])->whereHas('talmacen', function($q) use($sessionManager){ $q->where('codigoEmpresa', $sessionManager->get('codigoEmpresa')); })->whereRaw('compareFind(concat(codigoBarras, nombre), ?, 77)=1', [$term])->groupBy(['codigoBarras', 'nombre'])->get()->count());	
			}
			
			foreach($paginationPrepare["listaRegistros"] as $key => $value)
			{
				$value->distribucionProductoOficina=TOficinaProducto::with(['toficina'])->whereRaw("replace(concat(codigoBarras, nombre), ' ', '')=replace(?, ' ', '')", [$value->codigoBarras.$value->nombre])->whereHas('toficina', function($q) use($sessionManager){ $q->where('codigoEmpresa', $sessionManager->get('codigoEmpresa')); })->get();
				$value->distribucionProductoAlmacen=TAlmacenProducto::with(['talmacen'])->whereRaw("replace(concat(codigoBarras, nombre), ' ', '')=replace(?, ' ', '')", [$value->codigoBarras.$value->nombre])->whereHas('talmacen', function($q) use($sessionManager){ $q->where('codigoEmpresa', $sessionManager->get('codigoEmpresa')); })->get();
			}

			$paginationRender = $this->plataformHelper->renderizarPaginacion('almacenproducto/veragrupado', $paginationPrepare["cantidadPaginas"], $pagina, $request->input('q'));
	
			return view('almacenproducto/veragrupado', ['listaTAlmacenProductoAgrupado' => $paginationPrepare["listaRegistros"], "pagination" => $paginationRender, "q" => $request->input('q')]);
		}

		$paginationPrepare = $this->plataformHelper->prepararPaginacion(TAlmacenProducto::with(['tpresentacion', 'tunidadmedida'])->whereHas('talmacen', function($q) use($sessionManager){ $q->where('codigoEmpresa', $sessionManager->get('codigoEmpresa')); })->groupBy(['codigoBarras', 'nombre']), null, $pagina, TAlmacenProducto::with(['tpresentacion', 'tunidadmedida'])->whereHas('talmacen', function($q) use($sessionManager){ $q->where('codigoEmpresa', $sessionManager->get('codigoEmpresa')); })->groupBy(['codigoBarras', 'nombre'])->get()->count());		
		$paginationRender = $this->plataformHelper->renderizarPaginacion('almacenproducto/veragrupado', $paginationPrepare["cantidadPaginas"], $pagina);

		foreach($paginationPrepare["listaRegistros"] as $key => $value)
		{
			$value->distribucionProductoOficina=TOficinaProducto::with(['toficina'])->whereRaw("replace(concat(codigoBarras, nombre), ' ', '')=replace(?, ' ', '')", [$value->codigoBarras.$value->nombre])->whereHas('toficina', function($q) use($sessionManager){ $q->where('codigoEmpresa', $sessionManager->get('codigoEmpresa')); })->get();
			$value->distribucionProductoAlmacen=TAlmacenProducto::with(['talmacen'])->whereRaw("replace(concat(codigoBarras, nombre), ' ', '')=replace(?, ' ', '')", [$value->codigoBarras.$value->nombre])->whereHas('talmacen', function($q) use($sessionManager){ $q->where('codigoEmpresa', $sessionManager->get('codigoEmpresa')); })->get();
		}

		return view('almacenproducto/veragrupado', ['listaTAlmacenProductoAgrupado' => $paginationPrepare["listaRegistros"], "pagination" => $paginationRender]);
	}

	public function actionEditarAgrupado(Request $request, SessionManager $sessionManager)
	{
		if($request->has('hdCodigoBarrasProducto'))
		{
			DB::beginTransaction();

			$this->mensajeGlobal=(new AlmacenProductoValidation())->validationEditarAgrupado($request);

			if($this->mensajeGlobal!='')
			{
				DB::rollBack();

				return $this->plataformHelper->redirectError($this->mensajeGlobal, 'almacenproducto/veragrupado');
			}

			$listaTAlmacenProductoTemp=TAlmacenProducto::whereRaw("(replace(codigoBarras, ' ', '')=replace(?, ' ', '') and replace(nombre, ' ', '')=replace(?, ' ', ''))", [$request->input('hdCodigoBarrasProducto'), $request->input('hdNombreProducto')])->whereHas('talmacen', function($q) use($sessionManager){ $q->where('codigoEmpresa', $sessionManager->get('codigoEmpresa')); })->get();

			$codigoAlmacenProductoTemp='';

			foreach($listaTAlmacenProductoTemp as $item)
			{
				$codigoAlmacenProductoTemp.=','.$item->codigoAlmacenProducto;
			}

			if(strlen($codigoAlmacenProductoTemp)>0)
			{
				$codigoAlmacenProductoTemp=mb_substr($codigoAlmacenProductoTemp, 1);
				$codigoAlmacenProductoTemp=explode(',', $codigoAlmacenProductoTemp);
			}

			if(TAlmacenProducto::whereRaw("((replace(codigoBarras, ' ', '')=replace(?, ' ', '') and replace(codigoBarras, ' ', '')!=?) or replace(nombre, ' ', '')=replace(?, ' ', ''))", [$request->input('txtCodigoBarrasProducto'), '', $request->input('txtNombreProducto')])->whereNotIn('codigoAlmacenProducto', $codigoAlmacenProductoTemp)->whereHas('talmacen', function($q) use($sessionManager){ $q->where('codigoEmpresa', $sessionManager->get('codigoEmpresa')); })->count())
			{
				return $this->plataformHelper->redirectError('Existe otro producto con el mismo código de barras o nombre asignado.', 'almacenproducto/veragrupado');
			}

			TAlmacenProducto::whereRaw("replace(concat(codigoBarras, nombre), ' ', '')=replace(?, ' ', '')", [$request->input('hdCodigoBarrasProducto').$request->input('hdNombreProducto')])
				->whereHas('talmacen', function($q) use($sessionManager){ $q->where('codigoEmpresa', $sessionManager->get('codigoEmpresa')); })
				->update(
				[
					'codigoBarras' => $request->input('txtCodigoBarrasProducto'),
					'nombre' => $request->input('txtNombreProducto'),
					'codigoPresentacion' => $request->input('selectCodigoPresentacionProducto'),
					'codigoUnidadMedida' => $request->input('selectCodigoUnidadMedidaProducto'),
					'descripcion' => '',
					'tipo' => $request->input('selectTipoProducto'),
					'situacionImpuesto' => $request->input('selectSituacionImpuestoProducto'),
					'tipoImpuesto' => $request->input('selectTipoImpuestoProducto'),
					'porcentajeTributacion' => $request->input('txtPorcentajeTributacionProducto'),
					'cantidadMinimaAlertaStock' => $request->input('txtCantidadMinimaAlertaStockProducto'),
					'pesoGramosUnidad' => $request->input('txtPesoGramosUnidadProducto'),
					'precioCompraUnitario' => number_format($request->input('txtPrecioCompraUnitarioProducto'), 2, '.', ''),
					'precioVentaUnitario' => number_format($request->input('txtPrecioVentaUnitarioProducto'), 2, '.', ''),
					'ventaMenorUnidad' => $request->input('radioVentaMenorUnidadProducto'),
					'fechaVencimiento' => $request->input('txtFechaVencimientoProducto')=='' ? '1111-11-11' : $request->input('txtFechaVencimientoProducto')
				]);

			TOficinaProducto::whereRaw("replace(concat(codigoBarras, nombre), ' ', '')=replace(?, ' ', '')", [$request->input('hdCodigoBarrasProducto').$request->input('hdNombreProducto')])
				->whereHas('toficina', function($q) use($sessionManager){ $q->where('codigoEmpresa', $sessionManager->get('codigoEmpresa')); })
				->update(
				[
					'codigoBarras' => $request->input('txtCodigoBarrasProducto'),
					'nombre' => $request->input('txtNombreProducto'),
					'presentacion' => TPresentacion::find($request->input('selectCodigoPresentacionProducto'))->nombre,
					'unidadMedida' => TUnidadMedida::find($request->input('selectCodigoUnidadMedidaProducto'))->nombre,
					'descripcion' => '',
					'tipo' => $request->input('selectTipoProducto'),
					'situacionImpuesto' => $request->input('selectSituacionImpuestoProducto'),
					'tipoImpuesto' => $request->input('selectTipoImpuestoProducto'),
					'porcentajeTributacion' => $request->input('txtPorcentajeTributacionProducto'),
					'cantidadMinimaAlertaStock' => $request->input('txtCantidadMinimaAlertaStockProducto'),
					'pesoGramosUnidad' => $request->input('txtPesoGramosUnidadProducto'),
					'precioCompraUnitario' => number_format($request->input('txtPrecioCompraUnitarioProducto'), 2, '.', ''),
					'precioVentaUnitario' => number_format($request->input('txtPrecioVentaUnitarioProducto'), 2, '.', ''),
					'ventaMenorUnidad' => $request->input('radioVentaMenorUnidadProducto'),
					'fechaVencimiento' => $request->input('txtFechaVencimientoProducto')=='' ? '1111-11-11' : $request->input('txtFechaVencimientoProducto')
				]);

			$listaTAlmacenProductoTemp=TAlmacenProducto::whereRaw("(replace(codigoBarras, ' ', '')=replace(?, ' ', '') or replace(nombre, ' ', '')=replace(?, ' ', ''))", [$request->input('txtCodigoBarrasProducto'), $request->input('txtNombreProducto')])->whereHas('talmacen', function($q) use($sessionManager){ $q->where('codigoEmpresa', $sessionManager->get('codigoEmpresa')); })->get();

			foreach($listaTAlmacenProductoTemp as $item)
			{
				if(trim($request->input('txtCodigoBarrasProducto'))!='' && ($item->codigoBarras!=$request->input('txtCodigoBarrasProducto') || $item->nombre!=$request->input('txtNombreProducto')))
				{
					DB::rollback();

					return $this->plataformHelper->redirectError('El código de barras y nombre del producto es incoherente.', 'almacenproducto/veragrupado');
				}
			}

			DB::commit();

			return $this->plataformHelper->redirectCorrecto('Operación realizada correctamente.', 'almacenproducto/veragrupado');
		}

		$listaTUnidadMedida=TUnidadMedida::all();
		$listaTPresentacion=TPresentacion::all();
		$tAlmacenProducto=TAlmacenProducto::with(['tunidadmedida', 'tpresentacion'])->whereRaw('codigoAlmacenProducto=?', [$request->input('codigoAlmacenProducto')])->first();

		return view('almacenproducto/editaragrupado', ['tAlmacenProducto' => $tAlmacenProducto, 'listaTUnidadMedida' => $listaTUnidadMedida, 'listaTPresentacion' => $listaTPresentacion]);
	}

	public function actionBorrarProductosSinStock(SessionManager $sessionManager)
	{
		set_time_limit(0);

		$listProductForDelete=DB::select('select concat(top.codigoBarras, top.nombre) as codigoBarrasNombre from toficinaproducto as top inner join toficina as tox on top.codigoOficina=tox.codigoOficina where tox.codigoEmpresa=? group by top.codigoBarras, top.nombre having sum(top.cantidad)=0', [$sessionManager->get('codigoEmpresa')]);

		foreach($listProductForDelete as $key => $value)
		{
			DB::beginTransaction();

			TOficinaProducto::whereRaw("(replace(concat(codigoBarras, nombre), ' ', '')=replace(?, ' ', ''))", [$value->codigoBarrasNombre])->whereHas('toficina', function($q) use($sessionManager){ $q->where('codigoEmpresa', $sessionManager->get('codigoEmpresa')); })->delete();
			TAlmacenProducto::whereRaw("(replace(concat(codigoBarras, nombre), ' ', '')=replace(?, ' ', '') and cantidad=0)", [$value->codigoBarrasNombre])->whereHas('talmacen', function($q) use($sessionManager){ $q->where('codigoEmpresa', $sessionManager->get('codigoEmpresa')); })->delete();

			DB::commit();
		}

		return $this->plataformHelper->redirectCorrecto('Operación realizada correctamente.', 'almacenproducto/veragrupado');
	}
}
?>