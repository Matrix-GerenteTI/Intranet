function createTemplateHistorialColaboradoresMensual(element) {
    //console.log(element)
    valueToAppend = `
                     <tr style="cursor:pointer">
					 <td>${element.nombre}</td>
					 <td>${element.baja}</td>
					 </tr>`

	return valueToAppend;
}

$( "#colaboradores_bajas" ).ready(function() {
    var bajasMensual = document.getElementById('bajas_mensual');

    $.get("/intranet/getBajasColaboradores", {
            pagination: cambiaPag
        },
        function (data) {
            bajasMensual.value = data.bajasmensual;
            $("#tbodyHistorialColaboradores").html("")
            let valueToAppend = ""
            if( Array.isArray(data)){
                if ($.isEmptyObject(data) ) {
                    console.log("No se encontraron resultados")
                }
                else{
                    
                    $.each(data, function (i) {
                        valueToAppend += createTemplateHistorialColaboradoresMensual(data[i]);
                    });
                }
                
                $("#tbodyHistorialColaboradores").append(valueToAppend)
            }
            else{
                alert(data.error)
            }

        },
        "json"
    );
});