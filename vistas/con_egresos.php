

		<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>
			<div class="page-title">

				<div class="pull-left">
					<h1 class="title">GASTOS</h1>                            
				</div>

			</div>
		</div>
		<div class="clearfix"></div>
		
		<div class="col-md-12">
			<ul class="nav nav-tabs">
				<li class="active">
					<a href="#facturados" data-toggle="tab">
						Gastos de Operacion
					</a>
				</li>
				<?php if($_SESSION['nivel']==1){ ?>
				<li>
					<a href="#financieros" data-toggle="tab">
						Gastos Financieros
					</a>
				</li>
				<li>
					<a href="#saldosBancarios" data-toggle="tab">Saldos Bancarios</a>
				</li>
				<li>
					<a href="#actualizarSaldo" data-toggle="tab">Actualizar Saldos</a>
				</li>

				<li>
					<a href="#acreedoresTab" data-toggle="tab">Acreedores</a>
				</li>
				<?php } ?>
			</ul>		

			<div class="tab-content">

				<div class="tab-pane fade in" id="acreedoresTab">
					<!-- Acreedores -->
					<?php if($_SESSION['nivel'] == 1){?>
						<!-- Container -->
						<div class="container container-acreedor-expands">
							<!-- Table -->
							<div class="tabular-data module">
								<!-- Table Row -->
								<div class="data-group">
									<div class="row">
										<div class="data-expands">
											<div class="col-lg-1 col-md-1">
												
											</div>
											<div class="col-lg-8 col-md-7">
												<span class="title">Pagos a acreedores</span>
											</div>
											<div class="col-lg-3 col-md-4">
												<div class="red uppercase"><strong><i class="fa fa-exclamation-circle"></i> Nuevo crédito</strong>
												<span class="row-toggle">
													<span class="horizontal"></span>
													<span class="vertical"></span>
												</span>
												</div>
											</div>
										</div>
										<div class="expandable">
											<div class="col-md-12" style="text-align:right; margin-top: 1%; margin-bottom: 1%;">
												<button type="button" class="btn btn-primary" onclick="saveNewCreditor()"><i class="fa fa-check-square-o"></i></button>
												<!--button class="btn btn-toolbar boton-image fondoImg" data-toggle="tooltip" data-placement="top" title="Gastos de nomina" id="modalNomina"></button>
												<button type="button" class="btn btn-info " data-toggle="modal" data-target="#modal-xml"><span class="glyphicon glyphicon-asterisk"></span></button>
												<button type="button" class="btn btn-default" onclick="nuevo()" role="button"><i class="fa fa-file-o"></i></button-->
											</div>
											<div class="row">
												<div class="row" style="padding: 3%;">
													<div class="col-md-8">

														<div class="form-group">
															<label for="field-1" class="control-label">Entidad crediticia</label>

															<input type="text" class="form-control" id="entCredit">
														</div>	

													</div>
													<div class="col-md-4">

														<div class="form-group">
															<label for="field-1" class="control-label">Alias</label>

															<input type="text" class="form-control col-md-4" id="aliasEnt">

														</div>	

													</div>
												</div>
												<div class="row" style="padding: 3%;">							
													<div class="col-md-4">

														<div class="form-group">
															<label for="field-1" class="control-label">Monto ($)</label>
															<input type="text" class="form-control" id="montoTotalAcreedor">
														</div>	

													</div>
													
													<div class="col-md-4">

														<div class="form-group">
															<label for="field-2" class="control-label">Plazo (Meses)</label>

															<input type="text" class="form-control" id="plazoPagosAcreedor">
														</div>	

													</div>

													<div class="col-md-4">

														<div class="form-group">
															<label for="field-3" class="control-label">Interes (%)</label>

															<input type="text" class="form-control" id="interesAcreedor">
														</div>	

													</div>
													<div class="col-md-4">
														<div class="form-group">
															<label for="field-4" class="control-label">Fecha de apertura de credito</label>
															<br>
															<input type="text" class="form-control datepicker col-md-4" id="datepickerFechaAdeudo" data-format="yyyy/mm/dd">
														</div>
													</div>
												</div>
												<br>
												<hr>
												<br>
											</div>
										</div>
										<div class="row" style="padding: 3%;">
											<div class="col-lg-12 table-responsive">
												<table id="tableHistorialAcreedores" class="table table-hover">
													<thead>
														<tr>
															<th>Acreedor</th>
															<th>Crédito</th>
															<th>Plazo</th>
															<th>Interes</th>
															<th>Capital</th>
															<th>Amortización</th>
															<th>Fecha de apertura de credito</th>
															<th>Acciones</th>
														</tr>
													</thead>

													<tfoot>
														<tr>
															<th>Acreedor</th>
															<th>Crédito</th>
															<th>Plazo</th>
															<th>Interes</th>
															<th>Capital</th>
															<th>Amortización</th>
															<th>Fecha de apertura de credito</th>
															<th>Acciones</th>
														</tr>
													</tfoot>

													<tbody id="tbodyHistorialAcreedores">
														
													</tbody>
												</table>
											</div>
										</div>
									</div>
								</div>
								<!-- Table Row -->
							</div>
							<!-- Table -->
						</div>
						<!-- Container -->
						<!-- Modal detalles pagos-->
						<div class="modal fade" id="modalShowAcreedores" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
							<div class="modal-dialog" role="document" style="top: 8rem">
								<div class="modal-content">
									<div class="modal-header">
										<h5 class="modal-title" id="exampleModalLabel">DETALLE DE PAGO: </h5><p id="nombreAcreedor"></p>
										<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">&times;</span>
										</button>
									</div>
									<div class="modal-body">
										<table id="asd" class="table table-hover">
											<thead>
												<tr>
													<th>Monto total abonado</th>
													<th>Interes pagado</th>
													<th>Fecha de abono a credito</th>
												</tr>
											</thead>

											<tfoot>
												<tr>
													<th>Monto total abonado</th>
													<th>Interes pagado</th>
													<th>Fecha de abono a credito</th>
												</tr>
											</tfoot>

											<tbody id="tbodyasd">
												
											</tbody>
										</table>
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
									</div>
								</div>
							</div>
						</div>
						<!-- Modal Aplicar pago-->
						<div class="modal fade" id="modalAplicarPagoAcreedores" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
							<div class="modal-dialog" role="document" style="top: 8rem">
								<div class="modal-content">
									<div class="modal-header">
										<h5 class="modal-title" id="acreedorLabel">Pago a: </h5>
										<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">&times;</span>
										</button>
									</div>
									<div class="modal-body">
										<div class="row">
											<div class="col-md-8">

												<div class="form-group">
													<label for="field-1" class="control-label">Monto a pagar</label>

													$<input type="text" class="form-control" id="pagoCreditoMonto" placeholder="$0.00">
												</div>	

											</div>
											<div class="col-md-4">

												<div class="form-group">
													<label for="field-1" class="control-label">Interes pagado</label>

													$<input type="text" class="form-control col-md-4" id="pagoCreditoInteres" placeholder="Monto pagado">

												</div>	

											</div>
											<div class="col-md-8">

												<div class="form-group">
													<input type="text" class="form-control datepicker col-md-4" id="pagoCreditoFecha" data-format="dd/mm/yyyy" placeholder="Fecha de aplicacion de pago">
												</div>
											</div>
											<input type="hidden" id="idDetalleDeuda">
											<input type="hidden" id="Amortizacion">
											<button type="button" class="btn btn-default" onclick="savePayToCreditor()" 
													style="position: relative; top:0.8rem; left:6.1rem; background-color: #507DBC; color: white;"><i class="fa fa-check-circle-o" aria-hidden="true"></i>
											</button>
										</div>
									</div>
									<div class="modal-footer">
										<button type="button" id="pagoAcreedor" class="btn btn-secondary" data-dismiss="modal">Close</button>
									</div>
								</div>
							</div>
						</div>
					<?php } ?>
					<!-- Final acreedores -->
				</div>
	
				<div class="tab-pane fade in active" id="facturados">

					<?php  if($_SESSION['nivel'] == 1){?>
						<!-- Button trigger modal -->
						<button type="button" id="botonHistorialPagos" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
							Pagos pendientes
						</button>

						<!-- Modal -->
						<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
							<div class="modal-dialog" role="document" style="width: 50%;">
								<div class="modal-content">
									<div class="modal-header">
										<h5 class="modal-title" id="exampleModalLabel">Pagos pendientes</h5>
										<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">&times;</span>
										</button>
									</div>
									<div class="modal-body">
										<div class="row">
											<div class="col-lg-12" >
												<form class="form-inline">
													<div class="form-group">
														<label for="field-1" class="control-label">Fecha de inicio</label>
														<br>
														<input type="text" class="form-control datepicker col-md-4" id="datepickerFechaInicio" data-format="yyyy/mm/dd">
														&nbsp --&nbsp
													</div>
													
													<div class="form-group">
														<label for="field-1" class="control-label">Fecha fin</label>
														<br>
														<input type="text" class="form-control datepicker col-md-4" id="datepickerFechaFin" data-format="yyyy/mm/dd">
													</div>
													<div class="form-group" style="position: relative; top: 0.6rem;">
														<button type="button" class="btn btn-primary" id="btn-filtro-historiaPagos">Filtrar</button>
													</div>
												</form>
											</div>
										</div>
								
										<div class="row">
											<div class="col-lg-12 table-responsive"  style="height:400px">
												<table id="tableHistorialPagos" class="table table-hover">
													<thead>
														<tr>
															<th>Beneficiario</th>
															<th>Concepto</th>
															<th>Monto</th>
															<th>Fecha del evento</th>
														</tr>
													</thead>

													<tfoot>
														<tr>
															<th>Beneficiario</th>
															<th>Concepto</th>
															<th>Monto</th>
															<th>Fecha del evento</th>
															<th></th>
														</tr>
													</tfoot>

													<tbody id="tbodyHistorialPagos">
														
													</tbody>
													<div class="col-md-12" style="text-align:right;padding-left:5%">
														<ul class="pagination">
															<li class="prev"><a href="#paginationContainerGO" class="changeBefore">← Anterior</a></li>
															<li class="next"><a href="#paginationContainerGO" class="changeNext">Siguiente → </a></li>
														</ul>
													</div>
												</table>
											</div>	
										</div>
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-secondary closingModal" data-dismiss="modal">Cerrar</button>
									</div>
								</div>
							</div>
						</div>
					<?php } ?>
					
					<div class="row">
					<div class="col-md-12" style="text-align:right">
						<button class="btn btn-toolbar boton-image fondoImg" data-toggle="tooltip" data-placement="top" title="Gastos de nomina" id="modalNomina"></button>
						<button type="button" class="btn btn-info " data-toggle="modal" data-target="#modal-xml"><span class="glyphicon glyphicon-asterisk"></span></button>
						<button type="button" class="btn btn-default" onclick="nuevo()" role="button"><i class="fa fa-file-o"></i></button>
						<button type="button" id="btnsave" class="btn btn-primary" onclick="guardaGastoOp()"><i class="fa fa-plus"></i></button>
					</div>
					</div>
					
					<div class="row">
						<div class="col-lg-12">
						<div class="row">
							<div class="col-md-4">

								<div class="form-group">
									<label for="field-1" class="control-label">UDN</label>

									<input type="text" id="idOp" style="display:none">
									<select class="form-control" id="fudn"></select>
								</div>	
	
							</div>
							<div class="col-md-8">

								<div class="form-group">
									<label for="field-2" class="control-label">Proveedor</label>
									<input type="text" class="form-control" id="fproveedor">
								</div>	

							</div>
						</div>
						<div class="row">
							<div class="col-md-8">

								<div class="form-group">
									<label for="field-1" class="control-label">Concepto</label>

									<input type="text" class="form-control" id="fdescripcion">
								</div>	

							</div>
							<div class="col-md-4">

								<div class="form-group">
									<label for="field-1" class="control-label">Fecha Gasto</label>

									<input type="text" class="form-control datepicker col-md-4" id="ffecha" data-format="dd/mm/yyyy">

								</div>	

							</div>
						</div>

						<div class="row">							

							<div class="col-md-6">

								<div class="form-group">
									<label for="field-2" class="control-label">RFC</label>

									<input type="text" class="form-control" id="frfc">
								</div>	

							</div>
							
							<div class="col-md-3">

								<div class="form-group">
									<label for="field-4" class="control-label">SERIE</label>

									<input type="text" class="form-control" id="fserie">
								</div>	

							</div>

							<div class="col-md-3">

								<div class="form-group">
									<label for="field-5" class="control-label">FOLIO</label>

									<input type="text" class="form-control" id="ffolio">
								</div>	

							</div>

						</div>
						<div class="row">							

							<div class="col-md-4">

								<div class="form-group">
									<label for="field-2" class="control-label">UUID</label>

									<input type="text" class="form-control" id="fuuid">
								</div>	

							</div>
							
							<div class="col-md-2">

								<div class="form-group">
									<label for="field-3" class="control-label">Banco</label>

									<input type="text" class="form-control" id="fbanco">
								</div>	

							</div>

							<div class="col-md-3">

								<div class="form-group">
									<label for="field-5" class="control-label">No. Cuenta</label>

									<input type="text" class="form-control" id="fcuenta">
								</div>	

							</div>
							<div class="col-md-3">

								<div class="form-group">
									<label for="field-2" class="control-label">Cta. Contable</label>

									<select class="form-control" id="fcuentacontable"></select>
								</div>	

							</div>

						</div>
						<div class="row">							
							<div class="col-md-3">
									<label for="">Tipo de egreso</label>
									<select  id="selTipoEgreso" class="form-control">
									</select>
							</div>
							<div class="col-md-3">

								<div class="form-group">
									<label for="field-4" class="control-label">($) Subtotal</label>

									<input type="text" class="form-control" id="fsubtotal" >
								</div>	

							</div>

							<div class="col-md-3">

								<div class="form-group">
									<label for="field-5" class="control-label">($) IVA</label>

									<input type="text" class="form-control" id="fiva" >
								</div>	

							</div>

							<div class="col-md-3">

								<div class="form-group">
									<label for="field-5" class="control-label" >($) TOTAL</label>

									<input type="text" class="form-control" id="ftotal" >
								</div>	

							</div>

						</div>
						<div class="row">
						<div class="col-md-3">
								<div class="form-group">
									<label for="field-2" class="control-label">Tipo</label>

									<select class="form-control" id="ftipoMovimiento">
										<option value="1">Cargo</option>
										<option value="2">Abono</option>
									</select>
								</div>							
						</div>
							<div class="col-md-3">
								<label for="">Tipo cuenta</label>
								<select class="form-control" id="tipoCuentaFormOp"></select>
							</div>
							<div class="col-md-3">
								<label for="">Tipo Movimiento</label>
								<select class="form-control" id="tipoMovFormOp" disabled>
									<option value="Egresos">Egresos</option>
								</select>
							</div>								
							<div class="col-md-3">
								<label for="">Tipo Operación</label>
								<select  class="form-control" id="tipoOperacionFormOp"></select>
							</div>
						</div>						
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
									<label for="">Observaciones:</label>
									<textarea  id="observacionesGO" rows="5" class="form-control" style="resize: none;"></textarea>
							</div>
						</div>
					</div>
					<hr/>
					<div class="row">
						<div class="col-lg-12">
							<form class="form-inline">
								<div class="form-group">
									<div class=" checkbox checkbox-primary">
										<input type="checkbox" name="chkProrratear" id="chkProrratear" >
										<label for="chkProrratear">Prorratear</label>
									</div>
								</div>
								<div class="form-group" id="configAutomatico">
									<div class="form-group">
										<div class="checkbox checkbox-primary" >
											<input type="checkbox" name="chkAutomatico" id="chkAutomatico" v-model="visible">
											<label for="chkAutomatico" >Automatico&emsp;</label>
											
										</div>
									</div>
								
									<div class="form-group" v-show="visible">
										<label for="periodicidad">&emsp;Registrar cada:</label>
										<input type="text" name="periodicidad" id="periodicidad" class="form-control" placeholder="Frecuencia de registro">
										<div class="absCenter">
											<button class="infoButton" v-on:click.prevent>
												<div class="infoButton-btn">
													<span class="infoButton-btn-text">i</span>
												</div>
												<div class="infoButton-container">
													<div class="infoButton-container-message">
														<strong>Para indicar la frecuencia debes de ingresar los datos con el siguiente formato:</strong>
														<br>
														<ul>
															<li>Anual: <b>dd/mm/a</b> -- Ejemplo: 01/03/a</li>
															<li>Mensual: <b>dd/m</b> -- Ejemplo: 01/m</li>
														</ul>
													</div>
												</div>
											</button>
										</div>
									</div>
									<div class="form-group" v-show="visible">
										<label for="caducidad">&emsp;Caducidad:</label>					
										<input type="text" name="caducidad" id="caducidad" class="form-control datepicker" placeholder ="dd/mm/aaaa" data-format="yyyy/mm/dd" >
									</div>											
								</div>	
							</form>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12" id="sucursalesChk">
						
						</div>
					</div>
					<hr>
					<div class="row">
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
							<form class="form-inline">
							<div class="form-group">
							<label for="filtroSucursal">Sucursal</label>
								<br>
								<select class="form-control filtroSucursal">
									<option value="">TODAS</option>
								</select>
							</div>
							<div class="form-group input-daterange" id="contentRangeOperativo">
									<label for="">Periodo:</label>
									<br>
									<input type="text" class="form-control"  id="filtroFechaInicio" style="width:100px;" placeholder="Inicio" data-format="dd/mm/yyyy" >
									<b>&nbsp;A&nbsp;</b>
									<input type="text" class="form-control" id="filtroFechaFin" style="width:100px;" placeholder="Fin" data-format="dd/mm/yyyyy" >
							</div>	
							<div class="form-group">						
								<label for="">Tipo Egreso</label>
								<br>
								<select  id="filterTipoEgreso" class="form-control"></select>
							</div>
								<div class="form-group">
									<label for="descFacturado">Descripcion:</label>
									<br>
											<input id="descFacturado" class="form-control">
								</div>
								<div class="form-group">
									<label for="emisorFacturado">Emisor:</label>
									<br>
									<input type="text" name="emisorFacturado" id="emisorFacturado" class="form-control">
								</div>
								<div class="form-group">
									<label for="">Cta.Contable</label>
									<br>
									<select class="form-control" id="selCtaContableFiltro"  style="width:150px;" >
										
									</select>
								</div>
								<div class="form-group">
									<br>
									<button type="button" class="btn btn-primary" id="btn-filtro-operacion">Filtrar</button>
								</div>
								<div class="form-group">
										<br>
										<img src="/intranet/assets/images/exportar.png" id="descargaGastosOperativos" style="width:30px;height:auto;cursor:pointer" alt="Exportar" title="Exportar" srcset="">
								</div>
								<div class="form-group" style="text-align:right;padding-left:1%">
									<br>
									<ul class="pagination">
										<li class="prev"><a href="#paginationContainerGO" class="pagAntGO">← Ant.</a></li>
										<li class="next"><a href="#paginationContainerGO" class="pagSigGO">Sig. → </a></li>
									</ul>
								</div>								
							</form>
					</div>
					</div>							
					<div class="row">
						<div class="col-lg-12 table-responsive" style="height:400px">
							<table id="tablefacturados" class="table table-hover">
								<thead>
									<tr>
										<th>Emisor</th>
										<th>Descripcion</th>
										<th>Sucursal</th>
										<th>Cuenta</th>
										<th>Fecha</th>
										<th>Total</th>
										<th></th>
									</tr>
								</thead>

								<tfoot>
									<tr>
										<th>Emisor</th>
										<th>Descripcion</th>
										<th>Sucursal</th>	
										<th>Cuenta</th>									
										<th>Fecha</th>
										<th>Total</th>
										<th></th>
									</tr>
								</tfoot>

								<tbody id="tbodyFacturado">
									
								</tbody>
							</table>
								<div class="col-md-12" style="text-align:right;padding-left:5%">
									<ul class="pagination">
										<li class="prev"><a href="#paginationContainerGO" class="pagAntGO">← Anterior</a></li>
										<li class="next"><a href="#paginationContainerGO" class="pagSigGO">Siguiente → </a></li>
									</ul>
								</div>									
						</div>	
					</div>
				</div>
				<?php if($_SESSION['nivel']==1){ ?>
				<div class="tab-pane fade" id="actualizarSaldo">
					<div class="row">
						<div class="col-md-12">
							<div class="row">
								<div class="col-md-3">
									<div class="form-group">
										<label for="Cuenta">Cuenta</label>
										<select class="form-control" id=selectActCuenta>
											<option value="-1">Selecciona un Banco/Caja</option>
										</select>										
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label for="">Saldo</label>
										<input type="text"  id="saldoBanco" placeholder="0.00" class="form-control" disabled>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label for="">Agregar saldo</label>
										<input type="text"  id="plusSaldoBanco" placeholder="0.00" class="form-control">
									</div>
								</div>			
								<div class="col-md-2">
									<div class="form-group">
										<label for="">Nuevo Saldo:</label>
										<input type="number" name="saldoNvoPreview" id="saldoNvoPreview" class="form-control" placeholder="0.00" disabled>
									</div>
								</div>					
								<div class="col-md-2">
									<div class="form-group"><br>
										<button type="button" class="btn btn-default" id="guardaSaldo"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="tab-pane fade" id="saldosBancarios"> 
					<div class="col-md-12 col-sm-12 col-xs-12 btn-iconic" style="text-align:right">
					<button type="button" class="btn btn-purple"  onclick="descargaSaldos()"><i class="fa fa-download"></i></button>
						<button type="button" class="btn btn-default" ><i class="fa fa-file-o"></i></button>
						<button type="button" id="btnsave" class="btn btn-primary vimeo" onclick="guardarSaldos()"><i class="fa fa-plus"></i></button>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="row">
								<div class="col-md-5">
									<div class="form-group">
										<label for="Cuenta">Cuenta</label>
										<select class="form-control" id=selectCuenta>
											
										</select>
										
									</div>
								</div>
								<div class="col-md-4">
										<div class="form-group">
											<label for="saldosFecha">Fecha</label>
											<input type="text" class="form-control datepicker col-md-4" id="saldosFecha" data-format="yyyy-mm-dd" v-model="fecha">
										</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label for="saldoBeneficiario">Beneficiario:</label>
										<input type="text" name="saldoBeneficiario"  class="form-control" id="beneficiario">
									</div>
								</div>								
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="row">
								<div class="col-md-3">
									<div class="form-group">
										<label for="saldoBeneficiario">Referencia:</label>
										<input type="text" name="saldoReferencia"  class="form-control" id="referenciaSaldos">
									</div>
								</div>	
								<div class="col-md-3">
									<div class="form-group">
										<label for="saldoBeneficiario">Ingresos:</label>
										<input type="text" name="montoIngresos"  class="form-control" id="ingresosSaldos">
									</div>
								</div>	
								<div class="col-md-3">
									<div class="form-group">
										<label for="saldoBeneficiario">Egresos:</label>
										<input type="text" name="montoEgresos"  class="form-control" id="egresosSaldos">
									</div>
								</div>	
								<div class="col-md-3">
									<div class="form-group">
										<label for="selSucSaldosForm">Sucursal</label>
											<select name="" id="selSucSaldosForm" class="form-control">
												<option value="0">NINGUNO</option>
												<optgroup label="Prorratear">
													<option value="all">TODAS</option>
													<option value="zc">ZONA CENTRO</option>
													<option value="za">ZONA ALTOS</option>
												</optgroup>
											</select>
									</div>
								</div>																																			
							</div>
							<div class="row">
								<div class="col-md-3">
									<label for="">Tipo cuenta</label>
									<select class="form-control" id="tipoCuentaForm"></select>
								</div>
								<div class="col-md-3">
									<label for="">Tipo Movimiento</label>
									<select class="form-control" id="tipoMovForm" disabled></select>
								</div>								
								<div class="col-md-3">
									<label for="">Tipo Operación</label>
									<select  class="form-control" id="tipoOperacionForm"></select>
								</div>
							</div>
						</div>
					</div>
					<hr>
					<div class="row">
						<!--<div class="col-md-3">
							<label for="">Fila de inicio </label>
							<input  name="filaInicio" id="filaInicio" class="form-control">						
						</div>
						<div class="col-md-3">
							<label for="">Fila Final</label>
							<input  name="filaFinal" id="filaFinal" class="form-control">						
						</div> !-->
						<div class="col-md-3">
							<div class="form-group">
								<label for="">Selecciona archivo de Arrastre</label>
								<span class="btn btn-success fileinput-button">
									<i class="glyphicon glyphicon-plus"></i>
									<span>Subir documentos</span>
										<input type="file" id="fileupload" name="documentoArrastre"  data-url="/intranet/actualizaArrastre.php" >
									</span>
							</div>
						</div>	
						<div class="col-md-2"></div>
                        <div class="col-md-4">
                                <div class="loading"style="display:none">
                                <div>
                                    <div class="c1"></div>
                                    <div class="c2"></div>
                                    <div class="c3"></div>
                                    <div class="c4"></div>
                                </div>
                                <span>Cargando datos</span>
                                </div>                        
                        </div>											
					</div>
					<div class="row">
						<div class="col-md-12" style="background: #f1f2f7">
								<br><br>
						</div>
					</div>
					<div class="row">
						<hr>
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
							<form class="form-inline">
							<div class="form-group">
								<label>Cuenta:</label>
								<br>
								<select name="" class="form-control" id="filtroSaldoCuenta">
									<option value="%">TODAS</option>
								</select>	
							</div>
							<div class="form-group input-daterange" id="contentDateRangeBancos">
									<label for="">Periodo:</label>
									<br>
									<input type="text" class="form-control"  id="filtroFechaInicioBancos" style="width:100px;" placeholder="Inicio" data-format="dd/mm/yyyy" >
									<b>&nbsp;A&nbsp;</b>
									<input type="text" class="form-control" id="filtroFechaFinBancos" style="width:100px;" placeholder="Fin" data-format="dd/mm/yyyyy" >
							</div>							
								<div class="form-group">
									<label>Beneficiario:</label> <br>
									<input type="text" name="" class="form-control" id="filtroSaldoBeneficiario">
								</div>
								<div class="form-group">
									<label>Referencia:</label> <br>
									<input type="text" name="" class="form-control" id="filtroSaldoReferencia">
								</div>								
								<div class="form-group">
									<br>
									<button type="button" class="btn btn-primary" id="filtarrSaldos">Filtrar</button>
								</div>
								<div class="form-group">
										<br>
										&emsp;
										<img src="/intranet/assets/images/exportar.png" id="descargaMovtosBancos" style="width:30px;height:auto;cursor:pointer" alt="Exportar" title="Exportar" srcset="">
								</div>
								<div class="form-group" style="text-align:right;padding-left:5%">
									<br>
									<ul class="pagination">
								<li class="prev"><a href="#paginationSaldosBancarios" id="pagAnt">← Anterior</a></li>
								<li class="next"><a href="#paginationSaldosBancarios" id="pagSig">Siguiente → </a></li>
									</ul>
								</div>								
							</form>
					</div>
				</div>
					<div class="row">
						<div class="col-md-12">
							<table class="table table-hover">
								<thead>
										<tr>
										<th>Fecha</th>
										<th>Banco</th>
										<th>Beneficiario</th>
										<th>Referencia</th>
										<th>Egresos</th>
										<th>Ingresos</th>
										<th>Sucursal</th>
									</tr>
								</thead>
								<tbody id="tablaSaldos">
									
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="tab-pane fade" id="financieros">
					<div class="col-md-12 col-sm-12 col-xs-12 btn-iconic" style="text-align:right">
						<button type="button" class="btn btn-default" onclick="nuevo()"><i class="fa fa-file-o"></i></button>
						<button type="button" id="btnsave" class="btn btn-primary vimeo" onclick="guardaGastoFi()"><i class="fa fa-plus"></i></button>
					</div>
					<div class="col-lg-12">
						<div class="row">
						<div class="col-md-4">
							
							<div class="form-group">
								<label for="field-1" class="control-label">UDN</label>

								<input type="text" id="idFi" style="display:none">
								<select class="form-control" id="fiudn"></select>
							</div>	
							
						</div>
					</div>
					<div class="row">
						<div class="col-md-8">

							<div class="form-group">
								<label for="field-1" class="control-label">Concepto</label>

								<input type="text" class="form-control" id="fidescripcion">
							</div>	

						</div>
						<div class="col-md-4">

							<div class="form-group">
								<label for="field-1" class="control-label">Fecha Gasto</label>

								<input type="text" class="form-control datepicker col-md-4" id="fifecha" data-format="dd/mm/yyyy">

							</div>	

						</div>
					</div>
						
					<div class="row">							
						<div class="col-md-4">

							<div class="form-group">
								<label for="field-2" class="control-label">Cta. Contable</label>

								<select class="form-control" id="ficuentacontable" onchange="selcuenta()"></select>
							</div>	

						</div>
						
						<div class="col-md-4">

							<div class="form-group">
								<label for="field-4" class="control-label">Banco</label>

								<input type="text" class="form-control" id="fibanco">
							</div>	

						</div>

						<div class="col-md-4">

							<div class="form-group">
								<label for="field-5" class="control-label">No. Cuenta</label>

								<input type="text" class="form-control" id="ficuenta">
							</div>	

						</div>

					</div>



						<div class="row">							

							
							
							<div class="col-md-3">

								<div class="form-group">
									<label for="field-4" class="control-label">($) Subtotal</label>

									<input type="text" class="form-control" id="fisubtotal">
								</div>	

							</div>

							<div class="col-md-3">

								<div class="form-group">
									<label for="field-5" class="control-label">($) IVA</label>

									<input type="text" class="form-control" id="fiiva">
								</div>	

							</div>

							<div class="col-md-3">

								<div class="form-group">
									<label for="field-5" class="control-label">($) TOTAL</label>

									<input type="text" class="form-control" id="fitotal">
								</div>	

							</div>

						</div>

						</div>
					</div>
					<hr/>
					<br><br>
					<div class="row">
						<div class="col-lg-12" >
							<form class="form-inline">
							<div class="form-group">
							<label for="filtroSucursal">Sucursal</label>
								<select class="form-control filtroSucursal" >
									<option value="">TODAS</option>
								</select>
							</div>
								<div class="form-group">
									<label for="descFacturado">Descripcion:</label>
											<input id="descripcionFinanciero" class="form-control">
								</div>
								<div class="form-group">
									<label for="emisorFacturado">Mes y Año</label>
									<input type="text" name="emisorFacturado" id="FinancieroFecha" class="form-control" placeholder="mm/yy">
								</div>
								<div class="form-group">
									<button type="button" class="btn btn-primary" id="btn-filtro-financiero">Filtrar</button>
								</div>
							</form>
						</div>
					</div>
			
					<div class="row">
						<div class="col-lg-12 table-responsive"  style="height:400px">
							<table id="tablefinanciero" class="table table-hover">
								<thead>
									<tr>
										<th>Descripcion</th>
										<th>Fecha</th>
										<th>Banco</th>
										<th>NoCuenta</th>
										<th>Total</th>
										<th></th>
									</tr>
								</thead>

								<tfoot>
									<tr>
										<th>Descripcion</th>
										<th>Fecha</th>
										<th>Banco</th>
										<th>NoCuenta</th>
										<th>Total</th>
										<th></th>
									</tr>
								</tfoot>

								<tbody id="tbodyFinanciero">
									
								</tbody>
							</table>
						</div>	
					</div>
				</div>
				<?php } ?>
			</div>

		</div>
		<div class="clearfix"><br></div>
		

<div class="modal" id="modal-xml" tabindex="-1" role="dialog" aria-labelledby="ultraModal-Label" aria-hidden="true" >
	<div class="modal-dialog animated bounceInDown">
		<div class="modal-content" style="width:850px;">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Anexar datos de XML</h4>
			</div>
			<div class="modal-body"  >

				<div class="row">
					<div class="col-md-12">
						<form class="form-inline">
							<div class="form-group">
								<label for="email">UUID:</label>
								<input type="text" class="form-control" id="buscaUuid">
							</div>
							<div class="form-group">
								<label for="pwd">Fecha:</label>
								<input type="date" class="form-control" id="buscaFecha">
							</div>
							<div class="form-group">
								<label for="pwd">Folio:</label>
								<input type="text" class="form-control" id="buscaFolio">
							</div>
							<button type="submit"  id="buscarXml" class="btn btn-default">Buscar</button>
						</form>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12 table-responsive" id="contet-xml">
						<table class="table table-hover" id="xml-facturas">
							<tr>
								<th>UUID</th>
								<th>Concepto</th>
								<th>Fecha</th>
								<th>SubTotal</th>
								<th>Total</th>
							</tr>
						</table>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-default" type="button">Close</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modalMovimiento">
	<div class="modal-dialog" role="document">
			<div class="modal-content">
					<div class="modal-header">
						<button class="close" data-dismiss="modal" arial-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title">Completar Edición</h4>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
								<input type="hidden" id="hideMovimiento">
									<label for="">Tipo de cuenta</label>
									<select  class="form-control" id="tipoCuentaMovimiento"></select>
								</div>
								<div class="form-group">
									<label for="">Tipo de Movimiento</label>
									<select  class="form-control" id="selectTipoMovimiento" disabled>
									</select>
								</div>
								<div class="form-group">
									<label for="">Operación</label>
									<select class="form-control" id="tipoOperacionMovimiento" ></select>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button class="btn btn-default" id="setTipoCuentaModal" data-dismiss="modal">Guardar</button>
					</div>
			</div>
	</div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="modalGastonomina">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Anexar documento de pago de nómina</h4>
      </div>
      <div class="modal-body">
		<div class="row">
			<div class="col-md-12">

				<div class="form-group">
					<label for="">Selecciona archivo de nómina</label>
					<br>
					<span class="btn btn-success fileinput-button">
						<i class="glyphicon glyphicon-plus"></i>
						<span>Subir nomina</span>
							<input type="file" id="upNomina" name="formatoNomina"  data-url="/intranet/controladores/con_egresos.php">
						</span>
				</div>
			</div>			
		</div>
		
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
	

	<div id="ejemplo">
		<option-cuentas v-for="cuenta in cuentas" :key="cuenta.id" :cuenta="cuenta"></option-cuentas>
	</div>