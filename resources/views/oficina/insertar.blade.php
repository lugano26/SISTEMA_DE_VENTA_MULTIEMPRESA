@extends('template.layoutgeneral')
@section('titulo', 'Oficina')
@section('subTitulo', 'Insertar')
@section('cuerpoGeneral')
<div class="row">
	<div class="col-md-12">
		<div class="nav-tabs-custom">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#tab_1-1">Datos de la oficina</a></li>
			</ul>
			<div class="tab-content">
				<form id="frmInsertarOficina" action="{{url('oficina/insertar')}}" method="post">
					<div class="tab-pane active" id="tab_1-1">
						<div class="row">
							<div class="form-group col-md-4">
								<label for="txtDescripcion">Nombre</label>
								<input type="text" id="txtDescripcion" name="txtDescripcion" class="form-control" placeholder="Obligatorio" value="{{old('txtDescripcion')}}">
							</div>
							<div class="form-group col-md-4">
								<label for="txtPais">País</label>
								<input type="text" id="txtPais" name="txtPais" class="form-control" placeholder="Obligatorio" value="{{old('txtPais')}}">
							</div>
							<div class="form-group col-md-4">
								<label for="txtDepartamento">Departamento</label>
								<input type="text" id="txtDepartamento" name="txtDepartamento" class="form-control" placeholder="Obligatorio" value="{{old('txtDepartamento')}}">
							</div>
						</div>
						<div class="row">
							<div class="form-group col-md-4">
								<label for="txtProvincia">Provincia</label>
								<input type="text" id="txtProvincia" name="txtProvincia" class="form-control" placeholder="Obligatorio" value="{{old('txtProvincia')}}">
							</div>
							<div class="form-group col-md-4">
								<label for="txtDistrito">Distrito</label>
								<input type="text" id="txtDistrito" name="txtDistrito" class="form-control" placeholder="Obligatorio" value="{{old('txtDistrito')}}">
							</div>
							<div class="form-group col-md-4">
								<label for="txtDireccion">Dirección</label>
								<input type="text" id="txtDireccion" name="txtDireccion" class="form-control" placeholder="Obligatorio" value="{{old('txtDireccion')}}">
							</div>
						</div>
						<div class="row">
							<div class="form-group col-md-4">
								<label for="txtTelefono">Teléfono</label>
								<input type="text" id="txtTelefono" name="txtTelefono" class="form-control" placeholder="Obligatorio" value="{{old('txtTelefono')}}">
							</div>
							<div class="form-group col-md-4">
								<label for="txtNumeroVivienda">Número vivienda</label>
								<input type="text" id="txtNumeroVivienda" name="txtNumeroVivienda" class="form-control" placeholder="Obligatorio" value="{{old('txtNumeroVivienda')}}">
							</div>							
						</div>
						<div class="row">
							<div class="form-group col-md-12">
								<label>Descripción comercial</label>
								<textarea class="form-control" name="txtDescripcionComercialComprobante" rows="3" placeholder="Descripción comercial para comprobantes">{{old('txtDescripcionComercialComprobante')}}</textarea>
							</div>
						</div>
						<hr>
						<div class="row">
							<div class="col-md-12">
								{{csrf_field()}}
								<input type="button" class="btn btn-primary pull-right" value="Registrar datos ingresados" onclick="enviarFrmInsertarOficina();">
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<script src="{{asset('viewResources/oficina/insertar.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>
@endsection