<?php

    if(!isset($_SESSION)){ 
        session_start(); 
    }

    require_once 'DB.php';
    require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/Reportes/Resguardos/resguardoCel.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/Reportes/Resguardos/resguardoEquipComp.php";

    class Resguardo extends DB{

        public function parseFechaMysqlFormat($fecha){
            $fechaExplode = explode( '/',$fecha );
            
            return $fechaExplode[2].'-'.$fechaExplode[1]."-".$fechaExplode[0];
        }

        public function gettingResg($paginacion){
            $pagination = isset( $paginacion ) ? " LIMIT ".$paginacion." ,20 "  : "" ;
            $query="SELECT 
                        * 
                    FROM 
                        resguardos 
                    left JOIN 
                        resguardo_details ON resguardo_details.id = resguardos.fkid_detalle_resguardo
                    left JOIN
                        resgequipocomputo_detail ON resgequipocomputo_detail.id = resguardos.fkid_detalle_resguardo
                    ORDER BY 
                        resguardos.id DESC";
            
            return $this->select($query);

        }

        public function gettingResgPags($empleado, $tipoResg, $fechaResg, $paginacion){
            if ( strlen( $fechaResg ) > 0) {
                $fecha = $this->parseFechaMysqlFormat( $fechaResg );
                
            }

            $colita = "";
            $pagination = "";

            if($empleado != "" && $tipoResg == "" && $fechaResg == ""){

                $colita = "WHERE resguardos.nombre_empleado LIKE '%".$empleado."%' " ;

            }else if($empleado == "" && $tipoResg != "" && $fechaResg == ""){

                $colita = "WHERE resguardos.tipo_resg LIKE '%".$tipoResg."%' " ;

            }else if($empleado == "" && $tipoResg == "" && $fechaResg != ""){

                $colita = "WHERE resguardos.fecha = '".$fecha."' " ;

            }else if($empleado != "" && $tipoResg != "" && $fechaResg == ""){

                $colita = "WHERE resguardos.nombre_empleado LIKE '%".$empleado."%' AND resguardos.tipo_resg LIKE '%".$tipoResg."%'" ;

            }else if($empleado == "" && $tipoResg != "" && $fechaResg != ""){

                $colita = "WHERE resguardos.tipo_resg LIKE '%".$tipoResg."%' AND resguardos.fecha = '".$fecha."'" ;

            }else if($empleado != "" && $tipoResg == "" && $fechaResg != ""){

                $colita = "WHERE resguardos.nombre_empleado LIKE '%".$empleado."%' AND resguardos.fecha = '".$fecha."' " ;

            }else if($empleado == "" && $tipoResg == "" && $fechaResg != ""){

                $colita = "";

            }else{

                $colita = "WHERE resguardos.nombre_empleado LIKE '%".$empleado."%' AND resguardos.tipo_resg LIKE '%".$tipoResg."% AND resguardos.fecha = '".$fecha."'";
                
            }
            if(!isset( $paginacion )){
                $pagination= "LIMIT ".$paginacion." ,20 ;" ;
            }
            $query="SELECT * FROM resguardos LEFT JOIN resguardo_details ON resguardos.fkid_detalle_resguardo = resguardo_details.id
                    $colita
				    ORDER BY resguardos.fecha ASC $pagination ;";
            //echo $query;
            return $this->select($query);

        }

        public function gettingResgEmpEquipCel($id){
            $query = "SELECT * FROM resguardos LEFT JOIN resguardo_details ON resguardos.fkid_detalle_resguardo = resguardo_details.id WHERE resguardos.fkid_detalle_resguardo = ".$id.";";
            //echo $query;
            return $this->select($query);
        }

        public function gettingResgEmpEquipComp($id){
            $query = "SELECT * FROM resguardos LEFT JOIN resgequipocomputo_detail ON resguardos.fkid_detalle_resguardo = resgequipocomputo_detail.id 
                    WHERE resguardos.fkid_detalle_resguardo = ".$id." AND resguardos.tipo_resg = 'RESGUARDO_EQUIPO_COMPUTO';";
            //echo $query;
            return $this->select($query);
        }
        
        public function makingNewResg($data, $chks, $tipo_resg){
            if ( strlen( $data->fecha ) > 0) {
                $fecha = $this->parseFechaMysqlFormat( $data->fecha );
                
            }

            if(sizeof($chks)==1){
                $rest_uno = "chk_1";
                $rest_dos = $chks[0];
            }else if(sizeof($chks)==2){
                $rest_uno = "chk_1, chk_2";
                $rest_dos = $chks[0]."','".$chks[1];
            }else if(sizeof($chks)==3){
                $rest_uno = "chk_1, chk_2, chk_3";
                $rest_dos = $chks[0]."','".$chks[1]."','".$chks[2];
            }else if(sizeof($chks)==4){
                $rest_uno = "chk_1, chk_2, chk_3, chk_4";
                $rest_dos = $chks[0]."','".$chks[1]."','".$chks[2]."','".$chks[3];
            }else if(sizeof($chks)==5){
                $rest_uno = "chk_1, chk_2, chk_3, chk_4, chk_5";
                $rest_dos = $chks[0]."','".$chks[1]."','".$chks[2]."','".$chks[3]."','".$chks[4];
            }else if(sizeof($chks)==6){
                $rest_uno = "chk_1, chk_2, chk_3, chk_4, chk_5, chk_6";
                $rest_dos = $chks[0]."','".$chks[1]."','".$chks[2]."','".$chks[3]."','".$chks[4]."','".$chks[5];
            }else if(sizeof($chks)==7){
                $rest_uno = "chk_1, chk_2, chk_3, chk_4, chk_5, chk_6, chk_7";
                $rest_dos = $chks[0]."','".$chks[1]."','".$chks[2]."','".$chks[3]."','".$chks[4]."','".$chks[5]."','".$chks[6];
            }else if(sizeof($chks)==8){
                $rest_uno = "chk_1, chk_2, chk_3, chk_4, chk_5, chk_6, chk_7, chk_8";
                $rest_dos = $chks[0]."','".$chks[1]."','".$chks[2]."','".$chks[3]."','".$chks[4]."','".$chks[5]."','".$chks[6]."','".$chks[7];
            }else if(sizeof($chks)==9){
                $rest_uno = "chk_1, chk_2, chk_3, chk_4, chk_5, chk_6, chk_7, chk_8, chk_9";
                $rest_dos = $chks[0]."','".$chks[1]."','".$chks[2]."','".$chks[3]."','".$chks[4]."','".$chks[5]."','".$chks[6]."','".$chks[7]."','".$chks[8];
            }else if(sizeof($chks)==10){
                $rest_uno = "chk_1, chk_2, chk_3, chk_4, chk_5, chk_6, chk_7, chk_8, chk_9, chk_10";
                $rest_dos = $chks[0]."','".$chks[1]."','".$chks[2]."','".$chks[3]."','".$chks[4]."','".$chks[5]."','".$chks[6]."','".$chks[7]."','".$chks[8]."','".$chks[9];
            }else if(sizeof($chks)==11){
                $rest_uno = "chk_1, chk_2, chk_3, chk_4, chk_5, chk_6, chk_7, chk_8, chk_9, chk_10, chk_11";
                $rest_dos = $chks[0]."','".$chks[1]."','".$chks[2]."','".$chks[3]."','".$chks[4]."','".$chks[5]."','".$chks[6]."','".$chks[7]."','".$chks[8]."','".$chks[9]."','".$chks[10];
            }else if(sizeof($chks)==12){
                $rest_uno = "chk_1, chk_2, chk_3, chk_4, chk_5, chk_6, chk_7, chk_8, chk_9, chk_10, chk_11, chk_12";
                $rest_dos = $chks[0]."','".$chks[1]."','".$chks[2]."','".$chks[3]."','".$chks[4]."','".$chks[5]."','".$chks[6]."','".$chks[7]."','".$chks[8]."','".$chks[9]."','".$chks[10]."','".$chks[11];
            }else if(sizeof($chks)==13){
                $rest_uno = "chk_1, chk_2, chk_3, chk_4, chk_5, chk_6, chk_7, chk_8, chk_9, chk_10, chk_11, chk_12, chk_13";
                $rest_dos = $chks[0]."','".$chks[1]."','".$chks[2]."','".$chks[3]."','".$chks[4]."','".$chks[5]."','".$chks[6]."','".$chks[7]."','".$chks[8]."','".$chks[9]."','".$chks[10]."','".$chks[11]."','".$chks[12];
            }else if(sizeof($chks)==14){
                $rest_uno = "chk_1, chk_2, chk_3, chk_4, chk_5, chk_6, chk_7, chk_8, chk_9, chk_10, chk_11, chk_12, chk_13, chk_14";
                $rest_dos = $chks[0]."','".$chks[1]."','".$chks[2]."','".$chks[3]."','".$chks[4]."','".$chks[5]."','".$chks[6]."','".$chks[7]."','".$chks[8]."','".$chks[9]."','".$chks[10]."','".$chks[11]."','".$chks[12]."','".$chks[13];
            }

            $queryInsert = "INSERT INTO resguardos (empresa, nombre_empleado, area_depto, puesto, entrega_equipo, tipo_resg, fecha) 
                            VALUES ('".$data->empresa."','".$data->nombre."','".$data->areaDepto."','".$data->puesto."','".$data->entrega."','".$tipo_resg."','".$fecha."');";

            
            if($this->insert($queryInsert)){
                
                $querySelect ="SELECT MAX(id) AS id FROM resguardos;";
                
                if($idResg = $this->select($querySelect)){
                    $queryInsert_n2 = "INSERT INTO resguardo_details (modelo_cel, imei_cel, num_cel, compania, uso, $rest_uno, observaciones) 
                                   VALUES ('".$data->modelo."','".$data->imei."','".$data->numTel."','".$data->companiaTel."','".$data->uso."','".$rest_dos."','".$data->observaciones."');";

                    if($this->insert($queryInsert_n2)){

                        $querySelectDetail ="SELECT MAX(id) AS id FROM resguardo_details;";

                        if($idDetalleResg = $this->select($querySelectDetail)){
                            
                            $query = "UPDATE resguardos SET fkid_detalle_resguardo = ".$idDetalleResg[0]["id"]." WHERE id = ".$idResg[0]["id"].";";
                            
                            if($this->update($query)){
                                $pdf = new ResgCel;
                                return["registro"=>$this->select("SELECT * FROM resguardos LEFT JOIN resguardo_details ON resguardos.fkid_detalle_resguardo = resguardo_details.id ORDER BY resguardos.id DESC"),
                                        "queryPDF" => $pdf->generateResgCelPDF($this->select("SELECT * FROM resguardos LEFT JOIN resguardo_details ON resguardos.fkid_detalle_resguardo = resguardo_details.id WHERE resguardos.id = ".$idResg[0]["id"].";"))] ;
                            }
                        }
                    }
                }
            }else{
                return 0;
            }

        }

        public function updatingResg($data, $chks, $tipo_resg){
            var_dump("hola");
            if ( strlen( $data->fecha ) > 0) {
                $fecha = $this->parseFechaMysqlFormat( $data->fecha );
                
            }

            $query_uno = "SELECT * FROM resguardos LEFT JOIN resguardo_details ON resguardos.fkid_detalle_resguardo = resguardo_details.id WHERE resguardos.id = $data->id;";
            
            $res = $this->select($query_uno);

            $aux = [];
            for($i = 0; $i<14; $i = $i+1){
                if($res[0]["chk_".($i+1)] != null){
                    $aux[$i] = $res[0]["chk_".($i+1)];
                }
            }
            
            if($data->empresa != $res[0]["empresa"]){

                $variableSet = "empresa = '".$data->empresa."'";

            }else if ($data->nombre != $res[0]["nombre_empleado"]){

                $variableSet = "nombre_empleado = '".$data->nombre."'";

            }else if($data->areaDepto != $res[0]["area_depto"]){

                $variableSet = "area_depto = '".$data->areaDepto."'";

            }else if($data->puesto != $res[0]["puesto"]){

                $variableSet = "puesto = '".$data->puesto."'";

            }else if($data->entrega != $res[0]["entrega_equipo"]){

                $variableSet = "entrega_equipo = '".$data->entrega."'";

            }else if($tipo_resg != $res[0]["tipo_resg"]){

                $variableSet = "tipo_resg = '".$tipo_resg."'";

            }else if($fecha != $res[0]["fecha"]){

                $variableSet = "fecha = '".$fecha."'";

            }else if($data->empresa != $res[0]["empresa"] && $data->nombre != $res[0]["nombre_empleado"]){

                $variableSet = "empresa = '".$data->empresa."', nombre_empleado = '".$data->nombre."'";
                
            }else if($data->empresa != $res[0]["empresa"] && $data->areaDepto != $res[0]["area_depto"] ){

                $variableSet = "empresa = '".$data->empresa."', area_depto = '".$data->areaDepto."'";

            }else if($data->empresa != $res[0]["empresa"] && $data->puesto != $res[0]["puesto"]){
            
                $variableSet = "empresa = '".$data->empresa."', puesto = '".$data->puesto."'";

            }else if($data->empresa != $res[0]["empresa"] && $data->entrega != $res[0]["entrega_equipo"]){

                $variableSet = "empresa = '".$data->empresa."', entrega_equipo = '".$data->entrega."'";

            }else if($data->empresa != $res[0]["empresa"] && $tipo_resg != $res[0]["tipo_resg"]){

                $variableSet = "empresa = '".$data->empresa."', tipo_resg = '".$tipo_resg."'";

            }else if($data->empresa != $res[0]["empresa"] && $fecha != $res[0]["fecha"]){

                $variableSet = "empresa = '".$data->empresa."', fecha = '".$fecha."'";

            }else if($data->empresa != $res[0]["empresa"] && $data->nombre != $res[0]["nombre_empleado"] && $data->areaDepto != $res[0]["area_depto"]){

                $variableSet = "empresa = '".$data->empresa."',  nombre_empleado = '".$data->nombre."', area_depto = '".$data->areaDepto."'";

            }else if($data->empresa != $res[0]["empresa"] && $data->nombre != $res[0]["nombre_empleado"] && $data->puesto != $res[0]["puesto"]){

                $variableSet = "empresa = '".$data->empresa."',  nombre_empleado = '".$data->nombre."', puesto = '".$data->puesto."'";

            }else if($data->empresa != $res[0]["empresa"] && $data->nombre != $res[0]["nombre_empleado"] && $data->entrega != $res[0]["entrega_equipo"]){

                $variableSet = "empresa = '".$data->empresa."',  nombre_empleado = '".$data->nombre."', entrega_equipo = '".$data->entrega."'";

            }else if($data->empresa != $res[0]["empresa"] && $data->nombre != $res[0]["nombre_empleado"] && $tipo_resg != $res[0]["tipo_resg"]){

                $variableSet = "empresa = '".$data->empresa."',  nombre_empleado = '".$data->nombre."', tipo_resg = '".$tipo_resg."'";

            }else if($data->empresa != $res[0]["empresa"] && $data->nombre != $res[0]["nombre_empleado"] && $fecha != $res[0]["fecha"]){

                $variableSet = "empresa = '".$data->empresa."',  nombre_empleado = '".$data->nombre."', fecha = '".$fecha."'";

            }else{

                $variableSet = "empresa = '".$data->empresa."', nombre_empleado = '".$data->nombre."', area_depto = '".$data->areaDepto."', puesto = '".$data->puesto."', entrega_equipo = '".$data->entrega."', tipo_resg = '".$tipo_resg."', fecha = '".$fecha."'";

            }

            $rest_dos = "";
            $rest_tres = "";

            if(sizeof($aux) != sizeof($chks)){

                if(sizeof($chks)==1){

                    $rest_uno = "chk_1 = '". $chks[0]."', chk_2='', chk_3='', chk_4='', chk_5='', chk_6='', chk_7='', chk_8='', chk_9='', chk_10='', chk_11='', chk_12='', chk_13='', chk_14=''";

                }else if(sizeof($chks)==2){

                    $rest_uno = "chk_1 = '".$chks[0]."', chk_2 = '".$chks[1]."', chk_3='', chk_4='', chk_5='', chk_6='', chk_7='', chk_8='', chk_9='', chk_10='', chk_11='', chk_12='', chk_13='', chk_14=''";

                }else if(sizeof($chks)==3){

                    $rest_uno = "chk_1 ='".$chks[0]."', chk_2 = '".$chks[1]."', chk_3 = '".$chks[2]."', chk_4='', chk_5='', chk_6='', chk_7='', chk_8='', chk_9='', chk_10='', chk_11='', chk_12='', chk_13='', chk_14=''";

                }else if(sizeof($chks)==4){

                    $rest_uno = "chk_1 ='".$chks[0]."', chk_2 = '".$chks[1]."', chk_3 = '".$chks[2]."', chk_4 = '".$chks[3]."', chk_5='', chk_6='', chk_7='', chk_8='', chk_9='', chk_10='', chk_11='', chk_12='', chk_13='', chk_14=''";

                }else if(sizeof($chks)==5){

                    $rest_uno = "chk_1 = '".$chks[0]."', chk_2 ='".$chks[1]."', chk_3 = '".$chks[2]."', chk_4 = '".$chks[3]."', chk_5 = '".$chks[4]."', chk_6='', chk_7='', chk_8='', chk_9='', chk_10='', chk_11='', chk_12='', chk_13='', chk_14=''";

                }else if(sizeof($chks)==6){

                    $rest_uno = "chk_1 ='".$chks[0]."', chk_2 = '".$chks[1]."', chk_3 = '".$chks[2]."', chk_4 = '".$chks[3]."', chk_5 = '".$chks[4]."', chk_6 = '".$chks[5]."', chk_7='', chk_8='', chk_9='', chk_10='', chk_11='', chk_12='', chk_13='', chk_14=''";

                }else if(sizeof($chks)==7){

                    $rest_uno = "chk_1 = '".$chks[0]."', chk_2 = '".$chks[1]."', chk_3 = '".$chks[2]."', chk_4 = '".$chks[3]."', chk_5 = '".$chks[4]."', chk_6 = '".$chks[5]."', chk_7 = '".$chks[6]."', chk_8='', chk_9='', chk_10='', chk_11='', chk_12='', chk_13='', chk_14=''";

                }else if(sizeof($chks)==8){

                    $rest_uno = "chk_1 = '".$chks[0]."', chk_2 = '".$chks[1]."', chk_3 = '".$chks[2]."', chk_4 = '".$chks[3]."', chk_5 = '".$chks[4]."', chk_6 = '".$chks[5]."', chk_7 = '".$chks[6]."', chk_8 = '".$chks[7]."', chk_9='', chk_10='', chk_11='', chk_12='', chk_13='', chk_14=''";

                }else if(sizeof($chks)==9){

                    $rest_uno = "chk_1 = '".$chks[0]."', chk_2 ='".$chks[1]."', chk_3='".$chks[2]."', chk_4='".$chks[3]."', chk_5='".$chks[4]."', chk_6='".$chks[5]."', chk_7='".$chks[6]."', chk_8='".$chks[7]."', chk_9='".$chks[8]."', chk_10='', chk_11='', chk_12='', chk_13='', chk_14=''";

                }else if(sizeof($chks)==10){

                    $rest_uno = "chk_1='".$chks[0]."', chk_2='".$chks[1]."', chk_3='".$chks[2]."', chk_4='".$chks[3]."', chk_5='".$chks[4]."', chk_6='".$chks[5]."', chk_7='".$chks[6]."', chk_8='".$chks[7]."', chk_9='".$chks[8]."', chk_10='".$chks[9]."', chk_11='', chk_12='', chk_13='', chk_14=''";

                }else if(sizeof($chks)==11){

                    $rest_uno = "chk_1='".$chks[0]."', chk_2='".$chks[1]."', chk_3='".$chks[2]."', chk_4='".$chks[3]."', chk_5='".$chks[4]."', chk_6='".$chks[5]."', chk_7='".$chks[6]."', chk_8='".$chks[7]."', chk_9='".$chks[8]."', chk_10='".$chks[9]."', chk_11='".$chks[10]."', chk_12='', chk_13='', chk_14=''";

                }else if(sizeof($chks)==12){

                    $rest_uno = "chk_1='".$chks[0]."', chk_2='".$chks[1]."', chk_3='".$chks[2]."', chk_4='".$chks[3]."', chk_5='".$chks[4]."', chk_6='".$chks[5]."', chk_7='".$chks[6]."', chk_8='".$chks[7]."', chk_9='".$chks[8]."', chk_10='".$chks[9]."', chk_11='".$chks[10]."', chk_12='".$chks[11]."', chk_13='', chk_14=''";

                }else if(sizeof($chks)==13){

                    $rest_uno = "chk_1='".$chks[0]."', chk_2='".$chks[1]."', chk_3='".$chks[2]."', chk_4='".$chks[3]."', chk_5='".$chks[4]."', chk_6='".$chks[5]."', chk_7='".$chks[6]."', chk_8='".$chks[7]."', chk_9='".$chks[8]."', chk_10='".$chks[9]."', chk_11='".$chks[10]."', chk_12='".$chks[11]."', chk_13='".$chks[12]."', chk_14=''";

                }else if(sizeof($chks)==14){

                    $rest_uno = "chk_1='".$chks[0]."', chk_2='".$chks[1]."', chk_3='".$chks[2]."', chk_4='".$chks[3]."', chk_5='".$chks[4]."', chk_6='".$chks[5]."', chk_7='".$chks[6]."', chk_8='".$chks[7]."', chk_9='".$chks[8]."', chk_10='".$chks[9]."', chk_11='".$chks[10]."', chk_12='".$chks[11]."', chk_13='".$chks[12]."', chk_14='".$chks[13]."'";

                }
            }else{
                $rest_uno = "";
            }
            if($data->observaciones != $res[0]["observaciones"]){
                $rest_dos = "observaciones = '".$data->observaciones."'";
            }
            if($data->uso != $res[0]["uso"]){
                $rest_tres = ",uso = '".$data->uso."',";
            }
            $pdf = new ResgCel;
            if($rest_uno == ""){
                
                
                $queryUpdate = "UPDATE resguardos SET $variableSet WHERE resguardos.id = $data->id; ";
                
                $exe = $this->update($queryUpdate);

                if($exe == 1 || $exe == 0){
                    
                    $query = "SELECT * FROM resguardos LEFT JOIN resguardo_details ON resguardos.fkid_detalle_resguardo = resguardo_details.id WHERE resguardos.id = $data->id;";
                    if($resguardo = $this->select($query)){
                        
                        echo $pdf->generateResgCelPDF($resguardo);

                    }else{
                        echo "algo salió mal";
                    }
                }
            }else{
                
                $queryUpdate = "UPDATE resguardos SET $variableSet WHERE resguardos.id = $data->id; ";
                
                $exe = $this->update($queryUpdate);
                
                if($exe == 1 || $exe == 0){

                    $resguardo = "SELECT * FROM resguardos LEFT JOIN resguardo_details ON resguardos.fkid_detalle_resguardo = resguardo_details.id WHERE resguardos.id = $data->id;";
                    
                    if($exe = $this->select($resguardo)){
                        
                        echo $pdf->generateResgCelPDF($exe);
                    }
                }
            }
        }

        public function makingNewResgEquipoComputo($data, $tipo_resg){
            
            if ( strlen( $data->fecha ) > 0) {
                $fecha = $this->parseFechaMysqlFormat( $data->fecha );
                
            }

            $queryInsert = "INSERT INTO resguardos (empresa, sucursal, nombre_empleado, area_depto, puesto, entrega_equipo, tipo_resg, fecha) 
                            VALUES ('".$data->empresa."','".$data->sucursal."','".$data->usuario."','".$data->areaDepto."','".$data->puesto."',
                            '".$data->entrega."','".$tipo_resg."','".$fecha."');";

            
            if($this->insert($queryInsert)){
                
                $querySelect ="SELECT MAX(id) AS id FROM resguardos;";
                
                if($idResg = $this->select($querySelect)){
                    

                    $queryInsert_n2 = "INSERT INTO resgequipocomputo_detail (numResguardo, num_cel_emp, tipo_equipo, marca, modelo, dd_gb, ram_gb, procesador, ns_equipo, so,
                    licencia, monitor, ns_monitor, teclado, ns_teclado, mouse, ns_mouse, cargador, ns_cargador, impresora, ns_impresora,
                    no_brake, bocina, dvd_cd, observaciones)
                    VALUES ('".$data->equipoNo."','".$data->numTel."','".$data->tipoEquipo."','".$data->marca."','".$data->modelo."','".$data->ddGb."','".$data->ramGb."','".$data->procesador."',
                    '".$data->nsEquipo."','".$data->so."','".$data->licencia."','".$data->monitor."','".$data->nsMonitor."','".$data->teclado."',
                    '".$data->nsTeclado."','".$data->mouse."','".$data->nsMouse."','".$data->cargador."','".$data->nsCargador."','".$data->impresora."',
                    '".$data->nsImpresora."','".$data->noBrake."','".$data->bocina."','".$data->dvdCd."','".$data->observaciones."');";

                    
                    if($this->insert($queryInsert_n2)){
                        
                        $idResgDetail ="SELECT MAX(id) AS id FROM resgequipocomputo_detail;";

                        if($idDetalleResg = $this->select($idResgDetail)){
                            
                            $query = "UPDATE resguardos SET fkid_detalle_resguardo = ".$idDetalleResg[0]["id"]." WHERE id = ".$idResg[0]["id"].";";
                            
                            if($this->update($query)){
                                
                                $pdf = new ResgEquipComp;
                                return ["registro"=>$this->select("SELECT * FROM resguardos LEFT JOIN resguardo_details ON resguardos.fkid_detalle_resguardo = resguardo_details.id LEFT JOIN resgequipocomputo_detail ON resguardos.fkid_detalle_resguardo = resgequipocomputo_detail.id ORDER BY resguardos.id DESC ;"),
                                        "queryPDF" => $pdf->generateResgEquipCompPDF($this->select("SELECT * FROM resguardos LEFT JOIN resgequipocomputo_detail ON resguardos.fkid_detalle_resguardo = resgequipocomputo_detail.id WHERE resguardos.id = ".$idResg[0]["id"]." AND resguardos.tipo_resg = 'RESGUARDO_EQUIPO_COMPUTO';"))] ;
                            }
                        }
                    }
                }
            }else{
                return 0;
            }

        }

        public function updatingResgEquipComp($data){
            //var_dump($data);
            //var_dump("SELECT * FROM resguardos LEFT JOIN resgequipocomputo_detail ON resguardos.fkid_detalle_resguardo = resgequipocomputo_detail.id WHERE resguardos.id = ".$data[0].";");
            $pdf = new ResgEquipComp;
            $data = $this->select("SELECT * FROM 
                resguardos LEFT JOIN resgequipocomputo_detail 
                ON resguardos.fkid_detalle_resguardo = resgequipocomputo_detail.id 
                WHERE resguardos.id = ".$data[0].";");
            
            echo $pdf->generateResgEquipCompPDF();
        }
    }