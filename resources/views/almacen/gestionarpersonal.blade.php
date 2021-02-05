<form id="frmGestionarPersonalAlmacen" action="{{url('almacen/gestionarpersonal')}}" method="post">
	<div class="tab-pane active" id="tab_1-1">
		<div class="row">
			<div class="col-md-12">
				<select id="selectCodigoPersonal" name="selectCodigoPersonal[]" class="select" multiple style="width: 100%;">
					@foreach($listaTPersonal as $value)
						<?php $usuarioAsignado=false; ?>

						@foreach($tAlmacen->tpersonaltalmacen as $item)
							@if($value->codigoPersonal==$item->codigoPersonal)
								<?php $usuarioAsignado=true;break; ?>
							@endif
						@endforeach

						<option value="{{$value->codigoPersonal}}" {{$usuarioAsignado ? 'selected' : ''}}>{{$value->tusuario->nombreUsuario}}</option>
					@endforeach
				</select>
			</div>
		</div>
		<hr>
		<div class="row">
			<div class="col-md-12">
				{{csrf_field()}}
				<input type="hidden" name="hdCodigoAlmacen" value="{{$tAlmacen->codigoAlmacen}}">
				<input type="button" class="btn btn-default pull-left" value="Cerrar ventana" onclick="$('#dialogoGeneralModal').modal('hide');">
				<input type="button" class="btn btn-primary pull-right" value="Guardar cambios" onclick="enviarFrmGestionarPersonalAlmacen();">
			</div>
		</div>
	</div>
</form>
<script src="{{asset('viewResources/almacen/gestionarpersonal.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>