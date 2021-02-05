'use strict';

$(function()
{
	$('#frmInsertCategoriaVenta').formValidation(
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
						regexp: /^[a-zA-Z0-9ñÑàèìòùÀÈÌÒÙáéíóúÁÉÍÓÚS./\s*]*$/
					}
				}
			},
		}
	});
});

function enviarFrmInsertarCategoriaVenta()
{
	var isValid=null;

	$('#frmInsertCategoriaVenta').data('formValidation').validate();

	isValid=$('#frmInsertCategoriaVenta').data('formValidation').isValid();

	if(!isValid)
	{
		notaDatosIncorrectos();

		return;
	}

	confirmacionEnvio('frmInsertCategoriaVenta');
}

function cambiarEstadoCategoria(element) 
{
	confirmacion(function () 
	{
		var urlredirect = $(element).data('urlredirect');
		
		location.href = urlredirect;
	});
}