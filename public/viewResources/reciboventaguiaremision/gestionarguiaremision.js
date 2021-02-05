'use strict';

$('#frmInsertarGuiaRemision').formValidation(
{
	framework: 'bootstrap',
	excluded: [':disabled', ':hidden', ':not(:visible)', '[class*="notValidate"]'],
	live: 'enabled',
	message: '<b style="color: #9d9d9d;">Asegúrese que realmente no necesita este valor.</b>',
	trigger: null,
	fields:
	{
		txtDocumentoReceptorGuiaRemision:
		{
			validators:
			{
				notEmpty:
				{
					message: '<b style="color: red;">Este campo es requerido.</b>'
				},
				regexp:
				{
					message: '<b style="color: red;">Formato incorrecto. [Ejemplo: 12345678 o 12345678900].</b>',
					regexp: /^([0-9]{8}|[0-9]{11})$/
				}
			}
		},
		txtNombreCompletoReceptorGuiaRemision:
		{
			validators:
			{
				notEmpty:
				{
					message: '<b style="color: red;">Este campo es requerido.</b>'
				}
			}
		},
		txtDocumentoTransportistaGuiaRemision:
		{
			validators: 
			{
				notEmpty:
				{
					message: '<b style="color: red;">Este campo es requerido.</b>'
				},
				regexp:
				{
					message: '<b style="color: red;">Formato incorrecto. [Ejemplo: 12345678900].</b>',
					regexp: /^[0-9]{11}$/
				}
			}
		},
		txtNombreCompletoTransportistaGuiaRemision:
		{
			validators:
			{
				notEmpty:
				{
					message: '<b style="color: red;">Este campo es requerido.</b>'
				}
			}
		},
		txtDniConductorTransportistaGuiaRemision:
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
		txtPlacaVehiculoTransportistaGuiaRemision:
		{
			validators: 
			{
				notEmpty:
				{
					message: '<b style="color: red;">Este campo es requerido.</b>'
				}
			}
		},
		txtNumeroContenedorTransporteGuiaRemision:
		{
			validators: 
			{
				notEmpty:
				{
					message: '<b style="color: red;">Este campo es requerido.</b>'
				}
			}
		},
		txtPesoBrutoKilosBienesGuiaRemision:
		{
			validators: 
			{
				notEmpty:
				{
					message: '<b style="color: red;">Este campo es requerido.</b>'
				},
				regexp:
				{
					message: '<b style="color: red;">Formato incorrecto. [Ejemplo: 7, 7.7, 7.77].</b>',
					regexp: /^\d+([\.]{1}\d{1,2})?$/
				}
			}
		},
		dateFechaIniciaTrasladoGuiaRemision:
		{
			validators: 
			{
				notEmpty:
				{
					message: '<b style="color: red;">Este campo es requerido.</b>'
				}
			}
		},
		selectMotivoTrasladoGuiaRemision:
		{
			validators: 
			{
				notEmpty:
				{
					message: '<b style="color: red;">Este campo es requerido.</b>'
				}
			}
		},
		selectUbigeoPartidaGuiaRemision:
		{
			validators: 
			{
				notEmpty:
				{
					message: '<b style="color: red;">Este campo es requerido.</b>'
				}
			}
		},
		txtDireccionPartidaGuiaRemision:
		{
			validators: 
			{
				notEmpty:
				{
					message: '<b style="color: red;">Este campo es requerido.</b>'
				}
			}
		},
		selectUbigeoLlegadaGuiaRemision:
		{
			validators: 
			{
				notEmpty:
				{
					message: '<b style="color: red;">Este campo es requerido.</b>'
				}
			}
		},
		txtDireccionLlegadaGuiaRemision:
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

calcularKilosTotales();

var dataSelectUbigeoPartida=[];
var dataSelectUbigeoLlegada=[];

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
});

$('#selectUbigeoPartidaGuiaRemision').select2(
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
	placeholder: 'Buscar ciudad...',
	minimumInputLength: 3,
	ajax:
	{
		url: _urlBase+'/ubigeo/jsonporubicacion',
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
			dataSelectUbigeoPartida=data.items;

			return {
				results: data.items
			};
		},
		cache: false
	}
});

$('#selectUbigeoLlegadaGuiaRemision').select2(
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
	placeholder: 'Buscar ciudad...',
	minimumInputLength: 3,
	ajax:
	{
		url: _urlBase+'/ubigeo/jsonporubicacion',
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
			dataSelectUbigeoLlegada=data.items;

			return {
				results: data.items
			};
		},
		cache: false
	}
});

function onKeyUpTdCantidadProducto(element, pesoGramosUnidadProducto)
{
	var cantidadProductoTemp=((!isNaN($(element).text()) && $(element).text().trim()!='') ? parseFloat($(element).text()).toFixed(2) : 0);

	$(element).parent().find('input[name^=hdCantidadProducto]').val(cantidadProductoTemp);	
	$(element).parent().find('input[name^=hdPesoKilos]').val(((cantidadProductoTemp*pesoGramosUnidadProducto)/1000).toFixed(2));
	$(element).parent().find('.tdPesoKilos').text(((cantidadProductoTemp*pesoGramosUnidadProducto)/1000).toFixed(2));

	calcularKilosTotales();
}

function onKeyUpTdPesoTotalProducto(element)
{
	$(element).parent().find('input[name^=hdPesoKilos]').val((!isNaN($(element).text()) ? parseFloat($(element).text()).toFixed(2) : 0));

	calcularKilosTotales();
}

function calcularKilosTotales()
{
	var totalPesoKilosTemp=0;
	var valorIncorrecto=null;

	$('#tableProducto > tbody > tr').each(function(index, element)
	{
		valorIncorrecto=false;

		if(isNaN($(element).find('input[name^=hdPesoKilos]').val()) || $(element).find('input[name^=hdPesoKilos]').val()=='')
		{
			valorIncorrecto=true;
		}

		totalPesoKilosTemp+=valorIncorrecto ? 0 : parseFloat($(element).find('input[name^=hdPesoKilos]').val());
	});

	$('#txtPesoBrutoKilosBienesGuiaRemision').val(totalPesoKilosTemp.toFixed(2));
}

function quitarProducto(element)
{
	confirmacion(function()
	{
		$(element).parent().parent().remove();

		calcularKilosTotales();
		
		notaOperacionCorrecta();
	});
}

$('#dateFechaIniciaTrasladoGuiaRemision').datepicker(
{
	autoclose : true,
	format : 'yyyy-mm-dd'
});

function enviarFrmInsertarGuiaRemision()
{
	var isValid=null;

	$('#frmInsertarGuiaRemision').data('formValidation').resetForm();
	$('#frmInsertarGuiaRemision').data('formValidation').validate();

	isValid=$('#frmInsertarGuiaRemision').data('formValidation').isValid();

	$('#tableProducto > tbody > tr').each(function(index, element)
	{
		if(
			isNaN($(element).find('input[name^=hdPesoKilos]').val())
			|| $(element).find('input[name^=hdPesoKilos]').val()==''
			|| isNaN($(element).find('input[name^=hdCantidadProducto]').val())
			|| $(element).find('input[name^=hdCantidadProducto]').val()==''
			|| $(element).find('input[name^=hdCantidadProducto]').val()==0
		)
		{
			isValid=false;

			return false;
		}
	});

	if($('#tableProducto > tbody > tr').length==0)
	{
		isValid=false;
	}

	if(!isValid)
	{
		notaDatosIncorrectos();

		return;
	}

	confirmacionEnvio('frmInsertarGuiaRemision');
}