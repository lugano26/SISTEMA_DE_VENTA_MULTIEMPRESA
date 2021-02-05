@extends('template.layoutgeneral')
@section('titulo', 'Productos de almacén')
@section('subTitulo', 'Agrupado')
@section('cuerpoGeneral')
<link rel="stylesheet" href="{{asset('viewResources/almacenproducto/veragrupado.css?x='.env('CACHE_LAST_UPDATE'))}}">
<div class="row">
	<div class="col-md-12">
		<div class="nav-tabs-custom">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#tab_1-1">Lista agrupada de productos</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="tab_1-1">
					<div class="row">
						<div class="col-md-12">
							<div>
								<div class="row">
									<div class="form-group col-md-{{!((strpos(Session::get('rol'), 'Súper usuario')!==false || strpos(Session::get('rol'), 'Administrador')!==false)) ? '12' : '8'}}">
										<form id="frmSearch" method="get" action="{{url('almacenproducto/veragrupado')}}" onsubmit="validarExpresion(event)">
											<div class="input-group input-group-sm">
												<input type="hidden" name="searchPerformance" id="searchPerformanceInput">
												<input id="textSearch" type="text" class="form-control" onkeyup="searchItem(event);" placeholder="Buscar por nombre, código de barras (Enter)" name="q" value="{{ !empty($q) ? $q : '' }}" autofocus>
												<span class="input-group-btn">
													<button type="buttom" class="btn btn-primary btn-flat"><i class="fa fa-search" ></i></button>
												</span>
											</div>
										</form>
									</div>
									@if(strpos(Session::get('rol'), 'Súper usuario')!==false || strpos(Session::get('rol'), 'Administrador')!==false)
										<div class="form-group col-md-4">
											<input type="button" class="btn btn-danger btn-sm btn-block" value="Eliminar productos sin stock" onclick="confirmacion(function(){ $('#modalLoading').modal('show');window.location.href='{{url('almacenproducto/borrarproductosinstock')}}'; });">
										</div>
									@endif
								</div>
							</div>
							<hr>
							<div class="table-responsive">
								<table id="tableAlmacenProducto" class="table table-striped" style="min-width: 777px;">
									<thead>
										<tr>
											<th class="text-center">Código barras</th>
											<th>Nombre</th>
											<th class="text-center">Und.</th>
											<th class="text-center">Pres.</th>
											<th class="text-center">Tipo</th>
											<th class="text-center">Sit. Imp.</th>
											<th class="text-center">Tipo Imp.</th>
											<th class="text-center">Precio C. U.</th>
											<th class="text-center">Precio V. U.</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										@foreach($listaTAlmacenProductoAgrupado as $key => $value)
											<tr class="elementoBuscar">
												<td class="text-center">{{$value->codigoBarras}}</td>
												<td>
													<span style="cursor: default;text-decoration: underline;" data-toggle="tooltip" data-placement="top" data-html="true" title="
														<div>
															<table style='width: 270px;'>
																<thead>
																	<tr>
																		<th style='border: 1px solid #ffffff;padding-left: 10px; padding-right: 10px;text-align: left;'>Sucursal o almacén</th>
																		<th style='border: 1px solid #ffffff;padding-left: 10px; padding-right: 10px;text-align: center;'>Cantidad</th>
																	</tr>
																</thead>
																<tbody>
																	@foreach($value->distribucionProductoOficina as $item)
																		<tr>
																			<td style='border: 1px solid #ffffff;padding-left: 10px; padding-right: 10px;text-align: left;'>
																				{{$item->toficina->descripcion}}
																			</td>
																			<td style='border: 1px solid #ffffff;padding-left: 10px;padding-right: 10px;text-align: center;'>
																				{{$item->cantidad}}
																			</td>
																		</tr>
																	@endforeach
																	@foreach($value->distribucionProductoAlmacen as $item)
																		<tr>
																			<td style='border: 1px solid #ffffff;padding-left: 10px; padding-right: 10px;text-align: left;'>
																				{{$item->talmacen->descripcion}}
																			</td>
																			<td style='border: 1px solid #ffffff;padding-left: 10px;padding-right: 10px;text-align: center;'>
																				{{$item->cantidad}}
																			</td>
																		</tr>
																	@endforeach
																</tbody>
															</table>
														</div>
													">{{$value->nombre}}</span>
												</td>
												<td class="text-center">{{$value->tunidadmedida->nombre}}</td>
												<td class="text-center">{{$value->tpresentacion->nombre}}</td>
												<td class="text-center">{{$value->tipo}}</td>
												<td class="text-center">{{$value->situacionImpuesto}}</td>
												<td class="text-center">{{$value->tipoImpuesto}} ({{$value->porcentajeTributacion}}%)</td>
												<td class="text-center">S/{{$value->precioCompraUnitario}}</td>
												<td class="text-center">S/{{$value->precioVentaUnitario}}</td>
												<td>
													<span class="btn btn-default btn-xs glyphicon glyphicon-pencil" data-toggle="tooltip" data-placement="left" title="Editar" onclick="dialogoAjax('dialogoGeneral', 'modal-lg', 'Producto agrupado (Editar)', { _token : '{{csrf_token()}}', codigoAlmacenProducto : '{{$value->codigoAlmacenProducto}}' }, '{{url('almacenproducto/editaragrupado')}}', 'POST', null, null, false, true);"></span>
												</td>
											</tr>
										@endforeach
									</tbody>
								</table>
							</div>
						</div>
					</div>
					{!! $pagination !!}
				</div>
			</div>
		</div>
	</div>
</div>
<script src="{{asset('viewResources/almacenproducto/veragrupado.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>
@endsection