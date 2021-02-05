<div class="row">
	<div class="col-md-12">
		<ul class="todo-list">
			@foreach($tReciboVenta->treciboventanotadebito as $value)
				<li>
					@if($value->estadoEnvioSunat!='Pendiente de envío')
						@if($value->estadoEnvioSunat=='Aprobado')
							<img src="{{asset('img/general/sunat.png')}}" width="22" style="border-radius: 50px;">
						@else
							<img src="{{asset('img/general/sunatRechazado.png')}}" width="22" style="border-radius: 50px;">
						@endif
					@else
						@if($tReciboVenta->tipoRecibo=='Factura')
							<img class="debitNoteSyncUp{{$value->codigoReciboVentaNotaDebito}}" src="{{asset('img/general/sincronizacionSunat.gif')}}" width="22" style="border-radius: 50px;">
						@else
							<img src="{{asset('img/general/resumenSunat.gif')}}" width="22" style="border-radius: 50px;">
						@endif
					@endif
					&nbsp;&nbsp;<i class="glyphicon glyphicon-copy"></i>
					<span class="text">{{$value->numeroRecibo}} | {{$value->created_at}} | {{$tReciboVenta->divisa=='Soles' ? 'S/' : 'US$'}}{{$value->total}}</span>
					<div class="tools" style="display: inline-block;">
						<span class="btn btn-default btn-xs glyphicon glyphicon-floppy-save" data-toggle="tooltip" data-placement="left" title="Descargar PDF y XML" onclick="window.location.href='{{url('reciboventanotadebito/descargarpdfxml/'.$value->codigoReciboVentaNotaDebito)}}';"></span>
						<span class="btn btn-default btn-xs glyphicon glyphicon-print" data-toggle="tooltip" data-placement="left" title="Imprimir comprobante" onclick="window.open('{{url('reciboventanotadebito/imprimircomprobante/'.$value->codigoReciboVentaNotaDebito)}}', '_blank');"></span>
					</div>
				</li>
			@endforeach
		</ul>
	</div>
</div>
<hr>
<form id="frmInsertarReciboVentaNotaDebito" action="{{url('reciboventanotadebito/insertar')}}" method="post">
	<div class="row">
		<div class="col-md-3">
			<label for="selectMotivo">Motivo de la nota de débito</label>
			<select id="selectMotivo" name="selectMotivo" class="form-control">
				<option value="01_Intereses por mora">Intereses por mora</option>
				<option value="02_Aumento en el valor">Aumento en el valor</option>
				<option value="03_Penalidades">Penalidades</option>
			</select>
		</div>
		<div class="col-md-3">
			<label for="txtSubTotal">Sub total ({{$tReciboVenta->divisa=='Soles' ? 'S/' : 'US$'}})</label>
			<input type="text" id="txtSubTotal" name="txtSubTotal" class="form-control" readonly="readonly" value="0.00">
		</div>
		<div class="col-md-3">
			<label for="txtImpuestoAplicado">Imp. ({{$tReciboVenta->divisa=='Soles' ? 'S/' : 'US$'}})</label>
			<input type="text" id="txtImpuestoAplicado" name="txtImpuestoAplicado" class="form-control" readonly="readonly" value="0.00">
		</div>
		<div class="col-md-3">
			<label for="txtTotal">Total ({{$tReciboVenta->divisa=='Soles' ? 'S/' : 'US$'}})</label>
			<input type="text" id="txtTotal" name="txtTotal" class="form-control" onkeyup="calcularPreciosTotales();">
		</div>
	</div>
	<hr>
	<div class="row">
		<div class="col-md-12">
			{{csrf_field()}}
			<input type="hidden" name="hdCodigoReciboVenta" value="{{$tReciboVenta->codigoReciboVenta}}">
			<input type="button" class="btn btn-default pull-left" value="Cerrar ventana" onclick="$('#dialogoGeneralModal').modal('hide');">
			<input type="button" class="btn btn-primary pull-right" value="Generar nota de débito" onclick="enviarFrmInsertarReciboVentaNotaDebito();">
		</div>
	</div>
</form>
<script src="{{asset('viewResources/reciboventanotadebito/insertar.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>