@extends('template.layoutgeneral')
@section('titulo', 'Compra')
@section('subTitulo', 'Listado')
@section('cuerpoGeneral')
<div class="row">
	<div class="col-md-12">
		<div class="nav-tabs-custom">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#tab_1-1">Lista de compras</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="tab_1-1">
					<div class="row">
						<div class="col-md-12">
							<div>
								<form id="frmSearch" method="get" action="{{url('recibocompra/ver')}}" onsubmit="validarExpresion(event)">
									<div class="input-group input-group-sm">
										<input type="hidden" name="searchPerformance" id="searchPerformanceInput">
										<input id="textSearch" type="text" class="form-control" onkeyup="searchItem(event);" placeholder="Buscar por proveedor, tipo comprobante, guía de remisión, tipo pago, nombre producto, código barras producto (Enter)" name="q" value="{{ !empty($q) ? $q : '' }}" autofocus>
										<span class="input-group-btn">
										<button type="buttom" class="btn btn-primary btn-flat"><i class="fa fa-search" ></i></button>
										</span>
									</div>
								</form>
							</div>
							<hr>
							<div class="table-responsive">
								<table id="tableCompras" class="table table-striped" style="min-width: 777px;">
									<thead>
										<tr>
											<th>Proveedor</th>
											<th class="text-center">Comprobante</th>
											<th class="text-center">Guía de R.</th>
											<th class="text-center">Fecha a pagar</th>
											<th class="text-center">Total</th>
											<th class="text-center">Pago</th>
											<th></th>
											<th class="text-center">Estado</th>
											<th class="text-center">Fecha registro</th>
											<th class="text-center"></th>
										</tr>
									</thead>
									<tbody>
										@foreach($listaTReciboCompra as $value)
											<tr class="elementoBuscar">
												<td>{{$value->tproveedor->nombre}}</td>
												<td class="text-center">{{$value->numeroRecibo == '' ? '---' : $value->numeroRecibo}}</td>
												<td class="text-center">{{$value->numeroGuiaRemision == '' ? '---' : $value->numeroGuiaRemision}}</td>
												<td class="text-center">{{$value->fechaPagar}}</td>
												<td class="text-center">S/{{$value->total}}</td>
												<td class="text-center">
													<span class="label {{strtolower($value->tipoPago) == strtolower("Al Contado") ? "label-info" : "label-warning"}}">{{$value->tipoPago}}</span>
												</td>
												<td class="text-center">
													@if(strtolower($value->tipoPago) == strtolower("Al crédito"))
													<span class="label {{ $value->estadoCredito ? 'label-success' : 'label-warning' }}" data-toggle="tooltip" data-placement="top" title="{{ $value->estadoCredito ? "Sin pagos pendientes" : "Pagos pendientes!" }}" >
														<i class="fa {{ $value->estadoCredito ? 'fa-check' : 'fa-warning' }}"></i>
													</span>
													@endif
												</td>
												<td class="text-center">
													<span class="label {{$value->estado ? 'label-success' : 'label-danger'}}">{{$value->estado ? 'Conforme' : 'Anulado'}}</span>
												</td>												
												<td class="text-center">{{$value->created_at}}</td>
												<td class="text-right">
													<span class="btn btn-default btn-xs glyphicon glyphicon-th-list" data-toggle="tooltip" data-placement="left" title="Ver detalles" onclick="dialogoAjax('dialogoGeneral', 'modal-lg', 'Detalle de la compra', { _token : '{{csrf_token()}}', codigoReciboCompra : '{{$value->codigoReciboCompra}}' }, '{{url('recibocompra/detalle')}}', 'POST', null, null, false, true);"></span>
													@if(strtolower($value->tipoPago) != strtolower("Al Contado"))
														<span class="btn btn-default btn-xs glyphicon glyphicon-piggy-bank" data-toggle="tooltip" data-placement="left" title="Pagar" onclick="dialogoAjax('dialogoGeneral', 'modal-lg', 'Pagar', { _token : '{{csrf_token()}}', codigoReciboCompra : '{{$value->codigoReciboCompra}}' }, '{{url('recibocomprapago/pago')}}', 'POST', null, null, false, true);"></span>
													@else
														<span class="btn btn-default btn-xs glyphicon glyphicon-piggy-bank" data-toggle="tooltip" data-placement="left" title="Pagar" disabled></span>
													@endif
													@if($value->estado)
														<span class="btn btn-default btn-xs glyphicon glyphicon-ban-circle" data-toggle="tooltip" data-placement="left" title="Anular" data-urlredirect="{{url('recibocompra/anular', $value->codigoReciboCompra)}}" onclick="anularCompra(this)"></span>
													@else
														<span class="btn btn-default btn-xs glyphicon glyphicon-ban-circle" data-toggle="tooltip" data-placement="left" title="Anular" disabled></span>
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
<script src="{{asset('viewResources/recibocompra/ver.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>
@endsection