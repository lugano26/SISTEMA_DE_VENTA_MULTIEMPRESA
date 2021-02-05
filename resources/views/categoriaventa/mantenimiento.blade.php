@extends('template.layoutgeneral')
@section('titulo', 'Categoría de ventas')
@section('subTitulo', 'Manteminiento')
@section('cuerpoGeneral')
<div class="row">
	<div class="col-md-12">
		<div class="nav-tabs-custom">
			<ul class="nav nav-tabs">
            <li class="active"><a href="#tab_1-1">{{$categoriaVenta != null ? 'Subcategorias' : 'Categorias'}} de venta</a></li>
            @if($categoriaVenta != null && $categoriaVenta->tcategoriaVenta != null)
				<li class="pull-right header"><span><a href="{{url('categoriaventa/mantenimiento/'. $categoriaVenta->tcategoriaventa->codigoCategoriaVenta)}}" class="btn btn-warning btn-sm"><i class="fa fa-hand-o-left"></i> Regresar a categorías nivel 2</a></span></li>
			@elseif($categoriaVenta != null)
				<li class="pull-right header"><span><a href="{{url('categoriaventa/mantenimiento/')}}" class="btn btn-warning btn-sm"><i class="fa fa-hand-o-left"></i> Regresar a categorías nivel 1</a></span></li>
            @endif
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="tab_1-1">
					<div class="row">
						@if($categoriaVenta != null)
							<div class="col-xs-12 text-center" style="margin-bottom: 20px">
								<h4>Categoría padre: <strong>{{$categoriaVenta->descripcion}}</strong></h4>
							</div>
						@endif
						<div class="col-md-12">
							<div>
								<form id="frmInsertCategoriaVenta" class="form-horizontal" method="post" action="{{url('categoriaventa/mantenimiento')}}">
									<div class="form-group">
                                        <div class="col-sm-8 col-md-9 col-lg-10" style="margin-bottom: 15px">
                                            <input type="text" autocomplete="off" class="form-control" id="txtDescripcion" name="txtDescripcion" value="{{old('txtDescripcion')}}" placeholder="Descripción de la categoría">
                                            <input type="hidden" name="codigoCategoriaVentaPadre" value="{{$categoriaVenta->codigoCategoriaVenta ?? old('codigoCategoriaVentaPadre')}}">
                                        </div>
                                        <div class="col-sm-4 col-md-3 col-lg-2 text-right">
                                            {{csrf_field()}}
                                            <input type="button" class="btn btn-flat btn-block btn-primary" value="Registrar categoría" onclick="enviarFrmInsertarCategoriaVenta();">
                                        </div>
                                    </div>
								</form>
							</div>
							<hr>
							<div class="table-responsive">
								<table id="tableReciboVenta" class="table table-striped" style="min-width: 777px;">
									<thead>
										<tr>
											<th>Descripción</th>
											<th>Categoría padre</th>
											<th class="text-center">Estado</th>
											<th class="text-center">Fecha registro</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
                                            @foreach($listaCategoriaVenta as $value)
											<tr>
												<td>{{$value->descripcion}}</td>
                                                <td>{{$value->tcategoriaventa->descripcion ?? '-'}}</td>
                                                <td class="text-center">
                                                    <span class="label {{$value->estado ? 'label-success' : 'label-danger'}}">{{$value->estado ? 'Activo' : 'Eliminado'}}</span>
                                                </td>
												<td class="text-center">{{$value->created_at}}</td>
												<td class="text-right">
                                                    @if(!$value->estado || ($value->tcategoriaventa != null && $value->tcategoriaventa->tcategoriaventa != null))
                                                        <a disabled class="btn btn-default btn-xs glyphicon glyphicon-tags" data-toggle="tooltip" data-placement="left" title="Gestionar subcategorías" ></a>
                                                    @else
                                                        <a href="{{url('categoriaventa/mantenimiento/'. $value->codigoCategoriaVenta)}}" class="btn btn-default btn-xs glyphicon glyphicon-tags" data-toggle="tooltip" data-placement="left" title="Gestionar subcategorías" ></a>
													@endif

													<span class="btn btn-default btn-xs glyphicon glyphicon-pencil" data-toggle="tooltip" data-placement="left" title="Editar" onclick="dialogoAjax('dialogoGeneral', null, '{{$value->descripcion}} (Editar)', { _token : '{{csrf_token()}}', codigoCategoriaVenta : '{{$value->codigoCategoriaVenta}}' }, '{{url('categoriaventa/editar')}}', 'POST', null, null, false, true);"></span>
													
													@if($value->estado)
														<span class="btn btn-default btn-xs glyphicon glyphicon-ban-circle" data-toggle="tooltip" data-placement="left" title="Eliminar" data-urlredirect="{{url('categoriaventa/eliminar', $value->codigoCategoriaVenta)}}" onclick="cambiarEstadoCategoria(this)"></span>
													@else
                                                        <span class="btn btn-default btn-xs glyphicon glyphicon-ok" data-toggle="tooltip" data-placement="left" title="Habilitar" data-urlredirect="{{url('categoriaventa/habilitar', $value->codigoCategoriaVenta)}}" onclick="cambiarEstadoCategoria(this)"></span>
													@endif
												</td>
											</tr>
										@endforeach
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script src="{{asset('viewResources/categoriaventa/mantenimiento.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>
@endsection