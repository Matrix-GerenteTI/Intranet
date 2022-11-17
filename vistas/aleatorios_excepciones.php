<div class="tab-content" style="margin-right: 15px;" id="showRandomsExceptions">
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div id="divAlertException" class="col-md-12">
                    
                </div>
                <div class="col-md-12">
                    <h1 style="width: 50%;">INVENTARIOS ALEATORIOS (EXCEPCIONES)</h1>
                    <br>
                </div>
                <div class="col-md-4">

                    <div class="form-group">
                        <select class="form-control" id="sucursal" required>
                            <option value="">Seleccione UDN</option>
                            <option value="0">TODAS</option>
                        </select>

                    </div>

                </div>
                <div class="col-md-4">

                    <div class="form-group">
                        <input type="text" class="form-control" id="razonExcepcion" placeholder="Ingrese la razón" required>
                    </div>	

                </div>
                <div class="col-md-4">

                    <div class="form-group">
                        <input type="text" class="form-control datepicker col-md-4" id="fechaRazonExcp" data-format="dd/mm/yyyy" placeholder="Fecha" required>
                    </div>	

                </div>
                <div class="col-md-4">

                    <div class="form-group">
                        <button id="aplicarExcepcion" type="button" class="btn btn-primary" style=" position: relative; margin-top: 8%; left: 70%;">
                            Guardar
                        </button>
                    </div>	

                </div>
                <div class="col-md-12">
                    <table class="table table-borderless">
                        <thead>
                            <tr>
                                <th scope="col">UDN</th>
                                <th scope="col">Razón de la excepción</th>
                                <th scope="col">Fecha de la excepción</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyHistorialExcepciones">
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>