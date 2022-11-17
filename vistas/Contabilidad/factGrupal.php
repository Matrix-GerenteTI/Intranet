<?php  $this->layout("rootIndex") ?>

<?php $this->push("styles") ?>
    <style>
        .tableFixHead          { overflow-y: auto; height: 400px; }
        .tableFixHead thead th { position: sticky; top: 0; }

        /* Just common table stuff. Really. */
        table  { border-collapse: collapse; width: 100%; }
        th, td { padding: 8px 16px; }
        th     { background:#eee; }        


        .lds-roller {
            display: inline-block;
            position: relative;
            width: 80px;
            height: 80px;

            }
            .lds-roller div {
            animation: lds-roller 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
            transform-origin: 40px 40px;
            
            }
            .lds-roller div:after {
            content: " ";
            display: block;
            position: absolute;
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #fff;
            margin: -4px 0 0 -4px;
            }
            .lds-roller div:nth-child(1) {
            animation-delay: -0.036s;
            }
            .lds-roller div:nth-child(1):after {
            top: 63px;
            left: 63px;
            }
            .lds-roller div:nth-child(2) {
            animation-delay: -0.072s;
            }
            .lds-roller div:nth-child(2):after {
            top: 68px;
            left: 56px;
            }
            .lds-roller div:nth-child(3) {
            animation-delay: -0.108s;
            }
            .lds-roller div:nth-child(3):after {
            top: 71px;
            left: 48px;
            }
            .lds-roller div:nth-child(4) {
            animation-delay: -0.144s;
            }
            .lds-roller div:nth-child(4):after {
            top: 72px;
            left: 40px;
            }
            .lds-roller div:nth-child(5) {
            animation-delay: -0.18s;
            }
            .lds-roller div:nth-child(5):after {
            top: 71px;
            left: 32px;
            }
            .lds-roller div:nth-child(6) {
            animation-delay: -0.216s;
            }
            .lds-roller div:nth-child(6):after {
            top: 68px;
            left: 24px;
            }
            .lds-roller div:nth-child(7) {
            animation-delay: -0.252s;
            }
            .lds-roller div:nth-child(7):after {
            top: 63px;
            left: 17px;
            }
            .lds-roller div:nth-child(8) {
            animation-delay: -0.288s;
            }
            .lds-roller div:nth-child(8):after {
            top: 56px;
            left: 12px;
            }
            @keyframes lds-roller {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
            }

    </style>
<?php $this->end() ?>

<?php  $this->push('maincontent') ?>
    <div id ="loader"    style="display: none;position:absolute;width: 100%; height: 100%; z-index:9999999; background: rgba(0,0,0,0.3);  display: flex;
  flex-wrap: wrap;
  align-content: center; justify-content:center">
    <div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
    </div>
        <div class="col-md-12">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#">Facturacción Grupal</a></li>
                </ul>
                <div class="tab-content" style="max-height:800px;min-height:800px;">
	
                    <div class="tab-pane fade in active">
                        <div class="row">
							<div class="col-md-4">

								<div class="form-group">
									<label for="field-1" class="control-label">Inicio</label>

									<input type="text" class="form-control datepicker col-md-4" id="inicioFacturacion" data-format="dd/mm/yyyy">

								</div>	

                            </div>     
							<div class="col-md-4">

								<div class="form-group">
									<label for="field-1" class="control-label">Fin</label>

									<input type="text" class="form-control datepicker col-md-4" id="finFacturacion" data-format="dd/mm/yyyy">

								</div>	

                            </div>   
                            <div class="col-md-2 col-xs-4">
                                </br>
                                <a href="javascript: getMovimientos();" class="btn btn-primary btn-block">Generar</a>
                            </div>                                                                            
                        </div>
                        <div class="row">
                            <div class="col-md-8 col-md-offset-2 text-center">
                                    <h4>Movimientos en cuentas Bancarias</h4>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-center">
                                    <h4 class="col-md-4 col-md-offset-4 text-center">Total: <b id="totalGlobal"></b></h4>
                            </div>
                        </div>                        
                        <div class="content">  
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-md-5 col-md-offset-4 table-responsive " >
                                        <table class="table table-hover">
                                            <th>Movimiento</th>
                                            <th>Monto</th>
                                            <th>Facturado</th>
                                            <th>Diferencia</th>
                                            <tbody id="tblTotales">

                                            </tbody>
                                        </table>                                        
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <h3>Detalle de movimientos <b id="showMovto"></b></h3>
                            </div>
                        </div>

                        <div class="col-md-12 table-responsive tableFixHead" >
                            <table class=" table table-hover">
                                <thead>
                                    <tr>
                                        <th>Cantidad ($)</th>
                                        <th>Fecha</th>
                                        <th>Banco</th>
                                        <th>Referencia</th>     
                                        <th>Sucursal</th>                               
                                    </tr>                                    
                                </thead>
                                <tbody id="tblDesgloceMovtos" >
                                    
                                </tbody>
                            </table>
                        </div>                        
                    </div>
                </div>
            </div>
            
<?php $this->end() ?>

<?php $this->push("scripts") ?>
    <script>
        let dataObtenida = [];
        
        muestraMovtos = ( movimiento ) =>{
            let template = '';
            $("#showMovto").text( movimiento.toUpperCase() );
            $.each( dataObtenida[movimiento]['movimientos'], function (i, item) { 
                
                 template += `
                                            <tr>
                                                <td><b>$ ${new Intl.NumberFormat("en-US").format( item.ingresos)}</b></td>
                                                <td>${ item.fecha}</td>
                                                <td>${ item.banco}</td>
                                                <td>${ item.referencia}</td>
                                                <td class="text-center">${ item.beneficiario}</td>
                                            </tr>
                                        `;
            });
            console.log( template);

            $("#tblDesgloceMovtos").html( template);
        }
        getMovimientos = () =>{
            if ( $("#inicioFacturacion").val() == ''  &&  $("#finFacturacion").val() != '' ) {
                alert("Debes seleccionar una fecha de inicio de consulta")
                return;
            }else if( $("#inicioFacturacion").val() != ''  &&  $("#finFacturacion").val() == ''  ){
                alert("Debes seleccionar una fecha de finalización de consulta")
                return;

            }

            $("#loader").show();
            $.get("/intranet/contabilidad/bancos/movimientos", {
                inicio: $("#inicioFacturacion").val(),
                fin: $("#finFacturacion").val()
            },
                function (data, textStatus, jqXHR) {
                    let defaultMovto = "tarjetas";
                    let template = '';
                    let totalGlobal = 0;
                    dataObtenida =  data;
                    $.each( data , function (i, item) { 
                        totalGlobal += isNaN( item.total) ? 0 :  item.total;
                         template += `
                                            <tr onclick="muestraMovtos('${i}')" style="cursor: pointer;">
                                                <td>${ i.toUpperCase() }</td>
                                                <td>${isNaN(item.total) ? '' : "$"} ${ isNaN(item.total) ? '-' : new Intl.NumberFormat("en-US").format( item.total)}</td>
                                                <td>$${ new Intl.NumberFormat("en-US").format( item.facturado) }</td>
                                                <td> ${  isNaN( item.total) ?  '<span style="color:#25a59a">+'+item.facturado+"</span>"  :  "$" +new Intl.NumberFormat("en-US").format(  item.total - item.facturado )  } </td>
                                            </tr>`;

                    });
                    $("#loader").hide();
                    $("#tblTotales").html( template);
                    muestraMovtos(  defaultMovto );
                    $("#totalGlobal").text( "$ "+ new Intl.NumberFormat("en-US").format(  totalGlobal ) );
                    
                },
                "json"
            );
        }

        getMovimientos();
    </script>
<?php $this->end() ?>