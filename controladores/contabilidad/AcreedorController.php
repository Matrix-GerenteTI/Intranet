<?php

require_once($_SERVER['DOCUMENT_ROOT']."/intranet/modelos/acreedores.php");

class AcreedorController{

    function creditorsProcess($acreedor, $aliasAcreedor, $monto, $plazo, $interes, $fecha){
        
        $data = new stdClass();
        $data->acreedor = $acreedor;
        $data->aliasAcreedor = $aliasAcreedor;
        $data->monto = $monto;
        $data->plazo = $plazo;
        $data->interes = $interes;
        $data->fecha = $fecha;

        $acreedor = new Acreedores;

        $arreglo = $acreedor->overloadCreditors($data);
        
        echo json_encode($arreglo);
    }

    function creditors(){
        $acreedor = new Acreedores;

        $arreglo = $acreedor->creditors();
        
        echo json_encode($arreglo);
    }

    function detallePagos($id){
        $acreedor = new Acreedores;

        $arreglo = $acreedor->historialDetallePagos($id);
        
        echo json_encode($arreglo);
    }

    function payTo( $montoAplicado, $interesGenerado, $fechaAplicacion, $id, $ok ){
        $data = new stdClass();
        $data->montoAplicado = $montoAplicado;
        $data->interesGenerado = $interesGenerado;
        $data->fechaAplicacion = $fechaAplicacion;
        $data->id = $id;
        $data->ok = $ok;

        $acreedor = new Acreedores;

        $arreglo = $acreedor->payMountTo($data);
        
        echo json_encode($arreglo);
    }

    function down( $id ){
        $acreedor = new Acreedores;

        $arreglo = $acreedor->setCreditDown($id);
        
        echo json_encode($arreglo);
    }
    

}