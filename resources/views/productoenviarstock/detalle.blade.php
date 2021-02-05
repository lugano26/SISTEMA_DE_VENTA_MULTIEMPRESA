<div class="row invoice-info">	
	<div class="col-sm-4 invoice-col">
		<address>
		<strong><i class="fa fa-building-o"></i>  ALMACÉN</strong> <br>
			{{$tProductoEnviarStock->tAlmacen->descripcion}} <br>
			{{$tProductoEnviarStock->tAlmacen->distrito}}, {{$tProductoEnviarStock->tAlmacen->pais}} <br>
			{{$tProductoEnviarStock->tAlmacen->direccion}} {{$tProductoEnviarStock->tAlmacen->numeroVivienda}} <br>
			{{$tProductoEnviarStock->tAlmacen->telefono}}
		</address>
	</div>
	<div class="col-sm-4 invoice-col">
		<address>
		<strong><i class="fa fa-home"></i> OFICINA</strong> <br>
			{{$tProductoEnviarStock->toficina->descripcion}} <br>
			{{$tProductoEnviarStock->toficina->distrito}}, {{$tProductoEnviarStock->toficina->pais}} <br>
			{{$tProductoEnviarStock->toficina->direccion}} {{$tProductoEnviarStock->toficina->numeroVivienda}} <br>
			{{$tProductoEnviarStock->toficina->telefono}}
		</address>
	</div>
	<div class="col-sm-4 invoice-col">
		<address>
		<strong><i class="fa fa-truck"></i>  TRASLADO</strong> <br>
		<br>
	    <b>Fecha:</b> &nbsp;{{\Carbon\Carbon::parse($tProductoEnviarStock->created_at)->format('Y-m-d H:m')}} <br>
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
					@foreach($tProductoEnviarStock->tProductoEnviarStockDetalle as $value)
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
	<div class="col-md-12">
		<input type="button" class="btn btn-default pull-left" value="Cerrar ventana" onclick="$('#dialogoGeneralModal').modal('hide');">
		<input type="button" class="btn btn-primary pull-right" value="Aceptar" onclick="$('#dialogoGeneralModal').modal('hide');">
	</div>
</div>