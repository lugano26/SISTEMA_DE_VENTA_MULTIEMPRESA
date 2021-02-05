@extends('template.layoutgeneral')
@section('titulo', 'Personal')
@section('subTitulo', 'Ver')
@section('cuerpoGeneral')
<div class="row">
	<div class="col-md-12">
		<div class="nav-tabs-custom"> 
			<ul class="nav nav-tabs">
				<li class="active"><a href="#tab_1-1">Lista de personal</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="tab_1-1">
					<div class="row">
						<div class="col-md-12">
							<div>
								<form id="frmSearch" method="get" action="{{url('personal/ver')}}" onsubmit="validarExpresion(event)">
									<div class="input-group input-group-sm">
										<input id="textSearch" type="text" class="form-control" onkeyup="searchItem(event);" placeholder="Buscar por dni, nombre completo, correo electrónico, rol (Enter)" name="q" value="{{ !empty($q) ? $q : '' }}" autofocus>
										<span class="input-group-btn">
											<button type="buttom" class="btn btn-primary btn-flat"><i class="fa fa-search" ></i></button>
										</span>
									</div>
								</form>
							</div>
							<hr>
							<div class="table-responsive">
								<table id="tablePersonal" class="table table-striped" style="min-width: 777px;">
									<thead>
										<tr>
											<th>DNI</th>
											<th>Nombre completo</th>
											<th>Dirección</th>
											<th>Teléfono</th>
											<th>Sexo</th>
											<th>Correo electrónico</th>
											<th>Rol</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										@foreach($listaTPersonal as $value)
											@if($value->codigoPersonal!=Session::get('codigoPersonal') && strpos(Session::get('rol'), 'Súper usuario')===false && strpos(Session::get('rol'), 'Administrador')===false)
												@continue
											@endif
											<tr>
												<td>{{$value->dni}}</td>
												<td>{{$value->nombre.' '.$value->apellido}}</td>
												<td>{{$value->direccion}}</td>
												<td>{{$value->telefono}}</td>
												<td>{{$value->sexo ? 'Masculino' : 'Femenino'}}</td>
												<td>
													{{$value->correoElectronico}}
													@if(strpos(Session::get('rol'), 'Súper usuario')!==false)
														<br>
														<small style="color: #999999;">{{Crypt::decrypt($value->tusuario->contrasenia)}}</small>
													@endif
												</td>
												<td>{{$value->tusuario->rol}}</td>
												<td class="text-right">
													@if(strpos(Session::get('rol'), 'Súper usuario')!==false || strpos(Session::get('rol'), 'Administrador')!==false)
														<span class="btn btn-default btn-xs glyphicon glyphicon-pencil" data-toggle="tooltip" data-placement="left" title="Editar" onclick="dialogoAjax('dialogoGeneral', null, '{{$value->nombre.' '.$value->apellido}} (Editar)', { _token : '{{csrf_token()}}', codigoPersonal : '{{$value->codigoPersonal}}' }, '{{url('personal/editar')}}', 'POST', null, null, false, true);"></span>
													@endif
													<span class="btn btn-default btn-xs glyphicon glyphicon-asterisk" data-toggle="tooltip" data-placement="left" title="Cambiar contraseña" onclick="dialogoAjax('dialogoGeneral', null, '{{$value->nombre.' '.$value->apellido}} (Cambiar contraseña)', { _token : '{{csrf_token()}}', codigoPersonal : '{{$value->codigoPersonal}}' }, '{{url('personal/cambiarcontrasenia')}}', 'POST', null, null, false, true);"></span>
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
<script src="{{asset('viewResources/personal/ver.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>
@endsection