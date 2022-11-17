<?php

require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/DB.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/FirebirDB.php";



class Ventas extends DB
{
    protected $firebird;

    public function __construct() {
        $this->firebird = new FirebirdDB;
        parent::__construct();    
    }

    public function getUtilidadVenta( $mes )
    {
        //obteniendo el periodo
        $mesAnioSplit = explode("-", $mes );

        $numDiasMes = cal_days_in_month(CAL_GREGORIAN, $mesAnioSplit[1], $mesAnioSplit[0]);

        $queryUtilidad = "SELECT sum(importelinea+detiva) AS VENTA ,sum(costolinea) AS COSTO ,idalmacen,almacen
                                        from ventas
                                        where fecha >= '$mes-01' and fecha <= '$mes-$numDiasMes'
                                        group by idalmacen,almacen";


        return $this->firebird->fireSelect( $queryUtilidad );
    }

    public function getGastosOperativos( $mes )
    {
        //obteniendo el periodo
        $mesAnioSplit = explode("-", $mes );
        $numDiasMes = cal_days_in_month(CAL_GREGORIAN, $mesAnioSplit[1], $mesAnioSplit[0]);

        $query = "SELECT date(timestamp) as fecha, idsucursal
                    FROM dbnomina.pparking 
                    WHERE MONTH(TIMESTAMP) = $mes AND YEAR(TIMESTAMP) =  $anio AND idsucursal= $sucursalId
                    ORDER BY id desc";

        return $this->select( $query );
    }

    public function getSucursalesConVenta()
    {
        $query = "SELECT *
                    from CFG_ALMACENES
                    where PWD IN ('MATRIX','ONLINE')
                    ORDER by PWD,DESCRIPCION";


        return $this->firebird->fireSelect( $query );
    }

    public function getFlujoIngresos( $fecha )
	{
		 $queryIngresos = "SELECT P.fk1mcfg_almacenes,PAG.formapago,PAG.referencia,SUM(PAG.importe) AS importe, CFG_ALMACENES.DESCRIPCION
		 FROM ref_pedidospresup P
		  INNER JOIN ref_pedidospresupformspags PAG on P.id=PAG.fk1mref_pedidospresup 
		  INNER JOIN CFG_ALMACENES on CFG_ALMACENES.ID = p.FK1MCFG_ALMACENES
		  WHERE P.status in ('PEDIDO EMITIDO','PEDIDO FACTURADO') and P.fecha='$fecha' and (PAG.formapago like '%01%' or PAG.formapago like '%EFECTIVO%' or 
		  PAG.formapago like '%02%' or PAG.formapago like '%03%' or PAG.formapago like '%04%' or PAG.formapago like '%28%') 
		  and CFG_ALMACENES.PWD in ('MATRIX','LEON','OXIFUEL')
		  GROUP BY P.fk1mcfg_almacenes,CFG_ALMACENES.DESCRIPCION,PAG.formapago,PAG.referencia 
		  ORDER BY P.fk1mcfg_almacenes,PAG.formapago,PAG.referencia";

		  return $this->firebird->fireSelect( $queryIngresos );
    }

    public function getFlujoIngresosVentasOnline( $fecha )
    {
        $queryIngresos = "SELECT P.fk1mcfg_almacenes,PAG.formapago,PAG.referencia,SUM(PAG.importe) AS importe, CFG_ALMACENES.DESCRIPCION
        FROM ref_pedidospresup P
         INNER JOIN ref_pedidospresupformspags PAG on P.id=PAG.fk1mref_pedidospresup 
         INNER JOIN CFG_ALMACENES on CFG_ALMACENES.ID = p.FK1MCFG_ALMACENES
         WHERE P.status in ('PEDIDO EMITIDO','PEDIDO FACTURADO') and P.fecha='$fecha' and (PAG.formapago like '%01%' or PAG.formapago like '%EFECTIVO%' or 
         PAG.formapago like '%02%' or PAG.formapago like '%03%' or PAG.formapago like '%04%' or PAG.formapago like '%28%')  
         and CFG_ALMACENES.PWD in ('ONLINE')
         GROUP BY P.fk1mcfg_almacenes,CFG_ALMACENES.DESCRIPCION,PAG.formapago,PAG.referencia 
         ORDER BY P.fk1mcfg_almacenes,PAG.formapago,PAG.referencia";

         return $this->firebird->fireSelect( $queryIngresos );
    }
    
    //AND P.FK1MCFG_VENDEDORES  IN (7960)

    public function getTotalVentasMes( $fechaInicio , $fechaFin)
    {
        $queryVentas = "SELECT sum(importelinea + detiva) as TOTAL
                                        FROM VENTAS
                                         WHERE fecha >= '$fechaInicio' and fecha <= '$fechaFin' ";

        return $this->firebird->fireSelect( $queryVentas );
    }

    public function getAlineacionesBySucursal( $sucursalId , $mes , $anio = 2019 )
    {
        $queryAlineaciones = "SELECT * 
                            FROM VENTAS 
                            WHERE FAMILIA = 'SERVICIO' AND SUBFAMILIA = 'ALINEACION' AND IDALMACEN = $sucursalId  AND extract( MONTH FROM FECHA ) = $mes AND
                                            EXTRACT( YEAR FROM FECHA )  = $anio
                            ORDER BY ID DESC";
        
        return $this->firebird->fireSelect( $queryAlineaciones );
    }

    public function getTodasAlineaciones( $sucursalId  )
    {
        $queryAlineaciones = "SELECT IDALMACEN as sucursalID,NUMDOCTO as folioVentaAlineacion,FECHA as fechaAlineacion,ALMACEN
                            FROM VENTAS 
                            WHERE FAMILIA = 'SERVICIO' AND SUBFAMILIA = 'ALINEACION' AND IDALMACEN = $sucursalId AND fecha >= '2019-06-01'
                            ORDER BY FECHA DESC";
        
        return $this->firebird->fireSelect( $queryAlineaciones );
    }



    public function getAlineacionesPorSensores( $sucursalId , $mes , $anio = 2019 )
    {
        $querySensores = "SELECT date(timestamp) as fecha, idsucursal
                            FROM dbnomina.pparking 
                            WHERE MONTH(TIMESTAMP) = $mes AND YEAR(TIMESTAMP) =  $anio AND idsucursal= $sucursalId
                            ORDER BY id desc";

        return $this->select( $querySensores );
    }

    public function getTodasAlineacionesPorSensores( $sucursalId  )
    {
        $querySensores = "SELECT date(timestamp) as fecha, idsucursal
                            FROM dbnomina.pparking 
                            WHERE idsucursal= $sucursalId
                            ORDER BY timestamp desc";

        return $this->select( $querySensores );
    }

    public function getSucursalesConSensores( )
    {
        $querySucursalSensor = "SELECT idsucursal
                                FROM dbnomina.pparking 
                                GROUP BY  idsucursal";

        return $this->select( $querySucursalSensor );
    }

    public function getVentasEnLinea( $mes , $anio)
    {
                //accediendo a una nueva conexion porque es otra base de ddatos
                $this->conexion = new mysqli('127.0.0.1','sestrada','M@tr1x2017','matrix_eshop');
                mysqli_query($this->conexion,"SET NAMES 'utf8");
        
                $queryVentaOnline = "SELECT  folio_venta, fecha_realizado,fecha_enviado,usuarios.nombre, usuarios.appaterno, usuarios.apmaterno,usuarios.telefono,
                usuarios.email,idSitex AS prospectoId, direccion.calle, direccion.colonia, direccion.numero, direccion.cp, direccion.pais,
                direccion.ciudad,direccion.estado, responsable_recibido.nombre AS nombreRecibe, responsable_recibido.appaterno AS paternoRecibe,
                responsable_recibido.apmaterno AS maternoRecibe
                        FROM ventas
                        INNER JOIN usuarios ON usuarios.id = ventas.idusuario
                        INNER JOIN direccion ON direccion.id = ventas.iddireccion
                        INNER JOIN responsable_recibido ON responsable_recibido.id = ventas.idresponsable
                        WHERE MONTH(fecha_realizado) = $mes AND YEAR(fecha_realizado) = $anio ";
        
                return $this->select( $queryVentaOnline );
    }

    
    public function getMontoVentaEnlinea( $folioVenta )
    {
        $queryVenta = "SELECT *
                        FROM REF_PEDIDOSPRESUP
                        WHERE NUMDOCTO = '$folioVenta' ";
        return $this->firebird->fireSelect( $queryVenta );
    }

    public function getTotalVentasSucursal( $mes, $anio,$online=false)
    {
        $condicionOnline = $online == false ? " not in " : " in ";
        $queryMontos = "SELECT P.fk1mcfg_almacenes,SUM(PAG.importe) AS importe, CFG_ALMACENES.DESCRIPCION
        FROM ref_pedidospresup P
         INNER JOIN ref_pedidospresupformspags PAG on P.id=PAG.fk1mref_pedidospresup 
         INNER JOIN CFG_ALMACENES on CFG_ALMACENES.ID = p.FK1MCFG_ALMACENES
         WHERE P.status in ('PEDIDO EMITIDO','PEDIDO FACTURADO') and Extract(MONTH from P.fecha)= $mes and EXTRACT(YEAR FROM P.fecha )= $anio and (PAG.formapago like '%01%' or PAG.formapago like '%EFECTIVO%' or 
         PAG.formapago like '%02%' or PAG.formapago like '%03%' or PAG.formapago like '%04%' or PAG.formapago like '%28%') 
         and CFG_ALMACENES.PWD in ('MATRIX','LEON','OXIFUEL','ONLINE') and p.FK1MCFG_VENDEDORES  $condicionOnline (7960)
         GROUP BY P.fk1mcfg_almacenes,CFG_ALMACENES.DESCRIPCION
         ORDER BY IMPORTE desc";



         return ($this->firebird->fireSelect( $queryMontos ) );
    }

    public function getIngresosCXC( $mes , $anio, $online= false)
    {
        $condicionOnline = $online == false ? " not in " : " in ";
        $queryCobranza = "SELECT D.fk1mcfg_vendedores,D.fk1mcfg_almacenes,RP.formapago,sum(RP.importe) as importe From (cxc_historcc H inner join cxc_recibosformspags RP on H.id=RP.fk1mcxc_historcc
        inner join cxc_recibos R on R.imp_cobro_historcc=H.id inner join cxc_historcc HO on R.imp_factu_historcc=HO.id 
        inner join cfg_doctos D on D.id=HO.fk11cfg_cabdoctos) 
        Where 
               (H.concepto like '%INGXPAGPARCIAL%' OR H.concepto like '%INGXANTICIPO%') and EXTRACT(MONTH from H.fechamovi) = $mes AND EXTRACT(YEAR FROM H.fechamovi) =$anio
               and D.fk1mcfg_almacenes in (10816,10754,10780,10763,10757,10760,10787,10755,10756,748,10771,10772,10775,10778,10791,10828,10829,10831,10835,10834,10844,10837)  and  D.fk1mcfg_vendedores $condicionOnline (8091,7960,7903)
               group by D.fk1mcfg_vendedores,D.fk1mcfg_almacenes,RP.formapago order by D.fk1mcfg_almacenes,RP.formapago";


        return $this->firebird->fireSelect( $queryCobranza );
    }

    public function getMetasSucursales()
    {
        //accediendo a una nueva conexion porque es otra base de ddatos
        $this->conexion = new mysqli('127.0.0.1','sestrada','M@tr1x2017','dbnomina');
        mysqli_query($this->conexion,"SET NAMES 'utf8");

        $queryMetas = "SELECT * FROM  metaventas WHERE STATUS = 1";

        return $this->select( $queryMetas );
    }

    public function getTotalComprasEdoResultados( $fechaInicio , $fechaFin)
	{
		$queryTotalCompras= "SELECT sum(ctopartida + impiva) as IMPORTETOTAL from compras where fecha >= '$fechaInicio' and fecha <= '$fechaFin' ";
                                
		return $this->firebird->fireSelect( $queryTotalCompras );
    }
    
    public function getTotalVentasCredito( $fechaInicio , $fechaFin)
    {
        
        $queryVentasCredito = "SELECT SUM(ref_pedidospresup.TOTAL) AS TOTAL
                                                        from ref_pedidospresup 
                                                        inner join CFG_DOCTOS on CFG_DOCTOS.FK11PEDIDOID = REF_PEDIDOSPRESUP.ID
                                                        where ref_pedidospresup.FECHA>= '$fechaInicio' AND ref_pedidospresup.FECHA <= '$fechaFin' and REF_PEDIDOSPRESUP.serdocto = 'CREDITO' 
                                                        AND REF_PEDIDOSPRESUP.STATUS IN ('PEDIDO FACTURADO','PEDIDO EMITIDO') and CFG_DOCTOS.NUMDOCTO not like '%INT%' ";
                                               
        return $this->firebird->fireSelect( $queryVentasCredito );
    }

    public function getRazonesVentasFallidas( )
    {
        $queryRazones = "SELECT *
                                        FROM dbnomina.crazones_ventasfallidas
                                        WHERE STATUS = 1";
        return $this->select( $queryRazones );
    }

    public function setHistorialControlPiso( $params )
    {
        extract( $params );

        $razonFallida = " 'ok',NULL ";
        if ( $razon != '-1' ) {
            $razonFallida = " 'fail','$razon' ";
        }
        $queryControlPiso = "INSERT INTO pcontrol_piso(vendedor,cliente,vehiculo,correo,producto,estado_venta,razon_fallida) VALUES( '$vendedor','$cliente','$vehiculo','$email','$producto',$razonFallida)";

        return $this->insert( $queryControlPiso);
    }

    public function getClientesActivos( $nombre )
    {
        
        $queryClientesActivos ="SELECT c.ID_PROSPECTO as ID,c.PYM_NOMBRE as NOMBRE
                                FROM CRM_PROSPECTOS c 
                                WHERE c.PYM_NOMBRE LIKE '%".$nombre."%'";
        //die($queryClientesActivos);     
        return $this->firebird->fireSelect( $queryClientesActivos );
    }

    public function getTicketsFiltro( $cliente, $almacen, $fechainicial, $fechafinal, $formapago )
    {        
        $query = "  SELECT  p.ID as ID, 
                            p.FECHA as FECHA,
                            p.NUMDOCTO as FOLIO,
                            p.TOTAL as IMPORTE,
                            c.PYM_NOMBRE as CLIENTE,
                            a.DESCRIPCION as ALMACEN,
                            f.FORMAPAGO as FORMAPAGO,
                            f.IMPORTE as IMPORTEFP,
                            CASE WHEN p.ESPEDLIQAPARTADO='L' THEN 1 ELSE 0 END as LIQUIDACION
                    FROM    REF_PEDIDOSPRESUP p
                    INNER JOIN REF_PEDIDOSPRESUPFORMSPAGS f ON p.ID=f.FK1MREF_PEDIDOSPRESUP
                    INNER JOIN CRM_PROSPECTOS c ON p.FK1MCRM_PROSPECTOS=c.ID_PROSPECTO 
                    INNER JOIN CFG_ALMACENES a ON p.FK1MCFG_ALMACENES=a.ID
                    WHERE   p.STATUS='PEDIDO EMITIDO'
                    AND     p.ESPEDLIQAPARTADO NOT IN ('AA')
                    AND     p.FECHA>='$fechainicial' AND p.FECHA<='$fechafinal'"; 
        if($cliente>0)
            $query.= "  AND     p.FK1MCRM_PROSPECTOS='$cliente'";        
        if($almacen>0)
            $query.= "  AND     p.FK1MCFG_ALMACENES='$almacen'";
        $query.= "  AND     f.IMPORTE>0
                    ORDER BY ALMACEN,FECHA,FOLIO,IMPORTEFP DESC";
        //return $query;     
        $result = $this->firebird->fireSelect( $query );
        $arr = array();
        $arrTMP = array();
        $n=0;
        $tmp = "";
        foreach($result as $row){
            if(!in_array($row->ID,$arrTMP)){
                $arrTMP[] = $row->ID;
                $arr[$n]['ID'] = $row->ID;
                $arr[$n]['FECHA'] = $row->FECHA;
                $arr[$n]['FOLIO'] = $row->FOLIO;
                $arr[$n]['IMPORTE'] = $row->IMPORTE;
                $arr[$n]['CLIENTE'] = utf8_encode($row->CLIENTE);
                $arr[$n]['ALMACEN'] = $row->ALMACEN;
                $arr[$n]['FORMAPAGO'] = $row->FORMAPAGO;
                $arr[$n]['IMPORTEFP'] = $row->IMPORTEFP;                
                $arr[$n]['LIQUIDACION'] = $row->LIQUIDACION;             
                $n++;                
            }
        }
        if(strlen($formapago)>3){
            foreach($arr as $idx => $val){
                $formp = explode('#',$val['FORMAPAGO']);   
                if($formapago!=$formp[0]){
                    unset($arr[$idx]);
                }
            }       
            return [$arr];     
        }else{
            return [$arr];
        }
    }

    public function getAnticiposLiq( $id )
    {
        $intxt = '';
        $query1 = " SELECT *
                    FROM REF_PEDPRELIQAPARTADO r 
                    WHERE r.FK1MREF_PADPRE=".$id; 
        $result1 = $this->firebird->fireSelect( $query1 );
        foreach($result1 as $row1){
            $intxt.= $row1->FK1MREF_PADPREABONOANT.",";
        }
        $intxt = substr($intxt,0,-1);
                
        $query = "  SELECT  p.ID as ID, 
                            p.FECHA as FECHA,
                            p.NUMDOCTO as FOLIO,
                            p.TOTAL as IMPORTE,
                            c.PYM_NOMBRE as CLIENTE,
                            a.DESCRIPCION as ALMACEN,
                            f.FORMAPAGO as FORMAPAGO,
                            f.IMPORTE as IMPORTEFP,
                            CASE WHEN p.ESPEDLIQAPARTADO='AA' THEN 1 ELSE 0 END as ANTICIPO
                    FROM    REF_PEDIDOSPRESUP p
                    INNER JOIN REF_PEDIDOSPRESUPFORMSPAGS f ON p.ID=f.FK1MREF_PEDIDOSPRESUP
                    INNER JOIN CRM_PROSPECTOS c ON p.FK1MCRM_PROSPECTOS=c.ID_PROSPECTO 
                    INNER JOIN CFG_ALMACENES a ON p.FK1MCFG_ALMACENES=a.ID
                    WHERE   p.ID IN (".$intxt.")
                    AND     f.IMPORTE>0
                    ORDER BY ALMACEN,FECHA,FOLIO,IMPORTEFP DESC";
        //return $query;     
        $result = $this->firebird->fireSelect( $query );
        $arr = array();
        $arrTMP = array();
        $n=0;
        $tmp = "";
        foreach($result as $row){
            if(!in_array($row->ID,$arrTMP)){
                $arrTMP[] = $row->ID;
                $arr[$n]['ID'] = $row->ID;
                $arr[$n]['FECHA'] = $row->FECHA;
                $arr[$n]['FOLIO'] = $row->FOLIO;
                $arr[$n]['IMPORTE'] = $row->IMPORTE;
                $arr[$n]['CLIENTE'] = utf8_encode($row->CLIENTE);
                $arr[$n]['ALMACEN'] = $row->ALMACEN;
                $arr[$n]['FORMAPAGO'] = $row->FORMAPAGO;
                $arr[$n]['IMPORTEFP'] = $row->IMPORTEFP;               
                $arr[$n]['ANTICIPO'] = $row->ANTICIPO;             
                $n++;                
            }
        }
        return $arr;
    }

    public function getFormasPagos()
    {        
        $query = "  SELECT r.FORMAPAGO
                    FROM REF_PEDIDOSPRESUPFORMSPAGS r 
                    WHERE r.FORMAPAGO LIKE '%#%'
                    GROUP BY r.FORMAPAGO
                    ORDER BY r.FORMAPAGO ASC";
        //die($queryClientesActivos);     
        return $this->firebird->fireSelect( $query );
    }

    public function getUsoCFDI()
    {        
        $query = "SELECT r.ID as ID, r.VALOR as VALOR
                    FROM CFG_VALORCAMPOSGENERICOS r 
                    WHERE r.NOMBRECAMPO LIKE 'USO DE CFDI EN VER 3.3 EJEMPLO (ADQUISICION DE MERCANCIAS#G01)' ORDER BY VALOR ASC";
        //die($queryClientesActivos);     
        return $this->firebird->fireSelect( $query );
    }
}
