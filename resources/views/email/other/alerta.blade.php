<!doctype html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
</head>
<body>
	<div
		style="
			background-color: #eeeeee;
			font-size: 22px;
			padding: 10px;
			text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.4);">
		<img src="http://sysef.com/resources_sysef/logo.png" height="50" 
			onclick="window.location.href='http://sysef.com';" 
			style="
				cursor: pointer;
				display: inline-block;
				vertical-align: middle;">
		<h3 style="display: inline-block;margin-bottom: 0px;margin-top: 0px;padding-bottom: 0px;padding-top: 0px;vertical-align: middle;">Sistema de facturación electrónica</h3>
	</div>
	@if($tipo=='divAlertaAzul')
		<div style="background-color: #58A1EF;
			background-image: url('http://sysef.com/resources_sysef/alert.png');
			background-position: 3px center;
			background-repeat: no-repeat;
			color: white;
			margin: 4px;
			padding: 7px;
			padding-left: 33px;
			text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
			width: auto;">
			{!!$mensaje!!}
		</div>
	@endif
	@if($tipo=='divAlertaLadrillo')
		<div style="background-color: #DBC347;
			background-image: url('http://sysef.com/resources_sysef/alert.png');
			background-position: 3px center;
			background-repeat: no-repeat;
			color: white;
			margin: 4px;
			padding: 7px;
			padding-left: 33px;
			text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
			width: auto;">
			{!!$mensaje!!}
		</div>
	@endif
	@if($tipo=='divAlertaRojo')
		<div style="background-color: #d34141;
			background-image: url('http://sysef.com/resources_sysef/alert.png');
			background-position: 3px center;
			background-repeat: no-repeat;
			color: white;
			margin: 4px;
			padding: 7px;
			padding-left: 33px;
			text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
			width: auto;">
			{!!$mensaje!!}
		</div>
	@endif
	<hr>
	<b>
		<table>
			<tr>
				<td colspan="2">Atte: Plataforma <a href="http://sysef.com/">sysef.com</a></td>
			</tr>
			<tr><td colspan="2"><br></td></tr>
			<tr>
				<td colspan="2">Saludos.</td>
			</tr>
		</table>
	</b>
</body>
</html>