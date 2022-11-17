<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
    <div class="page-title">
        <div class="pull-left">
            <h1>Indicador Clave de Rendimiento (KPI)</h1>
        </div>
    </div>
</div>

<div class="clearfix"></div>

<div class="col-md-12">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#personal">Trabajadores</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade in active" id ="personal">
                <div class="row">
                        <div class="col-md-6">
                            <div class="db_box cards-kpi" style="position: relative; height:50vh; width:35vw">
                                <h4>Altas</h4>
                                <canvas id="chartAltas"></canvas>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="db_box cards-kpi" style="position: relative; height:50vh; width:35vw">
                                <h4>Bajas</h4>
                                <canvas id="chartBajas" ></canvas>
                            </div>
                        </div>
                </div>
                <br>
                <br>
        
                        <br>
                        <br>
                        <br>
                <div class="row">
                        <div class="col-md-6">
                            <div class="db_box cards-kpi" style="position: relative; height:50vh; width:35vw">
                                <h4>Retardos</h4>
                                <canvas id="chartRetardos"></canvas>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="db_box cards-kpi" style="position: relative; height:50vh; width:35vw">
                                <h4>Faltas</h4>
                                <canvas id="chartFaltas" ></canvas>
                            </div>
                        </div>
                </div>      
                <br><br>
                <div class="row">
                    <div class="col-md-10 col-md-offset-1" >
                        <div class="db_box cards-kpi">
                            <h4>Cambios de Adscripción</h4>
                            <div class="form-group">
                                <label for="">Sucursal</label>
                                <select  id="sucursal" class="form-control">
                                    <option value="%">Todas</option>
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-md-6" style="position: relative; height:50vh; width:30vw" id="containerCambioAdscripcionSalida">
                                    <canvas id="cambioAdscripcionSalida" ></canvas>
                                </div>
                                <div class="col-md-6" style="position: relative; height:50vh; width:30vw" id="containerCambioAdscripcionLlegada">
                                    <canvas id="cambioAdscripcionLlegada" ></canvas>
                                </div>
                            </div>
                        </div>
                    </div>                           
        </div>
    </div>
</div>
