<form id="frmEditarEmpresa" action="{{url('empresa/editar')}}" method="post" enctype="multipart/form-data">
	<div class="row">
		<div class="form-group col-md-6">
			<label for="txtRuc">RUC</label>
			<input type="text" id="txtRuc" name="txtRuc" class="form-control" placeholder="Obligatorio" value="{{$tEmpresa->ruc}}">
		</div>
		<div class="form-group col-md-6">
			<label for="txtRazonSocial">Razón social</label>
			<input type="text" id="txtRazonSocial" name="txtRazonSocial" class="form-control" placeholder="Obligatorio" value="{{$tEmpresa->razonSocial}}">
		</div>
	</div>
	<div class="row">
		<div class="form-group col-md-6">
			<label for="txtRepresentanteLegal">Representante legal</label>
			<input type="text" id="txtRepresentanteLegal" name="txtRepresentanteLegal" class="form-control" placeholder="Obligatorio" value="{{$tEmpresa->representanteLegal}}">
		</div>
		<div class="form-group col-md-6">
			<label for="fileLogoEmpresarial">Logo empresarial{!!$tEmpresa->existeLogoEmpresarialTemp ? '...<span class="glyphicon glyphicon-paperclip"></span>' : ''!!}</label>
			<input type="file" id="fileLogoEmpresarial" name="fileLogoEmpresarial" class="form-control" style="border: none;">
		</div>
	</div>
	<div class="row">
		<div class="form-group col-md-12">
			<label for="txtUrlConsultaFactura">Pagina de consulta de facturas</label>
			<input type="text" id="txtUrlConsultaFactura" name="txtUrlConsultaFactura" class="form-control" placeholder="Obligatorio" value="{{$tEmpresa->urlConsultaFactura}}">
		</div>
	</div>
	<div class="row">
		<div class="form-group col-md-6">
			<label>Facturación electrónica</label>
			<div class="form-control" style="border: none;">
				<label style="cursor: pointer;">
					<input type="radio" id="radioFacturacionElectronicaSi" name="radioFacturacionElectronica" value="1" {{$tEmpresa->facturacionElectronica ? 'checked="checked"' : ''}} onclick="onChangeRadioFacturacionElectronica();">
					Si
				</label>
				&nbsp;&nbsp;
				<label style="cursor: pointer;">
					<input type="radio" id="radioFacturacionElectronicaNo" name="radioFacturacionElectronica" value="0" {{!$tEmpresa->facturacionElectronica ? 'checked="checked"' : ''}} onclick="onChangeRadioFacturacionElectronica();">
					No
				</label>
			</div>
		</div>
		<div class="form-group col-md-6">
			<label>Estado</label>
			<div class="form-control" style="border: none;">
				<label style="cursor: pointer;">
					<input type="radio" id="radioEstadoHabilitado" name="radioEstado" value="1" {{$tEmpresa->estado ? 'checked="checked"' : ''}}>
					Habilitado
				</label>
				&nbsp;&nbsp;
				<label style="cursor: pointer;">
					<input type="radio" id="radioEstadoDeshabilitado" name="radioEstado" value="0" {{!$tEmpresa->estado ? 'checked="checked"' : ''}}>
					Deshabilitado
				</label>
			</div>
		</div>
	</div>
	<div class="row">
		<div id="divUserNameEf" class="form-group col-md-6">
			<label for="txtUserNameEf">Usuario para la facturación electrónica</label>
			<input type="text" id="txtUserNameEf" name="txtUserNameEf" class="form-control" placeholder="Obligatorio" value="{{$tEmpresa->userNameEf}}">
		</div>
		<div id="divTxtPasswordEf" class="form-group col-md-6">
			<label for="txtPasswordEf">Contraseña para la facturación electrónica</label>
			<input type="text" id="txtPasswordEf" name="txtPasswordEf" class="form-control" placeholder="Obligatorio" value="{{Crypt::decrypt($tEmpresa->passwordEf)}}">
		</div>
	</div>
	<div class="row">
		<div class="form-group col-md-6">
			<label for="selectFormatoComprobante">Formato del comprobante</label>
			<select id="selectFormatoComprobante" name="selectFormatoComprobante" class="form-control">
				<option value="Ticket" {{$tEmpresa->formatoComprobante=='Ticket' ? 'selected' : ''}}>Impresión en formato de ticket</option>
				<option value="Normal" {{$tEmpresa->formatoComprobante=='Normal' ? 'selected' : ''}}>Impresión en formato A4</option>
			</select>
		</div>
		<div class="form-group col-md-6">
			<label>Demo</label>
			<div class="form-control" style="border: none;">
				<label style="cursor: pointer;">
					<input type="radio" id="radioDemoSi" name="radioDemo" value="1" {{$tEmpresa->demo ? 'checked="checked"' : ''}}>
					Si
				</label>
				&nbsp;&nbsp;
				<label style="cursor: pointer;">
					<input type="radio" id="radioDemoNo" name="radioDemo" value="0" {{!$tEmpresa->demo ? 'checked="checked"' : ''}}>
					No
				</label>
			</div>
		</div>
	</div>
	<div class="row">
		<hr>
		<div class="col-md-12">
			{{csrf_field()}}
			<input type="hidden" name="hdCodigoEmpresa" value="{{$tEmpresa->codigoEmpresa}}">
			<input type="button" class="btn btn-default pull-left" value="Cerrar ventana" onclick="$('#dialogoGeneralModal').modal('hide');">
			<input type="button" class="btn btn-primary pull-right" value="Guardar cambios" onclick="enviarFrmEditarEmpresa();">
		</div>
	</div>
</form>
<script>
	var existeLogoEmpresarialTemp='{{$tEmpresa->existeLogoEmpresarialTemp}}';
</script>
<script src="{{asset('viewResources/empresa/editar.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>