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
	<body style="font-family: 'Calibri (Body)';font-size: 16px;padding-left: 33px;">
		<div style="text-align: center;">*** RECIBO - B : {{$tReciboVenta->numeroRecibo}} ***</div>
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
				@if(trim($tReciboVenta->documentoCliente!='00000000'))
					<tr>
						<td><b>D.N.I.</b></td>
						<td>{{$tReciboVenta->documentoCliente}}</td>
					</tr>
				@endif
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
				<tr>
					<td><b>Categoría de venta</b></td>
					<td>{{$genericHelper->obtenerRamaCategoriaVenta($tReciboVenta->tcategoriaventa, ' > ')}}</td>
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
				@foreach($tReciboVenta->treciboventadetalleoutef as $value)
					<tr>
						<td style="border-bottom: 1px solid #eeeeee;text-align: center;">{{number_format($value->cantidadProducto, 3, '.', '')}}<br>{{$value->unidadMedidaProducto}}</td>
						<td style="border-bottom: 1px solid #eeeeee;text-align: left;"><div>{{$value->nombreProducto.' '.$value->informacionAdicionalProducto}}<br><b>P.U.:</b>{{$value->precioVentaUnitarioProducto}}</div></td>
						<td style="border-bottom: 1px solid #eeeeee;text-align: center;">{{number_format($value->precioVentaTotalProducto, 2)}}</td>
					</tr>
				@endforeach
				<tr>
					<td colspan="2" style="text-align: right;vertical-align: top;">
						<div style="border: 1px solid transparent;margin: 4px;margin-top: 2px;">S.T. (S/):</div>
						<div style="border: 1px solid transparent;margin: 4px;">I.G.V. (S/):</div>
						{{-- <div style="border: 1px solid transparent;margin: 4px;">I.S.C. (S/):</div> --}}
						<div style="border: 1px solid transparent;margin: 4px;">Total (S/):</div>
					</td>
					<td style="text-align: center;vertical-align: top;">
						<div style="border: 1px solid #999999;margin: 4px;margin-top: 2px;">{{$tReciboVenta->subTotal}}</div>
						<div style="border: 1px solid #999999;margin: 4px;">{{$tReciboVenta->igv}}</div>
						{{-- <div style="border: 1px solid #999999;margin: 4px;">{{$tReciboVenta->isc}}</div> --}}
						<div style="border: 1px solid #999999;margin: 4px;">{{$tReciboVenta->total}}</div>
					</td>
				</tr>
			</tbody>
		</table>
		<br>
		<div style="border: 1px solid #999999;margin: 2px;padding: 5px;"><b>SON: </b>{{$valorTotalLetras}} soles</div>
		<div style="text-align: center;">(Por facturar)</div>
		<br>
		<div style="text-align: center;"><b>Vendedor</b></div>
		<div style="text-align: center;">{{$tReciboVenta->tpersonal->nombre.' '.$tReciboVenta->tpersonal->apellido}} ({{$tReciboVenta->tpersonal->tusuario->nombreUsuario}})</div>
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
							<div style="font-size: 15px;padding: 5px;">R.U.C. {{$tEmpresa->ruc}}</div>
							<div style="background-color: #f5f5f5;font-size: 15px;margin: 1px;padding: 7px;">RECIBO - B</div>
							<div style="font-size: 15px;padding: 5px;">{{$tReciboVenta->numeroRecibo}}</div>
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
					<td style="text-align: center;">
						<fieldset>
							<legend><b>D.N.I.</b></legend>
							{{$tReciboVenta->documentoCliente}}
						</fieldset>
					</td>
				</tr>
				<tr>
					<td style="padding-top: 5px;width: 550px;">
						<fieldset>
							<legend><b>Dirección</b></legend>
							{{$tReciboVenta->direccionCliente}}
						</fieldset>
					</td>
					<td style="padding-top: 5px;text-align: center;">
						<fieldset>
							<legend><b>Fecha de emisión</b></legend>
							{{substr($tReciboVenta->fechaComprobanteEmitido, 0, 10)}}
						</fieldset>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="padding-top: 5px;">
						<fieldset>
							<legend><b>Categoría de venta</b></legend>
							{{$genericHelper->obtenerRamaCategoriaVenta($tReciboVenta->tcategoriaventa, ' > ')}}
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
				@foreach($tReciboVenta->treciboventadetalleoutef as $value)
					<tr>
						<td style="border-bottom: 1px solid #eeeeee;text-align: center;">{{number_format($value->cantidadProducto, 3, '.', '')}}</td>
						<td style="border-bottom: 1px solid #eeeeee;text-align: center;">{{$value->unidadMedidaProducto}}</td>
						<td style="border-bottom: 1px solid #eeeeee;text-align: left;">{{$value->nombreProducto.' '.$value->informacionAdicionalProducto}}</td>
						<td style="border-bottom: 1px solid #eeeeee;text-align: center;">S/{{$value->precioVentaUnitarioProducto}}</td>
						<td style="border-bottom: 1px solid #eeeeee;text-align: center;">S/{{number_format($value->precioVentaTotalProducto, 2)}}</td>
					</tr>
				@endforeach
				<tr>
					<td colspan="3" style="vertical-align: top;">
						<div style="border: 1px solid #999999;margin: 2px;padding: 5px;"><b>SON: </b>{{$valorTotalLetras}} soles</div>
					</td>
					<td style="text-align: right;vertical-align: top;">
						<div style="border: 1px solid transparent;margin: 4px;margin-top: 2px;padding: 5px;">Sub total:</div>
						<div style="border: 1px solid transparent;margin: 4px;padding: 5px;">I.G.V.:</div>
						{{-- <div style="border: 1px solid transparent;margin: 4px;padding: 5px;">I.S.C.:</div> --}}
						<div style="border: 1px solid transparent;margin: 4px;padding: 5px;">Total:</div>
					</td>
					<td style="text-align: center;vertical-align: top;">
						<div style="border: 1px solid #999999;margin: 4px;margin-top: 2px;padding: 5px;">S/{{$tReciboVenta->subTotal}}</div>
						<div style="border: 1px solid #999999;margin: 4px;padding: 5px;">S/{{$tReciboVenta->igv}}</div>
						{{-- <div style="border: 1px solid #999999;margin: 4px;padding: 5px;">S/{{$tReciboVenta->isc}}</div> --}}
						<div style="border: 1px solid #999999;margin: 4px;padding: 5px;">S/{{$tReciboVenta->total}}</div>
					</td>
				</tr>
			</tbody>
		</table>
		<br>
		<div style="font-style: italic;text-align: right;">(Por facturar) - <b>Vendedor:</b> {{$tReciboVenta->tpersonal->nombre.' '.$tReciboVenta->tpersonal->apellido}} ({{$tReciboVenta->tpersonal->tusuario->nombreUsuario}})</div>
	</body>
@endif
</html>