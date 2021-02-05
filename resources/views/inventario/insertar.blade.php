@extends('template.layoutgeneral')
@section('titulo', 'Inventario')
@section('subTitulo', 'Insertar')
@section('cuerpoGeneral')
<div class="row">
    <div class="col-md-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab_1-1">Datos del inventario</a></li>
            </ul>
            <div class="tab-content">
                <form id="frmInsertarInventario" action="{{url('inventario/insertar')}}" method="post">
                    <div class="tab-pane active" id="tab_1-1">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="selectAmbiente">Ambiente o espacio</label>
                                <input type="hidden" id="txtMaskNameAmbiente" name="txtMaskNameAmbiente"
                                    value="{{old('txtMaskNameAmbiente')}}">
                                <div class="input-group input-group">
                                    <select name="selectAmbiente" id="selectAmbiente" style="width: 100%;" class="form-control selectStatic">
                                        <option></option>
                                        @foreach($listaTAmbiente as $value)
                                        <option {{(old('selectAmbiente')==$value->codigoAmbiente ? 'selected' : '')}}
                                            value="{{$value->codigoAmbiente}}">
                                            {{$value->nombre . ($value->codigo !== '' ? ' (' . $value->codigo . ')' : '')}}
                                        </option>
                                        @endforeach
                                    </select>
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default" data-toggle="tooltip"
                                            data-placement="top" title="Insertar nuevo"
                                            onclick="$('#modalAmbiente').modal('show');"><i
                                                class="fa fa-plus"></i></button>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="selectEspacio">Sección</label>
                                <div class="input-group input-group">
                                    <select name="selectEspacio" id="selectEspacio"
                                        class="form-control selectStatic"  style="width: 100%;"></select>
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default" data-toggle="tooltip"
                                            data-placement="top" title="Configurar" onclick="openModalSecciones()"><i
                                                class="fa fa-gear"></i></button>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4">                                
                                <div class="row">
                                    <div class="col-xs-4 col-sm-3">
                                        <label for="">Replicar</label>
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" class="minimal" id="chkReplicas"
                                                    name="chkReplicas" {{old('chkReplicas') === 'on' ? 'checked' : ''}}>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-xs-8 col-sm-9">
                                        <label for="txtInstancias">Cantidad de réplicas</label>
                                        <input type="number" id="txtInstancias" name="txtInstancias"
                                            class="form-control" placeholder="Obligatorio" min="1"
                                            value="{{old('txtInstancias') ?? 1}}"
                                            {{old('chkReplicas') === 'on' ? '' : 'disabled'}}>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">                            
                            <div class="form-group col-md-4">
                                <label for="txtCodigoBarras">Código barras</label>
                                <input type="text" id="txtCodigoBarras" name="txtCodigoBarras" class="form-control"
                                    placeholder="Opcional" value="{{old('txtCodigoBarras')}}">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="txtSerie">Serie</label>
                                <input type="text" id="txtSerie" name="txtSerie" class="form-control"
                                    placeholder="Opcional" value="{{old('txtSerie')}}">
                            </div>                            
                            <div class="form-group col-md-4">
                                <label for="txtNombre">Nombre</label>
                                <input type="text" id="txtNombre" name="txtNombre" class="form-control"
                                    placeholder="Obligatorio" value="{{old('txtNombre')}}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="txtDimensionAnchoNumero">Ancho</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <input type="text" id="txtDimensionAnchoNumero" name="txtDimensionAnchoNumero"
                                            class="form-control" placeholder="Opcional"
                                            value="{{old('txtDimensionAnchoNumero')}}">
                                    </div>
                                    <div class="col-md-6">
                                        <select name="txtDimensionAnchoMedida" id="txtDimensionAnchoMedida"
                                            class="form-control" value="{{old('txtDimensionAnchoMedida')}}">
                                            <option selected="1">Metro</option>
                                            <option>Centímetro</option>
                                            <option>Milímetro</option>
                                            <option>Micrómetro</option>
                                            <option>Pie</option>
                                            <option>Pulgada</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="txtDimensionLargoNumero">Largo</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <input type="text" id="txtDimensionLargoNumero" name="txtDimensionLargoNumero"
                                            class="form-control" placeholder="Opcional"
                                            value="{{old('txtDimensionLargoNumero')}}">
                                    </div>
                                    <div class="col-md-6">
                                        <select name="txtDimensionLargoMedida" id="txtDimensionLargoMedida"
                                            class="form-control" value="{{old('txtDimensionLargoMedida')}}">
                                            <option selected="1">Metro</option>
                                            <option>Centímetro</option>
                                            <option>Milímetro</option>
                                            <option>Micrómetro</option>
                                            <option>Pie</option>
                                            <option>Pulgada</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="txtDimensionAltoNumero">Alto</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <input type="text" id="txtDimensionAltoNumero" name="txtDimensionAltoNumero"
                                            class="form-control" placeholder="Opcional"
                                            value="{{old('txtDimensionAltoNumero')}}"
                                            value="{{old('txtDimensionAltoNumero')}}">
                                    </div>
                                    <div class="col-md-6">
                                        <select name="txtDimensionAltoMedida" id="txtDimensionAltoMedida"
                                            class="form-control">
                                            <option selected="1">Metro</option>
                                            <option>Centímetro</option>
                                            <option>Milímetro</option>
                                            <option>Micrómetro</option>
                                            <option>Pie</option>
                                            <option>Pulgada</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="txtModelo">Modelo</label>
                                <input type="text" id="txtModelo" name="txtModelo" class="form-control"
                                    placeholder="Opcional" value="{{old('txtModelo')}}">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="txtPeso">Peso (Kg)</label>
                                <input type="text" id="txtPeso" name="txtPeso" class="form-control"
                                    placeholder="Opcional" value="{{old('txtPeso') ?? 0}}">
                            </div>

                            <div class="form-group col-md-4">
                                <label for="txtEstado">Estado</label>
                                <select name="txtEstado" id="txtEstado" class="form-control"
                                    value="{{old('txtEstado')}}">
                                    <option selected="1">Nuevo</option>
                                    <option>Buen estado</option>
                                    <option>Con daños leves</option>
                                    <option>Deteriorado</option>
                                    <option>Inservible</option>
                                </select>
                            </div>                            
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label>Descripción u observación</label>
                                <textarea class="form-control" name="txtDescripcion" rows="3"
                                    placeholder="Opcional">{{old('txtDescripcion')}}</textarea>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                {{csrf_field()}}
                                <input type="button" class="btn btn-primary pull-right"
                                    value="Registrar datos ingresados" onclick="enviarFrmInsertarInventario();">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
<div class="modal fade" id="modalAmbiente" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Agregar ambiente o espacio</h4>
            </div>
            <div class="modal-body">
                <form id="fmrModalAmbiente">
                    <div class="row">
                        <div class="form-group col-md-3">
                            <label for="txtNombreAmbiente">Nombre</label>
                            <input type="text" id="txtNombreAmbiente" name="txtNombreAmbiente" placeholder="Obligatorio"
                                class="form-control">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="txtCodigo">Código</label>
                            <input type="text" id="txtCodigo" name="txtCodigo" placeholder="Opcional"
                                class="form-control">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="selectTipoAmbiente">Tipo</label>
                            <select id="selectTipoAmbiente" name="selectTipoAmbiente" class="form-control"
                                style="width: 100%;">
                                <option>Oficina</option>
                                <option>Cuarto</option>
                                <option>Local</option>
                                <option>Anaquel</option>
                                <option>Estante</option>
                                <option>Almacén</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
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

<div class="modal fade" id="modalEspacio" data-backdrop="static" data-keyboard="false">
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
                                @for($i = 0; $i < 5 ; $i++) <tr>
                                    @for($j = 1; $j <= 10 ; $j++) <td style="width: 10px"><button
                                            class="btn btn-info btn-block disabled buttonSeccion"
                                            id="buttonSeccion{{$j + ($i * 10)}}" data-index="{{$j + ($i * 10)}}">{{$j + ($i * 10)}}</button></td>
                                        @endfor
                                        </tr>
                                        @endfor
                            </table>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <input type="button" class="btn btn-default pull-left" value="Cerrar ventana"
                                onclick="$('#modalEspacio').modal('hide');">
                            <input type="button" class="btn btn-info pull-right" value="Guardar cambios"
                                onclick="enviarFrmEspacio();">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- EndModals -->

<script>
    var oldEspacio = null;

    $(function () {
        $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
            checkboxClass: 'icheckbox_minimal-blue',
            radioClass: 'iradio_minimal-blue'
        });
        if ("{{old('selectAmbiente')}}" != '') {
            oldEspacio = "{{old('selectEspacio')}}";

            var timmer = setInterval(function () {
                if (!oldEspacio) {
                    clearInterval(timmer);
                }

                if ($('#selectAmbiente').val() && typeof oldEspacio !== 'undefined' && oldEspacio) {
                    $('#selectAmbiente').trigger('change');
                }
            }, 300);
        }

        if ("{{old('txtDimensionAnchoMedida')}}" && "{{old('txtDimensionLargoMedida')}}" &&
            "{{old('txtDimensionAltoMedida')}}") {
            $('#txtDimensionAnchoMedida').val("{{old('txtDimensionAnchoMedida')}}")
            $('#txtDimensionLargoMedida').val("{{old('txtDimensionLargoMedida')}}")
            $('#txtDimensionAltoMedida').val("{{old('txtDimensionAltoMedida')}}")
        }

        if ("{{old('txtEstado')}}") {
            $('#txtEstado').val("{{old('txtEstado')}}");
        }
    });
</script>
<script src="{{asset('viewResources/inventario/insertar.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>
@endsection