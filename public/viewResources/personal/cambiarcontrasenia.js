'use strict'

$('#frmCambiarContraseniaPersonal').formValidation(
{
	framework: 'bootstrap',
	excluded: [':disabled', ':hidden', ':not(:visible)', '[class*="notValidate"]'],
	live: 'enabled',
	message: '<b style="color: #9d9d9d;">Asegúrese que realmente no necesita este valor.</b>',
	trigger: null,
	fields:
	{
		passContraseniaActualUsuario:
		{
			validators:
			{
				notEmpty:
				{
					message: '<b style="color: red;">Este campo es requerido.</b>'
				}
			}
		},
		passContraseniaUsuario:
		{
			validators:
			{
				notEmpty:
				{
					message: '<b style="color: red;">Este campo es requerido.</b>'
				},
				identical:
				{
					message: '<b style="color: red;">Este campo no coincide con su confirmación correspondiente.</b>',
					field: 'passContraseniaRepitaUsuario'
				}
			}
		},
		passContraseniaRepitaUsuario:
		{
			validators:
			{
				identical:
				{
					message: '<b style="color: red;">Este campo no coincide con su confirmación correspondiente.</b>',
					field: 'passContraseniaUsuario'
				}
			}
		}
	}
});

function enviarFrmCambiarContraseniaPersonal()
{
	var isValid=null;

	$('#frmCambiarContraseniaPersonal').data('formValidation').validate();

	isValid=$('#frmCambiarContraseniaPersonal').data('formValidation').isValid();

	if(!isValid)
	{
		notaDatosIncorrectos();

		return;
	}

	confirmacionEnvio('frmCambiarContraseniaPersonal');
}