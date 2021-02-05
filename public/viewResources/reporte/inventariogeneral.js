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
		codAlmacen:
		{
			validators:
			{
				blank: {}
			}
		},
		codOficina:
		{
			validators:
			{
				blank: {}
			}
        }
	}
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
    allowClear: true,
	placeholder: 'Buscar...',
});

function validarEnvio(element)
{
    var isValid=null;
    var $form = $('#frmFiltro'),
    fv = $form.data('formValidation');

	fv.validate();

    isValid=fv.isValid();
    
    if($('#codAlmacen').val() === '' && $('#codOficina').val() === '' )
    {
        isValid = false;
        fv
            .updateMessage('codAlmacen', 'blank', '<b style="color: red;">Este campo es requerido.</b>')
            .updateStatus('codAlmacen', 'INVALID', 'blank');  
        fv
            .updateMessage('codOficina', 'blank', '<b style="color: red;">Este campo es requerido.</b>')
            .updateStatus('codOficina', 'INVALID', 'blank');  
    }
    else if(($('#codAlmacen').val() && $('#codOficina').val()) && ($('#codAlmacen').val()  !== '' && $('#codOficina').val() !== ''))
    {
        isValid = false;
        fv
            .updateMessage('codAlmacen', 'blank', '<b style="color: red;">Seleccione oficina o almacen.</b>')
            .updateStatus('codAlmacen', 'INVALID', 'blank');  
        fv
            .updateMessage('codOficina', 'blank', '<b style="color: red;">Seleccione oficina o almacen.</b>')
            .updateStatus('codOficina', 'INVALID', 'blank');  
    }
    else
    {
        isValid = true;
        fv
        .updateMessage('codAlmacen', 'blank', '')
        .updateStatus('codAlmacen', 'VALID', 'blank'); 
        fv
        .updateMessage('codOficina', 'blank', '')
        .updateStatus('codOficina', 'VALID', 'blank');    
    }

    if(!isValid)
	{
		notaDatosIncorrectos();

		return;
	}

	$('#tipoReporte').val($(element).val());
	
	$('#frmFiltro')[0].submit();
}

function guardarNombreAlmacenOrigen()
{
    $('#hdAlmacen').val($("#codAlmacen option:selected").text());
    cargarAmbiente($("#codAlmacen").val(), 1);
}

function guardarNombreOficinaOrigen()
{   
    $('#hdOficina').val($("#codOficina option:selected").text());
    cargarAmbiente($("#codOficina").val(), 0);
}

function cargarAmbiente(codigoAlmacenOficina, type)
{
    if(!codigoAlmacenOficina) return false;
    
    $('#selectAmbiente').empty();
    paginaAjaxJSON({ _token: _token, "codigoAlmacenOficina" : codigoAlmacenOficina }, _urlBase + '/ambiente/cargarambientes', 'POST', null, function(data)
    {
        if(data && data.items)
        {
            if(type == 0)
            {
                $('#codAlmacen').select2('val', '[]');  
            }
            else {
                $('#codOficina').select2('val', '[]');  
            }

            data.items.forEach(function(item) 
            {
                var newAmbienteEspacio = {
                    id: item.id,
                    text: item.text
                };
                
                var newOption = new Option(newAmbienteEspacio.text, newAmbienteEspacio.id, false, false);
                $('#selectAmbiente').append(newOption).trigger('change');
            });
        }
    }, false, true);
}