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
@if($tEmpresa->formatoComprobante=='Ticket')
	<body style="font-family: 'Calibri (Body)';font-size: 14px;padding-left: 20px;">
		<div style="text-align: center;">*** N.C. ELECTRÓNICA : {{$tReciboVentaNotaCredito->numeroRecibo}} ***</div>
		<br>
		<div style="text-align: center;"><b>{{$tEmpresa->ruc}} - {{$tEmpresa->razonSocial}}</b></div>
		<br>
		{!!$tEmpresa->toficina[0]->descripcionComercialComprobante!!}
		<hr>
		<table>
			<tbody>
				<tr>
					<td><b>Señor(es)</b></td>
					<td>{{$tReciboVentaNotaCredito->treciboventa->nombreCompletoCliente}}</td>
				</tr>
				@if(trim($tReciboVentaNotaCredito->treciboventa->documentoCliente!='00000000'))
					<tr>
						<td><b>{{(strlen($tReciboVentaNotaCredito->treciboventa->documentoCliente)==8 ? 'D.N.I.' : 'R.U.C.')}}</b></td>
						<td>{{$tReciboVentaNotaCredito->treciboventa->documentoCliente}}</td>
					</tr>
				@endif
				@if(trim($tReciboVentaNotaCredito->treciboventa->direccionCliente!=''))
					<tr>
						<td><b>Dirección</b></td>
						<td>{{$tReciboVentaNotaCredito->treciboventa->direccionCliente}}</td>
					</tr>
				@endif
				<tr>
					<td><b>Fecha de emisión</b></td>
					<td>{{substr($tReciboVentaNotaCredito->fechaComprobanteEmitido, 0, 10)}}</td>
				</tr>
			</tbody>
		</table>
		<hr>
		<table style="width: 100%;">
			<thead>
				<tr>
					<th style="text-align: center;">C/U</th>
					<th>DESC.</th>
					<th style="text-align: center;">T.</th>
				</tr>
			</thead>
			<tbody>
				@foreach($tReciboVentaNotaCredito->treciboventanotacreditodetalle as $value)
					<tr>
						<td style="border-bottom: 1px solid #eeeeee;text-align: center;">{{number_format($value->cantidadProducto, 3, '.', '')}}<br>{{$value->unidadMedidaProducto}}</td>
						<td style="border-bottom: 1px solid #eeeeee;text-align: left;"><div>{{$value->nombreProducto.' '.$value->informacionAdicionalProducto}}</div></td>
						<td style="border-bottom: 1px solid #eeeeee;text-align: center;">{{number_format($value->precioVentaTotalProducto, 2)}}</td>
					</tr>
				@endforeach
				<tr>
					<td colspan="2" style="text-align: right;vertical-align: top;">
						<div style="border: 1px solid transparent;margin: 4px;margin-top: 2px;">S.T. ({{$tReciboVentaNotaCredito->treciboventa->divisa=='Soles' ? 'S/' : 'US$'}}):</div>
						<div style="border: 1px solid transparent;margin: 4px;">I.G.V. ({{$tReciboVentaNotaCredito->treciboventa->divisa=='Soles' ? 'S/' : 'US$'}}):</div>
						<div style="border: 1px solid transparent;margin: 4px;">Total ({{$tReciboVentaNotaCredito->treciboventa->divisa=='Soles' ? 'S/' : 'US$'}}):</div>
					</td>
					<td style="text-align: center;vertical-align: top;">
						<div style="border: 1px solid #999999;margin: 4px;margin-top: 2px;">{{$tReciboVentaNotaCredito->subTotal}}</div>
						<div style="border: 1px solid #999999;margin: 4px;">{{$tReciboVentaNotaCredito->igv}}</div>
						<div style="border: 1px solid #999999;margin: 4px;">{{$tReciboVentaNotaCredito->total}}</div>
					</td>
				</tr>
			</tbody>
		</table>
		<br>
		<div style="border: 1px solid #999999;margin: 2px;padding: 5px;"><b>SON: </b>{{$valorTotalLetras}} {{$tReciboVentaNotaCredito->treciboventa->divisa=='Soles' ? 'soles' : 'dólares americanos'}}</div>
		<div style="border: 1px solid #999999;margin: 2px;padding: 5px;"><b>MOTIVO DE LA NOTA DE CRÉDITO: </b>{{$tReciboVentaNotaCredito->descripcionMotivo}}</div>
		<div style="border: 1px solid #999999;margin: 2px;padding: 5px;"><b>DOCUMENTO QUE MODIFICA LA NOTA DE CRÉDITO: </b>{{$tReciboVentaNotaCredito->treciboventa->numeroRecibo}}</div>
		<div style="border: 1px solid #999999;margin: 2px;padding: 5px;"><b>EMISIÓN DEL COMPROBANTE DE PAGO QUE MODIFICA: </b>{{substr($tReciboVentaNotaCredito->treciboventa->created_at, 0, 10)}}</div>
		<hr>
		<b>{{$tReciboVentaNotaCredito->hash}}</b>
	</body>
@endif
@if($tEmpresa->formatoComprobante=='Normal')
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
							<div style="background-color: #f5f5f5;font-size: 15px;margin: 1px;padding: 7px;">NOTA DE CRÉDITO ELECTRÓNICA</div>
							<div style="font-size: 15px;padding: 5px;">{{$tReciboVentaNotaCredito->numeroRecibo}}</div>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
		<table style="border-top: 1px solid #cccccc;padding-top: 7px;width: 100%;">
			<tbody>
				<tr>
					<td style="width: 550px;">
						<fieldset>
							<legend><b>Señor(es)</b></legend>
							{{$tReciboVentaNotaCredito->treciboventa->nombreCompletoCliente}}
						</fieldset>
					</td>
					<td style="text-align: center;">
						<fieldset>
							<legend><b>{{(strlen($tReciboVentaNotaCredito->treciboventa->documentoCliente)==8 ? 'D.N.I.' : 'R.U.C.')}}</b></legend>
							{{$tReciboVentaNotaCredito->treciboventa->documentoCliente}}
						</fieldset>
					</td>
				</tr>
				<tr>
					<td style="padding-top: 5px;width: 550px;">
						<fieldset>
							<legend><b>Dirección</b></legend>
							{{$tReciboVentaNotaCredito->treciboventa->direccionCliente}}
						</fieldset>
					</td>
					<td style="padding-top: 5px;text-align: center;">
						<fieldset>
							<legend><b>Fecha de emisión</b></legend>
							{{substr($tReciboVentaNotaCredito->fechaComprobanteEmitido, 0, 10)}}
						</fieldset>
					</td>
				</tr>
			</tbody>
		</table>
		<table style="width: 100%;">
			<thead>
				<tr>				
					<th style="border-bottom: 1px solid #000000;text-align: center;width: 70px;">CANT.</th>
					<th style="border-bottom: 1px solid #000000;text-align: center;width: 70px;">UNIDAD</th>
					<th style="border-bottom: 1px solid #000000;">DESCRIPCIÓN</th>
					<th style="border-bottom: 1px solid #000000;text-align: center;width: 100px;">P. UNIT.</th>
					<th style="border-bottom: 1px solid #000000;text-align: center;width: 120px;">VALOR DE VENTA</th>
				</tr>
			</thead>
			<tbody>
				@foreach($tReciboVentaNotaCredito->treciboventanotacreditodetalle as $value)
					<tr>
						<td style="border-bottom: 1px solid #eeeeee;text-align: center;">{{number_format($value->cantidadProducto, 3, '.', '')}}</td>
						<td style="border-bottom: 1px solid #eeeeee;text-align: center;">{{$value->unidadMedidaProducto}}</td>
						<td style="border-bottom: 1px solid #eeeeee;text-align: left;">{{$value->nombreProducto.' '.$value->informacionAdicionalProducto}}</td>
						<td style="border-bottom: 1px solid #eeeeee;text-align: center;">{{$tReciboVentaNotaCredito->treciboventa->divisa=='Soles' ? 'S/' : 'US$'}}{{$value->precioVentaUnitarioProducto}}</td>
						<td style="border-bottom: 1px solid #eeeeee;text-align: center;">{{$tReciboVentaNotaCredito->treciboventa->divisa=='Soles' ? 'S/' : 'US$'}}{{number_format($value->precioVentaTotalProducto, 2)}}</td>
					</tr>
				@endforeach
				<tr>
					<td colspan="3" style="vertical-align: top;">
						<div style="border: 1px solid #999999;margin: 2px;padding: 5px;"><b>SON: </b>{{$valorTotalLetras}} {{$tReciboVentaNotaCredito->treciboventa->divisa=='Soles' ? 'soles' : 'dólares americanos'}}</div>
						<div style="border: 1px solid #999999;margin: 2px;padding: 5px;"><b>MOTIVO DE LA NOTA DE CRÉDITO: </b>{{$tReciboVentaNotaCredito->descripcionMotivo}}</div>
						<div style="border: 1px solid #999999;margin: 2px;padding: 5px;"><b>DOCUMENTO QUE MODIFICA LA NOTA DE CRÉDITO: </b>{{$tReciboVentaNotaCredito->treciboventa->numeroRecibo}}</div>
						<div style="border: 1px solid #999999;margin: 2px;padding: 5px;"><b>EMISIÓN DEL COMPROBANTE DE PAGO QUE MODIFICA: </b>{{substr($tReciboVentaNotaCredito->treciboventa->created_at, 0, 10)}}</div>
					</td>
					<td style="text-align: right;vertical-align: top;">
						<div style="border: 1px solid transparent;margin: 4px;margin-top: 2px;padding: 5px;">Sub total:</div>
						<div style="border: 1px solid transparent;margin: 4px;padding: 5px;">I.G.V.:</div>
						<div style="border: 1px solid transparent;margin: 4px;padding: 5px;">Total:</div>
					</td>
					<td style="text-align: center;vertical-align: top;">
						<div style="border: 1px solid #999999;margin: 4px;margin-top: 2px;padding: 5px;">{{$tReciboVentaNotaCredito->treciboventa->divisa=='Soles' ? 'S/' : 'US$'}}{{$tReciboVentaNotaCredito->subTotal}}</div>
						<div style="border: 1px solid #999999;margin: 4px;padding: 5px;">{{$tReciboVentaNotaCredito->treciboventa->divisa=='Soles' ? 'S/' : 'US$'}}{{$tReciboVentaNotaCredito->igv}}</div>
						<div style="border: 1px solid #999999;margin: 4px;padding: 5px;">{{$tReciboVentaNotaCredito->treciboventa->divisa=='Soles' ? 'S/' : 'US$'}}{{$tReciboVentaNotaCredito->total}}</div>
					</td>
				</tr>
			</tbody>
		</table>
		<hr>
		<b>{{$tReciboVentaNotaCredito->hash}}</b>
	</body>
@endif
</html>