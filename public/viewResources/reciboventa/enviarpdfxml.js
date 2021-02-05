'use strict';

$('.fileItem input[type="checkbox"]').iCheck(
{
	checkboxClass: 'icheckbox_flat-blue',
	radioClass: 'iradio_flat-blue'
});

$('#frmEnviarPdfXml').formValidation(
{
	framework: 'bootstrap',
	excluded: [':disabled', ':hidden', ':not(:visible)', '[class*="notValidate"]'],
	live: 'enabled',
	message: '<b style="color: #9d9d9d;">Asegúrese que realmente no necesita este valor.</b>',
	trigger: null,
	fields:
	{
		txtEmail:
		{
			validators:
			{
				notEmpty:
				{
					message: '<b style="color: red;">El correo electrónico es requerido.</b>'
				},
				regexp:
				{
					regexp: /^([a-zA-Z0-9\.\-_]+\@[a-zA-Z0-9\-_]+\.[a-zA-Z]+(\.[a-zA-Z]+)?)*$/,
					message: '<b style="color: red;">El correo electrónico no cumple el formato adecuado.</b>'
				}
			}
		},
		txtMessage:
		{
			validators:
			{
				notEmpty:
				{
					message: '<b style="color: red;">El mensaje es requerido.</b>'
				}
			}
		},
	}
});

$('.itemFile').on('click', function()
{
	$($(this).find('.selectedItem')).iCheck(!$(this).find('.selectedItem').is(':checked') ? 'check' : 'uncheck');
});

$('[data-toggle="tooltip"]').tooltip();

function enviarFrmEnviarPdfXml()
{
	var isValid=true;
	var allUnChecked=true;

	$('#frmEnviarPdfXml').data('formValidation').validateField('txtEmail');

	isValid=(!isValid ? false : $('#frmEnviarPdfXml').data('formValidation').isValidField('txtEmail'));

	$('.itemFile').find('.selectedItem').each(function(index, element)
	{
		if($(element).is(':checked'))
		{
			allUnChecked = false;

			return false;
		}
	});

	if(!isValid || $('#txtEmail').val().viiReplaceAll(' ', '')=='')
	{
		notaDatosIncorrectos();

		return;
	}

	if(allUnChecked)
	{
		notaError("No se pudo proceder", "Debe seleccionar por lo menos un archivo para hacer el envio.");

		return;
	}

	confirmacionEnvio('frmEnviarPdfXml');
}