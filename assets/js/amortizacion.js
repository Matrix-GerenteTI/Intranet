$.post("controladores/con_egresos.php?opc=cmbCuenta", {},
    function (data, textStatus, jqXHR) {
       
        $.each(data, function (i, value) { 
             let attribute = ""
            if( i == 0){
                attribute = "selected"
            }
            str = data[i].nombre;
            str = str.substring(0, 7);
            if (str == 'CREDITO' || data[i].nombre == 'RENTAS') {
                $("#tabla-amortizacion").append(`<option value=${data[i].id} ${attribute}>${data[i].nombre}</option>`)
            }
        });
    },
    "json"
);