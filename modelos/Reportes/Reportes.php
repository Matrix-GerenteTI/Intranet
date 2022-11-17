<?php
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/MySQL.php";

class Reportes extends CMySQLi
{
    public function getReporte( $reporteName)
    {
        $queryProductos = "SELECT * FROM preportes WHERE nombre='".$reporteName."'";
            return $this->select($queryProductos);
    }

    public function getDetalleReporte( $id)
    {
            $queryProductos = "SELECT * FROM preportes WHERE  id = $id ";
            return $this->select($queryProductos);
    }

}

