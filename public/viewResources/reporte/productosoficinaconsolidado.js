'use strict';

$('#frmFiltro').formValidation(
{
	framework: 'bootstrap',
	excluded: [':disabled', ':hidden', ':not(:visible)', '[class*="notValidate"]'],
	live: 'enabled',
	message: '<b style="color: #9d9d9d;">Asegúrese que realmente no necesita este valor.</b>',
	trigger: null,
	fields:
	{
		codOficina:
		{
			validators:
			{
				// notEmpty:
				// {
				// 	message: '<b style="color: red;">Este campo es requerido.</b>'
				// },
			}
		}
	}
});

$('.selectStatic').select2(
{
	allowClear: true,
	language:
	{
		noResults: function()
		{
			return "No se encontraron resultados.";        
		},
		searching: function()
		{
			return "Buscando...";
		},
		inputTooShort: function()
		{ 
			return 'Por favor ingrese 3 o más caracteres';
		}
	},
	placeholder: 'Buscar...',
});

$('.selectStaticNonClear').select2(
{
	language:
	{
		noResults: function()
		{
			return "No se encontraron resultados.";        
		},
		searching: function()
		{
			return "Buscando...";
		},
		inputTooShort: function()
		{ 
			return 'Por favor ingrese 3 o más caracteres';
		}
	},
	placeholder: 'Buscar...',
});

function validarEnvio(element)
{
	$('#frmFiltro').data('formValidation').resetForm();
	$('#frmFiltro').data('formValidation').validate();

	if( ! $('#frmFiltro').data('formValidation').isValidField('codOficina'))
	{
		notaDatosIncorrectos();

		return;
	}

	$('#tipoReporte').val($(element).val());
	
	$('#frmFiltro')[0].submit();
}

function guardarNombreOficinaOrigen()
{
	$('#hdOficina').val($("#codOficina option:selected").text());
}