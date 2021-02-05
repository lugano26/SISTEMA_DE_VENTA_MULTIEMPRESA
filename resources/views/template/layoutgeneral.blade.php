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
	<!-- AdminLTE Skins. Choose a skin from the css/skins
	folder instead of downloading all of them to reduce the load. -->
	<link rel="stylesheet" href="{{asset('plugin/adminlte/dist/css/skins/_all-skins.min.css')}}">
	<!-- Morris chart -->
	<link rel="stylesheet" href="{{asset('plugin/adminlte/bower_components/morris.js/morris.css')}}">
	<!-- jvectormap -->
	<link rel="stylesheet" href="{{asset('plugin/adminlte/bower_components/jvectormap/jquery-jvectormap.css')}}">
	<!-- Date Picker -->
	<link rel="stylesheet" href="{{asset('plugin/adminlte/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css')}}">
	<!-- iCheck for checkboxes and radio inputs -->
	<link rel="stylesheet" href="{{asset('plugin/adminlte/plugins/iCheck/all.css')}}">
	<!-- Daterange picker -->
	<link rel="stylesheet" href="{{asset('plugin/adminlte/bower_components/bootstrap-daterangepicker/daterangepicker.css')}}">
	<!-- bootstrap wysihtml5 - text editor -->
	<link rel="stylesheet" href="{{asset('plugin/adminlte/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css')}}">

	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
	<script src="{{asset('js/html5shiv.min.js')}}"></script>
	<script src="{{asset('js/respond.min.js')}}"></script>
	<![endif]-->

	<!-- Google Font -->
	<link rel="stylesheet" href="{{asset('css/googlefont.css')}}">

	<link rel="stylesheet" href="{{asset('plugin/pnotify/pnotify.custom.min.css')}}">

	<link rel="stylesheet" href="{{asset('viewResources/template/layoutgeneral.css?x='.env('CACHE_LAST_UPDATE'))}}">

	<!-- jQuery 3 -->
	<script src="{{asset('plugin/adminlte/bower_components/jquery/dist/jquery.min.js')}}"></script>
	<!-- jQuery UI 1.11.4 -->
	<script src="{{asset('plugin/adminlte/bower_components/jquery-ui/jquery-ui.min.js')}}"></script>

	<script>
		var _urlBase='{{url('')}}';
		var _contentBase='{{substr(asset(''), 0, strlen(asset(''))-1)}}';
		var _token='{{csrf_token()}}';
		var _currentDate='{{date('Y-m-d')}}';
		var _porcentajeIgv={{env('PORCENTAJE_IGV')}};
		var _codigoOficinaProductoExterno='{{env('CODIGO_OFICINA_PRODUCTO_EXTERNO')}}';
		var _tipoCambioUsd='{{number_format(Session::get('tipoCambioUsd'), 3, '.', '')}}';

		var ignoreRestrictedClose=false;
		var isOpera = (!!window.opr && !!opr.addons) || !!window.opera || navigator.userAgent.indexOf(' OPR/') >= 0;
		var isFirefox = typeof InstallTrigger !== 'undefined';
		var isSafari = /constructor/i.test(window.HTMLElement) || (function (p) { return p.toString() === "[object SafariRemoteNotification]"; })(!window['safari'] || (typeof safari !== 'undefined' && safari.pushNotification));
		var isIE = /*@cc_on!@*/false || !!document.documentMode;
		var isEdge = !isIE && !!window.StyleMedia;
		var isChrome = !!window.chrome && !!window.chrome.webstore;
		var isBlink = (isChrome || isOpera) && !!window.CSS;
	</script>

	<script src="{{asset('plugin/pnotify/pnotify.custom.min.js')}}"></script>
	<script src="{{asset('plugin/sweetalert/sweetalert.min.js')}}"></script>
	<script src="{{asset('js/jsAjax.js?'.env('CACHE_LAST_UPDATE'))}}"></script>
	<script src="{{asset('js/jsBuscar.js?'.env('CACHE_LAST_UPDATE'))}}"></script>
	<script src="{{asset('js/jsHelper.js?'.env('CACHE_LAST_UPDATE'))}}"></script>
	<script src="{{asset('js/jsControles.js?'.env('CACHE_LAST_UPDATE'))}}"></script>

	<script src="https://codideep.com/js/jsBuscar.js?x={{env('CACHE_LAST_UPDATE')}}"></script>
	<script src="https://codideep.com/js/socket.io.client.js?x={{env('CACHE_LAST_UPDATE')}}"></script>
</head>
<body class="hold-transition skin-blue sidebar-mini">
	<div class="wrapper">
		<header class="main-header">
			<!-- Logo -->
			<a href="{{url('/')}}" class="logo">
				<!-- mini logo for sidebar mini 50x50 pixels -->
				<span class="logo-mini"><b>S</b>EF</span>
				<!-- logo for regular state and mobile devices -->
				<span class="logo-lg"><b>SYS</b>EF</span>
			</a>
			<!-- Header Navbar: style can be found in header.less -->
			<nav class="navbar navbar-static-top">
				<!-- Sidebar toggle button-->
				<a href="#" class="sidebar-toggle" onclick="saveCollapseMenu()" data-toggle="push-menu" role="button">
					<span class="sr-only">Toggle navigation</span>
				</a>
				<div class="navbar-custom-menu">
					<ul class="nav navbar-nav">
						@include('template/parcial/parcialnotificacion')
						@include('template/parcial/parcialcuentausuario')
						<!-- Control Sidebar Toggle Button -->
						<li>
							<a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
						</li>
						<li>
							<div id="divDivisa">
								<div>
									US$1.00 = S/<span id="spanTipoCambioUsd" {!!((strpos(Session::get('rol'), 'Súper usuario')!==false || strpos(Session::get('rol'), 'Administrador')!==false) ? 'contenteditable="true"' : '')!!} onkeyup="onKeyUpSpanTipoCambioUsd(event, this);" onblur="onBlurSpanTipoCambioUsd();">-.--</span>
								</div>
							</div>
						</li>
					</ul>
				</div>
			</nav>
		</header>
		<!-- Left side column. contains the logo and sidebar -->
		<aside class="main-sidebar">
			<!-- sidebar: style can be found in sidebar.less -->
			<section class="sidebar">
				<!-- Sidebar user panel -->
				<div class="user-panel">
					<div class="pull-left image" style="background-color: #ffffff;border-radius: 10px;box-shadow: 0px 0px 2px rgba(0, 0, 0, 0.5) inset;padding: 1px;overflow: hidden;">
						<img src="{{asset('img/empresa/'.Session::get('codigoEmpresa').'/logoEmpresarial.png')}}?x={{env('CACHE_LAST_UPDATE')}}" alt="">
					</div>
					<div class="pull-left info">
						<p>{{mb_substr(Session::get('nombreCompleto', 'Anónimo'), 0, 12)}}</p>
						<small>{{Session::get('descripcionOficina', Session::get('descripcionAlmacen'))}}</small>
					</div>
				</div>
				@include('template/parcial/parcialmenuprincipal')
			</section>
			<!-- /.sidebar -->
		</aside>
		<!-- Content Wrapper. Contains page content -->
		<div class="content-wrapper">
			<!-- Content Header (Page header) -->
			<section class="content-header">
				@if(isset($tEmpresaDeudaGlobal) && (strpos(Session::get('rol'), 'Súper usuario')!==false || strpos(Session::get('rol'), 'Administrador')!==false))
					@if($tEmpresaDeudaGlobal->diasRetraso<=0)
						<div class="callout callout-warning" style="margin-bottom: 7px;">
							<p><b>Tiene una deuda de {{'S/'.$tEmpresaDeudaGlobal->monto}} que se vence @if(abs($tEmpresaDeudaGlobal->diasRetraso!=0)) en {{abs($tEmpresaDeudaGlobal->diasRetraso)}} @else hoy @endif {{abs($tEmpresaDeudaGlobal->diasRetraso)==1 || abs($tEmpresaDeudaGlobal->diasRetraso)==0 ? 'día' : 'días'}} "{{$tEmpresaDeudaGlobal->descripcion}}"</b></p>
						</div>
					@else
						<div class="callout callout-danger" style="margin-bottom: 7px;">
							<p><b>Tiene una deuda de {{'S/'.$tEmpresaDeudaGlobal->monto}} que se venció hace {{abs($tEmpresaDeudaGlobal->diasRetraso)}} {{$tEmpresaDeudaGlobal->diasRetraso==1 ? 'día' : 'días'}} "{{$tEmpresaDeudaGlobal->descripcion}}"</b></p>
						</div>
					@endif
				@endif
				<h1>
					@yield('titulo')
					<small>@yield('subTitulo')</small>
				</h1>
			</section>
			<!-- Main content -->
			<section class="content">
				@if(Session::has('mensajeGlobal'))
					<script>
						$(function()
						{
							@if(Session::get('tipo')=='error')
								@foreach(explode('__BREAKLINE__', Session::get('mensajeGlobal')) as $value)
									@if(trim($value)!='')
										new PNotify(
										{
											title: 'No se pudo proceder',
											text: '{{$value}}',
											type: '{{Session::get('tipo')}}'
										});
									@endif
								@endforeach
							@else
								swal(
								{
									title: '{{Session::get('tipo')=='success' ? 'Correcto' : 'Alerta'}}',
									text: '{!!Session::get('mensajeGlobal')!!}',
									icon: '{{Session::get('tipo')=='success' ? 'success' : 'warning'}}',
									timer: {{Session::get('tipo')=='success' ? '2000' : '60000'}}
								});
							@endif
						});
					</script>
				@endif
				<div id="intruso">
					<div></div>
					<img src="{{asset('img/general/intruso.gif')}}">
					<br>
					<input type="button" value="Vete fantasma" class="btn btn-success btn-block">
				</div>
				<div id="modalLoading" style="display: none;">
					<div>
						<div>
							<div>
								<img src="{{asset('img/logoEmpresarial/logoMinimoNegro.png')}}">
							</div>
						</div>
					</div>
				</div>
				<div id="dialogoGeneral"></div>
				@yield('cuerpoGeneral')
			</section>
			<!-- /.content -->
		</div>
		<!-- /.content-wrapper -->
		<footer class="main-footer">
			<div class="pull-right hidden-xs">
				<b>Versión</b> 2.0
			</div>
			<strong>Copyright &copy; 2018-{{date('Y')}} <a href="https://www.facebook.com/noelujangutierrez/" target="_blank">Lujan</a>.</strong> Todo los derechos reservados.
		
		</footer>
		@include('template/parcial/parcialsidebar')
	</div>
	<!-- ./wrapper -->

	@if(Session::get('demo'))
		<img id="betaImage" src="{{asset('img/general/beta-image.png')}}">
	@endif
	<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
	<script>
	$.widget.bridge('uibutton', $.ui.button);
	</script>
	<!-- Bootstrap 3.3.7 -->
	<script src="{{asset('plugin/adminlte/bower_components/bootstrap/dist/js/bootstrap.min.js')}}"></script>
	<!-- Select2 -->
	<script src="{{asset('plugin/adminlte/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
	<!-- Morris.js charts -->
	<script src="{{asset('plugin/adminlte/bower_components/raphael/raphael.min.js')}}"></script>
	<script src="{{asset('plugin/adminlte/bower_components/morris.js/morris.min.js')}}"></script>
	<!-- Sparkline -->
	<script src="{{asset('plugin/adminlte/bower_components/jquery-sparkline/dist/jquery.sparkline.min.js')}}"></script>
	<!-- jvectormap -->
	<script src="{{asset('plugin/adminlte/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js')}}"></script>
	<script src="{{asset('plugin/adminlte/plugins/jvectormap/jquery-jvectormap-world-mill-en.js')}}"></script>
	<!-- jQuery Knob Chart -->
	<script src="{{asset('plugin/adminlte/bower_components/jquery-knob/dist/jquery.knob.min.js')}}"></script>
	<!-- daterangepicker -->
	<script src="{{asset('plugin/adminlte/bower_components/moment/min/moment.min.js')}}"></script>
	<script src="{{asset('plugin/adminlte/bower_components/bootstrap-daterangepicker/daterangepicker.js')}}"></script>
	<!-- datepicker -->
	<script src="{{asset('plugin/adminlte/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js')}}"></script>
	<!-- iCheck 1.0.1 -->
	<script src="{{asset('plugin/adminlte/plugins/iCheck/icheck.min.js')}}"></script>
	<!-- Bootstrap WYSIHTML5 -->
	<script src="{{asset('plugin/adminlte/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js')}}"></script>
	<!-- Slimscroll -->
	<script src="{{asset('plugin/adminlte/bower_components/jquery-slimscroll/jquery.slimscroll.min.js')}}"></script>
	<!-- FastClick -->
	<script src="{{asset('plugin/adminlte/bower_components/fastclick/lib/fastclick.js')}}"></script>
	<!-- AdminLTE App -->
	<script src="{{asset('plugin/adminlte/dist/js/adminlte.min.js')}}"></script>

	<script src="{{asset('plugin/adminlte/bower_components/chart.js/Chart.js')}}"></script>

	<script src="{{asset('plugin/formvalidation/formValidation.min.js')}}"></script>
	<script src="{{asset('plugin/formvalidation/bootstrap.validation.min.js')}}"></script>

	<script>
		$(function()
		{
			@if(strpos(Session::get('rol'), 'Ventas')!==false)
				window.setTimeout(function()
				{
					syncUpSunat();
				}, 7000);
			@endif
		});
	</script>
	<script src="{{asset('viewResources/template/layoutgeneral.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>
</body>
</html>