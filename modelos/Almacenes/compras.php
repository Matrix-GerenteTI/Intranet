<?php

require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/FirebirDB.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/DB.php";

class Compras extends FirebirdDB 
{
    protected $mysqlConection; 

    public function __construct()
    {
        parent::__construct();
    }

    public function getListaCompras()
    {
        $queryHistoricoCompras = "SELECT RECOM.NUMFACTPROV,RECOM.FECHA AS FECHAENTRADA,RECOM.FECHAFACTPROV,RECOM.FK1MCFG_USUARIOS AS IDUSUARIO,USERS.USU_PILOTUSER,RECOM.SUBTOTAL,RECOM.IMPIVA,RECOM.TOTAL,RECOM.STATUS,
                                                            CFGA.CODIGOARTICULO,CFGA.DESCRIPCION,CFGA.FAMILIA,CFGA.SUBFAMILIA,PRECIOS.PVP1,PRECIOS.PVP2,PRECIOS.PVP3,PRECIOS.PVP4,PRECIOS.PVP5,PROV.PYM_NOMBRE,USERS.USU_NOMBRE,RAXA.CTOPROMEDIO, REDETCOM.CANTIDAD
                                                        FROM ref_comprastraspregs RECOM 
                                                        INNER JOIN ref_detcomprastraspregs REDETCOM on RECOM.id=REDETCOM.fkpadref_comprastraspregs 
                                                        INNER JOIN ref_artxalmacen RAXA on RAXA.id=REDETCOM.fk1mref_artxalmacen 
                                                        INNER JOIN cfg_articulos CFGA on CFGA.id=RAXA.fk1mcfg_articulos 
                                                        INNER JOIN CFG_PRECIOSXALMACENES AS PRECIOS ON PRECIOS.FK1MCFG_ARTICULOS = CFGA.ID
                                                        INNER JOIN CFG_USUARIOS AS USERS ON USERS.ID_USUARIO = RECOM.FK1MCFG_USUARIOS
                                                        INNER JOIN CRM_PROSPECTOS AS PROV ON PROV.ID_PROSPECTO = RECOM.FK1MCRM_PROSPECTOS
                                                        Where RECOM.STATUS='COMPRA EMITIDO' AND PRECIOS.PVP1 != ''";

        return$this->fireSelect( $queryHistoricoCompras );
    }

    public function getHistoricoFacturasCompras( $idProveedor )
    {
         $queryFacturas = "SELECT REF_COMPRASTRASPREGS.SECCION,REF_COMPRASTRASPREGS.FK1MCFG_ALMACENES,REF_COMPRASTRASPREGS.FECHA,REF_COMPRASTRASPREGS.NUMDOCTO,REF_COMPRASTRASPREGS.SUBTOTAL,
         REF_COMPRASTRASPREGS.PORCIVA,REF_COMPRASTRASPREGS.IMPIVA,REF_COMPRASTRASPREGS.TIPOPROVEEDOR,REF_COMPRASTRASPREGS.HORAMOVTO,REF_COMPRASTMP.ID AS COMPRASINDOCID,
         REF_COMPRASTMP.FECHAFACTPROV,REF_COMPRASTMP.NUMDOCTO AS UUID,CRM_PROSPECTOS.ID_PROSPECTO,CRM_PROSPECTOS.PYM_NOMBRE, REF_COMPRASTMP.STATUS,CFG_ALMACENES.DESCRIPCION AS ALMACEN,
         CFG_USUARIOS.USU_NOMBRE, CFG_USUARIOS.USU_APELPAT, REF_COMPRASTRASPREGS.ID as IDCOMPRA
         from REF_COMPRASTRASPREGS 
         RIGHT JOIN REF_COMPRASTMP ON REF_COMPRASTMP.FK1MCFG_USUARIOS2 = REF_COMPRASTRASPREGS.ID
         LEFT JOIN CRM_PROSPECTOS ON CRM_PROSPECTOS.ID_PROSPECTO = REF_COMPRASTRASPREGS.FK1MCRM_PROSPECTOS
         LEFT JOIN CFG_ALMACENES ON CFG_ALMACENES.ID = REF_COMPRASTRASPREGS.FK1MCFG_ALMACENES
         LEFT JOIN CFG_USUARIOS ON REF_COMPRASTRASPREGS.FK1MCFG_USUARIOS = CFG_USUARIOS.ID_USUARIO
         WHERE ( REF_COMPRASTRASPREGS.SECCION IN ('COMPRAS OTROS PROVS (CONTADO)', 'COMPRAS OTROS PROVS (CREDITO)') OR REF_COMPRASTRASPREGS.SECCION IS NULL )
                 AND REF_COMPRASTMP.STATUS NOT IN ('XML DESCARTADO') AND CRM_PROSPECTOS.ID_PROSPECTO LIKE '$idProveedor' ";
                
        return $this->fireSelect( $queryFacturas );
    }

    public function getListaItemsCompra( $idComprasTrasPreg )
    {
        $queryItems = "SELECT REF_ARTXALMACEN.ID,REF_DETCOMPRASTRASPREGS.CANTIDAD,REF_DETCOMPRASTRASPREGS.DESCRIPCION AS ARTICULO,CFG_ARTICULOS.CODIGOARTICULO,
                                CFG_ARTICULOS.FAMILIA, CFG_ARTICULOS.SUBFAMILIA
                            FROM  REF_DETCOMPRASTRASPREGS
                            INNER JOIN REF_ARTXALMACEN ON REF_ARTXALMACEN.ID = REF_DETCOMPRASTRASPREGS.FK1MREF_ARTXALMACEN
                            INNER JOIN CFG_ARTICULOS ON CFG_ARTICULOS.ID = REF_ARTXALMACEN.FK1MCFG_ARTICULOS
                            WHERE REF_DETCOMPRASTRASPREGS.FKPADREF_COMPRASTRASPREGS = $idComprasTrasPreg ";
        return $this->fireSelect( $queryItems );
    }

    public function getComprasSinProcesar(  $factura , $proveedor)
    {
        $queryFacturas = "SELECT REF_COMPRASTMP.id, REF_COMPRASTMP.FECHA,REF_COMPRASTMP.numdocto, REF_COMPRASTMP.FECHAFACTPROV, REF_COMPRASTMP.SUBTOTAL, REF_COMPRASTMP.IMPIVA,
                                        REF_COMPRASTMP.TOTAL,CRM_PROSPECTOS.PYM_NOMBRE as PROVEEDOR,REF_COMPRASTMP.NUMFACTPROV
                                        from REF_COMPRASTMP
                                        left join CRM_PROSPECTOS on CRM_PROSPECTOS.PYM_RFC = REF_COMPRASTMP.OBSERVACIONES
                                        where REF_COMPRASTMP.STATUS = '' AND REF_COMPRASTMP.NUMFACTPROV LIKE '%$factura' AND CRM_PROSPECTOS.PYM_NOMBRE like '%$proveedor'  ";
                                        
                                        
        return $this->fireSelect( $queryFacturas );
    }

    public function getLogCompraSinProcesar( $factura )
    {
        $this->mysqlConection = new DB;
        $queryLogsCompras = "SELECT *
                                            FROM logcompras_recepcion
                                            WHERE noFactura = '$factura' AND estado = 1";
        return $this->mysqlConection->select( $queryLogsCompras );
    }

    public function registraRecepcionCompra( $params )
    {
        extract( $params );
        $this->mysqlConection = new DB;

        $queryRecepcion = "INSERT INTO logcompras_recepcion(idCompratmp,noFactura,fechaRecepcion,idusuarioAuditor) VALUES ('$compraId','$factura','$recepcion','$usuario') ";

        return $this->mysqlConection->insert( $queryRecepcion );
    }

    public function registraAltaMercancia( $params )
    {
        extract( $params );
        $this->mysqlConection = new DB;

        $queryIngreso = "UPDATE logcompras_recepcion set fechaIngresoFact='$ingreso',noCompra='$entradaId',idusuarioCompras='$usuario' WHERE noFactura = '$factura' AND estado = 1 ";

        return $this->mysqlConection->update( $queryIngreso );
    }
    public function validaEntradaCompra( $numDocto , $factura )
    {
        $queryRegistrada ="SELECT *
                            FROM REF_COMPRASTRASPREGS
                            WHERE seccion LIKE '%COMPRA%' AND NUMDOCTO = '$numDocto' AND NUMFACTPROV = '$factura'";
        return $this->fireSelect( $queryRegistrada );
    }    
}
