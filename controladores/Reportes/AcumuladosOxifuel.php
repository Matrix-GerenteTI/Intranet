<?php 
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/Almacenes/Articulos.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/Almacenes/Almacen.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/Reportes/prepareExcel.php";


class AcumuladoVentasFamilias extends PrepareExcel
{
    private $columnaTotal;
    private $columnaTotalMeta;
    private $columnaDiferencia;

    public function __construct()
    {
        parent::__construct();
        $this->libro->getProperties()->setTitle('ACUMULADO DE VENTAS OXIFUEL'); 
    }
    
    public function getVentasAgrupadasMes( $mes , $anio)
    {
        $modeloArticulos =  new Articulos;
        $almacenes = [];
        $listadoVentas = $modeloArticulos->getListadoVentasMes($mes, $anio) ;

        $ventasAgrupadas = [];
        foreach ($listadoVentas as $articulo) {
            if($articulo->FAMILIA=='OXIFUEL'){
                if (!in_array( $articulo->ALMACEN , $almacenes ) ) {
                    $ventasAgrupadas[$articulo->FAMILIA][$articulo->FECHA][$articulo->ALMACEN]['balanceVentas']  = $modeloArticulos->getTotalVenta( $articulo->IDALMACEN , $mes , $anio)[0]->TOTALVENTA;
                    array_push( $almacenes , $articulo->ALMACEN );
                }
                if ( !isset($ventasAgrupadas[$articulo->FAMILIA][$articulo->FECHA][$articulo->ALMACEN]['cant'] ) )  {
                    $ventasAgrupadas[$articulo->FAMILIA][$articulo->FECHA][$articulo->ALMACEN]['cant'] = $articulo->CANTIDAD;
                    $ventasAgrupadas[$articulo->FAMILIA][$articulo->FECHA][$articulo->ALMACEN]['cantFacturado'] = $articulo->STATUS == 'PEDIDO FACTURADO' ? $articulo->CANTIDAD : 0;
                    $ventasAgrupadas[$articulo->FAMILIA][$articulo->FECHA][$articulo->ALMACEN]['totalEntradaSalidas'] = 0;
                    
                    
                }else{
                    $ventasAgrupadas[$articulo->FAMILIA][$articulo->FECHA][$articulo->ALMACEN]['cant'] += $articulo->CANTIDAD;
                    $ventasAgrupadas[$articulo->FAMILIA][$articulo->FECHA][$articulo->ALMACEN]['cantFacturado'] += $articulo->STATUS == 'PEDIDO FACTURADO' ? $articulo->CANTIDAD : 0;
                }

                $ventasAgrupadas[$articulo->FAMILIA][$articulo->FECHA][$articulo->ALMACEN]['idAlmacen'] = $articulo->IDALMACEN;
                //obteniendo los movimientos de entrada y salida en el almacen articulos de OXIFUEL
                $idRefOxifuel = $modeloArticulos->getIdRefArticulo( 'OXIFUEL' , $articulo->IDALMACEN )[0]->ID;

                $cantidadArticulosCirculados = $modeloArticulos->getStockInicial( $articulo->FECHA , $idRefOxifuel ,'OXIFUEL' , $articulo->IDALMACEN)[0]->CANTIDAD;
                
                

                $ventasAgrupadas[$articulo->FAMILIA][$articulo->FECHA][$articulo->ALMACEN]['saldoMovtosFecha'] = $cantidadArticulosCirculados;
                $ventasAgrupadas[$articulo->FAMILIA][$articulo->FECHA][$articulo->ALMACEN]['idRef'] = $idRefOxifuel;
                

            }
        }

        return $ventasAgrupadas;
    }

    public function generaReporteAcumulados( $mes, $anio)
    {
        $columnasReservadas = [['B','C','D','E'],['F','G','H','I'],['J','K','L','M'],['N','O','P','Q'],['R','S','T','U'],['V','W','X','Y'],['Z','AA','AB','AC'],['AD','AE','AF','AG'],['AH','AI','AJ','AK'],
        ['AL','AM','AN','AO'],['AP','AQ','AR','AS'],['AT','AU','AV','AW'],['AX','AY','AZ','BA'],['BB','BC','BD','BE']
                                                    ,['BF','BG','BH','BI'],['BJ','BK','BL','BM'],['BN','BO','BP','BQ'],['BR','BS','BT','BU'],['BV','BW','BX','BY'],['BZ','CA','CB','CC'],['CD','CE','CF','CG'],
                                                    ['CH','CI','CJ','CK'],['CL','CM','CN','CO'],['CP','CQ','CR','CS']
                                                    ,['CT','CU','CV','CW'],['CX','CY','CZ','DA'],['DB','DC','DD','DE'],['DF','DG','DH','DI'],['DJ','DK','DL','DM'],['DN','DO','DP','DR'],['DS','DT','DU','DV'] ];
        $columnaTotal = "DX"; //bm
        $columnaTotalMeta = "DW";
        $columnaDiferencia = "DY";

        $listadoVentas = self::getVentasAgrupadasMes($mes, $anio);

        $modeloAlmacen = new Almacen;
        $modeloArticulo = new Articulos;
        $sucursales = $modeloAlmacen->getSucursales();
        $cantSucursales = sizeof( $sucursales );
        $i = 10;
        $hoja = 0;
        $rowTotal = $i + $cantSucursales;

        $this->creaEmptySheet( 'OXIFUEL', $hoja );
        foreach ($sucursales as $x => $sucursal) {
                                //OBTENIENDO EL STOCK ACUTUAL DEL ITEM EN EL REF_ARTXALMACEN
                                $stockActual = $modeloArticulo->getIdRefArticulo('OXIFUEL', $sucursal->ID )[0]->STOCK;
                                $balanceMes = $modeloArticulo->getCantidadStockMovimientos( $mes , $anio , $modeloArticulo->getIdRefArticulo('OXIFUEL',$sucursal->ID)[0]->ID, 'OXIFUEL', $sucursal->ID)[0]->CANTIDAD;
                                $balanceVentas = $modeloArticulo->getTotalVenta( $sucursal->ID , $mes , $anio)[0]->TOTALVENTA;
            
                                //si el balance es negativo se suma al stock , si el balance es positivo se resta
                                $saldoInicial = ($stockActual +$balanceVentas) + $balanceMes ;

                                $this->libro->getActiveSheet()->setCellValue($columnasReservadas[0][2].($i+$x),  $saldoInicial  );
        }
        //echo $cantSucursales;
        $cantDias = cal_days_in_month( CAL_GREGORIAN, $mes, $anio);

        $inicioLabores = 0;
        foreach ($listadoVentas as $familia => $fechas) {

            if ( $familia == '' || $familia == 'RIN' || $familia == 'LLANTA' || $familia == 'REFACCION' || $familia == 'SERVICIO' ||  $familia == 'CONTRAPESO' ||  $familia == 'COLISION' ||  $familia == 'ACCESORIO' ||  $familia == 'ACCESORIOS') {
                continue;
            }
            $familia = $familia == "" ? "FAMILIA" : $familia;
            echo $familia."<br>";
             
             $hoja++;
             $dias = 1;
            //Reiniciando los valores de i 
             $i = 10;
             $this->libro->getActiveSheet()->setCellValue("A8", "SUCURSALES");
             if ( $familia == 'OXIFUEL' ) {
                $columnaTotal = $cantDias < 31 ? $columnasReservadas[$cantDias][1] : "DU";
                $columnaTotalMeta = $cantDias < 31 ? $columnasReservadas[$cantDias][0] : "DT";
                $columnaDiferencia = $cantDias < 31 ?  $cantDias == 30 ? 'DU' : $columnasReservadas[$cantDias+1][0] : "DV";               
                
                $this->columnaTotal = $columnaTotal;
                $this->columnaTotalMeta = $columnaTotalMeta;
                $this->columnaDiferencia = $columnaDiferencia;
				
				$this->libro->getActiveSheet()->setCellValue($columnaTotalMeta."8", "TOTAL");
				$this->libro->getActiveSheet()->setCellValue($columnaTotal."9",'$ TOTAL');
                $this->libro->getActiveSheet()->setCellValue($columnaTotalMeta."9",'CANT. TOTAL');
                //$this->libro->getActiveSheet()->setCellValue($columnaDiferencia."9", "DIFERENCIA");
             }else{
                 $columnaTotal = $columnaTotalMeta = $columnaDiferencia = "BL";
             }
                $this->libro->getActiveSheet()->getColumnDimension($columnaTotalMeta)->setAutoSize(false);
                $this->libro->getActiveSheet()->getColumnDimension($columnaTotalMeta)->setWidth("10");
                //$this->libro->getActiveSheet()->getColumnDimension($columnaDiferencia)->setAutoSize(false);
                //$this->libro->getActiveSheet()->getColumnDimension($columnaDiferencia)->setWidth("10");
                $this->libro->getActiveSheet()->getColumnDimension($columnaTotal)->setAutoSize(false);
                $this->libro->getActiveSheet()->getColumnDimension($columnaTotal)->setWidth("10");                

             $this->libro->getActiveSheet()->getStyle("A8:".$columnaTotal."9")->applyFromArray( $this->labelBold);
             $this->libro->getActiveSheet()->getStyle("A8:".$columnaTotal."9")->applyFromArray( $this->centrarTexto );
             $this->libro->getActiveSheet()->getStyle("A8:".$columnaTotal."9")->getFill()->applyFromArray( $this->setColorFill("DF013A") );
             $this->libro->getActiveSheet()->getStyle("A8:".$columnaTotal."9")->applyFromArray( $this->setColorText("ffffff",12) );

             //Agregando el logo
             $this->putLogo("H1", 150,150);
             $this->libro->getActiveSheet()->mergeCells("E4:M4");
             $this->libro->getActiveSheet()->setCellValue("E4","Reporte de acumulado de ventas de ".$familia);
            $this->libro->getActiveSheet()->getStyle("E4")->applyFromArray( $this->labelBold);   
             $this->libro->getActiveSheet()->getStyle("E4")->applyFromArray( $this->centrarTexto );

             $this->libro->getActiveSheet()->mergeCells("F5:L5");
            $this->libro->getActiveSheet()->setCellValue("F5", $this->getMesAsString($mes)." de ".$anio );
            $this->libro->getActiveSheet()->getStyle("F5")->applyFromArray( $this->labelBold);   
             $this->libro->getActiveSheet()->getStyle("F5")->applyFromArray( $this->centrarTexto );
             foreach ($columnasReservadas as $idxColumna => $columna) {
                 $days = array('Sun'=> 'Domingo', 'Mon' =>'Lunes', 'Tue'=>'Martes', 'Wed'=>'Miercoles','Thu' => 'Jueves','Fri'=>'Viernes', 'Sat'=>'Sábado');
                $diaSemana = date('D', strtotime(("$dias-$mes-$anio") ) );
                $diaSemana = $days[$diaSemana];
                 
                $idxColumna+= 1; //comienza desde 1


                if ( $cantDias < $dias) {
                    continue;
                }
 
                $this->libro->getActiveSheet()->setCellValue($columna[0]."7", $diaSemana);
                $this->libro->getActiveSheet()->mergeCells($columna[0]."7:".$columna[1]."7");
                $this->libro->getActiveSheet()->getStyle($columna[0]."7")->getAlignment()->setTextRotation(90);
                $this->libro->getActiveSheet()->getRowDimension("7")->setRowHeight(60);
                $this->libro->getActiveSheet()->getStyle($columna[0]."7")->applyFromArray( $this->labelBold);   
				$this->libro->getActiveSheet()->getStyle($columna[0]."7")->applyFromArray( $this->centrarTexto );
                 $this->libro->getActiveSheet()->setCellValue($columna[0]."8", $dias);
                 $this->libro->getActiveSheet()->mergeCells($columna[0]."8:".$columna[1]."8");
                 if (  $familia == 'OXIFUEL'  ) {
                     $this->libro->getActiveSheet()->setCellValue($columna[0]."9", "CANT");
                     $this->libro->getActiveSheet()->setCellValue($columna[1]."9", "$");
                     $this->libro->getActiveSheet()->setCellValue($columna[2]."9", "Inv. Ini");
                     $this->libro->getActiveSheet()->setCellValue($columna[3]."9", "Inv. Fin");
                 }else{
                    $this->libro->getActiveSheet()->setCellValue($columna[0]."9", "REAL");
                    $this->libro->getActiveSheet()->mergeCells($columna[0]."9:".$columna[1]."9");
                 }
                 $this->libro->getActiveSheet()->getStyle($columna[0]."9:".$columna[3]."9")->applyFromArray( $this->labelBold);   
                 $dias++;
                 
             }
             
             

             
            foreach ($fechas as $fecha => $almacenes) {
                $fechaSplit = explode("-", $fecha);
                $indiceColumna = $fechaSplit[2] -1;
                $columna = $columnasReservadas[ $indiceColumna ];				
                foreach ($almacenes as $almacen => $cantidad) {
                    //OBTENIENDO EL STOCK ACUTUAL DEL ITEM EN EL REF_ARTXALMACEN
                    $stockActual = $modeloArticulo->getIdRefArticulo('OXIFUEL', $cantidad['idAlmacen'] )[0]->STOCK;
                    $balanceMes = $modeloArticulo->getCantidadStockMovimientos( $mes , $anio , $cantidad['idRef'], 'OXIFUEL', $cantidad['idAlmacen'])[0]->CANTIDAD;
                    $balanceVentas = isset( $cantidad['balanceVentas']  ) ?$cantidad['balanceVentas'] : 0 ;

                    //si el balance es negativo se suma al stock , si el balance es positivo se resta
                    $saldoInicial = ($stockActual + $balanceVentas) + $balanceMes ;

                    $index = $this->buscaSucursal( $almacen );
                    if ( $familia == 'OXIFUEL') {						
                        $this->libro->getActiveSheet()->setCellValue($columna[0].($i+$index),  $cantidad['cant'] );
                        $this->libro->getActiveSheet()->setCellValue($columna[1].($i+$index),  number_format($cantidad['cant']*82.5,2,'.','') );
                        $this->libro->getActiveSheet()->getStyle($columna[1].($i+$index))->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)');
                        if ( $inicioLabores == 0 ) {
                            $this->libro->getActiveSheet()->setCellValue($columnasReservadas[0][3].($i+$index), "=". $saldoInicial."+". $cantidad['saldoMovtosFecha']."-".$columna[0].($i+$index) );
                        }else{
                            $this->libro->getActiveSheet()->setCellValue($columna[2].($i+$index),"=".  $columnasReservadas[$indiceColumna-1][3].($i+$index) );
                            $this->libro->getActiveSheet()->setCellValue($columna[3].($i+$index), "=". $columna[2].($i+$index)."+". ( -1 *$cantidad['saldoMovtosFecha'] )."-".$columna[0].($i+$index) );
                        }
                    }else{
                        $this->libro->getActiveSheet()->setCellValue($columna[0].($i+$index),  $cantidad['cant'] );
                    }
                    $this->libro->getActiveSheet()->getStyle($columna[0].($i+$index))->applyFromArray( $this->centrarTexto );

                    
                }
                $inicioLabores++;
                //Haciendo los totales del dia 
                if ( $familia == 'OXIFUEL') {
                    $this->libro->getActiveSheet()->setCellValue($columna[0].$rowTotal, "=SUM(".$columna[1]."10:$columna[1]".($rowTotal-1).")");
                    $this->libro->getActiveSheet()->getStyle($columna[0].$rowTotal)->applyFromArray( $this->labelBold);
                    $this->libro->getActiveSheet()->getStyle($columna[0].$rowTotal)->applyFromArray( $this->centrarTexto );
                }
                    $this->libro->getActiveSheet()->setCellValue($columna[0].$rowTotal, "=SUM(".$columna[0]."10:$columna[0]".($rowTotal-1).")");
                    $this->libro->getActiveSheet()->getStyle($columna[0].$rowTotal)->applyFromArray( $this->labelBold);
                    $this->libro->getActiveSheet()->getStyle($columna[0].$rowTotal)->applyFromArray( $this->centrarTexto );                

            }
            //recorriendo todos los dias que tiene el mes, de manera que se recorre desde la columna con el numero 1 al 28, 30 0 31 de acuerdo a la cantidad de dias del mesif
            if ( $familia == 'OXIFUEL') {
                for ($k=0; $k < $cantDias ; $k++) { 
                    $totalesMeta = $this->libro->getActiveSheet()->getCell( $columnasReservadas[$k][0].$rowTotal )->getValue();
                    $totalesReal = $this->libro->getActiveSheet()->getCell( $columnasReservadas[$k][1].$rowTotal  )->getValue();
                    $columnaDiaMeta = $columnasReservadas[$k][0];
                    $columnaDiaReal = $columnasReservadas[$k][1];
                    if ( $totalesMeta == '' || $totalesReal == '') {
                        
                        $this->libro->getActiveSheet()->setCellValue($columnaDiaMeta.$rowTotal, "=SUM(".$columnaDiaMeta."10:$columnaDiaMeta".($rowTotal-1).")");
                        $this->libro->getActiveSheet()->setCellValue($columnaDiaReal.$rowTotal, "=SUM(".$columnaDiaReal."10:$columnaDiaReal".($rowTotal-1).")");
						$this->libro->getActiveSheet()->getStyle($columnaDiaReal.$rowTotal)->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)');
                    }
                }
            }
            //Agregando ceros  a los campos que quedaron vacíos
            $dias = 1;
             $j= 10;
             foreach ($columnasReservadas as $position => $columna) {
                 if ( $position >= $cantDias) {
                     continue;
                 }
                 
                 foreach ($sucursales as $index => $sucursal) {
                     $valor = $familia == 'OXIFUEL' ? $this->libro->getActiveSheet()->getCell($columna[1].($j+$index) )->getValue() : $this->libro->getActiveSheet()->getCell($columna[0].($j+$index) )->getValue();
                     
                     if ( $valor == '' || $valor == NULL) {
                         if ( $familia == 'OXIFUEL') {
                            $tieneValor = $this->libro->getActiveSheet()->getCell($columnasReservadas[0][3].($j+$index) )->getValue() ;
                            if ( $tieneValor == '' || $tieneValor == NULL ) {
                                //COMPROBANDO QUE EL ELEMENTO DEL  PRIMER DIA TENGA UN VALOR
                                $this->libro->getActiveSheet()->setCellValue( $columnasReservadas[0][3].($j+$index)  ,  "=".$columnasReservadas[0][2].($j+$index)  );
                            }

                            echo $position."<br>";
                            $tieneValor = $this->libro->getActiveSheet()->getCell($columnasReservadas[$position][3].($j+$index) )->getValue();
                            if ( $tieneValor == '' || $tieneValor == NULL ) {
                                $this->libro->getActiveSheet()->setCellValue($columna[2].($i+$index),"=".  $columnasReservadas[$position-1][3].($i+$index) );
                                $this->libro->getActiveSheet()->setCellValue($columna[3].($i+$index), "=". $columnasReservadas[$position-1][3].($i+$index) );
                            }
                            $this->libro->getActiveSheet()->setCellValue($columna[0].($j+$index),  0 );
                            $this->libro->getActiveSheet()->setCellValue($columna[1].($j+$index),  0 );
                            
                            
                            

							$this->libro->getActiveSheet()->getStyle($columna[1].($j+$index))->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)');
                         }else{
                            $this->libro->getActiveSheet()->setCellValue($columna[0].($j+$index),  0 );
                         }
                         
                         $this->libro->getActiveSheet()->getStyle($columna[1].($j+$index))->applyFromArray( $this->centrarTexto );
                     }
                     if ( $familia == 'OXIFUEL'  ) {
                        $meta = $modeloArticulo->getMetasAcumulados( $sucursal->ID);
						$diarecorrido = $this->libro->getActiveSheet()->getCell($columna[0].'8')->getValue() * 1;
						$diarecorrido = date('D', strtotime(("$diarecorrido-$mes-$anio") ) );
						if($diarecorrido=='Sun'){
							//if($meta[0]['aplicadomingo']==1)
							//	$this->libro->getActiveSheet()->setCellValue($columna[0].($j+$index),  $meta[0]['meta_diaria_rin'] );
                            //else
                                 $tieneValor = $this->libro->getActiveSheet()->getCell($columna[0].($j+$index) )->getValue() ;
                                 if ( $tieneValor == '' || $tieneValor == NULL) {
                                   
                                $this->libro->getActiveSheet()->setCellValue($columna[2].($i+$index),"=".  $columnasReservadas[$position-1][3].($i+$index) );
                                $this->libro->getActiveSheet()->setCellValue($columna[3].($i+$index), "=". $columnasReservadas[$position-1][3].($i+$index) );
                                 }
								
                                 $this->libro->getActiveSheet()->setCellValue($columna[1].($j+$index),  0 );
                                     $this->libro->getActiveSheet()->getStyle($columna[1].($j+$index))->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)');

						}else{
							//$this->libro->getActiveSheet()->setCellValue($columna[0].($j+$index),  $meta[0]['meta_diaria_rin'] );
						}
                        $this->libro->getActiveSheet()->getStyle($columna[0].($j+$index))->applyFromArray( $this->centrarTexto );
                        $this->libro->getActiveSheet()->getStyle($columna[0].($j+$index))->applyFromArray( $this->labelBold);
                        /*
						if ( $valor > $meta[0]['meta_diaria_rin']) {
                            $this->libro->getActiveSheet()->getStyle($columna[1].($j+$index ) )->applyFromArray( $this->setColorText('00802b',11) );
                        }else{
                            $this->libro->getActiveSheet()->getStyle($columna[1].($j+$index ) )->applyFromArray( $this->setColorText('cc0000',11) );
                        }  
                        */
                     }else{
                         $this->libro->getActiveSheet()->mergeCells($columna[0].($j+$index).":". $columna[1].( $j+$index) );
                     }
                 }
                 $dias++;
                 
             }

             $i = 10;
             $this->libro->getActiveSheet()->setCellValue("A$rowTotal", "TOTAL");
             $this->libro->getActiveSheet()->getStyle("A$rowTotal")->applyFromArray( $this->labelBold);
            //Rellenado las filas con los nombres de las sucursales
             foreach ($sucursales as $index => $sucursal) {
                 
                 $this->libro->getActiveSheet()->setCellValue("A".($i+$index),  $sucursal->DESCRIPCION );
                 $this->libro->getActiveSheet()->setCellValue($columnaTotal.($i+$index), "=SUM(".$columnasReservadas[0][1].($i+$index).":$columnaTotal".($i+$index).")" );
                 $this->libro->getActiveSheet()->getStyle($columnaTotal.($i+$index))->applyFromArray( $this->labelBold);

                if ( $familia == 'OXIFUEL') {
                    $conentSuma = "";
                    $conentSumaMeta = '';
                    for ($k=0; $k < $cantDias ; $k++) { 
                        $conentSuma .= $columnasReservadas[$k][1].($i+$index)."+";
                        $conentSumaMeta .= $columnasReservadas[$k][0].($i+$index)."+";
                    }
                    $conentSuma = substr($conentSuma,0,-1);
                    $conentSumaMeta = substr($conentSumaMeta,0,-1);
                    $this->libro->getActiveSheet()->setCellValue($columnaTotal.($i+$index), "=$conentSuma"); 
					$this->libro->getActiveSheet()->getStyle($columnaTotal.($i+$index))->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)');
					//$metaM = $modeloArticulo->getMetasAcumulados( $sucursal->ID);
					$this->libro->getActiveSheet()->setCellValue($columnaTotalMeta.($i+$index), "=$conentSumaMeta");
					$this->libro->getActiveSheet()->getStyle($columnaTotalMeta.($i+$index))->applyFromArray( $this->labelBold);
					$this->libro->getActiveSheet()->getStyle($columnaTotalMeta.($i+$index))->applyFromArray( $this->centrarTexto );
                   // $this->libro->getActiveSheet()->setCellValue($columnaDiferencia.($i+$index), "=".$columnaTotal.($i+$index)."-".$columnaTotalMeta.($i+$index));    
                   //$diferencia =  $this->libro->getActiveSheet()->getCell($columnaDiferencia.($i+$index) )->getCalculatedValue() ;
                   //echo $diferencia ."  ---- $familia-----<br>";
                   // if ( $diferencia < 0) {
                   //     $this->libro->getActiveSheet()->getStyle($columnaDiferencia.($i+$index ) )->applyFromArray( $this->setColorText('cc0000',11) );
                    //}else if( $diferencia > 0){
                    //    $this->libro->getActiveSheet()->getStyle($columnaDiferencia.($i+$index ) )->applyFromArray( $this->setColorText('00802b',11) );
                   // }
                }

             }

             if ( $familia == 'OXIFUEL' ) {
                 //$this->libro->getActiveSheet()->setCellValue($columnaDiferencia.(10+$cantSucursales), "=SUM(".$columnaDiferencia."10:$columnaDiferencia".(10+$cantSucursales).')' );    
                 $this->anexarGraficas( $familia , $sucursales, $this->columnaTotal, 0, "VENTAS");
                 $this->anexarGraficas( $familia , $sucursales, $this->columnaTotalMeta, 1, "VENDIDO");
             }
            $this->libro->getActiveSheet()->setCellValue($columnaTotal.($i+sizeof( $sucursales )), "=SUM(".$columnaTotal.($i).":$columnaTotal".($i+ sizeof( $sucursales) ).")" );
            $this->libro->getActiveSheet()->getStyle($columnaTotal.($i+ sizeof( $sucursales )))->applyFromArray( $this->labelBold);
			$this->libro->getActiveSheet()->getStyle($columnaTotal.($i+sizeof( $sucursales )))->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)');
			
			
			$this->libro->getActiveSheet()->setCellValue($columnaTotalMeta.($i+sizeof( $sucursales )), "=SUM(".$columnaTotalMeta.($i).":$columnaTotalMeta".($i+ sizeof( $sucursales) ).")" );
            $this->libro->getActiveSheet()->getStyle($columnaTotalMeta.($i+ sizeof( $sucursales )))->applyFromArray( $this->labelBold);            
			$this->libro->getActiveSheet()->getStyle($columnaTotalMeta.($i+ sizeof( $sucursales )))->applyFromArray( $this->centrarTexto );

            foreach(range('A','BN') as $columnID) {
				$this->libro->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
			}
			
			foreach($columnasReservadas as $col){
				$this->libro->getActiveSheet()->getColumnDimension($col[1])->setWidth(15);
			}
			
			
            $this->libro->getActiveSheet()->getStyle("A8:".$columnaTotal.($rowTotal-1) )->applyFromArray( $this->bordes );
        }
        
         $reporteTerminado = new \PHPExcel_Writer_Excel2007( $this->libro);
        $reporteTerminado->setPreCalculateFormulas(true);
        $reporteTerminado->setIncludeCharts(TRUE);
         $reporteTerminado->save($_SERVER['DOCUMENT_ROOT']."/intranet/controladores/reportes/acumulado_ventas_oxifuel.xlsx");
        $ubicacion = "http://servermatrixxxb.ddns.net:8181/intranet/controladores/reportes/acumulado_ventas_oxifuel.xlsx";
        echo "<a href='$ubicacion'>Descargar</a>";
    }

    public function anexarGraficas($familia, $sucursales , $columnaTotal , $indexChart, $labelTitle)
    {
        $dimensioneGraficas = [['A','I'],['K','S']];
        $cantSucursales = sizeof( $sucursales );


        $cantDias =  cal_days_in_month(CAL_GREGORIAN, date('m')  ,date('Y')) -1 ; // se le resta uno para poder acceder al ulimo 

        $i=0;
        $inicioGrafica = 28;
        $finGrafica = 38;
        // foreach ($sucursales as $idx => $sucursal) {
            $etiquetas = array( new \PHPExcel_Chart_DataSeriesValues('String', $familia.'!$A$10:$A$'.(10+$cantSucursales-1), NULL, $cantSucursales));

            $xAxis = array( new \PHPExcel_Chart_DataSeriesValues('String', $familia.'!$A$10:$A$'.(10+$cantSucursales-1), NULL, 1 ) );         
            
            $valores = array( new \PHPExcel_Chart_DataSeriesValues('Number', $familia.'!$'.$columnaTotal.'$10:$'.$columnaTotal."$".(10+ $cantSucursales -1), NULL, $cantSucursales ) );     

                        $dataSeriesChart =new \PHPExcel_Chart_DataSeries(
                    \PHPExcel_Chart_DataSeries::TYPE_BARCHART,
                    \PHPExcel_Chart_DataSeries::GROUPING_STANDARD,
                    range(0, count($valores)-1),
                    [],
                    $xAxis,
                    $valores
                    );

                    // $dataSeriesChart->setPlotDirection(\PHPExcel_Chart_DataSeries::TYPE_BARCHART );

                    $plotArea = new \PHPExcel_Chart_PlotArea(null, array($dataSeriesChart));
                    $legend=new \PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);
                    $title = new \PHPExcel_Chart_Title("TOTAL $labelTitle POR SUC.");
                    $grafica= new \PHPExcel_Chart(
                                'ACUMULADO ',
                                $title,
                                $legend,
                                $plotArea,
                                true,
                                0,
                                NULL, 
                                NULL);
                    // if( $i > 3) {
                    //     $inicioGrafica = $finGrafica+4;
                    //     $finGrafica = $finGrafica+14;
                    //     $i = 0;
                    // }else{
                    //     $i++;
                    // }
                    
                    $grafica->setTopLeftPosition($dimensioneGraficas[ $indexChart][0]."".$inicioGrafica);
                    $grafica->setBottomRightPosition($dimensioneGraficas[ $indexChart][1]."".$finGrafica);
                    $this->libro->getActiveSheet()->addChart( $grafica);

        // }                            

    }
    public function buscaSucursal( $almacen)
    {
        $modeloAlmacen = new Almacen;
        $sucursales = $modeloAlmacen->getSucursales();
        foreach ($sucursales as $i  => $sucursal) {
            if ( $sucursal->DESCRIPCION == $almacen ) {
                return $i;
            }
        }
    }
}


$reporte = new AcumuladoVentasFamilias;
$reporte->generaReporteAcumulados( date("m"), date("Y"));
    $configCorreo = array("descripcionDestinatario" => "Acumulado de Ventas OXIFUEL",
                                        "mensaje" => "SITEX",
                                        "pathFile" => "acumulado_ventas_oxifuel.xlsx",
                                        "subject" => "Acumulado de Ventas OXIFUEL",
                                        //"correos" => array( "sestrada@matrix.com.mx")
                                        "correos" => array( "sestrada@matrix.com.mx",'ventasoxifuel@matrix.com.mx',"dispersion@matrix.com.mx","gerenteventas@matrix.com.mx","compras@matrix.com.mx","gtealmacen@matrix.com.mx","raulmatrixxx@hotmail.com","almacenes@matrix.com.mx","rocencran@matrix.com.mx","direccionestrategica@matrix.com.mx")
                                        );
     $reporte->enviarReporte( $configCorreo);