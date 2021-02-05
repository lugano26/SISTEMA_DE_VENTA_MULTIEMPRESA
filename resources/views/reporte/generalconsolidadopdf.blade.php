<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Reporte consolidado General</title>
	<style>
		@page {
            margin-top: 5px !important;
        }
	</style>
</head>
<body>
	<table style="border-bottom: 2px solid #333;margin-top: 15px;width: 100%;">
		<tbody>
			<tr>
				<td style="width: 55px;">
					<img src="{{$base64Logo}}" width="50">
				</td>
				<td style="text-align: left; vertical-align: middle;">
					<h3 style="margin: 0;">{{$tEmpresa->razonSocial}}</h3>
					<div style="color: #525659;font-size: 12px;text-align: left;">
						<b>ALMACÉN:</b> 
						{{$tAlmacen != null ? $tAlmacen->descripcion : 'Todos'}} | 
						<b>OFICINA:</b> 
						{{$tOficina != null ? $tOficina->descripcion : 'Todos'}} | 
						<b>FECHA:</b> 
						{{$fechaInicial}}
					</div>
				</td>
			</tr>
		</tbody>
	</table>

	@php
	$egresos = 0;
	$ingresos = 0;
	$final = 0;
	$totalCompras = 0;
	$totalVentasfe = 0;
	$totalNotaCredito = 0;
	$totalNotaDebito = 0;
	$totalVentasWef = 0;
	$totalEgresos = 0;
	@endphp
	
	<br>
	<h1 style="background: #eee; text-align: center; font-size: 15px;">MOVIMIENTOS MONETARIOS</h1>	
	<h3 style="font-size: 15px; border-bottom: 1px solid #ccc;">1. Compras</h3>
	<table style="width: 100%; font-size: 13px;">
		<tbody>
			<tr>
				<td style="background: #eee">Almacén</td>
				<td style="background: #eee">Documento</td>
				<td style="background: #eee">Proveedor</td>
				<td style="background: #eee">Pago</td>
				<td style="background: #eee">F. de registro</td>
				<td style="background: #eee">Estado</td>
				<td style="background: #eee; text-align: center;">Total</td>
			</tr>
			
			@foreach ($listaTReciboCompra as $compra )
			<tr>
				<td>{{$compra->talmacen->descripcion}}</td>
				<td style="min-width: 100px;"><b>{{$compra->numeroRecibo}}</b></td>
				<td>{{$compra->tproveedor->nombre}}</td>
				<td>{{$compra->tipoPago}}</td>
				<td>{{explode(' ', $compra->created_at)[0]}}</td>
				<td style="color: {{$compra->estado ? 'green' : 'red'}}">{{$compra->estado ? 'Conforme' : 'Anulado'}}</td>
				<td style="text-align: right;">S/{{number_format($compra->total, 2, '.' , ' ')}}</td>
			</tr>
			@endforeach
			<tr>
				<td colspan="6"></td>
				@php
				$totalCompras = $listaTReciboCompra->sum('total');
				@endphp
				<td style="text-align: right; font-weight: bold; border-top: 1px solid #ccc;">S/{{number_format($totalCompras, 2, '.', ' ')}}</td>
			</tr>
		</tbody>
	</table>

	<h3 style="font-size: 15px; border-bottom: 1px solid #ccc;">1.1. Pagos de compras al crédito &nbsp;<span style="color: #cc0000">(No aplica para cálculos monetarios)</span></h3>
	<table style="width: 100%; font-size: 13px;">
		<tbody>
			<tr>
				<td style="background: #eee">Documento compra</td>
				<td style="background: #eee">Descripción</td>
				<td style="background: #eee">F. de pago</td>
				<td style="background: #eee; text-align: center;">Monto</td>
			</tr>
			
			@foreach ($listaTReciboCompraPago as $value )
			<tr>
				<td style="min-width: 100px;"><b>{{$value->trecibocompra->numeroRecibo}}</b></td>
				<td>{{$value->descripcion}}</td>
				<td>{{explode(' ', $value->created_at)[0]}}</td>
				<td style="text-align: right;">S/{{number_format($value->monto, 2, '.' , ' ')}}</td>
			</tr>
			@endforeach
			<tr>
				<td colspan="3"></td>
				<td style="text-align: right; font-weight: bold; border-top: 1px solid #ccc;">S/{{number_format($listaTReciboCompraPago->sum('monto'), 2, '.', ' ')}}</td>
			</tr>
		</tbody>
	</table>

	<h3 style="font-size: 15px; border-bottom: 1px solid #ccc;">2. Egresos</h3>
	<table style="width: 100%; font-size: 13px;">
		<tbody>
			<tr>
				<td style="background: #eee">Oficina</td>
				<td style="background: #eee">Personal</td>
				<td style="background: #eee">Descripción</td>
				<td style="background: #eee">Fecha</td>
				<td style="background: #eee; text-align: center;">Total</td>
			</tr>
			@foreach ($listaTEgreso as $egreso )
			<tr>
				<td>{{$egreso->toficina->descripcion}}</td>
				<td>{{explode("@", $egreso->tpersonal->correoElectronico)[0]}}</td>
				<td>{{$egreso->descripcion}}</td>
				<td>{{$egreso->created_at}}</td>
				<td style="text-align: right;">S/{{number_format($egreso->monto, 2, '.', ' ')}}</td>
			</tr>
			@endforeach
			<tr>
				<td colspan="4"></td>
				@php
				$totalEgresos = $listaTEgreso->sum('monto');
				@endphp
				<td style="text-align: right; font-weight: bold; border-top: 1px solid #ccc;">S/{{number_format($totalEgresos, 2, '.', ' ')}}</td>
			</tr>
		</tbody>
	</table>

	<h3 style="font-size: 15px; border-bottom: 1px solid #ccc;">3. Ventas (Con facturación electrónica)</h3>
	<table style="width: 100%; font-size: 13px;">
		<tbody>
			<tr>
				<td style="background: #eee">Oficina</td>
				<td style="background: #eee">Documento</td>
				<td style="background: #eee">Cliente</td>
				<td style="background: #eee">Pago</td>
				<td style="background: #eee">F. de registro</td>
				<td style="background: #eee">Estado</td>
				<td style="background: #eee; text-align: center;">Total</td>
			</tr>
			@foreach ($listaTReciboVenta as $value)
			<tr>
				<td>{{$value->toficina->descripcion}}</td>
				<td style="font-weight: bold;">{{$value->numeroRecibo}}</td>
				<td>{{$value->nombreCompletoCliente}}</td>
				<td>{{$value->tipoPago}}</td>
				<td>{{explode(' ', $value->created_at)[0]}}</td>
				<td style="color: {{$value->estado ? 'green' : 'red'}}">{{$value->estado ? 'Conforme' : 'Anulado'}}</td>
				<td style="text-align: right;">{{$value->divisa == 'Soles' ? 'S/' : 'US$'}}{{number_format($value->total, 2, '.', ' ')}}</td>
			</tr>
			@endforeach
			<tr>
				<td colspan="6"></td>
				@php
				$totalVentasfe = $listaTReciboVenta->map(function($item, $key) {return $item->total * ($item->divisa == 'Soles' ? 1 : $item->tipoCambioUsd);})->sum();
				@endphp
				<td style="text-align: right; font-weight: bold; border-top: 1px solid #ccc;">S/{{number_format($totalVentasfe, 2, '.', ' ')}}</td>
			</tr>
		</tbody>
	</table>

	<h3 style="font-size: 15px; border-bottom: 1px solid #ccc;">3.1. Notas de crédito</h3>
	<table style="width: 100%; font-size: 13px;">
		<tbody>
			<tr>
				<td style="background: #eee">Oficina</td>
				<td style="background: #eee">Documento</td>
				<td style="background: #eee">Venta FE</td>
				<td style="background: #eee">Descripción</td>
				<td style="background: #eee">F. de registro</td>
				<td style="background: #eee; text-align: center;">Total</td>
			</tr>
			@foreach ($listaTReciboVentaNotaCredito as $value )
			<tr>
				<td>{{$value->toficina->descripcion}}</td>
				<td style="font-weight: bold;">{{$value->numeroRecibo}}</td>
				<td>{{$value->treciboventa->numeroRecibo}}</td>
				<td>{{$value->descripcionMotivo}}</td>
				<td>{{explode(' ', $value->created_at)[0]}}</td>
				<td style="text-align: right;">{{$value->treciboventa->divisa == 'Soles' ? 'S/' : 'US$'}}{{number_format($value->total, 2, '.', ' ')}}</td>
			</tr>
			@endforeach
			<tr>
				<td colspan="5"></td>
				@php
				$totalNotaCredito = $listaTReciboVentaNotaCredito->map(function($item, $key) {return $item->total * ($item->treciboventa->divisa == 'Soles' ? 1 : $item->treciboventa->tipoCambioUsd);})->sum();
				@endphp
				<td style="text-align: right; font-weight: bold; border-top: 1px solid #ccc;">S/{{number_format($totalNotaCredito, 2, '.', ' ')}}</td>
			</tr>
		</tbody>
	</table>

	<h3 style="font-size: 15px; border-bottom: 1px solid #ccc;">3.2. Notas de débito </h3>
	<table style="width: 100%; font-size: 13px;">
		<tbody>
			<tr>
				<td style="background: #eee">Oficina</td>
				<td style="background: #eee">Documento</td>
				<td style="background: #eee">Venta FE</td>
				<td style="background: #eee">Descripción</td>
				<td style="background: #eee">F. de registro</td>
				<td style="background: #eee; text-align: center;">Total</td>
			</tr>
			@foreach ($listaTReciboVentaNotaDebito as $value )
			<tr>
				<td>{{$value->toficina->descripcion}}</td>
				<td style="font-weight: bold;">{{$value->numeroRecibo}}</td>
				<td>{{$value->treciboventa->numeroRecibo}}</td>
				<td>{{$value->descripcionMotivo}}</td>
				<td>{{explode(' ', $value->created_at)[0]}}</td>
				<td style="text-align: right;">{{$value->treciboventa->divisa == 'Soles' ? 'S/' : 'US$'}}{{number_format($value->total, 2, '.', ' ')}}</td>
			</tr>
			@endforeach
			<tr>
				<td colspan="5"></td>
				@php
				$totalNotaDebito = $listaTReciboVentaNotaDebito->map(function($item, $key) {return $item->total * ($item->treciboventa->divisa == 'Soles' ? 1 : $item->treciboventa->tipoCambioUsd);})->sum();
				@endphp
				<td style="text-align: right; font-weight: bold; border-top: 1px solid #ccc;">S/{{number_format($totalNotaDebito, 2, '.', ' ')}}</td>
			</tr>
		</tbody>
	</table>

	<h3 style="font-size: 15px; border-bottom: 1px solid #ccc;">3.2. Pagos de ventas FE &nbsp;<span style="color: #cc0000">(No aplica para cálculos monetarios)</span></h3>
	<table style="width: 100%; font-size: 13px;">
		<tbody>
			<tr>
				<td style="background: #eee">Documento Venta FE</td>
				<td style="background: #eee">Descripción</td>
				<td style="background: #eee">F. de pago</td>
				<td style="background: #eee; text-align: center;">Monto</td>
			</tr>
			
			@foreach ($listaTReciboVentaPago as $value )
			<tr>
				<td style="min-width: 100px;"><b>{{$value->treciboventa->numeroRecibo}}</b></td>
				<td>{{$value->descripcion}}</td>
				<td>{{explode(' ', $value->created_at)[0]}}</td>
				<td style="text-align: right;">S/{{number_format($value->monto, 2, '.' , ' ')}}</td>
			</tr>
			@endforeach
			<tr>
				<td colspan="3"></td>
				<td style="text-align: right; font-weight: bold; border-top: 1px solid #ccc;">S/{{number_format($listaTReciboVentaPago->sum('monto'), 2, '.', ' ')}}</td>
			</tr>
		</tbody>
	</table>

	<h3 style="font-size: 15px; border-bottom: 1px solid #ccc;">4. Ventas (Sin facturación electrónica)</h3>
	<table style="width: 100%; font-size: 13px;">
		<tbody>
			<tr>
				<td style="background: #eee">Oficina</td>
				<td style="background: #eee">Documento</td>
				<td style="background: #eee">Cliente</td>
				<td style="background: #eee">Pago</td>
				<td style="background: #eee">F. de registro</td>
				<td style="background: #eee">Estado</td>
				<td style="background: #eee; text-align: center;">Total</td>
			</tr>
			@foreach ($listaTReciboVentaOutEf as $value )
			<tr>
				<td>{{$value->toficina->descripcion}}</td>
				<td style="font-weight: bold;">{{$value->numeroRecibo}}</td>
				<td>{{$value->nombreCompletoCliente}}</td>
				<td>{{$value->tipoPago}}</td>
				<td>{{explode(' ', $value->created_at)[0]}}</td>
				<td style="color: {{$value->estado ? 'green' : 'red'}}">{{$value->estado ? 'Conforme' : 'Anulado'}}</td>
				<td style="text-align: right;">S/{{number_format($value->total, 2, '.', ' ')}}</td>
			</tr>
			@endforeach
			<tr>
				<td colspan="6"></td>
				@php
				$totalVentasWef = $listaTReciboVentaOutEf->sum('total');
				@endphp
				<td style="text-align: right; font-weight: bold; border-top: 1px solid #ccc;">S/{{number_format($totalVentasWef, 2, '.', ' ')}}</td>
			</tr>
		</tbody>
	</table>

	<h3 style="font-size: 15px; border-bottom: 1px solid #ccc;">4.1. Pagos de ventas WEF &nbsp;<span style="color: #cc0000">(No aplica para cálculos monetarios)</span></h3>
	<table style="width: 100%; font-size: 13px;">
		<tbody>
			<tr>
				<td style="background: #eee">Documento Venta WEF</td>
				<td style="background: #eee">Descripción</td>
				<td style="background: #eee">F. de pago</td>
				<td style="background: #eee; text-align: center;">Monto</td>
			</tr>
			
			@foreach ($listaTReciboVentaPagoOutEf as $value )
			<tr>
				<td style="min-width: 100px;"><b>{{$value->treciboventaoutef->numeroRecibo}}</b></td>
				<td>{{$value->descripcion}}</td>
				<td>{{explode(' ', $value->created_at)[0]}}</td>
				<td style="text-align: right;">S/{{number_format($value->monto, 2, '.' , ' ')}}</td>
			</tr>
			@endforeach
			<tr>
				<td colspan="3"></td>
				<td style="text-align: right; font-weight: bold; border-top: 1px solid #ccc;">S/{{number_format($listaTReciboVentaPagoOutEf->sum('monto'), 2, '.', ' ')}}</td>
			</tr>
		</tbody>
	</table>
	
	<br>
	<h1 style="background: #eee; text-align: center; font-size: 15px;">TRASLADOS Y RETIROS</h1>

	<h3 style="font-size: 15px; border-bottom: 1px solid #ccc;">1. Traslados de almacén a oficina</h3>
	<table style="width: 100%; font-size: 13px;">
		<tbody>
			<tr>
				<td style="background: #eee">Almacén</td>
				<td style="background: #eee">Oficina</td>
				<td style="background: #eee">Cod. Barras</td>
				<td style="background: #eee">Producto</td>
				<td style="background: #eee">F. Traslado</td>
				<td style="background: #eee">Cantidad</td>
				<td style="background: #eee; text-align: center;">P. Compra</td>
				<td style="background: #eee; text-align: center;">P. Venta</td>
			</tr>
			
			@foreach ($listaTProductoEnviarStock as $traslado)
			@foreach ($traslado->tproductoenviarstockdetalle as $value)
				<tr>
					<td>{{$traslado->talmacen->descripcion}}</td>
					<td>{{$traslado->toficina->descripcion}}</td>
					<td>{{$value->codigoBarrasProducto}}</td>
					<td>{{$value->nombreProducto}}</td>
					<td>{{explode(' ', $value->created_at)[0]}}</td>
					<td style="text-align: right;">{{$value->cantidadProducto}}</td>
					<td style="text-align: right;">S/{{number_format($value->precioCompraUnitarioProducto, 2, '.' , ' ')}}</td>
					<td style="text-align: right;">S/{{number_format($value->precioVentaUnitarioProducto, 2, '.' , ' ')}}</td>
				</tr>
			@endforeach
			@endforeach
		</tbody>
	</table>

	<h3 style="font-size: 15px; border-bottom: 1px solid #ccc;">2. Traslados entre oficinas</h3>
	<table style="width: 100%; font-size: 13px;">
		<tbody>
			<tr>
				<td style="background: #eee">O. Partida</td>
				<td style="background: #eee">O. Llegada</td>
				<td style="background: #eee">Cod. Barras</td>
				<td style="background: #eee">Producto</td>
				<td style="background: #eee">F. Traslado</td>
				<td style="background: #eee">Cantidad</td>
				<td style="background: #eee; text-align: center;">P. Compra</td>
				<td style="background: #eee; text-align: center;">P. Venta</td>
			</tr>
			
			@foreach ($listaTProductoTrasladoOficina as $traslado)
			@foreach ($traslado->tproductotrasladooficinadetalle as $value)
				<tr>
					<td>{{$traslado->toficina->descripcion}}</td>
					<td>{{$traslado->toficinallegada->descripcion}}</td>
					<td>{{$value->codigoBarrasProducto}}</td>
					<td>{{$value->nombreProducto}}</td>
					<td>{{explode(' ', $value->created_at)[0]}}</td>
					<td style="text-align: right;">{{$value->cantidadProducto}}</td>
					<td style="text-align: right;">S/{{number_format($value->precioCompraUnitarioProducto, 2, '.' , ' ')}}</td>
					<td style="text-align: right;">S/{{number_format($value->precioVentaUnitarioProducto, 2, '.' , ' ')}}</td>
				</tr>
			@endforeach
			@endforeach
		</tbody>
	</table>

	<h3 style="font-size: 15px; border-bottom: 1px solid #ccc;">3. Retiro de productos de oficinas &nbsp;<span style="color: #cc0000">(No aplica para cálculos monetarios)</span></h3>
	<table style="width: 100%; font-size: 13px;">
		<tbody>
			<tr>
				<td style="background: #eee">Oficina</td>
				<td style="background: #eee">Cod. Barras</td>
				<td style="background: #eee">Producto</td>
				<td style="background: #eee">Descripción</td>
				<td style="background: #eee">F. Retiro</td>
				<td style="background: #eee">Cantidad</td>
				<td style="background: #eee; text-align: center;">Monto perdido</td>
			</tr>
			
			@foreach ($listaTOficinaProductoRetiro as $value)
				<tr>
					<td>{{$value->toficina->descripcion}}</td>
					<td>{{$value->toficinaproducto->codigoBarras}}</td>
					<td>{{$value->toficinaproducto->nombre}}</td>
					<td>{{$value->descripcion}}</td>
					<td>{{explode(' ', $value->created_at)[0]}}</td>
					<td style="text-align: right;">{{$value->cantidadUnidad}}</td>
					<td style="text-align: right;">S/{{number_format($value->montoPerdido, 2, '.' , ' ')}}</td>
				</tr>
			@endforeach
			<tr>
				<td colspan="6"></td>
				<td style="text-align: right; font-weight: bold; border-top: 1px solid #ccc;">S/{{number_format($listaTOficinaProductoRetiro->sum('montoPerdido'), 2, '.', ' ')}}</td>
			</tr>
		</tbody>
	</table>

	<h3 style="font-size: 15px; border-bottom: 1px solid #ccc;">3. Retiro de productos de almacén &nbsp;<span style="color: #cc0000">(No aplica para cálculos monetarios)</span></h3>
	<table style="width: 100%; font-size: 13px;">
		<tbody>
			<tr>
				<td style="background: #eee">Almacén</td>
				<td style="background: #eee">Cod. Barras</td>
				<td style="background: #eee">Producto</td>
				<td style="background: #eee">Descripción</td>
				<td style="background: #eee">F. Retiro</td>
				<td style="background: #eee">Cantidad</td>
				<td style="background: #eee; text-align: center;">Monto perdido</td>
			</tr>
			
			@foreach ($litaTAlmacenProductoRetiro as $value)
				<tr>
					<td>{{$value->talmacen->descripcion}}</td>
					<td>{{$value->talmacenproducto->codigoBarras}}</td>
					<td>{{$value->talmacenproducto->nombre}}</td>
					<td>{{$value->descripcion}}</td>
					<td>{{explode(' ', $value->created_at)[0]}}</td>
					<td style="text-align: right;">{{$value->cantidadUnidad}}</td>
					<td style="text-align: right;">S/{{number_format($value->montoPerdido, 2, '.' , ' ')}}</td>
				</tr>
			@endforeach
			<tr>
				<td colspan="6"></td>
				<td style="text-align: right; font-weight: bold; border-top: 1px solid #ccc;">S/{{number_format($litaTAlmacenProductoRetiro->sum('montoPerdido'), 2, '.', ' ')}}</td>
			</tr>
		</tbody>
	</table>

	<br>
	<h1 style="background: #eee; text-align: center; font-size: 15px;">CAJA</h1>
	<table style="width: 100%; font-size: 13px;">
		<tbody>
			<tr>
				<td style="background: #eee">Personal</td>
				<td style="background: #eee; text-align: center;">Fecha</td>
				<td style="background: #eee; text-align: center;">Egresos</td>
				<td style="background: #eee; text-align: center;">Ingresos</td>
				<td style="background: #eee; text-align: center;">Saldo final</td>
			</tr>

			@foreach ($listaTCaja as $caja)				
				@foreach($caja->tcajadetalle as $value)
				@if($value->egresos != 0 || $value->ingresos != 0)
				<tr>
					<td>{{explode("@", $value->tpersonal->correoElectronico)[0]}}</td>
					<td style="text-align: center">{{explode(' ', $value->created_at)[0]}}</td>
					<td style="text-align: right;">S/{{number_format($value->egresos, 2, '.', ' ')}}</td>
					<td style="text-align: right;">S/{{number_format($value->ingresos, 2, '.', ' ')}}</td>
					<td style="text-align: right;">S/{{number_format($value->saldoFinal, 2, '.', ' ')}}</td>					
				</tr>
				@php
					$egresos += $value->egresos;
					$ingresos += $value->ingresos;
					$final += $value->saldoFinal;
				@endphp
				@endif
				@endforeach				
			@endforeach
			<tr>
				<td colspan="2"></td>
				<td style="text-align: right; font-weight: bold; border-top: 1px solid #ccc;">S/{{number_format($egresos, 2, '.', ' ')}}</td>
				<td style="text-align: right; font-weight: bold; border-top: 1px solid #ccc;">S/{{number_format($ingresos, 2, '.', ' ')}}</td>
				<td style="text-align: right; font-weight: bold; border-top: 1px solid #ccc;">S/{{number_format($final, 2, '.', ' ')}}</td>
			</tr>
		</tbody>
	</table>

	<br>
	<h1 style="background: #eee; text-align: center; font-size: 15px;">RESUMEN</h1>
	<table style="width: 100%; font-size: 13px; border-collapse: collapse;">
		<tbody>
			<tr>
				<td style="text-align: center">(+)</td>
				<td><b>Ventas FE</b></td>
				<td style="text-align: right;">S/{{number_format($totalVentasfe, 2, '.', ' ')}}</td>
			</tr>
			<tr>
				<td style="text-align: center">(+)</td>
				<td><b>Notas de débito</b></td>
				<td style="text-align: right;">S/{{number_format($totalNotaDebito, 2, '.', ' ')}}</td>
			</tr>
			<tr>
				<td style="text-align: center;">(+)</td>
				<td><b>Ventas WEF</b></td>
				<td style="text-align: right;">S/{{number_format($totalVentasWef, 2, '.', ' ')}}</td>
			</tr>
			<tr>
				<td style="text-align: center">(-)</td>
				<td><b>Compras</b></td>
				<td style="text-align: right;">S/{{number_format($totalCompras, 2, '.', ' ')}}</td>
			</tr>
			<tr>
				<td style="text-align: center">(-)</td>
				<td><b>Egresos</b></td>
				<td style="text-align: right;">S/{{number_format($totalEgresos, 2, '.', ' ')}}</td>
			</tr>			
			<tr>
				<td style="text-align: center; padding-bottom: 15px;">(-)</td>
				<td style="padding-bottom: 15px;"><b>Notas de crédito</b></td>
				<td style="text-align: right; padding-bottom: 15px;">S/{{number_format($totalNotaCredito, 2, '.', ' ')}}</td>
			</tr>
			<tr>
				<td style="border-top: 1px solid #ccc;" colspan="2"><b>Total calculado</b></td>
				<td colspan="1" style="font-weight: bold; text-align: right; background: #eee;border-top: 1px solid #ccc;">S/{{number_format(($totalVentasfe + $totalVentasWef + $totalNotaDebito) - ($totalCompras + $totalEgresos + $totalNotaCredito), 2, '.', ' ')}}</td>
			</tr>
			<tr>
				<td style="border-top: 1px dashed #ccc;" colspan="2"><b>Verificación con caja</b></td>
				<td colspan="1" style="font-weight: bold; text-align: right; background: #eee;border-top: 1px dashed #ccc;">S/{{number_format($final, 2, '.', ' ')}}</td>
			</tr>
		</tbody>
	</table>
</body>
</html>