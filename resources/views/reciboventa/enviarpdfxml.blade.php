<div class="row">
	<div class="col-md-12">
		<form id="frmEnviarPdfXml" action="{{url('reciboventa/enviarpdfxml')}}" method="post">
            {{csrf_field()}}
            <div class="form-group">
                <label>Ingrese el correo electrónico</label>
                <input class="form-control" rows="3" placeholder="example@gmail.com" name="txtEmail" id="txtEmail"/>
			</div>
			<div class="form-group">
                <label>Cuerpo del mensaje:</label>
                <textarea class="form-control" rows="3" placeholder="example@gmail.com" name="txtMessage" id="txtMessage">Sr(a) {{$tReciboVenta->nombreCompletoCliente}} le hacemos el envío de su comprobante electrónico.&#13;&#10;&#13;&#10;Gracias por su preferencia!</textarea>
			</div>
			<div class="form-group">
				<label>Escoja los archivos a enviar:</label>
				<ul class="mailbox-attachments clearfix">
					@foreach ($listaFicheros as $file )
						<li class="itemFile" style="cursor:pointer;">
							<span class="mailbox-attachment-icon"><i class="fa fa-file-{{$file->type == 'xml' ? 'code' : 'pdf'}}-o"></i></span>
							<input type="hidden" name="listaFicherosSerie[]" value="{{$file->serie}}">
							<input type="hidden" name="listaFicherosPk[]" value="{{$file->pk}}">
							<div class="mailbox-attachment-info">
							<a class="mailbox-attachment-name"><i class="fa fa-paperclip"></i> {{$file->denomination . '.' . $file->type}}</a>
								<span class="mailbox-attachment-size">
									{{$file->serie}}
									
									<span class="pull-right fileItem"><input name="listaFicherosEnviar[]" class="selectedItem" type="checkbox" checked value="{{$file->name . '~' . $file->pk}}"></span>
								</span>
							</div>
						</li>
					@endforeach
				</ul>
			</div>
			<input type="hidden" name="hdCodigoReciboVenta" value="{{$tReciboVenta->codigoReciboVenta}}">
		</form>
	</div>
</div>
<hr>
<div class="row">
	<div class="col-md-12">
		<input type="button" class="btn btn-default pull-left" value="Cerrar ventana" onclick="$('#dialogoGeneralModal').modal('hide');">
		<input type="button" class="btn btn-primary pull-right" value="Enviar" onclick="enviarFrmEnviarPdfXml();">
	</div>
</div>
<script src="{{asset('viewResources/reciboventa/enviarpdfxml.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>