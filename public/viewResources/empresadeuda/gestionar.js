'use strict';

$(function()
{
	$('#frmGestionarEmpresaDeuda').formValidation(
	{
		framework: 'bootstrap',
		excluded: [':disabled', ':hidden', ':not(:visible)', '[class*="notValidate"]'],
		live: 'enabled',
		message: '<b style="color: #9d9d9d;">Asegúrese que realmente no necesita este valor.</b>',
		trigger: null,
		fields:
		{
			txtDescripcion:
			{
				validators:
				{
					notEmpty:
					{
						message: '<b style="color: red;">Este campo es requerido.</b>'
					}
				}
			},
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
			dateFechaPagar:
			{
				validators:
				{
					notEmpty:
					{
						message: '<b style="color: red;">Este campo es requerido.</b>'
					}
				}
			},
			dateFechaInicioPeriodo:
			{
				validators:
				{
					notEmpty:
					{
						message: '<b style="color: red;">Este campo es requerido.</b>'
					}
				}
			},
			dateFechaFinPeriodo:
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

function enviarFrmGestionarEmpresaDeuda()
{
	var isValid=null;

	$('#frmGestionarEmpresaDeuda').data('formValidation').resetField($('#dateFechaPagar'));
	$('#frmGestionarEmpresaDeuda').data('formValidation').resetField($('#dateFechaInicioPeriodo'));
	$('#frmGestionarEmpresaDeuda').data('formValidation').resetField($('#dateFechaFinPeriodo'));

	$('#frmGestionarEmpresaDeuda').data('formValidation').validate();

	isValid=$('#frmGestionarEmpresaDeuda').data('formValidation').isValid();

	if(!isValid)
	{
		notaDatosIncorrectos();

		return;
	}

	confirmacionEnvio('frmGestionarEmpresaDeuda');
}