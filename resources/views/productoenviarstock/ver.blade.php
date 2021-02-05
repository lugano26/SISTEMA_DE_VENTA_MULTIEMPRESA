@extends('template.layoutgeneral')
@section('titulo', 'Traslado de almacén a oficina')
@section('subTitulo', 'Listado')
@section('cuerpoGeneral')
<div class="row">
	<div class="col-md-12">
		<div class="nav-tabs-custom">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#tab_1-1">Lista de traslados de almacén a oficina</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="tab_1-1">
					<div class="row">
						<div class="col-md-12">
							<div>
								<form id="frmSearch" method="get" action="{{url('productoenviarstock/ver')}}" onsubmit="validarExpresion(event)">
									<div class="input-group input-group-sm">
										<input type="hidden" name="searchPerformance" id="searchPerformanceInput">
										<input id="textSearch" type="text" class="form-control" onkeyup="searchItem(event);" placeholder="Buscar por almacén, oficina, nombre producto, código barras producto (Enter)" name="q" value="{{ !empty($q) ? $q : '' }}" autofocus>
											<span class="input-group-btn">
											<button type="submit" class="btn btn-primary btn-flat"><i class="fa fa-search" ></i></button>
											</span>
									</div>
								</form>
							</div>
							<hr>
							<div class="table-responsive">
								<table id="tableCompras" class="table table-striped" style="min-width: 777px;">
									<thead>
										<tr>
											<th>Almacén origen</th>
											<th>Oficina destino</th>
											<th class="text-center">Fecha translado</th>
											<th class="text-center">Estado</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										@foreach($listaTProductoEnviarStock as $value)
											<tr class="elementoBuscar">
												<td>{{$value->talmacen->descripcion}}</td>
												<td>{{$value->toficina->descripcion}}</td>
												<td class="text-center">{{ $value->created_at}}</td>
												<td class="text-center">
													<span class="label {{$value->estado ? 'label-success' : 'label-danger'}}">{{$value->estado ? 'Conforme' : 'Anulado'}}</span>
												</td>
											<td class="text-right">
												<span class="btn btn-default btn-xs glyphicon glyphicon-th-list" data-toggle="tooltip" data-placement="left" title="Ver detalles" onclick="dialogoAjax('dialogoGeneral', 'modal-lg', 'Detalle del traslado', { _token : '{{csrf_token()}}', codigoProductoEnviarStock : '{{$value->codigoProductoEnviarStock}}' }, '{{url('productoenviarstock/detalle')}}', 'POST', null, null, false, true);"></span>
												@if($value->estado)
													@if(Session::has('codigoAlmacen'))
														<span class="btn btn-default btn-xs glyphicon glyphicon-ban-circle" data-toggle="tooltip" data-placement="left" title="Anular" data-urlredirect="{{url('productoenviarstock/anular', $value->codigoProductoEnviarStock)}}" onclick="anularTraslado(this)"></span>
													@endif
													
													<span class="btn btn-default btn-xs glyphicon glyphicon-print" data-toggle="tooltip" data-placement="left" title="Imprimir comprobante" onclick="window.open('{{url('productoenviarstock/imprimircomprobante/'.$value->codigoProductoEnviarStock)}}', '_blank');"></span>
												@endif
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
<script src="{{asset('viewResources/productoenviarstock/ver.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>
@endsection