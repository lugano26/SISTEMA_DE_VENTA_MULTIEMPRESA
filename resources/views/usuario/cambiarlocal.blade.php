<div class="">
	<!-- /.login-logo -->
	<div class="">
		<form action="{{url('usuario/cambiarlocal')}}" method="post">			
			<div class="row" style="{{ strpos(Session::get('rol'), 'Súper usuario')!==false ? "": "display:none;" }}">
				<div class="col-xs-12 form-group">
					<label>Empresa</label>
					<select id="selectCodigoEmpresa" name="selectCodigoEmpresa" class="form-control" onchange="onChangeSelectCodigoEmpresa();">
						@foreach($listaTEmpresa as $value)
							<option value="{{$value->codigoEmpresa}}">{{$value->razonSocial}}</option>
						@endforeach
					</select>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<div class="col-xs-6" style="background-color: #f5f5f5;padding-top: 5px;">
						<label style="cursor: pointer;"><input type="radio" id="radioLocalOficina" name="radioLocal" checked="true" value="1" onclick="onChangeRadioLocal();"> Tienda</label>
					</div>
					<div class="col-xs-6 text-right" style="background-color: #f5f5f5;padding-top: 5px;">
						<label style="cursor: pointer;"><input type="radio" id="radioLocalAlmacen" name="radioLocal" value="0" onclick="onChangeRadioLocal();"> Almacén</label>
					</div>
				</div>
			</div>
			<div id="divSelectCodigoLocal" class="row">
				<div class="col-xs-12 form-group">
					<select id="selectCodigoOficina" name="selectCodigoOficina" class="form-control">
						@foreach($listaTEmpresa[0]->toficina as $value)
							<option value="{{$value->codigoOficina}}">{{$value->descripcion}}</option>
						@endforeach
					</select>
				</div>
			</div>
			<div id="divSelectCodigoAlmacen" class="row" style="display: none;">
				<div class="col-xs-12 form-group">
					<select id="selectCodigoAlmacen" name="selectCodigoAlmacen" class="form-control">
						@foreach($listaTEmpresa[0]->talmacen as $value)
							<option value="{{$value->codigoAlmacen}}">{{$value->descripcion}}</option>
						@endforeach
					</select>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<hr>
					{{csrf_field()}}
					<input type="button" class="btn btn-default" value="Cerrar ventana" onclick="$('#dialogoGeneralModal').modal('hide');">
					<button type="submit" class="btn btn-primary pull-right">Ingresar</button>
				</div>
			</div>
		</form>
	</div>
	<!-- /.login-box-body -->
</div>
<!-- /.login-box -->
<script>
	var empresaLocales={};

	@foreach($listaTEmpresa as $value)
		empresaLocales['{{$value->codigoEmpresa}}Oficina']=[];
		empresaLocales['{{$value->codigoEmpresa}}Almacen']=[];

		@foreach($value->toficina as $item)
			empresaLocales['{{$value->codigoEmpresa}}Oficina'].push(['{{$item->codigoOficina}}', '{{$item->descripcion}}']);
		@endforeach

		@foreach($value->talmacen as $item)
			empresaLocales['{{$value->codigoEmpresa}}Almacen'].push(['{{$item->codigoAlmacen}}', '{{$item->descripcion}}']);
		@endforeach
	@endforeach
</script>
<script src="{{asset('viewResources/usuario/cambiarlocal.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>