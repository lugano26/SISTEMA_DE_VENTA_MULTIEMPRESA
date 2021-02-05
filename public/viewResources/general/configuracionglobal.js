'use strict';

$(function()
{
	$('#frmRegistrarClienteNuevo').formValidation(
	{
		framework: 'bootstrap',
		excluded: [':disabled', ':hidden', ':not(:visible)', '[class*="notValidate"]'],
		live: 'enabled',
		message: '<b style="color: #9d9d9d;">Asegúrese que realmente no necesita este valor.</b>',
		trigger: null,
		fields:
		{
			txtRucEmpresa:
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
			txtRazonSocialEmpresa:
			{
				validators:
				{
					notEmpty:
					{
						message: '<b style="color: red;">Este campo es requerido.</b>'
					}
				}
			},
			txtRepresentanteLegalEmpresa:
			{
				validators:
				{
					notEmpty:
					{
						message: '<b style="color: red;">Este campo es requerido.</b>'
					}
				}
			},
			fileLogoEmpresarialEmpresa:
			{
				validators:
				{
					notEmpty:
					{
						message: '<b style="color: red;">Este campo es requerido.</b>'
					},
					file:
					{
						message: '<b style="color: red;">Formato incorrecto. [Sólo archivos "png" o "jpg"].</b>',
						extension: 'png,jpg'
					}
				}
			},
			txtUserNameEfEmpresa:
			{
				validators:
				{
					notEmpty:
					{
						message: '<b style="color: red;">Este campo es requerido.</b>'
					}
				}
			},
			txtPasswordEfEmpresa:
			{
				validators:
				{
					notEmpty:
					{
						message: '<b style="color: red;">Este campo es requerido.</b>'
					}
				}
			},
			txtDescripcionOficina:
			{
				validators:
				{
					notEmpty:
					{
						message: '<b style="color: red;">Este campo es requerido.</b>'
					},
					regexp:
					{
						message: '<b style="color: red;">Formato incorrecto. [Sólo se permite texto, números y espacios].</b>',
						regexp: /^[a-zA-Z0-9ñÑàèìòùÀÈÌÒÙáéíóúÁÉÍÓÚ\s*]*$/
					}
				}
			},
			txtPaisOficina:
			{
				validators:
				{
					notEmpty:
					{
						message: '<b style="color: red;">Este campo es requerido.</b>'
					}
				}
			},
			txtDepartamentoOficina:
			{
				validators:
				{
					notEmpty:
					{
						message: '<b style="color: red;">Este campo es requerido.</b>'
					}
				}
			},
			txtProvinciaOficina:
			{
				validators:
				{
					notEmpty:
					{
						message: '<b style="color: red;">Este campo es requerido.</b>'
					}
				}
			},
			txtDistritoOficina:
			{
				validators:
				{
					notEmpty:
					{
						message: '<b style="color: red;">Este campo es requerido.</b>'
					}
				}
			},
			txtDireccionOficina:
			{
				validators:
				{
					notEmpty:
					{
						message: '<b style="color: red;">Este campo es requerido.</b>'
					}
				}
			},
			txtTelefonoOficina:
			{
				validators:
				{
					notEmpty:
					{
						message: '<b style="color: red;">Este campo es requerido.</b>'
					}
				}
			},
			txtNumeroViviendaOficina:
			{
				validators:
				{
					notEmpty:
					{
						message: '<b style="color: red;">Este campo es requerido.</b>'
					}
				}
			},
			txtDescripcionAlmacen:
			{
				validators:
				{
					notEmpty:
					{
						message: '<b style="color: red;">Este campo es requerido.</b>'
					},
					regexp:
					{
						message: '<b style="color: red;">Formato incorrecto. [Sólo se permite texto, números y espacios].</b>',
						regexp: /^[a-zA-Z0-9ñÑàèìòùÀÈÌÒÙáéíóúÁÉÍÓÚ\s*]*$/
					}
				}
			},
			txtPaisAlmacen:
			{
				validators:
				{
					notEmpty:
					{
						message: '<b style="color: red;">Este campo es requerido.</b>'
					}
				}
			},
			txtDepartamentoAlmacen:
			{
				validators:
				{
					notEmpty:
					{
						message: '<b style="color: red;">Este campo es requerido.</b>'
					}
				}
			},
			txtProvinciaAlmacen:
			{
				validators:
				{
					notEmpty:
					{
						message: '<b style="color: red;">Este campo es requerido.</b>'
					}
				}
			},
			txtDistritoAlmacen:
			{
				validators:
				{
					notEmpty:
					{
						message: '<b style="color: red;">Este campo es requerido.</b>'
					}
				}
			},
			txtDireccionAlmacen:
			{
				validators:
				{
					notEmpty:
					{
						message: '<b style="color: red;">Este campo es requerido.</b>'
					}
				}
			},
			txtTelefonoAlmacen:
			{
				validators:
				{
					notEmpty:
					{
						message: '<b style="color: red;">Este campo es requerido.</b>'
					}
				}
			},
			txtNumeroViviendaAlmacen:
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

	onChangeRadioFacturacionElectronicaEmpresa();
});

function onChangeRadioFacturacionElectronicaEmpresa()
{
	if($('#radioFacturacionElectronicaEmpresaSi').is(':checked'))
	{
		$('#divUserNameEfEmpresa').show();
		$('#divTxtPasswordEfEmpresa').show();
	}
	else
	{
		$('#divUserNameEfEmpresa').hide();
		$('#divTxtPasswordEfEmpresa').hide();
	}
}

function seleccionarPestania(idTab)
{
	var isValid=true;

	$('#frmRegistrarClienteNuevo').data('formValidation').resetForm();

	switch(idTab)
	{
		case 'tab_1-2':
			$('#frmRegistrarClienteNuevo').data('formValidation').validateField('txtRucEmpresa');
			$('#frmRegistrarClienteNuevo').data('formValidation').validateField('txtRazonSocialEmpresa');
			$('#frmRegistrarClienteNuevo').data('formValidation').validateField('txtRepresentanteLegalEmpresa');
			$('#frmRegistrarClienteNuevo').data('formValidation').validateField('fileLogoEmpresarialEmpresa');

			isValid=(!isValid ? false : ($('#frmRegistrarClienteNuevo').data('formValidation').isValidField('txtRucEmpresa') && $('#frmRegistrarClienteNuevo').data('formValidation').isValidField('txtRazonSocialEmpresa') && $('#frmRegistrarClienteNuevo').data('formValidation').isValidField('txtRepresentanteLegalEmpresa') && $('#frmRegistrarClienteNuevo').data('formValidation').isValidField('fileLogoEmpresarialEmpresa')));

			if($('#radioFacturacionElectronicaEmpresaSi').is(':checked'))
			{
				$('#frmRegistrarClienteNuevo').data('formValidation').validateField('txtUserNameEfEmpresa');
				$('#frmRegistrarClienteNuevo').data('formValidation').validateField('txtPasswordEfEmpresa');

				isValid=(!isValid ? false : ($('#frmRegistrarClienteNuevo').data('formValidation').isValidField('txtUserNameEfEmpresa') && $('#frmRegistrarClienteNuevo').data('formValidation').isValidField('txtPasswordEfEmpresa')));
			}

			break;

		case 'tab_1-3':
			$('#frmRegistrarClienteNuevo').data('formValidation').validateField('txtDescripcionOficina');
			$('#frmRegistrarClienteNuevo').data('formValidation').validateField('txtPaisOficina');
			$('#frmRegistrarClienteNuevo').data('formValidation').validateField('txtDepartamentoOficina');
			$('#frmRegistrarClienteNuevo').data('formValidation').validateField('txtProvinciaOficina');
			$('#frmRegistrarClienteNuevo').data('formValidation').validateField('txtDistritoOficina');
			$('#frmRegistrarClienteNuevo').data('formValidation').validateField('txtDireccionOficina');
			$('#frmRegistrarClienteNuevo').data('formValidation').validateField('txtTelefonoOficina');
			$('#frmRegistrarClienteNuevo').data('formValidation').validateField('txtNumeroViviendaOficina');

			isValid=(!isValid ? false : ($('#frmRegistrarClienteNuevo').data('formValidation').isValidField('txtDescripcionOficina') && $('#frmRegistrarClienteNuevo').data('formValidation').isValidField('txtPaisOficina') && $('#frmRegistrarClienteNuevo').data('formValidation').isValidField('txtDepartamentoOficina') && $('#frmRegistrarClienteNuevo').data('formValidation').isValidField('txtProvinciaOficina') && $('#frmRegistrarClienteNuevo').data('formValidation').isValidField('txtDistritoOficina') && $('#frmRegistrarClienteNuevo').data('formValidation').isValidField('txtDireccionOficina') && $('#frmRegistrarClienteNuevo').data('formValidation').isValidField('txtTelefonoOficina') && $('#frmRegistrarClienteNuevo').data('formValidation').isValidField('txtNumeroViviendaOficina')));

			break;
	}

	if(!isValid)
	{
		notaDatosIncorrectos();

		return;
	}

	$('.tab-pane').removeClass('active');
	$('.nav-tabs > li').removeClass('active');

	$('#'+idTab).addClass('active');
	$('#li'+idTab.substring(0, 1).toUpperCase()+idTab.substring(1)).addClass('active');
}

function enviarFrmRegistrarClienteNuevo()
{
	var isValid=true;

	$('#frmRegistrarClienteNuevo').data('formValidation').validateField('txtDescripcionAlmacen');
	$('#frmRegistrarClienteNuevo').data('formValidation').validateField('txtPaisAlmacen');
	$('#frmRegistrarClienteNuevo').data('formValidation').validateField('txtDepartamentoAlmacen');
	$('#frmRegistrarClienteNuevo').data('formValidation').validateField('txtProvinciaAlmacen');
	$('#frmRegistrarClienteNuevo').data('formValidation').validateField('txtDistritoAlmacen');
	$('#frmRegistrarClienteNuevo').data('formValidation').validateField('txtDireccionAlmacen');
	$('#frmRegistrarClienteNuevo').data('formValidation').validateField('txtTelefonoAlmacen');
	$('#frmRegistrarClienteNuevo').data('formValidation').validateField('txtNumeroViviendaAlmacen');

	isValid=(!isValid ? false : ($('#frmRegistrarClienteNuevo').data('formValidation').isValidField('txtDescripcionAlmacen') && $('#frmRegistrarClienteNuevo').data('formValidation').isValidField('txtPaisAlmacen') && $('#frmRegistrarClienteNuevo').data('formValidation').isValidField('txtDepartamentoAlmacen') && $('#frmRegistrarClienteNuevo').data('formValidation').isValidField('txtProvinciaAlmacen') && $('#frmRegistrarClienteNuevo').data('formValidation').isValidField('txtDistritoAlmacen') && $('#frmRegistrarClienteNuevo').data('formValidation').isValidField('txtDireccionAlmacen') && $('#frmRegistrarClienteNuevo').data('formValidation').isValidField('txtTelefonoAlmacen') && $('#frmRegistrarClienteNuevo').data('formValidation').isValidField('txtNumeroViviendaAlmacen')));

	if(!isValid)
	{
		notaDatosIncorrectos();

		return;
	}

	confirmacionEnvio('frmRegistrarClienteNuevo');
}