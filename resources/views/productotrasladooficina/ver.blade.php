@extends('template.layoutgeneral')
@section('titulo', 'Traslado de oficina a oficina')
@section('subTitulo', 'Listado')
@section('cuerpoGeneral')
<div class="row">
	<div class="col-md-12">
		<div class="nav-tabs-custom">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#tab_1-1">Lista de traslados de oficina a oficina</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="tab_1-1">
					<div class="row">
						<div class="col-md-12">
							<div>
								<form id="frmSearch" method="get" action="{{url('productotrasladooficina/ver')}}" onsubmit="validarExpresion(event)">
									<div class="input-group input-group-sm">
										<input type="hidden" name="searchPerformance" id="searchPerformanceInput">
										<input id="textSearch" onkeyup="searchItem(event);" type="text" class="form-control" placeholder="Buscar por oficina origen o destino, nombre producto, código barras producto (Enter)" name="q" value="{{ !empty($q) ? $q : '' }}" autofocus>
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
											<th>Oficina origen</th>
											<th>Oficina destino</th>
											<th class="text-center">Fecha translado</th>
											<th class="text-center">Estado</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										@foreach($listaTProductoTrasladoOficina as $value)
											<tr class="elementoBuscar">
												<td>{{$value->toficina->descripcion}}</td>
												<td>{{$value->toficinaLlegada->descripcion}}</td>
												<td class="text-center">{{$value->created_at}}</td>
												<td class="text-center">
													<span class="label {{$value->estado ? 'label-success' : 'label-danger'}}">{{$value->estado ? 'Conforme' : 'Anulado'}}</span>
												</td>
											<td class="text-right">
												<span class="btn btn-default btn-xs glyphicon glyphicon-th-list" data-toggle="tooltip" data-placement="left" title="Ver detalles" onclick="dialogoAjax('dialogoGeneral', 'modal-lg', 'Detalle del traslado', { _token : '{{csrf_token()}}', codigoProductoTrasladoOficina : '{{$value->codigoProductoTrasladoOficina}}' }, '{{url('productotrasladooficina/detalle')}}', 'POST', null, null, false, true);"></span>
												@if($value->estado)
													@if(Session::has('codigoOficina'))
														<span class="btn btn-default btn-xs glyphicon glyphicon-ban-circle" data-toggle="tooltip" data-placement="left" title="Anular" data-urlredirect="{{url('productotrasladooficina/anular', $value->codigoProductoTrasladoOficina)}}" onclick="anularTraslado(this)"></span>
													@endif
												@endif
												<span class="btn btn-default btn-xs glyphicon glyphicon-print" data-toggle="tooltip" data-placement="left" title="Imprimir comprobante" onclick="window.open('{{url('productotrasladooficina/imprimircomprobante/'.$value->codigoProductoTrasladoOficina)}}', '_blank');"></span>
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
<script src="{{asset('viewResources/productotrasladooficina/ver.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>
@endsection