@extends('template.layoutgeneral')
@section('titulo', 'General')
@section('subTitulo', 'Configuracion global')
@section('cuerpoGeneral')
<div class="row">
	<div class="col-md-12">
		<form id="frmRegistrarClienteNuevo" action="{{url('general/configuracionglobal')}}" method="post" enctype="multipart/form-data">
			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs">
					<li id="liTab_1-1" class="active"><a href="#">Datos de la empresa</a></li>
					<li id="liTab_1-2"><a href="#">Datos de oficina</a></li>
					<li id="liTab_1-3"><a href="#">Datos de almacén</a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane active" id="tab_1-1">
						<div class="row">
							<div class="col-md-12">
								<div style="background-color: #f5f5f5;height: 45px;padding: 5px;">
									<input type="button" class="btn btn-warning pull-left" value="Atrás" disabled="true" style="width: 120px;">
									<input type="button" class="btn btn-info pull-right" value="Siguiente" style="width: 120px;" onclick="seleccionarPestania('tab_1-2');">
								</div>
							</div>
						</div>
						<hr>
						<div class="row">
							<div class="form-group col-md-6">
								<label for="txtRucEmpresa">RUC</label>
								<input type="text" id="txtRucEmpresa" name="txtRucEmpresa" class="form-control" placeholder="Obligatorio" value="{{old('txtRucEmpresa')}}">
							</div>
							<div class="form-group col-md-6">
								<label for="txtRazonSocialEmpresa">Razón social</label>
								<input type="text" id="txtRazonSocialEmpresa" name="txtRazonSocialEmpresa" class="form-control" placeholder="Obligatorio" value="{{old('txtRazonSocialEmpresa')}}">
							</div>
						</div>
						<div class="row">
							<div class="form-group col-md-6">
								<label for="txtRepresentanteLegalEmpresa">Representante legal</label>
								<input type="text" id="txtRepresentanteLegalEmpresa" name="txtRepresentanteLegalEmpresa" class="form-control" placeholder="Obligatorio" value="{{old('txtRepresentanteLegalEmpresa')}}">
							</div>
							<div class="form-group col-md-6">
								<label for="fileLogoEmpresarialEmpresa">Logo empresarial</label>
								<input type="file" id="fileLogoEmpresarialEmpresa" name="fileLogoEmpresarialEmpresa" class="form-control" style="border: none;">
							</div>
						</div>
						<div class="row">
							<div class="form-group col-md-6">
								<label>Facturación electrónica</label>
								<div class="form-control" style="border: none;">
									<label style="cursor: pointer;">
										<input type="radio" id="radioFacturacionElectronicaEmpresaSi" name="radioFacturacionElectronicaEmpresa" value="1" {{(old('radioFacturacionElectronicaEmpresa')==null ? 'checked="checked"' : (old('radioFacturacionElectronicaEmpresa')) ? 'checked="checked"' : '')}} onclick="onChangeRadioFacturacionElectronicaEmpresa();">
										Si
									</label>
									&nbsp;&nbsp;
									<label style="cursor: pointer;">
										<input type="radio" id="radioFacturacionElectronicaEmpresaNo" name="radioFacturacionElectronicaEmpresa" value="0" {{(old('radioFacturacionElectronicaEmpresa')!=null && !old('radioFacturacionElectronicaEmpresa')) ? 'checked="checked"' : ''}} onclick="onChangeRadioFacturacionElectronicaEmpresa();">
										No
									</label>
								</div>
							</div>
							<div id="divUserNameEfEmpresa" class="form-group col-md-3">
								<label for="txtUserNameEfEmpresa">Usuario para FE</label>
								<input type="text" id="txtUserNameEfEmpresa" name="txtUserNameEfEmpresa" class="form-control" placeholder="Obligatorio" value="{{old('txtUserNameEfEmpresa')}}">
							</div>
							<div id="divTxtPasswordEfEmpresa" class="form-group col-md-3">
								<label for="txtPasswordEfEmpresa">Contraseña para FE</label>
								<input type="text" id="txtPasswordEfEmpresa" name="txtPasswordEfEmpresa" class="form-control" placeholder="Obligatorio" value="{{old('txtPasswordEfEmpresa')}}">
							</div>
						</div>
					</div>
					<div class="tab-pane" id="tab_1-2">
						<div class="row">
							<div class="col-md-12">
								<div style="background-color: #f5f5f5;height: 45px;padding: 5px;">
									<input type="button" class="btn btn-warning pull-left" value="Atrás" style="width: 120px;" onclick="seleccionarPestania('tab_1-1')">
									<input type="button" class="btn btn-info pull-right" value="Siguiente" style="width: 120px;" onclick="seleccionarPestania('tab_1-3');">
								</div>
							</div>
						</div>
						<hr>
						<div class="row">
							<div class="form-group col-md-6">
								<label for="txtDescripcionOficina">Nombre</label>
								<input type="text" id="txtDescripcionOficina" name="txtDescripcionOficina" class="form-control" placeholder="Obligatorio" value="{{old('txtDescripcionOficina')}}">
							</div>
							<div class="form-group col-md-6">
								<label for="txtPaisOficina">País</label>
								<input type="text" id="txtPaisOficina" name="txtPaisOficina" class="form-control" placeholder="Obligatorio" value="{{old('txtPaisOficina')}}">
							</div>
						</div>
						<div class="row">
							<div class="form-group col-md-6">
								<label for="txtDepartamentoOficina">Departamento</label>
								<input type="text" id="txtDepartamentoOficina" name="txtDepartamentoOficina" class="form-control" placeholder="Obligatorio" value="{{old('txtDepartamentoOficina')}}">
							</div>
							<div class="form-group col-md-6">
								<label for="txtProvinciaOficina">Provincia</label>
								<input type="text" id="txtProvinciaOficina" name="txtProvinciaOficina" class="form-control" placeholder="Obligatorio" value="{{old('txtProvinciaOficina')}}">
							</div>
						</div>
						<div class="row">
							<div class="form-group col-md-6">
								<label for="txtDistritoOficina">Distrito</label>
								<input type="text" id="txtDistritoOficina" name="txtDistritoOficina" class="form-control" placeholder="Obligatorio" value="{{old('txtDistritoOficina')}}">
							</div>
							<div class="form-group col-md-6">
								<label for="txtDireccionOficina">Dirección</label>
								<input type="text" id="txtDireccionOficina" name="txtDireccionOficina" class="form-control" placeholder="Obligatorio" value="{{old('txtDireccionOficina')}}">
							</div>
						</div>
						<div class="row">
							<div class="form-group col-md-6">
								<label for="txtTelefonoOficina">Teléfono</label>
								<input type="text" id="txtTelefonoOficina" name="txtTelefonoOficina" class="form-control" placeholder="Obligatorio" value="{{old('txtTelefonoOficina')}}">
							</div>
							<div class="form-group col-md-6">
								<label for="txtNumeroViviendaOficina">Número vivienda</label>
								<input type="text" id="txtNumeroViviendaOficina" name="txtNumeroViviendaOficina" class="form-control" placeholder="Obligatorio" value="{{old('txtNumeroViviendaOficina')}}">
							</div>
						</div>
					</div>
					<div class="tab-pane" id="tab_1-3">
						<div class="row">
							<div class="col-md-12">
								<div style="background-color: #f5f5f5;height: 45px;padding: 5px;">
									<input type="button" class="btn btn-warning pull-left" value="Atrás" style="width: 120px;" onclick="seleccionarPestania('tab_1-2')">
									<input type="button" class="btn btn-info pull-right" value="Siguiente" style="width: 120px;" disabled="true">
								</div>
							</div>
						</div>
						<hr>
						<div class="row">
							<div class="form-group col-md-6">
								<label for="txtDescripcionAlmacen">Nombre</label>
								<input type="text" id="txtDescripcionAlmacen" name="txtDescripcionAlmacen" class="form-control" placeholder="Obligatorio" value="{{old('txtDescripcionAlmacen')}}">
							</div>
							<div class="form-group col-md-6">
								<label for="txtPaisAlmacen">País</label>
								<input type="text" id="txtPaisAlmacen" name="txtPaisAlmacen" class="form-control" placeholder="Obligatorio" value="{{old('txtPaisAlmacen')}}">
							</div>
						</div>
						<div class="row">
							<div class="form-group col-md-6">
								<label for="txtDepartamentoAlmacen">Departamento</label>
								<input type="text" id="txtDepartamentoAlmacen" name="txtDepartamentoAlmacen" class="form-control" placeholder="Obligatorio" value="{{old('txtDepartamentoAlmacen')}}">
							</div>
							<div class="form-group col-md-6">
								<label for="txtProvinciaAlmacen">Provincia</label>
								<input type="text" id="txtProvinciaAlmacen" name="txtProvinciaAlmacen" class="form-control" placeholder="Obligatorio" value="{{old('txtProvinciaAlmacen')}}">
							</div>
						</div>
						<div class="row">
							<div class="form-group col-md-6">
								<label for="txtDistritoAlmacen">Distrito</label>
								<input type="text" id="txtDistritoAlmacen" name="txtDistritoAlmacen" class="form-control" placeholder="Obligatorio" value="{{old('txtDistritoAlmacen')}}">
							</div>
							<div class="form-group col-md-6">
								<label for="txtDireccionAlmacen">Dirección</label>
								<input type="text" id="txtDireccionAlmacen" name="txtDireccionAlmacen" class="form-control" placeholder="Obligatorio" value="{{old('txtDireccionAlmacen')}}">
							</div>
						</div>
						<div class="row">
							<div class="form-group col-md-6">
								<label for="txtTelefonoAlmacen">Teléfono</label>
								<input type="text" id="txtTelefonoAlmacen" name="txtTelefonoAlmacen" class="form-control" placeholder="Obligatorio" value="{{old('txtTelefonoAlmacen')}}">
							</div>
							<div class="form-group col-md-6">
								<label for="txtNumeroViviendaAlmacen">Número vivienda</label>
								<input type="text" id="txtNumeroViviendaAlmacen" name="txtNumeroViviendaAlmacen" class="form-control" placeholder="Obligatorio" value="{{old('txtNumeroViviendaAlmacen')}}">
							</div>
						</div>
						<hr>
						<div class="row">
							<div class="col-md-12">
								{{csrf_field()}}
								<input type="button" class="btn btn-primary pull-right" value="Registrar datos del nuevo cliente" onclick="enviarFrmRegistrarClienteNuevo();">
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
<script src="{{asset('viewResources/general/configuracionglobal.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>
@endsection