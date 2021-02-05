<form id="frmEditarAlmacen" action="{{url('almacen/editar')}}" method="post">
	<div class="tab-pane active" id="tab_1-1">
		<div class="row">
			<div class="form-group col-md-4">
				<label for="txtDescripcion">Nombre</label>
				<input type="text" id="txtDescripcion" name="txtDescripcion" class="form-control" placeholder="Obligatorio" value="{{$tAlmacen->descripcion}}">
			</div>
			<div class="form-group col-md-4">
				<label for="txtPais">País</label>
				<input type="text" id="txtPais" name="txtPais" class="form-control" placeholder="Obligatorio" value="{{$tAlmacen->pais}}">
			</div>
			<div class="form-group col-md-4">
				<label for="txtDepartamento">Departamento</label>
				<input type="text" id="txtDepartamento" name="txtDepartamento" class="form-control" placeholder="Obligatorio" value="{{$tAlmacen->departamento}}">
			</div>
		</div>
		<div class="row">
			<div class="form-group col-md-4">
				<label for="txtProvincia">Provincia</label>
				<input type="text" id="txtProvincia" name="txtProvincia" class="form-control" placeholder="Obligatorio" value="{{$tAlmacen->provincia}}">
			</div>
			<div class="form-group col-md-4">
				<label for="txtDistrito">Distrito</label>
				<input type="text" id="txtDistrito" name="txtDistrito" class="form-control" placeholder="Obligatorio" value="{{$tAlmacen->distrito}}">
			</div>
			<div class="form-group col-md-4">
				<label for="txtDireccion">Dirección</label>
				<input type="text" id="txtDireccion" name="txtDireccion" class="form-control" placeholder="Obligatorio" value="{{$tAlmacen->direccion}}">
			</div>
		</div>
		<div class="row">
			<div class="form-group col-md-4">
				<label for="txtTelefono">Teléfono</label>
				<input type="text" id="txtTelefono" name="txtTelefono" class="form-control" placeholder="Obligatorio" value="{{$tAlmacen->telefono}}">
			</div>
			<div class="form-group col-md-4">
				<label for="txtNumeroVivienda">Número vivienda</label>
				<input type="text" id="txtNumeroVivienda" name="txtNumeroVivienda" class="form-control" placeholder="Obligatorio" value="{{$tAlmacen->numeroVivienda}}">
			</div>
		</div>
		<hr>
		<div class="row">
			<div class="col-md-12">
				{{csrf_field()}}
				<input type="hidden" name="hdCodigoAlmacen" value="{{$tAlmacen->codigoAlmacen}}">
				<input type="button" class="btn btn-default pull-left" value="Cerrar ventana" onclick="$('#dialogoGeneralModal').modal('hide');">
				<input type="button" class="btn btn-primary pull-right" value="Guardar cambios" onclick="enviarFrmEditarAlmacen();">
			</div>
		</div>
	</div>
</form>
<script src="{{asset('viewResources/almacen/editar.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>