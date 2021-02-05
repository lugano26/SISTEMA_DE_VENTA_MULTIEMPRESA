'use strict';

$('#frmEditarAgrupadoAlmacenProducto').formValidation(
{
	framework: 'bootstrap',
	excluded: [':disabled', ':hidden', ':not(:visible)', '[class*="notValidate"]'],
	live: 'enabled',
	message: '<b style="color: #9d9d9d;">Asegúrese que realmente no necesita este valor.</b>',
	trigger: null,
	fields:
	{
		txtNombreProducto:
		{
			validators:
			{
				notEmpty:
				{
					message: '<b style="color: red;">Este campo es requerido.</b>'
				}
			}
		},
		txtPorcentajeGananciaProducto:
		{
			validators:
			{
				notEmpty:
				{
					message: '<b style="color: red;">Este campo es requerido.</b>'
				},
				regexp:
				{
					message: '<b style="color: red;">Formato incorrecto. [Ejemplo: 7, 7.7].</b>',
					regexp: /^\d+([\.]{1}\d{1,2})?$/
				}
			}
		},
		txtPrecioVentaUnitarioProducto:
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
		txtCantidadMinimaAlertaStockProducto:
		{
			validators:
			{
				notEmpty:
				{
					message: '<b style="color: red;">Este campo es requerido.</b>'
				},
				regexp:
				{
					message: '<b style="color: red;">Formato incorrecto. [Ejemplo: 7, 7.7, 7.77, ...].</b>',
					regexp: /^\d+([\.]{1}\d+)?$/
				}
			}
		},
		txtPesoGramosUnidadProducto:
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
		txtPorcentajeTributacionProducto:
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

$('.datepicker').datepicker(
{
	autoclose : true,
	format : 'yyyy-mm-dd'
});

function calcularPreciosImpuestos(init)
{
	var porcentajeTributacionProducto=$('#txtPorcentajeTributacionProducto').val();
	var precioCompraUnitarioProducto=$('#txtPrecioCompraUnitarioProducto').val();

	var porcentajeGananciaProducto=$('#txtPorcentajeGananciaProducto').val();
	var precioVentaUnitarioProducto=$('#txtPrecioVentaUnitarioProducto').val();

	if(isNaN(porcentajeTributacionProducto) || isNaN(precioCompraUnitarioProducto) || porcentajeTributacionProducto<=0 || porcentajeTributacionProducto>=100)
	{
		$('#txtImpuestoAplicadoProducto').val(null);
	}
	else
	{
		var valorOperacionalImpuesto=((porcentajeTributacionProducto/100)+1).toFixed(2);
		var impuestoAplicado=Math.abs((precioCompraUnitarioProducto/valorOperacionalImpuesto)-precioCompraUnitarioProducto).toFixed(2);

		$('#txtImpuestoAplicadoProducto').val(impuestoAplicado);
	}

	if(isNaN(porcentajeGananciaProducto))
	{
		$('#txtPrecioVentaUnitarioProducto').val(null);
	}

	if(isNaN(precioVentaUnitarioProducto))
	{
		$('#txtPorcentajeGananciaProducto').val(null);
	}

	if((!($('#txtPrecioVentaUnitarioProducto').is(':focus')) && !init) && !isNaN(porcentajeGananciaProducto) && $('#txtPrecioCompraUnitarioProducto').val()!='')
	{
		precioVentaUnitarioProducto=(parseFloat(precioCompraUnitarioProducto)+parseFloat((precioCompraUnitarioProducto*porcentajeGananciaProducto)/100)).toFixed(2);

		$('#txtPrecioVentaUnitarioProducto').val(precioVentaUnitarioProducto);
	}

	if(($('#txtPrecioVentaUnitarioProducto').is(':focus') || init) && !isNaN(precioVentaUnitarioProducto) && $('#txtPrecioCompraUnitarioProducto').val()!='')
	{
		var ganancia=precioVentaUnitarioProducto-precioCompraUnitarioProducto;

		if(ganancia<=0)
		{
			porcentajeGananciaProducto=0;
		}
		else
		{
			porcentajeGananciaProducto=((ganancia*100)/precioCompraUnitarioProducto).toFixed(2);
		}

		$('#txtPorcentajeGananciaProducto').val(porcentajeGananciaProducto=='Infinity' ? 100 : porcentajeGananciaProducto);
	}

	$('#frmEditarAgrupadoAlmacenProducto').data('formValidation').resetForm();
	$('#frmEditarAgrupadoAlmacenProducto').data('formValidation').validate();
}

function onChangeSelectSituacionImpuestoProducto()
{
	if($('#selectSituacionImpuestoProducto').val()=='Afecto')
	{
		$('#selectTipoImpuestoProducto').removeAttr('disabled');
	}
	else
	{
		$('#selectTipoImpuestoProducto').attr('disabled', true);
		$('#selectTipoImpuestoProducto').val('IGV').trigger('change');
	}
}

function onChangeSelectTipoImpuestoProducto()
{
	if($('#selectTipoImpuestoProducto').val()=='IGV')
	{			
		$('#txtPorcentajeTributacionProducto').attr('readonly', 'readonly');
		$('#txtPorcentajeTributacionProducto').val(_porcentajeIgv);
	}
	else
	{
		$('#txtPorcentajeTributacionProducto').removeAttr('readonly');
	}

	calcularPreciosImpuestos(false);
}

calcularPreciosImpuestos(true);

function enviarFrmEditarAgrupadoAlmacenProducto()
{
	var isValid=null;

	$('#frmEditarAgrupadoAlmacenProducto').data('formValidation').validate();

	isValid=$('#frmEditarAgrupadoAlmacenProducto').data('formValidation').isValid();

	if(!isValid)
	{
		notaDatosIncorrectos();

		return;
	}

	confirmacionEnvio('frmEditarAgrupadoAlmacenProducto');
}