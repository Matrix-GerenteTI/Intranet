<?php
if(!isset($_SESSION)){ 
	session_start(); 
}
require_once($_SERVER['DOCUMENT_ROOT']."/intranet/modelos/MySQL.php");
class Egresos extends CMySQLi{
	
    public function lista($opcion,$anio,$mes, $init= 0){
		if($opcion == 'NoFacturado'){
			$query = "SELECT 	m.id as id,
								IFNULL(m.docserie,'') as serie,
								IFNULL(m.docfolio,'') as folio,
								(SELECT IFNULL(f.nombreemisor,'') FROM con_facturassat f WHERE f.uuid=m.docuuid) as emisor,
								(SELECT IFNULL(f.nombrereceptor,'') FROM con_facturassat f WHERE f.uuid=m.docuuid) as receptor,
								m.docuuid as uuid,
								m.docfecha as fecha,
								m.descripcion as descripcion,
								m.total as total,
								c.nombre as cuenta,
								'".$_SESSION['nivel']."' as nivel
					  FROM 		con_movimientos m
					  INNER JOIN con_cuentas c ON m.idcon_cuentas=c.id
					  INNER JOIN pusuarios u ON m.usuario=u.username
					  WHERE		YEAR(m.docfecha)=".$anio."
					  -- AND		MONTH(m.docfecha)=".$mes."
					  -- AND		LENGTH(m.docuuid)>10
					  AND		m.tipo=2		  
					  AND		m.status=1 ";
			if($_SESSION['nivel']!='ADMINISTRADOR'){
				$query.=" AND m.usuario='".$_SESSION['usuario']."'";
			}
			$query.=" ORDER BY 	m.id DESC";
		}
		if($opcion == 'Facturado'){
			$query = "SELECT 	m.id as id,
								IFNULL(m.docserie,'') as serie,
								IFNULL(m.docfolio,'') as folio,
								IFNULL(m.emisor,'') as emisor,
								m.docuuid as uuid,
								m.docfecha as fecha,
								m.descripcion as descripcion,
								m.total as total,
								c.nombre as cuenta,
								csucursal.descripcion as sucursal,
								'".$_SESSION['nivel']."' as nivel
					  FROM 		con_movimientos m
					  INNER JOIN pusuarios u ON m.usuario=u.username
					  INNER JOIN csucursal ON csucursal.id = m.idcudn
					  INNER JOIN con_cuentas c ON m.idcon_cuentas=c.id
					  WHERE		YEAR(m.docfecha)=".$anio."
					  -- AND		MONTH(m.docfecha)=".$mes."
					  AND 		m.financiero<>1
					  AND		m.tipo=2	
					  AND		m.status=1 ";
			if($_SESSION['nivel']!='ADMINISTRADOR'){
				$query.=" AND m.usuario='".$_SESSION['usuario']."'";
			}
			$query.=" ORDER BY id DESC LIMIT  $init, 20"; 
			
		}
		if($opcion == 'Financiero'){
			$query = "SELECT 	m.id as id,
								IFNULL(m.docserie,'') as serie,
								IFNULL(m.docfolio,'') as folio,
								(SELECT IFNULL(f.nombreemisor,'') FROM con_facturassat f WHERE f.uuid=m.docuuid) as emisor,
								(SELECT IFNULL(f.nombrereceptor,'') FROM con_facturassat f WHERE f.uuid=m.docuuid) as receptor,
								m.docuuid as uuid,
								m.idcbanco as banco,
								m.cuenta as nocuenta,
								m.docfecha as fecha,
								m.descripcion as descripcion,
								m.subtotal as total,
								csucursal.descripcion as sucursal,
								'".$_SESSION['nivel']."' as nivel
					  FROM 		con_movimientos m
					  INNER JOIN con_cuentas c ON m.idcon_cuentas=c.id
					  INNER JOIN pusuarios u ON m.usuario=u.username
					  INNER JOIN csucursal ON csucursal.id = m.idcudn
					  WHERE		YEAR(m.docfecha)=".$anio."
					  -- AND		MONTH(m.docfecha)=".$mes."
					  AND		m.financiero=1
					  AND		m.tipo=2	
					  AND		m.status=1";
			if($_SESSION['nivel']!='ADMINISTRADOR'){
				$query.=" AND m.usuario='".$_SESSION['usuario']."'";
			}
			$query.=" ORDER BY 	m.id DESC"; 
		}
		if($opcion == 'Recurrente'){
			$query = "SELECT 	m.id as id,
								IFNULL(m.docserie,'') as serie,
								IFNULL(m.docfolio,'') as folio,
								(SELECT IFNULL(f.nombreemisor,'') FROM con_facturassat f WHERE f.uuid=m.docuuid) as emisor,
								(SELECT IFNULL(f.nombrereceptor,'') FROM con_facturassat f WHERE f.uuid=m.docuuid) as receptor,
								m.docuuid as uuid,
								m.docfecha as fecha,
								m.descripcion as descripcion,
								m.subtotal as total,
								'".$_SESSION['nivel']."' as nivel
					  FROM 		con_movimientos m
					  INNER JOIN con_cuentas c ON m.idcon_cuentas=c.id
					  INNER JOIN pusuarios u ON m.usuario=u.username
					  WHERE		YEAR(m.docfecha)=".$anio."
					  -- AND		MONTH(m.docfecha)=".$mes."
					  AND		m.recurrente=1
					  AND		m.tipo=2	
					  AND		m.status=1 ";
			if($_SESSION['nivel']!='ADMINISTRADOR'){
				$query.=" AND m.usuario='".$_SESSION['usuario']."'";
			}
			$query.=" ORDER BY 	m.id DESC"; 
		}
        return $this->select($query);
    }
	
	function getCuentasProveedores( $nombreProveedor){
		$queryCtaProveedor = "SELECT cc.id,cc.nombre,cm.descripcion,cm.emisor
										FROM con_movimientos AS cm
										INNER JOIN con_cuentas AS cc ON cc.id = cm.idcon_cuentas
										WHERE cm.emisor LIKE '%$nombreProveedor%' 
										GROUP BY cc.cuenta";
		return $this->select( $queryCtaProveedor );
	}

	public function getMetodosPagoEgreso()
	{
		$queryMetodosPago = "SELECT * FROM ctipoegreso WHERE status = 1";

		return $this->select( $queryMetodosPago );
	}

	public function getProveedores( )
	{
		$queryProveedor = "SELECT emisor
											FROM con_movimientos
											WHERE emisor  IS NOT NULL
											GROUP BY emisor";
		return $this->select($queryProveedor);
	}

	public function getCuentas($anio){
		//Revisamos si tiene permisos a nivel tipousuario
		$b1 = 0;
		$q1 = "SELECT 	*
				  FROM 		ptipousuariocuenta
				  WHERE		idtipousuario='".$_SESSION['nivel']."' 
				  AND		status=1";
		$arr = $this->select($q1);
		$in = "0";
		foreach($arr as $row){
			$in.= ",".$row['idcon_cuenta'];
			$b1++;
		}

		if($b1==0){				
			$q2 = "SELECT 	*
					FROM 		pusuariocuenta
					WHERE		username='".$_SESSION['usuario']."' 
					AND		status=1";
			$arr2 = $this->select($q2);
			foreach($arr2 as $row2){
				$in.= ",".$row2['idcon_cuenta'];
			}
		}
		if($_SESSION['nivel']!='1'){
			$query = "SELECT 	*
						FROM 		con_cuentas
						WHERE		ejercicio=".$anio."  
						AND		status=1
						AND 		naturaleza=2 
						AND 		id IN (".$in.")";
		}else{
			$query = "SELECT 	*
				  FROM 		con_cuentas
				  WHERE		ejercicio=".$anio."  
				  AND		status=1
				  AND 		naturaleza=2";
		}
		$query.= " ORDER BY nombre ASC";
		//if($_SESSION['nivel']!='ADMINISTRADOR')
		//	$query.= " AND 		tipousuario='".$_SESSION['nivel']."'";				  

        return $this->select($query);
    }
	
	public function guardar($post,$tabla){
		$where = "id='".$post['id']."'";
		if($post['id']>0){
			if(!$this->update($post,$tabla,$where))
				return 0;
			else
				return 1;
		}else{
			return $this->insert($post,$tabla);
		}
    }
	
	public function elimina($where,$tabla){
		if(!$this->updateTable($where,$tabla))
			return 0;
		else
			return 1;
	}

	public function eliminaEgrerso( $id, $idPadreProrrateo  )
	{
	
			$delEgreso = "UPDATE con_movimientos set status = 99  where idpadre_prorrateo= $id OR id= $id";
			return $this->updateTable( $delEgreso );
	
			//$delEgreso = "UPDATE con_movimientos set status = 99  where id= $id";

			
		return $this->updateTable( $delEgreso );
	}

	public function getHistorialPagos($fechaI, $fechaF, $pagination){
		$pagination = isset( $pagination ) ? " LIMIT ".$pagination." ,20 "  : "" ;

		$query="SELECT DISTINCT caa.beneficiario, caa.concepto, caa.monto, caa.fecha_evento FROM 
					cal_pagos_app caa
				LEFT JOIN 
					dipositivos_compartir_eventos_app 
				ON 
					caa.dispositivo = dipositivos_compartir_eventos_app.dispositivoEmisor 
				WHERE 
					caa.status = 0
				AND
					caa.fecha_evento BETWEEN '$fechaI' AND '$fechaF'
				AND
					caa.beneficiario != 'EL SERVIDOR'
				AND
					caa.beneficiario != 'PRUEBA SEMANAL'
				AND
					caa.beneficiario != 'PRUEBA SEMANAL 2'
				AND
					caa.beneficiario != 'PEPE PECAS'
				ORDER BY caa.status ASC $pagination";
		//echo $query;
		return $this->select($query);
	}

	public function updateHistorialPagos($id){
		$pagination = isset( $pagination ) ? " LIMIT ".$pagination." ,20 "  : "" ;

		$query="SELECT * FROM 
					cal_pagos_app 
				LEFT JOIN 
					dipositivos_compartir_eventos_app 
				ON 
					cal_pagos_app.dispositivo = dipositivos_compartir_eventos_app.dispositivoEmisor 
				WHERE 
					cal_pagos_app.status BETWEEN -1 AND 1 
				AND
					cal_pagos_app.fecha_evento BETWEEN '$fechaI' AND '$fechaF'
				ORDER BY cal_pagos_app.status ASC $pagination";
		//echo $query;
		return $this->select($query);
	}
	
	public function getFacturadosFiltro( $parametros ){
		$sucursalId  = $parametros['udn'];
		$emisor = $parametros['emisor'];
		$descripcion = $parametros['descripcion'];
		$tipoPago = $parametros ['tipoEgreso'];
		$paginacion = isset( $parametros['paginacion'] ) ? " LIMIT ".$parametros['paginacion']." ,20"  : "" ;
		$fechaInicio = "";
		$fechaFin = "";
		if ( strlen( $parametros['fechaInicio']  ) > 0 && strlen( $parametros['fechaFin'] ) > 0  ) {
			$fechaInicio = $this->parseFechaMysqlFormat( $parametros['fechaInicio'] );
			$fechaFin = $this->parseFechaMysqlFormat(  $parametros['fechaFin'] );
		} 
		


		$anio = date("Y");
		$condicionSucursal = "m.idcudn ='$sucursalId' ";
		if( strlen($sucursalId) == 0 ){
			$condicionSucursal = "m.idcudn LIKE '%$sucursalId%'";
		}

		$condicionFecha = "YEAR(m.docfecha)=$anio";
		if ( $fechaInicio != '' && $fechaFin != '') {
			$condicionFecha = " (m.docfecha >= '$fechaInicio' AND m.docfecha <= '$fechaFin' )";
		}elseif ($fechaInicio != '' && $fechaFin == '') {
			$condicionFecha = " m.docfecha = '$fechaInicio' ";
		}elseif ($fechaFin != '' and $fechaInicio == '' ) {
			$condicionFecha = "m.docfecha = '$fechaFin' ";
		}

		$condicionCuenta = '';
		if ( isset( $parametros['cuenta'] ) ) {
			if ( trim( $parametros['cuenta'] ) != ''  && is_numeric( $parametros['cuenta'] ) ) {
				$condicionCuenta = " AND idcon_cuentas =".$parametros['cuenta'] ;
			}
		}

		$empty = 'usuario';
		$nivel = isset( $_SESSION['nivel'] ) ? $_SESSION['nivel'] : isset($parametros['from']) ? '1' : '3';

		$query = "SELECT 	m.id as id,
					IFNULL(m.docserie,'') as serie,
					IFNULL(m.docfolio,'') as folio,
					IFNULL(m.emisor,'') as emisor,
					m.docuuid as uuid,
					m.docfecha as fecha,
					m.descripcion as descripcion,".
					(isset($parametros['reporte']) ? "csucursal.descripcion as sucursal," : "CASE WHEN COUNT(*)>1 THEN 'PRORRATEADO' ELSE csucursal.descripcion END as sucursal,")."
					'$nivel' as nivel,
					csucursal.zona as zona,
					tc.tipomovimiento as tmov,
					m.usuario,
					ctipoegreso.descripcion AS metodoPago,
					tc.operacion,
					con_cuentas.cuenta as cuentaClave,
					con_cuentas.padre as cuentaPadre,
					con_cuentas.nombre as cuentaNombre,".( !isset($parametros['reporte']) ? "
					CAST(SUM(m.total) AS DECIMAL(12,2)) as total ," : "CAST(m.total AS DECIMAL(12,2) ) as total" ).
					( !isset($parametros['reporte']) ? " CASE WHEN COUNT(*)>1 THEN 'P' ELSE 'S' END as tipoaplicacion " : ' ' ).
			"FROM 		con_movimientos m
			INNER JOIN con_cuentas ON con_cuentas.id = m.idcon_cuentas
			INNER JOIN pusuarios u ON m.usuario=u.username
			INNER JOIN csucursal ON csucursal.id = m.idcudn
			INNER JOIN ctipocuenta tc ON m.tipoCuenta=tc.id
			LEFT JOIN ctipoegreso on ctipoegreso.id = m.tipopago
			WHERE	m.status=1	
			AND $condicionFecha 
			AND 	m.emisor LIKE '%$emisor%'
			AND 	m.descripcion LIKE  '%$descripcion%'
			AND 		m.financiero<>1
			AND tipopago LIKE '$tipoPago'
			AND		m.tipo=2	
			AND    $condicionSucursal $condicionCuenta ";
		
		if ( isset( $_SESSION['nivel'] ) ) {
			if($_SESSION['nivel']!='1'){
				$query.=" AND m.usuario='".$_SESSION['usuario']."'";
			}
		}
		
		
		// $query.=" ORDER BY 	m.docfecha,m.id,csucursal.zona,csucursal.descripcion DESC LIMIT $init,20"; 

		$query.= !isset($parametros['reporte']) ? " GROUP BY 	m.fecha,m.hora" : ''; 
		$query.=" ORDER BY 	m.id DESC $paginacion"; 

		// echo $query;

		return $this->select($query);
	}


	public function parseFechaMysqlFormat($fecha)
	{
		$fechaExplode = explode( '/',$fecha );
		
		return $fechaExplode[2].'-'.$fechaExplode[1]."-".$fechaExplode[0];
	}

	public function getGastos($sucursalId, $anio, $mes, $dia)
	{
		if( strlen($sucursalId) == 0 ){
			$condicionSucursal = "idcudn LIKE '%$sucursalId%'";
		}
		$query = "SELECT 	m.id as id,
							IFNULL(m.docserie,'') as serie,
							IFNULL(m.docfolio,'') as folio,
							(SELECT IFNULL(f.nombreemisor,'') FROM con_facturassat f WHERE f.uuid=m.docuuid) as emisor,
							(SELECT IFNULL(f.nombrereceptor,'') FROM con_facturassat f WHERE f.uuid=m.docuuid) as receptor,
							m.docuuid as uuid,
							m.idcbanco as banco,
							m.cuenta as nocuenta,
							m.docfecha as fecha,
							m.descripcion as descripcion,
							m.subtotal as total,
							'".$_SESSION['nivel']."' as nivel
					FROM 		con_movimientos m
					INNER JOIN con_cuentas c ON m.idcon_cuentas=c.id
					INNER JOIN pusuarios u ON m.usuario=u.username
					WHERE		YEAR(m.fecha)=".$anio."
					AND		MONTH(m.fecha)=".$mes."
					AND		DAY(m.fecha)=".$dia."
					AND		m.financiero=0
					AND 	m.tipoCuenta=3
					AND		m.tipo=2  
					AND		m.status=1
					AND		m.descripcion like '%$descripcion%' 
					ORDER BY 	m.id DESC"; 
		
		return $this->select($query);
	}

	public function getMovtosFinancieros($sucursalId, $descripcion, $mesAnio)
	{
		$splitFecha = explode('/', $mesAnio);
		$condicionSucursal = "idcudn ='$sucursalId' ";
		if( strlen($sucursalId) == 0 ){
			$condicionSucursal = "idcudn LIKE '%$sucursalId%'";
		}
		$queryFiltroFinanciero = "SELECT 	m.id as id,
								IFNULL(m.docserie,'') as serie,
								IFNULL(m.docfolio,'') as folio,
								(SELECT IFNULL(f.nombreemisor,'') FROM con_facturassat f WHERE f.uuid=m.docuuid) as emisor,
								(SELECT IFNULL(f.nombrereceptor,'') FROM con_facturassat f WHERE f.uuid=m.docuuid) as receptor,
								m.docuuid as uuid,
								m.idcbanco as banco,
								m.cuenta as nocuenta,
								m.docfecha as fecha,
								m.descripcion as descripcion,
								m.subtotal as total,
								'".$_SESSION['nivel']."' as nivel
					  FROM 		con_movimientos m
					  INNER JOIN con_cuentas c ON m.idcon_cuentas=c.id
					  INNER JOIN pusuarios u ON m.usuario=u.username
					  WHERE		right(YEAR(m.fecha),2)=".$splitFecha[1]."
					 AND		MONTH(m.docfecha)=".$splitFecha[0]."
					  AND		m.financiero=1
					  AND		m.tipo=2	
					  AND		m.status=1
					  AND		m.descripcion like '%$descripcion%' ";
			if($_SESSION['nivel']!='ADMINISTRADOR'){
				$queryFiltroFinanciero.=" AND m.usuario='".$_SESSION['usuario']."'";
			}
			$queryFiltroFinanciero.=" ORDER BY 	m.id DESC"; 

		return $this->select($queryFiltroFinanciero);
	}
	public function registraMovimiento( $valores , $idProrrateoPadre = 'NULL')
	{
		extract($valores);
		$queryInsertaMovimiento  ="INSERT INTO con_movimientos(emisor,rfc,descripcion,fecha,hora,docfecha,dochora,docuuid,subtotal,iva,total,tipo_movimiento,idcon_cuentas,tipoCuenta,idcudn,usuario,tipopago,observaciones,idpadre_prorrateo) VALUES(
								'$emisor','$rfc','$descripcion','$fecha','$hora','$fecha','$hora','$uuid','$subtotal','$iva','$total',$tipoMovimiento,'$cuentaId',$tipoCuenta,'$sucursalId','".$_SESSION['usuario']."',$tipoPago, '$observaciones',$idProrrateoPadre	)";
			// echo $queryInsertaMovimiento;
		$this->insertDefined($queryInsertaMovimiento);
		
		if($this->db->affected_rows>0)
			return $this->db->insert_id;
		else
			return 0;
	}

	public function actualizaMovimientoOperacion( $parametros)
	{
		extract( $parametros );
		
		if ( strpos($fecha, "/") !== false) {
			$fechaExplode = explode("/",$fecha);
			$fecha = $fechaExplode[2]."-".$fechaExplode[1]."-".$fechaExplode[0];
		}
		
		$queryActualizaMovtoOperacion = "UPDATE con_movimientos set descripcion='$descripcion'
								 ,rfc='$rfc',docserie='$serie',docfolio='$folio',docuuid='$uuid', idcbanco='$banco',
								  cuenta='$cuenta', idcon_cuentas='$cuentaContable', subtotal=$subtotal, iva=$iva,
								  total= $total, idcudn= $sucursal,emisor='$proveedor', docfecha ='$fecha', tipo_movimiento = $tipoMov,
								  tipoCuenta = $tipoOperacion,tipopago = '$tipoEgreso', observaciones = '$observaciones' WHERE  id = $movimiento ;" ;
		
		$this->insertDefined( $queryActualizaMovtoOperacion);
		return $this->db->affected_rows;
	}

	public function checkProvedorFromProveedores($nombre)
	{
		$queryProveedor = "SELECT * FROM proveedores WHERE descripcion like '%$nombre%' ";
		
		return $this->select($queryProveedor);
	}
	
	public function insertToProveedores($datos)
	{
		extract($datos);
		$queryInsertaProv = "INSERT INTO proveedores VALUES('','$proveedor','$rfc')";
		
		$this->insertDefined($queryInsertaProv);
		$proveedor = $this->checkProvedorFromProveedores($proveedor);
		return  $proveedor;
	}

	public function getDescripcionFromCfg_descripcion($descripcion)
	{
		$queryDescripcion = "SELECT * FROM cfg_descripcion_movimientos WHERE descripcion='$descripcion' ";
		
		return $this->select($queryDescripcion);
	}

	public function insertDescripcionCfg_descripcion($datos)
	{
		extract($datos);
		$queryInsertDesc = "INSERT INTO cfg_descripcion_movimientos VALUES('','$descripcion') ";
		$this->insertDefined($queryInsertDesc);
		$descripcion = $this->getDescripcionFromCfg_descripcion($descripcion);
		return $descripcion;
	}

	function insertLogMovtosAutomaticos($datos){
		extract($datos);
		$queryInsertLog = "INSERT INTO cfg_movtos_automatico VALUES('$fecha','$sucursalId','$cuentaId','$descripcionId','$proveedorId','$subtotal','$iva','$total','A','$expresion','$fecha','$caducidad',now(),$tipoMovimiento,$tipoCuenta)";
		
		$this->insertDefined($queryInsertLog);
		return $this->db->affected_rows;
	}

	public function selectLogAutomatico($fecha="",$group = false)
	{
		$queryAutomaticos = "SELECT mvtoAu.*,d.descripcion as proveedor,p.descripcion
												FROM cfg_movtos_automatico AS mvtoAu 
												INNER JOIN cfg_descripcion_movimientos as d ON d.id = mvtoAu.idDescripcion
												INNER JOIN proveedores as p ON p.id = mvtoAu.idProveedor
												WHERE mvtoAu.log LIKE '%$fecha%' AND mvtoAu.status = 'A'
												";
		if ( $group) {
			$queryAutomaticos .= "GROUP BY mvtoAu.log";
		}												

		return $this->select($queryAutomaticos);

	}

	public function actualizaLogAutomatico($datos)
	{
		extract($datos);
		$queryUpdate = "UPDATE cfg_movtos_automatico SET cfg_movtos_automatico.log='$fechaLog' , status='$status'
										WHERE fecha_mvto ='$fecha_mvto' AND idSucursal='$idSucursal' AND idcuenta='$idcuenta' AND idProveedor='$idProveedor'
										AND subtotal ='$subtotal' AND total='$total'";
										
		$this->insertDefined($queryUpdate);
		return $this->db->affected_rows;
	}

	public function getMovimientoCargoAbono($mes, $tipoMvto)
	{
		$anio = date('Y');
		$queryCargoAbono = " SELECT cm.fecha,cm.emisor,cm.descripcion,sum(cm.total) as total,cm.tipo_movimiento,tc.tipomovimiento as tmov
													FROM con_movimientos cm 
													WHERE month(cm.fecha)='$mes' and cm.tipo_movimiento=$tipoMvto and year(cm.fecha) = $anio
													INNER JOIN ctipocuenta tc ON cm.tipoCuenta=tc.id
													group by  cm.idcon_cuentas,cm.descripcion,cm.total,cm.fecha
													order by cm.fecha ";
		return $this->select( $queryCargoAbono );
	}
	
	public function getMovimientoCargoAbonoGroupBy($mes, $tipoMvto)
	{
		$anio = date('Y');
		$queryCargoAbono = " SELECT fecha,sum(total) as total
									FROM con_movimientos 
									WHERE month(fecha)='$mes' and tipo_movimiento=$tipoMvto and year(fecha) = $anio
									group by  fecha
									order by fecha ASC";
		return $this->select( $queryCargoAbono );
	}

	public function getGastosCajaChicaMensual($mes, $anio)
	{
		$queryGastos = "SELECT con_movimientos.descripcion,con_cuentas.cuenta,con_cuentas.nombre,sum(con_movimientos.total) as total, csucursal.descripcion sucursal
							from con_movimientos
							inner join con_cuentas on con_cuentas.id = con_movimientos.idcon_cuentas
							inner join csucursal on csucursal.id = con_movimientos.idcudn
							where month(con_movimientos.docfecha) =$mes and year(con_movimientos.docfecha) =$anio
							group by con_cuentas.cuenta, con_movimientos.idcudn
							order by csucursal.id";
		return $this->select( $queryGastos);
	}

	public function getMovimientoCuentaTipeada( $movimiento)
	{
		$queryMovimientoTipeado = "SELECT *,con_movimientos.tipoCuenta as tCuenta,con_movimientos.id as idMovimiento,con_movimientos.descripcion as descripcion,csucursal.descripcion as sucursal,idpadre_prorrateo
						FROM con_movimientos
						INNER JOIN csucursal on con_movimientos.idcudn=csucursal.id
						LEFT JOIN ctipocuenta on ctipocuenta.id = con_movimientos.tipoCuenta
						WHERE con_movimientos.id = $movimiento";
						
		return $this->select( $queryMovimientoTipeado);
	}

	public function getMovimientoProrratado( $parametros )
	{
		extract( $parametros );
		$queryProrrateado ="SELECT  sum(total) as total,count(total) as cantidad FROM con_movimientos WHERE (emisor = '$emisor' or emisor is NULL ) and descripcion = '$descripcion' and total = $total  
											and idcon_cuentas = $idcon_cuentas and fecha = '$fecha' and hora = '$hora' 
											group by  total";
											
		return $this->select( $queryProrrateado );
	}

	public function getProgramacionPagos( $dia, $mes, $anio ){
		$queryMovimientoTipeado = "SELECT *
							FROM cal_pagos_app
							WHERE fecha_evento = '".$anio."-".$mes."-".$dia."'";
		
		return $this->select( $queryMovimientoTipeado);
	}

	public function getNombreSucursal( $id ){
		$queryMovimientoTipeado = "SELECT *
							FROM csucursal
							WHERE id = '".$id."'";
		
		return $this->select( $queryMovimientoTipeado);
	}
	

} 
?>