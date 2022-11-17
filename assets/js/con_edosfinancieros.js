cmbCatalogo('udn','csucursal','id','descripcion');

function cmbCatalogo(div,tabla,id,descripcion){
	$.post("controladores/general.php?opc=cmbCatalogo", "tabla="+tabla+"&id="+id+"&descripcion="+descripcion, function(resp){
		//alert(resp);
		$("#"+div).html("<option value='%'>Todas...<option>"+resp);
	});
}

function getEdoresultados(){
	$(".cargaSeccion").show();
	var udn = $("#udn").val();
	var fecini = $("#fechainicio").val();
	var fecfin = $("#fechafin").val();
	var params = "fecini="+fecini;
	// alert(fecini);
	//if(fecini=='01/12/2018'){
	//	window.open("/intranet/controladores/Reportes/estadoresultados.xlsx");
	//}else{
		
		params+= "&fecfin="+fecfin;
		params+= "&udn="+udn;
		$.post("controladores/con_edosfinancieros.php?opc=getedoresultados", params, function(resp){
			//alert(resp);
			$("#tbodyedoresultados").html(resp);
			$(".cargaSeccion").hide();
		});
	//}
}

function getDetalles(cuenta){
	$("#tbodymodal").html('');
	//alert("ENTRA");
	var udn = $("#udn").val();
	var fecini = $("#fechainicio").val();
	var fecfin = $("#fechafin").val();
	var params = "fecini="+fecini;
	params+= "&fecfin="+fecfin;
	params+= "&cuenta="+cuenta;
	params+= "&udn="+udn;
	$.post("controladores/con_edosfinancieros.php?opc=getDetallesCuenta", params, function(resp){
		//alert(resp);
		var row = eval('('+resp+')');			
		var echo = "";
		for(i in row){
			echo+= '<tr>';
			echo+= '	<td>'+row[i].udn+'	</td>';
			echo+= '	<td>'+row[i].serie+''+row[i].folio+'</td>';
			echo+= '	<td>'+row[i].emisor+'</td>';
			echo+= '	<td>'+row[i].receptor+'</td>';
			echo+= '	<td><div id="txtconcepto_'+row[i].id+'">'+row[i].concepto+'</div><div style="visibility:hidden" id="inputconcepto_'+row[i].id+'"><input type="text" id="concepto_'+row[i].id+'" value="'+row[i].concepto+'" /></div></td>';
			echo+= '	<td>'+row[i].uuid+'</td>';
			echo+= '	<td>'+row[i].fecha+'</td>';
			echo+= '	<td align="right"><div id="txtimporte_'+row[i].id+'">'+row[i].importe+'</div><div style="visibility:hidden" id="inputimporte_'+row[i].id+'"><input type="text" id="importe_'+row[i].id+'" value="'+row[i].importe+'" /></div></td>';
			$("#txtconcepto_"+row[i].id).show();
			$("#inputconcepto_"+row[i].id).hide();
			$("#txtimporte_"+row[i].id).show();
			$("#inputimporte_"+row[i].id).hide();
			if(row[i].nivel=='ADMINISTRADOR'){
				echo+= '	<td><button class="btn btn-danger" onclick="eliminaMov('+row[i].id+',\''+cuenta+'\')">Eliminar</button>&nbsp;<button class="btn btn-warning" onclick="EditaMov('+row[i].id+')" id="btne_'+row[i].id+'">Editar</button><button class="btn btn-primary" onclick="GuardaMov('+row[i].id+',\''+cuenta+'\')" style="visibility:hidden" id="btng_'+row[i].id+'">Guardar</button></td>';
				$("#btne_"+row[i].id).show();
				$("#btng_"+row[i].id).hide();
				
			}else{
				echo+= '	<td>&nbsp;</td>';
			}
			echo+= '</tr>';
			
		}
		$("#tbodymodal").html(echo);
	});
}

function EditaMov(id){
	$("#txtconcepto_"+id).hide();
	$("#inputconcepto_"+id).css('visibility','visible');
	$("#txtimporte_"+id).hide();
	$("#inputimporte_"+id).css('visibility','visible');
	$("#btne_"+id).hide();
	$("#btng_"+id).css('visibility','visible');
}

function GuardaMov(id,cuenta){
	
	//alert("Entra");
	var concepto = $("#concepto_"+id).val();
	var importe = $("#importe_"+id).val();
	if(concepto.length>0 && importe>0){
		//alert("Entra2");
		var params = "id="+id;
		params+= "&concepto="+concepto;
		params+= "&importe="+importe;
		//alert(params);
		$.post("controladores/con_egresos.php?opc=updateOp", params, function(resp){
			//alert(resp);
			if(resp>0){
				alert('El registro se guardo correctamente');
				$("#txtconcepto_"+id).show();
				$("#inputconcepto_"+id).css('visibility','hidden');
				$("#txtimporte_"+id).show();
				$("#inputimporte_"+id).css('visibility','hidden');
				$("#btne_"+id).show();
				$("#btng_"+id).css('visibility','hidden');		
				getDetalles(cuenta);
			}else{
				alert('Ocurrio un error, verifique su información e intente de nuevo. Si el error persiste contacte a Soporte Tecnico.');
			}
			
		});
	}else{
		//alert("Entra3	");
		alert('Los campos [concepto e importe] no deben ir vacios');
	}
	
	
	
}

function eliminaMov(id,cuenta){
	if(confirm("El registro sera eliminado del sistema. ¿Desea continuar?")){
		$.post("controladores/con_egresos.php?opc=eliminaMov", "id="+id, function(resp){
			getDetalles(cuenta);
			if(resp==0)
				showErrorMessage('Ocurrio un error, porfavor contacte a Sistemas');
			else
				showSuccess('El registro se elimino correctamente');
			
		});
	}
}

function getPE(){
	alert("OK");
}

$("#btn-descargaEdoFinanciero").click(function (e) { 
	const fInicio = $("#fechainicio").val();
	const fFin = $("#fechafin").val();
	$.post("controladores/Reportes/Financiero.php", {fInicio:fInicio,fFin:fFin},
		function (data, textStatus, jqXHR) {
			console.log(data)
			$("#btn-descargar").attr("href",data);
			$("#btn-descargar").text("Listo");
		},
		"text"
	);
})

$("#downResultados").click(function (e) {
	console.log('entra')
	const fInicio = $("#fechainicio").val();
	const fFin = $("#fechafin").val();
	//alert(fInicio);
	if(fInicio=='01/10/2018'){
		window.open("/intranet/controladores/Reportes/estadoresultados.xlsx");
	}else{
		$.get("controladores/Reportes/EstadoResultados.php", { fInicio: fInicio, fFin: fFin },
			function (data, textStatus, jqXHR) {
				console.log(data);
				
				if (fInicio.trim().length > 0 && fFin.trim().length > 0) {
					window.open(`http://servermatrixxxb.ddns.net:8181/intranet/${data}`, "Estado de resultados", "width=200,height=100")
				}else{
					alert("El reporte se ha envíado al email de administración");
				}
			},
			"text"
		);
	}
});



$("#downResultadosGral").click(function (e) {
	const fInicio = $("#fechainicio").val();
	const fFin = $("#fechafin").val();
	//alert(fInicio);
	if(fInicio=='01/10/2018'){
		window.open("/intranet/controladores/Reportes/estadoresultados.xlsx");
	}else{
		$.get("http://servermatrixxxb.ddns.net/intranet/controladores/reportes/contabilidad/estadoresultados.php", { fInicio: fInicio, fFin: fFin },
			function (data, textStatus, jqXHR) {
				console.log(fInicio.trim().length > 0 && fFin.trim().length > 0);
				
				if (fInicio.trim().length > 0 && fFin.trim().length > 0) {
					window.open(`http://servermatrixxxb.ddns.net/intranet/controladores/reportes/contabilidad/${data}`, "Estado de resultados", "width=200,height=100")
				}else{
					alert("El reporte se ha envíado al email de administración");
				}
			},
			"text"
		);
	}
});