<form id="frmEditarPersonal" action="{{url('personal/editar')}}" method="post">
	<div class="tab-pane active" id="tab_1-1">
		<div class="row">
			<div class="form-group col-md-6">
				<label for="txtDni">DNI</label>
				<input type="text" id="txtDni" name="txtDni" class="form-control" placeholder="Obligatorio" value="{{$tPersonal->dni}}">
			</div>
			<div class="form-group col-md-6">
				<label for="txtNombre">Nombre</label>
				<input type="text" id="txtNombre" name="txtNombre" class="form-control" placeholder="Obligatorio" value="{{$tPersonal->nombre}}">
			</div>
		</div>
		<div class="row">
			<div class="form-group col-md-6">
				<label for="txtApellido">Apellido</label>
				<input type="text" id="txtApellido" name="txtApellido" class="form-control" placeholder="Obligatorio" value="{{$tPersonal->apellido}}">
			</div>
			<div class="form-group col-md-6">
				<label for="txtTelefono">Teléfono</label>
				<input type="text" id="txtTelefono" name="txtTelefono" class="form-control" placeholder="Obligatorio" value="{{$tPersonal->telefono}}">
			</div>
		</div>
		<div class="row">
			<div class="form-group col-md-12">
				<label for="txtDireccion">Dirección</label>
				<input type="text" id="txtDireccion" name="txtDireccion" class="form-control" placeholder="Obligatorio" value="{{$tPersonal->direccion}}">
			</div>
		</div>
		<div class="row">
			<div class="form-group col-md-6">
				<label>Sexo</label>
				<div class="form-control" style="border: none;">
					<label style="cursor: pointer;">
						<input type="radio" id="radioSexoM" name="radioSexo" {{$tPersonal->sexo ? 'checked="checked"' : ''}} value="1">
						Masculino
					</label>
					&nbsp;&nbsp;
					<label style="cursor: pointer;">
						<input type="radio" id="radioSexoF" name="radioSexo" {{!($tPersonal->sexo) ? 'checked="checked"' : ''}} value="0">
						Femenino
					</label>
				</div>
			</div>
			<div class="form-group col-md-6">
				<label for="txtCorreoElectronico">Correo electrónico</label>
				<input type="text" id="txtCorreoElectronico" name="txtCorreoElectronico" class="form-control" placeholder="Obligatorio" readonly="readonly" value="{{$tPersonal->correoElectronico}}">
			</div>
		</div>
		<hr>
		<div class="row">
			<div class="form-group col-md-12">
				<label for="selectRolUsuario">Rol{{$tPersonal->tusuario->rol}}</label>
				<select id="selectRolUsuario" name="selectRolUsuario[]" class="form-control select" multiple style="width: 100%;">
					<option value="Administrador" {{strpos($tPersonal->tusuario->rol, 'Administrador')!==false ? 'selected' : ''}}>Administrador</option>
					<option value="Ventas" {{strpos($tPersonal->tusuario->rol, 'Ventas')!==false ? 'selected' : ''}}>Ventas</option>
					<option value="Revocador" {{strpos($tPersonal->tusuario->rol, 'Revocador')!==false ? 'selected' : ''}}>Revocador</option>
					<option value="Almacenero" {{strpos($tPersonal->tusuario->rol, 'Almacenero')!==false ? 'selected' : ''}}>Almacenero</option>
					<option value="Inventariador" {{strpos($tPersonal->tusuario->rol, 'Inventariador')!==false ? 'selected' : ''}}>Inventariador</option>
					<option value="Reporteador" {{strpos($tPersonal->tusuario->rol, 'Reporteador')!==false ? 'selected' : ''}}>Reporteador</option>
				</select>
			</div>
		</div>
		<hr>
		<div class="row">
			<div class="col-md-12">
				{{csrf_field()}}
				<input type="hidden" name="hdCodigoPersonal" value="{{$tPersonal->codigoPersonal}}">
				<input type="button" class="btn btn-default pull-left" value="Cerrar ventana" onclick="$('#dialogoGeneralModal').modal('hide');">
				<input type="button" class="btn btn-primary pull-right" value="Guardar cambios" onclick="enviarFrmEditarPersonal();">
			</div>
		</div>
	</div>
</form>
<script src="{{asset('viewResources/personal/editar.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>