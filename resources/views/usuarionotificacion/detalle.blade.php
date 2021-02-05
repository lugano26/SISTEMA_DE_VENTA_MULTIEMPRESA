<div class="row">
    <div class="col-md-12">
        <form id="frmMarcarComoLeidoNotifiacion" action="{{url('usuarionotificacion/marcarleido')}}" method="post">
            {{csrf_field()}}            
            <input type="hidden" name="hdCodigoUsuarioNotificacion" value="{{$tUsuarioNotificacion->codigoUsuarioNotificacion}}">            
        </form>
        <div class="post">
            <div class="user-block">
                <img class="img-circle img-bordered-sm" src="{{asset('plugin/adminlte/dist/img/user2-160x160.png')}}" alt="sysef logo">
                <span class="username">
                    <a href="#">Equipo Sysef</a>
                    @if($tUsuarioNotificacion->permanente)
                    <a href="#" class="pull-right badge bg-yellow"><i class="fa fa-warning"></i> Importante</a>
                    @endif
                </span>
                <span class="description">{{$tUsuarioNotificacion->created_at->format('d-m-Y')}}</span>
            </div>
            <p>
            {{$tUsuarioNotificacion->descripcion}}
            </p>
            @if($tUsuarioNotificacion->url != null && $tUsuarioNotificacion->url != '')
            <div class="row margin-bottom">
                <div class="col-md-10 col-md-push-1">
                    <img class="img-responsive" src="{{asset($tUsuarioNotificacion->url)}}" style="margin: 0 auto;" alt="Photo notification">
                </div>               
            </div>
            @endif
        </div>
    </div>
</div>
<hr>
<div class="row">
    <div class="col-md-12">
        <input type="button" class="btn btn-default pull-left" value="Cerrar ventana" onclick="$('#dialogoGeneralModal').modal('hide');">
        @if(!$tUsuarioNotificacion->permanente)
        	<input type="button" class="btn btn-primary pull-right" value="Marcar como leido" onclick="enviarfrmMarcarComoLeidoNotifiacion();">
        @endif
    </div>
</div>
<script src="{{asset('viewResources/usuarionotificacion/detalle.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>