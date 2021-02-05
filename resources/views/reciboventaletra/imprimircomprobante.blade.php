<!doctype html>
<html lang="es" style="{{$tEmpresa->formatoComprobante=='Ticket' ? '' : 'margin-top: 25px'}};">
<head>
	<meta charset="UTF-8">
	<title>Document</title>

	<style>
		/* @if($tEmpresa->formatoComprobante=='Ticket') */
			@page {
				margin: 7px;
				margin-right: 30px;
			}
		/* @endif */
	</style>
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
						{!!$tEmpresa->toficina[0]->descripcionComercialComprobante!!}
					</td>
					<td style="width: 270px;">
						<div style="border: 2px solid #4B89CC;">
							<div style="font-size: 15px;padding: 5px;">R.U.C. {{$tEmpresa->ruc}}</div>
							<div style="background-color: #f5f5f5;font-size: 15px;margin: 1px;padding: 7px;">RECIBO DE PAGO</div>
							<div style="font-size: 15px;padding: 5px;">{{$tReciboVentaPago->codigoReciboVentaPago}}</div>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
		<br>
		<table style="border-top: 1px solid #cccccc;padding-top: 7px;width: 100%;">
			<tbody>
				<tr>
					<td style="width: 550px;">
						<fieldset>
							<legend><b>Señor(es)</b></legend>
							{{$tReciboVenta->nombreCompletoCliente}}
						</fieldset>
					</td>
					<td style="text-align: center;">
						<fieldset>
							<legend><b>Fecha de pago</b></legend>
							{{substr($tReciboVentaPago->created_at, 0, 10)}}
						</fieldset>
					</td>
                </tr>
                <tr>
					<td style="padding-top: 5px;width: 550px;">
						<fieldset>
							<legend><b>Dirección</b></legend>
							{{empty($tReciboVenta->direccionCliente) ? '&nbsp;' : $tReciboVenta->direccionCliente}}
						</fieldset>
						
					</td>
					<td style="padding-top: 5px;text-align: center;">
						<fieldset>
                            <legend><b>{{$tReciboVenta->tipoRecibo == 'Factura' ? 'R.U.C' : 'D.N.I'}}</b></legend>
							{{$tReciboVenta->documentoCliente}}
						</fieldset>
					</td>
				</tr>
			</tbody>
		</table>
		<br>
		<table style="width: 100%;">
			<thead>
				<tr>
					<th style="border-bottom: 1px solid #000000;text-align: center;width: 70px;">CANT.</th>
					<th style="border-bottom: 1px solid #000000;">DESCRIPCIÓN</th>
					<th style="border-bottom: 1px solid #000000;text-align: center;width: 120px;">PAGADO</th>
				</tr>
			</thead>
			<tbody>
				<tr>
                    <td style="border-bottom: 1px solid #eeeeee;text-align: center;">1</td>
                    <td style="border-bottom: 1px solid #eeeeee;text-align: left;">Pago de letra de la venta - {{$tReciboVenta->numeroRecibo}}</td>
                    <td style="border-bottom: 1px solid #eeeeee;text-align: center;">S/{{number_format($tReciboVentaPago->monto, 2)}}</td>
                </tr>
				<tr>
					<td style="vertical-align: top;"></td>
					<td style="text-align: right;vertical-align: top;">
						<div style="border: 1px solid transparent;margin: 4px;margin-top: 2px;padding: 5px;">Total:</div>
						<div style="border: 1px solid transparent;margin: 4px;padding: 5px;">Pag. Actualmente:</div>
						<div style="border: 1px solid transparent;margin: 4px;padding: 5px;">Falta Pagar:</div>
					</td>
					<td style="text-align: center;vertical-align: top;">
						<div style="border: 1px solid #999999;margin: 4px;margin-top: 2px;padding: 5px;">S/{{number_format($tReciboVentaPago->monto, 2)}}</div>
						<div style="border: 1px solid #999999;margin: 4px;padding: 5px;">S/{{number_format($tReciboVenta->treciboventaletra->sum('pagado'), 2)}}</div>
						<div style="border: 1px solid #999999;margin: 4px;padding: 5px;">S/{{number_format($tReciboVenta->treciboventaletra->sum('porPagar'), 2)}}</div>
					</td>
				</tr>
			</tbody>
		</table>
	</body>
</html>