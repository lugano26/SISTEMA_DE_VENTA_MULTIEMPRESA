<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;

use App\Validation\ReciboCompraValidation;

use DB;

use App\Model\TReciboCompra;
use App\Model\TReciboCompraDetalle;
use App\Model\TUnidadMedida;
use App\Model\TPresentacion;
use App\Model\TProveedor;
use App\Model\TOficina;
use App\Model\TCajaDetalle;
use App\Model\TAlmacenProducto;
use App\Model\TOficinaProducto;
use App\Model\TProductoEnviarStock;
use App\Model\TProductoEnviarStockDetalle;

class ReciboCompraController extends Controller
{
	public function actionInsertar(Request $request, SessionManager $sessionManager)
	{
		if ($_POST) {
			try {
				DB::beginTransaction();

				if (!($sessionManager->has('codigoAlmacen'))) {
					return $this->plataformHelper->redirectError('Debe estar logueado en un almacén para realizar esta operación.', '/');
				}

				$documentoIdentidadProveedor = explode('-', $request->input('selectProveedor'))[0];
				$nombreProveedor = explode('-', $request->input('selectProveedor'))[1];

				$this->mensajeGlobal=(new ReciboCompraValidation())->validationEditarAgrupado($request, $documentoIdentidadProveedor, $nombreProveedor);

				if($this->mensajeGlobal!='')
				{
					DB::rollBack();
					
					$request->flash();

					return $this->plataformHelper->redirectError($this->mensajeGlobal, 'recibocompra/insertar');
				}

				$tProveedor = TProveedor::whereRaw('codigoEmpresa=? and documentoIdentidad=?', [$sessionManager->get('codigoEmpresa'), $documentoIdentidadProveedor])->first();

				if ($tProveedor == null) {
					$tProveedor = new TProveedor();

					$tProveedor->codigoEmpresa = $sessionManager->get('codigoEmpresa');
					$tProveedor->documentoIdentidad = $documentoIdentidadProveedor;
					$tProveedor->nombre = $nombreProveedor;

					$tProveedor->save();

					$tProveedor = TProveedor::whereRaw('codigoProveedor=(select max(codigoProveedor) from tproveedor)')->first();
				}

				$tReciboCompra = new TReciboCompra();

				$tReciboCompra->codigoProveedor = $tProveedor->codigoProveedor;
				$tReciboCompra->codigoAlmacen = $sessionManager->get('codigoAlmacen');
				$tReciboCompra->descripcion = '';
				$tReciboCompra->impuestoAplicado = 0;
				$tReciboCompra->subTotal = 0;
				$tReciboCompra->total = 0;
				$tReciboCompra->tipoRecibo = $request->input('selectTipoRecibo');
				$tReciboCompra->numeroRecibo = trim($request->input('txtNumeroRecibo'));
				$tReciboCompra->numeroGuiaRemision = trim($request->input('txtNumeroGuiaRemision'));
				$tReciboCompra->comprobanteEmitido = true;
				$tReciboCompra->fechaComprobanteEmitido = $request->input('dateFechaComprobanteEmitido');
				$tReciboCompra->tipoPago = $request->input('selectTipoPago');
				$tReciboCompra->fechaPagar = $request->input('dateFechaPagar');
				$tReciboCompra->estadoCredito = $request->input('selectTipoPago') == 'Al contado' ? true : false;
				$tReciboCompra->estado = true;
				$tReciboCompra->motivoAnulacion = '';

				$tReciboCompra->save();

				$tReciboCompra = TReciboCompra::whereRaw('codigoReciboCompra=(select max(codigoReciboCompra) from trecibocompra)')->first();

				$tProductoEnviarStock = null;

				if ($request->input('selectCodigoOficina') != '') {
					$tProductoEnviarStock = new TProductoEnviarStock();

					$tProductoEnviarStock->codigoAlmacen = $sessionManager->get('codigoAlmacen');
					$tProductoEnviarStock->codigoOficina = $request->input('selectCodigoOficina');
					$tProductoEnviarStock->flete = 0;
					$tProductoEnviarStock->estado = true;
					$tProductoEnviarStock->motivoAnulacion = '';

					$tProductoEnviarStock->save();

					$tProductoEnviarStock = TProductoEnviarStock::whereRaw('codigoProductoEnviarStock=(select max(codigoProductoEnviarStock) from tproductoenviarstock)')->first();
				}

				$impuestoAplicado = 0;
				$total = 0;

				foreach ($request->input('hdNombreProducto') as $key => $value)
				{
					if(
						trim($request->input('hdNombreProducto')[$key])==''
						|| (
							$request->input('hdTipoProducto')[$key]!='Genérico'
							&& $request->input('hdTipoProducto')[$key]!='Comercial'
						)
						|| (
							$request->input('hdSituacionImpuestoProducto')[$key]!='Afecto'
						)
						|| (
							$request->input('hdTipoImpuestoProducto')[$key]!='IGV'
						)
					)
					{
						DB::rollBack();

						$request->flash();

						return $this->plataformHelper->redirectError('Datos incorrectos. Por favor no trate de aleterar el comportamiento del sistema.', 'recibocompra/insertar');
					}

					if (!filter_var($request->input('hdRegistroSerieProducto')[$key], FILTER_VALIDATE_BOOLEAN)) {
						$listaTAlmacenProductoTemp = TAlmacenProducto::whereRaw("(replace(codigoBarras, ' ', '')=replace(?, ' ', '') or replace(nombre, ' ', '')=replace(?, ' ', ''))", [$request->input('hdCodigoBarrasProducto')[$key], $request->input('hdNombreProducto')[$key]])->whereHas('talmacen', function ($q) use ($sessionManager) {
							$q->where('codigoEmpresa', $sessionManager->get('codigoEmpresa'));
						})->get();

						foreach ($listaTAlmacenProductoTemp as $index => $item) {
							if (trim($request->input('hdCodigoBarrasProducto')[$key]) != '' && ($item->codigoBarras != $request->input('hdCodigoBarrasProducto')[$key] || $item->nombre != $request->input('hdNombreProducto')[$key])) {
								DB::rollback();

								$request->flash();

								return $this->plataformHelper->redirectError('El código de barras y nombre del producto ' . ($key + 1) . ' de la lista es incoherente.', 'recibocompra/insertar');
							}
						}

						TAlmacenProducto::whereRaw("replace(concat(codigoBarras, nombre), ' ', '')=replace(?, ' ', '')", [$request->input('hdCodigoBarrasProducto')[$key] . $request->input('hdNombreProducto')[$key]])
							->whereHas('talmacen', function ($q) use ($sessionManager) {
								$q->where('codigoEmpresa', $sessionManager->get('codigoEmpresa'));
							})
							->update(
								[
									'codigoPresentacion' => $request->input('hdCodigoPresentacionProducto')[$key],
									'codigoUnidadMedida' => $request->input('hdCodigoUnidadMedidaProducto')[$key],
									'descripcion' => '',
									'tipo' => $request->input('hdTipoProducto')[$key],
									'situacionImpuesto' => $request->input('hdSituacionImpuestoProducto')[$key],
									'tipoImpuesto' => $request->input('hdTipoImpuestoProducto')[$key],
									'porcentajeTributacion' => $request->input('hdPorcentajeTributacionProducto')[$key],
									'cantidadMinimaAlertaStock' => $request->input('hdCantidadMinimaAlertaStockProducto')[$key],
									'precioCompraUnitario' => number_format($request->input('hdPrecioCompraUnitarioProducto')[$key], 2, '.', ''),
									'precioVentaUnitario' => number_format($request->input('hdPrecioVentaUnitarioProducto')[$key], 2, '.', ''),
									'ventaMenorUnidad' => $request->input('hdVentaMenorUnidadProducto')[$key],
									'fechaVencimiento' => $request->input('hdFechaVencimientoProducto')[$key] == '' ? '1111-11-11' : $request->input('hdFechaVencimientoProducto')[$key]
								]
							);

						TOficinaProducto::whereRaw("replace(concat(codigoBarras, nombre), ' ', '')=replace(?, ' ', '')", [$request->input('hdCodigoBarrasProducto')[$key] . $request->input('hdNombreProducto')[$key]])
							->whereHas('toficina', function ($q) use ($sessionManager) {
								$q->where('codigoEmpresa', $sessionManager->get('codigoEmpresa'));
							})
							->update(
								[
									'presentacion' => TPresentacion::find($request->input('hdCodigoPresentacionProducto')[$key])->nombre,
									'unidadMedida' => TUnidadMedida::find($request->input('hdCodigoUnidadMedidaProducto')[$key])->nombre,
									'descripcion' => '',
									'tipo' => $request->input('hdTipoProducto')[$key],
									'situacionImpuesto' => $request->input('hdSituacionImpuestoProducto')[$key],
									'tipoImpuesto' => $request->input('hdTipoImpuestoProducto')[$key],
									'porcentajeTributacion' => $request->input('hdPorcentajeTributacionProducto')[$key],
									'cantidadMinimaAlertaStock' => $request->input('hdCantidadMinimaAlertaStockProducto')[$key],
									'precioCompraUnitario' => number_format($request->input('hdPrecioCompraUnitarioProducto')[$key], 2, '.', ''),
									'precioVentaUnitario' => number_format($request->input('hdPrecioVentaUnitarioProducto')[$key], 2, '.', ''),
									'ventaMenorUnidad' => $request->input('hdVentaMenorUnidadProducto')[$key],
									'fechaVencimiento' => $request->input('hdFechaVencimientoProducto')[$key] == '' ? '1111-11-11' : $request->input('hdFechaVencimientoProducto')[$key]
								]
							);

						$tReciboCompraDetalle = new TReciboCompraDetalle();

						$tReciboCompraDetalle->codigoReciboCompra = $tReciboCompra->codigoReciboCompra;
						$tReciboCompraDetalle->codigoPresentacionProducto = $request->input('hdCodigoPresentacionProducto')[$key];
						$tReciboCompraDetalle->codigoUnidadMedidaProducto = $request->input('hdCodigoUnidadMedidaProducto')[$key];
						$tReciboCompraDetalle->codigoBarrasProducto = trim($request->input('hdCodigoBarrasProducto')[$key]);
						$tReciboCompraDetalle->nombreProducto = trim($request->input('hdNombreProducto')[$key]);
						$tReciboCompraDetalle->descripcionProducto = '';
						$tReciboCompraDetalle->tipoProducto = $request->input('hdTipoProducto')[$key];
						$tReciboCompraDetalle->situacionImpuestoProducto = $request->input('hdSituacionImpuestoProducto')[$key];
						$tReciboCompraDetalle->tipoImpuestoProducto = $request->input('hdTipoImpuestoProducto')[$key];
						$tReciboCompraDetalle->porcentajeTributacionProducto = $request->input('hdPorcentajeTributacionProducto')[$key];
						$tReciboCompraDetalle->impuestoAplicadoProducto = number_format($request->input('hdImpuestoAplicadoProducto')[$key], 2, '.', '');
						$tReciboCompraDetalle->cantidadMinimaAlertaStockProducto = $request->input('hdCantidadMinimaAlertaStockProducto')[$key];
						$tReciboCompraDetalle->pesoGramosUnidadProducto = $request->input('hdPesoGramosUnidadProducto')[$key];
						$tReciboCompraDetalle->precioCompraTotalProducto = number_format($request->input('hdPrecioCompraTotalProducto')[$key], 2, '.', '');
						$tReciboCompraDetalle->precioCompraUnitarioProducto = number_format($request->input('hdPrecioCompraUnitarioProducto')[$key], 2, '.', '');
						$tReciboCompraDetalle->precioVentaUnitarioProducto = number_format($request->input('hdPrecioVentaUnitarioProducto')[$key], 2, '.', '');
						$tReciboCompraDetalle->cantidadProducto = $request->input('hdCantidadProducto')[$key];
						$tReciboCompraDetalle->ventaMenorUnidadProducto = $request->input('hdVentaMenorUnidadProducto')[$key];
						$tReciboCompraDetalle->unidadesBloqueProducto = 12;
						$tReciboCompraDetalle->unidadMedidaBloqueProducto = 'Docena';
						$tReciboCompraDetalle->fechaVencimientoProducto = $request->input('hdFechaVencimientoProducto')[$key] == '' ? '1111-11-11' : $request->input('hdFechaVencimientoProducto')[$key];

						$tReciboCompraDetalle->save();

						$impuestoAplicado += number_format($request->input('hdImpuestoAplicadoProducto')[$key], 2, '.', '');
						$total += number_format($request->input('hdPrecioCompraTotalProducto')[$key], 2, '.', '');

						if ($request->input('selectCodigoOficina') != '') {
							$tAlmacenProducto = TAlmacenProducto::whereRaw("replace(concat(codigoAlmacen, codigoBarras, nombre), ' ', '')=replace(?, ' ', '')", [$sessionManager->get('codigoAlmacen') . trim($request->input('hdCodigoBarrasProducto')[$key]) . trim($request->input('hdNombreProducto')[$key])])->first();

							$tProductoEnviarStockDetalle = new TProductoEnviarStockDetalle();

							$tProductoEnviarStockDetalle->codigoProductoEnviarStock = $tProductoEnviarStock->codigoProductoEnviarStock;
							$tProductoEnviarStockDetalle->codigoAlmacenProducto = $tAlmacenProducto->codigoAlmacenProducto;
							$tProductoEnviarStockDetalle->codigoPresentacionProducto = $request->input('hdCodigoPresentacionProducto')[$key];
							$tProductoEnviarStockDetalle->codigoUnidadMedidaProducto = $request->input('hdCodigoUnidadMedidaProducto')[$key];
							$tProductoEnviarStockDetalle->codigoBarrasProducto = trim($request->input('hdCodigoBarrasProducto')[$key]);
							$tProductoEnviarStockDetalle->nombreProducto = trim($request->input('hdNombreProducto')[$key]);
							$tProductoEnviarStockDetalle->descripcionProducto = '';
							$tProductoEnviarStockDetalle->tipoProducto = $request->input('hdTipoProducto')[$key];
							$tProductoEnviarStockDetalle->situacionImpuestoProducto = $request->input('hdSituacionImpuestoProducto')[$key];
							$tProductoEnviarStockDetalle->tipoImpuestoProducto = $request->input('hdTipoImpuestoProducto')[$key];
							$tProductoEnviarStockDetalle->porcentajeTributacionProducto = $request->input('hdPorcentajeTributacionProducto')[$key];
							$tProductoEnviarStockDetalle->cantidadMinimaAlertaStockProducto = $request->input('hdCantidadMinimaAlertaStockProducto')[$key];
							$tProductoEnviarStockDetalle->pesoGramosUnidadProducto = $request->input('hdPesoGramosUnidadProducto')[$key];
							$tProductoEnviarStockDetalle->precioCompraUnitarioProducto = number_format($request->input('hdPrecioCompraUnitarioProducto')[$key], 2, '.', '');
							$tProductoEnviarStockDetalle->precioVentaUnitarioProducto = number_format($request->input('hdPrecioVentaUnitarioProducto')[$key], 2, '.', '');
							$tProductoEnviarStockDetalle->cantidadProducto = $request->input('hdCantidadProducto')[$key];
							$tProductoEnviarStockDetalle->ventaMenorUnidadProducto = $request->input('hdVentaMenorUnidadProducto')[$key];
							$tProductoEnviarStockDetalle->unidadesBloqueProducto = 12;
							$tProductoEnviarStockDetalle->unidadMedidaBloqueProducto = 'Docena';
							$tProductoEnviarStockDetalle->fechaVencimientoProducto = $request->input('hdFechaVencimientoProducto')[$key] == '' ? '1111-11-11' : $request->input('hdFechaVencimientoProducto')[$key];

							$tProductoEnviarStockDetalle->save();
						}
					} else {
						if (!preg_match('/^[0-9]+$/', $request->input('hdCodigoBarrasProducto')[$key])) {
							DB::rollback();

							$request->flash();

							return $this->plataformHelper->redirectError('El código de barras del producto (en serie) ' . ($key + 1) . ' de la lista no puede generarse en serie, solo se permiten números.', 'recibocompra/insertar');
						}

						$codigoBarrasProductoSerie = gmp_init($request->input('hdCodigoBarrasProducto')[$key]);
						$codigoBarrasProductoSerieInicial = gmp_init($request->input('hdCodigoBarrasProducto')[$key]);
						$cantidadProductoSerie = $request->input('hdCantidadProducto')[$key];
						$valorOperacionalImpuesto = number_format(((floatval($request->input('hdPorcentajeTributacionProducto')[$key]) / 100) + 1), 2, '.', '');
						$impuestoAplicadoProducto = number_format(abs((number_format($request->input('hdPrecioCompraUnitarioProducto')[$key], 2, '.', '') / $valorOperacionalImpuesto) - number_format($request->input('hdPrecioCompraUnitarioProducto')[$key], 2, '.', '')), 2, '.', '');
						$datosInsertProducto = [];
						$datosInsertProductoEnviarStock = [];
						$sentinelProducto = 0;

						/** Serie parecida */
						$codigoBarrasProductoSerieFinal = $codigoBarrasProductoSerieInicial + $cantidadProductoSerie - 1;
						$cantidadInicial = strlen(strval($codigoBarrasProductoSerieInicial));
						$cantidadFinal = strlen(strval($codigoBarrasProductoSerieFinal));
						$similarity = '';

						for($cen = 0; $cen < max($cantidadInicial, $cantidadFinal); $cen++)
						{
							if($cen <= $cantidadInicial && $cen <= $cantidadInicial && strval($codigoBarrasProductoSerieInicial)[$cen] == strval($codigoBarrasProductoSerieFinal)[$cen])
							{
								$similarity .= strval($codigoBarrasProductoSerieInicial)[$cen];
							}
							else
							{
								break;
							}
						}
						/** */						

						if (!preg_match("/^[0-9]+(\.[0]*)?$/", $cantidadProductoSerie)) {
							DB::rollback();

							$request->flash();

							return $this->plataformHelper->redirectError('La compra de productos en serie solo se permite en unidades enteras.', 'recibocompra/insertar');
						}

						TAlmacenProducto::whereRaw("replace(nombre, ' ', '') like replace(?, ' ', '') and codigoBarras >= ? and codigoBarras <= ?", [$request->input('hdNombreProducto')[$key]. ' ' .$similarity. '%', $codigoBarrasProductoSerieInicial, $codigoBarrasProductoSerieInicial + $cantidadProductoSerie - 1])
							->whereHas('talmacen', function ($q) use ($sessionManager) {
								$q->where('codigoEmpresa', $sessionManager->get('codigoEmpresa'));
							})
							->update(
								[
									'codigoPresentacion' => $request->input('hdCodigoPresentacionProducto')[$key],
									'codigoUnidadMedida' => $request->input('hdCodigoUnidadMedidaProducto')[$key],
									'descripcion' => '',
									'tipo' => $request->input('hdTipoProducto')[$key],
									'situacionImpuesto' => $request->input('hdSituacionImpuestoProducto')[$key],
									'tipoImpuesto' => $request->input('hdTipoImpuestoProducto')[$key],
									'porcentajeTributacion' => $request->input('hdPorcentajeTributacionProducto')[$key],
									'cantidadMinimaAlertaStock' => $request->input('hdCantidadMinimaAlertaStockProducto')[$key],
									'pesoGramosUnidad' => $request->input('hdPesoGramosUnidadProducto')[$key],
									'precioCompraUnitario' => number_format($request->input('hdPrecioCompraUnitarioProducto')[$key], 2, '.', ''),
									'precioVentaUnitario' => number_format($request->input('hdPrecioVentaUnitarioProducto')[$key], 2, '.', ''),
									'ventaMenorUnidad' => $request->input('hdVentaMenorUnidadProducto')[$key],
									'fechaVencimiento' => $request->input('hdFechaVencimientoProducto')[$key] == '' ? '1111-11-11' : $request->input('hdFechaVencimientoProducto')[$key]
								]
							);

						TOficinaProducto::whereRaw("replace(nombre, ' ', '') like replace(?, ' ', '') and codigoBarras >= ? and codigoBarras <= ?", [$request->input('hdNombreProducto')[$key]. ' ' .$similarity . '%', $codigoBarrasProductoSerieInicial, $codigoBarrasProductoSerieInicial + $cantidadProductoSerie - 1])
							->whereHas('toficina', function ($q) use ($sessionManager) {
								$q->where('codigoEmpresa', $sessionManager->get('codigoEmpresa'));
							})
							->update(
								[
									'presentacion' => TPresentacion::find($request->input('hdCodigoPresentacionProducto')[$key])->nombre,
									'unidadMedida' => TUnidadMedida::find($request->input('hdCodigoUnidadMedidaProducto')[$key])->nombre,
									'descripcion' => '',
									'tipo' => $request->input('hdTipoProducto')[$key],
									'situacionImpuesto' => $request->input('hdSituacionImpuestoProducto')[$key],
									'tipoImpuesto' => $request->input('hdTipoImpuestoProducto')[$key],
									'porcentajeTributacion' => $request->input('hdPorcentajeTributacionProducto')[$key],
									'cantidadMinimaAlertaStock' => $request->input('hdCantidadMinimaAlertaStockProducto')[$key],
									'pesoGramosUnidad' => $request->input('hdPesoGramosUnidadProducto')[$key],
									'precioCompraUnitario' => number_format($request->input('hdPrecioCompraUnitarioProducto')[$key], 2, '.', ''),
									'precioVentaUnitario' => number_format($request->input('hdPrecioVentaUnitarioProducto')[$key], 2, '.', ''),
									'ventaMenorUnidad' => $request->input('hdVentaMenorUnidadProducto')[$key],
									'fechaVencimiento' => $request->input('hdFechaVencimientoProducto')[$key] == '' ? '1111-11-11' : $request->input('hdFechaVencimientoProducto')[$key]
								]
							);
						
						/** Data preparada */
						$auxDate = date("Y-m-d H:i:s");
						$codigoPresentacionProducto = $request->input('hdCodigoPresentacionProducto')[$key];
						$codigoUnidadMedidaProducto = $request->input('hdCodigoUnidadMedidaProducto')[$key];
						$tipoProducto = $request->input('hdTipoProducto')[$key];
						$situacionImpuestoProducto = $request->input('hdSituacionImpuestoProducto')[$key];
						$tipoImpuestoProducto = $request->input('hdTipoImpuestoProducto')[$key];
						$porcentajeTributacionProducto = $request->input('hdPorcentajeTributacionProducto')[$key];
						$cantidadMinimaAlertaStockProducto = $request->input('hdCantidadMinimaAlertaStockProducto')[$key];
						$pesoGramosUnidadProducto = $request->input('hdPesoGramosUnidadProducto')[$key];
						$precioCompraTotalProducto = number_format($request->input('hdPrecioCompraUnitarioProducto')[$key], 2, '.', '');
						$precioCompraUnitarioProducto = number_format($request->input('hdPrecioCompraUnitarioProducto')[$key], 2, '.', '');
						$precioVentaUnitarioProducto = number_format($request->input('hdPrecioVentaUnitarioProducto')[$key], 2, '.', '');				
						$fechaVencimientoProducto = $request->input('hdFechaVencimientoProducto')[$key] == '' ? '1111-11-11' : $request->input('hdFechaVencimientoProducto')[$key];
						$ventaMenorUnidadProducto = $request->input('hdVentaMenorUnidadProducto')[$key];

						for ($i = 1; $i <= $cantidadProductoSerie; $i++) {
							$nombreProductoSerie = $request->input('hdNombreProducto')[$key] . ' ' . $codigoBarrasProductoSerie;

							if($i == 1 || $i == $cantidadProductoSerie || $i == (intval($cantidadProductoSerie / 2)))
							{
								$listaTAlmacenProductoTemp = TAlmacenProducto::whereRaw("(replace(codigoBarras, ' ', '')=replace(?, ' ', '') or replace(nombre, ' ', '')=replace(?, ' ', ''))", [$codigoBarrasProductoSerie, $nombreProductoSerie])->whereHas('talmacen', function ($q) use ($sessionManager) {
									$q->where('codigoEmpresa', $sessionManager->get('codigoEmpresa'));
								})->get();
	
								foreach ($listaTAlmacenProductoTemp as $index => $item) {
									if (trim($codigoBarrasProductoSerie) != '' && ($item->codigoBarras != $codigoBarrasProductoSerie || $item->nombre != $nombreProductoSerie)) {
										DB::rollback();
	
										$request->flash();
	
										return $this->plataformHelper->redirectError('El código de barras y nombre del producto (en serie) ' . ($key + 1) . ' de la lista es incoherente.', 'recibocompra/insertar');
									}
								}
							}

							$datosInsertProducto[] = [
								'codigoReciboCompra' => $tReciboCompra->codigoReciboCompra,
								'codigoPresentacionProducto' => $codigoPresentacionProducto,
								'codigoUnidadMedidaProducto' => $codigoUnidadMedidaProducto,
								'codigoBarrasProducto' => $codigoBarrasProductoSerie,
								'nombreProducto' => $nombreProductoSerie,
								'descripcionProducto' => '',
								'tipoProducto' => $tipoProducto,
								'situacionImpuestoProducto' => $situacionImpuestoProducto,
								'tipoImpuestoProducto' => $tipoImpuestoProducto,
								'porcentajeTributacionProducto' => $porcentajeTributacionProducto,
								'impuestoAplicadoProducto' => $impuestoAplicadoProducto,
								'cantidadMinimaAlertaStockProducto' => $cantidadMinimaAlertaStockProducto,
								'pesoGramosUnidadProducto' => $pesoGramosUnidadProducto,
								'precioCompraTotalProducto' => $precioCompraTotalProducto,
								'precioCompraUnitarioProducto' => $precioCompraUnitarioProducto,
								'precioVentaUnitarioProducto' => $precioVentaUnitarioProducto,
								'cantidadProducto' => 1,
								'ventaMenorUnidadProducto' => $ventaMenorUnidadProducto,
								'unidadesBloqueProducto' => 12,
								'unidadMedidaBloqueProducto' => 'Docena',
								'fechaVencimientoProducto' => $fechaVencimientoProducto,
								'created_at' => $auxDate,
								'updated_at' => $auxDate
							];

							if ($request->input('selectCodigoOficina') != '') {
								$datosInsertProductoEnviarStock[] = [
									"codigoProductoEnviarStock" => $tProductoEnviarStock->codigoProductoEnviarStock,
									"codigoAlmacenProducto" => null,
									"codigoPresentacionProducto" => $codigoPresentacionProducto,
									"codigoUnidadMedidaProducto" => $codigoUnidadMedidaProducto,
									"codigoBarrasProducto" => trim($codigoBarrasProductoSerie),
									"nombreProducto" => trim($nombreProductoSerie),
									"descripcionProducto" => '',
									"tipoProducto" => $tipoProducto,
									"situacionImpuestoProducto" => $situacionImpuestoProducto,
									"tipoImpuestoProducto" => $tipoImpuestoProducto,
									"porcentajeTributacionProducto" => $porcentajeTributacionProducto,
									"cantidadMinimaAlertaStockProducto" => $cantidadMinimaAlertaStockProducto,
									'pesoGramosUnidadProducto' => $pesoGramosUnidadProducto,
									"precioCompraUnitarioProducto" => $precioCompraUnitarioProducto,
									"precioVentaUnitarioProducto" => $precioVentaUnitarioProducto,
									"cantidadProducto" => 1,
									"ventaMenorUnidadProducto" => $ventaMenorUnidadProducto,
									"unidadesBloqueProducto" => 12,
									"unidadMedidaBloqueProducto" => 'Docena',
									"fechaVencimientoProducto" => $fechaVencimientoProducto,
									'created_at' => $auxDate,
									'updated_at' => $auxDate
								];
							}

							$codigoBarrasProductoSerie++;
							$sentinelProducto++;

							$impuestoAplicado += $impuestoAplicadoProducto;
							$total += number_format($request->input('hdPrecioCompraUnitarioProducto')[$key], 2, '.', '');

							if ($sentinelProducto == 200 || $i == $cantidadProductoSerie) {
								TReciboCompraDetalle::insert($datosInsertProducto);

								if ($request->input('selectCodigoOficina') != '') {
									$tAlmacenProductoNUltimos = TAlmacenProducto::whereRaw('codigoBarras >= ?', [$codigoBarrasProductoSerieInicial])->orderBy('codigoBarras', 'asc')->orderBy('created_at', 'desc')->take(count($datosInsertProducto))->get();

									foreach ($tAlmacenProductoNUltimos as $clave => $productoRegistrado) {
										$datosInsertProductoEnviarStock[$clave]['codigoAlmacenProducto'] = $productoRegistrado->codigoAlmacenProducto;
									}

									TProductoEnviarStockDetalle::insert($datosInsertProductoEnviarStock);
								}

								$datosInsertProducto = [];
								$datosInsertProductoEnviarStock = [];
								$sentinelProducto = 0;
								$codigoBarrasProductoSerieInicial = $codigoBarrasProductoSerie;
							}
						}
					}
				}

				$impuestoAplicado = number_format($impuestoAplicado, 2, '.', '');
				$total = number_format($total, 2, '.', '');

				$tReciboCompra->impuestoAplicado = $impuestoAplicado;
				$tReciboCompra->subTotal = number_format($total - $impuestoAplicado, 2, '.', '');
				$tReciboCompra->total = $total;

				$tReciboCompra->save();

				$tCajaDetalle = TCajaDetalle::find($sessionManager->get('codigoCajaDetalle'));

				$tCajaDetalle->egresos += $total;
				$tCajaDetalle->saldoFinal -= $total;

				$tCajaDetalle->save();

				DB::commit();

				return $this->plataformHelper->redirectCorrecto('Operación realizada correctamente.', 'recibocompra/insertar');
			} catch (\Exception $e) {
				DB::rollback();

				return $this->plataformHelper->capturarExcepcion(__class__, __FUNCTION__, $e->getMessage(), '/');
			}
		}

		if (!($sessionManager->has('codigoAlmacen'))) {
			return $this->plataformHelper->redirectError('Debe estar logueado en un almacén para realizar esta operación.', '/');
		}

		$listaTProveedor = TProveedor::whereRaw('codigoEmpresa=?', [$sessionManager->get('codigoEmpresa')])->get();
		$listaTOficina = TOficina::whereRaw('codigoEmpresa=?', [$sessionManager->get('codigoEmpresa')])->get();
		$listaTUnidadMedida = TUnidadMedida::all();
		$listaTPresentacion = TPresentacion::all();

		return view('recibocompra/insertar', ['listaTProveedor' => $listaTProveedor, 'listaTOficina' => $listaTOficina, 'listaTUnidadMedida' => $listaTUnidadMedida, 'listaTPresentacion' => $listaTPresentacion]);
	}

	public function actionVer(Request $request, SessionManager $sessionManager, $pagina = 1)
	{
		if (!($sessionManager->has('codigoAlmacen'))) {
			return $this->plataformHelper->redirectError('Debe estar logueado en un almacén para realizar esta operación.', '/');
		}

		if ($request->input('q')) {
			$term = $request->input('q');
			$paginationPrepare = null;

			if($request->input('searchPerformance')=='Performance')
			{
				$paginationPrepare = $this->plataformHelper->prepararPaginacion(TReciboCompra::with('trecibocompradetalle')->whereRaw('(replace(concat(numeroRecibo, numeroGuiaRemision, tipoPago, tipoRecibo), \' \', \'\') like replace(?, \' \', \'\') or codigoProveedor in (select codigoProveedor from tproveedor where replace(concat(nombre, documentoIdentidad), \' \', \'\') like replace(?, \' \', \'\'))) and codigoAlmacen=?', ['%'.$term.'%', '%'.$term.'%', $sessionManager->get('codigoAlmacen')])
				->orWhereHas('trecibocompradetalle', function ($query) use ($term) {
					$query->whereRaw('replace(concat(codigoBarrasProducto, nombreProducto), \' \', \'\') like replace(?, \' \', \'\') ', ['%'.$term.'%']);
				})
				->whereRaw('codigoAlmacen=?', [$sessionManager->get('codigoAlmacen')])
				->orderBy('created_at', 'desc'), null, $pagina);
			}
			else
			{
				$paginationPrepare = $this->plataformHelper->prepararPaginacion(TReciboCompra::with('trecibocompradetalle')->whereRaw('(compareFind(concat(numeroRecibo, numeroGuiaRemision, tipoPago, tipoRecibo), ?, 77)=1 or codigoProveedor in (select codigoProveedor from tproveedor where compareFind(concat(nombre, documentoIdentidad), ?, 77)=1)) and codigoAlmacen=?', [$term, $term, $sessionManager->get('codigoAlmacen')])
				->orWhereHas('trecibocompradetalle', function ($query) use ($term) {
					$query->whereRaw('compareFind(concat(codigoBarrasProducto, nombreProducto), ?, 77)=1 ', [$term]);
				})
				->whereRaw('codigoAlmacen=?', [$sessionManager->get('codigoAlmacen')])
				->orderBy('created_at', 'desc'), null, $pagina);
			}	

			$paginationRender = $this->plataformHelper->renderizarPaginacion('recibocompra/ver', $paginationPrepare["cantidadPaginas"], $pagina, $request->input('q'));

			return view('recibocompra/ver', ['listaTReciboCompra' => $paginationPrepare["listaRegistros"], "pagination" => $paginationRender, "q" => $request->input('q')]);
		}

		$paginationPrepare = $this->plataformHelper->prepararPaginacion(TReciboCompra::with('trecibocompradetalle')->whereRaw('codigoAlmacen=?', [$sessionManager->get('codigoAlmacen')])->orderBy('created_at', 'desc'), null, $pagina);
		$paginationRender = $this->plataformHelper->renderizarPaginacion('recibocompra/ver', $paginationPrepare["cantidadPaginas"], $pagina);

		return view('recibocompra/ver', ['listaTReciboCompra' => $paginationPrepare["listaRegistros"], "pagination" => $paginationRender]);
	}

	public function actionDetalle(Request $request)
	{
		$tReciboCompra = TReciboCompra::with('trecibocompradetalle')->whereRaw('codigoReciboCompra=?', [$request->input('codigoReciboCompra')])->first();

		return view('recibocompra/detalle', ['tReciboCompra' => $tReciboCompra]);
	}

	public function actionAnular($codigoReciboCompra, SessionManager $sessionManager)
	{
		try {
			DB::beginTransaction();

			if ($sessionManager->has('codigoAlmacen') == null) {
				return $this->plataformHelper->redirectError('Debe estar logueado en un almacén para realizar esta operación.', '/');
			}

			$tReciboCompra = TReciboCompra::with('trecibocompradetalle')->whereRaw('codigoReciboCompra=?', [$codigoReciboCompra])->first();

			if ($tReciboCompra == null || $tReciboCompra->tReciboCompraDetalle == null || $tReciboCompra->tReciboCompraDetalle->isEmpty()) {
				return $this->plataformHelper->redirectError('No se encontro el almacen, contacte con el administrador.', 'recibocompra/ver');
			}

			if ($tReciboCompra->codigoAlmacen != $sessionManager->get('codigoAlmacen')) {
				return $this->plataformHelper->redirectError('No se encontro el almacen, contacte con el administrador.', 'recibocompra/ver');
			}

			$listaTAlmacenProducto = TAlmacenProducto::whereRaw('codigoAlmacen=?', [$sessionManager->get('codigoAlmacen')])->get();

			foreach ($tReciboCompra->tReciboCompraDetalle as $reciboDetalle) {
				$producto = $listaTAlmacenProducto->where('codigoBarras', $reciboDetalle->codigoBarrasProducto)->firstWhere('nombre', $reciboDetalle->nombreProducto);

				if ($producto == null || $producto->cantidad < $reciboDetalle->cantidadProducto) {
					return $this->plataformHelper->redirectError('El stock del producto en el almacen no coincide con el stock de la compra.', 'recibocompra/ver');
				}

				$producto->cantidad -= $reciboDetalle->cantidadProducto;
				$producto->save();
			}

			$tReciboCompra->estado = false;
			$tReciboCompra->save();

			$tCajaDetalle = TCajaDetalle::find($sessionManager->get('codigoCajaDetalle'));

			$tCajaDetalle->ingresos += $tReciboCompra->total;
			$tCajaDetalle->saldoFinal += $tReciboCompra->total;

			$tCajaDetalle->save();

			DB::commit();

			return $this->plataformHelper->redirectCorrecto('Operación realizada correctamente.', 'recibocompra/ver');
		} catch (\Exception $e) {
			DB::rollback();

			return $this->plataformHelper->capturarExcepcion(__class__, __FUNCTION__, $e->getMessage(), '/');
		}
	}
}
?>