<?php

class DB
{
    protected $conexion;

    public function __construct()
    {
        $this->conexion();
    }
    public function conexion(){
        $this->conexion = new mysqli('127.0.0.1','sestrada','M@tr1x2017','dbnomina_pruebas');
        mysqli_query($this->conexion,"SET NAMES 'utf8");
        mysqli_set_charset( $this->conexion ,"utf8");

        if($this->conexion->connect_errno){
            echo "Ocurrió un error por favor vuelva a intentarlo";
        }

        return $this->conexion;
    }

    public function insert($sentence){
        $statusInsert =    $this->conexion->query($sentence);
        // echo $sentence;
        $lastId = $this->conexion->insert_id;
        if ( $lastId > 0 ) {
            return $lastId;
        }
        $insertados = $this->conexion->affected_rows;
        return $insertados;
    }


    public function update($sentence){
        $statusInsert =    $this->conexion()->query($sentence);
        $insertados = $this->conexion->affected_rows;
        return $insertados;
    }


    public function select($sentence){        
        $executeQuery = $this->conexion()->query($sentence);        
        if ( $executeQuery ) {
            if ( mysqli_num_rows($executeQuery) ) {
                $registros = $executeQuery->fetch_all(MYSQLI_ASSOC);
                return $registros;
            } else {
                return array();
            }
        } else {
            return array();
        }        
    }


}

?>