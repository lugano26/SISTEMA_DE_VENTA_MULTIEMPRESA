<div class="row">
	<div class="col-md-12">
		<div class="nav-tabs-custom">
			<div class="tab-content">
				<div class="tab-pane active" id="tab_1-1">
					<div class="row">
						<div class="col-md-12">
							<form id="frmFiltro" method="post" action="{{url('reporte/compras')}}">
							<div class="row">
								<div class="form-group col-md-3">
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
								<div class="form-group col-md-3">
									<label for="tipoComprobante">Tipo comprobante</label>
									<select class="form-control" name="tipoComprobante" id="tipoComprobante">
										<option value="Indistinto" {{ old('tipoComprobante') == 'Indistinto' ? 'selected' : '' }}>Indistinto</option>
										<option value="Ninguno" {{ old('tipoComprobante') == 'Ninguno' ? 'selected' : '' }}>Ninguno</option>
										<option value="Boleta" {{ old('tipoComprobante') == 'Boleta' ? 'selected' : '' }}>Boleta</option>
										<option value="Factura" {{ old('tipoComprobante') == 'Factura' ? 'selected' : '' }}>Factura</option>
									</select>
								</div>
								<div class="form-group col-md-3">
									<label for="fechaInicial">Fecha inicial</label>
									<div class="input-group date">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control pull-right" id="fechaInicial" name="fechaInicial" autocomplete="off" value="{{old('fechaInicial')}}">
									</div>
								</div>
								<div class="form-group col-md-3">
									<label for="fechaFinal">Fecha final</label>
									<div class="input-group date">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control pull-right" id="fechaFinal" name="fechaFinal" autocomplete="off" value="{{old('fechaFinal')}}">
									</div>
								</div>							
							</div>
							<div class="row">
								<input type="hidden" name="reporte" id="tipoReporte" />
							</div>
							<div class="hidden">
								{{csrf_field()}}
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
		<input type="button" onclick="validarEnvio(this)" class="btn btn-primary pull-right" name="reporte" value="Exportar a Excel"/>
	</div>
</div>
<script src="{{asset('viewResources/reporte/compras.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>