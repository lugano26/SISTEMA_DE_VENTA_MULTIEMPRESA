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
                        Traslado realizado de almacén a oficina
					</td>
					<td style="width: 270px;">
                        <div style="border: 2px solid #4B89CC;">
                            <div style="font-size: 15px;padding: 5px;">R.U.C. {{$tEmpresa->ruc}}</div>							
                            <div style="background-color: #f5f5f5;font-size: 15px;margin: 1px;padding: 7px;">COMPROBANTE DE TRASLADO</div>
                            <!-- <div style="font-size: 15px;padding: 5px;">&nbsp;</div> -->
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
							<legend><b>Almacén</b></legend>
							{{$tProductoEnviarStock->talmacen->descripcion}}
						</fieldset>
					</td>
					<td style="text-align: center;">
						<fieldset>
							<legend><b>Estado</b></legend>
							{{$tProductoEnviarStock->estado ? 'Conforme' : 'Anulado'}}
						</fieldset>
					</td>
				</tr>
				<tr>
					<td style="padding-top: 5px;width: 550px;">
						<fieldset>
							<legend><b>Oficina</b></legend>
							{{$tProductoEnviarStock->toficina->descripcion}}
						</fieldset>
						
					</td>
					<td style="padding-top: 5px;text-align: center;">
						<fieldset>
							<legend><b>Fecha de traslado</b></legend>
							{{substr($tProductoEnviarStock->created_at, 0, 10)}}
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
					<th style="border-bottom: 1px solid #000000;text-align: center;width: 100px;">P. COMPRA</th>
					<th style="border-bottom: 1px solid #000000;text-align: center;width: 120px;">P. VENTA</th>
				</tr>
			</thead>
			<tbody>
				@foreach($tProductoEnviarStock->tproductoenviarstockdetalle as $value)
					<tr>
						<td style="border-bottom: 1px solid #eeeeee;text-align: center;">{{number_format($value->cantidadProducto, 3, '.', '')}}</td>
						<td style="border-bottom: 1px solid #eeeeee;text-align: left;">{{$value->nombreProducto}}</td>
						<td style="border-bottom: 1px solid #eeeeee;text-align: center;">S/{{$value->precioCompraUnitarioProducto}}</td>
						<td style="border-bottom: 1px solid #eeeeee;text-align: center;">S/{{$value->precioVentaUnitarioProducto}}</td>
					</tr>
				@endforeach
				<tr>
					<td colspan="2" style="vertical-align: top;">
					</td>
					<td style="text-align: center;vertical-align: top;">
						 <div style="border: 1px solid #999999;margin: 4px;margin-top: 2px;padding: 5px;">S/{{{number_format($tProductoEnviarStock->tproductoenviarstockdetalle->sum('precioCompraUnitarioProducto'),2, '.', '')}}}</div>
					</td>
					<td style="text-align: center;vertical-align: top;">
                        <div style="border: 1px solid #999999;margin: 4px;margin-top: 2px;padding: 5px;">S/{{number_format($tProductoEnviarStock->tproductoenviarstockdetalle->sum('precioVentaUnitarioProducto'),2, '.', '')}}</div>
					</td>
				</tr>
			</tbody>
		</table>
		<br>
	</body>
</html>