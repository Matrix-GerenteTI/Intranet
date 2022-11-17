<?php

require_once($_SERVER['DOCUMENT_ROOT']."/intranet/modelos/resguardo.php");

class ResguardoController{

    function getResg($paginacion){
        $resguardo = new Resguardo;

        $Resguardo = $resguardo->gettingResg($paginacion);
        
        echo json_encode($Resguardo);
    }

    function getResgPags($empleado, $tipoResg, $fechaResg, $pagination){
        
        $resguardo = new Resguardo;

        $Resguardo = $resguardo->gettingResgPags($empleado, $tipoResg, $fechaResg, $pagination);
        
        echo json_encode($Resguardo);
    }

    function getResgEmp($tipoResg, $id){
        $resguardo = new Resguardo;
        if($tipoResg == "RESGUARDO_CELULARES"){
            $Resguardo = $resguardo->gettingResgEmpEquipCel($id);
        }else if($tipoResg == "RESGUARDO_EQUIPO_COMPUTO"){
            $Resguardo = $resguardo->gettingResgEmpEquipComp($id);
        }            
        
        echo json_encode($Resguardo);
    }

    function saveResg($data, $chks, $tipo_resg){

        $datos = new stdClass();
        $datos->fecha = $data[0];
        $datos->uso = $data[1];
        $datos->empresa = $data[2];
        $datos->areaDepto = $data[3];
        $datos->companiaTel = $data[4];
        $datos->nombre = $data[5];
        $datos->puesto = $data[6];
        $datos->numTel = $data[7];
        $datos->modelo = $data[8];
        $datos->imei = $data[9];
        $datos->observaciones = $data[10];
        $datos->recibe = $data[11];
        $datos->entrega = $data[12];

        $resguardo = new Resguardo;

        $makeResguardo = $resguardo->makingNewResg($datos, $chks, $tipo_resg);
        
        echo json_encode($makeResguardo);
    }

    function updateResg($data, $chks, $tipo_resg){

        $datos = new stdClass();
        $datos->fecha = $data[0];
        $datos->uso = $data[1];
        $datos->empresa = $data[2];
        $datos->areaDepto = $data[3];
        $datos->companiaTel = $data[4];
        $datos->nombre = $data[5];
        $datos->puesto = $data[6];
        $datos->numTel = $data[7];
        $datos->modelo = $data[8];
        $datos->imei = $data[9];
        $datos->observaciones = $data[10];
        $datos->recibe = $data[11];
        $datos->entrega = $data[12];
        $datos->id = $data[14];

        $resguardo = new Resguardo;

        $makeResguardo = $resguardo->updatingResg($datos, $chks, $tipo_resg);
        
        json_encode($makeResguardo);
    }

    function saveResgEquipoComputo($data, $tipo_resg){
        $datos = new stdClass();
        
        $datos->equipoNo = $data[0];
        $datos->fecha = $data[1];
        $datos->usuario = $data[2];
        $datos->empresa = $data[3];
        $datos->areaDepto = $data[4];
        $datos->puesto = $data[5];
        $datos->sucursal = $data[6];
        $datos->numTel = $data[7];
        $datos->tipoEquipo = $data[8];
        $datos->marca = $data[9];
        $datos->modelo = $data[10];
        $datos->ddGb = $data[11];
        $datos->ramGb = $data[12];
        $datos->procesador = $data[13];
        $datos->nsEquipo = $data[14];
        $datos->so = $data[15];
        $datos->licencia = $data[16];
        $datos->monitor = $data[17];
        $datos->nsMonitor = $data[18];
        $datos->teclado = $data[19];
        $datos->nsTeclado = $data[20];
        $datos->mouse = $data[21];
        $datos->nsMouse = $data[22];
        $datos->cargador = $data[23];
        $datos->nsCargador = $data[24];
        $datos->impresora = $data[25];
        $datos->nsImpresora = $data[26];
        $datos->noBrake = $data[27];
        $datos->bocina = $data[28];
        $datos->dvdCd = $data[29];
        $datos->observaciones = $data[30];
        $datos->entrega = $data[32];

        $resguardo = new Resguardo;

        $makeResguardo = $resguardo->makingNewResgEquipoComputo($datos, $tipo_resg);
        
        echo json_encode($makeResguardo);
    }

    function updateResgEquipoComputo($data){

        $resguardo = new Resguardo;

        $updateResgEquipComp = $resguardo->updatingResgEquipComp($data);

        echo json_encode($updateResgEquipComp);
    }
}