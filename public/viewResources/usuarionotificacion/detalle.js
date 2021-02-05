'use strict';

window.setTimeout(function()
{
	$.ajax(
	{
		url: _urlBase+'/usuarionotificacion/marcarleidojson',
		type: 'POST',
		dataType: 'JSON',
		data: $('#frmMarcarComoLeidoNotifiacion').serialize(),
		cache: false,
	}).done(function(data) 
	{
	}).fail(function(erx)
	{
	});
}, 500);

$('[data-toggle="tooltip"]').tooltip();

function enviarfrmMarcarComoLeidoNotifiacion()
{
	$('#frmMarcarComoLeidoNotifiacion')[0].submit();
}