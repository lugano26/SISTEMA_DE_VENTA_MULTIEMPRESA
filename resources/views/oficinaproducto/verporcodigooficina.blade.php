@extends('template.layoutgeneral')
@section('titulo', 'Productos de oficina')
@section('subTitulo', 'Ver')
@section('cuerpoGeneral')
<div class="row">
	<div class="col-md-12">
		<div class="nav-tabs-custom">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#tab_1-1">Lista de productos de esta oficina</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="tab_1-1">
					<div class="row">
						<div class="col-md-12">
							<div>
								<div class="row">
									<div class="col-md-12">
										<form id="frmSearch" method="get" action="{{url('oficinaproducto/verporcodigooficina')}}" onsubmit="validarExpresion(event)">
											<div class="input-group input-group-sm">
												<input type="hidden" name="searchPerformance" id="searchPerformanceInput">
												<input id="textSearch" type="text" class="form-control" onkeyup="searchItem(event);" placeholder="Buscar por nombre, código de barras (Enter)" name="q" value="{{ !empty($q) ? $q : '' }}" autofocus>
												<span class="input-group-btn">
												<button type="buttom" class="btn btn-primary btn-flat"><i class="fa fa-search" ></i></button>
												</span>
											</div>
										</form>
									</div>
								</div>
								<hr>
								<div class="row">
									<div class="col-md-2">
										<label>Compras</label>
										<div>
											<span class="label label-danger" style="display: inline-block;font-size: 12px;width: 100%;">S/{{number_format($listaTOficinaProducto->sumaEstimacionCompra, 2, '.', ',')}}</span>
										</div>
									</div>
									<div class="col-md-2">
										<label>Imp. ventas</label>
										<div>
											<span class="label label-warning" style="display: inline-block;font-size: 12px;width: 100%;">S/{{number_format($listaTOficinaProducto->sumaEstimacionVentaTotal-$listaTOficinaProducto->sumaEstimacionVentaSubTotal, 2, '.', ',')}}</span>
										</div>
									</div>
									<div class="col-md-2">
										<label>Sub T. ventas</label>
										<div>
											<span class="label label-info" style="display: inline-block;font-size: 12px;width: 100%;">S/{{number_format($listaTOficinaProducto->sumaEstimacionVentaSubTotal, 2, '.', ',')}}</span>
										</div>
									</div>
									<div class="col-md-2">
										<label>Ventas T.</label>
										<div>
											<span class="label label-default" style="display: inline-block;font-size: 12px;width: 100%;">S/{{number_format($listaTOficinaProducto->sumaEstimacionVentaTotal, 2, '.', ',')}}</span>
										</div>
									</div>
									<div class="col-md-2">
										<label>Balance gener.</label>
										<div>
											<span class="label label-primary" style="display: inline-block;font-size: 12px;width: 100%;">S/{{number_format($listaTOficinaProducto->sumaEstimacionVentaTotal-$listaTOficinaProducto->sumaEstimacionCompra, 2, '.', ',')}}</span>
										</div>
									</div>
									<div class="col-md-2">
										<label>Balance neto</label>
										<div>
											<span class="label label-success" style="display: inline-block;font-size: 12px;width: 100%;">S/{{number_format($listaTOficinaProducto->sumaEstimacionVentaSubTotal-$listaTOficinaProducto->sumaEstimacionCompra, 2, '.', ',')}}</span>
										</div>
									</div>
								</div>
							</div>
							<hr>
							<div class="table-responsive">
								<table id="tableOficinaProducto" class="table table-striped" style="min-width: 777px;">
									<thead>
										<tr>
											<th class="text-center">Código barras</th>
											<th>Nombre</th>
											<th class="text-center">Und.</th>
											<th class="text-center">Pres.</th>
											<th class="text-center">Tipo</th>
											<th class="text-center">Sit. Imp.</th>
											<th class="text-center">Tipo Imp.</th>
											<th class="text-center">Cant.</th>
											<th class="text-center">Precio C. U.</th>
											<th class="text-center">Precio V. U.</th>
										</tr>
									</thead>
									<tbody>
										@foreach($listaTRegistrosPaginacion as $key => $value)
											<tr class="elementoBuscar">
												<td class="text-center">{{$value->codigoBarras}}</td>
												<td>{{$value->nombre}}</td>
												<td class="text-center">{{$value->unidadMedida}}</td>
												<td class="text-center">{{$value->presentacion}}</td>
												<td class="text-center">{{$value->tipo}}</td>
												<td class="text-center">{{$value->situacionImpuesto}}</td>
												<td class="text-center">{{$value->tipoImpuesto}} ({{$value->porcentajeTributacion}}%)</td>
												<td class="text-center">{{$value->cantidad}}</td>
												<td class="text-center">S/{{$value->precioCompraUnitario}}</td>
												<td class="text-center">S/{{$value->precioVentaUnitario}}</td>
											</tr>
										@endforeach
									</tbody>
								</table>
							</div>
						</div>
					</div>
					{!! $pagination !!}
				</div>
			</div>
		</div>
	</div>
</div>
<script src="{{asset('viewResources/oficinaproducto/verporcodigooficina.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>
@endsection