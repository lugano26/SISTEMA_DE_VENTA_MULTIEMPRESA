<!doctype html>
<html lang="es" style="margin-top: 25px;">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
</head>
<body style="font-size: 12px;text-align: center;">
	<table style="text-align: center;width: 100%;">
		<tbody>
			<tr>
				<td style="width: 75px;">
					<img src="{{$base64Logo}}" width="77">
				</td>
				<td>
					<h2 style="margin-bottom: 7px;margin-top: 0px;padding-top: 0px;">{{$tEmpresa->razonSocial}}</h2>
					<div style="text-align: justify;">{!!$tEmpresa->toficina[0]->descripcionComercialComprobante!!}</div>
				</td>
				<td style="width: 270px;">
					<div style="border: 2px solid #4B89CC;">
						<div style="font-size: 15px;padding: 5px;">R.U.C. {{$tEmpresa->ruc}}</div>
						<div style="background-color: #f5f5f5;font-size: 15px;margin: 1px;padding: 7px;">GUÍA DE REMISIÓN ELECTRÓNICA REMITENTE</div>
						<div style="font-size: 15px;padding: 5px;">{{$tReciboVentaGuiaRemision->numeroGuiaRemision}}</div>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
	<table style="border-top: 1px solid #cccccc;padding-top: 7px;width: 100%;">
		<tbody>
			<tr>
				<td style="width: 50%;">
					<fieldset>
						<legend><b>Señor(es)</b></legend>
						{{$tReciboVentaGuiaRemision->nombreCompletoReceptor}}
					</fieldset>
				</td>
				<td style="text-align: center;">
					<fieldset>
						<legend><b>{{(strlen($tReciboVentaGuiaRemision->documentoReceptor)==8 ? 'D.N.I.' : 'R.U.C.')}}</b></legend>
						{{$tReciboVentaGuiaRemision->documentoReceptor}}
					</fieldset>
				</td>
				<td style="text-align: center;">
					<fieldset>
						<legend><b>Fecha impresión guía</b></legend>
						{{substr($tReciboVentaGuiaRemision->fechaIniciaTraslado, 0, 10)}}
					</fieldset>
				</td>
			</tr>
			<tr>
				<td>
					<fieldset>
						<legend><b>Motivo del traslado</b></legend>
						{{$tReciboVentaGuiaRemision->motivoTraslado}}
					</fieldset>
				</td>
				<td style="text-align: center;">
					<fieldset>
						<legend><b>Documento relacionado</b></legend>
						{{$tReciboVentaGuiaRemision->treciboventa->numeroRecibo}}
					</fieldset>
				</td>
				<td style="text-align: center;">
					<fieldset>
						<legend><b>Fecha inicio traslado</b></legend>
						{{substr($tReciboVentaGuiaRemision->fechaIniciaTraslado, 0, 10)}}
					</fieldset>
				</td>
			</tr>
			<tr>
				<td>
					<fieldset>
						<legend><b>Dirección de partida</b></legend>
						{{$tReciboVentaGuiaRemision->ubigeoPartida}} - ({{$tReciboVentaGuiaRemision->tubigeopartida->ubicacion}})
						<br>
						{{$tReciboVentaGuiaRemision->direccionPartida}}
					</fieldset>
				</td>
				<td colspan="2">
					<fieldset>
						<legend><b>Dirección de llegada</b></legend>
						{{$tReciboVentaGuiaRemision->ubigeoLlegada}} - ({{$tReciboVentaGuiaRemision->tubigeollegada->ubicacion}})
						<br>
						{{$tReciboVentaGuiaRemision->direccionLlegada}}
					</fieldset>
				</td>
			</tr>
		</tbody>
	</table>
	<table style="width: 100%;">
		<thead>
			<tr>				
				<th style="border-bottom: 1px solid #000000;">DESCRIPCIÓN</th>
				<th style="border-bottom: 1px solid #000000;text-align: center;width: 70px;">CANT.</th>
				<th style="border-bottom: 1px solid #000000;text-align: center;width: 70px;">UNIDAD</th>
				<th style="border-bottom: 1px solid #000000;text-align: center;width: 100px;">PESO KL's</th>
			</tr>
		</thead>
		<tbody>
			@foreach($tReciboVentaGuiaRemision->treciboventaguiaremisiondetalle as $value)
				<tr>
					<td style="border-bottom: 1px solid #eeeeee;text-align: left;">{{$value->nombreProducto.' '.$value->informacionAdicionalProducto}}</td>
					<td style="border-bottom: 1px solid #eeeeee;text-align: center;">{{number_format($value->cantidadProducto, 3, '.', '')}}</td>
					<td style="border-bottom: 1px solid #eeeeee;text-align: center;">{{$value->unidadMedidaProducto}}</td>
					<td style="border-bottom: 1px solid #eeeeee;text-align: center;">{{$value->pesoKilos}}</td>
				</tr>
			@endforeach
		</tbody>
	</table>
	<hr>
	@if($tReciboVentaGuiaRemision->observacion!='')
		<fieldset style="text-align: left;">
			<legend><b>Observación</b></legend>
			{{$tReciboVentaGuiaRemision->observacion}}
		</fieldset>
	@endif
	<table style="padding-top: 7px;width: 100%;">
		<tbody>
			<tr>
				<td colspan="2">
					Datos del transportista
				</td>
			</tr>
			<tr>
				<td style="text-align: center;width: 50%;">
					<fieldset>
						<legend><b>R.U.C.</b></legend>
						{{$tReciboVentaGuiaRemision->documentoTransportista}}
					</fieldset>
				</td>
				<td style="text-align: left;width: 50%;">
					<fieldset>
						<legend><b>Denominación</b></legend>
						{{$tReciboVentaGuiaRemision->nombreCompletoTransportista}}
					</fieldset>
				</td>
			</tr>
		</tbody>
	</table>
	<hr>
	<b>{{$tReciboVentaGuiaRemision->hash}}</b>
</body>
</html>