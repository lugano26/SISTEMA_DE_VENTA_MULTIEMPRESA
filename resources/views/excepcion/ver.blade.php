@extends('template.layoutgeneral')
@section('titulo', 'Excepción')
@section('subTitulo', 'Lista de excepciones')
@section('cuerpoGeneral')
<div class="row">
	<div class="col-md-12">
		<div class="nav-tabs-custom">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#tab_1-1">Lista de excepciones</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="tab_1-1">
					<div class="row">
						<div class="col-md-12">
							<div>
								<form id="frmSearch" method="get" action="{{url('excepcion/ver')}}" onsubmit="validarExpresion(event)">
									<div class="input-group input-group-sm">
										<input id="textSearch" type="text" class="form-control" onkeyup="searchItem(event);" placeholder="Buscar por usuario, controlador, acción, error, estado (Enter)" name="q" value="{{ !empty($q) ? $q : '' }}" autofocus>
										<span class="input-group-btn">
										<button type="buttom" class="btn btn-primary btn-flat"><i class="fa fa-search" ></i></button>
										</span>
									</div>
								</form>
							</div>
							<hr>
							<div class="table-responsive">
								<table id="tableExcepcion" class="table table-striped" style="min-width: 777px;">
									<thead>
										<tr>
											<th>Sesión de usuario</th>
											<th>Controlador</th>
											<th>Acción</th>
											<th>Error</th>
											<th class="text-center">Estado</th>
											<th class="text-center">Fecha de registro</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										@foreach($listaTExcepcion as $value)
											<tr class="elementoBuscar">
												<td>{{$value->tusuario!=null ? $value->tusuario->tpersonal->correoElectronico : 'No especificado'}}</td>
												<td>{{$value->controlador}}</td>
												<td>{{$value->accion}}</td>
												<td>{{$value->error}}</td>
												<td class="text-center">{!!$value->estado=='Atendido' ? '<span class="label label-success">Atendido</span>' : '<span class="label label-warning">Pendiente</span>'!!}</td>
												<td class="text-center">{{$value->created_at}}</td>
												<td class="text-right">
													@if($value->estado!='Atendido')
														<span class="btn btn-default btn-xs glyphicon glyphicon glyphicon-ok" data-toggle="tooltip" data-placement="left" title="Marcar como atendido" onclick="$('#modalLoading').modal('show');window.location.href='{{url('excepcion/cambiarestado')}}/{{$value->codigoExcepcion}}/Atendido';"></span>
													@else
														<span class="btn btn-default btn-xs glyphicon glyphicon glyphicon-ok" data-toggle="tooltip" data-placement="left" title="Marcar como atendido" disabled></span>
													@endif
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
<script src="{{asset('viewResources/excepcion/ver.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>
@endsection