<?php

class DB
{
    protected $conexion;
    protected $conexionnew;

    public function __construct()
    {
        $this->conexion();
        $this->conexionnew();
    }
    public function conexion(){
        $this->conexion = new mysqli('127.0.0.1','sestrada','M@tr1x2017','dbnomina');
        mysqli_query($this->conexion,"SET NAMES 'utf8");
        mysqli_set_charset( $this->conexion ,"utf8");

        if($this->conexion->connect_errno){
            echo "Ocurrió un error por favor vuelva a intentarlo";
        }

        return $this->conexion;
    }

    public function conexionnew(){
        $this->conexionnew = new mysqli('127.0.0.1','sestrada','M@tr1x2017','sitexcloud');
        mysqli_query($this->conexionnew,"SET NAMES 'utf8");
        mysqli_set_charset( $this->conexionnew ,"utf8");

        if($this->conexionnew->connect_errno){
            echo "Ocurrió un error por favor vuelva a intentarlo";
        }

        return $this->conexionnew;
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

    public function insertnew($sentence){
        $statusInsert =    $this->conexionnew->query($sentence);
        // echo $sentence;
        $lastId = $this->conexionnew->insert_id;
        if ( $lastId > 0 ) {
            return $lastId;
        }
        $insertados = $this->conexionnew->affected_rows;
        return $insertados;
    }


    public function update($sentence){
        $statusInsert =    $this->conexion()->query($sentence);
        $insertados = $this->conexion->affected_rows;
        return $insertados;
    }

    public function updatenew($sentence){
        $statusInsert =    $this->conexionnew()->query($sentence);
        $insertados = $this->conexionnew->affected_rows;
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

    public function selectnew($sentence){        
        $executeQuery = $this->conexionnew()->query($sentence);        
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