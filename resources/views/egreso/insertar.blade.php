@extends('template.layoutgeneral')
@section('titulo', 'Egreso')
@section('subTitulo', 'Registro de egreso')
@section('cuerpoGeneral')
<div class="row">
	<div class="col-md-12">
		<div class="nav-tabs-custom">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#tab_1-1">Datos del egreso</a></li>
			</ul>
			<div class="tab-content">
				<form id="frmInsertarEgreso" action="{{url('egreso/insertar')}}" method="post">
					<div class="tab-pane active" id="tab_1-1">
						<div class="row">
							<div class="form-group col-md-8">
								<label for="txtDescripcion">Descripción</label>
								<input type="text" id="txtDescripcion" name="txtDescripcion" class="form-control" placeholder="Obligatorio" value="{{old('txtDescripcion')}}">
							</div>
							<div class="form-group col-md-4">
								<label for="txtMonto">Monto</label>
								<input type="text" id="txtMonto" name="txtMonto" class="form-control" placeholder="Obligatorio" value="{{old('txtMonto')}}">
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								{{csrf_field()}}
								<input type="button" class="btn btn-primary pull-right" value="Registrar datos ingresados" onclick="enviarFrmInsertarEgreso();">
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<script src="{{asset('viewResources/egreso/insertar.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>
@endsection