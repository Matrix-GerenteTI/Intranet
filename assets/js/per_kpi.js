let listadoMeses = ['','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
//cargando las sucursales
$.get("/intranet/controladores/nomina/recursos_humanos.php", { opc: 'getSucursales'},
    function (data, textStatus, jqXHR) {
        let template = `<option value='%' >TODAS</option>`
        $.each(data, function (i, item) { 
             template += `<option value='${item.id}'>${item.descripcion}</option>`
        });
        $("#sucursal").html(template);
    },
    "json"
);

$("#sucursal").change(function (e) {
    setGraficasCambiosAdscripcion();
});

function setAltasBajas( opc, config) {
    $.get("/intranet/controladores/nomina/recursos_humanos.php", {
        mes: -1,
        anio: 2019,
        opc: opc
    },
        function (data, textStatus, jqXHR) {
            generadorGraficaBarra(data, config );
        },
        "json"
    );    
}


function generadorGraficaBarra(data, config) {
    let meses = [];
    let cantidades = [];
    let colores = [];
    let colorBordes = []

    $.each(data, function (i, item) {
        let colorTemporal = '';
        meses.push(i);
        cantidades.push(item);
        colorTemporal = getRandomArbitrary(0, 255) + ',' + getRandomArbitrary(0, 255) + ',' + getRandomArbitrary(0, 255);
        colores.push("rgba(" + colorTemporal + ',0.2)');
        colorBordes.push("rgba(" + colorTemporal + ",1)");
    });
    

    //accediendo al contenedor de la grafica
    let context = $(`#${config.element}`);
    let grafica = new Chart(context, {
        type: 'bar',
        data: {
            labels: meses,
            datasets: [{
                label: "# de "+config.label,
                data: cantidades,
                backgroundColor: colores,
                borderColor: colorBordes,
                borderWidth: 1
            }],

        },
        // options:{
        //     scales:{
        //         yAxes:{
        //             ticks:{
        //                 beginAtZero: true
        //             }
        //         }
        //     }
        // }
    });
}

function setGraficasCambiosAdscripcion() {
    let sucursal = $("#sucursal").val();
    let mes = 1;
    let listaCambios = [];
    
    $.get("/intranet/controladores/nomina/recursos_humanos.php", {
        mes: 1,
        anio : 2019,
        sucursal: sucursal,
        opc: 'getCambiosAds'
    },
        function (data, textStatus, jqXHR) {
            if ( mes == -1 ) {
                $.each(data, function (modo, item) { 
                    listaCambios[modo] = {};
                     $.each(item, function (sucursal, meses) { 
                          $.each(meses, function (mes, cantidad) {         
                               try {
                                   if (listaCambios[modo][mes] == undefined ) {
                                       listaCambios[modo][mes] = parseInt(cantidad);
                                   }else{
                                        listaCambios[modo][mes] += parseInt(cantidad);
                                   }
                                    
                               }catch( error ){
                                }
                          });
                          
                     });
                     
                });
                
                generadorGraficaBarra(listaCambios.entra,  {
                    element: "cambioAdscripcionLlegada",
                    label: " Entrada de Personal"
                });
                generadorGraficaBarra(listaCambios.sale, {
                    element: "cambioAdscripcionSalida",
                    label: " Salida de  Personal"
                });                
            } else {
                listaCambios = [];
                sucursalSeleccionada = $("#sucursal option:selected").text();
                $.each(data, function (modo, item) { 
                     listaCambios[modo] = {};
                     $.each(item, function (sucursal, meses) { 
                         
                         
                         if ((sucursal == sucursalSeleccionada) || sucursalSeleccionada == 'TODAS' ) {
                             console.log( "poop");
                             
                             console.log(sucursal + "        " + sucursalSeleccionada + "    ");
                             $.each(meses, function (mes, cantidad) {
                                 try {
                                     if (listaCambios[modo][mes] == undefined) {
                                         listaCambios[modo][mes] = parseInt(cantidad);
                                     } else {
                                         listaCambios[modo][mes] += parseInt(cantidad);
                                     }

                                 } catch (error) {
                                 }
                             });
                         }

                     });
                });

                limpiarGraficas('containerCambioAdscripcionSalida', 'cambioAdscripcionSalida');
                limpiarGraficas('containerCambioAdscripcionLlegada', 'cambioAdscripcionLlegada');
                generadorGraficaBarra(listaCambios.entra, {
                    element: "cambioAdscripcionLlegada",
                    label: " Entrada de Personal"
                });
                generadorGraficaBarra(listaCambios.sale, {
                    element: "cambioAdscripcionSalida",
                    label: " Salida de  Personal"
                });       
                console.log(listaCambios.entra);
                
            }
        },
        "json"
    );
}

setGraficasCambiosAdscripcion();

function limpiarGraficas( idElemento, canvasId ) {
    $(`#${idElemento}`).html('');
    $(`#${idElemento}`).html(`<canvas id="${canvasId}"></canvas>`);
}

$.get("/intranet/controladores/nomina/recursos_humanos.php", {
    mes: -1,
    anio:2019,
    opc: 'getRetardos'
},
    function (data, textStatus, jqXHR) {
        generadorGraficaBarra(data, {
                element :"chartRetardos" ,
                label: " Retardos"});
    },
    "json"
);

setAltasBajas("cantidadAltas", {
    element: "chartAltas",
    label: " Altas"
});
setAltasBajas("cantidadBajas", {
    element: "chartBajas",
    label: " Bajas"
});

$.get("/intranet/controladores/nomina/recursos_humanos.php", {
        opc: 'cantidadFaltas',
        mes: -1,
        anio: 2019,
    },
    function (data, textStatus, jqXHR) {
        let faltas;
        $.each(data, function (i, item) { 
             
        });
    },
    "json"
);

function getRandomArbitrary(min, max) {
    let numeroAleatorio = parseInt(Math.random() * (max - min) + min);
    switch ( numeroAleatorio ) {
        case 10:
                numeroAleatorio = 'A';
            break;
        case 11:
            numeroAleatorio = 'B';
            break;
        case 12:
            numeroAleatorio = 'C';
            break;
        case 13:
            numeroAleatorio = 'D';
            break;
        case 14:
            numeroAleatorio = 'E';
            break;
        case 15:
            numeroAleatorio = 'F';
            break;                                                    
        default:
            break;
    }
    return  numeroAleatorio;
}