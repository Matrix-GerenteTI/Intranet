<?php  $this->layout("rootIndex",['titulo' => 'Requisiciones'] ) ?>

<?php $this->push("styles") ?>
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

        <h1 class="title">Requisición de Insumos</h1>
        <div class="container-fluid" id="requisiciones" style="background: #fafafa; padding:10">
            <div class="modal fade" id="modaItems" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel"><b>Insumos Solicitados</b></h4>
                    </div>
                    <div class="modal-body">
                    
                    <div class="row table-responsive" style="padding:15px">

                            <table class="raw-table-responsive">
                                <tr>
                                    <th scope="col">Cant.</th>
                                    <th scope="col">Descripción</th>
                                    <th scope="col">Entregar</th>
                                </tr>
                                <tr v-for="insumo in insumosASurtir" >
                                    <td data-label="Cantidad">{{ insumo.cantidad_solicitado}}</td>
                                    <td data-label="Descripci&oacute;n">{{ insumo.item }}</td>
                                    <td data-label="Cantidad a surtir"><input class="form-control" type="text" @focusout="setCantidadOtorgada(insumo.idinsumo)" :id="'item_'+insumo.idinsumo" :disabled="insumo.fecha_entregado != null " :value="insumo.cantidad_entregada != 0 ? insumo.cantidad_entregada : '' " /></td>
                                </tr>
                            </table>
                    </div>
                    </div>
                    <div class="modal-footer">
                    <button v-if="!verReimpresion" type="button" class="btn btn-primary" data-dismiss="modal" @click="surtir()">surtir</button>
                    <button v-else type="button" class="btn btn-primary" data-dismiss="modal" @click="reimpresionSurtido()">Reimprimir</button>
                    </div>
                </div>
                </div>
            </div>

            <div class="row">
                    <div class="col-md-12" style="background: #37474f;">
                        <h3 style="font-weight: bolder;text-decoration:underline;color:#fafafa">Pendientes</h3>
                    </div>
                </div>            
            <div class="row table-responsive" style="height: 300px;overflow:auto;">
                <table class="raw-table-responsive">
                    <thead>
                        <tr>
                            <th scope="col">Sucursal</th>
                            <th scope="col"> Solicitado</th>
                            <th scope="col">Ver</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for=" (sucursal, i)  in requisicionesPendientes">
                            <td data-label="Sucursal" >{{sucursal.sucursal}}</td>
                            <td data-label="Solicitado" >{{sucursal.fecha_solicitado}}</td>
                            <td data-label="Ver" >        
                                    <button type="button" class="btn btn-labeled btn-warning" style="margin-bottom:10px;" @click="getItemsSolicitados(sucursal.id) ">
                                    <span class="btn-label"><i class="glyphicon glyphicon-eye-open"></i></span>Ver</button>
                            </td>
                            
                        </tr>
                    </tbody>
                </table>

            </div>
            <hr>

                <div class="row">
                    <div class="col-md-12" style="background: #37474f;">
                        <h3 style="font-weight: bolder;text-decoration:underline;color:#fafafa">Hist&oacute;rico</h3>
                    </div>
                </div>
                <div class="row" style="height: 300px">
                    <div class="col-md-12 table-responsive">
                        <table class="table table-striped">
                            <tr>
                                <th class="headerTable contentTblLista" >Sucursal</th>
                                <th class="headerTable contentTblLista"> Solicitado</th>
                                <th class="headerTable contentTblLista"> Entregado</th>
                                <th class="headerTable contentTblLista">Ver</th>
                            </tr>
                            <tr v-for=" (sucursal, i)  in requisicionesEntregadas">
                                <td class="contentTblLista">{{sucursal.sucursal}}</td>
                                <td class="contentTblLista">{{sucursal.fecha_solicitado}}</td>
                                <td class="contentTblLista">{{sucursal.fecha_entregado}}</td>
                                <td class="contentTblLista">        
                                 <button type="button" class="btn btn-labeled btn-primary" style="margin-bottom:10px;" @click="getItemsSolicitados(sucursal.id,true) ">
                                <span class="btn-label"><i class="glyphicon glyphicon-eye-open"></i></span>Ver</button>
                                </td>
                            </tr>
                        </table>                    
                    </div>
                </div>

        </div>



<?php $this->end() ?>


<?php $this->push("scripts")  ?>
        <script src="https://cdn.jsdelivr.net/npm/vue@2.5.16/dist/vue.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.2/axios.min.js"></script>

        <script>
           root  =   new Vue({
                 el: "#requisiciones",
                 data:{
                     requisicionesPendientes: [],
                     requisicionesEntregadas: [],
                     insumosASurtir: [],
                     showItemsRequisicion: true,
                     solicitudSeleccionada: -1,
                     insumosSurtidos: [],
                     verReimpresion: false
                 },
                 mounted:function(){
                    if (  localStorage.getItem("progreso") != undefined )  {
                        localStorage.clear();
                    }
                },
                 methods:{
                     getSolicitudInsumos: function() {
                            axios.get("/intranet/requisicion/pendings")
                            .then( response => {
                                this.requisicionesPendientes = response.data
                                console.log( this.requisicionesPendientes.data );
                                
                            })
                     },
                     getItemsSolicitados: function ( id , entregado = false) {
                         this.solicitudSeleccionada = id;
                         this.verReimpresion =  entregado
                         progreso = JSON.parse(  localStorage.getItem("progreso") )
                        //ya hay una lista en la sesssion de navegador
                        if( localStorage.getItem("progreso") != undefined ){
                            
                            if ( progreso[id] != undefined) {
                                this.insumosASurtir =  progreso[id];
                            } else {
                                axios.get(`/intranet/requisicion/pendings/${id}`)
                                .then( response => {
                                    this.insumosASurtir = response.data
                                    
                                    progreso[id] = response.data;
                                    localStorage.setItem("progreso", JSON.stringify( progreso ) )
                                })                                
                            }
                        }else{
                            axios.get(`/intranet/requisicion/pendings/${id}`)
                                .then( response => {
                                    this.insumosASurtir = response.data
                                    progreso = [];
                                    progreso[id] = response.data;
                                    localStorage.setItem("progreso", JSON.stringify( progreso ) )
                                })
                        }
                         $("#modaItems").modal('show');

                     },
                     setCantidadOtorgada: function ( idInsumo) {
                         
                        let progreso = localStorage.getItem("progreso");
                        let savedProgress = [];
                        progreso = JSON.parse( progreso );
                        progreso = progreso[ this.solicitudSeleccionada ]
                        
                        
                        progreso.forEach( (element, i) => {
                            
                            if ( element.idinsumo == idInsumo ) {
                                
                                    cant = document.getElementById(`item_${idInsumo}`).value
                                    
                                    progreso[i].cantidad_entregada = cant;
                                    savedProgress[ this.solicitudSeleccionada ] =  progreso;
                                    localStorage.setItem("progreso", JSON.stringify(  savedProgress ) ) 
                                    
                                    
                                    
                                return 0;
                            }
                        });
                     },
                     reimpresionSurtido: function( ) {

                         let self = this;
                         axios.post('/intranet/requisicion/reimpresion',{
                            requisicion: self.solicitudSeleccionada,
                            
                        }).then( response => {
                            window.open(response.data ,'_blank');

                        })

                     },
                     surtir: function () {
                        let cant;
                         this.insumosASurtir.forEach( item => {
                             //getting values to set requisitions
                              cant = document.getElementById(`item_${item.idinsumo}`).value
                              this.insumosSurtidos.push({
                                  id: item.idinsumo,
                                  surtido: cant
                              })
                         })
                        
                        let self = this;
                        axios.post('/intranet/requisicion/surtir',{
                            requisicion: self.solicitudSeleccionada,
                            surtido: JSON.stringify( self.insumosSurtidos )
                        }).then( response => {
                            window.open(response.data ,'_blank');
                            if ( response.data != '404' ) {
                                this.getSolicitudInsumos();
                                this.insumosASurtir = [];
                                this.insumosSurtidos = [];
                            }else{
                                alert("Ocurrió el siguiente error: "+ response.data)
                            }
                        })
                         
                     },
                     listaSurtidos: function() {
                        axios.get("/intranet/requisicion/delivered")
                            .then( response => {
                                this.requisicionesEntregadas = response.data
                                this.getSolicitudInsumos();
                                
                            })                         
                     }
                 }
             })

             root.getSolicitudInsumos();
             root.listaSurtidos();
        </script>
<?php $this->end() ?>