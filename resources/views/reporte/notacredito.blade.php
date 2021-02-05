<div class="row">
	<div class="col-md-12">
		<div class="nav-tabs-custom">
			<div class="tab-content">
				<div class="tab-pane active" id="tab_1-1">
					<div class="row">
						<div class="col-md-12">
							<form id="frmFiltro" method="post" action="{{url('reporte/notascredito')}}">
								<div class="row">
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
									<div class="col-md-4">
										<div class="form-group">
											<label>Personal</label>
											<select id="codPersonal" name="codPersonal" style="width: 100%;">
											</select>
										</div>
									</div>
									<div class="form-group col-md-4">
										<label for="estadoSunat">documento SUNAT</label>
										<select class="form-control" name="estadoSunat" id="estadoSunat">
											<option value="no-rechazados">No rechazados</option>
											<option value="rechazados">Rechazados</option>
										</select>
									</div>	
								</div>
								<div class="row">
									<div class="form-group col-md-4">
										<label for="fechaInicial">Fecha inicial</label>
										<div class="input-group date">
											<div class="input-group-addon">
												<i class="fa fa-calendar"></i>
											</div>
											<input type="text" class="form-control pull-right" id="fechaInicial" name="fechaInicial" autocomplete="off"
											 value="{{old('fechaInicial')}}">
										</div>
									</div>
									<div class="form-group col-md-4">
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
		<input type="button" onclick="validarEnvio(this)" class="btn btn-primary pull-right" name="reporte" value="Exportar a Excel" />
	</div>
</div>
<script src="{{asset('viewResources/reporte/notacredito.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>