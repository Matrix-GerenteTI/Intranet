<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
    <div class="page-title">
        <div class="pull-left">
            <h1>Acciones correctivas</h1>
        </div>
    </div>
</div>

<div class="clearfix"></div>


<div class="col-md-12">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#personal">Edición</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade in active" id ="personal">
                <div class="col-md-12" style="text-align:right">
                    <button type="button" class="btn btn-danger "  id="eliminar" ><span class="glyphicon glyphicon-remove"></span></button>
                    <button type="button" class="btn btn-default"  role="button"><i class="fa fa-file-o"></i></button>
                    <button type="button" id="btnsave" class="btn btn-primary"  id="editar" ><i class="fa fa-pencil" aria-hidden="true"></i></button>
                </div>
                <div class="row">
                        <div class="col-md-3">
                                <div class="form-group">
                                        <label for="">Sancionado</label>
                                        <input type="text" name="" id="sancionado" class="form-control">
                                        <input type="text" name="" id="idSancionado">
                                </div>
                        </div>
                        <div class="col-md-3">
                                <div class="form-group">
                                        <label for="">Fecha de Aplicación</label>
                                        <input type="text" name="" id="fechaAplicacion" class="form-control date-correctiva">
                                </div>
                        </div>
                        <div class="col-md-3">
                                <div class="form-group">
                                        <label for="">Puesto</label>
                                        <select name="" id="puesto" class="form-control"></select>
                                </div>
                        </div>        
                        <div class="col-md-3">
                                <div class="form-group">
                                        <label for="">Sucursal</label>
                                        <select name="" id="sucursal" class="form-control"></select>
                                </div>
                        </div>                                        
                </div>
                <div class="row">
                    <div class="col-md-6">
                            <label for="">Motivo de la sanción</label>
                            <textarea name="" id="motivoAccionCorrectiva" rows="5" style=" resize: none" class="form-control"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label for="">Plan de acción</label>
                        <textarea name="" id="planAccionCorrectiva" rows="5" style=" resize: none" class="form-control"></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">Aplica sanción económica</label>
                        </div>
                        <label> <input type="radio" class="chkSancion" name="sancionEconomica" value="1"> Si </label>
                        <label> <input type="radio" class="chkSancion" name="sancionEconomica" value="0"> No </label>
                    </div>
                    <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Monto de sanción</label>
                                <input type="number" name="" id="monto" class="form-control" placeholder="Ej. 1000.00">
                            </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                                <label for="">Fecha de descuento</label>
                                <input type="text" name="" id="fechaDescuento" class="form-control date-correctiva">
                        </div>
                    </div>
                </div>

                <!--  acá se muestra el listado de las acciones correctibas -->
                <div class="row">
                    <table class="table table-striped">
                        <tr>
                            <th>Fecha de sanción</th>
                            <th>Sancionado</th>
                            <th>Motivo</th>
                            <th>Monto</th>
                        </tr>
                        <tbody id="tblAcorrectivas"></tbody>
                    </table>
                </div>
        </div>
    </div>
</div>