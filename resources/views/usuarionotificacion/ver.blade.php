@extends('template.layoutgeneral')
@section('titulo', 'Notificación de usuario')
@section('subTitulo', 'Lista de notificaciones')
@section('cuerpoGeneral')
<div class="row">
	<div class="col-md-12">
		<div class="nav-tabs-custom">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#tab_1-1">Lista de notificaciones para usuarios</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="tab_1-1">
					<div class="row">
						<div class="col-md-12">
							<div>
								<form id="frmSearch" method="get" action="{{url('usuarionotificacion/ver')}}" onsubmit="validarExpresion(event)">
									<div class="input-group input-group-sm">
										<input id="textSearch" type="text" onkeyup="searchItem(event);" class="form-control" placeholder="Buscar por descripción, personal (Enter)" name="q" value="{{!empty($q) ? $q : ''}}" autofocus>
										<span class="input-group-btn">
											<button type="submit" class="btn btn-primary btn-flat"><i class="fa fa-search" ></i></button>
										</span>
									</div>
								</form>
							</div>
						</div>
					</div>
					<hr>
					<div class="row">
						<div class="col-lg-12">
							<div class="table-responsive">
								<table id="tableUsuarioNotificacion" class="table table-striped table-bordered" style="min-width: 777px;">
									<thead>
										<tr>
											<th>Personal</th>
											<th>Descripción</th>
											<th class="text-center">Permanente</th>
											<th class="text-center">Fecha Inicio</th>
											<th class="text-center">Fecha Fin</th>
											<th class="text-center">Imagen</th>
											<th class="text-center">Estado</th>
											<th class="text-center">Fecha registro</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										@foreach($listaTUsuarioNotificacion as $value)
											<tr>
												<td style="max-width: 300px;">{{'('.explode("@", $value->tpersonal->correoElectronico)[0].') '.$value->tpersonal->nombre .' '. $value->tpersonal->apellido}}</td>
                                                <td>{{$value->descripcion}}</td>
                                                <td class="text-center">
                                                    <span class="label {{$value->permanente ? "label-info" : "label-warning"}}">
                                                        {{$value->permanente ? 'Si' : 'No'}}
                                                    </span>
                                                </td>
                                                <td class="text-center">{{$value->fechaInicioPeriodo}}</td>
                                                <td class="text-center">{{$value->fechaFinPeriodo}}</td>
                                                <td class="text-center">
                                                    @if($value->url == '')
                                                        -
                                                    @else
                                                        <img src="{{asset($value->url)}}" alt="Image notifiaction" style="width: 64px;">
                                                    @endif
                                                </td>
												<td class="text-center">
													<span class="label {{$value->estado ? "label-success" : "label-danger"}}">
														{{$value->estado ? 'Leido' : 'No leido'}}
													</span>
												</td>
												<td class="text-center">{{$value->created_at}}</td>
												<td class="text-right">
                                                    @if($value->permanente || !$value->estado)
                                                        <span class="btn btn-default btn-xs glyphicon glyphicon glyphicon-ban-circle" data-toggle="tooltip" data-placement="left" title="Ocultar notificación" onclick="javascript:location.href = '{{url('usuarionotificacion/ocultarnotificacion') . '/' . $value->codigoUsuarioNotificacion}}'"></span>
													@else
														<span class="btn btn-default btn-xs glyphicon glyphicon glyphicon-ban-circle" data-toggle="tooltip" data-placement="left" title="Ocultar notificación" disabled></span>
													@endif
												</td>
											</tr>
										@endforeach
									</tbody>
								</table>
							</div>
							{!!$pagination!!}
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script src="{{asset('viewResources/usuarionotificacion/ver.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>
@endsection