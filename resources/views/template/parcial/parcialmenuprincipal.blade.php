<!-- sidebar menu: : style can be found in sidebar.less -->
<ul class="sidebar-menu" data-widget="tree">
	<li class="header">{{Session::get('razonSocialEmpresa')}}</li>
	@if(strpos(Session::get('rol'), 'Súper usuario')!==false || strpos(Session::get('rol'), 'Administrador')!==false || strpos(Session::get('rol'), 'Almacenero')!==false || strpos(Session::get('rol'), 'Ventas')!==false || strpos(Session::get('rol'), 'Reporteador')!==false)
		<li id="liMenuPanelControl" class="treeview">
			<a href="#">
				<i class="fa fa-dashboard"></i> <span>Panel de control</span>
				<span class="pull-right-container">
					<i class="fa fa-angle-left pull-right"></i>
				</span>
			</a>
			<ul class="treeview-menu">
				<li id="liMenuItemPanelControlInicio"><a href="{{url('/')}}"><i class="fa fa-circle-o"></i> Inicio</a></li>
				<li id="liMenuItemPanelControlManualesUsuario"><a href="{{url('general/manualesusuario')}}"><i class="fa fa-circle-o"></i> Manuales de usuario</a></li>
				@if(strpos(Session::get('rol'), 'Súper usuario')!==false)
					<li id="liMenuItemPanelControlRegistrarCliente"><a href="{{url('general/configuracionglobal')}}"><i class="fa fa-circle-o"></i> Registrar cliente</a></li>
					<li id="liMenuItemPanelControlListarEmpresas"><a href="{{url('empresa/ver')}}"><i class="fa fa-circle-o"></i> Listar clientes</a></li>
				@endif
				@if(strpos(Session::get('rol'), 'Súper usuario')!==false || strpos(Session::get('rol'), 'Administrador')!==false)
					<li id="liMenuItemPanelControlListarDeudasEmpresa"><a href="{{url('empresadeuda/gestionar/'.Session::get('codigoEmpresa'))}}"><i class="fa fa-circle-o"></i> Pagos servicio SYSEF</a></li>
				@endif
				@if(strpos(Session::get('rol'), 'Súper usuario')!==false)
					<li id="liMenuItemNotificacion" class="treeview">
						<a href="#">
							<i class="fa fa-circle-o"></i> Notificaciones
							<span class="pull-right-container">
								<i class="fa fa-angle-left pull-right"></i>
							</span>
						</a>
						<ul class="treeview-menu">
							<li id="liMenuItemRegistrarNotificacion"><a href="{{url('usuarionotificacion/insertar')}}"><i class="fa fa-circle-o"></i> Registrar notif.</a></li>
							<li id="liMenuItemVerNotificacion"><a href="{{url('usuarionotificacion/ver')}}"><i class="fa fa-circle-o"></i> Listar notif.</a></li>
						</ul>
					</li>
				@endif
				@if(Session::get('facturacionElectronica') && (strpos(Session::get('rol'), 'Súper usuario')!==false || strpos(Session::get('rol'), 'Administrador')!==false))					
					<li id="liMenuItemPanelControlListarDocumentosGeneradosSunat"><a href="{{url('documentogeneradosunat/ver')}}"><i class="fa fa-circle-o"></i> Documentos SUNAT</a></li>
				@endif
				@if(strpos(Session::get('rol'), 'Súper usuario')!==false)
					<li id="liMenuItemPanelControlBackupDatos"><a href="#" onclick="confirmacion(function(){ window.location.href='{{url('general/databackup')}}'; });"><i class="fa fa-circle-o"></i> Backup de datos</a></li>
					<li id="liMenuItemPanelControlListarExcepciones"><a href="{{url('excepcion/ver')}}"><i class="fa fa-circle-o"></i> Listar excepciones</a></li>
				@endif
			</ul>
		</li>
	@endif
	@if(strpos(Session::get('rol'), 'Súper usuario')!==false || strpos(Session::get('rol'), 'Administrador')!==false)
		<li id="liMenuGestionPersonal" class="treeview">
			<a href="#">
				<i class="fa fa-user"></i> <span>Gestión de personal</span>
				<span class="pull-right-container">
					<i class="fa fa-angle-left pull-right"></i>
				</span>
			</a>
			<ul class="treeview-menu">
				@if(strpos(Session::get('rol'), 'Súper usuario')!==false)
					<li id="liMenuItemGestionPersonalRegistrarPersonal"><a href="{{url('personal/insertar')}}"><i class="fa fa-circle-o"></i> Registrar personal</a></li>
				@endif
				<li id="liMenuItemGestionPersonalListarPersonal"><a href="{{url('personal/ver')}}"><i class="fa fa-circle-o"></i> Listar personal</a></li>
			</ul>
		</li>
	@endif
	@if(strpos(Session::get('rol'), 'Súper usuario')!==false || strpos(Session::get('rol'), 'Administrador')!==false)
		<li id="liMenuGestionLocales" class="treeview">
			<a href="#">
				<i class="fa fa-home"></i> <span>Gestión de locales</span>
				<span class="pull-right-container">
					<i class="fa fa-angle-left pull-right"></i>
				</span>
			</a>
			<ul class="treeview-menu">
				@if(strpos(Session::get('rol'), 'Súper usuario')!==false)
					<li id="liMenuItemGestionLocalesRegistrarOficina"><a href="{{url('oficina/insertar')}}"><i class="fa fa-circle-o"></i> Registrar oficina</a></li>
				@endif
				<li id="liMenuItemGestionLocalesListarOficinas"><a href="{{url('oficina/ver')}}"><i class="fa fa-circle-o"></i> Listar oficinas</a></li>
				@if(strpos(Session::get('rol'), 'Súper usuario')!==false)
					<li id="liMenuItemGestionLocalesRegistrarAlmacen"><a href="{{url('almacen/insertar')}}"><i class="fa fa-circle-o"></i> Registrar almacén</a></li>
				@endif
				<li id="liMenuItemGestionLocalesListarAlmacenes"><a href="{{url('almacen/ver')}}"><i class="fa fa-circle-o"></i> Listar almacenes</a></li>
			</ul>
		</li>
	@endif	
	@if((strpos(Session::get('rol'), 'Súper usuario')!==false || strpos(Session::get('rol'), 'Administrador')!==false || strpos(Session::get('rol'), 'Ventas')!==false || strpos(Session::get('rol'), 'Revocador')!==false) && Session::has('codigoOficina'))
		<li id="liMenuGestionVentas" class="treeview">
			<a href="#">
				<i class="fa fa-tags"></i> <span>Gestión de ventas</span>
				<span class="pull-right-container">
					<i class="fa fa-angle-left pull-right"></i>
				</span>
			</a>
			<ul class="treeview-menu">
				@if(strpos(Session::get('rol'), 'Súper usuario')!==false || strpos(Session::get('rol'), 'Administrador')!==false)
					<li id="liMenuItemGestionVentasCategorizacionVenta"><a href="{{url('categoriaventa/mantenimiento')}}"><i class="fa fa-circle-o"></i> Categoría de ventas</a></li>
				@endif
				@if(Session::get('facturacionElectronica'))
					@if(strpos(Session::get('rol'), 'Súper usuario')!==false || strpos(Session::get('rol'), 'Administrador')!==false || strpos(Session::get('rol'), 'Ventas')!==false)
						<li id="liMenuItemGestionVentasRegistrarVenta"><a href="{{url('reciboventa/insertar')}}"><i class="fa fa-circle-o"></i> Registrar venta fe</a></li>
					@endif
					@if(strpos(Session::get('rol'), 'Súper usuario')!==false || strpos(Session::get('rol'), 'Administrador')!==false || strpos(Session::get('rol'), 'Ventas')!==false || strpos(Session::get('rol'), 'Revocador')!==false)
						<li id="liMenuItemGestionVentasListarVentas"><a href="{{url('reciboventa/ver')}}"><i class="fa fa-circle-o"></i> Listar ventas fe</a></li>
					@endif
					@if(strpos(Session::get('rol'), 'Súper usuario')!==false || strpos(Session::get('rol'), 'Administrador')!==false)
						<li id="liMenuItemGestionVentasResumenDiario"><a href="{{url('resumendiario/gestionar')}}"><i class="fa fa-circle-o"></i> Resumen diario</a></li>
					@endif
				@endif
				@if(strpos(Session::get('rol'), 'Súper usuario')!==false || strpos(Session::get('rol'), 'Administrador')!==false || strpos(Session::get('rol'), 'Ventas')!==false)
					<li id="liMenuItemGestionVentasRegistrarVentaSinFe"><a href="{{url('reciboventa/insertarsinfe')}}"><i class="fa fa-circle-o"></i> Registrar venta wef</a></li>
				@endif
				@if(strpos(Session::get('rol'), 'Súper usuario')!==false || strpos(Session::get('rol'), 'Administrador')!==false || strpos(Session::get('rol'), 'Ventas')!==false || strpos(Session::get('rol'), 'Revocador')!==false)
					<li id="liMenuItemGestionVentasListarVentasSinFe"><a href="{{url('reciboventa/listasinfe')}}"><i class="fa fa-circle-o"></i> Listar ventas wef</a></li>
				@endif
			</ul>
		</li>
	@endif
	@if((strpos(Session::get('rol'), 'Súper usuario')!==false || strpos(Session::get('rol'), 'Almacenero')!==false) && Session::has('codigoAlmacen'))
		<li id="liMenuGestionCompras" class="treeview">
			<a href="#">
				<i class="fa fa-shopping-cart"></i> <span>Gestión de compras</span>
				<span class="pull-right-container">
					<i class="fa fa-angle-left pull-right"></i>
				</span>
			</a>
			<ul class="treeview-menu">
				<li id="liMenuItemGestionComprasRegistrarCompra"><a href="{{url('recibocompra/insertar')}}"><i class="fa fa-circle-o"></i> Registrar compra</a></li>
				<li id="liMenuItemGestionComprasListarCompras"><a href="{{url('recibocompra/ver')}}"><i class="fa fa-circle-o"></i> Listar compras</a></li>
			</ul>
		</li>
	@endif
	@if((strpos(Session::get('rol'), 'Súper usuario')!==false || strpos(Session::get('rol'), 'Administrador')!==false))
		<li id="liMenuGestionProductos" class="treeview">
			<a href="#">
				<i class="fa fa-list-alt"></i> <span>Gestión de productos</span>
				<span class="pull-right-container">
					<i class="fa fa-angle-left pull-right"></i>
				</span>
			</a>
			<ul class="treeview-menu">
				<li id="liMenuItemGestionProductosListarProductosAgrupado"><a href="{{url('almacenproducto/veragrupado')}}"><i class="fa fa-circle-o"></i> Todo los productos</a></li>
				@if(Session::has('codigoOficina'))
					<li id="liMenuItemGestionProductosListarProductosOficina"><a href="{{url('oficinaproducto/verporcodigooficina')}}"><i class="fa fa-circle-o"></i> Productos de oficina</a></li>
				@endif
				@if(Session::has('codigoAlmacen'))
					<li id="liMenuItemGestionProductosListarProductosAlmacen"><a href="{{url('almacenproducto/verporcodigoalmacen')}}"><i class="fa fa-circle-o"></i> Productos de almacén</a></li>
				@endif
				<li id="liMenuItemGestionProductosRetiroProductoAlmacen"><a href="{{url('almacenproductoretiro/insertar')}}"><i class="fa fa-circle-o"></i> Retirar de almacén</a></li>
				<li id="liMenuItemGestionProductosListarRetiroProductoAlmacen"><a href="{{url('almacenproductoretiro/ver')}}"><i class="fa fa-circle-o"></i> Lista de retiros de alm.</a></li>
				<li id="liMenuItemGestionProductosRetiroProductoOficina"><a href="{{url('oficinaproductoretiro/insertar')}}"><i class="fa fa-circle-o"></i> Retirar de oficina</a></li>
				<li id="liMenuItemGestionProductosListarRetiroProductoOficina"><a href="{{url('oficinaproductoretiro/ver')}}"><i class="fa fa-circle-o"></i> Lista de retiros de ofi.</a></li>
			</ul>
		</li>
	@endif
	@if((strpos(Session::get('rol'), 'Súper usuario')!==false || strpos(Session::get('rol'), 'Administrador')!==false || strpos(Session::get('rol'), 'Almacenero')!==false))
		<li id="liMenuGestionTraslados" class="treeview">
			<a href="#">
				<i class="fa fa-truck"></i> <span>Gestión de traslados</span>
				<span class="pull-right-container">
					<i class="fa fa-angle-left pull-right"></i>
				</span>
			</a>
			<ul class="treeview-menu">
				<li id="liMenuItemGestionTransladoAlmacenOficina"><a href="{{url('productoenviarstock/insertar')}}"><i class="fa fa-circle-o"></i> Almacén a oficina</a></li>
				<li id="liMenuItemGestionListarTransladoAlmacenOficina"><a href="{{url('productoenviarstock/ver')}}"><i class="fa fa-circle-o"></i> Lista almacén a oficina</a></li>
				@if(strpos(Session::get('rol'), 'Súper usuario')!==false || strpos(Session::get('rol'), 'Administrador')!==false)
					<li id="liMenuItemGestionTransladoOficinaOficina"><a href="{{url('productotrasladooficina/insertar')}}"><i class="fa fa-circle-o"></i> Entre oficinas</a></li>
					<li id="liMenuItemGestionListarTransladoOficinaOficina"><a href="{{url('productotrasladooficina/ver')}}"><i class="fa fa-circle-o"></i> Lista entre oficinas</a></li>
				@endif
			</ul>
		</li>
	@endif
	@if((strpos(Session::get('rol'), 'Súper usuario')!==false || strpos(Session::get('rol'), 'Administrador')!==false || strpos(Session::get('rol'), 'Inventariador')!==false))
		<li id="liMenuInventario" class="treeview">
			<a href="#">
				<i class="fa fa-cubes"></i> <span>Inventario</span>
				<span class="pull-right-container">
					<i class="fa fa-angle-left pull-right"></i>
				</span>
			</a>
			<ul class="treeview-menu">
				<li id="liMenuInventarioInsertar"><a href="{{url('inventario/insertar')}}"><i class="fa fa-circle-o"></i> Registrar inventario</a></li>
				<li id="liMenuInventarioVer"><a href="{{url('inventario/ver')}}"><i class="fa fa-circle-o"></i> Listar inventario</a></li>
			</ul>
		</li>
	@endif
	@if(Session::has('codigoOficina'))
		@if(strpos(Session::get('rol'), 'Súper usuario')!==false || strpos(Session::get('rol'), 'Administrador')!==false)
			<li id="liMenuOperaciones" class="treeview">
				<a href="#">
					<i class="fa fa-tasks"></i> <span>Otras operaciones</span>
					<span class="pull-right-container">
						<i class="fa fa-angle-left pull-right"></i>
					</span>
				</a>
				<ul class="treeview-menu">
					<li id="liMenuItemEgreso" class="treeview">
						<a href="#">
							<i class="fa fa-sort-numeric-asc"></i> Egresos
							<span class="pull-right-container">
								<i class="fa fa-angle-left pull-right"></i>
							</span>
						</a>
						<ul class="treeview-menu">
							<li id="liMenuItemRegistrarEgreso"><a href="{{url('egreso/insertar')}}"><i class="fa fa-circle-o"></i> Registrar egreso</a></li>
							<li id="liMenuItemVerEgreso"><a href="{{url('egreso/ver')}}"><i class="fa fa-circle-o"></i> Listar egresos</a></li>
						</ul>
					</li>
				</ul>
			</li>
		@endif
	@endif
	@if(strpos(Session::get('rol'), 'Súper usuario')!==false || strpos(Session::get('rol'), 'Reporteador')!==false)
		<li id="liMenuReportes">
			<a href="{{url('reporte/index')}}"><i class="fa fa-book"></i> <span>Reportes</span></a>
		</li>
	@endif
</ul>
<script>
	@if(Session::has('menuItemPadreSelected')) 
		$('#{{Session::get('menuItemPadreSelected')}}').addClass('active');
	@endif
	@if(Session::has('menuItemHijoSelected')) 
		$('#{{Session::get('menuItemHijoSelected')}}').addClass('active');
	@endif
	@if(Session::has('menuItemSubHijoSelected')) 
		$('#{{Session::get('menuItemSubHijoSelected')}}').addClass('active');
	@endif
</script>