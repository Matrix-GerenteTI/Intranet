function createTemplateConsultaParte(element) {
    //console.log(element)
    valueToAppend = `<tr style="cursor:pointer">
                        <td>${element.CODIGOARTICULO}</td>
                        <td>${element.DESCRIPCION}</td>
                        `
    if(!$.isEmptyObject(element.PVP1))                    
        valueToAppend+= `<td>${element.PVP1}</td>`
    if(!$.isEmptyObject(element.PVP2))                    
        valueToAppend+= `<td>${element.PVP2}</td>`
    if(!$.isEmptyObject(element.PVP3))                    
        valueToAppend+= `<td>${element.PVP3}</td>`
    if(!$.isEmptyObject(element.PVP4))                    
        valueToAppend+= `<td>${element.PVP4}</td>`
    if(!$.isEmptyObject(element.PVP5))                    
        valueToAppend+= `<td>${element.PVP5}</td>`
    if(!$.isEmptyObject(element.PVP6))                    
        valueToAppend+= `<td>${element.PVP6}</td>`
    if(!$.isEmptyObject(element.PVP7))                    
        valueToAppend+= `<td>${element.PVP7}</td>`
    if(!$.isEmptyObject(element.PVP8))                    
        valueToAppend+= `<td>${element.PVP8}</td>`
    if(!$.isEmptyObject(element.PVP9))                   
        valueToAppend+= `<td>${element.PVP9}</td>`
    if(!$.isEmptyObject(element.PVP10))                    
        valueToAppend+= `<td>${element.PVP10}</td>`
                        
    valueToAppend += `
                      <td style="text-align: center;">${element.EXISTOTAL}</td>
                      <td style="text-align: center;">${element.ALMACEN}</td>
                      <td style="text-align: center;"><a href="#" onclick="verimagen('${element.CODIGOARTICULO}','${element.DESCRIPCION}')" data-toggle="modal" data-target="#imageModal">Ver Imagen</a></td>
					</tr>`

	return valueToAppend;
}

function verimagen(codigo,descripcion){
    console.log('entra a verimagen');
    $("#imageModalLabel").html(descripcion);
    $("#imagenProd").html('<img src="http://servermatrixxxb.ddns.net:9898/imgsProductos/'+codigo+'.jpg" style="max-width:800px;max-height:600px">');
}

$("#searchProduct").click(function(e){
    e.preventDefault();
	$.get("/intranet/productosColision", {
            codproduct: $("#codproduct").val(),
            descripcion: $("#descripProduct").val()
		},
		function (data) {
            //console.log(data);
			$("#tbodyConsultaParte").html("")
            let valueToAppend = ""
            
            if ($.isEmptyObject(data) ) {
                alert("No se encontraron resultados")
            }
            else{
                $.each(data, function (i) {
                    valueToAppend += createTemplateConsultaParte(data[i]);
                });           
            }
            
            $("#tbodyConsultaParte").append(valueToAppend)
            
		},
		"json"
	);	
});