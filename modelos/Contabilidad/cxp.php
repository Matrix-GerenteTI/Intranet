<?php

namespace cxp;

require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/FirebirDB.php";


class CuentasPorPagar extends  \FirebirdDB
{

    
    public function getUsuarioSistema( $usuario='%' )
    {
        $queryUsuario = "SELECT * FROM CFG_USUARIOS WHERE USU_PILOTUSER LIKE '$usuario' ";

        
        return $this->fireSelect( $queryUsuario );
    }

    public function getDetallePagoAplicado( $id )
    {
        $queryDetalle = "SELECT CXP_RECIBOS.NUMFACT,CXP_HISTORCP.IMPORTECOBRO,CRM_PROSPECTOS.PYM_NOMBRE
        from CXP_HISTORCP
        INNER JOIN CXP_RECIBOS ON CXP_RECIBOS.IMP_COBRO_HISTORCP = CXP_HISTORCP.ID
        INNER JOIN CRM_PROSPECTOS ON CRM_PROSPECTOS.ID_PROSPECTO = CXP_HISTORCP.FK1MCRM_PROSPECTOS
        where CXP_HISTORCP.Id = $id";

        return $this->fireSelect( $queryDetalle );
    }

    public function getUsuariosSistema()
    {
        $queryUsuarios = "SELECT CFG_USUARIOS.USU_PILOTUSER, CFG_USUARIOS.USU_STATUS,CFG_USUARIOS.ID_USUARIO
                                        FROM CFG_VENDEDORES
                                        INNER JOIN CFG_USUARIOS ON CFG_USUARIOS.ID_USUARIO = CFG_VENDEDORES.ID
                                        WHERE CFG_VENDEDORES.ACTIVO = 'S";

        return $this->fireSelect( $queryUsuarios );
    }

    public function setHistoricoPagos( $params )
    {
        extract( $params );
        $querySetHistorico = "INSERT INTO cxp_historcp(id,fechamovi,concepto,numdocumento,comentario,importefactu,
        importecobro,seccion,fk1mcfg_usuarios,fk1mcrm_prospectos,documentado,fk1mcfg_vendedores,nomvendedor,fk11cfg_cabdoctos,fk11cfg_cabdoctos2,porciva,fk11pedidoid,fechatrasp) VALUES (
        $id,'$fecha','EGRXPAGDOCTO (CHQ $numCheque','$numDocto','$comentario ',0,$importeAbono,'',$usuarioAplico,$idProveedor,'S',$usuarioAplico  ,'$nombreUsuario',-1,-1,$ivaAplicado,-1,NULL)";
        //return $querySetHistorico;
        return $this->insert( $querySetHistorico );
    }

    public function setAbonoAcuenta( $params ) 
    {
        extract( $params );

        $queryAbonoCuenta = "INSERT INTO cxp_recibos(id,numero,fechavto,fechaemision,tipocobro,banco,importe,acuenta,
        facturaproveedor,observaciones,numfact,rec_pagado,imp_factu_historcp,imp_cobro_historcp,fecha,origen,
        fechacobro,conceptoreal,numanticipo,importeiva1,tantoiva1,tipocliente,tipocliente2) values ($idRecibo,'$numeroMovto','$fechaVencimiento','$fechaEmision','','', $saldoFinal,0,'F',
        '$observaciones','$numeroFactura','F',$idFacturaAbonada,-1,NULL,'FRAGMENTADO P/EGRXPAGDOCTO',NULL,'$conceptoReal','',$iva,$valorIva,'$itpoCliente','')";
        //return $queryAbonoCuenta;
        return $this->insert( $queryAbonoCuenta );
    }

    public function actualizaSaldoDeudaProveedor( $params)
    {
        extract( $params );
        $querySaldo = "UPDATE cxp_recibos set importe=$importePagado,acuenta=$importePagado,rec_pagado='T',imp_cobro_historcp = $idHistoricoPago,fechacobro='$fechaPago',
        numanticipo='$numCheque (EGRXPAGDOCTO)',banco='$idcuentaChequera',importeiva1=$importeIva where id= $idRecibo";
        //return $querySaldo;
        return $this->insert( $querySaldo );
    }

    public function setDetalleFormaPago( $params )
    {
        extract( $params );

        $queryDetalleFormaPago = "INSERT INTO cxp_recibosformspags(fk1mcxp_historcp,formapago,importe,referencia,chequeno) VALUES ($idRecibo,'$medioPago', $importePagado,'$referencias','$cheque') ";

        return $this->insert( $queryDetalleFormaPago );
    }
}
