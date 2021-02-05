'use strict';

$(function()
{
	$('#selectCodigoEmpresa').select2(
	{
		width: '100%'
	});

	if(typeof localStorage.codigoEmpresa!='undefined')
	{
		$('#selectCodigoEmpresa').val(localStorage.codigoEmpresa).change();
	}
});

function onChangeSelectCodigoEmpresa()
{
	var htmlTemp=null;

	$('#selectCodigoOficina').html(null);
	$('#selectCodigoAlmacen').html(null);

	htmlTemp='';

	for(var i=0; i<empresaLocales[$('#selectCodigoEmpresa').val()+'Oficina'].length; i++)
	{
		htmlTemp+='<option value="'+empresaLocales[$('#selectCodigoEmpresa').val()+'Oficina'][i][0]+'">'+empresaLocales[$('#selectCodigoEmpresa').val()+'Oficina'][i][1]+'</option>';
	}

	$('#selectCodigoOficina').html(htmlTemp);

	htmlTemp='';

	for(var i=0; i<empresaLocales[$('#selectCodigoEmpresa').val()+'Almacen'].length; i++)
	{
		htmlTemp+='<option value="'+empresaLocales[$('#selectCodigoEmpresa').val()+'Almacen'][i][0]+'">'+empresaLocales[$('#selectCodigoEmpresa').val()+'Almacen'][i][1]+'</option>';
	}

	$('#selectCodigoAlmacen').html(htmlTemp);

	localStorage.codigoEmpresa=$('#selectCodigoEmpresa').val();
}

function onChangeRadioLocal()
{
	if($('#radioLocalOficina').is(':checked'))
	{
		$('#divSelectCodigoAlmacen').hide();
		$('#divSelectCodigoLocal').show();
	}
	else
	{
		$('#divSelectCodigoAlmacen').show();
		$('#divSelectCodigoLocal').hide();
	}
}