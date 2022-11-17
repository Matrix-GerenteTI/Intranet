<?php

require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/empresa/permisos.php";

ini_set('precision', 10);
ini_set('serialize_precision', 10);

class PermisosController  
{
    protected $modeloPermisos;

    public function __construct()
    {
        $this->modeloPermisos = new Permisos;
    }
    
    public function getAppMenu( $nivel, $vista){
        $listaMenu = $this->modeloPermisos->getListaMenu($nivel, $vista);
        return $listaMenu;
    }
}
?>