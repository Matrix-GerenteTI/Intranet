/**
 * @Package: Ultra Admin HTML Theme
 * @Since: Ultra 1.0
 * This file is part of Ultra Admin Theme HTML package.
 *
 
 
jQuery(function($) {

    'use strict';

    var ULTRA_SETTINGS = window.ULTRA_SETTINGS || {};

    
    //    Sparkline Chart
    
    ULTRA_SETTINGS.dbSparklineChart = function() {

        if ($.isFunction($.fn.sparkline)) {

            $('.db_dynamicbar').sparkline([8.4, 9, 8.8, 8, 9.5, 9.2, 9.9, 9, 9, 8, 7, 9, 9, 9.5, 8, 9.5, 9.8], {
                type: 'bar',
                barColor: '#f5f5f5',
                height: '40',
                barWidth: '10',
                barSpacing: 1,
            });

            $('.db_linesparkline').sparkline([2000, 3454, 5454, 2323, 3432, 4656, 2897, 3545, 4232, 5434, 4656, 3567, 4878, 3676, 3787], {
                type: 'line',
                width: '100%',
                height: '40',
                lineWidth: 2,
                lineColor: '#f5f5f5',
                fillColor: 'rgba(255,255,255,0.2)',
                highlightSpotColor: '#ffffff',
                highlightLineColor: '#ffffff',
                spotRadius: 3,
            });


            // Bar + line composite charts
            $('.db_compositebar').sparkline([4, 6, 7, 7, 4, 3, 2, 4, 6, 7, 7, 4, 3, 1, 4, 6, 5, 9], {
                type: 'bar',
                barColor: '#f5f5f5',
                height: '40',
                barWidth: '10',
                barSpacing: 1,
            });

            $('.db_compositebar').sparkline([4, 1, 5, 7, 9, 9, 8, 8, 4, 7, 9, 9, 8, 8, 4, 2, 5, 6, 7], {
                composite: true,
                fillColor: 'rgba(153,114,181,0)',
                type: 'line',
                width: '100%',
                height: '40',
                lineWidth: 2,
                lineColor: '#9972b5',
                highlightSpotColor: '#fa8564',
                highlightLineColor: '#9972b5',
                spotRadius: 3,
            });



        }

    };




   
    //    Easy PIE
     
    ULTRA_SETTINGS.dbEasyPieChart = function() {

        if ($.isFunction($.fn.easyPieChart)) {

            $('.db_easypiechart1').easyPieChart({
                barColor: '#9972b5',
                trackColor: '#f5f5f5',
                scaleColor: '#f5f5f5',
                lineCap: 'square',
                lineWidth: 6,
                size: 120,
                animate: 2000,
                onStep: function(from, to, percent) {
                    $(this.el).find('.percent').text(Math.round(percent));
                }
            });
        }

    };




    
    //    Morris 
    
    ULTRA_SETTINGS.dbMorrisChart = function() {


        //Area Graph
        // Use Morris.Area instead of Morris.Line
        Morris.Area({
            element: 'db_morris_area_graph',
            data: [{
                x: '2009 Q1',
                y: 3,
                z: 2
            }, {
                x: '2010 Q2',
                y: 2,
                z: 1
            }, {
                x: '2011 Q3',
                y: 1,
                z: 2
            }, {
                x: '2011 Q4',
                y: 2,
                z: 2
            }, {
                x: '2012 Q5',
                y: 4,
                z: 2
            }, {
                x: '2012 Q6',
                y: 2,
                z: 4
            }],
            resize: true,
            xkey: 'x',
            ykeys: ['y', 'z'],
            labels: ['Y', 'Z'],
            lineColors: ['#9972b5', '#1fb5ac'],
            pointFillColors: ['#fa8564']
        }).on('click', function(i, row) {
            console.log(i, row);
        });


        //Line Graph
        
        var day_data = [{
            "period": "2012-10-01",
            "pageviews": 3407,
            "unique": 660
        }, {
            "period": "2012-09-30",
            "pageviews": 3351,
            "unique": 629
        }, {
            "period": "2012-09-29",
            "pageviews": 3269,
            "unique": 618
        }, {
            "period": "2012-09-20",
            "pageviews": 3246,
            "unique": 661
        }, {
            "period": "2012-09-19",
            "pageviews": 3257,
            "unique": 667
        }, {
            "period": "2012-09-18",
            "pageviews": 3248,
            "unique": 627
        }, {
            "period": "2012-09-17",
            "pageviews": 3171,
            "unique": 660
        }, {
            "period": "2012-09-16",
            "pageviews": 3171,
            "unique": 676
        }, {
            "period": "2012-09-15",
            "pageviews": 3201,
            "unique": 656
        }, {
            "period": "2012-09-10",
            "pageviews": 3215,
            "unique": 622
        }];
        Morris.Line({
            element: 'db_morris_line_graph',
            data: day_data,
            resize: true,
            xkey: 'period',
            ykeys: ['pageviews', 'unique'],
            labels: ['Page Views', 'Unique Visitors'],
            lineColors: ['#9972b5', '#1fb5ac'],
            pointFillColors: ['#fa8564']
        });

        //Bar Graph
        // Use Morris.Bar
        Morris.Bar({
            element: 'db_morris_bar_graph',
            data: [{
                x: '2011 Q1',
                y: 3,
                z: 2
            }, {
                x: '2011 Q2',
                y: 2,
                z: 1
            }, {
                x: '2011 Q3',
                y: 1,
                z: 2
            }, {
                x: '2011 Q4',
                y: 2,
                z: 2
            }, {
                x: '2011 Q5',
                y: 4,
                z: 2
            }, {
                x: '2011 Q6',
                y: 2,
                z: 4
            }],
            resize: true,
            xkey: 'x',
            ykeys: ['y', 'z'],
            labels: ['Y', 'Z'],
            barColors: ['#9972b5', '#1fb5ac']
        }).on('click', function(i, row) {
            console.log(i, row);
        });

        $('.r1_maingraph .switch .fa').on('click', function() {

            $('.r1_maingraph .switch .fa').removeClass("icon-default").addClass("icon-secondary");

            if ($(this).hasClass("fa-bar-chart")) {
                $(this).toggleClass("icon-secondary icon-default");
                $("#db_morris_line_graph").hide();
                $("#db_morris_area_graph").hide();
                $("#db_morris_bar_graph").show();
            }

            if ($(this).hasClass("fa-line-chart")) {
                $(this).toggleClass("icon-secondary icon-default");
                $("#db_morris_line_graph").show();
                $("#db_morris_area_graph").hide();
                $("#db_morris_bar_graph").hide();
            }

            if ($(this).hasClass("fa-area-chart")) {
                $(this).toggleClass("icon-secondary icon-default");
                $("#db_morris_line_graph").hide();
                $("#db_morris_area_graph").show();
                $("#db_morris_bar_graph").hide();
            }

        });


    };



    
    //    Rickshaw charts
    
    ULTRA_SETTINGS.dbRickshawChart = function() {



        //------------------- extensions chart - start----------------------

        // set up our data series with 100 random data points

        var seriesData = [
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            []
        ];
        var random = new Rickshaw.Fixtures.RandomData(150);

        for (var i = 0; i < 50; i++) {
            random.addData(seriesData);
        }

        // instantiate our graph!

        var graph = new Rickshaw.Graph({
            element: document.getElementById("chart"),
            width: $(".rickshaw_ext").width(),
            height: 235,
            renderer: 'area',
            stroke: true,
            preserve: true,
            series: [{
                color: '#1fb5ac',
                data: seriesData[0],
                name: 'Upload'
            }, {
                color: '#fa8564',
                data: seriesData[1],
                name: 'Download'
            }, {
                color: '#9972b5',
                data: seriesData[2],
                name: 'Speed'
            }]
        });

        graph.render();

        var preview = new Rickshaw.Graph.RangeSlider({
            graph: graph,
            element: document.getElementById('preview'),
        });

        var hoverDetail = new Rickshaw.Graph.HoverDetail({
            graph: graph,
            xFormatter: function(x) {
                return new Date(x * 1000).toString();
            }
        });

        var annotator = new Rickshaw.Graph.Annotate({
            graph: graph,
            element: document.getElementById('timeline')
        });

        var legend = new Rickshaw.Graph.Legend({
            graph: graph,
            element: document.getElementById('legend')

        });

        var shelving = new Rickshaw.Graph.Behavior.Series.Toggle({
            graph: graph,
            legend: legend
        });

        var order = new Rickshaw.Graph.Behavior.Series.Order({
            graph: graph,
            legend: legend
        });

        var highlighter = new Rickshaw.Graph.Behavior.Series.Highlight({
            graph: graph,
            legend: legend
        });

        var smoother = new Rickshaw.Graph.Smoother({
            graph: graph,
            element: document.querySelector('#smoother')
        });

        var ticksTreatment = 'glow';

        var xAxis = new Rickshaw.Graph.Axis.Time({
            graph: graph,
            ticksTreatment: ticksTreatment,
            timeFixture: new Rickshaw.Fixtures.Time.Local()
        });

        xAxis.render();

        var yAxis = new Rickshaw.Graph.Axis.Y({
            graph: graph,
            tickFormat: Rickshaw.Fixtures.Number.formatKMBT,
            ticksTreatment: ticksTreatment
        });

        yAxis.render();


        var controls = new RenderControls({
            element: document.querySelector('form#rickshaw_side_panel'),
            graph: graph
        });

        // add some data every so often

        var messages = [
            "Changed home page welcome message",
            "Minified JS and CSS",
            "Changed button color from blue to green",
            "Refactored SQL query to use indexed columns",
            "Added additional logging for debugging",
            "Fixed typo",
            "Rewrite conditional logic for clarity",
            "Added documentation for new methods"
        ];

        setInterval(function() {
            random.removeData(seriesData);
            random.addData(seriesData);
            graph.update();

        }, 3000);

        function addAnnotation(force) {
            if (messages.length > 0 && (force || Math.random() >= 0.95)) {
                annotator.add(seriesData[2][seriesData[2].length - 1].x, messages.shift());
                annotator.update();
            }
        }

        addAnnotation(true);
        setTimeout(function() {
            setInterval(addAnnotation, 6000)
        }, 6000);

        var previewXAxis = new Rickshaw.Graph.Axis.Time({
            graph: graph,
            timeFixture: new Rickshaw.Fixtures.Time.Local(),
            ticksTreatment: ticksTreatment
        });

        previewXAxis.render();


        



    };





        //     gauge meter
    
    ULTRA_SETTINGS.dbGaugemeter = function() {

        if ($("#gauge-meter").length) {
            var opts = {
                lines: 1, // The number of lines to draw
                angle: 0.05, // The length of each line
                lineWidth: 0.30, // The line thickness
                pointer: {
                    length: 0.40, // The radius of the inner circle
                    strokeWidth: 0.038, // The rotation offset
                    color: '#ffffff' // Fill color
                },
                limitMax: 'false', // If true, the pointer will not go past the end of the gauge
                colorStart: '#9972b5', // Colors
                colorStop: '#9972b5', // just experiment with them
                strokeColor: '#ffffff', // to see which ones work best for you
                generateGradient: false
            };
            var target = document.getElementById('gauge-meter'); // your canvas element
            var gauge = new Gauge(target).setOptions(opts); // create sexy gauge!
            gauge.maxValue = 100; // set max gauge value
            gauge.animationSpeed = 90; // set animation speed (32 is default value)

            gauge.set(56); // set actual value
            gauge.setTextField(document.getElementById("gauge-meter-text"));
            randomGauge();

        }

        function randomGauge() {
            setTimeout(function() {
                var val = Math.random() * 99;
                gauge.set(val); // set actual va{lue
                AnimationUpdater.run();
                randomGauge();
            }, 2000);
        }

    };



    
    // initialize respective scripts 
    
    $(document).ready(function() {
        ULTRA_SETTINGS.dbSparklineChart();
        ULTRA_SETTINGS.dbEasyPieChart();
        ULTRA_SETTINGS.dbMorrisChart();
        ULTRA_SETTINGS.dbRickshawChart();
        ULTRA_SETTINGS.dbGaugemeter();
    });

    $(window).resize(function() {
        ULTRA_SETTINGS.dbSparklineChart();
    });

    $(window).load(function() {});

});
*/

let acumulable = 1;

 function getSubordinados() {

     $.get("/intranet/controladores/nomina/recursos_humanos.php", {
             opc: 'asistenciaDePersonal'
         },
         function (data, textStatus, jqXHR) {
            let templateEmpleados = '';
            $.each(data, function (i, item) { 
                templateEmpleados += `<tr>
                                            <td> <input type="checkbox" id="chk_${item.nip}" onclick="capturaAsistencia(${item.nip})"> </td>
                                            <td> ${item.nombre}</td>
                                            <td><button class="btn" title="Cambiar de sucursal" onclick="cambiarAdscripcion(${item.nip})" ><i class="glyphicon glyphicon-sort" ></i></button>
                                                     <button class="btn" title="Solicitar Permiso" onclick="solicitarPermiso(${item.nip})"><i class="glyphicon glyphicon-list-alt" ></i></button>  
                                                     <button class="btn" title="Acción Correctiva" onclick="modalAccionCorrectiva(${item.nip})"><i class="glyphicon glyphicon-remove-circle" ></i></button></td>
                                        </tr>`;
            });
            $("#contentEmpleados").html( templateEmpleados );
         },
         "json"
     );
 }

 function capturaAsistencia( id) {
     if ( $(`#chk_${id}`).is(":checked") ) {
         $("#asistenciasModal").modal("show");
         $("#empledoNip").val( id );
     }
 }
 $("#guardarAsistencia").click(function (e) {
     let asistencia = $("#selTipoAsistencia").val();
     let observaciones = $("#observacionesAsistencia").val();
    let empleado = $("#empledoNip").val();

     $.post("/intranet/controladores/nomina/recursos_humanos.php", {
         opc: 'registraAsistencia',
         observaciones: observaciones,
         asistencia: asistencia,
         empleado: empleado
     },
         function (data, textStatus, jqXHR) {
             if( data > 0){
                alert("Asistencia registrada");
                 $("#observacionesAsistencia").val('');
             }
         },
         "text"
     );
     
 });

function cargaSucursales( element ) {
    $.get("/intranet/controladores/nomina/empresa.php", {
        opc: 'getAllSucursales'
    },
        function (data, textStatus, jqXHR) {
            let templateSucursales = '';
            $.each(data, function (i, item) {
                templateSucursales += `<option value="${item.id}">${item.descripcion}</option>`
            });
            $("#"+ element ).html(templateSucursales);
        },
        "json"
    );
}

function cargaPuestos( element ) {
    $.get("/intranet/controladores/nomina/empresa.php", {
        opc: 'getAllPuestos'
    },
        function (data, textStatus, jqXHR) {
            let template= '';
            $.each(data, function (i, item) {
                template += `<option value="${item.id}">${item.descripcion}</option>`
            });
            $("#" + element).html(template);
        },
        "json"
    );    
}
function cambiarAdscripcion( personal ) {
    $("#empledoNip").val( personal );
    cargaSucursales( 'sucursalesAdscripcion');
    $("#modalCambioAdscripcion").modal('show');
}

function guardarAdscripcion() {
    const empleado = $("#empledoNip").val();
    const sucursal = $("#sucursalesAdscripcion").val();
    const fecha = $("#fechaAdscripcion").val();
    $.post("/intranet/controladores/nomina/recursos_humanos.php", {
        opc: 'cambiarAdscripcion',
        empleado: empleado,
        sucursal: sucursal,
        fecha: fecha
    },
        function (data, textStatus, jqXHR) {
            if ( data > 0 ) {
                alert("¡Registrado!. \nEl empleado ya no estará disponible en tu sucursal.");
                getSubordinados();
            }
        },
        "text"
    );
}

function solicitarPermiso( personal) {
    $("#empledoNip").val(personal);
    $("#modalPermisos").modal("show");
    cargaSucursales('sucursalPermiso');
    cargaPuestos('puestoPermiso');    
}

function guardarPermiso() {
    const permiso = {
        empleado: $("#empledoNip").val(),
        fecha: $("#fechaPermiso").val(),
        puesto: $("#puestoPermiso").val(),
        sucursal: $("#sucursalPermiso").val(),
        motivo: $("#motivoPermiso").val(),
        accionPermiso: $("input[name=accionPermiso]:checked").val(),
        dias: $("#diasPermiso").val(),
        opc: 'aplicarPermiso'
    }

    $.post("/intranet/controladores/nomina/recursos_humanos.php", permiso ,
        function (data, textStatus, jqXHR) {
            if ( data > 0) {
                alert("Permiso aplicado al trabajador seleccionado correctamente");
                $("#motivoPermiso").val('');
                $("#fechaPermiso").val('');
                $("#diasPermiso").val('');
                $.post("/intranet/controladores/nomina/Comprobantes/permiso.php", {
                    permiso: data
                },
                    function (data, textStatus, jqXHR) {
                        window.open( data, "_blank");
                    },
                    "text"
                );
            }
        },
        "text"
    );
}

function modalAccionCorrectiva( personal ) {
    $("#empledoNip").val(personal);
    $("#modalAccionCorrectiva").modal( "show");
    cargaSucursales( 'sucursalAccionCorrectiva' );
    cargaPuestos( 'puestoAccionCorrectiva' );
}

$(".chkSancion").change(function (e) { 
    if ( $(this).val() === '1') {
        $(".fieldsDescuento").fadeIn();
    }else{
        $(".fieldsDescuento").fadeOut();
    }
});

$(".chkSancion").attr("disabled",true);
$("#esAcumulable").change(function (e) { 
    e.preventDefault();
    if ( $(this).is(":checked") ) {
        acumulable = 1;
        $(".chkSancion").attr('disabled', true );
    } else {
        acumulable = 0;
        $(".chkSancion").attr('disabled', false );
    }
});

function guardarAccionCorrectiva() {
    
    const accionCorrectiva ={
    empleado : $("#empledoNip").val(),
    fechaIncidencia : $("#fechaAccionCorrectiva").val(),
    puesto : $("#puestoAccionCorrectiva").val(),
    sucursal : $("#sucursalAccionCorrectiva").val(),
    motivo : $("#motivoAccionCorrectiva").val(),
    plan : $("#planAccionCorrectiva").val(),
    esAcumulable: acumulable,
    opc: 'aplicarSancion'
    }
    
    const aplicaSancion = $("input[name=sancionEconomica]:checked").val();
    
    if ( aplicaSancion === '1' ) {
        accionCorrectiva.monto = $("#montoAccionCorrectiva").val();
        accionCorrectiva.fechaDescuento = $("#fechaDescuentoAccionCorrectiva").val();
    }    

    $.post("/intranet/controladores/nomina/recursos_humanos.php",  accionCorrectiva,
        function (data, textStatus, jqXHR) {
            if (data > 0 ) {
                alert("Acción correctiva aplicaca al trabajador correctamente");
                     $("#empledoNip").val('');
                     $("#fechaAccionCorrectiva").val('');
                     $("#motivoAccionCorrectiva").val('');
                     $("#planAccionCorrectiva").val('');
                     $("#montoAccionCorrectiva").val('');
                     $("#fechaDescuentoAccionCorrectiva").val('');
                    $("input[name=sancionEconomica][value=0]").prop("checked", true );
                    $(".fieldsDescuento").fadeOut();
                $.post("/intranet/controladores/nomina/Comprobantes/accionCorrectiva.php", {
                    accion: data
                },
                    function (data, textStatus, jqXHR) {
                        window.open(data, "_blank");
                    },
                    "text"
                );                    
            }
        },
        "text"
    );
}

 getSubordinados();


// getTareas();
var idtarea = 0;
 getMovtosAutomaticos()
 
function getTareas(){
	var params = "";
	//alert("Entra");
	$.post("controladores/per_tareas.php?opc=tareasUsuario", params, function(resp){
		//alert(resp);
		var row = eval('('+resp+')');			
		var echo = "";
		for(i in row){
			echo+= '<li>';
			if(row[i].status > 0){				
				echo+= '	<input type="checkbox" id="task-'+row[i].id+'" class="skin-square-red" onclick="checkTarea('+row[i].id+')" checked>';				
				echo+= '	<label class="icheck-label form-label" for="task-'+row[i].id+'" style="text-decoration: line-through">'+row[i].descripcion+'</label>';
			}else{
				echo+= '	<input type="checkbox" id="task-'+row[i].id+'" class="skin-square-red" onclick="checkTarea('+row[i].id+')">';			
				echo+= '	<label class="icheck-label form-label" for="task-'+row[i].id+'">'+row[i].descripcion+'</label>';
			}
			echo+= '</li> ';
		}
		$("#tareasUsuario").html(echo);
		//iCheck();
		//ultraToDoWidget();
	});	
}

function getMovtosAutomaticos() {
	$.post("controladores/con_egresos.php?opc=mvtosPendientes", {},
		function (data, textStatus, jqXHR) {
			
		},
		"json"
	);
}

function guardaCheck(){
	var id = idtarea;
	var observaciones = $("#tarobservaciones").val();
	var file = $("#tarfoto").val();
	if(observaciones.trim()!=""){
		var params = "observaciones=" + observaciones;
		params+= "&id=" + id;
		//alert(params);
		$.post("controladores/per_tareas.php?opc=checkTarea", params, function(resp){
			//alert(resp);
			if(resp>0){
				//alert("El registro se guardo exitosamente!");
				if(file.length>0){
					agregaFoto(resp);
				}
				$("#tarobservaciones").val('');
				getTareas();
			}else
				alert("Ocurrió un error, intente nuevamente. Si el problema persiste contacte a soporte");
		});
	}
}

function eliminaCheck(){
	var params = "id=" + idtarea;
	$.post("controladores/per_tareas.php?opc=eliminaCheck", params, function(resp){
		//alert(resp);
		if(resp>0){
			getTareas();
		}else
			alert("Ocurrió un error, intente nuevamente. Si el problema persiste contacte a soporte");
	});
}

function agregaFoto(id){
	//alert("Si entra");	
	var inputFileImage = document.getElementById("tarfoto");
	var file = inputFileImage.files[0];
	var data = new FormData();
	data.append('archivo',file);
	var url = "controladores/upload.php";
	$.ajax({
		url:url,
		type:'POST',
		contentType:false,
		data:data,
		processData:false,
		cache:false}).done(function(){
		   $.post("controladores/per_tareas.php?opc=updFotoTarea", "id="+id, function(resp){
				if(resp==1){
					$("#tareasmodal").modal('hide');
					$("#uncheckModal").modal('hide');
				}
			});
		});
}

function noEliminaCheck(){
	$("#task-"+idtarea).prop("checked",true);
}

function noGuardaCheck(){
	$("#task-"+idtarea).prop("checked",false);
}

function checkTarea(id){
	idtarea = id;
	if($("#task-"+id).prop("checked") == true){
		$("#tareasmodal").modal();
	}else{
		$("#uncheckModal").modal();
	}
}

function ultraToDoWidget(){
	$(".icheck-minimal-white.todo-task").on('ifChecked', function(event) {
            $(this).parent().parent().addClass("checked");
        });
        $(".icheck-minimal-white.todo-task").on('ifUnchecked', function(event) {
            $(this).parent().parent().removeClass("checked");
        });

        $(".wid-all-tasks ul").perfectScrollbar({
            suppressScrollX: true
        });
}

function iCheck(){

	var x;
	var colors = ["-green", "-red", "-yellow", "-blue", "-aero", "-orange", "-grey", "-pink", "-purple","-white"];

	for (x = 0; x < colors.length; x++) {

		if (x == 0) {
			$('input.icheck-minimal').iCheck({
				checkboxClass: 'icheckbox_minimal' + colors[x],
				radioClass: 'iradio_minimal' + colors[x],
				increaseArea: '20%'
			});

		} // end x = 0

		$('input.icheck-minimal' + colors[x]).iCheck({
			checkboxClass: 'icheckbox_minimal' + colors[x],
			radioClass: 'iradio_minimal' + colors[x],
			increaseArea: '20%'
		});

	}
}