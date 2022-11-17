<?php

require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/FirebirDB.php";
// require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/FirebirdPrueba.php";


class Proveedor 
{
    private $DB ;
    public function __construct() {
        $this->DB = new FirebirdDB;
    }
    public function getProveedores( $idProveedor  = "%" )
    {
        $queryProveedor = "SELECT CRM_PROSPECTOS.PYM_NOMBRE, CRM_PROSPECTOS.ID_PROSPECTO
                            FROM CRM_PROSPECTOS
                            INNER JOIN cxp_historcp ON cxp_historcp.FK1MCRM_PROSPECTOS = CRM_PROSPECTOS.ID_PROSPECTO
                            GROUP BY CRM_PROSPECTOS.PYM_NOMBRE, CRM_PROSPECTOS.ID_PROSPECTO";
        $queryProveedor = "SELECT C.PYM_NOMBRE, C.ID_PROSPECTO
                                    From (cxp_recibos R 
                                    INNER JOIN cxp_historcp H on R.imp_factu_historcp=H.id 
                                    INNER JOIN crm_prospectos C on H.fk1mcrm_prospectos=C.id_prospecto) 
                                    Where (H.documentado='S' and R.rec_pagado='F' or (R.rec_pagado='T' 
                                    and R.numero='ANTICIPO' and not R.tipocobro='CANCELADO'))
                                    group by c.id_prospecto,pym_nombre
                                        order by pym_nombre
                                     ";

        return $this->DB->fireSelect( $queryProveedor );
    }

    public function getFacturasPorSaldar( $provedorId = "%")
    {
        $aProveedor = $provedorId != '%' ? "AND  H.fk1mcrm_prospectos=".$provedorId : '';
        $queryFacturas = "SELECT C.id_prospecto AS CODIGO, ' ' AS APELLIDO1,R.NUMFACT AS NUMERO,C.pym_nombre AS CF1,R.ORIGEN,R.FECHAEMISION,R.FECHAVTO,
                                                    R.IMPORTE,C.id_prospecto AS ID,
                                                    R.TIPOCLIENTE,R.NUMERO AS NUMFACT,R.NUMANTICIPO,R.IMP_FACTU_HISTORCP AS IDFACTURA_DEUDA,R.ID as IDCXP
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
        // echo $queryFacturas;
        // die();
        return $this->DB->fireSelect( $queryFacturas );
    }

    public function deudaGlobal()
    {
        $queryDeuda = "SELECT sum(importe) as DEUDA
        From (cxp_recibos R 
        INNER JOIN cxp_historcp H on R.imp_factu_historcp=H.id 
        INNER JOIN crm_prospectos C on H.fk1mcrm_prospectos=C.id_prospecto) 
        Where (H.documentado='S' and R.rec_pagado='F' or (R.rec_pagado='T' 
        and R.numero='ANTICIPO' and not R.tipocobro='CANCELADO'))";

        return $this->DB->fireSelect( $queryDeuda);
    }
}

