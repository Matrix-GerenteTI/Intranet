<?php

require_once($_SERVER['DOCUMENT_ROOT']."/intranet/modelos/con_egresos.php");

class ControllerEgresos{

    function getPagosProgramados($fechaI, $fechaF, $pagination){
        $egresos = new Egresos;
        $arreglo = $egresos->getHistorialPagos($fechaI, $fechaF, $pagination);
        
        echo json_encode($arreglo);
    }
    

}