nuevo();

function cmbCatalogo(div,tabla,id,descripcion,where){
	var params = "";
	if(typeof(where) != "undefined")
		params+= "tabla="+tabla+"&id="+id+"&descripcion="+descripcion+"&where="+where;
	else
		params+= "tabla="+tabla+"&id="+id+"&descripcion="+descripcion;
	$.post("controladores/general.php?opc=cmbCatalogow", params, function(resp){
		//alert(resp);
		$("#"+div).html(resp);
	});
}

function uncheckbox(){
	$("input:checkbox").each(function() {
		$(this).prop('checked', false);
	});
}

function activaProgramacion(){
	var selprog = $("#programacion").val();
	if(selprog == 'diariamente'){
		$("#diario").hide();
		$("#mensual").hide();
		$("#semanal").hide();
		uncheckbox();
	}
	if(selprog == 'semanalmente'){
		$("#diario").hide();
		$("#mensual").hide();
		$("#semanal").show();
		uncheckbox();
	}
	if(selprog == 'quincenalmente'){
		$("#diario").hide();
		$("#mensual").hide();
		$("#semanal").hide();
		uncheckbox();
	}
	if(selprog == 'mensualmente'){
		$("#diario").hide();
		$("#mensual").show();
		$("#semanal").hide();
		uncheckbox();
	}
	if(selprog == 'especificos'){
		$("#diario").show();
		$("#mensual").hide();
		$("#semanal").hide();
	}
}

function nuevo(){
	$("#programacion").val('diariamente');
	activaProgramacion();
	cmbCatalogo('departamento','cdepartamento','id','descripcion');
	$("#puesto").html('');
	$("#btnsave").html('<i class="fa fa-plus"></i>');
	$("#id").val('');
	$("#idempleado").val('');
	$("#descripcion").val('');
	$("#fechaini").val('');
	$("#horaini").val('');
	$("#fechafin").val('');
	$("#horafin").val('');
	$("#empleado").val('');
	uncheckbox();
	listaUsuarios();
	lista();
}

function cmbPuestos(){
	var depto = $("#departamento").val();
	cmbCatalogo('puesto','cpuesto','id','descripcion','iddepartamento='+depto);
}

function lista(){
	var params = "";
	$.post("controladores/per_tareas.php?opc=lista", params, function(resp){
		//alert(resp);
		var row = eval('('+resp+')');			
		var echo = "";
		for(i in row){
			echo+= '<tr onclick="cargaDatos('+row[i].id+')">';
			echo+= '	<td>'+row[i].tarea+'</td>';
			echo+= '	<td>'+row[i].departamento+'</td>';
			echo+= '	<td>'+row[i].puesto+'</td>';
			echo+= '	<td>'+row[i].empleado+'</td>';
			echo+= '</tr>';
		}
		$("#tbody").html(echo);
	});	
}

function getSubordinados() {
	
	$.get("/intranet/controladores/nomina/recursos_humanos.php", { opc: 'asistenciaDePersonal'},
		function (data, textStatus, jqXHR) {
			
		},
		"json"
	);
}

getSubordinados();

function cargaDatos(id){
	uncheckbox();
	var params = "id="+id;
	$.post("controladores/per_tareas.php?opc=cargaDatos", params, function(resp){
		//alert(resp);
		var row = eval('('+resp+')');
		$("#id").val(row.id);
		$("#descripcion").val(row.descripcion);
		$("#fechaini").val(row.fechainicio);
		$("#horaini").val(row.horainicio);
		$("#fechafin").val(row.fechafin);
		$("#horafin").val(row.horafin);
		$("#idempleado").val(row.idempleado);
		$("#empleado").val(row.empleado);
		var depto = row.departamento;
		var puesto = row.puesto;
		if(depto == 0){
			$("#departamento").val('%');
		}else{
			$("#departamento").val(row.departamento);
			var params2 = "tabla=cpuesto&id=id&descripcion=descripcion&where=iddepartamento="+row.departamento;
			$.post("controladores/general.php?opc=cmbCatalogow", params2, function(resp2){
				//alert(resp);
				$("#puesto").html(resp2);
				
				if(puesto == 0){
					$("#puesto").val('%');
				}else{
					$("#puesto").val(row.puesto);
				}
			});
		}
		if(puesto == 0){
			$("#puesto").val('%');
		}
		
		$("#programacion").val(row.programacion);
		activaProgramacion();
		$("#diasemana").val(row.diasemana);
		$("#diames").val(row.diames);
		
		$("input:checkbox").each(function() {
			var id = $(this).attr('name');
			var strname = id.split("_");
			if(strname[0]==id){
				if ($('#'+clase).prop('checked') ) {
					$(".c"+clase).prop('checked', true);
				}else{
					$(".c"+clase).prop('checked', false);
				}
			}
		});
		
		var dias = row.dias;
		if(dias.length>0){
			if(dias.length == 1){
				$("#dia_"+dias).prop('checked', true);
			}else{
				var arrdias = dias.split(",");
				for(i in arrdias){
					$("#dia_"+arrdias[i]).prop('checked', true);
				}
			}
		}
		
		$("#btnsave").html('<i class="fa fa-save"></i>');
	});
}

function guardar(id){
	var id = $("#id").val();
	var descripcion = $("#descripcion").val();
	var fechaini = $("#fechaini").val();
	var fechafin = $("#fechafin").val();
	var horaini = $("#horaini").val();
	var horafin = $("#horafin").val();
	var departamento = $("#departamento").val();
	var puesto = $("#puesto").val();
	var idempleado = $("#idempleado").val();
	var programacion = $("#programacion").val();
	var diasemana = $("#diasemana").val();
	var diames = $("#diames").val();
	
	var dias = "";
	$("input:checkbox").each(function() {
		if($(this).prop('checked') == true){
			dias+= $(this).val()+",";
		}
	});
	var b =	dias.length;
	dias = dias.substr(0,(b-1));
	
	descripcion = descripcion.trim();
	if(descripcion.length>0){
		var params = "id="+id;
		params+= "&descripcion="+descripcion;
		params+= "&fechainicio="+fechaini;
		params+= "&fechafin="+fechafin;
		params+= "&horainicio="+horaini;
		params+= "&horafin="+horafin;
		params+= "&departamento="+departamento;
		params+= "&puesto="+puesto;
		params+= "&idempleado="+idempleado;
		params+= "&programacion="+programacion;
		if(programacion == 'semanalmente')
			params+= "&diasemana="+diasemana;
		else
			params+= "&diasemana=0";
		if(programacion == 'mensualmente')
			params+= "&diames="+diames;
		else
			params+= "&diames=0";			
		params+= "&dias="+dias;
		//alert(params);
		$.post("controladores/per_tareas.php?opc=guarda", params, function(resp){
			alert(resp);
			if(resp>0){
				showSuccess('El registro se guardo correctamente');
				nuevo();
			}else{
				showErrorMessage('Ocurrio un error, verifique su información e intente de nuevo. Si el error persiste contacte a Soporte Tecnico.');
			}
		});
	}else{
		showErrorMessage('Los campos indicados con (*) no deben ir vacios');
	}
}

function listaUsuarios(){
	
	$.post("controladores/per_tareas.php?opc=listaModal", "", function(resp){
		//alert(resp);
		var row = eval('('+resp+')');			
		var echo = "";
		for(i in row){
			echo+= '<tr onclick="selUsuario('+row[i].id+',\''+row[i].nombre+'\')" data-dismiss="modal">';
			echo+= '	<td>'+row[i].nombre+'</td>';
			echo+= '	<td>'+row[i].puesto+'</td>';
			echo+= '</tr>';
		}
		$("#tbodyusuarios").html(echo);
	});
}

function selUsuario(id,nombre){
	$("#empleado").val(nombre);
	$("#idempleado").val(id);
}