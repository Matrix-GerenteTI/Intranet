<?php

    if(!isset($_SESSION)){ 
        session_start(); 
    }

    require_once 'DB.php';

    class Quizz extends DB{

        public function getPreguntas($idusuario){
            $query = "";
            return $this->select($query);
        }
    }