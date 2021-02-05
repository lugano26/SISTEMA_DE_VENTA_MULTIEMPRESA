@extends('template.layoutgeneral')
@section('titulo', 'Inventario')
@section('subTitulo', 'listado')
@section('cuerpoGeneral')
<div class="row">
    <div class="col-md-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab_1-1">Lista de inventario</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tab_1-1">
                    <div class="row">
                        <div class="col-md-12">
                            <div>
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <form id="frmSearch" method="get" action="{{url('inventario/ver')}}"
                                            onsubmit="validarExpresion(event)">
                                            <div class="input-group input-group-sm">
                                                <input type="hidden" name="searchPerformance"
                                                    id="searchPerformanceInput">
                                                <input id="textSearch" type="text" class="form-control"
                                                    onkeyup="searchItem(event);"
                                                    placeholder="Buscar por nombre, código de barras, nombre ambiente o espacio, sección (Enter)"
                                                    name="q" value="{{ isset($q) ? $q : '' }}" autofocus>
                                                <span class="input-group-btn">
                                                    <button type="buttom" class="btn btn-primary btn-flat"><i class="fa fa-search"></i></button>
                                                </span>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="table-responsive">
                                <table id="tableAlmacenProducto" class="table table-striped" style="min-width: 777px;">
                                    <thead>
                                        <tr>
                                            <th>Nombre</th>
                                            <th class="text-center">Código barras</th>
                                            <th class="text-center">Serie</th>
                                            <th class="text-center">Modelo</th>
                                            <th class="text-center">Peso (Kg.)</th>
                                            <th class="text-center">Estado</th>
                                            <th class="text-center">Ambiente o espacio</th>
                                            <th class="text-center">Sección</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($listaTInventario as $key => $value)
											@php $color=null; @endphp
											
											@switch($value->estado)
												@case('Nuevo')
													@php $color='<span class="label label-success">Nuevo</span>'; @endphp
													@break
												@case('Buen estado')
													@php $color='<span class="label label-info">Buen estado</span>'; @endphp
													@break
												@case('Con daños leves')
													@php $color='<span class="label label-default">Con daños leves</span>'; @endphp
													@break
												@case('Deteriorado')
													@php $color='<span class="label label-warning">Deteriorado</span>'; @endphp
													@break
												@case('Inservible')
													@php $color='<span class="label label-danger">Inservible</span>'; @endphp
													@break
											@endswitch

											<tr class="elementoBuscar">
												<td>{{$value->nombre}}</td>
												<td class="text-center">{{$value->codigoBarras}}</td>
												<td class="text-center">{{$value->serie}}</td>
												<td class="text-center">{{$value->modelo}}</td>
												<td class="text-center">{{$value->pesoKg}}</td>
												<td class="text-center">{!!$color!!}</td>
												<td class="text-center">{{$value->tambienteespacio->tambiente->nombre}}</td>
												<td class="text-center">{{$value->tambienteespacio->seccion === 0 ? 'Sin sección' : $value->tambienteespacio->seccion}}</td>
												<td class="text-right">
													<span class="btn btn-default btn-xs glyphicon glyphicon-pencil" data-toggle="tooltip" data-placement="left" title="Editar" onclick="dialogoAjax('dialogoGeneral', 'modal-lg', 'Editar', { _token : '{{csrf_token()}}', codigoInventario : '{{$value->codigoInventario}}' }, '{{url('inventario/editar')}}', 'POST', null, null, false, true);"></span>
													<span class="btn btn-default btn-xs glyphicon glyphicon-trash" data-toggle="tooltip" data-placement="left" title="Eliminar" data-urlredirect="{{url('inventario/eliminar', $value->codigoInventario)}}" onclick="eliminarPago(this)"></span>
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

<!-- Modals -->
<div class="modal fade in" id="modalAmbiente" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Agregar ambiente o espacio</h4>
            </div>
            <div class="modal-body">
                <form id="fmrModalAmbiente">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="txtNombreAmbiente">Nombre</label>
                            <input type="text" id="txtNombreAmbiente" name="txtNombreAmbiente" placeholder="Obligatorio"
                                class="form-control">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="txtCodigo">Código</label>
                            <input type="text" id="txtCodigo" name="txtCodigo" placeholder="Opcional"
                                class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="selectTipoAmbiente">Tipo</label>
                            <select id="selectTipoAmbiente" name="selectTipoAmbiente" class="form-control" style="width: 100%;">
                                <option>Oficina</option>
                                <option>Cuarto</option>
                                <option>Local</option>
                                <option>Anaquel</option>
                                <option>Estante</option>
                                <option>Almacén</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="txtNivelUbicacion">Piso</label>
                            <input type="text" id="txtNivelUbicacion" name="txtNivelUbicacion" class="form-control" placeholder="Obligatorio">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label>Referencia</label>
                            <textarea class="form-control" name="txtReferenciaUbicacion" id="txtReferenciaUbicacion"
                                rows="3" placeholder="Obligatorio"></textarea>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <input type="button" class="btn btn-default pull-left" value="Cerrar ventana"
                                onclick="$('#modalAmbiente').modal('hide');">
                            <input type="button" class="btn btn-info pull-right" value="Agregar ambiente"
                                onclick="enviarFrmAmbiente();">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade in" id="modalEspacio" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Configurar secciones</h4>
            </div>
            <div class="modal-body">
                <form id="frmModalSeccion">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" name="txtNumberSeccion" id="txtNumberSeccion">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="chkSinSecciones" class="minimal" checked> Sin secciones
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12 table-responsive">
                            <table class="table" id="containerButtonsSeccions" style="display: none;">
                                @for($i = 0; $i < 5 ; $i++)
									<tr>
										@for($j = 1; $j <= 10 ; $j++) <td style="width: 10px"><button
											class="btn btn-info btn-block disabled buttonSeccion" id="buttonSeccion{{$j + ($i * 10)}}" data-index="{{$j + ($i * 10)}}">{{$j + ($i * 10)}}</button></td>
										@endfor
									</tr>
                                @endfor
                            </table>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <input type="button" class="btn btn-default pull-left" value="Cerrar ventana" onclick="$('#modalEspacio').modal('hide');">
                            <input type="button" class="btn btn-info pull-right" value="Guardar cambios" onclick="enviarFrmEspacio();">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- EndModals -->

<script src="{{asset('viewResources/inventario/ver.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>
@endsection