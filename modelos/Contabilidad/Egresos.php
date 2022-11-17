<?php

namespace apps\calendario;

require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/DB.php";


class Egresos extends \DB 
{
    
    public function getProgramacionPagos( $anio )
    {
        $queryProgramacion = "SELECT * FROM cal_pagos_app WHERE YEAR(fecha_evento) like '$anio'  ORDER BY fecha_evento desc";


        return $this->select( $queryProgramacion );
    }
}
