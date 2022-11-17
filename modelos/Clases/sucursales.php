<?php

require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/DB.php";

class Sucursales extends DB
{
    public function getSucursales( $sucursal = '%' )
    {
        $querySucursal = "SELECT * FROM csucursal WHERE status = 1 and ( id like '$sucursal'  OR idprediction like '$sucursal' )";

        return $this->select( $querySucursal);
    }

    public function getDepartamentos( $departamento = '%' )
    {
        $queryDepartamento = "SELECT * FROM cdepartamento WHERE status = 1 and id like '$departamento'";

        return $this->select( $queryDepartamento);
    }
}
