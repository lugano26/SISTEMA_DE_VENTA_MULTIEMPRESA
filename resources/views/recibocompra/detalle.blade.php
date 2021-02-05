<div class="row invoice-info">	
	<div class="col-sm-4 invoice-col">
		<address>
		<strong><i class="fa fa-building-o"></i>  ALMACEN</strong> <br>
	     {{$tReciboCompra->tAlmacen->descripcion}} <br>
		 {{$tReciboCompra->tAlmacen->distrito}}, {{$tReciboCompra->tAlmacen->pais}} <br>
		 {{$tReciboCompra->tAlmacen->direccion}} {{$tReciboCompra->tAlmacen->numeroVivienda}} <br>
	 	 {{$tReciboCompra->tAlmacen->telefono}}
		</address>
	</div>
	<div class="col-sm-4 invoice-col">
		<address>
		<strong><i class="fa fa-truck"></i> PROVEEDOR</strong> <br>
		{{$tReciboCompra->tProveedor->nombre}} <br>
		{{$tReciboCompra->tProveedor->documentoIdentidad}}
		</address>
	</div>
	<div class="col-sm-4 invoice-col">
		<address>
		<strong><i class="fa fa-line-chart"></i>  COMPRA</strong> <br>
		<br>
	    <b>Fecha a pagar &nbsp;:</b> {{\Carbon\Carbon::parse($tReciboCompra->fechaPagar)->format('Y-m-d')}} <br>
		<b>Tipo recibo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</b> {{$tReciboCompra->tipoRecibo}} <br>
		<b>Tipo pago &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</b> {{$tReciboCompra->tipoPago}} <br>
		</address>
	</div>
</div>
<hr>
<div class="row">
	<div class="col-xs-12">
		<div class="table-responsive">
			<table id="tableDetalleCompra" class="table table-striped" style="min-width: 777px;">
				<thead>
					<tr>
						<th>Producto</th>
						<th class="text-center">Tipo producto</th>
						<th class="text-center">Situación I.</th>
						<th class="text-center">Tipo I.</th>
						<th class="text-center">Porcentaje I.</th>
						<th class="text-center">Cantidad</th>
						<th class="text-center">Precio de compra</th>
						<th class="text-center" class="text-center">Precio de venta</th>
					</tr>
				</thead>
				<tbody>
					@foreach($tReciboCompra->tReciboCompraDetalle as $value)
					<tr>
						<td>{{$value->nombreProducto}}</td>
						<td class="text-center">{{$value->tipoProducto}}</td>
						<td class="text-center">{{$value->situacionImpuestoProducto}}</td>
						<td class="text-center">{{$value->tipoImpuestoProducto}}</td>
						<td class="text-center">{{$value->porcentajeTributacionProducto}}%</td>
						<td class="text-center">{{$value->cantidadProducto}}</td>
						<td class="text-center">S/{{$value->precioCompraUnitarioProducto}}</td>
						<td class="text-center">S/{{$value->precioVentaUnitarioProducto}}</td>
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
						<td class="text-center">S/{{$tReciboCompra->impuestoAplicado}}</td>
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
						<td class="text-center">S/{{$tReciboCompra->subTotal}}</td>
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
						<td class="text-center">S/{{$tReciboCompra->total}}</td>
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