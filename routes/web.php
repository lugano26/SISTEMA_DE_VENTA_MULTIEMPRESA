<?php
//TGeneral
Route::get('/', 'GeneralController@actionIndex');
Route::get('general/index/{pagina?}', 'GeneralController@actionIndex');
Route::match(['get', 'post'], 'general/configuracionglobal', 'GeneralController@actionConfiguracionGlobal');
Route::get('general/databackup', 'GeneralController@actionDataBackup');
Route::get('general/manualesusuario', 'GeneralController@actionManualesUsuario');

//TBillSyncUp
Route::post('billsyncup/sync', 'BillSyncUpController@actionSync');

//TEmpresa
Route::get('empresa/ver', 'EmpresaController@actionVer');
Route::post('empresa/editar', 'EmpresaController@actionEditar');
Route::post('empresa/editartipocambiousdconajax', 'EmpresaController@actionEditarTipoCambioUsdConAjax');

//TUbigeo
Route::post('ubigeo/jsonporubicacion', 'UbigeoController@actionJSONPorUbicacion');

//TEmpresaDeuda
Route::get('empresadeuda/gestionar/{codigoEmpresa}', 'EmpresaDeudaController@actionGestionar');
Route::post('empresadeuda/gestionar', 'EmpresaDeudaController@actionGestionar');
Route::get('empresadeuda/inclusionigv/{codigoEmpresaDeuda}', 'EmpresaDeudaController@actionInclusionIgv');
Route::get('empresadeuda/emisionfactura/{codigoEmpresaDeuda}', 'EmpresaDeudaController@actionEmisionFactura');
Route::get('empresadeuda/cambiopago/{codigoEmpresaDeuda}', 'EmpresaDeudaController@actionCambioPago');
Route::get('empresadeuda/eliminar/{codigoEmpresaDeuda}', 'EmpresaDeudaController@actionEliminar');

//TPersonal
Route::match(['get', 'post'], 'personal/insertar', 'PersonalController@actionInsertar');
Route::get('personal/ver/{pagina?}', 'PersonalController@actionVer');
Route::post('personal/editar', 'PersonalController@actionEditar');
Route::post('personal/cambiarcontrasenia', 'PersonalController@actionCambiarContrasenia');

//TAlmacen
Route::match(['get', 'post'], '/almacen/insertar', 'AlmacenController@actionInsertar');
Route::get('almacen/ver', 'AlmacenController@actionVer');
Route::post('almacen/editar', 'AlmacenController@actionEditar');
Route::post('almacen/gestionarpersonal', 'AlmacenController@actionGestionarPersonal');
Route::post('almacen/jsonpordescripcion', 'AlmacenController@actionJSONPorDescripcion');

//TAlmacenProducto
Route::post('almacenproducto/jsonporcodigoempresanombregroupbynombre', 'AlmacenProductoController@actionJSONPorCodigoEmpresaNombreGroupByNombre');
Route::post('almacenproducto/jsonporcodigoalmacennombre', 'AlmacenProductoController@actionJSONPorCodigoAlmacenNombre');
Route::post('almacenproducto/jsonporcodigobarrasnombre', 'AlmacenProductoController@actionJSONPorCodigoBarrasNombre');
Route::post('almacenproducto/jsonporcodigobarrasnombrealmacen', 'AlmacenProductoController@actionJSONPorCodigoBarrasNombreAlmacen');
Route::get('almacenproducto/verporcodigoalmacen/{pagina?}', 'AlmacenProductoController@actionVerPorCodigoAlmacen');
Route::get('almacenproducto/veragrupado/{pagina?}', 'AlmacenProductoController@actionVerAgrupado');
Route::post('almacenproducto/editaragrupado', 'AlmacenProductoController@actionEditarAgrupado');
Route::get('almacenproducto/borrarproductosinstock', 'AlmacenProductoController@actionBorrarProductosSinStock');

//TAlmacenProductoRetiro
Route::match(['get', 'post'], 'almacenproductoretiro/insertar', 'AlmacenProductoRetiroController@actionInsertar');
Route::get('almacenproductoretiro/ver/{pagina?}', 'AlmacenProductoRetiroController@actionVer');
Route::post('almacenproductoretiro/detalle', 'AlmacenProductoRetiroController@actionDetalle');

//TProductoEnviarStock
Route::match(['get', 'post'], 'productoenviarstock/insertar', 'ProductoEnviarStockController@actionInsertar');
Route::get('productoenviarstock/ver/{pagina?}', 'ProductoEnviarStockController@actionVer');
Route::post('productoenviarstock/detalle', 'ProductoEnviarStockController@actionDetalle');
Route::get('productoenviarstock/anular/{codigoProductoEnviarStock}', 'ProductoEnviarStockController@actionAnular');
Route::get('productoenviarstock/imprimircomprobante/{codigoProductoEnviarStock}', 'ProductoEnviarStockController@actionImprimirComprobante');

//TOficina
Route::match(['get', 'post'], '/oficina/insertar', 'OficinaController@actionInsertar');
Route::get('oficina/ver', 'OficinaController@actionVer');
Route::post('oficina/editar', 'OficinaController@actionEditar');
Route::post('oficina/gestionarpersonal', 'OficinaController@actionGestionarPersonal');
Route::post('oficina/jsonpordescripcion', 'OficinaController@actionJSONPorDescripcion');

//TPersonalTOficina
Route::post('personaltoficina/jsonpersonaltoficina', 'PersonalTOficinaController@actionJSONPersonalTOficina');

//TOficinaProducto
Route::post('oficinaproducto/jsonporcodigobarrasnombre', 'OficinaProductoController@actionJSONPorCodigoBarrasNombre');
Route::post('oficinaproducto/jsonporcodigobarrasnombreoficina', 'OficinaProductoController@actionJSONPorCodigoBarrasNombreOficina');
Route::get('oficinaproducto/verporcodigooficina/{pagina?}', 'OficinaProductoController@actionVerPorCodigoOficina');

//TProductoTrasladoOficina
Route::match(['get', 'post'], 'productotrasladooficina/insertar', 'ProductoTrasladoOficinaController@actionInsertar');
Route::get('productotrasladooficina/ver/{pagina?}', 'ProductoTrasladoOficinaController@actionVer');
Route::post('productotrasladooficina/detalle', 'ProductoTrasladoOficinaController@actionDetalle');
Route::get('productotrasladooficina/anular/{codigoProductoTransladoOficina}', 'ProductoTrasladoOficinaController@actionAnular');
Route::get('productotrasladooficina/imprimircomprobante/{codigoProductoTransladoOficina}', 'ProductoTrasladoOficinaController@actionImprimirComprobante');

//TOficinaProductoRetiro
Route::match(['get', 'post'], 'oficinaproductoretiro/insertar', 'OficinaProductoRetiroController@actionInsertar');
Route::get('oficinaproductoretiro/ver/{pagina?}', 'OficinaProductoRetiroController@actionVer');
Route::post('oficinaproductoretiro/detalle', 'OficinaProductoRetiroController@actionDetalle');

//TClienteNatural
Route::post('clientenatural/jsonpordni', 'ClienteNaturalController@actionJSONPorDni');

//TClienteJuridico
Route::post('clientejuridico/jsonporruc', 'ClienteJuridicoController@actionJSONPorRuc');
Route::post('clientejuridico/jsonporrazonsociallargaparaventa', 'ClienteJuridicoController@actionJSONPorRazonSocialLargaParaVenta');

//TUsuario
Route::match(['get', 'post'], 'usuario/login', 'UsuarioController@actionLogIn');
Route::get('usuario/logout', 'UsuarioController@actionLogOut');
Route::match(['get', 'post'], 'usuario/cambiarlocal', 'UsuarioController@actionCambiarLocal');

//TReciboCompra
Route::match(['get', 'post'], 'recibocompra/insertar', 'ReciboCompraController@actionInsertar');
Route::get('recibocompra/ver/{pagina?}', 'ReciboCompraController@actionVer');
Route::post('recibocompra/detalle', 'ReciboCompraController@actionDetalle');
Route::get('recibocompra/anular/{codigoReciboCompra}', 'ReciboCompraController@actionAnular');

//TReciboCompraPago
Route::post('recibocomprapago/pago', 'ReciboCompraPagoController@actionPago');
Route::post('recibocomprapago/realizarpago', 'ReciboCompraPagoController@actionRealizarPago');
Route::get('recibocomprapago/eliminar/{codigoReciboCompraPago}', 'ReciboCompraPagoController@actionEliminar');

//TReciboVenta
Route::match(['get', 'post'], 'reciboventa/insertar', 'ReciboVentaController@actionInsertar');
Route::post('reciboventa/proforma', 'ReciboVentaController@actionProforma');
Route::get('reciboventa/ver/{pagina?}', 'ReciboVentaController@actionVer');
Route::get('reciboventa/descargarpdfxml/{codigoReciboVenta}', 'ReciboVentaController@actionDescargarPdfXml');
Route::get('reciboventa/imprimircomprobante/{codigoReciboVenta}', 'ReciboVentaController@actionImprimirComprobante');
Route::post('reciboventa/detalle', 'ReciboVentaController@actionDetalle');
Route::match(['get', 'post'], 'reciboventa/insertarsinfe', 'ReciboVentaController@actionInsertarSinFe');
Route::get('reciboventa/listasinfe/{pagina?}', 'ReciboVentaController@actionListaSinFe');
Route::get('reciboventa/imprimircomprobantesinfe/{codigoReciboVentaOutEf}', 'ReciboVentaController@actionImprimirComprobanteSinFe');
Route::post('reciboventa/detallesinfe', 'ReciboVentaController@actionDetalleSinFe');
Route::get('reciboventa/generarventaconfe/{codigoReciboVentaOutEf}', 'ReciboVentaController@actionGenerarVentaConFe');
Route::post('reciboventa/anularventasinfe', 'ReciboVentaController@actionAnularVentaSinFe');
Route::post('reciboventa/enviarpdfxml', 'ReciboVentaController@actionEnviarPdfXml');

//TReciboVentaGuiaRemision
Route::post('reciboventaguiaremision/gestionarguiaremision', 'ReciboVentaGuiaRemisionController@actionGestionarGuiaRemision');
Route::get('reciboventaguiaremision/descargarpdfxml/{codigoReciboVentaGuiaRemision}', 'ReciboVentaGuiaRemisionController@actionDescargarPdfXml');
Route::get('reciboventaguiaremision/imprimircomprobante/{codigoReciboVentaGuiaRemision}', 'ReciboVentaGuiaRemisionController@actionImprimirComprobante');

//TReciboVentaLetra
Route::post('reciboventaletra/pagoletra', 'ReciboVentaLetraController@actionPagoLetra');
Route::post('reciboventaletra/realizarpagoletra', 'ReciboVentaLetraController@actionRealizarPagoLetra');
Route::get('reciboventaletra/eliminar/{codigoReciboVentaPago}', 'ReciboVentaLetraController@actionEliminar');
Route::post('reciboventaletra/pagoletrasinfe', 'ReciboVentaLetraController@actionPagoLetraSinFe');
Route::post('reciboventaletra/realizarpagoletrasinfe', 'ReciboVentaLetraController@actionRealizarPagoLetraSinFe');
Route::get('reciboventaletra/eliminarsinfe/{codigoReciboVentaPago}', 'ReciboVentaLetraController@actionEliminarSinFe');
Route::get('reciboventaletra/imprimircomprobantesinfe/{codigoReciboVentaPago}', 'ReciboVentaLetraController@actiomImprimirComprobanteSinFe');
Route::get('reciboventaletra/imprimircomprobante/{codigoReciboVentaPago}', 'ReciboVentaLetraController@actiomImprimirComprobante');
Route::get('reciboventaletra/marcarcomopagadoletra/{codigoReciboVentaLetra}', 'ReciboVentaLetraController@actionMarcarComoPagadoLetra');
Route::get('reciboventaletra/marcarcomopagadoletrasinfe/{codigoReciboVentaLetraOutEf}', 'ReciboVentaLetraController@actionMarcarComoPagadoLetraSinFe');

//TResumenDiario
Route::match(['get', 'post'], 'resumendiario/gestionar/{pagina?}', 'ResumenDiarioController@actionGestionar');
Route::get('resumendiario/cambiarestado/{codigoResumenDiario}/{estado}', 'ResumenDiarioController@actionCambiarEstado');
Route::get('resumendiario/descargarxml/{codigoResumenDiario}', 'ResumenDiarioController@actionDescargarXml');

//TReciboVentaNotaCredito
Route::post('reciboventanotacredito/insertar', 'ReciboVentaNotaCreditoController@actionInsertar');
Route::get('reciboventanotacredito/descargarpdfxml/{codigoReciboVentaNotaCredito}', 'ReciboVentaNotaCreditoController@actionDescargarPdfXml');
Route::get('reciboventanotacredito/imprimircomprobante/{codigoReciboVentaNotaCredito}', 'ReciboVentaNotaCreditoController@actionImprimirComprobante');

//TReciboVentaNotaDebito
Route::post('reciboventanotadebito/insertar', 'ReciboVentaNotaDebitoController@actionInsertar');
Route::get('reciboventanotadebito/descargarpdfxml/{codigoReciboVentaNotaDebito}', 'ReciboVentaNotaDebitoController@actionDescargarPdfXml');
Route::get('reciboventanotadebito/imprimircomprobante/{codigoReciboVentaNotaDebito}', 'ReciboVentaNotaDebitoController@actionImprimirComprobante');

//TDocumentoGeneradoSunat
Route::get('documentogeneradosunat/ver/{pagina?}', 'DocumentoGeneradoSunatController@actionVer');

//TReporte
Route::get('reporte/index', 'ReporteController@actionIndex');
Route::post('reporte/ventas', 'ReporteController@actionVentas');
Route::post('reporte/ventasconsolidado', 'ReporteController@actionVentasConsolidado');
Route::post('reporte/ventaswef', 'ReporteController@actionVentasWef');
Route::post('reporte/ventaswefconsolidado', 'ReporteController@actionVentasWefConsolidado');
Route::post('reporte/compras', 'ReporteController@actionCompras');
Route::post('reporte/comprasconsolidado', 'ReporteController@actionComprasConsolidado');
Route::post('reporte/caja', 'ReporteController@actionCaja');
Route::post('reporte/productosalmacen', 'ReporteController@actionProductosAlmacen');
Route::post('reporte/productosoficina', 'ReporteController@actionProductosOficina');
Route::post('reporte/productosoficinaconsolidado', 'ReporteController@actionProductosOficinaConsolidado');
Route::post('reporte/productosoficinacompra', 'ReporteController@actionProductosOficinaCompra');
Route::post('reporte/notascredito', 'ReporteController@actionNotaCredito');
Route::post('reporte/notasdebito', 'ReporteController@actionNotaDebito');
Route::post('reporte/documentogeneradosunat', 'ReporteController@actionDocumentoGeneradoSunat');
Route::get('reporte/generalconsolidado', 'ReporteController@actionGeneralConsolidado');
Route::post('reporte/inventariogeneral', 'ReporteController@actionInventarioGeneral');

//TEgreso
Route::match(['get', 'post'], 'egreso/insertar', 'EgresoController@actionInsertar');
Route::get('egreso/ver/{pagina?}', 'EgresoController@actionVer');

//TCategoriaVenta
Route::match(['get', 'post'], 'categoriaventa/mantenimiento/{codigoCategoriaVenta?}', 'CategoriaVentaController@actionMantenimiento');
Route::post('categoriaventa/editar', 'CategoriaVentaController@actionEditar');
Route::get('categoriaventa/eliminar/{codigoCategoriaVenta}', 'CategoriaVentaController@actionEliminar');
Route::get('categoriaventa/habilitar/{codigoCategoriaVenta}', 'CategoriaVentaController@actionHabilitar');

//TExcepcion
Route::get('excepcion/ver/{pagina?}', 'ExcepcionController@actionVer');
Route::get('excepcion/cambiarestado/{codigoExcepcion}/{estado}', 'ExcepcionController@actionCambiarEstado');

//Consulta Externa
Route::post('consultaexterna/clienteexterno', 'ConsultaExternaController@actionClienteExterno');
Route::get('consultaexterna/comprobantesemitidos', 'ConsultaExternaController@actionComprobantesEmitidos');
Route::get('consultaexterna/descargarpdfxml/{codigoComprobante}/{formato?}', 'ConsultaExternaController@actionDescargarPdfXml');
Route::get('consultaexterna/imprimircomprobanteventasinfe/{codigoComprobante}/{formato?}', 'ConsultaExternaController@actionImprimirComprobanteVentaSinFe');
Route::get('consultaexterna/descargarpdfxmlnotacredito/{codigoComprobante}/{formato?}', 'ConsultaExternaController@actionDescargarPdfXmlNotaCredito');
Route::get('consultaexterna/descargarpdfxmlnotadebito/{codigoComprobante}/{formato?}', 'ConsultaExternaController@actionDescargarPdfXmlNotaDebito');
Route::get('consultaexterna/imprimircomprobanteventa/{codigoComprobante}/{formato?}', 'ConsultaExternaController@actionImprimirComprobanteVenta');
Route::get('consultaexterna/imprimircomprobantenotacredito/{codigoComprobante}/{formato?}', 'ConsultaExternaController@actionImprimirComprobanteNotaCredito');
Route::get('consultaexterna/imprimircomprobantenotadebito/{codigoComprobante}/{formato?}', 'ConsultaExternaController@actionImprimirComprobanteNotaDebito');

//TUsuarioNotificacion
Route::match(['get', 'post'], 'usuarionotificacion/insertar', 'UsuarioNotificacionController@actionInsertar');
Route::match(['get', 'post'], 'usuarionotificacion/editar', 'UsuarioNotificacionController@actionEditar');
Route::get('usuarionotificacion/ver/{pagina?}', 'UsuarioNotificacionController@actionVer');
Route::get('usuarionotificacion/ocultarnotificacion/{codigoUsuarioNotificacion?}', 'UsuarioNotificacionController@actionOcultarNotificacion');
Route::get('usuarionotificacion/marcartodoleido', 'UsuarioNotificacionController@actionMarcarTodoLeido');
Route::get('usuarionotificacion/verporcodigopersonal', 'UsuarioNotificacionController@actionVerPorCodigoPersonal');
Route::post('usuarionotificacion/detalle', 'UsuarioNotificacionController@actionDetalle');
Route::post('usuarionotificacion/marcarleido', 'UsuarioNotificacionController@actionMarcarLeido');
Route::post('usuarionotificacion/marcarleidojson', 'UsuarioNotificacionController@actionMarcarLeidoJSON');

//TAmbiente
Route::post('ambiente/insertarajax', 'AmbienteController@actionInsertarAjax');
Route::post('ambiente/jsonpornombrecodigo', 'AmbienteController@actionJsonPorNombreCodigo');
Route::post('ambiente/cargarambientes', 'AmbienteController@actionCargarAmbientes');

//TAmbienteEspacio
Route::post('ambienteespacio/insertarajax', 'AmbienteEspacioController@actionInsertarAjax');
Route::post('ambienteespacio/cargarespacios', 'AmbienteEspacioController@actionCargarEspacios');

//TInventario
Route::match(['get', 'post'], 'inventario/insertar', 'InventarioController@actionInsertar');
Route::get('inventario/ver/{pagina?}', 'InventarioController@actionVer');
Route::post('inventario/editar', 'InventarioController@actionEditar');
Route::get('inventario/eliminar/{codigoInventario}', 'InventarioController@actionEliminar');
?>