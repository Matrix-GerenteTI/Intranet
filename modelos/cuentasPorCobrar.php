<?php


require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/FirebirDB.php";


class CuentasPorCobrar 
{
    protected $conexionFirebird;

    public function __construct()
    {
        $this->conexionFirebird = new FirebirdDB;
    }

    public function getDeudaClientes()
    {
        $queryCXC = "SELECT C.id_prospecto AS CODIGO, ' ' AS APELLIDO1,R.NUMFACT,C.pym_nombre AS CF1,
            R.ORIGEN,R.FECHAEMISION,R.FECHAVTO,R.IMPORTE,C.id_prospecto AS ID,R.TIPOCLIENTE,R.NUMERO,R.NUMANTICIPO
                                 FROM (cxc_recibos R 
                                 INNER JOIN cxc_historcc H on R.imp_factu_historcc=H.id
                                  INNER JOIN crm_prospectos C on H.fk1mcrm_prospectos=C.id_prospecto) 
                                  WHERE (H.documentado='S' and R.rec_pagado='F' or (R.rec_pagado='T' and R.numero='ANTICIPO')) and 1=1
                                   and not C.pym_nombre like '.%' and R.tipocobro<>'CANCELADO' and 1=1 and 1=1 
                                ORDER BY C.id_prospecto,R.tipocliente,R.fechaemision";
        return $this->conexionFirebird->fireSelect( $queryCXC );
    }

    public function getCobros()
    {
        $queryCobros =" SELECT R.NUMANTICIPO,H.FECHAMOVI,H.CONCEPTO,H.IMPORTECOBRO,H.COMENTARIO,
                                        P.ID_PROSPECTO AS CODIGO,P.PYM_NOMBRE AS CF1,
                                        H.ID AS ID1,H.ID AS ID2,H.ID AS ID3,H.ID AS ID4,H.ID AS ID5,H.ID AS ID6,
                                        'COBROS' AS FORMALISTAANTICIPOS,H.fk1mcfg_usuarios,NUMERO,H.WSUUID 
                                        FROM (crm_prospectos P 
                                        INNER JOIN cxc_historcc H on H.fk1mcrm_prospectos=P.id_prospecto 
                                        INNER JOIN cxc_recibos R on R.imp_cobro_historcc=H.id) 
                                        Where ((R.facturaproveedor='T' and R.numanticipo not like '%(ABONOMANUAL)'  and R.numanticipo<>'') or
                                        R.numanticipo like '%INGXPAGPARCIAL%' or R.numanticipo like '%INGXANTICIPO%') 
                                        GROUP BY R.NUMANTICIPO,H.FECHAMOVI,H.CONCEPTO,H.IMPORTECOBRO,H.COMENTARIO,P.ID_PROSPECTO,P.PYM_NOMBRE,H.ID,H.FK1MCFG_USUARIOS,NUMERO,WSUUID Order by 15,R.numanticipo";
        return $this->conexionFirebird->fireSelect( $queryCobros );
        
    }
}
