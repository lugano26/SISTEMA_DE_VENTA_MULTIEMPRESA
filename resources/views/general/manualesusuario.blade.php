@extends('template.layoutgeneral')
@section('titulo', 'General')
@section('subTitulo', 'Manuales de usuario')
@section('cuerpoGeneral')
<link rel="stylesheet" href="{{asset('viewResources/general/manualesusuario.css?x='.env('CACHE_LAST_UPDATE'))}}">
<div class="row">
	<div class="col-md-12">
		<div class="nav-tabs-custom">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#tab_1-1">Manuales de usuario</a></li>
			</ul>
			<div class="tab-content">
				<div class="row">
					<div class="col-md-12 text-center">
						<div class="elementManual" onclick="window.open('{{url('manuales/Mod1-Panel-de-control.pdf')}}', '_blank');">
							<div>
								<b>Mod 1</b>
								<br>
								Panel de control
							</div>
						</div>
						<div class="elementManual" onclick="window.open('{{url('manuales/Mod2-Gestion-de-personal.pdf')}}', '_blank');">
							<div>
								<b>Mod 2</b>
								<br>
								Gestión de personal
							</div>
						</div>
						<div class="elementManual" onclick="window.open('{{url('manuales/Mod3-Gestion-de-locales.pdf')}}', '_blank');">
							<div>
								<b>Mod 3</b>
								<br>
								Gestión de locales
							</div>
						</div>
						<div class="elementManual" onclick="window.open('{{url('manuales/Mod4-Gestion-de-compras.pdf')}}', '_blank');">
							<div>
								<b>Mod 4</b>
								<br>
								Gestión de compras
							</div>
						</div>
						<div class="elementManual" onclick="window.open('{{url('manuales/Mod5-Gestion-de-ventas.pdf')}}', '_blank');">
							<div>
								<b>Mod 5</b>
								<br>
								Gestión de ventas
							</div>
						</div>
						<div class="elementManual" onclick="window.open('{{url('manuales/Mod6-Gestion-de-productos.pdf')}}', '_blank');">
							<div>
								<b>Mod 6</b>
								<br>
								Gestión de productos
							</div>
						</div>
						<div class="elementManual" onclick="window.open('{{url('manuales/Mod7-Gestion-de-traslados.pdf')}}', '_blank');">
							<div>
								<b>Mod 7</b>
								<br>
								Gestión de traslados
							</div>
						</div>
						<div class="elementManual" onclick="window.open('{{url('manuales/Mod8-Otras-operaciones.pdf')}}', '_blank');">
							<div>
								<b>Mod 8</b>
								<br>
								Otras operaciones
							</div>
						</div>
						<div class="elementManual" onclick="window.open('{{url('manuales/Mod9-Reportes.pdf')}}', '_blank');">
							<div>
								<b>Mod 9</b>
								<br>
								Reportes
							</div>
						</div>
						<div class="elementManual" onclick="window.open('{{url('manuales/Problemas-frecuentes.pdf')}}', '_blank');">
							<div>
								<b>General</b>
								<br>
								Problemas frecuentes
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection