@extends('template.layoutgeneral')
@section('titulo', 'Lista de productos retirados')
@section('subTitulo', 'de oficina')
@section('cuerpoGeneral')
<div class="row">
	<div class="col-md-12">
		<div class="nav-tabs-custom">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#tab_1-1">Lista de productos retirados</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="tab_1-1">
					<div class="row">
						<div class="col-md-12">
							<div>
								<form  id="frmSearch"  method="get" action="{{url('oficinaproductoretiro/ver')}}" onsubmit="validarExpresion(event)">
									<div class="input-group input-group-sm">
										<input type="hidden" name="searchPerformance" id="searchPerformanceInput">
										<input id="textSearch" type="text" class="form-control" onkeyup="searchItem(event);" placeholder="Buscar por oficina, producto, descripción (Enter)" name="q" value="{{ !empty($q) ? $q : '' }}" autofocus>
											<span class="input-group-btn">
											<button type="submit" class="btn btn-primary btn-flat"><i class="fa fa-search" ></i></button>
											</span>
									</div>
								</form>
							</div>
							<hr>
							<div class="table-responsive">
								<table id="tableProductosRetirados" class="table table-striped" style="min-width: 777px;">
									<thead>
										<tr>
											<th>Oficina</th>
											<th>Producto</th>
											<th class="text-center">Tipo</th>
											<th class="text-center">Cantidad</th>
											<th>Descripción del retiro</th>
											<th class="text-center">Monto perdido</th>
											<th class="text-center">F. Retiro</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										@foreach($listaTOficinaProductoRetiro as $value)
											<tr class="elementoBuscar">
												<td>{{$value->descripcionOficina}}</td>
												<td>{{$value->nombreCompletoProducto}}</td>
												<td class="text-center">{{$value->tipoProducto}}</td>
												<td class="text-center">{{$value->cantidadUnidad}}</td>
												<td>{{$value->descripcion}}</td>
												<td class="text-center">S/{{$value->montoPerdido}}</td>
												<td class="text-center">{{$value->created_at}}</td>
												<td class="text-right">
													<span class="btn btn-default btn-xs glyphicon glyphicon-th-list" data-toggle="tooltip" data-placement="left" title="Ver detalles" onclick="dialogoAjax('dialogoGeneral', 'modal-lg', 'Detalle del retiro', { _token : '{{csrf_token()}}', codigoOficinaProductoRetiro : '{{$value->codigoOficinaProductoRetiro}}' }, '{{url('oficinaproductoretiro/detalle')}}', 'POST', null, null, false, true);"></span>
												</td>
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
<script src="{{asset('viewResources/oficinaproductoretiro/ver.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>
@endsection