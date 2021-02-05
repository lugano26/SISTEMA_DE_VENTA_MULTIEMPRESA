'use strict';

$(function()
{
	$('#frmInsertarReciboCompra').formValidation(
	{
		framework: 'bootstrap',
		excluded: [':disabled', ':hidden', ':not(:visible)', '[class*="notValidate"]'],
		live: 'enabled',
		message: '<b style="color: #9d9d9d;">Asegúrese que realmente no necesita este valor.</b>',
		trigger: null,
		fields:
		{
			selectProveedor:
			{
				validators:
				{
					notEmpty:
					{
						message: '<b style="color: red;">Este campo es requerido.</b>'
					},
					regexp:
					{
						message: '<b style="color: red;">Formato incorrecto. [Ejemplo: 77777777777-Nombre del proveedor].</b>',
						regexp: /^([0-9]{8}|[0-9]{11}){1}\-[a-zA-Z0-9\.\-\_\u00C0-\u00FF\s]+$/
					}
				}
			}
		}
	});

	$('#modalTemp').formValidation(
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
			txtCantidadProducto:
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
			txtPrecioCompraTotalProducto:
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

	$('#selectProveedor').select2(
	{
		tags: true,
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
		minimumInputLength: 3
	});

	$('#selectNombreProducto').select2(
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
		minimumInputLength: 3,
		ajax:
		{
			url: _urlBase+'/almacenproducto/jsonporcodigoempresanombregroupbynombre',
			method: 'POST',
			dataType: 'json',
			delay: 300,
			data: function(params)
			{
				return {
					q: params.term,
					searchPerformance: localStorage.searchPerformance,
					_token: _token
				};
			},
			processResults: function(data, params)
			{
				dataSelectNombreProducto=data.items;

				return {
					results: data.items
				};
			},
			cache: false
		}
	});

	if(oldSelectProveedor!='')
	{
		if($('#selectProveedor option[value="'+oldSelectProveedor+'"]').length)
		{
			$('#selectProveedor').val(oldSelectProveedor).change();
		}
		else
		{
			$('#selectProveedor').append('<option value="'+oldSelectProveedor+'" selected>'+oldSelectProveedor+'</option>');
		}

		onChangeSelectTipoRecibo();
		onChangeSelectTipoPago();

		$(oldData).each(function(index, element)
		{
			agregarProducto(
				element.codigoBarrasProducto,
				element.nombreProducto,
				element.codigoPresentacionProducto,
				element.codigoUnidadMedidaProducto,
				element.tipoProducto,
				element.situacionImpuestoProducto,
				element.tipoImpuestoProducto,
				element.porcentajeTributacionProducto,
				element.impuestoAplicadoProducto,
				element.cantidadMinimaAlertaStockProducto,
				element.pesoGramosUnidadProducto,
				element.precioCompraTotalProducto,
				element.precioCompraUnitarioProducto,
				element.precioVentaUnitarioProducto,
				element.cantidadProducto,
				element.radioVentaMenorUnidadProducto,
				element.teFechaVencimientoProducto,
				element.registroSerieProducto
			);
		});
	}
});

var dataSelectNombreProducto=[];

function changeSearchNombreProducto()
{
	$('#modalTemp').data('formValidation').resetField('txtNombreProducto');

	if($('#divNotSearchSelectNombreProducto').is(':visible'))
	{
		$('#divNotSearchSelectNombreProducto').hide();
		$('#divSearchTxtNombreProducto').show();

		$('#selectTipoProducto').removeAttr('disabled');
		$('#txtCodigoBarrasProducto').removeAttr('readonly');
		$('#selectCodigoUnidadMedidaProducto').removeAttr('disabled');
		$('#selectCodigoPresentacionProducto').removeAttr('disabled');
		$('#txtCantidadMinimaAlertaStockProducto').removeAttr('readonly');
		$('#txtPesoGramosUnidadProducto').removeAttr('readonly');
		$('#selectTipoImpuestoProducto').removeAttr('disabled');
		$('#selectSituacionImpuestoProducto').removeAttr('disabled');
		$('#radioVentaMenorUnidadProductoSi').removeAttr('disabled');
		$('#radioVentaMenorUnidadProductoNo').removeAttr('disabled');
	}
	else
	{
		$('#divNotSearchSelectNombreProducto').show();
		$('#divSearchTxtNombreProducto').hide();

		$('#selectTipoProducto').attr('disabled', true);
		$('#txtCodigoBarrasProducto').attr('readonly', 'readonly');
		$('#selectCodigoUnidadMedidaProducto').attr('disabled', true);
		$('#selectCodigoPresentacionProducto').attr('disabled', true);
		$('#txtCantidadMinimaAlertaStockProducto').attr('readonly', 'readonly');
		$('#txtPesoGramosUnidadProducto').attr('readonly', 'readonly');
		$('#selectTipoImpuestoProducto').attr('disabled', true);
		$('#selectSituacionImpuestoProducto').attr('disabled', true);
		$('#radioVentaMenorUnidadProductoSi').attr('disabled', true);
		$('#radioVentaMenorUnidadProductoNo').attr('disabled', true);
	}
}

function onChangeSelectNombreProducto()
{
	clearInputText('modalTemp', ['txtPorcentajeGananciaProducto', 'txtCantidadMinimaAlertaStockProducto', 'txtPesoGramosUnidadProducto', 'txtPorcentajeTributacionProducto']);

	$('#modalTemp').data('formValidation').resetForm();

	if($('#selectNombreProducto').val()==null || $('#selectNombreProducto').val()=='')
	{
		return;
	}

	var codigoAlmacenProductoTemp=$('#selectNombreProducto').val();
	var indexTemp=null;

	$(dataSelectNombreProducto).each(function(index, element)
	{
		if(element.row.codigoAlmacenProducto==codigoAlmacenProductoTemp)
		{
			indexTemp=index;

			return false;
		}
	});

	var porcentajeGananciaProductoTemp=((((dataSelectNombreProducto[indexTemp].row.precioVentaUnitario)*100)/dataSelectNombreProducto[indexTemp].row.precioCompraUnitario)-100).toFixed(2);

	$('#selectTipoProducto').val(dataSelectNombreProducto[indexTemp].row.tipo);
	$('#txtCodigoBarrasProducto').val(dataSelectNombreProducto[indexTemp].row.codigoBarras);
	$('#txtNombreProducto').val(dataSelectNombreProducto[indexTemp].row.nombre);
	$('#selectCodigoUnidadMedidaProducto').val(dataSelectNombreProducto[indexTemp].row.codigoUnidadMedida);
	$('#selectCodigoPresentacionProducto').val(dataSelectNombreProducto[indexTemp].row.codigoPresentacion);
	$('#dateFechaVencimientoProducto').val(dataSelectNombreProducto[indexTemp].row.fechaVencimiento=='1111-11-11' ? '' : dataSelectNombreProducto[indexTemp].row.fechaVencimiento);
	$('#txtPorcentajeGananciaProducto').val(porcentajeGananciaProductoTemp=='Infinity' ? 100 : porcentajeGananciaProductoTemp);
	$('input[name=radioVentaMenorUnidadProducto][value='+dataSelectNombreProducto[indexTemp].row.ventaMenorUnidad+']').prop('checked', true);
	$('#txtCantidadMinimaAlertaStockProducto').val(dataSelectNombreProducto[indexTemp].row.cantidadMinimaAlertaStock);
	$('#selectSituacionImpuestoProducto').val(dataSelectNombreProducto[indexTemp].row.situacionImpuesto);
	$('#selectTipoImpuestoProducto').val(dataSelectNombreProducto[indexTemp].row.tipoImpuesto);
	$('#txtPorcentajeTributacionProducto').val(dataSelectNombreProducto[indexTemp].row.porcentajeTributacion);
}

function onChangeSelectTipoRecibo()
{
	if($('#selectTipoRecibo').val()=='Ninguno')
	{
		$('#txtNumeroRecibo').attr('readonly', 'readonly');
		$('#txtNumeroRecibo').val(null);

		$('#txtNumeroGuiaRemision').attr('readonly', 'readonly');
		$('#txtNumeroGuiaRemision').val(null);
		
		$('#dateFechaComprobanteEmitido').attr('readonly', 'readonly');
		$('#dateFechaComprobanteEmitido').datepicker('remove');
		$('#dateFechaComprobanteEmitido').val(_currentDate);
	}
	else
	{
		$('#txtNumeroRecibo').removeAttr('readonly');

		$('#txtNumeroGuiaRemision').removeAttr('readonly');

		$('#dateFechaComprobanteEmitido').removeAttr('readonly');
		$('#dateFechaComprobanteEmitido').datepicker(
		{
			autoclose: true,
			format: 'yyyy-mm-dd'
		});
	}
}

function onChangeSelectTipoPago()
{
	if($('#selectTipoPago').val()=='Al contado')
	{			
		$('#dateFechaPagar').attr('readonly', 'readonly');
		$('#dateFechaPagar').datepicker('remove');
		$('#dateFechaPagar').val(_currentDate);
	}
	else
	{
		$('#dateFechaPagar').removeAttr('readonly');
		$('#dateFechaPagar').datepicker(
		{
			autoclose: true,
			format: 'yyyy-mm-dd'
		});
	}
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

	calcularPreciosImpuestos();
}

function restaurarDetalleMemoria()
{
	if(window.localStorage.detalleCompra!='undefined' && window.localStorage.detalleCompra!=null && window.localStorage.detalleCompra!='null' && window.localStorage.detalleCompra!='')
	{
		confirmacion(function()
		{
			$('#tableProducto').html(window.localStorage.detalleCompra);
		});
	}
}

function calcularPreciosImpuestos()
{
	var cantidadProducto=$('#txtCantidadProducto').val();
	var precioCompraTotalProducto=$('#txtPrecioCompraTotalProducto').val();

	var porcentajeTributacionProducto=$('#txtPorcentajeTributacionProducto').val();

	if(isNaN(porcentajeTributacionProducto) || isNaN(precioCompraTotalProducto) || porcentajeTributacionProducto<=0 || porcentajeTributacionProducto>=100)
	{
		$('#txtImpuestoAplicadoProducto').val(null);
	}
	else
	{
		var valorOperacionalImpuesto=((porcentajeTributacionProducto/100)+1).toFixed(2);
		var impuestoAplicado=Math.abs((precioCompraTotalProducto/valorOperacionalImpuesto)-precioCompraTotalProducto).toFixed(2);

		$('#txtImpuestoAplicadoProducto').val(impuestoAplicado);
	}

	if(isNaN(cantidadProducto) || isNaN(precioCompraTotalProducto) || cantidadProducto==0)
	{
		$('#txtPrecioCompraUnitarioProducto').val(null);
		$('#txtPrecioVentaUnitarioProducto').val(null);

		$('#modalTemp').data('formValidation').resetForm();
		$('#modalTemp').data('formValidation').validate();

		return;
	}

	var precioCompraUnitarioProducto=(precioCompraTotalProducto/cantidadProducto).toFixed(2);

	$('#txtPrecioCompraUnitarioProducto').val(precioCompraUnitarioProducto);

	var porcentajeGananciaProducto=$('#txtPorcentajeGananciaProducto').val();
	var precioVentaUnitarioProducto=$('#txtPrecioVentaUnitarioProducto').val();

	if(isNaN(porcentajeGananciaProducto))
	{
		$('#txtPrecioVentaUnitarioProducto').val(null);
	}

	if(isNaN(precioVentaUnitarioProducto))
	{
		$('#txtPorcentajeGananciaProducto').val(null);
	}

	if(!($('#txtPrecioVentaUnitarioProducto').is(':focus')) && !isNaN(porcentajeGananciaProducto) && $('#txtPrecioCompraUnitarioProducto').val()!='')
	{
		precioVentaUnitarioProducto=(parseFloat(precioCompraUnitarioProducto)+parseFloat((precioCompraUnitarioProducto*porcentajeGananciaProducto)/100)).toFixed(2);

		$('#txtPrecioVentaUnitarioProducto').val(precioVentaUnitarioProducto);
	}

	if($('#txtPrecioVentaUnitarioProducto').is(':focus') && !isNaN(precioVentaUnitarioProducto) && $('#txtPrecioCompraUnitarioProducto').val()!='')
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

	$('#modalTemp').data('formValidation').resetForm();
	$('#modalTemp').data('formValidation').validate();
}

function calcularPreciosImpuestosGenerado(cantidadProducto, precioCompraTotalProducto)
{
	var porcentajeTributacionProducto=$('#txtPorcentajeTributacionProducto').val();

	if(isNaN(porcentajeTributacionProducto) || isNaN(precioCompraTotalProducto) || porcentajeTributacionProducto<=0 || porcentajeTributacionProducto>=100)
	{
		return 0;
	}
	else
	{
		var valorOperacionalImpuesto=((porcentajeTributacionProducto/100)+1).toFixed(2);
		var impuestoAplicado=Math.abs((precioCompraTotalProducto/valorOperacionalImpuesto)-precioCompraTotalProducto).toFixed(2);

		return impuestoAplicado;
	}	
}

function calcularPreciosTotales()
{
	var totalProductoTemp=0;

	$('#tableProducto > tbody > tr').each(function(index, element)
	{
		if(!($(element).find('input[name^=hdNombreProducto]').length))
		{
			$(element).find('[class^=tdPrecioCompraTotalProducto]').text('S/'+totalProductoTemp.toFixed(2));

			return false;
		}

		totalProductoTemp+=parseFloat($(element).find('input[name^=hdPrecioCompraTotalProducto]').val());
	});
}

function quitarProducto(element)
{
	confirmacion(function()
	{
		$(element).parent().parent().remove();

		calcularPreciosTotales();
		
		window.localStorage.detalleCompra=($('#tableProducto > tbody > tr').length<=1 ? null : $('#tableProducto').html());
		
		notaOperacionCorrecta();
	});
}

function agregarProducto(codigoBarrasProducto, nombreProducto, codigoPresentacionProducto, codigoUnidadMedidaProducto, tipoProducto, situacionImpuestoProducto, tipoImpuestoProducto, porcentajeTributacionProducto, impuestoAplicadoProducto, cantidadMinimaAlertaStockProducto, pesoGramosUnidadProducto, precioCompraTotalProducto, precioCompraUnitarioProducto, precioVentaUnitarioProducto, cantidadProducto, ventaMenorUnidadProducto, fechaVencimientoProducto, registroEnSerie = 'false')
{
	var existeProducto=false;

	$('#tableProducto > tbody > tr').each(function(index, element)
	{
		if($(element).find('input[name^=hdNombreProducto]').length)
		{
			var codigoBarrasProductoTemp=$(element).find('input[name^=hdCodigoBarrasProducto]').val();
			var nombreProductoTemp=$(element).find('input[name^=hdNombreProducto]').val();
			
			codigoBarrasProductoTemp=codigoBarrasProductoTemp.viiReplaceAll(' ', '');
			nombreProductoTemp=nombreProductoTemp.viiReplaceAll(' ', '');

			if((codigoBarrasProducto.viiReplaceAll(' ', '')==codigoBarrasProductoTemp && codigoBarrasProducto.viiReplaceAll(' ', '')!='') || nombreProducto.viiReplaceAll(' ', '')==nombreProductoTemp)
			{
				existeProducto=true;

				return false;
			}
		}
	});

	if(existeProducto)
	{
		notaError('No se pudo proceder', 'El producto ya fue agregado a la lista.');

		return;
	}

	var htmlTemp='<tr class="elementoBuscar" ' + (registroEnSerie === 'true' ? 'style="background-color:#FFFC8F"' : '' ) + '>'
		+'<td style="display: none;">'
			+'<input type="hidden" name="hdCodigoPresentacionProducto[]" value="'+codigoPresentacionProducto.viiInjectionEscape()+'">'
			+'<input type="hidden" name="hdCodigoUnidadMedidaProducto[]" value="'+codigoUnidadMedidaProducto.viiInjectionEscape()+'">'
			+'<input type="hidden" name="hdCodigoBarrasProducto[]" value="'+codigoBarrasProducto.viiInjectionEscape()+'">'
			+'<input type="hidden" name="hdNombreProducto[]" value="'+nombreProducto.viiInjectionEscape()+'">'
			+'<input type="hidden" name="hdTipoProducto[]" value="'+tipoProducto.viiInjectionEscape()+'">'
			+'<input type="hidden" name="hdSituacionImpuestoProducto[]" value="'+situacionImpuestoProducto.viiInjectionEscape()+'">'
			+'<input type="hidden" name="hdTipoImpuestoProducto[]" value="'+tipoImpuestoProducto.viiInjectionEscape()+'">'
			+'<input type="hidden" name="hdPorcentajeTributacionProducto[]" value="'+porcentajeTributacionProducto.viiInjectionEscape()+'">'
			+'<input type="hidden" name="hdImpuestoAplicadoProducto[]" value="'+impuestoAplicadoProducto.viiInjectionEscape()+'">'
			+'<input type="hidden" name="hdCantidadMinimaAlertaStockProducto[]" value="'+cantidadMinimaAlertaStockProducto.viiInjectionEscape()+'">'
			+'<input type="hidden" name="hdPesoGramosUnidadProducto[]" value="'+pesoGramosUnidadProducto.viiInjectionEscape()+'">'
			+'<input type="hidden" name="hdPrecioCompraTotalProducto[]" value="'+precioCompraTotalProducto.viiInjectionEscape()+'">'
			+'<input type="hidden" name="hdPrecioCompraUnitarioProducto[]" value="'+precioCompraUnitarioProducto.viiInjectionEscape()+'">'
			+'<input type="hidden" name="hdPrecioVentaUnitarioProducto[]" value="'+precioVentaUnitarioProducto.viiInjectionEscape()+'">'
			+'<input type="hidden" name="hdCantidadProducto[]" value="'+cantidadProducto.viiInjectionEscape()+'">'
			+'<input type="hidden" name="hdVentaMenorUnidadProducto[]" value="'+ventaMenorUnidadProducto.viiInjectionEscape()+'">'
			+'<input type="hidden" name="hdFechaVencimientoProducto[]" value="'+fechaVencimientoProducto.viiInjectionEscape()+'">'
			+'<input type="hidden" name="hdRegistroSerieProducto[]" value="'+registroEnSerie+'">'
		+'</td>'
		+'<td class="text-center">'+codigoBarrasProducto.viiInjectionEscape()+'</td>'
		+'<td>'+nombreProducto.viiInjectionEscape()+'</td>'
		+'<td class="text-center">'+situacionImpuestoProducto.viiInjectionEscape()+'</td>'
		+'<td class="text-center">'+cantidadProducto.viiInjectionEscape()+'</td>'
		+'<td class="text-center">S/'+parseFloat(precioVentaUnitarioProducto.viiInjectionEscape()).toFixed(2)+'</td>'
		+'<td class="text-center">S/'+parseFloat(impuestoAplicadoProducto.viiInjectionEscape()).toFixed(2)+'</td>'
		+'<td class="text-center">S/'+parseFloat(precioCompraTotalProducto.viiInjectionEscape()).toFixed(2)+'</td>'
		+'<td class="text-right">'
			+'<span class="btn btn-default btn-xs glyphicon glyphicon-trash" data-toggle="tooltip" data-placement="left" title="Quitar de la tabla" onclick="quitarProducto(this);" style="margin: 1px;"></span>'
		+'</td>'
	+'</tr>';

	$('#tableProducto > tbody > tr:last').before(htmlTemp);

	calcularPreciosTotales();

	$('[data-toggle="tooltip"]').tooltip();

	return true;
}

function agregarProductoDetalleCompra()
{
	var isValid=true;

	$('#modalTemp').data('formValidation').resetForm();
	$('#modalTemp').data('formValidation').validate();

	isValid=!isValid ? isValid : $('#modalTemp').data('formValidation').isValidField('txtNombreProducto');
	isValid=!isValid ? isValid : $('#modalTemp').data('formValidation').isValidField('txtCantidadProducto');
	isValid=!isValid ? isValid : $('#modalTemp').data('formValidation').isValidField('txtPrecioCompraTotalProducto');
	isValid=!isValid ? isValid : $('#modalTemp').data('formValidation').isValidField('txtPrecioVentaUnitarioProducto');
	isValid=!isValid ? isValid : $('#modalTemp').data('formValidation').isValidField('txtCantidadMinimaAlertaStockProducto');
	isValid=!isValid ? isValid : $('#modalTemp').data('formValidation').isValidField('txtPesoGramosUnidadProducto');
	isValid=!isValid ? isValid : $('#modalTemp').data('formValidation').isValidField('txtPorcentajeTributacionProducto');

	if(!isValid)
	{
		notaDatosIncorrectos();

		return;
	}

	var productoAgregado=false;
	
	if($('#generacionCodigoBarras').is(':checked'))
	{
		if(!/^([0-9])*$/.test($('#txtCodigoBarrasProducto').val())) 
		{
			notaError("Código de barras incorrecto", "El código de barras no puede generarse. Para generar el código de barras, éste sólo debe ser números.");

			return;
		}

		productoAgregado=agregarProducto(
			$('#txtCodigoBarrasProducto').val(),
			$('#txtNombreProducto').val(),
			$('#selectCodigoPresentacionProducto').val(),
			$('#selectCodigoUnidadMedidaProducto').val(),
			$('#selectTipoProducto').val(),
			$('#selectSituacionImpuestoProducto').val(),
			$('#selectTipoImpuestoProducto').val(),
			$('#txtPorcentajeTributacionProducto').val(),
			$('#txtImpuestoAplicadoProducto').val(),
			$('#txtCantidadMinimaAlertaStockProducto').val(),
			$('#txtPesoGramosUnidadProducto').val(),
			$('#txtPrecioCompraTotalProducto').val(),
			$('#txtPrecioCompraUnitarioProducto').val(),
			$('#txtPrecioVentaUnitarioProducto').val(),
			$('#txtCantidadProducto').val(),
			$('input[name=radioVentaMenorUnidadProducto]:checked').val(),
			$('#dateFechaVencimientoProducto').val(),
			'true'
		);
	}
	else
	{
		productoAgregado=agregarProducto(
			$('#txtCodigoBarrasProducto').val(),
			$('#txtNombreProducto').val(),
			$('#selectCodigoPresentacionProducto').val(),
			$('#selectCodigoUnidadMedidaProducto').val(),
			$('#selectTipoProducto').val(),
			$('#selectSituacionImpuestoProducto').val(),
			$('#selectTipoImpuestoProducto').val(),
			$('#txtPorcentajeTributacionProducto').val(),
			$('#txtImpuestoAplicadoProducto').val(),
			$('#txtCantidadMinimaAlertaStockProducto').val(),
			$('#txtPesoGramosUnidadProducto').val(),
			$('#txtPrecioCompraTotalProducto').val(),
			$('#txtPrecioCompraUnitarioProducto').val(),
			$('#txtPrecioVentaUnitarioProducto').val(),
			$('#txtCantidadProducto').val(),
			$('input[name=radioVentaMenorUnidadProducto]:checked').val(),
			$('#dateFechaVencimientoProducto').val()
		);
	}	
	
	$('#generacionCodigoBarras').prop('checked', false);

	clearInputText('modalTemp', ['txtPorcentajeGananciaProducto', 'txtCantidadMinimaAlertaStockProducto', 'txtPesoGramosUnidadProducto', 'txtPorcentajeTributacionProducto']);
	$('#selectNombreProducto').val(null).trigger('change');

	$('#modalTemp').data('formValidation').resetForm();

	if(productoAgregado)
	{
		window.localStorage.detalleCompra=$('#tableProducto').html();
		
		notaOperacionCorrecta();
	}
}

function enviarFrmInsertarReciboCompra()
{
	var isValid=null;

	if($('#tableProducto > tbody > tr').length<=1)
	{
		notaError('No se pudo proceder', 'Debe agregar por lo menos un producto al detalle de la compra.');

		return;
	}

	$('#frmInsertarReciboCompra').data('formValidation').validate();

	isValid=$('#frmInsertarReciboCompra').data('formValidation').isValid();

	if(!isValid)
	{
		notaDatosIncorrectos();

		return;
	}

	confirmacionEnvio('frmInsertarReciboCompra');
}