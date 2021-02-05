<!-- User Account: style can be found in dropdown.less -->
<li class="dropdown user user-menu">
	<a href="#" class="dropdown-toggle" data-toggle="dropdown">
		<img src="{{asset('img/empresa/'.Session::get('codigoEmpresa').'/logoEmpresarial.png')}}?x={{env('CACHE_LAST_UPDATE')}}" class="user-image" style="background-color: #ffffff;" alt="">
		<span class="hidden-xs">{{mb_substr(Session::get('nombreCompleto', 'Anónimo'), 0, 12)}}</span>
	</a>
	<ul class="dropdown-menu">
		<!-- User image -->
		<li class="user-header">
			<img src="{{asset('img/empresa/'.Session::get('codigoEmpresa').'/logoEmpresarial.png')}}?x={{env('CACHE_LAST_UPDATE')}}" class="img-circle" style="background-color: #ffffff;" alt="">
			<p>
				{{mb_substr(Session::get('nombreCompleto', 'Anónimo'), 0, 12)}}
				<small>{{Session::get('rol', 'Acceso público')}}</small>
			</p>
		</li>
		<!-- Menu Body -->
		<li class="user-body">
			<div class="row">
				<div class="col-xs-12 text-center">
					Sistema de información empresarial para facturación electrónica comunicado directamente a la SUNAT.
				</div>
				<div class="col-xs-12" style="margin-top: 2rem">
					<button type="button" onclick="dialogoAjax('dialogoGeneral', 'modal-md', 'Cambiar de Oficina/Almacén', { _token : '{{csrf_token()}}' }, '{{url('usuario/cambiarlocal')}}', 'GET', null, null, false, true);" class="btn btn-flat btn-block btn-primary btn-sm">Cambiar Oficina/Almacén</button>
				</div>
			</div>
			<!-- /.row -->
		</li>
		<!-- Menu Footer-->
		<li class="user-footer">
			<div class="pull-left">
				<a href="{{url('personal/ver')}}" class="btn btn-default btn-flat">Mi perfil</a>
			</div>
			<div class="pull-right">
				<a href="{{url('usuario/logout')}}" class="btn btn-default btn-flat">Salir</a>
			</div>
		</li>
	</ul>
</li>