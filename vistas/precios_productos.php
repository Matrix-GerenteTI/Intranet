<div class="tab-content" style="margin-right: 15px;" id="muestrameResgs">
    <div class="row">
        <div class="col-md-12">
            <h1 style="width: 100%;">BUSCADOR DE PRODUCTOS</h1>
        </div>
        
        <div class="col-lg-12">
            <div class="form-group">                
                <input type="text" class="form-control" id="codproduct" placeholder="Código del producto">
            </div>
            <div class="form-group">                
                <input type="text" class="form-control" id="descripProduct" placeholder="Descripción del producto">
            </div>
            
            <button id="searchProduct" class="btn btn-default" style="position: relative; left:89%;">Buscar</button>
        </div>

        <div class="col-md-12">
            <table class="table table-borderless">
                <thead>
                    <tr>
                        <th scope="col">Código del Artículo</th>
                        <th scope="col">Descripción</th>
                <?php
                    $expusuarioprecios = explode(',',$_SESSION['usuarioprecios']);
                    foreach($expusuarioprecios as $precio){
                ?>
                        <th scope="col">PVP<?=$precio?></th>
                <?php
                    }
                ?>
                        <th scope="col">EXIST.</th>
                        <th scope="col">ALMACEN</th>
                    </tr>
                </thead>
                <tbody id="tbodyConsultaParte">
                    
                </tbody>
            </table>
        </div>

    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="imageModalLabel"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="imagenProd" style="text-align: center">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>