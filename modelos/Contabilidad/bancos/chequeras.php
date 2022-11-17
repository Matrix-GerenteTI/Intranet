<?php

require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/FirebirDB.php";


class ChequeraBancarias extends FirebirdDB
{
 
    public function getChequerasPagoProveedor( )
    {
        $queryChequeras = "SELECT valor
                                                FROM cfg_valorcamposgenericos 
                                                WHERE nombretabla='GENERICO' AND nombrecampo='CHEQUERA' 
                                                AND fk1mcfg_usuarios=-1 AND modulo='ADM' ";
        return $this->fireSelect( $queryChequeras );
    }

    public function getFacturados( $params)
    {
        extract( $params );

        $queryFacturados = "SELECT *
                                            from CFG_DOCTOS
                                            where  fecha >= '$fechaInicio' AND fecha <= '$fechaFin' and status = 'FACTURA EMITIDO'";
        return $this->fireSelect( $queryFacturados );
    }

}
