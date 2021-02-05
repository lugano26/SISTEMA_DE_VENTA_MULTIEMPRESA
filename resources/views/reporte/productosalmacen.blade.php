<div class="row">
	<div class="col-md-12">
		<div class="nav-tabs-custom">
			<div class="tab-content">
				<div class="tab-pane active" id="tab_1-1">
					<div class="row">
						<div class="col-md-12">
							<form id="frmFiltro" method="post" action="{{url('reporte/productosalmacen')}}">
							<div class="row">							
								<div class="form-group col-md-6">
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
								<div class="form-group col-md-6">
									<div class="form-group">
										<label>Filtro productos</label>
										<select id="filtroProductos" name="filtroProductos" class="selectStaticNotClear" style="width: 100%;">
											<option value="!=0" selected>Cantidad diferente a 0</option>
											<option value="all">Todos los productos</option>											
											<option value="=0">Cantidad igual a 0</option>											
										</select>
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
<script src="{{asset('viewResources/reporte/productosalmacen.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>