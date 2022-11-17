
<div class="tab-content" style="margin-right: 15px;" id="colaboradores_bajas">
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-md-12">
                    <h1>CONSULTA DE FECHAS DE BAJAS DE COLABORADORES</h1>
                </div>
                <div>
                    <div class="conteiner_name">
                        <input type="text" class="form-control form-control-sm rounded bright" id="empleadoNombre" placeholder="Nombre del empleado" required>
                    </div>
                    <div class="conteiner_date">
                        <input type="text" class="form-control form-control-sm rounded bright" id="empleadoNombre" placeholder="Nombre del empleado" required>
                    </div>  
                    <div class="conteiner_button">
                        <button id="buscarResguardos" type="button" class="btn btn-primary" >Buscar</button>                       
                    </div>
                </div>                             
                <div class="col-md-12">
                    <label>Bajas mensual: </label>
                    <input id="bajas_mensual" disabled>
                    <table class="table table-borderless">
                        <thead>
                            <tr>
                                <th scope="col">Nombre empleado</th>
                                <th scope="col">Fecha de baja</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyHistorialColaboradores">
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
