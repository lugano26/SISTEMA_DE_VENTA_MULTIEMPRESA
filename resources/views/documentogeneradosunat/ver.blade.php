@extends('template.layoutgeneral')
@section('titulo', 'Documentos generados SUNAT')
@section('subTitulo', 'Lista de documentos SUNAT')
@section('cuerpoGeneral')
<div class="row">
	<div class="col-md-12">
		<div class="nav-tabs-custom">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#tab_1-1">Lista de documentos SUNAT</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="tab_1-1">
					<div class="row">
						<div class="col-md-12">
							<div class="table-responsive">
								<div>
									<form id="frmSearch" method="get" action="{{url('documentogeneradosunat/ver')}}" onsubmit="validarExpresion(event)">
										<div class="input-group input-group-sm">
											<input id="textSearch" type="text" class="form-control" onkeyup="searchItem(event);" placeholder="Buscar por número de comprobante (Enter)" name="q" value="{{ !empty($q) ? $q : '' }}" autofocus>
											<span class="input-group-btn">
											<button type="buttom" class="btn btn-primary btn-flat"><i class="fa fa-search" ></i></button>
											</span>
										</div>
									</form>
								</div>
								<hr>
								<table class="table table-striped" style="min-width: 777px;">
									<thead>
										<tr>
											<th>Respuesta SUNAT</th>
											<th class="text-center">Nº comprobante</th>
											<th class="text-center">Nº comprobante afectado</th>
											<th class="text-center">Tipo</th>
											<th class="text-center">Estado</th>
											<th class="text-center">Fecha de registro</th>
										</tr>
									</thead>
									<tbody>
										@foreach($listaTDocumentoGeneradoSunat as $value)
											<tr>
												<td>{{$value->responseDescription}} ({{$value->responseCode}})</td>
												<td class="text-center">{{$value->numeroComprobante}}</td>
												<td class="text-center">{{($value->numeroComprobanteAfectado=='' ? '---' : $value->numeroComprobanteAfectado)}}</td>
												<td class="text-center">{{$value->tipo}}</td>
												<td class="text-center">
													<span class="label {{$value->estado=='Aprobado' ? 'label-success' : ($value->estado=='Rechazado' ? 'label-danger' : 'label-warning')}}">{{$value->estado}}</span>
												</td>
												<td class="text-center">{{$value->created_at}}</td>
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
<script src="{{asset('viewResources/documentogeneradosunat/ver.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>
@endsection