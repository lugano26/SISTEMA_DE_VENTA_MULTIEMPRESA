<form id="frmEditarAgrupadoAlmacenProducto" action="{{url('almacenproducto/editaragrupado')}}" method="post">
	<div class="tab-pane active" id="tab_1-1">
		<div class="row">
			<div class="form-group col-md-3">
				<label for="selectTipoProducto">Tipo de producto</label>
				<select id="selectTipoProducto" name="selectTipoProducto" class="form-control">
					<option value="Genérico" {{$tAlmacenProducto->tipo=='Genérico' ? 'selected' : ''}}>Genérico</option>
					<option value="Comercial" {{$tAlmacenProducto->tipo=='Comercial' ? 'selected' : ''}}>Comercial</option>
				</select>
			</div>
			<div class="form-group col-md-3">
				<label for="txtCodigoBarrasProducto">Código de barras</label>
				<input type="text" id="txtCodigoBarrasProducto" name="txtCodigoBarrasProducto" class="form-control" value="{{$tAlmacenProducto->codigoBarras}}">
			</div>
			<div class="form-group col-md-6">
				<label for="txtNombreProducto">Nombre</label>
				<input type="text" id="txtNombreProducto" name="txtNombreProducto" class="form-control" value="{{$tAlmacenProducto->nombre}}">
			</div>
		</div>
		<div class="row">
			<div class="form-group col-md-3">
				<label for="selectCodigoUnidadMedidaProducto">Unidad de medida</label>
				<select id="selectCodigoUnidadMedidaProducto" name="selectCodigoUnidadMedidaProducto" class="form-control" style="width: 100%;">
					@foreach($listaTUnidadMedida as $value)
						<option value="{{$value->codigoUnidadMedida}}" {{$tAlmacenProducto->codigoUnidadMedida==$value->codigoUnidadMedida ? 'selected' : ''}}>{{$value->nombre}}</option>
					@endforeach
				</select>
			</div>
			<div class="form-group col-md-3">
				<label for="selectCodigoPresentacionProducto">Presentación</label>
				<select id="selectCodigoPresentacionProducto" name="selectCodigoPresentacionProducto" class="form-control" style="width: 100%;">
					@foreach($listaTPresentacion as $value)
						<option value="{{$value->codigoPresentacion}}" {{$tAlmacenProducto->codigoPresentacion==$value->codigoPresentacion ? 'selected' : ''}}>{{$value->nombre}}</option>
					@endforeach
				</select>
			</div>
			<div class="form-group col-md-3">
				<label for="dateFechaVencimientoProducto">Fecha de vencimiento</label>
				<div class="input-group date">
					<div class="input-group-addon">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" id="dateFechaVencimientoProducto" name="dateFechaVencimientoProducto" class="form-control pull-right datepicker" value="{{substr($tAlmacenProducto->fechaVencimiento, 0, 10)=='1111-11-11' ? '' : substr($tAlmacenProducto->fechaVencimiento, 0, 10)}}">
				</div>
			</div>
			<div class="form-group col-md-3" style="display: none;">
				<label for="txtPrecioCompraUnitarioProducto">Precio de compra U.</label>
				<input type="text" id="txtPrecioCompraUnitarioProducto" name="txtPrecioCompraUnitarioProducto" class="form-control" readonly="readonly" value="{{$tAlmacenProducto->precioCompraUnitario}}">
			</div>
			<div class="form-group col-md-3">
				<label for="txtPesoGramosUnidadProducto">Peso en gramos por Und.</label>
				<input type="text" id="txtPesoGramosUnidadProducto" name="txtPesoGramosUnidadProducto" class="form-control" value="{{$tAlmacenProducto->pesoGramosUnidad}}">
			</div>
		</div>
		<div class="row">
			<div class="form-group col-md-3">
				<label for="txtPorcentajeGananciaProducto">% de ganancia</label>
				<input type="text" id="txtPorcentajeGananciaProducto" name="txtPorcentajeGananciaProducto" class="form-control" onkeyup="calcularPreciosImpuestos(false);">
			</div>
			<div class="form-group col-md-3">
				<label for="txtPrecioVentaUnitarioProducto">Precio de venta U.</label>
				<input type="text" id="txtPrecioVentaUnitarioProducto" name="txtPrecioVentaUnitarioProducto" class="form-control" onkeyup="calcularPreciosImpuestos(false);" value="{{$tAlmacenProducto->precioVentaUnitario}}">
			</div>
			<div class="form-group col-md-3">
				<label>Ventas parciales</label>
				<div class="form-control" style="border: none;">
					<label style="cursor: pointer;">
						<input type="radio" id="radioVentaMenorUnidadProductoSi" name="radioVentaMenorUnidadProducto" value="1" {{$tAlmacenProducto->ventaMenorUnidad ? 'checked="checked"' : ''}}>
						Si
					</label>
					&nbsp;&nbsp;
					<label style="cursor: pointer;">
						<input type="radio" id="radioVentaMenorUnidadProductoNo" name="radioVentaMenorUnidadProducto" value="0" {{!($tAlmacenProducto->ventaMenorUnidad) ? 'checked="checked"' : ''}}>
						No
					</label>
				</div>
			</div>
			<div class="form-group col-md-3">
				<label for="txtCantidadMinimaAlertaStockProducto">Alerta cantidad mínima</label>
				<input type="text" id="txtCantidadMinimaAlertaStockProducto" name="txtCantidadMinimaAlertaStockProducto" class="form-control" value="{{$tAlmacenProducto->cantidadMinimaAlertaStock}}">
			</div>
		</div>
		<div class="row">
			<div class="form-group col-md-3">
				<label for="selectSituacionImpuestoProducto">Situación del impuesto</label>
				<select id="selectSituacionImpuestoProducto" name="selectSituacionImpuestoProducto" class="form-control" onchange="onChangeSelectSituacionImpuestoProducto();">
					<option value="Afecto" {{$tAlmacenProducto->situacionImpuesto=='Afecto' ? 'selected' : ''}}>Afecto</option>
					{{-- <option value="Inafecto" {{$tAlmacenProducto->situacionImpuesto=='Inafecto' ? 'selected' : ''}}>Inafecto</option>
					<option value="Exonerado" {{$tAlmacenProducto->situacionImpuesto=='Exonerado' ? 'selected' : ''}}>Exonerado</option> --}}
				</select>
			</div>
			<div class="form-group col-md-3">
				<label for="selectTipoImpuestoProducto">Tipo de impuesto</label>
				<select id="selectTipoImpuestoProducto" name="selectTipoImpuestoProducto" class="form-control" onchange="onChangeSelectTipoImpuestoProducto();">
					<option value="IGV" {{$tAlmacenProducto->tipoImpuesto=='IGV' ? 'selected' : ''}}>IGV</option>
					{{-- <option value="ISC" {{$tAlmacenProducto->tipoImpuesto=='ISC' ? 'selected' : ''}}>ISC</option> --}}
				</select>
			</div>
			<div class="form-group col-md-3">
				<label for="txtPorcentajeTributacionProducto">% de tributación</label>
				<input type="text" id="txtPorcentajeTributacionProducto" name="txtPorcentajeTributacionProducto" class="form-control" readonly="readonly" value="{{env('PORCENTAJE_IGV')}}" onkeyup="calcularPreciosImpuestos(false);" value="{{$tAlmacenProducto->porcentajeTributacion}}">
			</div>
			<div class="form-group col-md-3">
				<label for="txtImpuestoAplicadoProducto">Impuesto aplicado compra</label>
				<input type="text" id="txtImpuestoAplicadoProducto" name="txtImpuestoAplicadoProducto" class="form-control" readonly="readonly">
			</div>
		</div>
		<hr>
		<div class="row">
			<div class="col-md-12">
				{{csrf_field()}}
				<input type="hidden" name="hdCodigoBarrasProducto" value="{{$tAlmacenProducto->codigoBarras}}">
				<input type="hidden" name="hdNombreProducto" value="{{$tAlmacenProducto->nombre}}">
				<input type="button" class="btn btn-default pull-left" value="Cerrar ventana" onclick="$('#dialogoGeneralModal').modal('hide');">
				<input type="button" class="btn btn-primary pull-right" value="Guardar cambios" onclick="enviarFrmEditarAgrupadoAlmacenProducto();">
			</div>
		</div>
	</div>
</form>
<script src="{{asset('viewResources/almacenproducto/editaragrupado.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>