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

$('#fechaInicial, #fechaFinal').datepicker(
{
	autoclose: true,
	format: 'yyyy/mm/dd'
});

$('.selectStaticNotClear').select2(
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
	allowClear: true,
});

$('#codPersonal').select2(
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
	placeholder: 'Buscar personal...',
	minimumInputLength: 3,
	allowClear: true,
	ajax:
	{
		url: _urlBase+'/personaltoficina/jsonpersonaltoficina',
		method: 'POST',
		dataType: 'json',
		delay: 300,
		data: function(params)
		{
			return {
				q: params.term,
				_token: _token
			};
		},
		processResults: function(data, params)
		{
			return {
				results: data.items
			};
		},
		cache: false
	}
});

function validarEnvio(element)
{
	$('#frmFiltro').data('formValidation').resetForm();
	$('#frmFiltro').data('formValidation').validate();

	if( ! $('#frmFiltro').data('formValidation').isValidField('codOficina') || ! $('#frmFiltro').data('formValidation').isValidField('fechaInicial') || ! $('#frmFiltro').data('formValidation').isValidField('fechaFinal'))
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

function onChangeSelectCategoriaVenta(selectChange)
{
	if(selectChange=='selectCategoriaVentaNivelUno')
	{
		$('#selectCategoriaVentaNivelDos > option').hide();
		$('#selectCategoriaVentaNivelTres > option').hide();

		$('.optionClearCategoriaVenta').show();
		$('.option'+$('#selectCategoriaVentaNivelUno').val()).show();
		
		$('#selectCategoriaVentaNivelDos').val(null);
		$('#selectCategoriaVentaNivelTres').val(null);
	}
	else
	{
		$('#selectCategoriaVentaNivelTres > option').hide();

		$('.optionClearCategoriaVenta').show();
		$('.option'+$('#selectCategoriaVentaNivelDos').val()).show();

		$('#selectCategoriaVentaNivelTres').val(null);
	}
}