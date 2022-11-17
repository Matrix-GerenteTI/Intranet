function busqueda() {
    $.get("/intranet/controladores/nomina/trabajadores.php?opc=getAcciones", {
    
    },
        function (data, textStatus, jqXHR) {
            let template = '';
            $.each(data, function (i, sancion) { 
                 template += `<tr>
                                            <td>${ (sancion.fecha_sancion).replace(/-/g , "/") }</td>
                                            <td onclick="descargar(${sancion.id})" style="cursor:pointer">${ sancion.sancionado}</td>
                                            <td onclick="edicion(${sancion.id})" style="cursor:pointer">${ sancion.motivo}</td>
                                            <td>${ sancion.monto }</td>
                                        </tr>`;
            });
    
            $("#tblAcorrectivas").html( template);
        },
        "json"
    );
}

function descargar( id ) {
    $.post("/intranet/controladores/nomina/Comprobantes/accionCorrectiva.php", {
        accion: id
    },
        function (data, textStatus, jqXHR) {
            window.open(data, "_blank");
        },
        "text"
    );    
}

function edicion( id ) {
    $.post("/intranet/controladores/nomina/trabajadores.php?opc=getAccionCorrectiva", {
        key: id
    },
        function (data, textStatus, jqXHR) {
            $("#idSancionado").val( data.nip );
            $("#sancionado").val( data.nombre);
            $("#motivoAccionCorrectiva").val( data.motivo );
            $("#planAccionCorrectiva").val( data.plan_accion );
            $("#monto").val( data.monto );
            $(".chkSancion[name=sancionEconomica]").val( data.monto > 0 ? [1] : [0] );
            $("#fechaDescuento").val( data.fecha_descuento);
            $("#fechaAplicacion").val( data.fecha );
            $("#puesto").val( data.idpuesto);
            $("#sucursal").val( data.idsucursal );

            $("#eliminar").attr("onclick", `eliminar(${data.idaccion})`);
            $("#editar").attr("onclick", `actualizar(${data.idaccion})`);
        },
        "json"
    );
}


function eliminar( id ) {
    $.post("/intranet/controladores/nomina/trabajadores.php?opc=delAccionCorrectiva", {
        key: id
    },
        function (data, textStatus, jqXHR) {
            if ( data > 0) {
                alert("Elemento eliminado correctamente");
                busqueda();
            } else {
                alert("No se pudo eliminar, porque ocurrió el siguiente error: "+ data )   ;
            }
            
        },
        "text"
    );
}

function actualizar(params) {
    
}

$.post("/intranet/controladores/general.php?opc=sucursales", {
    
},
    function (data, textStatus, jqXHR) {
        template = '';
        $.each( data, function (i, value) { 
            template += `<option value='${value.id}'>${value.descripcion}</option>`;
        });
        $("#sucursal").html( template );
    },
    "json"
);

$.post("/nomina/ajax/ajaxempresa.php?op=listaPuestos", {
    
},
    function (data, textStatus, jqXHR) {
        template = '';
        $.each(data, function (i, item) { 
             template += `<option value='${item.id}'>${item.puesto}</option>`
        });
        $("#puesto").html( template );
    },
    "json"
);

$(".date-correctiva").datepicker({
    format: "yyyy/mm/dd"
});

busqueda();