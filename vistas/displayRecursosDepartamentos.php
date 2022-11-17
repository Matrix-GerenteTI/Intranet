
<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
    <div class="page-title">
        <div class="pull-left">
            <h1>Recursos</h1>
        </div>
    </div>
</div>

<div class="clearfix"></div>

<div class="col-md-12">
    <div class="tab-content">
            <input type="hidden" id="departamento" value="<?= $_GET['view'] ?>">
            <div class="row">
                <div class="col-xs-10 col-sm-10 col-md-10">
                    <ol class="breadcrumb" id="directorioArchivos">
                        
                    </ol>
                </div>
                <div class="col-xs-1 col-sm-1 col-md-1 text-center">
                    <img src="/intranet/assets/images/png/folder_add.png" id="upFolder" class="img-responsive imgbuscadorRecursos" style="cursor:pointer;">
                </div>
                <div class="col-xs-1 col-sm-1 col-md-1 text-center " id="upFilesResources" style="cursor:pointer;">
                    <img src="/intranet/assets/images/png/file_add.png" class="img-responsive imgbuscadorRecursos">
                                                                 
                </div>                 
            </div>
            <hr>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12" id="displayFiles">
                    
                </div>            
            </div>
    </div>
    <div class="row">
            <div class="col-md-12">
                    <h2>Escribir un comentario</h2>
                    <div class="tab-content">
                            <div class="newPost">
                                    <div class="forumDivOuter">
                                        <textarea id="comment" class="autoExpand forumPost form-control" rows="4" data-min-rows="4" placeholder="Ingresa aquí tu comentario" style="margin: 0px 134.656px 0px 0px; height: 155px; width: 1065px;"></textarea>
                                        <br>
                                        <button class="forumPostButton btn btn-default" id="setComments">Publicar</button>
                                    </div>
                            </div>
                    </div>
            </div>
    </div>
</div>
<div class="" id="configHidden">
    <input type="file" id="resourceToSave" name="resourceToSave[]" style="display:none" multiple>
    <input type="text" id="typeNew" style="display:none">
</div>

<input type="hidden" id="nivel" value="<?= $_SESSION['nivelT'] ?>">

  <!-- Modal -->
  <div class="modal fade" id="modalAccesoRecursos" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Modal Header</h4>
        </div>
        <div class="modal-body">
          <div class="container">
            <div class="row" id="setterNameFolder" style="display:none">
                <div class="col-md-12">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="">Nombre de la carpeta</label>
                            <input type="text" name="" id="newFolderName" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
              <div class="row" id="datosAsignacion" style="display:none">
                  <div class="col-md-3">
                    <h4>Departamento</h4>
                    <div class="row">
                        <div class="col-md-12 table-responsive" style="height:350px">
                            <table class="table table-striped">
                                <tr>
                                    <th class="text-center">
                                        Todos
                                        <br>
                                        <input type="checkbox"  id="todosDepartamentos" value="1">
                                    </th>
                                    <th class="text-center">Nombre</th>
                                </tr>
                                <tbody id="listaDepartamentos">
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                  </div>
                  <div class="col-md-9">
                    <div class="container-fluid">
                        <h4>Eempleados</h4>
                        <div class="row">
                            <div class="col-md-7 table-responsive" style="height:350px">
                                <table class="table table-striped">
                                    <tr>
                                        <th class="text-center">
                                        Todos
                                        <br>
                                        <input type="checkbox"  id="todosEmpleados">                                        
                                        </th>
                                        <th class="text-center">Nombre</th>
                                    </tr>
                                    <tbody id="listaEmpleados">
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>                                
                    </div>      
                  </div>
              </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-default" id="guardaRecurso">Guardar</button>
        </div>
      </div>
    </div>
  </div>