
<!-- CORE JS FRAMEWORK - START --> 
<script src="/intranet/assets/js/jquery-1.11.2.min.js" type="text/javascript"></script>
<script src="/intranet/assets/js/jquery.easing.min.js" type="text/javascript"></script>
<script src="/intranet/assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="/intranet/assets/plugins/pace/pace.min.js" type="text/javascript"></script>
<script src="/intranet/assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js" type="text/javascript"></script>
<script src="/intranet/assets/plugins/viewport/viewportchecker.js" type="text/javascript"></script>
	<!-- Modificacion 07/04/20 -->
	<script src="/intranet/assets/js/acreedoresJS.js" type="text/javascript"></script>
	<script src="/intranet/assets/js/consultaRapidaInventario.js" type="text/javascript"></script>
    <script src="/intranet/assets/js/resguardoJS.js" type="text/javascript"></script>
    <script src="/intranet/assets/js/baja_colaboradores.js" type="text/javascript"></script>
	<script src="/intranet/assets/js/aleatoriosExcepcionesJS.js" type="text/javascript"></script>
	<script src="/intranet/assets/js/recursos_materialesJS.js" type="text/javascript"></script>
	
<!-- CORE JS FRAMEWORK - END --> 

<!-- OTHER SCRIPTS INCLUDED ON THIS PAGE - START --> 
		
		<script src="/intranet/assets/plugins/jquery-ui/smoothness/jquery-ui.min.js" type="text/javascript"></script> <script src="/intranet/assets/plugins/datepicker/js/datepicker.js" type="text/javascript"></script> <script src="/intranet/assets/plugins/daterangepicker/js/moment.min.js" type="text/javascript"></script> <script src="/intranet/assets/plugins/daterangepicker/js/daterangepicker.js" type="text/javascript"></script> <script src="/intranet/assets/plugins/timepicker/js/timepicker.min.js" type="text/javascript"></script> <script src="/intranet/assets/plugins/datetimepicker/js/datetimepicker.min.js" type="text/javascript"></script> <script src="/intranet/assets/plugins/datetimepicker/js/locales/bootstrap-datetimepicker.fr.js" type="text/javascript"></script> <script src="/intranet/assets/plugins/colorpicker/js/bootstrap-colorpicker.min.js" type="text/javascript"></script> <script src="/intranet/assets/plugins/tagsinput/js/bootstrap-tagsinput.min.js" type="text/javascript"></script> <script src="/intranet/assets/plugins/select2/select2.min.js" type="text/javascript"></script> <script src="/intranet/assets/plugins/typeahead/typeahead.bundle.js" type="text/javascript"></script> <script src="/intranet/assets/plugins/typeahead/handlebars.min.js" type="text/javascript"></script> <script src="/intranet/assets/plugins/multi-select/js/jquery.multi-select.js" type="text/javascript"></script> <script src="/intranet/assets/plugins/multi-select/js/jquery.quicksearch.js" type="text/javascript"></script> <!-- OTHER SCRIPTS INCLUDED ON THIS PAGE - END --> 
        <script src="/intranet/assets/plugins/autosize/autosize.min.js" type="text/javascript"></script><script src="/intranet/assets/plugins/icheck/icheck.min.js" type="text/javascript"></script><!-- OTHER SCRIPTS INCLUDED ON THIS PAGE - END --> 
		<script src="/intranet/assets/plugins/inputmask/jquery.inputmask.bundle.min.js" type="text/javascript"></script><script src="/intranet/assets/plugins/autonumeric/autoNumeric.js" type="text/javascript"></script><!-- OTHER SCRIPTS INCLUDED ON THIS PAGE - END --> 
		<script src="/intranet/assets/plugins/datatables/js/jquery.dataTables.min.js" type="text/javascript"></script><script src="/intranet/assets/plugins/datatables/extensions/TableTools/js/dataTables.tableTools.min.js" type="text/javascript"></script><script src="/intranet/assets/plugins/datatables/extensions/Responsive/js/dataTables.responsive.min.js" type="text/javascript"></script><script src="/intranet/assets/plugins/datatables/extensions/Responsive/bootstrap/3/dataTables.bootstrap.js" type="text/javascript"></script><!-- OTHER SCRIPTS INCLUDED ON THIS PAGE - END --> 
		<script src="/intranet/assets/plugins/rickshaw-chart/vendor/d3.v3.js" type="text/javascript"></script> <script src="/intranet/assets/plugins/rickshaw-chart/js/Rickshaw.All.js"></script><script src="/intranet/assets/plugins/sparkline-chart/jquery.sparkline.min.js" type="text/javascript"></script><script src="/intranet/assets/plugins/easypiechart/jquery.easypiechart.min.js" type="text/javascript"></script><script src="/intranet/assets/plugins/morris-chart/js/raphael-min.js" type="text/javascript"></script><script src="/intranet/assets/plugins/morris-chart/js/morris.min.js" type="text/javascript"></script><script src="/intranet/assets/plugins/jvectormap/jquery-jvectormap-2.0.1.min.js" type="text/javascript"></script><script src="/intranet/assets/plugins/jvectormap/jquery-jvectormap-world-mill-en.js" type="text/javascript"></script><script src="/intranet/assets/plugins/gauge/gauge.min.js" type="text/javascript"></script><script src="/intranet/assets/plugins/icheck/icheck.min.js" type="text/javascript"></script>



<script src="/intranet/assets/js/tooltipButton.js" type="text/javascript"></script>
<script src="/intranet/assets/js/jquery.iframe-transport.js"></script>
<script src="/intranet/assets/js/jquery.fileupload.js"></script>
<script src="/intranet/assets/js/Chart.js"></script>
<script src="assets/js/css3-animate-it.js" type="text/javascript"></script> 
<script src="/intranet/assets/plugins/getorgchart/getorgchart.js"></script>    
<!-- <script src="/intranet/assets/js/goChart.js"></script> -->
<!-- <script src="/intranet/assets/js/organigrama.js"></script> -->
<?php
	if($_SESSION['uri']!='close'){
		?>
			<script src="assets/js/<?php echo $_SESSION['uri']; ?>.js" type="text/javascript"></script>
		<?php
	}
	
?>

<!-- CORE TEMPLATE JS - START --> 
<script src="assets/js/scripts.js" type="text/javascript"></script> 
<!-- END CORE TEMPLATE JS - END --> 

<!-- Sidebar Graph - START --> 
<script src="/intranet/assets/plugins/sparkline-chart/jquery.sparkline.min.js" type="text/javascript"></script>
<script src="/intranet/assets/js/chart-sparkline.js" type="text/javascript"></script>
<script src="/intranet/assets/js/displayRecursosDepartamentos.js" type="text/javascript"></script>
<!-- Sidebar Graph - END --> 

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.min.js"></script>
<script>
    $("#mesCierre").datepicker({
    format: "mm/yyyy",
    startView: "months",
    minViewMode: "months"
});

let graficaProveedores;

$("#btnFiltrarCierre").click(function (e) { 
	$.get("/intranet/controladores/contabilidad/cxp.php", {
		periodo: $("#mesCierre").val()
	},
		function (data, textStatus, jqXHR) {
			let proveedores = [];
			let debug = []
			let deuda = [];
			let prospectoId = -1;
			let monto = 0;
			let deudaMonto = 0;
			let prov = '' ;
			$.each( data, function (prospectoID, item) { 
				monto = 0;
				deudaMonto = 0;
				$.each(item, function (facturaNo, movimientos) { 
					if ( prospectoId != prospectoID ) {
						proveedores.push( movimientos.proveedor );
						prospectoId = prospectoID ;
						prov = movimientos.proveedor ;
					}
					$.each(movimientos, function (movtos, itemMovtos) { 
						 monto +=  parseFloat( itemMovtos.IMPORTECOBRO  != undefined ? itemMovtos.IMPORTECOBRO : 0 );
						 
						 
						 
					});
					deudaMonto += parseFloat( movimientos.monto_factura );
					
				});
				// console.log(  deudaMonto+ "    -   "+ monto  );
				deuda.push( deudaMonto -monto  );
				debug.push(  prov + "    " +Math.abs( deudaMonto - monto )  )
			});
			console.log( proveedores);
			console.log( deuda);
			console.log( debug);
			
			
			const contenedor = document.getElementById('graficaDeudaCierre').getContext('2d');

			if ( graficaProveedores != undefined ) {
				graficaProveedores.destroy();
			}
			 graficaProveedores = new Chart( contenedor , {
				type: 'bar',
				data:{
					labels: proveedores,
					datasets:[{
						label:"Cierre deuda del mes",
						data: deuda,
						backgroundColor: 'rgb(255, 99, 132)',
						borderColor: 'rgb(255, 99, 132)',				
					}]
	
				},
				options: {
						tooltips: {
							callbacks: {
								label: function(t, d) {
								var xLabel = d.datasets[t.datasetIndex].label;
								var yLabel = t.yLabel >= 1000 ? '$' + t.yLabel.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '$' + t.yLabel;
								return xLabel + ': ' + yLabel;
								}
							}
						},					
						scales: {
							yAxes: [{
								ticks: {
								callback: function(value, index, values) {
									if (parseInt(value) >= 1000) {
										return '$' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
									} else {
										return '$' + value;
									}
								}
								}
							}],							
							xAxes: [{
								barThickness : 25,
								ticks: {
									autoSkip: false,
									maxRotation: 90,
									minRotation: 90,
									callback: function(value) {
										if (value.length > 12) {
											return value.substr(0, 12) + '...'; //truncate
										} else {
											return value
										}									
									}
								}
							}]
						}
					}
			})
			
		},
		"json"
	);
	
});
</script>
<!-- scripts para las eviddencias de los socioeconomicos -->

<script>
	function breadcrumbSocioeconomico(departamento, nodosConsecutivos) {
       departamento = departamento.split('_');
       departamento = departamento[0];
       let listadoNodos = nodosConsecutivos.split('>');
       let cantNodos = listadoNodos.length;
       let template = `<li><a href="javascript:getResourcesSocioeconomico('',0)">/</a></li>`;
    //    let template = '';
       
       for (let index = 0; index < cantNodos; index++) {
           template += `<li ${ index == (cantNodos -1 ) ? "class='active'"  : ''  }><a href="javascript:getResourcesSocioeconomico('${listadoNodos[index]}',${index})">${(listadoNodos[index]).replace(/_/g,' ')}</a></li>`;
       }

       $("#directorioArchivos").html( template );
   }

    function getResourcesSocioeconomico( folder, evaluador = '', comentario = '', fecha='' ) {
		$.get("/intranet/controladores/RH/socioeconomicos.php", {
			folderEmpleado: folder
		},
			function (data, textStatus, jqXHR) {
				console.log(data);
				if( folder ){
					$("#informeSocioeconomico").fadeIn();

				}else{
					$("#informeSocioeconomico").fadeOut();
				}
				$("#evaluador").html( evaluador );
				$("#socioecnomicoComentario").html( comentario);
				$("#socioeconomicoFecha").html( fecha);

				breadcrumbSocioeconomico( folder, folder != '' ? `>${folder}` : '');
				let template = contador = 0;
				
				$.each(data, function (i, item) {
					if (contador == 0 ) {
						
						template += `<div class="row">
														<div class="col-sm-2 col-xs-2 col-md-2 text-center" >`;
						if (item.type == 'folder') {
							template += `<a href="javascript:getResourcesSocioeconomico('${item.name}','${item.evaluador}','${item.observaciones}','${item.fechaRealizado}')"  >`;
						} else {
							template += `<a href="${item.path}" target="_blank"> `
						}
						template += `<img src="/intranet/assets/images/png/${item.ext}.png" class="img-responsive imgbuscadorRecursos">
															<b>${ ( item.folderEmpleado != undefined ? item.folderEmpleado : item.name).replace(/_/g,' ') }</b>
															</a>
														</div>`;
							// contador = contador == -1 ? 3 :contador; //if contador is negative mean that is a root directory
					} else {
						template += `
														<div class="col-sm-2 col-xs-2 col-md-2 text-center"  >`;
						if (item.type == 'folder') {
							template += `<a href="javascript:getResourcesSocioeconomico('${item.name}','${item.evaluador}','${item.observaciones}','${item.fechaRealizado}')" >`;
						} else {
							template += `<a href="${item.path}" target="_blank"> `
						}
						template += `<img src="/intranet/assets/images/png/${item.ext}.png" class="img-responsive imgbuscadorRecursos">
															<b>${ ( item.folderEmpleado != undefined ? item.folderEmpleado : item.name).replace(/_/g,' ') }</b>
															</a>
														</div>`;
					}
					contador++;
					if (contador == 6) {
						template += `</div>`; //cerrando el contenido del row
						contador = 0;
					}
				});
				if (contador > 0 && contador < 6) {
					template += `</div>`
				}

				$("#displayFilesEvidencias").html(template);
			},
			"json"
		);
	}

	getResourcesSocioeconomico('');
</script>
