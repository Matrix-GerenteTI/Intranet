<?php

    if(!isset($_SESSION)){ 
        session_start(); 
    }

    require_once 'DB.php';

    class Acreedores extends DB{

        public function parseFechaMysqlFormat($fecha){
            $fechaExplode = explode( '/',$fecha );
            
            return $fechaExplode[2].'-'.$fechaExplode[1]."-".$fechaExplode[0];
        }
        
        public function overloadCreditors($data){

            /* Se procede a llenar la tabla lista_acreedores */
            $queryInsert_n1 = "INSERT INTO lista_acreedores (nombre_entidad, alias) VALUES ('".$data->acreedor."','".$data->aliasAcreedor."');";
            
            if($this->insert($queryInsert_n1)){

                /* Se obtiene el id del registro creado con anterioridad */
                $querySelect_n1 ="SELECT MAX(id) AS id FROM lista_acreedores;";
                if($idAcreedor = $this->select($querySelect_n1)){
                    
                    /* Se procede a crear los detalles de la deuda */                    
                    $queryInsert_n2 = "INSERT INTO 
                                        detalles_deuda_acreedor (id_acreedor, monto_total_deuda, plazo_deuda, interes_deuda, restante_deuda, fecha_deuda_gen) 
                                       VALUES ('".$idAcreedor[0]["id"]."','".$data->monto."','".$data->plazo."','".$data->interes."','".$data->monto."','".$data->fecha."');
                                      ";
                    if($this->insert($queryInsert_n2)){

                        $querySelect_n2 ="SELECT MAX(id) AS id FROM detalles_deuda_acreedor;";
                        if($idDetalleDeuda = $this->select($querySelect_n2)){

                            /* Se procede relacion deuda-acreedor */
                            $queryInsert_n3 = "INSERT INTO listado_deudas_acreedores (id_acreedor, id_detalle_deuda) VALUES ('".$idAcreedor[0]["id"]."','".$idDetalleDeuda[0]["id"]."');";
                            if($this->insert($queryInsert_n3)){

                                $query = "  SELECT * FROM lista_acreedores la
                                            LEFT JOIN listado_deudas_acreedores lda ON la.id = lda.id_acreedor 
                                            LEFT JOIN detalles_deuda_acreedor dda ON lda.id_acreedor = dda.id_acreedor
                                            WHERE dda.liquidado = 0;
                                        ";
                                return $this->select($query);
                            }
                        }                        
                    }
                }
            }else{
                return 0;
            }
        }

        public function creditors(){
            $query = "  SELECT * FROM lista_acreedores la
                        LEFT JOIN listado_deudas_acreedores lda ON la.id = lda.id_acreedor 
                        LEFT JOIN detalles_deuda_acreedor dda ON lda.id_acreedor = dda.id_acreedor
                        WHERE dda.liquidado = 0;
                    ";
            return $this->select($query);
        }

        public function historialDetallePagos($id){
            $query = "SELECT * FROM  abonos_deuda_acreedor WHERE abonos_deuda_acreedor.detalle_deuda_acreedor = $id;";
            return $this->select($query);
        }

        public function payMountTo($data){
            
            $querySelect = "SELECT * FROM detalles_deuda_acreedor 
                            LEFT JOIN abonos_deuda_acreedor ON abonos_deuda_acreedor.id = detalles_deuda_acreedor.id_num_abono_deuda WHERE detalles_deuda_acreedor.id = $data->id;";

            if($datosDetallePago = $this->select($querySelect)){

                $restante = $datosDetallePago[0]["restante_deuda"] - $data->montoAplicado;

                if ( strlen( $data->fechaAplicacion ) > 0) {

                    $fecha = $this->parseFechaMysqlFormat( $data->fechaAplicacion );
                    
                } 

                $query = "INSERT INTO abonos_deuda_acreedor (detalle_deuda_acreedor, monto_abonado_capital, interes_pagado, fecha_abono) 
                          VALUES (".$data->id.",".$data->montoAplicado.",".$data->interesGenerado.",'".$fecha."');";
                
                if($this->insert($query)){
                
                    $query ="SELECT MAX(id) AS id FROM abonos_deuda_acreedor;";
                
                    if($idAbono = $this->select($query)){
                
                        $query = "UPDATE detalles_deuda_acreedor SET id_num_abono_deuda = ".$idAbono[0]["id"].", restante_deuda = ".$restante." WHERE detalles_deuda_acreedor.id = $data->id;";
                        if($this->update($query)==1){
                            if($data->ok == 1){
                                $query = "UPDATE detalles_deuda_acreedor SET liquidado = 1 WHERE detalles_deuda_acreedor.id = $data->id;";
                                return "done";
                            }else{
                                return 1;
                            }
                        }
                        
                    }   
                }
            }
        }

        public function setCreditDown($id){
            $query = "UPDATE detalles_deuda_acreedor SET liquidado = 1 WHERE id_acreedor = $id;";
            if($this->update($query) == 1){
                $query = "  SELECT * FROM lista_acreedores la
                        LEFT JOIN listado_deudas_acreedores lda ON la.id = lda.id_acreedor 
                        LEFT JOIN detalles_deuda_acreedor dda ON lda.id_acreedor = dda.id_acreedor
                        WHERE dda.liquidado = 0;
                    ";
                return $this->select($query);
            }
            
        }
    }