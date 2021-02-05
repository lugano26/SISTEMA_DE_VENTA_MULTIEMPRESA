<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>SYSEF</title>
	<!-- Tell the browser to be responsive to screen width -->
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<!-- Bootstrap 3.3.7 -->
	<link rel="stylesheet" href="{{asset('plugin/adminlte/bower_components/bootstrap/dist/css/bootstrap.min.css')}}">
	<!-- Font Awesome -->
	<link rel="stylesheet" href="{{asset('plugin/adminlte/bower_components/font-awesome/css/font-awesome.min.css')}}">
	<!-- Ionicons -->
	<link rel="stylesheet" href="{{asset('plugin/adminlte/bower_components/Ionicons/css/ionicons.min.css')}}">
	<!-- Select2 -->
	<link rel="stylesheet" href="{{asset('plugin/adminlte/bower_components/select2/dist/css/select2.min.css')}}">
	<!-- Theme style -->
	<link rel="stylesheet" href="{{asset('plugin/adminlte/dist/css/AdminLTE.min.css')}}">
	<!-- iCheck -->
	<link rel="stylesheet" href="{{asset('plugin/adminlte/plugins/iCheck/square/blue.css')}}">

	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
	<script src="{{asset('js/html5shiv.min.js')}}"></script>
	<script src="{{asset('js/respond.min.js')}}"></script>
	<![endif]-->

	<!-- Google Font -->
	<link rel="stylesheet" href="{{asset('css/googlefont.css')}}">

	<link rel="stylesheet" href="{{asset('plugin/pnotify/pnotify.custom.min.css')}}">

	<!-- jQuery 3 -->
	<script src="{{asset('plugin/adminlte/bower_components/jquery/dist/jquery.min.js')}}"></script>

	<script src="{{asset('plugin/pnotify/pnotify.custom.min.js')}}"></script>
	<script src="{{asset('plugin/sweetalert/sweetalert.min.js')}}"></script>
</head>
<body class="hold-transition login-page">
	@if(Session::has('mensajeGlobal'))
		<script>
			$(function()
			{
				@if(Session::get('tipo')=='error')
					@foreach(explode('__BREAKLINE__', Session::get('mensajeGlobal')) as $value)
						@if(trim($value)!='')
							new PNotify(
							{
								title : 'No se pudo proceder',
								text : '{{$value}}',
								type : '{{Session::get('tipo')}}'
							});
						@endif
					@endforeach
				@else
					swal(
					{
						title : '{{Session::get('tipo')=='success' ? 'Correcto' : 'Alerta'}}',
						text : '{!!Session::get('mensajeGlobal')!!}',
						icon : '{{Session::get('tipo')=='success' ? 'success' : 'warning'}}',
						timer: {{Session::get('tipo')=='success' ? '2000' : '60000'}}
					});
				@endif
			});
		</script>
	@endif
	<div class="login-box" style="margin-top: 20px;">
		<div class="login-logo">
			<b>SYS</b>EF
		</div>
		<div class="login-box-body">
			<div class="text-center">
				<b>SISTEMA DE FACTURACIÓN ELECTRÓNICA</b>
				<div class="text-sm">
					Lujan
				</div>
			</div>
			<hr>
			<p class="login-box-msg">Datos de usuario para el acceso al sistema</p>
			<form action="{{url('usuario/login')}}" method="post">
				<div class="form-group has-feedback">
					<input type="text" id="txtCorreoElectronico" name="txtCorreoElectronico" class="form-control" placeholder="Correo electrónico">
					<span class="glyphicon glyphicon-envelope form-control-feedback"></span>
				</div>
				<div class="form-group has-feedback">
					<input type="password" id="passContrasenia" name="passContrasenia" class="form-control" placeholder="Contraseña">
					<span class="glyphicon glyphicon-lock form-control-feedback"></span>
				</div>
				<div class="row">
					<div class="col-xs-12 form-group">
						<select id="selectCodigoEmpresa" name="selectCodigoEmpresa" class="form-control" onchange="onChangeSelectCodigoEmpresa();">
							@foreach($listaTEmpresa as $value)
								<option value="{{$value->codigoEmpresa}}">{{$value->razonSocial}}</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12">
						<div class="col-xs-6" style="background-color: #f5f5f5;padding-top: 5px;">
							<label style="cursor: pointer;"><input type="radio" id="radioLocalOficina" name="radioLocal" checked="true" value="1" onclick="onChangeRadioLocal();"> Tienda</label>
						</div>
						<div class="col-xs-6 text-right" style="background-color: #f5f5f5;padding-top: 5px;">
							<label style="cursor: pointer;"><input type="radio" id="radioLocalAlmacen" name="radioLocal" value="0" onclick="onChangeRadioLocal();"> Almacén</label>
						</div>
					</div>
				</div>
				<div id="divSelectCodigoLocal" class="row">
					<div class="col-xs-12 form-group">
						<select id="selectCodigoOficina" name="selectCodigoOficina" class="form-control">
							@foreach($listaTEmpresa[0]->toficina as $value)
								<option value="{{$value->codigoOficina}}">{{$value->descripcion}}</option>
							@endforeach
						</select>
					</div>
				</div>
				<div id="divSelectCodigoAlmacen" class="row" style="display: none;">
					<div class="col-xs-12 form-group">
						<select id="selectCodigoAlmacen" name="selectCodigoAlmacen" class="form-control">
							@foreach($listaTEmpresa[0]->talmacen as $value)
								<option value="{{$value->codigoAlmacen}}">{{$value->descripcion}}</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12">
						{{csrf_field()}}
						<button type="submit" class="btn btn-primary btn-block btn-flat">Ingresar al sistema</button>
					</div>
				</div>
			</form>
		</div>
		<!-- /.login-box-body -->
	</div>
	<!-- /.login-box -->
	<!-- Bootstrap 3.3.7 -->
	<script src="{{asset('plugin/adminlte/bower_components/bootstrap/dist/js/bootstrap.min.js')}}"></script>
	<!-- Select2 -->
	<script src="{{asset('plugin/adminlte/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
	<script>
		$(function()
		{
			$('#selectCodigoEmpresa').select2({
				width: '100%'
			});

			if(typeof localStorage.codigoEmpresa!='undefined')
			{
				$('#selectCodigoEmpresa').val(localStorage.codigoEmpresa).change();
			}
		});

		var empresaLocales={};

		@foreach($listaTEmpresa as $value)
			empresaLocales['{{$value->codigoEmpresa}}Oficina']=[];
			empresaLocales['{{$value->codigoEmpresa}}Almacen']=[];

			@foreach($value->toficina as $item)
				empresaLocales['{{$value->codigoEmpresa}}Oficina'].push(['{{$item->codigoOficina}}', '{{$item->descripcion}}']);
			@endforeach

			@foreach($value->talmacen as $item)
				empresaLocales['{{$value->codigoEmpresa}}Almacen'].push(['{{$item->codigoAlmacen}}', '{{$item->descripcion}}']);
			@endforeach
		@endforeach

		function onChangeSelectCodigoEmpresa()
		{
			var htmlTemp=null;

			$('#selectCodigoOficina').html(null);
			$('#selectCodigoAlmacen').html(null);

			htmlTemp='';

			for(var i=0; i<empresaLocales[$('#selectCodigoEmpresa').val()+'Oficina'].length; i++)
			{
				htmlTemp+='<option value="'+empresaLocales[$('#selectCodigoEmpresa').val()+'Oficina'][i][0]+'">'+empresaLocales[$('#selectCodigoEmpresa').val()+'Oficina'][i][1]+'</option>';
			}

			$('#selectCodigoOficina').html(htmlTemp);

			htmlTemp='';

			for(var i=0; i<empresaLocales[$('#selectCodigoEmpresa').val()+'Almacen'].length; i++)
			{
				htmlTemp+='<option value="'+empresaLocales[$('#selectCodigoEmpresa').val()+'Almacen'][i][0]+'">'+empresaLocales[$('#selectCodigoEmpresa').val()+'Almacen'][i][1]+'</option>';
			}

			$('#selectCodigoAlmacen').html(htmlTemp);

			localStorage.codigoEmpresa=$('#selectCodigoEmpresa').val();
		}

		function onChangeRadioLocal()
		{
			if($('#radioLocalOficina').is(':checked'))
			{
				$('#divSelectCodigoAlmacen').hide();
				$('#divSelectCodigoLocal').show();
			}
			else
			{
				$('#divSelectCodigoAlmacen').show();
				$('#divSelectCodigoLocal').hide();
			}
		}
	</script>
</body>
</html>