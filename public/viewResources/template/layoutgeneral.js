'use strict';

function onBlurSpanTipoCambioUsd()
{
	$('#spanTipoCambioUsd').text(_tipoCambioUsd);
}

function onKeyUpSpanTipoCambioUsd(event, element)
{
	var evt=event || window.event;

	var code=0;

	if(evt!='noEventHandle')
	{
		code=evt.charCode || evt.keyCode || evt.which;
	}

	if(code==13)
	{
		if(!$('#spanTipoCambioUsd').text().match(/^([0-9]+([\.][0-9]{1,3})*)$/))
		{
			notaError('Error!', 'Formato incorrecto para la moneda [Ejemplo: 3.333].');

			$('#spanTipoCambioUsd').text(_tipoCambioUsd);

			return;
		}

		if(typeof applyCurrency==='function' && $('#tableProducto > tbody > tr').length>1)
		{
			notaError('Error!', 'Quite todo los productos de la lista de venta para aplicar nuevo tipo de cambio.');

			$('#spanTipoCambioUsd').text(_tipoCambioUsd);

			return;
		}

		paginaAjaxJSON({ _token: _token, tipoCambioUsd: $('#spanTipoCambioUsd').text().trim() }, _urlBase+'/empresa/editartipocambiousdconajax', 'POST', null, function(objectJSON)
		{
			if(objectJSON.error)
			{
				notaError('No se pudo proceder', objectJSON.mensajeGlobal);

				return false;
			}

			_tipoCambioUsd=objectJSON.tipoCambioUsd;

			$('#spanTipoCambioUsd').text(_tipoCambioUsd);

			notaOperacionCorrecta();
		}, false, true);
	}
}

$(function()
{
	$('#spanTipoCambioUsd').text(_tipoCambioUsd);

	localStorage.searchPerformance=((localStorage.searchPerformance==null || localStorage.searchPerformance=='undefined') ? 'Performance' : localStorage.searchPerformance);

	$('#selectSearchPerformance').val(localStorage.searchPerformance);

	$('#selectSearchPerformance').on('change', function()
	{
		localStorage.searchPerformance=$(this).val();

		$('#modalLoading').show();

		window.location.reload();
	});

	$('body').keypress(function(event)
	{
		if(event.keyCode===10 || event.keyCode===13)
		{
			event.preventDefault();
		}
	});

	$('[data-toggle="tooltip"]').tooltip();

	$('.select').select2(
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
		allowClear: true,
		placeholder: 'Buscar...',
		minimumInputLength: 3
	});

	$('.selectStatic').select2(
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
		allowClear: true,
		placeholder: 'Buscar...'
	});

	$('.selectStaticNotClear').select2(
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
		placeholder: 'Buscar...'
	});

	$('.datepicker').datepicker(
	{
		autoclose : true,
		format : 'yyyy-mm-dd'
	});

	if(localStorage.getItem('collapseMenu') !== null && localStorage.getItem('collapseMenu') === "true")
	{
		$('body').addClass('sidebar-collapse');
	}

	if(isChrome)
	{
		$('input[type=text]').attr('autocomplete', '~!@#$%^&*()_+');
	}
	else
	{
		$('input[type=text]').attr('autocomplete', 'off');
	}
	
	$('img').on('dragstart', function(a){ a.preventDefault(); });
});

function syncUpSunat()
{
	$.ajax(
	{
		url: _urlBase+'/billsyncup/sync',
		type: 'POST',
		data: { _token: _token },
		cache: false,
		async: true
	}).done(function(objectJSON) 
	{
		if(objectJSON.mo.type!='success')
		{
			window.setTimeout(function()
			{
				syncUpSunat();
			}, 50000);

			return false;
		}

		if(objectJSON.dto.tipoComprobante=='Factura' && $('.billSyncUp'+objectJSON.dto.codigoRegistro).length)
		{
			if(objectJSON.dto.estadoEnvio=='Aprobado')
			{
				$('.billSyncUp'+objectJSON.dto.codigoRegistro).attr('src', _contentBase+'/img/general/sunat.png');
			}
			else
			{
				$('.billSyncUp'+objectJSON.dto.codigoRegistro).attr('src', _contentBase+'/img/general/sunatRechazado.png');

				if($('#tdEstado'+objectJSON.dto.codigoRegistro).length)
				{
					$('#tdEstado'+objectJSON.dto.codigoRegistro).html('<span class="label label-danger">Rechazado</span>');
				}
			}
		}

		if(objectJSON.dto.tipoComprobante=='Nota de crédito' && $('.creditNoteSyncUp'+objectJSON.dto.codigoRegistro).length)
		{
			if(objectJSON.dto.estadoEnvio=='Aprobado')
			{
				$('.creditNoteSyncUp'+objectJSON.dto.codigoRegistro).attr('src', _contentBase+'/img/general/sunat.png');
			}
			else
			{
				$('.creditNoteSyncUp'+objectJSON.dto.codigoRegistro).attr('src', _contentBase+'/img/general/sunatRechazado.png');
			}
		}

		if(objectJSON.dto.tipoComprobante=='Nota de débito' && $('.debitNoteSyncUp'+objectJSON.dto.codigoRegistro).length)
		{
			if(objectJSON.dto.estadoEnvio=='Aprobado')
			{
				$('.debitNoteSyncUp'+objectJSON.dto.codigoRegistro).attr('src', _contentBase+'/img/general/sunat.png');
			}
			else
			{
				$('.debitNoteSyncUp'+objectJSON.dto.codigoRegistro).attr('src', _contentBase+'/img/general/sunatRechazado.png');
			}
		}

		if(objectJSON.dto.tipoComprobante=='Guía de remisión de remitente' && $('.referralGuideSyncUp'+objectJSON.dto.codigoRegistro).length)
		{
			if(objectJSON.dto.estadoEnvio=='Aprobado')
			{
				$('.referralGuideSyncUp'+objectJSON.dto.codigoRegistro).attr('src', _contentBase+'/img/general/sunat.png');
			}
			else
			{
				$('.referralGuideSyncUp'+objectJSON.dto.codigoRegistro).attr('src', _contentBase+'/img/general/sunatRechazado.png');
			}
		}

		window.setTimeout(function()
		{
			syncUpSunat();
		}, 70000);
	});
}

function saveCollapseMenu()
{
	localStorage.setItem('collapseMenu', !$('body').hasClass('sidebar-collapse'));
}

$('body').on('click', function(e)
{
	e=e || window.event;
	e=e.target || e.srcElement;

	if(!$(e).hasClass('mostrarIntruso'))
	{
		$('#intruso').css(
		{
			"left": "-250px",
			"transition": "left 0.5s"
		});	
	}
});

if(isChrome)
{
	window.addEventListener('beforeunload', function(e)
	{
		var closeTab=true;

		$('.verifyForClose').each(function(index, element)
		{
			if($(element).val().trim()!='')
			{
				closeTab=false;

				return false;
			}
		});

		$('.verifyForCloseTable0').each(function(index, element)
		{
			if($('.verifyForCloseTable0 > tbody > tr').length>0)
			{
				closeTab=false;

				return false;
			}
		});

		$('.verifyForCloseTable1').each(function(index, element)
		{
			if($('.verifyForCloseTable1 > tbody > tr').length>1)
			{
				closeTab=false;

				return false;
			}
		});

		$('.verifyForCloseTable2').each(function(index, element)
		{
			if($('.verifyForCloseTable2 > tbody > tr').length>2)
			{
				closeTab=false;

				return false;
			}
		});

		if(!closeTab && !ignoreRestrictedClose)
		{
			var confirmationMessage='\o/';

			(e || window.event).returnValue=confirmationMessage;
			
			return confirmationMessage;
		}
	});
}

function mostrarIntruso(mensaje)
{
	$('#intruso > div').html(mensaje);

	$('#intruso').css(
	{
		"left": "0px",
		"transition": "left 0.5s"
	});
}

Date.prototype.addDays=function(days) 
{
	var date=new Date(this.valueOf());
	
	date.setDate(date.getDate() + days);
	
	return date;
}