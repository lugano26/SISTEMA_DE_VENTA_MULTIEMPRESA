@extends('template.layoutgeneral')
@section('titulo', '')
@section('subTitulo', '')
@section('cuerpoGeneral')
<link rel="stylesheet" href="{{asset('viewResources/reporte/index.css?x='.env('CACHE_LAST_UPDATE'))}}">
<h2 class="page-header reportTitle"><i class="fa fa-cube"></i> Reportes de ventas</h2>
<div class="row">
	@if((Session::get('facturacionElectronica') && strpos(Session::get('rol'), 'Súper usuario')!==false) ||
	(Session::get('facturacionElectronica') && (((strpos(Session::get('rol'), 'Administrador')!==false) ||
	(strpos(Session::get('rol'), 'Ventas')!==false)) && strpos(Session::get('rol'), 'Reporteador')!==false)))
	<div class="col-lg-3 col-md-6">
		<div class="small-box bg-aqua-active" style="cursor: pointer" onclick="openModal(this)">
			<div class="inner">
				<h3>Ventas fe</h3>
				<p>Reporte detallado</p>
			</div>
			<div class="icon">
				<i class="fa fa-line-chart"></i>
			</div>
			<a data-openmodal="dialogoAjax('dialogoGeneral', 'modal-lg', 'Reporte de ventas fe detallado', { _token : '{{csrf_token()}}'}, '{{url('reporte/ventas')}}', 'POST', null, null, false, true);"
			 class="small-box-footer">
				Ver reporte&nbsp; <i class="fa fa-arrow-circle-right"></i>
			</a>
		</div>
	</div>
	@if((Session::get('facturacionElectronica') && strpos(Session::get('rol'), 'Súper usuario')!==false) ||
	(Session::get('facturacionElectronica') && (((strpos(Session::get('rol'), 'Administrador')!==false) ||
	(strpos(Session::get('rol'), 'Ventas')!==false)) && strpos(Session::get('rol'), 'Reporteador')!==false)))
	<div class="col-lg-3 col-md-6">
		<div class="small-box bg-aqua disabled" style="cursor: pointer;" onclick="openModal(this)">
			<div class="inner">
				<h3>Ventas fe</h3>
				<p>Reporte resumido</p>
			</div>
			<div class="icon">
				<i class="fa fa-bolt"></i>
			</div>
			<a data-openmodal="dialogoAjax('dialogoGeneral', 'modal-lg', 'Reporte de ventas fe resumido', { _token : '{{csrf_token()}}'}, '{{url('reporte/ventasconsolidado')}}', 'POST', null, null, false, true);"
			 class="small-box-footer">
				Ver reporte&nbsp; <i class="fa fa-arrow-circle-right"></i>
			</a>
		</div>
	</div>
	@endif
	@endif
	@if(strpos(Session::get('rol'), 'Súper usuario')!==false || (((strpos(Session::get('rol'), 'Administrador')!==false)
	|| (strpos(Session::get('rol'), 'Ventas')!==false)) && strpos(Session::get('rol'), 'Reporteador')!==false))
	<div class="col-lg-3 col-md-6">
		<div class="small-box bg-purple-active" style="cursor: pointer" onclick="openModal(this)">
			<div class="inner">
				<h3>Ventas wef</h3>
				<p>Reporte detallado</p>
			</div>
			<div class="icon">
				<i class="fa fa-pie-chart"></i>
			</div>
			<a data-openmodal="dialogoAjax('dialogoGeneral', 'modal-lg', 'Reporte de ventas wef detallado', { _token : '{{csrf_token()}}'}, '{{url('reporte/ventaswef')}}', 'POST', null, null, false, true);"
			 class="small-box-footer">
				Ver reporte&nbsp; <i class="fa fa-arrow-circle-right"></i>
			</a>
		</div>
	</div>
	@endif
	@if(strpos(Session::get('rol'), 'Súper usuario')!==false || (((strpos(Session::get('rol'), 'Administrador')!==false)
	|| (strpos(Session::get('rol'), 'Ventas')!==false)) && strpos(Session::get('rol'), 'Reporteador')!==false))
	<div class="col-lg-3 col-md-6">
		<div class="small-box bg-purple disabled" style="cursor: pointer" onclick="openModal(this)">
			<div class="inner">
				<h3>Ventas wef</h3>
				<p>Reporte resumido</p>
			</div>
			<div class="icon">
				<i class="fa fa-bolt"></i>
			</div>
			<a data-openmodal="dialogoAjax('dialogoGeneral', 'modal-lg', 'Reporte de ventas wef resumido', { _token : '{{csrf_token()}}'}, '{{url('reporte/ventaswefconsolidado')}}', 'POST', null, null, false, true);"
			 class="small-box-footer">
				Ver reporte&nbsp; <i class="fa fa-arrow-circle-right"></i>
			</a>
		</div>
	</div>
	@endif
</div>


@if((Session::get('facturacionElectronica') && strpos(Session::get('rol'), 'Súper usuario')!==false) ||
	(Session::get('facturacionElectronica') && (((strpos(Session::get('rol'), 'Administrador')!==false) ||
	(strpos(Session::get('rol'), 'Ventas')!==false)) && strpos(Session::get('rol'), 'Reporteador')!==false)))
<h2 class="page-header reportTitle"><i class="fa fa-cube"></i> Reportes de notas de crédito y débito</h2>
<div class="row">
	<div class="col-lg-3 col-md-6">
		<div class="small-box bg-blue disabled" style="cursor: pointer" onclick="openModal(this)">
			<div class="inner">
				<h3>Notas de crédito</h3>
				<p>Reporte resumido</p>
			</div>
			<div class="icon">
				<i class="fa fa-filter"></i>
			</div>
			<a data-openmodal="dialogoAjax('dialogoGeneral', 'modal-lg', 'Reporte de notas de crédito resumido', { _token : '{{csrf_token()}}'}, '{{url('reporte/notascredito')}}', 'POST', null, null, false, true);"
			 class="small-box-footer">
				Ver reporte&nbsp; <i class="fa fa-arrow-circle-right"></i>
			</a>
		</div>
	</div>
	<div class="col-lg-3 col-md-6">
		<div class="small-box bg-green" style="cursor: pointer" onclick="openModal(this)">
			<div class="inner">
				<h3>Notas de débito</h3>
				<p>Reporte resumido</p>
			</div>
			<div class="icon">
				<i class="fa fa-fire"></i>
			</div>
			<a data-openmodal="dialogoAjax('dialogoGeneral', 'modal-lg', 'Reporte de notas de débito resumido', { _token : '{{csrf_token()}}'}, '{{url('reporte/notasdebito')}}', 'POST', null, null, false, true);"
			 class="small-box-footer">
				Ver reporte&nbsp; <i class="fa fa-arrow-circle-right"></i>
			</a>
		</div>
	</div>	
</div>
@endif

@if(strpos(Session::get('rol'), 'Súper usuario')!==false || (((strpos(Session::get('rol'), 'Administrador')!==false)
|| (strpos(Session::get('rol'), 'Almacenero')!==false)) && strpos(Session::get('rol'), 'Reporteador')!==false))
<h2 class="page-header reportTitle"><i class="fa fa-cube"></i> Reportes de compras</h2>
<div class="row">
	<div class="col-lg-3 col-md-6">
		<div class="small-box bg-red-active" style="cursor: pointer" onclick="openModal(this)">
			<div class="inner">
				<h3>Compras</h3>
				<p>Reporte detallado</p>
			</div>
			<div class="icon">
				<i class="fa fa-shopping-cart"></i>
			</div>
			<a data-openmodal="dialogoAjax('dialogoGeneral', 'modal-lg', 'Reporte de compras', { _token : '{{csrf_token()}}'}, '{{url('reporte/compras')}}', 'POST', null, null, false, true);"
			 class="small-box-footer">
				Ver reporte&nbsp; <i class="fa fa-arrow-circle-right"></i>
			</a>
		</div>
	</div>
	
	<div class="col-lg-3 col-md-6">
		<div class="small-box bg-red" style="cursor: pointer" onclick="openModal(this)">
			<div class="inner">
				<h3>Compras</h3>
				<p>Reporte resumido</p>
			</div>
			<div class="icon">
				<i class="fa fa-bolt"></i>
			</div>
			<a data-openmodal="dialogoAjax('dialogoGeneral', 'modal-lg', 'Reporte de compras resumido', { _token : '{{csrf_token()}}'}, '{{url('reporte/comprasconsolidado')}}', 'POST', null, null, false, true);"
			 class="small-box-footer">
				Ver reporte&nbsp; <i class="fa fa-arrow-circle-right"></i>
			</a>
		</div>
	</div>
</div>
@endif

@if(strpos(Session::get('rol'), 'Súper usuario')!==false || (((strpos(Session::get('rol'), 'Administrador')!==false))
	&& strpos(Session::get('rol'), 'Reporteador')!==false))
<h2 class="page-header reportTitle"><i class="fa fa-cube"></i> Reportes de productos</h2>
<div class="row">
	
	<div class="col-lg-3 col-md-6">
		<div class="small-box bg-orange-active" style="cursor: pointer" onclick="openModal(this)">
			<div class="inner">
				<h3>Almacén</h3>
				<p>Productos por almacén</p>
			</div>
			<div class="icon">
				<i class="fa fa-building-o"></i>
			</div>
			<a data-openmodal="dialogoAjax('dialogoGeneral', 'modal-lg', 'Reporte de productos por almacén', { _token : '{{csrf_token()}}'}, '{{url('reporte/productosalmacen')}}', 'POST', null, null, false, true);"
			 class="small-box-footer">
				Ver reporte&nbsp; <i class="fa fa-arrow-circle-right"></i>
			</a>
		</div>
	</div>
	
	<div class="col-lg-3 col-md-6">
		<div class="small-box bg-yellow" style="cursor: pointer" onclick="openModal(this)">
			<div class="inner">
				<h3>Oficina</h3>
				<p>Productos por oficina</p>
			</div>
			<div class="icon">
				<i class="fa fa-street-view"></i>
			</div>
			<a data-openmodal="dialogoAjax('dialogoGeneral', 'modal-lg', 'Reporte de productos por oficina', { _token : '{{csrf_token()}}'}, '{{url('reporte/productosoficina')}}', 'POST', null, null, false, true);"
			 class="small-box-footer">
				Ver reporte&nbsp; <i class="fa fa-arrow-circle-right"></i>
			</a>
		</div>
	</div>
	
	<div class="col-lg-3 col-md-6">
		<div class="small-box bg-yellow" style="cursor: pointer" onclick="openModal(this)">
			<div class="inner">
				<h3>Oficina</h3>
				<p>Productos consolidado</p>
			</div>
			<div class="icon">
				<i class="fa fa-sort-amount-desc"></i>
			</div>
			<a data-openmodal="dialogoAjax('dialogoGeneral', 'modal-lg', 'Reporte de productos por oficina consolidado', { _token : '{{csrf_token()}}'}, '{{url('reporte/productosoficinaconsolidado')}}', 'POST', null, null, false, true);"
			 class="small-box-footer">
				Ver reporte&nbsp; <i class="fa fa-arrow-circle-right"></i>
			</a>
		</div>
	</div>
	
	<div class="col-lg-3 col-md-6">
		<div class="small-box bg-yellow" style="cursor: pointer" onclick="openModal(this)">
			<div class="inner">
				<h3>Oficina</h3>
				<p>Productos con info. de compra</p>
			</div>
			<div class="icon">
				<i class="fa fa-server"></i>
			</div>
			<a data-openmodal="dialogoAjax('dialogoGeneral', 'modal-lg', 'Reporte de productos por oficina con info de compra', { _token : '{{csrf_token()}}'}, '{{url('reporte/productosoficinacompra')}}', 'POST', null, null, false, true);"
			 class="small-box-footer">
				Ver reporte&nbsp; <i class="fa fa-arrow-circle-right"></i>
			</a>
		</div>
	</div>
</div>
@endif

@if(strpos(Session::get('rol'), 'Súper usuario')!==false || (((strpos(Session::get('rol'), 'Administrador')!==false))
	&& strpos(Session::get('rol'), 'Reporteador')!==false))
<h2 class="page-header reportTitle"><i class="fa fa-cubes"></i> Reportes de inventario</h2>
<div class="row">	
	<div class="col-lg-3 col-md-6">
		<div class="small-box bg-light-blue-active" style="cursor: pointer" onclick="openModal(this)">
			<div class="inner">
				<h3>Inventario</h3>
				<p>Reporte de items</p>
			</div>
			<div class="icon">
				<i class="fa fa-server"></i>
			</div>
			<a data-openmodal="dialogoAjax('dialogoGeneral', 'modal-lg', 'Reporte de inventario', { _token : '{{csrf_token()}}'}, '{{url('reporte/inventariogeneral')}}', 'POST', null, null, false, true);"
			 class="small-box-footer">
				Ver reporte&nbsp; <i class="fa fa-arrow-circle-right"></i>
			</a>
		</div>
	</div>
</div>
@endif

@if(strpos(Session::get('rol'), 'Súper usuario')!==false || (((strpos(Session::get('rol'), 'Administrador')!==false))
	&& strpos(Session::get('rol'), 'Reporteador')!==false))
<h2 class="page-header reportTitle"><i class="fa fa-cube"></i> Reportes de caja</h2>
<div class="row">	
	<div class="col-lg-3 col-md-6">
		<div class="small-box bg-maroon-active" style="cursor: pointer" onclick="openModal(this)">
			<div class="inner">
				<h3>Caja</h3>
				<p>Reporte de caja</p>
			</div>
			<div class="icon">
				<i class="fa fa-calculator"></i>
			</div>
			<a data-openmodal="dialogoAjax('dialogoGeneral', 'modal-lg', 'Reporte de caja', { _token : '{{csrf_token()}}'}, '{{url('reporte/caja')}}', 'POST', null, null, false, true);"
			 class="small-box-footer">
				Ver reporte&nbsp; <i class="fa fa-arrow-circle-right"></i>
			</a>
		</div>
	</div>
</div>
@endif

@if(( strpos(Session::get('rol'), 'Súper usuario')!==false) ||
	( (((strpos(Session::get('rol'), 'Administrador')!==false) ||
	(strpos(Session::get('rol'), 'Ventas')!==false)) && strpos(Session::get('rol'), 'Reporteador')!==false)))
<h2 class="page-header reportTitle"><i class="fa fa-cube"></i> Reportes consolidados</h2>
<div class="row">	
	<div class="col-lg-3 col-md-6">
		<div class="small-box bg-navy" style="cursor: pointer" onclick="openModal(this)">
			<div class="inner">
				<h3>Consolidado</h3>
				<p>General diario</p>
			</div>
			<div class="icon">
				<i class="fa fa-list-alt"></i>
			</div>
			<a data-openmodal="dialogoAjax('dialogoGeneral', 'modal-lg', 'Reporte consolidado general diario', { _token : '{{csrf_token()}}'}, '{{url('reporte/generalconsolidado')}}', 'GET', null, null, false, true);"
			 class="small-box-footer">
				Ver reporte&nbsp; <i class="fa fa-arrow-circle-right"></i>
			</a>
		</div>
	</div>
</div>
@endif

@if((Session::get('facturacionElectronica') && strpos(Session::get('rol'), 'Súper usuario')!==false) ||
	(Session::get('facturacionElectronica') && (((strpos(Session::get('rol'), 'Administrador')!==false) ||
	(strpos(Session::get('rol'), 'Ventas')!==false)) && strpos(Session::get('rol'), 'Reporteador')!==false)))
<h2 class="page-header reportTitle"><i class="fa fa-cube"></i> Reportes de documentos generados</h2>
<div class="row">	
	<div class="col-lg-3 col-md-6">
		<div class="small-box bg-teal-gradient" style="cursor: pointer" onclick="openModal(this)">
			<div class="inner">
				<h3>SUNAT</h3>
				<p>Reporte de documentos</p>
			</div>
			<div class="icon">
				<i class="fa fa-database"></i>
			</div>
			<a data-openmodal="dialogoAjax('dialogoGeneral', 'modal-lg', 'Reporte de documentos SUNAT', { _token : '{{csrf_token()}}'}, '{{url('reporte/documentogeneradosunat')}}', 'POST', null, null, false, true);"
			 class="small-box-footer">
				Ver reporte&nbsp; <i class="fa fa-arrow-circle-right"></i>
			</a>
		</div>
	</div>
</div>
@endif
<script src="{{asset('viewResources/reporte/index.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>
@endsection