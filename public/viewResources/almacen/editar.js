'use strict';

$('#frmEditarAlmacen').formValidation(
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
				},
				regexp:
				{
					message: '<b style="color: red;">Formato incorrecto. [Sólo se permite texto, números y espacios].</b>',
					regexp: /^[a-zA-Z0-9ñÑàèìòùÀÈÌÒÙáéíóúÁÉÍÓÚ\s*]*$/
				}
			}
		},
		txtPais:
		{
			validators:
			{
				notEmpty:
				{
					message: '<b style="color: red;">Este campo es requerido.</b>'
				}
			}
		},
		txtDepartamento:
		{
			validators:
			{
				notEmpty:
				{
					message: '<b style="color: red;">Este campo es requerido.</b>'
				}
			}
		},
		txtProvincia:
		{
			validators:
			{
				notEmpty:
				{
					message: '<b style="color: red;">Este campo es requerido.</b>'
				}
			}
		},
		txtDistrito:
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
		txtNumeroVivienda:
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

function enviarFrmEditarAlmacen()
{
	var isValid=null;

	$('#frmEditarAlmacen').data('formValidation').validate();

	isValid=$('#frmEditarAlmacen').data('formValidation').isValid();

	if(!isValid)
	{
		notaDatosIncorrectos();

		return;
	}

	confirmacionEnvio('frmEditarAlmacen');
}