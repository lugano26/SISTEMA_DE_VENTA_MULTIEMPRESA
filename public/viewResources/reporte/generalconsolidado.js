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
}).datepicker('setDate','now');

$('#fechaInicial').change(function()
{
	var date2 = $('#fechaInicial').datepicker('getDate', '+1d'); 
	$('#fechaFinal').datepicker('setDate', (date2.addDays(1)));
});

$('#fechaFinal').datepicker('setDate', new Date().addDays(1));
// $('#fechaFinal').datepicker('remove').prop('readonly',true);

$('.selectStaticNotClear').select2(
{
	language:
	{
		noResults: function () {
			return "No se encontraron resultados.";
		},
		searching: function () {
			return "Buscando...";
		},
		inputTooShort: function () {
			return 'Por favor ingrese 3 o más caracteres';
		}
	},
	placeholder: 'Buscar...',
	allowClear: true,
});

function validarEnvio(element)
{
	$('#frmFiltro').data('formValidation').resetForm();
	$('#frmFiltro').data('formValidation').validate();

	if(!$('#frmFiltro').data('formValidation').isValidField('fechaInicial') || !$('#frmFiltro').data('formValidation').isValidField('fechaFinal'))
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

function guardarNombreAlmacenOrigen()
{
	$('#hdAlmacen').val($("#codAlmacen option:selected").text());
}