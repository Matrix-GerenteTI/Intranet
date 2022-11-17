<?php $this->layout("rootIndex") ?>

<?php $this->push("styles") ?>
    <style>
            /* generic */

            .list ul:nth-child(odd) {
            background-color:#ddd;
            }

            .list ul:nth-child(even) {
            background-color:#fff;
            }

            /* big */
            @media screen and (min-width:600px) {
            
            .list {
                display:table;
                margin:1em;
            }
            
            .list ul {
                display:table-row;
            }
            
            .list ul:first-child li {
                background-color:#444;
                color:#fff;
            }
            
            .list ul > li {
                display:table-cell;
                padding:.5em 1em;
            }
            
            }

            /* small */
            @media screen and (max-width:599px) {
            
            .list ul {
                border:solid 1px #ccc;
                display:block;
                list-style:none;
                margin:1em;
                padding:.5em 1em;
            }
            
            .list ul:first-child {
                display:none;
            }
            
            .list ul > li {
                display:block;
                padding:.25em 0;
            }
            
            .list ul:nth-child(odd) > li + li {
                border-top:solid 1px #ccc;
            }
            
            .list ul:nth-child(even) > li + li {
                border-top:solid 1px #eee;
            }
            
            .list ul > li:before {
                color:#000;
                content:attr(data-label);
                display:inline-block;
                font-size:75%;
                font-weight:bold;
                text-transform:capitalize;
                vertical-align:top;
                width:50%;
            }
            
            .list p {
                margin:-1em 0 0 50%;
            }
            
            }

            /* tiny */
            @media screen and (max-width:349px) {
                
            .list ul > li:before {
                display:block;
            }
            
            .list p {
                margin:0;
            }
            
            }
    
    </style>
<?php $this->end() ?>

<?php $this->push('maincontent') ?>
    <div class="list">
    </div>
    <!-- Modal-->
    <div class="modal fade" id="modalIncidencias" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">
                <span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span>
            </button>
            <h4 class="modal-title" id="myModalLabel">Incidencias</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                            <b>Ingresa un monto monetario y/o una observación en caso de aplique, de lo contrario solo presione en el botón aplicar.</b>
                    </div>
                </div>
                <div class="row" style="padding:15px">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="">Monto:</label>
                            <input type="number" name="" id="cargoIncidencia" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="">Observaciones / Comentarios</label>
                            <textarea  id="comentario" cols="30" rows="7" class="form-control" placeholder="Escribe aquí una observación"></textarea>
                        </div>
                    </div>        
                </div>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal" id="btnCerrarModal">Cerrar</button>
            <button type="button" class="btn btn-primary" data-dismiss="modal" id="btnAplicarModal">Aplicar</button>
            </div>
        </div>
        </div>
    </div>
<?php $this->end() ?>

<?php $this->push('scripts') ?>
        <script>
            //variables globales 
            let contratoId, tipoDeduccion;

                $.get("/intranet/nomina/asistencia", {},
                    function (data, textStatus, jqXHR) {
                        let template = `  <ul>
                                                        <li>Empleado</li>
                                                        <li>Entrada</li>
                                                        <li>Estado</li>
                                                        <li>Aplicar</li>
                                                        <li>Eliminar</li>
                                                    </ul>`;
                        $.each(data, function (i, item) { 
                             template +=  `    <ul>
                                                        <li data-label="Nombre">${item.nombre}</li>
                                                        <li data-label="Entrada/Salida">${ item.timecheck == null ? 'FALTA' : item.timecheck}</li>
                                                        <li data-label="Estado">${ item.estado}</li>
                                                        <li data-label="Aplicar">
                                                            <div class="btn-group" role="group" aria-label="...">
                                                                <button type="button" class="btn btn-danger" ${item.aplicaSancion == 's' ? '' : 'disabled'} onclick="aplicarIncidencia(${item.contratoId}, '${item.tipoDeduccion}')">Sí</button>
                                                                <button type="button" class="btn btn-success" ${item.aplicaSancion == 's' ? '' : 'disabled'}>No</button>
                                                            </div>
                                                        </li>
                                                        <li data-label="Eliminar">x</li>
                                                        </ul>`;
                        });
                        $(".list").html( template );
                    },
                    "json"
                );

                $("#btnAplicarModal").click(function (e) { 
                    let monto = $("#cargoIncidencia").val();
                    let observacion = $("#comentario").val();

                    $.post("/intranet/nomina/incidencias", {
                        tipo: tipoDeduccion,
                        monto: monto,
                        contratoId: contratoId,
                        observaciones: observacion
                    },
                        function (data, textStatus, jqXHR) {
                            
                        },
                        "json"
                    );
                });

                function aplicarIncidencia( empleado , $deduccion) {
                    $("#modalIncidencias").modal("show");
                    contratoId = empleado;
                    tipoDeduccion = $deduccion;
                }


        </script>
<?php $this->end() ?>