@extends('template.layoutgeneral')
@section('titulo', 'Egreso')
@section('subTitulo', 'Lista de egresos')
@section('cuerpoGeneral')
<div class="row">
	<div class="col-md-12">
		<div class="nav-tabs-custom">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#tab_1-1">Lista de egresos</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="tab_1-1">
					<div class="row">
						<div class="col-md-12">
							<div>
								<form id="frmSearch" method="get" action="{{url('egreso/ver')}}" onsubmit="validarExpresion(event)">
									<div class="input-group input-group-sm">
										<input id="textSearch" onkeyup="searchItem(event);" type="text" class="form-control" placeholder="Buscar por oficina, personal, descripción (Enter)" name="q" value="{{ !empty($q) ? $q : '' }}" autofocus>
										<span class="input-group-btn">
											<button type="submit" class="btn btn-primary btn-flat"><i class="fa fa-search" ></i></button>
										</span>
									</div>
								</form>
							</div>
							<hr>
							<div class="table-responsive">
								<table id="tableCompras" class="table table-striped" style="min-width: 777px;">
									<thead>
										<tr>
											<th>Oficina</th>
											<th>Personal</th>
											<th>Descripción</th>
											<th class="text-center">Monto</th>
											<th class="text-center">Fecha registro</th>
										</tr>
									</thead>
									<tbody>
										@foreach($listaTEgreso as $value)
											<tr class="elementoBuscar">
												<td>{{$value->toficina->descripcion}}</td>
												<td>{{explode("@", $value->tpersonal->correoElectronico)[0]}}</td>
												<td>{{$value->descripcion}}</td>
												<td class="text-center">S/{{ $value->monto }}</td>
												<td class="text-center">{{ $value->created_at }}</td>
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
<script src="{{asset('viewResources/egreso/ver.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>
@endsection