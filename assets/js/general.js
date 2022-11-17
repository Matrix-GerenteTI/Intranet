function cmbCatalogo(div,tabla,id,descripcion){
	$.post("controladores/general.php?opc=cmbCatalogo", "tabla="+tabla+"&id="+id+"&descripcion="+descripcion, function(resp){
		//alert(resp);
		$("#"+div).html(resp);
	});
}