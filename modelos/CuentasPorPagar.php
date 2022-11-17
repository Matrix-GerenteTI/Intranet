<?php
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/FirebirDB.php";

class CuentasPorPagar 
{
    protected $conexionFirebird;

    public function __construct()
    {
        $this->conexionFirebird = new FirebirdDB;
    }

    public function getFacturasPorSaldar( $provedorId = "%")
    {
        $aProveedor = $provedorId != '%' ? "AND  H.fk1mcrm_prospectos=".$provedorId : '';
        $queryFacturas = "SELECT C.id_prospecto AS CODIGO, ' ' AS APELLIDO1,R.NUMFACT,C.pym_nombre AS CF1,R.ORIGEN,R.FECHAEMISION,R.FECHAVTO,
                                                    R.IMPORTE,C.id_prospecto AS ID,
                                                    R.TIPOCLIENTE,R.NUMERO,R.NUMANTICIPO 
                                        From (cxp_recibos R 
                                        INNER JOIN cxp_historcp H on R.imp_factu_historcp=H.id 
                                        INNER JOIN crm_prospectos C on H.fk1mcrm_prospectos=C.id_prospecto) 
                                        Where (H.documentado='S' and R.rec_pagado='F' or (R.rec_pagado='T' 
                                        and R.numero='ANTICIPO' and not R.tipocobro='CANCELADO'))  $aProveedor";

        // $queryFacturas = "SELECT C.id_prospecto AS CODIGO, ' ' AS APELLIDO1,R.NUMFACT,C.pym_nombre AS CF1,R.ORIGEN,R.FECHAEMISION,R.FECHAVTO,
        //                                             R.IMPORTE,C.id_prospecto AS ID,
        //                                             R.TIPOCLIENTE,R.NUMERO,R.NUMANTICIPO, REC_PAGADO ,R.NUMERO, R.TIPOCOBRO
        //                                 From (cxp_recibos R 
        //                                 INNER JOIN cxp_historcp H on R.imp_factu_historcp=H.id 
        //                                 INNER JOIN crm_prospectos C on H.fk1mcrm_prospectos=C.id_prospecto) 
        //                                 Where (H.documentado='S' or ( not R.tipocobro='CANCELADO')) and 1=1 
        //                                 Order by C.id_prospecto,R.tipocliente,R.fechaemision";
        return $this->conexionFirebird->fireSelect( $queryFacturas );
    }

    

    public function getPagosAProveedores($mes , $anio )
    {
        $queryPagos = "SELECT * 
                                    FROM CXP
                                    WHERE EXTRACT(MONTH FROM FECHAMOVI) = $mes AND EXTRACT(YEAR FROM FECHAMOVI) = $anio";
                                    
        return $this->conexionFirebird->fireSelect( $queryPagos );
    }

    public function getTodasFacturas( )
    {
        $queryFacturas = "SELECT CXP_RECIBOS.numero,CXP_RECIBOS.FECHAVTO,CXP_RECIBOS.FECHAEMISION,CXP_RECIBOS.IMPORTE,CXP_RECIBOS.REC_PAGADO,CXP_RECIBOS.NUMFACT,
                                        CXP_RECIBOS.IMP_FACTU_HISTORCP,CXP_RECIBOS.IMP_COBRO_HISTORCP
                        
                        from CXP_RECIBOS
                        
                        WHERE (   CXP_RECIBOS.TIPOCOBRO != 'CANCELADO' )   AND CXP_RECIBOS.NUMFACT  NOT IN ( '','-1')

                        ";
                /*
                 UNION 

                        SELECT CXP_RECIBOS.numero,CXP_RECIBOS.FECHAVTO,CXP_RECIBOS.FECHAEMISION,CXP_RECIBOS.IMPORTE,CXP_RECIBOS.REC_PAGADO,CXP_RECIBOS.NUMFACT,
                        HCXP.fechamovi,HCXP.concepto,HCXP.numdocumento as ndocumento,HCXP.importefactu,HCXP.importecobro,HCXP.fk1mcrm_prospectos
                        from CXP_RECIBOS
                        RIGHT JOIN CXP_HISTORCP AS HCXP ON HCXP.NUMDOCUMENTO = CXP_RECIBOS.NUMERO
                        WHERE ( CXP_RECIBOS.TIPOCOBRO != 'CANCELADO' )   AND CXP_RECIBOS.NUMFACT != ''*/

        return $this->conexionFirebird->fireSelect( $queryFacturas );
    }

    public function getPagosAplicacdos( $factura, $facturaCobro )
    {
 
        $queryFacturasAbonos = " SELECT 
                        HCXP.fechamovi,cast( HCXP.concepto as char(255) character set UTF8) as concepto ,cast( HCXP.numdocumento as char(255)  character set UTF8 ) as ndocumento,HCXP.importefactu,HCXP.importecobro,HCXP.fk1mcrm_prospectos,CRM_PROSPECTOS.PYM_NOMBRE
                        FROM CXP_HISTORCP as HCXP
                        INNER JOIN CRM_PROSPECTOS ON CRM_PROSPECTOS.ID_PROSPECTO = HCXP.FK1MCRM_PROSPECTOS
                        WHERE  id = $factura  OR id = $facturaCobro
                        ORDER BY FECHAMOVI ASC";
        return $this->conexionFirebird->fireSelect( $queryFacturasAbonos  );
    }
    


}
