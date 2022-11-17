<?php
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/CuentasPorPagar.php";


class CxPHistorico  
{
    public function getHistorico()
    {
        $modeloCxP = new CuentasPorPagar;

        $listaHistorico = $modeloCxP->getAllPagosAplicados();

        return $listaHistorico;
    }
}


$historico = new CxPHistorico;
$listado = $historico->getHistorico();