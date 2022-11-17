<?php

    if(!isset($_SESSION)){ 
        session_start(); 
    }

    require_once 'DB.php';

    class AleatorioExcepcion extends DB{

        public function parseFechaMysqlFormat($fecha){
            $fechaExplode = explode( '/',$fecha );
            
            return $fechaExplode[2].'-'.$fechaExplode[1]."-".$fechaExplode[0];
        }
        
        public function getAllRandom(){
            //$pagination = isset( $paginacion ) ? " LIMIT ".$paginacion." ,20 "  : "" ;
            $queryExcepcions="SELECT 
                        * 
                    FROM 
                        inventario_excepciones 
                    LEFT JOIN 
                        csucursal 
                    ON 
                        inventario_excepciones.almacen = csucursal.idprediction 
                    WHERE 
                        csucursal.status = 1 
                    ORDER BY inventario_excepciones.id DESC; ";

            $queryUDNs = "SELECT * FROM csucursal WHERE STATUS = 1 AND csucursal.descripcion NOT IN ( 'CEDIM CENTRAL' ,'CEDIM BOULEVARD' ,'CORPORATIVO', 'VENTAS ONLINE');";

            return ["historialExcepciones"=>$this->select($queryExcepcions),
            "udns" =>$this->select($queryUDNs)] ;

        }

        public function saveAleatorio($udn, $razon, $fecha){

            $fecha = $this->parseFechaMysqlFormat( $fecha );
            if($udn == 0){
                $queryUDNs = "SELECT * FROM csucursal WHERE STATUS = 1 AND csucursal.descripcion 
                          NOT IN ( 'CEDIM CENTRAL' ,'CEDIM BOULEVARD' ,'CORPORATIVO', 'VENTAS ONLINE');";

                if($resUdn = $this->select($queryUDNs)){
                    $queryInsert = "INSERT INTO inventario_excepciones ( almacen, fecha, causa, status) 
                    VALUES ";
                    
                    for($i = 0; $i < sizeof($resUdn); $i = $i + 1){
                        $idPrediction = (int)$resUdn[$i]["idprediction"];
                        
                        $queryInsert .= "('$idPrediction', '".$fecha."','".$razon."', 1),";
                    }
                    
                    $queryInsert = substr($queryInsert, 0, -1);

                    if($this->insert($queryInsert)!= -1){
                        $queryExcepcions="SELECT 
                                * 
                            FROM 
                                inventario_excepciones 
                            LEFT JOIN 
                                csucursal 
                            ON 
                                inventario_excepciones.almacen = csucursal.idprediction 
                            WHERE 
                                csucursal.status = 1 
                            ORDER BY inventario_excepciones.id DESC; ";

                        return $this->select($queryExcepcions);
                    }
                }else{
                    return "Error en consultar sucursales";
                }
            }else{
                $queryInsert = "INSERT INTO inventario_excepciones ( almacen, fecha, causa, status) 
                                    VALUES (".$udn.", '".$fecha."','".$razon."', 1);";
                if($this->insert($queryInsert)!= -1){
                    $queryExcepcions="SELECT 
                            * 
                        FROM 
                            inventario_excepciones 
                        LEFT JOIN 
                            csucursal 
                        ON 
                            inventario_excepciones.almacen = csucursal.idprediction 
                        WHERE 
                            csucursal.status = 1 
                        ORDER BY inventario_excepciones.id DESC; ";

                    return $this->select($queryExcepcions);
                }
            }
        }
    }