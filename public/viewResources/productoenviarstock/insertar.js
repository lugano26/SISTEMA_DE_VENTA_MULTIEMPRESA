'use strict';

$(function()
{
	$('#frmInsertarProductoEnviarStock').formValidation(
	{
		framework: 'bootstrap',
		excluded: [':disabled', ':hidden', ':not(:visible)', '[class*="notValidate"]'],
		live: 'enabled',
		message: '<b style="color: #9d9d9d;">Asegúrese que realmente no necesita este valor.</b>',
		trigger: null,
		fields:
		{
			selectNombreOficinaDestino:
			{
				validators:
				{
					notEmpty:
					{
						message: '<b style="color: red;">Este campo es requerido.</b>'
					},
				}
			},
			selectAlmacenOrigen:
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

	iniciarSelect2AlmacenOrigen();

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
			url: _urlBase+'/almacenproducto/jsonporcodigobarrasnombrealmacen',
			method: 'POST',
			dataType: 'json',
			delay: 300,
			data: function(params)
			{
				return {
					q: params.term,
					_token: _token,
					codigoAlmacen: $("#selectAlmacenOrigen").val()
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
		escapeMarkup: function(markup)
		{
			return markup;
		},
		templateResult: formatRepo,
		templateSelection: formatRepoSelection
	});

	if(oldData.length>0)
	{
		deshabilitarSelect2AlmacenOrigen();
		
		$(oldData).each(function(index, element)
		{
			agregarProducto(
				element.codigoAlmacenProducto,
				element.codigoPresentacion,
				element.codigoUnidadMedida,
				element.codigoBarras,
				element.nombre,
				element.descripcion,
				element.tipo,
				element.situacionImpuesto,
				element.tipoImpuesto,
				element.porcentajeTributacion,
				element.cantidadMinimaAlertaStock,
				element.pesoGramosUnidad,
				element.ventaMenorUnidad,
				element.unidadesBloque,
				element.unidadMedidaBloque,
				element.precioCompraUnitario,
				element.precioVentaUnitario,
				element.fechaVencimiento,
				element.cantidadProducto,
				element.registroSerieProducto
			);
		});
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
		if(element.row.codigoAlmacenProducto==repo.id)
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
					+'<td style="border: 1px dotted #594444;font-size: 11px;padding: 4px;width: 120px;text-align: center;">'+dataSelectNombreProducto[indexTemp].row.tunidadmedida.nombre+'</td>'
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

	deshabilitarSelect2AlmacenOrigen();

	agregarProducto(
		dataSelectNombreProducto[indexTemp].row.codigoAlmacenProducto,
		dataSelectNombreProducto[indexTemp].row.codigoPresentacion,
		dataSelectNombreProducto[indexTemp].row.codigoUnidadMedida,
		dataSelectNombreProducto[indexTemp].row.codigoBarras,
		dataSelectNombreProducto[indexTemp].row.nombre,
		dataSelectNombreProducto[indexTemp].row.descripcion,
		dataSelectNombreProducto[indexTemp].row.tipo,
		dataSelectNombreProducto[indexTemp].row.situacionImpuesto,
		dataSelectNombreProducto[indexTemp].row.tipoImpuesto,
		dataSelectNombreProducto[indexTemp].row.porcentajeTributacion,
		dataSelectNombreProducto[indexTemp].row.cantidadMinimaAlertaStock,
		dataSelectNombreProducto[indexTemp].row.pesoGramosUnidad,
		dataSelectNombreProducto[indexTemp].row.ventaMenorUnidad,
		dataSelectNombreProducto[indexTemp].row.unidadesBloque,
		dataSelectNombreProducto[indexTemp].row.unidadMedidaBloque,
		dataSelectNombreProducto[indexTemp].row.precioCompraUnitario,
		dataSelectNombreProducto[indexTemp].row.precioVentaUnitario,
		dataSelectNombreProducto[indexTemp].row.fechaVencimiento,
		1
	);

	$('#selectNombreProducto').val(null).trigger('change');

	window.setTimeout(function()
	{
		$('#selectNombreProducto').select2('open');
	}, 50);

	notaOperacionCorrecta();
}

function agregarProducto(codigoAlmacenProducto,
		codigoPresentacion,
		codigoUnidadMedida,
		codigoBarras,
		nombre,
		descripcion,
		tipo,
		situacionImpuesto,
		tipoImpuesto,
		porcentajeTributacion,
		cantidadMinimaAlertaStock,
		pesoGramosUnidad,
		ventaMenorUnidad,
		unidadesBloque,
		unidadMedidaBloque,
		precioCompraUnitario,
		precioVentaUnitario,
		fechaVencimiento, 
		cantidadProducto,
		registroEnSerie = 'false')
{
	var htmlTemp='<tr ' + (registroEnSerie === 'true' ? 'style="background-color:#FFFC8F"' : '' ) + '>'
		+'<td style="display: none;">'
			+'<input type="hidden" name="hdCodigoAlmacenProducto[]" value="'+codigoAlmacenProducto+'">'
			+'<input type="hidden" name="hdCodigoPresentacion[]" value="'+codigoPresentacion+'">'
			+'<input type="hidden" name="hdCodigoUnidadMedida[]" value="'+codigoUnidadMedida+'">'
			+'<input type="hidden" name="hdCodigoBarras[]" value="'+codigoBarras.viiInjectionEscape()+'">'
			+'<input type="hidden" name="hdNombre[]" value="'+nombre.trim().viiInjectionEscape()+'">'
			+'<input type="hidden" name="hdDescripcion[]" value="'+descripcion.trim().viiInjectionEscape()+'">'
			+'<input type="hidden" name="hdTipo[]" value="'+tipo.viiInjectionEscape()+'">'
			+'<input type="hidden" name="hdSituacionImpuesto[]" value="'+situacionImpuesto.viiInjectionEscape()+'">'
			+'<input type="hidden" name="hdTipoImpuesto[]" value="'+tipoImpuesto.viiInjectionEscape()+'">'
			+'<input type="hidden" name="hdPorcentajeTributacion[]" value="'+porcentajeTributacion+'">'
			+'<input type="hidden" name="hdCantidadMinimaAlertaStock[]" value="'+cantidadMinimaAlertaStock+'">'
			+'<input type="hidden" name="hdPesoGramosUnidad[]" value="'+pesoGramosUnidad+'">'
			+'<input type="hidden" name="hdVentaMenorUnidad[]" value="'+ventaMenorUnidad+'">'
			+'<input type="hidden" name="hdUnidadesBloque[]" value="'+unidadesBloque+'">'
			+'<input type="hidden" name="hdUnidadMedidaBloque[]" value="'+unidadMedidaBloque.viiInjectionEscape()+'">'
			+'<input type="hidden" name="hdPrecioCompraUnitario[]" value="'+precioCompraUnitario+'">'
			+'<input type="hidden" name="hdPrecioVentaUnitario[]" value="'+precioVentaUnitario+'">'
			+'<input type="hidden" name="hdFechaVencimiento[]" value="'+fechaVencimiento+'">'
			+'<input type="hidden" name="hdCantidadProducto[]" value="'+cantidadProducto+'">'
			+'<input type="hidden" name="hdRegistroSerieProducto[]" value="'+registroEnSerie+'">'
		+'</td>'
		+'<td class="text-center"><span class="fa fa-tag"></span></td>'
		+'<td class="text-left">'+nombre.viiInjectionEscape()+'</td>'
		+'<td class="text-center">'+tipo.viiInjectionEscape()+'</td>'
		+'<td class="text-center">'+unidadMedidaBloque.viiInjectionEscape()+'</td>'
		+'<td class="tdCantidadProducto text-center tdEditable" contenteditable onkeyup="onKeyUpTdCantidadProducto(this);">'+cantidadProducto+'</td>'
		+'<td class="text-center">'+precioCompraUnitario+'</td>'
		+'<td class="text-center">'+precioVentaUnitario+'</td>'
		+'<td class="text-center">'+fechaVencimiento+'</td>'
		+'<td class="text-center"><input type="checkbox" ' + (registroEnSerie === 'true' ? 'checked' : '') + ' onclick="confirmSerie(this)"></td>'
		+'<td class="text-right">'
			+'<span class="btn btn-default btn-xs glyphicon glyphicon-trash" data-toggle="tooltip" data-placement="left" title="Quitar de la tabla" onclick="quitarProducto(this);" style="margin: 1px;"></span>'
		+'</td>'
	+'</tr>';

	$('#tableProducto > tbody').append(htmlTemp);

	$('[data-toggle="tooltip"]').tooltip();
}

function confirmSerie(element)
{
	if($(element).is(':checked'))
	{			
		$(element).closest('tr').css('background-color', '#FFFC8F');
		$(element).closest('tr').find('input[name="hdRegistroSerieProducto[]"]').val('true');
	}
	else
	{
		$(element).closest('tr').css('background-color', 'rgb(249, 249, 249)');			
		$(element).closest('tr').find('input[name="hdRegistroSerieProducto[]"]').val('false');
	}
}

function quitarProducto(element)
{
	confirmacion(function()
	{
		$(element).parent().parent().remove();
		
		if(!$('#tableProducto > tbody > tr').length)
		{
			iniciarSelect2AlmacenOrigen();
		}
		
		notaOperacionCorrecta();
	});
}

function onKeyUpTdCantidadProducto(element)
{
	$(element).parent().find('input[name^=hdCantidadProducto]').val($(element).text());
}

function enviarFrmInsertarProductoEnviarStock()
{
	if($('#tableProducto > tbody > tr').length<1)
	{
		notaError('No se pudo proceder', 'Debe agregar por lo menos un producto al detalle del traslado.');

		return;
	}
	
	var isValid = true;

	$('#tableProducto > tbody > tr').each(function(index, element)
	{
		if($(element).find('input[name^=hdNombre]').length && ( isNaN($(element).find('input[name^=hdCantidadProducto]').val()) || $(element).find('input[name^=hdCantidadProducto]').val()<=0))
		{
			isValid = false;

			return false;
		}
	});

	if(!isValid)
	{
		notaError('No se pudo proceder', 'Datos incorrectos. Por favor corrija los valores del traslado.');
		
		return;
	}

	$('#frmInsertarProductoEnviarStock').data('formValidation').resetForm();
	$('#frmInsertarProductoEnviarStock').data('formValidation').validate();

	if( ! $('#frmInsertarProductoEnviarStock').data('formValidation').isValidField('selectNombreOficinaDestino') ||
		! $('#frmInsertarProductoEnviarStock').data('formValidation').isValidField('selectAlmacenOrigen'))
	{
		notaDatosIncorrectos();

		return;
	}

	confirmacionEnvio('frmInsertarProductoEnviarStock');
}

function guardarNombreOficinaDestino()
{
	$('#hdNombreOficinaDestino').val($("#selectNombreOficinaDestino option:selected").text());
}

function guardarNombreAlmacenOrigen()
{
	$('#hdNombreAlmacenOrigen').val($("#selectAlmacenOrigen option:selected").text());
}

function iniciarSelect2AlmacenOrigen()
{
	$("#selectAlmacenOrigen>option").removeAttr('disabled');
	
	$('#selectAlmacenOrigen').select2(
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

function deshabilitarSelect2AlmacenOrigen()
{
	$("#selectAlmacenOrigen>option[value!=" + $("#selectAlmacenOrigen").val() + "]").attr('disabled','disabled');

	$('#selectAlmacenOrigen').select2(
	{
		language:
		{
			noResults: function()
			{
				return "Para cambiar de almacen, porfavor retire todos los productos del detalle.";        
			}
		}
	});
}