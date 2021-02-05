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
	<body style="font-family: 'Calibri (Body)';font-size: 16px;padding-left: 20px;">
		<div style="text-align: center;">*PROFORMA*</div>
		<br>
		<div style="text-align: center;"><b>{{$tEmpresa->ruc}} - {{$tEmpresa->razonSocial}}</b></div>
		<br>
		{!!$tEmpresa->toficina[0]->descripcionComercialComprobante!!}
		<hr>
		<table>
			<tbody>
				<tr>
					<td><b>Señor(es)</b></td>
					<td>{{$tReciboVenta->nombreCompletoCliente}}</td>
				</tr>
				<tr>
					<td><b>Documento</b></td>
					<td>{{!empty($tReciboVenta->documentoCliente) ? $tReciboVenta->documentoCliente : '&nbsp;'}}</td>
				</tr>
				@if(trim($tReciboVenta->direccionCliente!=''))
					<tr>
						<td><b>Dirección</b></td>
						<td>{{$tReciboVenta->direccionCliente}}</td>
					</tr>
				@endif
				<tr>
					<td><b>Fecha de emisión</b></td>
					<td>{{$tReciboVenta->fechaComprobanteEmitido}}</td>
				</tr>				
			</tbody>
		</table>
		<hr>
		<table style="width: 100%;">
			<thead>
				<tr>
					<th style="text-align: center;">C/U</th>
					<th>DESC./P.U.</th>
					<th style="text-align: center;">T.</th>
				</tr>
			</thead>
			<tbody>
				@foreach($tReciboVenta->treciboventadetalle as $value)
					<tr>
						<td style="border-bottom: 1px solid #eeeeee;text-align: center;">{{number_format($value->cantidadProducto, 3, '.', '')}}<br>{{$value->unidadMedidaProducto}}</td>
						<td style="border-bottom: 1px solid #eeeeee;text-align: left;"><div>{{$value->nombreProducto.' '.$value->informacionAdicionalProducto}}<br><b>P.U.:</b>{{$value->precioVentaUnitarioProducto}}</div></td>
						<td style="border-bottom: 1px solid #eeeeee;text-align: center;">{{number_format($value->precioVentaTotalProducto, 2)}}</td>
					</tr>
				@endforeach
				<tr>
					<td colspan="2" style="text-align: right;vertical-align: top;">
						<div style="border: 1px solid transparent;margin: 4px;">Total ({{$tReciboVenta->divisa!='Dólares' ? 'S/' : 'US$'}}):</div>
					</td>
					<td style="text-align: center;vertical-align: top;">
						<div style="border: 1px solid #999999;margin: 4px;">{{$tReciboVenta->total}}</div>
					</td>
				</tr>
			</tbody>
		</table>
		<br>
		<div style="border: 1px solid #999999;margin: 2px;padding: 5px;"><b>SON: </b>{{$valorTotalLetras}} {{$tReciboVenta->divisa!='Dólares' ? 'soles' : 'dólares americanos'}}</div>
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
						{!!$tEmpresa->toficina[0]->descripcionComercialComprobante!!}
					</td>
					<td style="width: 270px;">
                        <div style="border: 2px solid #4B89CC;">
                            <div style="padding: 5px;">&nbsp;</div>
							<div style="background-color: #f5f5f5;font-size: 15px;margin: 1px;padding: 7px;">PROFORMA</div>
							<div style="padding: 5px;">&nbsp;</div>
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
							{{$tReciboVenta->nombreCompletoCliente}}
						</fieldset>
                    </td>
                    <td style="padding-top: 5px;text-align: center;">
						<fieldset>
							<legend><b>Documento</b></legend>
							{{!empty($tReciboVenta->documentoCliente) ? $tReciboVenta->documentoCliente : '&nbsp;'}}
						</fieldset>
					</td>
				</tr>
				<tr>
					<td style="padding-top: 5px;width: 550px;">
						<fieldset>
							<legend><b>Dirección</b></legend>
							{{!empty($tReciboVenta->direccionCliente) ? $tReciboVenta->direccionCliente :  '&nbsp;'}}
						</fieldset>
						
					</td>
					<td style="padding-top: 5px;text-align: center;">
						<fieldset>
							<legend><b>Fecha de emisión</b></legend>
							{{substr($tReciboVenta->fechaComprobanteEmitido, 0, 10)}}
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
				@foreach($tReciboVenta->treciboventadetalle as $value)
					<tr>
						<td style="border-bottom: 1px solid #eeeeee;text-align: center;">{{number_format($value->cantidadProducto, 3, '.', '')}}</td>
						<td style="border-bottom: 1px solid #eeeeee;text-align: center;">{{$value->unidadMedidaProducto}}</td>
						<td style="border-bottom: 1px solid #eeeeee;text-align: left;">{{$value->nombreProducto.' '.$value->informacionAdicionalProducto}}</td>
						<td style="border-bottom: 1px solid #eeeeee;text-align: center;">{{$tReciboVenta->divisa!='Dólares' ? 'S/' : 'US$'}}{{$value->precioVentaUnitarioProducto}}</td>
						<td style="border-bottom: 1px solid #eeeeee;text-align: center;">{{$tReciboVenta->divisa!='Dólares' ? 'S/' : 'US$'}}{{number_format($value->precioVentaTotalProducto, 2)}}</td>
					</tr>
				@endforeach
				<tr>
					<td colspan="3" style="vertical-align: top;">
						<div style="border: 1px solid #999999;margin: 2px;padding: 5px;"><b>SON: </b>{{$valorTotalLetras}} {{$tReciboVenta->divisa!='Dólares' ? 'soles' : 'dólares americanos'}}</div>
					</td>
					<td style="text-align: right;vertical-align: top;">
						<div style="border: 1px solid transparent;margin: 4px;padding: 5px;">Total:</div>
					</td>
					<td style="text-align: center;vertical-align: top;">
						<div style="border: 1px solid #999999;margin: 4px;padding: 5px;">{{$tReciboVenta->divisa!='Dólares' ? 'S/' : 'US$'}}{{$tReciboVenta->total}}</div>
					</td>
				</tr>
			</tbody>
		</table>
	</body>
@endif
</html>