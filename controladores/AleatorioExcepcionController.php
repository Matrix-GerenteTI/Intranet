<?php

require_once($_SERVER['DOCUMENT_ROOT']."/intranet/modelos/AleatorioExcepcion.php");

class AleatorioExcepcionController{

    function getAllExceptios($data){
        $aleatorios = new AleatorioExcepcion;

        $AleatorioExcepcion = $aleatorios->getAllRandom();
        
        echo json_encode($AleatorioExcepcion);
    }

    function saveNewException($udn, $razon, $fecha){
        
        $aleatorios = new AleatorioExcepcion;

        $AleatorioExcepcion = $aleatorios->saveAleatorio($udn, $razon, $fecha);
        
        echo json_encode($AleatorioExcepcion);
    }

}