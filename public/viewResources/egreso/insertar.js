'use strict';

$(function()
{
	$('#frmInsertarEgreso').formValidation(
	{
		framework: 'bootstrap',
		excluded: [':disabled', ':hidden', ':not(:visible)', '[class*="notValidate"]'],
		live: 'enabled',
		message: '<b style="color: #9d9d9d;">Asegúrese que realmente no necesita este valor.</b>',
		trigger: null,
		fields:
		{
			txtMonto:
			{
				validators:
				{
					notEmpty:
					{
						message: '<b style="color: red;">Este campo es requerido.</b>'
					},
					regexp:
					{
						message: '<b style="color: red;">Formato incorrecto. [Ejemplo: 1354.42].</b>',
						regexp: /^\d+([\.]{1}\d{1,2})?$/i,
					}
				}
			},
			txtDescripcion:
			{
				validators:
				{
					notEmpty:
					{
						message: '<b style="color: red;">Este campo es requerido.</b>'
					}
				}
			}
		}
	});
});

function enviarFrmInsertarEgreso()
{
	var isValid=null;

	$('#frmInsertarEgreso').data('formValidation').validate();

	isValid=$('#frmInsertarEgreso').data('formValidation').isValid();

	if(!isValid)
	{
		notaDatosIncorrectos();

		return;
	}

	confirmacionEnvio('frmInsertarEgreso');
}