<div class="row invoice-info">	
	<div class="col-sm-4 invoice-col">
		<address>
		<strong><i class="fa fa-home"></i>  OFICINA ORIGEN</strong> <br>
			{{$tProductoTrasladoOficina->tOficina->descripcion}} <br>
			{{$tProductoTrasladoOficina->tOficina->distrito}}, {{$tProductoTrasladoOficina->tOficina->pais}} <br>
			{{$tProductoTrasladoOficina->tOficina->direccion}} {{$tProductoTrasladoOficina->tOficina->numeroVivienda}} <br>
			{{$tProductoTrasladoOficina->tOficina->telefono}}
		</address>
	</div>
	<div class="col-sm-4 invoice-col">
		<address>
		<strong><i class="fa fa-home"></i> OFICINA DESTINO</strong> <br>
			{{$tProductoTrasladoOficina->tOficinaLlegada->descripcion}} <br>
			{{$tProductoTrasladoOficina->tOficinaLlegada->distrito}}, {{$tProductoTrasladoOficina->tOficinaLlegada->pais}} <br>
			{{$tProductoTrasladoOficina->tOficinaLlegada->direccion}} {{$tProductoTrasladoOficina->tOficinaLlegada->numeroVivienda}} <br>
			{{$tProductoTrasladoOficina->tOficinaLlegada->telefono}}
		</address>
	</div>
	<div class="col-sm-4 invoice-col">
		<address>
		<strong><i class="fa fa-truck"></i>  TRASLADO</strong> <br>
		<br>
	    <b>Fecha:</b> &nbsp;{{\Carbon\Carbon::parse($tProductoTrasladoOficina->created_at)->format('Y-m-d H:m')}} <br>
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
					@foreach($tProductoTrasladoOficina->tProductoTrasladoOficinaDetalle as $value)
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