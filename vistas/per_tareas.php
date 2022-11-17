

		<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>
			<div class="page-title">

				<div class="pull-left">
					<h1 class="title">TAREAS</h1>                            </div>


			</div>
		</div>
		<div class="clearfix"></div>


		<div class="col-lg-12">
			<div class="col-md-12 col-sm-12 col-xs-12 btn-iconic" style="text-align:right">
				<button type="button" class="btn btn-default" onclick="nuevo()"><i class="fa fa-file-o"></i></button>
				<button type="button" id="btnsave" class="btn btn-primary vimeo" onclick="guardar()"><i class="fa fa-plus"></i></button>
				<button type="button" class="btn btn-primary youtube" onclick="eliminar()"><i class="fa fa-close"></i></button>
			</div>
			<div class="col-lg-12">
				<section class="box ">
					<header class="panel_header">
						<h2 class="title pull-left">Asignaci&oacute;n de Tareas</h2>
						<div class="actions panel_actions pull-right">
							<i class="box_toggle fa fa-chevron-down"></i>
							<i class="box_setting fa fa-cog" data-toggle="modal" href="#section-settings"></i>
							<i class="box_close fa fa-times"></i>
						</div>
					</header>
					<div class="content-body">
						<div class='row'>
							<div class="col-xs-12">
								<div class="form-group">
									<label class="form-label" for="field-1">Descripci&oacute;n</label>
									<span class="desc"> </span>
									<div class="controls">
										<input type="text" style="display:none" id="id" ><input type="text" class="form-control" id="descripcion" >
									</div>
								</div>
							</div>
						</div>
						<div class='row'>							
							<div class="col-xs-3">
								<div class="form-group">
									<label class="form-label" for="field-1">Fec. Inicio</label>
									<span class="desc"> </span>
									<div class="controls">
										<input type="text" class="form-control datepicker col-md-4" id="fechaini" data-format="dd/mm/yyyy">
									</div>
								</div>
							</div>
							<div class="col-xs-3">
								<div class="form-group">
									<label class="form-label" for="field-1">Hora Inicio</label>
									<span class="desc"> </span>
									<div class="controls">
										<input type="text" class="form-control" id="horaini" >
									</div>
								</div>
							</div>
							<div class="col-xs-3">
								<div class="form-group">
									<label class="form-label" for="field-1">Fecha Fin</label>
									<span class="desc"> </span>
									<div class="controls">
										<input type="text" class="form-control datepicker col-md-4" id="fechafin" data-format="dd/mm/yyyy">
									</div>
								</div>
							</div>
							<div class="col-xs-3">
								<div class="form-group">
									<label class="form-label" for="field-1">Hora Fin</label>
									<span class="desc"> </span>
									<div class="controls">
										<input type="text" class="form-control" id="horafin" >
									</div>
								</div>
							</div>
						</div>
						<div class='row'>								
							<div class="col-xs-4">
								<div class="form-group">
									<label class="form-label" for="field-1">Departamento</label>
									<span class="desc"></span>
									<div class="controls">
										<select class="form-control" id="departamento" onchange="cmbPuestos()" >
										</select>
									</div>
								</div>
							</div>
							<div class="col-xs-4">
								<div class="form-group">
									<label class="form-label" for="field-1">Puesto</label>
									<span class="desc"></span>
									<div class="controls">
										<select class="form-control" id="puesto" >
										</select>
									</div>
								</div>
							</div>
							<div class="col-xs-4">
								<div class="form-group">
									<label class="form-label" for="field-1">Empleado</label>
									<span class="desc"></span>
									<div class="controls">
										<input type="text" style="display:none" id="idempleado" ><input type="text" class="form-control" id="empleado" readonly ><a data-toggle="modal" href="#modalusuarios" class="btn btn-primary btn-block"><i class="fa fa-user"></i></a>
									</div>
								</div>
							</div>
						</div>
						<div class='row'>								
							<div class="col-xs-4">
								<div class="form-group">
									<label class="form-label" for="field-1"><b>Programaci&oacute;n de la tarea</b></label>
									<span class="desc"></span>
									<div class="controls">
										<select class="form-control" id="programacion" onchange="activaProgramacion()" >
											<option value="diariamente">Diariamente</option>
											<option value="semanalmente">Semanalmente</option>
											<option value="quincenalmente">Quincenalmente</option>
											<option value="mensualmente">Mensualmente</option>
											<option value="especificos">Dias espec&iacute;ficos</option>
										</select>
									</div>
								</div>
							</div>
							<div class="col-xs-4">
								<div class="form-group">
									<div id="diario">
										<input type="checkbox" value="1" id="dia_1" name="dia_1" />&nbsp;Lunes
										<br/>
										<input type="checkbox" value="2" id="dia_2" name="dia_1" />&nbsp;Martes
										<br/>
										<input type="checkbox" value="3" id="dia_3" name="dia_1" />&nbsp;Mi&eacute;rcoles
										<br/>
										<input type="checkbox" value="4" id="dia_4" name="dia_1" />&nbsp;Jueves
										<br/>
										<input type="checkbox" value="5" id="dia_5" name="dia_1" />&nbsp;Viernes
										<br/>
										<input type="checkbox" value="6" id="dia_6" name="dia_1" />&nbsp;S&aacute;bado
										<br/>
										<input type="checkbox" value="0" id="dia_0" name="dia_1" />&nbsp;Domingo
									</div>
									<div id="semanal">
										<label class="form-label" for="field-1"><b>D&iacute;a de la semana</b></label>
										<span class="desc"></span>
										<div class="controls">
											<select class="form-control" id="diasemana" >
												<option value="1">Lunes</option>
												<option value="2">Martes</option>
												<option value="3">Mi&eacute;rcoles</option>
												<option value="4">Jueves</option>
												<option value="5">Viernes</option>
												<option value="6">S&aacute;bado</option>
												<option value="0">Domingo</option>
											</select>
										</div>
									</div>
									<div id="mensual">
										<label class="form-label" for="field-1"><b>D&iacute;a del mes</b></label>
										<span class="desc"></span>
										<div class="controls">
											<select class="form-control" id="diames" >
												<option value="1">1</option>
												<option value="2">2</option>
												<option value="3">3</option>
												<option value="4">4</option>
												<option value="5">5</option>
												<option value="6">6</option>
												<option value="7">7</option>
												<option value="8">8</option>
												<option value="9">9</option>
												<option value="10">10</option>
												<option value="11">11</option>
												<option value="12">12</option>
												<option value="13">13</option>
												<option value="14">14</option>
												<option value="15">15</option>
												<option value="16">16</option>
												<option value="17">17</option>
												<option value="18">18</option>
												<option value="19">19</option>
												<option value="20">20</option>
												<option value="21">21</option>
												<option value="22">22</option>
												<option value="23">23</option>
												<option value="24">24</option>
												<option value="25">25</option>
												<option value="26">26</option>
												<option value="27">27</option>
												<option value="28">28</option>
												<option value="29">29</option>
												<option value="30">30</option>
												<option value="31">31</option>
											</select>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</section>
				<section class="box ">
					<!--<header class="panel_header">
						<h2 class="title pull-left">Cat&aacute;logo</h2>
						<div class="actions panel_actions pull-right">
							<i class="box_toggle fa fa-chevron-down"></i>
							<i class="box_setting fa fa-cog" data-toggle="modal" href="#section-settings"></i>
							<i class="box_close fa fa-times"></i>
						</div>
					</header>-->
					<div class="content-body">    <div class="row">
							<div class="col-md-12 col-sm-12 col-xs-12">


								<table id="example-1" class="table table-striped dt-responsive display" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th>Tarea</th>
											<th>Departamento</th>
											<th>Puesto</th>
											<th>Empleado</th>
										</tr>
									</thead>

									<tfoot>
										<tr>
											<th>Tarea</th>
											<th>Departamento</th>
											<th>Puesto</th>
											<th>Empleado</th>
										</tr>
									</tfoot>

									<tbody id="tbody" style="cursor:pointer">
										
									</tbody>
								</table>




							</div>
						</div>
					</div>
				</section>
				
				
			</div>

		</div>
		
		<!-- modal start -->
		<div class="modal fade" id="modalusuarios"  tabindex="-1" role="dialog" aria-labelledby="ultraModal-Label" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title">SELECCIONAR PERSONAL</h4>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<section class="box ">
									<div class="content-body">    
										<div class="row">
											<div class="col-md-12 col-sm-12 col-xs-12">

												<table class="table table-hover">
													<thead>
														<tr>
															<th>Nombre</th>
															<th>Puesto</th>
														</tr>
													</thead>
													<tbody id="tbodyusuarios" style="cursor:pointer">
														
													</tbody>
												</table>

											</div>
										</div>
									</div>
								</section>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
					</div>
				</div>
			</div>
		</div>
		<!-- modal end -->
	