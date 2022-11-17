<?php  $this->layout("rootIndex",['titulo' => 'Traspasos'] ) ?>

<?php $this->push("styles") ?>
    <link href="/intranet/assets/plugins/datepicker/css/datepicker.css" rel="stylesheet" type="text/css" media="screen"/>
    <style>
    .btn-label {position: relative;left: -12px;display: inline-block;padding: 6px 12px;background: rgba(0,0,0,0.15);border-radius: 3px 0 0 3px;}
    .btn-labeled {padding-top: 0;padding-bottom: 0;}
    .headerTable{
        
        font-size: 12pt;
    }
    .contentTblLista{
        text-align:center;
    }
    .raw-table-responsive {
  border: 1px solid #ccc;
  border-collapse: collapse;
  margin: 0;
  padding: 0;
  width: 100%;
  table-layout: fixed;
}

.raw-table-responsive caption {
  font-size: 1.5em;
  margin: .5em 0 .75em;
}

.raw-table-responsive tr {
  background-color: #f8f8f8;
  border: 1px solid #ddd;
  padding: .35em;
}

.raw-table-responsive th,
.raw-table-responsive td {
  padding: .625em;
  text-align: center;
}

.raw-table-responsive th {
  font-size: .85em;
  letter-spacing: .1em;
  text-transform: uppercase;
}

@media screen and (max-width: 600px) {
  .raw-table-responsive {
    border: 0;
  }

  
  .raw-table-responsive thead {
    border: none;
    clip: rect(0 0 0 0);
    height: 1px;
    margin: -1px;
    overflow: hidden;
    padding: 0;
    position: absolute;
    width: 1px;
  }
  
  .raw-table-responsive tr {
    border-bottom: 3px solid #ddd;
    display: block;
    margin-bottom: .625em;
  }
  
  .raw-table-responsive td {
    border-bottom: 1px solid #ddd;
    display: block;
    font-size: .8em;
    text-align: right;
  }
  
  .raw-table-responsive td::before {
    /*
    * aria-label has no advantage, it won't be read inside a table
    content: attr(aria-label);
    */
    content: attr(data-label);
    float: left;
    font-weight: bold;
    text-transform: uppercase;
  }
  
  .raw-table-responsive td:last-child {
    border-bottom: 0;
  }
}
    </style>
<?php $this->end() ?>

<?php $this->push("maincontent")  ?>
<h1 class="title">Relación de Traspasos</h1>
        <div class="container-fluid" id="traspasos" style="background: #fafafa; padding:10">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                                <label for="">Sucursal</label>
                                <select  class="form-control" id="udn">
                                    <option value="%">TODAS</option>
                                </select>
                        </div>                    
                    </div>                
                    <div class="col-md-3">
                        <div class="form-group">
                                <label for="">Fecha de Inicio</label>
                                <input type="text"  id="fechaIni" class="form-control datepicker" placeholder="Ej. 01/01/2000" data-format="dd/mm/yyyy">
                        </div>                    
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                                <label for="">Fecha Final</label>
                                <input type="text" id="fechaFin" class="form-control datepicker " placeholder="Ej. 01/01/2000" data-format="dd/mm/yyyy">
                        </div>                          
                    </div>      
                    <div class="col-md-3">
                        <br>
                        <button class="btn btn-primary col-md-3" @click="filtrar">Filtrar</button>
                    </div>
                </div>

                <div class="row table-responsive" style="height: 600px;overflow:auto;">
                    <div class="row" v-if="listaTraspasos.length > 0 ">
                        <div class="col-md-12"><h1>Resultados Obtenidos</h1>            </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="raw-table-responsive">
                                <thead>
                                    <tr>
                                        <th scope="col">Fecha</th>
                                        <th scope="col">Folio</th>
                                        <th scope="col">Origen</th>
                                        <th scope="col">Destino</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for=" (traspaso, i)  in listaTraspasos">
                                        <td data-label="Fecha" >{{traspaso.FECHA}} {{traspaso.HORAMOVTO}}</td>
                                        
                                        <td data-label="Folio" > {{  traspaso.NUMDOCTO }}                </td>
                                        <td data-label="Origen" > {{  traspaso.ORIGEN }}                </td>
                                        <td data-label="Destino" > {{  traspaso.DESTINO }}                </td>
                                    </tr>
                                </tbody>
                            </table>                                            
                        </div>        
                    </div>
                </div>                

        </div>
<?php $this->end() ?>

<?php $this->push("scripts")  ?>
        <script src="/intranet/assets/plugins/datepicker/js/datepicker.js" type="text/javascript"></script>
        <script src="https://cdn.jsdelivr.net/npm/vue@2.5.16/dist/vue.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.2/axios.min.js"></script>

        <script>
                $("#fechaIni").datepicker({
                    format: "dd/mm/yyyy",
                   
                });
                $("#fechaFin").datepicker({
                    format: "dd/mm/yyyy",
                   
                });

                
                $(document).ready(function () {
                    let today =new Date().toLocaleDateString()

                    $("#fechaIni").val( today);                
                    $("#fechaFin").val( today);        
                    console.log( $("#fechaFin").val() );  
                })
      

                
            let traspaso = new Vue ({
                el: "#traspasos",
                data:{
                    listaTraspasos: []
                },
                methods: {
                    filtrar: function () {  
                        axios.get("/intranet/almacen/traspasos/",{
                            params:{
                                fechaInicio: $("#fechaIni").val(),
                                fechaFin: $("#fechaFin").val(),
                                sucursal: $("#udn").val()
                            }
                        }).then( response  =>{
                                this.listaTraspasos = response.data
                        })
                    }
                },
            })
        </script>
<?php $this->end() ?>