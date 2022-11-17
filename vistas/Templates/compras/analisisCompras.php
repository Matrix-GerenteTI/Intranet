<?php  $this->layout("rootIndex") ?>

<?php  $this->push('maincontent') ?>

<div class="col-md-12">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#">Fotografiar</a></li>
                </ul>
                <div class="tab-content" style="max-height:800px;min-height:800px;">
	
                    <div class="tab-pane fade in active">
                        <div class="row">
                            <div class="col-md-8 col-md-offset-2 text-center">
                                    <h4>Listado de compras</h4>
                            </div>
                        </div>
                        <div class="content">  
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-md-12" id="listaFacturas">
                                        
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>



<?php $this->end() ?>

<?php $this->push("scripts") ?>
    <script>
        $.get("/intranet/proveedores/facturas", {

        },
            function (data, textStatus, jqXHR) {
                let template = '';
                $.each( data , function (i, item) { 
                    template += `
                    <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h4 class="panel-title"
                                        data-toggle="collapse" 
                                        data-target="#collapseOne_${item.NUMDOCTO}" onclick="cargaListaCompra( ${ item.IDCOMPRA }) ">
                                        <b>Proveedor :</b>${item.PYM_NOMBRE}&ensp; <b>No. Documento:</b>${ item.NUMDOCTO}
                                    </h4>
                                </div>
                                <div id="collapseOne_${item.NUMDOCTO}" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <table class="table">
                                            <tr>
                                                <td><b>Fecha de Ingreso</b></td>
                                                <td>${item.FECHA}</td>
                                                <td><b>Almacen de Ingreso</b></td>
                                                <td>${item.ALMACEN}</td>
                                                <td><b>Ingresó</b></td>
                                                <td>${item.USU_NOMBRE} ${item.USU_APELPAT}</td>
                                            </tr>
                                            <tr>
                                                <td><b>Fecha de Emisión</b></td>
                                                <td>${item.FECHAFACTPROV}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                    `;
                });
                $("#listaFacturas").html( template );
            },
            "json"
        );

        function cargaListaCompra( idCompra ) {
            $.get("/intranet/proveedores/facturas/listaItems", {
                compra: idCompra
            },
                function (data, textStatus, jqXHR) {
                    
                },
                "json"
            );
        }

    </script>
<?php $this->end() ?>

