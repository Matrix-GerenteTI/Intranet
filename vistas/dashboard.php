<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>
	<div class="page-title">

		<div class="pull-left">
			<h1 class="title">Tablero</h1>                            </div>


	</div>
</div>
<div class="clearfix"></div>
<div class="col-lg-12">
	<section class="box nobox">
		<div class="content-body">


		<div class="row">
			<div class="col-md-6 col-sm-6 col-xs-12">

				<div class="ultra-widget ultra-todo-task bg-primary">
					<div class="wid-task-header">
						<div class="wid-icon">
							<i class="fa fa-pencil-square"></i>
						</div>
						<div class="wid-text">
							<h4>Admnistración del Personal</h4>
							<?php
								$dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
								$meses = array("","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

							?>
							<span><?php echo $dias[date('w')]; ?>, <small><?php echo date("d")." de ".$meses[date('m')*1]." del ".date("Y"); ?></small></span>
						</div>
					</div>
					<div class="wid-all-tasks">
						<table class="table ">
							<tr>
								<th>Asistió</th>
								<th style="text-align:center">Nombre</th>
								<th></th>
							</tr>
							<tbody id="contentEmpleados">
								
							</tbody>
						</table>
					</div>
					<!--
					<div class="wid-add-task">
						<input type="text" class="form-control" placeholder="Agregar Tarea" />
					</div>
					-->
				</div>


			</div>

			<!-- <div class="row">
				<div class="col-md-4 col-sm-6 col-xs-12">

					<div class="ultra-widget ultra-todo-task bg-primary">
						<div class="wid-task-header">
							<div class="wid-icon">
								<i class="fa fa-pencil-square"></i>
							</div>
							<div class="wid-text">
								<h4>Check Asistencia</h4> -->
								<?php
									// $dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
									// $meses = array("","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
 
								?>
								<!-- <span><?php // echo $dias[date('w')]; 
								?><small><?php //echo date("d")." de ".$meses[date('m')*1]." del ".date("Y");
								 ?></small></span>
							</div>
						</div>
						<div class="wid-all-tasks">

							<ul class="list-unstyled" id="tareasUsuario">
							</ul>

						</div>
						
						<div class="wid-add-task">
							<input type="text" class="form-control" placeholder="Agregar Tarea" />
						</div>
						-->
					<!-- </div>


				</div> -->
				<!--
					
				-->	
				<?php
					$context  = stream_context_create(array('http' => array('header' => 'Accept: application/xml')));
					$xml = file_get_contents ("http://xml.tutiempo.net/xml/33199.xml", false, $context); 
					$arrtiempo = array();
					$carga_xml = simplexml_load_string($xml);
					//	var_dump($carga_xml->pronostico_dias[25]);
					$arrclima = (array) $carga_xml->pronostico_dias;
					$n = 0;
					foreach ($arrclima['dia'] as $dia) {
						$arrdia = (array) $dia;
						$arrtiempo[$arrdia['fecha']]['fecha_larga'] = $arrdia['fecha_larga'];
						$arrtiempo[$arrdia['fecha']]['temp_maxima'] = $arrdia['temp_maxima'];
						$arrtiempo[$arrdia['fecha']]['temp_minima'] = $arrdia['temp_minima'];
						$arrtiempo[$arrdia['fecha']]['icono'] = $arrdia['icono'];
						$arrtiempo[$arrdia['fecha']]['texto'] = str_replace("nuboso","nublado",$arrdia['texto']);
						$n++;
						//var_dump($arrdia);
					}
					$hoy = date("Y")."-".(int)date("m")."-".(int)date("d");
					$horaactual = (int)date("H").":00";
					$arrhoras = (array) $carga_xml->pronostico_horas;
					$n = 0;
					$hoytemp = "";
					$hoyicono = "";
					$hoytexto = "";
					foreach ($arrhoras['hora'] as $hora) {
						$arrhora = (array) $hora;
						if($arrhora['fecha'] == $hoy && $arrhora['hora_datos'] == $horaactual){
							$hoytemp = $arrhora['temperatura'];
							$hoyicono = $arrhora['icono'];
							$hoytexto = str_replace("nuboso","nublado",$arrhora['texto']);
						}
					}
					
				?>
				<div class="col-md-3 col-sm-6 col-xs-12">
					<div class="r3_weather">
						<div class="wid-weather wid-weather-small">
							<div class="">

								<div class="location">
									<h3>Tuxtla Gutierrez, CHIS</h3>
									<span>Hoy, <?php echo $arrtiempo[$hoy]['fecha_larga']; ?></span>
								</div>
								<div class="clearfix"></div>
								<div class="degree">
									<img src="<?php echo $hoyicono; ?>" style="position:absolute; margin-left:-40px" /><span>Ahora</span><br/><h3><?php echo $hoytemp; ?>°</h3>
									<div class="clearfix"></div>
									<h4 class="text-white text-center"><?php echo $hoytexto; ?></h4>
								</div>
								<div class="clearfix"></div>
								<div class="weekdays bg-white">
									<ul class="list-unstyled">
									<?php
										foreach($arrtiempo as $idx => $val){
											$diasemana = explode(" ",$val['fecha_larga']);
											$diasemana = $diasemana[0];
									?>
										<li><span class='day'><?php echo $diasemana; ?></span><img src="<?php echo $val['icono']; ?>" style="height:20px" /><span class='temp'><?php echo $val['temp_minima']; ?>° - <?php echo $val['temp_maxima']; ?>°</span></li>
									<?php
										}
									?>
									</ul>
								</div>

							</div>
						</div>

					</div>
				</div>		

				<div class="col-md-5 col-sm-12 col-xs-12">
					
					<div class="r3_notification db_box">
						<h4>Mensajes</h4>

						<ul class="list-unstyled notification-widget">

							<!--
							<li class="unread status-available">
								<a href="javascript:;">
									<div class="user-img">
										<img src="data/profile/avatar-1.png" alt="user-image" class="img-circle img-inline">
									</div>
									<div>
										<span class="name">
											<strong>Clarine Vassar</strong>
											<span class="time small">- 15 mins ago</span>
											<span class="profile-status available pull-right"></span>
										</span>
										<span class="desc small">
											Sometimes it takes a lifetime to win a battle.
										</span>
									</div>
								</a>
							</li>

								
							<li class=" status-away">
								<a href="javascript:;">
									<div class="user-img">
										<img src="data/profile/avatar-2.png" alt="user-image" class="img-circle img-inline">
									</div>
									<div>
										<span class="name">
											<strong>Brooks Latshaw</strong>
											<span class="time small">- 45 mins ago</span>
											<span class="profile-status away pull-right"></span>
										</span>
										<span class="desc small">
											Sometimes it takes a lifetime to win a battle.
										</span>
									</div>
								</a>
							</li>


							<li class=" status-busy">
								<a href="javascript:;">
									<div class="user-img">
										<img src="data/profile/avatar-3.png" alt="user-image" class="img-circle img-inline">
									</div>
									<div>
										<span class="name">
											<strong>Clementina Brodeur</strong>
											<span class="time small">- 1 hour ago</span>
											<span class="profile-status busy pull-right"></span>
										</span>
										<span class="desc small">
											Sometimes it takes a lifetime to win a battle.
										</span>
									</div>
								</a>
							</li>


							<li class=" status-offline">
								<a href="javascript:;">
									<div class="user-img">
										<img src="data/profile/avatar-4.png" alt="user-image" class="img-circle img-inline">
									</div>
									<div>
										<span class="name">
											<strong>Carri Busey</strong>
											<span class="time small">- 5 hours ago</span>
											<span class="profile-status offline pull-right"></span>
										</span>
										<span class="desc small">
											Sometimes it takes a lifetime to win a battle.
										</span>
									</div>
								</a>
							</li>


							<li class=" status-offline">
								<a href="javascript:;">
									<div class="user-img">
										<img src="data/profile/avatar-5.png" alt="user-image" class="img-circle img-inline">
									</div>
									<div>
										<span class="name">
											<strong>Melissa Dock</strong>
											<span class="time small">- Yesterday</span>
											<span class="profile-status offline pull-right"></span>
										</span>
										<span class="desc small">
											Sometimes it takes a lifetime to win a battle.
										</span>
									</div>
								</a>
							</li>


							<li class=" status-available">
								<a href="javascript:;">
									<div class="user-img">
										<img src="data/profile/avatar-1.png" alt="user-image" class="img-circle img-inline">
									</div>
									<div>
										<span class="name">
											<strong>Verdell Rea</strong>
											<span class="time small">- 14th Mar</span>
											<span class="profile-status available pull-right"></span>
										</span>
										<span class="desc small">
											Sometimes it takes a lifetime to win a battle.
										</span>
									</div>
								</a>
							</li>


							<li class=" status-busy">
								<a href="javascript:;">
									<div class="user-img">
										<img src="data/profile/avatar-2.png" alt="user-image" class="img-circle img-inline">
									</div>
									<div>
										<span class="name">
											<strong>Linette Lheureux</strong>
											<span class="time small">- 16th Mar</span>
											<span class="profile-status busy pull-right"></span>
										</span>
										<span class="desc small">
											Sometimes it takes a lifetime to win a battle.
										</span>
									</div>
								</a>
							</li>


							<li class=" status-away">
								<a href="javascript:;">
									<div class="user-img">
										<img src="data/profile/avatar-3.png" alt="user-image" class="img-circle img-inline">
									</div>
									<div>
										<span class="name">
											<strong>Araceli Boatright</strong>
											<span class="time small">- 16th Mar</span>
											<span class="profile-status away pull-right"></span>
										</span>
										<span class="desc small">
											Sometimes it takes a lifetime to win a battle.
										</span>
									</div>
								</a>
							</li>
							-->

						</ul>

					</div>
					
				</div>		

			</div> <!-- End .row -->
			
			<!--
			<div class="row">

				<div class="col-md-3 col-sm-5 col-xs-12">

					<div class="r1_graph1 db_box">
						<span class='bold'>98.95%</span>
						<span class='pull-right'><small>SERVER UP</small></span>
						<div class="clearfix"></div>
						<span class="db_dynamicbar">Loading...</span>
					</div>


					<div class="r1_graph2 db_box">
						<span class='bold'>2332</span>
						<span class='pull-right'><small>USERS ONLINE</small></span>
						<div class="clearfix"></div>
						<span class="db_linesparkline">Loading...</span>
					</div>


					<div class="r1_graph3 db_box">
						<span class='bold'>342/123</span>
						<span class='pull-right'><small>ORDERS / SALES</small></span>
						<div class="clearfix"></div>
						<span class="db_compositebar">Loading...</span>
					</div>

				</div>



				<div class="col-md-6 col-sm-7 col-xs-12">
					<div class="r1_maingraph db_box">
						<span class='pull-left'>
							<i class='icon-purple fa fa-square icon-xs'></i>&nbsp;<small>PAGE VIEWS</small>&nbsp; &nbsp;<i class='fa fa-square icon-xs icon-primary'></i>&nbsp;<small>UNIQUE VISITORS</small>
						</span>
						<span class='pull-right switch'>
							<i class='icon-default fa fa-line-chart icon-xs'></i>&nbsp; &nbsp;<i class='icon-secondary fa fa-bar-chart icon-xs'></i>&nbsp; &nbsp;<i class='icon-secondary fa fa-area-chart icon-xs'></i>
						</span>

						<div id="db_morris_line_graph" style="height:272px;width:95%;"></div>
						<div id="db_morris_area_graph" style="height:272px;width:90%;display:none;"></div>
						<div id="db_morris_bar_graph" style="height:272px;width:90%;display:none;"></div>
					</div>
				</div>

				<div class="col-md-3 col-sm-12 col-xs-12">
					<div class="r1_graph4 db_box">
						<span class=''>
							<i class='icon-purple fa fa-square icon-xs icon-1'></i>&nbsp;<small>CPU USAGE</small>
						</span>
						<canvas width='180' height='90' id="gauge-meter"></canvas>
						<h4 id='gauge-meter-text'></h4>
					</div>
					<div class="r1_graph5 db_box col-xs-6">
						<span class=''><i class='icon-purple fa fa-square icon-xs icon-1'></i>&nbsp;<small>LONDON</small>&nbsp; &nbsp;<i class='fa fa-square icon-xs icon-2'></i>&nbsp;<small>PARIS</small></span>
						<div style="width:120px;height:120px;margin: 0 auto;">
							<span class="db_easypiechart1 easypiechart" data-percent="66"><span class="percent" style='line-height:120px;'></span></span>
						</div>
					</div>

				</div>

			</div> <!-- End .row -->

			<!--
			<div class="row">
				<div class="col-md-8 col-sm-12 col-xs-12">
					<div class="wid-vectormap">
						<h4>Visitor's Statistics</h4>
						<div class="row">
							<div class="col-md-9 col-sm-9 col-xs-12">
								<figure>
									<div id="db-world-map-markers" style="width: 100%; height: 300px"></div>        
								</figure>
							</div>
							<div class="col-md-3 col-sm-3 col-xs-12 map_progress">
								<h4>Unique Visitors</h4>
								<span class='text-muted'><small>Last Week Rise by 62%</small></span>
								<div class="progress"><div class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="62" aria-valuemin="0" aria-valuemax="100" style="width: 62%"></div></div>
								<br>
								<h4>Registrations</h4>
								<span class='text-muted'><small>Up by 57% last 7 days</small></span>
								<div class="progress"><div class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="57" aria-valuemin="0" aria-valuemax="100" style="width: 57%"></div></div>
								<br>
								<h4>Direct Sales</h4>
								<span class='text-muted'><small>Last Month Rise by 22%</small></span>
								<div class="progress"><div class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="22" aria-valuemin="0" aria-valuemax="100" style="width: 22%"></div></div>
							</div>
						</div>
					</div>
				</div>		

				<div class="col-md-4 col-sm-12 col-xs-12">
					<div class="r2_graph1 db_box">



						<form id="rickshaw_side_panel">
							<section><div id="legend"></div></section>
							<section>
								<div id="renderer_form" class="toggler">
									<select name="renderer">
										<option value="area" selected>Area</option>
										<option value="bar">Bar</option>
										<option value="line">Line</option>
										<option value="scatterplot">Scatter</option>
									</select>
								</div>
							</section>
							<section>
								<div id="offset_form">
									<label for="stack">
										<input type="radio" name="offset" id="stack" value="zero" checked>
										<span>stack</span>
									</label>
									<label for="stream">
										<input type="radio" name="offset" id="stream" value="wiggle">
										<span>stream</span>
									</label>
									<label for="pct">
										<input type="radio" name="offset" id="pct" value="expand">
										<span>pct</span>
									</label>
									<label for="value">
										<input type="radio" name="offset" id="value" value="value">
										<span>value</span>
									</label>
								</div>
								<div id="interpolation_form">
									<label for="cardinal">
										<input type="radio" name="interpolation" id="cardinal" value="cardinal" checked>
										<span>cardinal</span>
									</label>
									<label for="linear">
										<input type="radio" name="interpolation" id="linear" value="linear">
										<span>linear</span>
									</label>
									<label for="step">
										<input type="radio" name="interpolation" id="step" value="step-after">
										<span>step</span>
									</label>
								</div>
							</section>
						</form>

						<div id="chart_container" class="rickshaw_ext">
							<div id="chart"></div>
							<div id="timeline"></div>
						</div>

						<div id='rickshaw_side_panel' class="rickshaw_sliders">
							<section>
								<h5>Smoothing</h5>
								<div id="smoother"></div>
							</section>
							<section>
								<h5>Preview Range</h5>
								<div id="preview" class="rickshaw_ext_preview"></div>
							</section>
						</div>

					</div>
					<!-- 
													<div class="r2_counter1 db_box">
															counter 1
													</div>
					
													<div class="r2_counter2 db_box">
															counter 2
													</div> -->

				</div>		

			</div> <!-- End .row -->




<!--
			<div class="row">
				<div class="col-md-3 col-sm-6 col-xs-6">
					<div class="r4_counter db_box">
						<i class='pull-left fa fa-thumbs-up icon-md icon-rounded icon-primary'></i>
						<div class="stats">
							<h4><strong>45%</strong></h4>
							<span>New Orders</span>
						</div>
					</div>
				</div>
				<div class="col-md-3 col-sm-6 col-xs-6">
					<div class="r4_counter db_box">
						<i class='pull-left fa fa-shopping-cart icon-md icon-rounded icon-orange'></i>
						<div class="stats">
							<h4><strong>243</strong></h4>
							<span>New Products</span>
						</div>
					</div>
				</div>
				<div class="col-md-3 col-sm-6 col-xs-6">
					<div class="r4_counter db_box">
						<i class='pull-left fa fa-dollar icon-md icon-rounded icon-purple'></i>
						<div class="stats">
							<h4><strong>$3424</strong></h4>
							<span>Profit Today</span>
						</div>
					</div>
				</div>
				<div class="col-md-3 col-sm-6 col-xs-6">
					<div class="r4_counter db_box">
						<i class='pull-left fa fa-users icon-md icon-rounded icon-warning'></i>
						<div class="stats">
							<h4><strong>1433</strong></h4>
							<span>New Users</span>
						</div>
					</div>
				</div>
			</div>  End .row -->	





		</div>
	</section>
</div>

<!-- Modal para las asistencia de los trabajadores -->
<div class="modal fade" id="asistenciasModal"  tabindex="-1" role="dialog" aria-labelledby="ultraModal-Label" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Confirmación de Asistencia</h4>
			</div>
			<div class="modal-body">
					<input type="hidden" id="empledoNip">
					<div class="form-group">
								<label for="">Tipo de asistencia</label>
								<select name="" id="selTipoAsistencia" class="form-control">
									<option value="PUNTUAL">PUNTUAL</option>
									<option value="RETARDO MENOR">RETARDO MENOR O IGUAL A 5 MIN</option>
									<option value="RETARDO MAYOR">RETARDO MAYOR a 5 MIN Y MENOR A 10 MIN</option>
									<option value="FALTA POR RETARDO">RETARDO MAYOR A 10 MIN</option>
									<option value="INASISTENCIA">NO SE PRESENTO A LABORAR</option>
									<option value="PERMISO">TIENE PERMISO</option>
								</select>
					</div>
					<div class="form-group">
							<label for="">Observaciones</label>
							<textarea class="form-control" id ="observacionesAsistencia" rows="5" style="resize: none"></textarea>
					</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-info" data-dismiss="modal" id="guardarAsistencia" >Guardar</button>
				<button type="button" class="btn btn-default" data-dismiss="modal" >Cancelar</button>
			</div>
		</div>
	</div>
</div>


<div class="modal fade" id="modalPermisos"  tabindex="-1" role="dialog" aria-labelledby="ultraModal-Label" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Permisos</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label for="">Fecha</label>
							<input type="text" name="" id="fechaPermiso" class="form-control datepicker" data-format="dd/mm/yyyy">
						</div>	
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="">Puesto</label>
							<select name="" id="puestoPermiso" class="form-control"></select>
						</div>	
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="">Sucursal</label>
							<select name="" id="sucursalPermiso" class="form-control"></select>
						</div>	
					</div>										
				</div>
				<div>
					<div class="row">
						<div class="col-md-4">
								<div class="form-group">
									<label for="">Días de permiso</label>
									<input type="number" id="diasPermiso" class="form-control">
								</div>
						</div>					
						<div class="col-md-8">
							<label for="">Seleccione la opción que aplique:</label>
							<label> <input type="radio" class="chkAccionPermiso" name="accionPermiso" value="1"> Con goce de sueldo </label>
							<label><input type="radio"  class="chkSAccionPermiso"  name="accionPermiso" value="0" checked >Sin goce de sueldo </label>
						</div>
					</div>				
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label for="">Motivo:</label>
								<textarea name="" id="motivoPermiso" rows="5" style=" resize: none" class="form-control"></textarea>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-info" data-dismiss="modal" onclick="guardarPermiso()">Guardar</button>
				<button type="button" class="btn btn-default" data-dismiss="modal" >Cancelar</button>
			</div>
		</div>
	</div>
</div>


<div class="modal fade" id="modalAccionCorrectiva"  tabindex="-1" role="dialog" aria-labelledby="ultraModal-Label" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Acciones Correctivas</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-9">
						<b>¿Acción Correctiva Amulable?</b>
						<br>
					</div>
					<div class="col-md-3">
						<div class="button-switch">
							<input type="checkbox" id="esAcumulable" class="switch" checked />
							<label for="switch-orange" class="lbl-off">No</label>
							<label for="switch-orange" class="lbl-on">Sí</label>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label for="">Fecha</label>
							<input type="text" name="" id="fechaAccionCorrectiva" class="form-control datepicker" data-format="dd/mm/yyyy">
						</div>	
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="">Puesto</label>
							<select name="" id="puestoAccionCorrectiva" class="form-control"></select>
						</div>	
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="">Sucursal</label>
							<select name="" id="sucursalAccionCorrectiva" class="form-control"></select>
						</div>	
					</div>										
				</div>
				<div>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label for="">Motivo:</label>
								<textarea name="" id="motivoAccionCorrectiva" rows="5" style=" resize: none" class="form-control"></textarea>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label for="">Plan de Acción:</label>
								<textarea name="" id="planAccionCorrectiva" rows="5" style=" resize: none" class="form-control"></textarea>
							</div>						
						</div>
					</div>
					<div class="row">
						<div class="col-md-4">
							<label for="">Aplica sanción económica:</label>
							<label> <input type="radio" class="chkSancion" name="sancionEconomica" value="1"> Si </label>
							<label><input type="radio"  class="chkSancion"  name="sancionEconomica" value="0" checked placeholder="0.00">No </label>
						</div>
						<div class="col-md-4 fieldsDescuento">
							<div class="form-group">
								<label for="">Indica monto:</label>
								<input type="number" name="" id="montoAccionCorrectiva" class="form-control">
							</div>
						</div>
						<div class="col-md-4 fieldsDescuento">
							<div class="form-group">
								<label for="">Fecha de descuento:</label>
								<input type="text" name="" id="fechaDescuentoAccionCorrectiva" class="form-control datepicker" data-format="dd/mm/yyyy">
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-info" data-dismiss="modal" onclick="guardarAccionCorrectiva()">Guardar</button>
				<button type="button" class="btn btn-default" data-dismiss="modal" >Cancelar</button>
			</div>
		</div>
	</div>
</div>



<!-- modal start -->
<div class="modal fade" id="modalCambioAdscripcion"  tabindex="-1" role="dialog" aria-labelledby="ultraModal-Label" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Cambiar de Sucursal</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
								<label for="">Fecha</label>
								<input type="text" name="" id="fechaAdscripcion" class="form-control datepicker" data-format="dd/mm/yyyy">
						</div>
						<div class="form-group">
							<label for="field-1" class="control-label">Sucursal</label>

							<select name="" id="sucursalesAdscripcion" class="form-control"></select>
						</div>	

					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-info" data-dismiss="modal" onclick="guardarAdscripcion()">Guardar</button>
				<button type="button" class="btn btn-default" data-dismiss="modal" >Cancelar</button>
			</div>
		</div>
	</div>
</div>
<!-- modal end -->



<!-- modal start -->
<div class="modal fade" id="tareasmodal"  tabindex="-1" role="dialog" aria-labelledby="ultraModal-Label" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">REALIZACI&Oacute;N DE TAREA</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">

						<div class="form-group">
							<label for="field-1" class="control-label">Observaciones</label>

							<input type="text" class="form-control" id="tarobservaciones">
						</div>	

					</div>
				</div>
				<div class="row">							
					<div class="col-md-4">

						<div class="form-group">
							<label for="field-2" class="control-label">Foto</label>

							<input accept="image/*"  type="file" id="tarfoto" name="tarfoto" capture/>
						</div>	

					</div>

				</div>

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-info" data-dismiss="modal" onclick="guardaCheck()">Guardar</button>
				<button type="button" class="btn btn-default" data-dismiss="modal" onclick="noGuardaCheck()">Cancelar</button>
			</div>
		</div>
	</div>
</div>
<!-- modal end -->

<!-- modal start -->
<div class="modal fade" id="uncheckModal"  tabindex="-1" role="dialog" aria-labelledby="ultraModal-Label" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">DESHACER REALIZACI&OacuteN DE TAREA</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					Proceder&aacute; a eliminar el registro de la tarea realizada. ¿Desea continuar?
				</div>

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-info" data-dismiss="modal" onclick="eliminaCheck()">Aceptar</button>
				<button type="button" class="btn btn-default" data-dismiss="modal" onclick="noEliminaCheck()">Cancelar</button>
			</div>
		</div>
	</div>
</div>
<!-- modal end -->
