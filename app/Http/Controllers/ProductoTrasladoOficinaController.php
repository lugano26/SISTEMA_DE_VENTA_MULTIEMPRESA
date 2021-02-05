<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;
use Illuminate\Foundation\Application;

use DB;

use App\Model\TOficina;
use App\Model\TEmpresa;
use App\Model\TOficinaProducto;
use App\Model\TProductoTrasladoOficina;
use App\Model\TProductoTrasladoOficinaDetalle;

class ProductoTrasladoOficinaController extends Controller
{
	public function actionInsertar(Request $request, SessionManager $sessionManager)
	{
		try {
			if ($_POST) {
				DB::beginTransaction();

				if ($request->input('selectNombreOficinaOrigen') == $request->input('selectNombreOficinaDestino')) {
					DB::rollBack();

					$request->flash();

					return $this->plataformHelper->redirectError('Esta intentando hacer un traslado en la misma oficina', 'productotrasladooficina/insertar');
				}

				$tProductoTrasladoOficina = new TProductoTrasladoOficina();

				$tProductoTrasladoOficina->codigoOficina = $request->input('selectNombreOficinaOrigen');
				$tProductoTrasladoOficina->codigoOficinaLlegada = $request->input('selectNombreOficinaDestino');
				$tProductoTrasladoOficina->flete = 0;
				$tProductoTrasladoOficina->estado = true;
				$tProductoTrasladoOficina->motivoAnulacion = '';

				$tProductoTrasladoOficina->save();

				$ultimoRegistroTProductoTrasladoOficina = TProductoTrasladoOficina::whereRaw('codigoProductoTrasladoOficina=(select max(codigoProductoTrasladoOficina) from tproductotrasladooficina)')->first();

				foreach ($request->input('hdCodigoOficinaProducto') as $key => $value)
				{
					if(
						$request->input('hdPresentacion')[$key]==''
						|| $request->input('hdUnidadMedida')[$key]==''
						|| trim($request->input('hdNombre')[$key])==''
						|| !in_array($request->input('hdTipo')[$key], ['Genérico', 'Comercial'])
						|| !in_array($request->input('hdSituacionImpuesto')[$key], ['Afecto'])
						|| !in_array($request->input('hdTipoImpuesto')[$key], ['IGV'])
						|| trim($request->input('hdUnidadMedidaBloque')[$key])==''
					)
					{
						DB::rollBack();

						$request->flash();

						return $this->plataformHelper->redirectError('Datos incorrectos. Por favor no trate de aleterar el comportamiento del sistema.', 'productotrasladooficina/insertar');
					}

					if (!filter_var($request->input('hdRegistroSerieProducto')[$key], FILTER_VALIDATE_BOOLEAN)) {
						$tOficinaProducto = TOficinaProducto::find($request->input('hdCodigoOficinaProducto')[$key]);

						if ($tOficinaProducto == null) {
							DB::rollBack();

							$request->flash();

							return $this->plataformHelper->redirectError('No se encontró el producto ' . ($key + 1) . ' de la lista.', 'productotrasladooficina/insertar');
						}

						if ($tOficinaProducto->cantidad < $request->input('hdCantidadProducto')[$key]) {
							DB::rollBack();

							$request->flash();

							return $this->plataformHelper->redirectError('Stock insuficiente para el producto ' . ($key + 1) . ' de la lista.', 'productotrasladooficina/insertar');
						}

						if (!$tOficinaProducto->ventaMenorUnidad && !preg_match("/^[0-9]+(\.[0]*)?$/", $request->input('hdCantidadProducto')[$key])) {
							DB::rollBack();

							$request->flash();

							return $this->plataformHelper->redirectError('Sólo se permiten traslados por unidades enteras mayores a 1 en el producto ' . ($key + 1) . ' de la lista.', 'productotrasladooficina/insertar');
						}

						if (!$tOficinaProducto->ventaMenorUnidad && intval($request->input('hdCantidadProducto')[$key]) < 1) {
							DB::rollBack();

							$request->flash();

							return $this->plataformHelper->redirectError('Sólo se permiten traslados por unidades enteras mayores a 1 en el producto ' . ($key + 1) . ' de la lista.', 'productotrasladooficina/insertar');
						}

						if($tOficinaProducto->ventaMenorUnidad && $request->input('hdCantidadProducto')[$key] <= 0)
						{
							DB::rollBack();

							$request->flash();

							return $this->plataformHelper->redirectError('Sólo se permiten traslados mayores a 0 en el producto '.($key+1).' de la lista.', 'productotrasladooficina/insertar');
						}

						$tProductoTrasladoOficinaDetalle = new TProductoTrasladoOficinaDetalle();

						$tProductoTrasladoOficinaDetalle->codigoProductoTrasladoOficina = $ultimoRegistroTProductoTrasladoOficina->codigoProductoTrasladoOficina;
						$tProductoTrasladoOficinaDetalle->categoriaProducto = "";
						$tProductoTrasladoOficinaDetalle->codigoOficinaProducto = $request->input('hdCodigoOficinaProducto')[$key];
						$tProductoTrasladoOficinaDetalle->presentacionProducto = $request->input('hdPresentacion')[$key];
						$tProductoTrasladoOficinaDetalle->unidadMedidaProducto = $request->input('hdUnidadMedida')[$key];
						$tProductoTrasladoOficinaDetalle->codigoBarrasProducto = $request->input('hdCodigoBarras')[$key];
						$tProductoTrasladoOficinaDetalle->nombreProducto = $request->input('hdNombre')[$key];
						$tProductoTrasladoOficinaDetalle->descripcionProducto = $request->input('hdDescripcion')[$key];
						$tProductoTrasladoOficinaDetalle->tipoProducto = $request->input('hdTipo')[$key];
						$tProductoTrasladoOficinaDetalle->cantidadProducto = $request->input('hdCantidadProducto')[$key];
						$tProductoTrasladoOficinaDetalle->situacionImpuestoProducto = $request->input('hdSituacionImpuesto')[$key];
						$tProductoTrasladoOficinaDetalle->tipoImpuestoProducto = $request->input('hdTipoImpuesto')[$key];
						$tProductoTrasladoOficinaDetalle->porcentajeTributacionProducto = $request->input('hdPorcentajeTributacion')[$key];
						$tProductoTrasladoOficinaDetalle->cantidadMinimaAlertaStockProducto = $request->input('hdCantidadMinimaAlertaStock')[$key];
						$tProductoTrasladoOficinaDetalle->pesoGramosUnidadProducto = $request->input('hdPesoGramosUnidad')[$key];
						$tProductoTrasladoOficinaDetalle->ventaMenorUnidadProducto = $request->input('hdVentaMenorUnidad')[$key];
						$tProductoTrasladoOficinaDetalle->unidadesBloqueProducto = $request->input('hdUnidadesBloque')[$key];
						$tProductoTrasladoOficinaDetalle->unidadMedidaBloqueProducto = $request->input('hdUnidadMedidaBloque')[$key];
						$tProductoTrasladoOficinaDetalle->precioCompraUnitarioProducto = $request->input('hdPrecioCompraUnitario')[$key];
						$tProductoTrasladoOficinaDetalle->precioVentaUnitarioProducto = $request->input('hdPrecioVentaUnitario')[$key];
						$tProductoTrasladoOficinaDetalle->fechaVencimientoProducto = $request->input('hdFechaVencimiento')[$key];

						$tProductoTrasladoOficinaDetalle->save();
					} else {
						$tOficinaProductoAuditado = TOficinaProducto::find($request->input('hdCodigoOficinaProducto')[$key]);

						if ($tOficinaProductoAuditado == null) {
							DB::rollBack();

							$request->flash();

							return $this->plataformHelper->redirectError('No se encontró el producto ' . ($key + 1) . ' (en serie) de la lista.', 'productotrasladooficina/insertar');
						}

						if (!preg_match('/^[0-9]+$/', $tOficinaProductoAuditado->codigoBarras)) {
							DB::rollback();

							$request->flash();

							return $this->plataformHelper->redirectError('El código de barras del producto (en serie) ' . ($key + 1) . ' de la lista no puede generarse en serie, solo se permiten números.', 'productotrasladooficina/insertar');
						}

						$codigoBarrasProductoSerie = gmp_init($tOficinaProductoAuditado->codigoBarras);
						$cantidadProductoSerie = $request->input('hdCantidadProducto')[$key];
						$datosInsertProducto = [];
						$sentinelProducto = 0;

						if (!preg_match("/^[0-9]+(\.[0]*)?$/", $cantidadProductoSerie)) {
							DB::rollback();

							$request->flash();

							return $this->plataformHelper->redirectError('El envio de productos en serie solo se permiten en unidades enteras', 'productotrasladooficina/insertar');
						}

						for ($i = 1; $i <= $cantidadProductoSerie; $i++) {
							$nombreProductoSerie = substr(trim($tOficinaProductoAuditado->nombre), 0, strrpos(trim($tOficinaProductoAuditado->nombre), ' ')) . ' ' . $codigoBarrasProductoSerie;

							$tOficinaProducto = TOficinaProducto::whereRaw("codigoOficina=? and (replace(codigoBarras, ' ', '')=replace(?, ' ', '') or replace(nombre, ' ', '')=replace(?, ' ', ''))", [$request->input('selectNombreOficinaOrigen'), $codigoBarrasProductoSerie, $nombreProductoSerie])
								->whereHas('toficina', function ($q) use ($sessionManager) {
									$q->whereRaw('codigoEmpresa=?', [$sessionManager->get('codigoEmpresa')]);
								})
								->first();

							if ($tOficinaProducto == null) {
								DB::rollBack();

								$request->flash();

								return $this->plataformHelper->redirectError('No se encontró el producto ' . ($key + 1) . ' (en serie) de la lista.', 'productotrasladooficina/insertar');
							}

							if ($tOficinaProducto->cantidad < 1) {
								DB::rollBack();

								$request->flash();

								return $this->plataformHelper->redirectError('Stock insuficiente para el producto ' . ($key + 1) . ' (en serie) de la lista.', 'productotrasladooficina/insertar');
							}

							if (!$tOficinaProducto->ventaMenorUnidad && !preg_match("/^[0-9]+(\.[0]*)?$/", $request->input('hdCantidadProducto')[$key])) {
								DB::rollBack();

								$request->flash();

								return $this->plataformHelper->redirectError('Sólo se permiten translados por unidades enteras (> 1) en el producto ' . ($key + 1) . ' de la lista.', 'productotrasladooficina/insertar');
							}

							$datosInsertProducto[] = [
								'codigoProductoTrasladoOficina' => $ultimoRegistroTProductoTrasladoOficina->codigoProductoTrasladoOficina,
								'categoriaProducto' => '',
								'codigoOficinaProducto' => $tOficinaProducto->codigoOficinaProducto,
								'presentacionProducto' => $tOficinaProducto->presentacion,
								'unidadMedidaProducto' => $tOficinaProducto->unidadMedida,
								'codigoBarrasProducto' => $tOficinaProducto->codigoBarras,
								'nombreProducto' => $tOficinaProducto->nombre,
								'descripcionProducto' => $tOficinaProducto->descripcion,
								'tipoProducto' => $tOficinaProducto->tipo,
								'cantidadProducto' => 1,
								'situacionImpuestoProducto' => $tOficinaProducto->situacionImpuesto,
								'tipoImpuestoProducto' => $tOficinaProducto->tipoImpuesto,
								'porcentajeTributacionProducto' => $tOficinaProducto->porcentajeTributacion,
								'cantidadMinimaAlertaStockProducto' => $tOficinaProducto->cantidadMinimaAlertaStock,
								'pesoGramosUnidadProducto' => $tOficinaProducto->pesoGramosUnidad,
								'ventaMenorUnidadProducto' => $tOficinaProducto->ventaMenorUnidad,
								'unidadesBloqueProducto' => $tOficinaProducto->unidadesBloque,
								'unidadMedidaBloqueProducto' => $tOficinaProducto->unidadMedidaBloque,
								'precioCompraUnitarioProducto' => $tOficinaProducto->precioCompraUnitario,
								'precioVentaUnitarioProducto' => $tOficinaProducto->precioVentaUnitario,
								'fechaVencimientoProducto' => $tOficinaProducto->fechaVencimiento,
								'created_at' => date("Y-m-d H:i:s"),
								'updated_at' => date("Y-m-d H:i:s")
							];

							$codigoBarrasProductoSerie++;
							$sentinelProducto++;

							if ($sentinelProducto == 200 || $i == $cantidadProductoSerie) {
								TProductoTrasladoOficinaDetalle::insert($datosInsertProducto);

								$datosInsertProducto = [];
								$datosInsertProductoEnviarStock = [];
								$sentinelProducto = 0;
							}
						}
					}
				}

				DB::commit();

				return $this->plataformHelper->redirectCorrecto('Operación realizada correctamente.', 'productotrasladooficina/ver');
			}

			$listTOficina = TOficina::whereRaw('codigoEmpresa = ?', [$sessionManager->get('codigoEmpresa')])->get();

			return View('productotrasladooficina/insertar', ['listTOficina' => $listTOficina]);
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
				$paginationPrepare = $this->plataformHelper->prepararPaginacion(TProductoTrasladoOficina::WhereHas('toficinallegada', function ($query) use ($term, $sessionManager) {
					$query->whereRaw('replace(concat(descripcion), \' \', \'\') like replace(?, \' \', \'\')  and codigoEmpresa=?', ['%'.$term.'%', $sessionManager->get('codigoEmpresa')]);
	
				})
					->OrWhereHas('toficina', function ($query) use ($term, $sessionManager) {
						$query->whereRaw('replace(concat(descripcion), \' \', \'\') like replace(?, \' \', \'\')  and codigoEmpresa=?', ['%'.$term.'%', $sessionManager->get('codigoEmpresa')]);
	
					})
					->orWhereHas('tproductotrasladooficinadetalle', function ($query) use ($term) {
						$query->whereRaw('replace(concat(codigoBarrasProducto, nombreProducto), \' \', \'\') like replace(?, \' \', \'\') ', ['%'.$term.'%']);
					})
					->WhereHas('toficina', function ($query) use ($sessionManager) {
						$query->whereRaw('codigoEmpresa=?', [$sessionManager->get('codigoEmpresa')]);
	
					})
					->orderBy('created_at', 'desc'), null, $pagina);
			}
			else
			{
				$paginationPrepare = $this->plataformHelper->prepararPaginacion(TProductoTrasladoOficina::WhereHas('toficinallegada', function ($query) use ($term, $sessionManager) {
					$query->whereRaw('compareFind(concat(descripcion), ?, 77)=1  and codigoEmpresa=?', [$term, $sessionManager->get('codigoEmpresa')]);
	
				})
					->OrWhereHas('toficina', function ($query) use ($term, $sessionManager) {
						$query->whereRaw('compareFind(concat(descripcion), ?, 77)=1  and codigoEmpresa=?', [$term, $sessionManager->get('codigoEmpresa')]);
	
					})
					->orWhereHas('tproductotrasladooficinadetalle', function ($query) use ($term) {
						$query->whereRaw('compareFind(concat(codigoBarrasProducto, nombreProducto), ?, 77)=1 ', [$term]);
					})
					->WhereHas('toficina', function ($query) use ($sessionManager) {
						$query->whereRaw('codigoEmpresa=?', [$sessionManager->get('codigoEmpresa')]);
	
					})
					->orderBy('created_at', 'desc'), null, $pagina);
			}	

			$paginationRender = $this->plataformHelper->renderizarPaginacion('productotrasladooficina/ver', $paginationPrepare["cantidadPaginas"], $pagina, $request->input('q'));

			return view('productotrasladooficina/ver', ['listaTProductoTrasladoOficina' => $paginationPrepare["listaRegistros"], "pagination" => $paginationRender, "q" => $request->input('q')]);
		}

		$paginationPrepare = $this->plataformHelper->prepararPaginacion(TProductoTrasladoOficina::with('tproductotrasladooficinadetalle')
			->WhereHas('toficina', function ($query) use ($sessionManager) {
				$query->whereRaw('codigoEmpresa=?', [$sessionManager->get('codigoEmpresa')]);
			})->orderBy('created_at', 'desc'), null, $pagina);
		$paginationRender = $this->plataformHelper->renderizarPaginacion('productotrasladooficina/ver', $paginationPrepare["cantidadPaginas"], $pagina);

		return view('productotrasladooficina/ver', ['listaTProductoTrasladoOficina' => $paginationPrepare["listaRegistros"], "pagination" => $paginationRender]);
	}

	public function actionDetalle(Request $request)
	{
		$tProductoTrasladoOficina = TProductoTrasladoOficina::with('tproductotrasladooficinadetalle')->whereRaw('codigoProductoTrasladoOficina=?', [$request->input('codigoProductoTrasladoOficina')])->first();

		return view('productotrasladooficina/detalle', ['tProductoTrasladoOficina' => $tProductoTrasladoOficina]);
	}

	public function actionAnular($codigoProductoTrasladoOficina, SessionManager $sessionManager)
	{
		try {
			DB::beginTransaction();

			if ($sessionManager->has('codigoOficina') == null) {
				return $this->plataformHelper->redirectError('Debe estar logueado en una oficina para realizar esta operación.', '/');
			}

			$tProductoTrasladoOficina = TProductoTrasladoOficina::with('tproductotrasladooficinadetalle')->whereRaw('codigoProductoTrasladoOficina=?', [$codigoProductoTrasladoOficina])->first();

			if ($tProductoTrasladoOficina == null || $tProductoTrasladoOficina->tProductoTrasladoOficinaDetalle == null || $tProductoTrasladoOficina->tProductoTrasladoOficinaDetalle->isEmpty()) {
				return $this->plataformHelper->redirectError('No se encontró el registro del traslado, contacte con el administrador.', 'productotrasladooficina/ver');
			}

			if ($tProductoTrasladoOficina->codigoOficina != $sessionManager->get('codigoOficina')) {
				return $this->plataformHelper->redirectError('No se encontró el registro del traslado, contacte con el administrador.', 'productotrasladooficina/ver');
			}

			$listaTOficinaProducto = TOficinaProducto::whereRaw('codigoOficina=?', [$tProductoTrasladoOficina->codigoOficinaLlegada])->get();

			foreach ($tProductoTrasladoOficina->tProductoTrasladoOficinaDetalle as $envioDetalle) {
				$producto = $listaTOficinaProducto->where('codigoBarras', $envioDetalle->codigoBarrasProducto)->firstWhere('nombre', $envioDetalle->nombreProducto);

				if ($producto == null || $producto->cantidad < $envioDetalle->cantidadProducto) {
					return $this->plataformHelper->redirectError('El stock del producto en la oficina no coincide con el stock del traslado.', 'productotrasladooficina/ver');
				}

				$producto->cantidad -= $envioDetalle->cantidadProducto;

				$producto->save();

				$tOficinaProducto = tOficinaProducto::where('codigoBarras', $envioDetalle->codigoBarrasProducto)->Where('nombre', $envioDetalle->nombreProducto)
					->Where('codigoOficina', $sessionManager->get('codigoOficina'))->first();

				$tOficinaProducto->cantidad += $envioDetalle->cantidadProducto;

				$tOficinaProducto->save();
			}

			$tProductoTrasladoOficina->estado = false;
			
			$tProductoTrasladoOficina->save();

			DB::commit();

			return $this->plataformHelper->redirectCorrecto('Operación realizada correctamente.', 'productotrasladooficina/ver');
		} catch (\Exception $e) {
			DB::rollback();

			return $this->plataformHelper->capturarExcepcion(__class__, __FUNCTION__, $e->getMessage(), '/');
		}
	}

	public function actionImprimirComprobante(SessionManager $sessionManager, Application $application, $codigoProductoTrasladoOficina)
	{
		$tProductoTrasladoOficina = TProductoTrasladoOficina::with(['tproductotrasladooficinadetalle', 'toficinallegada', 'toficina'])->whereRaw('codigoProductoTrasladoOficina=?', [$codigoProductoTrasladoOficina])->first();
		
		$tEmpresa=TEmpresa::with(['toficina' => function($q) use($tProductoTrasladoOficina){ $q->whereRaw('codigoOficina=?', [$tProductoTrasladoOficina->codigoOficina]); }])->whereRaw('codigoEmpresa=?', [$sessionManager->get('codigoEmpresa')])->first();

		$nombreArchivoTemp=$tEmpresa->ruc.'-'.$tProductoTrasladoOficina->codigoProductoTrasladoOficina;

		$pdf=$application->make('dompdf.wrapper');

		$pathLogo=public_path().'/img/empresa/'.$tEmpresa->codigoEmpresa.'/logoEmpresarial.png';
		$typeLogo=pathinfo($pathLogo, PATHINFO_EXTENSION);
		$dataLogo=file_get_contents($pathLogo);
		$base64Logo='data:image/' . $typeLogo . ';base64,' . base64_encode($dataLogo);
		
		$pdf->loadHTML(view('productotrasladooficina/comprobante', ['genericHelper' => $this->plataformHelper, 'tEmpresa' => $tEmpresa, 'tProductoTrasladoOficina' => $tProductoTrasladoOficina, 'base64Logo' => $base64Logo]));
		
		return $pdf->stream($nombreArchivoTemp.'.pdf', ['attachment' => false]);
	}
}