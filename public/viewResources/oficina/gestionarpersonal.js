'use strict';

$('.select').select2(
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
	placeholder: 'Buscar...'
});

function enviarFrmGestionarPersonalOficina()
{
	confirmacionEnvio('frmGestionarPersonalOficina');
}