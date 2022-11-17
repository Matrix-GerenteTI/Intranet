<?php
	if(!isset($_SESSION)){ 
		session_start(); 
	}
	require_once($_SERVER['DOCUMENT_ROOT']."/".$_SESSION['RUTA']."/controladores/sesiones.php");
	require_once($_SERVER['DOCUMENT_ROOT']."/".$_SESSION['RUTA']."/modelos/con_edosfinancieros.php");
	$sesion = new Sesion();
	$edosfinancieros = new EdoFinancieros();
	$opcion = $_GET['opc'];
	switch($opcion){
		case 'getedoresultados':{
			$udn = $_POST['udn'];
			$fecini = $sesion->formateaFecha($_POST['fecini'],'d2g',1);
			$fecfin = $sesion->formateaFecha($_POST['fecfin'],'d2g',1);
			$fecinif = $sesion->formateaFecha($_POST['fecini'],'d2f',0);
			$fecfinf = $sesion->formateaFecha($_POST['fecfin'],'d2f',0);
			$totalesventas = $edosfinancieros->getTotalVentas($udn,$fecinif,$fecfinf);
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
				}
			}
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
			}else{
				$utilidadbruta = $totventas - $totcostoventas;
				if($totventas>0){
					$prcutilb = ($utilidadbruta*100) / $totventas;
					$prcctob = ($totcostoventas*100) / $totventas;
				}else{
					$prcutilb = 0;
					$prcctob = 0;
				}
			}	
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
			if($udn=='%'){
				$echo.= '			<div class="col-xs-9">';
				$echo.= ' 				<b>(-) CXP</b>';
				$echo.= '			</div>';
				$echo.= '			<div class="col-xs-3" style="text-align:right">';
				$echo.= ' 				('.number_format($prcctob,0,'.',',').'%) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- '.number_format($totalcxp,2,'.',',');
				$echo.= '			</div>';
			}else{
				$echo.= '			<div class="col-xs-9">';
				$echo.= ' 				<b>(-) Costo de Ventas</b>';
				$echo.= '			</div>';
				$echo.= '			<div class="col-xs-3" style="text-align:right">';
				$echo.= ' 				('.number_format($prcctob,0,'.',',').'%) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- '.number_format($totcostoventas,2,'.',',');
				$echo.= '			</div>';
			}	
			$echo.= '			</div>';
			$echo.= '		</div>';
			$echo.= '	  </div>';
			$echo.= '	</div>';
			//CABECERA GASTOS DE OPERACION
			foreach($arrArbol as $idx => $val){
				$totcta = 0;
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
			$echo.= '	<div class="panel panel-default">';
			$echo.= '	  <div class="panel-heading">';
			$echo.= '		<div class="row">';
			$echo.= '			<div class="col-xs-9">';
			$echo.= '				<h4 class="panel-title">';
			$echo.= '		  			<a data-toggle="collapse" data-parent="#accordion" href="#collapse2"><b>GASTOS DE OPERACION</b></a>';
			$echo.= '				</h4>';
			$echo.= '			</div>';
			$echo.= '			<div class="col-xs-3" style="text-align:right">';
			$echo.= '				<h4 class="panel-title">';
			$echo.= '		  			<a data-toggle="collapse" data-parent="#accordion" href="#collapse2"><b>-'.number_format(abs($totcta),2,'.',',').'</b></a>';
			$echo.= '				</h4>';
			$echo.= '			</div>';
			$echo.= '		</div>';			
			$echo.= '	  </div>';
			//Comienza el contenido del panel colapsado
			$echo.= '	  <div id="collapse2" class="panel-collapse collapse">';
			$echo.= '		<div class="panel-body">';
			//ROW DE LOS GASTOS
			foreach($arrArbol as $idx => $val){
				$totcta = 0;
				foreach($val as $idx1 => $val1){
					$totsubcta = 0;
					if($idx1 == '601-01-000'){
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
					$totcta = $totcta + $totsubcta;
				}
			}
			$echo.= '		</div>';
			$echo.= '	  </div>';
			$echo.= '	</div>';
			//CABECERA GASTOS FINANCIEROS
			foreach($arrArbol as $idx => $val){
				$gastosFinancieros = 0;
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
			$echo.= '	<div class="panel panel-default">';
			$echo.= '	  <div class="panel-heading">';
			$echo.= '		<div class="row">';
			$echo.= '			<div class="col-xs-9">';
			$echo.= '				<h4 class="panel-title">';
			$echo.= '		  			<a data-toggle="collapse" data-parent="#accordion" href="#collapse3"><b>GASTOS FINANCIEROS</b></a>';
			$echo.= '				</h4>';
			$echo.= '			</div>';
			$echo.= '			<div class="col-xs-3" style="text-align:right">';
			$echo.= '				<h4 class="panel-title">';
			$echo.= '		  			<a data-toggle="collapse" data-parent="#accordion" href="#collapse3"><b>-'.number_format(abs($gastosFinancieros),2,'.',',').'</b></a>';
			$echo.= '				</h4>';
			$echo.= '			</div>';
			$echo.= '		</div>';			
			$echo.= '	  </div>';
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
			$utilidad = $utilidadoperacion - abs($gastosFinancieros);
			if($utilidad<0){
				$txtcolor = 'red';
				$txtutilperd = 'P&Eacute;RDIDA';
			}else{
				$txtcolor = 'green';
				$txtutilperd = 'UTILIDAD';
			}
			$echo.= '	<div class="panel panel-default">';
			$echo.= '	  <div class="panel-heading">';
			$echo.= '		<div class="row">';
			$echo.= '			<div class="col-xs-9">';
			$echo.= '				<h4 class="panel-title">';
			$echo.= '		  			<a data-toggle="collapse" data-parent="#accordion" href="#collapse4" style="color:'.$txtcolor.'"><b>'.$txtutilperd.'</b></a>';
			$echo.= '				</h4>';
			$echo.= '			</div>';
			$echo.= '			<div class="col-xs-3" style="text-align:right">';
			$echo.= '				<h4 class="panel-title">';
			$echo.= '		  			<a data-toggle="collapse" data-parent="#accordion" href="#collapse4" style="color:'.$txtcolor.'"><b>'.number_format($utilidad,2,'.',',').'</b></a>';
			$echo.= '				</h4>';
			$echo.= '			</div>';
			$echo.= '		</div>';			
			$echo.= '	  </div>';
			$echo.= '	</div>';
			$echo.= '</div>';
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
?>