<div class="row">
    <div class="col-md-6 col-md-offset-4"> 
        <h3>Sistema de Gestión de Calidad</h3>
    </div>
</div>
<div class="row">
  <div class="col-md-12">
    <ul id="listaDepartamentos"> 
    </ul>
    <div class="row" id="content-sucursal-selection" style="display:none">
      <div class="col-md-10 col-md-offset-1">
        <div class="form-group">
          <label for="">Selecciona sucursal</label>
          <select name="" id="sucursalOrganigrama" class="form-control"></select>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="organigramas" style="width:100%; height:400px;"></div>
<div id="modalFinderDocumentos" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Explorador de recursos</h4>
      </div>
      <div class="modal-body">
          <div class="row">
              <div class="col-md-2 col-xs-2  col-sm-2 tarjetas">
                  <strong class="titulo-tarjeta">Políticas</strong>
                  <img src="/intranet/assets/images/politicas.png" class="img-responsive icono-tarjeta" onclick="cagraBuscador('Politicas')">
              </div>
              <div class="col-md-2 col-xs-2  col-sm-2 tarjetas">
                  <strong class="titulo-tarjeta">Perfiles</strong>
                  <img src="/intranet/assets/images/perfil.png" class="img-responsive icono-tarjeta" onclick="cagraBuscador('Perfiles')">
              </div>
              <div class="col-md-2 col-xs-2  col-sm-2 tarjetas">
                  <strong class="titulo-tarjeta">Organigramas</strong>
                  <img src="/intranet/assets/images/organigrama.png" class="img-responsive icono-tarjeta" onclick="cagraBuscador('Organigramas')">    
              </div>
              <div class="col-md-2 col-xs-2  col-sm-2 tarjetas">
                  <strong class="titulo-tarjeta">Procesos</strong>
                  <img src="/intranet/assets/images/procesos.png" class="img-responsive icono-tarjeta" onclick="cagraBuscador('Procesos')">
              </div>
              <div class="col-md- col-xs-2 col-sm-2 tarjetas" >
                  <strong class="titulo-tarjeta">Anexos</strong>
                  <img src="/intranet/assets/images/anexos.png" class="img-responsive icono-tarjeta" onclick="cagraBuscador('Anexos')">    
              </div>
              <div class="col-md- col-xs-2 col-sm-2 tarjetas" >
                  <strong class="titulo-tarjeta">Instructivos</strong>
                  <img src="/intranet/assets/images/instrucciones.png" class="img-responsive icono-tarjeta" onclick="cagraBuscador('Instructivos')">    
              </div>
          </div>
        <div class="row">
            <div id="buscador">
                    <div class="fila-divisor">
                        <div class="item-col">
                            <strong>Buscar en:</strong>
                            <input type="text" name="" id="categoriaFinder" class="form-control" disabled> 
                        </div>
                        <div class="item-col">
                            <strong>Nombre:</strong>
                            <input type="text" name="" id="descripcionFinder" class="form-control">
                        </div>
                        <div class="item-col">
                            <br>
                            <button class="btn btn-primary" id="buscarRecursos">Buscar</button>
                        </div>
                    </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-10 col-md-offset-1 col-xs-offset-1 col-sm-offset-1 table-responsive" style="height:400px;">
                <table class="table table-stripped">
                    <tr>
                        <th>Resultados</th>
                        <th>Acción</th>
                    </tr>
                    <tbody id="contentFinder">
                        
                    </tbody>
                </table>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>

  </div>
</div>





