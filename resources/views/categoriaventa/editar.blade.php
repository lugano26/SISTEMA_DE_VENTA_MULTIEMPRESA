<form id="frmEditarCategoriaVenta" action="{{url('categoriaventa/editar')}}" method="post">
	<div class="tab-pane active" id="tab_1-1">
		<div class="row">
			<div class="form-group col-md-12">
				<label for="txtDescripcion">Descripción</label>
				<input type="text" id="txtDescripcion" autocomplete="off" name="txtDescripcion" class="form-control" placeholder="Obligatorio" value="{{$tCategoriaVenta->descripcion}}">
			</div>
		</div>
		<hr>
		<div class="row">
			<div class="col-md-12">
				{{csrf_field()}}
				<input type="hidden" name="hdCodigoCategoriaVenta" value="{{$tCategoriaVenta->codigoCategoriaVenta}}">
				<input type="button" class="btn btn-default pull-left" value="Cerrar ventana" onclick="$('#dialogoGeneralModal').modal('hide');">
				<input type="button" class="btn btn-primary pull-right" value="Guardar cambios" onclick="enviarFmrEditarCategoriaVenta();">
			</div>
		</div>
	</div>
</form>
<script src="{{asset('viewResources/categoriaventa/editar.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>