<?php
namespace App\Http\Middleware;

use Closure;
use Session;
use DB;

use DateTime;

use App\Model\TEmpresa;
use App\Model\TUsuario;
use App\Model\TCaja;
use App\Model\TCajaDetalle;
use App\Model\TEmpresaDeuda;
use App\Model\TUsuarioNotificacion;

class GenericMiddleware
{
	public function handle($request, Closure $next)
	{
		if(Session::has('codigoPersonal'))
		{
			$tEmpresa=TEmpresa::find(Session::get('codigoEmpresa'));
			
			if(!($tEmpresa->estado))
			{
				Session::flush();

				return redirect('/usuario/login');
			}

			Session::put('facturacionElectronica', $tEmpresa->facturacionElectronica);
			Session::put('tipoCambioUsd', $tEmpresa->tipoCambioUsd ?? 3.333);

			$tCaja=TCaja::whereRaw('codigoEmpresa=? and mid(created_at, 1, 10)=?', [Session::get('codigoEmpresa'), date('Y-m-d')])->first();

			if($tCaja==null)
			{
				$tCaja=new TCaja();

				$tCaja->codigoEmpresa=Session::get('codigoEmpresa');

				$tCaja->save();

				$tCaja=TCaja::whereRaw('codigoEmpresa=? and mid(created_at, 1, 10)=?', [Session::get('codigoEmpresa'), date('Y-m-d')])->first();
			}

			if(TCajaDetalle::whereRaw('codigoCaja=? and codigoPersonal=?', [$tCaja->codigoCaja, Session::get('codigoPersonal')])->count()==0)
			{
				$tCajaDetalle=new TCajaDetalle();

				$tCajaDetalle->codigoCaja=$tCaja->codigoCaja;
				$tCajaDetalle->codigoPersonal=Session::get('codigoPersonal');
				$tCajaDetalle->saldoInicial=0;
				$tCajaDetalle->egresos=0;
				$tCajaDetalle->ingresos=0;
				$tCajaDetalle->saldoFinal=0;
				$tCajaDetalle->descripcion='';
				$tCajaDetalle->cerrado=false;

				$tCajaDetalle->save();
			}

			Session::put('rol', TUsuario::find(Session::get('codigoPersonal'))->rol);
			Session::put('codigoCajaDetalle', TCajaDetalle::whereRaw('codigoCaja=? and codigoPersonal=?', [$tCaja->codigoCaja, Session::get('codigoPersonal')])->first()->codigoCajaDetalle);
		}

		$url=explode('/', $request->url());

		$url=$url[0].'//'.$url[1].$url[2].env('URL_ADICIONAL_FILTRO');

		$accesoUrl=false;

		$permisosUrl=
		[
			//TGeneral
			['Súper usuario,Administrador,Almacenero,Ventas,Reporteador,Revocador', false, $url, 'liMenuPanelControl', 'liMenuItemPanelControlInicio'],
			['Súper usuario,Administrador,Almacenero,Ventas,Reporteador,Revocador', true, $url.'/general/index', 'liMenuPanelControl', 'liMenuItemPanelControlInicio'],
			['Súper usuario,Público', false, $url.'/general/configuracionglobal', 'liMenuPanelControl', 'liMenuItemPanelControlRegistrarCliente'],
			['Súper usuario', true, $url.'/general/databackup', null, null],
			['Súper usuario,Administrador,Almacenero,Ventas,Reporteador', false, $url.'/general/manualesusuario', 'liMenuPanelControl', 'liMenuItemPanelControlManualesUsuario'],
			
			//TBillSyncUp
			['Ventas', false, $url.'/billsyncup/sync', null, null],

			//TEmpresa
			['Súper usuario', false, $url.'/empresa/ver', 'liMenuPanelControl', 'liMenuItemPanelControlListarEmpresas'],
			['Súper usuario', false, $url.'/empresa/editar', null, null],
			['Súper usuario,Administrador', false, $url.'/empresa/editartipocambiousdconajax', null, null],

			//TUbigeo
			['Súper usuario,Administrador,Ventas,Almacenero', false, $url.'/ubigeo/jsonporubicacion', null, null],

			//TEmpresaDeuda
			['Súper usuario,Administrador', true, $url.'/empresadeuda/gestionar', 'liMenuPanelControl', 'liMenuItemPanelControlListarDeudasEmpresa'],
			['Súper usuario', true, $url.'/empresadeuda/inclusionigv', null, null],
			['Súper usuario', true, $url.'/empresadeuda/emisionfactura', null, null],
			['Súper usuario', true, $url.'/empresadeuda/cambiopago', null, null],
			['Súper usuario', true, $url.'/empresadeuda/eliminar', null, null],

			//TPersonal
			['Súper usuario', false, $url.'/personal/insertar', 'liMenuGestionPersonal', 'liMenuItemGestionPersonalRegistrarPersonal'],
			['Súper usuario,Administrador,Ventas,Almacenero,Reporteador', true, $url.'/personal/ver', 'liMenuGestionPersonal', 'liMenuItemGestionPersonalListarPersonal'],
			['Súper usuario,Administrador', false, $url.'/personal/editar', null, null],
			['Súper usuario,Administrador,Ventas,Almacenero,Reporteador', false, $url.'/personal/cambiarcontrasenia', null, null],

			//TAlmacen
			['Súper usuario', false, $url.'/almacen/insertar', 'liMenuGestionLocales', 'liMenuItemGestionLocalesRegistrarAlmacen'],
			['Súper usuario,Administrador', false, $url.'/almacen/ver', 'liMenuGestionLocales', 'liMenuItemGestionLocalesListarAlmacenes'],
			['Súper usuario,Administrador', false, $url.'/almacen/editar', null, null],
			['Súper usuario,Administrador', false, $url.'/almacen/gestionarpersonal', null, null],
			['Súper usuario,Administrador,Ventas,Almacenero', false, $url.'/almacen/jsonpordescripcion', null, null],

			//TAlmacenProducto
			['Súper usuario,Administrador,Almacenero', false, $url.'/almacenproducto/jsonporcodigoempresanombregroupbynombre', null, null],
			['Súper usuario,Administrador,Almacenero', false, $url.'/almacenproducto/jsonporcodigoalmacennombre', null, null],
			['Súper usuario,Administrador,Almacenero', false, $url.'/almacenproducto/jsonporcodigobarrasnombre', null, null],
			['Súper usuario,Administrador,Almacenero', false, $url.'/almacenproducto/jsonporcodigobarrasnombrealmacen', null, null],
			['Súper usuario,Administrador', true, $url.'/almacenproducto/verporcodigoalmacen', 'liMenuGestionProductos', 'liMenuItemGestionProductosListarProductosAlmacen'],
			['Súper usuario,Administrador', true, $url.'/almacenproducto/veragrupado', 'liMenuGestionProductos', 'liMenuItemGestionProductosListarProductosAgrupado'],
			['Súper usuario,Administrador', false, $url.'/almacenproducto/editaragrupado', null, null],
			['Súper usuario,Administrador', false, $url.'/almacenproducto/borrarproductosinstock', null, null],

			//TAlmacenProductoRetiro
			['Súper usuario,Administrador', false, $url.'/almacenproductoretiro/insertar', 'liMenuGestionProductos', 'liMenuItemGestionProductosRetiroProductoAlmacen'],
			['Súper usuario,Administrador', true, $url.'/almacenproductoretiro/ver', 'liMenuGestionProductos', 'liMenuItemGestionProductosListarRetiroProductoAlmacen'],
			['Súper usuario,Administrador', false, $url.'/almacenproductoretiro/detalle', 'liMenuGestionProductos', 'liMenuItemGestionProductosListarRetiroProductoAlmacen'],

			//TProductoEnviarStock
			['Súper usuario,Administrador,Almacenero', false, $url.'/productoenviarstock/insertar', 'liMenuGestionTraslados', 'liMenuItemGestionTransladoAlmacenOficina'],
			['Súper usuario,Administrador,Almacenero', true, $url.'/productoenviarstock/ver', 'liMenuGestionTraslados', 'liMenuItemGestionListarTransladoAlmacenOficina'],
			['Súper usuario,Administrador,Almacenero', false, $url.'/productoenviarstock/detalle', 'liMenuGestionTraslados', 'liMenuItemGestionListarTransladoAlmacenOficina'],
			['Súper usuario,Administrador,Almacenero', true, $url.'/productoenviarstock/anular', 'liMenuGestionTraslados', 'liMenuItemGestionListarTransladoAlmacenOficina'],
			['Súper usuario,Administrador,Almacenero', true, $url.'/productoenviarstock/imprimircomprobante', null, null],

			//TOficina
			['Súper usuario', false, $url.'/oficina/insertar', 'liMenuGestionLocales', 'liMenuItemGestionLocalesRegistrarOficina'],
			['Súper usuario,Administrador', false, $url.'/oficina/ver', 'liMenuGestionLocales', 'liMenuItemGestionLocalesListarOficinas'],
			['Súper usuario,Administrador', false, $url.'/oficina/editar', null, null],
			['Súper usuario,Administrador', false, $url.'/oficina/gestionarpersonal', null, null],
			['Súper usuario,Administrador,Ventas,Almacenero', false, $url.'/oficina/jsonpordescripcion', null, null],
			
			//TPersonalTOficina
			['Súper usuario,Administrador,Ventas,Almacenero,Reporteador', false, $url.'/personaltoficina/jsonpersonaltoficina', null, null],

			//TOficinaProducto
			['Súper usuario,Administrador,Ventas', false, $url.'/oficinaproducto/jsonporcodigobarrasnombre', null, null],
			['Súper usuario,Administrador,Ventas', false, $url.'/oficinaproducto/jsonporcodigobarrasnombreoficina', null, null],
			['Súper usuario,Administrador', true, $url.'/oficinaproducto/verporcodigooficina', 'liMenuGestionProductos', 'liMenuItemGestionProductosListarProductosOficina'],

			//TOficinaProductoRetiro
			['Súper usuario,Administrador', false, $url.'/oficinaproductoretiro/insertar', 'liMenuGestionProductos', 'liMenuItemGestionProductosRetiroProductoOficina'],
			['Súper usuario,Administrador', true, $url.'/oficinaproductoretiro/ver', 'liMenuGestionProductos', 'liMenuItemGestionProductosListarRetiroProductoOficina'],
			['Súper usuario,Administrador', false, $url.'/oficinaproductoretiro/detalle', 'liMenuGestionProductos', 'liMenuItemGestionProductosListarRetiroProductoOficina'],

			//TProductoTrasladoOficina
			['Súper usuario,Administrador', false, $url.'/productotrasladooficina/insertar', 'liMenuGestionTraslados', 'liMenuItemGestionTransladoOficinaOficina'],
			['Súper usuario,Administrador', true, $url.'/productotrasladooficina/ver', 'liMenuGestionTraslados', 'liMenuItemGestionListarTransladoOficinaOficina'],
			['Súper usuario,Administrador', false, $url.'/productotrasladooficina/detalle', 'liMenuGestionTraslados', 'liMenuItemGestionListarTransladoOficinaOficina'],
			['Súper usuario,Administrador', true, $url.'/productotrasladooficina/anular', 'liMenuGestionTraslados', 'liMenuItemGestionListarTransladoOficinaOficina'],
			['Súper usuario,Administrador', true, $url.'/productotrasladooficina/imprimircomprobante', null, null],

			//TUsuario
			['Súper usuario,Administrador,Almacenero,Ventas,Reporteador,Revocador,Público', false, $url.'/usuario/login', null, null],
			['Súper usuario,Administrador,Almacenero,Ventas,Reporteador,Revocador,Público', false, $url.'/usuario/logout', null, null],
			['Súper usuario,Administrador,Almacenero,Ventas,Revocador,Reporteador', false, $url.'/usuario/cambiarlocal', null, null],

			//TClienteNatural
			['Súper usuario,Administrador,Ventas', false, $url.'/clientenatural/jsonpordni', null, null],

			//TClienteJuridico
			['Súper usuario,Administrador,Ventas', false, $url.'/clientejuridico/jsonporruc', null, null],
			['Súper usuario,Administrador,Ventas', false, $url.'/clientejuridico/jsonporrazonsociallargaparaventa', null, null],

			//TReciboCompra
			['Súper usuario,Administrador,Almacenero', false, $url.'/recibocompra/insertar', 'liMenuGestionCompras', 'liMenuItemGestionComprasRegistrarCompra'],
			['Súper usuario,Administrador,Almacenero', true, $url.'/recibocompra/ver', 'liMenuGestionCompras', 'liMenuItemGestionComprasListarCompras'],
			['Súper usuario,Administrador,Almacenero', false, $url.'/recibocompra/detalle', 'liMenuGestionCompras', 'liMenuItemGestionComprasListarCompras'],
			['Súper usuario,Administrador,Almacenero', true, $url.'/recibocompra/anular', 'liMenuGestionCompras', 'liMenuItemGestionComprasListarCompras'],
						
			//TReciboCompraPago
			['Súper usuario,Administrador,Almacenero', false, $url.'/recibocomprapago/pago', null, null],
			['Súper usuario,Administrador,Almacenero', false, $url.'/recibocomprapago/realizarpago', null, null],
			['Súper usuario,Administrador,Almacenero', true, $url.'/recibocomprapago/eliminar', null, null],

			//TReciboVenta
			['Súper usuario,Administrador,Ventas', false, $url.'/reciboventa/insertar', 'liMenuGestionVentas', 'liMenuItemGestionVentasRegistrarVenta'],
			['Súper usuario,Administrador,Ventas', false, $url.'/reciboventa/proforma', null, null],
			['Súper usuario,Administrador,Ventas,Revocador', true, $url.'/reciboventa/ver', 'liMenuGestionVentas', 'liMenuItemGestionVentasListarVentas'],
			['Súper usuario,Administrador,Ventas', false, $url.'/reciboventa/detalle', 'liMenuGestionVentas', 'liMenuItemGestionVentasListarVentas'],
			['Súper usuario,Administrador,Ventas', true, $url.'/reciboventa/descargarpdfxml', null, null],
			['Súper usuario,Administrador,Ventas', true, $url.'/reciboventa/imprimircomprobante', null, null],
			['Súper usuario,Administrador,Ventas', false, $url.'/reciboventa/insertarsinfe', 'liMenuGestionVentas', 'liMenuItemGestionVentasRegistrarVentaSinFe'],
			['Súper usuario,Administrador,Ventas,Revocador', true, $url.'/reciboventa/listasinfe', 'liMenuGestionVentas', 'liMenuItemGestionVentasListarVentasSinFe'],
			['Súper usuario,Administrador,Ventas', true, $url.'/reciboventa/imprimircomprobantesinfe', null, null],
			['Súper usuario,Administrador,Ventas', false, $url.'/reciboventa/detallesinfe', null, null],
			['Súper usuario,Administrador,Ventas', true, $url.'/reciboventa/generarventaconfe', null, null],
			['Súper usuario,Administrador,Revocador', false, $url.'/reciboventa/anularventasinfe', null, null],
			['Súper usuario,Administrador,Ventas', false, $url.'/reciboventa/enviarpdfxml', null, null],

			//TReciboVentaGuiaRemision
			['Súper usuario,Administrador,Ventas', false, $url.'/reciboventaguiaremision/gestionarguiaremision', null, null],
			['Súper usuario,Administrador,Ventas', true, $url.'/reciboventaguiaremision/descargarpdfxml', null, null],
			['Súper usuario,Administrador,Ventas', true, $url.'/reciboventaguiaremision/imprimircomprobante', null, null],
						
			//TReciboVentaLetra
			['Súper usuario,Administrador,Ventas', false, $url.'/reciboventaletra/pagoletra', null, null],
			['Súper usuario,Administrador,Ventas', false, $url.'/reciboventaletra/realizarpagoletra', null, null],
			['Súper usuario,Administrador,Ventas', true, $url.'/reciboventaletra/eliminar', null, null],
			['Súper usuario,Administrador,Ventas', false, $url.'/reciboventaletra/pagoletrasinfe', null, null],
			['Súper usuario,Administrador,Ventas', false, $url.'/reciboventaletra/realizarpagoletrasinfe', null, null],
			['Súper usuario,Administrador,Ventas', true, $url.'/reciboventaletra/eliminarsinfe', null, null],
			['Súper usuario,Administrador,Ventas', true, $url.'/reciboventaletra/imprimircomprobantesinfe', null, null],
			['Súper usuario,Administrador,Ventas', true, $url.'/reciboventaletra/imprimircomprobante', null, null],
			['Súper usuario,Administrador,Ventas', true, $url.'/reciboventaletra/marcarcomopagadoletra', null, null],
			['Súper usuario,Administrador,Ventas', true, $url.'/reciboventaletra/marcarcomopagadoletrasinfe', null, null],

			//TResumenDiario
			['Súper usuario,Administrador', true, $url.'/resumendiario/gestionar', 'liMenuGestionVentas', 'liMenuItemGestionVentasResumenDiario'],
			['Súper usuario', true, $url.'/resumendiario/cambiarestado', null, null],
			['Súper usuario', true, $url.'/resumendiario/descargarxml', null, null],
			
			//TReciboVentaNotaCredito
			['Súper usuario,Administrador,Revocador', false, $url.'/reciboventanotacredito/insertar', null, null],
			['Súper usuario,Administrador,Ventas', true, $url.'/reciboventanotacredito/descargarpdfxml', null, null],
			['Súper usuario,Administrador,Ventas', true, $url.'/reciboventanotacredito/imprimircomprobante', null, null],

			//TReciboVentaNotaDebito
			['Súper usuario,Administrador,Revocador', false, $url.'/reciboventanotadebito/insertar', null, null],
			['Súper usuario,Administrador,Ventas', true, $url.'/reciboventanotadebito/descargarpdfxml', null, null],
			['Súper usuario,Administrador,Ventas', true, $url.'/reciboventanotadebito/imprimircomprobante', null, null],

			//TDocumentoGeneradoSunat
			['Súper usuario,Administrador', true, $url.'/documentogeneradosunat/ver', 'liMenuPanelControl', 'liMenuItemPanelControlListarDocumentosGeneradosSunat'],

			//TReportes
			['Súper usuario,Reporteador', false, $url.'/reporte/index', 'liMenuReportes', null],
			['Súper usuario,Reporteador', false, $url.'/reporte/ventas', null, null],
			['Súper usuario,Reporteador', false, $url.'/reporte/ventasconsolidado', null, null],
			['Súper usuario,Reporteador', false, $url.'/reporte/ventaswef', null, null],
			['Súper usuario,Reporteador', false, $url.'/reporte/ventaswefconsolidado', null, null],
			['Súper usuario,Reporteador', false, $url.'/reporte/compras', null, null],
			['Súper usuario,Reporteador', false, $url.'/reporte/comprasconsolidado', null, null],
			['Súper usuario,Reporteador', false, $url.'/reporte/caja', null, null],
			['Súper usuario,Reporteador', false, $url.'/reporte/productosalmacen', null, null],
			['Súper usuario,Reporteador', false, $url.'/reporte/productosoficina', null, null],
			['Súper usuario,Reporteador', false, $url.'/reporte/productosoficinaconsolidado', null, null],
			['Súper usuario,Reporteador', false, $url.'/reporte/productosoficinacompra', null, null],
			['Súper usuario,Reporteador', false, $url.'/reporte/notascredito', null, null],
			['Súper usuario,Reporteador', false, $url.'/reporte/notasdebito', null, null],
			['Súper usuario,Reporteador', false, $url.'/reporte/documentogeneradosunat', null, null],
			['Súper usuario,Reporteador', false, $url.'/reporte/generalconsolidado', null, null],
			['Súper usuario,Reporteador', false, $url.'/reporte/inventariogeneral', null, null],

			//TEgreso
			['Súper usuario,Administrador', false, $url.'/egreso/insertar', 'liMenuOperaciones', 'liMenuItemEgreso', 'liMenuItemRegistrarEgreso'],
			['Súper usuario,Administrador', true, $url.'/egreso/ver', 'liMenuOperaciones', 'liMenuItemEgreso', 'liMenuItemVerEgreso'],
			
			//TCategoriaVenta
			['Súper usuario,Administrador', true, $url.'/categoriaventa/mantenimiento', 'liMenuGestionVentas', 'liMenuItemGestionVentasCategorizacionVenta'],
			['Súper usuario,Administrador', false, $url.'/categoriaventa/editar', null, null],
			['Súper usuario,Administrador', true, $url.'/categoriaventa/eliminar', null, null],
			['Súper usuario,Administrador', true, $url.'/categoriaventa/habilitar', null, null],

			//TExcepcion
			['Súper usuario', true, $url.'/excepcion/ver', 'liMenuPanelControl', 'liMenuItemPanelControlListarExcepciones'],
			['Súper usuario', true, $url.'/excepcion/cambiarestado', null, null],

			//Consulta Externa
			['Súper usuario,Administrador,Almacenero,Ventas,Reporteador,Público', false, $url.'/consultaexterna/clienteexterno', null, null],
			['Súper usuario,Administrador,Almacenero,Ventas,Reporteador,Público', false, $url.'/consultaexterna/comprobantesemitidos', null, null],
			['Súper usuario,Administrador,Almacenero,Ventas,Reporteador,Público', true, $url.'/consultaexterna/descargarpdfxml', null, null],
			['Súper usuario,Administrador,Almacenero,Ventas,Reporteador,Público', true, $url.'/consultaexterna/imprimircomprobanteventasinfe', null, null],
			['Súper usuario,Administrador,Almacenero,Ventas,Reporteador,Público', true, $url.'/consultaexterna/descargarpdfxmlnotacredito', null, null],
			['Súper usuario,Administrador,Almacenero,Ventas,Reporteador,Público', true, $url.'/consultaexterna/descargarpdfxmlnotadebito', null, null],
			['Súper usuario,Administrador,Almacenero,Ventas,Reporteador,Público', true, $url.'/consultaexterna/imprimircomprobanteventa', null, null],
			['Súper usuario,Administrador,Almacenero,Ventas,Reporteador,Público', true, $url.'/consultaexterna/imprimircomprobantenotacredito', null, null],
			['Súper usuario,Administrador,Almacenero,Ventas,Reporteador,Público', true, $url.'/consultaexterna/imprimircomprobantenotadebito', null, null],

			//TUsuarioNotificacion
			['Súper usuario', false, $url.'/usuarionotificacion/insertar', 'liMenuPanelControl', 'liMenuItemNotificacion', 'liMenuItemRegistrarNotificacion'],
			['Súper usuario', true, $url.'/usuarionotificacion/ver', 'liMenuPanelControl', 'liMenuItemNotificacion', 'liMenuItemVerNotificacion'],
			['Súper usuario', true, $url.'/usuarionotificacion/ocultarnotificacion', null, null],
			['Súper usuario,Administrador,Almacenero,Ventas,Reporteador', false, $url.'/usuarionotificacion/marcartodoleido', null, null],
			['Súper usuario,Administrador,Almacenero,Ventas,Reporteador', false, $url.'/usuarionotificacion/detalle', null, null],
			['Súper usuario,Administrador,Almacenero,Ventas,Reporteador', false, $url.'/usuarionotificacion/marcarleido', null, null],
			['Súper usuario,Administrador,Almacenero,Ventas,Reporteador', false, $url.'/usuarionotificacion/marcarleidojson', null, null],
			
			//TAmbiente
			['Súper usuario,Administrador,Inventariador', false, $url.'/ambiente/insertarajax', null, null],
			['Súper usuario,Administrador,Inventariador', false, $url.'/ambiente/jsonpornombrecodigo', null, null],
			['Súper usuario,Administrador,Inventariador', false, $url.'/ambiente/cargarambientes', null, null],

			//TAmbienteEspacio
			['Súper usuario,Administrador,Inventariador', false, $url.'/ambienteespacio/insertarajax', null, null],
			['Súper usuario,Administrador,Inventariador', false, $url.'/ambienteespacio/cargarespacios', null, null],

			//TInventario
			['Súper usuario,Administrador,Inventariador', false, $url.'/inventario/insertar', 'liMenuInventario', 'liMenuInventarioInsertar'],
			['Súper usuario,Administrador,Inventariador', true, $url.'/inventario/ver', 'liMenuInventario', 'liMenuInventarioVer'],
			['Súper usuario,Administrador,Inventariador', false, $url.'/inventario/editar', null, null],
			['Súper usuario,Administrador,Inventariador', true, $url.'/inventario/eliminar', null, null]
		];

		$miRol=Session::get('rol', 'Público');
		$miRol=$miRol=='' ? 'Público' : $miRol;
		
		foreach ($permisosUrl as $key => $value)
		{
			if($request->url()==$value[2] || ($value[1] && strlen(strpos($request->url(), $value[2]))>0))
			{
				$permisos=explode(',', $value[0]);
				$roles=explode(',', $miRol);

				foreach ($permisos as $key2 => $value2)
				{
					foreach ($roles as $item)
					{
						if($value2==$item)
						{
							$accesoUrl=true;

							Session::put('menuItemPadreSelected', $value[3]);
							Session::put('menuItemHijoSelected', $value[4]);
							Session::put('menuItemSubHijoSelected', $value[count($value) - 1]);

							break 3;
						}
					}
				}
			}
		}

		if(!$accesoUrl)
		{
			if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])=='xmlhttprequest')
			{
				echo '<div class="alert alert-danger"><h4><i class="icon fa fa-ban"></i> Prohibido!</h4>No tiene autorización para realizar esta operación o su "sesión de usuario" ya ha finalizado.</div>';
				exit;
			}
			else 
			{
				return redirect('/usuario/login');
			}
		}

		$tEmpresaDeudaGlobal=TEmpresaDeuda::whereRaw('codigoEmpresa=? and !estado and adddate(fechaPagar, interval -1 day)<=?', [Session::get('codigoEmpresa'), date('Y-m-d')])->orderBy('fechaPagar', 'asc')->first();

		if($tEmpresaDeudaGlobal!=null)
		{
			$fecha1TempGlobal=new DateTime(date('Y-m-d'));
			$fecha2TempGlobal=new DateTime($tEmpresaDeudaGlobal->fechaPagar);
			$resultado=$fecha2TempGlobal->diff($fecha1TempGlobal);
			$tEmpresaDeudaGlobal->diasRetraso=$resultado->format('%R%a');

			view()->share('tEmpresaDeudaGlobal', $tEmpresaDeudaGlobal);
		}

		$tUsuarioNotificacion=TUsuarioNotificacion::whereRaw(
			'codigoPersonal=? and ((permanente=? and ? between fechaInicioPeriodo and fechaFinPeriodo) or (estado=? and permanente=? ) )',
			[Session::get('codigoPersonal'), true, date('Y-m-d'), false, false]
		)->orderBy('created_at', 'desc')->get();

		if($tUsuarioNotificacion!=null && $tUsuarioNotificacion->count()>0)
		{
			view()->share('tUsuarioNotificacion', $tUsuarioNotificacion);
		}

		return $next($request);
	}
}