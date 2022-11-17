<?php

require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/FirebirDB.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/DB.php";

class Almacen extends FirebirdDB
{
    public function getSucursales()
    {
        $querySucursales = " SELECT * FROM CFG_ALMACENES
                                WHERE PWD IN ('MATRIX','LEON','OXIFUEL') AND ACTIVO='S'";
        return self::fireSelect( $querySucursales);
    }

    public function getAlmacenes()
    {
        $querySucursales = " SELECT * FROM CFG_ALMACENES
        WHERE PWD IN ('MATRIX','LEON','OXIFUEL','CEDIM') AND ACTIVO='S'";

        return self::fireSelect( $querySucursales);
    }

    public function getSucursalesApp()
    {
        $querySucursales = " SELECT ID as ID,DESCRIPCION as NAME 
                             FROM CFG_ALMACENES 
                             WHERE ZONA IN ('CENTRO','ALTOS','COSTA') 
                             AND     ACTIVO='S'
                             AND     PWD IN ('MATRIX','ONLINE') 
                             ORDER BY DESCRIPCION";

        return self::fireSelect( $querySucursales);
    }

    public function getSucursalesById($sucursal = "")
    {
        $querySucursales = "SELECT * 
                                            FROM  CFG_ALMACENES
                                            WHERE ID = $sucursal ";
        return self::fireSelect($querySucursales);
    }

    public function getSucursalesIntranet()
    {
        $db = new DB;
        $querySucursales = "SELECT id, descripcion as name  from csucursal where status = 1 and menuapp=1";

        return $db->select( $querySucursales );
    }

    public function getTraspasosCedimSucursales()
    {
         $queryMovtos = "SELECT CFG_ALMACENES.DESCRIPCION AS ORIGEN, ALM.DESCRIPCION AS DESTINO,REF_COMPRASTRASPREGS.FECHA,REF_COMPRASTRASPREGS.HORAMOVTO  
                                        FROM REF_COMPRASTRASPREGS
                                        INNER JOIN CFG_ALMACENES ON CFG_ALMACENES.ID = REF_COMPRASTRASPREGS.FK1MCFG_ALMACENES
                                        INNER JOIN CFG_ALMACENES AS ALM ON ALM.ID = REF_COMPRASTRASPREGS.FK1MCFG_ALMACENES2
                                        
                                        WHERE SECCION = 'TRASPASO ENTRE ALMACENES' AND FECHA >= '2019-11-01' AND STATUS = 'TRASPASO EMITIDO' AND CFG_ALMACENES.PWD = 'CEDIM' 
                                        ORDER BY FECHA ASC";
        return self::fireSelect( $queryMovtos );
    }

    public function getEntradasSalidas( $data, $transitos = false )
    {
        extract( $data);
        // $inTransitoDevuelto = $transitos == true  ? " OR  seccion IN ('TRASPASO ENTRE ALMACENES') AND NUMDOCTO ='TRANSITO-DEVUELTO'  " : "";
        
        $queryEntradaSalida = "SELECT c.FK1MCFG_ALMACENES,c.FK1MCFG_USUARIOS,c.fecha,c.numdocto,c.seccion,c.observaciones,c.horamovto,dc.cantidad,DC.CODIGO,DC.DESCRIPCION,
                                        udn.descripcion as sucursal ,CFG_USUARIOS.USU_NOMBRE, CFG_USUARIOS.USU_PILOTUSER
                                        from REF_COMPRASTRASPREGS AS c
                                inner join REF_DETCOMPRASTRASPREGS dc on dc.FKPADREF_COMPRASTRASPREGS = C.ID         
                                INNER JOIN REF_ARTXALMACEN AS ALM ON ALM.ID = dc.FK1MREF_ARTXALMACEN
                                INNER JOIN CFG_USUARIOS ON CFG_USUARIOS.ID_USUARIO = C.FK1MCFG_USUARIOS
                                inner JOIN CFG_ALMACENES AS UDN ON UDN.ID = c.FK1MCFG_ALMACENES
                                where (seccion IN ('ENTRADA X AJUSTE', 'SALIDA X AJUSTE' )  ) and c.fecha >= '$inicio' AND c.fecha <= '$fin'
                                            and CFG_USUARIOS.ID_USUARIO like '$usuario%' and c.numdocto like '$folio%'  ";


        return self::fireSelect( $queryEntradaSalida );
    }

    public function getEmitidoDevuelto( $data, $transitos = false )
    {
        extract( $data);
        $inTransitoDevuelto = $transitos == true  ? " OR  seccion IN ('TRASPASO ENTRE ALMACENES') AND NUMDOCTO ='TRANSITO-DEVUELTO'  " : "";
        
        $queryEntradaSalida = "SELECT c.FK1MCFG_ALMACENES,c.FK1MCFG_USUARIOS,c.fecha,c.numdocto,c.seccion,c.STATUS,c.observaciones,c.horamovto,dc.cantidad,DC.CODIGO,DC.DESCRIPCION,
                                        udn.descripcion as sucursal ,CFG_USUARIOS.USU_NOMBRE, CFG_USUARIOS.USU_PILOTUSER
                                        from REF_COMPRASTRASPREGS AS c
                                inner join REF_DETCOMPRASTRASPREGS dc on dc.FKPADREF_COMPRASTRASPREGS = C.ID         
                                INNER JOIN REF_ARTXALMACEN AS ALM ON ALM.ID = dc.FK1MREF_ARTXALMACEN
                                INNER JOIN CFG_USUARIOS ON CFG_USUARIOS.ID_USUARIO = C.FK1MCFG_USUARIOS
                                inner JOIN CFG_ALMACENES AS UDN ON UDN.ID = c.FK1MCFG_ALMACENES
                                where c.SECCION = 'TRASPASO ENTRE ALMACENES' and C.FECHA >= '$inicio' and c.fecha <= '$fin'
                                            and CFG_USUARIOS.ID_USUARIO like '$usuario%' and c.numdocto like '$folio%'  ";


        return self::fireSelect( $queryEntradaSalida );
    }

    public function getProductoInventario(){
        $query = " SELECT A2.descripcion as almacen,
                        CFGA.familia as fam,
                        CFGA.subfamilia as subfam,
                        REPLACE(CFGA.codigoarticulo,'/','_slash_') as cod,                        
                        CFGA.descripcion as descripcion,                        
                        COUNT(*) as registros
                from 	   (cfg_articulos CFGA 
                        inner join ref_artxalmacen RAXA on CFGA.id=RAXA.fk1mcfg_articulos 
                        inner join cfg_almacenes A2 on A2.id=RAXA.fk1mcfg_almacenes ) 
                where 	CFGA.itemservicio=''
                AND 		CFGA.DESCRIPCION NOT LIKE '%USADO%'
                AND 		(RAXA.existotal-RAXA.exispedidos-RAXA.exisproceso)>0
                group by cod, A2.descripcion, CFGA.familia,CFGA.subfamilia,(RAXA.existotal-RAXA.exispedidos-RAXA.exisproceso), CFGA.descripcion;";
        return self::fireSelect( $query );
    }

    public function getUsuariosMovtosAlmacenes()
    {
        $queryUsuarios = "SELECT (CFG_USUARIOS.USU_NOMBRE||' '||CFG_USUARIOS.USU_APELPAT) as usuario, CFG_USUARIOS.ID_USUARIO
                            from REF_COMPRASTRASPREGS AS c
                            inner join REF_DETCOMPRASTRASPREGS dc on dc.FKPADREF_COMPRASTRASPREGS = C.ID
                            INNER JOIN REF_HISTXARTXALM AS H ON H.FK1MREF_COMPRASTRASPREGS = C.ID
                            INNER JOIN REF_ARTXALMACEN AS ALM ON ALM.ID = H.FK1MREF_ARTXALMACEN
                            INNER JOIN CFG_USUARIOS ON CFG_USUARIOS.ID_USUARIO = C.FK1MCFG_USUARIOS
                            
                            INNER JOIN CFG_ALMACENES AS UDN ON UDN.ID = c.FK1MCFG_ALMACENES
                            where seccion IN ('ENTRADA X AJUSTE', 'SALIDA X AJUSTE') 
                            group by usu_nombre,USU_APELPAT,ID_USUARIO";
        return $this->fireSelect( $queryUsuarios );
    }

    public function getFoliosTraspasos( $data )
    {
        extract( $data );
        $queryFolios = "SELECT T.FECHA,T.HORAMOVTO,T.NUMDOCTO, A1.DESCRIPCION AS ORIGEN, A2.DESCRIPCION AS DESTINO,T.TOTAL
        FROM REF_COMPRASTRASPREGS AS T
        INNER JOIN CFG_ALMACENES AS A1 ON A1.ID = T.FK1MCFG_ALMACENES
        INNER JOIN CFG_ALMACENES AS A2 ON A2.ID = T.FK1MCFG_ALMACENES2
        WHERE SECCION = 'TRASPASO ENTRE ALMACENES' AND STATUS = 'TRASPASO EMITIDO' AND FECHA >='$inicio'  AND FECHA <='$fin'";

        return $this->fireSelect( $queryFolios );
    }

    public function checkInventario( $params )
    {
        $db = new DB;
        //var_dump($params);
        extract( $params );
        $query = "INSERT INTO checkinventario (sucursal,familia,subfamilia,fecha,hora,usuario) VALUES ('".$almacen."','".$familia."','".$subfamilia."',NOW(),NOW(),'".$usuario."')";
        return $db->insert( $query );
    }    

    public function getConfirmados( $params )
    {
        $db = new DB;
        //var_dump($params);
        extract( $params );
        $query = "SELECT i.*,s.descripcion as almacen FROM checkinventario i INNER JOIN csucursal s ON i.sucursal=s.idprediction WHERE i.fecha='".$fecha."'";
        return $db->select( $query );
    }
}
