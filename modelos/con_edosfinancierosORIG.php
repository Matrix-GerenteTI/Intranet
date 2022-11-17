<?php
if(!isset($_SESSION)){ 
	session_start(); 
}
require_once($_SERVER['DOCUMENT_ROOT']."/".$_SESSION['RUTA']."/modelos/MySQL.php");
class EdoFinancieros extends CMySQLi{
	
    public function getMovimientos($udn,$fecini,$fecfin){
		$query = "SELECT 	*
					  FROM 	con_movimientos
					  WHERE	docfecha>='".$fecini."'
					  AND	docfecha<='".$fecfin."'
					  AND	status=1 
					  AND 	idcudn LIKE '".$udn."'";
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
							m.subtotal as importe,
							u.descripcion as udn,
							'".$_SESSION['nivel']."' as nivel
				  FROM 		con_movimientos m
				  INNER JOIN con_cuentas c ON m.idcon_cuentas=c.id
				  INNER JOIN csucursal u ON m.idcudn=u.id
				  WHERE		m.docfecha>='".$fecini."'
				  AND		m.docfecha<='".$fecfin."'
				  AND 		c.cuenta='".$cuenta."'
				  AND		m.status=1
				  AND 		m.idcudn LIKE '".$udn."'";
        return $this->select($query);
    }
	
	public function getMovimientosCuentaTotal($udn,$fecini,$fecfin,$cuenta){
		$query = "SELECT 	SUM(m.subtotal) as subtotal
				  FROM 		con_movimientos m
				  INNER JOIN con_cuentas c ON m.idcon_cuentas=c.id
				  WHERE		m.docfecha>='".$fecini."'
				  AND		m.docfecha<='".$fecfin."'
				  AND 		c.cuenta='".$cuenta."'
				  AND		m.status=1
				  AND 		m.idcudn LIKE '".$udn."'";
		$row = $this->select($query);
        return $row[0]['subtotal'];
    }
	
	public function getTotalCXP($fecini,$fecfin){
		$host = "172.16.0.70:C:\\Prediction\\BDs\\Prediction.fdb";
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
		
		$host = "172.16.0.70:C:\\Prediction\\BDs\\Prediction.fdb";
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
					and     (P.fecha>=CAST('".$fecini."' as date) and P.fecha<=CAST('".$fecfin."' as date))
					and 	P.FK1MCFG_ALMACENES LIKE '".$fkalmacen."' 
					and (	PAG.formapago like '%01%' or PAG.formapago like '%EFECTIVO%' or PAG.formapago like '%02%' or PAG.formapago like '%03%' or PAG.formapago like '%04%' or PAG.formapago like '%28%') 
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
					and     P.SERDOCTO<>'CREDITO'
					and 	P.FK1MCFG_ALMACENES LIKE '".$fkalmacen."' ";
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
					and 	H.fechamovi>=CAST('".$fecini."' as date) and H.fechamovi<=CAST('".$fecfin."' as date) 
					and 	D.fk1mcfg_almacenes LIKE '".$fkalmacen."' 
					group by D.fk1mcfg_almacenes,RP.formapago order by D.fk1mcfg_almacenes,RP.formapago";
		$sentence2 = ibase_query($conexion,$query2); 
		$row2 = ibase_fetch_assoc($sentence2); 
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
	
} 
?>