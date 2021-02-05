<form id="frmCambiarContraseniaPersonal" action="{{url('personal/cambiarcontrasenia')}}" method="post">
	<div class="tab-pane active" id="tab_1-1">
		<div class="row">
			<div class="form-group col-md-12">
				<label for="passContraseniaActualUsuario">Contraseña actual</label>
				<input type="password" id="passContraseniaActualUsuario" name="passContraseniaActualUsuario" class="form-control" placeholder="Obligatorio">
			</div>
		</div>
		<hr>
		<div class="row">
			<div class="form-group col-md-6">
				<label for="passContraseniaUsuario">Nueva contraseña</label>
				<input type="password" id="passContraseniaUsuario" name="passContraseniaUsuario" class="form-control" placeholder="Obligatorio">
			</div>
			<div class="form-group col-md-6">
				<label for="passContraseniaRepitaUsuario">Repita nueva contraseña</label>
				<input type="password" id="passContraseniaRepitaUsuario" name="passContraseniaRepitaUsuario" class="form-control" placeholder="Obligatorio">
			</div>
		</div>
		<hr>
		<div class="row">
			<div class="col-md-12">
				{{csrf_field()}}
				<input type="hidden" name="hdCodigoPersonal" value="{{$tPersonal->codigoPersonal}}">
				<input type="button" class="btn btn-default pull-left" value="Cerrar ventana" onclick="$('#dialogoGeneralModal').modal('hide');">
				<input type="button" class="btn btn-primary pull-right" value="Guardar cambios" onclick="enviarFrmCambiarContraseniaPersonal();">
			</div>
		</div>
	</div>
</form>
<script src="{{asset('viewResources/personal/cambiarcontrasenia.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>