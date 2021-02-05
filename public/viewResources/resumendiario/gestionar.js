'use strict';

function generarResumen()
{
	$("#modalLoading").modal("show");
	
	$('body').append('<form id="frmSendDataTemp" action="'+_urlBase+'/resumendiario/gestionar" method="post" style="display: none;"><input type="hidden" name="_token" value="'+_token+'"></form>');

	$('#frmSendDataTemp').submit();
}