

		<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>
			<div class="page-title">

				<div class="pull-left">
					<h1 class="title">ESTADOS FINANCIEROS</h1>
				</div>


			</div>
		</div>
		<div class="clearfix"></div>
		<div class="col-md-12">

			<ul class="nav nav-tabs">
				<li class="active">
					<a href="#edoresultados" data-toggle="tab">
						Estado de Resultados
					</a>
				</li>
				<li>
					<a href="#balancegeneral" data-toggle="tab">
						Balance General 
					</a>
				</li>
				<li>
					<a href="#balanza" data-toggle="tab">
						Balanza de Comprobaci&oacute;n
					</a>
				</li>
			</ul>

			<div class="tab-content">
				<div class="tab-pane fade in active" id="edoresultados">
					<div class="row">
						<div class="col-md-3">

							<div class="form-group">
								<label for="field-1" class="control-label">UDN</label>

								<input type="text" id="idOp" style="display:none"><select class="form-control" id="udn"></select>
							</div>	

						</div>
						<div class="col-xs-2">
							<label class="form-label" for="field-1">Fecha Inicio</label>
							<div class="controls">
								<input type="text" class="form-control datepicker col-md-4" data-format="dd/mm/yyyy" id="fechainicio">
							</div>
						</div>
						<div class="col-xs-2">
							<label class="form-label" for="field-1">Fecha Final</label>
							<div class="controls">
								<input type="text" class="form-control datepicker col-md-4" data-format="dd/mm/yyyy" id="fechafin">
							</div>
						</div>
						<div class="col-xs-2">
							</br>
							<a href="javascript: getEdoresultados();" class="btn btn-primary btn-block">Generar</a>
						</div>
						<div class="col-xs-2">
							</br>
							<a data-toggle="modal" href="#modelpe" class="btn btn-warning btn-block">P.E.</a>
						</div>
						<div class="col-xs-2">
							</br>
							<a  href="#modelpe" class="btn btn-primary btn-block" id="btn-descargaEdoFinanciero">Descargar</a>
						</div>
						<div class="col-xs-2">
							</br>
							<a  href="" class="btn btn-primary btn-block" id="btn-descargar">Cargando...</a>
						</div>
					</div>
					<hr/>
					<div class="row">
						<div class="col-xs-12">
							
							<table class="table table-hover">
									<thead>
										<tr>
											<th></th>
										</tr>
									</thead>
									<tbody id="tbodyedoresultados" style="cursor:pointer">
									</tbody>
								</table>
						</div>
					</div>
				</div>
				<div class="tab-pane fade" id="balancegeneral">
					
				</div>
				<div class="tab-pane fade" id="balanza">
					
				</div>
			</div>

		</div>
		<div class="clearfix"><br></div>
		
		<!-- modal start -->
		<div class="modal fade" id="modelpe"  tabindex="-1" role="dialog" aria-labelledby="ultraModal-Label" aria-hidden="true">
			<div class="modal-dialog" style="width: 96%">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title">PUNTO DE EQUILIBRIO</h4>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-lg-12">
								<section class="box ">
									<div class="content-body">    
										<div class="row">
											<div class="col-md-12 col-sm-12 col-xs-12">
												<div id="morris_line_graph"></div>	                
											</div>
										</div>
									</div>
								</section>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-info" data-dismiss="modal">Cerrar</button>
					</div>
				</div>
			</div>
		</div>
		<!-- modal end -->

		<!-- modal start -->
		<div class="modal fade" id="modaldetalles"  tabindex="-1" role="dialog" aria-labelledby="ultraModal-Label" aria-hidden="true">
			<div class="modal-dialog" style="width: 96%">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title">DETALLES DE LA CUENTA</h4>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-lg-12">
								<table id="tabledetallescuenta" class="table table-striped dt-responsive display" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th>Sucursal</th>
											<th>Folio</th>
											<th>Emisor</th>
											<th>Receptor</th>
											<th>Concepto</th>
											<th>UUID</th>
											<th>Fecha</th>
											<th>Importe</th>
											<th></th>
										</tr>
									</thead>
									
									<tfoot>
										<tr>
											<th>Sucursal</th>
											<th>Folio</th>
											<th>Emisor</th>
											<th>Receptor</th>
											<th>Concepto</th>
											<th>UUID</th>
											<th>Fecha</th>
											<th>Importe</th>
											<th></th>
										</tr>
									</tfoot>

									<tbody id="tbodymodal">
										
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-info" data-dismiss="modal">Cerrar</button>
					</div>
				</div>
			</div>
		</div>
		<!-- modal end -->

	