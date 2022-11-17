
    // getting view of kind of reosources from  text field
    const viewDepartamento = $("#departamento").val();
    let documentsTree = '';

   getResources();


   function breadcrumb(departamento, nodosConsecutivos) {
       departamento = departamento.split('_');
       departamento = departamento[0];
       let listadoNodos = nodosConsecutivos.split('>');
       let cantNodos = listadoNodos.length;
       let template = `<li><a href="javascript:getResources('',0)">${departamento}</a></li>`;
       
       
       for (let index = 0; index < cantNodos; index++) {
           template += `<li ${ index == (cantNodos -1 ) ? "class='active'"  : ''  }><a href="javascript:getResources('${listadoNodos[index]}',${index})">${(listadoNodos[index]).replace(/_/g,' ')}</a></li>`;
       }

       $("#directorioArchivos").html( template );
   }

   function remueveIndexRepetidos( directorios, indexDir) {
       let listadoNodos = directorios.split('>');
       let cantNodos = listadoNodos.length;
        
        
       if ( indexDir > -1 ) {
            for (let index = 0; index < cantNodos; index++) {
                if (indexDir < index) {
                    listadoNodos.splice(index);
                }
            }

            return listadoNodos.join(">");
       }else{
           return directorios;
       }

   }

   function getResources(subDirectorios = '', indexSubDirectorio = -1) {
       if (subDirectorios != '' && documentsTree == '') {
           documentsTree = subDirectorios;
       } else if (subDirectorios != '' && documentsTree != '') {
           documentsTree += `>${subDirectorios}`;
           documentsTree = remueveIndexRepetidos(documentsTree, indexSubDirectorio);
       }else if( subDirectorios == '' && documentsTree != ''){
            documentsTree = subDirectorios;
       }else{
           
       }
              
       const nivelUsr = $("#nivel").val();
              
       //retrieving folders from server that maintain the resources 
       $.get("/intranet/controladores/buscadorRecursos.php", {
               root: viewDepartamento,
               subFolders: documentsTree
           },
           function (data, textStatus, jqXHR) {
               breadcrumb(viewDepartamento, documentsTree);
               let template = '';
               let contador = 0;
                                                                
               $.each(data, function (i, item) {
                   if (contador == 0 ) {
                    
                       template += `<div class="row">
                                                    
                                                    <div class="col-sm-2 col-xs-2 col-md-2" >`;
                        template += nivelUsr == 'ADMINISTRADOR' ?  `<span class='del-resource' onclick="deleteFile('${item.path}', '${subDirectorios}',${ indexSubDirectorio }, '${item.type}') " >x</span>` : '';
                                                        
                       if (item.type == 'folder') {
                           template += `<a href="javascript:getResources('${item.name}')" >`;
                       } else {
                           template += `<a href="${item.path}" target="_blank"> `
                       }
                       template += `<img src="/intranet/assets/images/png/${item.ext}.png" class="img-responsive imgbuscadorRecursos">
                                                            <p class="text-center"><b>${ (item.name).replace(/_/g,' ') }</b></p>
                                                        </a>
                                                    </div>`;
                        // contador = contador == -1 ? 3 :contador; //if contador is negative mean that is a root directory
                   } else {
                       template += `
                                                    <div class="col-sm-2 col-xs-2 col-md-2"  >`;
                        template += nivelUsr == 'ADMINISTRADOR' ?  `<span class='del-resource' onclick="deleteFile('${item.path}', '${subDirectorios}',${ indexSubDirectorio }, '${item.type}') " >x</span>` : '';  
                       if (item.type == 'folder') {
                           template += `<a href="javascript:getResources('${item.name}')" >`;
                       } else {
                           template += `<a href="${item.path}" target="_blank"> `
                       }
                       template += `<img src="/intranet/assets/images/png/${item.ext}.png" class="img-responsive imgbuscadorRecursos">
                                                        <p class="text-center"><b>${ (item.name).replace(/_/g,' ') }</b></p>
                                                        </a>
                                                    </div>`;
                   }
                   contador++;
                   if (contador == 6) {
                       template += `</div>`; //cerrando el contenido del row
                       contador = 0;
                   }
               });
               if (contador > 0 && contador < 6) {
                   template += `</div>`
               }

               $("#displayFiles").html(template);

           },
           "json"
       );

   }

   //Eliminar recurso del intranet
   function deleteFile( resource , subDirectorios , indexSubDirectorio, tipoArchivo  ) {
       let confirmacion = false;

       if ( tipoArchivo == 'folder') {
           confirmacion = confirm( "La carpeta se eliminará con el contenido de la misma");
       } else {
           confirmacion = confirm("¿Deseas continuar?");
       }

       if ( confirmacion) {
            $.get("/intranet/resources/delete", {
                path: resource
            },
                function (data, textStatus, jqXHR) {
                    if ( data != 1 ) {
                        alert("No se pudo cargar el archivo, ocurrió el siguiente error: "+ data );
                    }else{
                        getResources();
                    }
                },
                "text"
            );           
       }
   }

          //    will handle when user to do click on new file 
          $("#upFilesResources").click(function (e) {
              var filelement = document.getElementById('resourceToSave');
              filelement.click();
              e.preventDefault();
              //$("#resourceToSave").focus();
              //$("#resourceToSave").trigger('click');
              //$("#resourceToSave").blur();
              $("#typeNew").val("r");
              $("#datosAsignacion").css("display","block");
              $("#setterNameFolder").css("display", "none");
          });

          $("#upFolder").click(function (e) {
              e.preventDefault();
              $("#typeNew").val("f");
              $("#modalAccesoRecursos").modal("show");
              $("#datosAsignacion").css("display","none");
              $("#setterNameFolder").css("display", "block");
          });

          function hanldeUpResources() {
                $.get("/intranet/controladores/nomina/empresa.php", {
                        opc: "getAllDepartamentos"
                    },
                    function (data, textStatus, jqXHR) {
                        let template = '';
                        $.each(data, function (i, item) {
                            template += `
                                    <tr>
                                        <td><input type="checkbox" class="departamentos" value="${item.id}" name="departamento[]"></td>
                                        <td>${item.descripcion}</td>
                                    </tr>
                                `;
                        });
                        $("#listaDepartamentos").html(template);


                        $(".departamentos").change(function (e) {
                            e.preventDefault();
                            cargaTrabajadores();

                        });

                    },
                    "json"
                );
          }

          function cargaTrabajadores() {
                    let departamentos = $("input[name='departamento[]']:checked");
                    let departamentosSeleccionados = [];
                    let trabajadoresSeleccionados = [];

                    departamentos.each(function () {
                        departamentosSeleccionados.push($(this).val());
                    });
                    //check whether there are any worker checked 
                    let checkElementTrabajadores = $(".trabajadores");

                    if (checkElementTrabajadores != undefined) {
                        checkElementTrabajadores.each(function () {

                            if (!$(this).is(":checked")) {
                                trabajadoresSeleccionados.push($(this).val());
                            }
                        });
                    }

                    if (departamentosSeleccionados.length > 0) {
                        $.post("/intranet/controladores/nomina/recursos_humanos.php", {
                                opc: "getTrabajadorForResources",
                                departamentos: departamentosSeleccionados.join("#"),
                                trabajadores: trabajadoresSeleccionados
                            },
                            function (data, textStatus, jqXHR) {
                                let templateTrabjadador = ``;
                                $.each(data, function (i, item) {
                                    templateTrabjadador += `<tr>
                                                                    <td><input type="checkbox" class="trabajadores" value="${item.nip}" name="trabajador[]" ${item.check}></td>
                                                                    <td>${item.nombre}</td>
                                                                </tr>`
                                });
                                $("#listaEmpleados").html(templateTrabjadador);
                            },
                            "json"
                        );
                    } else {
                        $("#listaEmpleados").html('');
                    }
          }
          $("#resourceToSave").change(function (e) {
              //e.preventDefault();
              $("#modalAccesoRecursos").modal("show");
                hanldeUpResources();
          });

          $("#todosDepartamentos").change(function (e) {
              e.preventDefault();
              if ( $(this).is(":checked") ) {
                   let checkElementDepartamento = $(".departamentos");
                  checkElementDepartamento.each(function () {
                        $(this).prop("checked",true);
                  });
              }else{
                   let checkElementDepartamento = $(".departamentos");
                  checkElementDepartamento.each(function () {
                        $(this).prop("checked",false);
                  });                  
              }

              cargaTrabajadores();
          });

          $("#todosEmpleados").change(function (e) {
              e.preventDefault();
              if ($(this).is(":checked")) {
                  let checkElementTrabajadores = $(".trabajadores");
                  checkElementTrabajadores.each(function () {
                      $(this).prop("checked", true);
                  });
              } else {
                  let checkElementTrabajadores = $(".trabajadores");
                  checkElementTrabajadores.each(function () {
                      $(this).prop("checked", false);
                  });
              }
          });

          $("#guardaRecurso").click(function (e) {
              e.preventDefault();
              $(".cargaSeccion").css("display", "block");
              $("#modalAccesoRecursos").modal("hide");

              let checkElementTrabajadores = $(".trabajadores");
              let trabajadoresSeleccionados = [];
              if (checkElementTrabajadores != undefined) {
                  checkElementTrabajadores.each(function () {

                      if ($(this).is(":checked")) {
                          trabajadoresSeleccionados.push($(this).val());
                      }
                  });
            }
                  console.log(trabajadoresSeleccionados);

                      let formData = new FormData();
                      let tipoNuevo = $("#typeNew").val();

                      formData.append('path', documentsTree);
                      formData.append('folder', viewDepartamento);
                      formData.append("opc", "setRecursosTrabajador");
                      formData.append('trabajadores', trabajadoresSeleccionados);
                      if (tipoNuevo == 'r') {
                          let ins = document.getElementById('resourceToSave').files.length;
                          for (let x = 0; x < ins; x++) {
                              formData.append("nuevoArchivo[]", document.getElementById('resourceToSave').files[x]);
                          }                          
                        //   formData.append('nuevoArchivo[]', $("#resourceToSave").prop('files'));
                      } else {
                          formData.append("nuevoFolder", $("#newFolderName").val());
                      }

                      $.ajax({
                          type: "post",
                          url: "/intranet/controladores/nomina/recursos_humanos.php",
                          data: formData,
                          enctype: 'multipart/form-data',
                          processData: false,
                          contentType: false,
                          success: function (response) {
                              if (tipoNuevo == 'r') {
                                  if (response == 2) {
                                      alert("Se guardó correctamente el archivo, pero no pudo ser asignado a ningun trabajador");
                                  } else if (response > 2) {
                                      alert("Se guardó correctamente el archivo");
                                  } else if( isNaN(response) ) {
                                      alert("Se produjo el siguiente error al intentar guardar el archivo: "+response);
                                  }
                              }else{
                                  if (response == 1) {
                                      alert("Se ha creado la carpeta correactamente");
                                  } else if (response == -1) {
                                      alert("No se pudo crear la carpeta");
                                  } else if (isNaN(response)) {
                                      alert("Se produjo el siguiente error al intentar crear la carpeta: " + response);
                                  }else{
                                      alert("La carpeta ya se encuentra creada");
                                  }
                              }
                            $(".cargaSeccion").css("display", "none");
                            let listadoNodos = documentsTree.split('>');
                            let cantNodos = listadoNodos.length;
                            console.log(listadoNodos[cantNodos - 1] + "    ", cantNodos);

                            getResources(listadoNodos[cantNodos - 1], cantNodos - 1)
                            
                          }
                      });

              

          });

        //   FUNCIONES PARA AGREGAR COMENTARIOS A LAS CARPETAS DE RECURSOS
        $("#setComments").click(function (e) { 
            const comentario = $("#comment").val();
            $.post("/intranet/recursos/post/comments", {
                comentario: comentario,
                raiz: viewDepartamento,
                subfolders: documentsTree
            },
                function (data, textStatus, jqXHR) {
                    
                },
                "json"
            );
            
            
        });