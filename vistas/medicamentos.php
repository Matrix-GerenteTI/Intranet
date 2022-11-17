

		<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>
			<div class="page-title">

				<div class="pull-left">
					<h1 class="title">MEDICAMENTOS</h1>                            </div>


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
						<h2 class="title pull-left">Cat&aacute;logo</h2>
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
									<label class="form-label" for="field-1">Nombre</label>
									<span class="desc"> </span>
									<div class="controls">
										<input type="text" class="form-control" id="nombre" >
									</div>
								</div>
							</div>
							<div class="col-xs-6">
								<div class="form-group">
									<label class="form-label" for="field-1">Descripci&oacute;n</label>
									<span class="desc"> </span>
									<div class="controls">
										<input type="text" class="form-control" id="descripcion" >
									</div>
								</div>
							</div>
							<div class="col-xs-2">
								<div class="form-group">
									<label class="form-label" for="field-1">Clave</label>
									<span class="desc"> </span>
									<div class="controls">
										<input type="text" class="form-control" id="clave" >
									</div>
								</div>
							</div>
						</div>
						<div class='row'>
							<div class="col-xs-2">
								<div class="form-group">
									<label class="form-label" for="field-1">Unidades</label>
									<span class="desc"></span>
									<div class="controls">
										<input type="text" class="form-control" id="unidades" >
									</div>
								</div>
							</div>
							<div class="col-xs-2">
								<div class="form-group">
									<label class="form-label" for="field-1">mg/ml</label>
									<span class="desc"> </span>
									<div class="controls">
										<input type="text" class="form-control" id="mgml" >
									</div>
								</div>
							</div>
							<div class="col-xs-2">
								<div class="form-group">
									<label class="form-label" for="field-1">Presentaci&oacute;n Empaque</label>
									<span class="desc"> </span>
									<div class="controls">
										<select class="form-control" id="empaque" >
										</select>
										<button type="button" class="btn btn-default" ><i class="fa fa-plus"></i></button>
									</div>
								</div>
							</div>
							<div class="col-xs-2">
								<div class="form-group">
									<label class="form-label" for="field-1">Presentaci&oacute;n p/Unidad</label>
									<span class="desc"> </span>
									<div class="controls">
										<select class="form-control" id="unidad" >
										</select>
										<button type="button" class="btn btn-default" ><i class="fa fa-plus"></i></button>
									</div>
								</div>
							</div>
							<div class="col-xs-2">
								<div class="form-group">
									<label class="form-label" for="field-1">Clasificaci&oacute;n</label>
									<span class="desc"> </span>
									<div class="controls">
										<select class="form-control" id="clasificacion" >
										</select>
										<button type="button" class="btn btn-default" ><i class="fa fa-plus"></i></button>
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
											<th>Clave</th>
											<th>Nombre</th>
											<th>Empaque</th>
											<th>Presentaci&oacute;n</th>
											<th>Unidades</th>
											<th>ml/mg</th>
										</tr>
									</thead>

									<tfoot>
										<tr>
											<th>Clave</th>
											<th>Nombre</th>
											<th>Empaque</th>
											<th>Presentaci&oacute;n</th>
											<th>Unidades</th>
											<th>ml/mg</th>
										</tr>
									</tfoot>

									<tbody>
										
									</tbody>
								</table>




							</div>
						</div>
					</div>
				</section>
				
				
			</div>


	