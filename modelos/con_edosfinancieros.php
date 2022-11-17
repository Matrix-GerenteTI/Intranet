<?php
if(!isset($_SESSION)){ 
	session_start(); 
}
 require_once dirname(__DIR__)."/modelos/MySQL.php";
 
class EdoFinancieros extends CMySQLi{
	
	private $conexionIbase ;

	private function conexionFirebird()
	{
		$host = "172.16.0.70:/var/lib/firebird/3.0/data/PREDICTION.FDB";
		$user="SYSDBA";
		$pass="masterkey";
		$this->conexionIbase = @ibase_pconnect($host,$user,$pass) or die("Error al conectarse a la base de datos: ".ibase_errmsg());
		return $this->conexionIbase;
	}

    public function getMovimientos($udn,$fecini,$fecfin){
		$query = "SELECT 	SUM(total) as total,docfecha
					  FROM 	con_movimientos
					  WHERE	docfecha between '$fecini' AND '$fecfin'
					  AND	status=1 
					  AND 	idcudn LIKE '$udn'
					  GROUP BY docfecha";
        return $this->select($query);
    }

	public function getSucursalesConVenta()
    {
		$query = "SELECT * FROM csucursal WHERE preporte=1 AND status=1";
        return $this->select($query);
    }
	
	public function getDetallesCuenta($udn,$fecini,$fecfin,$cuenta){
		$query = "SELECT 	m.id as id,
							IFNULL(m.docserie,'') as serie,
							IFNULL(m.docfolio,'') as folio,
							IFNULL((SELECT IFNULL(f.nombreemisor,'') FROM con_facturassat f WHERE f.uuid=m.docuuid),'') as emisor,
							IFNULL((SELECT IFNULL(f.nombrereceptor,'') FROM con_facturassat f WHERE f.uuid=m.docuuid),'') as receptor,
							m.docuuid as uuid,
							m.docfecha as fecha,
							m.descripcion as concepto,
							m.total as importe,
							u.descripcion as udn,
							'".$_SESSION['nivel']."' as nivel
				  FROM 		con_movimientos m
				  INNER JOIN con_cuentas c ON m.idcon_cuentas=c.id
				  INNER JOIN csucursal u ON m.idcudn=u.id
				  WHERE		m.status = 1 AND m.docfecha>='".$fecini."'
				  AND		m.docfecha<='".$fecfin."'
				  AND 		c.cuenta='".$cuenta."'
				  AND		m.status=1
				  AND 		m.idcudn LIKE '".$udn."'";
        return $this->select($query);
    }
	
	public function getDetalleGastoCuenta( $cuenta, $fechaInicio, $fechaFin)
	{
		$queryDetalleGasto = "SELECT cm.* 
								FROM con_cuentas as cc
								INNER JOIN con_movimientos as cm on cm.idcon_cuentas = cc.id
								WHERE cc.cuenta='".$cuenta."' and  cm.docfecha>='".$fechaInicio."' and cm.docfecha<='".$fechaFin."' ";
		die($queryDetalleGasto);
		//return "ALGO :: ".$queryDetalleGasto;
		return $this->select( $queryDetalleGasto);
	}

	public function getMovimientosCuentaTotal($udn,$fecini,$fecfin,$cuenta){
		$query = "SELECT 	SUM(m.total) as subtotal
				  FROM 		con_movimientos m
				  INNER JOIN con_cuentas c ON m.idcon_cuentas=c.id
				  WHERE		m.status = 1 AND m.docfecha>='".$fecini."'
				  AND		m.docfecha<='".$fecfin."'
				  AND 		c.cuenta='".$cuenta."'
				  AND 		m.idcudn>0
				  AND		m.status=1 AND ( m.tipo_movimiento = 1 OR m.tipo_movimiento is null)
				  AND 		m.idcudn LIKE '".$udn."'";
				  
		$row = $this->select($query);
        return $row[0]['subtotal'];
    }
	
	public function getTotalCXP($fecini,$fecfin){
		$host = "172.16.0.70:/var/lib/firebird/3.0/data/PREDICTION.FDB";
		$user="SYSDBA";
		$pass="masterkey";
		$conexion = @ibase_pconnect($host,$user,$pass) or die("Error al conectarse a la base de datos: ".ibase_errmsg());
		$array = array();
		$query = "Select 	R.NUMANTICIPO,
							H.FECHAMOVI,
							H.CONCEPTO,
							H.IMPORTECOBRO as IMPORTECOBRO,
							H.COMENTARIO,
							P.ID_PROSPECTO AS CODIGO,
							P.PYM_NOMBRE AS CF1,
							H.ID AS ID1,
							H.ID AS ID2,
							H.ID AS ID3,
							H.ID AS ID4,
							H.ID AS ID5, 
							'COBROS' AS FORMALISTAANTICIPOS,
							H.fk1mcfg_usuarios 
				  From 		(crm_prospectos P 
							inner join cxp_historcp H on H.fk1mcrm_prospectos=P.id_prospecto 
							inner join cxp_recibos R on R.imp_cobro_historcp=H.id) 
				  Where 	((R.facturaproveedor='T' and R.numanticipo not like '%(ABONOMANUAL)'  and R.numanticipo<>'') or R.numanticipo like '%(EGRXPAGDOCTO)') 
				  and 		H.fechamovi>='".$fecini."'
				  and 		H.fechamovi<='".$fecfin."'
				  group by R.NUMANTICIPO,H.FECHAMOVI,H.CONCEPTO,H.IMPORTECOBRO,H.COMENTARIO,P.ID_PROSPECTO,P.PYM_NOMBRE,H.ID,H.FK1MCFG_USUARIOS 
				  Order by 13,R.numanticipo";
		$sentence = ibase_query($conexion,$query);
		while($row = ibase_fetch_assoc($sentence)){
			$array[] = $row;
		}
		/*
		//OBTENEMOS LOS COSTOS
		$queryc =   "select  sum(DET.importelinea + DET.impiva) as ventas,
							sum(DET.costolinea) as IMPORTECOBRO
					from    ref_pedidospresup P  
					inner join REF_DETPEDIDOSPRESUP DET on P.id=DET.fkpadref_pedidospresup 
					where   P.status in ('PEDIDO EMITIDO','PEDIDO FACTURADO') 
					and     (P.fecha>=CAST('".$fecini."' as date) and P.fecha<=CAST('".$fecfin."' as date)) 
					and     P.SERDOCTO<>'CREDITO'
					and 	P.FK1MCFG_ALMACENES LIKE '%' ";
		$sentencec = ibase_query($conexion,$queryc);
		$costotot = 0;
		while($rowc = ibase_fetch_assoc($sentencec)){
			$array[] = $rowc;
			//$costotot = $costotot + $rowc['COSTOS'];
		}*/
		
		return $array;
    }
	
	public function getTotalVentas($udn,$fecini,$fecfin){
		if($udn>0){
			$query = "SELECT 	idprediction as almacen
					  FROM 		csucursal
					  WHERE		id='".$udn."'";
			$row = $this->select($query);
			$fkalmacen = $row[0]['almacen'].".%";
		}else{
			$fkalmacen = '%';
		}
		
		$host = "172.16.0.70:/var/lib/firebird/3.0/data/PREDICTION.FDB";
		$user="SYSDBA";
		$pass="masterkey";
		$conexion = @ibase_pconnect($host,$user,$pass) or die("Error al conectarse a la base de datos: ".ibase_errmsg());
		$array = array('ventas'=>0,
					   'costos'=>0);
		
		$query =   "select 	P.fk1mcfg_almacenes, 
							PAG.formapago, 
							PAG.referencia, 
							sum(PAG.importe) as importe 
					from 	ref_pedidospresup P 
					inner join ref_pedidospresupformspags PAG on P.id=PAG.fk1mref_pedidospresup 
					where 	P.status in ('PEDIDO EMITIDO','PEDIDO FACTURADO') 
					and     (P.fecha>=CAST('".$fecini."' as date) and P.fecha<=CAST('".$fecfin."' as date)) ";
		if($fkalmacen==10754)
			$query.= "	and 	P.FK1MCFG_ALMACENES IN ('10754','10780') ";
		else
			$query.= "	and 	P.FK1MCFG_ALMACENES LIKE '".$fkalmacen."' ";
		
		$query.= "	and (	PAG.formapago like '%01%' or PAG.formapago like '%EFECTIVO%' or PAG.formapago like '%02%' or PAG.formapago like '%03%' or PAG.formapago like '%04%' or PAG.formapago like '%28%') 
					group by P.fk1mcfg_almacenes,PAG.formapago,PAG.referencia order by P.fk1mcfg_almacenes,PAG.formapago,PAG.referencia";
					

					
		$sentence = ibase_query($conexion,$query);
		$ventastot = 0;
		while($row1 = ibase_fetch_assoc($sentence)){
			$ventastot = $ventastot + $row1['IMPORTE'];
		}
		
		//OBTENEMOS LOS COSTOS
		$queryc =   "select  sum(DET.importelinea + DET.impiva) as ventas,
							sum(DET.costolinea) as costos
					from    ref_pedidospresup P  
					inner join REF_DETPEDIDOSPRESUP DET on P.id=DET.fkpadref_pedidospresup 
					where   P.status in ('PEDIDO EMITIDO','PEDIDO FACTURADO') 
					and     (P.fecha>=CAST('".$fecini."' as date) and P.fecha<=CAST('".$fecfin."' as date)) 
					and     P.SERDOCTO<>'CREDITO'";
		if($fkalmacen==10754)
			$queryc.= "	and 	P.FK1MCFG_ALMACENES IN ('10754','10780') ";
		else
			$queryc.= "	and 	P.FK1MCFG_ALMACENES LIKE '".$fkalmacen."' ";
		
		$sentencec = ibase_query($conexion,$queryc);
		$costotot = 0;
		while($rowc = ibase_fetch_assoc($sentencec)){
			$costotot = $costotot + $rowc['COSTOS'];
		}
		
		$query2 =   "Select D.fk1mcfg_almacenes,
							RP.formapago,sum(RP.importe) as importe 
					From 	(cxc_historcc H 
					inner join cxc_recibosformspags RP on H.id=RP.fk1mcxc_historcc 
					inner join cxc_recibos R on R.imp_cobro_historcc=H.id 
					inner join cxc_historcc HO on R.imp_factu_historcc=HO.id 
					inner join cfg_doctos D on D.id=HO.fk11cfg_cabdoctos) 
					Where (H.concepto like '%INGXPAGPARCIAL%' OR H.concepto like '%INGXANTICIPO%') 
					and 	H.fechamovi>=CAST('".$fecini."' as date) and H.fechamovi<=CAST('".$fecfin."' as date) ";
		if($fkalmacen==10754)
			$query2.= "	and 	D.fk1mcfg_almacenes IN ('10754','10780') ";
		else
			$query2.= "	and 	D.fk1mcfg_almacenes LIKE '".$fkalmacen."%' ";
					 
		$query2.= "	group by D.fk1mcfg_almacenes,RP.formapago order by D.fk1mcfg_almacenes,RP.formapago";

		// echo $query2;
		$sentence2 = ibase_query($conexion,$query2); 
		// $row2 = ibase_fetch_assoc($sentence2); 
		$cobrotot = 0;
		while($row2 = ibase_fetch_assoc($sentence2)){
			$cobrotot = $cobrotot + $row2['IMPORTE'];
		}
		if($fkalmacen=='%'){
			$array['ventas'] = $ventastot + $cobrotot;
		}else{
			if($fkalmacen==11)
				$array['ventas'] = $ventastot + $cobrotot;
			else
				$array['ventas'] = $ventastot + $cobrotot;
		}
		
		$array['costos'] = $costotot;
		return $array;
    }
	
	public function getNombreCuenta($cuenta){
		$query = "SELECT 	*
				  FROM 		con_cuentas
				  WHERE		cuenta='".$cuenta."'";
		$row = $this->select($query);
        return $row[0]['nombre'];
    }
	
	public function getCuentas(){
		$query = "SELECT 	*
					  FROM 	con_cuentas
					  WHERE	status=1
					  ORDER BY cuenta";
					  
        return $this->select($query);
	}
	
	public function getCountCuentasOperativas(){
		$query = "SELECT 	COUNT(*) as cantidad
					  FROM 	con_cuentas
					  WHERE	status=1
					  AND padre='601-01-000'
					  ORDER BY nombre";
					  
		$row = $this->select($query);
		return $row[0]['cantidad'];
	}
	
	public function getTotalCompras($udn,$fecini,$fecfin){

		$arrayCompras = array();

		if($udn>0){
			$query = "SELECT 	idprediction as almacen
					  FROM 		csucursal
					  WHERE		id=$udn or idprediction = $udn ";
					  
			$row = $this->select($query);
			$fkalmacen = $row[0]['almacen'].".%";
		}else{
			$fkalmacen = '%';
		}

		$queryCompras = "Select RECOM.NUMDOCTO,
											RECOM.FECHA,RECOM.OBSERVACIONES,
											CRMP.PYM_NOMBRE AS PROVEEDOR,
											CFGALM.DESCRIPCION AS ALMORIGEN,
											CFGALM2.DESCRIPCION AS ALMDTNO,
											CFGALM.ID,
											RECOM.NUMFACTPROV,
											RECOM.FECHAFACTPROV,
											CFGA.CODIGOARTICULO,
											CFGA.DESCRIPCION,
											REDETCOM.CANTIDAD,
											REDETCOM.COSTO,
											(REDETCOM.CANTIDAD * REDETCOM.COSTO) AS CTOPARTIDA,
											REDETCOM.PORCIVA,
											REDETCOM.IMPIVA,
											((REDETCOM.CANTIDAD * REDETCOM.COSTO) + REDETCOM.IMPIVA) AS IMPORTETOTAL,
											CFGA.MARCA,CFGA.FAMILIA,
											'MOVTOS' AS FORMADELISTA 
										From (ref_comprastraspregs RECOM 
														inner join ref_detcomprastraspregs REDETCOM on RECOM.id=REDETCOM.fkpadref_comprastraspregs 
														inner join ref_artxalmacen RAXA on RAXA.id=REDETCOM.fk1mref_artxalmacen 
														inner join cfg_articulos CFGA on CFGA.id=RAXA.fk1mcfg_articulos 
														inner join cfg_almacenes CFGALM on CFGALM.id=RECOM.fk1mcfg_almacenes 
														left outer join cfg_almacenes CFGALM2 on CFGALM2.id=RECOM.fk1mcfg_almacenes2 
														inner join crm_prospectos CRMP on CRMP.id_prospecto=RECOM.fk1mcrm_prospectos) 
										Where RECOM.FECHAFACTPROV BETWEEN '$fecini' AND '$fecfin'  AND CFGALM.id LIKE '$fkalmacen'
													AND (RECOM.STATUS='COMPRA EMITIDO' OR RECOM.STATUS='COMPRA DEVUELTO') 
										Order by 18,RECOM.ID,CFGA.CODIGOARTICULO
		
		";
		
		$exeQueryCompras = ibase_query($this->conexionFirebird(), $queryCompras);

		while ( $rowCompra = ibase_fetch_assoc($exeQueryCompras) ) {
			array_push($arrayCompras , $rowCompra);
		}

		return $arrayCompras;
	}
	
	public function getSucursal( $idSucursal = "%" , $descripcion = "%")
	{

		$querySucursal = "SELECT * FROM csucursal WHERE  (id like '$idSucursal' OR descripcion like '$descripcion' )AND csucursal.status=1";
		
		$sucursal = $this->select( $querySucursal );

		return $sucursal;
	}

	public function getTotalMovCuentaPadre( $idSucursal , $fechaInicio , $fechaFin , $ctaPadre)
	{
		$queryMovientos = "SELECT 	SUM(m.subtotal) as subtotal
											FROM 		con_movimientos m
											INNER JOIN con_cuentas c ON m.idcon_cuentas=c.id
											WHERE		m.docfecha BETWEEN '$fechaInicio' AND	'$fechaFin'
											AND 		c.padre like'$ctaPadre'
											AND		m.status=1
											AND 		m.idcudn LIKE '$idSucursal' ";
		$subtotalMovimientos = $this->select( $queryMovientos );

		return $subtotalMovimientos[0]['subtotal'];
	}

	public function getVentasDiariasInDateRange( $idSucursal , $fechaInicio , $fechaFin)
	{
		$queryVentaDiaria ="select 	P.fk1mcfg_almacenes, 
							PAG.formapago, 
							PAG.referencia, 
							sum(p.subtotal) as importesiniva, p.FECHA,sum(pag.IMPORTE) as importe
					from 	ref_pedidospresup P 
					inner join ref_pedidospresupformspags PAG on P.id=PAG.fk1mref_pedidospresup 
					where 	P.status in ('PEDIDO EMITIDO','PEDIDO FACTURADO') 
					and     (P.fecha>=CAST('$fechaInicio' as date) and P.fecha<=CAST('$fechaFin' as date)) ";
		if($idSucursal==10754)			
			$queryVentaDiaria.=	"and 	P.FK1MCFG_ALMACENES IN ('10754',10780) ";
		else
			$queryVentaDiaria.=	"and 	P.FK1MCFG_ALMACENES LIKE '$idSucursal%' ";

		$queryVentaDiaria.=	" and (	PAG.formapago like '%01%' or PAG.formapago like '%EFECTIVO%' or PAG.formapago like '%02%' or PAG.formapago like '%03%' or PAG.formapago like '%04%' or PAG.formapago like '%28%') 
					group by P.fk1mcfg_almacenes,PAG.formapago,PAG.referencia,p.FECHA order by P.fk1mcfg_almacenes,p.FECHA";
					 
					// echo $queryVentaDiaria."<br>";


		$exeVentaDiaria= ibase_query($this->conexionFirebird(), $queryVentaDiaria);

		$arrayVentasDiarias = array();
		while ( $rowVenta = ibase_fetch_assoc($exeVentaDiaria) ) {
			array_push($arrayVentasDiarias , $rowVenta);
		}

		return $arrayVentasDiarias;
	}

	public function getCobrosInDateRange( $idSucursal , $fechaInicio , $fechaFin )
	{
		$queryCobros =   "Select D.fk1mcfg_almacenes,
							RP.formapago,sum(RP.importe) as importe 
					From 	(cxc_historcc H 
					inner join cxc_recibosformspags RP on H.id=RP.fk1mcxc_historcc 
					inner join cxc_recibos R on R.imp_cobro_historcc=H.id 
					inner join cxc_historcc HO on R.imp_factu_historcc=HO.id 
					inner join cfg_doctos D on D.id=HO.fk11cfg_cabdoctos) 
					Where (H.concepto like '%INGXPAGPARCIAL%' OR H.concepto like '%INGXANTICIPO%') 
					and 	H.fechamovi>=CAST('".$fechaInicio."' as date) and H.fechamovi<=CAST('".$fechaFin."' as date) ";
		if($idSucursal==10754)
			$queryCobros.= "	and 	D.fk1mcfg_almacenes IN ('10754','10780') ";
		else
			$queryCobros.= "	and 	D.fk1mcfg_almacenes LIKE '".$idSucursal."%' ";
					 
		$queryCobros.= "	group by D.fk1mcfg_almacenes,RP.formapago order by D.fk1mcfg_almacenes,RP.formapago";

		$exeCobros= ibase_query($this->conexionFirebird(), $queryCobros);

		// echo $queryCobros."<br><br>";
		$arrayCobros = array();
		while ( $rowVenta = ibase_fetch_assoc($exeCobros) ) {
			array_push($arrayCobros , $rowVenta);
		}
		return $arrayCobros;		
	}
	
	public function getCostosVentas( $params)
	{
		extract( $params );
		// $queryc =   "select  sum(DET.importelinea + DET.impiva) as ventas,
		// 					sum(DET.costolinea) as costos
		// 			from    ref_pedidospresup P  
		// 			inner join REF_DETPEDIDOSPRESUP DET on P.id=DET.fkpadref_pedidospresup 
		// 			where   P.status in ('PEDIDO EMITIDO','PEDIDO FACTURADO') 
		// 			and     (P.fecha>=CAST('$fechaInicio' as date) and P.fecha<=CAST('$fechaFin' as date)) 
		// 			and     P.SERDOCTO<>'CREDITO'";
		// if($almacen==10754)
		// 	$queryc.= "	and 	P.FK1MCFG_ALMACENES IN ('10754','10780') ";
		// else
		// 	$queryc.= "	and 	P.FK1MCFG_ALMACENES LIKE '".$almacen."%' ";

		$queryc =   "select  sum(DET.importelinea + DET.impiva) as ventas,
							sum(DET.costolinea) as costos
					from    ref_pedidospresup P  
					inner join REF_DETPEDIDOSPRESUP DET on P.id=DET.fkpadref_pedidospresup 
					where   P.status in ('PEDIDO EMITIDO','PEDIDO FACTURADO') 
					and     (P.fecha>=CAST('".$fechaInicio."' as date) and P.fecha<=CAST('".$fechaFin."' as date)) 
					and     P.SERDOCTO<>'CREDITO'";
		if($almacen==10754)
			$queryc.= "	and 	P.FK1MCFG_ALMACENES IN ('10754','10780') ";
		else
			$queryc.= "	and 	P.FK1MCFG_ALMACENES LIKE '".$almacen."%' ";
	
			
		$exeCostos = ibase_query( $this->conexionFirebird(), $queryc);
		
		$arrayCostos = array();
		while ($costo = ibase_fetch_assoc($exeCostos) ){
			array_push($arrayCostos, $costo);
		}

		return $arrayCostos;
	}

		public function getMvtosDiariosByOpF($udn,$fecini,$fecfin,$cuentaF,$cuentaO){
		$query = "SELECT 	SUM(m.total) as total,m.docfecha
				  FROM 		con_movimientos m
				  INNER JOIN con_cuentas c ON m.idcon_cuentas=c.id
				  WHERE		m.docfecha between '$fecini'
				  AND		'$fecfin'
				  AND 		(c.padre='$cuentaF' OR c.padre = '$cuentaO')
				  AND		m.status=1
				  AND 		m.idcudn LIKE '$udn' 
				  GROUP BY m.docfecha ";
		$movimientos = $this->select($query);
        return $movimientos;
	}
	
	public function getGasto_conceptoMensual ( $mes )
	{
		$queryGastos = "SELECT emisor,descripcion,fecha,SUM(total) as total
									FROM dbnomina.con_movimientos 
									WHERE month( fecha) = $mes  AND year( CURDATE() ) = YEAR( fecha )
									GROUP BY emisor,descripcion,fecha 
									ORDER BY fecha ASC";
		return	$exeGastos = $this->select( $queryGastos );
	
	}

	public function getGastosCuentasSucursal( $idSucursal, $fechaInicio ="", $fechaFin = "")
	{
		$queryCuentasSucursal =" SELECT *
							FROM (
							(SELECT sum(total) as monto, cm.descripcion, cc.cuenta,cc.nombre
							FROM con_movimientos as cm
							JOIN con_cuentas as cc ON  cc.id = cm.idcon_cuentas
							where fecha between '$fechaInicio' AND '$fechaFin' AND cm.idcudn = $idSucursal and cc.padre is not null and cc.status = 1 AND CM.STATUS = 1
							group by cm.idcon_cuentas)

							UNION 

							(SELECT tipousuario,padre,cuenta,nombre FROM dbnomina.con_cuentas where padre is not null  and status = 1 ) order by cuenta ) as cuentas
							group by cuenta
							";
		$exeCuentasSucursal = $this->select( $queryCuentasSucursal);
		return $exeCuentasSucursal;
	}

	public function getTotalGastosFinancieros( $fechaInicio , $fechaFin)
	{
		$queryFinancieros = "SELECT SUM(total) AS financieros
											FROM con_movimientos 
											WHERE docfecha >= '$fechaInicio' AND docfecha <= '$fechaFin'
											AND tipoCuenta>= 6 AND tipocuenta <= 8 and status = 1";
											// echo $queryFinancieros;
		return $this->select( $queryFinancieros );
	}

	public function getTotalGastosOperativos( $fechaInicio , $fechaFin )
	{
		$queryOperativos = "SELECT SUM(total) AS operativos
											FROM con_movimientos 
											WHERE docfecha >= '$fechaInicio' AND docfecha <= '$fechaFin'
											AND tipoCuenta>= 1 AND tipocuenta <= 3 and status= 1";
											
		return $this->select( $queryOperativos );
	}
} 
?>

