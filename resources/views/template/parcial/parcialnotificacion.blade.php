<link rel="stylesheet" href="{{asset('viewResources/template/parcial/parcialnotificacion.css?x='.env('CACHE_LAST_UPDATE'))}}">
<li class="dropdown messages-menu">
	<a href="#" id="notificacionIcono" class="dropdown-toggle {{ !empty($tUsuarioNotificacion) && count($tUsuarioNotificacion) > 0 ? 'notificationUser' : ''}}" data-toggle="dropdown">
		<i class="fa fa-envelope"></i>
		@if(!empty($tUsuarioNotificacion))
			<span class="label label-success">{{count($tUsuarioNotificacion)}}</span>
		@endif
	</a>
	<ul class="dropdown-menu">
		<li class="header">Tienes {{!empty($tUsuarioNotificacion) ? count($tUsuarioNotificacion) : '0'}} mensajes</li>
		<li>
			<ul class="menu" id="contenedorNotificaciones">
				@if(!empty($tUsuarioNotificacion))
				@foreach ($tUsuarioNotificacion as $value )
				<li>
					<a href="#" onclick="dialogoAjax('dialogoGeneral', 'modal-lg', 'Mensaje recibido', { _token : '{{csrf_token()}}', codigoUsuarioNotificacion : '{{$value->codigoUsuarioNotificacion}}' }, '{{url('usuarionotificacion/detalle')}}', 'POST', null, null, false, true);">
						<div class="pull-left">
							@if ($value->url != '')
								<img src="{{asset($value->url)}}" class="img-circle" alt="Notification Image">
							@else
								<img src="{{asset('plugin/adminlte/dist/img/user2-160x160.png')}}?x={{env('CACHE_DATE')}}" class="img-circle" alt="Notification Image">
							@endif							
						</div>
						<h4>
							Equipo Sysef
							<small><i class="fa fa-clock-o"></i> {{$value->created_at->format('d-m-Y')}}</small>
						</h4>
						<p>{{strlen($value->descripcion) > 35 ? (substr($value->descripcion, 0, 32) . '...' ) : $value->descripcion}}</p>
					</a>
				</li>
				@endforeach
				@endif
			</ul>
		</li>
		@if(!empty($tUsuarioNotificacion))
			<li class="footer"><a style="font-weight: bold;" href="{{url('usuarionotificacion/marcartodoleido')}}">Marcar todo como leido</a></li>
		@endif
	</ul>
</li>
@if(!empty($tUsuarioNotificacion) && count($tUsuarioNotificacion) > 0)
	<script>
		$(function(){
			new PNotify({
				title: 'Mensaje importante',
				text: 'Tienes {{count($tUsuarioNotificacion)}} mensaje{{count($tUsuarioNotificacion) > 1 ? "s" : ""}} importante sin leer. <br>Revísalo en el icono \'<i style="font-size: 10px" class="fa fa-envelope"></i>\' del menú.',
				icon: 'fa fa-envelope',
				delay: 3000
			});
		});
	</script>
@endif