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
		<img src="{{$logoEmpresa}}" height="50" 
			onclick="window.location.href='http://sysef.com';" 
			style="
				cursor: pointer;
				display: inline-block;
				vertical-align: middle;">
		<h3 style="display: inline-block;margin-bottom: 0px;margin-top: 0px;padding-bottom: 0px;padding-top: 0px;vertical-align: middle;">{{$nombreEmpresa}}</h3>
	</div>
	<div style="margin: 4px;
    padding: 7px;
    width: auto;">
		{!!$mensaje!!}
    </div>
	<hr>
	<b>
		<table>
			<tr>
				<td colspan="2">Atte: Plataforma <a href="https://web.sysef.com/">sysef.com</a></td>
			</tr>
			<tr><td colspan="2"><br></td></tr>
			<tr>
				<td colspan="2">Saludos.</td>
			</tr>
		</table>
	</b>
</body>
</html>