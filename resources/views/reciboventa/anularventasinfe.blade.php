<div class="row">
	<div class="col-md-12">
		<form id="frmAnularVentaSinFe" action="{{url('reciboventa/anularventasinfe')}}" method="post">
            {{csrf_field()}}
            <div class="form-group">
                <label>Motivo anulación</label>
                <textarea class="form-control" rows="3" placeholder="Ingresa el motivo de anulación" name="txtMotivoAnulacion" id="txtMotivoAnulacion"></textarea>
            </div>
			<input type="hidden" name="hdCodigoReciboVentaOutEf" value="{{$tReciboVentaOutEf->codigoReciboVentaOutEf}}">
		</form>
	</div>
</div>
<hr>
<div class="row">
	<div class="col-md-12">
		<input type="button" class="btn btn-default pull-left" value="Cerrar ventana" onclick="$('#dialogoGeneralModal').modal('hide');">
		<input type="button" class="btn btn-primary pull-right" value="Anular venta" onclick="enviarFrmAnularVentaSinFe();">
	</div>
</div>
<script src="{{asset('viewResources/reciboventa/anularventasinfe.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>