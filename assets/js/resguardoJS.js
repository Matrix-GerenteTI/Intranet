let cambiaPag = 0;

function setVal(){
    $("#nameEmpleadoRecibResg").val($("#nombreEmpleadoResguardo").val());
}

function setValDos(){
    $("#recibeResgEquipComp").val($("#usuarioResguardoEquipComp").val());
}

$( "#muestrameResgs" ).ready(function() {
    $.post("/intranet/obtenerResguardo", {
            pagination: cambiaPag
        },
        function (data) {
            $("#tbodyHistorialResguardos").html("")
            let valueToAppend = ""
            if( Array.isArray(data)){
                if ($.isEmptyObject(data) ) {
                    console.log("No se encontraron resultados")
                }
                else{
                    
                    $.each(data, function (i) {
                        valueToAppend += createTemplateHistorialResg(data[i]);
                    });
                }
                
                $("#tbodyHistorialResguardos").append(valueToAppend)
            }
            else{
                alert(data.error)
            }
        },
        "json"
    );
});

$("#buscarResguardos").click(function(e){
    
	$.post("/intranet/obtenerResguardosPaginados", {
            empleado: $("#empleadoNombre").val(),
            tipoResg: $("#usoEquipoResguardo").val(),
            fechaResg: $("#fechaBusquedaResg").val(),
			pagination: cambiaPag
		},
		function (data) {
			$("#tbodyHistorialResguardos").html("")
            let valueToAppend = ""
            if( Array.isArray(data)){
                if ($.isEmptyObject(data) ) {
                    alert("No se encontraron resultados")
                }
                else{
                   
                    $.each(data, function (i) {
                        valueToAppend += createTemplateHistorialResg(data[i]);
                    });
                }
                
                $("#tbodyHistorialResguardos").append(valueToAppend)
            }
            else{
                alert(data.error)
            }
		},
		"json"
	);	
});

$('#modalResgCel').on('hidden.bs.modal', function (e) {
    
    var tipoResgSelect = document.getElementById('tipoResgSelect');
    var select = document.getElementById('usoEquipoCelResguardo');

    $(".modal-title-resg").html ("Crear nuevo resguardo")

    $("input[type=text], textarea").each(function() {
        
        if( this.id != "nameEmpleadoEntrResgCel" && this.id != "nameEmpleadoEntrResgComp" && this.value != ''){
            this.value = ''
        }
    });

    $("input:checkbox:checked").each(function() {
        if($('input[type=checkbox]:checked').length != 0){
            $(this).prop("checked", false);

        }
    })

    tipoResgSelect.value=""
    select.selectedIndex = "0";
    $("#RESGUARDO_CELULARES").hide();
    $("#RESGUARDO_EQUIPO_COMPUTO").hide();
    
    $(".selectResguardo").css("display","block")

    $("#sendResguardo").css("display","block")
    $("#updateResguardo").css("display","none")
    $("#sendResguardoEquipoComputo").css("display","block")
    $("#updateResguardoEquipoComputo").css("display","none")
})

function createTemplateHistorialResg(element) {
    console.log(element)
    valueToAppend = `
                     <tr style="cursor:pointer" onclick="getDataToResg('${element.fkid_detalle_resguardo}', '${element.tipo_resg}')">
					 <td> ${element.nombre_empleado}</td>
					 <td>${element.tipo_resg}</td>
					 <td>${element.fecha.replace(/-/g,"/")}</td>
					 </tr>`

	return valueToAppend;
}

function createTemplateHistorialResgDone(element) {
    
    valueToAppend = ""
    for(i=0; i<element.length; i = i + 1){
        //console.log("i: " + i)
        //console.log("Elementos: "+element[i].id)
        valueToAppend += `<tr style="cursor:pointer" onclick="getDataToResg('${element[i].fkid_detalle_resguardo}', '${element[i].tipo_resg}')">
					 <td> ${element[i].nombre_empleado}</td>
					 <td>${element[i].tipo_resg}</td>
					 <td>${element[i].fecha.replace(/-/g,"/")}</td>
					 </tr>`
    }
    
	return valueToAppend;
}

function getDataToResg(id, tipo_resg){
    if(tipo_resg == "RESGUARDO_CELULARES"){
        $("#newResguardo").click();
        $(".selectResguardo").css("display","none")
        $("#sendResguardo").css("display","none")
        $(".modal-title-resg").html ("Editar resguardo");

        /**
         * Obtencion de los inputs del modal de creacion de resguardos
         */
        var fechaResg = document.getElementById('fechaResguardo');
        var usoResg = document.getElementById('usoEquipoResguardo');
        var empresaResg = document.getElementById('empresaResguardo');
        var AreaDeptoResg = document.getElementById('areadeptoResguardo');
        var companiaResg = document.getElementById('companiaTelResguardo');
        var empleadoResg = document.getElementById('nombreEmpleadoResguardo');
        var puestoResg = document.getElementById('puestoEmpleadoResguardo');
        var telefonoResg = document.getElementById('telefonoAsigEmpResg');
        var modeloCelResg = document.getElementById('modeloCelAsigEmpResg');
        var imeiResg = document.getElementById('imeiCelAsigEmpResg');
        var observacionResg = document.getElementById('observacionesResg');
        var recibeResg = document.getElementById('nameEmpleadoRecibResg');
        var entregaResg = document.getElementById('nameEmpleadoEntrResg');

        var idResguardo = document.getElementById('idResguardo');

        //Obtener datos de ese resguado con el id
        $.post("/intranet/obtenerResguardosEmpleado", {
                tipoResg: tipo_resg,
                id: id
            },
            function (data) {
                
                if( Array.isArray(data)){
                    if ($.isEmptyObject(data) ) {
                        alert("No se encontraron resultados")
                    }
                    else{
                        
                        var select = document.getElementById(data[0]["uso"]+"_equipoCel")
                        
                        select.selected = "true"
                        fechaResg.value = data[0]["fecha"].replace(/^(\d{4})-(\d{2})-(\d{2})$/g,'$3/$2/$1')
                        empresaResg.value = data[0]["empresa"]
                        AreaDeptoResg.value = data[0]["area_depto"]
                        companiaResg.value = data[0]["compania"]
                        empleadoResg.value = data[0]["nombre_empleado"]
                        puestoResg.value = data[0]["puesto"]
                        telefonoResg.value = data[0]["num_cel"]
                        modeloCelResg.value = data[0]["modelo_cel"]
                        imeiResg.value = data[0]["imei_cel"]
                        observacionResg.value = data[0]["observaciones"]
                        recibeResg.value = data[0]["nombre_empleado"]
                        
                        $("input:checkbox").each(function() {
                            var paso
                            var hola = []
                            for(paso = 1; paso < 15; paso++){
                                hola[paso] = data[0]["chk_"+paso]
                                if($(this).attr("value") == hola[paso]){
                                    $(this).prop("checked", true);
                                }
                            }
                        });
                        $("#updateResguardo").css("display","block")
                        idResguardo.value = id;
                        $("#RESGUARDO_CELULARES").show()
                    }
                }
                else{
                    alert(data.error)
                }
            },
            "json"
        );
    }
    if(tipo_resg == "RESGUARDO_EQUIPO_COMPUTO"){
        $("#newResguardo").click();
        $(".selectResguardo").css("display","none")
        $("#sendResguardo").css("display","none")
        $("#sendResguardoEquipoComputo").css("display","none")
        $(".modal-title-resg").html ("Editar resguardo");

        /**
         * Obtencion de los inputs del modal de creacion de resguardos
         */
        var numResg = document.getElementById('numResg');
        var fechaResguardoEquipComp = document.getElementById('fechaResguardoEquipComp');
        var usuarioResguardoEquipComp = document.getElementById('usuarioResguardoEquipComp');
        var empresaResguardoEquipComp = document.getElementById('empresaResguardoEquipComp');
        var areaDeptoResguardoEquipComp = document.getElementById('areaDeptoResguardoEquipComp');
        var puestoResguardoEquipComp = document.getElementById('puestoResguardoEquipComp');
        var sucursalResguardoEquipComp = document.getElementById('sucursalResguardoEquipComp');
        var telefonoResguardoEquipComp = document.getElementById('telefonoResguardoEquipComp');
        var tipoEquipoResguardoEquipComp = document.getElementById('tipoEquipoResguardoEquipComp');
        var marcaEquipoResguardoEquipComp = document.getElementById('marcaEquipoResguardoEquipComp');
        var modeloEquipoResguardoEquipComp = document.getElementById('modeloEquipoResguardoEquipComp');
        var ddGbResguardoEquipComp = document.getElementById('ddGbResguardoEquipComp');
        var ramGbResguardoEquipComp = document.getElementById('ramGbResguardoEquipComp');
        var procesadorEquipoResguardoEquipComp = document.getElementById('procesadorEquipoResguardoEquipComp');
        var nsEquipoResguardoEquipComp = document.getElementById('nsEquipoResguardoEquipComp');
        var soEquipoResguardoEquipComp = document.getElementById('soEquipoResguardoEquipComp');
        var licenciaEquipoResguardoEquipComp = document.getElementById('licenciaEquipoResguardoEquipComp');
        var monitorResguardoEquipComp = document.getElementById('monitorResguardoEquipComp');
        var nsMonitorResguardoEquipComp = document.getElementById('nsMonitorResguardoEquipComp');
        var tecladoEquipoResguardoEquipComp = document.getElementById('tecladoEquipoResguardoEquipComp');
        var nsTecladoResguardoEquipComp = document.getElementById('nsTecladoResguardoEquipComp');
        var mouseEquipoResguardoEquipComp = document.getElementById('mouseEquipoResguardoEquipComp');
        var nsMouseResguardoEquipComp = document.getElementById('nsMouseResguardoEquipComp');
        var cargadorEquipoResguardoEquipComp = document.getElementById('cargadorEquipoResguardoEquipComp');
        var nsCargadorResguardoEquipComp = document.getElementById('nsCargadorResguardoEquipComp');
        var impresoraEquipoResguardoEquipComp = document.getElementById('impresoraEquipoResguardoEquipComp');
        var nsImpresoraResguardoEquipComp = document.getElementById('nsImpresoraResguardoEquipComp');
        var noBrakeEquipoResguardoEquipComp = document.getElementById('noBrakeEquipoResguardoEquipComp');
        var bocinaEquipoResguardoEquipComp = document.getElementById('bocinaEquipoResguardoEquipComp');
        var lectoraDiscoEquipoResguardoEquipComp = document.getElementById('lectoraDiscoEquipoResguardoEquipComp');
        var observacionesResguardoEquipComp = document.getElementById('observacionesResguardoEquipComp');
        var recibeResgEquipComp = document.getElementById('recibeResgEquipComp');
        var idResguardo = document.getElementById('idResguardoEquipoComp');

        //Obtener datos de ese resguado con el id
        $.post("/intranet/obtenerResguardosEmpleado", {
                tipoResg: tipo_resg,
                id: id
            },
            function (data) {
                
                if( Array.isArray(data)){
                    if ($.isEmptyObject(data) ) {
                        alert("No se encontraron resultados")
                    }
                    else{
                        console.log(data)
                        fechaResguardoEquipComp.value = data[0]["fecha"]
                        usuarioResguardoEquipComp.value = data[0]["nombre_empleado"]
                        empresaResguardoEquipComp.value = data[0]["empresa"]
                        areaDeptoResguardoEquipComp.value = data[0]["area_depto"]
                        puestoResguardoEquipComp.value = data[0]["puesto"]
                        sucursalResguardoEquipComp.value = data[0]["sucursal"]
                        telefonoResguardoEquipComp.value = data[0]["num_cel_emp"]
                        tipoEquipoResguardoEquipComp.value = data[0]["tipo_equipo"]
                        marcaEquipoResguardoEquipComp.value = data[0]["marca"]
                        modeloEquipoResguardoEquipComp.value = data[0]["modelo"]
                        ddGbResguardoEquipComp.value = data[0]["dd_gb"]
                        ramGbResguardoEquipComp.value = data[0]["ram_gb"]
                        procesadorEquipoResguardoEquipComp.value = data[0]["procesador"]
                        nsEquipoResguardoEquipComp.value = data[0]["ns_equipo"]
                        soEquipoResguardoEquipComp.value = data[0]["so"]
                        licenciaEquipoResguardoEquipComp.value = data[0]["licencia"]
                        monitorResguardoEquipComp.value = data[0]["monitor"]
                        nsMonitorResguardoEquipComp.value = data[0]["ns_monitor"]
                        tecladoEquipoResguardoEquipComp.value = data[0]["teclado"]
                        nsTecladoResguardoEquipComp.value = data[0]["ns_teclado"]
                        mouseEquipoResguardoEquipComp.value = data[0]["mouse"]
                        nsMouseResguardoEquipComp.value = data[0]["ns_mouse"]
                        cargadorEquipoResguardoEquipComp.value = data[0]["cargador"]
                        nsCargadorResguardoEquipComp.value = data[0]["ns_cargador"]
                        impresoraEquipoResguardoEquipComp.value = data[0]["impresora"]
                        nsImpresoraResguardoEquipComp.value = data[0]["ns_impresora"]
                        noBrakeEquipoResguardoEquipComp.value = data[0]["no_brake"]
                        bocinaEquipoResguardoEquipComp.value = data[0]["bocina"]
                        lectoraDiscoEquipoResguardoEquipComp.value = data[0]["dvd_cd"]
                        observacionesResguardoEquipComp.value = data[0]["observaciones"]
                        recibeResgEquipComp.value = data[0]["nombre_empleado"]
                        idResguardo.value = data[0]["id"]
                        numResg.value = data[0]["numResguardo"]
                        $("#updateResguardoEquipoComputo").css("display","block")
                        $("#RESGUARDO_EQUIPO_COMPUTO").show()
                    }
                }
                else{
                    alert(data.error)
                }
            },
            "json"
        );
    }
}

$("#sendResguardo").click(function (e) {
    if ($('input[type=checkbox]:checked').length === 0) {

        alert('Debe seleccionar al menos un valor');

    }else{

        var campos, valido
        var i = 0, j = 0
        var chk = []
        var inputs = []

        e.preventDefault();

        campos = document.querySelectorAll('#resgcel .campo')

        valido = true; // es valido hasta demostrar lo contrario

        // recorremos todos los campos
        [].slice.call(campos).forEach(function(campo) {
            inputs[i] = campo.value.trim()
            // el campo esta vacio?
            if (campo.value.trim() === '') {
                valido = false
            }else{
                i = i + 1
            }
        })

        $("input:checkbox:checked").each(function() {
            if($('input[type=checkbox]:checked').length != 0){
                chk[j] = $(this).attr("value")
                j = j + 1
            }
        })

        if (valido) {
            $(".cargaSeccion").show();
            $.post("/intranet/guardarResguardoEquipoCel", {
                    data: inputs,
                    chks: chk,
                    tipo_resg: $("#tipoResg").val()
                },
                function (data) {
                    $("#tbodyHistorialResguardos").html("");
                    let valueToAppend = "";
                    if ($.isEmptyObject(data.registro) ) {
                        alert("No se encontraron resultados");
                    }
                    else{
                        
                        alert("Resguardo guardado con exito");

                        $("form select").each(function() { this.selectedIndex = 0 });
                        $("#resgcel input[type=checkbox]").prop( "checked", false );
                        $("#resgcel input[type=text] , #resgcel textarea").each(function() { this.value = '' });
                        window.open(data.queryPDF)

                        valueToAppend += createTemplateHistorialResgDone(data.registro);

                        $(".closingModalResg").click()
                        $("#tbodyHistorialResguardos").append(valueToAppend)
                    }  
                    $(".cargaSeccion").hide();                  
                },
                "json"
            );
        } else {
            alert('invalido! te falta completar campos')
        }
    }
});

$("#updateResguardo").click(function (e) {
    if ($('input[type=checkbox]:checked').length === 0) {

        alert('Debe seleccionar al menos un valor');

    }else{

        var campos, valido
        var i = 0, j = 0
        var chk = []
        var inputs = []

        e.preventDefault();

        campos = document.querySelectorAll('#resgcel .campo')

        valido = true; // es valido hasta demostrar lo contrario

        // recorremos todos los campos
        [].slice.call(campos).forEach(function(campo) {
            inputs[i] = campo.value.trim()
            // el campo esta vacio?
            if (campo.value.trim() === '') {
                valido = false
            }else{
                i = i + 1
            }
        });

        $("input:checkbox:checked").each(function() {
            if($('input[type=checkbox]:checked').length != 0){
                chk[j] = $(this).attr("value")
                j = j + 1
            }
        });

        if (valido) {
            $(".cargaSeccion").show();
            $.post("/intranet/actualizarResguardo", {
                    data: inputs,
                    chks: chk,
                    tipo_resg: $("#tipoResg").val()
                },
                function (data) {
                    window.open(data)
                    $(".closingModalResg").click()
                    /*if( data == "OK"){
                        alert("RESGUARDO EDITADO CORRECTAMENTE")
                        $(".closingModalResg").click()
                        $("#buscarResguardos").click()
                    }
                    else{
                        alert(data.eror)
                    }*/
                    $(".cargaSeccion").hide();
                },
                "text"
            );
        } else {
            alert('invalido! te falta completar campos')
        }
    }
});

$("#sendResguardoEquipoComputo").click(function (e) {
 
    var campos
    var i = 0
    var inputs = []

    e.preventDefault();

    campos = document.querySelectorAll('#resgequipcomp .campo_equipComp')

    valido = true; // es valido hasta demostrar lo contrario

    // recorremos todos los campos
    [].slice.call(campos).forEach(function(campo) {
        inputs[i] = campo.value.trim()
        // el campo esta vacio?
        if (campo.value.trim() === '') {
            valido = false
        }else{
            i = i + 1
        }
    })

    if (valido) {
        $.post("/intranet/guardarResguardoEquipoComputo", {
                data: inputs,
                tipo_resg: $("#tipoResgEquipComp").val()
            },
            function (data) {
                
                $("#tbodyHistorialResguardos").html("")
                let valueToAppend = ""
                if ($.isEmptyObject(data.registro) ) {
                    alert("No se encontraron resultados")
                }
                else{
                    
                    alert("Resguardo guardado con exito")

                    $("form select").each(function() { this.selectedIndex = 0 });
                    $("#resgcel input[type=checkbox]").prop( "checked", false );
                    $("#resgcel input[type=text] , #resgcel textarea").each(function() { this.value = '' });
                    window.open(data.queryPDF)

                    valueToAppend += createTemplateHistorialResgDone(data.registro);

                    $(".closingModalResg").click()
                    $("#tbodyHistorialResguardos").append(valueToAppend)
                }                    
            },
            "json"
        );
    } else {
        alert('Invalido! te falta completar campos')
    }
});

$("#updateResguardoEquipoComputo").click(function (e) {
 
    var campos
    var i = 0
    var inputs = []

    e.preventDefault();

    campos = document.querySelectorAll('#resgequipcomp .campo_equipComp')

    valido = true; // es valido hasta demostrar lo contrario

    // recorremos todos los campos
    [].slice.call(campos).forEach(function(campo) {
        inputs[i] = campo.value.trim()
        // el campo esta vacio?
        if (campo.value.trim() === '') {
            valido = false
        }else{
            i = i + 1
        }
    })

    if (valido) {
        $.post("/intranet/actualizarResguardoEquipoComputo", {
                data: inputs
            },
            function (data) {
                
                window.open(data)
            },
            "json"
        );
    } else {
        alert('Invalido! te falta completar campos')
    }
});

function mostrar(id) {
    if (id == "RESGUARDO_CELULARES") {
        $("#RESGUARDO_CELULARES").show();
        $("#RESGUARDO_EQUIPO_COMPUTO").hide();
        $("#autonomo").hide();
        $("#paro").hide();
    }

    if (id == "RESGUARDO_EQUIPO_COMPUTO") {
        $("#RESGUARDO_CELULARES").hide();
        $("#RESGUARDO_EQUIPO_COMPUTO").show();
        $("#autonomo").hide();
        $("#paro").hide();
    }

    if (id == "RESGUARDO_EQUIPO_HERRAMIENTA") {
        $("#estudiante").hide();
        $("#trabajador").hide();
        $("#autonomo").show();
        $("#paro").hide();
    }

    if (id == "RESGUARDO_UNIFORMES") {
        $("#estudiante").hide();
        $("#trabajador").hide();
        $("#autonomo").hide();
        $("#paro").show();
    }

    if (id == "RESGUARDO_MOTOCICLETAS") {
        $("#estudiante").hide();
        $("#trabajador").hide();
        $("#autonomo").hide();
        $("#paro").show();
    }

    if (id == "RESGUARDO_UNIDADES_PESADAS") {
        $("#estudiante").hide();
        $("#trabajador").hide();
        $("#autonomo").hide();
        $("#paro").show();
    }
}