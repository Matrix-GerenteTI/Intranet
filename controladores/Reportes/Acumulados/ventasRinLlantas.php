<?php

require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/Ventas.php";
// require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/Almacenes/Articulos.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/Almacenes/Almacen.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/Reportes/prepareExcel.php";
require_once $_SERVER['DOCUMENT_ROOT']."/eshop/modelos/Articulos.php";

class VentasRinLlantas extends prepareExcel
{
    private  $columnasReservadas =['', 'C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL'];
    private $columnaTotal = "AH";
    private $columnaStock = "AI";
    private $columnaAltos = "AJ";
    private $columnaCentro = "AK";
    private $columnaCosta = "AL";
    private $articulosContabilizados = [];

    public function getConcentradoVentas( $fecha, $familia, $almacenesFiltro = [])
    {
        $modeloVentas = new VentasExtended;
        $listadoVentas  = [];
        if ( sizeof( $almacenesFiltro) ) {
            $listadoVentas = $modeloVentas->getMensualFamiliaAltos( $fecha, $familia ); 
            echo "Mensual Familia Altos";
            echo '<pre>'; print_r($listadoVentas); echo '</pre>';
        }else{
            $listadoVentas = $modeloVentas->getMensualFamilia( $fecha, $familia );
            echo "Mensual Familia";
            echo '<pre>'; print_r($listadoVentas); echo '</pre>';
        }
        
        $concentradoVentas = [];
        // Estructura del arreglo
            // RIN: $array['$medida']['subfamilia']...
            // LLANTa: $array['subfamilia']['barrenacion']
        foreach ($listadoVentas as $i => $venta) {
            
            $descripcionSeparada =explode(' ', $venta->DESCRIPCION );

            if ($familia == 'RIN') {
                $descripcionRin = $descripcionSeparada[1];
                
                if ( strpos($descripcionSeparada[1], '-') !== false  ) {
                    if ( $descripcionRin != $venta->PVP4) {
                        $descripcionRin = $descripcionRin;
                    }
                } else if ( strpos($descripcionRin, '/') !== false ) {
                    if ( $descripcionRin != $venta->PVP4) {
                        $descripcionRin = $descripcionRin;
                    }                        
                }else{
                    $descripcionRin  = $venta->PVP4;
                }
                $cantStock = $this->cuentaStock( $descripcionRin , $familia, $venta->SUBFAMILIA, $almacenesFiltro);

                if ( isset(  $concentradoVentas[$venta->SUBFAMILIA][trim( $descripcionRin ) ]['indexDia'][$venta->DIA]['cantidad']  ) ) {
                    $concentradoVentas[$venta->SUBFAMILIA][trim( $descripcionRin ) ]['indexDia'][$venta->DIA]['cantidad'] +=  $venta->CANTIDAD;
                } else {        
                    if( ! isset($concentradoVentas[$venta->SUBFAMILIA][trim( $descripcionRin ) ]['stock'] ) ){
                        $concentradoVentas[$venta->SUBFAMILIA][trim( $descripcionRin ) ]['stock'] = 0;
                    }      

                    $concentradoVentas[$venta->SUBFAMILIA][trim( $descripcionRin ) ]['indexDia'][$venta->DIA]['cantidad'] =  $venta->CANTIDAD;
              
                }        
                if ( $cantStock  != -1 && isset($concentradoVentas[$venta->SUBFAMILIA][trim( $descripcionRin ) ]['stock'] ) ) {
                    $concentradoVentas[$venta->SUBFAMILIA][trim( $descripcionRin ) ]['stock'] = $cantStock;
                }
                switch ($venta->ZONA) {
                    case 'CENTRO':
                        $concentradoVentas[$venta->SUBFAMILIA][trim( $descripcionRin ) ]['centro'] += $venta->CANTIDAD;
                        break;
                    case 'ALTOS':
                        $concentradoVentas[$venta->SUBFAMILIA][trim( $descripcionRin ) ]['altos'] += $venta->CANTIDAD;
                        break;
                    default:
                        $concentradoVentas[$venta->SUBFAMILIA][trim( $descripcionRin ) ]['costa'] += $venta->CANTIDAD;
                        break;
                }
                // echo "$venta->SUBFAMILIA  $descripcionRin ".$concentradoVentas[$venta->SUBFAMILIA][trim( $descripcionRin ) ]['stock']."<br>";
            } else if( $familia == 'LLANTA' ) {
                

                $medida = $descripcionSeparada[0];
                $cantStock = $this->cuentaStock($medida, $familia,$venta->SUBFAMILIA, $almacenesFiltro);

                if ( isset(  $concentradoVentas[$medida][$venta->SUBFAMILIA]['indexDia'][$venta->DIA]['cantidad']  ) ) {
                    $concentradoVentas[$medida][$venta->SUBFAMILIA]['indexDia'][$venta->DIA]['cantidad'] +=  $venta->CANTIDAD;
                } else {
                    if (!isset( $concentradoVentas[$medida][$venta->SUBFAMILIA]['stock'] )) {

                        $concentradoVentas[$medida][$venta->SUBFAMILIA]['stock'] = 0;
                    }                          
                    $concentradoVentas[$medida][$venta->SUBFAMILIA]['indexDia'][$venta->DIA]['cantidad'] =  $venta->CANTIDAD;

              
                }        
                if ( $cantStock  != -1 && isset( $concentradoVentas[$medida][$venta->SUBFAMILIA]['stock'] ) ) {
                    
                    $concentradoVentas[$medida][$venta->SUBFAMILIA]['stock'] = $cantStock;
                }
                switch ($venta->ZONA) {
                    case 'CENTRO':
                        $concentradoVentas[$medida][$venta->SUBFAMILIA]['centro'] += $venta->CANTIDAD;
                        break;
                    case 'ALTOS':
                        $concentradoVentas[$medida][$venta->SUBFAMILIA]['altos'] += $venta->CANTIDAD;
                        break;
                    default:
                        $concentradoVentas[$medida][$venta->SUBFAMILIA]['costa'] += $venta->CANTIDAD;
                        break;
                }      


            }elseif( $familia == 'COLISION'){
                if ( $venta->MARCA == 'RADEC' ) {
                    $cantStock = $this->cuentaStock($venta->CODIGOARTICULO, $familia,$venta->SUBFAMILIA, $almacenesFiltro);

                    if ( isset(  $concentradoVentas[$venta->CODIGOARTICULO][$venta->DESCRIPCION]['indexDia'][$venta->DIA]['cantidad']  ) ) {
                        $concentradoVentas[$venta->CODIGOARTICULO][$venta->DESCRIPCION]['indexDia'][$venta->DIA]['cantidad'] +=  $venta->CANTIDAD;
                    } else {
                        if (!isset( $concentradoVentas[$venta->CODIGOARTICULO][$venta->DESCRIPCION]['stock'] )) {

                            $concentradoVentas[$venta->CODIGOARTICULO][$venta->DESCRIPCION]['stock'] = 0;
                        }                          
                        $concentradoVentas[$venta->CODIGOARTICULO][$venta->DESCRIPCION]['indexDia'][$venta->DIA]['cantidad'] =  $venta->CANTIDAD;

                
                    }        
                    if ( $cantStock  != -1 && isset( $concentradoVentas[$venta->CODIGOARTICULO][$venta->DESCRIPCION]['stock'] ) ) {
                        
                        $concentradoVentas[$venta->CODIGOARTICULO][$venta->DESCRIPCION]['stock'] = $cantStock;
                    }
                    switch ($venta->ZONA) {
                        case 'CENTRO':
                            $concentradoVentas[$venta->CODIGOARTICULO][$venta->DESCRIPCION]['centro'] += $venta->CANTIDAD;
                            break;
                        case 'ALTOS':
                            $concentradoVentas[$venta->CODIGOARTICULO][$venta->DESCRIPCION]['altos'] += $venta->CANTIDAD;
                            break;
                        default:
                            $concentradoVentas[$venta->CODIGOARTICULO][$venta->DESCRIPCION]['costa'] += $venta->CANTIDAD;
                            break;
                    }             
                }

                
            }
            
        }
        echo "Concentrado de ventas";
        echo '<pre>'; print_r($concentradoVentas); echo '</pre>';
        return $concentradoVentas;
    }

    public function cuentaStock(  $palabraClave , $familia, $subfamilia, $zona = [])
    {
        $modeloArticulos = new Articulos;
        $stockEnAlmacenes =  $modeloArticulos->getStockArticuloDescripion( $palabraClave , $familia ,$subfamilia );
        $stock = 0;
        $isZona = sizeof( $zona );

        
        if (!in_array($palabraClave, $this->articulosContabilizados )  ) {
            foreach ($stockEnAlmacenes as  $almacen) {
                if ( $isZona > 0) {

                    if ( $almacen->ZONA == "ALTOS") {
                        $stock += $almacen->STOCK;
                        
                    }
                }else{
                    $stock += $almacen->STOCK;
                }
                
            }
            if ( $familia == 'RIN') {
                $stock /= 4;
            }
            
            array_push( $this->articulosContabilizados, $subfamilia."_". $palabraClave );
            return $stock;
        } else {
            return -1;
        }
        
    }

    public function estableceColumnasFinales($mes, $anio)
    {
        $cantidadDias = cal_days_in_month(CAL_GREGORIAN, $mes, $anio);
        if ( $cantidadDias == 28 ) {
            $this->columnaTotal = "AE";
            $this->columnaStock = "AF";
            $this->columnaAltos = "AG";
            $this->columnaCentro = "AH";
            $this->columnaCosta = "AI";
        } else if( $cantidadDias == 29) {
            $this->columnaTotal = "AF";
            $this->columnaStock = "AG";
            $this->columnaAltos = "AH";
            $this->columnaCentro = "AI";
            $this->columnaCosta = "AJ";
        }else if( $cantidadDias == 30){
            $this->columnaTotal = "AG";
            $this->columnaStock = "AH";
            $this->columnaAltos = "AI";
            $this->columnaCentro = "AJ";
            $this->columnaCosta = "AK";
        }else{
            $this->columnaTotal = "AH";
            $this->columnaStock = "AI";
            $this->columnaAltos = "AJ";
            $this->columnaCentro = "AK";
            $this->columnaCosta = "AL";
        }
        
    }

    public function generarReporte($fecha, $familias, $zona = [] )
    {
        $hoja = 0;
        
        extract( $fecha );
        $cantidadDias = 31; //cal_days_in_month(CAL_GREGORIAN, $mes, $anio);
        $this->estableceColumnasFinales( $mes, $anio );

        foreach ($familias as  $familia) {
            $ventasGeneradas = self::getConcentradoVentas( $fecha, $familia , $zona);

            $this->creaEmptySheet( utf8_encode($familia), $hoja );
            $i = 9;

             $this->putLogo("F1", 150,200);
             $this->libro->getActiveSheet()->mergeCells("C4:S4");
             $this->libro->getActiveSheet()->setCellValue("C4","Reporte de acumulado de ventas de ".$familia);
            $this->libro->getActiveSheet()->getStyle("C4")->applyFromArray( $this->labelBold);   
             $this->libro->getActiveSheet()->getStyle("C4")->applyFromArray( $this->centrarTexto );

             $this->libro->getActiveSheet()->mergeCells("F5:S5");
            $this->libro->getActiveSheet()->setCellValue("F5", $this->getMesAsString($mes)." de ".$anio );
            $this->libro->getActiveSheet()->getStyle("F5")->applyFromArray( $this->labelBold);   
             $this->libro->getActiveSheet()->getStyle("F5")->applyFromArray( $this->centrarTexto );


            if ( $familia == 'LLANTA' ) {
                $this->libro->getActiveSheet()->setCellValue("A8", "MEDIDA");
                $this->libro->getActiveSheet()->setCellValue("B8", "RIN");
            } elseif( $familia == 'RIN') {
                $this->libro->getActiveSheet()->setCellValue("B8", "BARRENACIÓN");
                $this->libro->getActiveSheet()->setCellValue("A8", "RIN");                
            }else{
                $this->libro->getActiveSheet()->setCellValue("B8", "DESCRIPCION");
                $this->libro->getActiveSheet()->setCellValue("A8", "CODIGO");                      
            }

            $this->libro->getActiveSheet()->getStyle("A8:".$this->columnaCosta."8" )->applyFromArray( $this->labelBold);   
             $this->libro->getActiveSheet()->getStyle("A8:".$this->columnaCosta."8")->applyFromArray( $this->centrarTexto );
             $this->libro->getActiveSheet()->getStyle("A8:".$this->columnaCosta."8")->getFill()->applyFromArray( $this->setColorFill("DF013A") );
             $this->libro->getActiveSheet()->getStyle("A8:".$this->columnaCosta."8")->applyFromArray( $this->setColorText("ffffff",12) );             

            foreach ($ventasGeneradas as $primerSegmento => $contentSeguntoSegmento) {

                foreach ($contentSeguntoSegmento as $segundoSegmento => $dias) {
                    foreach ($dias['indexDia'] as $noDia => $dia) {                        
                        $this->libro->getActiveSheet()->setCellValue("A$i", $primerSegmento);
                        $this->libro->getActiveSheet()->setCellValue("B$i", utf8_encode( $segundoSegmento) );
                        $this->libro->getActiveSheet()->setCellValue($this->columnasReservadas[$noDia].$i, $dia['cantidad']);
                        $this->libro->getActiveSheet()->setCellValue($this->columnaStock.$i, $dias['stock']);
                        // $this->libro->getActiveSheet()->getStyle($this->columnasReservadas[$noDia].$i)->applyFromArray( $this->centrarTexto );
                    }
                    $this->libro->getActiveSheet()->setCellValue($this->columnaAltos.$i,$dias['altos']);
                    $this->libro->getActiveSheet()->setCellValue($this->columnaCentro.$i,$dias['centro']);
                    $this->libro->getActiveSheet()->setCellValue($this->columnaCosta.$i,$dias['costa']);
                    $i++;
                }
                
                $this->libro->getActiveSheet()->getStyle("A8:A$i" )->applyFromArray( $this->labelBold); 
                $this->libro->getActiveSheet()->getStyle("A8:A$i" )->applyFromArray( $this->centrarTexto); 
            }            

            // Colocando encabezados y relllando con 0 los espacions vacios
            for ($k=1; $k <= $cantidadDias ; $k++) { 
                $this->libro->getActiveSheet()->setCellValue($this->columnasReservadas[$k]."8", $k);
                // Colocando el dia como encabezado
                $days = array('Sun'=> 'Domingo', 'Mon' =>'Lunes', 'Tue'=>'Martes', 'Wed'=>'Miercoles','Thu' => 'Jueves','Fri'=>'Viernes', 'Sat'=>'Sábado');
                $diaSemana = date('D', strtotime(("$k-$mes-$anio") ) );
                $diaSemana = $days[$diaSemana];

                $this->libro->getActiveSheet()->setCellValue($this->columnasReservadas[$k]."7", $diaSemana);
                $this->libro->getActiveSheet()->getStyle($this->columnasReservadas[$k]."7")->getAlignment()->setTextRotation(90);
                $this->libro->getActiveSheet()->getRowDimension("7")->setRowHeight(60);
                $this->libro->getActiveSheet()->getStyle($this->columnasReservadas[$k]."7")->applyFromArray( $this->labelBold);                   

                for ($j=9; $j < $i; $j++) { 
                    $cantidad = $this->libro->getActiveSheet()->getCell($this->columnasReservadas[$k].$j)->getCalculatedValue();
                    if ( $cantidad == NULL|| $cantidad == '') {
                        $this->libro->getActiveSheet()->setCellValue($this->columnasReservadas[$k]."$j", 0);
                        // $this->libro->getActiveSheet()->getStyle($this->columnasReservadas[$k]."$j")->applyFromArray( $this->centrarTexto );
                    }
                    
                }

                $this->libro->getActiveSheet()->setCellValue($this->columnaTotal."8", "TOTAL");
                $this->libro->getActiveSheet()->setCellValue($this->columnaStock."8", "STOCK"); 
                $this->libro->getActiveSheet()->setCellValue($this->columnaAltos."8","ALTOS");
                $this->libro->getActiveSheet()->setCellValue($this->columnaCentro."8","CENTRO");
                $this->libro->getActiveSheet()->setCellValue($this->columnaCosta."8","COSTA");
                $this->libro->getActiveSheet()->getColumnDimension( $this->columnasReservadas[$k] )->setAutoSize(true);
                // $this->libro->getActiveSheet()->getColumnDimension( $this->columnasReservadas[$k] )->setWidth("10");
            }            

            for ($j=9; $j <$i ; $j++) { 
                // Asignando los totales 
                $this->libro->getActiveSheet()->setCellValue($this->columnaTotal.$j, "=SUM(".$this->columnasReservadas[1]."$j:".$this->columnasReservadas[$cantidadDias] ."$j) " );
                $this->libro->getActiveSheet()->getStyle($this->columnaTotal."$j" )->applyFromArray( $this->labelBold); 
                $this->libro->getActiveSheet()->getStyle($this->columnaStock."$j" )->applyFromArray( $this->labelBold); 
                $this->libro->getActiveSheet()->getStyle($this->columnaAltos."$j" )->applyFromArray( $this->labelBold); 
                $this->libro->getActiveSheet()->getStyle($this->columnaCentro."$j" )->applyFromArray( $this->labelBold); 
                $this->libro->getActiveSheet()->getStyle($this->columnaCosta."$j" )->applyFromArray( $this->labelBold); 
                $this->libro->getActiveSheet()->getStyle($this->columnaTotal."$j" )->applyFromArray( $this->centrarTexto);                 
                $this->libro->getActiveSheet()->getStyle($this->columnaStock."$j" )->applyFromArray( $this->centrarTexto); 
                $this->libro->getActiveSheet()->getStyle($this->columnaAltos."$j" )->applyFromArray( $this->centrarTexto); 
                $this->libro->getActiveSheet()->getStyle($this->columnaCentro."$j" )->applyFromArray( $this->centrarTexto); 
                $this->libro->getActiveSheet()->getStyle($this->columnaCosta."$j" )->applyFromArray( $this->centrarTexto); 
            }
            $this->libro->getActiveSheet()->getColumnDimension( $this->columnaTotal )->setAutoSize(false);
            $this->libro->getActiveSheet()->getColumnDimension( $this->columnaTotal )->setWidth("10");       
            $this->libro->getActiveSheet()->getColumnDimension( $this->columnaStock )->setAutoSize(false);
            $this->libro->getActiveSheet()->getColumnDimension( $this->columnaStock )->setWidth("10");    
            $this->libro->getActiveSheet()->getColumnDimension( $this->columnaAltos )->setAutoSize(false);
            $this->libro->getActiveSheet()->getColumnDimension( $this->columnaAltos )->setWidth("10");          
            $this->libro->getActiveSheet()->getColumnDimension( $this->columnaCentro )->setAutoSize(false);
            $this->libro->getActiveSheet()->getColumnDimension( $this->columnaCentro )->setWidth("10");          
            $this->libro->getActiveSheet()->getColumnDimension( $this->columnaCosta )->setAutoSize(false);
            $this->libro->getActiveSheet()->getColumnDimension( $this->columnaCosta )->setWidth("10");          

            $this->libro->getActiveSheet()->getStyle("A8:".$this->columnaCosta.($i-1)  )->applyFromArray( $this->bordes );

            $this->libro->getActiveSheet()->getColumnDimension( "A" )->setAutoSize(false);
            $this->libro->getActiveSheet()->getColumnDimension( "A" )->setWidth("20");   

            if ( $familia == 'COLISION') {
                $this->libro->getActiveSheet()->getColumnDimension( "B" )->setAutoSize(false);
                $this->libro->getActiveSheet()->getColumnDimension( "B" )->setWidth("50");   
            } else {
                $this->libro->getActiveSheet()->getColumnDimension( "B" )->setAutoSize(false);
                $this->libro->getActiveSheet()->getColumnDimension( "B" )->setWidth("15");        
            }
                             
            $hoja++;
        }

         $reporteTerminado = new \PHPExcel_Writer_Excel2007( $this->libro);
        $reporteTerminado->setPreCalculateFormulas(true);
        $reporteTerminado->setIncludeCharts(TRUE);

        $nombre = "";
        if ( sizeof($zona) ) {
            $nombre = "ventasRinLlantasAltos";
        } else {
            $nombre = "ventasRinLlantas";
        }
        

         $reporteTerminado->save($_SERVER['DOCUMENT_ROOT']."/intranet/controladores/reportes/Acumulados/$nombre.xlsx");
        $ubicacion = "http://matrix.com.mx/intranet/controladores/reportes/Acumulados/$nombre.xlsx";
        // echo "<a href='$ubicacion'>Descargar</a>";
    }

}

$reporte = new VentasRinLlantas;
$mes = date('m');
 $reporte->generarReporte( ['mes' => $mes , 'anio' => date('Y')],['LLANTA' ,'RIN','COLISION']);

     $configCorreo = array("descripcionDestinatario" => "Acumulado de Ventas RINES , LLANTAS,COLISION",
                                        "mensaje" => "SITEX",
                                        "pathFile" => "ventasRinLlantas.xlsx",
                                        "subject" => "Acumulado de Ventas por Familia",
                                        "correos" => array( "gerenteti@matrix.com.mx","director@matrix.com.mx","sestrada@matrix.com.mx","raulmatrixxx@hotmail.com",'gerenteventas@matrix.com.mx','compras@matrix.com.mx','gerenteventasnorte@matrix.com.mx','gerente_auditoria@matrix.com.mx')
                                        //"correos" => array( "sestrada@matrix.com.mx")
                                        );
       $reporte->enviarReporte( $configCorreo);


    /*
$reporte->generarReporte( ['mes' => date('m'), 'anio' => date('Y')],['LLANTA' ,'RIN','COLISION'] ,['ALTOS'] );
          $configCorreo = array("descripcionDestinatario" => "Acumulado de Ventas RINES y LLANTAS ZONA ALTOS",
                                        "mensaje" => "SITEX",
                                        "pathFile" => "ventasRinLlantas.xlsx",
                                        "subject" => "Acumulado de Ventas por Familia",
                                        "correos" => array( "sestrada@matrix.com.mx","raulmatrixxx@hotmail.com",'encargadosanramonmatrix@outlook.com','compras@matrix.com.mx','gerenteventas@matrix.com.mx','gerenteventasnorte@matrix.com.mx')
                                        //"correos" => array( "sestrada@matrix.com.mx")
                                        );
       $reporte->enviarReporte( $configCorreo);
       */