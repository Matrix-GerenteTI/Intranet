<?php
	if(!isset($_SESSION)){ 
		session_start(); 
	}
	require_once(dirname(__DIR__)."/controladores/sesiones.php");
	require_once(dirname(__DIR__)."/modelos/con_edosfinancieros.php");
	require_once(dirname(__DIR__)."/modelos/saldos_bancarios.php");
	
	$sesion = new Sesion();
	$edosfinancieros = new EdoFinancieros();
	$saldosBancarios = new SaldosBancarios;

	$opcion = $_GET['opc'];
	switch($opcion){
		case 'getedoresultados':{
			

			$udn = $_POST['udn'];
			
			$fecini = $sesion->formateaFecha($_POST['fecini'],'d2g',1);
			$fecfin = $sesion->formateaFecha($_POST['fecfin'],'d2g',1);
			$fecinif = $sesion->formateaFecha($_POST['fecini'],'d2f',0);
			$fecfinf = $sesion->formateaFecha($_POST['fecfin'],'d2f',0);
			$totalesventas = $edosfinancieros->getTotalVentas($udn,$fecinif,$fecfinf);
			$totalCompras =  $edosfinancieros->getTotalCompras($udn,$fecini,$fecfinf); //New added
			$sucursalFiliacion = $saldosBancarios->getFiliaciones( $udn );
			$totalEgresoBancos = 0;

			if ($udn != '%') {
				foreach ($sucursalFiliacion as $i => $sucursal) {
					$egresosBancarios = $saldosBancarios->getEgresoPorFilicacion($sucursal['filiacion'], $fecini, $fecfin);
					$totalEgresoBancos += $egresosBancarios[0]['egresos'];
				}				
			}else{
					$egresosBancarios = $saldosBancarios->getEgresoPorFilicacion('%', $fecini, $fecfin);
					$totalEgresoBancos += $egresosBancarios[0]['egresos'];				
			}

			$arrayCtas = array();
			$arrIN = array();
			$totventas = $totalesventas['ventas'];
			$totcostoventas = $totalesventas['costos'];
			$echo = "";
			$arregloCuentas = $edosfinancieros->getCuentas();
			$arrArbol = array();
			$arrCuentas = array();
			foreach($arregloCuentas as $cuenta){
				$xta = explode("-",$cuenta['cuenta']);
				if((int)$xta[1]==0 && (int)$xta[2]==0)
					$arrArbol[$xta[0].'-00-000'] = array();
				
				if((int)$xta[1]>0 && (int)$xta[2]==0)
					$arrArbol[$xta[0].'-00-000'][$xta[0].'-'.$xta[1].'-000'] = array();
				
				if((int)$xta[1]>0 && (int)$xta[2]>0){
					$arrArbol[$xta[0].'-00-000'][$xta[0].'-'.$xta[1].'-000'][$cuenta['cuenta']]['naturaleza'] = $cuenta['naturaleza'];
					$arrArbol[$xta[0].'-00-000'][$xta[0].'-'.$xta[1].'-000'][$cuenta['cuenta']]['nombre'] = $cuenta['nombre'];
					$arrArbol[$xta[0].'-00-000'][$xta[0].'-'.$xta[1].'-000'][$cuenta['cuenta']]['subtotal'] = $edosfinancieros->getMovimientosCuentaTotal($udn,$fecini,$fecfin,$cuenta['cuenta']);
					if($arrArbol[$xta[0].'-00-000'][$xta[0].'-'.$xta[1].'-000'][$cuenta['cuenta']]['subtotal']>0){
						if(!in_array($xta[0].'-'.$xta[1].'-000',$arrIN)){
							$arrayCtas[$cuenta['cuenta']] = $arrArbol[$xta[0].'-00-000'][$xta[0].'-'.$xta[1].'-000'][$cuenta['cuenta']]['subtotal'];
							$arrIN[] = $cuenta['cuenta'];
						}else{
							$arrayCtas[$cuenta['cuenta']]+= $arrArbol[$xta[0].'-00-000'][$xta[0].'-'.$xta[1].'-000'][$cuenta['cuenta']]['subtotal'];
						}
					}
				}
			}

			// echo "<pre>";
			// var_dump($arrArbol);
			// echo "</pre>";
			// die();
			/*
			if($udn=='%'){
				$totalcxp = 0;
				$arrcxp = $edosfinancieros->getTotalCXP($fecinif,$fecfinf);
				
				foreach($arrcxp as $rcxp){
					$totalcxp= $totalcxp + $rcxp['IMPORTECOBRO'];
				}
				$utilidadbruta = $totventas - $totalcxp;
				if($totventas>0){
					$prcutilb = ($utilidadbruta*100) / $totventas;
					$prcctob = ($totalcxp*100) / $totventas;
				}else{
					$prcutilb = 0;
					$prcctob = 0;
				}
			}else{*/
				$utilidadbruta = $totventas - $totcostoventas;
				if($totventas>0){
					$prcutilb = ($utilidadbruta*100) / $totventas;
					$prcctob = ($totcostoventas*100) / $totventas;
				}else{
					$prcutilb = 0;
					$prcctob = 0;
				}
			//}	
			//CABECERA UTILIDAD BRUTA
			$echo.= '<div class="panel-group">';
			$echo.= '	<div class="panel panel-default">';
			$echo.= '	  <div class="panel-heading">';
			$echo.= '		<div class="row">';
			$echo.= '			<div class="col-xs-9">';
			$echo.= '				<h4 class="panel-title">';
			$echo.= '		  			<a data-toggle="collapse" data-parent="#accordion" href="#collapse1"><b>UTILIDAD BRUTA</b></a>';
			$echo.= '				</h4>';
			$echo.= '			</div>';
			$echo.= '			<div class="col-xs-3" style="text-align:right">';
			$echo.= '				<h4 class="panel-title">';
			$echo.= '		  			<a data-toggle="collapse" data-parent="#accordion" href="#collapse1"><b>('.number_format($prcutilb,0,'.',',').'%) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.number_format($utilidadbruta,2,'.',',').' </b></a>';
			$echo.= '				</h4>';
			$echo.= '			</div>';
			$echo.= '		</div>';			
			$echo.= '	  </div>';
			//Comienza el contenido del panel colapsado
			$echo.= '	  <div id="collapse1" class="panel-collapse collapse in">';
			$echo.= '		<div class="panel-body">';
			//ROW PARA VENTAS
			$echo.= '			<div class="row">';
			$echo.= '				<div class="col-xs-9">';
			$echo.= ' 					<b>(+) Ventas</b>';
			$echo.= '				</div>';
			$echo.= '				<div class="col-xs-3" style="text-align:right">';
			$echo.= ' 					'.number_format($totventas,2,'.',',');
			$echo.= '				</div>';
			$echo.= '			</div>';
			//ROW DE LOS COSTOS O CXP
			$echo.= '			<div class="row">';
			/*if($udn=='%'){
				$echo.= '			<div class="col-xs-9">';
				$echo.= ' 				<b>(-) CXP</b>';
				$echo.= '			</div>';
				$echo.= '			<div class="col-xs-3" style="text-align:right">';
				$echo.= ' 				('.number_format($prcctob,0,'.',',').'%) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- '.number_format($totalcxp,2,'.',',');
				$echo.= '			</div>';
			}else{*/
				$echo.= '			<div class="col-xs-9">';
				$echo.= ' 				<b>(-) Costo de Ventas</b>';
				$echo.= '			</div>';
				$echo.= '			<div class="col-xs-3" style="text-align:right">';
				$echo.= ' 				('.number_format($prcctob,0,'.',',').'%) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- '.number_format($totcostoventas,2,'.',',');
				$echo.= '			</div>';
			//}	
			$echo.= '			</div>';
			$echo.= '		</div>';
			$echo.= '	  </div>';
			$echo.= '	</div>';
			
			//CABECERA DE COMPRAS
			$importeTotalCompra = 0;
			foreach ($totalCompras as $compra) {
				$importeTotalCompra +=  $compra['IMPORTETOTAL'];
			}
			//agregando parametros de edicion al panel header
			$setPanel = array('cantidad' => $importeTotalCompra,'collapseId' => 0, 'titulo' => "(*) COMPRAS",'signo' => " ");
			//$echo .=setPanelHeader($setPanel); //Crea el panel con la cantidad

			//agregando parametros de edicion al panel header EGRESOS BANCOS
			$setPanel = array('cantidad' => $totalEgresoBancos,'collapseId' => 0, 'titulo' => "(*) BANCOS - EGRESOS",'signo' => " ");
			//$echo .=setPanelHeader($setPanel); //Crea el panel con la cantidad

			//CABECERA GASTOS DE OPERACION
			$totcta = 0;
			foreach($arrArbol as $idx => $val){
				foreach($val as $idx1 => $val1){
					$totsubcta = 0;
					if($idx1 == '601-01-000'){
						foreach($val1 as $idx2=> $val2){
							$importe = $edosfinancieros->getMovimientosCuentaTotal($udn,$fecini,$fecfin,$idx2);

							if($val2['naturaleza'] == 1)
								$totsubcta = $totsubcta + $importe;
							else
								$totsubcta = $totsubcta - $importe;
						}
					}

					$totcta = $totcta + $totsubcta;		
				}
			}
			$utilidadoperacion = $utilidadbruta - abs($totcta);
			
			$porcOperativo = number_format(($totcta/$utilidadbruta)*100,0,'.','');

			$setPanel = array('cantidad' => abs($totcta),'collapseId' => 2, 'titulo' => "GASTOS DE OPERACIÓN",'signo' => "-",'porcentaje'=>$porcOperativo);
			$echo .=setPanelHeader($setPanel); //Crea el panel con la cantidad

			//Comienza el contenido del panel colapsado
			$echo.= '	  <div id="collapse2" class="panel-collapse collapse">';
			$echo.= '		<div class="panel-body">';
			//ROW DE LOS GASTOS
			$totcta = 0;
			foreach($arrArbol as $idx => $val){
				
				foreach($val as $idx1 => $val1){
					$totsubcta = 0;
					//if($idx1 == '601-01-000'){
						foreach($val1 as $idx2=> $val2){
							$importe = $edosfinancieros->getMovimientosCuentaTotal($udn,$fecini,$fecfin,$idx2);
							$echo.= '<div class="row">';
							$echo.= '	<div class="col-xs-9">';
							$echo.= ' 		(-) <a data-toggle="modal" href="#modaldetalles" onclick="getDetalles(\''.$idx2.'\')" >'.$edosfinancieros->getNombreCuenta($idx2).'</a>';
							$echo.= '	</div>';							
							$echo.= '	<div class="col-xs-3" style="text-align:right">';
							$echo.= ' 		- '.number_format($importe,2,'.',',');
							$echo.= '	</div>';
							$echo.= '</div>';
							if($val2['naturaleza'] == 1)
								$totsubcta = $totsubcta + $importe;
							else
								$totsubcta = $totsubcta - $importe;
						}
					//}
					$totcta = $totcta + $totsubcta;
				}
			}
			$gastosOperativos = $totcta;
			$echo.= '		</div>';
			$echo.= '	  </div>';
			$echo.= '	</div>';
			//CABECERA GASTOS FINANCIEROS
			$gastosFinancieros = 0;
			foreach($arrArbol as $idx => $val){
				
				foreach($val as $idx1 => $val1){
					$totsubcta = 0;
					if($idx1 == '601-03-000'){
						foreach($val1 as $idx2=> $val2){
							$importe = $edosfinancieros->getMovimientosCuentaTotal($udn,$fecini,$fecfin,$idx2);
							if($val2['naturaleza'] == 1)
								$totsubcta = $totsubcta + $importe;
							else
								$totsubcta = $totsubcta - $importe;
						}
					}
					$gastosFinancieros = $gastosFinancieros + $totsubcta;
				}
			}
			
			$porcFinancieros = number_format(($gastosFinancieros/$utilidadbruta)*100,0,'.','');

			$setPanel = array('cantidad' => abs($gastosFinancieros),'collapseId' => 3, 'titulo' => "GASTOS FINANCIEROS",'signo' => "-",'porcentaje'=>$porcFinancieros);
			$echo .=setPanelHeader($setPanel); //Crea el panel con la cantidad

			//Comienza el contenido del panel colapsado
			$echo.= '	  <div id="collapse3" class="panel-collapse collapse">';
			$echo.= '		<div class="panel-body">';
			//ROW DE LOS GASTOS
			foreach($arrArbol as $idx => $val){
				$gastosFinancieros = 0;
				foreach($val as $idx1 => $val1){
					$totsubcta = 0;
					if($idx1 == '601-03-000'){
						foreach($val1 as $idx2=> $val2){
							$importe = $edosfinancieros->getMovimientosCuentaTotal($udn,$fecini,$fecfin,$idx2);
							$echo.= '<div class="row">';
							$echo.= '	<div class="col-xs-9">';
							$echo.= ' 		(-) <a data-toggle="modal" href="#modaldetalles" onclick="getDetalles(\''.$idx2.'\')" >'.$edosfinancieros->getNombreCuenta($idx2).'</a>';
							$echo.= '	</div>';							
							$echo.= '	<div class="col-xs-3" style="text-align:right">';
							$echo.= ' 		- '.number_format($importe,2,'.',',');
							$echo.= '	</div>';
							$echo.= '</div>';
							if($val2['naturaleza'] == 1)
								$totsubcta = $totsubcta + $importe;
							else
								$totsubcta = $totsubcta - $importe;
						}
					}
					$gastosFinancieros = $gastosFinancieros + $totsubcta;
				}
			}
			$echo.= '		</div>';
			$echo.= '	  </div>';
			$echo.= '	</div>';
			//CABECERA UTILIDAD O PERDIDA
			$utilidad = $utilidadbruta - abs($gastosFinancieros)- abs( $gastosOperativos );
			//var_dump( $utilidadoperacion );
			if($utilidad<0){
				$txtcolor = 'red';
				$txtutilperd = 'P&Eacute;RDIDA';
			}else{
				$txtcolor = 'green';
				$txtutilperd = 'UTILIDAD';
			}

			$porcUtilidad = number_format(($utilidad/$utilidadbruta)*100,0,'.','');

			$setPanel = array('cantidad' => $utilidad,'collapseId' => 4, 'titulo' => "$txtutilperd",'signo' => "",'porcentaje'=>$porcUtilidad);
			$echo .=setPanelHeader($setPanel,"color:$txtcolor"); //Crea el panel con la cantidad

			echo $echo;
			break;
		}
		
		case 'getDetallesCuenta':{
			$udn = $_POST['udn'];
			$fecini = $sesion->formateaFecha($_POST['fecini'],'d2g',1);
			$fecfin = $sesion->formateaFecha($_POST['fecfin'],'d2g',1);
			$cuenta = $_POST['cuenta'];
			$arreglo = $edosfinancieros->getDetallesCuenta($udn,$fecini,$fecfin,$cuenta);
			echo json_encode($arreglo);
			break;
		}
	}

	function setPanelHeader($params,$style = ""){
		$porc = '';
		if($params['porcentaje']!='null'){
			$porc = "(".abs($params['porcentaje'])."%)     ";
		}
		$echo = "";
		$echo.= '	<div class="panel panel-default" style:"display:none">';
		$echo.= '	  <div class="panel-heading">';
		$echo.= '		<div class="row">';
		$echo.= '			<div class="col-xs-9">';
		$echo.= '				<h4 class="panel-title">';
		$echo.= "		  			<a data-toggle='collapse' data-parent='#accordion' href='#collapse".$params['collapseId']."' style='".$style."'><b>".$params['titulo']."</b></a>";
		$echo.= '				</h4>';
		$echo.= '			</div>';
		$echo.= '			<div class="col-xs-3" style="text-align:right">';
		$echo.= '				<h4 class="panel-title">';
		$echo.= "		  			<a data-toggle='collapse' data-parent='#accordion' href='#collapse".$params['collapseId']."' style='".$style."'><b>".$porc." ".$params['signo']."". number_format($params['cantidad'],2,'.',',') ."</b></a>";
		$echo.= '				</h4>';
		$echo.= '			</div>';
		$echo.= '		</div>';			
		$echo.= '	  </div>';

		return $echo;
	}
?>