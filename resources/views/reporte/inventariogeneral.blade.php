<div class="row">
	<div class="col-md-12">
		<div class="nav-tabs-custom">
			<div class="tab-content">
				<div class="tab-pane active" id="tab_1-1">
					<div class="row">
						<div class="col-md-12">
							<form id="frmFiltro" method="post" action="{{url('reporte/inventariogeneral')}}">
								<div class="row">
									<div class="form-group col-md-6">
										<div class="form-group">
											<label>Almacén</label>
											<select id="codAlmacen" name="codAlmacen" class="selectStaticNotClear"
												onchange="guardarNombreAlmacenOrigen()" style="width: 100%;">
												<option value="" selected></option>
												@foreach ($listTAlmacen as $almacen )
												<option value="{{$almacen->codigoAlmacen}}">{{$almacen->descripcion}}
												</option>
												@endforeach
											</select>
										</div>
									</div>
									<div class="form-group col-md-6">
										<div class="form-group">
											<label>Oficina</label>
											<select id="codOficina" name="codOficina" class="selectStaticNotClear"
												onchange="guardarNombreOficinaOrigen()" style="width: 100%;">
												<option value="" selected></option>
												@foreach ($listTOficina as $oficina )
												<option value="{{$oficina->codigoOficina}}">{{$oficina->descripcion}}
												</option>
												@endforeach
											</select>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="form-group col-md-6">
										<label for="selectAmbiente">Ambiente</label>
										<select name="selectAmbiente" id="selectAmbiente" style="width: 100%;"
											class="form-control selectStaticNotClear">
											<option></option>
										</select>
									</div>
									<div class="form-group col-md-6">
										<label for="txtEstado">Estado</label>
										<select name="txtEstado" id="txtEstado" class="form-control">
											<option selected="1">Indistinto</option>
											<option>Nuevo</option>
											<option>Buen estado</option>
											<option>Con daños leves</option>
											<option>Deteriorado</option>
											<option>Inservible</option>
										</select>
									</div>
								</div>

								<div class="row">
									<input type="hidden" name="reporte" id="tipoReporte" />
								</div>
								<div class="hidden">
									{{csrf_field()}}
									<input type="hidden" id="hdAlmacen" name="hdAlmacen" value="{{old('hdAlmacen')}}">
									<input type="hidden" id="hdOficina" name="hdOficina" value="{{old('hdOficina')}}">
									<input type="hidden" id="hdAmbiente" name="hdAmbiente"
										value="{{old('hdAmbiente')}}">
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<input type="button" class="btn btn-default" value="Cerrar ventana"
			onclick="$('#dialogoGeneralModal').modal('hide');">
		<input type="button" onclick="validarEnvio(this)" class="btn btn-primary pull-right" name="reporte"
			value="Exportar a Excel" />
	</div>
</div>
<script src="{{asset('viewResources/reporte/inventariogeneral.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>