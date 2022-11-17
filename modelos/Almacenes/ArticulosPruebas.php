<?php

use phpDocumentor\Reflection\DocBlock\Tags\Return_;

require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/FirebirdDB_pruebas.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/DB_pruebas.php";

class Articulos extends FirebirdDB
{

    public function formateaFecha($date){
        $exp = explode('-',$date);
        return $exp[2].'/'.$exp[1].'/'.$exp[0];
    }

    public function getFamilias( )
    {
        $quertFamilas = "SELECT 	CFGA.familia as fam
                                    from 	   cfg_articulos CFGA 
                                    where 	CFGA.itemservicio=''
                                                     AND 		CFGA.familia NOT IN ('APARTADO','SERVICIO','','HERRAMIENTA','HERRAMIENTAS','REFACCION')
                                    GROUP 	by CFGA.familia";
                                    
        return self::fireSelect( $quertFamilas );
    }

    public function getSubfamilias( $familia )
    {
        $querySubfamilia =  "SELECT 	CASE CFGA.subfamilia WHEN '' THEN 'OTROS' ELSE CFGA.subfamilia END as subfam
      
                                from 	   cfg_articulos CFGA 

                                where 	CFGA.itemservicio=''
                                AND 		CFGA.familia LIKE '%".$familia."%'
                                GROUP 	by CFGA.subfamilia";
        return self::fireSelect( $querySubfamilia );
    }
    public function getValuado()
    {
       $queryValuado = "SELECT RAXA.id,CFGA.id as idarticulo,CFGA.codigoarticulo as codigoart,CFGA.familia as fam,
       CFGA.descripcion as descrip,CFGA.subfamilia as subfamilia,A2.descripcion as almacen,RAXA.ctopromedio as premecos,(RAXA.existotal-RAXA.exispedidos-RAXA.exisproceso) as stock,
       0 as DIASINV
       FROM  
       (cfg_articulos CFGA 
       INNER JOIN  ref_artxalmacen RAXA on CFGA.id=RAXA.fk1mcfg_articulos 
       INNER JOIN  cfg_almacenes A2 on A2.id=RAXA.fk1mcfg_almacenes 
       INNER JOIN  cfg_preciosxalmacenes PA on (PA.fk1mcfg_articulos=CFGA.id and PA.fk1mcfg_almacenes=RAXA.fk1mcfg_almacenes)) 
       WHERE  CFGA.itemservicio=''  and (RAXA.existotal-RAXA.exispedidos-RAXA.exisproceso) >  0
                       AND A2.PWD in ('MATRIX','LEON','CEDIM','OXIFUEL')
       order by CFGA.familia,CFGA.subfamilia,CFGA.codigoarticulo";

        return self::selectValuado( $queryValuado );
    }

    public function getProductosFamilia( $familia, $almacen)
    {
        $queryProductos = "SELECT RAXA.id,CFGA.id as idarticulo,CFGA.codigoarticulo as codigoart,CFGA.familia as fam,
                        CFGA.descripcion as descrip,CFGA.subfamilia as subfamilia,A2.descripcion as almacen,RAXA.ctopromedio as premecos,
                        (RAXA.existotal-RAXA.exispedidos-RAXA.exisproceso) as stock,CFGA.marca as ADIC1,PA.pvp1  as ADIC2,
                        PA.pvp2 as ADIC3,PA.pvp3 as ADIC4,PA.pvp4 as ADIC5,PA.pvp5 as ADIC6,PA.pvp6 as ADIC7,PA.pvp7 as ADIC8,PA.pvp8 as ADIC9,PA.pvp9 as ADIC10,
                        CFGA.pvp2 as subfam2,CFGA.pvp3 as subfam3,CFGA.pvp4 as subfam4,CFGA.pvp5 as subfam5,CFGA.pvp6 as subfam6,CFGA.pvp7 as subfam7,CFGA.pvp8 as subfam8, 0 as DIASINV 
                        FROM  
                        (cfg_articulos CFGA 
                        INNER JOIN  ref_artxalmacen RAXA on CFGA.id=RAXA.fk1mcfg_articulos 
                        INNER JOIN  cfg_almacenes A2 on A2.id=RAXA.fk1mcfg_almacenes 
                        INNER JOIN  cfg_preciosxalmacenes PA on (PA.fk1mcfg_articulos=CFGA.id and PA.fk1mcfg_almacenes=RAXA.fk1mcfg_almacenes)) 
                        WHERE 1=1 and CFGA.familia like '$familia%' and CFGA.itemservicio=''  and (RAXA.existotal-RAXA.exispedidos-RAXA.exisproceso) >  0
                                        AND PA.fk1mcfg_almacenes LIKE '$almacen'
                        order by CFGA.familia,CFGA.subfamilia,CFGA.codigoarticulo";

            return self::fireSelect( $queryProductos );
    }


    public function getValuadoSucursalMysql( $famila , $almacen )
    {
        $queryValuado = "SELECT *,prediction_pruebas.inventario.CTOPROMEDIO AS PREMECOS,prediction_pruebas.inventario.IDARTICULO AS IDARTICULO FROM  prediction_pruebas.inventario 
                                        LEFT JOIN prediction_pruebas.precios ON prediction_pruebas.inventario.IDARTICULO = prediction_pruebas.precios.IDARTICULO
                                        WHERE almacen like '%$almacen%' and FAMILIA = '$famila'
                                        ORDER BY id desc";

        $mysqlConexion  = new DB;
        return  $mysqlConexion->select( $queryValuado );                                        

    }

    public function getPoliticaPrecios( $familia , $subfamilia, $llanta ){
        $conexion = new mysqli('127.0.0.1','sestrada','M@tr1x2017','dbnomina_pruebas');
        mysqli_query($conexion,"SET NAMES 'utf8");
        mysqli_set_charset( $conexion ,"utf8");
        $array = array();
        $b1 = 0;
        $queryPolitica = "SELECT * FROM  prediction_pruebas.politica_precios 
                        WHERE familia='".$familia."' AND subfamilia='".$subfamilia."'";
        $sql = $conexion->query($queryPolitica);
        while($row = $sql->fetch_assoc()){
            $b1++;
            $array[] = $row;
        }
        //return $queryPolitica;
        if($b1==0){            
            $b2 = 0;
            $queryPolitica = "SELECT * FROM  prediction_pruebas.politica_precios WHERE familia='".$familia."' AND subfamilia='ALL'";
            $sql2 = $conexion->query($queryPolitica);
            while($row2 = $sql2->fetch_assoc()){
                $b2++;
                $array[] = $row2;
            }
            if($b2 == 0){
                return 0;
            }else{
                return $array[0];
            }
        }else{
            return $array[0];
        }
    }

    public function getUltimaCompra($codigo){

        // $queryUC = "SELECT FIRST 1 (r.costo+(r.costo*(r.PORCIVA/100))) as ctoultcompra,c.FECHAFACTPROV as fecultcompra, CAST((CURRENT_DATE+1 - c.FECHAFACTPROV) AS INTEGER) AS DIF 
        // FROM REF_COMPRASTRASPREGS c 
        // INNER JOIN REF_DETCOMPRASTRASPREGS r ON c.ID=r.FKPADREF_COMPRASTRASPREGS 
        // WHERE r.CODIGO='$codigo' AND c.STATUS IN ('COMPRA EMITIDO') ORDER BY c.ID DESC";
        
        $dias = 0;

        $hoyd = date('Y-m-d');
        $dias = (strtotime('2017-01-01')-strtotime($hoyd))/86400;
        $dias = abs($dias); $dias = floor($dias);
        $resp = array('CTOULTCOMPRA'=>0,'FECULTCOMPRA'=>'01/01/2017','DIF'=>$dias);

        $costo = 0;
        $n1 = 0;
        $q1 = "SELECT 	FIRST 1 r.costo as ctoultcompra, 
                        c.FECHAFACTPROV as fecultcompra, 
                        CAST((CURRENT_DATE+1 - c.FECHAFACTPROV) AS INTEGER) AS DIF 
                FROM 	REF_COMPRASTRASPREGS c 
                INNER JOIN REF_DETCOMPRASTRASPREGS r ON c.ID=r.FKPADREF_COMPRASTRASPREGS 
                WHERE 	r.CODIGO='".$codigo."' 
                AND 	c.STATUS IN ('COMPRA EMITIDO') 
                ORDER BY c.FECHA DESC";
        $s1 = ibase_query($this->conexionFireBird,$q1);
        while($r1 = ibase_fetch_assoc($s1)){
            $resp['CTOULTCOMPRA'] = $r1['CTOULTCOMPRA'];
            $resp['FECULTCOMPRA'] = $this->formateaFecha($r1['FECULTCOMPRA']);
            $resp['DIF'] = $r1['DIF'];
            $n1++;
        }

        if($n1==0){
            $encuetra = 0;
            $q3 = "select 	FIRST 1 AA.CTOPROMEDIO as COSTO,
                            AA.FECULTCOM as fecultcompra,  
                            CAST((CURRENT_DATE+1 - AA.FECULTCOM) AS INTEGER) AS DIF,
                            CFGA.ID as ID
                    from 	REF_ARTXALMACEN AA 
                    inner join cfg_articulos CFGA on AA.fk1mcfg_articulos=CFGA.id 
                    where 	CFGA.CODIGOARTICULO='".$codigo."'
                    and (AA.existotal-AA.exispedidos-AA.exisproceso)>0 
                    ORDER BY AA.CTOPROMEDIO DESC";
            $s3 = ibase_query($this->conexionFireBird,$q3);
            while($r3 = ibase_fetch_assoc($s3)){
                if($r3['COSTO']>0){
                    $resp['CTOULTCOMPRA'] = $r3['COSTO'];
                    $resp['FECULTCOMPRA'] = $this->formateaFecha($r3['FECULTCOMPRA']);
                    $resp['DIF'] = $r3['DIF'];
                    $encuetra++;
                }
            }

            if($encuetra==0){
                $q2 = "SELECT 	FIRST 1 c.ID as IDENTRADA,
                                r.COSTO as COSTO,  
                                c.FECHA as fecultcompra,  
                                CAST((CURRENT_DATE+1 - c.FECHA) AS INTEGER) AS DIF,
                                c.fk1mcfg_almacenes as IDALM
                        FROM 	REF_COMPRASTRASPREGS c 
                        INNER JOIN REF_DETCOMPRASTRASPREGS r ON c.ID=r.FKPADREF_COMPRASTRASPREGS 
                        WHERE 	r.CODIGO='".$codigo."' 
                        AND 	c.STATUS IN ('ENTRADA EMITIDO') 
                        ORDER BY c.ID DESC";

                $s2 = ibase_query($this->conexionFireBird,$q2);
                while($r2 = ibase_fetch_assoc($s2)){
                    $idalm = $r2['IDALM'];
                    $resp['CTOULTCOMPRA'] = $r2['COSTO'];
                    $resp['FECULTCOMPRA'] = $this->formateaFecha($r2['FECULTCOMPRA']);
                    $resp['DIF'] = $r2['DIF'];
                }
            }
        }

        return  $resp; //self::fireSelect( $queryUC );
    }

        public function getProductosFamiliaGeneral( $familia, $almacen)
    {
        $queryProductos = "SELECT RAXA.id,CFGA.id as idarticulo,CFGA.codigoarticulo as codigoart,CFGA.familia as fam,
                        CFGA.descripcion as descrip,CFGA.subfamilia as subfamilia,A2.descripcion as almacen,RAXA.ctopromedio as premecos,
                        (RAXA.existotal-RAXA.exispedidos-RAXA.exisproceso) as stock,CFGA.marca as ADIC1,PA.pvp1  as ADIC2,
                        PA.pvp2 as ADIC3,PA.pvp3 as ADIC4,PA.pvp4 as ADIC5,PA.pvp5 as ADIC6,PA.pvp6 as ADIC7,PA.pvp7 as ADIC8,PA.pvp8 as ADIC9,PA.pvp9 as ADIC10,
                        CFGA.pvp2 as subfam2,CFGA.pvp3 as subfam3,CFGA.pvp4 as subfam4,CFGA.pvp5 as subfam5,CFGA.pvp6 as subfam6,CFGA.pvp7 as subfam7,CFGA.pvp8 as subfam8 
                        FROM  
                        (cfg_articulos CFGA 
                        INNER JOIN  ref_artxalmacen RAXA on CFGA.id=RAXA.fk1mcfg_articulos 
                        INNER JOIN  cfg_almacenes A2 on A2.id=RAXA.fk1mcfg_almacenes 
                        INNER JOIN  cfg_preciosxalmacenes PA on (PA.fk1mcfg_articulos=CFGA.id and PA.fk1mcfg_almacenes=RAXA.fk1mcfg_almacenes)) 
                        WHERE 1=1 and CFGA.familia IN ($familia) and CFGA.itemservicio=''  and (RAXA.existotal-RAXA.exispedidos-RAXA.exisproceso) >  0
                                        AND PA.fk1mcfg_almacenes LIKE '$almacen'
                        order by CFGA.familia,CFGA.subfamilia,CFGA.codigoarticulo";

            return self::fireSelect( $queryProductos );
    }

    public function getResurtidoArticulos( $sucursal, $familas)
    {
        $queryResurtido = "SELECT CFG_ALMACENES.ID,CFG_ALMACENES.DESCRIPCION AS ALMACEN, CFG_ARTICULOS.CODIGOARTICULO, CFG_ARTICULOS.DESCRIPCION,
                                            CFG_ARTICULOS.FAMILIA,CFG_ARTICULOS.SUBFAMILIA,REF_ARTXALMACEN.CTOPROMEDIO,(REF_ARTXALMACEN.EXISTOTAL-REF_ARTXALMACEN.EXISPEDIDOS- REF_ARTXALMACEN.EXISPROCESO) AS STOCK
                                        FROM CFG_ALMACENES
                                        INNER JOIN REF_ARTXALMACEN ON REF_ARTXALMACEN.FK1MCFG_ALMACENES = CFG_ALMACENES.ID
                                        INNER JOIN CFG_ARTICULOS ON CFG_ARTICULOS.ID = REF_ARTXALMACEN.FK1MCFG_ARTICULOS
                                        WHERE CFG_ALMACENES.ID IN (10788,10754) AND (REF_ARTXALMACEN.EXISTOTAL - REF_ARTXALMACEN.EXISPEDIDOS-REF_ARTXALMACEN.EXISPROCESO) > 0
                                                AND CFG_ARTICULOS.CODIGOARTICULO NOT IN (select  CFG_ARTICULOS.CODIGOARTICULO
                                        from CFG_ALMACENES
                                        INNER JOIN REF_ARTXALMACEN ON REF_ARTXALMACEN.FK1MCFG_ALMACENES = CFG_ALMACENES.ID
                                        INNER JOIN CFG_ARTICULOS ON CFG_ARTICULOS.ID = REF_ARTXALMACEN.FK1MCFG_ARTICULOS
                                        WHERE CFG_ALMACENES.ID = $sucursal  AND (REF_ARTXALMACEN.EXISTOTAL - REF_ARTXALMACEN.EXISPROCESO -REF_ARTXALMACEN.EXISPEDIDOS) > 0
                                        GROUP BY CFG_ARTICULOS.CODIGOARTICULO
                                        ) AND CFG_ARTICULOS.FAMILIA IN ($familas) AND CFG_ARTICULOS.FAMILIA != ''
                                        ORDER BY CFG_ARTICULOS.FAMILIA,CFG_ARTICULOS.SUBFAMILIA";
        return self::fireSelect( $queryResurtido);
    }

    public function getPrecioByArticulo( $articuloId)
    {
        $queryPrecio = "SELECT *
                                FROM cfg_preciosxalmacenes PA1
                                INNER JOIN REF_ARTXALMACEN RAXA ON RAXA.fk1mcfg_articulos = PA1.FK1MCFG_ARTICULOS AND RAXA.FK1MCFG_ALMACENES=PA1.FK1MCFG_ALMACENES
                                where PA1.FK1MCFG_ARTICULOS = $articuloId  AND ( PA1.PVP1 != '') AND PA1.FK1MCFG_ALMACENES = 10754";
        return self::fireSelect( $queryPrecio);
    }

    public function getAlmacenes( $almacen )
    {
        $queryAlmacenes = "SELECT *
                                FROM CFG_ALMACENES
                                where ID = $almacen";
        return self::fireSelect( $queryAlmacenes);
    }

    public function getListadoVentasMes( $mes , $anio)
    {
        $queryVentas = "SELECT * FROM  VENTAS where extract( MONTH FROM fecha ) = $mes  
        AND extract(YEAR FROM fecha ) = $anio  AND STATUS IN('PEDIDO EMITIDO','PEDIDO FACTURADO')
        ORDER BY FECHA ASC ";
        return self::fireSelect( $queryVentas );
    }

    public function getMetasAcumulados( $idalmacen )
    {
        $mysqlConexion = new DB;

        $queryMetaAcumulado = "SELECT * FROM metas_acumulados WHERE almacen_id ='$idalmacen'  ";
        return $mysqlConexion->select( $queryMetaAcumulado );
    }

    public function getValuadoSucursal( $sucursal )
    {
        $querySucursal = "SELECT CFG_ARTICULOS.CODIGOARTICULO,CFG_ARTICULOS.FAMILIA,CFG_ARTICULOS.SUBFAMILIA,
                     CFG_ARTICULOS.DESCRIPCION,( REF_ARTXALMACEN.EXISTOTAL - (REF_ARTXALMACEN.EXISPEDIDOS + REF_ARTXALMACEN.EXISPROCESO ) ) AS STOCK
                            from REF_ARTXALMACEN
                            inner join CFG_ARTICULOS on CFG_ARTICULOS.ID = REF_ARTXALMACEN.FK1MCFG_ARTICULOS
                            inner join CFG_ALMACENES ON CFG_ALMACENES.ID = REF_ARTXALMACEN.FK1MCFG_ALMACENES
                            where CFG_ALMACENES.ID = $sucursal AND CFG_ARTICULOS.FAMILIA NOT IN ('SERVICIO') AND ( REF_ARTXALMACEN.EXISTOTAL - (REF_ARTXALMACEN.EXISPEDIDOS + REF_ARTXALMACEN.EXISPROCESO ) ) > 0 ";
        return self::fireSelect( $querySucursal );
    }

    public function getInventarioEnSucursal( $sucursal , $fecha, $usuario)
    {
        $mysqlConexion = new DB;

        $queryInventario = "SELECT * 
        FROM dbnomina.inventarios
        WHERE tipo = 4 AND sucursal_id = $sucursal AND usuario_id='$usuario' AND fechacaptura LIKE '%$fecha%'
         ORDER BY id desc";

         return  $mysqlConexion->select( $queryInventario );
    }

    public function getStockInicial( $fechaActual , $idRef, $codigo, $almacen)
    {
        $queryStockInicial = "SELECT SUM(CANTIDAD) as CANTIDAD FROM (SELECT SUM(CASE TIPOMOV 
        WHEN 'S' THEN CANTIDAD
        WHEN 'D' THEN CANTIDAD*-1
        WHEN 'O' THEN CANTIDAD
        WHEN 'E' THEN CANTIDAD*-1
                    END) as CANTIDAD 
            FROM REF_HISTXARTXALM r 
            WHERE r.FK1MREF_ARTXALMACEN= $idRef
            AND     r.FECHA='$fechaActual'
        UNION ALL
        SELECT  SUM(d.CANTIDAD) as CANTIDAD
        FROM    REF_PEDIDOSPRESUP p
        INNER JOIN REF_DETPEDIDOSPRESUP d ON p.ID=d.FKPADREF_PEDIDOSPRESUP
        WHERE   d.CODIGO='$codigo'
        AND     p.STATUS='PEDIDO FACTURADO'
        AND     p.FECHA ='$fechaActual'
        AND     p.FK1MCFG_ALMACENES=$almacen)";

        //echo $queryStockInicial;
        //die();
        return  self::fireSelect( $queryStockInicial );
    }

    public function getIdRefArticulo( $codigo , $almacen )
    {
        $queryIdRef = "SELECT REF_ARTXALMACEN.ID,( REF_ARTXALMACEN.EXISTOTAL - ( REF_ARTXALMACEN.EXISPEDIDOS + REF_ARTXALMACEN.EXISPROCESO) ) AS STOCK
        FROM CFG_ARTICULOS
        inner join REF_ARTXALMACEN on REF_ARTXALMACEN.FK1MCFG_ARTICULOS = CFG_ARTICULOS.ID
        where codigoarticulo = '$codigo' AND REF_ARTXALMACEN.FK1MCFG_ALMACENES = $almacen";

        return  self::fireSelect( $queryIdRef );
    }


    public function getUbicaciones  ( $codigo )
    {
        $queryIdRef = "SELECT REF_ARTXALMACEN.ID,( REF_ARTXALMACEN.EXISTOTAL - ( REF_ARTXALMACEN.EXISPEDIDOS + REF_ARTXALMACEN.EXISPROCESO) ) AS STOCK,CFG_ALMACENES.DESCRIPCION
        FROM CFG_ARTICULOS
        inner join REF_ARTXALMACEN on REF_ARTXALMACEN.FK1MCFG_ARTICULOS = CFG_ARTICULOS.ID
        INNER JOIN CFG_ALMACENES ON CFG_ALMACENES.ID = REF_ARTXALMACEN.FK1MCFG_ALMACENES
        where codigoarticulo = '$codigo'  AND  ( REF_ARTXALMACEN.EXISTOTAL - ( REF_ARTXALMACEN.EXISPEDIDOS + REF_ARTXALMACEN.EXISPROCESO) ) > 0";

        return  self::fireSelect( $queryIdRef );
    }

    public function getCantidadStockMovimientos( $mes, $anio , $idRef, $codigo, $almacen)
    {
        $numeroDeDiasMes = cal_days_in_month(CAL_GREGORIAN, $mes, $anio);

        $queryCantidad = "SELECT SUM(CANTIDAD) as CANTIDAD FROM (SELECT SUM(CASE TIPOMOV 
                            WHEN 'S' THEN CANTIDAD
                            WHEN 'D' THEN CANTIDAD*-1
                            WHEN 'O' THEN CANTIDAD
                            WHEN 'E' THEN CANTIDAD*-1
                                        END) as CANTIDAD
                                FROM REF_HISTXARTXALM r 
                                WHERE r.FK1MREF_ARTXALMACEN= $idRef
                                AND   r.FECHA >= '2019-10-01' AND r.FECHA <='2019-$mes-$numeroDeDiasMes'
                            UNION ALL
                            SELECT  SUM(d.CANTIDAD) as CANTIDAD
                            FROM    REF_PEDIDOSPRESUP p
                            INNER JOIN REF_DETPEDIDOSPRESUP d ON p.ID=d.FKPADREF_PEDIDOSPRESUP
                            WHERE   d.CODIGO='$codigo'
                            AND     p.STATUS='PEDIDO FACTURADO'
                            AND     p.FECHA >= '2019-10-01' AND p.FECHA <='2019-$mes-$numeroDeDiasMes'
                            AND     p.FK1MCFG_ALMACENES=$almacen)";
        
        return self::fireSelect( $queryCantidad );
    }

    public function getTotalVenta( $almacen , $mes , $anio )
    {
        $numeroDeDiasMes = cal_days_in_month(CAL_GREGORIAN, $mes, $anio);
        $queryTotal = "SELECT SUM(CANTIDAD) AS TOTALVENTA FROM VENTAS WHERE FECHA >= '$anio-$mes-01'  AND FECHA <= '$anio-$mes-$numeroDeDiasMes' AND IDALMACEN = $almacen AND FAMILIA = 'OXIFUEL'";

        return self::fireSelect( $queryTotal);
    }

    public function getCostoInventario( $familia )
    {
        $queryCostoInventario = "SELECT ctopromedio, existotal, exispedidos , exisproceso from CFG_ARTICULOS
                                                        inner join REF_ARTXALMACEN on  REF_ARTXALMACEN.FK1MCFG_ARTICULOS = CFG_ARTICULOS.ID
                                                        inner join CFG_PRECIOSXALMACENES on CFG_PRECIOSXALMACENES.FK1MCFG_ARTICULOS =CFG_ARTICULOS.ID
                                                        where ( existotal - ( exispedidos + exisproceso) ) > 0 and CFG_ARTICULOS.FAMILIA in ('$familia')";
        return self::fireSelect( $queryCostoInventario );
    }

    public function buscaArticulosConPvp( $params)
    {
        
        extract( $params );

        $queryArticulo = "SELECT FIRST 700  
							RAXA.ID as IDARTXALMACEN, 
							CFGA.ID,      
                            A2.ZONA as ZONA,                         
							CFGA.CODIGOARTICULO as CODIGO,
							CFGA.DESCRIPCION as DESCRIPCION,
							CFGA.FAMILIA as FAMILIA,
							CFGA.SUBFAMILIA as SUBFAMILIA, 
							RAXA.fk1mcfg_almacenes as ALMACENARTXALM,
							RAXA.CTOPROMEDIO,
							(select MAX(REF_COMPRASTRASPREGS.FECHA)
                                     from REF_DETCOMPRASTRASPREGS
                                     INNER JOIN REF_COMPRASTRASPREGS ON REF_COMPRASTRASPREGS.ID = REF_DETCOMPRASTRASPREGS.FKPADREF_COMPRASTRASPREGS
                                     WHERE ( (REF_COMPRASTRASPREGS.SECCION LIKE '%COMPRA%' AND REF_COMPRASTRASPREGS.STATUS = 'COMPRA EMITIDO') OR REF_COMPRASTRASPREGS.SECCION like '%ENTRADA%' )
                                     AND REF_DETCOMPRASTRASPREGS.CODIGO = CFGA.CODIGOARTICULO) AS ULTCOMPRA,                            
							(RAXA.existotal-RAXA.exispedidos-RAXA.exisproceso) AS existotal ,
							(SELECT REPLACE(REPLACE(PA1.PVP1,'$',''),',','') FROM cfg_preciosxalmacenes PA1 WHERE PA1.fk1mcfg_articulos=CFGA.id AND PA1.fk1mcfg_almacenes=10754) as PVP1,
							(SELECT REPLACE(REPLACE(PA1.PVP2,'$',''),',','') FROM cfg_preciosxalmacenes PA1 WHERE PA1.fk1mcfg_articulos=CFGA.id AND PA1.fk1mcfg_almacenes=10754) as PVP2,
							(SELECT REPLACE(REPLACE(PA1.PVP3,'$',''),',','') FROM cfg_preciosxalmacenes PA1 WHERE PA1.fk1mcfg_articulos=CFGA.id AND PA1.fk1mcfg_almacenes=10754) as PVP3,";
					for($i=4;$i<=6;$i++){
                        if($this->DameVALORPARAMETRO("cfg_valorcamposgenericos", "valor", "where nombretabla='GENERICO' and nombrecampo='PERMITIR AL USUARIO VER PRECIO PVP".$i." EN CONSULTA RAPIDA DE INVENTARIOS ?' and modulo='REF'", $idusuario) == "S" || $idusuario == 0){
                            $queryArticulo.= "(SELECT   CASE  WHEN PA1.PVP$i  != '' THEN  REPLACE(REPLACE(PA1.PVP".$i.",'$',''),',','')
                                                                            WHEN PA1.PVP$i = '' THEN 0 
                                                                            END AS PVP$i FROM cfg_preciosxalmacenes PA1 WHERE PA1.fk1mcfg_articulos=CFGA.id AND PA1.fk1mcfg_almacenes=10754) as PVP".$i.",";
						}else{
                            if($i==4){
                                $queryArticulo.= "(SELECT CASE WHEN CFGA.familia='COLISION' THEN REPLACE(REPLACE(PA1.PVP4,'$',''),',','') ELSE '' END AS PVP4 FROM cfg_preciosxalmacenes PA1 WHERE PA1.fk1mcfg_articulos=CFGA.id AND PA1.fk1mcfg_almacenes=10754) as PVP4,";
                            }
                        }
					}			
                    


		$queryArticulo.= "	RAXA.CTOPROMEDIO as COSTO,A2.DESCRIPCION AS ALMACEN
                            from 	   cfg_articulos CFGA 
                                    inner join ref_artxalmacen RAXA on CFGA.id=RAXA.fk1mcfg_articulos 
                                    inner join cfg_almacenes A2 on A2.id=RAXA.fk1mcfg_almacenes 
                            where 	CFGA.itemservicio=''
                                        AND 		CFGA.familia NOT IN ('APARTADO','SERVICIO')
                                        AND  ( CFGA.CODIGOARTICULO LIKE '%$codigo%'  OR CFGA.DESCRIPCION LIKE '%$codigo%' ) AND (RAXA.existotal-RAXA.exispedidos-RAXA.exisproceso) > 0 
                                        AND CFGA.FAMILIA LIKE '%$familia%' AND CFGA.subfamilia  LIKE '%$subfamilia%' 
                                        AND A2.ZONA IN ('ALTOS','CENTRO','COSTA')
                            ";
                                        

        $exeArticulo = self::fireSelect($queryArticulo);

		//echo $queryArticulo;
        return $exeArticulo;
    }

    
    public function buscaArticulosAgrup( $params)
    {
        
        extract( $params );
        if(trim($almacen)=='' || trim($almacen)=='%' || trim($almacen)=='0')
            $almacen = '';
        if(trim($familia)=='' || trim($familia)=='%')
            $familia = '';
        if(trim($subfamilia)=='' || trim($subfamilia)=='%' || trim($subfamilia)=='%25')
            $subfamilia = '';
        $queryArticulo = "SELECT     
                            A2.DESCRIPCION as ALMACEN,
							CFGA.FAMILIA as FAMILIA,
							CFGA.SUBFAMILIA as SUBFAMILIA, 
                            SUM(RAXA.existotal-RAXA.exispedidos-RAXA.exisproceso) as existotal
                            from 	cfg_articulos CFGA 
                                    inner join ref_artxalmacen RAXA on CFGA.id=RAXA.fk1mcfg_articulos 
                                    inner join cfg_almacenes A2 on A2.id=RAXA.fk1mcfg_almacenes 
                            where 	CFGA.itemservicio=''
                            AND 	A2.ID LIKE '%".trim($almacen)."%'
                            AND     (RAXA.existotal-RAXA.exispedidos-RAXA.exisproceso) > 0 
                            AND     CFGA.FAMILIA LIKE '%".trim($familia)."%' AND CFGA.subfamilia  LIKE '%".trim($subfamilia)."%' 
                            AND     A2.ZONA IN ('ALTOS','CENTRO','COSTA')
                            GROUP BY A2.DESCRIPCION,CFGA.FAMILIA,CFGA.SUBFAMILIA";
                                        

        $exeArticulo = self::fireSelect($queryArticulo);

		//return $queryArticulo;
        return $exeArticulo;
    }

    public function getCantidadVendid( $codigo )
    {

        $anioAct = date("Y");
        $queryCantidad = "SELECT sum(cantidad) CANT,EXTRACT(MONTH FROM REF_PEDIDOSPRESUP.FECHA) MES
        FROM REF_PEDIDOSPRESUP
        INNER JOIN REF_DETPEDIDOSPRESUP on REF_PEDIDOSPRESUP.ID = REF_DETPEDIDOSPRESUP.FKPADREF_PEDIDOSPRESUP
        WHERE REF_PEDIDOSPRESUP.STATUS LIKE '%EMITIDO%' and codigo = '$codigo' AND EXTRACT(YEAR FROM REF_PEDIDOSPRESUP.FECHA) = $anioAct
        GROUP BY EXTRACT(MONTH FROM REF_PEDIDOSPRESUP.FECHA) ";

        return $this->fireSelect( $queryCantidad );
    }

    public function getPvpXCod($cod, $descripcion,$familia){
        
        $condicion = "";
        $precios="";
        //die($familia);
        if($familia != "ALL"){
            $exp = explode(',',$familia);
            $in = "";
            foreach($exp as $v)
                $in.= "'".$v."',";
            $in = substr($in,0,-1);
            $condicion.= " AND CFGA.FAMILIA IN (".$v.") ";
        }

        if($cod != ""){
            $condicion .= "AND
                            CFGA.CODIGOARTICULO LIKE '%$cod%'";
        }else if($descripcion != ""){
            $condicion .= "AND
                            CFGA.DESCRIPCION LIKE '%$descripcion%'";
        }

        $query = "SELECT
                    CFGA.CODIGOARTICULO,
                    CFGA.DESCRIPCION,
                    CFGPXA.PVP1,
                    CFGPXA.PVP2,
                    CFGPXA.PVP3,
                    CFGPXA.PVP4,
                    CFGPXA.PVP5,
                    CFGPXA.PVP6,
                    CFGPXA.PVP7,
                    CFGPXA.PVP8,
                    CFGPXA.PVP9,
                    CFGPXA.PVP10,
                    (RAXA.EXISTOTAL-RAXA.EXISPEDIDOS-RAXA.EXISPROCESO) as EXISTOTAL,
                    A2.DESCRIPCION AS ALMACEN
                FROM
                    CFG_ARTICULOS CFGA
                INNER JOIN
                    CFG_PRECIOSXALMACENES CFGPXA
                ON
                    CFGA.ID = CFGPXA.FK1MCFG_ARTICULOS
                INNER JOIN
                    REF_ARTXALMACEN RAXA 
                ON
                    CFGA.ID = RAXA.FK1MCFG_ARTICULOS
                INNER JOIN
                    CFG_ALMACENES A2 ON A2.ID=RAXA.FK1MCFG_ALMACENES 
                WHERE
                    CFGPXA.FK1MCFG_ALMACENES = '10754'
                AND
                    (RAXA.EXISTOTAL-RAXA.EXISPEDIDOS-RAXA.EXISPROCESO) > 0
                $condicion
                GROUP BY
                    CFGA.CODIGOARTICULO,
                    CFGA.DESCRIPCION,
                    CFGPXA.PVP1,
                    CFGPXA.PVP2,
                    CFGPXA.PVP3,
                    CFGPXA.PVP4,
                    CFGPXA.PVP5,
                    CFGPXA.PVP6,
                    CFGPXA.PVP7,
                    CFGPXA.PVP8,
                    CFGPXA.PVP9,
                    CFGPXA.PVP10,
                    EXISTOTAL,
                    ALMACEN
                ";
        //die($query);
        $sentence = self::fireSelect( $query );
		return $sentence;
    }
    public function DameVALORPARAMETRO($tabla,$campo,$condicion,$idusuario){
		$query = "SELECT ".$campo." FROM ".$tabla." ".$condicion." and fk1mcfg_usuarios=".$idusuario;
        
        
		$sentence = self::fireSelect( $query );
		
		
		if(sizeof($sentence)==0){
			$squery = "SELECT ".$campo." FROM ".$tabla." ".$condicion." and fk1mcfg_usuarios=-1";
			$sentence = self::fireSelect( $squery );
			return $sentence[0]->VALOR;
		}else{
			return $sentence[0]->VALOR;
		}
	
		
    }
    
}

