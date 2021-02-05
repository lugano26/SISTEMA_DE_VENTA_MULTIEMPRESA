<div class="row">
	<div class="col-md-12">
		<div class="nav-tabs-custom">
			<div class="tab-content">
				<div class="tab-pane active" id="tab_1-1">
					<div class="row">
						<form id="frmFiltro" method="post" action="{{url('reporte/caja')}}">							
						<div class="col-md-12">
							<div class="row">									
								<div class="form-group col-md-6">
									<label for="fechaInicial">Fecha inicial</label>
									<div class="input-group date">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control pull-right" id="fechaInicial" name="fechaInicial" autocomplete="off" value="{{old('fechaInicial')}}">
									</div>
								</div>
								<div class="form-group col-md-6">
									<label for="fechaFinal">Fecha final</label>
									<div class="input-group date">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control pull-right" id="fechaFinal" name="fechaFinal" autocomplete="off" value="{{old('fechaFinal')}}">
									</div>
								</div>
								<div class="hidden">
									{{csrf_field()}}
									<input type="hidden" name="reporte" id="tipoReporte" />
								</div>
							</div>
						</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<input type="button" class="btn btn-default" value="Cerrar ventana" onclick="$('#dialogoGeneralModal').modal('hide');">
		<input type="button" onclick="validarEnvio(this)" class="btn btn-primary pull-right" name="reporte" value="Exportar a Excel"/>
	</div>
</div>
<script src="{{asset('viewResources/reporte/caja.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>