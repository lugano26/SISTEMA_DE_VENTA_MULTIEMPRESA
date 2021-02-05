'use strict';

$('#frmEditarEmpresa').formValidation(
{
	framework: 'bootstrap',
	excluded: [':disabled', ':hidden', ':not(:visible)', '[class*="notValidate"]'],
	live: 'enabled',
	message: '<b style="color: #9d9d9d;">Asegúrese que realmente no necesita este valor.</b>',
	trigger: null,
	fields:
	{
		txtRuc:
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
		txtRazonSocial:
		{
			validators:
			{
				notEmpty:
				{
					message: '<b style="color: red;">Este campo es requerido.</b>'
				}
			}
		},
		txtRepresentanteLegal:
		{
			validators:
			{
				notEmpty:
				{
					message: '<b style="color: red;">Este campo es requerido.</b>'
				}
			}
		},
		fileLogoEmpresarial:
		{
			validators:
			{
				notEmpty:
				{
					enabled: !parseInt(existeLogoEmpresarialTemp),
					message: '<b style="color: red;">Este campo es requerido.</b>'
				},
				file:
				{
					message: '<b style="color: red;">Formato incorrecto. [Sólo archivos "png" o "jpg"].</b>',
					extension: 'png,jpg'
				}
			}
		},
		txtUrlConsultaFactura:
		{
			validators:
			{
				notEmpty:
				{
					message: '<b style="color: red;">Este campo es requerido.</b>'
				}
			}
		},
		txtUserNameEf:
		{
			validators:
			{
				notEmpty:
				{
					message: '<b style="color: red;">Este campo es requerido.</b>'
				}
			}
		},
		txtPasswordEf:
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

function onChangeRadioFacturacionElectronica()
{
	if($('#radioFacturacionElectronicaSi').is(':checked'))
	{
		$('#divUserNameEf').show();
		$('#divTxtPasswordEf').show();
	}
	else
	{
		$('#divUserNameEf').hide();
		$('#divTxtPasswordEf').hide();
	}
}

onChangeRadioFacturacionElectronica();

function enviarFrmEditarEmpresa()
{
	var isValid=true;

	$('#frmEditarEmpresa').data('formValidation').resetForm();

	$('#frmEditarEmpresa').data('formValidation').validateField('txtRuc');
	$('#frmEditarEmpresa').data('formValidation').validateField('txtRazonSocial');
	$('#frmEditarEmpresa').data('formValidation').validateField('txtRepresentanteLegal');
	$('#frmEditarEmpresa').data('formValidation').validateField('fileLogoEmpresarial');
	$('#frmEditarEmpresa').data('formValidation').validateField('txtUrlConsultaFactura');

	isValid=(!isValid ? false : ($('#frmEditarEmpresa').data('formValidation').isValidField('txtRuc') && $('#frmEditarEmpresa').data('formValidation').isValidField('txtRazonSocial') && $('#frmEditarEmpresa').data('formValidation').isValidField('txtRepresentanteLegal') && $('#frmEditarEmpresa').data('formValidation').isValidField('fileLogoEmpresarial') && $('#frmEditarEmpresa').data('formValidation').isValidField('txtUrlConsultaFactura')));

	if($('#radioFacturacionElectronicaSi').is(':checked'))
	{
		$('#frmEditarEmpresa').data('formValidation').validateField('txtUserNameEf');
		$('#frmEditarEmpresa').data('formValidation').validateField('txtPasswordEf');

		isValid=(!isValid ? false : ($('#frmEditarEmpresa').data('formValidation').isValidField('txtUserNameEf') && $('#frmEditarEmpresa').data('formValidation').isValidField('txtPasswordEf')));
	}

	if(!isValid)
	{
		notaDatosIncorrectos();

		return;
	}

	confirmacionEnvio('frmEditarEmpresa');
}