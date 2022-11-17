<?php

require_once($_SERVER['DOCUMENT_ROOT']."/intranet/modelos/colaboradores.php");

class ColaboradoresController{

    function index(){
        $colaborades = new Colaboradores;

        $bajas = $colaborades->getColaboradoresMes();
        
        echo json_encode($bajas);
    }
}