function createTemplateHistorialExcp(element) {
    valueToAppend = ""
    for(i=0; i<element.length; i = i + 1){
	    valueToAppend += `<tr style="cursor:pointer">
                        <td> ${element[i].descripcion}</td>
                        <td>${element[i].causa}</td>
                        <td>${element[i].fecha.replace(/-/g,"/")}</td>                        
					 </tr>`
    }
	return valueToAppend;
}

function createTemplateHistorialExcp2(element) {
	valueToAppend = `<tr style="cursor:pointer">
                        <td> ${element[i].descripcion}</td>
                        <td>${element[i].causa}</td>
                        <td>${element[i].fecha.replace(/-/g,"/")}</td>                        
                    </tr>`

	return valueToAppend;
}

function addingOptionsUdn(element){
    value = ""
    for(i=0; i<element.length; i = i + 1){
        value += `<option value="${element[i].idprediction}">${element[i].descripcion}</option>`
        
    }
	return value;
}

$( "#showRandomsExceptions" ).ready(function() {
    $.get("/intranet/obtenerExcepcionesAleatorios", {
            pagination: cambiaPag
        },
        function (data) {

            $("#tbodyHistorialExcepciones").html("")
            let valueToAppend = ""
            let options = ""

            if( Array.isArray(data.historialExcepciones)){

                if ($.isEmptyObject(data.historialExcepciones) ) {
                    console.log("No se encontraron resultados")
                }
                else{
                    valueToAppend += createTemplateHistorialExcp(data.historialExcepciones);
                }                
                $("#tbodyHistorialExcepciones").append(valueToAppend)
                
                if ($.isEmptyObject(data.udns) ) {
                    console.log("No se encontraron resultados")
                }
                else{
                    options += addingOptionsUdn(data.udns);
                }
                $("#sucursal").append(options)
                
            }
            else{
                alert(data.error)
            }
        },
        "json"
    );
});

$( "#aplicarExcepcion").click(function(e){
    var razon = $("#razonExcepcion").val()
    var udnSeleccionada = $("#sucursal").val()
    var fecha = $("#fechaRazonExcp").val()
    
    if( razon == '' && udnSeleccionada == '' && fecha == ''){
        
        valueToAppend = `<div class="alert alert-warning alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <strong>ATENCIÓN!</strong> No hay ningun dato por guardar.
        </div>`
        $("#divAlertException").append(valueToAppend)
    }

	$.post("/intranet/guardarExcepcionAleatorio", {
            razon, 
            udnSeleccionada,
            fecha
		},
		function (data) {
			$("#tbodyHistorialExcepciones").html("")
            let valueToAppend = ""
            if( Array.isArray(data)){
                if ($.isEmptyObject(data) ) {
                    console.log("No se encontraron resultados")
                }
                else{
                    var alert = `<div class="alert alert-success alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    Datos almacenados correctamente.
                    </div>`
                    $("#divAlertException").append(alert)

                    console.log(data)
                    
                    valueToAppend += createTemplateHistorialExcp(data);
                }                
                $("#tbodyHistorialExcepciones").append(valueToAppend)
                
            }
            else{
                alert(data.error)
            }
		},
		"json"
	);
});