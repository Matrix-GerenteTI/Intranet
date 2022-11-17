cmbCatalogo('tipo','ctipo','id','descripcion');
cmbCatalogo('puesto','cpuesto','id','descripcion');
cmbCatalogo('nivel','cnivel','id','descripcion');


function cmbCatalogo(div,tabla,id,descripcion){
	$.post("controladores/general.php?opc=cmbCatalogo", "tabla="+tabla+"&id="+id+"&descripcion="+descripcion, function(resp){
		//alert(resp);
		$("#"+div).html(resp);
	});
}