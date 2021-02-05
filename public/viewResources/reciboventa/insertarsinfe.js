'use strict';

$(function()
{
	if(sessionCodigoReciboVentaTemp!='undefined')
	{
		window.open(_urlBase+'/reciboventa/imprimircomprobantesinfe/'+sessionCodigoReciboVentaTemp, '_blank');
	}

	$('#frmInsertarReciboVenta').formValidation(
	{
		framework: 'bootstrap',
		excluded: [':disabled', ':hidden', ':not(:visible)', '[class*="notValidate"]'],
		live: 'enabled',
		message: '<b style="color: #9d9d9d;">Asegúrese que realmente no necesita este valor.</b>',
		trigger: null,
		fields:
		{
			txtDniCliente:
			{
				validators:
				{
					regexp:
					{
						message: '<b style="color: red;">Formato incorrecto. [Ejemplo: 12345678].</b>',
						regexp: /^[0-9]{8}$/
					}
				}
			},
			txtNombreCliente:
			{
				validators: 
				{
					regexp:
					{
						message: '<b style="color: red;">Sólo letras, números, comas, puntos, guiones o subguiones.</b>',
						regexp: /^[a-zA-Z0-9\.\-\_\u00C0-\u00FF\s]*$/
					}
				}
			},
			txtApellidoCliente:
			{
				validators: 
				{
					regexp:
					{
						message: '<b style="color: red;">Sólo letras, números, comas, puntos, guiones o subguiones.</b>',
						regexp: /^[a-zA-Z0-9\.\-\_\u00C0-\u00FF\s]*$/
					}
				}
			},
			txtDireccionCliente:
			{
				validators: 
				{
					regexp:
					{
						message: '<b style="color: red;">Sólo letras, números, comas, puntos, guiones o subguiones.</b>',
						regexp: /^[a-zA-Z0-9\.\-\_\u00C0-\u00FF\s]+$/
					}
				}
			},
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
			selectRazonSocialEmpresa:
			{
				validators: 
				{
					notEmpty:
					{
						message: '<b style="color: red;">Este campo es requerido.</b>'
					},
					regexp:
					{
						message: '<b style="color: red;">Sólo letras, números, comas, puntos, guiones o subguiones.</b>',
						regexp: /^[a-zA-Z0-9\.\-\_\u00C0-\u00FF\s]+$/
					}
				}
			},
			txtDireccionEmpresa:
			{
				validators: 
				{
					notEmpty:
					{
						message: '<b style="color: red;">Este campo es requerido.</b>'
					},
					regexp:
					{
						message: '<b style="color: red;">Sólo letras, números, comas, puntos, guiones o subguiones.</b>',
						regexp: /^[a-zA-Z0-9\.\-\_\u00C0-\u00FF\s]+$/
					}
				}
			},
			txtLetras:
			{
				validators: 
				{
					notEmpty:
					{
						message: '<b style="color: red;">Este campo es requerido.</b>'
					},
					regexp:
					{
						message: '<b style="color: red;">Formato incorrecto. [Ejemplo: 1, 2, 3...].</b>',
						regexp: /^[0-9]+$/
					}
				}
			},
			dateFechaPrimerPago:
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

	$('#modalProductoExterno').formValidation(
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

	$('#selectRazonSocialEmpresa').select2(
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
		placeholder: 'Buscar empresa...',
		minimumInputLength: 3,
		tags: true,
		ajax:
		{
			url: _urlBase+'/clientejuridico/jsonporrazonsociallargaparaventa',
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
				dataSelectRazonSocialEmpresa=data.items;

				return {
					results: data.items
				};
			},
			cache: false
		}
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
		placeholder: 'Buscar producto...',
		minimumInputLength: 3,
		ajax:
		{
			url: _urlBase+'/oficinaproducto/jsonporcodigobarrasnombre',
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

				var searchTerm=$('#selectNombreProducto').data('select2').$dropdown.find('input').val();

				if(data.items.length==1 && data.items[0].row.codigoBarras==searchTerm)
				{
					$('#selectNombreProducto').append($('<option/>')
						.attr('value', data.items[0].id)
						.html(data.items[0].text)
					).val(data.items[0].id).trigger('change').select2('close');
				}

				return {
					results: data.items
				};
			},
			cache: false
		},
		escapeMarkup : function(markup)
		{
			return markup;
		},
		templateResult : formatRepo,
		templateSelection : formatRepoSelection
	});

	if(localStorage.autoCalculoCantidadPrecioVenta!=null && localStorage.autoCalculoCantidadPrecioVenta==='true')
	{
		$('#cbxAutoCalculoCantidadPrecioVenta').prop('checked', true);
	}

	$('.option'+$('#selectCategoriaVentaNivelUno').val()).show();
	$('.option'+$('#selectCategoriaVentaNivelDos').val()).show();

	if(oldData.length>0)
	{
		onChangeSelectTipoRecibo();
		onChangeSelectTipoPago();

		$(oldData).each(function(index, element)
		{
			agregarProducto(
				false,
				element.codigoOficinaProducto,
				element.codigoBarrasProducto,
				element.nombreProducto,
				element.informacionAdicionalProducto,
				element.tipoProducto,
				element.situacionImpuestoProducto,
				element.tipoImpuestoProducto,
				element.porcentajeTributacionProducto,
				element.presentacionProducto,
				element.unidadMedidaProducto,
				element.pesoGramosUnidadProducto,
				element.precioCompraUnitarioProducto,
				element.precioVentaUnitarioProducto,
				element.cantidadProducto,
				element.subTotalProducto,
				element.impuestoAplicadoProducto,
				element.precioVentaTotalProducto
			);
		});
	}
});

function formatRepo(repo)
{
	if(repo.loading)
	{
		return repo.text;
	}

	var indexTemp=null;

	$(dataSelectNombreProducto).each(function(index, element)
	{
		if(element.row.codigoOficinaProducto==repo.id)
		{
			indexTemp=index;

			return false;
		}
	});

	var markup='<div class="select2-result-repository clearfix">'
		+'<table style="width: 100%;">'
			+'<tbody>'
				+'<tr>'
					+'<td style="border: 1px dotted #594444;padding: 4px;width: 30px;text-align: center;"><span class="fa fa-tag"></span></td>'
					+'<td style="border: 1px dotted #594444;padding: 4px;">'+dataSelectNombreProducto[indexTemp].row.nombre.viiInjectionEscape()+'</td>'
					+'<td style="border: 1px dotted #594444;padding: 4px;width: 80px;text-align: center;'+(dataSelectNombreProducto[indexTemp].row.cantidad==0 ? 'color: #d6993e;font-weight: bold;text-shadow: 0px 0px 2px #000000;' : '')+'">'+dataSelectNombreProducto[indexTemp].row.cantidad+'</td>'
					+'<td style="border: 1px dotted #594444;font-size: 11px;padding: 4px;width: 120px;text-align: center;">'+dataSelectNombreProducto[indexTemp].row.unidadMedida+'</td>'
					+'<td style="border: 1px dotted #594444;padding: 4px;width: 110px;text-align: center;"><b>S/'+dataSelectNombreProducto[indexTemp].row.precioVentaUnitario+'</b></td>'
				+'</tr>'
			+'</tbody>'
		+'</table>'
	+'</div>';

	return markup;
}

function formatRepoSelection(repo)
{
	return repo.text;
}

function onChangeSelectCategoriaVenta(selectChange)
{
	if(selectChange=='selectCategoriaVentaNivelUno')
	{
		$('#selectCategoriaVentaNivelDos > option').hide();
		$('#selectCategoriaVentaNivelTres > option').hide();

		$('.optionClearCategoriaVenta').show();
		$('.option'+$('#selectCategoriaVentaNivelUno').val()).show();
		
		$('#selectCategoriaVentaNivelDos').val(null);
		$('#selectCategoriaVentaNivelTres').val(null);
	}
	else
	{
		$('#selectCategoriaVentaNivelTres > option').hide();

		$('.optionClearCategoriaVenta').show();
		$('.option'+$('#selectCategoriaVentaNivelDos').val()).show();

		$('#selectCategoriaVentaNivelTres').val(null);
	}
}

function agregarProducto(isntLoad, codigoOficinaProducto, codigoBarrasProducto, nombreProducto, informacionAdicionalProducto, tipoProducto, situacionImpuestoProducto, tipoImpuestoProducto, porcentajeTributacionProducto, presentacionProducto, unidadMedidaProducto, pesoGramosUnidadProducto, precioCompraUnitarioProducto, precioVentaUnitarioProducto, cantidadProducto, subTotalProducto, impuestoAplicadoProducto, precioVentaTotalProducto)
{
	var existeProducto=false;

	$('#tableProducto > tbody > tr').each(function(index, element)
	{
		if($(element).find('input[name^=hdNombreProducto]').length)
		{
			var nombreProductoTemp=$(element).find('input[name^=hdNombreProducto]').val().viiInjectionEscape();

			if(nombreProducto.viiInjectionEscape().viiReplaceAll(' ', '')==nombreProductoTemp.viiReplaceAll(' ', ''))
			{
				existeProducto=true;

				return false;
			}
		}
	});

	if(existeProducto)
	{
		notaError('No se pudo proceder', 'El producto ya existe en la lista.');

		return false;
	}

	var htmlTemp='<tr>'
		+'<td style="display: none;">'
			+'<input type="hidden" name="hdCodigoOficinaProducto[]" value="'+codigoOficinaProducto+'">'
			+'<input type="hidden" name="hdCodigoBarrasProducto[]" value="'+codigoBarrasProducto.viiInjectionEscape()+'">'
			+'<input type="hidden" name="hdNombreProducto[]" value="'+nombreProducto.trim().viiInjectionEscape()+'">'
			+'<input type="hidden" name="hdInformacionAdicionalProducto[]" value="'+informacionAdicionalProducto.trim().viiInjectionEscape()+'">'
			+'<input type="hidden" name="hdTipoProducto[]" value="'+tipoProducto.viiInjectionEscape()+'">'
			+'<input type="hidden" name="hdSituacionImpuestoProducto[]" value="'+situacionImpuestoProducto.viiInjectionEscape()+'">'
			+'<input type="hidden" name="hdTipoImpuestoProducto[]" value="'+tipoImpuestoProducto.viiInjectionEscape()+'">'
			+'<input type="hidden" name="hdPorcentajeTributacionProducto[]" value="'+porcentajeTributacionProducto+'">'
			+'<input type="hidden" name="hdPresentacionProducto[]" value="'+presentacionProducto.viiInjectionEscape()+'">'
			+'<input type="hidden" name="hdUnidadMedidaProducto[]" value="'+unidadMedidaProducto.viiInjectionEscape()+'">'
			+'<input type="hidden" name="hdPesoGramosUnidadProducto[]" value="'+pesoGramosUnidadProducto+'">'
			+'<input type="hidden" name="hdPrecioCompraUnitarioProducto[]" value="'+precioCompraUnitarioProducto+'">'
			+'<input type="hidden" name="hdPrecioVentaUnitarioProducto[]" value="'+precioVentaUnitarioProducto+'">'
			+'<input type="hidden" name="hdCantidadProducto[]" value="'+cantidadProducto+'">'
			+'<input type="hidden" name="hdSubTotalProducto[]" value="'+subTotalProducto+'">'
			+'<input type="hidden" name="hdImpuestoAplicadoProducto[]" value="'+impuestoAplicadoProducto+'">'
			+'<input type="hidden" name="hdPrecioVentaTotalProducto[]" value="'+precioVentaTotalProducto+'">'
		+'</td>'
		+'<td class="text-center"><span class="'+(codigoOficinaProducto>=900000000000001 ? 'fa fa-circle-o' : 'fa fa-tag')+'"></span></td>'
		+'<td class="text-left">'+nombreProducto.trim().viiInjectionEscape()+'</td>'
		+'<td class="tdInformacionAdicionalProducto text-left tdEditable" contenteditable onkeyup="onKeyUpTdInformacionAdicionalProducto(this);">'+informacionAdicionalProducto.trim().viiInjectionEscape()+'</td>'
		+'<td class="text-center">'+presentacionProducto.viiInjectionEscape()+'</td>'
		+'<td class="text-center">'+unidadMedidaProducto.viiInjectionEscape()+'</td>'
		+'<td class="text-center">'+(precioCompraUnitarioProducto!='---' ? 'S/' : '')+precioCompraUnitarioProducto+'</td>'
		+'<td class="tdPrecioVentaUnitarioProducto text-center">S/'+precioVentaUnitarioProducto+'</td>'
		+'<td class="tdCantidadProducto text-center tdEditable" contenteditable onkeyup="onKeyUpTdCantidadProducto(this);">'+cantidadProducto+'</td>'
		+'<td class="tdSubTotalProducto text-center">'+subTotalProducto+'</td>'
		+'<td class="tdImpuestoAplicadoProducto text-center">'+impuestoAplicadoProducto+'</td>'
		+'<td class="tdPrecioVentaTotalProducto text-center tdEditable" contenteditable onkeyup="onKeyUpTdPrecioVentaTotalProducto(this);" onfocus="onFocusTdPrecioVentaTotalProducto(this);" onblur="onBlurTdPrecioVentaTotalProducto(this);">S/'+precioVentaTotalProducto+'</td>'
		+'<td class="tdPorcentajeGanancia text-center" style="position: relative;">'
			+'<span class="globoPorcentajeGanancia">'
				+'<span></span>'
				+'<b class="tdPorcentajeGananciaValue">0%</b>'
			+'</span>'
		+'</td>'
		+'<td class="text-right">'
			+'<span class="btn btn-default btn-xs glyphicon glyphicon-trash" data-toggle="tooltip" data-placement="left" title="Quitar de la tabla" onclick="quitarProducto(this);" style="margin: 1px;"></span>'
		+'</td>'
	+'</tr>';

	$('#tableProducto > tbody > tr:last').before(htmlTemp);

	calcularPreciosItem($('#tableProducto > tbody > tr')[$('#tableProducto > tbody > tr').length-2], isntLoad, porcentajeTributacionProducto);

	calcularPreciosTotales();

	$('[data-toggle="tooltip"]').tooltip();

	return true;
}

var dataSelectNombreProducto=[];
var dataSelectRazonSocialEmpresa=[];

function onChangeSelectRazonSocialEmpresa()
{
	var selectRazonSocialEmpresaTemp=$('#selectRazonSocialEmpresa').val();
	var indexTemp=null;

	$(dataSelectRazonSocialEmpresa).each(function(index, element)
	{
		if((typeof element.row)!='undefined' && element.row.razonSocialLarga==selectRazonSocialEmpresaTemp)
		{
			indexTemp=index;

			return false;
		}
	});

	if(indexTemp!=null)
	{
		$('#txtRucEmpresa').val(dataSelectRazonSocialEmpresa[indexTemp].row.ruc);
		$('#txtDireccionEmpresa').val(dataSelectRazonSocialEmpresa[indexTemp].row.direccion);
	}
}

function onChangeSelectNombreProducto()
{
	if($('#selectNombreProducto').val()==null || $('#selectNombreProducto').val()=='')
	{
		return;
	}

	var codigoOficinaProductoTemp=$('#selectNombreProducto').val();
	var indexTemp=null;

	$(dataSelectNombreProducto).each(function(index, element)
	{
		if(element.row.codigoOficinaProducto==codigoOficinaProductoTemp)
		{
			indexTemp=index;

			return false;
		}
	});

	var productoAgregado=agregarProducto(
		true,
		dataSelectNombreProducto[indexTemp].row.codigoOficinaProducto,
		dataSelectNombreProducto[indexTemp].row.codigoBarras,
		dataSelectNombreProducto[indexTemp].row.nombre,
		'',
		dataSelectNombreProducto[indexTemp].row.tipo,
		dataSelectNombreProducto[indexTemp].row.situacionImpuesto,
		dataSelectNombreProducto[indexTemp].row.tipoImpuesto,
		dataSelectNombreProducto[indexTemp].row.porcentajeTributacion,
		dataSelectNombreProducto[indexTemp].row.presentacion,
		dataSelectNombreProducto[indexTemp].row.unidadMedida,
		dataSelectNombreProducto[indexTemp].row.pesoGramosUnidad,
		dataSelectNombreProducto[indexTemp].row.precioCompraUnitario,
		dataSelectNombreProducto[indexTemp].row.precioVentaUnitario,
		1,
		0,
		0,
		0
	);

	$('#selectNombreProducto').val(null).trigger('change');

	window.setTimeout(function()
	{
		$('#selectNombreProducto').select2('open');
	}, 50);

	if(productoAgregado)
	{
		notaOperacionCorrecta();
	}
}

function onChangeCbxAutoCalculoCantidadPrecioVenta()
{
	localStorage.autoCalculoCantidadPrecioVenta=$('#cbxAutoCalculoCantidadPrecioVenta').is(':checked');
}

function onKeyUpTdInformacionAdicionalProducto(element)
{
	$(element).parent().find('input[name^=hdInformacionAdicionalProducto]').val($(element).text());
}

function onKeyUpTdCantidadProducto(element)
{
	$(element).parent().find('input[name^=hdCantidadProducto]').val($(element).text());
	
	calcularPreciosItem($(element).parent(), true, $(element).parent().find('input[name^=hdPorcentajeTributacionProducto]').val());

	calcularPreciosTotales();
}

function onKeyUpTdPrecioVentaTotalProducto(element)
{
	$(element).parent().find('input[name^=hdPrecioVentaTotalProducto]').val((!isNaN($(element).text()) ? parseFloat($(element).text()).toFixed(2) : $(element).text()));

	calcularPreciosItem($(element).parent(), false, $(element).parent().find('input[name^=hdPorcentajeTributacionProducto]').val());

	calcularPreciosTotales();
}

function onFocusTdPrecioVentaTotalProducto(element)
{
	$(element).text($(element).text().viiReplaceAll('S/', ''));
}

function onBlurTdPrecioVentaTotalProducto(element)
{
	$(element).text('S/'+(!isNaN($(element).text()) ? parseFloat($(element).text()).toFixed(2) : $(element).text()));
	
	calcularPreciosItem($(element).parent(), false, $(element).parent().find('input[name^=hdPorcentajeTributacionProducto]').val());

	calcularPreciosTotales();
}

function calcularPreciosItem(element, parametroCantidad, porcentajeTributacion)
{
	var cantidadProductoTemp=$(element).find('input[name^=hdCantidadProducto]').val();
	var precioVentaUnitarioProductoTemp=$(element).find('input[name^=hdPrecioVentaUnitarioProducto]').val();
	var precioVentaTotalProductoTemp=$(element).find('input[name^=hdPrecioVentaTotalProducto]').val();

	cantidadProductoTemp=isNaN(cantidadProductoTemp) ? 0 : cantidadProductoTemp;
	precioVentaTotalProductoTemp=isNaN(precioVentaTotalProductoTemp) ? 0 : precioVentaTotalProductoTemp;
	
	var totalCalculado=(parametroCantidad ? (cantidadProductoTemp*precioVentaUnitarioProductoTemp).toFixed(2) : precioVentaTotalProductoTemp);

	if(parametroCantidad)
	{
		var subTotalCalculado=(totalCalculado/((parseFloat(porcentajeTributacion)/100)+1)).toFixed(2);
		var impuestoCalculado=(totalCalculado-subTotalCalculado).toFixed(2);

		$(element).find('[class^=tdSubTotalProducto]').text('S/'+subTotalCalculado);
		$(element).find('[class^=tdImpuestoAplicadoProducto]').text('S/'+impuestoCalculado);
		$(element).find('[class^=tdPrecioVentaTotalProducto]').text('S/'+totalCalculado);

		$(element).find('input[name^=hdSubTotalProducto]').val(subTotalCalculado);
		$(element).find('input[name^=hdImpuestoAplicadoProducto]').val(impuestoCalculado);
		$(element).find('input[name^=hdPrecioVentaTotalProducto]').val(totalCalculado);
	}
	else
	{
		var subTotalCalculado=(precioVentaTotalProductoTemp/((parseFloat(porcentajeTributacion)/100)+1)).toFixed(2);
		var impuestoCalculado=(precioVentaTotalProductoTemp-subTotalCalculado).toFixed(2);
		var cantidadCalculado=(precioVentaTotalProductoTemp/precioVentaUnitarioProductoTemp).toFixed(10);

		$(element).find('[class^=tdSubTotalProducto]').text('S/'+subTotalCalculado);
		$(element).find('[class^=tdImpuestoAplicadoProducto]').text('S/'+impuestoCalculado);

		$(element).find('input[name^=hdSubTotalProducto]').val(subTotalCalculado);
		$(element).find('input[name^=hdImpuestoAplicadoProducto]').val(impuestoCalculado);
		
		if(localStorage.autoCalculoCantidadPrecioVenta==='true')
		{
			$(element).find('[class^=tdCantidadProducto]').text(cantidadCalculado);
			$(element).find('input[name^=hdCantidadProducto]').val(cantidadCalculado);
			cantidadProductoTemp=cantidadCalculado;
		}
	}

	var precioCompraUnitarioTemp=$(element).find('input[name^=hdPrecioCompraUnitarioProducto]').val();

	if(isNaN(precioCompraUnitarioTemp) || precioCompraUnitarioTemp==0)
	{
		$(element).find('.tdPorcentajeGananciaValue').text('100%');
	}
	else
	{
		$(element).find('.tdPorcentajeGananciaValue').text(((((totalCalculado/(cantidadProductoTemp==0 ? 1 : cantidadProductoTemp))-precioCompraUnitarioTemp)*100)/precioCompraUnitarioTemp).toFixed(1)+'%');
	}

	$(element).find('.tdPrecioVentaUnitarioProducto').text('S/'+(totalCalculado/(cantidadProductoTemp==0 ? 1 : cantidadProductoTemp)).toFixed(2));
}

function calcularPreciosTotales()
{
	var totalProductoTemp=0;
	var valorIncorrecto=false;

	$('#tableProducto > tbody > tr').each(function(index, element)
	{
		if(!($(element).find('input[name^=hdNombreProducto]').length))
		{
			var subTotalProductoTemp=(totalProductoTemp.toFixed(2)/((_porcentajeIgv/100)+1)).toFixed(2);

			$('#hdSubTotal').val(valorIncorrecto ? '0.00' : subTotalProductoTemp);
			$('#hdImpuestoAplicado').val(valorIncorrecto ? '0.00' : (totalProductoTemp.toFixed(2)-subTotalProductoTemp).toFixed(2));
			$('#hdTotal').val(valorIncorrecto ? '0.00' : totalProductoTemp.toFixed(2));

			$(element).find('[class^=tdSubTotalProducto]').text('S/'+$('#hdSubTotal').val());
			$(element).find('[class^=tdImpuestoAplicadoProducto]').text('S/'+$('#hdImpuestoAplicado').val());
			$(element).find('[class^=tdPrecioVentaTotalProducto]').text('S/'+$('#hdTotal').val());

			return false;
		}

		if(isNaN($(element).find('input[name^=hdPrecioVentaTotalProducto]').val()))
		{
			valorIncorrecto=true;
		}

		totalProductoTemp+=parseFloat($(element).find('input[name^=hdPrecioVentaTotalProducto]').val());
	});
}

function quitarProducto(element)
{
	confirmacion(function()
	{
		$(element).parent().parent().remove();

		calcularPreciosTotales();
		
		notaOperacionCorrecta();
	});
}

function onChangeSelectTipoRecibo()
{
	if($('#selectTipoRecibo').val()=='Boleta')
	{
		$('#divClienteNatural').show();
		$('#divClienteJuridico').hide();
		$('#btnGuiaRemision').attr('disabled', true);
	}
	else
	{
		$('#divClienteNatural').hide();
		$('#divClienteJuridico').show();
		$('#btnGuiaRemision').removeAttr('disabled');
	}
}

function onBlurTxtDniCliente()
{
	var isValid=true;

	$('#frmInsertarReciboVenta').data('formValidation').validateField('txtDniCliente');

	isValid=(!isValid ? false : $('#frmInsertarReciboVenta').data('formValidation').isValidField('txtDniCliente'));

	if(!isValid || $('#txtDniCliente').val().viiReplaceAll(' ', '')=='')
	{
		return;
	}

	paginaAjaxJSON({ _token: _token, dni: $('#txtDniCliente').val() }, _urlBase+'/clientenatural/jsonpordni', 'POST', null, function(objectJSON)
	{
		if(objectJSON.error)
		{
			notaError('No se pudo proceder', objectJSON.mensajeGlobal);

			return false;
		}

		if(typeof objectJSON.codigoClienteNatural!='undefined')
		{
			$('#txtNombreCliente').val(objectJSON.nombre.viiInjectionEscape());
			$('#txtApellidoCliente').val((objectJSON.apellido).viiInjectionEscape());
			$('#txtDireccionCliente').val(objectJSON.direccion.viiInjectionEscape());
		}
		else
		{
			$('#modalLoading').modal('show');

			var formDataTemp=new FormData();

			formDataTemp.append("token", "95bdc874-0ba5-49c5-8b46-10b6f593ee32-93dfe044-e731-4d1d-a8e4-ee238a0b8389");
			formDataTemp.append("dni", $('#txtDniCliente').val());

			var requestTemp=new XMLHttpRequest();

			requestTemp.open("POST", "https://api.migoperu.pe/api/v1/dni");

			requestTemp.setRequestHeader("Accept", "application/json");

			requestTemp.send(formDataTemp);

			requestTemp.onload=function()
			{
				var dataTemp=JSON.parse(this.response);

				if(!dataTemp.success)
				{
					return false;
				}

				if(dataTemp.dni!=undefined)
				{
					$('#txtNombreCliente').val(dataTemp.nombre.viiInjectionEscape());
					$('#txtApellidoCliente').val('.');
					$('#txtDireccionCliente').val(dataTemp.direccion!=undefined ? dataTemp.direccion.viiInjectionEscape() : '');
				}

				$('#modalLoading').modal('hide');
			};

			requestTemp.onreadystatechange=function(oEvent)
			{
				if(requestTemp.readyState===4)
				{
					if(requestTemp.status!==200)
					{
						$('#modalLoading').modal('hide');
					}
				}  
			};
		}
	}, false, true);
}

function onBlurTxtRucEmpresa()
{
	var isValid=true;

	$('#frmInsertarReciboVenta').data('formValidation').validateField('txtRucEmpresa');

	isValid=(!isValid ? false : $('#frmInsertarReciboVenta').data('formValidation').isValidField('txtRucEmpresa'));

	if(!isValid || $('#txtRucEmpresa').val().viiReplaceAll(' ', '')=='')
	{
		return;
	}

	paginaAjaxJSON({ _token: _token, ruc: $('#txtRucEmpresa').val() }, _urlBase+'/clientejuridico/jsonporruc', 'POST', null, function(objectJSON)
	{
		if(objectJSON.error)
		{
			notaError('No se pudo proceder', objectJSON.mensajeGlobal);

			return false;
		}

		if(typeof objectJSON.codigoClienteJuridico!='undefined')
		{
			$('#selectRazonSocialEmpresa').append('<option value="'+objectJSON.razonSocialLarga.viiInjectionEscape()+'" selected>'+objectJSON.razonSocialLarga.viiInjectionEscape()+'</option>');
			$('#txtDireccionEmpresa').val(objectJSON.direccion.viiInjectionEscape());

			$('#frmInsertarReciboVenta').data('formValidation').resetField('selectRazonSocialEmpresa');
			$('#frmInsertarReciboVenta').data('formValidation').resetField('txtDireccionEmpresa');
		}
		else
		{
			$('#modalLoading').modal('show');

			var formDataTemp=new FormData();

			formDataTemp.append("token", "95bdc874-0ba5-49c5-8b46-10b6f593ee32-93dfe044-e731-4d1d-a8e4-ee238a0b8389");
			formDataTemp.append("ruc", $('#txtRucEmpresa').val());

			var requestTemp=new XMLHttpRequest();

			requestTemp.open("POST", "https://api.migoperu.pe/api/v1/ruc");

			requestTemp.setRequestHeader("Accept", "application/json");

			requestTemp.send(formDataTemp);

			requestTemp.onload=function()
			{
				$('#modalLoading').modal('hide');

				var dataTemp=JSON.parse(this.response);

				if(!dataTemp.success)
				{
					return false;
				}

				if(dataTemp.ruc!=undefined)
				{
					$('#selectRazonSocialEmpresa').append('<option value="'+dataTemp.nombre_o_razon_social.viiInjectionEscape()+'" selected>'+dataTemp.nombre_o_razon_social.viiInjectionEscape()+'</option>');
					$('#txtDireccionEmpresa').val(dataTemp.direccion!=undefined ? dataTemp.direccion.viiInjectionEscape() : '');

					$('#frmInsertarReciboVenta').data('formValidation').resetField('selectRazonSocialEmpresa');
					$('#frmInsertarReciboVenta').data('formValidation').resetField('txtDireccionEmpresa');
				}
			};

			requestTemp.onreadystatechange=function(oEvent)
			{
				if(requestTemp.readyState===4)
				{
					if(requestTemp.status!==200)
					{
						$('#modalLoading').modal('hide');
					}
				}  
			};
		}
	}, false, true);
}

function onChangeSelectTipoPago()
{
	if($('#selectTipoPago').val()=='Al crédito')
	{
		$('#dateFechaPrimerPago').removeAttr('readonly');
		$('#dateFechaPrimerPago').datepicker(
		{
			autoclose: true,
			format: "yyyy-mm-dd",
			orientation: "bottom auto"
		});
		$('#txtLetras').removeAttr('readonly');
		$('#selectPagoAutomatico').removeAttr('disabled');
	}
	else
	{
		$('#dateFechaPrimerPago').attr('readonly', 'readonly');
		$('#dateFechaPrimerPago').datepicker('remove');
		$('#dateFechaPrimerPago').val(_currentDate);
		$('#txtLetras').attr('readonly', 'readonly');
		$('#txtLetras').val(null);
		$('#frmInsertarReciboVenta').data('formValidation').resetField('txtLetras');
		$('#selectPagoAutomatico').attr('disabled', true);
		$('#selectPagoAutomatico').val('Primer día laboral del mes');
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

function calcularPreciosImpuestos()
{
	var precioVentaUnitarioProducto=$('#txtPrecioVentaUnitarioProducto').val();
	var porcentajeTributacionProducto=$('#txtPorcentajeTributacionProducto').val();

	if(isNaN(porcentajeTributacionProducto) || isNaN(precioVentaUnitarioProducto) || porcentajeTributacionProducto<=0 || porcentajeTributacionProducto>=100)
	{
		$('#txtImpuestoAplicadoProducto').val(null);
	}
	else
	{
		var valorOperacionalImpuesto=((porcentajeTributacionProducto/100)+1).toFixed(2);
		var impuestoAplicado=Math.abs((precioVentaUnitarioProducto/valorOperacionalImpuesto)-precioVentaUnitarioProducto).toFixed(2);

		$('#txtImpuestoAplicadoProducto').val(impuestoAplicado);
	}

	$('#modalProductoExterno').data('formValidation').resetForm();
	$('#modalProductoExterno').data('formValidation').validate();
}

function agregarProductoDetalleVenta()
{
	var isValid=true;

	$('#modalProductoExterno').data('formValidation').resetForm();
	$('#modalProductoExterno').data('formValidation').validate();

	isValid=!isValid ? isValid : $('#modalProductoExterno').data('formValidation').isValidField('txtNombreProducto');
	isValid=!isValid ? isValid : $('#modalProductoExterno').data('formValidation').isValidField('txtPrecioVentaUnitarioProducto');
	isValid=!isValid ? isValid : $('#modalProductoExterno').data('formValidation').isValidField('txtPorcentajeTributacionProducto');

	if(!isValid)
	{
		notaDatosIncorrectos();

		return;
	}

	var existProductoExternal=null;
	var codigoOficinaProductoExternoTemp=_codigoOficinaProductoExterno;

	do
	{
		existProductoExternal=false;

		$('#tableProducto > tbody > tr').each(function(index, element)
		{
			if($(element).find('input[name^=hdNombreProducto]').length)
			{
				if($(element).find('input[name^=hdCodigoOficinaProducto]').val()==codigoOficinaProductoExternoTemp)
				{
					codigoOficinaProductoExternoTemp=parseInt(codigoOficinaProductoExternoTemp)+1;

					existProductoExternal=true;

					return false;
				}
			}
		});
	}
	while(existProductoExternal);

	var productoAgregado=agregarProducto(
		true,
		codigoOficinaProductoExternoTemp.toString(),
		'',
		$('#txtNombreProducto').val(),
		'',
		'Genérico',
		$('#selectSituacionImpuestoProducto').val(),
		$('#selectTipoImpuestoProducto').val(),
		$('#txtPorcentajeTributacionProducto').val(),
		$('#selectCodigoPresentacionProducto option:selected').text(),
		$('#selectCodigoUnidadMedidaProducto option:selected').text(),
		10,
		'---',
		$('#txtPrecioVentaUnitarioProducto').val(),
		1,
		0,
		0,
		0
	);

	clearInputText('modalProductoExterno', ['txtPorcentajeTributacionProducto']);

	if(productoAgregado)
	{
		notaOperacionCorrecta();
	}
}

function enviarFrmInsertarReciboVenta()
{
	var isValid=true;
	var priceCorrect=true;

	if($('#tableProducto > tbody > tr').length<=1)
	{
		notaError('No se pudo proceder', 'Debe agregar por lo menos un producto al detalle de la venta.');

		return;
	}

	$('#tableProducto > tbody > tr').each(function(index, element)
	{
		if($(element).find('input[name^=hdNombreProducto]').length && (isNaN($(element).find('input[name^=hdPrecioVentaTotalProducto]').val()) || $(element).find('input[name^=hdPrecioVentaTotalProducto]').val()<0 || isNaN($(element).find('input[name^=hdCantidadProducto]').val()) || $(element).find('input[name^=hdCantidadProducto]').val()<=0))
		{
			isValid=false;

			notaError('No se pudo proceder', 'Datos incorrectos. Por favor corrija los valores de la venta.');

			return false;
		}
		else
		{
			if($(element).find('input[name^=hdNombreProducto]').length)
			{
				var valueTemp=parseFloat($(element).find('input[name^=hdPrecioVentaTotalProducto]').val())/parseFloat($(element).find('input[name^=hdCantidadProducto]').val());

				if(valueTemp!=valueTemp.toFixed(2))
				{
					priceCorrect=false;
				}
			}
		}
	});

	if(!isValid)
	{
		return;
	}

	$('#frmInsertarReciboVenta').data('formValidation').resetForm();

	if($('#selectTipoRecibo').val()=='Boleta')
	{
		$('#frmInsertarReciboVenta').data('formValidation').validateField('txtDniCliente');
		
		isValid=!isValid ? isValid : $('#frmInsertarReciboVenta').data('formValidation').isValidField('txtDniCliente');
	}
	else
	{
		$('#frmInsertarReciboVenta').data('formValidation').validateField('txtRucEmpresa');
		$('#frmInsertarReciboVenta').data('formValidation').validateField('selectRazonSocialEmpresa');
		$('#frmInsertarReciboVenta').data('formValidation').validateField('txtDireccionEmpresa');

		isValid=!isValid ? isValid : $('#frmInsertarReciboVenta').data('formValidation').isValidField('txtRucEmpresa');
		isValid=!isValid ? isValid : $('#frmInsertarReciboVenta').data('formValidation').isValidField('selectRazonSocialEmpresa');
		isValid=!isValid ? isValid : $('#frmInsertarReciboVenta').data('formValidation').isValidField('txtDireccionEmpresa');
	}

	if($('#selectTipoPago').val()=='Al crédito')
	{
		$('#frmInsertarReciboVenta').data('formValidation').validateField('txtLetras');
		$('#frmInsertarReciboVenta').data('formValidation').validateField('dateFechaPrimerPago');

		isValid=!isValid ? isValid : $('#frmInsertarReciboVenta').data('formValidation').isValidField('txtLetras');
	}

	if(!isValid)
	{
		notaDatosIncorrectos();

		return;
	}

	mostrarIntruso("No olvides asignar la categoría de venta.");

	if(!priceCorrect)
	{
		swal(
		{
			title : 'Confirmar operación',
			text : 'Alguno de los precios totales de los productos no coincide con sus precios unitarios y cantidad correspondiente. Si procede con esta venta es posible que no exista la posibilidad de anularlo.\n\n¿Realmente desea proceder?',
			dangerMode : true,
			icon : 'warning',
			buttons : ['No, cancelar.', 'Si, proceder.']
		})
		.then((proceed) =>
		{
			if(proceed)
			{
				ignoreRestrictedClose=true;
				
				$('#modalLoading').modal('show');
		
				$('#frmInsertarReciboVenta')[0].submit();
			}
		});
	}
	else
	{
		confirmacionEnvio('frmInsertarReciboVenta');
	}
}

function enviarFrmProforma()
{
	var isValid=true;

	if($('#tableProducto > tbody > tr').length<=1)
	{
		notaError('No se pudo proceder', 'Debe agregar por lo menos un producto al detalle de la venta.');

		return;
	}

	$('#tableProducto > tbody > tr').each(function(index, element)
	{
		if($(element).find('input[name^=hdNombreProducto]').length && (isNaN($(element).find('input[name^=hdPrecioVentaTotalProducto]').val()) || $(element).find('input[name^=hdPrecioVentaTotalProducto]').val()<0 || isNaN($(element).find('input[name^=hdCantidadProducto]').val()) || $(element).find('input[name^=hdCantidadProducto]').val()<=0))
		{
			isValid=false;

			notaError('No se pudo proceder', 'Datos incorrectos. Por favor corrija los valores de la venta.');

			return false;
		}
	});

	if(!isValid)
	{
		return;
	}

	$('#frmInsertarReciboVenta').data('formValidation').resetForm();

	if($('#selectTipoRecibo').val()=='Boleta')
	{
		$('#frmInsertarReciboVenta').data('formValidation').validateField('txtDniCliente');
		
		isValid=!isValid ? isValid : $('#frmInsertarReciboVenta').data('formValidation').isValidField('txtDniCliente');
	}
	else
	{
		$('#frmInsertarReciboVenta').data('formValidation').validateField('txtRucEmpresa');
		$('#frmInsertarReciboVenta').data('formValidation').validateField('selectRazonSocialEmpresa');
		$('#frmInsertarReciboVenta').data('formValidation').validateField('txtDireccionEmpresa');

		isValid=!isValid ? isValid : $('#frmInsertarReciboVenta').data('formValidation').isValidField('txtRucEmpresa');
		isValid=!isValid ? isValid : $('#frmInsertarReciboVenta').data('formValidation').isValidField('selectRazonSocialEmpresa');
		isValid=!isValid ? isValid : $('#frmInsertarReciboVenta').data('formValidation').isValidField('txtDireccionEmpresa');
	}

	if($('#selectTipoPago').val()=='Al crédito')
	{
		$('#frmInsertarReciboVenta').data('formValidation').validateField('txtLetras');
		$('#frmInsertarReciboVenta').data('formValidation').validateField('dateFechaPrimerPago');

		isValid=!isValid ? isValid : $('#frmInsertarReciboVenta').data('formValidation').isValidField('txtLetras');
	}

	if(!isValid)
	{
		notaDatosIncorrectos();

		return;
	}
	
	confirmacion(function(){
		$('#frmInsertarReciboVenta').attr('action', _urlBase+'/reciboventa/proforma');
		$('#frmInsertarReciboVenta').attr('target', '_blank');
		$('#frmInsertarReciboVenta')[0].submit();
		$('#frmInsertarReciboVenta').attr('action', _urlBase+'/reciboventa/insertarsinfe');
		$('#frmInsertarReciboVenta').removeAttr('target');
	});
}