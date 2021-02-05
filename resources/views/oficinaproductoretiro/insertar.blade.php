@extends('template.layoutgeneral')
@section('titulo', 'Retiro de productos')
@section('subTitulo', 'de oficina')
@section('cuerpoGeneral')
<div class="row">
	<div class="col-md-12">
		<div class="nav-tabs-custom">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#tab_1-1">Datos del retiro</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="tab_1-1">
					<form id="frmInsertarOficinaProductoRetiro" action="{{url('oficinaproductoretiro/insertar')}}" method="post">
						<div class="row">
							<div class="col-md-12">	
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label>Oficina</label>
											<select id="selectNombreOficinaOrigen" name="selectNombreOficinaOrigen" onchange="guardarNombreOficinaOrigen()" style="width: 100%;">
												<option value="" selected></option>
												@foreach ($listTOficina as $oficina )
												@if(old('hdNombreOficinaOrigen') != '' && $oficina->codigoOficina == old('selectNombreOficinaOrigen'))	
													<option value="{{old('selectNombreOficinaOrigen')}}" selected>{{old('hdNombreOficinaOrigen')}}</option>
												@else
													<option value="{{$oficina->codigoOficina}}">{{$oficina->descripcion}}</option>	
												@endif
												@endforeach	
											</select>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label>Agregue productos</label>
											<select id="selectNombreProducto" name="selectNombreProducto" style="width: 100%;" onchange="onChangeSelectNombreProducto();">											
											</select>
										</div>
									</div>
									<div class="col-md-12">
										<div class="form-group">
											<label>Descripción del retiro</label>
											<input  type="text" class="form-control" id="hdDescripcionRetiro" name="hdDescripcionRetiro" value="{{old('hdDescripcionRetiro')}}">
										</div>
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
												<th class="text-center">Tipo</th>												
												<th class="text-center">Cant.</th>
												<th class="text-center">Precio compra</th>
												<th class="text-center">Precio venta</th>
												<th class="text-center">Fecha V.</th>
												<th class="text-center">Monto perdido</th>
												<th class="text-right"></th>
											</tr>
										</thead>
										<tbody>
											<tr style="background-color: #ffffff;">
												<td style="display: none;"></td>
												<td class="text-center"></td>
												<td class="text-left"></td>
												<td class="text-center"></td>
												<td class="text-center"></td>
												<td class="text-center"></td>
												<td class="text-center"></td>
												<td class="text-center"></td>
												<td class="tdMontoTotalPerdida text-center" style="border: 1px solid #999999;font-weight: bold;text-decoration: underline;">S/0.00</td>
												<td class="text-right"></td>
											</tr>
										</tbody>
									</table>
								</div>
								<hr>
								<div class="row">
									<div class="col-md-12">
										{{csrf_field()}}
										<input type="hidden" id="hdNombreOficinaOrigen" name="hdNombreOficinaOrigen" value="{{old('hdNombreOficinaOrigen')}}">
										<input type="hidden" id="hdCodigoOficinaOrigen" name="hdCodigoOficinaOrigen" value="{{old('hdCodigoOficinaOrigen')}}">
										<input type="button" class="btn btn-primary pull-right" value="Proceder con el retiro" onclick="enviarFrmInsertarOficinaProductoRetiro();">
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
<script>
	var oldData=[];

	if('{{old('hdCodigoOficinaProducto.0')}}'!='')
	{
		@php $i=0; @endphp

		@while(old('hdCodigoOficinaProducto.'.$i)!='')
			oldData.push(
			{
				codigoOficinaProducto: '{{old('hdCodigoOficinaProducto.'.$i)}}',
				presentacion: '{{old('hdPresentacion.'.$i)}}',
				unidadMedida: '{{old('hdUnidadMedida.'.$i)}}',
				codigoBarras: '{{old('hdCodigoBarras.'.$i)}}',
				nombre: '{{old('hdNombre.'.$i)}}',
				tipo: '{{old('hdTipo.'.$i)}}',
				situacionImpuesto: '{{old('hdSituacionImpuesto.'.$i)}}',
				tipoImpuesto: '{{old('hdTipoImpuesto.'.$i)}}',
				porcentajeTributacion: '{{old('hdPorcentajeTributacion.'.$i)}}',
				cantidadMinimaAlertaStok: '{{old('hdCantidadMinimaAlertaStok.'.$i)}}',
				ventaMenorUnidad: '{{old('hdVentaMenorUnidad.'.$i)}}',
				unidadesBloque: '{{old('hdUnidadesBloque.'.$i)}}',
				unidadMedidaBloque: '{{old('hdUnidadMedidaBloque.'.$i)}}',
				precioCompraUnitario: '{{old('hdPrecioCompraUnitario.'.$i)}}',
				precioVentaUnitario: '{{old('hdPrecioVentaUnitario.'.$i)}}',
				fechaVencimiento: '{{old('hdFechaVencimiento.'.$i)}}',
				cantidadProducto: '{{old('hdCantidadProducto.'.$i)}}',
				montoPerdido: '{{old('hdMontoPerdido.'.$i)}}'
			});

			@php $i++; @endphp
		@endwhile
	}
</script>
<script src="{{asset('viewResources/oficinaproductoretiro/insertar.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>
@endsection