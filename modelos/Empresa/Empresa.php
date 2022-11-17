<?php
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/DB.php";

class Empresa extends DB
{
    public function getAllDepartamentos()
    {
        $queryDepartamentos = "SELECT * FROM cdepartamento WHERE status = 1 ";

        return $this->select( $queryDepartamentos );
    }

    public function getDepartamentoDelPuesto( $id)
    {
        $queryDepartamento = "SELECT *,cpuesto.descripcion as puesto
                                                    FROM cdepartamento
                                                    INNER JOIN cpuesto ON cpuesto.iddepartamento = cdepartamento.id
                                                    WHERE cpuesto.id = $id ";
        return $this->select( $queryDepartamento );
    }
    
    public function getAllPuestos()
    {
        $queryPuestos = "SELECT * FROM cpuesto WHERE status = 1";
        return $this->select( $queryPuestos );
    }

    public function getSucursal( $sucursal )
    {
        $querySucursal = "SELECT * FROM csucursal WHERE id = $sucursal ";

        return $this->select($querySucursal );
    }
}
