<?php 
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/Almacenes/Articulos.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/Almacenes/Almacen.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/Reportes/prepareExcel.php";


class AcumuladoVentasFamilias extends PrepareExcel
{
    private $columnaTotal;
    private $columnaTotalMeta;
    private $columnaDiferencia;
    private $modeloAlmacen;
    private $modeloArticulo ;

    public function __construct()
    {
        $this->modeloAlmacen = new Almacen;
        $this->modeloArticulo = new Articulos;
        parent::__construct();
        $this->libro->getProperties()->setTitle('ACUMULADOS POR FAMILIA'); 
    }
    
    public function getVentasAgrupadasMes( $mes , $anio)
    {
        $modeloArticulos = $this->modeloArticulo;
        $listadoVentas = $modeloArticulos->getListadoVentasMes($mes, $anio) ;

        $ventasAgrupadas = [];
        foreach ($listadoVentas as $articulo) {
            if ( !isset($ventasAgrupadas[$articulo->FAMILIA][$articulo->FECHA][$articulo->ALMACEN] ) )  {
                $ventasAgrupadas[$articulo->FAMILIA][$articulo->FECHA][$articulo->ALMACEN]['cant'] = $articulo->CANTIDAD;
            }else{
                $ventasAgrupadas[$articulo->FAMILIA][$articulo->FECHA][$articulo->ALMACEN]['cant'] += $articulo->CANTIDAD;
            }
        }

        return $ventasAgrupadas;
    }

    public function generaReporteAcumulados( $mes, $anio)
    {
        $columnasReservadas = [['B','C'],['D','E'],['F','G'],['H','I'],['J','K'],['L','M'],['N','O'],['P','Q'],['R','S'],['T','U'],['V','W'],['X','Y'],['Z','AA'],['AB','AC']
                                                    ,['AD','AE'],['AF','AG'],['AH','AI'],['AJ','AK'],['AL','AM'],['AN','AO'],['AP','AQ'],['AR','AS'],['AT','AU'],['AV','AW']
                                                    ,['AX','AY'],['AZ','BA'],['BB','BC'],['BD','BE'],['BF','BG'],['BH','BI'],['BJ','BK'] ];
        $columnaTotal = "BM";
        $columnaTotalMeta = "BL";
        $columnaDiferencia = "BN";

        $listadoVentas = self::getVentasAgrupadasMes($mes, $anio);
        
        $modeloArticulo =$this->modeloArticulo;
        $sucursales = $this->modeloAlmacen->getSucursales();
        $cantSucursales = sizeof( $sucursales );
        $i = 10;
        $hoja = 0;
        $rowTotal = $i + $cantSucursales;
        
        $cantDias = cal_days_in_month( CAL_GREGORIAN, $mes, $anio);


        foreach ($listadoVentas as $familia => $fechas) {

            if ( $familia == '' || $familia == 'REFACCION' || $familia == 'SERVICIO' ||  $familia == 'CONTRAPESO' ||  $familia == 'ACCESORIOS' ||  $familia == 'ACCESORIO') {
                continue;
            }
            $familia = $familia == "" ? "FAMILIA" : $familia;
            echo $familia."<br>";
             $this->creaEmptySheet( utf8_encode($familia), $hoja );
             $hoja++;
             $dias = 1;
            //Reiniciando los valores de i 
             $i = 10;
             $this->libro->getActiveSheet()->setCellValue("A8", "SUCURSALES");
             $this->libro->getActiveSheet()->setCellValue("$columnaTotal"."8", "TOTAL");
             $this->libro->getActiveSheet()->setCellValue($columnaTotal."9",'TOTAL REAL');
             if ( $familia == 'RIN' || $familia == 'LLANTA' || $familia == 'COLISION' || $familia == 'ACCESORIO' || $familia == 'OXIFUEL' ) {
                $columnaTotal = $cantDias < 31 ? $columnasReservadas[$cantDias][1] : "BM";
                $columnaTotalMeta = $cantDias < 31 ? $columnasReservadas[$cantDias][0] : "BL";
                $columnaDiferencia = $cantDias < 31 ?  $cantDias == 30 ? 'BM' : $columnasReservadas[$cantDias+1][0] : "BN";               
                
                $this->columnaTotal = $columnaTotal;
                $this->columnaTotalMeta = $columnaTotalMeta;
                $this->columnaDiferencia = $columnaDiferencia;

                $this->libro->getActiveSheet()->setCellValue($columnaTotalMeta."9",'META MES');
                $this->libro->getActiveSheet()->setCellValue($columnaDiferencia."9", "DIFERENCIA");
             }else{
                 $columnaTotal = $columnaTotalMeta = $columnaDiferencia = "BL";
             }
                $this->libro->getActiveSheet()->getColumnDimension($columnaTotalMeta)->setAutoSize(false);
                $this->libro->getActiveSheet()->getColumnDimension($columnaTotalMeta)->setWidth("10");
                $this->libro->getActiveSheet()->getColumnDimension($columnaDiferencia)->setAutoSize(false);
                $this->libro->getActiveSheet()->getColumnDimension($columnaDiferencia)->setWidth("10");
                $this->libro->getActiveSheet()->getColumnDimension($columnaTotal)->setAutoSize(false);
                $this->libro->getActiveSheet()->getColumnDimension($columnaTotal)->setWidth("10");                

             $this->libro->getActiveSheet()->getStyle("A8:".$columnaDiferencia."9")->applyFromArray( $this->labelBold);
             $this->libro->getActiveSheet()->getStyle("A8:".$columnaDiferencia."9")->applyFromArray( $this->centrarTexto );
             $this->libro->getActiveSheet()->getStyle("A8:".$columnaDiferencia."9")->getFill()->applyFromArray( $this->setColorFill("DF013A") );
             $this->libro->getActiveSheet()->getStyle("A8:".$columnaDiferencia."9")->applyFromArray( $this->setColorText("ffffff",12) );

             //Agregando el logo
             $this->putLogo("F1", 300,200);
             $this->libro->getActiveSheet()->mergeCells("E4:M4");
             $this->libro->getActiveSheet()->setCellValue("E4","Reporte de acumulado de ventas de ".$familia);
            $this->libro->getActiveSheet()->getStyle("E4")->applyFromArray( $this->labelBold);   
             $this->libro->getActiveSheet()->getStyle("E4")->applyFromArray( $this->centrarTexto );

             $this->libro->getActiveSheet()->mergeCells("F5:L5");
            $this->libro->getActiveSheet()->setCellValue("F5", $this->getMesAsString($mes)." de ".$anio );
            $this->libro->getActiveSheet()->getStyle("F5")->applyFromArray( $this->labelBold);   
             $this->libro->getActiveSheet()->getStyle("F5")->applyFromArray( $this->centrarTexto );
             foreach ($columnasReservadas as $columna) {
                 $days = array('Sun'=> 'Domingo', 'Mon' =>'Lunes', 'Tue'=>'Martes', 'Wed'=>'Miercoles','Thu' => 'Jueves','Fri'=>'Viernes', 'Sat'=>'Sábado');
                $diaSemana = date('D', strtotime(("$dias-$mes-$anio") ) );
                $diaSemana = $days[$diaSemana];
                 
                if ( $cantDias < $dias) {
                    continue;
                }
                $this->libro->getActiveSheet()->setCellValue($columna[0]."7", $diaSemana);
                $this->libro->getActiveSheet()->mergeCells($columna[0]."7:".$columna[1]."7");
                $this->libro->getActiveSheet()->getStyle($columna[0]."7")->getAlignment()->setTextRotation(90);
                $this->libro->getActiveSheet()->getRowDimension("7")->setRowHeight(60);
                $this->libro->getActiveSheet()->getStyle($columna[0]."7")->applyFromArray( $this->labelBold);   
                 $this->libro->getActiveSheet()->setCellValue($columna[0]."8", $dias);
                 $this->libro->getActiveSheet()->mergeCells($columna[0]."8:".$columna[1]."8");
                 if (  $familia == 'RIN' || $familia == 'LLANTA' || $familia == 'COLISION' || $familia == 'ACCESORIO' || $familia == 'OXIFUEL' ) {
                     $this->libro->getActiveSheet()->setCellValue($columna[0]."9", "META");
                     $this->libro->getActiveSheet()->setCellValue($columna[1]."9", "REAL");
                 }else{
                    $this->libro->getActiveSheet()->setCellValue($columna[0]."9", "REAL");
                    $this->libro->getActiveSheet()->mergeCells($columna[0]."9:".$columna[1]."9");
                 }
                 $this->libro->getActiveSheet()->getStyle($columna[0]."9:".$columna[1]."9")->applyFromArray( $this->labelBold);   
                 $dias++;
                 
             }
             

            foreach ($fechas as $fecha => $almacenes) {
                $fechaSplit = explode("-", $fecha);
                $indiceColumna = $fechaSplit[2] -1;
                $columna = $columnasReservadas[ $indiceColumna ];				
                foreach ($almacenes as $almacen => $cantidad) {
                    $index = $this->buscaSucursal( $almacen );
                    if ( $familia == 'RIN' || $familia == 'LLANTA' || $familia == 'COLISION' || $familia == 'ACCESORIO' || $familia == 'OXIFUEL') {						
                        $this->libro->getActiveSheet()->setCellValue($columna[1].($i+$index),  $cantidad['cant'] );
                    }else{
                        $this->libro->getActiveSheet()->setCellValue($columna[0].($i+$index),  $cantidad['cant'] );
                    }
                    $this->libro->getActiveSheet()->getStyle($columna[1].($i+$index))->applyFromArray( $this->centrarTexto );
                }
                //Haciendo los totales del dia 
                if ( $familia == 'RIN' || $familia == 'LLANTA' || $familia == 'COLISION' || $familia == 'ACCESORIO' || $familia == 'OXIFUEL') {
                    $this->libro->getActiveSheet()->setCellValue($columna[1].$rowTotal, "=SUM(".$columna[1]."10:$columna[1]".($rowTotal-1).")");
                    $this->libro->getActiveSheet()->getStyle($columna[1].$rowTotal)->applyFromArray( $this->labelBold);
                    $this->libro->getActiveSheet()->getStyle($columna[1].$rowTotal)->applyFromArray( $this->centrarTexto );
                }else{
                    $this->libro->getActiveSheet()->setCellValue($columna[0].$rowTotal, "=SUM(".$columna[0]."10:$columna[0]".($rowTotal-1).")");
                    $this->libro->getActiveSheet()->getStyle($columna[0].$rowTotal)->applyFromArray( $this->labelBold);
                    $this->libro->getActiveSheet()->getStyle($columna[0].$rowTotal)->applyFromArray( $this->centrarTexto );       
                }         

            }
            //recorriendo todos los dias que tiene el mes, de manera que se recorre desde la columna con el numero 1 al 28, 30 0 31 de acuerdo a la cantidad de dias del mesif
            if ( $familia == 'RIN' || $familia == 'LLANTA' || $familia == 'COLISION' || $familia == 'ACCESORIO' || $familia == 'OXIFUEL') {
                for ($k=0; $k < $cantDias ; $k++) { 
                    $totalesMeta = $this->libro->getActiveSheet()->getCell( $columnasReservadas[$k][0].$rowTotal )->getValue();
                    $totalesReal = $this->libro->getActiveSheet()->getCell( $columnasReservadas[$k][0].$rowTotal  )->getValue();
                    $columnaDiaMeta = $columnasReservadas[$k][0];
                    $columnaDiaReal = $columnasReservadas[$k][1];
                    if ( $totalesMeta == '' || $totalesReal == '') {
                        
                        $this->libro->getActiveSheet()->setCellValue($columnaDiaMeta.$rowTotal, "=SUM(".$columnaDiaMeta."10:$columnaDiaMeta".($rowTotal-1).")");
                        $this->libro->getActiveSheet()->setCellValue($columnaDiaReal.$rowTotal, "=SUM(".$columnaDiaReal."10:$columnaDiaReal".($rowTotal-1).")");
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
                     if($valor = $familia == 'RIN' || $familia == 'LLANTA' || $familia == 'COLISION' || $familia == 'ACCESORIO' || $familia == 'OXIFUEL'){ 
						$this->libro->getActiveSheet()->getCell($columna[1].($j+$index) )->getValue();
					 }else{
						 $this->libro->getActiveSheet()->getCell($columna[0].($j+$index) )->getValue();
					 }
                     
                     if ( $valor == '' || $valor == NULL) {
                         if ( $familia == 'RIN' || $familia == 'LLANTA' || $familia == 'COLISION' || $familia == 'ACCESORIO' || $familia == 'OXIFUEL') {
                            $this->libro->getActiveSheet()->setCellValue($columna[1].($j+$index),  0 );
                         }else{
                            $this->libro->getActiveSheet()->setCellValue($columna[0].($j+$index),  0 );
                         }
                         
                         $this->libro->getActiveSheet()->getStyle($columna[1].($j+$index))->applyFromArray( $this->centrarTexto );
                     }
                     if ( $familia == 'RIN'  ) {
                        $meta = $modeloArticulo->getMetasAcumulados( $sucursal->ID);
						$diarecorrido = $this->libro->getActiveSheet()->getCell($columna[0].'8')->getValue() * 1;
						$diarecorrido = date('D', strtotime(("$diarecorrido-$mes-$anio") ) );
						if($diarecorrido=='Sun'){
							if($meta[0]['aplicadomingo']==1)
								$this->libro->getActiveSheet()->setCellValue($columna[0].($j+$index),  $meta[0]['meta_diaria_rin'] );
							else
								$this->libro->getActiveSheet()->setCellValue($columna[0].($j+$index),  0 );
						}else{
							$this->libro->getActiveSheet()->setCellValue($columna[0].($j+$index),  $meta[0]['meta_diaria_rin'] );
						}
                        $this->libro->getActiveSheet()->getStyle($columna[0].($j+$index))->applyFromArray( $this->centrarTexto );
                        $this->libro->getActiveSheet()->getStyle($columna[0].($j+$index))->applyFromArray( $this->labelBold);
                       if( isset( $meta[0]) ){
                            if ( $valor > $meta[0]['meta_diaria_rin']) {
                                $this->libro->getActiveSheet()->getStyle($columna[1].($j+$index ) )->applyFromArray( $this->setColorText('00802b',11) );
                            }else{
                                $this->libro->getActiveSheet()->getStyle($columna[1].($j+$index ) )->applyFromArray( $this->setColorText('cc0000',11) );
                            }  
                       }
                     }else if( $familia == 'LLANTA') {
                        $meta = $modeloArticulo->getMetasAcumulados( $sucursal->ID);
                        $diarecorrido = $this->libro->getActiveSheet()->getCell($columna[0].'8')->getValue() * 1;
						$diarecorrido = date('D', strtotime(("$diarecorrido-$mes-$anio") ) );
						if($diarecorrido=='Sun'){
                            if( isset( $meta[0]) ){                            
                                if($meta[0]['aplicadomingo']==1)
                                    $this->libro->getActiveSheet()->setCellValue($columna[0].($j+$index),  $meta[0]['meta_diaria_llanta'] );
                                else
                                    $this->libro->getActiveSheet()->setCellValue($columna[0].($j+$index),  0 );
                            }
						}else{
							$this->libro->getActiveSheet()->setCellValue($columna[0].($j+$index),  $meta[0]['meta_diaria_llanta'] );
						}
                        $this->libro->getActiveSheet()->getStyle($columna[0].($j+$index))->applyFromArray( $this->centrarTexto );
                        $this->libro->getActiveSheet()->getStyle($columna[0].($j+$index))->applyFromArray( $this->labelBold);
                        if( isset( $meta[0]) ){
                            if ( $valor > $meta[0]['meta_diaria_llanta']) {
                                $this->libro->getActiveSheet()->getStyle($columna[1].($j+$index ) )->applyFromArray( $this->setColorText('00802b',11) );
                            }else{
                                $this->libro->getActiveSheet()->getStyle($columna[1].($j+$index ) )->applyFromArray( $this->setColorText('cc0000',11) );
                            }   
                        }                       
                     }else if( $familia == 'COLISION') {
						$meta = $modeloArticulo->getMetasAcumulados( $sucursal->ID);
                        $diarecorrido = $this->libro->getActiveSheet()->getCell($columna[0].'8')->getValue() * 1;
						$diarecorrido = date('D', strtotime(("$diarecorrido-$mes-$anio") ) );
						if($diarecorrido=='Sun'){
                            if( isset( $meta[0]) ){
                                if($meta[0]['aplicadomingo']==1)
                                    $this->libro->getActiveSheet()->setCellValue($columna[0].($j+$index),  $meta[0]['meta_diaria_colision'] );
                                else
                                    $this->libro->getActiveSheet()->setCellValue($columna[0].($j+$index),  0 );
                            }
						}else{
							$this->libro->getActiveSheet()->setCellValue($columna[0].($j+$index),  $meta[0]['meta_diaria_colision'] );
						}
                        $this->libro->getActiveSheet()->getStyle($columna[0].($j+$index))->applyFromArray( $this->centrarTexto );
                        $this->libro->getActiveSheet()->getStyle($columna[0].($j+$index))->applyFromArray( $this->labelBold);
                        if( isset( $meta[0]) ){
                            if ( $valor > $meta[0]['meta_diaria_colision']) {
                                $this->libro->getActiveSheet()->getStyle($columna[1].($j+$index ) )->applyFromArray( $this->setColorText('00802b',11) );
                            }else{
                                $this->libro->getActiveSheet()->getStyle($columna[1].($j+$index ) )->applyFromArray( $this->setColorText('cc0000',11) );
                            }
                        }
                        
                     }else if( $familia == 'ACCESORIO') {
						$meta = $modeloArticulo->getMetasAcumulados( $sucursal->ID);
                        $diarecorrido = $this->libro->getActiveSheet()->getCell($columna[0].'8')->getValue() * 1;
						$diarecorrido = date('D', strtotime(("$diarecorrido-$mes-$anio") ) );
						if($diarecorrido=='Sun'){
                            if( isset( $meta[0]) ){
                                if($meta[0]['aplicadomingo']==1)
                                    $this->libro->getActiveSheet()->setCellValue($columna[0].($j+$index),  $meta[0]['meta_diaria_accesorio'] );
                                else
                                    $this->libro->getActiveSheet()->setCellValue($columna[0].($j+$index),  0 );
                            }
						}else{
							$this->libro->getActiveSheet()->setCellValue($columna[0].($j+$index),  $meta[0]['meta_diaria_accesorio'] );
						}
                        $this->libro->getActiveSheet()->getStyle($columna[0].($j+$index))->applyFromArray( $this->centrarTexto );
                        $this->libro->getActiveSheet()->getStyle($columna[0].($j+$index))->applyFromArray( $this->labelBold);
                        if( isset( $meta[0]) ){
                            if ( $valor > $meta[0]['meta_diaria_accesorio']) {
                                $this->libro->getActiveSheet()->getStyle($columna[1].($j+$index ) )->applyFromArray( $this->setColorText('00802b',11) );
                            }else{
                                $this->libro->getActiveSheet()->getStyle($columna[1].($j+$index ) )->applyFromArray( $this->setColorText('cc0000',11) );
                            }
                        }
                        
                     }else if( $familia == 'OXIFUEL') {
						$meta = $modeloArticulo->getMetasAcumulados( $sucursal->ID);
                        $diarecorrido = $this->libro->getActiveSheet()->getCell($columna[0].'8')->getValue() * 1;
						$diarecorrido = date('D', strtotime(("$diarecorrido-$mes-$anio") ) );
						if($diarecorrido=='Sun'){
                            if( isset( $meta[0]) ){
                                if($meta[0]['aplicadomingo']==1)
                                    $this->libro->getActiveSheet()->setCellValue($columna[0].($j+$index),  $meta[0]['meta_diaria_oxifuel'] );
                                else
                                    $this->libro->getActiveSheet()->setCellValue($columna[0].($j+$index),  0 );
                            }

						}else{
							$this->libro->getActiveSheet()->setCellValue($columna[0].($j+$index),  $meta[0]['meta_diaria_oxifuel'] );
						}
                        $this->libro->getActiveSheet()->getStyle($columna[0].($j+$index))->applyFromArray( $this->centrarTexto );
                        $this->libro->getActiveSheet()->getStyle($columna[0].($j+$index))->applyFromArray( $this->labelBold);
                        if ( $valor > $meta[0]['meta_diaria_oxifuel']) {
                            $this->libro->getActiveSheet()->getStyle($columna[1].($j+$index ) )->applyFromArray( $this->setColorText('00802b',11) );
                        }else{
                            $this->libro->getActiveSheet()->getStyle($columna[1].($j+$index ) )->applyFromArray( $this->setColorText('cc0000',11) );
                        }
                        
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

                if ( $familia == 'RIN' || $familia == 'LLANTA' || $familia == 'COLISION' || $familia == 'ACCESORIO' || $familia == 'OXIFUEL') {
                    $conentSuma = "";
                    $conentSumaMeta = '';
                    for ($k=0; $k < $cantDias ; $k++) { 
                        $conentSuma .= $columnasReservadas[$k][1].($i+$index)."+";
                        $conentSumaMeta .= $columnasReservadas[$k][0].($i+$index)."+";
                    }
                    $conentSuma = substr($conentSuma,0,-1);
                    $conentSumaMeta = substr($conentSumaMeta,0,-1);
                    $this->libro->getActiveSheet()->setCellValue($columnaTotal.($i+$index), "=$conentSuma"); 
					$metaM = $modeloArticulo->getMetasAcumulados( $sucursal->ID);
					if($familia=='RIN'){
						$this->libro->getActiveSheet()->setCellValue($columnaTotalMeta.($i+$index), $metaM[0]['meta_mensual_rin']);
					}else if($familia=='LLANTA'){
						$this->libro->getActiveSheet()->setCellValue($columnaTotalMeta.($i+$index), $metaM[0]['meta_mensual_llanta']);
					}else if($familia=='COLISION'){
						$this->libro->getActiveSheet()->setCellValue($columnaTotalMeta.($i+$index), $metaM[0]['meta_mensual_colision']);
					}else if($familia=='ACCESORIO'){
						$this->libro->getActiveSheet()->setCellValue($columnaTotalMeta.($i+$index), $metaM[0]['meta_mensual_accesorio']);
					}else{
						$this->libro->getActiveSheet()->setCellValue($columnaTotalMeta.($i+$index), $metaM[0]['meta_mensual_oxifuel']);
					}
                    //$this->libro->getActiveSheet()->setCellValue($columnaTotal.($i+$index), "=$conentSumaMeta");    
                    $this->libro->getActiveSheet()->setCellValue($columnaDiferencia.($i+$index), "=".$columnaTotal.($i+$index)."-".$columnaTotalMeta.($i+$index));    
                   $diferencia =  $this->libro->getActiveSheet()->getCell($columnaDiferencia.($i+$index) )->getCalculatedValue() ;
                   echo $diferencia ."  ---- $familia-----<br>";
                    if ( $diferencia < 0) {
                        $this->libro->getActiveSheet()->getStyle($columnaDiferencia.($i+$index ) )->applyFromArray( $this->setColorText('cc0000',11) );
                    }else if( $diferencia > 0){
                        $this->libro->getActiveSheet()->getStyle($columnaDiferencia.($i+$index ) )->applyFromArray( $this->setColorText('00802b',11) );
                    }
                }

             }

             if ( $familia == 'RIN' || $familia == 'LLANTA' || $familia == 'COLISION' || $familia == 'ACCESORIO' || $familia == 'OXIFUEL' ) {
                 $this->libro->getActiveSheet()->setCellValue($columnaDiferencia.(10+$cantSucursales), "=SUM(".$columnaDiferencia."10:$columnaDiferencia".(10+$cantSucursales).')' );    
                 $this->anexarGraficas( $familia , $sucursales);
             }
            $this->libro->getActiveSheet()->setCellValue($columnaTotal.($i+sizeof( $sucursales )), "=SUM(".$columnaTotal.($i).":$columnaTotal".($i+ sizeof( $sucursales) ).")" );
            $this->libro->getActiveSheet()->getStyle($columnaTotal.($i+ sizeof( $sucursales )))->applyFromArray( $this->labelBold);
			
			
			$this->libro->getActiveSheet()->setCellValue($columnaTotalMeta.($i+sizeof( $sucursales )), "=SUM(".$columnaTotalMeta.($i).":$columnaTotalMeta".($i+ sizeof( $sucursales) ).")" );
            $this->libro->getActiveSheet()->getStyle($columnaTotalMeta.($i+ sizeof( $sucursales )))->applyFromArray( $this->labelBold);            

             $this->libro->getActiveSheet()->getColumnDimension("A" )->setAutoSize(true);
             $this->libro->getActiveSheet()->getStyle("A8:".$columnaDiferencia.($rowTotal-1) )->applyFromArray( $this->bordes );
            
        }
        
         $reporteTerminado = new \PHPExcel_Writer_Excel2007( $this->libro);
        $reporteTerminado->setPreCalculateFormulas(true);
        $reporteTerminado->setIncludeCharts(TRUE);
         $reporteTerminado->save($_SERVER['DOCUMENT_ROOT']."/intranet/controladores/reportes/acumulado_ventas.xlsx");
        $ubicacion = "http://servermatrixxxb.ddns.net:8181/intranet/controladores/reportes/acumulado_ventas.xlsx";
        echo "<a href='$ubicacion'>Descargar</a>";
    }

    public function anexarGraficas($familia, $sucursales)
    {
        $dimensioneGraficas = [['A','E'],['F','J'],['K','O'],['P','T'],['U','Y'],['Z','AD'] ];
        $cantSucursales = sizeof( $sucursales );
        $columnaTotal = $this->columnaTotal;
        $columnaTotalMeta = $this->columnaTotalMeta;

        $cantDias =  cal_days_in_month(CAL_GREGORIAN, date('m')  ,date('Y')) -1 ; // se le resta uno para poder acceder al ulimo 

        $i=0;
        $inicioGrafica = 23;
        $finGrafica = 33;
        foreach ($sucursales as $idx => $sucursal) {
            $etiquetas = array( new \PHPExcel_Chart_DataSeriesValues('String', $familia.'!$'.$columnaTotalMeta.'$9', NULL, 1),
                        new \PHPExcel_Chart_DataSeriesValues('String', $familia.'!$'.$columnaTotal.'$9', NULL, 1)
                    );              

            $xAxis = array( new \PHPExcel_Chart_DataSeriesValues('String', $familia.'!$'.$columnaTotalMeta.'$9', NULL, 1 ),
                new \PHPExcel_Chart_DataSeriesValues('String', $familia.'!$'.$columnaTotal.'$9', NULL, 1 ) );         
            
            $valores = array( new \PHPExcel_Chart_DataSeriesValues('Number', $familia.'!$'.$columnaTotalMeta.'$'.(10+$idx), NULL, 1 ),
            new \PHPExcel_Chart_DataSeriesValues('Number', $familia.'!$'.$columnaTotal.'$'.(10+$idx), NULL, 1 ));     

                        $dataSeriesChart =new \PHPExcel_Chart_DataSeries(
                    \PHPExcel_Chart_DataSeries::TYPE_BARCHART,
                    \PHPExcel_Chart_DataSeries::GROUPING_STANDARD,
                    range(0, count($valores)-1),
                    $etiquetas,
                    $xAxis,
                    $valores
                    );

                    // $dataSeriesChart->setPlotDirection(\PHPExcel_Chart_DataSeries::TYPE_BARCHART );

                    $plotArea = new \PHPExcel_Chart_PlotArea(null, array($dataSeriesChart));
                    $legend=new \PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);
                    $title = new \PHPExcel_Chart_Title($sucursal->DESCRIPCION);
                    $grafica= new \PHPExcel_Chart(
                                'ACUMULADO ',
                                $title,
                                $legend,
                                $plotArea,
                                true,
                                0,
                                NULL, 
                                NULL);
                    if( $i > 3) {
                        $inicioGrafica = $finGrafica+4;
                        $finGrafica = $finGrafica+14;
                        $i = 0;
                    }else{
                        $i++;
                    }
                    
                    $grafica->setTopLeftPosition($dimensioneGraficas[$i][0]."".$inicioGrafica);
                    $grafica->setBottomRightPosition($dimensioneGraficas[$i][1]."".$finGrafica);
                    $this->libro->getActiveSheet()->addChart( $grafica);

        }                            

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
$reporte->generaReporteAcumulados(  date("m") , date("Y"));
    $configCorreo = array("descripcionDestinatario" => "Acumulado de Ventas por Familia",
                                        "mensaje" => "SITEX",
                                        "pathFile" => "acumulado_ventas.xlsx",
                                        "subject" => "Acumulado de Ventas por Familia",
                                        //"correos" => array( "sestrada@matrix.com.mx")
                                        "correos" => array( "sestrada@matrix.com.mx","gerenteti@matrix.com.mx","gerente_auditoria@matrix.com.mx","director@matrix.com.mx","dispersion@matrix.com.mx","gerenteventas@matrix.com.mx","gerenteventasnorte@matrix.com.mx","raulmatrixxx@hotmail.com",'compras@matrix.com.mx')
                                        );
$reporte->enviarReporte( $configCorreo);