@extends('template.layoutgeneral')
@section('titulo', 'Empresa deuda: '.$tEmpresa->razonSocial)
@section('subTitulo', 'Gestión de deudas')
@section('cuerpoGeneral')
<div class="row">
	<div class="col-md-12">
		<div class="nav-tabs-custom">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#tab_1-1">Deudas pagadas y por pagar</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="tab_1-1">
					@if(strpos(Session::get('rol', 'Público'), 'Súper usuario')!==false)
						<div class="box box-info collapsed-box">
							<div class="box-header with-border">
								<h3 class="box-title">Programación de pago</h3>
								<div class="box-tools pull-right">
									<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
								</div>
							</div>
							<div class="box-body">
								<form id="frmGestionarEmpresaDeuda" action="{{url('empresadeuda/gestionar')}}" method="post">
									<div class="row">
										<div class="form-group col-md-8">
											<label for="txtDescripcion">Descripción</label>
											<input type="text" id="txtDescripcion" name="txtDescripcion" class="form-control" placeholder="Obligatorio">
										</div>
										<div class="form-group col-md-4">
											<label for="txtMonto">Monto</label>
											<input type="text" id="txtMonto" name="txtMonto" class="form-control" placeholder="Obligatorio">
										</div>
									</div>
									<div class="row">
										<div class="form-group col-md-4">
											<label for="dateFechaPagar">Fecha a pagar</label>
											<div class="input-group date">
												<div class="input-group-addon">
													<i class="fa fa-calendar"></i>
												</div>
												<input type="text" id="dateFechaPagar" name="dateFechaPagar" class="form-control datepicker pull-right" placeholder="Obligatorio">
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
										<div class="col-md-12">
											{{csrf_field()}}
											<input type="hidden" name="hdCodigoEmpresa" value="{{$tEmpresa->codigoEmpresa}}">
											<input type="button" class="btn btn-primary pull-right" value="Registrar deuda" onclick="enviarFrmGestionarEmpresaDeuda();">
										</div>
									</div>
								</form>
							</div>
						</div>
					@endif
					<div class="row">
						<div class="col-md-12">
							<div>
								<input type="text" id="txtBuscar" name="txtBuscar" class="form-control" autocomplete="off" placeholder="Ingrese datos de búsqueda (Enter)" onkeyup="filtrarHtml('tableEmpresaDeuda', this.value, false, 0, event);">
							</div>
							<hr>
							<div class="table-responsive">
								<table id="tableEmpresaDeuda" class="table table-striped" style="min-width: 777px;">
									<thead>
										<tr>
											<th class="text-center">Fecha a pagar</th>
											<th>Descripción</th>
											<th class="text-center">Monto</th>
											<th class="text-center">In. IGV</th>
											<th class="text-center">F. emit.</th>
											<th class="text-center">Periodo</th>
											<th class="text-center">Estado</th>
											@if(strpos(Session::get('rol', 'Público'), 'Súper usuario')!==false)
												<th></th>
											@endif
										</tr>
									</thead>
									<tbody>
										@foreach($listaTEmpresaDeuda as $value)
											<tr class="elementoBuscar">
												<td class="text-center">
													{{$value->fechaPagar}}
												</td>
												<td>
													{{$value->descripcion}}
												</td>
												<td class="text-center">S/{{$value->monto}}</td>
												<td class="text-center">{!!($value->incluyeIgv ? '<span class="label label-success">Si</span>' : '<span class="label label-warning">No</span>')!!}</td>
												<td class="text-center">{!!($value->facturaEmitida ? '<span class="label label-success">Si</span>' : '<span class="label label-warning">No</span>')!!}</td>
												<td class="text-center"><div>{{$value->fechaInicioPeriodo}}</div><div>{{$value->fechaFinPeriodo}}</div></td>
												<td class="text-center">{!!($value->estado ? '<span class="label label-info">Pagado</span>' : ($value->diasRetraso>=-2 && $value->diasRetraso<=0 ? '<span class="label label-warning">Por vencer</span>' : ($value->diasRetraso>0 ? '<span class="label label-danger">Vencido</span>' : '<span class="label label-default">Pendiente</span>')))!!}</td>
												@if(strpos(Session::get('rol', 'Público'), 'Súper usuario')!==false)
													<td class="text-right">
														<span class="btn btn-default btn-xs glyphicon glyphicon-tag" data-toggle="tooltip" data-placement="left" title="Inclusión de IGV" onclick="confirmacion(function(){ $('#modalLoading').modal('show');window.location.href='{{url('empresadeuda/inclusionigv/'.$value->codigoEmpresaDeuda)}}'; });"></span>
														<span class="btn btn-default btn-xs glyphicon glyphicon-transfer" data-toggle="tooltip" data-placement="left" title="Emisión de factura" onclick="confirmacion(function(){ $('#modalLoading').modal('show');window.location.href='{{url('empresadeuda/emisionfactura/'.$value->codigoEmpresaDeuda)}}'; });"></span>
														@if(!$value->estado)
															<span class="btn btn-default btn-xs glyphicon glyphicon-ok" data-toggle="tooltip" data-placement="left" title="Marcar como pagado" onclick="confirmacion(function(){ $('#modalLoading').modal('show');window.location.href='{{url('empresadeuda/cambiopago/'.$value->codigoEmpresaDeuda)}}'; });"></span>
														@else
															<span class="btn btn-default btn-xs glyphicon glyphicon-ban-circle" data-toggle="tooltip" data-placement="left" title="Anular pago" onclick="confirmacion(function(){ $('#modalLoading').modal('show');window.location.href='{{url('empresadeuda/cambiopago/'.$value->codigoEmpresaDeuda)}}'; });"></span>
														@endif
														<span class="btn btn-default btn-xs glyphicon glyphicon-trash" data-toggle="tooltip" data-placement="left" title="Eliminar" onclick="confirmacion(function(){ $('#modalLoading').modal('show');window.location.href='{{url('empresadeuda/eliminar/'.$value->codigoEmpresaDeuda)}}'; });"></span>
													</td>
												@endif
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
<script src="{{asset('viewResources/empresadeuda/gestionar.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>
@endsection