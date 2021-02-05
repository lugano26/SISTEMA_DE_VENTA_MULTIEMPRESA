@extends('template.layoutgeneral')
@section('titulo', 'Ventas sin facturación electrónica')
@section('subTitulo', 'Ver')
@section('cuerpoGeneral')
<link rel="stylesheet" href="{{asset('viewResources/reciboventa/listasinfe.css?x='.env('CACHE_LAST_UPDATE'))}}">
<div class="row">
	<div class="col-md-12">
		<div class="nav-tabs-custom">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#tab_1-1">Lista de ventas</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="tab_1-1">
					<div class="row">
						<div class="col-md-12">
							<div>
								<form id="frmSearch" method="get" action="{{url('reciboventa/listasinfe')}}" onsubmit="validarExpresion(event)">
									<div class="input-group input-group-sm">
										<input type="hidden" name="searchPerformance" id="searchPerformanceInput">
										<input id="textSearch" type="text" onkeyup="searchItem(event);" class="form-control" placeholder="Buscar por comprobante, cliente, personal, número recibo, tipo pago, nombre producto, código barras producto (Enter)" name="q" value="{{!empty($q) ? $q : ''}}" autofocus>
										<span class="input-group-btn">
											<button type="submit" class="btn btn-primary btn-flat"><i class="fa fa-search" ></i></button>
										</span>
									</div>
								</form>
							</div>							
						</div>
					</div>
					<hr>
					<div class="row">
						<div class="col-lg-10">
							<div class="table-responsive">
								<table id="tableReciboVenta" class="table table-striped table-bordered" style="min-width: 777px;">
									<thead>
										<tr>
											<th class="text-center">Comprobante</th>
											<th class="text-center">N° Comp. FE</th>
											<th>Cliente</th>
											<th>Personal</th>
											<th class="text-center">Total</th>
											<th class="text-center">Pago</th>
											<th class="text-center"></th>
											<th class="text-center">Estado</th>
											<th class="text-center">Fecha registro</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										@foreach($listaTReciboVenta as $value)
											<tr>
												<td class="text-center"><span data-toggle="tooltip" data-placement="top" data-html="true" title="
													<b>Categoría de venta</b>
													<br>▼<br>
													{{$genericHelper->obtenerRamaCategoriaVenta($value->tcategoriaventa, '<br>▼<br>')}}
												" style="cursor: default;text-decoration: underline;">{{$value->numeroRecibo}}</span></td>
												<td class="text-center">{{{$value->tReciboVenta != null ? $value->tReciboVenta->numeroRecibo : '-' }}}</td>
												<td style="max-width: 300px;">{{'('.$value->documentoCliente.') '.$value->nombreCompletoCliente}}</td>
												<td>{{explode("@", $value->tPersonal->correoElectronico)[0]}}</td>
												<td class="text-center">S/{{$value->total}}</td>
												<td class="text-center">
													<span class="label {{strtolower($value->tipoPago) == strtolower("Al contado") ? "label-info" : "label-warning"}}">
														{{$value->tipoPago}}
													</span>
												</td>
												<td class="text-center">
													@if(strtolower($value->tipoPago) == strtolower("Al crédito"))
														<span class="label {{$value->estadoCredito ? 'label-success' : 'label-warning'}}" data-toggle="tooltip" data-placement="top" title="{{$value->estadoCredito ? "Sin cobros" : "Cobros pendientes!"}}" >
															<i class="fa {{$value->estadoCredito ? 'fa-check' : 'fa-warning'}}"></i>
														</span>
													@endif
												</td>
												<td class="text-center">
													<span class="label {{$value->estado ? 'label-success' : 'label-danger'}}">{{$value->estado ? 'Conforme' : 'Anulado'}}</span>
												</td>
												<td class="text-center">{{$value->created_at}}</td>
												<td class="text-right">
													@if((strpos(Session::get('rol'), 'Súper usuario')!==false || strpos(Session::get('rol'), 'Administrador')!==false || strpos(Session::get('rol'), 'Ventas')!==false) && Session::has('codigoOficina'))
													@if(Session::get('facturacionElectronica') && !isset($value->tReciboVenta->numeroRecibo) && $value->estado)
														<span class="btn btn-default btn-xs glyphicon glyphicon-hdd" data-toggle="tooltip" data-placement="left" title="Generar venta con FE a partir de esta venta" onclick="generaVenta({{$value->codigoReciboVentaOutEf}});"></span>
													@else
														<span class="btn btn-default btn-xs glyphicon glyphicon-hdd" data-toggle="tooltip" data-placement="left" title="Generar venta con FE a partir de esta venta" disabled></span>
													@endif
													<span class="btn btn-default btn-xs glyphicon glyphicon-print" data-toggle="tooltip" data-placement="left" title="Imprimir comprobante" onclick="window.open('{{url('reciboventa/imprimircomprobantesinfe/'.$value->codigoReciboVentaOutEf)}}', '_blank');"></span>
													<span class="btn btn-default btn-xs glyphicon glyphicon-th-list" data-toggle="tooltip" data-placement="left" title="Ver detalles" onclick="dialogoAjax('dialogoGeneral', 'modal-lg', 'Detalle de la venta', { _token : '{{csrf_token()}}', codigoReciboVenta : '{{$value->codigoReciboVentaOutEf}}' }, '{{url('reciboventa/detallesinfe')}}', 'POST', null, null, false, true);"></span>
													@if(strtolower($value->tipoPago) != strtolower("Al contado") && $value->estado)
														<span class="btn btn-default btn-xs glyphicon glyphicon-piggy-bank" data-toggle="tooltip" data-placement="left" title="Pagar letras" onclick="dialogoAjax('dialogoGeneral', 'modal-lg', 'Pago de letras', { _token : '{{csrf_token()}}', codigoReciboVenta : '{{$value->codigoReciboVentaOutEf}}' }, '{{url('reciboventaletra/pagoletrasinfe')}}', 'POST', null, null, false, true);"></span>
													@else
														<span class="btn btn-default btn-xs glyphicon glyphicon-piggy-bank" data-toggle="tooltip" data-placement="left" title="Pagar letras" disabled></span>
													@endif
													@endif

													@if((strpos(Session::get('rol'), 'Súper usuario')!==false || strpos(Session::get('rol'), 'Administrador')!==false || strpos(Session::get('rol'), 'Revocador')!==false) && Session::has('codigoOficina'))
													@if(!isset($value->tReciboVenta->numeroRecibo) && $value->estado)
														<span class="btn btn-default btn-xs glyphicon glyphicon-remove" data-toggle="tooltip" data-placement="left" title="Anular venta" onclick="dialogoAjax('dialogoGeneral', 'modal-lg', 'Anular Venta', { _token : '{{csrf_token()}}', codigoReciboVentaOutEf : '{{$value->codigoReciboVentaOutEf}}' }, '{{url('reciboventa/anularventasinfe')}}', 'POST', null, null, false, true);"></span>
													@else
														<span class="btn btn-default btn-xs glyphicon glyphicon-remove" data-toggle="tooltip" data-placement="left" title="Anular venta" disabled></span>
													@endif
													@endif
												</td>
											</tr>
										@endforeach
									</tbody>
								</table>
							</div>
							{!!$pagination!!}
						</div>
						<div class="col-lg-2">
							<div class="box-group" style="min-height: 500px;">
								<div class="panel box box-primary">
									<div class="box-header with-border">
										<h4 class="box-title text-center" style="display: block;">
											Estadísticas
										</h4>
									</div>
									<div>
										<div class="box-body">
											<div>
												<input type="text" id="txtBuscar" name="txtBuscar" class="form-control" autocomplete="off" placeholder="Buscar (Enter)" onkeyup="filtrarHtml('contenedorEstadisticas', this.value, false, 0, event);">
											</div>
											<hr>
											<div id="contenedorEstadisticas" style="font-size: 12px;height: 350px;overflow-y: scroll;padding: 2px;">
												@foreach($listaEstadisticaVenta as $value)
													<div class="text-center elementoBuscar" style="background-color: #f1e8e8;border-radius: 5px;margin-bottom: 7px;padding: 4px;">
														<div class="wordWrap">{!!$genericHelper->obtenerRamaCategoriaVenta($value->tcategoriaventa, '<br>▼</br>')!!}</div>
														<b style="color: #349dda;font-size: 17px;">{{$value->cantidadVentas}}</b>
													</div>
												@endforeach
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script src="{{asset('viewResources/reciboventa/listasinfe.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>
@endsection