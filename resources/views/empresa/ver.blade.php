@extends('template.layoutgeneral')
@section('titulo', 'Empresa')
@section('subTitulo', 'Ver')
@section('cuerpoGeneral')
<div class="row">
	<div class="col-md-12">
		<div class="nav-tabs-custom">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#tab_1-1">Lista de empresas</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="tab_1-1">
					<div class="row">
						<div class="col-md-12">
							<div>
								<input type="text" id="txtBuscar" name="txtBuscar" class="form-control" autocomplete="off" placeholder="Ingrese datos de búsqueda (Enter)" onkeyup="filtrarHtml('tableEmpresa', this.value, false, 0, event);">
							</div>
							<hr>
							<div class="table-responsive">
								<table id="tableEmpresa" class="table table-striped" style="min-width: 777px;">
									<thead>
										<tr>
											<th></th>
											<th>RUC</th>
											<th>Razón social</th>
											<th>Representante legal</th>
											<th>Página de consultas</th>
											<th class="text-center">F.E.</th>
											<th class="text-center">Estado</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										@foreach($listaTEmpresa as $value)
											<tr class="elementoBuscar">
												<td style="width: 35px;"><img src="{{asset('img/empresa/'.$value->codigoEmpresa.'/logoEmpresarial.png')}}" height="30" width="30"></td>
												<td>{{$value->ruc}}</td>
												<td>{{$value->razonSocial}}</td>
												<td>{{$value->representanteLegal}}</td>
												<td><a href="{{$value->urlConsultaFactura}}" target="_blank">{{$value->urlConsultaFactura}}</a></td>
												<td class="text-center">{!!$value->facturacionElectronica ? '<span class="label label-success">Si</span>' : '<span class="label label-warning">No</span>'!!}</td>
												<td class="text-center">{!!$value->estado ? '<span class="label label-success">Habilitado</span>' : '<span class="label label-danger">Deshabilitado</span>'!!}</td>
												<td class="text-right">
													<a href="{{url('empresadeuda/gestionar/'.$value->codigoEmpresa)}}" class="btn btn-default btn-xs glyphicon glyphicon-usd" data-toggle="tooltip" data-placement="left" title="Gestionar deudas"></a>
													<span class="btn btn-default btn-xs glyphicon glyphicon-pencil" data-toggle="tooltip" data-placement="left" title="Editar" onclick="dialogoAjax('dialogoGeneral', null, '{{$value->razonSocial}} (Editar)', { _token : '{{csrf_token()}}', codigoEmpresa : '{{$value->codigoEmpresa}}' }, '{{url('empresa/editar')}}', 'POST', null, null, false, true);"></span>
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