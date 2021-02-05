@extends('template.layoutgeneral')
@section('titulo', 'Resumen diario')
@section('subTitulo', 'Ver')
@section('cuerpoGeneral')
<div class="row">
	<div class="col-md-12">
		<div class="nav-tabs-custom">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#tab_1-1">Lista de resumen diario</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="tab_1-1">
					<div class="row">
						<div class="col-md-12">
							<div class="table-responsive">
								<table id="tableResumenDiario" class="table table-striped text-center" style="min-width: 777px;">
									<thead>
										<tr>
											<th>Nº de ticket</th>
											<th>Nº de comprobante</th>
											<th>Resumen de la fecha</th>
											<th>Estado</th>
											<th>Fecha de registro</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td>---</td>
											<td>---</td>
											<td>---</td>
											<td>---</td>
											<td>---</td>
											<td class="text-right">
												<span class="btn btn-default btn-xs glyphicon glyphicon-random" data-toggle="tooltip" data-placement="left" title="Generar resumen" onclick="generarResumen();"></span>
											</td>
										</tr>
										@foreach($listaTResumenDiario as $value)
											<tr>
												<td>{{$value->numeroTicket=='' ? '---' : $value->numeroTicket}}</td>
												<td>{{$value->numeroComprobante}}</td>
												<td>{{$value->fecha}}</td>
												<td>
													<span class="label {{$value->estado=='En proceso' ? 'label-warning' : ($value->estado=='Aprobado' ? 'label-info' : 'label-danger')}}">{{$value->estado}}</span>
												</td>
												<td>{{$value->created_at}}</td>
												<td class="text-right">
													@if(strpos(Session::get('rol'), 'Súper usuario')!==false)
														@if($value->estado!='En proceso')
															<span class="btn btn-default btn-xs glyphicon glyphicon-remove" data-toggle="tooltip" data-placement="left" title="Cambiar a rechazado" disabled></span>
														@else
															<span class="btn btn-default btn-xs glyphicon glyphicon-remove" data-toggle="tooltip" data-placement="left" title="Cambiar a rechazado" onclick="confirmacion(function(){ window.location.href='{{url('resumendiario/cambiarestado/'.$value->codigoResumenDiario.'/Rechazado')}}'; });"></span>
														@endif
													@endif
													<span class="btn btn-default btn-xs glyphicon glyphicon-floppy-save" data-toggle="tooltip" data-placement="left" title="Descargar XML" onclick="window.location.href='{{url('resumendiario/descargarxml/'.$value->codigoResumenDiario)}}';"></span>
												</td>
											</tr>
										@endforeach
									</tbody>
								</table>
							</div>
						</div>
					</div>
					{!!$pagination!!}
				</div>
			</div>
		</div>
	</div>
</div>
<script src="{{asset('viewResources/resumendiario/gestionar.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>
@endsection