'use strict';

$(function()
{
	$('#frmInsertarPersonal').formValidation(
	{
		framework: 'bootstrap',
		excluded: [':disabled', ':hidden', ':not(:visible)', '[class*="notValidate"]'],
		live: 'enabled',
		message: '<b style="color: #9d9d9d;">Asegúrese que realmente no necesita este valor.</b>',
		trigger: null,
		fields:
		{
			txtDni:
			{
				validators:
				{
					notEmpty:
					{
						message: '<b style="color: red;">Este campo es requerido.</b>'
					},
					regexp:
					{
						message: '<b style="color: red;">Formato incorrecto. [Ejemplo: 12345678].</b>',
						regexp: /^[0-9]{8}$/
					}
				}
			},
			txtNombre:
			{
				validators:
				{
					notEmpty:
					{
						message: '<b style="color: red;">Este campo es requerido.</b>'
					}
				}
			},
			txtApellido:
			{
				validators:
				{
					notEmpty:
					{
						message: '<b style="color: red;">Este campo es requerido.</b>'
					}
				}
			},
			txtDireccion:
			{
				validators:
				{
					notEmpty:
					{
						message: '<b style="color: red;">Este campo es requerido.</b>'
					}
				}
			},
			txtTelefono:
			{
				validators:
				{
					notEmpty:
					{
						message: '<b style="color: red;">Este campo es requerido.</b>'
					}
				}
			},
			txtCorreoElectronico:
			{
				validators:
				{
					notEmpty:
					{
						message: '<b style="color: red;">Este campo es requerido.</b>'
					},
					regexp:
					{
						message: '<b style="color: red;">Formato incorrecto. [Ejemplo: nombre@gmail.com].</b>',
						regexp: /^[a-zA-Z0-9\.\-_]+\@[a-zA-Z0-9\-_]+\.[a-zA-Z]+(\.[a-zA-Z]+)?$/
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
});

function enviarFrmInsertarPersonal()
{
	var isValid=null;

	$('#frmInsertarPersonal').data('formValidation').validate();

	isValid=$('#frmInsertarPersonal').data('formValidation').isValid();

	if(!isValid)
	{
		notaDatosIncorrectos();

		return;
	}

	confirmacionEnvio('frmInsertarPersonal');
}