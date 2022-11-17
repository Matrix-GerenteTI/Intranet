
<div class="tab-content" style="margin-right: 15px;" id="muestrameResgs">
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-md-12">
                    <h1 style="width: 50%;">RESGUARDOS</h1>
                    <br>
                    <button id="newResguardo" type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalResgCel">
                        <i class="fa fa-exclamation-circle"></i>&nbsp;&nbsp;Nuevo resguardo
                    </button>
                </div>
                <div class="col-md-4">

                    <div class="form-group">
                        <input type="text" class="form-control" id="empleadoNombre" placeholder="Nombre del empleado" required>
                    </div>	

                </div>
                <div class="col-md-4">

                    <div class="form-group">
                        <select class="form-control" id="usoEquipoResguardo" required>
                            <option value="">Tipo de resguardo</option>
                            <option value="RESGUARDO_CELULARES">Resguardo de celulares</option>
                            <option value="RESGUARDO_EQUIPO_COMPUTO">Resguardo de equipos de computo</option>
                            <option value="RESGUARDO_EQUIPO_HERRAMIENTA">Resguardo de equipo y herramienta</option>
                            <option value="RESGUARDO_UNIFORMES">Resguardo de uniformes</option>
                            <option value="RESGUARDO_MOTOCICLETAS">Resguardo de motocicletas</option>
                            <option value="RESGUARDO_UNIDADES_PESADAS">Resguardo de unidades pesadas</option>                            
                        </select>

                    </div>

                </div>
                <div class="col-md-4">

                    <div class="form-group">
                        <input type="text" class="form-control datepicker col-md-4" id="fechaBusquedaResg" data-format="dd/mm/yyyy" placeholder="Fecha" required>
                    </div>	

                </div>
                <div class="col-md-4">

                    <div class="form-group">
                        <button id="buscarResguardos" type="button" class="btn btn-primary" style=" position: relative; margin-top: 8%; left: 70%;">Buscar</button>
                    </div>	

                </div>
                <div class="col-md-12">
                    <table class="table table-borderless">
                        <thead>
                            <tr>
                                <th scope="col">Nombre empleado</th>
                                <th scope="col">Tipo de resguardo</th>
                                <th scope="col">Fecha del resguardo</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyHistorialResguardos">
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="modalResgCel" tabindex="-1" role="dialog" aria-labelledby="modalResgCelLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title modal-title-resg" id="modalResgCelLabel">Crear nuevo resguardo</h5>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <!-- Seleccion de form -->
                        <select class="form-control selectResguardo" id="tipoResgSelect" style="border-radius:20px;" onChange="mostrar(this.value);">
                            <option value="">Tipo de resguardo</option>
                            <option value="RESGUARDO_CELULARES">Resguardo de celulares</option>
                            <option value="RESGUARDO_EQUIPO_COMPUTO">Resguardo de equipos de computo</option>
                            <option value="RESGUARDO_EQUIPO_HERRAMIENTA">Resguardo de equipo y herramienta</option>
                            <option value="RESGUARDO_UNIFORMES">Resguardo de uniformes</option>
                            <option value="RESGUARDO_MOTOCICLETAS">Resguardo de motocicletas</option>
                            <option value="RESGUARDO_UNIDADES_PESADAS">Resguardo de unidades pesadas</option>                            
                        </select>
                        <hr>
                        <!-- formulario resguardo equipo celulares -->
                        <div id="RESGUARDO_CELULARES" class="row">
                            <!-- Forms -->
                            <form id="resgcel">
                                <div class="row">
                                    <div class="col-md-3" style="left: 68%; width: 32%;">

                                        <div class="form-group">
                                            <label for="field-1" class="control-label">FECHA</label>

                                            <input type="text" class="form-control datepicker col-md-4 campo" id="fechaResguardo" data-format="dd/mm/yyyy" required/>

                                        </div>
                                        <div class="form-group">
                                            <label for="field-5" class="control-label">USO</label>
                                            <select class="form-control campo" id="usoEquipoCelResguardo" required/>
                                                <option value="">Elige una opcion</option>
                                                <option id="Nuevo_equipoCel" value="Nuevo">Nuevo</option>
                                                <option id="Usado_equipoCel" value="Usado">Usado</option>
                                            </select>
                                        </div>

                                    </div>
                                    <div class="col-md-8" style="right: 25%;">

                                        <div class="form-group">
                                            <label for="field-1" class="control-label " style="position: relative; top: 3.5rem; left: 0; font-size: 20px;">
                                                CARTA RESGUARDO DE EQUIPO CELULAR
                                            </label>
                                        </div>	

                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">

                                        <div class="form-group">
                                            <label for="field-1" class="control-label">Empresa</label>

                                            <input type="text" class="form-control campo" id="empresaResguardo" required/>
                                        </div>	

                                    </div>
                                    <div class="col-md-4">

                                        <div class="form-group">
                                            <label for="field-1" class="control-label">Area / Departamento</label>

                                            <input type="text" class="form-control col-md-4 campo" id="areadeptoResguardo" required/>

                                        </div>	

                                    </div>
                                    <div class="col-md-4">

                                        <div class="form-group">
                                            <label for="field-1" class="control-label">Compañia telefonica</label>

                                            <input type="text" class="form-control col-md-4 campo" id="companiaTelResguardo" required/>

                                        </div>	

                                    </div>
                                </div>

                                <div class="row">							

                                    <div class="col-md-4">

                                        <div class="form-group">
                                            <label for="field-2" class="control-label">Nombre</label>

                                            <input type="text" class="form-control campo" id="nombreEmpleadoResguardo" onkeyup="setVal();" required/>
                                        </div>	

                                    </div>
                                    <div class="col-md-4">

                                        <div class="form-group">
                                            <label for="field-4" class="control-label">Puesto</label>

                                            <input type="text" class="form-control campo" id="puestoEmpleadoResguardo" required/>
                                        </div>	

                                    </div>
                                    <div class="col-md-4">

                                        <div class="form-group">
                                            <label for="field-4" class="control-label">Telefono</label>

                                            <input type="text" class="form-control campo" id="telefonoAsigEmpResg" required/>
                                        </div>	

                                    </div>
                                </div>
                                <div class="row">							

                                    <div class="col-md-4">

                                        <div class="form-group">
                                            <label for="field-2" class="control-label">Modelo</label>

                                            <input type="text" class="form-control campo" id="modeloCelAsigEmpResg" required/>
                                        </div>	

                                    </div>
                                    
                                    <div class="col-md-8">

                                        <div class="form-group">
                                            <label for="field-3" class="control-label">IMEI</label>

                                            <input type="text" class="form-control campo" id="imeiCelAsigEmpResg" required/>
                                        </div>	

                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group" style="text-align: center;">
                                                <label >CARACTERISTICAS DEL USO DEL EQUIPO</label>
                                                <textarea  id="observacionesGO" rows="5" class="form-control" style="resize: none; text-align: center; height: auto;" disabled>
                                                    Se utiliza para enviar y recibir pedidos por medio de whatsapp y llamadas de la empresa.
                                                </textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <table class="table table-borderless">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">EQUIPO</th>
                                                        <th scope="col">SI</th>
                                                        <th scope="col">EQUIPO</th>
                                                        <th scope="col">SI</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td style="width: 30%;">TARJETA O TELEFONO</td>
                                                        <td>
                                                            <div class="checkbox checkbox-primary">
                                                                <input type="checkbox" name="chkTarjTelResguardo" id="chk_1" value="TARJETA O TELEFONO">
                                                                <label for="chkAutomatico"></label>
                                                            </div>
                                                        </td>
                                                        <td>MICA DE CRISTAL</td>
                                                        <td>
                                                            <div class="checkbox checkbox-primary">
                                                                <input type="checkbox" name="chkMicaCristalResguardo" id="chk_2" value="MICA DE CRISTAL">
                                                                <label for="chkAutomatico"></label>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width: 30%;">ANTENA</td>
                                                        <td>
                                                            <div class="checkbox checkbox-primary">
                                                                <input type="checkbox" name="chkAntenaResg" id="chk_3" value="ANTENA">
                                                                <label for="chkAutomatico"></label>
                                                            </div>
                                                        </td>
                                                        <td style="width: 30%;">MICA DE PLASTICO</td>
                                                        <td>
                                                            <div class="checkbox checkbox-primary">
                                                                <input type="checkbox" name="chkMicaPlasticoResg" id="chk_4" value="MICA DE PLASTICO">
                                                                <label for="chkAutomatico"></label>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width: 30%;">CARGADOR</td>
                                                        <td>
                                                            <div class="checkbox checkbox-primary">
                                                                <input type="checkbox" name="chkCargadorResg" id="chk_5" value="CARGADOR">
                                                                <label for="chkAutomatico"></label>
                                                            </div>
                                                        </td>
                                                        <td style="width: 30%;">TARJETA DE MEMORIA EXTERNA</td>
                                                        <td>
                                                            <div class="checkbox checkbox-primary">
                                                                <input type="checkbox" name="chkTarjMemExtResg" id="chk_6" value="TARJETA DE MEMORIA EXTERNA">
                                                                <label for="chkAutomatico"></label>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width: 30%;">MANUALES</td>
                                                        <td>
                                                            <div class="checkbox checkbox-primary">
                                                                <input type="checkbox" name="chkManualResg" id="chk_7" value="MANUALES">
                                                                <label for="chkAutomatico"></label>
                                                            </div>
                                                        </td>
                                                        <td style="width: 30%;">CABLE DE DATOS 2 USB A MINI USB</td>
                                                        <td>
                                                            <div class="checkbox checkbox-primary">
                                                                <input type="checkbox" name="chkCableDatResg" id="chk_8" value="CABLE DE DATOS 2 USB A MINI USB">
                                                                <label for="chkAutomatico"></label>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width: 30%;">MANOS LIBRES INALAMBRICOS</td>
                                                        <td>
                                                            <div class="checkbox checkbox-primary">
                                                                <input type="checkbox" name="chkHeadPhoneInalamResg" id="chk_9" value="MANOS LIBRES INALAMBRICOS">
                                                                <label for="chkAutomatico"></label>
                                                            </div>
                                                        </td>
                                                        <td style="width: 30%;">ADAPTADOR EXPRESS PCMCIA</td>
                                                        <td>
                                                            <div class="checkbox checkbox-primary">
                                                                <input type="checkbox" name="chkPcmciaResg" id="chk_10" value="ADAPTADOR EXPRESS PCMCIA">
                                                                <label for="chkAutomatico"></label>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width: 30%;">FUNDA/CLIP</td>
                                                        <td>
                                                            <div class="checkbox checkbox-primary">
                                                                <input type="checkbox" name="chkFundaClipResg" id="chk_11" value="FUNDA/CLIP">
                                                                <label for="chkAutomatico"></label>
                                                            </div>
                                                        </td>
                                                        <td style="width: 30%;">SCREEN SLIP</td>
                                                        <td colspan="2">
                                                            <div class="checkbox checkbox-primary">
                                                                <input type="checkbox" name="chkSlipResg" id="chk_12" value="SCREEN SLIP">
                                                                <label for="chkAutomatico"></label>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width: 30%;">CARGADOR DE CARRO</td>
                                                        <td colspan="3">
                                                            <div class="checkbox checkbox-primary">
                                                                <input type="checkbox" name="chkCargCarrResg" id="chk_13" value="CARGADOR DE CARRO">
                                                                <label for="chkAutomatico"></label>
                                                            </div>
                                                        </td>
                                                        
                                                    </tr>
                                                    <tr>
                                                        <td style="width: 30%;">MANOS LIBRES ALAMBRICOS</td>
                                                        <td>
                                                            <div class="checkbox checkbox-primary">
                                                                <input type="checkbox" name="chkHeadPhoneAlamResg" id="chk_14" value="MANOS LIBRES ALAMBRICOS">
                                                                <label for="chkAutomatico"></label>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                                <label for="">OBSERVACIONES</label>
                                                <textarea  id="observacionesResg" rows="5" class="form-control campo" style="resize: none;" required/></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">

                                    <div class="form-group">
                                        <label for="field-3" class="control-label">RECIBIÓ</label>

                                        <input type="text" class="form-control campo" id="nameEmpleadoRecibResg" required disabled/>
                                    </div>	

                                </div>
                                <div class="col-md-6">

                                    <div class="form-group">
                                        <label for="field-3" class="control-label">ENTREGÓ</label>

                                        <input type="text" class="form-control campo" id="nameEmpleadoEntrResgCel" value="<?php echo $_SESSION['nombre'];?>" disabled/>
                                    </div>	
                                </div>
                                <input type="hidden" id="tipoResg" class="form-control campo" value="RESGUARDO_CELULARES"/>
                                <input type="hidden" id="idResguardo" class="form-control campo" value="1"/>
                                <br>
                                <button id="sendResguardo" type="button" class="btn btn-outline-primary" style="position: relative; left:80%;">
                                    Guardar
                                </button>
                                <button id="updateResguardo" type="button" class="btn btn-outline-primary" style="position: relative; left:80%;">
                                    Actualizar
                                </button>
                            </form>
                        </div>
                        <!-- formulario resguardo equipo computo -->
                        <div id="RESGUARDO_EQUIPO_COMPUTO" class="row">
                            <!-- Forms -->
                            <form id="resgequipcomp">
                                <div class="row">
                                    <div class="col-md-3" style="left: 68%; width: 32%;">
                                    
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">EQUIPO NO.</label>
                                            <input type="text" class="form-control col-md-4 campo_equipComp" id="numResg" required/>

                                        </div>
                                        <div class="form-group">
                                            <label for="field-5" class="control-label">FECHA</label>
                                            <input type="text" class="form-control datepicker col-md-4 campo_equipComp" id="fechaResguardoEquipComp" data-format="dd/mm/yyyy" required/>
                                        </div>

                                    </div>
                                    <div class="col-md-8" style="right: 27%;">

                                        <div class="form-group">
                                            <label for="field-1" class="control-label " style="position: relative; top: 3.5rem; left: 0; font-size: 20px;">
                                                CARTA RESGUARDO DE EQUIPO DE COMPUTO
                                            </label>
                                        </div>	

                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">

                                        <div class="form-group">
                                            <label for="field-1" class="control-label">USUARIO</label>

                                            <input type="text" class="form-control campo_equipComp" id="usuarioResguardoEquipComp" onkeyup="setValDos();" required/>
                                        </div>	

                                    </div>
                                    <div class="col-md-6">

                                        <div class="form-group">
                                            <label for="field-1" class="control-label">EMPRESA</label>

                                            <input type="text" class="form-control col-md-4 campo_equipComp" id="empresaResguardoEquipComp" required/>

                                        </div>	

                                    </div>
                                    <div class="col-md-6">

                                        <div class="form-group">
                                            <label for="field-1" class="control-label">AREA/DEPARTAMENTO</label>

                                            <input type="text" class="form-control col-md-4 campo_equipComp" id="areaDeptoResguardoEquipComp" required/>

                                        </div>	

                                    </div>
                                    <div class="col-md-6">

                                        <div class="form-group">
                                            <label for="field-1" class="control-label">PUESTO</label>

                                            <input type="text" class="form-control col-md-4 campo_equipComp" id="puestoResguardoEquipComp" required/>

                                        </div>	

                                    </div>
                                    <div class="col-md-6">

                                        <div class="form-group">
                                            <label for="field-1" class="control-label">SUCURSAL</label>

                                            <input type="text" class="form-control col-md-4 campo_equipComp" id="sucursalResguardoEquipComp" required/>

                                        </div>	

                                    </div>
                                    <div class="col-md-6">

                                        <div class="form-group">
                                            <label for="field-1" class="control-label">TELEFONO</label>

                                            <input type="text" class="form-control col-md-4 campo_equipComp" id="telefonoResguardoEquipComp" required/>

                                        </div>	

                                    </div>
                                </div>
                                <br><br>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label " style="font-size: 20px;">
                                                DESCRIPCION DEL EQUIPO
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">TIPO DE EQUIPO</label>

                                            <input type="text" class="form-control col-md-4 campo_equipComp" id="tipoEquipoResguardoEquipComp" required/>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">MARCA</label>

                                            <input type="text" class="form-control col-md-4 campo_equipComp" id="marcaEquipoResguardoEquipComp" required/>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">MODELO</label>

                                            <input type="text" class="form-control col-md-4 campo_equipComp" id="modeloEquipoResguardoEquipComp" required/>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">DD - GB</label>

                                            <input type="text" class="form-control col-md-4 campo_equipComp" id="ddGbResguardoEquipComp" required/>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">RAM - GB</label>

                                            <input type="text" class="form-control col-md-4 campo_equipComp" id="ramGbResguardoEquipComp" required/>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">PROCESADOR</label>

                                            <input type="text" class="form-control col-md-4 campo_equipComp" id="procesadorEquipoResguardoEquipComp" required/>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">N/S EQUIPO</label>

                                            <input type="text" class="form-control col-md-4 campo_equipComp" id="nsEquipoResguardoEquipComp" required/>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">S.O.</label>

                                            <input type="text" class="form-control col-md-4 campo_equipComp" id="soEquipoResguardoEquipComp" required/>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label">LICENCIA</label>

                                            <input type="text" class="form-control col-md-4 campo_equipComp" id="licenciaEquipoResguardoEquipComp" required/>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">MONITOR</label>

                                            <input type="text" class="form-control col-md-4 campo_equipComp" id="monitorResguardoEquipComp" required/>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">N/S</label>

                                            <input type="text" class="form-control col-md-4 campo_equipComp" id="nsMonitorResguardoEquipComp" required/>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">TECLADO</label>

                                            <input type="text" class="form-control col-md-4 campo_equipComp" id="tecladoEquipoResguardoEquipComp" required/>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">N/S</label>

                                            <input type="text" class="form-control col-md-4 campo_equipComp" id="nsTecladoResguardoEquipComp" required/>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">MOUSE</label>

                                            <input type="text" class="form-control col-md-4 campo_equipComp" id="mouseEquipoResguardoEquipComp" required/>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">N/S</label>

                                            <input type="text" class="form-control col-md-4 campo_equipComp" id="nsMouseResguardoEquipComp" required/>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">CARGADOR</label>

                                            <input type="text" class="form-control col-md-4 campo_equipComp" id="cargadorEquipoResguardoEquipComp" required/>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">N/S</label>

                                            <input type="text" class="form-control col-md-4 campo_equipComp" id="nsCargadorResguardoEquipComp" required/>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">IMPRESORA</label>

                                            <input type="text" class="form-control col-md-4 campo_equipComp" id="impresoraEquipoResguardoEquipComp" required/>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">N/S</label>

                                            <input type="text" class="form-control col-md-4 campo_equipComp" id="nsImpresoraResguardoEquipComp" required/>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">NO BRAKE</label>

                                            <input type="text" class="form-control col-md-4 campo_equipComp" id="noBrakeEquipoResguardoEquipComp" required/>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">BOCINA</label>

                                            <input type="text" class="form-control col-md-4 campo_equipComp" id="bocinaEquipoResguardoEquipComp" required/>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">DVD/CD</label>

                                            <input type="text" class="form-control col-md-4 campo_equipComp" id="lectoraDiscoEquipoResguardoEquipComp" required/>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label">OBSERVACIONES</label>

                                            <input type="text" class="form-control col-md-4 campo_equipComp" id="observacionesResguardoEquipComp" required/>
                                        </div>
                                    </div>
                                </div>
                                <br><br>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">RECIBIÓ</label>

                                            <input type="text" class="form-control col-md-4 campo_equipComp" id="recibeResgEquipComp" disabled required/>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">ENTREGÓ</label>

                                            <input type="text" class="form-control campo_equipComp" id="nameEmpleadoEntrResgComp" value="<?php echo $_SESSION['nombre'];?>" disabled/>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" id="tipoResgEquipComp" class="form-control campo" value="RESGUARDO_EQUIPO_COMPUTO"/>
                                <input type="hidden" id="idResguardoEquipoComp" class="form-control campo"/>
                                <br>
                                <button id="sendResguardoEquipoComputo" type="button" class="btn btn-outline-primary" style="position: relative; left:80%;">
                                    Guardar
                                </button>
                                <button id="updateResguardoEquipoComputo" type="button" class="btn btn-outline-primary" style="position: relative; left:80%;">
                                    Actualizar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary closingModalResg" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
