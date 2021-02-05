<div class="row">
	<div class="col-md-12">
		<div class="nav-tabs-custom">
			<div class="tab-content">
				<div class="tab-pane active" id="tab_1-1">
					<div class="row">
						<div class="col-md-12">
							<form id="frmFiltro" method="post" action="{{url('reporte/documentogeneradosunat')}}">
								<div class="row">
									<div class="form-group col-md-4">
										<div class="form-group">
											<label>Cliente</label>
											<input type="text" class="form-control" name="txtCliente" id="txtCliente" placeholder="Documento o nombre del cliente">											
										</div>
									</div>
									<div class="form-group col-md-4">
										<div class="form-group">
											<label>Número comprobante</label>
											<input type="text" class="form-control" name="txtNumeroComprobante" id="txtNumeroComprobante" placeholder="F000-00000000">											
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label>Tipo documento</label>
											<select id="tipoDocumento" name="tipoDocumento" class="selectStaticNotClear" style="width: 100%;">
												<option value="Todos" selected>Todos</option>
												@if((Session::get('facturacionElectronica') && strpos(Session::get('rol'), 'Súper usuario')!==false))
												<option value="Resumen diario">Resumen diario</option>
												@endif
												<option value="Boleta">Boleta</option>
												<option value="Factura">Factura</option>
												<option value="Nota de débito">Nota de débito</option>
												<option value="Nota de crédito">Nota de crédito</option>
												<option value="Guía de remisión">Guía de remisión</option>
											</select>
										</div>
									</div>	
								</div>
								<div class="row">																
									<div class="col-md-4">
										<div class="form-group">
											<label>Estado</label>
											<select id="tipoEstado" name="tipoEstado" class="selectStaticNotClear" style="width: 100%;">
												<option value="Todos">Todos</option>
												<option value="Aprobado">Aprobado</option>
												<option value="Rechazado" selected>Rechazado</option>
											</select>
										</div>
									</div>
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
<script src="{{asset('viewResources/reporte/documentogeneradosunat.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>