<?php $this->layout("rootIndex", ['titulo' => 'Fotografiar' ]) ?>

<?php $this->push("styles") ?>
    <style>
        .content {
            position: absolute;
                left: 50%;
                top: 50%;
                -webkit-transform: translate(-50%, -50%);
                transform: translate(-50%, -50%);
         }

         .img-preview{
                max-width:  90%;
                min-width:  80%;
                height:  auto;
         }
         .inTrabajador,.botonera{
            max-width:  90%;
            min-width:  80%;
         }
         .botonera{
             float: right;
                background:  "#f00"
         }

         @media only screen and (max-width: 600px) {
            .img-preview{
                max-width:  100%;
                min-width:  80%;
                height:  auto;
         }
         .content {
            position: relative;
            left: 50%;
                top: 50%;
                -webkit-transform: translate(-15%, 50%);
                transform: translate(-50%, 50%);
         }

         .inTrabajador,.botonera{
            max-width:  100%;
            min-width:  90%;
         }

        }

    </style>
<?php $this->end() ?>
<?php $this->push("maincontent")  ?>


<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
        <div class="page-title">
            <div class="pull-left">
                <h1>Idenficiación de Colaboradores</h1>
            </div>
        </div>
    </div>

            <div class="clearfix"></div>

            <div class="col-md-12">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#">Fotografiar</a></li>
                </ul>
                <div class="tab-content" style="max-height:800px;min-height:800px;">
	
                    <div class="tab-pane fade in active">
                        <div class="row">
                            <div class="col-md-8 col-md-offset-2 text-center">
                                    <h3>Captura o importa una imagen desde tu dispositivo</h3>
                            </div>
                        </div>
                        <div class="content">
                            <img src="/intranet/assets/images/add-image.png"  id="preview" class="img-preview"/>
                            <input type="file" accept="image/*;capture=camera" id="fotoEmpleado">
                            <input type="text" class="form-control inTrabajador" id="trabajador" placeholder="Buscar nombre aquí...">
                                <div id="suggestions-container" style="position: relative; float: left; width: 400px; margin: 10px;"></div>
                                <div class="botonera">
                                    <button class="btn btn-primary" id="guardaImg">Guardar</button>
                                </div>
                            
                        </div>
                    </div>
                </div>
            </div>



<?php $this->end() ?>


<?php $this->push("scripts") ?>
    <script src="/intranet/assets/js/jquery.autocomplete.js"></script>

    
    <script >
        let empleadoNip = -1;

        $("#fotoEmpleado").change(function() {
            var oFReader = new FileReader();
            oFReader.readAsDataURL(document.getElementById("fotoEmpleado").files[0]);

            oFReader.onload = function (oFREvent) {
                
                document.getElementById("preview").src = oFREvent.target.result;
            };

        });

        $.get("/intranet/trabajadores/all", {},
            function (data, textStatus, jqXHR) {
                let empleado = [];
                $.each( data, function (i , value) { 
                     empleado.push( { value : value.nombre , data: value.id } )
                });
                console.log( empleado );
                

                $('#trabajador').autocomplete({
                    source: empleado,
                    appendTo: '#suggestions-container',
                    select: function(event, ui) {
                             empleadoNip = (ui.item.data);
                    }   
                });
            },
            "json"
        );

        $("#guardaImg").click(function (e) { 
            e.preventDefault();
            let fotoIn = document.getElementById("fotoEmpleado");
            let foto = fotoIn.files[0];

            if ( empleadoNip != - 1) {
                let data = new FormData();
                data.append("foto", foto);
                data.append('nip', empleadoNip);
                $.ajax({
				url:"/intranet/trabajadores/fotografiar/save",
				type:'POST',
				contentType:false,
				data:data,
				processData:false,
				cache:false}).done(function(resp1){		
                    if ( resp1 == 1) {
                        alert("Fotografía almacenada correctamente");
                        document.getElementById("preview").src = "/intranet/assets/images/add-image.png";
                        fotoIn.value = null;
                        empleadoNip = -1;
                        $("#trabajador").val('');
                    } else {
                        alert("No se pudo guardar la imagen");
                    }
                });
                
            } else {
                alert("Debes seleccionar un trabajador")
            }
        });


    </script>
<?php $this->end() ?>