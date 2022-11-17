<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/intranet/modelos/DB.php';

class Permisos extends DB
{
    // private $conexion;

    
    public function getListaMenu( $nivel, $vista)
    {

        $queryMenus = "SELECT * FROM pappmenus WHERE grupo>=$nivel AND vista='".$vista."' AND status=1";
        //return $queryMenus;
        return $this->select( $queryMenus );
    }
}
