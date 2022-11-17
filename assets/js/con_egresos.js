let arrayEmisores = []
let arrayDescripciones =[]
let arrayFechaFacturado = []
let bancos =[]
let listaSucursales
let pagination = 0;
let paginationGO = 0;

let vmAutomatico = new Vue({
	el: "#configAutomatico",
	data: {
		visible: false
	},
	methods: {
		toggleParametrosAutoMatico: function () {

			this.visible = !this.visible
			
		}
	}
})

nuevo();

$('#contentRangeOperativo input').each(function () {
	$(this).datepicker({
		format: "dd/mm/yyyy"
	});
});


$("#filtroFechaInicioBancos").datepicker({
    format: "dd/mm/yyyy"
});
$("#filtroFechaFinBancos").datepicker({
		format: "dd/mm/yyyy"
	});	


function cmbCatalogo(div,tabla,id,descripcion){
	$.post("controladores/general.php?opc=cmbCatalogo", "tabla="+tabla+"&id="+id+"&descripcion="+descripcion, function(resp){
	
		$("#"+div).html(resp);
	});
}
//funcion que se ejecuta al cargar el scrpt
const filSuc = ()=>{
$.post("controladores/general.php?opc=sucursales", {type:"select"},
	function (data, textStatus, jqXHR) {
		$.each(data, function (i, index) { 
			 $(".filtroSucursal").append(`<option value="${data[i].id}">${data[i].descripcion}</option>`);
			listaSucursales += `<option value="${data[i].id}">${data[i].descripcion}</option>`;
			 
		});
		$("#selSucSaldosForm").append(`<optgroup label="Individual">${listaSucursales}</optgroup>`);
	},
	"json"
);
}

filSuc()

function nuevo(){
	cmbCatalogo('fudn','csucursal','id','descripcion');
	cmbCatalogo('fiudn','csucursal','id','descripcion');
	cmbCuenta();
	$("#fdescripcion").val('');
	$("#frfc").val('');
	$("#fserie").val('');
	$("#ffolio").val('');
	$("#fuuid").val('');
	$("#fbanco").val('');
	$("#fcuenta").val('');
	$("#fctacontable").val('');	
	$("#fsubtotal").val('');
	$("#fiva").val('');
	$("#ftotal").val('');
	$("#fudn").val('');
	$("#fidescripcion").val('');
	$("#fictacontable").val('');
	$("#fibanco").val('');
	$("#ficuenta").val('');
	$("#fisubtotal").val('');
	$("#fiiva").val('');
	$("#fitotal").val('');
	$("#fproveedor").val("")
	$("#chkProrratear").prop('checked', false)
	if (vmAutomatico.visible) {
		vmAutomatico.visible = false
	} 
	$("#sucursalesChk").html("")
	$("#periodicidad").val("")
	$("#caducidad").val("")
	$("#ffecha").val("")
	$("#observacionesGO").val("")
	// $("#btnsave").removeAttr('onclick');
	$("#btnsave").attr('onclick', `guardaGastoOp()`);
	lista('Facturado');
	lista('Financiero');
}

function cmbCuenta(){	
	var params = "";
	$.post("controladores/con_egresos.php?opc=cmbCuenta", params, function(resp){
		//alert(resp);
		var row = eval('('+resp+')');			
		var echo = "";
		var str = "";
		for(i in row){
			echo+= '<option value="'+row[i].id+'">'+row[i].nombre+'</option>';
		}
		$("#fcuentacontable").html(echo);
		$("#selCtaContableFiltro").html(`<option value=''>Selecciona una cuenta</option>${echo}`);
		echo = "";
		for(j in row){
			str = row[j].nombre;
			str = str.substring(0, 7);
			if(str=='CREDITO')
				echo+= '<option value="'+row[j].id+'">'+row[j].nombre+'</option>';
		}
		$("#ficuentacontable").html(echo);
		
	});	
}

function selcuenta(){
	var fdescripcion = $("#ficuentacontable option:selected").text();
	$("#fidescripcion").val(fdescripcion);
}

function lista(opc){
	var params = "opcion="+opc;
	$.post("controladores/con_egresos.php?opc=lista", {opcion: opc, pagina: paginationGO}, function(resp){
		//alert(resp);
		var row = eval('('+resp+')');			
		var echo = "";
		for(i in row){
			if(opc == 'Facturado'){
				echo += createTemplateListaOperacion(row[i])

			emisor = (row[i].emisor).trim()
			desc = (row[i].descripcion).trim()
			if ( arrayEmisores.indexOf(emisor) == -1) {
				
				arrayEmisores.push(emisor)
			}
			if ( arrayDescripciones.indexOf(desc) == -1 ) {
				arrayDescripciones.push(desc)
			}
			}
			if(opc == 'Financiero'){
				echo += createTemplateListaFinanciero(row[i])
			}
		}
		if ( opc =="Facturado") {
			setSelectFinder(arrayEmisores, "emisorFacturado")
			setSelectFinder(arrayDescripciones, "descFacturado")
		}
		
		$("#tbody"+opc).html(echo);
	});	
}

function setSelectFinder(arrayElement, element) {
 $(function () {
 	$("#"+element).autocomplete({
		 maxResults:10,
 		source: function (request, response) {
 			var results = $.ui.autocomplete.filter(arrayElement, request.term);
 			response(results.slice(0, this.options.maxResults));
 		}
 	});
 });
}


function createTemplateListaOperacion(element) {
	
	valueToAppend = `<tr>
									<td> ${element.emisor}</td>
									<td style="cursor:pointer" onclick="reimpresionVale(${element.id})">${element.descripcion}</td>`
	if (element.nivel == "1") {
		valueToAppend += `<td style="cursor:pointer" onclick="fillEditaMovimiento(${element.id})">${element.sucursal}</td>`;
	}else{
		valueToAppend += `<td>${element.sucursal}</td>`;
	}
	valueToAppend +=	`			<td>${element.cuenta == undefined ? element.cuentaNombre : element.cuenta}</td>
									<td>${element.fecha.replace(/-/g,"/")}</td>
									<td align="right">${ formatoMoneda(element.total) }</td>`
	if (element.nivel == "1") {
		valueToAppend += `<td><button class="btn btn-danger" onclick="eliminaMov('${element.id}')">Eliminar</button></td>`
	} else {
		valueToAppend += `<td>&nbsp;</td>`
	}
	valueToAppend += `</tr>`

	return valueToAppend;
}

function createTemplateListaFinanciero(element) {
	valueToAppend = `<tr>
									<td> ${element.descripcion}</td>
									<td>${element.fecha}</td>
									<td>${element.banco}</td>
									<td>${element.nocuenta}</td>
									<td align="right">${element.total}</td>`
	if (element.nivel == "1") {
		valueToAppend += `<td><button class="btn btn-danger" onclick="eliminaMov('${element.id}')">Eliminar</button></td>`
	} else {
		valueToAppend += `<td>&nbsp;</td>`
	}
	valueToAppend += `</tr>`

	return valueToAppend;
}

function fillEditaMovimiento(movimientoId) {
	$.get("controladores/con_egresos.php", {
				opc: 'geMovimientoEditar',
				movimiento: movimientoId
			},
		function (data, textStatus, jqXHR) {
			let movimiento = data[0];
			$("#fdescripcion").val(movimiento.descripcion);
			$("#frfc").val(movimiento.rfc);
			$("#fserie").val(movimiento.docserie);
			$("#ffolio").val(movimiento.docfolio);
			$("#fuuid").val(movimiento.docuuid);
			$("#fbanco").val(movimiento.idcbanco);
			$("#fcuenta").val(movimiento.cuenta);
			$("#fcuentacontable").val(movimiento.idcon_cuentas);
			$("#fsubtotal").val(movimiento.subtotal);
			$("#fiva").val(movimiento.iva);
			$("#ftotal").val(movimiento.total);
			$("#fudn").val(movimiento.idcudn);
			$("#fproveedor").val(movimiento.emisor)
			$("#ffecha").val(movimiento.docfecha)
			$("#ftipoMovimiento").val( parseInt(movimiento.tipo_movimiento) );
			$("#tipoCuentaFormOp").val(movimiento.tipocuenta);
			$("#tipoMovFormOp").val(movimiento.tipomovimiento);
			$("#selTipoEgreso").val(movimiento.tipopago);
			$("#observacionesGO").val( movimiento.observaciones);
			
			//Cargando el tipo de operación seleccionado al movimiento
			setTipoMovimientoForm($('#tipoMovFormOp').val(), 'tipoOperacionFormOp', $("#tipoCuentaFormOp").val());
			//como la función anterior es asincrona se cargará el valor del tipo de operacion cuando se cargue el combo via ajax
			setTimeout(() => {
				$("#tipoOperacionFormOp").val(movimiento.tCuenta);
				//removiendo la propiedad onclick del boton save y cambiarlo por otro onclick
				$("#btnsave").removeAttr('onclick');
				$("#btnsave").attr('onclick',`editaMovimientoGO('${movimientoId}')`);
			}, 200);
			// alert(`tipo operacion = ${movimiento.tCuenta} y tipo mov = ${movimiento.tipo_movimiento}`);
		},
		"json"
	);
}

function editaMovimientoGO(movimiento) {
	let descripcion = $("#fdescripcion").val();
	let rfc = $("#frfc").val();
	let serie = $("#fserie").val();
	let folio =$("#ffolio").val();
	let uuid = $("#fuuid").val();
	let banco = $("#fbanco").val();
	let cuenta = $("#fcuenta").val();
	let cuentaContable = $("#fcuentacontable").val();
	let subtotal = $("#fsubtotal").val();
	let iva =$("#fiva").val();
	let total = $("#ftotal").val();
	let sucursal = $("#fudn").val();
	let proveedor =$("#fproveedor").val();
	let fecha = $("#ffecha").val();
	let tipoMov = $("#ftipoMovimiento").val();
	let tipoOperacion = $("#tipoOperacionFormOp").val();
	let tipoEgreso = $("#selTipoEgreso").val();
	let observaciones = $("#observacionesGO").val();

	dataUpdate = {
		descripcion: descripcion,
		rfc:rfc,
		serie:serie,
		folio:folio,
		uuid:uuid,
		banco:banco,
		cuenta:cuenta,
		cuentaContable:cuentaContable,
		subtotal:subtotal,
		iva:iva,
		total:total,
		sucursal:sucursal,
		proveedor: proveedor,
		fecha: fecha,
		tipoMov: tipoMov,
		tipoOperacion: tipoOperacion,
		movimiento:movimiento,
		tipoEgreso: tipoEgreso,
		observaciones: observaciones,
		opc: 'editaMovtoGO'
	},
		$.post("controladores/con_egresos.php", dataUpdate,
		function (data, textStatus, jqXHR) {
			
			if (data > 0) {
				$("#btnsave").removeAttr('onclick');
				$("#btnsave").attr('onclick', `guardaGastoOp()`);
				alert("Movimiento actualizado correctamente");
				nuevo();
			} else {
				alert("El movimiento no pudo ser actualizado");
			}
		},
		"json"
	);
}

$("#btn-filtro-operacion").click(function (e) {
	e.preventDefault();
	$.post("controladores/con_egresos.php?opc=filtroOperacion", {
				emisor: $("#emisorFacturado").val(),
				descripcion: $("#descFacturado").val(),
				udn: $(".filtroSucursal").val(),
				fechaInicio: $("#filtroFechaInicio").val(),
				fechaFin: $("#filtroFechaFin").val(),
				cuenta: $("#selCtaContableFiltro").val(),
				tipoEgreso: $("#filterTipoEgreso").val(),
				reporte: true,
				paginacion: paginationGO
			},
		function (data, textStatus, jqXHR) {
			let valueToAppend = ""
			$.each(data, function (i, valueOfElement) { 
				valueToAppend += createTemplateListaOperacion(data[i])
			});
			$("#tbodyFacturado").html("")
			$("#tbodyFacturado").append(valueToAppend)
		},
		"json"
	);
	
});

$("#btn-filtro-financiero").click(function () {
	$.post("controladores/con_egresos.php?opc=filtroFinanciero", {
		udn: $(".filtroSucursal").val(),
		descripcion: $("#descripcionFinanciero").val(),
		fecha: $("#FinancieroFecha").val()
	},
		function (data, textStatus, jqXHR) {
			if( Array.isArray(data)){
				if ($.isEmptyObject(data) ) {
					alert("No se encontraron resultados")
				}
				else{
					let valueToAppend = ""
					$.each(data, function (i, item) {
						valueToAppend += data[i];
					});
				}
				$("#tbodyFinanciero").html("")
				$("#tbodyFinanciero").append(valueToAppend)
			}
			else{
				alert(data.error)
			}
		},
		"json"
	);
  })
function eliminaMov(id){
	if(confirm("El registro sera eliminado del sistema. ¿Desea continuar?")){
		$.post("controladores/con_egresos.php?opc=eliminaMov", "id="+id, function(resp){
			nuevo();
			if(resp==0)
				alert('Ocurrio un error, porfavor contacte a Sistemas');
			else
				alert('El registro se elimino correctamente');
			
		});
	}
}

  $("#fsubtotal").focusout(function (e) { 
	calculaTotal( $(this).val(),'t')
  });

  $("#fiva").focusout(function (e) {
	calculaTotal($(this).val(),'i')
	  
  });

  function calculaTotal(valor,tipo) {
		if (isNaN(valor) ) {
		alert("El subtotal debe ser númerico")
		} else {
			let iva = 0;
			let total = 0
			const subtotal = $("#fsubtotal").val();
		if (valor.length == 0 || valor == '0') {
			$("#fiva").val(0)
			total = subtotal
		} else {
			if ( tipo == 't') {
				iva = (subtotal * 0.16).toFixed(2)
				total = parseFloat(subtotal * 1.16).toFixed(2)
			}else{
				iva = valor;
				total = parseFloat(iva*1 + subtotal*1).toFixed(2)
			}

		}
		$("#fiva").val(iva)
		$("#ftotal").val(total)
		}
  }
// function guardaGastoOp(){
// 	//alert("Entra");
// 	var id = $("#idOp").val();
// 	var udn = $("#fudn").val();
// 	var proveedor = $("#fproveedor").val();
// 	var descripcion = $("#fdescripcion").val();
// 	var docfecha = $("#ffecha").val();
// 	var rfc = $("#frfc").val();
// 	var serie = $("#fserie").val();
// 	var folio = $("#ffolio").val();
// 	var uuid = $("#fuuid").val();
// 	var banco = $("#fbanco").val();
// 	var cuenta = $("#fcuenta").val();
// 	var cuentacontable = $("#fcuentacontable").val();
// 	var subtotal = $("#fsubtotal").val();
// 	var iva = $("#fiva").val();
// 	var total = $("#ftotal").val();
// 	descripcion = descripcion.trim();
// 	proveedor = proveedor.trim();
// 	total = total.trim();
// 	docfecha = docfecha.trim();
// 	if(descripcion.length>0 && docfecha.length>0 && proveedor.length>0 && total.length>0){
// 		//alert("Entra2");
// 		var params = "id="+id;
// 		params+= "&idcudn="+udn;
// 		params+= "&emisor="+proveedor;
// 		params+= "&descripcion="+descripcion;
// 		params+= "&docfecha="+docfecha;
// 		params+= "&rfc="+rfc;
// 		params+= "&docserie="+serie;
// 		params+= "&docfolio="+folio;
// 		params+= "&docuuid="+uuid;
// 		params+= "&idcbanco="+banco;
// 		params+= "&cuenta="+cuenta;
// 		params+= "&idcon_cuentas="+cuentacontable;
// 		params+= "&subtotal="+subtotal;
// 		params+= "&iva="+iva;
// 		params+= "&total="+total;
// 		//alert(params);
// 		$.post("controladores/con_egresos.php?opc=guardaOp", params, function(resp){
// 			//alert(resp);
// 			if(resp>0){
// 				nuevo();
// 				alert('El registro se guardo correctamente');
				
// 			}else{
// 				alert('Ocurrio un error, verifique su información e intente de nuevo. Si el error persiste contacte a Soporte Tecnico.');
// 			}
			
// 		});
// 	}else{
// 		//alert("Entra3	");
// 		alert('Los campos [descripcion, fecha, proveedor y total] no deben ir vacios');
// 	}
// }

function guardaGastoFi(){
	//alert("Entra");
	var id = $("#idFi").val();
	var udn = $("#fiudn").val();
	var descripcion = $("#fidescripcion").val();
	var docfecha = $("#fifecha").val();
	var banco = $("#fibanco").val();
	var cuenta = $("#ficuenta").val();
	var cuentacontable = $("#ficuentacontable").val();
	var subtotal = $("#fisubtotal").val();
	var iva = $("#fiiva").val();
	var total = $("#fitotal").val();
	
	descripcion = descripcion.trim();
	total = total.trim();
	docfecha = docfecha.trim();
	if(descripcion.length>0 && docfecha.length>0 && total.length>0){
		//alert("Entra2");
		var params = "id="+id;
		params+= "&idcudn="+udn;
		params+= "&descripcion="+descripcion;
		params+= "&docfecha="+docfecha;
		params+= "&idcbanco="+banco;
		params+= "&cuenta="+cuenta;
		params+= "&idcon_cuentas="+cuentacontable;
		params+= "&subtotal="+subtotal;
		params+= "&iva="+iva;
		params+= "&total="+total;
		
		//alert(params);
		$.post("controladores/con_egresos.php?opc=guardaFi", params, function(resp){
			//alert(resp);
			if(resp>0){
				nuevo();
				//alert('El registro se guardo correctamente');
			}else{
				alert('Ocurrio un error, verifique su información e intente de nuevo. Si el error persiste contacte a Soporte Tecnico.');
			}
		});
	}else{
		//alert("Entra3	");
		alert('Los campos [descripcion, fecha y total] no deben ir vacios');
	}
}

$("#fproveedor").focusout(function (e) { 
	setCtaProveedor($(this).val())
});

function setCtaProveedor(nombre) {
		$.get("controladores/con_egresos.php?opc=cuentaProveedor", {
				proveedor: nombre
			},
			function (data, textStatus, jqXHR) {
				if (!$.isEmptyObject(data)) {
					$.each(data, function (i, item) {
						$(`#fcuentacontable option[value='${data[i].id}']`).attr("selected",true);
					});
				}
				else{
					cmbCuenta()
				}
			},
			"json"
		);
}

$("#buscarXml").click(function (e) { 
	e.preventDefault();
	const uuid = $("#buscaUuid").val()
	const fecha = $("#buscaFecha").val()
	const folio = $("#buscaFolio").val()
	// $("#contet-xml").removeAttr("style");
	$.post("controladores/buscadorXml.php", {uuid:uuid,fecha:fecha,folio:folio},
		function (data, textStatus, jqXHR) {
			$(".row-Uuid").remove();
			$("#contet-xml").attr("style", "height:410px");
			$.each(data, function (i, item) { 
					let resultaddos = data[i]
					if ( !$.isEmptyObject(resultaddos) ) {
						$("#xml-facturas tbody").append(`
							<tr data-dismiss="modal" class="row-Uuid" onclick="setGastosXml('${resultaddos.uuid}')"> 
								<td>${resultaddos.uuid}</td>
								<td>${resultaddos.concepto[0]}</td>
								<td>${resultaddos.fecha}</td>
								<td>${resultaddos.subtotal[0]}</td>
								<td>${resultaddos.total[0]}
							</tr>
						`);
					} else {
						
					}
			});
		},
		"json"
	);
});

 function setGastosXml (gastoUuid) { 
     $.post("controladores/buscadorXml.php", {uuid:gastoUuid,fecha:"",folio:""},
		 function (data, textStatus, jqXHR) {
			 	$("#fdescripcion").val(data[0].concepto[0]);
			 	$("#frfc").val(data[0].rfcEmisor[0]);
			 	$("#ffolio").val(data[0].folio[0]);
			 	$("#fuuid").val(gastoUuid);
			 	$("#fsubtotal").val(data[0].subtotal[0]);
			 	$("#fiva").val( ( data[0].total[0] - data[0].subtotal[0] ).toFixed(2) );
				 $("#ftotal").val(data[0].total[0]);
				 $("#fproveedor").val(data[0].emisor[0]);
				 let fechaSplit = data[0].fecha.split("-")
				 $("#ffecha").val(`${fechaSplit[2]}/${fechaSplit[1]}/${fechaSplit[0]}`);
				 setCtaProveedor( $("#fproveedor").val())
		 },
		 "json"
	 );
 }
   
 //Variable bandera cuando cambien la seleccion del select udn
 let  udnSeleccionada = -1;
 $("#fudn").change(function (e) { 
	 udnSeleccionada = $(this).val()
	 $("#sucursalesChk").html("")
	 $("#chkProrratear").prop('checked', false)
 });


   $("#chkProrratear").change(function (e) {
	//    if ( udnSeleccionada == -1) {
	// 	   udnSeleccionada = 1
	//    }
	   if ($(this).is(":checked") ) {
		   let checksSucursales = ""
		   udnSeleccionada = $("#fudn").val();
			$.post("controladores/general.php?opc=sucursales", {},
			function (data, textStatus, jqXHR) {
				$.each(data, function (i, item) {
					if(udnSeleccionada != data[i].id){
						checksSucursales = `<div class="checkbox checkbox-warning">
						<input type="checkbox" name="chkSucursales[]" value="${data[i].id}"><label>${data[i].descripcion}</label>
						 </div>`
						$("#sucursalesChk").append(checksSucursales)
					}else{
						
					}
				});
			},
			"json"
			);
	   }else{
		   $("#sucursalesChk").html("")
	   }
	   
   });

function guardaGastoOp() {
	let sucursalesSelected = []
	let bandAuto = 0
	let udn = $("#fudn").val();
	let proveedor = $("#fproveedor").val();
	let descripcion = $("#fdescripcion").val();
	let docfecha = $("#ffecha").val();
	let rfc = $("#frfc").val();
	let serie = $("#fserie").val();
	let folio = $("#ffolio").val();
	let uuid = $("#fuuid").val();
	let banco = $("#fbanco").val();
	let cuenta = $("#fcuenta").val();
	let cuentacontable = $("#fcuentacontable").val();
	let subtotal = $("#fsubtotal").val();
	let iva = $("#fiva").val();
	let total = $("#ftotal").val();
	let tipoMovimiento = $("#ftipoMovimiento").val();
	let tipoCuenta = $("#tipoOperacionFormOp").val();
	let periodo = "";
	let caducidad ="";
	let tipoEgreso = $("#selTipoEgreso").val();
	let observaciones = $("#observacionesGO").val();

	descripcion = descripcion.trim();
	proveedor = proveedor.trim();
	total = total.trim();
	if ($("#chkProrratear").is(":checked")) {
		$selectUdn = $("#fudn").val()
		
		 $("input[name$='chkSucursales[]']").each(function (param) {
				if ($(this).is(":checked")) {
					sucursalesSelected.push($(this).val() )
				}
		  })
	}
	if ($("#chkAutomatico").is(":checked")) {
		bandAuto = 1;
		periodo = $("#periodicidad").val()
		caducidad = $("#caducidad").val()
	}
	else{}
	//preparando los datos
	data ={
		proveedor:proveedor,
		descripcion:descripcion,
		fecha:docfecha,
		rfc:rfc,
		serie,serie,
		folio,folio,
		uuid:uuid,
		banco:banco,
		cuenta:cuenta,
		contable:cuentacontable,
		subtotal:subtotal,
		iva:iva,
		total:total,
		sucursales:sucursalesSelected,
		auto:bandAuto,
		sucursal:udn,
		periodo:periodo,
		caducidad:caducidad,
		tipoMovimiento: tipoMovimiento,
		tipoCuenta: tipoCuenta,
		recibo: false,
		tipoPago:tipoEgreso,
		observaciones: observaciones
	}
	
	data.recibo = confirm( "¿Generar recibo?")
	$.post("controladores/con_egresos.php?opc=registraGO", data,
			 function (resp) {
				 if (resp.cant > 0) {
					alert("El registro se ha guardado correctamente")
					if (data.recibo) {
						// window.open("http://servermatrixxxb.ddns.net:8181/intranet/controladores/Reportes/egresos/RecibosDeDinero.php?folio="+resp.recibo,'_blank');
						
						
						window.open(resp.recibo, '_blank');
					}
					 nuevo()
				 }
				 else if( resp.cant != -1){
					 alert("No se pudo agregar el registro")
				 }else{
					 alert("La sesión a caducado")
					 location.reload();
				 }
			 },
			"json")
}



guardarSaldos=  function () {
			$.post("controladores/saldos_bancarios.php", {
				fecha: $("#saldosFecha").val(),
				beneficiario: $("#beneficiario").val(),
				referencia: $("#referenciaSaldos").val(),
				egresos: $("#egresosSaldos").val(),
				ingresos: $("#ingresosSaldos").val(),
				cuentaId: $("#selectCuenta").val(),
				sucursal: $("#selSucSaldosForm").val(),
				movimientoId: "NULL",
				tipoMov: $("#tipoOperacionForm").val()
				},
					function (data, textStatus, jqXHR) {
						if ( data > 0) {

							alert(" Se registró correctamente");
							reiniciar();
							pagination = 0;
							cargarSaldos();
						} else {
							alert("No se pudo registrar la información")
						}
					},
					"text"
				);
		  }

		  reiniciar = function () {
			  $("#saldosFecha").val(''),
				 $("#beneficiario").val(''),
				$("#referenciaSaldos").val(''),
				$("#egresosSaldos").val(''),
				$("#ingresosSaldos").val('')
			}
		cargaCuentas = function (idElement,  tipo= true) {
			$.get('controladores/saldos_bancarios.php', 
				{ opc: 'getAllCuentas' },
			function (response) { 
				if (tipo) {
						$("#"+idElement).html("")
				}else{
					$("#"+idElement).html(`<option value="-1">Selecciona un Banco/Caja</option>`)
				}
				$.each(response, function (i, item) { 
					if ( item.id != 6 && tipo) {
						$("#"+idElement).append(`<option value=${item.id}>${item.banco}</option>`)
					}else{
						$("#"+idElement).append(`<option value=${item.id}>${item.banco}</option>`)
					}
				});
				bancos = response;
			 },
			"json");
		}		


		$("#pagSig").click( function () {
			pagination += 20;
			cargarSaldos();
		  });
		$("#pagAnt").click(function () {
			pagination -= 20;
			if ( pagination < 0) {
				pagination = 0
			}
			cargarSaldos();
		});

		$(".pagSigGO").click(function () {
			paginationGO += 20;
			// lista("Facturado");
			$("#btn-filtro-operacion").click();
		});
		$(".pagAntGO").click(function () {
			paginationGO -= 20;
			if (paginationGO < 0) {
				paginationGO = 0
			}
			// lista("Facturado");
			$("#btn-filtro-operacion").click();
		});

		function cargarSaldos() {
				let cuenta = $("#filtroSaldoCuenta").val();
				let beneficiario = $("#filtroSaldoBeneficiario").val();
				let fecha = $("#filtroFechaInicioBancos").val();
				let fechaFin = $("#filtroFechaFinBancos").val();
				let referencia = $("#filtroSaldoReferencia").val();
				
			$.get("controladores/saldos_bancarios.php", {opc: "allSaldos",pag : pagination, cuenta:cuenta,referencia:referencia,beneficiario:beneficiario,fecha:fecha,fechaFin:fechaFin},
				function (data, textStatus, jqXHR) {
					$(".tr-saldos").remove()
					let template = ``;
					// data.reverse()
					$.each(data, function (i, item) { 
						
						template += `
							<tr class="tr-saldos">
								<td>${item.fecha}</td>
								<td>${item.banco}</td>
							<td><input type="text" id="setBeneficiarioSaldo${item.id}" class="form-control" value="${item.beneficiario}" disabled></td>`
						
								
						template +=	`<td>${item.referencia}</td>
								<td>${parseFloat(item.egresos).toFixed(2)}</td>
								<td>${parseFloat(item.ingresos).toFixed(2)}</td>
								<td>
									<select class='form-control' id="selSucSaldos-${item.id}" disabled>
									<option value = "0"> NINGUNO </option>
										${listaSucursales}
									<optgroup label="Prorratear">
										<option value="all">TODAS</option>
										<option value="zc">ZONA CENTRO</option>
										<option value="za">ZONA ALTOS</option>
									</optgroup>										
										
									</select>
								</td>
								<td><button class="btn btn-danger" onclick="eliminaSaldo('${item.id}')">Eliminar</button>
								<td><button class="btn btn-warning" id="editBeneficiario${item.id}" onclick="EditarBeneficiario('${item.id}')">Editar</button>
								</tr>`;

					});
					$("#tablaSaldos").append(template);
					$.each(data, function (i, item) { 
							selected = "";
							if (item.sucursal != "NULL") {
								$(`#selSucSaldos-${item.id} option[value=${item.sucursal}]`).attr('selected', 'selected');
							}
					});
				},
				"json"
			);
		}
	
function cargaTipoCuentas(opc,movimientoId) {
	//creando la condición para el filtro de acuerdo a la selección
	data = {opc:opc,movId:movimientoId};

	$.get("controladores/saldos_bancarios.php", data,
		function (data, textStatus, jqXHR) {
				let optionTipoCuenta = '';
				let cantTipoCuenta = data.tipoCuenta.length;
				console.log(cantTipoCuenta)
				
			//for (cantTipoCuenta = cantTipoCuenta - 1; cantTipoCuenta >= 0; cantTipoCuenta = -1) {
				
				
			//		item = data.tipoCuenta[cantTipoCuenta];			
			//		optionTipoCuenta += `<option value='${item.tipocuenta}'>${item.tipocuenta}</option>`
			//	}
				 $.each(data.tipoCuenta, function (i, item) {
				 	optionTipoCuenta += `<option value='${item.tipocuenta}'>${item.tipocuenta}</option>`
				 });
				if ( movimientoId != -1) {
					$("#hideMovimiento").val(movimientoId);
					$("#selectTipoMovimiento").html(`<option value="${data.cargoAbono[0].tipoMovimiento}">${data.cargoAbono[0].tipoMovimiento}</option>`);
					$("#tipoCuentaMovimiento").html(`
						${optionTipoCuenta}
					`);
				}
					//obteniendo el item que está seleccionado en el select
				let tipoCuenta = $("#tipoCuentaMovimiento").val()
				let templateOptionTipo = '';
			let tipoMov = movimientoId != -1 ? data.cargoAbono[0].tipoMovimiento : $("#selectTipoMovimiento").val();
				dataGet = {
					opc: 'getTipoOperacionMov',
					tipoCuenta: tipoCuenta,
					tipoMov: tipoMov
				};
				
				
				$.get("controladores/saldos_bancarios.php", dataGet,
					function (dataTipo, textStatus, jqXHR) {
						$.each(dataTipo, function (i, item) { 
							 templateOptionTipo +=`<option value="${item.id}">${item.operacion}</option>`
						});
						$("#tipoOperacionMovimiento").html(templateOptionTipo);
					},
					"json"
				);
		},
		"json"
	);
}

function getTipoCuentaForm(idElemento) {
	$.get("controladores/saldos_bancarios.php", { opc: 'getTipoCuentas', movId:'' },
		function (data, textStatus, jqXHR) {
			let templateOptionCuenta = '';
			let cant = data.tipoCuenta.length;
			for ( cant = cant -1; cant  >= 0; cant--) {
				item = data.tipoCuenta[cant];
				templateOptionCuenta += `<option value="${item.tipocuenta}">${item.tipocuenta}</option>`		
			}

			$(`#${idElemento}`).html( templateOptionCuenta);
			if (idElemento == 'tipoCuentaFormOp') {
				setTipoMovimientoForm($('#tipoMovFormOp').val(), 'tipoOperacionFormOp', $("#tipoCuentaFormOp").val());
			}
		},
		"json"
	);
}

$("#ingresosSaldos").focusout(function () {
	if ( $(this).val().trim().length > 0 && (parseInt( $(this).val() )) >0  ) {
		$("#tipoMovForm").html(`<option value="Ingresos">Ingresos</option>`)
		setTipoMovimientoForm("Ingresos", 'tipoOperacionForm', $("#tipoCuentaForm").val());
	}
});
$("#egresosSaldos").focusout( function () {
	if ( $(this).val().trim().length > 0 && (parseInt($(this).val() ) ) > 0 ) {
		$("#tipoMovForm").html(`<option ="Egresos">Egresos</option>`)
		setTipoMovimientoForm('Egresos', 'tipoOperacionForm', $("#tipoCuentaForm").val() );
	}
  })

$("#tipoCuentaForm").change(function (e) { 
	e.preventDefault();
	setTipoMovimientoForm($('#tipoMovForm').val(), 'tipoOperacionForm', $(this).val());
});

function setTipoMovimientoForm(tipoMov, idElemento,tipoCuenta= 'Operativo') {
	templateOptionTipo = '';
	dataGet = {
		opc: 'getTipoOperacionMov',
		tipoCuenta: tipoCuenta,
		tipoMov: tipoMov
	};

	// alert(tipoCuenta);
	
	$.get("controladores/saldos_bancarios.php", dataGet,
		function (dataTipo, textStatus, jqXHR) {
			$.each(dataTipo, function (i, item) {
				if(item.id==3)
					templateOptionTipo += `<option value="${item.id}" selected>${item.operacion}</option>`
				else
					templateOptionTipo += `<option value="${item.id}">${item.operacion}</option>`
			});
			$(`#${idElemento}`).html(templateOptionTipo);
		},
		"json"
	);
}
$("#setTipoCuentaModal").click(function (e) { 
	e.preventDefault();
	
	//obteniendo el tipo de movimiento aplicaco
	let tipoOperacion = $("#tipoOperacionMovimiento").val();
	let movimiento = $("#hideMovimiento").val();
	let beneficiario = $("#setBeneficiarioSaldo" + movimiento).val();
	let sucursal = $("#selSucSaldos-" + movimiento).val();
	$.post("controladores/saldos_bancarios.php", {
		opc: 'setBeneficiario',
		beneficiario: beneficiario, 
		sucursal: sucursal  ,
		movimiento: movimiento,
		tipoOperacion: tipoOperacion
		},
		function (data, textStatus, jqXHR) {
			if ( data > 0) {
				alert("Benficiario actualizado.")
				$(`#editBeneficiario${movimiento}`).addClass('btn-warning');
				$(`#editBeneficiario${movimiento}`).removeClass('btn-primary');
				$('#editBeneficiario' + movimiento).html('Editar');
				$("#setBeneficiarioSaldo" + movimiento).prop('disabled',true);
				$("#selSucSaldos-"+movimiento).prop('disabled',true);
				cargarSaldos();
			}else{
				alert('No se actualizó el beneficiario')
				$("#setBeneficiarioSaldo" + movimiento).prop('disabled', true);
			}
		},
		"text"
	);
});

$("#tipoCuentaMovimiento").change(function (e) {
	e.preventDefault();
	let idCuenta = $("#hideMovimiento").val();
	cargaTipoCuentas("getTipoCuentas", -1);
});

function EditarBeneficiario(id) {
	let funcion = $('#editBeneficiario'+id).html();
	
	if (funcion == 'Editar') {
		$("#setBeneficiarioSaldo"+id).removeAttr('disabled');
		$("#selSucSaldos-"+id).removeAttr('disabled');
		$(`#editBeneficiario${id}`).removeClass('btn-warning');
		$(`#editBeneficiario${id}`).addClass('btn-primary');
		$('#editBeneficiario'+id).html('Guardar');
		
	} else {

		$("#modalMovimiento").modal();
		cargaTipoCuentas("getTipoCuentas", id);

	}
	
}

	function eliminaSaldo(id) {
		$.get("controladores/saldos_bancarios.php", {opc: "delSaldo",id:id},
			function (data, textStatus, jqXHR) {
				if ( data == 1) {
					alert( "Se ha eliminado el registro")
					cargarSaldos();
				} else {
					alert("No se pudo eliminar");
				}
				
			},
			"text"
		);
	}

	function descargaSaldos() {
		window.open("controladores/Reportes/ArrastreBancario.php", "Descarga", "width=200,height=100")
	}

	$("#selectActCuenta").change(function (e) { 
		e.preventDefault();
		let banco = $(this).val()
		$.each(bancos, function (i, item) { 
			 if(item.id == banco){
				 $("#saldoBanco").val(item.saldo);
			 }
			 
		});
	});

$("#guardaSaldo").click(function (e) { 
		e.preventDefault();
	$.post("controladores/saldos_bancarios.php?opc=actManual", { id: $("#selectActCuenta").val(), saldo: $("#plusSaldoBanco").val()},
			function (data, textStatus, jqXHR) {
				if ( data  > 0) {
					alert("Saldo actualizado correctamente");
					cargaCuentas('selectCuenta');
					cargaCuentas('selectActCuenta', false);
					cargarSaldos();
					$("#saldoBanco").val("");
				}else{
					alert("Saldo no pudo ser actualizado");
				}
			},
			"text"
		);
	});

$("#plusSaldoBanco").keyup(function (e) {
	let nvoSaldo = $("#plusSaldoBanco").val();
	let saldoAnt = $("#saldoBanco").val();
	let diferencia = parseFloat(saldoAnt) + parseFloat(nvoSaldo);
	$("#saldoNvoPreview").val( diferencia.toFixed(2));
});

$("#filtarrSaldos").click(function (e) {
	e.preventDefault();
	let cuenta = $("#filtroSaldoCuenta").val();
	let beneficiario = $("#filtroSaldoBeneficiario").val();
	let fecha = $("#filtroFechaInicioBancos").val();
	let fechaFin = $("#filtroFechaFinBancos").val();
	let referencia = $("#filtroSaldoReferencia").val();

	$.get("/intranet/controladores/saldos_bancarios.php", {cuenta: cuenta, beneficiario:beneficiario,referencia:referencia,fecha:fecha,fechaFin:fechaFin,opc:'filtro'},
		function (data, textStatus, jqXHR) {
					$(".tr-saldos").remove()
					let template = ``;
					// data.reverse()
					$.each(data, function (i, item) {
						selected = "";
						template += `
							<tr class="tr-saldos">
								<td>${item.fecha}</td>
								<td>${item.banco}</td>
							<td><input type="text" id="setBeneficiarioSaldo${item.id}" class="form-control" value="${item.beneficiario}" disabled></td>`

						template += `<td>${item.referencia}</td>
								<td>${parseFloat(item.egresos).toFixed(2)}</td>
								<td>${parseFloat(item.ingresos).toFixed(2)}</td>
								<td>
									<select class='form-control' id="selSucSaldos-${item.id}" disabled>
										<option value="0">NINGUNO</option>
										${listaSucursales}
									<optgroup label="Prorratear">
										<option value="all">TODAS</option>
										<option value="zc">ZONA CENTRO</option>
										<option value="za">ZONA ALTOS</option>
									</optgroup>										
										
									</select>
								</td>
								<td><button class="btn btn-danger" onclick="eliminaSaldo('${item.id}')">Eliminar</button>
								<td><button class="btn btn-warning" id="editBeneficiario${item.id}" onclick="EditarBeneficiario('${item.id}')">Editar</button>
								</tr>`;
								if (item.sucursal != "NULL" ) {
									$(`#selSucSaldos-${item.id}`).attr('selected', 'selected');
								}
					});
					$("#tablaSaldos").append(template);
		},
		"json"
	);
});
		
$("#ftipoMovimiento").change(function (e) {
	e.preventDefault();
	if ( parseInt( $(this).val() ) == 1) {
		$("#tipoMovFormOp").html(`<option value="Egresos" selected>Egresos</option>`);
	} else {
		$("#tipoMovFormOp").html(`<option value="Ingresos" selected>Ingresos</option>`);
	}
	setTipoMovimientoForm($('#tipoMovFormOp').val(), 'tipoOperacionFormOp', $("#tipoCuentaFormOp").val());
});

$("#tipoCuentaFormOp").change(function () {
	setTipoMovimientoForm($('#tipoMovFormOp').val(), 'tipoOperacionFormOp', $(this).val());
})
		cargaCuentas('selectCuenta');
		cargaCuentas('selectActCuenta', false);
		cargaCuentas('filtroSaldoCuenta', false);
		
		cargarSaldos();

		// new Vue({
		// 	el:"#ejemplo",
		// 	data:{
		// 		cuentas:[
		// 			{id: 1 , banco:"BANAMEX"},
		// 			{ id: 2, banco: "BANCOMER" },
		// 			{ id: 3, banco: "BANORTE" }
		// 		]
		// 	}
		// })




	$('#fileupload').bind('fileuploadsubmit', function (e, data) {
		// selecting each input with name "doctype" property checked true
		data.formData = {
				hoja: $("#selectCuenta").val(),
				inicio: $("#filaInicio").val(),
				fin: $("#filaFinal").val()
		};
	});
		$('#fileupload').fileupload({
			dataType: 'text',
			done: function (e, data) {
				console.log(data.result)
				if(data.result > 0){
					setTimeout(() => {
						cargarSaldos();
						$("#saldoBanco").val("");
						$("#plusSaldoBanco").val("");
						$("#saldoNvoPreview").val();

						$(".cargaSeccion").hide();
						alert("Movimientos registrados");
						$('#progress .bar').css(
							'width', 0 + '%'
						);
					}, 1500);
					cargarSaldos();
					cargaCuentas('selectActCuenta', false);
					$("#filaInicio").val('');
					$("#filaFinal").val('');
				}else{
					$(".cargaSeccion").hide();
					alert("No se pudo registrar");
				}

			},
			progress: function () {
				$(".cargaSeccion").show();
				
			},
			progressall: function (e, data) {
				var progress = parseInt(data.loaded / data.total * 100, 10);
				$('#progress .bar').css(
					'width',
					progress + '%'
				);
			}
		});

		
	$("#modalNomina").click( function(){
		
		$("#modalGastonomina").modal();
	});
	$("#modalNomina").tooltip();
	
	$("#upNomina").fileupload({
		dataType: 'json',
		formData: {opc: 'calulaNomina'},		
		done: function(e, data){
			if ( data.result > 0) {
				alert('Se ha registrado los gastos de nomina');
				lista('Facturado');
			} else {
				alert('No se pudo registrat los gastos de nomina');
			}
		},

	})

getTipoCuentaForm("tipoCuentaForm");
getTipoCuentaForm("tipoCuentaFormOp");


$("#descargaGastosOperativos").click(function (e) { 
	e.preventDefault();
	$.post("/intranet/controladores/Reportes/Egresos/gastosOperativos.php", {
		emisor: $("#emisorFacturado").val(),
		descripcion: $("#descFacturado").val(),
		udn: $(".filtroSucursal").val(),
		fechaInicio: $("#filtroFechaInicio").val(),
		cuenta: $('#selCtaContableFiltro').val(),
		fechaFin: $("#filtroFechaFin").val(),
		tipoEgreso: $("#filterTipoEgreso").val()
	},
		function (data, textStatus, jqXHR) {
			//console.log(data);
			window.open(data, "_blank");
		},
		"text"
	);
});

$("#descargaMovtosBancos").click(function (e) { 
	let fechaInicio = $("#filtroFechaInicioBancos").val();
	let fechaFin = $("#filtroFechaFinBancos").val();
	e.preventDefault();
	$.get("/intranet/controladores/Reportes/Egresos/movimientos_bancos.php" ,{
		opc: 'reporteMovimientos',
		fechaInicio: fechaInicio,
		fechaFin: fechaFin
	},
		 function (data, textStatus, jqXHR) {
			 window.open(data, "_blank");
		 },
		 "text"
	 );
});

function reimpresionVale( id) {
	$.get("/intranet/controladores/reportes/egresos/RecibosDeDinero.php", {
		folioReimpresion: id
	},
		function (data, textStatus, jqXHR) {
			window.open(data, "_blank")
		},
		"text"
	);
}

// obteniendo los tipos de egresos y llenando el select correspondiente
$.get("/intranet/controladores/con_egresos.php", {
	opc: 'getTipoEgresos'
},
	function (data, textStatus, jqXHR) {
		let template = '';
		$.each(data, function (i, item) { 
			template += `<option value="${item.id}">${item.descripcion}</option>`
		});
		$("#selTipoEgreso").html( template );
		$("#filterTipoEgreso").html(`<option value='%'>Todos</option>${template}`);
	},
	"json"
);

function formatoMoneda(amount, decimalCount = 2, decimal = ".", thousands = ",") {
	try {
		decimalCount = Math.abs(decimalCount);
		decimalCount = isNaN(decimalCount) ? 2 : decimalCount;

		const negativeSign = amount < 0 ? "-" : "";

		let i = parseInt(amount = Math.abs(Number(amount) || 0).toFixed(decimalCount)).toString();
		let j = (i.length > 3) ? i.length % 3 : 0;

		return negativeSign + (j ? i.substr(0, j) + thousands : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands) + (decimalCount ? decimal + Math.abs(amount - i).toFixed(decimalCount).slice(2) : "");
	} catch (e) {
		console.log(e)
	}
};