@extends('template.layoutgeneral')
@section('titulo', 'Ventas sin facturación electrónica')
@section('subTitulo', 'Insertar')
@section('cuerpoGeneral')
<link rel="stylesheet" href="{{asset('viewResources/reciboventa/insertarsinfe.css?x='.env('CACHE_LAST_UPDATE'))}}">
<div class="row">
	<div class="col-md-12">
		<div class="nav-tabs-custom">
			<ul class="nav nav-tabs">
                <li class="active"><a href="#tab_1-1">Datos para la venta</a></li>
                <li class="pull-right"><a class="text-muted text-yellow"><b><i class="fa fa-warning"></i> Venta sin facturación electrónica!</b></a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="tab_1-1">
					<form id="frmInsertarReciboVenta" action="{{url('reciboventa/insertarsinfe')}}" method="post">
						<div class="row">
							<div class="col-md-12">
								<div class="row">
									<div class="form-group col-md-2">
										<label for="selectTipoRecibo">Comprobante</label>
										<select id="selectTipoRecibo" name="selectTipoRecibo" class="form-control" onchange="onChangeSelectTipoRecibo();">
											<option value="Boleta" {{(old('selectTipoRecibo')=='Boleta' ? 'selected' : '')}}>Boleta</option>
											<option value="Factura" {{(old('selectTipoRecibo')=='Factura' ? 'selected' : '')}}>Factura</option>
										</select>
									</div>
									<div id="divClienteNatural">
										<div class="form-group col-md-2">
											<label for="txtDniCliente">DNI</label>
											<input type="text" id="txtDniCliente" name="txtDniCliente" class="form-control" placeholder="00000000" onblur="onBlurTxtDniCliente();" value="{{old('txtDniCliente')}}">
										</div>
										<div class="form-group col-md-2">
											<label for="txtNombreCliente">Nom. cliente</label>
											<input type="text" id="txtNombreCliente" name="txtNombreCliente" class="form-control" placeholder="Anónimo" value="{{old('txtNombreCliente')}}">
										</div>
										<div class="form-group col-md-2">
											<label for="txtApellidoCliente">Ap. cliente</label>
											<input type="text" id="txtApellidoCliente" name="txtApellidoCliente" class="form-control" placeholder="Anónimo" value="{{old('txtApellidoCliente')}}">
										</div>
										<div class="form-group col-md-4">
											<label for="txtDireccionCliente">Dirección cliente</label>
											<input type="text" id="txtDireccionCliente" name="txtDireccionCliente" class="form-control" placeholder="Sin definir" value="{{old('txtDireccionCliente')}}">
										</div>
									</div>
									<div id="divClienteJuridico" style="display: none;">
										<div class="form-group col-md-2">
											<label for="txtRucEmpresa">RUC</label>
											<input type="text" id="txtRucEmpresa" name="txtRucEmpresa" class="form-control" placeholder="00000000000" onblur="onBlurTxtRucEmpresa();" value="{{old('txtRucEmpresa')}}">
										</div>
										<div class="form-group col-md-4">
											<label for="selectRazonSocialEmpresa">Razón social</label>
											<select id="selectRazonSocialEmpresa" name="selectRazonSocialEmpresa" style="width: 100%;" onchange="onChangeSelectRazonSocialEmpresa();">
												@if(old('selectRazonSocialEmpresa')!='')
													<option value="{{old('selectRazonSocialEmpresa')}}" selected>{{old('selectRazonSocialEmpresa')}}</option>
												@endif
											</select>
										</div>
										<div class="form-group col-md-4">
											<label for="txtDireccionEmpresa">Dirección cliente</label>
											<input type="text" id="txtDireccionEmpresa" name="txtDireccionEmpresa" class="form-control" placeholder="Av. Dirección de la empresa" value="{{old('txtDireccionEmpresa')}}">
										</div>
									</div>
								</div>
								<div class="row">
									<div class="form-group col-md-2">
										<label for="selectTipoPago">Al crédito</label>
										<select id="selectTipoPago" name="selectTipoPago" class="form-control" onchange="onChangeSelectTipoPago();">
											<option value="Al contado" {{(old('selectTipoPago')=='Al contado' ? 'selected' : '')}}>No</option>
											<option value="Al crédito" {{(old('selectTipoPago')=='Al crédito' ? 'selected' : '')}}>Si</option>
										</select>
									</div>
									<div class="form-group col-md-2">
										<label for="txtLetras">Nº de letras</label>
										<input type="text" id="txtLetras" name="txtLetras" class="form-control" readonly="readonly" value="{{old('txtLetras')}}">
									</div>
									<div class="form-group col-md-4">
										<label for="dateFechaPrimerPago">Fecha del primer pago</label>
										<div class="input-group date">
											<div class="input-group-addon">
												<i class="fa fa-calendar"></i>
											</div>
											<input type="text" id="dateFechaPrimerPago" name="dateFechaPrimerPago" class="form-control pull-right" readonly="readonly" value="{{old('dateFechaPrimerPago', date('Y-m-d'))}}">
										</div>
									</div>
									<div class="form-group col-md-4">
										<label for="selectPagoAutomatico">Modo de pago</label>
										<select id="selectPagoAutomatico" name="selectPagoAutomatico" class="form-control" disabled="true">
											<option value="Primer día laboral del mes" {{(old('selectPagoAutomatico')=='Primer día laboral del mes' ? 'selected' : '')}}>Primer día laboral del mes</option>
											<option value="Semanalmente los lunes" {{(old('selectPagoAutomatico')=='Semanalmente los lunes' ? 'selected' : '')}}>Semanalmente los lunes</option>
											<option value="Semanalmente los viernes" {{(old('selectPagoAutomatico')=='Semanalmente los viernes' ? 'selected' : '')}}>Semanalmente los viernes</option>
										</select>
									</div>
								</div>
								<hr>
								<div class="row">
									<div class="col-md-8">
										<select id="selectNombreProducto" name="selectNombreProducto" class="form-control" style="width: 100%;" onchange="onChangeSelectNombreProducto();"></select>
									</div>
									<div class="col-md-2">
										<input type="button" class="btn btn-warning" value="Prod. ext." style="width: 100%;" onclick="$('#modalProductoExterno').modal('show');">
									</div>
									<div class="col-md-2 text-center">
										<label class="form-control" style="cursor: pointer;user-select: none;">
											<input type="checkbox" id="cbxAutoCalculoCantidadPrecioVenta" style="vertical-align: middle;" onchange="onChangeCbxAutoCalculoCantidadPrecioVenta();">
											<span style="vertical-align: middle;">Auto C.</span>
										</label>
									</div>
								</div>
								<hr>
								<div class="table-responsive">
									<table id="tableProducto" class="table table-striped verifyForCloseTable1" style="min-width: 777px;">
										<thead>
											<tr>
												<th style="display: none;"></th>
												<th class="text-center"></th>
												<th class="text-left">Nombre del producto</th>
												<th class="text-left">Inf. adicional</th>
												<th class="text-center">Pres.</th>
												<th class="text-center">Und.</th>
												<th class="text-center">Precio C. U.</th>
												<th class="text-center">Precio V. U.</th>
												<th class="text-center">Cant.</th>
												<th class="text-center">Sub total</th>
												<th class="text-center">Imp.</th>
												<th class="text-center">Total</th>
												<th class="text-center"></th>
												<th class="text-right"></th>
											</tr>
										</thead>
										<tbody>
											<tr style="background-color: #ffffff;">
												<td style="display: none;"></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td class="tdSubTotalProducto text-center" style="border: 1px solid #999999;text-decoration: underline;">S/0.00</td>
												<td class="tdImpuestoAplicadoProducto text-center" style="border: 1px solid #999999;text-decoration: underline;">S/0.00</td>
												<td class="tdPrecioVentaTotalProducto text-center" style="border: 1px solid #999999;font-weight: bold;text-decoration: underline;">S/0.00</td>
												<td></td>
												<td></td>
											</tr>
										</tbody>
									</table>
								</div>
								<hr>
								<div class="row">
									<div class="col-md-4">
										<select id="selectCategoriaVentaNivelUno" name="selectCategoriaVentaNivelUno" class="form-control btnCategorias" style="display: inline-block;width: 100%;" onchange="onChangeSelectCategoriaVenta('selectCategoriaVentaNivelUno');">
											@foreach($listaTCategoriaVenta as $value)
												<option {{(old('selectCategoriaVentaNivelUno')==$value->codigoCategoriaVenta ? 'selected' : '')}} value="{{$value->codigoCategoriaVenta}}">{{$value->descripcion}}</option>
											@endforeach
										</select>
									</div>
									<div class="col-md-4">
										<select id="selectCategoriaVentaNivelDos" name="selectCategoriaVentaNivelDos" class="form-control btnCategorias" style="display: inline-block;width: 100%;" onchange="onChangeSelectCategoriaVenta('selectCategoriaVentaNivelDos');">
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
									<div class="col-md-4">
										<select id="selectCategoriaVentaNivelTres" name="selectCategoriaVentaNivelTres" class="form-control btnCategorias" style="display: inline-block;width: 100%;">
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
								<hr>
								<div class="row">
									<div class="col-md-12">
										{{csrf_field()}}
										<input type="hidden" id="hdImpuestoAplicado" name="hdImpuestoAplicado" value="{{old('hdImpuestoAplicado')}}">
										<input type="hidden" id="hdSubTotal" name="hdSubTotal" value="{{old('hdSubTotal')}}">
										<input type="hidden" id="hdTotal" name="hdTotal" value="{{old('hdTotal')}}">

										<input type="button" class="btn btn-primary pull-right mostrarIntruso" value="Proceder con la venta" onclick="enviarFrmInsertarReciboVenta();">
										<input type="button" class="btn btn-info pull-left mostrarIntruso" style="margin-right: 10px;" value="Genera proforma" onclick="enviarFrmProforma();">
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="modalProductoExterno" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Agregar producto al detalle de la venta</h4>
            </div>
            <div class="modal-body">
                <div class="row">
					<div class="form-group col-md-6">
						<label for="txtNombreProducto">Nombre</label>
						<input type="text" id="txtNombreProducto" name="txtNombreProducto" class="form-control">
					</div>
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
				</div>
				<div class="row">
					<div class="form-group col-md-3">
						<label for="txtPrecioVentaUnitarioProducto">Precio de venta U.</label>
						<input type="text" id="txtPrecioVentaUnitarioProducto" name="txtPrecioVentaUnitarioProducto" class="form-control" onkeyup="calcularPreciosImpuestos();">
					</div>
					<div class="col-md-3">
						<label for="selectSituacionImpuestoProducto">Situación del impuesto</label>
						<select id="selectSituacionImpuestoProducto" name="selectSituacionImpuestoProducto" class="form-control" onchange="onChangeSelectSituacionImpuestoProducto();">
							<option value="Afecto">Afecto</option>
							{{-- <option value="Inafecto">Inafecto</option>
							<option value="Exonerado">Exonerado</option> --}}
						</select>
					</div>
					<div class="col-md-3">
						<div class="row">
							<div class="col-md-12">
								<div class="row">
									<div class="form-group col-md-6">
										<label for="selectTipoImpuestoProducto">Tipo imp.</label>
										<select id="selectTipoImpuestoProducto" name="selectTipoImpuestoProducto" class="form-control" onchange="onChangeSelectTipoImpuestoProducto();">
											<option value="IGV">IGV</option>
											{{-- <option value="ISC">ISC</option> --}}
										</select>
									</div>
									<div class="form-group col-md-6">
										<label for="txtPorcentajeTributacionProducto">% trib.</label>
										<input type="text" id="txtPorcentajeTributacionProducto" name="txtPorcentajeTributacionProducto" class="form-control" readonly="readonly" value="{{env('PORCENTAJE_IGV')}}" onkeyup="calcularPreciosImpuestos();">
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="form-group col-md-3">
						<label for="txtImpuestoAplicadoProducto">Impuesto aplicado</label>
						<input type="text" id="txtImpuestoAplicadoProducto" name="txtImpuestoAplicadoProducto" class="form-control" readonly="readonly">
					</div>
				</div>
                <hr>
                <div class="row">
                	<div class="col-md-12">
                    	<input type="button" class="btn btn-default pull-left" value="Cerrar ventana" onclick="$('#modalProductoExterno').modal('hide');">
                    	<input type="button" class="btn btn-info pull-right" value="Agregar producto" onclick="agregarProductoDetalleVenta();">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
	var sessionCodigoReciboVentaTemp='{{Session::get('codigoReciboVenta', 'undefined')}}';
	var oldData=[];

	if('{{old('selectTipoRecibo')}}'!='')
	{
		@php $i=0; @endphp

		@while(old('hdCodigoOficinaProducto.'.$i)!='')
			oldData.push(
			{
				codigoOficinaProducto: '{{old('hdCodigoOficinaProducto.'.$i)}}',
				codigoBarrasProducto: '{{old('hdCodigoBarrasProducto.'.$i)}}',
				nombreProducto: '{{old('hdNombreProducto.'.$i)}}',
				informacionAdicionalProducto: '{{old('hdInformacionAdicionalProducto.'.$i)}}',
				tipoProducto: '{{old('hdTipoProducto.'.$i)}}',
				situacionImpuestoProducto: '{{old('hdSituacionImpuestoProducto.'.$i)}}',
				tipoImpuestoProducto: '{{old('hdTipoImpuestoProducto.'.$i)}}',
				porcentajeTributacionProducto: '{{old('hdPorcentajeTributacionProducto.'.$i)}}',
				presentacionProducto: '{{old('hdPresentacionProducto.'.$i)}}',
				unidadMedidaProducto: '{{old('hdUnidadMedidaProducto.'.$i)}}',
				pesoGramosUnidadProducto: '{{old('hdPesoGramosUnidadProducto.'.$i)}}',
				precioCompraUnitarioProducto: '{{old('hdPrecioCompraUnitarioProducto.'.$i)}}',
				precioVentaUnitarioProducto: '{{old('hdPrecioVentaUnitarioProducto.'.$i)}}',
				cantidadProducto: '{{old('hdCantidadProducto.'.$i)}}',
				subTotalProducto: '{{old('hdSubTotalProducto.'.$i)}}',
				impuestoAplicadoProducto: '{{old('hdImpuestoAplicadoProducto.'.$i)}}',
				precioVentaTotalProducto: '{{old('hdPrecioVentaTotalProducto.'.$i)}}'
			});

			@php $i++; @endphp
		@endwhile
	}
</script>
<script src="{{asset('viewResources/reciboventa/insertarsinfe.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>
@endsection