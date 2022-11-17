<?php
require_once($_SERVER['DOCUMENT_ROOT']."/intranet/modelos/quizz.php");

class QuizzController{

    function getPreguntas($idusuario){
        $quizz = new Quizz;

        $preguntas = $quizz->getPreguntas($idusuario);
        
        echo json_encode($preguntas);
    }
}

?>