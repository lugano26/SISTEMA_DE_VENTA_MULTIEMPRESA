<div class="row invoice-info">	
	<div class="col-sm-6 invoice-col">
		<address>
		<strong><i class="fa fa-home"></i>  OFICINA</strong> <br>
	     {{$tOficinaProductoRetiro->tOficina->descripcion}} <br>
		 {{$tOficinaProductoRetiro->tOficina->distrito}}, {{$tOficinaProductoRetiro->tOficina->pais}} <br>
		 {{$tOficinaProductoRetiro->tOficina->direccion}} {{$tOficinaProductoRetiro->tOficina->numeroVivienda}} <br>
	 	 {{$tOficinaProductoRetiro->tOficina->telefono}}
		</address>
	</div>
	<div class="col-sm-6 invoice-col">
		<address>
		<strong><i class="fa fa-minus-circle"></i>  Retiro</strong> <br>
		<br>
	    <b>Descripción</b><br>
		<span>{{$tOficinaProductoRetiro->descripcion}}</span><br>
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
						<td>{{$tOficinaProductoRetiro->nombreCompletoProducto}}</td>
						<td class="text-center">{{$tOficinaProductoRetiro->presentacionProducto}}</td>
						<td class="text-center">{{$tOficinaProductoRetiro->unidadMedidaProducto}}</td>
						<td class="text-center">{{$tOficinaProductoRetiro->tipoProducto}}</td>
						<td class="text-center">S/{{$tOficinaProductoRetiro->precioCompraUnitarioProducto}}</td>
						<td class="text-center">S/{{$tOficinaProductoRetiro->precioVentaUnitarioProducto}}</td>
						<td class="text-center">{{$tOficinaProductoRetiro->fechaVencimientoProducto}}</td>
						<td class="text-center">{{$tOficinaProductoRetiro->cantidadUnidad}}</td>
						<td class="text-center">S/{{$tOficinaProductoRetiro->montoPerdido}}</td>
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