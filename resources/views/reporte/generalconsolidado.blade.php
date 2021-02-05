<div class="row">
	<div class="col-md-12">
		<div class="nav-tabs-custom">
			<div class="tab-content">
				<div class="tab-pane active" id="tab_1-1">
					<div class="row">
						<div class="col-md-12">
							<form id="frmFiltro" target="_blank" method="get" action="{{url('reporte/generalconsolidado')}}">
								<div class="row">
									<div class="form-group col-md-4">
										<div class="form-group">
											<label>Almacén</label>
											<select id="codAlmacen" name="codAlmacen" class="selectStaticNotClear" onchange="guardarNombreAlmacenOrigen()" style="width: 100%;">
												<option value="" selected></option>
												@foreach ($listTAlmacen as $almacen )
												<option value="{{$almacen->codigoAlmacen}}">{{$almacen->descripcion}}</option>	
												@endforeach	
											</select>
										</div>
									</div>
									<div class="form-group col-md-4">
										<div class="form-group">
											<label>Oficina</label>
											<select id="codOficina" name="codOficina" class="selectStaticNotClear" onchange="guardarNombreOficinaOrigen()"
											 style="width: 100%;">
												<option value="" selected></option>
												@foreach ($listTOficina as $oficina )
												<option value="{{$oficina->codigoOficina}}">{{$oficina->descripcion}}</option>
												@endforeach
											</select>
										</div>
									</div>
									<div class="form-group col-md-4">
										<label for="fechaInicial">Fecha</label>
										<div class="input-group date">
											<div class="input-group-addon">
												<i class="fa fa-calendar"></i>
											</div>
											<input type="text" class="form-control pull-right" id="fechaInicial" name="fechaInicial" autocomplete="off"
											 value="{{old('fechaInicial')}}">
										</div>
									</div>
									<!-- <div class="form-group col-md-4">
										<label>&nbsp;</label>
										<div class="checkbox">
											<label>
											  <input type="checkbox" id="trasladosRetiros" name="trasladosRetiros"> Incluir traslados y retiros
											</label>
										</div>
									</div> -->
									<div class="form-group col-md-6" style="display: none;">
										<label for="fechaFinal">Fecha final</label>
										<div class="input-group date">
											<div class="input-group-addon">
												<i class="fa fa-calendar"></i>
											</div>
											<input type="text" class="form-control pull-right" id="fechaFinal" name="fechaFinal" autocomplete="off"
											 value="{{old('fechaFinal')}}">
										</div>
									</div>
								</div>
								<div class="row">
									<input type="hidden" name="reporte" id="tipoReporte" />
								</div>
								<div class="hidden">
									{{csrf_field()}}
									<input type="hidden" id="hdOficina" name="hdOficina" value="{{old('hdOficina')}}">
									<input type="hidden" id="hdAlmacen" name="hdAlmacen" value="{{old('hdAlmacen')}}">
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
		<input type="button" class="btn btn-default" value="Cerrar ventana" onclick="$('#dialogoGeneralModal').modal('hide');">
		<input type="button" onclick="validarEnvio(this)" class="btn btn-primary pull-right" name="reporte" value="Generar Reporte" />
	</div>
</div>
<script src="{{asset('viewResources/reporte/generalconsolidado.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>