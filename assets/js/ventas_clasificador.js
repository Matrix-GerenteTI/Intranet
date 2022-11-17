$(function(){
    inicio();
});

function inicio(){
	cmbCatalogo('cudn','csucursal','id','descripcion','dbnomina',0,0);
	cmbCatalogo('cmarca','autos_marcas','id','marca','matrix_eshop','cmodelo','canio');
	getGaleria();
}

function cmbCatalogo(div,tabla,id,descripcion,db,modelo,anio){
	$.post("controladores/general.php?opc=cmbCatalogo", "tabla="+tabla+"&id="+id+"&descripcion="+descripcion+"&db="+db, function(resp){
		$("#"+div).html('<option value="TODOS">TODOS</option>'+resp);
		if(modelo!=0){
			getModelos();
		}
	});
}

function getModelos(){
	$.post("controladores/general.php?opc=cmbCatalogo", "tabla=modelos_autos&id=modelo&descripcion=modelo&db=matrix_eshop&where=marca_id in ("+$("#cmarca").val()+") GROUP BY modelo", function(resp2){
		$("#cmodelo").html('<option value="TODOS">TODOS</option>'+resp2);
		getAnios();
	});
}

function getAnios(){
	$.post("controladores/general.php?opc=cmbCatalogo", "tabla=modelos_autos&id=id&descripcion=modelo_anio&db=matrix_eshop&where=marca_id in ("+$("#cmarca").val()+") AND modelo IN ('"+$("#cmodelo").val()+"') GROUP BY modelo_anio", function(resp2){
		//alert(resp2);
		$("#canio").html('<option value="TODOS">TODOS</option>'+resp2);
	});
}

$("#cmarca").change(function(){
	getModelos();
});

$("#cmodelo").change(function(){
	getAnios();
});

$("#getgaleria").click(function(){
	getGaleria();
});

$("#subirfoto").click(function(){
	var marca = $("#cmarca option:selected").text();
	var modelo = $("#cmodelo option:selected").text();
	var anio = $("#canio option:selected").text();
	var sucursal = $("#cudn").val();
	if(marca!='TODOS' && modelo!='TODOS' && anio!='TODOS' && sucursal!='TODOS')
		getFoto();
	else
		alert("Para subir una foto debe seleccionar los datos del auto y la sucursal");
});

function getGaleria(){
	//alert("Entra");
	var marca = $("#cmarca option:selected").text();
	var modelo = $("#cmodelo option:selected").text();
	var anio = $("#canio option:selected").text();
	var sucursal = $("#cudn").val();
	//alert("marca="+marca+"&linea="+modelo+"&modelo="+anio+"&sucursal="+sucursal);
	$.post("controladores/ventas_clasificador.php?opc=galeria", "marca="+marca+"&linea="+modelo+"&modelo="+anio+"&sucursal="+sucursal, function(resp){
		//alert(resp);
		$("#galeria").html(resp);
	});
}

function delImg(imagen){
	if(confirm("¿Esta seguro de eliminar la imagen?")){
		$.post("controladores/ventas_clasificador.php?opc=delimagen", "imagen="+imagen, function(resp){
			//alert(resp);
			getGaleria()
		});
	}
}

document.getElementById('upfile').addEventListener("change", function(e) {
	//alert("Entra");
	var marca = $("#cmarca option:selected").text();
	var modelo = $("#cmodelo option:selected").text();
	var anio = $("#canio option:selected").text();
	var sucursal = $("#cudn").val();
	var inputFileImage = document.getElementById("upfile");
	var file = inputFileImage.files[0];
	var data = new FormData();
	data.append('archivo',file);
	data.append('marca',marca);
	data.append('linea',modelo);
	data.append('modelo',anio);
	data.append('sucursal',sucursal);
	var url = "controladores/uploadFoto.php";
	$.ajax({
		url:url,
		type:'POST',
		contentType:false,
		data:data,
		processData:false,
		cache:false}).done(function(resp1){		
			//alert(resp1);
			getGaleria();
		});
});

function getFoto(){
	fileElem = document.getElementById("upfile");
	fileElem.click();
	e.preventDefault(); // prevent navigation to "#"
}
