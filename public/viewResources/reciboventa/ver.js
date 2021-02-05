'use strict';

$(function()
{
	$('#searchPerformanceInput').val(localStorage.searchPerformance);

	if(sessionCodigoReciboVentaNotaCreditoTemp!='undefined')
	{
		window.open(_urlBase+'/reciboventanotacredito/imprimircomprobante/'+sessionCodigoReciboVentaNotaCreditoTemp, '_blank');
	}

	if(sessionCodigoReciboVentaNotaDebitoTemp!='undefined')
	{
		window.open(_urlBase+'/reciboventanotadebito/imprimircomprobante/'+sessionCodigoReciboVentaNotaDebitoTemp, '_blank');
	}

	if(sessionCodigoReciboVentaGuiaRemisionTemp!='undefined')
	{
		window.open(_urlBase+'/reciboventaguiaremision/imprimircomprobante/'+sessionCodigoReciboVentaGuiaRemisionTemp, '_blank');
	}
});

function searchItem(event)
{
	var evt=event || window.event;
	var code=evt.charCode || evt.keyCode || evt.which;
	
	if(code==13)
	{
		validarExpresion(event);
	}
}

function validarExpresion(event)
{
	event.preventDefault();

	if(! /^[0-9A-zÀ-ÿ\u00f1\u00d1.,;_\- ]*$/.test($('#textSearch').val()))
	{
		notaError('Búsqueda no permitida', 'Por favor realice búsquedas con caracteres válidos.');

		return;
	}

	$('#modalLoading').show();

	$('#frmSearch')[0].submit();
}