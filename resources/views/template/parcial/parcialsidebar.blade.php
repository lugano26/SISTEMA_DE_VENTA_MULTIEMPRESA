<aside class="control-sidebar control-sidebar-dark">
	<ul class="nav nav-tabs nav-justified control-sidebar-tabs">
		<li class="active"><a href="#control-sidebar-home-tab" data-toggle="tab" aria-expanded="true"><i class="fa fa-home"></i></a></li>
		<li><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-gears"></i></a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active text-justify" id="control-sidebar-home-tab">
			<h3 class="control-sidebar-heading">Información del sistema</h3>
			<hr>
			<div style="background-color: #ffffff;border-radius: 10px;padding: 5px;">
				<img src="{{asset('img/logoEmpresarial/logoNegro.png')}}" width="100%">
			</div>
			<br>
			
			
			<p>
				<div style="background-color: #f5f5f5;border-radius: 5px;padding: 2px;">
					
				</div>
			</p>
		</div>
		<div class="tab-pane" id="control-sidebar-settings-tab">
			<h3 class="control-sidebar-heading">Configuraciones generales</h3>
			<hr>
			<p class="text-justify">
				Elije rendimiento y disminuye precisión o elije precisión y disminuye rendimiento.
			</p>
			<select id="selectSearchPerformance" class="form-control">
				<option value="Performance">Rendimiento en búsquedas</option>
				<option value="Precision">Precisión en búsquedas</option>
			</select>
		</div>
	</div>
</aside>
<div class="control-sidebar-bg"></div>