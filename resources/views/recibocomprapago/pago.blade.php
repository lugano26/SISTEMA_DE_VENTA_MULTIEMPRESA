<div class="row">
	<div class="col-md-12">
		<div class="nav-tabs-custom">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#pagoLetra" data-toggle="tab"><i class="fa fa-money"></i> Pagar</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="pagoLetra">
					<div class="row clear">
						<div class="col-md-12">
							<div>
								<p><b>Monto a pagar:</b></p>
								<form id="fmrPagoLetra" class="frmPagoLetra" method="POST" action="{{url('recibocomprapago/realizarpago')}}">
									{{csrf_field()}}
									<div class="form-group">
										<div class="input-group input-group-sm">
											<input type="hidden" name="codigoReciboCompra" value="{{ $tReciboCompra->codigoReciboCompra }}">
											<input type="text" class="form-control monto" placeholder="Monto a pagar" name="monto">
											<span class="input-group-btn">
											<button type="button" onclick="sendFrmLetra(event, this)" class="btn btn-primary btn-flat"><i class="fa fa-dollar" ></i></button>
											</span>
										</div>
									</div>
								</form>
							</div>
						</div>
						<div class="col-md-6">
							<div class="table-responsive">
								<table class="table">
								<tbody><tr>
									<th style="width:50%">Fecha a pagar:</th>
									<td>{{ $tReciboCompra->fechaPagar }}</td>
								</tr>
								<tr>
									<th>Días mora:</th>
									<td>{{ ($tReciboCompra->fechaPagar <= now()->toDateString() ? Carbon\Carbon::parse($tReciboCompra->fechaPagar)->diffInDays(Carbon\Carbon::parse(now()->toDateString())) : 0) }}</td>
								</tr>
								</tbody></table>
							</div>
						</div>
						<div class="col-md-6">
							<div class="table-responsive">
								<table class="table">
								<tbody>
								<tr>
									<th>Pagado:</th>
									<td>S/{{ number_format($tReciboCompra->tReciboCompraPago->sum('monto'),2 ,'.', '') }}</td>
								</tr>
								<tr>
									<th>Por pagar:</th>
									<td>S/{{ number_format($tReciboCompra->total - $tReciboCompra->tReciboCompraPago->sum('monto'), 2, '.', '') }}</td>
								</tr>
								</tbody></table>
							</div>
						</div>
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
									@if(isset($tReciboCompra->tReciboCompraPago))
									@foreach ($tReciboCompra->tReciboCompraPago as $key => $value )
									<tr>
										<td class="text-center">{{$key + 1}}.</td>
										<td class="text-center">{{$value->created_at}}</td>
										<td class="text-center">S/{{$value->monto}}</td>										
										<td class="text-right">
											<span class="btn btn-danger btn-xs glyphicon glyphicon-remove" data-toggle="tooltip" data-placement="left" title="Eliminar" data-urlredirect="{{url('recibocomprapago/eliminar', $value->codigoReciboCompraPago)}}" onclick="eliminarPago(this)"></span>
										</td>
									</tr>	
									@endforeach
									<tr>
										<td class="text-center" colspan="2" style="background: rgb(228, 228, 228)"><b>Total</b></td>
										<td class="text-center" style="background: rgb(228, 228, 228)"><b>S/{{number_format($tReciboCompra->tReciboCompraPago->sum('monto'), 2, '.', '')}}</b></td>
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
<script src="{{asset('viewResources/recibocomprapago/pago.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>