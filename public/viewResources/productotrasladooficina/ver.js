'use strict';

$(function()
{
	$('#searchPerformanceInput').val(localStorage.searchPerformance);
});

function anularTraslado(element) 
{
	confirmacion(function()
	{
		var urlredirect=$(element).data('urlredirect');

		$('#modalLoading').show();
		
		location.href=urlredirect;
	});
}

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