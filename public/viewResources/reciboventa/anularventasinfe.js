'use strict';

$('#frmAnularVentaSinFe').formValidation(
{
	framework: 'bootstrap',
	excluded: [':disabled', ':hidden', ':not(:visible)', '[class*="notValidate"]'],
	live: 'enabled',
	message: '<b style="color: #9d9d9d;">Asegúrese que realmente no necesita este valor.</b>',
	trigger: null,
	fields:
	{
		txtMotivoAnulacion:
		{
			validators:
			{
				notEmpty:
				{
					message: '<b style="color: red;">El motivo de anulación es requerido.</b>'
				}
			}
		}
	}
});

$('[data-toggle="tooltip"]').tooltip();

function enviarFrmAnularVentaSinFe()
{
	var isValid=true;

	$('#frmAnularVentaSinFe').data('formValidation').validateField('txtMotivoAnulacion');

	isValid=(!isValid ? false : $('#frmAnularVentaSinFe').data('formValidation').isValidField('txtMotivoAnulacion'));

	if(!isValid || $('#txtMotivoAnulacion').val().viiReplaceAll(' ', '')=='')
	{
		notaDatosIncorrectos();

		return;
	}

	confirmacionEnvio('frmAnularVentaSinFe');
}