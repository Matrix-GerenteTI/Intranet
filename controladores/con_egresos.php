<?php
	if(!isset($_SESSION)){ 
		session_start(); 
	}
	require_once($_SERVER['DOCUMENT_ROOT']."/intranet/controladores/sesiones.php");
	require_once($_SERVER['DOCUMENT_ROOT']."/intranet/controladores/Reportes/Egresos/RecibosDeDinero.php");
	require_once($_SERVER['DOCUMENT_ROOT']."/intranet/modelos/con_egresos.php");
	require_once($_SERVER['DOCUMENT_ROOT']."/intranet/modelos/con_edosfinancieros.php");
	require_once $_SERVER['DOCUMENT_ROOT']."/intranet/lib/PHPExcel/IOFactory.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/intranet/lib/PHPExcel.php";

	require_once $_SERVER['DOCUMENT_ROOT']."/Apps/sitex/models/pushnotificaciones.php";

	require_once $_SERVER['DOCUMENT_ROOT']."/intranet/vendor/autoload.php";

	use Kreait\Firebase\Factory;
	use Kreait\Firebase\ServiceAccount;
	use  Kreait\Firebase\Messaging\Notification;
	use  Kreait\Firebase\Messaging\CloudMessage;
	use Kreait\Firebase\Messaging\AndroidConfig;

	$sesion = new Sesion();
	
	$egresos = new Egresos();
	
	$opcion =isset($_GET['opc']) ? $_GET['opc'] : $_POST['opc'];
	switch($opcion){		
		case 'lista':{
			$opcion = $_POST['opcion'];
			$skipPagination = $_POST['pagina'];
			$anio = (int)date("Y");
			$mes = (int)date("m");
			$arreglo = $egresos->lista($opcion, $anio, $mes,$skipPagination);
			
			
			echo json_encode($arreglo);
			break;
		}
		case 'cmbCuenta':{
			$anio = (int)date("Y");
			$arreglo = $egresos->getCuentas($anio);
			
			echo json_encode($arreglo);
			break;
		}
		
		case 'guardaOp':{
			$arr = array('id','idcudn','emisor','descripcion','rfc','docserie','docfolio','docuuid','idcbanco','cuenta','idcon_cuentas','subtotal','iva','total');
			$array = array();
			foreach($_POST as $idx => $val){
				if(in_array($idx,$arr)){
					$array[$idx] = $val;
				}
			}
			//$arrayT['nip'] = $_SESSION['nip'];
			//print_r($arrayT);
			//die();
			$array['fecha'] = '';
			$array['hora'] = '';
			$array['usuario'] = $_SESSION['usuario'];
			$array['docfecha'] = $sesion->formateaFecha($_POST['docfecha'],'d2g',1);
			$res = $egresos->guardar($array,'con_movimientos');
			
			echo $res;
			break;
		}
		
		case 'updateOp':{
			$array = array();
			//$arrayT['nip'] = $_SESSION['nip'];
			//print_r($arrayT);
			//die();
			$array['id'] = $_POST['id'];
			$array['descripcion'] = $_POST['concepto'];
			$array['subtotal'] = $_POST['importe'];
			$array['total'] = $_POST['importe'];
			$res = $egresos->guardar($array,'con_movimientos');
			
			echo $res;
			break;
		}

		case 'calulaNomina':{
 $objReader = new PHPExcel_Reader_Excel2007();
		  $objPHPExcel = $objReader->load($_FILES['formatoNomina']['tmp_name']);
		  $hoja = $objPHPExcel->setActiveSheetIndex(0);			

			$highestRow = $hoja->getHighestRow();
			$adscripcion = '';
			$sucursalMonto = array();
			$fecha = $hoja->getCell("A2")->getValue();
			$fechaExplode = explode('/',$fecha);
			$anio = substr($fechaExplode[2],0,4);
			$dia= substr($fechaExplode[0],-2,2);

			
			$mes = array(1=> 'Enero',2 =>'Febrero',3=> 'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre');
			
			for($i= 5; $i<= $highestRow; $i++){
				$sucursal = trim($hoja->getCell("A$i"));
				if( $sucursal != null){
					if($sucursal != $adscripcion){
						if ( isset($sucursalMonto[$sucursal.""]) ) {
							$sucursalMonto[$sucursal.""] += $hoja->getCell("N$i")->getCalculatedValue();
						}else{
							$sucursalMonto[$sucursal.""] = $hoja->getCell("N$i")->getCalculatedValue();
						}
						$adscripcion = $sucursal;
					}
					else{
						$sucursalMonto[$sucursal.""] += $hoja->getCell("N$i")->getCalculatedValue();
					}
				}
			}

			$montos = 0;
			foreach($sucursalMonto as $sucursal => $monto){
				$edosFinancieros = new EdoFinancieros;
				$egresos = new Egresos;
				$cuentaInsertado = 0;
				$quincena = ($dia/1) <= 15 ? "PRIMERA QUINCENA" : "SEGUNDA QUINCENA";
				if( $sucursal == 'CORPORATIVO') {
					//Prorratear Monto entre las doce sucursales
					$montos += $monto;
					// $montoProrrateado = ($monto/12);
					// $sucursales = $edosFinancieros->getSucursal();
					// foreach($sucursales as $sucursal ){
					// 	if( $sucursal['id'] <= 10 || $sucursal['id'] == 13 || $sucursal['id'] == 15 ){
					// 		$valuesToInsert = array(
					// 			'emisor' => 'SIN PROVEEDOR',
					// 			'rfc' => '',
					// 			'descripcion' => 'PAGO CORRESPONDIENTE A LA '. $quincena.' DEL MES DE '.$mes[($fechaExplode[1]/1)].' EN '.$sucursal['descripcion'],
					// 			'fecha' => date('Y-m-d'),
					// 			'hora' => date("H:i:s"),
					// 			'uuid' => '',
					// 			'subtotal' => 0,
					// 			'iva' => 0,
					// 			'total' => $montoProrrateado,
					// 			'tipoMovimiento' => 2,
					// 			'cuentaId' => 28,
					// 			'sucursalId' => $sucursal['id']
					// 		);
					// 		$cuentaInsertado += $egresos->registraMovimiento( $valuesToInsert );
					// 	}
					// }
				}elseif($sucursal == 'ALMACEN MATRIZ' || $sucursal == 'ALMACEN LAURELES'){
					// foreach($sucursales as $sucursal ){
					// 	if( $sucursal['id'] <= 10 || $sucursal['id'] == 13 || $sucursal['id'] == 15 ){
							$montos += $monto;
							// $valuesToInsert = array(
							// 	'emisor' => 'SIN PROVEEDOR',
							// 	'rfc' => '',
							// 	'descripcion' => 'PAGO CORRESPONDIENTE A LA '. $quincena.' DEL MES DE '.$mes[($fechaExplode[1]/1)].' EN '.$sucursal['descripcion'],
							// 	'fecha' => date('Y-m-d'),
							// 	'hora' => date("H:i:s"),
							// 	'uuid' => '',
							// 	'subtotal' => 0,
							// 	'iva' => 0,
							// 	'total' => $montoProrrateado,
							// 	'tipoMovimiento' => 2,
							// 	'cuentaId' => 28,
							// 	'sucursalId' => $sucursal['id']
							// );
							// $cuentaInsertado += $egresos->registraMovimiento( $valuesToInsert );
					// 	}
					// }
				}else{
					$sucursalId ="";
					switch($sucursal){
						case 'LEON VILLAFLORES': $sucursalId = 2; break;
						case 'VILLAFLORES': $sucursalId = 2; break;
						case 'PALMERAS': $sucursalId = 4; break;
						case '9a LLANTERA' : $sucursalId = 6; break;
						case 'LLANTERA 9A' : $sucursalId = 6; break;
						case 'LLANTERA 9A.' : $sucursalId = 6; break;
						case '5a LLANTERA' : $sucursalId = 7; break;
						case 'LLANTERA 5A' : $sucursalId = 7; break;
						case 'LEON 5a': $sucursalId = 3; break;
						case 'LEON 5TA': $sucursalId = 3; break;
						case 'LEON 5TA.': $sucursalId = 3; break;
						case 'LIBRAMIENTO': $sucursalId = 5; break;
						case 'LIB. SUR': $sucursalId = 5; break;
						// case 'ALMACEN LAURELES': $sucursalId = 15; break;
						case 'MATRIZ': $sucursalId= 1; break;
						case 'LEON 9a': $sucursalId = 10; break;
						case 'LEON 9A': $sucursalId = 10; break;
						case 'LEON 9A.': $sucursalId = 10; break;
						case 'LAURELES': $sucursalId= 15;break;
					}
					
				
						$valuesToInsert = array(
							'emisor' => 'SIN PROVEEDOR',
							'rfc' => '',
							'descripcion' => 'PAGO CORRESPONDIENTE A LA '. $quincena.' DEL MES DE '.$mes[($fechaExplode[1]/1)].' EN '.$sucursal,
							'fecha' => $anio.'-'.$fechaExplode[1].'-'.$dia,
							'hora' => date("H:i:s"),
							'uuid' => '',
							'subtotal' => 0,
							'iva' => 0,
							'total' => $monto,
							'tipoMovimiento' => 2,
							'cuentaId' => 28,
							'sucursalId' => $sucursalId,
							'tipoCuenta' => 3
						);	
						$cuentaInsertado += $egresos->registraMovimiento( $valuesToInsert ); 
						
				}
			}
				$sucursales = $edosFinancieros->getSucursal();
				foreach($sucursales as $sucursal ){
						if( $sucursal['id'] <= 10 || $sucursal['id'] == 13 || $sucursal['id'] == 15 ){
							$montoProrrateado = $montos / 12 ;
							$valuesToInsert = array(
								'emisor' => 'SIN PROVEEDOR',
								'rfc' => '',
								'descripcion' => 'PAGO CORRESPONDIENTE A LA '. $quincena.' DEL MES DE '.$mes[($fechaExplode[1]/1)].' EN '.$sucursal['descripcion'],
								'fecha' => $anio.'-'.$fechaExplode[1].'-'.$dia,
								'hora' => date("H:i:s"),
								'uuid' => '',
								'subtotal' => 0,
								'iva' => 0,
								'total' => $montoProrrateado,
								'tipoMovimiento' => 2,
								'cuentaId' => 28,
								'sucursalId' => $sucursal['id'],
								'tipoCuenta' => 3
							);
							$cuentaInsertado += $egresos->registraMovimiento( $valuesToInsert );
						}
					}
			echo $cuentaInsertado;
			break;
		}
		case 'eliminaMov':{
			//Verificando si  es un movimiento con prorrateo
			$detalleMovimiento = $egresos->getMovimientoCuentaTipeada($_POST['id'])[0]; 
			//Eliminando todos los movimientos que son hijos del prorrateo
			// $where = "idpadre_prorrateo=".$detalleMovimiento['idpadre_prorrateo']." OR id=".$detalleMovimiento['idpadre_prorrateo'];
			// $tabla = "con_movimientos";	
			$res = $egresos->eliminaEgrerso( $_POST['id'] , $detalleMovimiento['idpadre_prorrateo'] );

			//Eliminando el registro 'PRINCIPAL'
			// $where = "id=".$_POST['id'];
			// $tabla = "con_movimientos";			
			// $res = $egresos->eliminaEgrerso($_POST['id'] , 0 );
			echo $res;
			break;
		}
		
		case 'guardaFi':{
			$arr = array('id','idcudn','descripcion','idcbanco','cuenta','idcon_cuentas','subtotal','iva','total','tipoCuenta');
			$array = array();
			foreach($_POST as $idx => $val){
				if(in_array($idx,$arr)){
					$array[$idx] = $val;
				}
			}
			//$arrayT['nip'] = $_SESSION['nip'];
			//print_r($arrayT);
			//die();
			$array['fecha'] = '';
			$array['hora'] = '';
			$array['financiero'] = 1;
			$array['usuario'] = $_SESSION['usuario'];
			$array['tipoCuenta'] = 7;
			$array['docfecha'] = $sesion->formateaFecha($_POST['docfecha'],'d2g',1);
			$res = $egresos->guardar($array,'con_movimientos');
			
			echo $res;
			break;
		}

		case 'cuentaProveedor':{
			$proveedor = $_GET['proveedor'];
			$cuentasProveedor = $egresos->getCuentasProveedores( $proveedor );
			echo json_encode($cuentasProveedor);
			break;
		}

		case 'buscaProveedor':{
			$proveedores = array();
			$proveedor = $egresos->getProveedores( );
			foreach ($proveedor as $proveedorNombre) {
				array_push( $proveedores, $proveedorNombre['emisor']);
			}
			echo json_encode($proveedores);
			break;
		}

		case 'filtroOperacion':{
			$filtrados = $egresos->getFacturadosFiltro($_POST);
			echo json_encode($filtrados);
			break;
		}

		case 'filtroFinanciero':{
			$expresionFecha = "^[0-9][0-9]\/[0-9][0-9]";
			$fecha= $_POST['fecha'];
			if ( preg_match("/$expresionFecha/", $fecha) ) {
				$filtrados = $egresos->getMovtosFinancieros($_POST['udn'], $_POST['descripcion'], $fecha);
				echo json_encode( $filtrados);
			}
			else{
				echo json_encode(array('error' =>"Formato Incorrecto de la fecha"));
			}

		}

		case 'getGastos':{
			$filtrados = $egresos->getGastos($_GET['udn'], $_POST['anio'], $_POST['mes'], $_POST['dia']);
			echo json_encode( $filtrados);
		}

		case 'geMovimientoEditar':

				echo  json_encode ($egresos->getMovimientoCuentaTipeada($_GET['movimiento']) );
			break;
		case 'editaMovtoGO':
				echo json_encode( $egresos->actualizaMovimientoOperacion($_POST));
			break;
		case 'mvtosPendientes':{
			$fechas =  $egresos->selectLogAutomatico('',true);
			foreach ($fechas as $movimiento) {
				$fechaPrimaria = $movimiento['fecha_mvto'];
				$fechaLog = $movimiento['log'];
				$caducidad = $movimiento['caducidad'];
				$frecuencia = $movimiento['expresionPeriodo'];
				$fechaRegistro = "";
				
				$tipoFrecuencia =  validateExpression( $frecuencia );
				 echo "$tipoFrecuencia <br>";
				$fechaFrecuencia ="";
				$fechaPlusLog = "";


				$logSplit = explode('-', $fechaLog )  ;
				$caducidadSplit = explode('-', $caducidad );
				
				$mesActual = date("m");
				$diaActual = date("d");
				$anioActual = date("Y");
				//guardando el dia de pago 
				$diaPago = $caducidadSplit[2];

				switch ($tipoFrecuencia) {
					case 'm':
							if ( ($logSplit[1]/1) < ($mesActual/1 ) ) {
								if ( ($diaActual/1)  >  ($logSplit[2]/1) ) {
									$fechaRegistro = "$anioActual-$mesActual-$caducidadSplit[2]";
									
									registraAutomatico($fechaRegistro, $fechaLog);
								}
							}
						break;
					case 'a':
						if( ($logSplit[0]) < ($anioActual ) ){
							$mesDia = explode( '',$frecuencia );
							$mesFrecuencia = $mesDia[1] /1;
							$diaFrecuencia = $mesDia[0]/1;
							if ( $mesFrecuencia < ($mesActual/1) ) {
								$fechaRegistro = "$anioActual-$mesFrecuencia-$diaFrecuencia";
								registraAutomatico($fechaRegistro, $fechaLog);
							}
						}
						break;
					default:
						# code...
						break;
				}
				//verificando que  el log no sea igual a la caducidad
				if ( trim($fechaRegistro) != '' && strtotime( $fechaLog) >= strtotime( $caducidad )  && $caducidad != "0000-00-00" ) {
					//Se dará de baja el movimiento automatico y pero se registra el ultimo 
					registraAutomatico($fechaRegistro, $fechaLog,true);
					echo ( $fechaRegistro ." --- ---". $fechaLog);
				} 
			
				
			}
			break;
		}

		case 'getTipoEgresos':
			$egresos = new Egresos;
			echo json_encode( $egresos->getMetodosPagoEgreso() );
			break;
		case 'registraGO':{
			if ( !isset($_SESSION['nivel']) ) {
				echo  json_encode( ['cant' => -1 ]);
				exit();
			}
			$numSucursales = 0;
			$ivaProrrateado = 0;
			$subtotalProrrateado = 0;
			$totalProrrateado = 0;
			$logRegistrados = array();
			$contMovtoRegistrado = 0;
			$registrado = 0;

			$automatico = $_POST['auto'];
			$sucursales = isset($_POST['sucursales']) ? $_POST['sucursales'] : array();
			$fecha = $sesion->formateaFecha($_POST['fecha'],'d2g',1);
			$iva = verificaMontos( $_POST['iva'] );
			$subtotal = verificaMontos( $_POST['subtotal'] );
			$total = verificaMontos($_POST['total']);
			$generaRecibo = $_POST['recibo'];

			$valuesToInsert =array(
				'uuid' => $_POST['uuid'],
				'descripcion' =>$_POST['descripcion'],
				'rfc' => $_POST['rfc'],
				'hora' => date("H:i:s"),
				'cuentaId' => $_POST['contable'],
				'emisor' => $_POST['proveedor'],
				'fecha' => $fecha,
				'tipoMovimiento' => $_POST['tipoMovimiento'],
				'tipoCuenta' => $_POST['tipoCuenta'],
				'tipoPago' => $_POST['tipoPago'],
				'observaciones' => $_POST['observaciones']
			);


			if ( sizeof($sucursales) ) { //Prorratear valores
				
				$numSucursales = (sizeof($sucursales)+1);
				$ivaProrrateado = $iva / $numSucursales;
				$subtotalProrrateado =$subtotal / $numSucursales;
				$totalProrrateado = $total / $numSucursales;
				$valuesToInsert['iva'] =  trim($ivaProrrateado);
				$valuesToInsert['subtotal'] = trim($subtotalProrrateado) ;
				$valuesToInsert['total'] =  trim($totalProrrateado);
			}else{
				$valuesToInsert['iva'] = trim($iva);
				$valuesToInsert['subtotal'] = trim($subtotal);
				$valuesToInsert['total'] = trim($total);
			}
			
			if ($automatico) {
				$periodicidad = $_POST['periodo'];
				$caducidad = $_POST['caducidad'];
				$caducidad = dateFormatter( $caducidad);

				validateExpression( $periodicidad );
				
				$idProveedor =0;
				$idDescripcion =0;
				//inserta en la tabla proveedores y cfg_movtos y obtiene la id del proveedor y descripcion del movimiento
				$provRegistrado = $egresos->checkProvedorFromProveedores(trim($_POST['proveedor']));
				if ( sizeof($provRegistrado) ) {
					$idProveedor = $provRegistrado[0]['id'];
				}else{
					$provRegistrado = $egresos->insertToProveedores( array('proveedor' => $_POST['proveedor'],'rfc' => $_POST['rfc']) );
					$idProveedor = $provRegistrado[0]['id'];
				}
				//Comprobando que la descripcion esté registrada
				$desRegistrado = $egresos->getDescripcionFromCfg_descripcion($_POST['descripcion']);
				if ( sizeof($desRegistrado) ) {
					$idDescripcion = $desRegistrado[0]['id'];
				} else {
					$desRegistrado = $egresos->insertDescripcionCfg_descripcion(array('descripcion'=>$_POST['descripcion']));
					$idDescripcion = $desRegistrado[0]['id'];
				}
				//Haciendo el registro a la tabla cfg_movtos_automaticos
				$valuesToInsert['proveedorId'] = $idProveedor;
				$valuesToInsert['descripcionId'] = $idDescripcion;
				$valuesToInsert['expresion'] = $periodicidad;
				$valuesToInsert['caducidad'] = $caducidad;
				if( sizeof($sucursales) ){
					foreach ($sucursales as $sucursal) {
						$valuesToInsert['sucursalId'] = $sucursal;
						$registrado = $egresos->insertLogMovtosAutomaticos($valuesToInsert);
						if ( $registrado > 0) {
							array_push($logRegistrados,$valuesToInsert);
						}
					}
				}
				//Agregando la sucursal unica del select fudn
				$valuesToInsert['sucursalId'] = $_POST['sucursal'];
				$registrado = $egresos->insertLogMovtosAutomaticos($valuesToInsert);
				if ( $registrado > 0) {
					array_push($logRegistrados,$valuesToInsert);
				}
			} 

			if (  $automatico) { //se registro algun movimento para automatizar?
				if (sizeof($logRegistrados)) {
					foreach ($logRegistrados as $movimiento) {
						$registrado = $egresos->registraMovimiento($movimiento);
						if ( $registrado > 0) {
							$contMovtoRegistrado++;
						}
					}
				}
				// echo $contMovtoRegistrado;
				// exit();
			} else {
				//Verficando que haya mas de una sucursal en el array de sucursales
				if ( sizeof($sucursales) ) {
					//Ingresando el movimiento para la sucursal seleccionada via el elemento #fudn			
					$valuesToInsert['sucursalId'] = $_POST['sucursal'];
					$registrado = $egresos->registraMovimiento($valuesToInsert);
					$registradoPadre =  $registrado;

					if ( $registrado > 0) {
							$contMovtoRegistrado++;
					}

					foreach ($sucursales as $idSucursal) {
						$valuesToInsert['sucursalId']  = $idSucursal;
						$registrado = $egresos->registraMovimiento($valuesToInsert , $registradoPadre );
						if ( $registrado > 0) {
							$contMovtoRegistrado++;
						}
					}
				} else{
					$valuesToInsert['sucursalId'] = $_POST['sucursal'];
					$registrado = $egresos->registraMovimiento($valuesToInsert);
					$registradoPadre =  $registrado;

					if ( $registrado > 0) {
							$contMovtoRegistrado++;
					}
				}

			}

			//Enviando la notificacion
			if ( $registrado ) {
				$detalleGasto = $egresos->getMovimientoCuentaTipeada( $registrado );
				if( $detalleGasto[0]['tipo_movimiento'] == 1){
					// if( $detalleGasto[0]['tipo_pago'] == 1){
						try {
							$serviceAccount = ServiceAccount::fromJsonFile(  $_SERVER['DOCUMENT_ROOT']."/intranet/sitex-defenitive-app-714e858f0431.json");
							$firebase = ( new Factory )
									->withServiceAccount( $serviceAccount )
									->create();
							
							$mensajes = $firebase->getMessaging();        
							
							
								$contendio = [
									'descripcion' => $detalleGasto[0]['descripcion'],
									'monto' => $total,
									'id' => $detalleGasto[0]['idMovimiento'],
									'solicitante' => "Administrador",
									'origen' => 'Gastos'
								];
								//guardadndo la notificacion
								$modeloMensaje = new Notificaciones;
								$modeloMensaje->registraMensajeNotificacion($detalleGasto[0]['descripcion'] , "Monto: ".$total.' '.( sizeof($sucursales) > 0 ? (" Monto total antes de ser prorrateado") : '' ), 'Gastos','', $_POST['sucursal']);
								
									$config = AndroidConfig::fromArray([
										'ttl' => '3600s',
										'priority' => 'normal',
										'data' => $contendio,
										'notification' => [
											'title' => $detalleGasto[0]['descripcion'],
											'body' => "Monto: ".$total,
											'color' => '#f45342',
											"sound" => "default",
											
										],
									]);
							
								//Obteniendo el token de registro del usuario para utilizar firebase
								// $usuario = $mensajesObj->getUsuarioActivoTelefono( $usuario);
							
								// if ( sizeof( $usuario ) ) {
							
									$tokenDispositivo = "cfYBPdR-5Dw:APA91bFrNgHIvRsFwu3Oup5Tpq5NF5cGeyTwz4ricQDgzvAjNlmR5aAv4StuSqlPpbxzrmEqimay_5vXXNgNNAM3M7-tBf_w8SQTEFmQ_Fyyio0OXdqki5CkOywm8OxDUZic4O2-pSOR";
											$mensaje  = CloudMessage::withTarget('token', $tokenDispositivo)
											->withAndroidConfig( $config );
					
									$mensajes->send( $mensaje );
							
									// echo "enviado<br>";
								// }
							
						} catch ( Kreait\Firebase\Exception\Messaging\NotFound $error ) {
							echo "ERROR".$error;
							//throw $th;
						}
					// }
				}
			}
			
			if ( $generaRecibo  && $registrado) {
				$recibo = new ValesdeEfectivo;

				$urlRecibo = $recibo->generaRecibo( $registrado);
				echo json_encode( ['cant' =>$contMovtoRegistrado, 'recibo' =>  $urlRecibo ]);
				// GENERANDO LA NOTIFICACIÖN PARA LOS TELEFONOS MOVILES

				exit();
			}
			echo json_encode( ['cant' =>$contMovtoRegistrado, 'recibo' =>  '' ]);
		}
	}

	 function registraAutomatico( $fecha, $fechaLog, $baja =  false)
	{
			$egresos = new Egresos;
			$movimentos =  $egresos->selectLogAutomatico($fechaLog);
			foreach ($movimentos as $movimento) {
				$valuesToInsert =array(
							'uuid' => "",
							'descripcion' =>$movimento['descripcion'],
							'rfc' => '',
							'hora' => date("H:i:s"),
							'cuentaId' => $movimento['idcuenta'],
							'emisor' => $movimento['proveedor'],
							'fecha' => $fecha,
							'tipoCuenta' => $movimento['tipoCuenta']
						);
				$valuesToInsert['iva'] = $movimento['iva'];
				$valuesToInsert['subtotal'] = $movimento['subtotal'];
				$valuesToInsert['total'] = $movimento['total'];
				$valuesToInsert['sucursalId'] = $movimento['idSucursal'];
				$valuesToInsert['tipoMovimiento'] = $movimento['tipoMovimiento'];
				$movimento['status'] = 'A';
				$movimento['fechaLog'] = $fecha;
				
				if ( $baja) {
					$valuesToInsert['status'] = 'B';
					$egresos->actualizaLogAutomatico($movimento);
				}
				else{
					$egresos->registraMovimiento($valuesToInsert);
					$egresos->actualizaLogAutomatico($movimento);
				}
			}
	}

	function verificaMontos($monto)
	{
		if ( is_numeric($monto)) {
			return $monto;
		} else {
			if ( $monto == "") {
				return 0;
			}
			exit(0);
		}
		
	}

	 function validateExpression( $expresion)
	{
		$expresionMensual = "/^[0-9][0-9]\/m$/";
		$expresionDiaria = "/^-$/";
		// $expresionAnual = "/^[0-3][0-9]\/[0-1][0-9]\/[2][0-9][0-9][0-9]$/";
		$expresionAnual = "/^\[0-3][0-9]\/[0-1][0-9]\/a$/";
		$expresionNdias = "/^\/[0-9][0-9]?$/";

		if( preg_match($expresionMensual, $expresion) ){
			return 'm';
		}elseif ( preg_match($expresionAnual, $expresion) ) {
			return 'a';
		}//elseif ( preg_match($expresionDiaria, $expresion) ) {
			//return 'd';
		//}elseif ( preg_match($expresionNdias, $expresion) ) {
			//return 'n';
		//}
		else{
			return false;
		}
	}

	function num2letras($num, $fem = false, $dec = true) { 
	   $matuni[2]  = "dos"; 
	   $matuni[3]  = "tres"; 
	   $matuni[4]  = "cuatro"; 
	   $matuni[5]  = "cinco"; 
	   $matuni[6]  = "seis"; 
	   $matuni[7]  = "siete"; 
	   $matuni[8]  = "ocho"; 
	   $matuni[9]  = "nueve"; 
	   $matuni[10] = "diez"; 
	   $matuni[11] = "once"; 
	   $matuni[12] = "doce"; 
	   $matuni[13] = "trece"; 
	   $matuni[14] = "catorce"; 
	   $matuni[15] = "quince"; 
	   $matuni[16] = "dieciseis"; 
	   $matuni[17] = "diecisiete"; 
	   $matuni[18] = "dieciocho"; 
	   $matuni[19] = "diecinueve"; 
	   $matuni[20] = "veinte"; 
	   $matunisub[2] = "dos"; 
	   $matunisub[3] = "tres"; 
	   $matunisub[4] = "cuatro"; 
	   $matunisub[5] = "quin"; 
	   $matunisub[6] = "seis"; 
	   $matunisub[7] = "sete"; 
	   $matunisub[8] = "ocho"; 
	   $matunisub[9] = "nove"; 
	
	   $matdec[2] = "veint"; 
	   $matdec[3] = "treinta"; 
	   $matdec[4] = "cuarenta"; 
	   $matdec[5] = "cincuenta"; 
	   $matdec[6] = "sesenta"; 
	   $matdec[7] = "setenta"; 
	   $matdec[8] = "ochenta"; 
	   $matdec[9] = "noventa"; 
	   $matsub[3]  = 'mill'; 
	   $matsub[5]  = 'bill'; 
	   $matsub[7]  = 'mill'; 
	   $matsub[9]  = 'trill'; 
	   $matsub[11] = 'mill'; 
	   $matsub[13] = 'bill'; 
	   $matsub[15] = 'mill'; 
	   $matmil[4]  = 'millones'; 
	   $matmil[6]  = 'billones'; 
	   $matmil[7]  = 'de billones'; 
	   $matmil[8]  = 'millones de billones'; 
	   $matmil[10] = 'trillones'; 
	   $matmil[11] = 'de trillones'; 
	   $matmil[12] = 'millones de trillones'; 
	   $matmil[13] = 'de trillones'; 
	   $matmil[14] = 'billones de trillones'; 
	   $matmil[15] = 'de billones de trillones'; 
	   $matmil[16] = 'millones de billones de trillones'; 
	   
	   //Zi hack
	   $float=explode('.',$num);
	   $num=$float[0];
	
	   $num = trim((string)@$num); 
	   if ($num[0] == '-') { 
		  $neg = 'menos '; 
		  $num = substr($num, 1); 
	   }else 
		  $neg = ''; 
	   while ($num[0] == '0') $num = substr($num, 1); 
	   if ($num[0] < '1' or $num[0] > 9) $num = '0' . $num; 
	   $zeros = true; 
	   $punt = false; 
	   $ent = ''; 
	   $fra = ''; 
	   for ($c = 0; $c < strlen($num); $c++) { 
		  $n = $num[$c]; 
		  if (! (strpos(".,'''", $n) === false)) { 
			 if ($punt) break; 
			 else{ 
				$punt = true; 
				continue; 
			 } 
	
		  }elseif (! (strpos('0123456789', $n) === false)) { 
			 if ($punt) { 
				if ($n != '0') $zeros = false; 
				$fra .= $n; 
			 }else 
	
				$ent .= $n; 
		  }else 
	
			 break; 
	
	   } 
	   $ent = '     ' . $ent; 
	   if ($dec and $fra and ! $zeros) { 
		  $fin = ' coma'; 
		  for ($n = 0; $n < strlen($fra); $n++) { 
			 if (($s = $fra[$n]) == '0') 
				$fin .= ' cero'; 
			 elseif ($s == '1') 
				$fin .= $fem ? ' una' : ' un'; 
			 else 
				$fin .= ' ' . $matuni[$s]; 
		  } 
	   }else 
		  $fin = ''; 
	   if ((int)$ent === 0) return 'Cero ' . $fin; 
	   $tex = ''; 
	   $sub = 0; 
	   $mils = 0; 
	   $neutro = false; 
	   while ( ($num = substr($ent, -3)) != '   ') { 
		  $ent = substr($ent, 0, -3); 
		  if (++$sub < 3 and $fem) { 
			 $matuni[1] = 'una'; 
			 $subcent = 'as'; 
		  }else{ 
			 $matuni[1] = $neutro ? 'un' : 'uno'; 
			 $subcent = 'os'; 
		  } 
		  $t = ''; 
		  $n2 = substr($num, 1); 
		  if ($n2 == '00') { 
		  }elseif ($n2 < 21) 
			 $t = ' ' . $matuni[(int)$n2]; 
		  elseif ($n2 < 30) { 
			 $n3 = $num[2]; 
			 if ($n3 != 0) $t = 'i' . $matuni[$n3]; 
			 $n2 = $num[1]; 
			 $t = ' ' . $matdec[$n2] . $t; 
		  }else{ 
			 $n3 = $num[2]; 
			 if ($n3 != 0) $t = ' y ' . $matuni[$n3]; 
			 $n2 = $num[1]; 
			 $t = ' ' . $matdec[$n2] . $t; 
		  } 
		  $n = $num[0]; 
		  if ($n == 1) { 
			  
			  if ( ($num % 100) == 0) {
				  $t = ' cien' . $t; 
			  }else{
				$t = ' ciento' . $t; 
			  }
			 
		  }elseif ($n == 5){ 
			 $t = ' ' . $matunisub[$n] . 'ient' . $subcent . $t; 
		  }elseif ($n != 0){ 
			 $t = ' ' . $matunisub[$n] . 'cient' . $subcent . $t; 
		  } 
		  if ($sub == 1) { 
		  }elseif (! isset($matsub[$sub])) { 
			 if ($num == 1) { 
				$t = ' mil'; 
			 }elseif ($num > 1){ 
				$t .= ' mil'; 
			 } 
		  }elseif ($num == 1) { 
			 $t .= ' ' . $matsub[$sub] . '?n'; 
		  }elseif ($num > 1){ 
			 $t .= ' ' . $matsub[$sub] . 'ones'; 
		  }   
		  if ($num == '000') $mils ++; 
		  elseif ($mils != 0) { 
			 if (isset($matmil[$sub])) $t .= ' ' . $matmil[$sub]; 
			 $mils = 0; 
		  } 
		  $neutro = true; 
		  $tex = $t . $tex; 
	   } 
	   $tex = $neg . substr($tex, 1) . $fin; 
	   //Zi hack --> return ucfirst($tex);
	//    var_dump( $float);
	   $end_num=ucfirst($tex).' pesos '.$float[1].'/100 M.N.';
	   return $end_num; 
	}


	function dateFormatter( $fecha){
		$fecha = str_replace('/', '-', $fecha);

		return date('Y-m-d', strtotime($fecha) );
	}

	 function getMesAsString( $mes)
    {
        $mes = $mes / 1;
        $meses = array('-','ENERO','FEBRERO','MARZO','ABRIL','MAYO','JUNIO','JULIO','AGOSTO','SEPTIEMBRE','OCTUBRE','NOVIEMBRE','DICIEMBRE');
        return $meses[$mes];
	}
?>