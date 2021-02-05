'use strict';

function onKeyUpTdCantidadProducto(element)
{
	$(element).parent().find('input[name^=hdCantidadProducto]').val($(element).text());
	
	calcularPreciosItem($(element).parent(), true, $(element).parent().find('input[name^=hdPorcentajeTributacionProducto]').val());

	calcularPreciosTotales('tableProductoNotaCredito');
}

function onKeyUpTdPrecioVentaTotalProducto(element)
{
	$(element).parent().find('input[name^=hdPrecioVentaTotalProducto]').val((!isNaN($(element).text()) ? parseFloat($(element).text()).toFixed(2) : $(element).text()));

	calcularPreciosItem($(element).parent(), false, $(element).parent().find('input[name^=hdPorcentajeTributacionProducto]').val());

	calcularPreciosTotales('tableProductoNotaCredito');
}

function onBlurTdPrecioVentaTotalProducto(element)
{
	$(element).text((!isNaN($(element).text()) ? parseFloat($(element).text()).toFixed(2) : $(element).text()));
	
	calcularPreciosItem($(element).parent(), false, $(element).parent().find('input[name^=hdPorcentajeTributacionProducto]').val());

	calcularPreciosTotales('tableProductoNotaCredito');
}

function agregarProductoNotaCredito(codigoOficinaProducto, codigoBarrasProducto, nombreProducto, informacionAdicionalProducto, tipoProducto, situacionImpuestoProducto, tipoImpuestoProducto, porcentajeTributacionProducto, presentacionProducto, unidadMedidaProducto, precioVentaUnitarioProducto, cantidadProducto, subTotalProducto, impuestoAplicadoProducto, precioVentaTotalProducto)
{
	var htmlTemp='<tr>'
		+'<td style="display: none;">'
			+'<input type="hidden" name="hdCodigoOficinaProducto[]" value="'+codigoOficinaProducto+'">'
			+'<input type="hidden" name="hdCodigoBarrasProducto[]" value="'+codigoBarrasProducto.viiInjectionEscape()+'">'
			+'<input type="hidden" name="hdNombreProducto[]" value="'+nombreProducto.trim().viiInjectionEscape()+'">'
			+'<input type="hidden" name="hdInformacionAdicionalProducto[]" value="'+informacionAdicionalProducto.trim().viiInjectionEscape()+'">'
			+'<input type="hidden" name="hdTipoProducto[]" value="'+tipoProducto.viiInjectionEscape()+'">'
			+'<input type="hidden" name="hdSituacionImpuestoProducto[]" value="'+situacionImpuestoProducto.viiInjectionEscape()+'">'
			+'<input type="hidden" name="hdTipoImpuestoProducto[]" value="'+tipoImpuestoProducto.viiInjectionEscape()+'">'
			+'<input type="hidden" name="hdPorcentajeTributacionProducto[]" value="'+porcentajeTributacionProducto+'">'
			+'<input type="hidden" name="hdPresentacionProducto[]" value="'+presentacionProducto.viiInjectionEscape()+'">'
			+'<input type="hidden" name="hdUnidadMedidaProducto[]" value="'+unidadMedidaProducto.viiInjectionEscape()+'">'
			+'<input type="hidden" name="hdPrecioVentaUnitarioProducto[]" value="'+precioVentaUnitarioProducto+'">'
			+'<input type="hidden" name="hdCantidadProducto[]" value="'+cantidadProducto+'">'
			+'<input type="hidden" name="hdSubTotalProducto[]" value="'+subTotalProducto+'">'
			+'<input type="hidden" name="hdImpuestoAplicadoProducto[]" value="'+impuestoAplicadoProducto+'">'
			+'<input type="hidden" name="hdPrecioVentaTotalProducto[]" value="'+precioVentaTotalProducto+'">'
		+'</td>'
		+'<td class="text-center"><span class="'+(codigoOficinaProducto>='900000000000001' ? 'fa fa-circle-o' : 'fa fa-tag')+'"></span></td>'
		+'<td class="text-left">'+(nombreProducto+' '+informacionAdicionalProducto).trim().viiInjectionEscape()+'</td>'
		+'<td class="tdCantidadProducto text-center tdEditable" contenteditable onkeyup="onKeyUpTdCantidadProducto(this);">'+cantidadProducto+'</td>'
		+'<td class="tdPrecioVentaTotalProducto text-center tdEditable" contenteditable onkeyup="onKeyUpTdPrecioVentaTotalProducto(this);" onblur="onBlurTdPrecioVentaTotalProducto(this);">'+precioVentaTotalProducto+'</td>'
		+'<td class="text-right">'
			+'<span class="btn btn-default btn-xs glyphicon glyphicon-trash" data-toggle="tooltip" data-placement="left" title="Quitar de la tabla" onclick="quitarProducto(this);" style="margin: 1px;"></span>'
		+'</td>'
	+'</tr>';

	$('#tableProductoNotaCredito > tbody > tr:last').before(htmlTemp);

	calcularPreciosItem($('#tableProductoNotaCredito > tbody > tr')[$('#tableProductoNotaCredito > tbody > tr').length-2], false, porcentajeTributacionProducto);

	calcularPreciosTotales('tableProductoNotaCredito');

	$('[data-toggle="tooltip"]').tooltip();
}

function moverProducto(element, mostrarMensaje)
{
	var existeProducto=false;

	var nombreProducto=$(element).find('input[name^=hdNombreProducto]').val().viiInjectionEscape();

	$('#tableProductoNotaCredito > tbody > tr').each(function(index, element)
	{
		if($(element).find('input[name^=hdNombreProducto]').length)
		{
			var nombreProductoTemp=$(element).find('input[name^=hdNombreProducto]').val().viiInjectionEscape();

			if(nombreProducto.viiReplaceAll(' ', '')==nombreProductoTemp.viiReplaceAll(' ', ''))
			{
				existeProducto=true;

				return false;
			}
		}
	});

	if(existeProducto)
	{
		if(mostrarMensaje)
		{
			notaError('No se pudo proceder', 'El producto ya existe en la lista.');
		}

		return;
	}

	agregarProductoNotaCredito(
		$(element).find('input[name^=hdCodigoOficinaProducto]').val(),
		$(element).find('input[name^=hdCodigoBarrasProducto]').val(),
		$(element).find('input[name^=hdNombreProducto]').val(),
		$(element).find('input[name^=hdInformacionAdicionalProducto]').val(),
		$(element).find('input[name^=hdTipoProducto]').val(),
		$(element).find('input[name^=hdSituacionImpuestoProducto]').val(),
		$(element).find('input[name^=hdTipoImpuestoProducto]').val(),
		$(element).find('input[name^=hdPorcentajeTributacionProducto]').val(),
		$(element).find('input[name^=hdPresentacionProducto]').val(),
		$(element).find('input[name^=hdUnidadMedidaProducto]').val(),
		$(element).find('input[name^=hdPrecioVentaUnitarioProducto]').val(),
		$(element).find('input[name^=hdCantidadProducto]').val(),
		$(element).find('input[name^=hdSubTotalProducto]').val(),
		$(element).find('input[name^=hdImpuestoAplicadoProducto]').val(),
		$(element).find('input[name^=hdPrecioVentaTotalProducto]').val()
	);

	if(mostrarMensaje)
	{
		notaOperacionCorrecta();
	}
}

function moverTodoProductos()
{
	if($('#tableProducto > tbody > tr').length==$('#tableProductoNotaCredito > tbody > tr').length)
	{
		notaError('No se pudo proceder', 'Los productos ya existen en la lista.');

		return;
	}

	$('#tableProducto > tbody > tr').each(function(index, element)
	{
		if($(element).find('input[name^=hdNombreProducto]').length)
		{
			moverProducto(element, false);
		}
	});

	notaOperacionCorrecta();
}

function calcularPreciosItem(element, parametroCantidad, porcentajeTributacion)
{
	var cantidadProductoTemp=$(element).find('input[name^=hdCantidadProducto]').val();
	var precioVentaUnitarioProductoTemp=$(element).find('input[name^=hdPrecioVentaUnitarioProducto]').val();
	var precioVentaTotalProductoTemp=$(element).find('input[name^=hdPrecioVentaTotalProducto]').val();

	cantidadProductoTemp=isNaN(cantidadProductoTemp) ? 0 : cantidadProductoTemp;
	precioVentaTotalProductoTemp=isNaN(precioVentaTotalProductoTemp) ? 0 : precioVentaTotalProductoTemp;

	if(parametroCantidad)
	{
		var totalCalculado=(cantidadProductoTemp*precioVentaUnitarioProductoTemp).toFixed(2);
		var subTotalCalculado=(totalCalculado/((parseFloat(porcentajeTributacion)/100)+1)).toFixed(2);
		var impuestoCalculado=(totalCalculado-subTotalCalculado).toFixed(2);

		$(element).find('[class^=tdPrecioVentaTotalProducto]').text(simboloDivisa+totalCalculado);

		$(element).find('input[name^=hdSubTotalProducto]').val(subTotalCalculado);
		$(element).find('input[name^=hdImpuestoAplicadoProducto]').val(impuestoCalculado);
		$(element).find('input[name^=hdPrecioVentaTotalProducto]').val(totalCalculado);
	}
	else
	{
		var subTotalCalculado=(precioVentaTotalProductoTemp/((parseFloat(porcentajeTributacion)/100)+1)).toFixed(2);
		var impuestoCalculado=(precioVentaTotalProductoTemp-subTotalCalculado).toFixed(2);

		$(element).find('input[name^=hdSubTotalProducto]').val(subTotalCalculado);
		$(element).find('input[name^=hdImpuestoAplicadoProducto]').val(impuestoCalculado);
	}
}

function calcularPreciosTotales(idTable)
{
	var totalProductoTemp=0;
	var valorIncorrecto=false;

	var sufijoTemp='';

	if(idTable=='tableProducto')
	{
		sufijoTemp='Temp';
	}

	$('#'+idTable+' > tbody > tr').each(function(index, element)
	{
		if($('#'+idTable+' > tbody > tr').length==index+1)
		{
			var subTotalProductoTemp=(totalProductoTemp.toFixed(2)/((_porcentajeIgv/100)+1)).toFixed(2);

			$('#hdSubTotal'+sufijoTemp).val(valorIncorrecto ? '0.00' : subTotalProductoTemp);
			$('#hdImpuestoAplicado'+sufijoTemp).val(valorIncorrecto ? '0.00' : (totalProductoTemp.toFixed(2)-subTotalProductoTemp).toFixed(2));
			$('#hdTotal'+sufijoTemp).val(valorIncorrecto ? '0.00' : totalProductoTemp.toFixed(2));

			$(element).find('[class^=tdPrecioVentaTotalProducto]').text(simboloDivisa+$('#hdTotal').val());

			return false;
		}

		if(isNaN($(element).find('input[name^=hdPrecioVentaTotalProducto]').val()))
		{
			valorIncorrecto=true;
		}

		totalProductoTemp+=parseFloat($(element).find('input[name^=hdPrecioVentaTotalProducto]').val());
	});
}

function quitarProducto(element)
{
	confirmacion(function()
	{
		$(element).parent().parent().remove();

		calcularPreciosTotales('tableProductoNotaCredito');
		
		notaOperacionCorrecta();
	});
}

$('[data-toggle="tooltip"]').tooltip();

$('#tableProducto > tbody > tr').each(function(index, element)
{
	if($(element).find('input[name^=hdNombreProducto]').length)
	{
		calcularPreciosItem(element, false, $(element).find('input[name^=hdPorcentajeTributacionProducto]').val());
	}
});

calcularPreciosTotales('tableProducto');

function enviarFrmInsertarReciboVentaNotaCredito()
{
	var isValid=true;

	if($('#tableProductoNotaCredito > tbody > tr').length<=1)
	{
		notaError('No se pudo proceder', 'Debe agregar por lo menos un producto al detalle de la nota de crédito.');

		return;
	}

	$('#tableProductoNotaCredito > tbody > tr').each(function(index, element)
	{
		if($(element).find('input[name^=hdNombreProducto]').length && (isNaN($(element).find('input[name^=hdPrecioVentaTotalProducto]').val()) || $(element).find('input[name^=hdPrecioVentaTotalProducto]').val()<0 || isNaN($(element).find('input[name^=hdCantidadProducto]').val()) || $(element).find('input[name^=hdCantidadProducto]').val()<=0))
		{
			isValid=false;

			notaError('No se pudo proceder', 'Datos incorrectos. Por favor corrija los valores de la venta.');

			return false;
		}
	});

	if(!isValid)
	{
		return;
	}

	$('#hdSelectMotivo').val($('#selectMotivo').val());

	confirmacionEnvio('frmInsertarReciboVentaNotaCredito');
}