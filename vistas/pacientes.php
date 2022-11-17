

		<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>
			<div class="page-title">

				<div class="pull-left">
					<h1 class="title">PACIENTES</h1>                            </div>


			</div>
		</div>
		<div class="clearfix"></div>


		<div class="col-lg-12">
			<div class="col-md-12 col-sm-12 col-xs-12 btn-iconic" style="text-align:right">
				<button type="button" class="btn btn-default" onclick="nuevo()"><i class="fa fa-file-o"></i></button>
				<button type="button" class="btn btn-primary vimeo" onclick="guardar()"><i class="fa fa-plus"></i></button>
				<button type="button" class="btn btn-primary youtube" onclick="eliminar()"><i class="fa fa-close"></i></button>
			</div>
			<div class="col-lg-12">
				<section class="box ">
					<header class="panel_header">
						<h2 class="title pull-left">Datos Personales</h2>
						<div class="actions panel_actions pull-right">
							<i class="box_toggle fa fa-chevron-down"></i>
							<i class="box_setting fa fa-cog" data-toggle="modal" href="#section-settings"></i>
							<i class="box_close fa fa-times"></i>
						</div>
					</header>
					<div class="content-body">
						<div class='row'>
							<div class="col-xs-4">
								<div class="form-group">
									<label class="form-label" for="field-1">Nombre(s)</label>
									<span class="desc"> </span>
									<div class="controls">
										<input type="text" class="form-control" id="nombre" >
									</div>
								</div>
							</div>
							<div class="col-xs-2">
								<div class="form-group">
									<label class="form-label" for="field-1">Ap. Paterno</label>
									<span class="desc"> </span>
									<div class="controls">
										<input type="text" class="form-control" id="paterno" >
									</div>
								</div>
							</div>
							<div class="col-xs-2">
								<div class="form-group">
									<label class="form-label" for="field-1">Ap. Materno</label>
									<span class="desc"> </span>
									<div class="controls">
										<input type="text" class="form-control" id="materno" >
									</div>
								</div>
							</div>
							<div class="col-xs-2">
								<div class="form-group">
									<label class="form-label" for="field-1">Sexo</label>
									<span class="desc"> </span>
									<div class="controls">
										<select class="form-control" id="sexo" >
											<option value="H">Hombre</option>
											<option value="M">Mujer</option>
										</select>
									</div>
								</div>
							</div>
							<div class="col-xs-2">
								<div class="form-group">
									<label class="form-label" for="field-1">CURP</label>
									<span class="desc"> </span>
									<div class="controls">
										<input type="text" class="form-control" id="curp" >
									</div>
								</div>
							</div>
						</div>
						<div class='row'>
							<div class="col-xs-3">
								<div class="form-group">
									<label class="form-label" for="field-1">Fec. Nacimiento</label>
									<div class="controls">
										<input type="text" value="01/Enero/1990" class="form-control datepicker col-md-4" data-format="dd/MM/yyyy">
									</div>
								</div>
							</div>
							<div class="col-xs-3">
								<div class="form-group">
									<label class="form-label" for="field-1">Telefono</label>
									<span class="desc"> </span>
									<div class="controls">
										<input type="text" class="form-control" id="telefono" data-mask="phone" >
									</div>
								</div>
							</div>
						</div>
					</div>
				</section>
				<section class="box ">
					<header class="panel_header">
						<h2 class="title pull-left">Historial Cl&iacute;nico</h2>
						<div class="actions panel_actions pull-right">
							<i class="box_toggle fa fa-chevron-down"></i>
							<i class="box_setting fa fa-cog" data-toggle="modal" href="#section-settings"></i>
							<i class="box_close fa fa-times"></i>
						</div>
					</header>
					<div class="content-body">    
						<div class="row">

							<div class="col-md-12">

								<ul class="nav nav-tabs vertical col-lg-3 col-md-3 col-sm-4 col-xs-4 left-aligned primary">
									<li class="active">
										<a href="#diagnostico1" data-toggle="tab">
											<i class="fa fa-stethoscope"></i>Diagnostico 1<br>
											<i class="fa fa-bed"></i>Ambulatorio :: General<br>
											<i class="fa fa-user-md"></i>Administrador General<br>
											<i class="fa fa-address-card-o"></i>Juan Perez Ruiz<br>
											<i class="fa fa-chevron-right"></i>01/12/2017 23:12<br>
											<i class="fa fa-chevron-left"></i>03/12/2017 18:35<br>
										</a>
									</li>
									<li>
										<a href="#diagnostico2" data-toggle="tab">
											<i class="fa fa-stethoscope"></i>Diagnostico 2<br>
											<i class="fa fa-bed"></i>Ambulatorio :: General<br>
											<i class="fa fa-user-md"></i>Administrador General<br>
											<i class="fa fa-address-card-o"></i>Juan Perez Ruiz<br>											
											<i class="fa fa-chevron-right"></i>Ingreso: 28/11/2017 18:01<br>
											<i class="fa fa-chevron-left"></i>Alta: 28/11/2017 20:11<br>
										</a>
									</li>
									<!--
									<li>
										<a href="#profile-5" data-toggle="tab">
											<i class="fa fa-user"></i> Profile 
										</a>
									</li>
									<li>
										<a href="#messages-5" data-toggle="tab">
											<i class="fa fa-envelope"></i> Messages
										</a>
									</li>
									<li>
										<a href="#settings-5" data-toggle="tab">
											<i class="fa fa-cog"></i> Settings
										</a>
									</li>
									-->
								</ul>					

								<div class="tab-content vertical col-lg-9 col-md-9 col-sm-8 col-xs-8 left-aligned primary">
									<div class="tab-pane fade in active" id="diagnostico1">
										<div class="col-md-12">
										<ul class="timeline">
                                            <li>
                                                <div class="timeline-badge animated flipInY"><i class="glyphicon glyphicon-filter"></i></div>
                                                <div class="timeline-panel animated flipInY">
                                                    <div class="timeline-heading">
                                                        <h4 class="timeline-title">Nebulizador [21]</h4>
                                                        <p><small class="text-muted"><i class="glyphicon glyphicon-time"></i> 01/12/2017 23:30 por Administrador General</small></p>
                                                    </div>
                                                    <div class="timeline-body">
                                                        <p>
															<b>CC:</b> 00<br/>
															<b>MS:</b> 00<br/>
															<b>MT:</b> 00<br/>
															<b>T:</b> 00<br/>
															<b>AMB:</b> 00<br/>
															<b>CPAP:</b> 00<br/>
														</p>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="timeline-inverted ">
                                                <div class="timeline-badge warning animated flipInY"><i class="glyphicon glyphicon-ban-circle"></i></div>
                                                <div class="timeline-panel animated flipInY">
                                                    <div class="timeline-heading">
                                                        <h4 class="timeline-title">Albuterol Sulfate</h4>
                                                        <p><small class="text-muted"><i class="glyphicon glyphicon-time"></i> 01/12/2017 23:30 por Administrador General</small></p>            
                                                    </div>
                                                    <div class="timeline-body">
                                                        <p>
															<b>Presentacion:</b> Caja<br/>
															<b>Unidad:</b> Sobre Solucion<br/>
															<b>mg/ml:</b> 0.63mg/3ml<br/>
															<b>Aplicacion:</b> Sobre<br/>
															<b>Observaciones:</b> <br/>
														</p>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
										</div>
									</div>
									<div class="tab-pane fade" id="diagnostico2">
										<div class="col-md-12">
										<ul class="timeline">
                                            <li>
                                                <div class="timeline-badge animated flipInY"><i class="glyphicon glyphicon-filter"></i></div>
                                                <div class="timeline-panel animated flipInY">
                                                    <div class="timeline-heading">
                                                        <h4 class="timeline-title">Nebulizador [21]</h4>
                                                        <p><small class="text-muted"><i class="glyphicon glyphicon-time"></i> 01/12/2017 23:30 por admin</small></p>
                                                    </div>
                                                    <div class="timeline-body">
                                                        <p>
															<b>CC:</b> 00<br/>
															<b>MS:</b> 00<br/>
															<b>MT:</b> 00<br/>
															<b>T:</b> 00<br/>
															<b>AMB:</b> 00<br/>
															<b>CPAP:</b> 00<br/>
														</p>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="timeline-inverted ">
                                                <div class="timeline-badge warning animated flipInY"><i class="glyphicon glyphicon-ban-circle"></i></div>
                                                <div class="timeline-panel animated flipInY">
                                                    <div class="timeline-heading">
                                                        <h4 class="timeline-title">Albuterol Sulfate</h4>
                                                        <p><small class="text-muted"><i class="glyphicon glyphicon-time"></i> 01/12/2017 23:30 por admin</small></p>            
                                                    </div>
                                                    <div class="timeline-body">
                                                        <p>
															<b>Presentacion:</b> Caja<br/>
															<b>Unidad:</b> Sobre Solucion<br/>
															<b>mg/ml:</b> 0.63mg/3ml<br/>
															<b>Aplicacion:</b> Sobre<br/>
															<b>Observaciones:</b> <br/>
														</p>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
										</div>
									</div>
									<!--
									<div class="tab-pane fade" id="profile-5">

										<p>That said, in some situations it may be desirable to turn this functionality off. Therefore, we also provide the ability to disable the data attribute API by unbinding all events on the document namespaced with data-api. </p>

									</div>
									<div class="tab-pane fade" id="messages-5">

										<p>Don't use data attributes from multiple plugins on the same element. For example, a button cannot both have a tooltip and toggle a modal. To accomplish this, use a wrapping element.</p>

									</div>

									<div class="tab-pane fade" id="settings-5">

										<p>We also believe you should be able to use all Bootstrap plugins purely through the JavaScript API. All public APIs are single, chainable methods, and return the collection acted upon.</p>
										<p>Don't use data attributes from multiple plugins on the same element. For example, a button cannot both have a tooltip and toggle a modal. To accomplish this, use a wrapping element.</p>					


									</div>
									-->
								</div>


								<!-- </div>	 -->

							</div>
							<div class="clearfix"><br>
							</div>
							
						</div>
					</div>
				</section>
				
				
			</div>
		</div>