@extends('template.layoutgeneral')
@section('titulo', 'Translado de producto')
@section('subTitulo', 'Almacén a oficina')
@section('cuerpoGeneral')
<div class="row">
	<div class="col-md-12">
		<div class="nav-tabs-custom">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#tab_1-1">Datos del traslado</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="tab_1-1">
					<form id="frmInsertarProductoEnviarStock" action="{{url('productoenviarstock/insertar')}}" method="post">
						<div class="row">
							<div class="col-md-12">
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label>Almacén origen</label>
											<select id="selectAlmacenOrigen" name="selectAlmacenOrigen" onchange="guardarNombreAlmacenOrigen()" style="width: 100%;">
												<option value="" selected></option>												
												@foreach ($listTAlmacen as $almacen )
												@if(old('hdNombreAlmacenOrigen') != '' && $almacen->codigoAlmacen == old('selectAlmacenOrigen'))	
													<option value="{{old('selectAlmacenOrigen')}}" selected>{{old('hdNombreAlmacenOrigen')}}</option>
												@else
													<option value="{{$almacen->codigoAlmacen}}">{{$almacen->descripcion}}</option>	
												@endif
												@endforeach										
											</select>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label>Oficina destino</label>
											<select id="selectNombreOficinaDestino" class="selectStaticNotClear" name="selectNombreOficinaDestino" onchange="guardarNombreOficinaDestino()" style="width: 100%;">
												<option value="" selected></option>
												@foreach ($listTOficina as $oficina )
												@if(old('hdNombreOficinaDestino') != '' && $oficina->codigoOficina == old('selectNombreOficinaDestino'))	
													<option value="{{old('selectNombreOficinaDestino')}}" selected>{{old('hdNombreOficinaDestino')}}</option>
												@else
													<option value="{{$oficina->codigoOficina}}">{{$oficina->descripcion}}</option>	
												@endif
												@endforeach		
											</select>
										</div>
									</div>
								</div>
								<hr>
								<div class="row">									
									<div class="col-md-12">
										<div class="form-group">
											<label>Agregue productos</label>
											<select id="selectNombreProducto" name="selectNombreProducto" style="width: 100%;" onchange="onChangeSelectNombreProducto();">											
											</select>
										</div>
									</div>
									
								</div>
								<hr>
								<div class="table-responsive">
									<table id="tableProducto" class="table table-striped verifyForCloseTable0" style="min-width: 777px;">
										<thead>
											<tr>
												<th style="display: none;"></th>
												<th class="text-center"></th>
												<th class="text-left">Nombre del producto</th>
												<th class="text-center">Tipo</th>
												<th class="text-center">Und.</th>
												<th class="text-center">Cant.</th>
												<th class="text-center">Precio compra</th>
												<th class="text-center">Precio venta</th>
												<th class="text-center">Fecha V.</th>
												<th class="text-center">Serie</th>
												<th class="text-right"></th>
											</tr>
										</thead>
										<tbody>											
										</tbody>
									</table>
								</div>
								<hr>
								<div class="row">
									<div class="col-md-12">
										{{csrf_field()}}
										<input type="hidden" id="hdNombreOficinaDestino" name="hdNombreOficinaDestino" value="{{old('hdNombreOficinaDestino')}}">
										<input type="hidden" id="hdNombreAlmacenOrigen" name="hdNombreAlmacenOrigen" value="{{old('hdNombreAlmacenOrigen')}}">
										<input type="button" class="btn btn-primary pull-right" value="Proceder con el traslado" onclick="enviarFrmInsertarProductoEnviarStock();">
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

	if('{{old('selectAlmacenOrigen')}}'!='')
	{		
		@php $i=0; @endphp

		@while(old('hdCodigoAlmacenProducto.'.$i)!='')
			oldData.push(
			{
				codigoAlmacenProducto: '{{old('hdCodigoAlmacenProducto.'.$i)}}',
				codigoPresentacion: '{{old('hdCodigoPresentacion.'.$i)}}',
				codigoUnidadMedida: '{{old('hdCodigoUnidadMedida.'.$i)}}',
				codigoBarras: '{{old('hdCodigoBarras.'.$i)}}',
				nombre: '{{old('hdNombre.'.$i)}}',
				descripcion: '{{old('hdDescripcion.'.$i)}}',
				tipo: '{{old('hdTipo.'.$i)}}',
				situacionImpuesto: '{{old('hdSituacionImpuesto.'.$i)}}',
				tipoImpuesto: '{{old('hdTipoImpuesto.'.$i)}}',
				porcentajeTributacion: '{{old('hdPorcentajeTributacion.'.$i)}}',
				cantidadMinimaAlertaStock: '{{old('hdCantidadMinimaAlertaStock.'.$i)}}',
				pesoGramosUnidad: '{{old('hdPesoGramosUnidad.'.$i)}}',
				ventaMenorUnidad: '{{old('hdVentaMenorUnidad.'.$i)}}',
				unidadesBloque: '{{old('hdUnidadesBloque.'.$i)}}',
				unidadMedidaBloque: '{{old('hdUnidadMedidaBloque.'.$i)}}',
				precioCompraUnitario: '{{old('hdPrecioCompraUnitario.'.$i)}}',
				precioVentaUnitario: '{{old('hdPrecioVentaUnitario.'.$i)}}',
				fechaVencimiento: '{{old('hdFechaVencimiento.'.$i)}}',
				cantidadProducto: '{{old('hdCantidadProducto.'.$i)}}',
				registroSerieProducto: '{{old('hdRegistroSerieProducto.'.$i)}}'
			});

			@php $i++; @endphp
		@endwhile
	}
</script>
<script src="{{asset('viewResources/productoenviarstock/insertar.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>
@endsection