var orgChart;
let departamentoID;

function cagraBuscador( categoria ) {
    $("#contentFinder").html('');
    $("#modalFinderDocumentos").modal("show");
    $("#categoriaFinder").val( categoria );
    
    cargaBusquedaRecursos(categoria, '');
}

$("#buscarRecursos").click(function (e) {
   const categoria =  $("#categoriaFinder").val();
   const descripcion =  $("#descripcionFinder").val();  
   if ( categoria != '' ) {
       cargaBusquedaRecursos(categoria, descripcion);
   } else {
       $("#contentFinder").html(`<tr>
                                                        <td colspan="2"><b>Debes de seleccionar una categoría</b></td>
                                                    </tr>`);
   }
   
});

function cargaBusquedaRecursos(categoria, descripcion) {
       $.get("/intranet/controladores/buscadorArchivos.php", {
               directorio: categoria,
               archivo: descripcion
           },
           function (data, textStatus, jqXHR) {
               let template = '';
               $.each(data.items, function (i, item) {
                   template += `
                    <tr>
                        <td><img src="/intranet/assets/images/png/${item.ext}.png" style="float:left; width:20px;height:auto"> ${item.name}  </td>
                        <td><img src="/intranet/assets/images/png/search.png" class="imgBuscador" style="float:left; width:30px;height:auto;margin-left:auto;margin-right:auto;" onclick="muestraDocumento('${categoria}','${item.name}')"></td>
                    </tr>
                `;
                   $(".imgBuscador").hover(function () {
                       $(this).css('cursor', 'pointer');
                   }, function () {
                       $(this).css('cursor', 'default');
                   });

               });
               $("#contentFinder").html(template);
           },
           "json"
       );
}

function muestraDocumento( directorio, nombre) {
    window.open("/intranet/vistas/SGC/cargaDocumentos.php?directorio="+directorio+"&archivo="+nombre,'_blank');
}


    $.get("/intranet/controladores/organigrama/organigrama.php", {
        departamento: '%',
        op: 'getDepartamento'
    },
      function (data, textStatus, jqXHR) {
        let template = '';
        let index = 3;
        template += `<li>
                                        <a href="javascript:CargaOrganigrama('General')" class="menu-sgc-org" >
                                        <span class="menu-title menu-title_2">General</span>
                                </li>`
        $.each(data, function (i, item) { 
             template += `<li>
                                        <a href="javascript:CargaOrganigrama('${item.id}')" class="menu-sgc-org" >
                                            <span class="menu-title menu-title_${index}">${item.descripcion}</span>
                                        </a>
                                    </li>`
            index = index <= 4 ? index +1 : 2;
        });
        template += `<li>
                                        <a href="javascript:CargaOrganigrama('js')" class="menu-sgc-org" >
                                        <span class="menu-title menu-title_${index}">Jefes de Sucursal</span>
                                </li>`
          $("#listaDepartamentos").html( template);
      },
      "json"
    );

    $.get("/intranet/controladores/organigrama/organigrama.php", {
        op: "getSucursales"
    },
        function (data, textStatus, jqXHR) {
            let template = '';
            $.each(data, function (i, item) { 
                 template += `<option value='${item.id}'>${item.descripcion}</option>`
            });
            $("#sucursalOrganigrama").append(template);
        },
        "json"
    );

    $('#sucursalOrganigrama').on('change', function () {
        let sucursal = $(this).val();
        setEstructuraOrganigrama(departamentoID, sucursal);
    });

function CargaOrganigrama(idDepartamento ) {
    // let sucursal = $("#sucursalOrganigrama").val();
        departamentoID = idDepartamento
        if ( idDepartamento != 'js') {
            $("#content-sucursal-selection").css('display', 'none');
            setEstructuraOrganigrama(idDepartamento )
        }else{
            $("#content-sucursal-selection").css('display','block');
        }
    }

    function setEstructuraOrganigrama( departamento, sucursal = '' ) {
        $.get("/intranet/controladores/organigrama/organigrama.php", {
            op: 'getOrgSGC',
            departamento: departamento,
            sucursal: sucursal
        },
            function (data, textStatus, jqXHR) {
                var peopleElement = document.getElementById("organigramas");
                orgChart = new getOrgChart(peopleElement, {
                    primaryFields: ["puestoHijo", "depaHijo"],
                    // photoFields: ['foto'],
                    clickNodeEvent: function () {
                        $("#modalFinderDocumentos").modal("show");

                    },
                    enableEdit: false,
                    enableDetailsView: false,
                    dataSource: data,
                    color: 'neutralgrey',
                    theme: 'cassandra',
                    enableExportToImage: true,
                });
            },
            "json"
        );              
    }


