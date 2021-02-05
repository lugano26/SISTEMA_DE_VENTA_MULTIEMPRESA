<div class="row invoice-info">	
	<div class="col-sm-3 invoice-col">
		<address>
		<strong><i class="fa fa-home"></i>  OFICINA</strong> <br>
	     {{$tReciboVenta->tOficina->descripcion}} <br>
		 {{$tReciboVenta->tOficina->distrito}}, {{$tReciboVenta->tOficina->pais}} <br>
		 {{$tReciboVenta->tOficina->direccion}} {{$tReciboVenta->tOficina->numeroVivienda}} <br>
	 	 {{$tReciboVenta->tOficina->telefono}}
		</address>
	</div>
	<div class="col-sm-3 invoice-col">
		<address>
		<strong><i class="fa fa-user"></i> PERSONAL</strong> <br>
		{{$tReciboVenta->tPersonal->dni}} <br>
		{{$tReciboVenta->tPersonal->nombre . ' ' . $tReciboVenta->tPersonal->apellido}} <br>
		{{$tReciboVenta->tPersonal->documentoIdentidad}}
		{{$tReciboVenta->tPersonal->direccion}} <br>
		{{$tReciboVenta->tPersonal->telefono}}
		</address>
	</div>
	<div class="col-sm-6 invoice-col table-responsive" style="border: none;">
		<address>
		<strong><i class="fa fa-shopping-cart"></i>  VENTA</strong>
		<table class="table table-condensed">
			<tbody>
				<tr>
					<td class="text-bold">Cliente</td>		
					<td>{{ '(' . $tReciboVenta->documentoCliente . ') ' . $tReciboVenta->nombreCompletoCliente}}</td>	
				</tr>
				<tr>
					<td class="text-bold">Recibo</td>			
					<td>{{$tReciboVenta->tipoRecibo . ' #' . $tReciboVenta->numeroRecibo}}</td>
				</tr>
				<tr>
					<td class="text-bold">Fecha emitido</td>
					<td>{{$tReciboVenta->fechaComprobanteEmitido}}</td>
				</tr>
				<tr>
					<td class="text-bold">Tipo pago</td>	
					<td>{{$tReciboVenta->tipoPago}}</td>		
				</tr>
				<tr>
					<td class="text-bold">Letras</td>
					<td>{{$tReciboVenta->letras}}</td>
				</tr>
			</tbody>
		</table>
		</address>
	</div>
	@if(!$tReciboVenta->estado)
		<div class="col-md-12">
			<span class="text-red"><b><i class="fa fa-warning"></i> Venta anulada: </b> {{$tReciboVenta->motivoAnulacion}}</span>
			<br>
			<br>
		</div>		
	@endif
</div>
<hr>
<div class="row">
	<div class="col-md-12">
		<b>Categoría:</b> {{$genericHelper->obtenerRamaCategoriaVenta($tReciboVenta->tcategoriaventa, ' > ')}}
	</div>
</div>
<hr>
<div class="row">
	<div class="col-xs-12">
		<div class="table-responsive">
			<table id="tableDetalleVenta" class="table table-striped" style="min-width: 777px;">
				<thead>
					<tr>
						<th></th>
						<th>Producto</th>
						<th class="text-center">Tipo producto</th>
						<th class="text-center">Situación I.</th>
						<th class="text-center">Tipo I.</th>
						<th class="text-center">Porcentaje I.</th>
						<th class="text-center">Cantidad</th>
						<th class="text-center">Precio venta uni.</th>
						<th class="text-center">Precio venta tot.</th>
					</tr>
				</thead>
				<tbody>
					@foreach($tReciboVenta->tReciboVentaDetalleOutEf as $value)
					<tr>
						<td>
							<span class="{{($value->codigoOficinaProducto>='900000000000001' ? 'fa fa-circle-o' : 'fa fa-tag')}}"></span>
						</td>
						<td>{{$value->nombreProducto.' '.$value->informacionAdicionalProducto}}</td>
						<td class="text-center">{{$value->tipoProducto}}</td>
						<td class="text-center">{{$value->situacionImpuestoProducto}}</td>
						<td class="text-center">{{$value->tipoImpuestoProducto}}</td>
						<td class="text-center">{{$value->porcentajeTributacionProducto}}%</td>
						<td class="text-center">{{$value->cantidadProducto}}</td>
						<td class="text-center">S/{{$value->precioVentaUnitarioProducto}}</td>
						<td class="text-center">S/{{$value->precioVentaTotalProducto}}</td>
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
</div>
<hr>
<div class="row">
	<div class="col-xs-4">
		<div class="table-responsive">
			<table class="table">
				<tbody>
					<tr>
						<th>Impuesto aplicado:</th>
						<td class="text-center">S/{{$tReciboVenta->impuestoAplicado}}</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<div class="col-xs-4">
		<div class="table-responsive">
			<table class="table">
				<tbody>
					<tr>
						<th>Subtotal:</th>
						<td class="text-center">S/{{$tReciboVenta->subTotal}}</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<div class="col-xs-4">
		<div class="table-responsive">
			<table class="table">
				<tbody>
					<tr>
						<th>Total:</th>
						<td class="text-center">S/{{$tReciboVenta->total}}</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
<hr>
<div class="row">
	<div class="col-md-12">
		<input type="button" class="btn btn-default pull-left" value="Cerrar ventana" onclick="$('#dialogoGeneralModal').modal('hide');">
		<input type="button" class="btn btn-primary pull-right" value="Aceptar" onclick="$('#dialogoGeneralModal').modal('hide');">
	</div>
</div>