<?php
require_once $_SERVER['DOCUMENT_ROOT']."C:\\wamp\\www\\intranet\\modelos\\MySQLPolitica.php";

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

