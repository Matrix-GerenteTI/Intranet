var orgChart
let nodoSeleccionado

var camera = document.getElementsByClassName("inputFileOculto")
var frame = document.getElementById('frame');

// console.log(camera);
// alert("Cambio")
camera[0].addEventListener('change', function (e) {
    var file = e.target.files[0];
});

$('.inputFileOculto').on("change", function () {
    formData = new FormData(document.getElementById('frmFoto'));
    formData.append("op", "subir");
    formData.append('trabajador', $("#trabajadorField").val())
    $.ajax({
        type: "post",
        url: "/intranet/controladores/organigrama/organigrama.php",
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "text",
        success: function (response) {
            $(".inputFileModificado input").css("background-size", `200px 200px`);
            $(".inputFileModificado input").css("background-image", `url(${response})`);
            // console.log($("#parentId").val( ));
            nodoSeleccionado.foto = response
            orgChart.updateNode($("#nodoId").val(), $("#parentId").val(), nodoSeleccionado);


        }
    });
});


var timelines
eventsMinDistance = 60;



function agregaHijos(data) {

    $.get("/intranet/controladores/organigrama/organigrama.php", {
        op: "trabajadoresNodo",
        hijo: data.idhijo,
        padre: data.idpadre
    },
        function (response, textStatus, jqXHR) {
            $.each(response, function (i, item) {
                if (i > 0) {
                    nodoPadre = data.idpadre;
                    $.each(orgChart.nodes, function (j, nodos) {
                        if (nodos.id == data.idpadre + "_" + item.idSucursal) {
                            nodoPadre = data.idpadre + "_" + item.idSucursal;

                        }
                        // console.log(nodos.id +"     "+ data.idpadre+"_"+item.idSucursal)

                    });
                    orgChart.insertNode(nodoPadre, {
                        descripcion: item.descripcion,
                        contrato: item.contrato,
                        fechainiciolab: item.fechainiciolab,
                        nip: item.nip,
                        nombre: item.nombre,
                        sueldo: item.sueldo,
                        curp: item.curp,
                        nss: item.nss,
                        sucursal: item.sucursal,
                        idSucursal: item.idSucursal,
                        iddepartamento: item.iddepartamento,
                        mames: 2,
                        foto: item.foto,
                    }, data.idhijo + "_" + item.idSucursal);
                }

            });
        },
        "json"
    );

}
function showTrabajador(padre, hijo, nip) {
    $.get("/intranet/controladores/organigrama/organigrama.php", {
        op: "trabajadoresNodo",
        hijo: hijo,
        padre: padre,
        nip: nip
    },
        function (data, textStatus, jqXHR) {
            $('#myModal').modal()
            $("#puesto").val(data[0].descripcion)
            $("#nombre").val(data[0].nombre);
            $("#adscripcion").val(data[0].sucursal);
            $("#sueldo").val("$" + parseFloat(data[0].sueldo * 2).toFixed(0));
            $("#inicioLabores").val(data[0].fechainiciolab.replace(/-/g, '/'));
            $("#curpEmpleado").val(data[0].curp);
            $("#nssEmpleado").val(data[0].nss);
            $("#trabajadorField").val(data[0].nip)
            if (data[0].foto != null) {
                $(".inputFileModificado input").css("background-size", `200px 200px`);
                $(".inputFileModificado input").css("background-image", `url(${data[0].foto})`);
            } else {
                $(".inputFileModificado input").css("background-image", `url('/intranet/assets/img/network.svg')`);
            }
            setTimeLine(data[0].nip, data[0].contrato, data[0].parentId, data[0].id)
        },
        "json"
    );
}

function clickHandler(sender, args) {
    nodoSeleccionado = args.node.data
    $('#myModal').modal()
    $("#nodoId").val(args.node.id)
    $("#parentId").val(args.node.pid)
    $("#puesto").val(args.node.data.descripcion)
    $("#nombre").val(args.node.data.nombre);
    $("#adscripcion").val(args.node.data.sucursal);
    $("#sueldo").val("$" + parseFloat(args.node.data.sueldo * 2).toFixed(0));
    $("#inicioLabores").val(args.node.data.fechainiciolab.replace(/-/g, '/'));
    $("#curpEmpleado").val(args.node.data.curp);
    $("#nssEmpleado").val(args.node.data.nss);
    $("#trabajadorField").val(args.node.data.nip)
    if (args.node.data.foto != null) {
        $(".inputFileModificado input").css("background-size", `200px 200px`);
        $(".inputFileModificado input").css("background-image", `url(${args.node.data.foto})`);
    } else {
        $(".inputFileModificado input").css("background-image", `url('/intranet/assets/img/network.svg')`);
    }
    setTimeLine(args.node.data.nip, args.node.data.contrato, args.node.pid, args.node.id)

}

function setTimeLine(nip, contratoId, padre, hijo) {

    //obtener deducciones del mes del empleado
    $.get("/intranet/controladores/organigrama/organigrama.php", { op: "deducciones", empleado: nip, contrato: contratoId },
        function (data, textStatus, jqXHR) {
            $("#listaDeducciones").html("")
            $("#listaAsistencia").html("");
            $("#contenidoAsistencia").html("");
            eventsMinDistance = 60;
            // timelines = undefined;         
            if (data.incidencias.length > 0) {
                $.each(data.incidencias, function (i, item) {
                    $("#listaDeducciones").append(`<li>${item.descripcion} <strong>Importe: $</strong>${item.importe} <b>Fecha de Cargo: </b>${item.fechaCargo}</li>`);
                });
            }


            $.get("/intranet/controladores/organigrama/organigrama.php", {
                op: "trabajadoresNodo",
                hijo: hijo,
                padre: padre
            },
                function (data, textStatus, jqXHR) {
                    let templateLi = "";
                    // console.log( padre );
                    // console.log(data);


                    $.each(data, function (i, item) {
                        templateLi += `<li><a href="#" onclick="showTrabajador(${item.parentId}, ${item.id}, ${item.nip})">${item.nombre}</a></li>`;
                    });

                    $("#dropdownTrabajador").html(templateLi)
                },
                "json"
            );

            $('#fileupload').fileupload({
                dataType: 'json',
                formData: { trabajador: nip },
                done: function (e, data) {
                    setTimeout(() => {
                        alert("Documento almacenado correctamente");
                        $('#progress .bar').css(
                            'width', 0 + '%'
                        );
                        getDocumentosTrabajador(nip)
                    }, 1500);

                },
                progressall: function (e, data) {
                    // console.log(data )
                    // console.log(data.loaded / data.total * 100, 10)
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    $('#progress .bar').css(
                        'width',
                        progress + '%'
                    );
                }
            });

            let template = ``;
            let liElement = "";
            let contenidoFecha = "";
            if (data.asistencia.length > 0) {

                cantAsistencia = data.asistencia.length;
                fechaAnterior = ""
                let fecha = "";

                $.each(data.asistencia, function (i, item) {
                    if (item.hora < 16) {
                        if (item.fecha != fechaAnterior) {
                            fechaAnterior = item.fecha
                            fecha = item.fecha.split('/');
                            console.log(`${fecha[0]}/${fecha[1]}     ${item.minuto}`);
                            
                            if ( parseInt(item.minuto) > 30) {
                                
                                liElement += `<li><a href="#0" class="itemRetardo"  data-date="${item.fecha}">${fecha[0]}/${fecha[1]}</a></li>`
                            } else {
                                
                                liElement += `<li><a href="#0" class="itemPuntual"  data-date="${item.fecha}">${fecha[0]}/${fecha[1]}</a></li>`
                            }
                            

                            //    $("#listaAsistencia").append(`<li><a href="#0"  data-date="${item.fecha}">${fecha[0]}/${fecha[1]}</a></li>`);

                            contenidoFecha += `<li data-date="${item.fecha}" >
                                                                <h3>Asistencia: ${item.fecha}<h3>  
                                                                <p>Entrada: ${item.hora}:${item.minuto} `;
                                                                                        
                            contenidoFecha += item.minuto > 30 ? `<span style="color:red">Retardo</span> </p>` : `</p>`;

                            if ((i + 1) <= cantAsistencia - 1) {
                                // console.log(data.asistencia[i+1].hora)
                                if (data.asistencia[i + 1].hora > 16) {
                                    contenidoFecha += `<p>Salida: ${data.asistencia[i + 1].hora}:${data.asistencia[i + 1].minuto}</p>`
                                } else {
                                    contenidoFecha += `<p>Salida: Sin registro</p>`
                                }
                            }
                            contenidoFecha += `</li>`;
                            // $("#contenidoAsistencia").append( template);                                                     


                        }
                    }
                    if (item.hora == undefined) {
                        fecha = item.fecha.split('/');
                        liElement += `<li><a href="#0" class="itemFalta" data-date="${item.fecha}">${fecha[0]}/${fecha[1]}</a></li>`
                        contenidoFecha += `<li data-date="${item.fecha}">
                                                                            <h3>Fecha: ${item.fecha}<h3>
                                                                            <em style="color:red">Falta</em>
                                                                    </li>`;
                    }
                });

            }
            template = `  <section class="cd-horizontal-timeline">
                                <div class="timeline">
                                    <div class="events-wrapper">
                                        <div class="events">
                                            <ol id="listaAsistencia">
                                                ${liElement}
                                            </ol>

                                            <span class="filling-line" aria-hidden="true"></span>
                                        </div> <!-- .events -->
                                    </div> <!-- .events-wrapper -->
                                        
                                    <ul class="cd-timeline-navigation">
                                        <li><a href="#0" class="prev inactive">Prev</a></li>
                                        <li><a href="#0" class="next">Next</a></li>
                                    </ul> <!-- .cd-timeline-navigation -->
                                </div> <!-- .timeline -->

                                <div class="events-content">
                                    <ol id="contenidoAsistencia">
                                            ${contenidoFecha}
                                    </ol>
                                </div> <!-- .events-content -->
                            </section>  `
            $("#linea").html(template);
            if (data.asistencia.length > 0) {
                timelines = $(".cd-horizontal-timeline"),
                    (timelines.length > 0) && initTimeline(timelines);
            }
            getDocumentosTrabajador(nip)

        },
        "json"
    );

}


$("#tipoOrganigrama").change(function (e) {
    e.preventDefault();
    if ($(this).val() != 1 ) {
        $.get("/intranet/controladores/organigrama/organigrama.php", { op: 'getJerarquiaPuestos', selJerarquia: $(this).val() },
            function (data, textStatus, jqXHR) {
                let optionPuestos = '';
            $.each(data, function (i, item) { 
                 optionPuestos += `<option value='${item.idhijo}'>${item.descripcion}</option>`
            });
                $("#selPuesto").html(optionPuestos);
                cargaEncargados();
            },
            "json"
        );
    }else{
        $("#selPuesto").html("");
        $("#selJefe").html();
    }
    
});

$("#selPuesto").change(function (e) {
    e.preventDefault();
    cargaEncargados();
});

function cargaEncargados() {
        $.get("/intranet/controladores/organigrama/organigrama.php", {
                op: 'getJefePuesto',
                selPuesto: $("#selPuesto").val()
            },
            function (data, textStatus, jqXHR) {
                $(".selJefe").remove();
                $.each(data, function (i, item) {
                    $("#selJefe").append(`<option value='${item.id}_${item.nip}' class="selJefe">${item.nombre}</option>`);
                });
            },
            "json"
        );
}

$("#btnGenerarOrganigrama").click(function (e) {
    e.preventDefault();
    let tipoAbstraccion = $("#tipoOrganigrama").val();
    let elementoAbstraccion = $("#selPuesto").val();
    let personal = $("#selJefe").val();
    $.getJSON("/intranet/controladores/organigrama/organigrama.php?op=getOrganigrama", {
                tipoAbs: tipoAbstraccion,
                elemento: elementoAbstraccion,
                personal: personal
            }, function (source) {
        var peopleElement = document.getElementById("matrix");
        orgChart = new getOrgChart(peopleElement, {
            primaryFields: ["descripcion", "nombre"],
            photoFields: ['foto'],
            clickNodeEvent: clickHandler,
            enableEdit: false,
            enableDetailsView: false,
            dataSource: source,
            color: 'neutralgrey',
            theme: 'cassandra',
            enableExportToImage: true,
        });



        $.each(source, function (i, item) {
            agregaHijos(item)
        });


    });    

});
function getDocumentosTrabajador(id) {
    var filemanager = $('.filemanager'),
        breadcrumbs = $('.breadcrumbs'),
        fileList = filemanager.find('.data');

    // Start by fetching the file data from scan.php with an AJAX request

    $.get('/intranet/controladores/scanDocumentos.php', { trabajador: id }, function (data) {
        $(".data").html('');
        if (data.items.length > 0) {
            // console.log(data.items.length)
            $(".nothingfound").hide()
            $.each(data.items, function (i, item) {

                var fileSize = bytesToSize(item.size),
                    name = escapeHTML(item.name),
                    fileType = name.split('.'),
                    icon = '<span class="icon file"></span>';

                fileType = fileType[fileType.length - 1];

                icon = '<span class="icon file f-' + fileType + '">.' + fileType + '</span>';

                var file = $('<li class="files"><a href="' + item.path + '" title="' + item.path + '" class="files">' + icon + '<span class="name">' + name + '</span> <span class="details">' + fileSize + '</span></a></li>');


                $(".data").append(file);
            });
        }
        else {
            $(".nothingfound").show()
        }
    }, 'json');
}

function escapeHTML(text) {
    return text.replace(/\&/g, '&amp;').replace(/\</g, '&lt;').replace(/\>/g, '&gt;');
}


// Convert file sizes from bytes to human readable units

function bytesToSize(bytes) {
    var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    if (bytes == 0) return '0 Bytes';
    var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
    return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
}