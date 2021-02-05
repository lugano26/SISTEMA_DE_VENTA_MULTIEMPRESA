'use strict';

$('#fechaInicial, #fechaFinal').datepicker(
{
	autoclose: true,
	format: 'yyyy/mm/dd'
});

$('#frmFiltro').formValidation(
{
	framework: 'bootstrap',
	excluded: [':disabled', ':hidden', ':not(:visible)', '[class*="notValidate"]'],
	live: 'enabled',
	message: '<b style="color: #9d9d9d;">Asegúrese que realmente no necesita este valor.</b>',
	trigger: null,
	fields:
	{
		fechaInicial:
		{
			validators:
			{
				notEmpty:
				{
					message: '<b style="color: red;">Este campo es requerido.</b>'
				},
			}
		},
		fechaFinal:
		{
			validators:
			{
				notEmpty:
				{
					message: '<b style="color: red;">Este campo es requerido.</b>'
				},
			}
		}
	}
});

function validarEnvio(element)
{
	$('#frmFiltro').data('formValidation').resetForm();
	$('#frmFiltro').data('formValidation').validate();

	if( ! $('#frmFiltro').data('formValidation').isValidField('fechaInicial') || ! $('#frmFiltro').data('formValidation').isValidField('fechaFinal'))
	{
		notaDatosIncorrectos();

		return;
	}

	$('#tipoReporte').val($(element).val());

	$('#frmFiltro')[0].submit();
}