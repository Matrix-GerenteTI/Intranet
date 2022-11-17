<?php
require_once  $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/sesiones.php";

class UsuarioController  
{
    protected $sesiones;

    public function __construct()
    {
        $this->sesiones = new Sesiones;
    }

    public function getUser( $user , $pass)
    {
        return $this->sesiones->getUser( $user , $pass );
    }
}
