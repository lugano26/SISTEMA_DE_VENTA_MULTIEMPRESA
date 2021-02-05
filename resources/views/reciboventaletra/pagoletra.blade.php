<div class="row">
	<div class="col-md-12">
		<div class="nav-tabs-custom">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#pagoLetra" data-toggle="tab"><i class="fa fa-money"></i> Pagar</a></li>
				<li><a href="#pagoLetraHistorial" data-toggle="tab"><i class="fa fa-align-left"></i> Historial de pagos</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="pagoLetra">
					<div class="row clear">
						<div class="col-md-12">
							<div>
								<p><b>Monto a pagar:</b></p>
								<form id="fmrPagoLetra" class="frmPagoLetra" method="POST" action="{{url('reciboventaletra/realizarpagoletra')}}">
									{{csrf_field()}}
									<div class="form-group">
										<div class="input-group input-group-sm">
											<input type="hidden" name="codigoReciboVenta" value="{{$tReciboVenta->codigoReciboVenta }}">
											<input type="text" class="form-control monto" placeholder="Monto a pagar" name="monto">
											<span class="input-group-btn">
											<button type="button" onclick="sendFrmLetra(event, this)" class="btn btn-primary btn-flat"><i class="fa fa-dollar"></i></button>
											</span>
										</div>
									</div>
								</form>
							</div>
						</div>
						<div class="col-md-12">
							<div class="table-responsive">
							<table class="table table-striped" style="min-width: 777px;">
								<tbody>
									<tr>
										<th class="text-center" style="width: 20px;">#Letra</th>
										<th class="text-center">Pagado</th>
										<th class="text-center">Por cobrar</th>
										<th class="text-center">Fecha cobrar</th>
										<th class="text-center">Días mora</th>
										<th class="text-center">Estado</th>
										<th>Progreso</th>
										<th></th>
										<th></th>
									</tr>
									@if(isset($tReciboVenta->tReciboVentaLetra))
									@php($pagadoAnterior=true)
										@foreach ($tReciboVenta->tReciboVentaLetra as $key => $value)
											@php($letraAtrasada = $value->fechaPagar < now()->toDateString() && $value->porPagar > 0)
											<tr>
												<td class="text-center">{{$key + 1}}.</td>												
												<td class="text-center">{{$tReciboVenta->divisa=='Soles' ? 'S/' : 'US$'}}{{$value->pagado}}</td>
												<td class="text-center">{{$tReciboVenta->divisa=='Soles' ? 'S/' : 'US$'}}{{$value->porPagar}}</td>
												<td class="text-center">{{$value->fechaPagar}}</td>
												<td class="text-center">{{
													$value->porPagar == 0 ? $value->diasMora : 
													($value->fechaPagar <= now()->toDateString() ? Carbon\Carbon::parse($value->fechaPagar)->diffInDays(Carbon\Carbon::parse(now()->toDateString())) : 0)
												}}</td>
												<td class="text-center">
													<span class="label {{$letraAtrasada ? 'label-danger' : ($value->estado ? 'label-success': 'label-primary')}}">{{$letraAtrasada ? 'Atrasado' : ($value->estado ? 'Pagado': 'Pendiente')}}</span>
												</td>
												<td>
													<div class="progress progress-xs progress-striped active">
													<div class="progress-bar progress-bar-{{$letraAtrasada ? 'danger' : ($value->estado ? 'success': 'primary')}}" style="width: {{100 * $value->pagado / ($value->porPagar + $value->pagado == 0 ? 1 : $value->porPagar + $value->pagado)}}%"></div>
													</div>
												</td>
												<td class="text-center">
													<span class="badge bg-{{$letraAtrasada ? 'red' : ($value->estado ? 'green': 'light-blue')}}">{{round((100 * $value->pagado )/ ($value->porPagar + $value->pagado == 0 ? 1 : $value->porPagar + $value->pagado),0, PHP_ROUND_HALF_DOWN)}}%</span>
												</td>
												<td>
													@if($value->estado || !$pagadoAnterior)																							
														<span class="btn btn-default btn-xs glyphicon glyphicon-ok" data-toggle="tooltip" data-placement="left" title="Marcar como pagado" disabled></span>
													@else
														@php($pagadoAnterior=false)
														<span data-urlredirect="{{url('reciboventaletra/marcarcomopagadoletra', $value->codigoReciboVentaLetra)}}" onclick="confirmarMarcarComoPagado(this)" class="btn btn-default btn-xs glyphicon glyphicon-ok" data-toggle="tooltip" data-placement="left" title="Marcar como pagado"></span>
													@endif
												</td>
											</tr>
										@endforeach
									@endif								
								</tbody>
							</table>
							</div>
						</div>
					</div>
				</div>
				<div class="tab-pane" id="pagoLetraHistorial">
					<div class="row">
						<div class="col-md-12">
							<div class="table-responsive">
							<table class="table table-striped" style="min-width: 777px;">
								<tbody>
									<tr>
										<th class="text-center" style="width: 10px;"><b>#</b></th>
										<th class="text-center">Fecha del pago</th>
										<th class="text-center">Monto</th>					
										<th class="text-right"></th>					
									</tr>
									@if(isset($tReciboVenta->tReciboVentaPago))
										@foreach($tReciboVenta->tReciboVentaPago as $key => $value)
											<tr>
												<td class="text-center">{{$key + 1}}.</td>
												<td class="text-center">{{$value->created_at}}</td>
												<td class="text-center">{{$tReciboVenta->divisa=='Soles' ? 'S/' : 'US$'}}{{$value->monto}}</td>										
												<td class="text-right">
													<span class="btn btn-default btn-xs glyphicon glyphicon-print" data-toggle="tooltip" data-placement="left" title="Imprimir comprobante" data-urlredirect="{{url('reciboventaletra/imprimircomprobante', $value->codigoReciboVentaPago)}}" onclick="imprimirComprobante(this)"></span>
													<span class="btn btn-danger btn-xs glyphicon glyphicon-remove" data-toggle="tooltip" data-placement="left" title="Eliminar" data-urlredirect="{{url('reciboventaletra/eliminar', $value->codigoReciboVentaPago)}}" onclick="eliminarPago(this)"></span>
												</td>
											</tr>	
										@endforeach
										<tr>
											<td class="text-center" colspan="2" style="background: rgb(228, 228, 228)"><b>Total</b></td>
											<td class="text-center" style="background: rgb(228, 228, 228)"><b>{{$tReciboVenta->divisa=='Soles' ? 'S/' : 'US$'}}{{number_format($tReciboVenta->tReciboVentaPago->sum('monto'), 2, '.', '')}}</b></td>
											<td style="background: rgb(228, 228, 228)"></td>										
										</tr>
									@endif											
								</tbody>
							</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<hr>
<div class="row">
	<div class="col-md-12">
		<input type="button" class="btn btn-default pull-right" value="Cerrar ventana" onclick="$('#dialogoGeneralModal').modal('hide');">
	</div>
</div>
<script src="{{asset('viewResources/reciboventaletra/pagoletra.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>