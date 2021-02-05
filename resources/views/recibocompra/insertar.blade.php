@extends('template.layoutgeneral')
@section('titulo', 'Compra')
@section('subTitulo', 'Insertar')
@section('cuerpoGeneral')
<div class="row">
	<div class="col-md-12">
		<div class="nav-tabs-custom">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#tab_1-1">Datos de la compra</a></li>
			</ul>
			<div class="tab-content">
				<form id="frmInsertarReciboCompra" action="{{url('recibocompra/insertar')}}" method="post">
					<div class="tab-pane active" id="tab_1-1">
						<hr>
						<h4>Datos generales de la compra</h4>
						<hr>
						<div class="row">
							<div class="form-group col-md-3">
								<label for="selectProveedor">Proveedor</label>
								<select id="selectProveedor" name="selectProveedor" style="width: 100%;">
									@foreach($listaTProveedor as $value)
										<option value="{{$value->documentoIdentidad.'-'.$value->nombre}}">{{$value->documentoIdentidad.'-'.$value->nombre}}</option>
									@endforeach
								</select>
							</div>
							<div class="form-group col-md-3">
								<label for="selectTipoRecibo">Tipo de comprobante</label>
								<select id="selectTipoRecibo" name="selectTipoRecibo" class="form-control" onchange="onChangeSelectTipoRecibo();">
									<option value="Ninguno" {{old('selectTipoRecibo')=='Ninguno' ? 'selected' : ''}}>Ninguno</option>
									<option value="Boleta" {{old('selectTipoRecibo')=='Boleta' ? 'selected' : ''}}>Boleta</option>
									<option value="Factura" {{old('selectTipoRecibo')=='Factura' ? 'selected' : ''}}>Factura</option>
								</select>
							</div>
							<div class="col-md-3 no-padding">
								<div class="col-md-6 form-group">
									<label for="txtNumeroRecibo">Nº comp.</label>
									<input type="text" id="txtNumeroRecibo" name="txtNumeroRecibo" class="form-control" readonly="readonly" value="{{old('txtNumeroRecibo')}}">
								</div>
								<div class="col-md-6 form-group">
									<label for="txtNumeroGuiaRemision">Nº G. R.</label>
									<input type="text" id="txtNumeroGuiaRemision" name="txtNumeroGuiaRemision" class="form-control" readonly="readonly" value="{{old('txtNumeroGuiaRemision')}}">
								</div>
							</div>
							<div class="form-group col-md-3">
								<label for="dateFechaComprobanteEmitido">Fecha del comprobante</label>
								<div class="input-group date">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" id="dateFechaComprobanteEmitido" name="dateFechaComprobanteEmitido" class="form-control pull-right" readonly="readonly" value="{{old('dateFechaComprobanteEmitido')!=null ? old('dateFechaComprobanteEmitido') : date('Y-m-d')}}">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="form-group col-md-3">
								<label for="selectTipoPago">Tipo de pago</label>
								<select id="selectTipoPago" name="selectTipoPago" class="form-control" onchange="onChangeSelectTipoPago();">
									<option value="Al contado" {{old('selectTipoPago')=='Al contado' ? 'selected' : ''}}>Al contado</option>
									<option value="Al crédito" {{old('selectTipoPago')=='Al crédito' ? 'selected' : ''}}>Al crédito</option>
								</select>
							</div>
							<div class="form-group col-md-3">
								<label for="dateFechaPagar">Fecha a pagar</label>
								<div class="input-group date">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" id="dateFechaPagar" name="dateFechaPagar" class="form-control pull-right" readonly="readonly" value="{{old('dateFechaPagar')!=null ? old('dateFechaPagar') : date('Y-m-d')}}">
								</div>
							</div>
							<div class="form-group col-md-6">
								<label for="selectCodigoOficina">Enviar productos directamente a una oficina</label>
								<select id="selectCodigoOficina" name="selectCodigoOficina" class="selectStatic" style="width: 100%;">
									<option value=""></option>
									@foreach($listaTOficina as $value)
										<option value="{{$value->codigoOficina}}" {{old('selectCodigoOficina')==$value->codigoOficina ? 'selected' : ''}}>{{$value->descripcion}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<hr>
						<h4>Detalle de la compra (Productos)</h4>
						<hr>
						<div class="row">
							<div class="col-md-12">
								<input type="button" class="btn btn-info" value="Agregar producto al detalle de la compra" style="width: 100%;" onclick="$('#modalTemp').modal('show');">
							</div>
						</div>
						<hr>
						<div class="row">
							<div class="col-md-12">
								<div>
									<input type="text" id="txtBuscar" name="txtBuscar" class="form-control" autocomplete="off" placeholder="Ingrese datos de búsqueda (Enter)" onkeyup="filtrarHtml('tableProducto', this.value, false, 0, event);">
								</div>
								<hr>
								<div class="table-responsive">
									<table id="tableProducto" class="table table-striped verifyForCloseTable1" style="min-width: 777px;">
										<thead>
											<tr>
												<th style="display: none;"></th>
												<th class="text-center">Codigo Barras</th>
												<th>Nombre</th>
												<th class="text-center">Situación del impuesto</th>
												<th class="text-center">Cantidad</th>
												<th class="text-center">Precio de venta U.</th>
												<th class="text-center">Impuesto aplicado</th>
												<th class="text-center">Precio de compra T.</th>
												<th></th>
											</tr>
										</thead>
										<tbody>
											<tr style="background-color: #ffffff;">
												<td style="display: none;"></td>
												<td class="text-left"></td>
												<td class="text-left"></td>
												<td class="text-center"></td>
												<td class="text-center"></td>
												<td class="text-center"></td>
												<td class="text-center"></td>
												<td class="tdPrecioCompraTotalProducto text-center" style="border: 1px solid #999999;font-weight: bold;text-decoration: underline;">S/0.00</td>
												<td class="text-right"></td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<hr>
						<div class="row">
							<div class="col-md-12">
								{{csrf_field()}}
								<input type="button" class="btn btn-warning pull-left" value="Restaurar detalle, de memoria" onclick="restaurarDetalleMemoria();">
								<input type="button" class="btn btn-primary pull-right" value="Registrar datos ingresados" onclick="enviarFrmInsertarReciboCompra();">
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="modalTemp" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" style="min-width: 77%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Agregar producto al detalle de la compra</h4>
            </div>
            <div class="modal-body">
                <div class="row">
					<div class="form-group col-md-3">
						<label for="selectTipoProducto">Tipo de producto</label>
						<select id="selectTipoProducto" name="selectTipoProducto" class="form-control">
							<option value="Genérico">Genérico</option>
							<option value="Comercial">Comercial</option>
						</select>
					</div>
					<div class="form-group col-md-3">
						<label for="txtCodigoBarrasProducto">Código de barras</label>
						<input type="text" id="txtCodigoBarrasProducto" name="txtCodigoBarrasProducto" class="form-control">
					</div>
					<div class="form-group col-md-6">
						<label for="txtNombreProducto">Nombre</label>
						<div id="divSearchTxtNombreProducto" class="input-group">
							<div class="input-group-addon" style="cursor: pointer;" onclick="changeSearchNombreProducto();">
								<i class="fa fa-search"></i>
							</div>
							<input type="text" id="txtNombreProducto" name="txtNombreProducto" class="form-control pull-right">
						</div>
						<div id="divNotSearchSelectNombreProducto" class="input-group" style="display: none;">
							<div class="input-group-addon" style="cursor: pointer;" onclick="changeSearchNombreProducto();">
								<i class="fa fa-arrow-left"></i>
							</div>
							<select id="selectNombreProducto" name="selectNombreProducto" style="width: 100%;" onchange="onChangeSelectNombreProducto();"></select>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="form-group col-md-3">
						<label for="selectCodigoUnidadMedidaProducto">Unidad de medida</label>
						<select id="selectCodigoUnidadMedidaProducto" name="selectCodigoUnidadMedidaProducto" class="form-control" style="width: 100%;">
							@foreach($listaTUnidadMedida as $value)
								<option value="{{$value->codigoUnidadMedida}}">{{$value->nombre}}</option>
							@endforeach
						</select>
					</div>
					<div class="form-group col-md-3">
						<label for="selectCodigoPresentacionProducto">Presentación</label>
						<select id="selectCodigoPresentacionProducto" name="selectCodigoPresentacionProducto" class="form-control" style="width: 100%;">
							@foreach($listaTPresentacion as $value)
								<option value="{{$value->codigoPresentacion}}">{{$value->nombre}}</option>
							@endforeach
						</select>
					</div>
					<div class="form-group col-md-3">
						<label for="dateFechaVencimientoProducto">Fecha de vencimiento</label>
						<div class="input-group date">
							<div class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" id="dateFechaVencimientoProducto" name="dateFechaVencimientoProducto" class="form-control pull-right datepicker">
						</div>
					</div>
					<div class="form-group col-md-3">
						<label for="txtCantidadProducto">Cantidad</label>
						<input type="text" id="txtCantidadProducto" name="txtCantidadProducto" class="form-control" onkeyup="calcularPreciosImpuestos();">
					</div>
				</div>
				<div class="row">
					<div class="form-group col-md-3">
						<label for="txtPrecioCompraTotalProducto">Precio de compra T.</label>
						<input type="text" id="txtPrecioCompraTotalProducto" name="txtPrecioCompraTotalProducto" class="form-control" onkeyup="calcularPreciosImpuestos();">
					</div>
					<div class="form-group col-md-3">
						<label for="txtPrecioCompraUnitarioProducto">Precio de compra U.</label>
						<input type="text" id="txtPrecioCompraUnitarioProducto" name="txtPrecioCompraUnitarioProducto" class="form-control" readonly="readonly">
					</div>
					<div class="form-group col-md-3">
						<label for="txtPorcentajeGananciaProducto">% de ganancia</label>
						<input type="text" id="txtPorcentajeGananciaProducto" name="txtPorcentajeGananciaProducto" class="form-control" value="50" onkeyup="calcularPreciosImpuestos();">
					</div>
					<div class="form-group col-md-3">
						<label for="txtPrecioVentaUnitarioProducto">Precio de venta U.</label>
						<input type="text" id="txtPrecioVentaUnitarioProducto" name="txtPrecioVentaUnitarioProducto" class="form-control" onkeyup="calcularPreciosImpuestos();">
					</div>
				</div>
				<div class="row">
					<div class="form-group col-md-3">
						<label>Ventas parciales</label>
						<div class="form-control" style="border: none;">
							<label style="cursor: pointer;">
								<input type="radio" id="radioVentaMenorUnidadProductoSi" name="radioVentaMenorUnidadProducto" value="1">
								Si
							</label>
							&nbsp;&nbsp;
							<label style="cursor: pointer;">
								<input type="radio" id="radioVentaMenorUnidadProductoNo" name="radioVentaMenorUnidadProducto" value="0" checked="checked">
								No
							</label>
						</div>
					</div>
					<div class="form-group col-md-3">
						<label for="txtCantidadMinimaAlertaStockProducto">Alerta cantidad mínima</label>
						<input type="text" id="txtCantidadMinimaAlertaStockProducto" name="txtCantidadMinimaAlertaStockProducto" class="form-control" value="7">
					</div>
					<div class="form-group col-md-3">
						<label for="txtPesoGramosUnidadProducto">Peso en gramos por Und.</label>
						<input type="text" id="txtPesoGramosUnidadProducto" name="txtPesoGramosUnidadProducto" class="form-control" value="10">
					</div>
					<div class="form-group col-md-3">
						<label>&nbsp;</label>
						<div class="checkbox">
							<label data-toggle="tooltip" data-original-title="Generar productos con código de barras en serie">
							  <input type="checkbox" id="generacionCodigoBarras"> Registro en serie
							</label>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="form-group col-md-3">
						<label for="selectSituacionImpuestoProducto">Situación del impuesto</label>
						<select id="selectSituacionImpuestoProducto" name="selectSituacionImpuestoProducto" class="form-control" onchange="onChangeSelectSituacionImpuestoProducto();">
							<option value="Afecto">Afecto</option>
							{{-- <option value="Inafecto">Inafecto</option>
							<option value="Exonerado">Exonerado</option> --}}
						</select>
					</div>
					<div class="form-group col-md-3">
						<label for="selectTipoImpuestoProducto">Tipo de impuesto</label>
						<select id="selectTipoImpuestoProducto" name="selectTipoImpuestoProducto" class="form-control" onchange="onChangeSelectTipoImpuestoProducto();">
							<option value="IGV">IGV</option>
							{{-- <option value="ISC">ISC</option> --}}
						</select>
					</div>
					<div class="form-group col-md-3">
						<label for="txtPorcentajeTributacionProducto">% de tributación</label>
						<input type="text" id="txtPorcentajeTributacionProducto" name="txtPorcentajeTributacionProducto" class="form-control" readonly="readonly" value="{{env('PORCENTAJE_IGV')}}" onkeyup="calcularPreciosImpuestos();">
					</div>
					<div class="form-group col-md-3">
						<label for="txtImpuestoAplicadoProducto">Impuesto aplicado compra</label>
						<input type="text" id="txtImpuestoAplicadoProducto" name="txtImpuestoAplicadoProducto" class="form-control" readonly="readonly">
					</div>
				</div>
                <hr>
                <div class="row">
                	<div class="col-md-12">
                    	<input type="button" class="btn btn-default pull-left" value="Cerrar ventana" onclick="$('#modalTemp').modal('hide');">
                    	<input type="button" class="btn btn-info pull-right" value="Agregar producto" onclick="agregarProductoDetalleCompra();">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
	var oldSelectProveedor='{{old('selectProveedor')}}';
	var oldData=[];

	@php $i=0; @endphp

	@while(old('hdNombreProducto.'.$i)!='')
		oldData.push(
		{
			codigoBarrasProducto: '{{old('hdCodigoBarrasProducto.'.$i)}}',
			nombreProducto: '{{old('hdNombreProducto.'.$i)}}',
			codigoPresentacionProducto: '{{old('hdCodigoPresentacionProducto.'.$i)}}',
			codigoUnidadMedidaProducto: '{{old('hdCodigoUnidadMedidaProducto.'.$i)}}',
			tipoProducto: '{{old('hdTipoProducto.'.$i)}}',
			situacionImpuestoProducto: '{{old('hdSituacionImpuestoProducto.'.$i)}}',
			tipoImpuestoProducto: '{{old('hdTipoImpuestoProducto.'.$i)}}',
			porcentajeTributacionProducto: '{{old('hdPorcentajeTributacionProducto.'.$i)}}',
			impuestoAplicadoProducto: '{{old('hdImpuestoAplicadoProducto.'.$i)}}',
			cantidadMinimaAlertaStockProducto: '{{old('hdCantidadMinimaAlertaStockProducto.'.$i)}}',
			pesoGramosUnidadProducto: '{{old('hdPesoGramosUnidadProducto.'.$i)}}',
			precioCompraTotalProducto: '{{old('hdPrecioCompraTotalProducto.'.$i)}}',
			precioCompraUnitarioProducto: '{{old('hdPrecioCompraUnitarioProducto.'.$i)}}',
			precioVentaUnitarioProducto: '{{old('hdPrecioVentaUnitarioProducto.'.$i)}}',
			cantidadProducto: '{{old('hdCantidadProducto.'.$i)}}',
			radioVentaMenorUnidadProducto: '{{old('hdRadioVentaMenorUnidadProducto.'.$i)}}',
			teFechaVencimientoProducto: '{{old('dateFechaVencimientoProducto.'.$i)}}',
			registroSerieProducto: '{{old('hdRegistroSerieProducto.'.$i)}}'
		});

		@php $i++; @endphp
	@endwhile
</script>
<script src="{{asset('viewResources/recibocompra/insertar.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>
@endsection