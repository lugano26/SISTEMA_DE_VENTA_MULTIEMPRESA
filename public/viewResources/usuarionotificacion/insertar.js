'use strict';

$(function()
{
	$('#frmUsuarioNotificacionInsertar').formValidation(
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
					}
				}
			},
			dateFechaInicioPeriodo:
			{
				validators:
				{
					notEmpty:
					{
						message: '<b style="color: red;">Este campo es requerido.</b>'
					}
				}
			},
			dateFechaFinPeriodo:
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

	$('#txtDescripcion').on('keydown', function(e) 
	{ 
		if (e.keyCode === 13)
		{
			$('#txtDescripcion').val( $('#txtDescripcion').val() + "\n");
			return false;
		}
		
		return true;
	});
	
	$('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck(
	{
		checkboxClass: 'icheckbox_flat-blue',
		radioClass   : 'iradio_flat-blue'
	});

	$('input[type="checkbox"].flat-red, input[type="radio"].flat-red').on('ifChecked', cambiarEstadoPermanente);

	cambiarEstadoPermanente();
});

function enviarFrmGestionarEmpresaDeuda()
{
	var isValid=null;

	$('#frmUsuarioNotificacionInsertar').data('formValidation').resetField($('#txtDescripcion'));
	$('#frmUsuarioNotificacionInsertar').data('formValidation').resetField($('#dateFechaInicioPeriodo'));
	$('#frmUsuarioNotificacionInsertar').data('formValidation').resetField($('#dateFechaFinPeriodo'));

	$('#frmUsuarioNotificacionInsertar').data('formValidation').validate();

	isValid=$('#frmUsuarioNotificacionInsertar').data('formValidation').isValid();
	
	if(!isValid)
	{
		notaDatosIncorrectos();

		return;
	}

	isValid = false;

	$('.itemPersonal input').each(function(index, element) 
	{ 
		if($(element).is(':checked'))
		{
			isValid = true;
		}
	});

	if(!isValid)
	{
		notaError('No se pudo proceder', 'Seleccione al menos un usuario para mandar la notificación');

		return;
	}

	if(new Date($('#dateFechaInicioPeriodo').val()) > new Date($('#dateFechaFinPeriodo').val()))
	{
		notaError('No se pudo proceder', 'La fecha de inicio no puede ser mayor a la fecha de fin.');

		return;
	}

	confirmacionEnvio('frmUsuarioNotificacionInsertar');
}

function cambiarEstadoPermanente()
{
	if(!$('#chkPermanenteSi').is(':checked'))
	{
		$('#dateFechaInicioPeriodo').attr('disabled', true);
		$('#dateFechaFinPeriodo').attr('disabled', true);
	}
	else
	{
		$('#dateFechaInicioPeriodo').attr('disabled', false);
		$('#dateFechaFinPeriodo').attr('disabled', false);
	}
}

function moverPersonalPorRol()
{
	if($('#selectRolUsuario').val().length)
	{
		$('.itemPersonal').find('input').iCheck('unCheck');

		$('.itemPersonal').each(function(index, element)
		{
			$('#selectRolUsuario').val().forEach(function(rol)
			{
				if($(element).data('rol').indexOf(rol) != -1)
				{
					$('#' + $(element).data('codigopersonal')).iCheck('check');
				}
			})
		});
	}
	else
	{
		notaError('No se pudo proceder', 'Seleccione al menos un rol para hacer el seleccionado.');
	}
}