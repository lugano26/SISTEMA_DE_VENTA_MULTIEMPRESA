@extends('template.layoutgeneral')
@section('titulo', 'Notificación de usuario')
@section('subTitulo', 'Gestión de notificaciones')
@section('cuerpoGeneral')
<div class="row">
	<div class="col-md-12">
		<div class="nav-tabs-custom">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#tab_1-1">Notificaciones para usuarios</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="tab_1-1">
                        <form id="frmUsuarioNotificacionInsertar" action="{{url('usuarionotificacion/insertar')}}" method="post" enctype="multipart/form-data">
                            <div class="row">
								<div class="form-group col-md-4">
									<label for="txtImagen">Imagen adjunta</label>
									<input type="file" id="txtImagen" name="txtImagen">
								</div>
                                <div class="form-group col-md-8">
                                    <label for="txtDescripcion">Descripción</label>
                                    <textarea  id="txtDescripcion" name="txtDescripcion" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="row">								
                                <div class="form-group col-md-4">
									<label>¿Permanente?</label>
									<div style="padding-top: 5px;">
										<label style="margin-right: 15px;">
											<input type="radio" name="chkPermanente" id="chkPermanenteSi" class="flat-red" value="true">
											Si
										</label>
										<label>
											<input type="radio" name="chkPermanente" id="chkPermanenteNo" class="flat-red" value="false" checked>
											No
										</label>
									</div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="dateFechaInicioPeriodo">Fecha inicio de periodo</label>
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" id="dateFechaInicioPeriodo" name="dateFechaInicioPeriodo" class="form-control datepicker pull-right" placeholder="Obligatorio">
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="dateFechaFinPeriodo">Fecha fin de periodo</label>
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" id="dateFechaFinPeriodo" name="dateFechaFinPeriodo" class="form-control datepicker pull-right" placeholder="Obligatorio">
                                    </div>
								</div>								
							</div>
							<hr>
							<div class="row">
								<div class="form-group col-md-8">
									<label for="selectRolUsuario">Rol</label>
									<select id="selectRolUsuario" name="selectRolUsuario[]" class="form-control selectStatic" multiple style="width: 100%;">
										<option value="Administrador">Administrador</option>
										<option value="Ventas">Ventas</option>
										<option value="Almacenero">Almacenero</option>
										<option value="Reporteador">Reporteador</option>
									</select>
								</div>
								<div class="form-group col-md-4 text-right" style="padding-top: 23.22px">
									<input type="button" class="btn btn-block btn-info" value="Seleccionar personal por rol" onclick="moverPersonalPorRol();">
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12">
									<div class="box-header ui-sortable-handle">
										<i class="fa fa-users"></i>
							
										<h3 class="box-title">Personal</h3>
									</div>
									<div class="box-body">
										@php
											$margin = true;
										@endphp
										@foreach ($listaGrupoPersonal as $grupoPersonal)
											<ul class="todo-list ui-sortable col-md-6" style="{{$margin ? 'padding-right: 15px !important;' : ''}}">	
												@php
													$margin = false;
												@endphp
												@foreach ($grupoPersonal as $personal )
												<li>
													<label style="cursor:pointer; user-select: none;-moz-user-select: none; display:block" data-codigopersonal="{{$personal->codigoPersonal}}" class="itemPersonal" data-rol="{{$personal->tusuario->rol}}">
														<input type="checkbox" name="hdPersonalSeleccionado[]" id="{{$personal->codigoPersonal}}" value="{{$personal->codigoPersonal}}" class="flat-red">
														<span class="text" style="vertical-align: middle">{{$personal->nombre}} {{$personal->apellido}}</span>											
													</label>			
												</li>	
												@endforeach										
											</ul>
										@endforeach
									</div>
								</div>
							</div>
                            <hr>
                            <div class="row">
                                <div class="col-md-12">
                                    {{csrf_field()}}
                                    <input type="button" class="btn btn-primary pull-right" value="Registrar notificaciones" onclick="enviarFrmGestionarEmpresaDeuda();">
                                </div>
                            </div>
                        </form>
				</div>
			</div>
		</div>
	</div>
</div>
<script src="{{asset('viewResources/usuarionotificacion/insertar.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>
@endsection