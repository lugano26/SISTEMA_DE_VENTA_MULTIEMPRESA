<form id="frmEditarInventario" action="{{url('inventario/editar')}}" method="post">
    <div class="tab-pane active" id="tab_1-1">
        <div class="row">
            <input type="hidden" name="hdCodigoInventario" value="{{$tInventario->codigoInventario}}">
            <div class="form-group col-md-4">
                <label for="selectAmbiente">Ambiente o espacio</label>
                <input type="hidden" id="txtMaskNameAmbiente" name="txtMaskNameAmbiente"
                value="{{$tInventario->tambienteespacio->tambiente->codigo !== '' ? $tInventario->tambienteespacio->tambiente->nombre . ' (' . $tInventario->tambienteespacio->tambiente->codigo .')' : $tInventario->tambienteespacio->tambiente->nombre}}">
                <div class="input-group input-group">
                    <select name="selectAmbiente" id="selectAmbiente" class="form-control selectStatic" style="width: 100%">
                        <option></option>
                        @foreach($listaTAmbiente as $value)
                        <option {{($tInventario->tambienteespacio->tambiente->codigoAmbiente==$value->codigoAmbiente ? 'selected' : '')}} value="{{$value->codigoAmbiente}}">{{$value->nombre . ($value->codigo !== '' ? ' (' . $value->codigo . ')' : '')}}</option>
                        @endforeach
                    </select>
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-default" data-toggle="tooltip" data-placement="top"
                            title="Insertar nuevo" onclick="openModalAmbiente()"><i
                                class="fa fa-plus"></i></button>
                    </span>
                </div>
            </div>
            <div class="form-group col-md-4">
                <label for="selectEspacio">Sección</label>
                <div class="input-group input-group">
                    <select name="selectEspacio" id="selectEspacio"  style="width: 100%" class="form-control selectStatic"
                    value="{{$tInventario->tambienteespacio->codigoAmbienteEspacio}}"></select>
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-default" data-toggle="tooltip" data-placement="top"
                            title="Configurar" onclick="openModalSecciones()"><i class="fa fa-gear"></i></button>
                    </span>
                </div>
            </div>
            <div class="form-group col-md-4">
                <label for="txtCodigoBarras">Código barras</label>
                <input type="text" id="txtCodigoBarras" name="txtCodigoBarras" class="form-control"
                    placeholder="Opcional" value="{{$tInventario->codigoBarras}}">
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-4">
                <label for="txtSerie">Serie</label>
                <input type="text" id="txtSerie" name="txtSerie" class="form-control" placeholder="Opcional"
                value="{{$tInventario->serie}}">
            </div>
            <div class="form-group col-md-4">
                <label for="txtModelo">Modelo</label>
                <input type="text" id="txtModelo" name="txtModelo" class="form-control" placeholder="Opcional"
                value="{{$tInventario->modelo}}">
            </div>
            <div class="form-group col-md-4">
                <label for="txtNombre">Nombre</label>
                <input type="text" id="txtNombre" name="txtNombre" class="form-control" placeholder="Obligatorio"
                value="{{$tInventario->nombre}}">
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-4">
                <label for="txtDimensionAnchoNumero">Ancho</label>
                <div class="row">
                    <div class="col-md-6">
                        <input type="text" id="txtDimensionAnchoNumero" name="txtDimensionAnchoNumero"
                            class="form-control" placeholder="Opcional" value="{{$tInventario->dimensionAncho !== '' ? explode(' ', $tInventario->dimensionAncho)[0] : ''}}">
                    </div>
                    <div class="col-md-6">
                        <select name="txtDimensionAnchoMedida" id="txtDimensionAnchoMedida" class="form-control">
                            <option {{($tInventario->dimensionAncho !== '' ? explode(' ', $tInventario->dimensionAncho)[1] : '') === 'Metro' ? 'selected' : ''}}>Metro</option>
                            <option {{($tInventario->dimensionAncho !== '' ? explode(' ', $tInventario->dimensionAncho)[1] : '') === 'Centímetro' ? 'selected' : ''}}>Centímetro</option>
                            <option {{($tInventario->dimensionAncho !== '' ? explode(' ', $tInventario->dimensionAncho)[1] : '') === 'Milímetro' ? 'selected' : ''}}>Milímetro</option>
                            <option {{($tInventario->dimensionAncho !== '' ? explode(' ', $tInventario->dimensionAncho)[1] : '') === 'Micrómetro' ? 'selected' : ''}}>Micrómetro</option>
                            <option {{($tInventario->dimensionAncho !== '' ? explode(' ', $tInventario->dimensionAncho)[1] : '') === 'Pie' ? 'selected' : ''}}>Pie</option>
                            <option {{($tInventario->dimensionAncho !== '' ? explode(' ', $tInventario->dimensionAncho)[1] : '') === 'Pulgada' ? 'selected' : ''}}>Pulgada</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group col-md-4">
                <label for="txtDimensionLargoNumero">Largo</label>
                <div class="row">
                    <div class="col-md-6">
                        <input type="text" id="txtDimensionLargoNumero" name="txtDimensionLargoNumero"
                            class="form-control" placeholder="Opcional" value="{{$tInventario->dimensionLargo !== '' ? explode(' ', $tInventario->dimensionLargo)[0] : ''}}">
                    </div>
                    <div class="col-md-6">
                        <select name="txtDimensionLargoMedida" id="txtDimensionLargoMedida" class="form-control">
                            <option {{($tInventario->dimensionLargo !== '' ? explode(' ', $tInventario->dimensionLargo)[1] : '') === 'Metro' ? 'selected' : ''}}>Metro</option>
                            <option {{($tInventario->dimensionLargo !== '' ? explode(' ', $tInventario->dimensionLargo)[1] : '') === 'Centímetro' ? 'selected' : ''}}>Centímetro</option>
                            <option {{($tInventario->dimensionLargo !== '' ? explode(' ', $tInventario->dimensionLargo)[1] : '') === 'Milímetro' ? 'selected' : ''}}>Milímetro</option>
                            <option {{($tInventario->dimensionLargo !== '' ? explode(' ', $tInventario->dimensionLargo)[1] : '') === 'Micrómetro' ? 'selected' : ''}}>Micrómetro</option>
                            <option {{($tInventario->dimensionLargo !== '' ? explode(' ', $tInventario->dimensionLargo)[1] : '') === 'Pie' ? 'selected' : ''}}>Pie</option>
                            <option {{($tInventario->dimensionLargo !== '' ? explode(' ', $tInventario->dimensionLargo)[1] : '') === 'Pulgada' ? 'selected' : ''}}>Pulgada</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group col-md-4">
                <label for="txtDimensionAltoNumero">Alto</label>
                <div class="row">
                    <div class="col-md-6">
                        <input type="text" id="txtDimensionAltoNumero" name="txtDimensionAltoNumero"
                            class="form-control" placeholder="Opcional" value="{{$tInventario->dimensionAlto !== '' ? explode(' ', $tInventario->dimensionAlto)[0] : ''}}">
                    </div>
                    <div class="col-md-6">
                        <select name="txtDimensionAltoMedida" id="txtDimensionAltoMedida" class="form-control">
                            <option {{($tInventario->dimensionAlto !== '' ? explode(' ', $tInventario->dimensionAlto)[1] : '') === 'Metro' ? 'selected' : ''}}>Metro</option>
                            <option {{($tInventario->dimensionAlto !== '' ? explode(' ', $tInventario->dimensionAlto)[1] : '') === 'Centímetro' ? 'selected' : ''}}>Centímetro</option>
                            <option {{($tInventario->dimensionAlto !== '' ? explode(' ', $tInventario->dimensionAlto)[1] : '') === 'Milímetro' ? 'selected' : ''}}>Milímetro</option>
                            <option {{($tInventario->dimensionAlto !== '' ? explode(' ', $tInventario->dimensionAlto)[1] : '') === 'Micrómetro' ? 'selected' : ''}}>Micrómetro</option>
                            <option {{($tInventario->dimensionAlto !== '' ? explode(' ', $tInventario->dimensionAlto)[1] : '') === 'Pie' ? 'selected' : ''}}>Pie</option>
                            <option {{($tInventario->dimensionAlto !== '' ? explode(' ', $tInventario->dimensionAlto)[1] : '') === 'Pulgada' ? 'selected' : ''}}>Pulgada</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-4">
                <label for="txtPeso">Peso (Kg)</label>
                <input type="text" id="txtPeso" name="txtPeso" class="form-control" placeholder="Opcional"
                value="{{$tInventario->pesoKg}}">
            </div>

            <div class="form-group col-md-4">
                <label for="txtEstado">Estado</label>
                <select name="txtEstado" id="txtEstado" class="form-control">
                    <option {{$tInventario->estado === 'Nuevo' ? 'selected' : ''}}>Nuevo</option>
                    <option {{$tInventario->estado === 'Buen estado' ? 'selected' : ''}}>Buen estado</option>
                    <option {{$tInventario->estado === 'Con daños leves' ? 'selected' : ''}}>Con daños leves</option>
                    <option {{$tInventario->estado === 'Deteriorado' ? 'selected' : ''}}>Deteriorado</option>
                    <option {{$tInventario->estado === 'Inservible' ? 'selected' : ''}}>Inservible</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-12">
                <label>Descripción u observación</label>
                <textarea class="form-control" name="txtDescripcion" rows="3"
                    placeholder="Descripción">{{$tInventario->descripcion}}</textarea>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-12">
                {{csrf_field()}}
                <input type="button" class="btn btn-default pull-left" value="Cerrar ventana" onclick="$('#dialogoGeneralModal').modal('hide');">
                <input type="button" class="btn btn-primary pull-right" value="Guardar cambios"
                    onclick="enviarFrmEditarInventario();">
            </div>
        </div>
    </div>
</form>

<script>
    var oldEspacio = null;

    $(function () {
        $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
            checkboxClass: 'icheckbox_minimal-blue',
            radioClass: 'iradio_minimal-blue'
        });

        oldEspacio = "{{$tInventario->tambienteespacio->codigoAmbienteEspacio}}";

        var timmer = setInterval(function () {
            if (!oldEspacio) {
                clearInterval(timmer);
            }
            console.log(oldEspacio);
            if ($('#selectAmbiente').val() && typeof oldEspacio !== 'undefined' && oldEspacio) {
                $('#selectAmbiente').trigger('change');
            }
        }, 300);
    });
</script>
<script src="{{asset('viewResources/inventario/editar.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>