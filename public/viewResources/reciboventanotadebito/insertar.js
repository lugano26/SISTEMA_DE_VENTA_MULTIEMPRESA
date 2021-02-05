'use strict';

$('#frmInsertarReciboVentaNotaDebito').formValidation(
{
	framework: 'bootstrap',
	excluded: [':disabled', ':hidden', ':not(:visible)', '[class*="notValidate"]'],
	live: 'enabled',
	message: '<b style="color: #9d9d9d;">Asegúrese que realmente no necesita este valor.</b>',
	trigger: null,
	fields:
	{
		txtTotal:
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
		}
	}
});

function calcularPreciosTotales()
{
	var total=isNaN($('#txtTotal').val()) ? 0 : $('#txtTotal').val();

	var subTotalCalculado=(total/((_porcentajeIgv/100)+1)).toFixed(2);
	var impuestoCalculado=(total-subTotalCalculado).toFixed(2);

	$('#txtSubTotal').val(subTotalCalculado);
	$('#txtImpuestoAplicado').val(impuestoCalculado);
}

function enviarFrmInsertarReciboVentaNotaDebito()
{
	var isValid=null;

	$('#frmInsertarReciboVentaNotaDebito').data('formValidation').validate();

	isValid=$('#frmInsertarReciboVentaNotaDebito').data('formValidation').isValid();

	if(!isValid)
	{
		return;
	}

	confirmacionEnvio('frmInsertarReciboVentaNotaDebito');
}