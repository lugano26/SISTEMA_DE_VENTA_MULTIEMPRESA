<?php
namespace App\Http\Controllers;

use DB;
use App\Model\TCaja;
use App\Model\TEgreso;
use App\Model\TAlmacen;
use App\Model\TEmpresa;

use App\Model\TOficina;

use App\Model\TAmbiente;
use App\Model\TInventario;
use App\Model\TReciboVenta;
use App\Model\TReciboCompra;
use Illuminate\Http\Request;
use App\Exports\ReportGeneratorExport;
use App\Model\TCategoriaVenta;
use App\Model\TAlmacenProducto;
use App\Model\TOficinaProducto;
use App\Model\TReciboVentaPago;
use App\Model\TReciboCompraPago;
use App\Model\TReciboVentaOutEf;
use App\Model\TProductoEnviarStock;
use App\Http\Controllers\Controller;
use App\Model\TReciboVentaPagoOutEf;
use App\Model\TAlmacenProductoRetiro;
use App\Model\TOficinaProductoRetiro;
use App\Model\TReciboVentaNotaDebito;
use App\Model\TDocumentoGeneradoSunat;
use App\Model\TReciboVentaNotaCredito;
use Illuminate\Foundation\Application;
use Illuminate\Session\SessionManager;
use App\Model\TProductoTrasladoOficina;
use Maatwebsite\Excel\Events\AfterSheet;

class ReporteController extends Controller
{
	public function actionIndex(Request $request)
	{
		return view('reporte/index');
	}

	public function actionGeneralConsolidado(Request $request, SessionManager $sessionManager, Application $application)
	{
		if ($request->has('reporte')) 
		{
			if (!(strpos($sessionManager->get('rol'), 'Súper usuario') !== false || (((strpos($sessionManager->get('rol'), 'Administrador') !== false || strpos($sessionManager->get('rol'), 'Ventas') !== false)) && strpos($sessionManager->get('rol'), 'Reporteador') !== false))) {
				return $this->plataformHelper->redirectError('No tiene permisos para generar este reporte.', 'reporte/index');
			}

			$fechaInicial = $request->input('fechaInicial');
			$fechaFinal = $request->input('fechaFinal');
			$codigoOficina = $request->input('codOficina') == null ? '' : $request->input('codOficina');
			$codigoAlmacen = $request->input('codAlmacen') == null ? '' : $request->input('codAlmacen');
			$tOficina = TOficina::find($codigoOficina);
			$tAlmacen = TAlmacen::find($codigoAlmacen);

			$listaTCaja = TCaja::with(['tcajadetalle'])->WhereRaw('created_at between ? and ? and codigoEmpresa=?', [$fechaInicial, $fechaFinal, $sessionManager->get('codigoEmpresa')])->orderBy('created_at', 'desc')->get();

			$listaTEgreso = TEgreso::with('tpersonal')->WhereRaw('created_at between ? and ? ' . ($codigoOficina != '' ? 'and codigoOficina=?' : 'and codigoOficina in (select codigoOficina from toficina where codigoEmpresa=?)'), 
			[
				$fechaInicial, $fechaFinal,
				($codigoOficina != '' ? $codigoOficina : $sessionManager->get('codigoEmpresa'))
			])
			->orderBy('created_at', 'desc')->get();
			
			$listaTReciboCompra = TReciboCompra::with(['trecibocompradetalle', 'talmacen', 'tproveedor'])
					->WhereRaw(
						'created_at between ? and ? ' . ($codigoAlmacen != '' ? 'and codigoAlmacen=?' : 'and codigoAlmacen in (select codigoAlmacen from talmacen where codigoEmpresa=?)')
						, 
						[
							$fechaInicial, $fechaFinal,
							($codigoAlmacen != '' ? $codigoAlmacen : $sessionManager->get('codigoEmpresa'))
						])
					->orderBy('codigoAlmacen')->orderby('numeroRecibo')->orderBy('created_at', 'desc')->get();

			$listaTReciboVenta = TReciboVenta::with(['treciboventadetalle', 'tpersonal', 'tcategoriaventa', 'toficina'])
					->whereRaw(
						'estadoEnvioSunat <> \'Rechazado\' and created_at between ? and ? ' . ($codigoOficina != '' ? 'and codigoOficina=?' : 'and codigoOficina in (select codigoOficina from toficina where codigoEmpresa=?)')
						,
							[
								$fechaInicial, $fechaFinal,
								($codigoOficina != '' ? $codigoOficina : $sessionManager->get('codigoEmpresa'))
							]
						)
					->orderBy('codigoOficina')->orderby('numeroRecibo')->orderBy('created_at', 'desc')->get();
			
			$listaTReciboVentaNotaCredito = TReciboVentaNotaCredito::with(['treciboventa', 'tpersonal', 'toficina'])
					->whereRaw(
						'estadoEnvioSunat <> \'Rechazado\' and created_at between ? and ? ' . ($codigoOficina != '' ? 'and codigoOficina=?' : 'and codigoOficina in (select codigoOficina from toficina where codigoEmpresa=?)'),
							[
								$fechaInicial, $fechaFinal,
								($codigoOficina != '' ? $codigoOficina : $sessionManager->get('codigoEmpresa'))
							]
						)
					->orderBy('codigoOficina')->orderby('numeroRecibo')->orderBy('created_at', 'desc')->get();

			$listaTReciboVentaNotaDebito = TReciboVentaNotaDebito::with(['treciboventa', 'tpersonal', 'toficina'])
					->whereRaw(
						'estadoEnvioSunat <> \'Rechazado\' and created_at between ? and ? ' . ($codigoOficina != '' ? 'and codigoOficina=?' : 'and codigoOficina in (select codigoOficina from toficina where codigoEmpresa=?)'),
							[
								$fechaInicial, $fechaFinal,
								($codigoOficina != '' ? $codigoOficina : $sessionManager->get('codigoEmpresa'))
							]
						)
					->orderBy('codigoOficina')->orderby('numeroRecibo')->orderBy('created_at', 'desc')->get();

			$listaTReciboVentaOutEf = TReciboVentaOutEf::with(['treciboventadetalleoutef', 'tpersonal', 'tcategoriaventa', 'toficina'])
					->whereRaw(
						'created_at between ? and ? ' . ($codigoOficina != '' ? 'and codigoOficina=?' : 'and codigoOficina in (select codigoOficina from toficina where codigoEmpresa=?)')
						. ' and codigoReciboVentaOutEf not in (select codigoReciboVentaOutEf from treciboventa where codigoOficina in (select codigoOficina from toficina where codigoEmpresa = ?) AND codigoReciboVentaOutEf is NOT NULL)',
							[
								$fechaInicial, $fechaFinal,
								($codigoOficina != '' ? $codigoOficina : $sessionManager->get('codigoEmpresa')),
								($codigoOficina != '' ? $codigoOficina : $sessionManager->get('codigoEmpresa'))
							]
						)
					->orderBy('codigoOficina')->orderby('numeroRecibo')->orderBy('created_at', 'desc')->get();

			$listaTProductoTrasladoOficina = TProductoTrasladoOficina::with(['toficina', 'toficinallegada', 'tproductotrasladooficinadetalle'])
					->whereRaw(
						'estado=? and created_at between ? and ? ' . ($codigoOficina != '' ? 'and (codigoOficina=? or codigoOficinaLlegada=?)' : 'and codigoOficina in (select codigoOficina from toficina where codigoEmpresa=?)'),
							[
								true,
								$fechaInicial, $fechaFinal,
								($codigoOficina != '' ? $codigoOficina : $sessionManager->get('codigoEmpresa')),
								($codigoOficina != '' ? $codigoOficina : $sessionManager->get('codigoEmpresa'))
							]
					)->orderBy('codigoOficina')->orderBy('created_at', 'desc')->get();
			
			$listaTProductoEnviarStock = TProductoEnviarStock::with(['toficina', 'talmacen', 'tproductoenviarstockdetalle'])
					->whereRaw(
						'estado=? and created_at between ? and ? ' . ($codigoAlmacen != '' ? 'and codigoAlmacen=?' : 'and codigoAlmacen in (select codigoAlmacen from talmacen where codigoEmpresa=?)')
						. ($codigoOficina != '' ? ' and codigoOficina=?' : ' and codigoOficina in (select codigoOficina from toficina where codigoEmpresa=?)'),
							[
								true,
								$fechaInicial, $fechaFinal,
								($codigoAlmacen != '' ? $codigoAlmacen : $sessionManager->get('codigoEmpresa')),
								($codigoOficina != '' ? $codigoOficina : $sessionManager->get('codigoEmpresa')),
							]
					)->orderBy('codigoAlmacen')->orderBy('codigoOficina')->orderBy('created_at', 'desc')->get();

			$listaTOficinaProductoRetiro = TOficinaProductoRetiro::with(['toficina', 'toficinaproducto'])
					->whereRaw(
						'created_at between ? and ? ' . ($codigoOficina != '' ? 'and codigoOficina=?' : 'and codigoOficina in (select codigoOficina from toficina where codigoEmpresa=?)'),
							[
								$fechaInicial, $fechaFinal,
								($codigoOficina != '' ? $codigoOficina : $sessionManager->get('codigoEmpresa'))
							]
					)->orderBy('codigoOficina')->orderBy('created_at', 'desc')->get();;

			$litaTAlmacenProductoRetiro = TAlmacenProductoRetiro::with(['talmacen', 'talmacenproducto'])
					->whereRaw(
						'created_at between ? and ? ' . ($codigoAlmacen != '' ? 'and codigoAlmacen=?' : 'and codigoAlmacen in (select codigoAlmacen from talmacen where codigoEmpresa=?)'),
							[
								$fechaInicial, $fechaFinal,
								($codigoAlmacen != '' ? $codigoAlmacen : $sessionManager->get('codigoEmpresa'))
							]
					)->orderBy('codigoAlmacen')->orderBy('created_at', 'desc')->get();;

			$listaTReciboVentaPago = TReciboVentaPago::with('treciboventa')
				->whereHas('treciboventa', function ($query) use ($codigoOficina, $sessionManager) {
					$query->whereRaw(
						($codigoOficina != '' ? '(codigoOficina=?)' : '(codigoOficina in (select codigoOficina from toficina where codigoEmpresa=?))'),
						[
							($codigoOficina != '' ? $codigoOficina : $sessionManager->get('codigoEmpresa'))
						]);
				})
				->whereRaw(
					'created_at between ? and ?',
						[
							$fechaInicial, $fechaFinal
						]
				)
				->orderBy('created_at')					
				->get();
		
			$listaTReciboCompraPago = TReciboCompraPago::with('trecibocompra')
				->whereHas('trecibocompra', function ($query) use ($codigoAlmacen, $sessionManager) {
					$query->whereRaw(
						($codigoAlmacen != '' ? '(codigoAlmacen=?)' : '(codigoAlmacen in (select codigoAlmacen from talmacen where codigoEmpresa=?))'),
						[
							($codigoAlmacen != '' ? $codigoAlmacen : $sessionManager->get('codigoEmpresa'))
						]);
				})
				->whereRaw(
					'created_at between ? and ? ',
						[
							$fechaInicial, $fechaFinal
						]
				)->orderBy('created_at')->get();

			$listaTReciboVentaPagoOutEf = TReciboVentaPagoOutEf::with('treciboventaoutef')
				->whereHas('treciboventaoutef', function ($query) use ($codigoOficina, $sessionManager) {
					$query->whereRaw(
						($codigoOficina != '' ? '(codigoOficina=?)' : '(codigoOficina in (select codigoOficina from toficina where codigoEmpresa=?))'),
						[
							($codigoOficina != '' ? $codigoOficina : $sessionManager->get('codigoEmpresa'))
						]);
				})
				->whereRaw(
					'created_at between ? and ? ',
						[
							$fechaInicial, $fechaFinal
						]
				)->orderBy('created_at')->get();

			$tEmpresa=TEmpresa::whereRaw('codigoEmpresa=?', [$sessionManager->get('codigoEmpresa')])->first();
			$pathLogo=public_path().'/img/empresa/'.$tEmpresa->codigoEmpresa.'/logoEmpresarial.png';
			$typeLogo=pathinfo($pathLogo, PATHINFO_EXTENSION);
			$dataLogo=file_get_contents($pathLogo);
			$base64Logo='data:image/' . $typeLogo . ';base64,' . base64_encode($dataLogo);

			switch (strtolower(str_replace(' ', '', $request->input('reporte')))) {
				case "generarreporte":
					$nombreArchivoTemp = 'General consolidado ' . date('Y-m-d H:i:s');
					$pdf = $application->make('dompdf.wrapper');

					$pdf->loadHTML(view('reporte/generalconsolidadopdf', ['genericHelper' => $this->plataformHelper, 'tOficina' => $tOficina, 'tAlmacen' => $tAlmacen, 'listaTCaja' => $listaTCaja, 'listaTEgreso' => $listaTEgreso, 'listaTReciboCompra' => $listaTReciboCompra, 'listaTReciboVenta' => $listaTReciboVenta, 'listaTReciboVentaNotaCredito' => $listaTReciboVentaNotaCredito, 'listaTReciboVentaNotaDebito' => $listaTReciboVentaNotaDebito,'listaTReciboVentaOutEf' => $listaTReciboVentaOutEf, 'base64Logo' => $base64Logo, 'tEmpresa' => $tEmpresa, 'fechaInicial' => $fechaInicial, 'fechaFinal' => $fechaFinal, 'listaTProductoTrasladoOficina' => $listaTProductoTrasladoOficina, 'listaTProductoEnviarStock' => $listaTProductoEnviarStock, 'listaTOficinaProductoRetiro' => $listaTOficinaProductoRetiro, 'litaTAlmacenProductoRetiro' => $litaTAlmacenProductoRetiro, 'listaTReciboVentaPago' => $listaTReciboVentaPago, 'listaTReciboCompraPago' => $listaTReciboCompraPago, 'listaTReciboVentaPagoOutEf' => $listaTReciboVentaPagoOutEf]));
		
					return $pdf->stream($nombreArchivoTemp.'.pdf', ['attachment' => false]);
					
					break;
			}
		}

		if (!(strpos($sessionManager->get('rol'), 'Súper usuario') !== false || (((strpos($sessionManager->get('rol'), 'Administrador') !== false || strpos($sessionManager->get('rol'), 'Ventas') !== false)) && strpos($sessionManager->get('rol'), 'Reporteador') !== false))) {
			echo '<div class="alert alert-danger"><h4><i class="icon fa fa-ban"></i> Prohibido!</h4>No tiene autorización para realizar esta operación o su "sesión de usuario" ya ha finalizado.</div>';
			exit;
		}

		$listTOficina = TOficina::whereRaw('codigoEmpresa = ?', [$sessionManager->get('codigoEmpresa')])->get();
		$listTAlmacen = TAlmacen::whereRaw('codigoEmpresa = ?', [$sessionManager->get('codigoEmpresa')])->get();

		return View("reporte/generalconsolidado", ['listTOficina' => $listTOficina, 'listTAlmacen' => $listTAlmacen]);
	}

	public function actionVentas(Request $request, SessionManager $sessionManager, Application $application)
	{
		if ($request->has('reporte')) {
			if (!$sessionManager->get('facturacionElectronica')) {
				return $this->plataformHelper->redirectError('No tiene autorización para realizar operaciones electrónicas con la SUNAT.', '/');
			}

			if (!(strpos($sessionManager->get('rol'), 'Súper usuario') !== false || (((strpos($sessionManager->get('rol'), 'Administrador') !== false || strpos($sessionManager->get('rol'), 'Ventas') !== false)) && strpos($sessionManager->get('rol'), 'Reporteador') !== false))) {
				return $this->plataformHelper->redirectError('No tiene permisos para generar este reporte.', 'reporte/index');
			}

			$tipoRecibo = $request->input('tipoComprobante');
			$fechaInicial = $request->input('fechaInicial');
			$fechaFinal = $request->input('fechaFinal');
			$codigoOficina = $request->input('codOficina') == null ? '' : $request->input('codOficina');
			$codigoPersonal = $request->input('codPersonal') == null ? '' : $request->input('codPersonal');
			$tipoComprobante = $request->input('tipoComprobante') == "Indistinto" ? "" : $request->input('tipoComprobante');
			$listaTReciboVenta = null;
			$tOficina = TOficina::find($codigoOficina);
			$filtro = $request->input('filtroVenta');
			$listaEstadisticaVenta = null;
			$listaEstadisticaVentaProductos = null;
			$estadoSunat = $request->input('estadoSunat') == 'no-rechazados' ? " and estadoEnvioSunat <> 'Rechazado'" : " and estadoEnvioSunat = 'Rechazado'";

			if ($request->input('selectCategoriaVentaNivelTres') != null && $request->input('selectCategoriaVentaNivelTres') != "") {
				$listaTReciboVenta = TReciboVenta::with(['treciboventadetalle', 'tpersonal'])
					->whereRaw(
						'tipoRecibo like ? and fechaComprobanteEmitido >= ? and fechaComprobanteEmitido <= ? ' . ($codigoPersonal != '' ? 'and codigoPersonal=?' : ($codigoOficina != '' ? 'and codigoOficina=?' : 'and codigoOficina in (select codigoOficina from toficina where codigoEmpresa=?)'))
							. ' and codigoCategoriaVenta=? ' . ($filtro == 'all' ? '' : ($filtro == '!=0' ? 'and total!=0' : ' and total=0'))
							. ' and (? or estado = ?)' . $estadoSunat,
						[
							'%' . $tipoComprobante . '%', $request->input('fechaInicial'), $request->input('fechaFinal'),
							($codigoPersonal != '' ? $codigoPersonal : ($codigoOficina != '' ? $codigoOficina : $sessionManager->get('codigoEmpresa'))),
							$request->input('selectCategoriaVentaNivelTres'), $request->input('tipoVenta') == 'todos', ($request->input('tipoVenta') == 'conforme' ? true : false)
						]
					)
					->orderBy('fechaComprobanteEmitido', 'desc')
					->get();

				$listaEstadisticaVenta = TReciboVenta::with(['tcategoriaventa.tcategoriaventa.tcategoriaventa', 'treciboventadetalle'])
					->whereRaw(
						'tipoRecibo like ? and fechaComprobanteEmitido >= ? and fechaComprobanteEmitido <= ? ' . ($codigoPersonal != '' ? 'and codigoPersonal=?' : ($codigoOficina != '' ? 'and codigoOficina=?' : 'and codigoOficina in (select codigoOficina from toficina where codigoEmpresa=?)'))
							. ' and codigoCategoriaVenta=? ' . ($filtro == 'all' ? '' : ($filtro == '!=0' ? 'and total!=0' : ' and total=0'))
							. ' and (? or estado = ?)',
						[
							'%' . $tipoComprobante . '%', $request->input('fechaInicial'), $request->input('fechaFinal'),
							($codigoPersonal != '' ? $codigoPersonal : ($codigoOficina != '' ? $codigoOficina : $sessionManager->get('codigoEmpresa'))),
							$request->input('selectCategoriaVentaNivelTres'), $request->input('tipoVenta') == 'todos', ($request->input('tipoVenta') == 'conforme' ? true : false)
						]
					)
					->groupBy(['codigoCategoriaVenta'])
					->selectRaw('codigoCategoriaVenta, count(*) as cantidadVentas')
					->orderBy('codigoCategoriaVenta', 'asc')
					->get();

				$listaEstadisticaVentaProductos = DB::select('select t.codigoCategoriaVenta, sum(t.productos) as cantidadProductos from (select codigoCategoriaVenta, (select count(trvd.codigoReciboVentaDetalle) from treciboventadetalle trvd where trvd.codigoReciboVenta = tr.codigoReciboVenta ) as productos from treciboventa tr where tipoRecibo like ? and fechaComprobanteEmitido >= ? and fechaComprobanteEmitido <= ? ' . ($codigoPersonal != '' ? 'and codigoPersonal=?' : ($codigoOficina != '' ? 'and codigoOficina=?' : 'and codigoOficina in (select codigoOficina from toficina where codigoEmpresa=?)'))
					. ' and codigoCategoriaVenta=? ' . ($filtro == 'all' ? '' : ($filtro == '!=0' ? 'and total!=0' : ' and total=0'))
					. ' and (? or estado = ?) ) t group by t.codigoCategoriaVenta order by t.codigoCategoriaVenta asc', [
					'%' . $tipoComprobante . '%', $request->input('fechaInicial'), $request->input('fechaFinal'),
					($codigoPersonal != '' ? $codigoPersonal : ($codigoOficina != '' ? $codigoOficina : $sessionManager->get('codigoEmpresa'))),
					$request->input('selectCategoriaVentaNivelTres'), $request->input('tipoVenta') == 'todos', ($request->input('tipoVenta') == 'conforme' ? true : false)
				]);
			} elseif ($request->input('selectCategoriaVentaNivelDos') != null && $request->input('selectCategoriaVentaNivelDos') != "") {
				$listaTReciboVenta = TReciboVenta::with(['treciboventadetalle', 'tpersonal'])
					->whereRaw(
						'tipoRecibo like ? and fechaComprobanteEmitido >= ? and fechaComprobanteEmitido <= ? ' . ($codigoPersonal != '' ? 'and codigoPersonal=?' : ($codigoOficina != '' ? 'and codigoOficina=?' : 'and codigoOficina in (select codigoOficina from toficina where codigoEmpresa=?)'))
							. ' and (codigoCategoriaVenta in (select codigoCategoriaVenta from tcategoriaventa tcv1 where codigoCategoriaVentaPadre = ? and codigoEmpresa = ? or codigoCategoriaVentaPadre in (select codigoCategoriaVenta from tcategoriaventa tcv2 where tcv2.codigoCategoriaVentaPadre = ? and tcv2.codigoEmpresa = ?)) or codigoCategoriaVenta=?) ' . ($filtro == 'all' ? '' : ($filtro == '!=0' ? 'and total!=0' : ' and total=0'))
							. ' and (? or estado = ?)' . $estadoSunat,
						[
							'%' . $tipoComprobante . '%', $request->input('fechaInicial'), $request->input('fechaFinal'),
							($codigoPersonal != '' ? $codigoPersonal : ($codigoOficina != '' ? $codigoOficina : $sessionManager->get('codigoEmpresa'))), $request->input('selectCategoriaVentaNivelDos'), $sessionManager->get('codigoEmpresa'), $request->input('selectCategoriaVentaNivelDos'), $sessionManager->get('codigoEmpresa'), $request->input('selectCategoriaVentaNivelDos'), $request->input('tipoVenta') == 'todos', ($request->input('tipoVenta') == 'conforme' ? true : false)
						]
					)
					->orderBy('fechaComprobanteEmitido', 'desc')
					->get();

				$listaEstadisticaVenta = TReciboVenta::with(['tcategoriaventa.tcategoriaventa.tcategoriaventa', 'treciboventadetalle'])
					->whereRaw(
						'tipoRecibo like ? and fechaComprobanteEmitido >= ? and fechaComprobanteEmitido <= ? ' . ($codigoPersonal != '' ? 'and codigoPersonal=?' : ($codigoOficina != '' ? 'and codigoOficina=?' : 'and codigoOficina in (select codigoOficina from toficina where codigoEmpresa=?)'))
							. ' and (codigoCategoriaVenta in (select codigoCategoriaVenta from tcategoriaventa tcv1 where codigoCategoriaVentaPadre = ? and codigoEmpresa = ? or codigoCategoriaVentaPadre in (select codigoCategoriaVenta from tcategoriaventa tcv2 where tcv2.codigoCategoriaVentaPadre = ? and tcv2.codigoEmpresa = ?)) or codigoCategoriaVenta=?) ' . ($filtro == 'all' ? '' : ($filtro == '!=0' ? 'and total!=0' : ' and total=0'))
							. ' and (? or estado = ?)',
						[
							'%' . $tipoComprobante . '%', $request->input('fechaInicial'), $request->input('fechaFinal'),
							($codigoPersonal != '' ? $codigoPersonal : ($codigoOficina != '' ? $codigoOficina : $sessionManager->get('codigoEmpresa'))),
							$request->input('selectCategoriaVentaNivelDos'), $sessionManager->get('codigoEmpresa'), $request->input('selectCategoriaVentaNivelDos'), $sessionManager->get('codigoEmpresa'), $request->input('selectCategoriaVentaNivelDos'), $request->input('tipoVenta') == 'todos', ($request->input('tipoVenta') == 'conforme' ? true : false)
						]
					)
					->groupBy(['codigoCategoriaVenta'])
					->selectRaw('codigoCategoriaVenta, count(*) as cantidadVentas')
					->orderBy('codigoCategoriaVenta', 'asc')
					->get();

				$listaEstadisticaVentaProductos = DB::select('select t.codigoCategoriaVenta, sum(t.productos) as cantidadProductos from (select codigoCategoriaVenta, (select count(trvd.codigoReciboVentaDetalle) from treciboventadetalle trvd where trvd.codigoReciboVenta = tr.codigoReciboVenta ) as productos from treciboventa tr where tipoRecibo like ? and fechaComprobanteEmitido >= ? and fechaComprobanteEmitido <= ? ' . ($codigoPersonal != '' ? 'and codigoPersonal=?' : ($codigoOficina != '' ? 'and codigoOficina=?' : 'and codigoOficina in (select codigoOficina from toficina where codigoEmpresa=?)'))
					. ' and (codigoCategoriaVenta in (select codigoCategoriaVenta from tcategoriaventa tcv1 where codigoCategoriaVentaPadre = ? and codigoEmpresa = ? or codigoCategoriaVentaPadre in (select codigoCategoriaVenta from tcategoriaventa tcv2 where tcv2.codigoCategoriaVentaPadre = ? and tcv2.codigoEmpresa = ?)) or codigoCategoriaVenta=?) ' . ($filtro == 'all' ? '' : ($filtro == '!=0' ? 'and total!=0' : ' and total=0'))
					. ' and (? or estado = ?) ) t group by t.codigoCategoriaVenta order by t.codigoCategoriaVenta asc', [
					'%' . $tipoComprobante . '%', $request->input('fechaInicial'), $request->input('fechaFinal'),
					($codigoPersonal != '' ? $codigoPersonal : ($codigoOficina != '' ? $codigoOficina : $sessionManager->get('codigoEmpresa'))), $request->input('selectCategoriaVentaNivelDos'), $sessionManager->get('codigoEmpresa'), $request->input('selectCategoriaVentaNivelDos'), $sessionManager->get('codigoEmpresa'), $request->input('selectCategoriaVentaNivelDos'), $request->input('tipoVenta') == 'todos', ($request->input('tipoVenta') == 'conforme' ? true : false)
				]);
			} elseif ($request->input('selectCategoriaVentaNivelUno') != null && $request->input('selectCategoriaVentaNivelUno') != "") {
				$listaTReciboVenta = TReciboVenta::with(['treciboventadetalle', 'tpersonal'])
					->whereRaw(
						'tipoRecibo like ? and fechaComprobanteEmitido >= ? and fechaComprobanteEmitido <= ? ' . ($codigoPersonal != '' ? 'and codigoPersonal=?' : ($codigoOficina != '' ? 'and codigoOficina=?' : 'and codigoOficina in (select codigoOficina from toficina where codigoEmpresa=?)'))
							. ' and (codigoCategoriaVenta in (select codigoCategoriaVenta from tcategoriaventa tcv1 where codigoCategoriaVentaPadre = ? and codigoEmpresa = ? or codigoCategoriaVentaPadre in (select codigoCategoriaVenta from tcategoriaventa tcv2 where tcv2.codigoCategoriaVentaPadre = ? and tcv2.codigoEmpresa = ?)) or codigoCategoriaVenta=?) ' . ($filtro == 'all' ? '' : ($filtro == '!=0' ? 'and total!=0' : ' and total=0'))
							. ' and (? or estado = ?)' . $estadoSunat,
						[
							'%' . $tipoComprobante . '%', $request->input('fechaInicial'), $request->input('fechaFinal'),
							($codigoPersonal != '' ? $codigoPersonal : ($codigoOficina != '' ? $codigoOficina : $sessionManager->get('codigoEmpresa'))),
							$request->input('selectCategoriaVentaNivelUno'), $sessionManager->get('codigoEmpresa'), $request->input('selectCategoriaVentaNivelUno'), $sessionManager->get('codigoEmpresa'), $request->input('selectCategoriaVentaNivelUno'), $request->input('tipoVenta') == 'todos', ($request->input('tipoVenta') == 'conforme' ? true : false)
						]
					)
					->orderBy('fechaComprobanteEmitido', 'desc')
					->get();

				$listaEstadisticaVenta = TReciboVenta::with(['tcategoriaventa.tcategoriaventa.tcategoriaventa', 'treciboventadetalle'])
					->whereRaw(
						'tipoRecibo like ? and fechaComprobanteEmitido >= ? and fechaComprobanteEmitido <= ? ' . ($codigoPersonal != '' ? 'and codigoPersonal=?' : ($codigoOficina != '' ? 'and codigoOficina=?' : 'and codigoOficina in (select codigoOficina from toficina where codigoEmpresa=?)'))
							. ' and (codigoCategoriaVenta in (select codigoCategoriaVenta from tcategoriaventa tcv1 where codigoCategoriaVentaPadre = ? and codigoEmpresa = ? or codigoCategoriaVentaPadre in (select codigoCategoriaVenta from tcategoriaventa tcv2 where tcv2.codigoCategoriaVentaPadre = ? and tcv2.codigoEmpresa = ?)) or codigoCategoriaVenta=?) ' . ($filtro == 'all' ? '' : ($filtro == '!=0' ? 'and total!=0' : ' and total=0'))
							. ' and (? or estado = ?)',
						[
							'%' . $tipoComprobante . '%', $request->input('fechaInicial'), $request->input('fechaFinal'),
							($codigoPersonal != '' ? $codigoPersonal : ($codigoOficina != '' ? $codigoOficina : $sessionManager->get('codigoEmpresa'))),
							$request->input('selectCategoriaVentaNivelUno'), $sessionManager->get('codigoEmpresa'), $request->input('selectCategoriaVentaNivelUno'), $sessionManager->get('codigoEmpresa'), $request->input('selectCategoriaVentaNivelUno'), $request->input('tipoVenta') == 'todos', ($request->input('tipoVenta') == 'conforme' ? true : false)
						]
					)
					->groupBy(['codigoCategoriaVenta'])
					->selectRaw('codigoCategoriaVenta, count(*) as cantidadVentas')
					->orderBy('codigoCategoriaVenta', 'asc')
					->get();

				$listaEstadisticaVentaProductos = DB::select('select t.codigoCategoriaVenta, sum(t.productos) as cantidadProductos from (select codigoCategoriaVenta, (select count(trvd.codigoReciboVentaDetalle) from treciboventadetalle trvd where trvd.codigoReciboVenta = tr.codigoReciboVenta ) as productos from treciboventa tr where tipoRecibo like ? and fechaComprobanteEmitido >= ? and fechaComprobanteEmitido <= ? ' . ($codigoPersonal != '' ? 'and codigoPersonal=?' : ($codigoOficina != '' ? 'and codigoOficina=?' : 'and codigoOficina in (select codigoOficina from toficina where codigoEmpresa=?)'))
					. ' and (codigoCategoriaVenta in (select codigoCategoriaVenta from tcategoriaventa tcv1 where codigoCategoriaVentaPadre = ? and codigoEmpresa = ? or codigoCategoriaVentaPadre in (select codigoCategoriaVenta from tcategoriaventa tcv2 where tcv2.codigoCategoriaVentaPadre = ? and tcv2.codigoEmpresa = ?)) or codigoCategoriaVenta=?) ' . ($filtro == 'all' ? '' : ($filtro == '!=0' ? 'and total!=0' : ' and total=0'))
					. ' and (? or estado = ?) ) t group by t.codigoCategoriaVenta order by t.codigoCategoriaVenta asc', [
					'%' . $tipoComprobante . '%', $request->input('fechaInicial'), $request->input('fechaFinal'),
					($codigoPersonal != '' ? $codigoPersonal : ($codigoOficina != '' ? $codigoOficina : $sessionManager->get('codigoEmpresa'))),
					$request->input('selectCategoriaVentaNivelUno'), $sessionManager->get('codigoEmpresa'), $request->input('selectCategoriaVentaNivelUno'), $sessionManager->get('codigoEmpresa'), $request->input('selectCategoriaVentaNivelUno'), $request->input('tipoVenta') == 'todos', ($request->input('tipoVenta') == 'conforme' ? true : false)
				]);
			} else {
				$listaTReciboVenta = TReciboVenta::with(['treciboventadetalle', 'tpersonal'])
					->whereRaw(
						'tipoRecibo like ? and fechaComprobanteEmitido >= ? and fechaComprobanteEmitido <= ? ' . ($codigoPersonal != '' ? 'and codigoPersonal=?' : ($codigoOficina != '' ? 'and codigoOficina=?' : 'and codigoOficina in (select codigoOficina from toficina where codigoEmpresa=?)')) . ($filtro == 'all' ? '' : ($filtro == '!=0' ? 'and total!=0' : ' and total=0'))
							. ' and (? or estado = ?)' . $estadoSunat,
						[
							'%' . $tipoComprobante . '%', $request->input('fechaInicial'), $request->input('fechaFinal'),
							($codigoPersonal != '' ? $codigoPersonal : ($codigoOficina != '' ? $codigoOficina : $sessionManager->get('codigoEmpresa'))),
							$request->input('tipoVenta') == 'todos', ($request->input('tipoVenta') == 'conforme' ? true : false)
						]
					)
					->orderBy('fechaComprobanteEmitido', 'desc')
					->get();


				$listaEstadisticaVenta = TReciboVenta::with(['tcategoriaventa.tcategoriaventa.tcategoriaventa', 'treciboventadetalle'])
					->whereRaw(
						'tipoRecibo like ? and fechaComprobanteEmitido >= ? and fechaComprobanteEmitido <= ? ' . ($codigoPersonal != '' ? 'and codigoPersonal=?' : ($codigoOficina != '' ? 'and codigoOficina=?' : 'and codigoOficina in (select codigoOficina from toficina where codigoEmpresa=?)')) . ($filtro == 'all' ? '' : ($filtro == '!=0' ? 'and total!=0' : ' and total=0'))
							. ' and (? or estado = ?)',
						[
							'%' . $tipoComprobante . '%', $request->input('fechaInicial'), $request->input('fechaFinal'),
							($codigoPersonal != '' ? $codigoPersonal : ($codigoOficina != '' ? $codigoOficina : $sessionManager->get('codigoEmpresa'))),
							$request->input('tipoVenta') == 'todos', ($request->input('tipoVenta') == 'conforme' ? true : false)
						]
					)
					->groupBy(['codigoCategoriaVenta'])
					->selectRaw('codigoCategoriaVenta, count(*) as cantidadVentas')
					->orderBy('codigoCategoriaVenta', 'asc')
					->get();

				$listaEstadisticaVentaProductos = DB::select('select t.codigoCategoriaVenta, sum(t.productos) as cantidadProductos from (select codigoCategoriaVenta, (select count(trvd.codigoReciboVentaDetalle) from treciboventadetalle trvd where trvd.codigoReciboVenta = tr.codigoReciboVenta ) as productos from treciboventa tr where tipoRecibo like ? and fechaComprobanteEmitido >= ? and fechaComprobanteEmitido <= ? ' . ($codigoPersonal != '' ? 'and codigoPersonal=?' : ($codigoOficina != '' ? 'and codigoOficina=?' : 'and codigoOficina in (select codigoOficina from toficina where codigoEmpresa=?)')) . ($filtro == 'all' ? '' : ($filtro == '!=0' ? 'and total!=0' : ' and total=0'))
					. ' and (? or estado = ?) ) t group by t.codigoCategoriaVenta order by t.codigoCategoriaVenta asc', [
					'%' . $tipoComprobante . '%', $request->input('fechaInicial'), $request->input('fechaFinal'),
					($codigoPersonal != '' ? $codigoPersonal : ($codigoOficina != '' ? $codigoOficina : $sessionManager->get('codigoEmpresa'))),
					$request->input('tipoVenta') == 'todos', ($request->input('tipoVenta') == 'conforme' ? true : false)
				]);
			}

			switch (strtolower(str_replace(' ', '', $request->input('reporte')))) {
				case "exportaraexcel":
					if ($listaTReciboVenta->count() < 1) {
						$request->flash();

						return $this->plataformHelper->redirectError('No se puede exportar, no se encontraron registros con el filtro actual!.', '/reporte/index');
					}

					$data = [];
					$styleRows = (object) [
						"merge" => [],
						"sales" => [],
						"creditNote" => [],
						"debitNote" => [],
						"generalTitle" => [],
						"generalSubtitle" => [],
						"summaryTitle" => [],
						"separator" => [],
					];
					$headers = [];
					$fileName = 'reporteVentas-' . $tipoRecibo . ($fechaInicial != '' ? ('-' . str_replace('/', '', $fechaInicial) . '-' . str_replace('/', '', $fechaFinal)) : '') . '-Oficina[' . ($tOficina !== null ? $tOficina->descripcion : 'Todas') . '].xlsx';

					foreach ($listaTReciboVenta as $key => $venta) {
						$data[] = ['VENTA #' . ($key + 1)];

						$styleRows->merge[] = count($data);
						$styleRows->sales[] = count($data);

						$data[] = [
							'N° recibo',
							'Categoría',
							'Personal',
							'Cliente',
							'Tipo recibo',
							'Tipo pago',
							'Divisa',
							'Fecha emitido',
							'Estado.'
						];

						$styleRows->generalTitle[] = count($data);

						$data[] = [							
							$venta->numeroRecibo,
							$this->plataformHelper->obtenerRamaCategoriaVenta($venta->tcategoriaventa, ' > '),
							$venta->tPersonal->dni . ' - ' . $venta->tPersonal->nombre . ' ' . $venta->tPersonal->apellido,
							$venta->documentoCliente . ' - ' . $venta->nombreCompletoCliente,
							$venta->tipoRecibo,
							$venta->tipoPago . ($venta->tipoPago == "Al crédito" ? (' (' . $venta->letras . ' letras)') : ''),
							$venta->divisa == 'Soles' ? 'PEN' : 'USD',
							$venta->fechaComprobanteEmitido,
							$venta->estado ? 'Conforme' : 'Anulado'
						];

						$data[] = [
							'Producto',
							'Inf. Adicional',
							'Tipo',
							'Impuesto',
							'Cantidad',
							'Impuesto aplicado',
							'',
							'Precio venta',
							'Venta total'
						];

						$styleRows->generalSubtitle[] = count($data);

						foreach ($venta->treciboventadetalle as $key => $detalleVenta) {							

							$data[] = [
								'(' . $detalleVenta->codigoBarrasProducto . ') ' . $detalleVenta->nombreProducto,
								$detalleVenta->informacionAdicionalProducto,
								$detalleVenta->tipoProducto,
								$detalleVenta->tipoImpuestoProducto . ' ' . $detalleVenta->porcentajeTributacionProducto . '%',
								$detalleVenta->cantidadProducto,
								$detalleVenta->impuestoAplicadoProducto,
								$venta->divisa == 'Soles' ? 'PEN' : 'USD',
								($venta->divisa == 'Soles' ? 'S/' : 'US$') . $detalleVenta->precioVentaUnitarioProducto,
								($venta->divisa == 'Soles' ? 'S/' : 'US$') . $detalleVenta->precioVentaTotalProducto
							];
						}

						$countCell = 9;
						$data[] = array_pad(['ISC', ($venta->divisa == 'Soles' ? 'S/' : 'US$') . $venta->isc], -$countCell, ' ');
						$data[] = array_pad(['IGV', ($venta->divisa == 'Soles' ? 'S/' : 'US$') . $venta->igv], -$countCell, ' ');
						$data[] = array_pad(['SUBTOTAL', ($venta->divisa == 'Soles' ? 'S/' : 'US$') . $venta->subTotal], -$countCell, ' ');
						$data[] = array_pad(['TOTAL', ($venta->divisa == 'Soles' ? 'S/' : 'US$') . $venta->total], -$countCell, ' ');

						$listaTReciboVentaNotaCredito = TReciboVentaNotaCredito::with('treciboventanotacreditodetalle')->whereRaw('codigoReciboVenta=?', $venta->codigoReciboVenta)->get();

						if ($listaTReciboVentaNotaCredito->count() > 0) {
							$data[] = [' '];
							$data[] = ['NOTAS DE CRÉDITO'];
							$styleRows->merge[] = count($data);
							$styleRows->creditNote[] = count($data);

							foreach ($listaTReciboVentaNotaCredito as $key => $value_credito) {
								$data[] = [
									'#',
									'Personal',
									'N° recibo',
									'Código',
									'Divisa',
									'Motivo',
									'Fecha emitido',
								];	

								$styleRows->generalSubtitle[] = count($data);

								$data[] = [
									'NOTA CREDITO #' . ($key + 1),
									'(' . $value_credito->tpersonal->dni . ') ' . $value_credito->tpersonal->nombre . ' ' . $value_credito->tpersonal->apellido,
									$value_credito->numeroRecibo,
									$value_credito->codigoMotivo,
									$venta->divisa == 'Soles' ? 'PEN' : 'USD',
									$value_credito->descripcionMotivo,
									$value_credito->fechaComprobanteEmitido,
								];
								$data[] = [
									'Producto',
									'Tipo',
									'Impuesto',
									'Cantidad',
									'Impuesto aplicado',
									'',
									'Precio venta',
									'Venta total'
								];

								$styleRows->generalSubtitle[] = count($data);

								foreach ($value_credito->treciboventanotacreditodetalle as $key => $detalleNotaCredito) {
									$data[] = [
										'(' . $detalleNotaCredito->codigoBarrasProducto . ') ' . $detalleNotaCredito->nombreProducto,
										$detalleNotaCredito->tipoProducto,
										$detalleNotaCredito->tipoImpuestoProducto . ' ' . $detalleNotaCredito->porcentajeTributacionProducto . '%',
										$detalleNotaCredito->cantidadProducto,
										$detalleNotaCredito->impuestoAplicadoProducto,
										$venta->divisa == 'Soles' ? 'PEN' : 'USD',	
										($venta->divisa == 'Soles' ? 'S/' : 'US$') . $detalleNotaCredito->precioVentaUnitarioProducto,
										($venta->divisa == 'Soles' ? 'S/' : 'US$') . $detalleNotaCredito->precioVentaTotalProducto
									];
								}

								$data[] = array_pad(['ISC', ($venta->divisa == 'Soles' ? 'S/' : 'US$') . $value_credito->isc], -$countCell + 1, ' ');
								$data[] = array_pad(['IGV', ($venta->divisa == 'Soles' ? 'S/' : 'US$') . $value_credito->igv], -$countCell + 1, ' ');
								$data[] = array_pad(['SUBTOTAL', ($venta->divisa == 'Soles' ? 'S/' : 'US$') . $value_credito->subTotal], -$countCell + 1, ' ');
								$data[] = array_pad(['TOTAL', ($venta->divisa == 'Soles' ? 'S/' : 'US$') . $value_credito->total], -$countCell + 1, ' ');
							}
						}

						$listaTReciboVentaNotaDebito = TReciboVentaNotaDebito::with('treciboventanotadebitodetalle')->whereRaw('codigoReciboVenta=?', $venta->codigoReciboVenta)->get();

						if ($listaTReciboVentaNotaDebito->count() > 0) {
							$data[] = [' '];
							$data[] = ['NOTAS DE DÉBITO'];
							$styleRows->merge[] = count($data);
							$styleRows->debitNote[] = count($data);

							foreach ($listaTReciboVentaNotaDebito as $key => $value_debito) {
								$data[] = [
									'#',
									'Personal',
									'N° recibo',
									'Código',
									'Divisa',
									'Motivo',
									'Fecha emitido',
								];

								$styleRows->generalSubtitle[] = count($data);

								$data[] = [
									'NOTA DÉBITO #' . ($key + 1),
									'(' . $value_debito->tpersonal->dni . ') ' . $value_debito->tpersonal->nombre . ' ' . $value_debito->tpersonal->apellido,
									$value_debito->numeroRecibo,
									$value_debito->codigoMotivo,
									$venta->divisa == 'Soles' ? 'PEN' : 'USD',
									$value_debito->descripcionMotivo,
									$value_debito->fechaComprobanteEmitido,
								];
								$data[] = [
									'Producto',
									'Tipo',
									'Impuesto',
									'Cantidad',
									'Impuesto aplicado',
									'',
									'Precio venta',
									'Venta total'
								];

								$styleRows->generalSubtitle[] = count($data);

								foreach ($value_debito->treciboventanotadebitodetalle as $key => $detalleNotaDebito) {
									$data[] = [
										'(' . $detalleNotaDebito->codigoBarrasProducto . ') ' . $detalleNotaDebito->nombreProducto,
										$detalleNotaDebito->tipoProducto,
										$detalleNotaDebito->tipoImpuestoProducto . ' ' . $detalleNotaDebito->porcentajeTributacionProducto . '%',
										$detalleNotaDebito->cantidadProducto,
										$detalleNotaDebito->impuestoAplicadoProducto,
										$venta->divisa == 'Soles' ? 'PEN' : 'USD',
										($venta->divisa == 'Soles' ? 'S/' : 'US$') . $detalleNotaDebito->precioVentaUnitarioProducto,
										($venta->divisa == 'Soles' ? 'S/' : 'US$') . $detalleNotaDebito->precioVentaTotalProducto,
									];
								}

								$data[] = array_pad(['ISC', ($venta->divisa == 'Soles' ? 'S/' : 'US$') . $value_debito->isc], -$countCell + 1, ' ');
								$data[] = array_pad(['IGV', ($venta->divisa == 'Soles' ? 'S/' : 'US$') . $value_debito->igv], -$countCell + 1, ' ');
								$data[] = array_pad(['SUBTOTAL', ($venta->divisa == 'Soles' ? 'S/' : 'US$') . $value_debito->subTotal], -$countCell + 1, ' ');
								$data[] = array_pad(['TOTAL', ($venta->divisa == 'Soles' ? 'S/' : 'US$') . $value_debito->total], -$countCell + 1, ' ');
							}
						}

						$data[] = [' '];
						$styleRows->separator[] = count($data);
						$data[] = [' '];
					}

					$data[] = ['RESUMEN', ''];
					$styleRows->summaryTitle[] = count($data);
					$data[] = ['ISC', 'S/' . (number_format($listaTReciboVenta->map(function($item, $key) {
						return $item->isc * ($item->divisa == 'Soles' ? 1 : $item->tipoCambioUsd);
					})->sum(), 2, '.', ''))];
					$data[] = ['IGV', 'S/' . (number_format($listaTReciboVenta->map(function($item, $key){
						return $item->igv * ($item->divisa == 'Soles' ? 1 : $item->tipoCambioUsd);
					})->sum(), 2, '.', ''))];
					$data[] = ['SUBTOTAL', 'S/' . (number_format($listaTReciboVenta->map(function($item, $key){
						return $item->subTotal * ($item->divisa == 'Soles' ? 1 : $item->tipoCambioUsd);
					})->sum(), 2, '.', ''))];
					$data[] = ['TOTAL', 'S/' . (number_format($listaTReciboVenta->map(function($item, $key){
						return $item->total * ($item->divisa == 'Soles' ? 1 : $item->tipoCambioUsd);
					})->sum(), 2, '.', ''))];
					$data[] = ['TOTAL ANULADAS', 'S/' . (number_format($listaTReciboVenta->where('estado', 0)->map(function($item, $key){
						return $item->total * ($item->divisa == 'Soles' ? 1 : $item->tipoCambioUsd);
					})->sum(), 2, '.', ''))];
					$data[] = [' '];

					$data[] = ['ESTADÍSTICAS VENTA', ''];
					$styleRows->summaryTitle[] = count($data);

					foreach ($listaEstadisticaVenta as $value) {
						$data[] = [$this->plataformHelper->obtenerRamaCategoriaVenta($value->tcategoriaventa, '>'), $value->cantidadVentas];
					}

					$data[] = [' '];
					$data[] = ['ESTADÍSTICAS VENTA PRODUCTOS', ''];
					$styleRows->summaryTitle[] = count($data);

					foreach ($listaEstadisticaVentaProductos as $value) {
						$data[] = [$this->plataformHelper->obtenerRamaCategoriaVenta(TCategoriaVenta::with('tcategoriaventa.tcategoriaventa')->find($value->codigoCategoriaVenta), '>'), $value->cantidadProductos];
					}

					return new ReportGeneratorExport($headers, $data, $fileName, function(AfterSheet $event) use ($styleRows) {
						$styleMerge = [
							'font' => [
								'bold' => true,
								'color' => [
									'argb' => 'FFFFFF',
								]
							],
							'alignment' => [
								'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
							],
							'borders' => [
								'allBorders' => [
									'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
								],
							]
						];

						$styleSale = [
							'fill' => [
								'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
								'color' => [
									'argb' => '141618',
								]
							],
						];

						$styleCreditNote = [
							'fill' => [
								'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
								'color' => [
									'argb' => 'DF17E4',
								]
							],
						];

						$styleDebitNote = [
							'fill' => [
								'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
								'color' => [
									'argb' => '18AC3F',
								]
							],
						];
						
						$styleTitleGeneral = [
							'font' => [
								'bold' => true,
								'color' => [
									'argb' => 'FFFFFF',
								]
							],
							'fill' => [
								'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
								'color' => [
									'argb' => '282D31',
								]
							],
						];

						$styleSubtitleGeneral = [
							'font' => [
								'bold' => true,
								'color' => [
									'argb' => 'FFFFFF',
								]
							],
							'fill' => [
								'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
								'color' => [
									'argb' => '53575A',
								]
							],
						];

						$styleSummary = [
							'font' => [
								'bold' => true,
								'color' => [
									'argb' => 'FFFFFF',
								]
							],
							'fill' => [
								'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
								'color' => [
									'argb' => '141618',
								]
							],
						];

						$styleSeparator = [
							'fill' => [
								'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
								'color' => [
									'argb' => 'FBA905',
								]
							],
						];

						foreach($styleRows->merge as $merge)
						{
							$event->sheet->getDelegate()->mergeCells('A' . $merge . ':I' . $merge)->getStyle('A' . $merge . ':I' . $merge)->applyFromArray($styleMerge);
						}

						foreach($styleRows->sales as $sales)
						{
							$event->sheet->getDelegate()->getStyle('A' . $sales . ':I' . $sales)->applyFromArray($styleSale);
						}

						foreach($styleRows->creditNote as $creditNote)
						{
							$event->sheet->getDelegate()->getStyle('A' . $creditNote . ':I' . $creditNote)->applyFromArray($styleCreditNote);
						}

						foreach($styleRows->debitNote as $debitNote)
						{
							$event->sheet->getDelegate()->getStyle('A' . $debitNote . ':I' . $debitNote)->applyFromArray($styleDebitNote);
						}
						
						foreach($styleRows->generalTitle as $generalTitle)
						{
							$event->sheet->getDelegate()->getStyle('A' . $generalTitle . ':I' . $generalTitle)->applyFromArray($styleTitleGeneral);
						}

						foreach($styleRows->generalSubtitle as $generalSubtitle)
						{
							$event->sheet->getDelegate()->getStyle('A' . $generalSubtitle . ':I' . $generalSubtitle)->applyFromArray($styleSubtitleGeneral);
						}

						foreach($styleRows->summaryTitle as $summaryTitle)
						{
							$event->sheet->getDelegate()->getStyle('A' . $summaryTitle . ':B' . $summaryTitle)->applyFromArray($styleSummary);
						}

						foreach($styleRows->separator as $separator)
						{
							$event->sheet->getDelegate()->getStyle('A' . $separator . ':I' . $separator)->applyFromArray($styleSeparator);
						}
					});
					
					break;
			}
		}

		if (!$sessionManager->get('facturacionElectronica')) {
			echo '<div class="alert alert-danger"><h4><i class="icon fa fa-ban"></i> Prohibido!</h4>No tiene autorización para realizar operaciones electrónicas con la SUNAT.</div>';
			exit;
		}

		if (!(strpos($sessionManager->get('rol'), 'Súper usuario') !== false || (((strpos($sessionManager->get('rol'), 'Administrador') !== false || strpos($sessionManager->get('rol'), 'Ventas') !== false)) && strpos($sessionManager->get('rol'), 'Reporteador') !== false))) {
			echo '<div class="alert alert-danger"><h4><i class="icon fa fa-ban"></i> Prohibido!</h4>No tiene autorización para realizar esta operación o su "sesión de usuario" ya ha finalizado.</div>';
			exit;
		}

		$listTOficina = TOficina::whereRaw('codigoEmpresa = ?', [$sessionManager->get('codigoEmpresa')])->get();
		$listaTCategoriaVenta = TCategoriaVenta::with(['tcategoriaventachild.tcategoriaventachild'])->whereRaw('codigoEmpresa=? and estado=? and codigoCategoriaVentaPadre is null', [$sessionManager->get('codigoEmpresa'), true])->get();

		return View("reporte/ventas", ['listTOficina' => $listTOficina, 'listaTCategoriaVenta' => $listaTCategoriaVenta]);
	}

	public function actionVentasConsolidado(Request $request, SessionManager $sessionManager, Application $application)
	{
		if ($request->has('reporte')) {
			if (!$sessionManager->get('facturacionElectronica')) {
				return $this->plataformHelper->redirectError('No tiene autorización para realizar operaciones electrónicas con la SUNAT.', '/');
			}

			if (!(strpos($sessionManager->get('rol'), 'Súper usuario') !== false || (((strpos($sessionManager->get('rol'), 'Administrador') !== false || strpos($sessionManager->get('rol'), 'Ventas') !== false)) && strpos($sessionManager->get('rol'), 'Reporteador') !== false))) {
				return $this->plataformHelper->redirectError('No tiene permisos para generar este reporte.', 'reporte/index');
			}

			$tipoRecibo = $request->input('tipoComprobante');
			$fechaInicial = $request->input('fechaInicial');
			$fechaFinal = $request->input('fechaFinal');
			$codigoOficina = $request->input('codOficina') == null ? '' : $request->input('codOficina');
			$codigoPersonal = $request->input('codPersonal') == null ? '' : $request->input('codPersonal');
			$tipoComprobante = $request->input('tipoComprobante') == "Indistinto" ? "" : $request->input('tipoComprobante');
			$listaTReciboVenta = null;
			$tOficina = TOficina::find($codigoOficina);
			$filtro = $request->input('filtroVenta');
			$estadoSunat = $request->input('estadoSunat') == 'no-rechazados' ? " and estadoEnvioSunat <> 'Rechazado'" : " and estadoEnvioSunat = 'Rechazado'";

			if ($request->input('selectCategoriaVentaNivelTres') != null && $request->input('selectCategoriaVentaNivelTres') != "") {
				$listaTReciboVenta = TReciboVenta::with(['treciboventadetalle', 'tpersonal'])
					->whereRaw(
						'tipoRecibo like ? and fechaComprobanteEmitido >= ? and fechaComprobanteEmitido <= ? ' . ($codigoPersonal != '' ? 'and codigoPersonal=?' : ($codigoOficina != '' ? 'and codigoOficina=?' : 'and codigoOficina in (select codigoOficina from toficina where codigoEmpresa=?)'))
							. ' and codigoCategoriaVenta=? ' . ($filtro == 'all' ? '' : ($filtro == '!=0' ? 'and total!=0' : ' and total=0'))
							. ' and (? or estado = ?)' . $estadoSunat,
						[
							'%' . $tipoComprobante . '%', $request->input('fechaInicial'), $request->input('fechaFinal'),
							($codigoPersonal != '' ? $codigoPersonal : ($codigoOficina != '' ? $codigoOficina : $sessionManager->get('codigoEmpresa'))),
							$request->input('selectCategoriaVentaNivelTres'), $request->input('tipoVenta') == 'todos', ($request->input('tipoVenta') == 'conforme' ? true : false)
						]
					)
					->orderBy('fechaComprobanteEmitido', 'desc')
					->get();
			} elseif ($request->input('selectCategoriaVentaNivelDos') != null && $request->input('selectCategoriaVentaNivelDos') != "") {
				$listaTReciboVenta = TReciboVenta::with(['treciboventadetalle', 'tpersonal'])
					->whereRaw(
						'tipoRecibo like ? and fechaComprobanteEmitido >= ? and fechaComprobanteEmitido <= ? ' . ($codigoPersonal != '' ? 'and codigoPersonal=?' : ($codigoOficina != '' ? 'and codigoOficina=?' : 'and codigoOficina in (select codigoOficina from toficina where codigoEmpresa=?)'))
							. ' and (codigoCategoriaVenta in (select codigoCategoriaVenta from tcategoriaventa tcv1 where codigoCategoriaVentaPadre = ? and codigoEmpresa = ? or codigoCategoriaVentaPadre in (select codigoCategoriaVenta from tcategoriaventa tcv2 where tcv2.codigoCategoriaVentaPadre = ? and tcv2.codigoEmpresa = ?)) or codigoCategoriaVenta=?) ' . ($filtro == 'all' ? '' : ($filtro == '!=0' ? 'and total!=0' : ' and total=0'))
							. ' and (? or estado = ?)' . $estadoSunat,
						[
							'%' . $tipoComprobante . '%', $request->input('fechaInicial'), $request->input('fechaFinal'),
							($codigoPersonal != '' ? $codigoPersonal : ($codigoOficina != '' ? $codigoOficina : $sessionManager->get('codigoEmpresa'))), $request->input('selectCategoriaVentaNivelDos'), $sessionManager->get('codigoEmpresa'), $request->input('selectCategoriaVentaNivelDos'), $sessionManager->get('codigoEmpresa'), $request->input('selectCategoriaVentaNivelDos'), $request->input('tipoVenta') == 'todos', ($request->input('tipoVenta') == 'conforme' ? true : false)
						]
					)
					->orderBy('fechaComprobanteEmitido', 'desc')
					->get();
			} elseif ($request->input('selectCategoriaVentaNivelUno') != null && $request->input('selectCategoriaVentaNivelUno') != "") {
				$listaTReciboVenta = TReciboVenta::with(['treciboventadetalle', 'tpersonal'])
					->whereRaw(
						'tipoRecibo like ? and fechaComprobanteEmitido >= ? and fechaComprobanteEmitido <= ? ' . ($codigoPersonal != '' ? 'and codigoPersonal=?' : ($codigoOficina != '' ? 'and codigoOficina=?' : 'and codigoOficina in (select codigoOficina from toficina where codigoEmpresa=?)'))
							. ' and (codigoCategoriaVenta in (select codigoCategoriaVenta from tcategoriaventa tcv1 where codigoCategoriaVentaPadre = ? and codigoEmpresa = ? or codigoCategoriaVentaPadre in (select codigoCategoriaVenta from tcategoriaventa tcv2 where tcv2.codigoCategoriaVentaPadre = ? and tcv2.codigoEmpresa = ?)) or codigoCategoriaVenta=?) ' . ($filtro == 'all' ? '' : ($filtro == '!=0' ? 'and total!=0' : ' and total=0'))
							. ' and (? or estado = ?)' . $estadoSunat,
						[
							'%' . $tipoComprobante . '%', $request->input('fechaInicial'), $request->input('fechaFinal'),
							($codigoPersonal != '' ? $codigoPersonal : ($codigoOficina != '' ? $codigoOficina : $sessionManager->get('codigoEmpresa'))),
							$request->input('selectCategoriaVentaNivelUno'), $sessionManager->get('codigoEmpresa'), $request->input('selectCategoriaVentaNivelUno'), $sessionManager->get('codigoEmpresa'), $request->input('selectCategoriaVentaNivelUno'), $request->input('tipoVenta') == 'todos', ($request->input('tipoVenta') == 'conforme' ? true : false)
						]
					)
					->orderBy('fechaComprobanteEmitido', 'desc')
					->get();
			} else {
				$listaTReciboVenta = TReciboVenta::with(['treciboventadetalle', 'tpersonal'])
					->whereRaw(
						'tipoRecibo like ? and fechaComprobanteEmitido >= ? and fechaComprobanteEmitido <= ? ' . ($codigoPersonal != '' ? 'and codigoPersonal=?' : ($codigoOficina != '' ? 'and codigoOficina=?' : 'and codigoOficina in (select codigoOficina from toficina where codigoEmpresa=?)')) . ($filtro == 'all' ? '' : ($filtro == '!=0' ? 'and total!=0' : ' and total=0'))
							. ' and (? or estado = ?)' . $estadoSunat,
						[
							'%' . $tipoComprobante . '%', $request->input('fechaInicial'), $request->input('fechaFinal'),
							($codigoPersonal != '' ? $codigoPersonal : ($codigoOficina != '' ? $codigoOficina : $sessionManager->get('codigoEmpresa'))),
							$request->input('tipoVenta') == 'todos', ($request->input('tipoVenta') == 'conforme' ? true : false)
						]
					)
					->orderBy('fechaComprobanteEmitido', 'desc')
					->get();
			}

			switch (strtolower(str_replace(' ', '', $request->input('reporte')))) {
				case "exportaraexcel":
					if ($listaTReciboVenta->count() < 1) {
						$request->flash();

						return $this->plataformHelper->redirectError('No se puede exportar, no se encontraron registros con el filtro actual!.', '/reporte/index');
					}

					$data = [];
					$styleRows = (object) [
						"generalTitle" => [],
						"summary" => [],
					];
					$headers = [];
					$fileName = 'reporteVentasConsolidado-' . $tipoRecibo . ($fechaInicial != '' ? ('-' . str_replace('/', '', $fechaInicial) . '-' . str_replace('/', '', $fechaFinal)) : '') . '-Oficina[' . ($tOficina !== null ? $tOficina->descripcion : 'Todas') . '].xlsx';

					$data[] = [
						'Tipo recibo',
						'N° recibo',
						'Tipo pago',
						'Categoría',
						'Cliente',
						'Fecha emitido',
						'Divisa',
						'ISC',
						'IGV',
						'SubTotal',
						'Total',
						'Estado'
					];

					$styleRows->generalTitle[] = count($data);

					foreach ($listaTReciboVenta as $key => $venta) {
						$data[] = [
							$venta->tipoRecibo,
							$venta->numeroRecibo,
							$venta->tipoPago . ($venta->tipoPago == "Al crédito" ? (' (' . $venta->letras . ' letras)') : ''),
							$this->plataformHelper->obtenerRamaCategoriaVenta($venta->tcategoriaventa, ' > '),
							$venta->documentoCliente . ' - ' . $venta->nombreCompletoCliente,
							$venta->fechaComprobanteEmitido,
							$venta->divisa == 'Soles' ? 'PEN' : 'USD',
							($venta->divisa == 'Soles' ? 'S/' : 'US$') . $venta->isc,
							($venta->divisa == 'Soles' ? 'S/' : 'US$') . $venta->igv,
							($venta->divisa == 'Soles' ? 'S/' : 'US$') . $venta->subTotal,
							($venta->divisa == 'Soles' ? 'S/' : 'US$') . $venta->total,
							$venta->estado ? 'Conforme' : 'Anulado'
						];
					}

					$data[] = [''];
					$data[] = ['RESUMEN', ''];
					$styleRows->summary[] = count($data);
					$data[] = ['ISC', 'S/' . (number_format($listaTReciboVenta->map(function($item, $key) {
						return $item->isc * ($item->divisa == 'Soles' ? 1 : $item->tipoCambioUsd);
					})->sum(), 2, '.', ''))];
					$data[] = ['IGV', 'S/' . (number_format($listaTReciboVenta->map(function($item, $key){
						return $item->igv * ($item->divisa == 'Soles' ? 1 : $item->tipoCambioUsd);
					})->sum(), 2, '.', ''))];
					$data[] = ['SUBTOTAL', 'S/' . (number_format($listaTReciboVenta->map(function($item, $key){
						return $item->subTotal * ($item->divisa == 'Soles' ? 1 : $item->tipoCambioUsd);
					})->sum(), 2, '.', ''))];
					$data[] = ['TOTAL', 'S/' . (number_format($listaTReciboVenta->map(function($item, $key){
						return $item->total * ($item->divisa == 'Soles' ? 1 : $item->tipoCambioUsd);
					})->sum(), 2, '.', ''))];
					$data[] = ['TOTAL ANULADAS', 'S/' . (number_format($listaTReciboVenta->where('estado', 0)->map(function($item, $key){
						return $item->total * ($item->divisa == 'Soles' ? 1 : $item->tipoCambioUsd);
					})->sum(), 2, '.', ''))];

					return new ReportGeneratorExport($headers, $data, $fileName, function(AfterSheet $event) use ($styleRows) {
						$styleTitleGeneral = [
							'font' => [
								'bold' => true,
								'color' => [
									'argb' => 'FFFFFF',
								]
							],
							'fill' => [
								'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
								'color' => [
									'argb' => '141618',
								]
							],
						];

						foreach($styleRows->generalTitle as $generalTitle)
						{
							$event->sheet->getDelegate()->getStyle('A' . $generalTitle . ':L' . $generalTitle)->applyFromArray($styleTitleGeneral);
						}

						foreach($styleRows->summary as $summary)
						{
							$event->sheet->getDelegate()->getStyle('A' . $summary . ':B' . $summary)->applyFromArray($styleTitleGeneral);
						}
					});
					break;
			}
		}

		if (!$sessionManager->get('facturacionElectronica')) {
			echo '<div class="alert alert-danger"><h4><i class="icon fa fa-ban"></i> Prohibido!</h4>No tiene autorización para realizar operaciones electrónicas con la SUNAT.</div>';
			exit;
		}

		if (!(strpos($sessionManager->get('rol'), 'Súper usuario') !== false || (((strpos($sessionManager->get('rol'), 'Administrador') !== false || strpos($sessionManager->get('rol'), 'Ventas') !== false)) && strpos($sessionManager->get('rol'), 'Reporteador') !== false))) {
			echo '<div class="alert alert-danger"><h4><i class="icon fa fa-ban"></i> Prohibido!</h4>No tiene autorización para realizar esta operación o su "sesión de usuario" ya ha finalizado.</div>';
			exit;
		}

		$listTOficina = TOficina::whereRaw('codigoEmpresa = ?', [$sessionManager->get('codigoEmpresa')])->get();
		$listaTCategoriaVenta = TCategoriaVenta::with(['tcategoriaventachild.tcategoriaventachild'])->whereRaw('codigoEmpresa=? and estado=? and codigoCategoriaVentaPadre is null', [$sessionManager->get('codigoEmpresa'), true])->get();

		return View("reporte/ventasconsolidado", ['listTOficina' => $listTOficina, 'listaTCategoriaVenta' => $listaTCategoriaVenta]);
	}

	public function actionVentasWef(Request $request, SessionManager $sessionManager, Application $application)
	{
		if ($request->has('reporte')) {
			if (!(strpos($sessionManager->get('rol'), 'Súper usuario') !== false || (((strpos($sessionManager->get('rol'), 'Administrador') !== false || strpos($sessionManager->get('rol'), 'Ventas') !== false)) && strpos($sessionManager->get('rol'), 'Reporteador') !== false))) {
				return $this->plataformHelper->redirectError('No tiene permisos para generar este reporte.', 'reporte/index');
			}

			$tipoRecibo = $request->input('tipoComprobante');
			$fechaInicial = $request->input('fechaInicial');
			$fechaFinal = $request->input('fechaFinal');
			$codigoOficina = $request->input('codOficina') == null ? '' : $request->input('codOficina');
			$codigoPersonal = $request->input('codPersonal') == null ? '' : $request->input('codPersonal');
			$tipoComprobante = $request->input('tipoComprobante') == "Indistinto" ? "" : $request->input('tipoComprobante');
			$listaTReciboVenta = null;
			$tOficina = TOficina::find($codigoOficina);
			$filtro = $request->input('filtroVenta');
			$listaEstadisticaVenta = null;
			$listaEstadisticaVentaProductos = null;

			if ($request->input('selectCategoriaVentaNivelTres') != null && $request->input('selectCategoriaVentaNivelTres') != "") {
				$listaTReciboVenta = TReciboVentaOutEf::with(['treciboventadetalleoutef', 'tpersonal'])
					->whereRaw(
						'tipoRecibo like ? and fechaComprobanteEmitido >= ? and fechaComprobanteEmitido <= ? ' . ($codigoPersonal != '' ? 'and codigoPersonal=?' : ($codigoOficina != '' ? 'and codigoOficina=?' : 'and codigoOficina in (select codigoOficina from toficina where codigoEmpresa=?)'))
							. ' and codigoCategoriaVenta=? ' . ($filtro == 'all' ? '' : ($filtro == '!=0' ? 'and total!=0' : ' and total=0'))
							. ' and (? or estado = ?)',
						[
							'%' . $tipoComprobante . '%', $request->input('fechaInicial'), $request->input('fechaFinal'),
							($codigoPersonal != '' ? $codigoPersonal : ($codigoOficina != '' ? $codigoOficina : $sessionManager->get('codigoEmpresa'))),
							$request->input('selectCategoriaVentaNivelTres'), $request->input('tipoVenta') == 'todos', ($request->input('tipoVenta') == 'conforme' ? true : false)
						]
					)
					->orderBy('fechaComprobanteEmitido', 'desc')
					->get();

				$listaEstadisticaVenta = TReciboVentaOutEf::with(['tcategoriaventa.tcategoriaventa.tcategoriaventa', 'treciboventadetalleoutef'])
					->whereRaw(
						'tipoRecibo like ? and fechaComprobanteEmitido >= ? and fechaComprobanteEmitido <= ? ' . ($codigoPersonal != '' ? 'and codigoPersonal=?' : ($codigoOficina != '' ? 'and codigoOficina=?' : 'and codigoOficina in (select codigoOficina from toficina where codigoEmpresa=?)'))
							. ' and codigoCategoriaVenta=? ' . ($filtro == 'all' ? '' : ($filtro == '!=0' ? 'and total!=0' : ' and total=0'))
							. ' and (? or estado = ?)',
						[
							'%' . $tipoComprobante . '%', $request->input('fechaInicial'), $request->input('fechaFinal'),
							($codigoPersonal != '' ? $codigoPersonal : ($codigoOficina != '' ? $codigoOficina : $sessionManager->get('codigoEmpresa'))),
							$request->input('selectCategoriaVentaNivelTres'), $request->input('tipoVenta') == 'todos', ($request->input('tipoVenta') == 'conforme' ? true : false)
						]
					)
					->groupBy(['codigoCategoriaVenta'])
					->selectRaw('codigoCategoriaVenta, count(*) as cantidadVentas')
					->orderBy('codigoCategoriaVenta', 'asc')
					->get();

				$listaEstadisticaVentaProductos = DB::select('select t.codigoCategoriaVenta, sum(t.productos) as cantidadProductos from (select codigoCategoriaVenta, (select count(trvd.codigoReciboVentaDetalleOutEf) from treciboventadetalleoutef trvd where trvd.codigoReciboVentaOutEf = tr.codigoReciboVentaOutEf ) as productos from treciboventaoutef tr where tipoRecibo like ? and fechaComprobanteEmitido >= ? and fechaComprobanteEmitido <= ? ' . ($codigoPersonal != '' ? 'and codigoPersonal=?' : ($codigoOficina != '' ? 'and codigoOficina=?' : 'and codigoOficina in (select codigoOficina from toficina where codigoEmpresa=?)'))
					. ' and codigoCategoriaVenta=? ' . ($filtro == 'all' ? '' : ($filtro == '!=0' ? 'and total!=0' : ' and total=0'))
					. ' and (? or estado = ?) ) t group by t.codigoCategoriaVenta order by t.codigoCategoriaVenta asc', [
					'%' . $tipoComprobante . '%', $request->input('fechaInicial'), $request->input('fechaFinal'),
					($codigoPersonal != '' ? $codigoPersonal : ($codigoOficina != '' ? $codigoOficina : $sessionManager->get('codigoEmpresa'))),
					$request->input('selectCategoriaVentaNivelTres'), $request->input('tipoVenta') == 'todos', ($request->input('tipoVenta') == 'conforme' ? true : false)
				]);
			} elseif ($request->input('selectCategoriaVentaNivelDos') != null && $request->input('selectCategoriaVentaNivelDos') != "") {
				$listaTReciboVenta = TReciboVentaOutEf::with(['treciboventadetalleoutef', 'tpersonal'])
					->whereRaw(
						'tipoRecibo like ? and fechaComprobanteEmitido >= ? and fechaComprobanteEmitido <= ? ' . ($codigoPersonal != '' ? 'and codigoPersonal=?' : ($codigoOficina != '' ? 'and codigoOficina=?' : 'and codigoOficina in (select codigoOficina from toficina where codigoEmpresa=?)'))
							. ' and (codigoCategoriaVenta in (select codigoCategoriaVenta from tcategoriaventa tcv1 where codigoCategoriaVentaPadre = ? and codigoEmpresa = ? or codigoCategoriaVentaPadre in (select codigoCategoriaVenta from tcategoriaventa tcv2 where tcv2.codigoCategoriaVentaPadre = ? and tcv2.codigoEmpresa = ?)) or codigoCategoriaVenta=?) ' . ($filtro == 'all' ? '' : ($filtro == '!=0' ? 'and total!=0' : ' and total=0'))
							. ' and (? or estado = ?)',
						[
							'%' . $tipoComprobante . '%', $request->input('fechaInicial'), $request->input('fechaFinal'),
							($codigoPersonal != '' ? $codigoPersonal : ($codigoOficina != '' ? $codigoOficina : $sessionManager->get('codigoEmpresa'))), $request->input('selectCategoriaVentaNivelDos'), $sessionManager->get('codigoEmpresa'), $request->input('selectCategoriaVentaNivelDos'), $sessionManager->get('codigoEmpresa'), $request->input('selectCategoriaVentaNivelDos'), $request->input('tipoVenta') == 'todos', ($request->input('tipoVenta') == 'conforme' ? true : false)
						]
					)
					->orderBy('fechaComprobanteEmitido', 'desc')
					->get();

				$listaEstadisticaVenta = TReciboVentaOutEf::with(['tcategoriaventa.tcategoriaventa.tcategoriaventa', 'treciboventadetalleoutef'])
					->whereRaw(
						'tipoRecibo like ? and fechaComprobanteEmitido >= ? and fechaComprobanteEmitido <= ? ' . ($codigoPersonal != '' ? 'and codigoPersonal=?' : ($codigoOficina != '' ? 'and codigoOficina=?' : 'and codigoOficina in (select codigoOficina from toficina where codigoEmpresa=?)'))
							. ' and (codigoCategoriaVenta in (select codigoCategoriaVenta from tcategoriaventa tcv1 where codigoCategoriaVentaPadre = ? and codigoEmpresa = ? or codigoCategoriaVentaPadre in (select codigoCategoriaVenta from tcategoriaventa tcv2 where tcv2.codigoCategoriaVentaPadre = ? and tcv2.codigoEmpresa = ?)) or codigoCategoriaVenta=?) ' . ($filtro == 'all' ? '' : ($filtro == '!=0' ? 'and total!=0' : ' and total=0'))
							. ' and (? or estado = ?)',
						[
							'%' . $tipoComprobante . '%', $request->input('fechaInicial'), $request->input('fechaFinal'),
							($codigoPersonal != '' ? $codigoPersonal : ($codigoOficina != '' ? $codigoOficina : $sessionManager->get('codigoEmpresa'))),
							$request->input('selectCategoriaVentaNivelDos'), $sessionManager->get('codigoEmpresa'), $request->input('selectCategoriaVentaNivelDos'), $sessionManager->get('codigoEmpresa'), $request->input('selectCategoriaVentaNivelDos'), $request->input('tipoVenta') == 'todos', ($request->input('tipoVenta') == 'conforme' ? true : false)
						]
					)
					->groupBy(['codigoCategoriaVenta'])
					->selectRaw('codigoCategoriaVenta, count(*) as cantidadVentas')
					->orderBy('codigoCategoriaVenta', 'asc')
					->get();

				$listaEstadisticaVentaProductos = DB::select('select t.codigoCategoriaVenta, sum(t.productos) as cantidadProductos from (select codigoCategoriaVenta, (select count(trvd.codigoReciboVentaDetalleOutEf) from treciboventadetalleoutef trvd where trvd.codigoReciboVentaOutEf = tr.codigoReciboVentaOutEf ) as productos from treciboventaoutef tr where tipoRecibo like ? and fechaComprobanteEmitido >= ? and fechaComprobanteEmitido <= ? ' . ($codigoPersonal != '' ? 'and codigoPersonal=?' : ($codigoOficina != '' ? 'and codigoOficina=?' : 'and codigoOficina in (select codigoOficina from toficina where codigoEmpresa=?)'))
					. ' and (codigoCategoriaVenta in (select codigoCategoriaVenta from tcategoriaventa tcv1 where codigoCategoriaVentaPadre = ? and codigoEmpresa = ? or codigoCategoriaVentaPadre in (select codigoCategoriaVenta from tcategoriaventa tcv2 where tcv2.codigoCategoriaVentaPadre = ? and tcv2.codigoEmpresa = ?)) or codigoCategoriaVenta=?) ' . ($filtro == 'all' ? '' : ($filtro == '!=0' ? 'and total!=0' : ' and total=0'))
					. ' and (? or estado = ?) ) t group by t.codigoCategoriaVenta order by t.codigoCategoriaVenta asc', [
					'%' . $tipoComprobante . '%', $request->input('fechaInicial'), $request->input('fechaFinal'),
					($codigoPersonal != '' ? $codigoPersonal : ($codigoOficina != '' ? $codigoOficina : $sessionManager->get('codigoEmpresa'))), $request->input('selectCategoriaVentaNivelDos'), $sessionManager->get('codigoEmpresa'), $request->input('selectCategoriaVentaNivelDos'), $sessionManager->get('codigoEmpresa'), $request->input('selectCategoriaVentaNivelDos'), $request->input('tipoVenta') == 'todos', ($request->input('tipoVenta') == 'conforme' ? true : false)
				]);
			} elseif ($request->input('selectCategoriaVentaNivelUno') != null && $request->input('selectCategoriaVentaNivelUno') != "") {
				$listaTReciboVenta = TReciboVentaOutEf::with(['treciboventadetalleoutef', 'tpersonal'])
					->whereRaw(
						'tipoRecibo like ? and fechaComprobanteEmitido >= ? and fechaComprobanteEmitido <= ? ' . ($codigoPersonal != '' ? 'and codigoPersonal=?' : ($codigoOficina != '' ? 'and codigoOficina=?' : 'and codigoOficina in (select codigoOficina from toficina where codigoEmpresa=?)'))
							. ' and (codigoCategoriaVenta in (select codigoCategoriaVenta from tcategoriaventa tcv1 where codigoCategoriaVentaPadre = ? and codigoEmpresa = ? or codigoCategoriaVentaPadre in (select codigoCategoriaVenta from tcategoriaventa tcv2 where tcv2.codigoCategoriaVentaPadre = ? and tcv2.codigoEmpresa = ?)) or codigoCategoriaVenta=?) ' . ($filtro == 'all' ? '' : ($filtro == '!=0' ? 'and total!=0' : ' and total=0'))
							. ' and (? or estado = ?)',
						[
							'%' . $tipoComprobante . '%', $request->input('fechaInicial'), $request->input('fechaFinal'),
							($codigoPersonal != '' ? $codigoPersonal : ($codigoOficina != '' ? $codigoOficina : $sessionManager->get('codigoEmpresa'))),
							$request->input('selectCategoriaVentaNivelUno'), $sessionManager->get('codigoEmpresa'), $request->input('selectCategoriaVentaNivelUno'), $sessionManager->get('codigoEmpresa'), $request->input('selectCategoriaVentaNivelUno'), $request->input('tipoVenta') == 'todos', ($request->input('tipoVenta') == 'conforme' ? true : false)
						]
					)
					->orderBy('fechaComprobanteEmitido', 'desc')
					->get();

				$listaEstadisticaVenta = TReciboVentaOutEf::with(['tcategoriaventa.tcategoriaventa.tcategoriaventa', 'treciboventadetalleoutef'])
					->whereRaw(
						'tipoRecibo like ? and fechaComprobanteEmitido >= ? and fechaComprobanteEmitido <= ? ' . ($codigoPersonal != '' ? 'and codigoPersonal=?' : ($codigoOficina != '' ? 'and codigoOficina=?' : 'and codigoOficina in (select codigoOficina from toficina where codigoEmpresa=?)'))
							. ' and (codigoCategoriaVenta in (select codigoCategoriaVenta from tcategoriaventa tcv1 where codigoCategoriaVentaPadre = ? and codigoEmpresa = ? or codigoCategoriaVentaPadre in (select codigoCategoriaVenta from tcategoriaventa tcv2 where tcv2.codigoCategoriaVentaPadre = ? and tcv2.codigoEmpresa = ?)) or codigoCategoriaVenta=?) ' . ($filtro == 'all' ? '' : ($filtro == '!=0' ? 'and total!=0' : ' and total=0'))
							. ' and (? or estado = ?)',
						[
							'%' . $tipoComprobante . '%', $request->input('fechaInicial'), $request->input('fechaFinal'),
							($codigoPersonal != '' ? $codigoPersonal : ($codigoOficina != '' ? $codigoOficina : $sessionManager->get('codigoEmpresa'))),
							$request->input('selectCategoriaVentaNivelUno'), $sessionManager->get('codigoEmpresa'), $request->input('selectCategoriaVentaNivelUno'), $sessionManager->get('codigoEmpresa'), $request->input('selectCategoriaVentaNivelUno'), $request->input('tipoVenta') == 'todos', ($request->input('tipoVenta') == 'conforme' ? true : false)
						]
					)
					->groupBy(['codigoCategoriaVenta'])
					->selectRaw('codigoCategoriaVenta, count(*) as cantidadVentas')
					->orderBy('codigoCategoriaVenta', 'asc')
					->get();

				$listaEstadisticaVentaProductos = DB::select('select t.codigoCategoriaVenta, sum(t.productos) as cantidadProductos from (select codigoCategoriaVenta, (select count(trvd.codigoReciboVentaDetalleOutEf) from treciboventadetalleoutef trvd where trvd.codigoReciboVentaOutEf = tr.codigoReciboVentaOutEf ) as productos from treciboventaoutef tr where tipoRecibo like ? and fechaComprobanteEmitido >= ? and fechaComprobanteEmitido <= ? ' . ($codigoPersonal != '' ? 'and codigoPersonal=?' : ($codigoOficina != '' ? 'and codigoOficina=?' : 'and codigoOficina in (select codigoOficina from toficina where codigoEmpresa=?)'))
					. ' and (codigoCategoriaVenta in (select codigoCategoriaVenta from tcategoriaventa tcv1 where codigoCategoriaVentaPadre = ? and codigoEmpresa = ? or codigoCategoriaVentaPadre in (select codigoCategoriaVenta from tcategoriaventa tcv2 where tcv2.codigoCategoriaVentaPadre = ? and tcv2.codigoEmpresa = ?)) or codigoCategoriaVenta=?) ' . ($filtro == 'all' ? '' : ($filtro == '!=0' ? 'and total!=0' : ' and total=0'))
					. ' and (? or estado = ?) ) t group by t.codigoCategoriaVenta order by t.codigoCategoriaVenta asc', [
					'%' . $tipoComprobante . '%', $request->input('fechaInicial'), $request->input('fechaFinal'),
					($codigoPersonal != '' ? $codigoPersonal : ($codigoOficina != '' ? $codigoOficina : $sessionManager->get('codigoEmpresa'))),
					$request->input('selectCategoriaVentaNivelUno'), $sessionManager->get('codigoEmpresa'), $request->input('selectCategoriaVentaNivelUno'), $sessionManager->get('codigoEmpresa'), $request->input('selectCategoriaVentaNivelUno'), $request->input('tipoVenta') == 'todos', ($request->input('tipoVenta') == 'conforme' ? true : false)
				]);
			} else {
				$listaTReciboVenta = TReciboVentaOutEf::with(['treciboventadetalleoutef', 'tpersonal'])
					->whereRaw(
						'tipoRecibo like ? and fechaComprobanteEmitido >= ? and fechaComprobanteEmitido <= ? ' . ($codigoPersonal != '' ? 'and codigoPersonal=?' : ($codigoOficina != '' ? 'and codigoOficina=?' : 'and codigoOficina in (select codigoOficina from toficina where codigoEmpresa=?)')) . ($filtro == 'all' ? '' : ($filtro == '!=0' ? 'and total!=0' : ' and total=0'))
							. ' and (? or estado = ?)',
						[
							'%' . $tipoComprobante . '%', $request->input('fechaInicial'), $request->input('fechaFinal'),
							($codigoPersonal != '' ? $codigoPersonal : ($codigoOficina != '' ? $codigoOficina : $sessionManager->get('codigoEmpresa'))),
							$request->input('tipoVenta') == 'todos', ($request->input('tipoVenta') == 'conforme' ? true : false)
						]
					)
					->orderBy('fechaComprobanteEmitido', 'desc')
					->get();


				$listaEstadisticaVenta = TReciboVentaOutEf::with(['tcategoriaventa.tcategoriaventa.tcategoriaventa', 'treciboventadetalleoutef'])
					->whereRaw(
						'tipoRecibo like ? and fechaComprobanteEmitido >= ? and fechaComprobanteEmitido <= ? ' . ($codigoPersonal != '' ? 'and codigoPersonal=?' : ($codigoOficina != '' ? 'and codigoOficina=?' : 'and codigoOficina in (select codigoOficina from toficina where codigoEmpresa=?)')) . ($filtro == 'all' ? '' : ($filtro == '!=0' ? 'and total!=0' : ' and total=0'))
							. ' and (? or estado = ?)',
						[
							'%' . $tipoComprobante . '%', $request->input('fechaInicial'), $request->input('fechaFinal'),
							($codigoPersonal != '' ? $codigoPersonal : ($codigoOficina != '' ? $codigoOficina : $sessionManager->get('codigoEmpresa'))),
							$request->input('tipoVenta') == 'todos', ($request->input('tipoVenta') == 'conforme' ? true : false)
						]
					)
					->groupBy(['codigoCategoriaVenta'])
					->selectRaw('codigoCategoriaVenta, count(*) as cantidadVentas')
					->orderBy('codigoCategoriaVenta', 'asc')
					->get();

				$listaEstadisticaVentaProductos = DB::select('select t.codigoCategoriaVenta, sum(t.productos) as cantidadProductos from (select codigoCategoriaVenta, (select count(trvd.codigoReciboVentaDetalleOutEf) from treciboventadetalleoutef trvd where trvd.codigoReciboVentaOutEf = tr.codigoReciboVentaOutEf ) as productos from treciboventaoutef tr where tipoRecibo like ? and fechaComprobanteEmitido >= ? and fechaComprobanteEmitido <= ? ' . ($codigoPersonal != '' ? 'and codigoPersonal=?' : ($codigoOficina != '' ? 'and codigoOficina=?' : 'and codigoOficina in (select codigoOficina from toficina where codigoEmpresa=?)')) . ($filtro == 'all' ? '' : ($filtro == '!=0' ? 'and total!=0' : ' and total=0'))
					. ' and (? or estado = ?) ) t group by t.codigoCategoriaVenta order by t.codigoCategoriaVenta asc', [
					'%' . $tipoComprobante . '%', $request->input('fechaInicial'), $request->input('fechaFinal'),
					($codigoPersonal != '' ? $codigoPersonal : ($codigoOficina != '' ? $codigoOficina : $sessionManager->get('codigoEmpresa'))),
					$request->input('tipoVenta') == 'todos', ($request->input('tipoVenta') == 'conforme' ? true : false)
				]);
			}

			switch (strtolower(str_replace(' ', '', $request->input('reporte')))) {
				case "exportaraexcel":
					if ($listaTReciboVenta->count() < 1) {
						$request->flash();

						return $this->plataformHelper->redirectError('No se puede exportar, no se encontraron registros con el filtro actual!.', '/reporte/index');
					}

					$data = [];
					$styleRows = (object) [
						"merge" => [],
						"sales" => [],
						"generalTitle" => [],
						"generalSubtitle" => [],
						"summaryTitle" => [],
						"separator" => [],
					];
					$headers = [];
					$fileName = 'reporteVentasWef-' . $tipoRecibo . ($fechaInicial != '' ? ('-' . str_replace('/', '', $fechaInicial) . '-' . str_replace('/', '', $fechaFinal)) : '') . '-Oficina[' . ($tOficina !== null ? $tOficina->descripcion : 'Todas') . '].xlsx';

					foreach ($listaTReciboVenta as $key => $venta) {
						$data[] = ['VENTA #' . ($key + 1)];

						$styleRows->merge[] = count($data);
						$styleRows->sales[] = count($data);

						$data[] = [
							'N° recibo',
							'Categoría',
							'Personal',
							'Cliente',
							'Tipo recibo',
							'Tipo pago',
							'Fecha emitido',
							'Estado.'
						];

						$styleRows->generalTitle[] = count($data);

						$data[] = [							
							$venta->numeroRecibo,
							$this->plataformHelper->obtenerRamaCategoriaVenta($venta->tcategoriaventa, ' > '),
							$venta->tpersonal->dni . ' - ' . $venta->tpersonal->nombre . ' ' . $venta->tpersonal->apellido,
							$venta->documentoCliente . ' - ' . $venta->nombreCompletoCliente,
							$venta->tipoRecibo,
							$venta->tipoPago . ($venta->tipoPago == "Al crédito" ? (' (' . $venta->letras . ' letras)') : ''),
							$venta->fechaComprobanteEmitido,
							$venta->estado ? 'Conforme' : 'Anulado'
						];
						$data[] = [
							'Producto',
							'Inf. Adicional',
							'Tipo',
							'Impuesto',
							'Cantidad',
							'Impuesto aplicado',
							'Precio venta',
							'Venta total'
						];

						$styleRows->generalSubtitle[] = count($data);

						foreach ($venta->treciboventadetalleoutef as $key => $detalleVenta) {

							$data[] = [
								'(' . $detalleVenta->codigoBarrasProducto . ') ' . $detalleVenta->nombreProducto,
								$detalleVenta->informacionAdicionalProducto,
								$detalleVenta->tipoProducto,
								$detalleVenta->tipoImpuestoProducto . ' ' . $detalleVenta->porcentajeTributacionProducto . '%',
								$detalleVenta->cantidadProducto,
								$detalleVenta->impuestoAplicadoProducto,
								'S/' . $detalleVenta->precioVentaUnitarioProducto,
								'S/' . $detalleVenta->precioVentaTotalProducto
							];
						}

						$countCell = 8;
						$data[] = array_pad(['ISC', 'S/' . $venta->isc], -$countCell, ' ');
						$data[] = array_pad(['IGV', 'S/' . $venta->igv], -$countCell, ' ');
						$data[] = array_pad(['SUBTOTAL', 'S/' . $venta->subTotal], -$countCell, ' ');
						$data[] = array_pad(['TOTAL', 'S/' . $venta->total], -$countCell, ' ');

						$data[] = [' '];
						$styleRows->separator[] = count($data);
						$data[] = [' '];
					}

					$data[] = ['RESUMEN', ''];
					$styleRows->summaryTitle[] = count($data);
					$data[] = ['ISC', 'S/' . (number_format($listaTReciboVenta->sum('isc'), 2, '.', ''))];
					$data[] = ['IGV', 'S/' . (number_format($listaTReciboVenta->sum('igv'), 2, '.', ''))];
					$data[] = ['SUBTOTAL', 'S/' . (number_format($listaTReciboVenta->sum('subTotal'), 2, '.', ''))];
					$data[] = ['TOTAL', 'S/' . (number_format($listaTReciboVenta->sum('total'), 2, '.', ''))];
					$data[] = ['TOTAL ANULADAS', 'S/' . (number_format($listaTReciboVenta->where('estado', 0)->sum('total'), 2, '.', ''))];
					$data[] = [' '];

					$data[] = ['ESTADÍSTICAS VENTA', ''];
					$styleRows->summaryTitle[] = count($data);

					foreach ($listaEstadisticaVenta as $value) {
						$data[] = [$this->plataformHelper->obtenerRamaCategoriaVenta($value->tcategoriaventa, '>'), $value->cantidadVentas];
					}

					$data[] = [' '];
					$data[] = ['ESTADÍSTICAS VENTA PRODUCTOS', ''];
					$styleRows->summaryTitle[] = count($data);

					foreach ($listaEstadisticaVentaProductos as $value) {
						$data[] = [$this->plataformHelper->obtenerRamaCategoriaVenta(TCategoriaVenta::with('tcategoriaventa.tcategoriaventa')->find($value->codigoCategoriaVenta), '>'), $value->cantidadProductos];
					}

					return new ReportGeneratorExport($headers, $data, $fileName, function(AfterSheet $event) use ($styleRows) {
						$styleMerge = [
							'font' => [
								'bold' => true,
								'color' => [
									'argb' => 'FFFFFF',
								]
							],
							'alignment' => [
								'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
							],
							'borders' => [
								'allBorders' => [
									'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
								],
							]
						];

						$styleSale = [
							'fill' => [
								'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
								'color' => [
									'argb' => '141618',
								]
							],
						];

						$styleTitleGeneral = [
							'font' => [
								'bold' => true,
								'color' => [
									'argb' => 'FFFFFF',
								]
							],
							'fill' => [
								'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
								'color' => [
									'argb' => '282D31',
								]
							],
						];

						$styleSubtitleGeneral = [
							'font' => [
								'bold' => true,
								'color' => [
									'argb' => 'FFFFFF',
								]
							],
							'fill' => [
								'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
								'color' => [
									'argb' => '53575A',
								]
							],
						];

						$styleSummary = [
							'font' => [
								'bold' => true,
								'color' => [
									'argb' => 'FFFFFF',
								]
							],
							'fill' => [
								'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
								'color' => [
									'argb' => '141618',
								]
							],
						];

						$styleSeparator = [
							'fill' => [
								'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
								'color' => [
									'argb' => 'FBA905',
								]
							],
						];

						foreach($styleRows->merge as $merge)
						{
							$event->sheet->getDelegate()->mergeCells('A' . $merge . ':H' . $merge)->getStyle('A' . $merge . ':H' . $merge)->applyFromArray($styleMerge);
						}

						foreach($styleRows->sales as $sales)
						{
							$event->sheet->getDelegate()->getStyle('A' . $sales . ':H' . $sales)->applyFromArray($styleSale);
						}
						
						foreach($styleRows->generalTitle as $generalTitle)
						{
							$event->sheet->getDelegate()->getStyle('A' . $generalTitle . ':H' . $generalTitle)->applyFromArray($styleTitleGeneral);
						}

						foreach($styleRows->generalSubtitle as $generalSubtitle)
						{
							$event->sheet->getDelegate()->getStyle('A' . $generalSubtitle . ':H' . $generalSubtitle)->applyFromArray($styleSubtitleGeneral);
						}

						foreach($styleRows->summaryTitle as $summaryTitle)
						{
							$event->sheet->getDelegate()->getStyle('A' . $summaryTitle . ':B' . $summaryTitle)->applyFromArray($styleSummary);
						}

						foreach($styleRows->separator as $separator)
						{
							$event->sheet->getDelegate()->getStyle('A' . $separator . ':I' . $separator)->applyFromArray($styleSeparator);
						}
					});
					
					break;
			}
		}

		if (!(strpos($sessionManager->get('rol'), 'Súper usuario') !== false || (((strpos($sessionManager->get('rol'), 'Administrador') !== false || strpos($sessionManager->get('rol'), 'Ventas') !== false)) && strpos($sessionManager->get('rol'), 'Reporteador') !== false))) {
			echo '<div class="alert alert-danger"><h4><i class="icon fa fa-ban"></i> Prohibido!</h4>No tiene autorización para realizar esta operación o su "sesión de usuario" ya ha finalizado.</div>';
			exit;
		}

		$listTOficina = TOficina::whereRaw('codigoEmpresa = ?', [$sessionManager->get('codigoEmpresa')])->get();
		$listaTCategoriaVenta = TCategoriaVenta::with(['tcategoriaventachild.tcategoriaventachild'])->whereRaw('codigoEmpresa=? and estado=? and codigoCategoriaVentaPadre is null', [$sessionManager->get('codigoEmpresa'), true])->get();

		return View("reporte/ventaswef", ['listTOficina' => $listTOficina, 'listaTCategoriaVenta' => $listaTCategoriaVenta]);
	}

	public function actionVentasWefConsolidado(Request $request, SessionManager $sessionManager, Application $application)
	{
		if ($request->has('reporte')) {
			if (!(strpos($sessionManager->get('rol'), 'Súper usuario') !== false || (((strpos($sessionManager->get('rol'), 'Administrador') !== false || strpos($sessionManager->get('rol'), 'Ventas') !== false)) && strpos($sessionManager->get('rol'), 'Reporteador') !== false))) {
				return $this->plataformHelper->redirectError('No tiene permisos para generar este reporte.', 'reporte/index');
			}

			$tipoRecibo = $request->input('tipoComprobante');
			$fechaInicial = $request->input('fechaInicial');
			$fechaFinal = $request->input('fechaFinal');
			$codigoOficina = $request->input('codOficina') == null ? '' : $request->input('codOficina');
			$codigoPersonal = $request->input('codPersonal') == null ? '' : $request->input('codPersonal');
			$tipoComprobante = $request->input('tipoComprobante') == "Indistinto" ? "" : $request->input('tipoComprobante');
			$listaTReciboVenta = null;
			$tOficina = TOficina::find($codigoOficina);
			$filtro = $request->input('filtroVenta');

			if ($request->input('selectCategoriaVentaNivelTres') != null && $request->input('selectCategoriaVentaNivelTres') != "") {
				$listaTReciboVenta = TReciboVentaOutEf::with(['treciboventadetalleoutef', 'tpersonal'])
					->whereRaw(
						'tipoRecibo like ? and fechaComprobanteEmitido >= ? and fechaComprobanteEmitido <= ? ' . ($codigoPersonal != '' ? 'and codigoPersonal=?' : ($codigoOficina != '' ? 'and codigoOficina=?' : 'and codigoOficina in (select codigoOficina from toficina where codigoEmpresa=?)'))
							. ' and codigoCategoriaVenta=? ' . ($filtro == 'all' ? '' : ($filtro == '!=0' ? 'and total!=0' : ' and total=0'))
							. ' and (? or estado = ?)',
						[
							'%' . $tipoComprobante . '%', $request->input('fechaInicial'), $request->input('fechaFinal'),
							($codigoPersonal != '' ? $codigoPersonal : ($codigoOficina != '' ? $codigoOficina : $sessionManager->get('codigoEmpresa'))),
							$request->input('selectCategoriaVentaNivelTres'), $request->input('tipoVenta') == 'todos', ($request->input('tipoVenta') == 'conforme' ? true : false)
						]
					)
					->orderBy('fechaComprobanteEmitido', 'desc')
					->get();
			} elseif ($request->input('selectCategoriaVentaNivelDos') != null && $request->input('selectCategoriaVentaNivelDos') != "") {
				$listaTReciboVenta = TReciboVentaOutEf::with(['treciboventadetalleoutef', 'tpersonal'])
					->whereRaw(
						'tipoRecibo like ? and fechaComprobanteEmitido >= ? and fechaComprobanteEmitido <= ? ' . ($codigoPersonal != '' ? 'and codigoPersonal=?' : ($codigoOficina != '' ? 'and codigoOficina=?' : 'and codigoOficina in (select codigoOficina from toficina where codigoEmpresa=?)'))
							. ' and (codigoCategoriaVenta in (select codigoCategoriaVenta from tcategoriaventa tcv1 where codigoCategoriaVentaPadre = ? and codigoEmpresa = ? or codigoCategoriaVentaPadre in (select codigoCategoriaVenta from tcategoriaventa tcv2 where tcv2.codigoCategoriaVentaPadre = ? and tcv2.codigoEmpresa = ?)) or codigoCategoriaVenta=?) ' . ($filtro == 'all' ? '' : ($filtro == '!=0' ? 'and total!=0' : ' and total=0'))
							. ' and (? or estado = ?)',
						[
							'%' . $tipoComprobante . '%', $request->input('fechaInicial'), $request->input('fechaFinal'),
							($codigoPersonal != '' ? $codigoPersonal : ($codigoOficina != '' ? $codigoOficina : $sessionManager->get('codigoEmpresa'))), $request->input('selectCategoriaVentaNivelDos'), $sessionManager->get('codigoEmpresa'), $request->input('selectCategoriaVentaNivelDos'), $sessionManager->get('codigoEmpresa'), $request->input('selectCategoriaVentaNivelDos'), $request->input('tipoVenta') == 'todos', ($request->input('tipoVenta') == 'conforme' ? true : false)
						]
					)
					->orderBy('fechaComprobanteEmitido', 'desc')
					->get();
			} elseif ($request->input('selectCategoriaVentaNivelUno') != null && $request->input('selectCategoriaVentaNivelUno') != "") {
				$listaTReciboVenta = TReciboVentaOutEf::with(['treciboventadetalleoutef', 'tpersonal'])
					->whereRaw(
						'tipoRecibo like ? and fechaComprobanteEmitido >= ? and fechaComprobanteEmitido <= ? ' . ($codigoPersonal != '' ? 'and codigoPersonal=?' : ($codigoOficina != '' ? 'and codigoOficina=?' : 'and codigoOficina in (select codigoOficina from toficina where codigoEmpresa=?)'))
							. ' and (codigoCategoriaVenta in (select codigoCategoriaVenta from tcategoriaventa tcv1 where codigoCategoriaVentaPadre = ? and codigoEmpresa = ? or codigoCategoriaVentaPadre in (select codigoCategoriaVenta from tcategoriaventa tcv2 where tcv2.codigoCategoriaVentaPadre = ? and tcv2.codigoEmpresa = ?)) or codigoCategoriaVenta=?) ' . ($filtro == 'all' ? '' : ($filtro == '!=0' ? 'and total!=0' : ' and total=0'))
							. ' and (? or estado = ?)',
						[
							'%' . $tipoComprobante . '%', $request->input('fechaInicial'), $request->input('fechaFinal'),
							($codigoPersonal != '' ? $codigoPersonal : ($codigoOficina != '' ? $codigoOficina : $sessionManager->get('codigoEmpresa'))),
							$request->input('selectCategoriaVentaNivelUno'), $sessionManager->get('codigoEmpresa'), $request->input('selectCategoriaVentaNivelUno'), $sessionManager->get('codigoEmpresa'), $request->input('selectCategoriaVentaNivelUno'), $request->input('tipoVenta') == 'todos', ($request->input('tipoVenta') == 'conforme' ? true : false)
						]
					)
					->orderBy('fechaComprobanteEmitido', 'desc')
					->get();
			} else {
				$listaTReciboVenta = TReciboVentaOutEf::with(['treciboventadetalleoutef', 'tpersonal'])
					->whereRaw(
						'tipoRecibo like ? and fechaComprobanteEmitido >= ? and fechaComprobanteEmitido <= ? ' . ($codigoPersonal != '' ? 'and codigoPersonal=?' : ($codigoOficina != '' ? 'and codigoOficina=?' : 'and codigoOficina in (select codigoOficina from toficina where codigoEmpresa=?)')) . ($filtro == 'all' ? '' : ($filtro == '!=0' ? 'and total!=0' : ' and total=0'))
							. ' and (? or estado = ?)',
						[
							'%' . $tipoComprobante . '%', $request->input('fechaInicial'), $request->input('fechaFinal'),
							($codigoPersonal != '' ? $codigoPersonal : ($codigoOficina != '' ? $codigoOficina : $sessionManager->get('codigoEmpresa'))),
							$request->input('tipoVenta') == 'todos', ($request->input('tipoVenta') == 'conforme' ? true : false)
						]
					)
					->orderBy('fechaComprobanteEmitido', 'desc')
					->get();
			}

			switch (strtolower(str_replace(' ', '', $request->input('reporte')))) {
				case "exportaraexcel":
					if ($listaTReciboVenta->count() < 1) {
						$request->flash();

						return $this->plataformHelper->redirectError('No se puede exportar, no se encontraron registros con el filtro actual!.', '/reporte/index');
					}

					$data = [];
					$styleRows = (object) [
						"generalTitle" => [],
						"summary" => [],
					];
					$headers = [];
					$fileName = 'reporteVentasWefConsolidado-' . $tipoRecibo . ($fechaInicial != '' ? ('-' . str_replace('/', '', $fechaInicial) . '-' . str_replace('/', '', $fechaFinal)) : '') . '-Oficina[' . ($tOficina !== null ? $tOficina->descripcion : 'Todas') . '].xlsx';

					$data[] = [
						'Tipo recibo',
						'N° recibo',
						'Tipo pago',
						'Categoría',
						'Cliente',
						'Fecha emitido',
						'ISC',
						'IGV',
						'SubTotal',
						'Total',
						'Estado'
					];

					$styleRows->generalTitle[] = count($data);

					foreach ($listaTReciboVenta as $key => $venta) {
						$data[] = [
							$venta->tipoRecibo,
							$venta->numeroRecibo,
							$venta->tipoPago . ($venta->tipoPago == "Al crédito" ? (' (' . $venta->letras . ' letras)') : ''),
							$this->plataformHelper->obtenerRamaCategoriaVenta($venta->tcategoriaventa, ' > '),
							$venta->documentoCliente . ' - ' . $venta->nombreCompletoCliente,
							$venta->fechaComprobanteEmitido,
							'S/' . $venta->isc,
							'S/' . $venta->igv,
							'S/' . $venta->subTotal,
							'S/' . $venta->total,
							$venta->estado ? 'Conforme' : 'Anulado'
						];
					}

					$data[] = [''];
					$data[] = ['RESUMEN', ''];
					$styleRows->summary[] = count($data);
					$data[] = ['ISC', 'S/' . (number_format($listaTReciboVenta->sum('isc'), 2, '.', ''))];
					$data[] = ['IGV', 'S/' . (number_format($listaTReciboVenta->sum('igv'), 2, '.', ''))];
					$data[] = ['SUBTOTAL', 'S/' . (number_format($listaTReciboVenta->sum('subTotal'), 2, '.', ''))];
					$data[] = ['TOTAL', 'S/' . (number_format($listaTReciboVenta->sum('total'), 2, '.', ''))];
					$data[] = ['TOTAL ANULADAS', 'S/' . (number_format($listaTReciboVenta->where('estado', 0)->sum('total'), 2, '.', ''))];

					return new ReportGeneratorExport($headers, $data, $fileName, function(AfterSheet $event) use ($styleRows) {
						$styleTitleGeneral = [
							'font' => [
								'bold' => true,
								'color' => [
									'argb' => 'FFFFFF',
								]
							],
							'fill' => [
								'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
								'color' => [
									'argb' => '141618',
								]
							],
						];

						foreach($styleRows->generalTitle as $generalTitle)
						{
							$event->sheet->getDelegate()->getStyle('A' . $generalTitle . ':K' . $generalTitle)->applyFromArray($styleTitleGeneral);
						}

						foreach($styleRows->summary as $summary)
						{
							$event->sheet->getDelegate()->getStyle('A' . $summary . ':B' . $summary)->applyFromArray($styleTitleGeneral);
						}
					});
					
					break;
			}
		}

		if (!(strpos($sessionManager->get('rol'), 'Súper usuario') !== false || (((strpos($sessionManager->get('rol'), 'Administrador') !== false || strpos($sessionManager->get('rol'), 'Ventas') !== false)) && strpos($sessionManager->get('rol'), 'Reporteador') !== false))) {
			echo '<div class="alert alert-danger"><h4><i class="icon fa fa-ban"></i> Prohibido!</h4>No tiene autorización para realizar esta operación o su "sesión de usuario" ya ha finalizado.</div>';
			exit;
		}

		$listTOficina = TOficina::whereRaw('codigoEmpresa = ?', [$sessionManager->get('codigoEmpresa')])->get();
		$listaTCategoriaVenta = TCategoriaVenta::with(['tcategoriaventachild.tcategoriaventachild'])->whereRaw('codigoEmpresa=? and estado=? and codigoCategoriaVentaPadre is null', [$sessionManager->get('codigoEmpresa'), true])->get();

		return View("reporte/ventaswefconsolidado", ['listTOficina' => $listTOficina, 'listaTCategoriaVenta' => $listaTCategoriaVenta]);
	}

	public function actionNotaCredito(Request $request, SessionManager $sessionManager, Application $application)
	{
		if ($request->has('reporte')) {
			if (!$sessionManager->get('facturacionElectronica')) {
				return $this->plataformHelper->redirectError('No tiene autorización para realizar operaciones electrónicas con la SUNAT.', '/');
			}

			if (!(strpos($sessionManager->get('rol'), 'Súper usuario') !== false || (((strpos($sessionManager->get('rol'), 'Administrador') !== false || strpos($sessionManager->get('rol'), 'Ventas') !== false)) && strpos($sessionManager->get('rol'), 'Reporteador') !== false))) {
				return $this->plataformHelper->redirectError('No tiene permisos para generar este reporte.', 'reporte/index');
			}

			$fechaInicial = $request->input('fechaInicial');
			$fechaFinal = $request->input('fechaFinal');
			$codigoOficina = $request->input('codOficina') == null ? '' : $request->input('codOficina');
			$codigoPersonal = $request->input('codPersonal') == null ? '' : $request->input('codPersonal');
			$tOficina = TOficina::find($codigoOficina);
			$estadoSunat = $request->input('estadoSunat') == 'no-rechazados' ? " and estadoEnvioSunat <> 'Rechazado'" : " and estadoEnvioSunat = 'Rechazado'";

			$listaTReciboVentaNotaCredito = TReciboVentaNotaCredito::with(['treciboventa'])
				->whereRaw(
					'fechaComprobanteEmitido >= ? and fechaComprobanteEmitido <= ? ' . ($codigoPersonal != '' ? 'and codigoPersonal=?' : ($codigoOficina != '' ? 'and codigoOficina=?' : 'and codigoOficina in (select codigoOficina from toficina where codigoEmpresa=?)')) . $estadoSunat,
					[
						$request->input('fechaInicial'), $request->input('fechaFinal'),
						($codigoPersonal != '' ? $codigoPersonal : ($codigoOficina != '' ? $codigoOficina : $sessionManager->get('codigoEmpresa')))
					]
				)
				->orderBy('fechaComprobanteEmitido', 'desc')
				->get();

			switch (strtolower(str_replace(' ', '', $request->input('reporte')))) {
				case "exportaraexcel":
					if ($listaTReciboVentaNotaCredito->count() < 1) {
						$request->flash();

						return $this->plataformHelper->redirectError('No se puede exportar, no se encontraron registros con el filtro actual!.', '/reporte/index');
					}

					$data = [];
					$styleRows = (object) [
						"generalTitle" => [],
						"summary" => [],
					];
					$headers = [];
					$fileName = 'reporteNotasDeCredito-' . ($fechaInicial != '' ? ('-' . str_replace('/', '', $fechaInicial) . '-' . str_replace('/', '', $fechaFinal)) : '') . '-Oficina[' . ($tOficina !== null ? $tOficina->descripcion : 'Todas') . '].xlsx';

					$data[] = [
						'N° recibo',
						'Cod. Motivo',
						'Motivo',
						'Recibo Perteneciente',
						'Fecha emitido',
						'Divisa',
						'ISC',
						'IGV',
						'SubTotal',
						'Total'
					];

					$styleRows->generalTitle[] = count($data);

					foreach ($listaTReciboVentaNotaCredito as $key => $nc) {
						$data[] = [
							$nc->numeroRecibo,
							$nc->codigoMotivo,
							$nc->descripcionMotivo,
							$nc->treciboventa->numeroRecibo,
							$nc->fechaComprobanteEmitido,
							$nc->treciboventa->divisa == 'Soles' ? 'PEN' : 'USD',
							($nc->treciboventa->divisa == 'Soles' ? 'S/' : 'US$') . $nc->isc,
							($nc->treciboventa->divisa == 'Soles' ? 'S/' : 'US$') . $nc->igv,
							($nc->treciboventa->divisa == 'Soles' ? 'S/' : 'US$') . $nc->subTotal,
							($nc->treciboventa->divisa == 'Soles' ? 'S/' : 'US$') . $nc->total
						];
					}

					$data[] = [''];
					$data[] = ['RESUMEN', ''];
					$styleRows->summary[] = count($data);
					$data[] = ['ISC', 'S/' . (number_format($listaTReciboVentaNotaCredito->map(function($item, $key){
						return $item->isc * ($item->treciboventa->divisa == 'Soles' ? 1 : $item->treciboventa->tipoCambioUsd);
					})->sum(), 2, '.', ''))];
					$data[] = ['IGV', 'S/' . (number_format($listaTReciboVentaNotaCredito->map(function($item, $key){
						return $item->igv * ($item->treciboventa->divisa == 'Soles' ? 1 : $item->treciboventa->tipoCambioUsd);
					})->sum(), 2, '.', ''))];
					$data[] = ['SUBTOTAL', 'S/' . (number_format($listaTReciboVentaNotaCredito->map(function($item, $key){
						return $item->subTotal * ($item->treciboventa->divisa == 'Soles' ? 1 : $item->treciboventa->tipoCambioUsd);
					})->sum(), 2, '.', ''))];
					$data[] = ['TOTAL', 'S/' . (number_format($listaTReciboVentaNotaCredito->map(function($item, $key){
						return $item->total * ($item->treciboventa->divisa == 'Soles' ? 1 : $item->treciboventa->tipoCambioUsd);
					})->sum(), 2, '.', ''))];

					return new ReportGeneratorExport($headers, $data, $fileName, function(AfterSheet $event) use ($styleRows) {
						$styleTitleGeneral = [
							'font' => [
								'bold' => true,
								'color' => [
									'argb' => 'FFFFFF',
								]
							],
							'fill' => [
								'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
								'color' => [
									'argb' => '141618',
								]
							],
						];

						foreach($styleRows->generalTitle as $generalTitle)
						{
							$event->sheet->getDelegate()->getStyle('A' . $generalTitle . ':J' . $generalTitle)->applyFromArray($styleTitleGeneral);
						}

						foreach($styleRows->summary as $summary)
						{
							$event->sheet->getDelegate()->getStyle('A' . $summary . ':B' . $summary)->applyFromArray($styleTitleGeneral);
						}
					});
					
					break;
			}
		}

		if (!$sessionManager->get('facturacionElectronica')) {
			echo '<div class="alert alert-danger"><h4><i class="icon fa fa-ban"></i> Prohibido!</h4>No tiene autorización para realizar operaciones electrónicas con la SUNAT.</div>';
			exit;
		}

		if (!(strpos($sessionManager->get('rol'), 'Súper usuario') !== false || (((strpos($sessionManager->get('rol'), 'Administrador') !== false || strpos($sessionManager->get('rol'), 'Ventas') !== false)) && strpos($sessionManager->get('rol'), 'Reporteador') !== false))) {
			echo '<div class="alert alert-danger"><h4><i class="icon fa fa-ban"></i> Prohibido!</h4>No tiene autorización para realizar esta operación o su "sesión de usuario" ya ha finalizado.</div>';
			exit;
		}

		$listTOficina = TOficina::whereRaw('codigoEmpresa = ?', [$sessionManager->get('codigoEmpresa')])->get();
		$listaTCategoriaVenta = TCategoriaVenta::with(['tcategoriaventachild.tcategoriaventachild'])->whereRaw('codigoEmpresa=? and estado=? and codigoCategoriaVentaPadre is null', [$sessionManager->get('codigoEmpresa'), true])->get();

		return View("reporte/notacredito", ['listTOficina' => $listTOficina]);
	}

	public function actionNotaDebito(Request $request, SessionManager $sessionManager, Application $application)
	{
		if ($request->has('reporte')) {
			if (!$sessionManager->get('facturacionElectronica')) {
				return $this->plataformHelper->redirectError('No tiene autorización para realizar operaciones electrónicas con la SUNAT.', '/');
			}

			if (!(strpos($sessionManager->get('rol'), 'Súper usuario') !== false || (((strpos($sessionManager->get('rol'), 'Administrador') !== false || strpos($sessionManager->get('rol'), 'Ventas') !== false)) && strpos($sessionManager->get('rol'), 'Reporteador') !== false))) {
				return $this->plataformHelper->redirectError('No tiene permisos para generar este reporte.', 'reporte/index');
			}

			$fechaInicial = $request->input('fechaInicial');
			$fechaFinal = $request->input('fechaFinal');
			$codigoOficina = $request->input('codOficina') == null ? '' : $request->input('codOficina');
			$codigoPersonal = $request->input('codPersonal') == null ? '' : $request->input('codPersonal');
			$tOficina = TOficina::find($codigoOficina);
			$estadoSunat = $request->input('estadoSunat') == 'no-rechazados' ? " and estadoEnvioSunat <> 'Rechazado'" : " and estadoEnvioSunat = 'Rechazado'";

			$listaTReciboVentaNotaDebito = TReciboVentaNotaDebito::with(['treciboventa'])
				->whereRaw(
					'fechaComprobanteEmitido >= ? and fechaComprobanteEmitido <= ? ' . ($codigoPersonal != '' ? 'and codigoPersonal=?' : ($codigoOficina != '' ? 'and codigoOficina=?' : 'and codigoOficina in (select codigoOficina from toficina where codigoEmpresa=?)')) . $estadoSunat,
					[
						$request->input('fechaInicial'), $request->input('fechaFinal'),
						($codigoPersonal != '' ? $codigoPersonal : ($codigoOficina != '' ? $codigoOficina : $sessionManager->get('codigoEmpresa')))
					]
				)
				->orderBy('fechaComprobanteEmitido', 'desc')
				->get();

			switch (strtolower(str_replace(' ', '', $request->input('reporte')))) {
				case "exportaraexcel":
					if ($listaTReciboVentaNotaDebito->count() < 1) {
						$request->flash();

						return $this->plataformHelper->redirectError('No se puede exportar, no se encontraron registros con el filtro actual!.', '/reporte/index');
					}

					$data = [];
					$styleRows = (object) [
						"generalTitle" => [],
						"summary" => [],
					];
					$headers = [];
					$fileName = 'reporteNotasDeDebito-' . ($fechaInicial != '' ? ('-' . str_replace('/', '', $fechaInicial) . '-' . str_replace('/', '', $fechaFinal)) : '') . '-Oficina[' . ($tOficina !== null ? $tOficina->descripcion : 'Todas') . '].xlsx';

					$data[] = [
						'N° recibo',
						'Cod. Motivo',
						'Motivo',
						'Recibo Perteneciente',
						'Fecha emitido',
						'Divisa',
						'ISC',
						'IGV',
						'SubTotal',
						'Total'
					];

					$styleRows->generalTitle[] = count($data);

					foreach ($listaTReciboVentaNotaDebito as $key => $nc) {
						$data[] = [
							$nc->numeroRecibo,
							$nc->codigoMotivo,
							$nc->descripcionMotivo,
							$nc->treciboventa->numeroRecibo,
							$nc->fechaComprobanteEmitido,
							$nc->treciboventa->divisa == 'Soles' ? 'PEN' : 'USD',
							($nc->treciboventa->divisa == 'Soles' ? 'S/' : 'US$') . $nc->isc,
							($nc->treciboventa->divisa == 'Soles' ? 'S/' : 'US$') . $nc->igv,
							($nc->treciboventa->divisa == 'Soles' ? 'S/' : 'US$') . $nc->subTotal,
							($nc->treciboventa->divisa == 'Soles' ? 'S/' : 'US$') . $nc->total
						];
					}

					$data[] = [''];
					$data[] = ['RESUMEN', ''];
					$styleRows->summary[] = count($data);
					$data[] = ['ISC', 'S/' . (number_format($listaTReciboVentaNotaDebito->map(function($item, $key){
						return $item->isc * ($item->treciboventa->divisa == 'Soles' ? 1 : $item->treciboventa->tipoCambioUsd);
					})->sum(), 2, '.', ''))];
					$data[] = ['IGV', 'S/' . (number_format($listaTReciboVentaNotaDebito->map(function($item, $key){
						return $item->igv * ($item->treciboventa->divisa == 'Soles' ? 1 : $item->treciboventa->tipoCambioUsd);
					})->sum(), 2, '.', ''))];
					$data[] = ['SUBTOTAL', 'S/' . (number_format($listaTReciboVentaNotaDebito->map(function($item, $key){
						return $item->subTotal * ($item->treciboventa->divisa == 'Soles' ? 1 : $item->treciboventa->tipoCambioUsd);
					})->sum(), 2, '.', ''))];
					$data[] = ['TOTAL', 'S/' . (number_format($listaTReciboVentaNotaDebito->map(function($item, $key){
						return $item->total * ($item->treciboventa->divisa == 'Soles' ? 1 : $item->treciboventa->tipoCambioUsd);
					})->sum(), 2, '.', ''))];

					return new ReportGeneratorExport($headers, $data, $fileName, function(AfterSheet $event) use ($styleRows) {
						$styleTitleGeneral = [
							'font' => [
								'bold' => true,
								'color' => [
									'argb' => 'FFFFFF',
								]
							],
							'fill' => [
								'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
								'color' => [
									'argb' => '141618',
								]
							],
						];

						foreach($styleRows->generalTitle as $generalTitle)
						{
							$event->sheet->getDelegate()->getStyle('A' . $generalTitle . ':J' . $generalTitle)->applyFromArray($styleTitleGeneral);
						}

						foreach($styleRows->summary as $summary)
						{
							$event->sheet->getDelegate()->getStyle('A' . $summary . ':B' . $summary)->applyFromArray($styleTitleGeneral);
						}
					});

					break;
			}
		}

		if (!$sessionManager->get('facturacionElectronica')) {
			echo '<div class="alert alert-danger"><h4><i class="icon fa fa-ban"></i> Prohibido!</h4>No tiene autorización para realizar operaciones electrónicas con la SUNAT.</div>';
			exit;
		}

		if (!(strpos($sessionManager->get('rol'), 'Súper usuario') !== false || (((strpos($sessionManager->get('rol'), 'Administrador') !== false || strpos($sessionManager->get('rol'), 'Ventas') !== false)) && strpos($sessionManager->get('rol'), 'Reporteador') !== false))) {
			echo '<div class="alert alert-danger"><h4><i class="icon fa fa-ban"></i> Prohibido!</h4>No tiene autorización para realizar esta operación o su "sesión de usuario" ya ha finalizado.</div>';
			exit;
		}

		$listTOficina = TOficina::whereRaw('codigoEmpresa = ?', [$sessionManager->get('codigoEmpresa')])->get();
		$listaTCategoriaVenta = TCategoriaVenta::with(['tcategoriaventachild.tcategoriaventachild'])->whereRaw('codigoEmpresa=? and estado=? and codigoCategoriaVentaPadre is null', [$sessionManager->get('codigoEmpresa'), true])->get();

		return View("reporte/notadebito", ['listTOficina' => $listTOficina]);
	}

	public function actionCompras(Request $request, SessionManager $sessionManager, Application $application)
	{
		if ($request->has('reporte')) {
			if (!(strpos($sessionManager->get('rol'), 'Súper usuario') !== false || (((strpos($sessionManager->get('rol'), 'Administrador') !== false || strpos($sessionManager->get('rol'), 'Almacenero') !== false)) && strpos($sessionManager->get('rol'), 'Reporteador') !== false))) {
				return $this->plataformHelper->redirectError('No tiene permisos para generar este reporte.', 'reporte/index');
			}

			$tipoRecibo = $request->input('tipoComprobante');
			$fechaInicial = $request->input('fechaInicial');
			$fechaFinal = $request->input('fechaFinal');
			$codigoAlmacen = $request->input('codAlmacen') == null ? '' : $request->input('codAlmacen');
			$tAlmacen = TAlmacen::find($codigoAlmacen);
			$tipoComprobante = $request->input('tipoComprobante') == "Indistinto" ? "" : $request->input('tipoComprobante');

			$listaTReciboCompra = TReciboCompra::with('trecibocompradetalle')->where('tipoRecibo', 'like', '%' . $tipoComprobante . '%')->Where('fechaComprobanteEmitido', '>=', $request->input('fechaInicial'))->where('fechaComprobanteEmitido', '<=', $request->input('fechaFinal'))->whereRaw('codigoAlmacen=?', [$codigoAlmacen])->orderBy('fechaComprobanteEmitido', 'desc')->get();

			switch (strtolower(str_replace(' ', '', $request->input('reporte')))) {
				case "exportaraexcel":
					if ($listaTReciboCompra->count() < 1) {
						$request->flash();

						return $this->plataformHelper->redirectError('No se puede exportar, no se encontraron registros con el filtro actual!.', '/reporte/index');
					}

					$data = [];
					$styleRows = (object) [
						"merge" => [],
						"purchase" => [],
						"generalTitle" => [],
						"generalSubtitle" => [],
						"summaryTitle" => [],
						"separator" => [],
					];
					$headers = [];
					$fileName = 'reporteCompras-' . $tipoRecibo . ($fechaInicial != '' ? ('-' . str_replace('/', '', $fechaInicial) . '-' . str_replace('/', '', $fechaFinal)) : '') . '-Almacen[' . ($tAlmacen !== null ? $tAlmacen->descripcion : 'Todas') . '].xlsx';

					foreach ($listaTReciboCompra as $key => $compra) {
						$data[] = ['COMPRA #' . ($key + 1)];

						$styleRows->merge[] = count($data);
						$styleRows->purchase[] = count($data);

						$data[] = [
							'N° recibo',
							'Guía de remisión',
							'Proveedor',
							'Personal',
							'Tipo recibo',
							'Tipo pago',
							'Fecha emitido',
							'Estado.'
						];

						$styleRows->generalTitle[] = count($data);

						$data[] = [							
							$compra->numeroRecibo,
							$compra->numeroGuiaRemision,
							$compra->tproveedor->documentoIdentidad . ' - ' . $compra->tproveedor->nombre,
							$compra->documentoCliente . ' - ' . $compra->nombreCompletoCliente,
							$compra->tipoRecibo . ' (' . $compra->numeroRecibo . ')',
							$compra->tipoPago . ($compra->tipoPago == "Al crédito" ? (' (' . $compra->letras . ' letras)') : ''),
							$compra->fechaComprobanteEmitido,
							$compra->estado ? 'Conforme' : 'Anulado'
						];

						$data[] = [
							'Producto',
							'Tipo',
							'Impuesto',
							'Cantidad',
							'Impuesto aplicado',
							'Precio compra',
							'Compra total'
						];

						$styleRows->generalSubtitle[] = count($data);

						foreach ($compra->trecibocompradetalle as $key => $detalleCompra) {							

							$data[] = [
								'(' . $detalleCompra->codigoBarrasProducto . ') ' . $detalleCompra->nombreProducto,
								$detalleCompra->tipoProducto,
								$detalleCompra->tipoImpuestoProducto . ' ' . $detalleCompra->porcentajeTributacionProducto . '%',
								$detalleCompra->cantidadProducto,
								$detalleCompra->impuestoAplicadoProducto,
								'S/' . $detalleCompra->precioCompraUnitarioProducto,
								'S/' . $detalleCompra->precioCompraTotalProducto
							];
						}

						$countCell = 8;
						$data[] = array_pad(['IMPUESTO APLICADO', 'S/' . $compra->impuestoAplicado], -$countCell+1, ' ');
						$data[] = array_pad(['SUBTOTAL', 'S/' . $compra->subTotal], -$countCell+1, ' ');
						$data[] = array_pad(['TOTAL', 'S/' . $compra->total], -$countCell+1, ' ');

						$data[] = [' '];
						$styleRows->separator[] = count($data);
						$data[] = [' '];
					}

					$data[] = ['RESUMEN', ''];
					$styleRows->summaryTitle[] = count($data);
					$data[] = ['IMPUESTO APLICADO', 'S/' . (number_format($listaTReciboCompra->sum('impuestoAplicado'), 2, '.', ''))];
					$data[] = ['SUBTOTAL', 'S/' . (number_format($listaTReciboCompra->sum('subTotal'), 2, '.', ''))];
					$data[] = ['TOTAL', 'S/' . (number_format($listaTReciboCompra->sum('total'), 2, '.', ''))];
					$data[] = [' '];

					return new ReportGeneratorExport($headers, $data, $fileName, function(AfterSheet $event) use ($styleRows) {
						$styleMerge = [
							'font' => [
								'bold' => true,
								'color' => [
									'argb' => 'FFFFFF',
								]
							],
							'alignment' => [
								'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
							],
							'borders' => [
								'allBorders' => [
									'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
								],
							]
						];

						$stylePurchase = [
							'fill' => [
								'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
								'color' => [
									'argb' => '141618',
								]
							],
						];

						$styleTitleGeneral = [
							'font' => [
								'bold' => true,
								'color' => [
									'argb' => 'FFFFFF',
								]
							],
							'fill' => [
								'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
								'color' => [
									'argb' => '282D31',
								]
							],
						];

						$styleSubtitleGeneral = [
							'font' => [
								'bold' => true,
								'color' => [
									'argb' => 'FFFFFF',
								]
							],
							'fill' => [
								'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
								'color' => [
									'argb' => '53575A',
								]
							],
						];

						$styleSummary = [
							'font' => [
								'bold' => true,
								'color' => [
									'argb' => 'FFFFFF',
								]
							],
							'fill' => [
								'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
								'color' => [
									'argb' => '141618',
								]
							],
						];

						$styleSeparator = [
							'fill' => [
								'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
								'color' => [
									'argb' => 'FBA905',
								]
							],
						];

						foreach($styleRows->merge as $merge)
						{
							$event->sheet->getDelegate()->mergeCells('A' . $merge . ':H' . $merge)->getStyle('A' . $merge . ':H' . $merge)->applyFromArray($styleMerge);
						}

						foreach($styleRows->purchase as $purchase)
						{
							$event->sheet->getDelegate()->getStyle('A' . $purchase . ':H' . $purchase)->applyFromArray($stylePurchase);
						}
						
						foreach($styleRows->generalTitle as $generalTitle)
						{
							$event->sheet->getDelegate()->getStyle('A' . $generalTitle . ':H' . $generalTitle)->applyFromArray($styleTitleGeneral);
						}

						foreach($styleRows->generalSubtitle as $generalSubtitle)
						{
							$event->sheet->getDelegate()->getStyle('A' . $generalSubtitle . ':H' . $generalSubtitle)->applyFromArray($styleSubtitleGeneral);
						}

						foreach($styleRows->summaryTitle as $summaryTitle)
						{
							$event->sheet->getDelegate()->getStyle('A' . $summaryTitle . ':B' . $summaryTitle)->applyFromArray($styleSummary);
						}

						foreach($styleRows->separator as $separator)
						{
							$event->sheet->getDelegate()->getStyle('A' . $separator . ':H' . $separator)->applyFromArray($styleSeparator);
						}
					});
					break;
			}
		}

		if (!(strpos($sessionManager->get('rol'), 'Súper usuario') !== false || (((strpos($sessionManager->get('rol'), 'Administrador') !== false || strpos($sessionManager->get('rol'), 'Almacenero') !== false)) && strpos($sessionManager->get('rol'), 'Reporteador') !== false))) {
			echo '<div class="alert alert-danger"><h4><i class="icon fa fa-ban"></i> Prohibido!</h4>No tiene autorización para realizar esta operación o su "sesión de usuario" ya ha finalizado.</div>';
			exit;
		}

		$listTAlmacen = TAlmacen::whereRaw('codigoEmpresa = ?', [$sessionManager->get('codigoEmpresa')])->get();

		return View("reporte/compras", ['listTAlmacen' => $listTAlmacen]);
	}

	public function actionComprasConsolidado(Request $request, SessionManager $sessionManager, Application $application)
	{
		if ($request->has('reporte')) {
			if (!(strpos($sessionManager->get('rol'), 'Súper usuario') !== false || (((strpos($sessionManager->get('rol'), 'Administrador') !== false || strpos($sessionManager->get('rol'), 'Almacenero') !== false)) && strpos($sessionManager->get('rol'), 'Reporteador') !== false))) {
				return $this->plataformHelper->redirectError('No tiene permisos para generar este reporte.', 'reporte/index');
			}

			$tipoRecibo = $request->input('tipoComprobante');
			$fechaInicial = $request->input('fechaInicial');
			$fechaFinal = $request->input('fechaFinal');
			$codigoAlmacen = $request->input('codAlmacen') == null ? '' : $request->input('codAlmacen');
			$tAlmacen = TAlmacen::find($codigoAlmacen);
			$tipoComprobante = $request->input('tipoComprobante') == "Indistinto" ? "" : $request->input('tipoComprobante');

			$listaTReciboCompra = TReciboCompra::where('tipoRecibo', 'like', '%' . $tipoComprobante . '%')->Where('fechaComprobanteEmitido', '>=', $request->input('fechaInicial'))->where('fechaComprobanteEmitido', '<=', $request->input('fechaFinal'))->whereRaw('codigoAlmacen=?', [$codigoAlmacen])->orderBy('fechaComprobanteEmitido', 'desc')->get();

			switch (strtolower(str_replace(' ', '', $request->input('reporte')))) {
				case "exportaraexcel":
					if ($listaTReciboCompra->count() < 1) {
						$request->flash();

						return $this->plataformHelper->redirectError('No se puede exportar, no se encontraron registros con el filtro actual!.', '/reporte/index');
					}

					$data = [];
					$styleRows = (object) [
						"generalTitle" => [],
						"summary" => [],
					];
					$headers = [];
					$fileName = 'reporteComprasConsolidado-' . $tipoRecibo . ($fechaInicial != '' ? ('-' . str_replace('/', '', $fechaInicial) . '-' . str_replace('/', '', $fechaFinal)) : '') . '-Almacen[' . ($tAlmacen !== null ? $tAlmacen->descripcion : 'Todas') . '].xlsx';

					$data[] = [
						'N° recibo',
						'Guía de remisión',
						'Proveedor',
						'Personal',
						'Tipo recibo',
						'Tipo pago',
						'Impuesto aplicado',
						'Subtotal',
						'Total',
						'Fecha emitido',
						'Estado.'
					];

					$styleRows->generalTitle[] = count($data);

					foreach ($listaTReciboCompra as $key => $compra) {
						$data[] = [
							$compra->numeroRecibo,
							$compra->numeroGuiaRemision,
							$compra->tproveedor->documentoIdentidad . ' - ' . $compra->tproveedor->nombre,
							$compra->documentoCliente . ' - ' . $compra->nombreCompletoCliente,
							$compra->tipoRecibo . ' (' . $compra->numeroRecibo . ')',
							$compra->tipoPago . ($compra->tipoPago == "Al crédito" ? (' (' . $compra->letras . ' letras)') : ''),
							'S/' . $compra->impuestoAplicado,
							'S/' . $compra->subTotal,
							'S/' . $compra->total,
							$compra->fechaComprobanteEmitido,
							$compra->estado ? 'Conforme' : 'Anulado'
						];
					}

					$data[] = [''];
					$data[] = ['RESUMEN', ''];
					$styleRows->summary[] = count($data);
					$data[] = ['IMPUESTO APLICADO', 'S/' . (number_format($listaTReciboCompra->sum('impuestoAplicado'), 2, '.', ''))];
					$data[] = ['SUBTOTAL', 'S/' . (number_format($listaTReciboCompra->sum('subTotal'), 2, '.', ''))];
					$data[] = ['TOTAL', 'S/' . (number_format($listaTReciboCompra->sum('total'), 2, '.', ''))];
					$data[] = [' '];

					return new ReportGeneratorExport($headers, $data, $fileName, function(AfterSheet $event) use ($styleRows) {
						$styleTitleGeneral = [
							'font' => [
								'bold' => true,
								'color' => [
									'argb' => 'FFFFFF',
								]
							],
							'fill' => [
								'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
								'color' => [
									'argb' => '141618',
								]
							],
						];

						foreach($styleRows->generalTitle as $generalTitle)
						{
							$event->sheet->getDelegate()->getStyle('A' . $generalTitle . ':K' . $generalTitle)->applyFromArray($styleTitleGeneral);
						}

						foreach($styleRows->summary as $summary)
						{
							$event->sheet->getDelegate()->getStyle('A' . $summary . ':B' . $summary)->applyFromArray($styleTitleGeneral);
						}
					});
					
					break;
			}
		}

		if (!(strpos($sessionManager->get('rol'), 'Súper usuario') !== false || (((strpos($sessionManager->get('rol'), 'Administrador') !== false || strpos($sessionManager->get('rol'), 'Almacenero') !== false)) && strpos($sessionManager->get('rol'), 'Reporteador') !== false))) {
			echo '<div class="alert alert-danger"><h4><i class="icon fa fa-ban"></i> Prohibido!</h4>No tiene autorización para realizar esta operación o su "sesión de usuario" ya ha finalizado.</div>';
			exit;
		}

		$listTAlmacen = TAlmacen::whereRaw('codigoEmpresa = ?', [$sessionManager->get('codigoEmpresa')])->get();

		return View("reporte/comprasconsolidado", ['listTAlmacen' => $listTAlmacen]);
	}

	public function actionCaja(Request $request, SessionManager $sessionManager, Application $application)
	{
		if ($request->has('reporte')) {
			if (!(strpos($sessionManager->get('rol'), 'Súper usuario') !== false || (((strpos($sessionManager->get('rol'), 'Administrador') !== false)) && strpos($sessionManager->get('rol'), 'Reporteador') !== false))) {
				return $this->plataformHelper->redirectError('No tiene permisos para generar este reporte.', 'reporte/index');
			}

			$fechaInicial = $request->input('fechaInicial');
			$fechaFinal = $request->input('fechaFinal');
			$listaTCaja = TCaja::with('tcajadetalle')->Where('created_at', '>=', $request->input('fechaInicial'))->where('created_at', '<=', $request->input('fechaFinal'))->whereRaw('codigoEmpresa=?', [$sessionManager->get('codigoEmpresa')])->orderBy('created_at', 'desc')->get();

			switch (strtolower(str_replace(' ', '', $request->input('reporte')))) {
				case "exportaraexcel":
					if ($listaTCaja->count() < 1) {
						$request->flash();

						return $this->plataformHelper->redirectError('No se puede exportar, no se encontraron registros con el filtro actual!.', '/reporte/index');
					}

					$data = [];
					$styleRows = (object) [
						"merge" => [],
						"box" => [],
						"generalTitle" => [],
						"generalSubtitle" => [],
						"summaryTitle" => [],
						"separator" => [],
						"emphasis" => [],
						"subEmphasis" => [],
					];
					$headers = [];
					$fileName = 'reporteCaja-' . ($fechaInicial != '' ? ('-' . str_replace('/', '', $fechaInicial) . '-' . str_replace('/', '', $fechaFinal)) : '') . '.xlsx';

					foreach ($listaTCaja as $key => $caja) {
						$data[] = ['CAJA #' . ($key + 1)];

						$styleRows->merge[] = count($data);
						$styleRows->box[] = count($data);

						$data[] = [
							'Fecha apertura',
							'', '', '', '',
						];

						$styleRows->generalTitle[] = count($data);

						$data[] = [							
							$caja->created_at
						];

						$styleRows->emphasis[] = count($data);

						$data[] = [
							'Personal',
							'Egresos',
							'Ingresos',
							'Saldo final',
							'Estado'
						];

						$styleRows->generalSubtitle[] = count($data);

						foreach ($caja->tcajadetalle as $key => $detalleCaja) {
							
							if (strpos($sessionManager->get('rol'), 'Súper usuario') === false && $detalleCaja->tpersonal->cargo == "Súper usuario") {
								continue;
							}

							if ($detalleCaja->egresos == 0 && $detalleCaja->ingresos == 0) {
								continue;
							}

							$data[] = [
								'(' . $detalleCaja->tpersonal->dni . ') ' . $detalleCaja->tPersonal->nombre . ' ' . $detalleCaja->tpersonal->apellido,
								'S/' . $detalleCaja->egresos,
								'S/' . $detalleCaja->ingresos,
								'S/' . $detalleCaja->saldoFinal,
								$detalleCaja->cerrado ? 'Cerrado' : 'Abierto'
							];
						}

						$countCell = 5;
						$data[] = array_pad([
							'S/' . number_format($caja->tcajadetalle->sum('egresos'), 2, '.', ''),
							'S/' . number_format($caja->tcajadetalle->sum('ingresos'), 2, '.', ''),
							'S/' . number_format($caja->tcajadetalle->sum('saldoFinal'), 2, '.', '')
						], -$countCell+1, ' ');

						$styleRows->subEmphasis[] = count($data);
						$data[] = [' '];
						$data[] = [''];
						$styleRows->separator[] = count($data);
					}

					$data[] = [''];
					$data[] = ['RESUMEN', ''];
					$styleRows->summaryTitle[] = count($data);
					$data[] = ['EGRESOS', 'S/' . (number_format($listaTCaja->sum(function ($caja) {
						return $caja->tcajadetalle->sum('egresos');
					}), 2, '.', ''))];
					$data[] = ['INGRESOS', 'S/' . (number_format($listaTCaja->sum(function ($caja) {
						return $caja->tcajadetalle->sum('ingresos');
					}), 2, '.', ''))];
					$data[] = ['SALDO FINAL', 'S/' . (number_format($listaTCaja->sum(function ($caja) {
						return $caja->tcajadetalle->sum('saldoFinal');
					}), 2, '.', ''))];

					return new ReportGeneratorExport($headers, $data, $fileName, function(AfterSheet $event) use ($styleRows) {
						$styleMerge = [
							'font' => [
								'bold' => true,
								'color' => [
									'argb' => 'FFFFFF',
								]
							],
							'alignment' => [
								'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
							],
							'borders' => [
								'allBorders' => [
									'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
								],
							]
						];

						$styleBox = [
							'fill' => [
								'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
								'color' => [
									'argb' => '141618',
								]
							],
						];

						$styleTitleGeneral = [
							'font' => [
								'bold' => true,
								'color' => [
									'argb' => 'FFFFFF',
								]
							],
							'fill' => [
								'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
								'color' => [
									'argb' => '282D31',
								]
							],
						];

						$styleSubtitleGeneral = [
							'font' => [
								'bold' => true,
								'color' => [
									'argb' => 'FFFFFF',
								]
							],
							'fill' => [
								'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
								'color' => [
									'argb' => '53575A',
								]
							],
						];

						$styleSummary = [
							'font' => [
								'bold' => true,
								'color' => [
									'argb' => 'FFFFFF',
								]
							],
							'fill' => [
								'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
								'color' => [
									'argb' => '141618',
								]
							],
						];


						$styleSeparator = [
							'fill' => [
								'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
								'color' => [
									'argb' => 'FBA905',
								]
							],
						];

						foreach($styleRows->merge as $merge)
						{
							$event->sheet->getDelegate()->mergeCells('A' . $merge . ':E' . $merge)->getStyle('A' . $merge . ':E' . $merge)->applyFromArray($styleMerge);
						}

						foreach($styleRows->box as $box)
						{
							$event->sheet->getDelegate()->getStyle('A' . $box . ':E' . $box)->applyFromArray($styleBox);
						}
						
						foreach($styleRows->generalTitle as $generalTitle)
						{
							$event->sheet->getDelegate()->getStyle('A' . $generalTitle . ':E' . $generalTitle)->applyFromArray($styleTitleGeneral);
						}

						foreach($styleRows->generalSubtitle as $generalSubtitle)
						{
							$event->sheet->getDelegate()->getStyle('A' . $generalSubtitle . ':E' . $generalSubtitle)->applyFromArray($styleSubtitleGeneral);
						}

						foreach($styleRows->separator as $separator)
						{
							$event->sheet->getDelegate()->getStyle('A' . $separator . ':E' . $separator)->applyFromArray($styleSeparator);
						}

						foreach($styleRows->emphasis as $emphasis)
						{
							$event->sheet->getDelegate()->getStyle('A' . $emphasis . ':A' . $emphasis)->applyFromArray($styleSeparator);
						}

						foreach($styleRows->subEmphasis as $subEmphasis)
						{
							$event->sheet->getDelegate()->getStyle('B' . $subEmphasis . ':D' . $subEmphasis)->applyFromArray($styleSeparator);
						}

						foreach($styleRows->summaryTitle as $summaryTitle)
						{
							$event->sheet->getDelegate()->getStyle('A' . $summaryTitle . ':B' . $summaryTitle)->applyFromArray($styleSummary);
						}
					});
					
					break;
			}
		}

		if (!(strpos($sessionManager->get('rol'), 'Súper usuario') !== false || (((strpos($sessionManager->get('rol'), 'Administrador') !== false)) && strpos($sessionManager->get('rol'), 'Reporteador') !== false))) {
			echo '<div class="alert alert-danger"><h4><i class="icon fa fa-ban"></i> Prohibido!</h4>No tiene autorización para realizar esta operación o su "sesión de usuario" ya ha finalizado.</div>';
			exit;
		}

		return View("reporte/caja");
	}

	public function actionProductosAlmacen(Request $request, SessionManager $sessionManager, Application $application)
	{
		if ($request->has('reporte')) {
			if (!(strpos($sessionManager->get('rol'), 'Súper usuario') !== false || (((strpos($sessionManager->get('rol'), 'Administrador') !== false)) && strpos($sessionManager->get('rol'), 'Reporteador') !== false))) {
				return $this->plataformHelper->redirectError('No tiene permisos para generar este reporte.', 'reporte/index');
			}

			$codigoAlmacen = $request->input('codAlmacen') == null ? '' : $request->input('codAlmacen');
			$tAlmacen = TAlmacen::find($codigoAlmacen);
			$filtro = $request->input('filtroProductos');
			$listaTAlmacenProducto = null;

			if ($filtro == '=0') {
				$listaTAlmacenProducto = TAlmacenProducto::with('tpresentacion', 'tunidadmedida')->whereRaw('cantidad = 0 and codigoAlmacen=?', [$codigoAlmacen])->orderBy('nombre')->get();
			} elseif ($filtro == '!=0') {
				$listaTAlmacenProducto = TAlmacenProducto::with('tpresentacion', 'tunidadmedida')->whereRaw('cantidad != 0 and codigoAlmacen=?', [$codigoAlmacen])->orderBy('nombre')->get();
			} else {
				if (TAlmacenProducto::with('tpresentacion', 'tunidadmedida')->whereRaw('codigoAlmacen=?', [$codigoAlmacen])->count() > 15000) {
					return $this->plataformHelper->redirectError('No se puede generar el reporte, la cantidad de registro excede el limite permitido.', '/reporte/index');
				}

				$listaTAlmacenProducto = TAlmacenProducto::with('tpresentacion', 'tunidadmedida')->whereRaw('codigoAlmacen=?', [$codigoAlmacen])->orderBy('nombre')->get();
			}

			switch (strtolower(str_replace(' ', '', $request->input('reporte')))) {
				case "exportaraexcel":
					if ($listaTAlmacenProducto->count() < 1) {
						$request->flash();

						return $this->plataformHelper->redirectError('No se puede exportar, no se encontraron registros con el filtro actual!.', '/reporte/index');
					}

					$data = [];
					$styleRows = (object) [
						"generalTitle" => [],
						"summary" => [],
					];
					$headers = [];
					$fileName = 'reporteProductosAlmacen-[' . ($tAlmacen !== null ? $tAlmacen->descripcion : 'Todas') . '].xlsx';

					$data[] = [
						'Codigo Barras',
						'Producto',
						'Presentación',
						'Unidad medida',
						'Tipo',
						'Cantidad',
						'Situación I.',
						'Impuesto',
						'Cantidad alerta',
						'Venta menor unidad',
						'U. bloque',
						'Precio Compra U.',
						'Precio Venta U.',
						'Fecha Vencimiento'
					];

					$styleRows->generalTitle[] = count($data);

					foreach ($listaTAlmacenProducto as $key => $producto) {
						$data[] = [
							$producto->codigoBarras != "" ? " " . $producto->codigoBarras : "-",
							$producto->nombre,
							$producto->tPresentacion->nombre,
							$producto->tUnidadMedida->nombre,
							$producto->tipo,
							$producto->cantidad,
							$producto->situacionImpuesto,
							$producto->tipoImpuesto . ' ' . $producto->porcentajeTributacion . '%',
							$producto->cantidadMinimaAlertaStock,
							$producto->ventaMenorUnidad ? 'Si' : 'No',
							$producto->unidadesBloque . ' (' . $producto->unidadMedidaBloque . ')',
							'S/' . $producto->precioCompraUnitario,
							'S/' . $producto->precioVentaUnitario,
							$producto->fechaVencimiento
						];
					}

					$countCell = 14;
					$data[] = array_pad([
						'S/' . (number_format($listaTAlmacenProducto->sum('precioCompraUnitario'), 2, '.', '')), 
						'S/' . (number_format($listaTAlmacenProducto->sum('precioVentaUnitario'), 2, '.', ''))
					], -$countCell+1, ' ');

					$styleRows->summary [] = count($data);

					return new ReportGeneratorExport($headers, $data, $fileName, function(AfterSheet $event) use ($styleRows) {
						$styleTitleGeneral = [
							'font' => [
								'bold' => true,
								'color' => [
									'argb' => 'FFFFFF',
								]
							],
							'fill' => [
								'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
								'color' => [
									'argb' => '141618',
								]
							],
						];

						$styleSummary = [
							'fill' => [
								'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
								'color' => [
									'argb' => 'FBA905',
								]
							],
						];

						foreach($styleRows->generalTitle as $generalTitle)
						{
							$event->sheet->getDelegate()->getStyle('A' . $generalTitle . ':N' . $generalTitle)->applyFromArray($styleTitleGeneral);
						}

						foreach($styleRows->summary as $summary)
						{
							$event->sheet->getDelegate()->getStyle('L' . $summary . ':M' . $summary)->applyFromArray($styleSummary);
						}
					});					
					break;
			}
		}

		if (!(strpos($sessionManager->get('rol'), 'Súper usuario') !== false || (((strpos($sessionManager->get('rol'), 'Administrador') !== false)) && strpos($sessionManager->get('rol'), 'Reporteador') !== false))) {
			echo '<div class="alert alert-danger"><h4><i class="icon fa fa-ban"></i> Prohibido!</h4>No tiene autorización para realizar esta operación o su "sesión de usuario" ya ha finalizado.</div>';
			exit;
		}

		$listTAlmacen = TAlmacen::whereRaw('codigoEmpresa = ?', [$sessionManager->get('codigoEmpresa')])->get();

		return View("reporte/productosalmacen", ['listTAlmacen' => $listTAlmacen]);
	}

	public function actionProductosOficina(Request $request, SessionManager $sessionManager, Application $application)
	{
		if ($request->has('reporte')) {
			if (!(strpos($sessionManager->get('rol'), 'Súper usuario') !== false || (((strpos($sessionManager->get('rol'), 'Administrador') !== false)) && strpos($sessionManager->get('rol'), 'Reporteador') !== false))) {
				return $this->plataformHelper->redirectError('No tiene permisos para generar este reporte.', 'reporte/index');
			}

			$codigoOficina = $request->input('codOficina') == null ? '' : $request->input('codOficina');
			$tOficina = TOficina::find($codigoOficina);
			$filtro = $request->input('filtroProductos');
			$listaTOficinaProducto = null;

			if ($filtro == '=0') {
				$listaTOficinaProducto = TOficinaProducto::whereRaw('cantidad = 0 and codigoOficina=?', [$codigoOficina])->orderBy('nombre')->get();
			} elseif ($filtro == '!=0') {
				$listaTOficinaProducto = TOficinaProducto::whereRaw('cantidad != 0 and codigoOficina=?', [$codigoOficina])->orderBy('nombre')->get();
			} else {
				if ($listaTOficinaProducto = TOficinaProducto::whereRaw('codigoOficina=?', [$codigoOficina])->count() > 15000) {
					return $this->plataformHelper->redirectError('No se puede generar el reporte, la cantidad de registro excede el limite permitido.', '/reporte/index');
				}

				$listaTOficinaProducto = TOficinaProducto::whereRaw('codigoOficina=?', [$codigoOficina])->orderBy('nombre')->get();
			}

			switch (strtolower(str_replace(' ', '', $request->input('reporte')))) {
				case "exportaraexcel":
					if ($listaTOficinaProducto->count() < 1) {
						$request->flash();

						return $this->plataformHelper->redirectError('No se puede exportar, no se encontraron registros con el filtro actual!.', '/reporte/index');
					}

					$data = [];
					$styleRows = (object) [
						"generalTitle" => [],
						"summary" => [],
					];
					$headers = [];
					$fileName = 'reporteProductosOficina-[' . ($tOficina !== null ? $tOficina->descripcion : 'Todas') . '].xlsx';

					$data[] = [
						'Codigo Barras',
						'Producto',
						'Presentación',
						'Unidad medida',
						'Tipo',
						'Cantidad',
						'Situación I.',
						'Impuesto',
						'Cantidad alerta',
						'Venta menor unidad',
						'U. bloque',
						'Precio Compra U.',
						'Precio Venta U.',
						'Fecha Vencimiento'
					];

					$styleRows->generalTitle[] = count($data);

					foreach ($listaTOficinaProducto as $key => $producto) {
						$data[] = [
							$producto->codigoBarras != "" ? " " . $producto->codigoBarras : "-",
							$producto->nombre,
							$producto->presentacion,
							$producto->unidadMedida,
							$producto->tipo,
							$producto->cantidad,
							$producto->situacionImpuesto,
							$producto->tipoImpuesto . ' ' . $producto->porcentajeTributacion . '%',
							$producto->cantidadMinimaAlertaStock,
							$producto->ventaMenorUnidad ? 'Si' : 'No',
							$producto->unidadesBloque . ' (' . $producto->unidadMedidaBloque . ')',
							'S/' . $producto->precioCompraUnitario,
							'S/' . $producto->precioVentaUnitario,
							$producto->fechaVencimiento
						];
					}

					$countCell = 14;
					$data[] = array_pad([
						'S/' . (number_format($listaTOficinaProducto->sum('precioCompraUnitario'), 2, '.', '')), 
						'S/' . (number_format($listaTOficinaProducto->sum('precioVentaUnitario'), 2, '.', ''))
					], -$countCell+1, ' ');

					$styleRows->summary [] = count($data);

					return new ReportGeneratorExport($headers, $data, $fileName, function(AfterSheet $event) use ($styleRows) {
						$styleTitleGeneral = [
							'font' => [
								'bold' => true,
								'color' => [
									'argb' => 'FFFFFF',
								]
							],
							'fill' => [
								'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
								'color' => [
									'argb' => '141618',
								]
							],
						];

						$styleSummary = [
							'fill' => [
								'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
								'color' => [
									'argb' => 'FBA905',
								]
							],
						];

						foreach($styleRows->generalTitle as $generalTitle)
						{
							$event->sheet->getDelegate()->getStyle('A' . $generalTitle . ':N' . $generalTitle)->applyFromArray($styleTitleGeneral);
						}

						foreach($styleRows->summary as $summary)
						{
							$event->sheet->getDelegate()->getStyle('L' . $summary . ':M' . $summary)->applyFromArray($styleSummary);
						}
					});		

					break;
			}
		}

		if (!(strpos($sessionManager->get('rol'), 'Súper usuario') !== false || (((strpos($sessionManager->get('rol'), 'Administrador') !== false)) && strpos($sessionManager->get('rol'), 'Reporteador') !== false))) {
			echo '<div class="alert alert-danger"><h4><i class="icon fa fa-ban"></i> Prohibido!</h4>No tiene autorización para realizar esta operación o su "sesión de usuario" ya ha finalizado.</div>';
			exit;
		}

		$listTOficina = TOficina::whereRaw('codigoEmpresa = ?', [$sessionManager->get('codigoEmpresa')])->get();

		return View("reporte/productosoficina", ['listTOficina' => $listTOficina]);
	}

	public function actionProductosOficinaConsolidado(Request $request, SessionManager $sessionManager, Application $application)
	{
		if ($request->has('reporte')) {
			if (!(strpos($sessionManager->get('rol'), 'Súper usuario') !== false || (((strpos($sessionManager->get('rol'), 'Administrador') !== false)) && strpos($sessionManager->get('rol'), 'Reporteador') !== false))) {
				return $this->plataformHelper->redirectError('No tiene permisos para generar este reporte.', 'reporte/index');
			}

			$codigoOficina = $request->input('codOficina') == null ? '' : $request->input('codOficina');
			$filtro = $request->input('filtroProductos');
			$listaTOficinaProducto = null;
			$tOficina = TOficina::find($codigoOficina);

			if ($tOficina == null) {
				$listaTOficinaProducto = DB::select(
					'select sum(cantidad) as cantidad, replace(nombre, codigoBarras, "") as nombreProducto from toficinaproducto where '
						. ($filtro == '=0' ? 'cantidad=0 and' : ($filtro == '!=0' ? 'cantidad!= 0 and' : '')) .
						' codigoOficina in (select codigoOficina from toficina where codigoEmpresa=?) group by nombreProducto order by nombreProducto',
					[$sessionManager->get('codigoEmpresa')]
				);
			} else {
				$listaTOficinaProducto = DB::select(
					'select sum(cantidad) as cantidad, replace(nombre, codigoBarras, "") as nombreProducto from toficinaproducto where '
						. ($filtro == '=0' ? 'cantidad=0 and' : ($filtro == '!=0' ? 'cantidad!= 0 and' : '')) .
						' codigoOficina=? group by nombreProducto order by nombreProducto',
					[$codigoOficina]
				);
			}

			if (count($listaTOficinaProducto) > 15000) {
				return $this->plataformHelper->redirectError('No se puede generar el reporte, la cantidad de registro excede el limite permitido.', '/reporte/index');
			}

			switch (strtolower(str_replace(' ', '', $request->input('reporte')))) {
				case "exportaraexcel":
					if (count($listaTOficinaProducto) < 1) {
						$request->flash();

						return $this->plataformHelper->redirectError('No se puede exportar, no se encontraron registros con el filtro actual!.', '/reporte/index');
					}

					$data = [];
					$styleRows = (object) [
						"generalTitle" => [],
					];
					$headers = [];
					$fileName = 'reporteProductosOficinaConsolidado-[' . ($tOficina !== null ? $tOficina->descripcion : 'Todas') . '].xlsx';

					$data[] = [
						'Cantidad',
						'Producto'
					];

					$styleRows->generalTitle[] = count($data);

					foreach ($listaTOficinaProducto as $key => $producto) {
						$data[] = [
							$producto->cantidad,
							$producto->nombreProducto
						];
					}

					return new ReportGeneratorExport($headers, $data, $fileName, function(AfterSheet $event) use ($styleRows) {
						$styleTitleGeneral = [
							'font' => [
								'bold' => true,
								'color' => [
									'argb' => 'FFFFFF',
								]
							],
							'fill' => [
								'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
								'color' => [
									'argb' => '141618',
								]
							],
						];

						foreach($styleRows->generalTitle as $generalTitle)
						{
							$event->sheet->getDelegate()->getStyle('A' . $generalTitle . ':B' . $generalTitle)->applyFromArray($styleTitleGeneral);
						}
					});		
					
					break;
			}
		}

		if (!(strpos($sessionManager->get('rol'), 'Súper usuario') !== false || (((strpos($sessionManager->get('rol'), 'Administrador') !== false)) && strpos($sessionManager->get('rol'), 'Reporteador') !== false))) {
			echo '<div class="alert alert-danger"><h4><i class="icon fa fa-ban"></i> Prohibido!</h4>No tiene autorización para realizar esta operación o su "sesión de usuario" ya ha finalizado.</div>';
			exit;
		}

		$listTOficina = TOficina::whereRaw('codigoEmpresa = ?', [$sessionManager->get('codigoEmpresa')])->get();

		return View("reporte/productosoficinaconsolidado", ['listTOficina' => $listTOficina]);
	}

	public function actionProductosOficinaCompra(Request $request, SessionManager $sessionManager, Application $application)
	{
		if ($request->has('reporte')) {
			if (!(strpos($sessionManager->get('rol'), 'Súper usuario') !== false || (((strpos($sessionManager->get('rol'), 'Administrador') !== false)) && strpos($sessionManager->get('rol'), 'Reporteador') !== false))) {
				return $this->plataformHelper->redirectError('No tiene permisos para generar este reporte.', 'reporte/index');
			}

			$codigoOficina = $request->input('codOficina') == null ? '' : $request->input('codOficina');
			$filtro = $request->input('filtroProductos');
			$listaTOficinaProducto = null;
			$tOficina = TOficina::find($codigoOficina);

			if ($tOficina == null) {
				$listaTOficinaProducto = DB::select(
					'select top.codigoBarras, top.nombre, top.cantidad, top.precioCompraUnitario, top.precioVentaUnitario, (select max(trc.codigoReciboCompra) from trecibocompradetalle trc where trc.nombreProducto= top.nombre and trc.codigoBarrasProducto = top.codigoBarras) as codigoReciboCompra from toficinaproducto top where '
						. ($filtro == '=0' ? 'cantidad=0 and' : ($filtro == '!=0' ? 'cantidad!= 0 and' : '')) .
						' codigoOficina in (select codigoOficina from toficina where codigoEmpresa=?) order by top.nombre',
					[$sessionManager->get('codigoEmpresa')]
				);
			} else {
				$listaTOficinaProducto = DB::select(
					'select top.codigoBarras, top.nombre, top.cantidad, top.precioCompraUnitario, top.precioVentaUnitario, (select max(trc.codigoReciboCompra) from trecibocompradetalle trc where trc.nombreProducto= top.nombre and trc.codigoBarrasProducto = top.codigoBarras) as codigoReciboCompra from toficinaproducto top where '
						. ($filtro == '=0' ? 'cantidad=0 and' : ($filtro == '!=0' ? 'cantidad!= 0 and' : '')) .
						' codigoOficina=? order by top.nombre',
					[$codigoOficina]
				);
			}

			if (count($listaTOficinaProducto) > 15000) {
				return $this->plataformHelper->redirectError('No se puede generar el reporte, la cantidad de registro excede el limite permitido.', '/reporte/index');
			}

			switch (strtolower(str_replace(' ', '', $request->input('reporte')))) {
				case "exportaraexcel":
					if (count($listaTOficinaProducto) < 1) {
						$request->flash();

						return $this->plataformHelper->redirectError('No se puede exportar, no se encontraron registros con el filtro actual!.', '/reporte/index');
					}

					$data = [];
					$styleRows = (object) [
						"generalTitle" => [],
					];
					$headers = [];
					$fileName = 'reporteProductosOficinaInfoCompra-[' . ($tOficina !== null ? $tOficina->descripcion : 'Todas') . '].xlsx';

					$data[] = [
						'Cod. Barras',
						'Producto',
						'Cantidad',
						'Ultimo Comprobante',
						'Ultima Guía Remisión',
						'Precio Compra',
						'Precio Venta'
					];

					$styleRows->generalTitle[] = count($data);

					foreach ($listaTOficinaProducto as $key => $producto) {
						$reciboCompra = TReciboCompra::find(!empty($producto->codigoReciboCompra) ? $producto->codigoReciboCompra : '');

						$data[] = [
							$producto->codigoBarras != "" ? " " . $producto->codigoBarras : "-",
							$producto->nombre,
							$producto->cantidad,
							($reciboCompra != null ? $reciboCompra->tipoRecibo . ($reciboCompra->numeroRecibo != '' ? '-' . $reciboCompra->numeroRecibo : '') : '-'),
							($reciboCompra != null ? $reciboCompra->numeroGuiaRemision : '-'),
							$producto->precioCompraUnitario,
							$producto->precioVentaUnitario
						];
					}

					return new ReportGeneratorExport($headers, $data, $fileName, function(AfterSheet $event) use ($styleRows) {
						$styleTitleGeneral = [
							'font' => [
								'bold' => true,
								'color' => [
									'argb' => 'FFFFFF',
								]
							],
							'fill' => [
								'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
								'color' => [
									'argb' => '141618',
								]
							],
						];

						foreach($styleRows->generalTitle as $generalTitle)
						{
							$event->sheet->getDelegate()->getStyle('A' . $generalTitle . ':G' . $generalTitle)->applyFromArray($styleTitleGeneral);
						}
					});		
					
					break;
			}
		}

		if (!(strpos($sessionManager->get('rol'), 'Súper usuario') !== false || (((strpos($sessionManager->get('rol'), 'Administrador') !== false)) && strpos($sessionManager->get('rol'), 'Reporteador') !== false))) {
			echo '<div class="alert alert-danger"><h4><i class="icon fa fa-ban"></i> Prohibido!</h4>No tiene autorización para realizar esta operación o su "sesión de usuario" ya ha finalizado.</div>';
			exit;
		}

		$listTOficina = TOficina::whereRaw('codigoEmpresa = ?', [$sessionManager->get('codigoEmpresa')])->get();

		return View("reporte/productosoficinacompra", ['listTOficina' => $listTOficina]);
	}

	public function actionDocumentoGeneradoSunat(Request $request, SessionManager $sessionManager, Application $application)
	{
		if ($request->has('reporte')) {
			if (!(strpos($sessionManager->get('rol'), 'Súper usuario') !== false || (((strpos($sessionManager->get('rol'), 'Administrador') !== false)) && strpos($sessionManager->get('rol'), 'Reporteador') !== false))) {
				return $this->plataformHelper->redirectError('No tiene permisos para generar este reporte.', 'reporte/index');
			}

			$tipoDocumento = $request->input('tipoDocumento') == null || $request->input('tipoDocumento') == 'Todos' ? '' : $request->input('tipoDocumento');
			$estado =  $request->input('tipoEstado') == null || $request->input('tipoEstado') == 'Todos' ? '' : $request->input('tipoEstado');
			$cliente = $request->input('txtCliente');
			$numeroComprobante = $request->input('txtNumeroComprobante');
			$tipoDocumentoReal = $request->input('tipoDocumento');
			$estadoReal = $request->input('tipoEstado');
			$fechaInicial = $request->input('fechaInicial');
			$fechaFinal = $request->input('fechaFinal');

			$listaDocumentosGeneradosSunat = TDocumentoGeneradoSunat::whereRaw('codigoEmpresa=? and ( (documento = ? or nombre like ?) and tipo like ? and tipo != ? and (? or (numeroComprobante =? or numeroComprobanteAfectado =?)) and (? or (estado=?)) and created_at between ? and ?)', 
			[$sessionManager->get('codigoEmpresa'), $cliente, '%'.$cliente.'%', '%'. $tipoDocumento . '%', strpos($sessionManager->get('rol'), 'Súper usuario')!==false ? '' : 'Resumen diario', $numeroComprobante==null || $numeroComprobante == '', $numeroComprobante, $numeroComprobante, $estado == null || $estado == '', $estado, $fechaInicial, $fechaFinal])
			->get();

			if (count($listaDocumentosGeneradosSunat) > 15000) {
				return $this->plataformHelper->redirectError('No se puede generar el reporte, la cantidad de registro excede el limite permitido.', '/reporte/index');
			}

			switch (strtolower(str_replace(' ', '', $request->input('reporte')))) {
				case "exportaraexcel":
					if (count($listaDocumentosGeneradosSunat) < 1) {
						$request->flash();

						return $this->plataformHelper->redirectError('No se puede exportar, no se encontraron registros con el filtro actual!.', '/reporte/index');
					}

					$data = [];
					$styleRows = (object) [
						"generalTitle" => [],
						"summary" => [],
					];
					$headers = [];
					$fileName = 'reporteDocumentosGeneradosSUNAT-Cliente[' . $cliente . ']-Comprobante[' . $numeroComprobante .']-TipoDocumento[' . $tipoDocumentoReal . ']-Estado[' . $estadoReal					 
						. ']' . ($fechaInicial != '' ? ('-' . str_replace('/', '', $fechaInicial) . '-' . str_replace('/', '', $fechaFinal)) : '') . '.xlsx';

					$data[] = [
						'Documento',
						'Respuesta SUNAT',
						'Cliente',
						'Comprobante',
						'Comp. afectado',
						'Tipo',
						'Estado',
						'Fecha Registro'
					];

					$styleRows->generalTitle[] = count($data);

					foreach ($listaDocumentosGeneradosSunat as $key => $documentoGenerado) {
						$data[] = [
							$documentoGenerado->documento,
							$documentoGenerado->responseDescription,
							$documentoGenerado->nombre,
							$documentoGenerado->numeroComprobante,
							$documentoGenerado->numeroComprobanteAfectado,
							$documentoGenerado->tipo,
							$documentoGenerado->estado,
							$documentoGenerado->created_at
						];
					}

					return new ReportGeneratorExport($headers, $data, $fileName, function(AfterSheet $event) use ($styleRows) {
						$styleTitleGeneral = [
							'font' => [
								'bold' => true,
								'color' => [
									'argb' => 'FFFFFF',
								]
							],
							'fill' => [
								'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
								'color' => [
									'argb' => '141618',
								]
							],
						];

						foreach($styleRows->generalTitle as $generalTitle)
						{
							$event->sheet->getDelegate()->getStyle('A' . $generalTitle . ':H' . $generalTitle)->applyFromArray($styleTitleGeneral);
						}
					});
					

					break;
			}
		}

		if (!(strpos($sessionManager->get('rol'), 'Súper usuario') !== false || (((strpos($sessionManager->get('rol'), 'Administrador') !== false)) && strpos($sessionManager->get('rol'), 'Reporteador') !== false))) {
			echo '<div class="alert alert-danger"><h4><i class="icon fa fa-ban"></i> Prohibido!</h4>No tiene autorización para realizar esta operación o su "sesión de usuario" ya ha finalizado.</div>';
			exit;
		}

		return View("reporte/documentogeneradosunat");
	}

	public function actionInventarioGeneral(Request $request, SessionManager $sessionManager, Application $application)
	{
		if ($request->has('reporte')) {
			if (!(strpos($sessionManager->get('rol'), 'Súper usuario') !== false || (((strpos($sessionManager->get('rol'), 'Administrador') !== false)) && strpos($sessionManager->get('rol'), 'Reporteador') !== false))) {
				return $this->plataformHelper->redirectError('No tiene permisos para generar este reporte.', 'reporte/index');
			}
			
			$estado =  $request->input('txtEstado') == null || $request->input('txtEstado') == 'Indistinto' ? '' : $request->input('txtEstado');
			$codigoAmbiente = $request->input('selectAmbiente');
			
			$listaTInventario = TInventario::with('tambienteespacio.tambiente')
							->whereRaw('((? and codigoAmbienteEspacio in (select codigoAmbienteEspacio from tambienteespacio where estado=? and codigoAmbiente in (select codigoAmbiente from tambiente where codigoOficina=?))) or (? and codigoAmbienteEspacio in (select codigoAmbienteEspacio from tambienteespacio where estado=? and codigoAmbiente in (select codigoAmbiente from tambiente where codigoAlmacen=?)))) and (? or codigoAmbienteEspacio in (select codigoAmbienteEspacio from tambienteespacio where estado=? and codigoAmbiente =?)) and (? or (estado=?))', 
			[$request->has('codOficina'), true, $request->get('codOficina'), $request->has('codAlmacen'), true, $request->get('codAlmacen'), $codigoAmbiente == null, true, $codigoAmbiente , $estado == null || $estado == '', $estado])
			->get();

			$tAmbiente = TAmbiente::with(['toficina', 'talmacen'])->find($codigoAmbiente);
			$nombreOficinaAlmacen = $request->has('codOficina') ? TOficina::find($request->get('codOficina'))->descripcion : TAlmacen::find($request->get('codAlmacen'))->descripcion;
			$origenOficina = $request->has('codOficina');
			
			if (count($listaTInventario) > 15000) {
				return $this->plataformHelper->redirectError('No se puede generar el reporte, la cantidad de registro excede el limite permitido.', '/reporte/index');
			}

			switch (strtolower(str_replace(' ', '', $request->input('reporte')))) {
				case "exportaraexcel":
					if (count($listaTInventario) < 1) {
						$request->flash();

						return $this->plataformHelper->redirectError('No se puede exportar, no se encontraron registros con el filtro actual!.', '/reporte/index');
					}

					$data = [];
					$styleRows = (object) [
						"generalTitle" => [],
					];
					$headers = [];
					$fileName = 'reporteInventarioGeneral-[' . ($tAmbiente != null ? $tAmbiente->nombre : 'TodosLosAmbientes') . '].xlsx';

					$data[] = [
						'Nombre',
						'Ambiente',
						'Seccion',
						'Codigo barras',
						'Serie',
						'Modelo',
						'Descripcion',
						'Ancho',
						'Largo',
						'Alto',
						'Peso',
						'Estado'
					];

					$styleRows->generalTitle[] = count($data);

					foreach ($listaTInventario as $key => $value) {
						$data[] = [
							$value->nombre,
							$value->tambienteespacio->tambiente->nombre,
							$value->tambienteespacio->seccion,
							$value->codigoBarras,
							$value->serie,
							$value->modelo,
							$value->descripcion,
							$value->dimensionAncho,
							$value->dimensionLargo,
							$value->dimensionAlto,
							$value->pesoKg,
							$value->estado
						];
					}

					return new ReportGeneratorExport($headers, $data, $fileName, function(AfterSheet $event) use ($styleRows) {
						$styleTitleGeneral = [
							'font' => [
								'bold' => true,
								'color' => [
									'argb' => 'FFFFFF',
								]
							],
							'fill' => [
								'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
								'color' => [
									'argb' => '141618',
								]
							],
						];

						foreach($styleRows->generalTitle as $generalTitle)
						{
							$event->sheet->getDelegate()->getStyle('A' . $generalTitle . ':L' . $generalTitle)->applyFromArray($styleTitleGeneral);
						}
					});		
					
					break;
			}
		}

		if (!(strpos($sessionManager->get('rol'), 'Súper usuario') !== false || (((strpos($sessionManager->get('rol'), 'Administrador') !== false)) && strpos($sessionManager->get('rol'), 'Reporteador') !== false))) {
			echo '<div class="alert alert-danger"><h4><i class="icon fa fa-ban"></i> Prohibido!</h4>No tiene autorización para realizar esta operación o su "sesión de usuario" ya ha finalizado.</div>';
			exit;
		}

		$listTAlmacen = TAlmacen::whereRaw('codigoEmpresa = ?', [$sessionManager->get('codigoEmpresa')])->get();
		$listTOficina = TOficina::whereRaw('codigoEmpresa = ?', [$sessionManager->get('codigoEmpresa')])->get();

		return View("reporte/inventariogeneral", ['listTAlmacen' => $listTAlmacen, 'listTOficina' => $listTOficina]);
	}
}
?>