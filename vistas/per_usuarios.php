

		<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>
			<div class="page-title">

				<div class="pull-left">
					<h1 class="title">PERSONAL</h1>                            </div>


			</div>
		</div>
		<div class="clearfix"></div>


		<div class="col-lg-12">
			<div class="col-md-12 col-sm-12 col-xs-12 btn-iconic" style="text-align:right">
				<button type="button" class="btn btn-default" onclick="nuevo()"><i class="fa fa-file-o"></i></button>
				<button type="button" class="btn btn-primary vimeo" onclick="guardar()"><i class="fa fa-save"></i></button>
				<button type="button" class="btn btn-primary youtube" onclick="eliminar()"><i class="fa fa-close"></i></button>
			</div>
			<div class="col-lg-12 col-md-12 col-xs-12 col-sm-12">
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
									<label class="form-label" for="field-1">Apellido Paterno</label>
									<span class="desc"> </span>
									<div class="controls">
										<input type="text" class="form-control" id="paterno" >
									</div>
								</div>
							</div>
							<div class="col-xs-2">
								<div class="form-group">
									<label class="form-label" for="field-1">Apellido Materno</label>
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
							<div class="col-xs-2">
								<div class="form-group">
									<label class="form-label" for="field-1">Email</label>
									<span class="desc"></span>
									<div class="controls">
										<input type="text" class="form-control" id="email" data-mask="email" >
									</div>
								</div>
							</div>
							<div class="col-xs-2">
								<div class="form-group">
									<label class="form-label" for="field-1">Telefono</label>
									<span class="desc"> </span>
									<div class="controls">
										<input type="text" class="form-control" id="telefono" data-mask="phone" >
									</div>
								</div>
							</div>
							<div class="col-xs-2">
								<div class="form-group">
									<label class="form-label" for="field-1">Puesto</label>
									<span class="desc"> </span>
									<div class="controls">
										<select class="form-control" id="puesto" >
										</select>
									</div>
								</div>
							</div>
							<div class="col-xs-2">
								<div class="form-group">
									<label class="form-label" for="field-1">Tipo</label>
									<span class="desc"> </span>
									<div class="controls">
										<select class="form-control" id="tipo" >
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
				</section>
				
				<section class="box ">
					<header class="panel_header">
						<h2 class="title pull-left">Datos de Usuario</h2>
						<div class="actions panel_actions pull-right">
							<i class="box_toggle fa fa-chevron-down"></i>
							<i class="box_setting fa fa-cog" data-toggle="modal" href="#section-settings"></i>
							<i class="box_close fa fa-times"></i>
						</div>
					</header>
					<div class="content-body">
						<div class='row'>
							<div class="col-xs-2">
								<div class="form-group">
									<label class="form-label" for="field-1">Usuario</label>
									<span class="desc"> </span>
									<div class="controls">
										<input type="text" class="form-control" id="usuario" >
									</div>
								</div>
							</div>
							<div class="col-xs-3">
								<div class="form-group">
									<label class="form-label" for="field-1">Contrase&ntilde;a</label>
									<span class="desc"> </span>
									<div class="controls">
										<input type="text" class="form-control" id="password" >
									</div>
								</div>
							</div>
							<div class="col-xs-3">
								<div class="form-group">
									<label class="form-label" for="field-1">Rep. Contrase&ntilde;a</label>
									<span class="desc"> </span>
									<div class="controls">
										<input type="text" class="form-control" id="password2" >
									</div>
								</div>
							</div>
							<div class="col-xs-3">
								<div class="form-group">
									<label class="form-label" for="field-1">Nivel</label>
									<span class="desc"> </span>
									<div class="controls">
										<select class="form-control" id="nivel" >
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
				</section>
				
				<section class="box ">
					<div class="content-body">    
						<div class="row">
							<div class="col-md-12 col-sm-12 col-xs-12">

								<table class="table table-hover">
									<thead>
										<tr>
											<th>Nombre(s)</th>
											<th>Usuario</th>
											<th>Puesto</th>
											<th>Tipo</th>
											<th>Nivel</th>
										</tr>
									</thead>
									<tbody id="tbody">
										<tr>
											<td>Administrador General</td>
											<td>admin</td>
											<td>Enfermera</td>
											<td>Usuario</td>
											<td>Administrador</td>
										</tr>
									</tbody>
								</table>

							</div>
						</div>
					</div>
				</section>
			</div>
		</div>
	