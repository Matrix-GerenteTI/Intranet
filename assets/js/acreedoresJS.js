/* Scripts 'Gastos de operacion' */
var resultados = document.getElementById('tbodyHistorialPagos');
let changePage = 0;

function createTemplateHistorialPagos(element) {
	valueToAppend = `<tr>
					 <td> ${element.beneficiario}</td>
					 <td style="cursor:pointer" onclick="setElemensTo('${element.concepto}','${element.monto}','${element.fecha_evento}')">${element.concepto}</td>
					 <td align="right">${ formatoMoneda(element.monto) }</td>
					 <td>${element.fecha_evento.replace(/-/g,"/")}</td>
					 </tr>`

	return valueToAppend;
}

$(".closingModal").click(function(e){
	
	resultados.innerHTML = '';
})

$(".close").click(function(e){
	
	resultados.innerHTML = '';
})

function setElemensTo(concepto, monto, fecha){
	
	var fdescripcion = document.getElementById('fdescripcion');
	var ffecha = document.getElementById('ffecha');
	var ftotal = document.getElementById('ftotal');
	var fsubtotal = document.getElementById('fsubtotal');
	var fiva = document.getElementById('fiva');

	fdescripcion.value = concepto;
	ffecha.value = fecha.replace(/^(\d{4})-(\d{2})-(\d{2})$/g,'$3/$2/$1');
	ftotal.value = monto;
	fsubtotal.value = monto;
	fiva.value = 0;

	$(".closingModal").click();
}

//Buscar pagos pendientes
$("#btn-filtro-historiaPagos").click(function (e) {
	e.preventDefault();
	$.get("/intranet/obtenerPagosProgramados", {
			fechaI: $("#datepickerFechaInicio").val(),
			fechaF: $("#datepickerFechaFin").val(),
			pagination: changePage
		},
		function (data) {
			$("#tbodyHistorialPagos").html("")
			let valueToAppend = ""
			if( Array.isArray(data)){
				if ($.isEmptyObject(data) ) {
					alert("No se encontraron resultados")
				}
				else{
					
					$.each(data, function (i) {
						valueToAppend += createTemplateHistorialPagos(data[i]);
					});
				}
				
				$("#tbodyHistorialPagos").append(valueToAppend)
			}
			else{
				alert(data.error)
			}
		},
		"json"
	);	
});

$(".changeNext").click( function () {
	changePage += 20;
	$("#btn-filtro-historiaPagos").click();
});
$(".changeBefore").click(function () {
	changePage -= 20;
	if ( changePage < 0) {
		changePage = 0
	}
	$("#btn-filtro-historiaPagos").click();
});
/* End scripts 'Gastos de operacion' */

// Expandable Data Table

$('.data-expands').each(function(){
	$(this).click(function(){
		$(this).toggleClass('row-active');
		$(this).parent().find('.expandable').toggleClass('row-open');
		$(this).parent().find('.row-toggle').toggleClass('row-toggle-twist');
	});
});

$(document).ready(function(){
	
	$.get("/intranet/obtenerCreditosAcreedores", {
			
		},
		function (data) {
			$("#tbodyHistorialAcreedores").html('');
			let valueToAppend = ""
			if( Array.isArray(data)){
				if ($.isEmptyObject(data) ) {
					
				}
				else{
					console.log(data)
					
					$.each(data, function (i) {
						valueToAppend += createTemplateHistorialPagosAcreedores(data[i]);
					});
				}
				
				$("#tbodyHistorialAcreedores").append(valueToAppend)
			}
			else{
				alert(data.error)
			}
		},
		"json"
	);	
});

/* Funcionalidad del modulo 'Pago acreedores' */

function createTemplateHistorialPagosAcreedores(element) {
	//<i class="fa fa-pencil" aria-hidden="true"></i>
	var amortizacion = (parseFloat(element.restante_deuda)) + ((element.interes_deuda/100) * element.monto_total_deuda);
	valueToAppend = `<tr>
						<td> ${element.nombre_entidad}</td>
						<td align="right">$ ${ formatoMoneda(element.monto_total_deuda) }</td>
						<td>${element.plazo_deuda} Meses</td>
						<td>${element.interes_deuda}%</td>
						<td>$ ${formatoMoneda(element.restante_deuda)}</td>
						<td id="amortizacion_"${element.id_acreedor}>$ ${formatoMoneda(amortizacion)}</td>
						<td>${element.fecha_deuda_gen}</td>
						<td>
							<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalShowAcreedores" 
							 onclick="setDetallePagosAcreedores('${element.id}','${element.nombre_entidad}')">
								<i class="fa fa-eye" aria-hidden="true"></i>
							</button>
							<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalAplicarPagoAcreedores" 
							 onclick="setDetallesAcreedor('${element.id}','${element.alias}','${amortizacion}')">
							 	<i class="fa fa-money fa-4x"></i>
							</button>
							<button class="btn btn-danger" onclick="eliminaCredito('${element.id_acreedor}')">Eliminar</button>
						</td>
					 </tr>`

	return valueToAppend;
}

function setDetallesAcreedor(id, nameAcreedor, amortizacion){

	var nombreacreedor = document.getElementById('acreedorLabel');
	var idDetalleDeuda = document.getElementById('idDetalleDeuda');
	var Amortizacion = document.getElementById('Amortizacion');
	
	nombreacreedor.innerHTML = "Pago a: "+nameAcreedor;
	idDetalleDeuda.value = id;
	Amortizacion.value = amortizacion;

}

function setDetallePagosAcreedores(id, acreedor){

	var nombreAcreedor = document.getElementById('nombreAcreedor');
	
	nombreAcreedor.innerHTML = acreedor;

	$.get("/intranet/obtenerDetallePago", {
			id: id
		},
		function (data) {
			$("#tbodyasd").html("")
			let a = ""
			if( Array.isArray(data)){
				if ($.isEmptyObject(data) ) {
					alert("No se encontraron resultados")
				}
				else{
					$.each(data, function (i) {
						a += createTemplateDetallePagosAcreedores(data[i])
					});
				}
				
				$("#tbodyasd").append(a)
			}
			else{
				alert(data.error)
			}
		},
		"json"
	);	
}

function createTemplateDetallePagosAcreedores(element){
	valueToAppend = `<tr>
						<td align="right">$ ${ formatoMoneda(element.monto_abonado_capital) }</td>
						<td align="right">$ ${ formatoMoneda(element.interes_pagado) }</td>
						<td align="right">${ element.fecha_abono }</td>
					</tr>`
	return valueToAppend;
}


function saveNewCreditor() {
	if(confirm("Desea crear este nuevo credito?")){
		$.post("/intranet/applyCreditors", {
				acreedor: $("#entCredit").val(),
				aliasAcreedor: $("#aliasEnt").val(),
				monto: $("#montoTotalAcreedor").val(),
				plazo: $("#plazoPagosAcreedor").val(),
				interes: $("#interesAcreedor").val(),
				fecha: $("#datepickerFechaAdeudo").val()
			},
			function (data) {
				$("#tbodyHistorialAcreedores").html("")
				let valueToAppend = ""
				if( Array.isArray(data)){
					if ($.isEmptyObject(data) ) {
						alert("No se encontraron resultados")
					}
					else{
						alert("Credito creado con exito.")
						
						$.each(data, function (i) {
							valueToAppend += createTemplateHistorialPagosAcreedores(data[i]);
						});

						$('.data-expands').click()
					}
					
					$("#tbodyHistorialAcreedores").append(valueToAppend)
				}
				else{
					alert(data.error)
				}
			},
			"json"
		);	
		var entCredit = document.getElementById('entCredit');
		var aliasEnt = document.getElementById('aliasEnt');
		var montoTotalAcreedor = document.getElementById('montoTotalAcreedor');
		var plazoPagosAcreedor = document.getElementById('plazoPagosAcreedor');
		var interesAcreedor = document.getElementById('interesAcreedor');

		entCredit.value = "";
		aliasEnt.value = "";
		montoTotalAcreedor.value = "";
		plazoPagosAcreedor.value = "";
		interesAcreedor.value = "";
	}
}

function savePayToCreditor(){
	var montoAplicado =  $("#pagoCreditoMonto").val();
	var interesGenerado = $("#pagoCreditoInteres").val();
	var Amortizacion = $("#Amortizacion").val();
	var total = parseFloat(montoAplicado) + parseFloat(interesGenerado);
	var ok;
	
	if(total == Amortizacion){
		ok = 1;
	}else{
		ok = 0;
	}
	if(confirm("Desea crear este nuevo abono?")){
		$.post("/intranet/payTo", {
				montoAplicado,
				interesGenerado,
				fechaAplicacion: $("#pagoCreditoFecha").val(),
				id: $("#idDetalleDeuda").val(),
				ok
			},
			function (data) {
				console.log(data);
				if( data == 0){
					alert("No se encontraron resultados");					
				}
				else if( data == 1){
					alert("Abono realizado correctamente.");
					location.reload();
				}else if( data == "done" ){
					alert("Abono realizado correctamente.");
					alert("Deuda liquidada.")
					location.reload();
				}else{
					alert("Ha ocurrido un error.");
				}
			},
			"json"
		);	
		var pagoCreditoMonto = document.getElementById('pagoCreditoMonto');
		var pagoCreditoInteres = document.getElementById('pagoCreditoInteres');
		var pagoCreditoFecha = document.getElementById('pagoCreditoFecha');

		pagoCreditoMonto.value = "";
		pagoCreditoInteres.value = "";
		pagoCreditoFecha.value = "";
		$('#pagoAcreedor').click()
	}
}

function eliminaCredito(id){
	if(confirm("Desea eliminar este crédito?")){
		$.post("/intranet/eliminarCredito", {
				id: id
			},
			function (data) {
				$("#tbodyHistorialAcreedores").html("")
				let valueToAppend = ""
				if( Array.isArray(data)){
					if ($.isEmptyObject(data) ) {
						alert("No se encontraron resultados")
					}
					else{
						alert("Baja dada con éxito.")
						
						$.each(data, function (i) {
							valueToAppend += createTemplateHistorialPagosAcreedores(data[i]);
						});
					}
					
					$("#tbodyHistorialAcreedores").append(valueToAppend)
				}
				else{
					alert(data.error)
				}
			},
			"json"
		);
	}
}