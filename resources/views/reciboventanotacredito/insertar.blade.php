<div class="row">
	<div class="col-md-12">
		<ul class="todo-list">
			@foreach($tReciboVenta->treciboventanotacredito as $value)
				<li>
					@if($value->estadoEnvioSunat!='Pendiente de envío')
						@if($value->estadoEnvioSunat=='Aprobado')
							<img src="{{asset('img/general/sunat.png')}}" width="22" style="border-radius: 50px;">
						@else
							<img src="{{asset('img/general/sunatRechazado.png')}}" width="22" style="border-radius: 50px;">
						@endif
					@else
						@if($tReciboVenta->tipoRecibo=='Factura')
							<img class="creditNoteSyncUp{{$value->codigoReciboVentaNotaCredito}}" src="{{asset('img/general/sincronizacionSunat.gif')}}" width="22" style="border-radius: 50px;">
						@else
							<img src="{{asset('img/general/resumenSunat.gif')}}" width="22" style="border-radius: 50px;">
						@endif
					@endif
					&nbsp;&nbsp;<i class="glyphicon glyphicon-paste"></i>
					<span class="text">{{$value->numeroRecibo}} | {{$value->created_at}} | {{$tReciboVenta->divisa=='Soles' ? 'S/' : 'US$'}}{{$value->total}}</span>
					<div class="tools" style="display: inline-block;">
						<span class="btn btn-default btn-xs glyphicon glyphicon-floppy-save" data-toggle="tooltip" data-placement="left" title="Descargar PDF y XML" onclick="window.location.href='{{url('reciboventanotacredito/descargarpdfxml/'.$value->codigoReciboVentaNotaCredito)}}';"></span>
						<span class="btn btn-default btn-xs glyphicon glyphicon-print" data-toggle="tooltip" data-placement="left" title="Imprimir comprobante" onclick="window.open('{{url('reciboventanotacredito/imprimircomprobante/'.$value->codigoReciboVentaNotaCredito)}}', '_blank');"></span>
					</div>
				</li>
			@endforeach
		</ul>
	</div>
</div>
<hr>
<div class="row">
	<div class="col-md-6">
		<div class="table-responsive">
			<table id="tableProducto" class="table table-striped" style="border: 1px solid #eeeeee;">
				<thead>
					<tr>
						<th style="display: none;"></th>
						<th class="text-center"></th>
						<th class="text-left">Nombre del producto</th>
						<th class="text-center">Cant.</th>
						<th class="text-center">Total</th>
						<th class="text-right">
							<span class="btn btn-default btn-xs glyphicon glyphicon-share-alt" data-toggle="tooltip" data-placement="left" title="Enviar todo para nota de crédito" onclick="moverTodoProductos();" style="margin: 1px;"></span>
						</th>
					</tr>
				</thead>
				<tbody>
					@foreach($tReciboVenta->treciboventadetalle as $value)
						<tr>
							<td style="display: none;">
								<input type="hidden" name="hdCodigoOficinaProducto[]" value="{{$value->codigoOficinaProducto}}">
								<input type="hidden" name="hdCodigoBarrasProducto[]" value="{{$value->codigoBarrasProducto}}">
								<input type="hidden" name="hdNombreProducto[]" value="{{$value->nombreProducto}}">
								<input type="hidden" name="hdInformacionAdicionalProducto[]" value="{{$value->informacionAdicionalProducto}}">
								<input type="hidden" name="hdTipoProducto[]" value="{{$value->tipoProducto}}">
								<input type="hidden" name="hdSituacionImpuestoProducto[]" value="{{$value->situacionImpuestoProducto}}">
								<input type="hidden" name="hdTipoImpuestoProducto[]" value="{{$value->tipoImpuestoProducto}}">
								<input type="hidden" name="hdPorcentajeTributacionProducto[]" value="{{$value->porcentajeTributacionProducto}}">
								<input type="hidden" name="hdPresentacionProducto[]" value="{{$value->presentacionProducto}}">
								<input type="hidden" name="hdUnidadMedidaProducto[]" value="{{$value->unidadMedidaProducto}}">
								<input type="hidden" name="hdPrecioVentaUnitarioProducto[]" value="{{number_format(($value->precioVentaTotalProducto)/($value->cantidadProducto), 2, '.', '')}}">
								<input type="hidden" name="hdCantidadProducto[]" value="{{$value->cantidadProducto}}">
								<input type="hidden" name="hdSubTotalProducto[]">
								<input type="hidden" name="hdImpuestoAplicadoProducto[]" value="{{$value->impuestoAplicadoProducto}}">
								<input type="hidden" name="hdPrecioVentaTotalProducto[]" value="{{$value->precioVentaTotalProducto}}">
							</td>
							<td class="text-center"><span class="fa {{$value->codigoOficinaProducto>='900000000000001' ? 'fa-circle-o' : 'fa-tag'}}"></span></td>
							<td class="text-left">{{$value->nombreProducto.' '.$value->informacionAdicionalProducto}}</td>
							<td class="tdCantidadProducto text-center">{{$value->cantidadProducto}}</td>
							<td class="tdPrecioVentaTotalProducto text-center">{{$value->precioVentaTotalProducto}}</td>
							<td class="text-right">
								<span class="btn btn-default btn-xs glyphicon glyphicon-circle-arrow-right" data-toggle="tooltip" data-placement="left" title="Enviar para nota de crédito" onclick="moverProducto($(this).parent().parent(), true);" style="margin: 1px;"></span>
							</td>
						</tr>	
					@endforeach
					<tr style="background-color: #ffffff;">
						<td style="display: none;"></td>
						<td class="text-center"></td>
						<td class="text-left"></td>
						<td class="text-center"></td>
						<td class="tdPrecioVentaTotalProducto text-center" style="border: 1px solid #999999;font-weight: bold;text-decoration: underline;">{{$tReciboVenta->divisa=='Soles' ? 'S/' : 'US$'}}0.00</td>
						<td class="text-right"></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<div class="col-md-6">
		<form id="frmInsertarReciboVentaNotaCredito" action="{{url('reciboventanotacredito/insertar')}}" method="post">
			<div class="table-responsive">
				<table id="tableProductoNotaCredito" class="table table-striped" style="border: 1px solid #eeeeee;">
					<thead>
						<tr>
							<th style="display: none;"></th>
							<th class="text-center"></th>
							<th class="text-left">Nombre del producto</th>
							<th class="text-center">Cant.</th>
							<th class="text-center">Total</th>
							<th class="text-right"></th>
						</tr>
					</thead>
					<tbody>
						<tr style="background-color: #ffffff;">
							<td style="display: none;"></td>
							<td class="text-center"></td>
							<td class="text-left"></td>
							<td class="text-center"></td>
							<td class="tdPrecioVentaTotalProducto text-center" style="border: 1px solid #999999;font-weight: bold;text-decoration: underline;">{{$tReciboVenta->divisa=='Soles' ? 'S/' : 'US$'}}0.00</td>
							<td class="text-right"></td>
						</tr>
					</tbody>
				</table>
			</div>
			{{csrf_field()}}
			<input type="hidden" name="hdCodigoReciboVenta" value="{{$tReciboVenta->codigoReciboVenta}}">
			<input type="hidden" id="hdImpuestoAplicado" name="hdImpuestoAplicado" value="{{$tReciboVenta->impuestoAplicado}}">
			<input type="hidden" id="hdSubTotal" name="hdSubTotal" value="{{$tReciboVenta->subTotal}}">
			<input type="hidden" id="hdTotal" name="hdTotal" value="{{$tReciboVenta->total}}">
			<input type="hidden" id="hdSelectMotivo" name="hdSelectMotivo">
		</form>
	</div>
</div>
<hr>
<div class="row">
	<div class="col-md-12">
		<label for="selectMotivo">Motivo de la nota de crédito</label>
		<select id="selectMotivo" name="selectMotivo" class="form-control">
			<option value="01_Anulación de la operación">Anulación de la operación</option>
			<option value="02_Anulación por error en el RUC">Anulación por error en el RUC</option>
			<option value="03_Corrección por error en la descripción">Corrección por error en la descripción</option>
			<option value="04_Descuento global">Descuento global</option>
			<option value="05_Descuento por ítem">Descuento por ítem</option>
			<option value="06_Devolución total">Devolución total</option>
			<option value="07_Devolución por ítem">Devolución por ítem</option>
			<option value="08_Bonificación">Bonificación</option>
			<option value="09_Disminución en el valor">Disminución en el valor</option>
		</select>
	</div>
</div>
<hr>
<div class="row">
	<div class="col-md-12">
		{{csrf_field()}}
		<input type="hidden" id="hdImpuestoAplicadoTemp" name="hdImpuestoAplicado" value="{{$tReciboVenta->impuestoAplicado}}">
		<input type="hidden" id="hdSubTotalTemp" name="hdSubTotal" value="{{$tReciboVenta->subTotal}}">
		<input type="hidden" id="hdTotalTemp" name="hdTotal" value="{{$tReciboVenta->total}}">
		<input type="button" class="btn btn-default pull-left" value="Cerrar ventana" onclick="$('#dialogoGeneralModal').modal('hide');">
		<input type="button" class="btn btn-primary pull-right" value="Generar nota de crédito" onclick="enviarFrmInsertarReciboVentaNotaCredito();">
	</div>
</div>
<script>
	var simboloDivisa='{{$tReciboVenta->divisa=='Soles' ? 'S/' : 'US$'}}';
</script>
<script src="{{asset('viewResources/reciboventanotacredito/insertar.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>