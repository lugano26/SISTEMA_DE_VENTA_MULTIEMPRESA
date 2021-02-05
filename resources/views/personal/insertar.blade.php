@extends('template.layoutgeneral')
@section('titulo', 'Personal')
@section('subTitulo', 'Insertar')
@section('cuerpoGeneral')
<div class="row">
	<div class="col-md-12">
		<div class="nav-tabs-custom">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#tab_1-1">Datos del personal</a></li>
			</ul>
			<div class="tab-content">
				<form id="frmInsertarPersonal" action="{{url('personal/insertar')}}" method="post">
					<div class="tab-pane active" id="tab_1-1">
						<div class="row">
							<div class="form-group col-md-6">
								<label for="txtDni">DNI</label>
								<input type="text" id="txtDni" name="txtDni" class="form-control" placeholder="Obligatorio" value="{{old('txtDni')}}">
							</div>
							<div class="form-group col-md-6">
								<label for="txtNombre">Nombre</label>
								<input type="text" id="txtNombre" name="txtNombre" class="form-control" placeholder="Obligatorio" value="{{old('txtNombre')}}">
							</div>
						</div>
						<div class="row">
							<div class="form-group col-md-6">
								<label for="txtApellido">Apellido</label>
								<input type="text" id="txtApellido" name="txtApellido" class="form-control" placeholder="Obligatorio" value="{{old('txtApellido')}}">
							</div>
							<div class="form-group col-md-6">
								<label for="txtTelefono">Teléfono</label>
								<input type="text" id="txtTelefono" name="txtTelefono" class="form-control" placeholder="Obligatorio" value="{{old('txtTelefono')}}">
							</div>
						</div>
						<div class="row">
							<div class="form-group col-md-12">
								<label for="txtDireccion">Dirección</label>
								<input type="text" id="txtDireccion" name="txtDireccion" class="form-control" placeholder="Obligatorio" value="{{old('txtDireccion')}}">
							</div>
						</div>
						<div class="row">
							<div class="form-group col-md-6">
								<label>Sexo</label>
								<div class="form-control" style="border: none;">
									<label style="cursor: pointer;">
										<input type="radio" id="radioSexoM" name="radioSexo" {{(old('radioSexo')==null ? 'checked="checked"' : (old('radioSexo')) ? 'checked="checked"' : '')}} value="1">
										Masculino
									</label>
									&nbsp;&nbsp;
									<label style="cursor: pointer;">
										<input type="radio" id="radioSexoF" name="radioSexo" {{(old('radioSexo')!=null && !old('radioSexo')) ? 'checked="checked"' : ''}} value="0">
										Femenino
									</label>
								</div>
							</div>
							<div class="form-group col-md-6">
								<label for="txtCorreoElectronico">Correo electrónico</label>
								<input type="text" id="txtCorreoElectronico" name="txtCorreoElectronico" class="form-control" placeholder="Obligatorio" value="{{old('txtCorreoElectronico')}}">
							</div>
						</div>
						<div class="row">
							<div class="form-group col-md-6">
								<label for="passContraseniaUsuario">Contraseña</label>
								<input type="password" id="passContraseniaUsuario" name="passContraseniaUsuario" class="form-control" placeholder="Obligatorio" value="{{old('passContraseniaUsuario')}}">
							</div>
							<div class="form-group col-md-6">
								<label for="passContraseniaRepitaUsuario">Repita contraseña</label>
								<input type="password" id="passContraseniaRepitaUsuario" name="passContraseniaRepitaUsuario" class="form-control" placeholder="Obligatorio" value="{{old('passContraseniaRepitaUsuario')}}">
							</div>
						</div>
						<hr>
						<div class="row">
							<div class="form-group col-md-12">
								<label for="selectRolUsuario">Rol</label>
								<select id="selectRolUsuario" name="selectRolUsuario[]" class="form-control selectStatic" multiple style="width: 100%;">
									<option value="Administrador" {{old('selectRolUsuario')!=null ? (in_array('Administrador', old('selectRolUsuario')) ? 'selected' : '') : ''}}>Administrador</option>
									<option value="Ventas" {{old('selectRolUsuario')!=null ? (in_array('Ventas', old('selectRolUsuario')) ? 'selected' : '') : ''}}>Ventas</option>
									<option value="Revocador" {{old('selectRolUsuario')!=null ? (in_array('Revocador', old('selectRolUsuario')) ? 'selected' : '') : ''}}>Revocador</option>
									<option value="Almacenero" {{old('selectRolUsuario')!=null ? (in_array('Almacenero', old('selectRolUsuario')) ? 'selected' : '') : ''}}>Almacenero</option>
									<option value="Inventariador" {{old('selectRolUsuario')!=null ? (in_array('Inventariador', old('selectRolUsuario')) ? 'selected' : '') : ''}}>Inventariador</option>
									<option value="Reporteador" {{old('selectRolUsuario')!=null ? (in_array('Reporteador', old('selectRolUsuario')) ? 'selected' : '') : ''}}>Reporteador</option>
								</select>
							</div>
						</div>
						<hr>
						<div class="row">
							<div class="col-md-12">
								{{csrf_field()}}
								<input type="button" class="btn btn-primary pull-right" value="Registrar datos ingresados" onclick="enviarFrmInsertarPersonal();">
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<script src="{{asset('viewResources/personal/insertar.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>
@endsection