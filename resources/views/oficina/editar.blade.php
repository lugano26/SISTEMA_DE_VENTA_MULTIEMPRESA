<form id="frmEditarOficina" action="{{url('oficina/editar')}}" method="post">
	<div class="tab-pane active" id="tab_1-1">
		<div class="row">
			<div class="form-group col-md-4">
				<label for="txtDescripcion">Nombre</label>
				<input type="text" id="txtDescripcion" name="txtDescripcion" class="form-control" placeholder="Obligatorio" value="{{$tOficina->descripcion}}">
			</div>
			<div class="form-group col-md-4">
				<label for="txtPais">País</label>
				<input type="text" id="txtPais" name="txtPais" class="form-control" placeholder="Obligatorio" value="{{$tOficina->pais}}">
			</div>
			<div class="form-group col-md-4">
				<label for="txtDepartamento">Departamento</label>
				<input type="text" id="txtDepartamento" name="txtDepartamento" class="form-control" placeholder="Obligatorio" value="{{$tOficina->departamento}}">
			</div>
		</div>
		<div class="row">
			<div class="form-group col-md-4">
				<label for="txtProvincia">Provincia</label>
				<input type="text" id="txtProvincia" name="txtProvincia" class="form-control" placeholder="Obligatorio" value="{{$tOficina->provincia}}">
			</div>
			<div class="form-group col-md-4">
				<label for="txtDistrito">Distrito</label>
				<input type="text" id="txtDistrito" name="txtDistrito" class="form-control" placeholder="Obligatorio" value="{{$tOficina->distrito}}">
			</div>
			<div class="form-group col-md-4">
				<label for="txtDireccion">Dirección</label>
				<input type="text" id="txtDireccion" name="txtDireccion" class="form-control" placeholder="Obligatorio" value="{{$tOficina->direccion}}">
			</div>
		</div>
		<div class="row">
			<div class="form-group col-md-4">
				<label for="txtTelefono">Teléfono</label>
				<input type="text" id="txtTelefono" name="txtTelefono" class="form-control" placeholder="Obligatorio" value="{{$tOficina->telefono}}">
			</div>
			<div class="form-group col-md-4">
				<label for="txtNumeroVivienda">Número vivienda</label>
				<input type="text" id="txtNumeroVivienda" name="txtNumeroVivienda" class="form-control" placeholder="Obligatorio" value="{{$tOficina->numeroVivienda}}">
			</div>
		</div>
		<div class="row">
			<div class="form-group col-xs-12">
				<label>Descripción comercial</label>
				<textarea class="form-control" name="txtDescripcionComercialComprobante" rows="3" placeholder="Descripción comercial para comprobantes">{{$tOficina->descripcionComercialComprobante}}</textarea>
			</div>
		</div>
		<hr>
		<div class="row">
			<div class="col-md-12">
				{{csrf_field()}}
				<input type="hidden" name="hdCodigoOficina" value="{{$tOficina->codigoOficina}}">
				<input type="button" class="btn btn-default pull-left" value="Cerrar ventana" onclick="$('#dialogoGeneralModal').modal('hide');">
				<input type="button" class="btn btn-primary pull-right" value="Guardar cambios" onclick="enviarFrmEditarOficina();">
			</div>
		</div>
	</div>
</form>
<script src="{{asset('viewResources/oficina/editar.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>