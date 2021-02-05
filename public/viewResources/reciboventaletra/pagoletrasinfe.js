'use strict';

$('[data-toggle="tooltip"]').tooltip();

$('.frmPagoLetra').formValidation(
{
	framework: 'bootstrap',
	excluded: [':disabled', ':hidden', ':not(:visible)', '[class*="notValidate"]'],
	live: 'enabled',
	message: '<b style="color: #9d9d9d;">Asegúrese que realmente no necesita este valor.</b>',
	trigger: null,
	fields:
	{
		monto:
		{
			validators:
			{
				notEmpty:
				{
					message: '<b style="color: red;">El campo "Monto" es requerido.</b>'
				},
				regexp: {
					regexp: /^\d+([\.]{1}\d{1,2})?$/i,
					message: '<b style="color: red;">El monto no cumple el formato adecuado. 10.25</b>'
				}
			}
		}
	}
});

function sendFrmLetra(e, element)
{
	e.preventDefault();

	$('.frmPagoLetra').data('formValidation').resetField($('.monto'));

	$('#fmrPagoLetra').data('formValidation').validate();

	if ($('#fmrPagoLetra').data('formValidation').isValid())
	{
		if($('#fmrPagoLetra').find('.monto').val() === "0")
		{
			notaError('No se pudo proceder', 'El monto a pagar no puede ser 0.');

			return;
		}

		confirmacionEnvio('fmrPagoLetra');
	}
}

function eliminarPago(element) 
{
	confirmacion(function()
	{
		var urlredirect=$(element).data('urlredirect');
		
		location.href=urlredirect;
	});
}

function imprimirComprobante(element) 
{
	open($(element).data('urlredirect'));
}

function confirmarMarcarComoPagado(element)
{
	confirmacion(function()
	{
		var urlredirect=$(element).data('urlredirect');
		
		location.href=urlredirect;
	});
}