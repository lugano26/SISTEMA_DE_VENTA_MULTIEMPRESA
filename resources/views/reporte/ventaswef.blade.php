<div class="row">
	<div class="col-md-12">
		<div class="nav-tabs-custom">
			<div class="tab-content">
				<div class="tab-pane active" id="tab_1-1">
					<div class="row">
						<div class="col-md-12">
							<form id="frmFiltro" method="post" action="{{url('reporte/ventaswef')}}">
							<div class="row">								
								<div class="col-md-4">
									<div class="form-group">
										<label>Oficina</label>
										<select id="codOficina" name="codOficina" class="selectStaticNotClear" onchange="guardarNombreOficinaOrigen()" style="width: 100%;">
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
								<div class="col-md-4">
									<label for="tipoComprobante">Tipo comprobante</label>
									<select class="form-control" name="tipoComprobante" id="tipoComprobante">
										<option value="Indistinto" {{ old('tipoComprobante') == 'Indistinto' ? 'selected' : '' }}>Indistinto</option>
										<option value="Boleta" {{ old('tipoComprobante') == 'Boleta' ? 'selected' : '' }}>Boleta</option>
										<option value="Factura" {{ old('tipoComprobante') == 'Factura' ? 'selected' : '' }}>Factura</option>
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
										<input type="text" class="form-control pull-right" id="fechaInicial" name="fechaInicial" autocomplete="off" value="{{old('fechaInicial')}}">
									</div>
								</div>
								<div class="form-group col-md-4">
									<label for="fechaFinal">Fecha final</label>
									<div class="input-group date">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control pull-right" id="fechaFinal" name="fechaFinal" autocomplete="off" value="{{old('fechaFinal')}}">
									</div>
								</div>	
								<div class="form-group col-md-4">
									<label for="filtroVenta">Filtro venta</label>
									<select class="form-control" name="filtroVenta" id="filtroVenta">
										<option value="all">Todas las ventas</option>
										<option value="=0">Consignación gratuita</option>
										<option value="!=0">Sin consignación gratuita</option>
									</select>
								</div>	
							</div>
							<div class="row">
								<div class="form-group col-md-4">
									<label>Filtro categoría</label>
									<select id="selectCategoriaVentaNivelUno" name="selectCategoriaVentaNivelUno" class="form-control" style="display: inline-block;width: 100%;" onchange="onChangeSelectCategoriaVenta('selectCategoriaVentaNivelUno');">
										<option class="optionClearCategoriaVenta"></option>
										@foreach($listaTCategoriaVenta as $value)
											<option {{(old('selectCategoriaVentaNivelUno')==$value->codigoCategoriaVenta ? 'selected' : '')}} value="{{$value->codigoCategoriaVenta}}">{{$value->descripcion}}</option>
										@endforeach
									</select>
								</div>
								<div class="form-group col-md-4">
									<label>&nbsp;</label>
									<select id="selectCategoriaVentaNivelDos" name="selectCategoriaVentaNivelDos" class="form-control" style="display: inline-block;width: 100%;" onchange="onChangeSelectCategoriaVenta('selectCategoriaVentaNivelDos');">
										<option class="optionClearCategoriaVenta"></option>
										@foreach($listaTCategoriaVenta as $value)
											@if(!$value->estado) @continue; @endif
											@foreach($value->tcategoriaventachild as $item)
												@if(!$item->estado) @continue; @endif
												<option {{(old('selectCategoriaVentaNivelDos')==$item->codigoCategoriaVenta ? 'selected' : '')}} class="option{{$value->codigoCategoriaVenta}}" value="{{$item->codigoCategoriaVenta}}" style="display: none;">{{$item->descripcion}}</option>
											@endforeach
										@endforeach
									</select>
								</div>
								<div class="form-group col-md-4">
									<label>&nbsp;</label>
									<select id="selectCategoriaVentaNivelTres" name="selectCategoriaVentaNivelTres" class="form-control" style="display: inline-block;width: 100%;">
										<option class="optionClearCategoriaVenta"></option>
										@foreach($listaTCategoriaVenta as $value)
											@if(!$value->estado) @continue; @endif
											@foreach($value->tcategoriaventachild as $item)
												@if(!$item->estado) @continue; @endif
												@foreach($item->tcategoriaventachild as $v)
													@if(!$v->estado) @continue; @endif
													<option {{(old('selectCategoriaVentaNivelTres')==$v->codigoCategoriaVenta ? 'selected' : '')}} class="option{{$item->codigoCategoriaVenta}}" value="{{$v->codigoCategoriaVenta}}" style="display: none;">{{$v->descripcion}}</option>
												@endforeach
											@endforeach
										@endforeach
									</select>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-md-4">
									<label for="tipoVenta">Tipo ventas</label>
									<select class="form-control" name="tipoVenta" id="tipoVenta">
										<option value="conforme">No anulados</option>
										<option value="anulado">Anulados</option>
										<option value="todos" selected>Indistinto</option>
									</select>
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
		<input type="button" onclick="validarEnvio(this)" class="btn btn-primary pull-right" name="reporte" value="Exportar a Excel"/>
	</div>
</div>
<script src="{{asset('viewResources/reporte/ventaswef.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>