<form id="frmInsertarGuiaRemision" action="{{url('reciboventaguiaremision/gestionarguiaremision')}}" method="post">
	<div class="row">
		<div class="col-md-12">
			<ul class="todo-list">
				@foreach($listaTReciboVentaGuiaRemision as $value)
					<li>
						@if($value->estadoEnvioSunat!='Pendiente de envío')
							@if($value->estadoEnvioSunat=='Aprobado')
								<img src="{{asset('img/general/sunat.png')}}" width="22" style="border-radius: 50px;">
							@else
								<img src="{{asset('img/general/sunatRechazado.png')}}" width="22" style="border-radius: 50px;">
							@endif
						@else
							@if($tReciboVenta->tipoRecibo=='Factura')
								<img class="referralGuideSyncUp{{$value->codigoReciboVentaGuiaRemision}}" src="{{asset('img/general/sincronizacionSunat.gif')}}" width="22" style="border-radius: 50px;">
							@else
								<img src="{{asset('img/general/resumenSunat.gif')}}" width="22" style="border-radius: 50px;">
							@endif
						@endif
						&nbsp;&nbsp;<i class="glyphicon glyphicon-paste"></i>
						<span class="text">{{$value->numeroGuiaRemision}} | {{$value->created_at}} | {{$value->pesoBrutoKilosBienes}} KL's</span>
						<div class="tools" style="display: inline-block;">
							<span class="btn btn-default btn-xs glyphicon glyphicon-floppy-save" data-toggle="tooltip" data-placement="left" title="Descargar PDF y XML" onclick="window.location.href='{{url('reciboventaguiaremision/descargarpdfxml/'.$value->codigoReciboVentaGuiaRemision)}}';"></span>
							<span class="btn btn-default btn-xs glyphicon glyphicon-print" data-toggle="tooltip" data-placement="left" title="Imprimir comprobante" onclick="window.open('{{url('reciboventaguiaremision/imprimircomprobante/'.$value->codigoReciboVentaGuiaRemision)}}', '_blank');"></span>
						</div>
					</li>
				@endforeach
			</ul>
		</div>
	</div>
	<hr>
	<div class="row">
		<div class="form-group col-md-4">
			<label for="txtDocumentoReceptorGuiaRemision">DNI o RUC del receptor</label>
			<input type="text" id="txtDocumentoReceptorGuiaRemision" name="txtDocumentoReceptorGuiaRemision" class="form-control" value="{{$tReciboVenta->documentoCliente}}" placeholder="Obligatorio">
		</div>
		<div class="form-group col-md-8">
			<label for="txtNombreCompletoReceptorGuiaRemision">Nombre completo o razón social del receptor</label>
			<input type="text" id="txtNombreCompletoReceptorGuiaRemision" name="txtNombreCompletoReceptorGuiaRemision" class="form-control" value="{{$tReciboVenta->nombreCompletoCliente}}" placeholder="Obligatorio">
		</div>
	</div>
	<div class="row">
		<div class="form-group col-md-4">
			<label for="txtDocumentoTransportistaGuiaRemision">RUC del transportista</label>
			<input type="text" id="txtDocumentoTransportistaGuiaRemision" name="txtDocumentoTransportistaGuiaRemision" class="form-control" placeholder="Obligatorio">
		</div>
		<div class="form-group col-md-8">
			<label for="txtNombreCompletoTransportistaGuiaRemision">Razón social del transportista</label>
			<input type="text" id="txtNombreCompletoTransportistaGuiaRemision" name="txtNombreCompletoTransportistaGuiaRemision" class="form-control" placeholder="Obligatorio">
		</div>
	</div>
	<div class="row">
		<div class="form-group col-md-4">
			<label for="txtDniConductorTransportistaGuiaRemision">DNI del conductor que transporta</label>
			<input type="text" id="txtDniConductorTransportistaGuiaRemision" name="txtDniConductorTransportistaGuiaRemision" class="form-control" placeholder="Obligatorio">
		</div>
		<div class="form-group col-md-4">
			<label for="txtPlacaVehiculoTransportistaGuiaRemision">Marca y placa vehículo que transporta</label>
			<input type="text" id="txtPlacaVehiculoTransportistaGuiaRemision" name="txtPlacaVehiculoTransportistaGuiaRemision" class="form-control" placeholder="Obligatorio">
		</div>
		<div class="form-group col-md-4">
			<label for="selectMotivoTrasladoGuiaRemision">Motivo del traslado</label>
			<select id="selectMotivoTrasladoGuiaRemision" name="selectMotivoTrasladoGuiaRemision" class="form-control selectStaticNotClear" style="width: 100%;">
				<option value="Venta" selected>Venta</option>
				<option value="Venta normal" {{($tReciboVenta->motivoTraslado=='Venta normal') ? 'selected' : ''}}>Venta normal</option>
				<option value="Venta sujeta a confirmación por el comprador" {{($tReciboVenta->motivoTraslado=='Venta sujeta a confirmación por el comprador') ? 'selected' : ''}}>Venta sujeta a confirmación por el comprador</option>
				<option value="Recojo de bienes" {{($tReciboVenta->motivoTraslado=='Recojo de bienes') ? 'selected' : ''}}>Recojo de bienes</option>
				<option value="Traslado zona primaria" {{($tReciboVenta->motivoTraslado=='Traslado zona primaria') ? 'selected' : ''}}>Traslado zona primaria</option>
				<option value="Compra" {{($tReciboVenta->motivoTraslado=='Compra') ? 'selected' : ''}}>Compra</option>
				<option value="Traslado entre establecimientos de la misma empresa" {{($tReciboVenta->motivoTraslado=='Traslado entre establecimientos de la misma empresa') ? 'selected' : ''}}>Traslado entre establecimientos de la misma empresa</option>
				<option value="Importación" {{($tReciboVenta->motivoTraslado=='Importación') ? 'selected' : ''}}>Importación</option>
				<option value="Traslado por emisor itinerante" {{($tReciboVenta->motivoTraslado=='Traslado por emisor itinerante') ? 'selected' : ''}}>Traslado por emisor itinerante</option>
				<option value="Consignación" {{($tReciboVenta->motivoTraslado=='Consignación') ? 'selected' : ''}}>Consignación</option>
				<option value="Devolución" {{($tReciboVenta->motivoTraslado=='Devolución') ? 'selected' : ''}}>Devolución</option>
				<option value="Exportación" {{($tReciboVenta->motivoTraslado=='Exportación') ? 'selected' : ''}}>Exportación</option>
				<option value="Traslado de bienes para transformación" {{($tReciboVenta->motivoTraslado=='Traslado de bienes para transformación') ? 'selected' : ''}}>Traslado de bienes para transformación</option>
				<option value="Venta con entrega a terceros" {{($tReciboVenta->motivoTraslado=='Venta con entrega a terceros') ? 'selected' : ''}}>Venta con entrega a terceros</option>
			</select>
		</div>
	</div>
	<div class="row">
		<div class="form-group col-md-4">
			<label for="txtNumeroContenedorTransporteGuiaRemision">Número de contenedor transportado</label>
			<input type="text" id="txtNumeroContenedorTransporteGuiaRemision" name="txtNumeroContenedorTransporteGuiaRemision" class="form-control" placeholder="Obligatorio">
		</div>
		<div class="form-group col-md-4">
			<label for="txtPesoBrutoKilosBienesGuiaRemision">Peso en bruto de los bienes en kilos</label>
			<input type="text" id="txtPesoBrutoKilosBienesGuiaRemision" name="txtPesoBrutoKilosBienesGuiaRemision" readonly="readonly" class="form-control" value="0">
		</div>
		<div class="form-group col-md-4">
			<label for="dateFechaIniciaTrasladoGuiaRemision">Fecha que inicia el traslado</label>
			<div class="input-group date">
				<div class="input-group-addon">
					<i class="fa fa-calendar"></i>
				</div>
				<input type="text" id="dateFechaIniciaTrasladoGuiaRemision" name="dateFechaIniciaTrasladoGuiaRemision" class="form-control pull-right" value="{{date('Y-m-d')}}">
			</div>
		</div>
	</div>
	<div class="row">
		<div class="form-group col-md-4">
			<label for="selectUbigeoPartidaGuiaRemision">Ubigeo de partida</label>
			<select id="selectUbigeoPartidaGuiaRemision" name="selectUbigeoPartidaGuiaRemision" class="form-control" style="width: 100%;"></select>
		</div>
		<div class="form-group col-md-8">
			<label for="txtDireccionPartidaGuiaRemision">Dirección de partida</label>
			<input type="text" id="txtDireccionPartidaGuiaRemision" name="txtDireccionPartidaGuiaRemision" class="form-control" placeholder="Obligatorio">
		</div>
	</div>
	<div class="row">
		<div class="form-group col-md-4">
			<label for="selectUbigeoLlegadaGuiaRemision">Ubigeo de llegada</label>
			<select id="selectUbigeoLlegadaGuiaRemision" name="selectUbigeoLlegadaGuiaRemision" class="form-control" style="width: 100%;"></select>
		</div>
		<div class="form-group col-md-8">
			<label for="txtDireccionLlegadaGuiaRemision">Dirección de llegada</label>
			<input type="text" id="txtDireccionLlegadaGuiaRemision" name="txtDireccionLlegadaGuiaRemision" class="form-control" placeholder="Obligatorio">
		</div>
	</div>
	<hr>
	<div class="row">
		<div class="col-md-12">
			<table id="tableProducto" class="table table-striped table-bordered text-center">
				<thead>
					<tr>
						<th style="display: none;"></th>
						<th>Nº</th>
						<th style="text-align: left;">DESCRIPCIÓN</th>
						<th>CANT.</th>
						<th>UND. M.</th>
						<th>PESO EN KL</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					@foreach($tReciboVenta->treciboventadetalle as $key => $item)
						<tr>
							<td style="display: none;">
								<input type="hidden" name="hdCodigoOficinaProducto[]" value="{{$item->codigoOficinaProducto}}">
								<input type="hidden" name="hdCodigoBarrasProducto[]" value="{{$item->codigoBarrasProducto}}">
								<input type="hidden" name="hdNombreProducto[]" value="{{$item->nombreProducto}}">
								<input type="hidden" name="hdInformacionAdicionalProducto[]" value="{{$item->informacionAdicionalProducto}}">
								<input type="hidden" name="hdUnidadMedidaProducto[]" value="{{$item->unidadMedidaProducto}}">
								<input type="hidden" name="hdCantidadProducto[]" value="{{$item->cantidadProducto}}">
								<input type="hidden" name="hdPesoKilos[]" value="{{number_format((($item->pesoGramosUnidadProducto*$item->cantidadProducto)/1000), 2, '.', '')}}">
							</td>
							<td>{{($key+1)}}</td>
							<td style="text-align: left;">{{$item->nombreProducto.' '.$item->informacionAdicionalProducto}}</td>
							<td class="tdEditable" contenteditable="true" onkeyup="onKeyUpTdCantidadProducto(this, {{$item->pesoGramosUnidadProducto}});">{{$item->cantidadProducto}}</td>
							<td>{{$item->unidadMedidaProducto}}</td>
							<td class="tdEditable tdPesoKilos" contenteditable="true" onkeyup="onKeyUpTdPesoTotalProducto(this);">{{number_format((($item->pesoGramosUnidadProducto*$item->cantidadProducto)/1000), 2, '.', '')}}</td>
							<td style="text-align: right;">
								<span class="btn btn-default btn-xs glyphicon glyphicon-trash" data-toggle="tooltip" data-placement="left" title="Quitar" onclick="quitarProducto(this);"></span>
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
	<hr>
	<div class="row">
		<div class="col-md-12 form-group">
			<label for="txtObservacion">Observación (Máximo de 222 caracteres)</label>
			<textarea id="txtObservacion" name="txtObservacion" class="form-control" rows="4" maxlength="222"></textarea>
		</div>
	</div>
	<hr>
	<div class="row">
		<div class="col-md-12">
			{{csrf_field()}}
			<input type="hidden" name="hdCodigoReciboVenta" value="{{$tReciboVenta->codigoReciboVenta}}">
			<input type="button" class="btn btn-default pull-left" value="Cerrar ventana" onclick="$('#dialogoGeneralModal').modal('hide');">
			<input type="button" class="btn btn-info pull-right" value="{{$tReciboVenta->numeroGuiaRemision=='' ? 'Registrar guía de remisión' : 'Generar otra guía (Actualizar datos)'}}" onclick="enviarFrmInsertarGuiaRemision();">
		</div>
	</div>
</form>
<script src="{{asset('viewResources/reciboventaguiaremision/gestionarguiaremision.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>