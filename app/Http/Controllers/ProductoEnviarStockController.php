<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;
use Illuminate\Foundation\Application;

use DB;

use App\Model\TOficina;
use App\Model\TEmpresa;
use App\Model\TAlmacen;
use App\Model\TAlmacenProducto;
use App\Model\TOficinaProducto;
use App\Model\TProductoEnviarStock;
use App\Model\TProductoEnviarStockDetalle;

class ProductoEnviarStockController extends Controller
{
	public function actionInsertar(Request $request, SessionManager $sessionManager)
	{
		try {
			if ($_POST) {
				DB::beginTransaction();

				$tProductoEnviarStock = new TProductoEnviarStock();

				$tProductoEnviarStock->codigoAlmacen = $request->input('selectAlmacenOrigen');
				$tProductoEnviarStock->codigoOficina = $request->input('selectNombreOficinaDestino');
				$tProductoEnviarStock->flete = 0;
				$tProductoEnviarStock->estado = true;
				$tProductoEnviarStock->motivoAnulacion = '';

				$tProductoEnviarStock->save();

				$ultimoRegistroTProductoEnviarStock = TProductoEnviarStock::whereRaw('codigoProductoEnviarStock=(select max(codigoProductoEnviarStock) from tproductoenviarstock)')->first();

				foreach ($request->input('hdCodigoAlmacenProducto') as $key => $value)
				{
					if(
						$request->input('hdCodigoPresentacion')[$key]==''
						|| $request->input('hdCodigoUnidadMedida')[$key]==''
						|| trim($request->input('hdNombre')[$key])==''
						|| !in_array($request->input('hdTipo')[$key], ['Genérico', 'Comercial'])
						|| !in_array($request->input('hdSituacionImpuesto')[$key], ['Afecto'])
						|| !in_array($request->input('hdTipoImpuesto')[$key], ['IGV'])
						|| trim($request->input('hdUnidadMedidaBloque')[$key])==''
					)
					{
						DB::rollBack();

						$request->flash();

						return $this->plataformHelper->redirectError('Datos incorrectos. Por favor no trate de aleterar el comportamiento del sistema.', 'productoenviarstock/insertar');
					}

					if (!filter_var($request->input('hdRegistroSerieProducto')[$key], FILTER_VALIDATE_BOOLEAN)) {
						$tAlmacenProducto = TAlmacenProducto::find($request->input('hdCodigoAlmacenProducto')[$key]);

						if ($tAlmacenProducto == null) {
							DB::rollBack();

							$request->flash();

							return $this->plataformHelper->redirectError('No se encontró el producto ' . ($key + 1) . ' de la lista.', 'productoenviarstock/insertar');
						}

						if ($tAlmacenProducto->cantidad < $request->input('hdCantidadProducto')[$key]) {
							DB::rollBack();

							$request->flash();

							return $this->plataformHelper->redirectError('Stock insuficiente para el producto ' . ($key + 1) . ' de la lista.', 'productoenviarstock/insertar');
						}

						if (!$tAlmacenProducto->ventaMenorUnidad && !preg_match("/^[0-9]+(\.[0]*)?$/", $request->input('hdCantidadProducto')[$key])) {
							DB::rollBack();

							$request->flash();

							return $this->plataformHelper->redirectError('Sólo se permiten traslados por unidades enteras mayores a 1 en el producto ' . ($key + 1) . ' de la lista.', 'productoenviarstock/insertar');
						}

						if (!$tAlmacenProducto->ventaMenorUnidad && intval($request->input('hdCantidadProducto')[$key]) < 1) {
							DB::rollBack();

							$request->flash();

							return $this->plataformHelper->redirectError('Sólo se permiten traslados por unidades enteras mayores a 1 en el producto ' . ($key + 1) . ' de la lista.', 'productoenviarstock/insertar');
						}

						if($tAlmacenProducto->ventaMenorUnidad && $request->input('hdCantidadProducto')[$key] <= 0)
						{
							DB::rollBack();

							$request->flash();

							return $this->plataformHelper->redirectError('Sólo se permiten traslados mayores a 0 en el producto '.($key+1).' de la lista.', 'productoenviarstock/insertar');
						}

						$tProductoEnviarStockDetalle = new TProductoEnviarStockDetalle();

						$tProductoEnviarStockDetalle->codigoProductoEnviarStock = $ultimoRegistroTProductoEnviarStock->codigoProductoEnviarStock;
						$tProductoEnviarStockDetalle->codigoAlmacenProducto = $request->input('hdCodigoAlmacenProducto')[$key];
						$tProductoEnviarStockDetalle->codigoPresentacionProducto = $request->input('hdCodigoPresentacion')[$key];
						$tProductoEnviarStockDetalle->codigoUnidadMedidaProducto = $request->input('hdCodigoUnidadMedida')[$key];
						$tProductoEnviarStockDetalle->codigoBarrasProducto = $request->input('hdCodigoBarras')[$key];
						$tProductoEnviarStockDetalle->nombreProducto = $request->input('hdNombre')[$key];
						$tProductoEnviarStockDetalle->descripcionProducto = $request->input('hdDescripcion')[$key];
						$tProductoEnviarStockDetalle->tipoProducto = $request->input('hdTipo')[$key];
						$tProductoEnviarStockDetalle->cantidadProducto = $request->input('hdCantidadProducto')[$key];
						$tProductoEnviarStockDetalle->situacionImpuestoProducto = $request->input('hdSituacionImpuesto')[$key];
						$tProductoEnviarStockDetalle->tipoImpuestoProducto = $request->input('hdTipoImpuesto')[$key];
						$tProductoEnviarStockDetalle->porcentajeTributacionProducto = $request->input('hdPorcentajeTributacion')[$key];
						$tProductoEnviarStockDetalle->cantidadMinimaAlertaStockProducto = $request->input('hdCantidadMinimaAlertaStock')[$key];
						$tProductoEnviarStockDetalle->pesoGramosUnidadProducto = $request->input('hdPesoGramosUnidad')[$key];
						$tProductoEnviarStockDetalle->ventaMenorUnidadProducto = $request->input('hdVentaMenorUnidad')[$key];
						$tProductoEnviarStockDetalle->unidadesBloqueProducto = $request->input('hdUnidadesBloque')[$key];
						$tProductoEnviarStockDetalle->unidadMedidaBloqueProducto = $request->input('hdUnidadMedidaBloque')[$key];
						$tProductoEnviarStockDetalle->precioCompraUnitarioProducto = $request->input('hdPrecioCompraUnitario')[$key];
						$tProductoEnviarStockDetalle->precioVentaUnitarioProducto = $request->input('hdPrecioVentaUnitario')[$key];
						$tProductoEnviarStockDetalle->fechaVencimientoProducto = $request->input('hdFechaVencimiento')[$key];

						$tProductoEnviarStockDetalle->save();
					} else {
						$tAlmacenProductoAuditado = TAlmacenProducto::find($request->input('hdCodigoAlmacenProducto')[$key]);

						if ($tAlmacenProductoAuditado == null) {
							DB::rollBack();

							$request->flash();

							return $this->plataformHelper->redirectError('No se encontró el producto ' . ($key + 1) . ' (en serie) de la lista.', 'productoenviarstock/insertar');
						}

						if (!preg_match('/^[0-9]+$/', $tAlmacenProductoAuditado->codigoBarras)) {
							DB::rollback();

							$request->flash();

							return $this->plataformHelper->redirectError('El código de barras del producto (en serie) ' . ($key + 1) . ' de la lista no puede generarse en serie, solo se permiten números.', 'productoenviarstock/insertar');
						}

						$codigoBarrasProductoSerie = gmp_init($tAlmacenProductoAuditado->codigoBarras);
						$cantidadProductoSerie = $request->input('hdCantidadProducto')[$key];
						$datosInsertProducto = [];
						$sentinelProducto = 0;

						if (!preg_match("/^[0-9]+(\.[0]*)?$/", $cantidadProductoSerie)) {
							DB::rollback();

							$request->flash();

							return $this->plataformHelper->redirectError('El envio de productos en serie solo se permiten en unidades enteras', 'productoenviarstock/insertar');
						}

						for ($i = 1; $i <= $cantidadProductoSerie; $i++) {
							$nombreProductoSerie = substr(trim($tAlmacenProductoAuditado->nombre), 0, strrpos(trim($tAlmacenProductoAuditado->nombre), ' ')) . ' ' . $codigoBarrasProductoSerie;

							$tAlmacenProducto = TAlmacenProducto::whereRaw("codigoAlmacen=? and (replace(codigoBarras, ' ', '')=replace(?, ' ', '') or replace(nombre, ' ', '')=replace(?, ' ', ''))", [$request->input('selectAlmacenOrigen'), $codigoBarrasProductoSerie, $nombreProductoSerie])
								->whereHas('talmacen', function ($q) use ($sessionManager) {
									$q->where('codigoEmpresa', $sessionManager->get('codigoEmpresa'));
								})
								->first();

							if ($tAlmacenProducto == null) {
								DB::rollBack();

								$request->flash();

								return $this->plataformHelper->redirectError('No se encontró el producto ' . ($key + 1) . ' (en serie) de la lista.', 'productoenviarstock/insertar');
							}

							if ($tAlmacenProducto->cantidad < 1) {
								DB::rollBack();

								$request->flash();

								return $this->plataformHelper->redirectError('Stock insuficiente para el producto ' . ($key + 1) . ' (en serie) de la lista.', 'productoenviarstock/insertar');
							}

							if (!$tAlmacenProducto->ventaMenorUnidad && !preg_match("/^[0-9]+(\.[0]*)?$/", $request->input('hdCantidadProducto')[$key])) {
								DB::rollBack();

								$request->flash();

								return $this->plataformHelper->redirectError('Sólo se permiten translados por unidades enteras (> 1) en el producto ' . ($key + 1) . ' de la lista.', 'productoenviarstock/insertar');
							}

							$datosInsertProducto[] = [
								'codigoProductoEnviarStock' => $ultimoRegistroTProductoEnviarStock->codigoProductoEnviarStock,
								'codigoAlmacenProducto' => $tAlmacenProducto->codigoAlmacenProducto,
								'codigoPresentacionProducto' => $tAlmacenProducto->codigoPresentacion,
								'codigoUnidadMedidaProducto' => $tAlmacenProducto->codigoUnidadMedida,
								'codigoBarrasProducto' => $tAlmacenProducto->codigoBarras,
								'nombreProducto' => $tAlmacenProducto->nombre,
								'descripcionProducto' => $tAlmacenProducto->descripcion,
								'tipoProducto' => $tAlmacenProducto->tipo,
								'cantidadProducto' => 1,
								'situacionImpuestoProducto' => $tAlmacenProducto->situacionImpuesto,
								'tipoImpuestoProducto' => $tAlmacenProducto->tipoImpuesto,
								'porcentajeTributacionProducto' => $tAlmacenProducto->porcentajeTributacion,
								'cantidadMinimaAlertaStockProducto' => $tAlmacenProducto->cantidadMinimaAlertaStock,
								'pesoGramosUnidadProducto' => $tAlmacenProducto->pesoGramosUnidad,
								'ventaMenorUnidadProducto' => $tAlmacenProducto->ventaMenorUnidad,
								'unidadesBloqueProducto' => $tAlmacenProducto->unidadesBloque,
								'unidadMedidaBloqueProducto' => $tAlmacenProducto->unidadMedidaBloque,
								'precioCompraUnitarioProducto' => $tAlmacenProducto->precioCompraUnitario,
								'precioVentaUnitarioProducto' => $tAlmacenProducto->precioVentaUnitario,
								'fechaVencimientoProducto' => $tAlmacenProducto->fechaVencimiento,
								'created_at' => date("Y-m-d H:i:s"),
								'updated_at' => date("Y-m-d H:i:s")
							];

							$codigoBarrasProductoSerie++;
							$sentinelProducto++;

							if ($sentinelProducto == 200 || $i == $cantidadProductoSerie) {
								TProductoEnviarStockDetalle::insert($datosInsertProducto);

								$datosInsertProducto = [];
								$datosInsertProductoEnviarStock = [];
								$sentinelProducto = 0;
							}
						}
					}
				}

				DB::commit();

				return $this->plataformHelper->redirectCorrecto('Operación realizada correctamente.', 'productoenviarstock/ver');
			}

			$listTOficina = TOficina::whereRaw('codigoEmpresa = ?', [$sessionManager->get('codigoEmpresa')])->get();
			$listTAlmacen = TAlmacen::whereRaw('codigoEmpresa = ?', [$sessionManager->get('codigoEmpresa')])->get();

			return View('productoenviarstock/insertar', ['listTOficina' => $listTOficina, 'listTAlmacen' => $listTAlmacen]);
		} catch (\Exception $e) {
			DB::rollback();

			return $this->plataformHelper->capturarExcepcion(__class__, __FUNCTION__, $e->getMessage(), '/');
		}
	}

	public function actionVer(Request $request, SessionManager $sessionManager, $pagina = 1)
	{
		if ($request->input('q')) {
			$term = $request->input('q');
			$paginationPrepare = null;

			if($request->input('searchPerformance')=='Performance')
			{
				$paginationPrepare = $this->plataformHelper->prepararPaginacion(TProductoEnviarStock::WhereHas('talmacen', function ($query) use ($term, $sessionManager) {
					$query->whereRaw('replace(concat(descripcion), \' \', \'\') like replace(?, \' \', \'\') and codigoEmpresa=?', ['%'.$term.'%', $sessionManager->get('codigoEmpresa')]);
				})
					->orWhereHas('toficina', function ($query) use ($term, $sessionManager) {
						$query->whereRaw('replace(concat(descripcion), \' \', \'\') like replace(?, \' \', \'\') and codigoEmpresa=?', ['%'.$term.'%', $sessionManager->get('codigoEmpresa')]);
	
					})
					->orWhereHas('tproductoenviarstockdetalle', function ($query) use ($term) {
						$query->whereRaw('replace(concat(codigoBarrasProducto, nombreProducto), \' \', \'\') like replace(?, \' \', \'\') ', ['%'.$term.'%']);
					})
					->WhereHas('toficina', function ($query) use ($sessionManager) {
						$query->whereRaw('codigoEmpresa=?', [$sessionManager->get('codigoEmpresa')]);
	
					})
					->orderBy('created_at', 'desc'), null, $pagina);
			}
			else
			{
				$paginationPrepare = $this->plataformHelper->prepararPaginacion(TProductoEnviarStock::WhereHas('talmacen', function ($query) use ($term, $sessionManager) {
					$query->whereRaw('compareFind(concat(descripcion), ?, 77)=1 and codigoEmpresa=?', [$term, $sessionManager->get('codigoEmpresa')]);
				})
					->orWhereHas('toficina', function ($query) use ($term, $sessionManager) {
						$query->whereRaw('compareFind(concat(descripcion), ?, 77)=1 and codigoEmpresa=?', [$term, $sessionManager->get('codigoEmpresa')]);
	
					})
					->orWhereHas('tproductoenviarstockdetalle', function ($query) use ($term) {
						$query->whereRaw('compareFind(concat(codigoBarrasProducto, nombreProducto), ?, 77)=1 ', [$term]);
					})
					->WhereHas('toficina', function ($query) use ($sessionManager) {
						$query->whereRaw('codigoEmpresa=?', [$sessionManager->get('codigoEmpresa')]);
	
					})
					->orderBy('created_at', 'desc'), null, $pagina);
			}

			$paginationRender = $this->plataformHelper->renderizarPaginacion('productoenviarstock/ver', $paginationPrepare["cantidadPaginas"], $pagina, $request->input('q'));

			return view('productoenviarstock/ver', ['listaTProductoEnviarStock' => $paginationPrepare["listaRegistros"], "pagination" => $paginationRender, "q" => $request->input('q')]);
		}

		$paginationPrepare = $this->plataformHelper->prepararPaginacion(TProductoEnviarStock::with('tproductoenviarstockdetalle')->WhereHas('talmacen', function ($query) use ($sessionManager) {
			$query->whereRaw('codigoEmpresa=?', [$sessionManager->get('codigoEmpresa')]);
		})->orderBy('created_at', 'desc'), null, $pagina);
		$paginationRender = $this->plataformHelper->renderizarPaginacion('productoenviarstock/ver', $paginationPrepare["cantidadPaginas"], $pagina);

		return view('productoenviarstock/ver', ['listaTProductoEnviarStock' => $paginationPrepare["listaRegistros"], "pagination" => $paginationRender]);
	}

	public function actionDetalle(Request $request)
	{
		$tProductoEnviarStock = TProductoEnviarStock::with('tproductoenviarstockdetalle')->whereRaw('codigoProductoEnviarStock=?', [$request->input('codigoProductoEnviarStock')])->first();

		return view('productoenviarstock/detalle', ['tProductoEnviarStock' => $tProductoEnviarStock]);
	}

	public function actionAnular($codigoProductoEnviarStock, SessionManager $sessionManager)
	{
		try {
			DB::beginTransaction();

			if ($sessionManager->has('codigoAlmacen') == null) {
				return $this->plataformHelper->redirectError('Debe estar logueado en un almacén para realizar esta operación.', '/');
			}

			$tProductoEnviarStock = TProductoEnviarStock::with('tproductoenviarstockdetalle')->whereRaw('codigoProductoEnviarStock=?', [$codigoProductoEnviarStock])->first();

			if ($tProductoEnviarStock == null || $tProductoEnviarStock->tProductoEnviarStockDetalle == null || $tProductoEnviarStock->tProductoEnviarStockDetalle->isEmpty()) {
				return $this->plataformHelper->redirectError('No se encontró el registro del traslado, contacte con el administrador.', 'productoenviarstock/ver');
			}

			if ($tProductoEnviarStock->codigoAlmacen != $sessionManager->get('codigoAlmacen')) {
				return $this->plataformHelper->redirectError('No se encontró el registro del traslado, contacte con el administrador.', 'productoenviarstock/ver');
			}

			$listaTOficinaProducto = TOficinaProducto::whereRaw('codigoOficina=?', [$tProductoEnviarStock->codigoOficina])->get();

			foreach ($tProductoEnviarStock->tProductoEnviarStockDetalle as $envioDetalle) {
				$producto = $listaTOficinaProducto->where('codigoBarras', $envioDetalle->codigoBarrasProducto)->firstWhere('nombre', $envioDetalle->nombreProducto);

				if ($producto == null || $producto->cantidad < $envioDetalle->cantidadProducto) {
					return $this->plataformHelper->redirectError('El stock del producto en la oficina no coincide con el stock del traslado.', 'productoenviarstock/ver');
				}

				$producto->cantidad -= $envioDetalle->cantidadProducto;

				$producto->save();

				$tAlmacenProducto = TAlmacenProducto::where('codigoBarras', $envioDetalle->codigoBarrasProducto)->Where('nombre', $envioDetalle->nombreProducto)->first();

				$tAlmacenProducto->cantidad += $envioDetalle->cantidadProducto;
				
				$tAlmacenProducto->save();
			}

			$tProductoEnviarStock->estado = false;
			
			$tProductoEnviarStock->save();

			DB::commit();

			return $this->plataformHelper->redirectCorrecto('Operación realizada correctamente.', 'productoenviarstock/ver');
		} catch (\Exception $e) {
			DB::rollback();

			return $this->plataformHelper->capturarExcepcion(__class__, __FUNCTION__, $e->getMessage(), '/');
		}
	}

	public function actionImprimirComprobante(SessionManager $sessionManager, Application $application, $codigoProductoEnviarStock)
	{
		$tProductoEnviarStock = TProductoEnviarStock::with(['tproductoenviarstockdetalle', 'talmacen', 'toficina'])->whereRaw('codigoProductoEnviarStock=?', [$codigoProductoEnviarStock])->first();
		
		$tEmpresa=TEmpresa::with(['toficina' => function($q) use($tProductoEnviarStock){ $q->whereRaw('codigoOficina=?', [$tProductoEnviarStock->codigoOficina]); }])->whereRaw('codigoEmpresa=?', [$sessionManager->get('codigoEmpresa')])->first();

		$nombreArchivoTemp=$tEmpresa->ruc.'-'.$tProductoEnviarStock->codigoProductoEnviarStock;

		$pdf=$application->make('dompdf.wrapper');

		$pathLogo=public_path().'/img/empresa/'.$tEmpresa->codigoEmpresa.'/logoEmpresarial.png';
		$typeLogo=pathinfo($pathLogo, PATHINFO_EXTENSION);
		$dataLogo=file_get_contents($pathLogo);
		$base64Logo='data:image/' . $typeLogo . ';base64,' . base64_encode($dataLogo);

		$pdf->loadHTML(view('productoenviarstock/comprobante', ['genericHelper' => $this->plataformHelper, 'tEmpresa' => $tEmpresa, 'tProductoEnviarStock' => $tProductoEnviarStock, 'base64Logo' => $base64Logo]));
		
		return $pdf->stream($nombreArchivoTemp.'.pdf', ['attachment' => false]);
	}
}