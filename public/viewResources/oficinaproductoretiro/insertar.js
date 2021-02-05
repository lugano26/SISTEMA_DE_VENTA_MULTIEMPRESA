'use strict';

$(function()
{
	$('#frmInsertarOficinaProductoRetiro').formValidation(
	{
		framework: 'bootstrap',
		excluded: [':disabled', ':hidden', ':not(:visible)', '[class*="notValidate"]'],
		live: 'enabled',
		message: '<b style="color: #9d9d9d;">Asegúrese que realmente no necesita este valor.</b>',
		trigger: null,
		fields:
		{
			selectNombreOficinaOrigen:
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

	iniciarSelect2OficinaOrigen();

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
			url: _urlBase+'/oficinaproducto/jsonporcodigobarrasnombreoficina',
			method: 'POST',
			dataType: 'json',
			delay: 300,
			data: function(params)
			{
				return {
					q: params.term,
					_token: _token,
					codigoOficina: $('#selectNombreOficinaOrigen').val()
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
		},
		escapeMarkup : function(markup)
		{
			return markup;
		},
		templateResult : formatRepo,
		templateSelection : formatRepoSelection
	});

	if(oldData.length>0)
	{	
		deshabilitarSelect2OficinaOrigen();

		$(oldData).each(function(index, element)
		{
			agregarProducto(
				element.codigoOficinaProducto,
				element.presentacion,
				element.unidadMedida,
				element.codigoBarras,
				element.nombre,
				element.tipo,
				element.situacionImpuesto,
				element.tipoImpuesto,
				element.porcentajeTributacion,
				element.cantidadMinimaAlertaStok,
				element.ventaMenorUnidad,
				element.unidadesBloque,
				element.unidadMedidaBloque,
				element.precioCompraUnitario,
				element.precioVentaUnitario,
				element.fechaVencimiento,
				element.cantidadProducto,
				element.montoPerdido
			);
		});

		calcularPerdidaTotal();
	}
});

var dataSelectNombreProducto=[];

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
					+'<td style="border: 1px dotted #594444;font-size: 11px;padding: 4px;width: 120px;text-align: center;">'+dataSelectNombreProducto[indexTemp].row.unidadMedidaBloque+'</td>'
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

	var existeProducto=false;

	var nombreProducto=dataSelectNombreProducto[indexTemp].row.nombre.viiInjectionEscape();

	$('#tableProducto > tbody > tr').each(function(index, element)
	{
		if($(element).find('input[name^=hdNombre]').length)
		{
			var nombreProductoTemp=$(element).find('input[name^=hdNombre]').val().viiInjectionEscape();

			if(nombreProducto.viiReplaceAll(' ', '')==nombreProductoTemp.viiReplaceAll(' ', ''))
			{
				existeProducto=true;

				return false;
			}
		}
	});

	if(existeProducto)
	{
		$('#selectNombreProducto').val(null).trigger('change');

		window.setTimeout(function()
		{
			$('#selectNombreProducto').select2('open');
		}, 50);

		notaError('No se pudo proceder', 'El producto ya fue agregado a la lista.');

		return;
	}

	deshabilitarSelect2OficinaOrigen();
	
	agregarProducto(
		dataSelectNombreProducto[indexTemp].row.codigoOficinaProducto,
		dataSelectNombreProducto[indexTemp].row.presentacion,
		dataSelectNombreProducto[indexTemp].row.unidadMedida,
		dataSelectNombreProducto[indexTemp].row.codigoBarras,
		dataSelectNombreProducto[indexTemp].row.nombre,
		dataSelectNombreProducto[indexTemp].row.tipo,
		dataSelectNombreProducto[indexTemp].row.situacionImpuesto,
		dataSelectNombreProducto[indexTemp].row.tipoImpuesto,
		dataSelectNombreProducto[indexTemp].row.porcentajeTributacion,
		dataSelectNombreProducto[indexTemp].row.cantidadMinimaAlertaStock,
		dataSelectNombreProducto[indexTemp].row.ventaMenorUnidad,
		dataSelectNombreProducto[indexTemp].row.unidadesBloque,
		dataSelectNombreProducto[indexTemp].row.unidadMedidaBloque,
		dataSelectNombreProducto[indexTemp].row.precioCompraUnitario,
		dataSelectNombreProducto[indexTemp].row.precioVentaUnitario,
		dataSelectNombreProducto[indexTemp].row.fechaVencimiento,
		1,
		0
	);

	$('#selectNombreProducto').val(null).trigger('change');

	window.setTimeout(function()
	{
		$('#selectNombreProducto').select2('open');
	}, 50);

	notaOperacionCorrecta();
}

function agregarProducto(codigoOficinaProducto,
		presentacion,
		unidadMedida,
		codigoBarras,
		nombre,
		tipo,
		situacionImpuesto,
		tipoImpuesto,
		porcentajeTributacion,
		cantidadMinimaAlertaStock,
		ventaMenorUnidad,
		unidadesBloque,
		unidadMedidaBloque,
		precioCompraUnitario,
		precioVentaUnitario,
		fechaVencimiento, 
		cantidadProducto,
		montoPerdido)
{
	var htmlTemp='<tr>'
		+'<td style="display: none;">'
			+'<input type="hidden" name="hdCodigoOficinaProducto[]" value="'+codigoOficinaProducto+'">'
			+'<input type="hidden" name="hdPresentacion[]" value="'+presentacion+'">'
			+'<input type="hidden" name="hdUnidadMedida[]" value="'+unidadMedida+'">'
			+'<input type="hidden" name="hdCodigoBarras[]" value="'+codigoBarras.viiInjectionEscape()+'">'
			+'<input type="hidden" name="hdNombre[]" value="'+nombre.trim().viiInjectionEscape()+'">'
			+'<input type="hidden" name="hdTipo[]" value="'+tipo.viiInjectionEscape()+'">'
			+'<input type="hidden" name="hdSituacionImpuesto[]" value="'+situacionImpuesto.viiInjectionEscape()+'">'
			+'<input type="hidden" name="hdTipoImpuesto[]" value="'+tipoImpuesto.viiInjectionEscape()+'">'
			+'<input type="hidden" name="hdPorcentajeTributacion[]" value="'+porcentajeTributacion+'">'
			+'<input type="hidden" name="hdCantidadMinimaAlertaStock[]" value="'+cantidadMinimaAlertaStock+'">'
			+'<input type="hidden" name="hdVentaMenorUnidad[]" value="'+ventaMenorUnidad+'">'
			+'<input type="hidden" name="hdUnidadesBloque[]" value="'+unidadesBloque+'">'
			+'<input type="hidden" name="hdUnidadMedidaBloque[]" value="'+unidadMedidaBloque.viiInjectionEscape()+'">'
			+'<input type="hidden" name="hdPrecioCompraUnitario[]" value="'+precioCompraUnitario+'">'
			+'<input type="hidden" name="hdPrecioVentaUnitario[]" value="'+precioVentaUnitario+'">'
			+'<input type="hidden" name="hdFechaVencimiento[]" value="'+fechaVencimiento+'">'
			+'<input type="hidden" name="hdCantidadProducto[]" value="'+cantidadProducto+'">'
			+'<input type="hidden" name="hdMontoPerdido[]" value="'+montoPerdido+'">'
		+'</td>'
		+'<td class="text-center"><span class="fa fa-tag"></span></td>'
		+'<td class="text-left">'+nombre.viiInjectionEscape()+'</td>'
		+'<td class="text-center">'+tipo.viiInjectionEscape()+'</td>'
		+'<td class="tdCantidadProducto text-center tdEditable" contenteditable onkeyup="onKeyUpTdCantidadProducto(this);">'+cantidadProducto+'</td>'
		+'<td class="text-center">'+precioCompraUnitario+'</td>'
		+'<td class="text-center">'+precioVentaUnitario+'</td>'
		+'<td class="text-center">'+fechaVencimiento+'</td>'
		+'<td class="TdMontoPerdido text-center tdEditable" contenteditable onkeyup="onKeyUpTdMontoPerdido(this);" onfocus="onFocusTdMontoPerdido(this);" onblur="onBlurTdMontoPerdido(this);">S/'+montoPerdido+'</td>'
		+'<td class="text-right">'
			+'<span class="btn btn-default btn-xs glyphicon glyphicon-trash" data-toggle="tooltip" data-placement="left" title="Quitar de la tabla" onclick="quitarProducto(this);" style="margin: 1px;"></span>'
		+'</td>'
	+'</tr>';

	$('#tableProducto > tbody').prepend(htmlTemp);

	$('[data-toggle="tooltip"]').tooltip();
}

function onKeyUpTdMontoPerdido(element)
{
	$(element).parent().find('input[name^=hdMontoPerdido]').val((!isNaN($(element).text()) ? parseFloat($(element).text()).toFixed(2) : $(element).text()));

	calcularPerdidaTotal();
}

function onFocusTdMontoPerdido(element)
{
	$(element).text($(element).text().viiReplaceAll('S/', ''));
}

function onBlurTdMontoPerdido(element)
{
	$(element).text('S/'+(!isNaN($(element).text()) ? parseFloat($(element).text()).toFixed(2) : '0.00'));

	calcularPerdidaTotal();
}

function calcularPerdidaTotal()
{
	var totalMontoPerdido=0;
	var valorIncorrecto=false;

	$('#tableProducto > tbody > tr').each(function(index, element)
	{
		if(!($(element).find('input[name^=hdNombre]').length))
		{
			$(element).find('[class^=tdMontoTotalPerdida]').text('S/'+ totalMontoPerdido.toFixed(2));

			return false;
		}

		totalMontoPerdido +=parseFloat($(element).find('input[name^=hdMontoPerdido]').val());
	});
}

function quitarProducto(element)
{
	confirmacion(function()
	{
		$(element).parent().parent().remove();

		if($('#tableProducto > tbody > tr').length == 1)
		{
			iniciarSelect2OficinaOrigen();
		}
		
		notaOperacionCorrecta();
	});
}

function onKeyUpTdCantidadProducto(element)
{
	$(element).parent().find('input[name^=hdCantidadProducto]').val($(element).text());
}

function enviarFrmInsertarOficinaProductoRetiro()
{
	if($('#tableProducto > tbody > tr').length<2)
	{
		notaError('No se pudo proceder', 'Debe agregar por lo menos un producto a retirar.');

		return;
	}

	$('#frmInsertarOficinaProductoRetiro').data('formValidation').resetForm();
	$('#frmInsertarOficinaProductoRetiro').data('formValidation').validate();

	if( ! $('#frmInsertarOficinaProductoRetiro').data('formValidation').isValidField('selectNombreOficina')||
		! $('#frmInsertarOficinaProductoRetiro').data('formValidation').isValidField('selectNombreOficinaOrigen'))
	{
		notaDatosIncorrectos();

		return;
	}

	confirmacionEnvio('frmInsertarOficinaProductoRetiro');
}

function guardarNombreOficina()
{
	$('#hdNombreOficina').val($("#selectNombreOficina option:selected").text());
}

function guardarNombreOficinaOrigen()
{
	$('#hdNombreOficinaOrigen').val($("#selectNombreOficinaOrigen option:selected").text());
	$('#hdCodigoOficinaOrigen').val($("#selectNombreOficinaOrigen").val());
}

function iniciarSelect2OficinaOrigen()
{
	$("#selectNombreOficinaOrigen>option").removeAttr('disabled');
	
	$('#selectNombreOficinaOrigen').select2(
	{
		language:
		{
			noResults : function()
			{
				return "No se encontraron resultados.";        
			},
			searching : function()
			{
				return "Buscando...";
			},
			inputTooShort : function()
			{ 
				return 'Por favor ingrese 3 o más caracteres';
			}
		},
		placeholder: 'Buscar...',
	});		
}

function deshabilitarSelect2OficinaOrigen()
{
	$("#selectNombreOficinaOrigen>option[value!=" + $("#selectNombreOficinaOrigen").val() + "]").attr('disabled','disabled');

	$('#selectNombreOficinaOrigen').select2(
	{
		language:
		{
			noResults: function()
			{
				return "Para cambiar de oficina, porfavor retire todos los productos del detalle.";        
			}
		}
	});
}