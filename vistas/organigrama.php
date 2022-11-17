<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="shortcut icon" href="/intranet/assets/images/favicon.png" type="image/x-icon" />

    <link rel="stylesheet" type="text/css" href="/intranet/assets/plugins/getorgchart/getorgchart.css">
    <link href="/intranet/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="/intranet/assets/css/timeline.css" rel="stylesheet" type="text/css"/>
    <link href="/intranet/assets/css/scanDocs.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="/intranet/assets/css/jquery.fileupload.css">
<link rel="stylesheet" href="/intranet/assets/css/jquery.fileupload-ui.css">
<link rel="stylesheet" href="/intranet/assets/css/camara.css">
    <title>Organigrama</title>
    <style>
    .bar {
        height: 18px;
        background: green;
    }
    h3{
        font-weight: bold;
    }

    .footer-table{
        border-collapse: collapse;
        font-size:1.3em;
    }
    .footer-content{
        background: #EAEAEA;
    }
    /* .column { float:left; height: 250px; }
    #copyright span{
        vertical-align: bottom;
    } */
    </style>

</head>
<body>
    <div class="container-fluid">
    <div class="row header">
        <div class="col-md-4"> <img src="/intranet/assets/images/login-logo.png" alt=""></div>
        <div class="col-md-4 text-center"><h3>Organigrama Organizacional de MATRIX y LEON Autopartes.</h3></div>
        <div class="col-md-4"> <img src="/intranet/assets/images/logoleon.png"  alt="" style="width:45%; height:45%; margin-left:27%;"></div>
    </div>
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-2">
            <label for="">Tipo:</label>
            <select  id="tipoOrganigrama" class="form-control">
                <option value="1">General</option>
                <option value="Dir">Directivo</option>
                <option value="Ger">Gerencial</option>
                <option value="Jef">Jefaturas</option>
            </select>
        </div>
        <div class="col-md-2">
            <label for="">Nivel de abstracción</label>
            <select  id="selPuesto" class="form-control"></select>
        </div>
        <div class="col-md-2">
            <label >Jefe</label>
            <select id="selJefe" class="form-control">
            </select>
        </div>        
        <div class="col-md-2">
            <br>
            <button class="btn btn-primary" id="btnGenerarOrganigrama">Generar</button>
        </div>
    </div>
    <div class="row"><br></div>
        <div class="row">
            <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12"><div id="matrix"> </div></div>
                    </div>
            </div>
        </div>
        <!-- <div class="row footer-content" >
            <div class="col-md-4 column">
                <table class="footer-table">
                    <tr>
                        <td colspan="2"><img src="/intranet/assets/images/logo.png" alt=""></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="text-center"><strong>Contacto.</strong></td>
                    </tr>
                    <tr>
                        <td><strong>Telefono:</strong></td>
                        <td></td>
                    </tr>
                    <tr>
                       <td><strong>Correo:</strong></td>
                       <td></td>
                    </tr>
                    <tr>
                       <td><strong>Pagina:</strong></td>
                       <td></td>
                    </tr>
                    <tr>
                       <td colspan="2" class="text-center"><strong>Redes Sociales</strong></td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-4 column"></div>
            <div class="col-md-4 column" class="bottom" style=" background:red;" id="copyright" ><span>© Matrix Intranet. Todos los derechos reservados.</span></div>
        </div> -->
    </div>


    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <div class="row">
                <div class="col-md-6">
                    <h4 class="modal-title" id="myModalLabel">  
                        Información laboral del trabajador: 
                    <h4>
                </div>
                <div class="col-md-4">
                    <div class="dropdown">
                        <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                            Selecciona un trabajador
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" id="dropdownTrabajador" aria-labelledby="dropdownMenu1">
                        </ul>
                    </div>
                </div>
            </div>            
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                        <div class="col-md-3">
                                <!-- <img src="/intranet/assets/img/network.svg" alt="" class="img-rounded"> -->
                                <div class="inputFileModificado">
                                    <form id="frmFoto" >
                                        <input class="inputFileOculto" accept="image/*" capture="camera" name="input1" type="file" >
                                    </form>
                                    
                                    <div class="inputParaMostrar">
                                        <input>
                                        <!-- <img src="/intranet/assets/img/network.svg"> -->
                                    </div>
                                </div>
                        </div>
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-12">
                                    <input type="hidden" id="trabajadorField">
                                        <div class="form-group">
                                            <label for="">Nombre:</label>
                                            <input type="text" class="form-control" placeholder="Nombre" id="nombre">
                                        </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="">Puesto:</label>
                                        <input type="text" class="form-control" id="puesto">
                                        <input type="hidden" id="nodoId">
                                        <input type="hidden" id="parentId">
                                    </div>
                                </div>
                            </div>
                    </div>                            
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true" >
                    <div class="panel panel-default">
                        <div class="panel-heading customPanelHead" role="tab" id="headingOne">
                        <h4 class="panel-title ">
                            <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                <strong>Nómina</strong> 
                            </a>
                        </h4>
                        </div>
                        <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Adscripción</label>
                                        <input type="text"  class="form-control" placeholder="Sucursal" id="adscripcion">
                                    </div>
                                </div>                                  
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Sueldo Mensual</label>
                                        <input type="text"  value="$0.0" class="form-control" id="sueldo">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">INGRESÓ A LABORAR</label>
                                        <input type="text"  placeholder="YYYY/MM/DD" class="form-control" id="inicioLabores">
                                    </div>
                                </div>                                                          
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">CURP</label>
                                        <input type="text"  placeholder="XXXX000000XXXXXXXX" class="form-control" id="curpEmpleado">
                                    </div>
                                </div>  
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">NSS</label>
                                        <input type="text"  value="0" class="form-control" id="nssEmpleado">
                                    </div>
                                </div>                                                                          
                            </div>
                            <div class="row">
                                <div class="col-md-12" style="background:#D54F4F; color:white;">
                                        <strong>Cuentas por Pagar </strong> <br>
                                </div>
                                <div class="col-md-8">
                                        <ul id="listaDeducciones">
                                        </ul>
                                </div>
                            </div>
                        </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading customPanelHead" role="tab" id="headingTwo">
                        <h4 class="panel-title">
                            <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                Histograma*
                            </a>
                        </h4>
                        </div>
                        <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
                        <div class="panel-body">
                            Información pendiente
                        </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading customPanelHead" role="tab" id="headingThree">
                        <h4 class="panel-title">
                            <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            <strong>  Documentación</strong>
                            </a>
                        </h4>
                        </div>
                        <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <ul class="nav nav-tabs">
                                        <li class="active"><a data-toggle="tab" href="#home">Documentos</a></li>
                                        <li><a data-toggle="tab" href="#menu1">Anexar Documentación</a></li>
                                    </ul>

                                    <div class="tab-content">
                                        <div id="home" class="tab-pane fade in active">
                                            <div class="filemanager">

                                                <div class="search">
                                                    <input type="search" placeholder="Find a file.." />
                                                </div>

                                                <div class="breadcrumbs"></div>

                                                <ul class="data"></ul>

                                                <div class="nothingfound">
                                                    <div class="nofiles"></div>
                                                    <span>No hay documentos.</span>
                                                </div>

                                            </div>
                                        </div>
                                        <div id="menu1" class="tab-pane fade">
                                            <div class="form-group">
                                                <label for="">Sube un archivo o un grupo de archivos que corresponda al trabajador</label>
                                                <br>
                                                <span class="btn btn-success fileinput-button">
                                                    <i class="glyphicon glyphicon-plus"></i>
                                                    <span>Subir documentos</span>
                                                     <input type="file" id="fileupload" name="documentoTrabajador[]" accept="*" data-url="/intranet/controladores/anexardocumentacion.php" multiple>
                                                    </span>
                                                    <div class="progress progress-striped active" id="progress" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                                                        <div class="progress-bar progress-bar-success bar" style="width:0%;"></div>
                                                    </div>
                                            </div>
                                        </div>
                                    </div>                                                            
                                </div>
                            </div>
                        </div>
                        </div>
                    </div>
                    </div>                                
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12">
                            <h3>Faltas y Retardos</h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12" id="linea">         
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
        </div>
        </div>
    </div>
    </div>


    <script src="/intranet/assets/plugins/getorgchart/getorgchart.js"></script>    
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="crossorigin="anonymous"></script>
    <script src="/intranet/assets/js/timeline.js" type="text/javascript"></script> 
    <script src="/intranet/assets/js/scanDocs.js" type="text/javascript"></script> 
    <script src="/intranet/assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script> 
    <script src="/intranet/assets/js/vendor/jquery.ui.widget.js"></script>
    <script src="/intranet/assets/js/jquery.iframe-transport.js"></script>
    <script src="/intranet/assets/js/jquery.fileupload.js"></script>
    <script src="/intranet/assets/js/organigrama.js"></script>
</body>
</html>

 