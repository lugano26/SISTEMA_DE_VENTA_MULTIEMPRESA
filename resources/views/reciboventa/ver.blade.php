@extends('template.layoutgeneral')
@section('titulo', 'Venta')
@section('subTitulo', 'Ver')
@section('cuerpoGeneral')
<link rel="stylesheet" href="{{asset('viewResources/reciboventa/ver.css?x='.env('CACHE_LAST_UPDATE'))}}">
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
								<form id="frmSearch" method="get" action="{{url('reciboventa/ver')}}" onsubmit="validarExpresion(event)">
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
											<th></th>
											<th class="text-center">Comprobante</th>
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
												<td class="text-center">
													@if($value->estadoEnvioSunat!='Pendiente de envío')
														@if($value->estadoEnvioSunat=='Aprobado')
															<img src="{{asset('img/general/sunat.png')}}" width="22" style="border-radius: 50px;">
														@else
															<img src="{{asset('img/general/sunatRechazado.png')}}" width="22" style="border-radius: 50px;">
														@endif
													@else
														@if($value->tipoRecibo=='Factura')
															<img class="billSyncUp{{$value->codigoReciboVenta}}" src="{{asset('img/general/sincronizacionSunat.gif')}}" width="22" style="border-radius: 50px;">
														@else
															<img src="{{asset('img/general/resumenSunat.gif')}}" width="22" style="border-radius: 50px;">
														@endif
													@endif
												</td>
												<td class="text-center">
													<span data-toggle="tooltip" data-placement="top" data-html="true" title="
														<b>Categoría de venta</b>
														<br>▼<br>
														{{$genericHelper->obtenerRamaCategoriaVenta($value->tcategoriaventa, '<br>▼<br>')}}
														" style="cursor: default;text-decoration: underline;">
														{{$value->numeroRecibo}}
													</span>
												</td>
												<td style="max-width: 300px;">{{'('.$value->documentoCliente.') '.$value->nombreCompletoCliente}}</td>
												<td>{{explode("@", $value->tPersonal->correoElectronico)[0]}}</td>
												<td class="text-center">{{$value->divisa=='Soles' ? 'S/' : 'US$'}}{{$value->total}}</td>
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
												<td id="tdEstado{{$value->codigoReciboVenta}}" class="text-center">
													<span class="label {{($value->estadoEnvioSunat=='Rechazado' ? 'label-danger' : ($value->estado ? 'label-success' : 'label-danger'))}}">{{($value->estadoEnvioSunat=='Rechazado' ? 'Rechazado' : ($value->estado ? 'Conforme' : 'Anulado'))}}</span>
												</td>
												<td class="text-center">{{$value->created_at}}</td>
												<td class="text-right">
													@if((strpos(Session::get('rol'), 'Súper usuario')!==false || strpos(Session::get('rol'), 'Administrador')!==false || strpos(Session::get('rol'), 'Revocador')!==false) && Session::has('codigoOficina'))
													<span class="btn btn-default btn-xs glyphicon glyphicon-paste" data-toggle="tooltip" data-placement="left" title="Gestión de notas de crédito" onclick="dialogoAjax('dialogoGeneral', 'modal-lg', '{{'['.$value->numeroRecibo.'] - ('.$value->documentoCliente.')'.' '.$value->nombreCompletoCliente}} (Gestión de notas de crédito)', { _token : '{{csrf_token()}}', codigoReciboVenta : '{{$value->codigoReciboVenta}}' }, '{{url('reciboventanotacredito/insertar')}}', 'POST', null, null, false, true);"></span>
													<span class="btn btn-default btn-xs glyphicon glyphicon-copy" data-toggle="tooltip" data-placement="left" title="Gestión de notas de débito" onclick="dialogoAjax('dialogoGeneral', 'modal-lg', '{{'['.$value->numeroRecibo.'] - ('.$value->documentoCliente.')'.' '.$value->nombreCompletoCliente}} (Gestión de notas de débito)', { _token : '{{csrf_token()}}', codigoReciboVenta : '{{$value->codigoReciboVenta}}' }, '{{url('reciboventanotadebito/insertar')}}', 'POST', null, null, false, true);"></span>
													@endif

													@if((strpos(Session::get('rol'), 'Súper usuario')!==false || strpos(Session::get('rol'), 'Administrador')!==false || strpos(Session::get('rol'), 'Ventas')!==false) && Session::has('codigoOficina'))
													<span class="btn btn-default btn-xs glyphicon glyphicon-list-alt" data-toggle="tooltip" data-placement="left" title="Gestión de guía de remisión" onclick="dialogoAjax('dialogoGeneral', 'modal-lg', '{{'['.$value->numeroRecibo.'] - ('.$value->documentoCliente.')'.' '.$value->nombreCompletoCliente}} (Gestión de guía de remisión)', { _token : '{{csrf_token()}}', codigoReciboVenta : '{{$value->codigoReciboVenta}}' }, '{{url('reciboventaguiaremision/gestionarguiaremision')}}', 'POST', null, null, false, true);"></span>
													<span class="btn btn-default btn-xs glyphicon glyphicon-floppy-save" data-toggle="tooltip" data-placement="left" title="Descargar PDF y XML" onclick="window.location.href='{{url('reciboventa/descargarpdfxml/'.$value->codigoReciboVenta)}}';"></span>
													<span class="btn btn-default btn-xs glyphicon glyphicon-print" data-toggle="tooltip" data-placement="left" title="Imprimir comprobante" onclick="window.open('{{url('reciboventa/imprimircomprobante/'.$value->codigoReciboVenta)}}', '_blank');"></span>
													<span class="btn btn-default btn-xs glyphicon glyphicon-th-list" data-toggle="tooltip" data-placement="left" title="Ver detalles" onclick="dialogoAjax('dialogoGeneral', 'modal-lg', 'Detalle de la venta', { _token : '{{csrf_token()}}', codigoReciboVenta : '{{$value->codigoReciboVenta}}' }, '{{url('reciboventa/detalle')}}', 'POST', null, null, false, true);"></span>
													@if(strtolower($value->tipoPago) != strtolower("Al contado"))
														<span class="btn btn-default btn-xs glyphicon glyphicon-piggy-bank" data-toggle="tooltip" data-placement="left" title="Pagar letras" onclick="dialogoAjax('dialogoGeneral', 'modal-lg', 'Pago de letras', { _token : '{{csrf_token()}}', codigoReciboVenta : '{{$value->codigoReciboVenta}}' }, '{{url('reciboventaletra/pagoletra')}}', 'POST', null, null, false, true);"></span>
													@else
														<span class="btn btn-default btn-xs glyphicon glyphicon-piggy-bank" data-toggle="tooltip" data-placement="left" title="Pagar letras" disabled></span>
													@endif
													<span class="btn btn-default btn-xs glyphicon glyphicon-envelope" data-toggle="tooltip" data-placement="left" title="Enviar PDF y XML por correo electrónico" onclick="dialogoAjax('dialogoGeneral', 'modal-lg', 'Enviar por correo electrónico', { _token : '{{csrf_token()}}', codigoReciboVenta : '{{$value->codigoReciboVenta}}' }, '{{url('reciboventa/enviarpdfxml')}}', 'POST', null, null, false, true);"></span>
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
<script>
	var sessionCodigoReciboVentaNotaCreditoTemp='{{Session::get('codigoReciboVentaNotaCredito', 'undefined')}}';
	var sessionCodigoReciboVentaNotaDebitoTemp='{{Session::get('codigoReciboVentaNotaDebito', 'undefined')}}';
	var sessionCodigoReciboVentaGuiaRemisionTemp='{{Session::get('codigoReciboVentaGuiaRemision', 'undefined')}}';
</script>
<script src="{{asset('viewResources/reciboventa/ver.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>
@endsection