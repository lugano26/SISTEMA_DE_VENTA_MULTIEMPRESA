@extends('template.layoutgeneral')
@section('titulo', 'Almacén')
@section('subTitulo', 'Ver')
@section('cuerpoGeneral')
<div class="row">
	<div class="col-md-12">
		<div class="nav-tabs-custom">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#tab_1-1">Lista de almacenes</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="tab_1-1">
					<div class="row">
						<div class="col-md-12">
							<div>
								<input type="text" id="txtBuscar" name="txtBuscar" class="form-control" autocomplete="off" placeholder="Ingrese datos de búsqueda (Enter)" onkeyup="filtrarHtml('tableAlmacen', this.value, false, 0, event);">
							</div>
							<hr>
							<div class="table-responsive">
								<table id="tableAlmacen" class="table table-striped" style="min-width: 777px;">
									<thead>
										<tr>
											<th>Nombre</th>
											<th>País</th>
											<th>Departamento</th>
											<th>Provincia</th>
											<th>Distrito</th>
											<th>Dirección</th>
											<th>Teléfono</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										@foreach($listaTAlmacen as $value)
											<tr class="elementoBuscar">
												<td>{{$value->descripcion}}</td>
												<td>{{$value->pais}}</td>
												<td>{{$value->departamento}}</td>
												<td>{{$value->provincia}}</td>
												<td>{{$value->distrito}}</td>
												<td>{{$value->direccion.' '.$value->numeroVivienda}}</td>
												<td>{{$value->telefono}}</td>
												<td class="text-right">
													<span class="btn btn-default btn-xs glyphicon glyphicon-pencil" data-toggle="tooltip" data-placement="left" title="Editar" onclick="dialogoAjax('dialogoGeneral', null, '{{$value->descripcion}} (Editar)', { _token : '{{csrf_token()}}', codigoAlmacen : '{{$value->codigoAlmacen}}' }, '{{url('almacen/editar')}}', 'POST', null, null, false, true);"></span>
													<span class="btn btn-default btn-xs glyphicon glyphicon-user" data-toggle="tooltip" data-placement="left" title="Gestionar personal" onclick="dialogoAjax('dialogoGeneral', null, '{{$value->descripcion}} (Gestión de personal)', { _token : '{{csrf_token()}}', codigoAlmacen : '{{$value->codigoAlmacen}}' }, '{{url('almacen/gestionarpersonal')}}', 'POST', null, null, false, true);"></span>
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
@endsection