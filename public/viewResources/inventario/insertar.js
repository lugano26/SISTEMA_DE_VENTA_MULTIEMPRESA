'use strict'

var numeroDeSeccionesCargadas = 0;

$(function() {
    $('#frmInsertarInventario').formValidation(
    {
        framework: 'bootstrap',
        excluded: [':disabled', ':hidden', ':not(:visible)', '[class*="notValidate"]'],
        live: 'enabled',
        message: '<b style="color: #9d9d9d;">Asegúrese que realmente no necesita este valor.</b>',
        trigger: null,
        fields:
        {
            selectAmbiente:
            {
                validators:
                {
                    notEmpty:
                    {
                        message: '<b style="color: red;">Este campo es requerido.</b>'
                    }
                }
            },
            selectEspacio:
            {
                validators: 
                {
                    notEmpty:
                    {
                        message: '<b style="color: red;">Este campo es requerido.</b>'
                    },
                }
            },
            txtNombre:
            {
                validators: 
                {
                    notEmpty:
                    {
                        message: '<b style="color: red;">Este campo es requerido.</b>'
                    },
                }
            },
            txtDimensionAnchoNumero:
            {
                validators: 
                {
                    regexp:
                    {
                        message: '<b style="color: red;">Formato incorrecto. [Ejemplo: 5.55].</b>',
                        regexp: /^\s*-?\d+(\.\d{1,2})?\s*$/
                    },
                    blank: {}
                }
            },
            txtDimensionLargoNumero:
            {
                validators: 
                {
                    regexp:
                    {
                        message: '<b style="color: red;">Formato incorrecto. [Ejemplo: 5.55].</b>',
                        regexp: /^\s*-?\d+(\.\d{1,2})?\s*$/
                    },
                    blank: {}
                }
            },
            txtDimensionAltoNumero:
            {
                validators: 
                {
                    regexp:
                    {
                        message: '<b style="color: red;">Formato incorrecto. [Ejemplo: 5.55].</b>',
                        regexp: /^\s*-?\d+(\.\d{1,2})?\s*$/
                    },
                    blank: {}
                }                
            },
            txtPeso:
            {
                validators: 
                {
                    regexp:
                    {
                        message: '<b style="color: red;">Formato incorrecto. [Ejemplo: 5.55].</b>',
                        regexp: /^\s*-?\d+(\.\d{1,2})?\s*$/
                    }
                }
            },
            txtInstancias:
            {
                validators: 
                {
                    notEmpty:
                    {
                        message: '<b style="color: red;">Este campo es requerido.</b>'
                    },
                    regexp:
                    {
                        message: '<b style="color: red;">Formato incorrecto. [Ejemplo: 1].</b>',
                        regexp: /^^[1-9][0-9]*$/
                    },
                }
            },
        }
    });

    $('#fmrModalAmbiente').formValidation(
    {
        framework: 'bootstrap',
        excluded: [':disabled', ':hidden', ':not(:visible)', '[class*="notValidate"]'],
        live: 'enabled',
        message: '<b style="color: #9d9d9d;">Asegúrese que realmente no necesita este valor.</b>',
        trigger: null,
        fields:
        {
            txtNombreAmbiente:
            {
                validators:
                {
                    notEmpty:
                    {
                        message: '<b style="color: red;">Este campo es requerido.</b>'
                    },
                    blank: {}
                }
            },
            txtCodigo:
            {
                validators:
                {
                    blank: {}
                }
            },
            txtNivelUbicacion:
            {
                validators:
                {
                    notEmpty:
                    {
                        message: '<b style="color: red;">Este campo es requerido.</b>'
                    },
                    regexp:
                    {
                        message: '<b style="color: red;">Formato incorrecto. [Ejemplo: 1].</b>',
                        regexp: /^^[1-9][0-9]*$/
                    },
                }
            },
            txtReferenciaUbicacion:
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
    
    $('#selectAmbiente').on('change', function()
    {
        numeroDeSeccionesCargadas = 0;

        $('#txtMaskNameAmbiente').val($('#selectAmbiente').text());
        $('#selectEspacio').empty();

        paginaAjaxJSON({ _token: _token, "codigoAmbiente" : $('#selectAmbiente').val() }, _urlBase + '/ambienteespacio/cargarespacios', 'POST', null, function(data)
        {
            if(data && data.items)
            {
                data.items.forEach(function(item) 
                {
                    if(parseInt(item.text) > numeroDeSeccionesCargadas)
                    {
                        numeroDeSeccionesCargadas = parseInt(item.text);
                    }

                    var newAmbienteEspacio = {
                        id: item.id,
                        text: (parseInt(item.text) === 0 ? 'Sin sección' : item.text)
                    };
                    
                    var newOption = new Option(newAmbienteEspacio.text, newAmbienteEspacio.id, false, false);
                    $('#selectEspacio').append(newOption).trigger('change');
                });
            }

            if(oldEspacio && oldEspacio !== null)
            {
                $('#selectEspacio').val(oldEspacio);
                oldEspacio = null;                    
            }
        }, false, true);
    });

    $('#chkReplicas').on('ifChecked', function(event){
        $('#txtInstancias').removeAttr('disabled');   
        $('#txtCodigoBarras').val('');
        $('#txtCodigoBarras').attr('disabled', 'disabled');
        $('#txtSerie').val('');
        $('#txtSerie').attr('disabled', 'disabled');     
    });

    $('#chkReplicas').on('ifUnchecked ', function(event){
        $('#txtInstancias').attr('disabled', 'disabled');
        $('#txtCodigoBarras').removeAttr('disabled');
            $('#txtSerie').removeAttr('disabled');
    });

    $('#chkSinSecciones').on('ifChecked', function(event){
        $('.buttonSeccion').addClass('disabled');
        $('.buttonSeccion').removeClass('btn-success');
        $('.buttonSeccion').addClass('btn-info');
        $('#txtNumberSeccion').val('');
        $('#containerButtonsSeccions').fadeOut();
    });

    $('#chkSinSecciones').on('ifUnchecked ', function(event){
        $('.buttonSeccion').removeClass('disabled');
        $('#txtNumberSeccion').val('');
        $('#containerButtonsSeccions').fadeIn('slow');
    });    

    $('.buttonSeccion').on('click', function(event){
        event.preventDefault();

        if(!$(this).hasClass('disabled'))
        {
            var index = $(this).data('index');
            $('#txtNumberSeccion').val(index);
            $('.buttonSeccion').removeClass('btn-success');
            $('.buttonSeccion').addClass('btn-info');
            
            for(var i = 1; i <= index; i++)
            {
                $('#buttonSeccion' + i).removeClass('btn-info')
                $('#buttonSeccion' + i).addClass('btn-success')
            }
        }        
    });
});

function enviarFrmInsertarInventario()
{
	var isValid=null;
    var $form = $('#frmInsertarInventario'),
    fv = $form.data('formValidation');

	fv.validate();

    isValid=fv.isValid();
    
    if( ($('#txtDimensionLargoNumero').val() !== '' || $('#txtDimensionAltoNumero').val() !== '') && $('#txtDimensionAnchoNumero').val() === '')
    {
        isValid = false;
        fv
            .updateMessage('txtDimensionAnchoNumero', 'blank', '<b style="color: red;">Este campo es requerido.</b>')
            .updateStatus('txtDimensionAnchoNumero', 'INVALID', 'blank');    
    }
    else
    {
        fv
        .updateMessage('txtDimensionAnchoNumero', 'blank', '')
        .updateStatus('txtDimensionAnchoNumero', 'VALID', 'blank');    
    }

    if( ($('#txtDimensionAnchoNumero').val() !== '' || $('#txtDimensionAltoNumero').val() !== '') && $('#txtDimensionLargoNumero').val() === '')
    {
        isValid = false;
        fv
            .updateMessage('txtDimensionLargoNumero', 'blank', '<b style="color: red;">Este campo es requerido.</b>')
            .updateStatus('txtDimensionLargoNumero', 'INVALID', 'blank');    
    }
    else
    {
        fv
        .updateMessage('txtDimensionLargoNumero', 'blank', '')
        .updateStatus('txtDimensionLargoNumero', 'VALID', 'blank');    
    }

    if( ($('#txtDimensionAnchoNumero').val() !== '' || $('#txtDimensionLargoNumero').val() !== '') && $('#txtDimensionAltoNumero').val() === '' )
    {
        isValid = false;
        fv
            .updateMessage('txtDimensionAltoNumero', 'blank', '<b style="color: red;">Este campo es requerido.</b>')
            .updateStatus('txtDimensionAltoNumero', 'INVALID', 'blank');    
    }
    else
    {
        fv
        .updateMessage('txtDimensionAltoNumero', 'blank', '')
        .updateStatus('txtDimensionAltoNumero', 'VALID', 'blank');    
    }

	if(!isValid)
	{
		notaDatosIncorrectos();

		return;
	}

	confirmacionEnvio('frmInsertarInventario');
}

function openModalSecciones()
{
    $('#frmInsertarInventario').data('formValidation').validateField('selectAmbiente');
		
    var isValid= $('#frmInsertarInventario').data('formValidation').isValidField('selectAmbiente');
    
    if(isValid)
    {
        if(numeroDeSeccionesCargadas === 0)
        {
            $('#containerButtonsSeccions').fadeOut();
            $('#chkSinSecciones').iCheck('check');
        }
        else
        {
            $('#containerButtonsSeccions').fadeIn('slow');
            $('#chkSinSecciones').iCheck('uncheck');

            $('#buttonSeccion' + numeroDeSeccionesCargadas).click();
        }

        $('#modalEspacio').modal('show');
    }        
}

function enviarFrmAmbiente()
{
    var isValid=null;

	$('#fmrModalAmbiente').data('formValidation').validate();

	isValid=$('#fmrModalAmbiente').data('formValidation').isValid();

	if(!isValid)
	{
		notaDatosIncorrectos();

		return;
	}

	confirmacion(function(){
        var fmrModalAmbiente = $("#fmrModalAmbiente").serializeArray();
        var data = {};
        $(fmrModalAmbiente).each(function(index, obj)
        {
            data[obj.name] = obj.value;
        });
        data._token = _token;

        paginaAjaxJSON(data, _urlBase + '/ambiente/insertarajax', 'POST', null, function(data)
        {
            if(data && data.isValid && data.tAmbiente)
            {
                $("#modalAmbiente").modal("hide");

                notaOperacionCorrecta();

                $('#txtCodigo').val('');
                $('#txtNivelUbicacion').val('');
                $('#txtReferenciaUbicación').val('');
                $('#txtNombreAmbiente').val('');
                $('#selectTipoAmbiente').val('Oficina');
                $('#fmrModalAmbiente').data('formValidation').resetForm('#fmrModalAmbiente');

                var newAmbiente = {
                    id: data.tAmbiente.codigoAmbiente,
                    text: data.tAmbiente.nombre + (data.tAmbiente.codigo !== "" ? " (" + data.tAmbiente.codigo + ")" : "")
                };
                
                var newOption = new Option(newAmbiente.text, newAmbiente.id, false, true);
                $('#selectAmbiente').append(newOption).trigger('change');
            }
            else
            {
                var $form = $('#fmrModalAmbiente'),
                fv = $form.data('formValidation');

                if(data.messages.length <= 0)
                {
                    notaError('Error', 'No se pudo guardar el ambiente, contacte con el administrador!');
                }

                data.messages.forEach(function(item) {
                    fv
                    .updateMessage(item.field, 'blank', '<b style="color: red;">' + item.message + '</b>')
                    .updateStatus(item.field, 'INVALID', 'blank');
                });
            }
        }, false, true);
    });
}

function enviarFrmEspacio()
{
    if(!$('#chkSinSecciones').is(':checked') && $('#txtNumberSeccion').val() === '')
    {
        notaError('No se pudo proceder', 'Seleccione al menos una sección');
		
        return;
    }    

	confirmacion(function(){
        paginaAjaxJSON(
        {
            "codigoAmbiente" : $('#selectAmbiente').val(),
            "chkSinSecciones": $('#chkSinSecciones').is(':checked'),
            "txtNumberSeccion": $('#txtNumberSeccion').val(),
            _token: _token
        },
        _urlBase + '/ambienteespacio/insertarajax', 'POST', null, function(data)
        {
            if(data && data.isValid)
            {
                $("#modalEspacio").modal("hide");
                $('#selectAmbiente').trigger('change');

                notaOperacionCorrecta();
            }
            else
            {
                notaError('No se pudo proceder', data.messages[0]);
            }
        }, false, true);
    });
}