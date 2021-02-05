<div class="row invoice-info">	
	<div class="col-sm-6 invoice-col">
		<address>
		<strong><i class="fa fa-home"></i>  ALMACEN</strong> <br>
	     {{$tAlmacenProductoRetiro->tAlmacen->descripcion}} <br>
		 {{$tAlmacenProductoRetiro->tAlmacen->distrito}}, {{$tAlmacenProductoRetiro->tAlmacen->pais}} <br>
		 {{$tAlmacenProductoRetiro->tAlmacen->direccion}} {{$tAlmacenProductoRetiro->tAlmacen->numeroVivienda}} <br>
	 	 {{$tAlmacenProductoRetiro->tAlmacen->telefono}}
		</address>
	</div>
	<div class="col-sm-6 invoice-col">
		<address>
		<strong><i class="fa fa-minus-circle"></i>  Retiro</strong> <br>
		<br>
	    <b>Descripción</b><br>
		<span>{{$tAlmacenProductoRetiro->descripcion}}</span><br>
		</address>
	</div>
</div>
<hr>
<div class="row">
	<div class="col-xs-12">
		<div class="table-responsive">
			<table id="tableRetiroProducto" class="table table-striped" style="min-width: 777px;">
				<thead>
					<tr>
						<th>Producto</th>
						<th class="text-center">Presentación</th>
						<th class="text-center">U. Medida</th>
						<th class="text-center">Tipo</th>
						<th class="text-center">P. Compra</th>
						<th class="text-center">P. Venta</th>
						<th class="text-center">F. V.</th>
						<th class="text-center">Cantidad</th>
						<th class="text-center">Perdida</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>{{$tAlmacenProductoRetiro->nombreCompletoProducto}}</td>
						<td class="text-center">{{$tAlmacenProductoRetiro->presentacionProducto}}</td>
						<td class="text-center">{{$tAlmacenProductoRetiro->unidadMedidaProducto}}</td>
						<td class="text-center">{{$tAlmacenProductoRetiro->tipoProducto}}</td>
						<td class="text-center">S/{{$tAlmacenProductoRetiro->precioCompraUnitarioProducto}}</td>
						<td class="text-center">S/{{$tAlmacenProductoRetiro->precioVentaUnitarioProducto}}</td>
						<td class="text-center">{{$tAlmacenProductoRetiro->fechaVencimientoProducto}}</td>
						<td class="text-center">{{$tAlmacenProductoRetiro->cantidadUnidad}}</td>
						<td class="text-center">S/{{$tAlmacenProductoRetiro->montoPerdido}}</td>
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