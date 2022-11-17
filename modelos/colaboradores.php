<?php

    if(!isset($_SESSION)){ 
        session_start(); 
    }

    require_once 'DB.php';

    class Colaboradores extends DB{

        public function getColaboradoresMes(){
            $dia = date("d");
            $mes = date("n");
            $año = date("Y");

            $query="SELECT 
                        pe.nombre AS nombre,
                        pe.fecha_baja AS baja
                    FROM pempleado as pe
                    INNER JOIN pdireccion as pd
                    ON pe.nip = pd.nip 
                    WHERE MONTH(pe.fecha_baja) = $mes;";
                                
            return $this->select($query);
        }
    }