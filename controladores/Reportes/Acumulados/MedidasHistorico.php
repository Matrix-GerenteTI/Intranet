<?php

require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/Ventas.php";
// require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/Almacenes/Articulos.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/Almacenes/Almacen.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/Reportes/prepareExcel.php";
require_once $_SERVER['DOCUMENT_ROOT']."/eshop/modelos/Articulos.php";


class HistoricoVentasLlantasMedidas extends prepareExcel
{
    protected $columnasReservadas = [['C','D'],['E','F'],['G','H'],['I','J'],['K','L'],['M','N'],['O','P'],['Q','R'],['S','T'],['U','V'],['W','X'],['Y','Z'],['AA','AB']];
    protected $columnasTotales = ['AC','AD'];


    public function __construct()
    {
        parent::__construct();
        $this->libro->getProperties()->setTitle('ACUMULADOS DE LLANTAS'); 
    }

    public function prepareData()
    {
        $modeloVentas = new VentasExtended;
        $listaVendidoStock = $modeloVentas->historicoVentaLlantasYStockActual();

        $numDocto = '';
        $acumuladoMedidas = [];
        $sucursales = [];
        $medidaActual = '';
        $j = 0;
 
        //recorremos todas las ventas, pero la consulta devuelde  datos "repetidos" de la venta, pero que se diferencian en el stock de las diferentes sucursales
        foreach ($listaVendidoStock as $i => $articuloVenta) {
                // $listaVendidoStock[$i]->DESCRIPCION = utf8_decode( $articuloVenta->DESCRIPCION);

                if ( ! in_array($articuloVenta->ALMACEN_STOCK , $sucursales)  ) {
                    array_push( $sucursales , $articuloVenta->ALMACEN_STOCK );
                }         //comprueba  que sea la misma venta 
                if ( $numDocto != $articuloVenta->NUMDOCTO) {
                    $j++;
                    $numDocto = $articuloVenta->NUMDOCTO;
                    $medidaActual = explode( " ", $articuloVenta->DESCRIPCION );
                    $medidaActual = $medidaActual[0];
                    //se procede a agrupar las llantas por medidas y contabilizar la cantidad de articulos vendidos de la misma medida en la sucursal
                    if ( isset($acumuladoMedidas[$medidaActual][$articuloVenta->ALMACEN_STOCK]['VENDIDOS'] ) ) {
                        if ( $articuloVenta->ALMACEN == $articuloVenta->ALMACEN_STOCK  ) {
                            $acumuladoMedidas[$medidaActual][$articuloVenta->ALMACEN_STOCK]['VENDIDOS'] += $articuloVenta->CANTIDAD;
                        }
                    }else{
                        if ( $articuloVenta->ALMACEN == $articuloVenta->ALMACEN_STOCK  ) {
                            $acumuladoMedidas[$medidaActual][$articuloVenta->ALMACEN_STOCK]['VENDIDOS'] = $articuloVenta->CANTIDAD;
                        }
                    }
                    
                    if ( !isset ( $acumuladoMedidas[$medidaActual][$articuloVenta->ALMACEN_STOCK]['CODIGOS']) ) {
                        $acumuladoMedidas[$medidaActual][$articuloVenta->ALMACEN_STOCK]['CODIGOS'] = [$articuloVenta->CODIGO];
                        $acumuladoMedidas[$medidaActual][$articuloVenta->ALMACEN_STOCK]['STOCK'] = $articuloVenta->STOCK;
                        $acumuladoMedidas[$medidaActual][$articuloVenta->ALMACEN_STOCK]['STK'] = [$articuloVenta->CODIGO ." -- ". $articuloVenta->STOCK];

                    }elseif ( !in_array( $articuloVenta->CODIGO, $acumuladoMedidas[$medidaActual][$articuloVenta->ALMACEN_STOCK]['CODIGOS']) ) {
                        array_push($acumuladoMedidas[$medidaActual][$articuloVenta->ALMACEN_STOCK]['CODIGOS'] , $articuloVenta->CODIGO );
                        $acumuladoMedidas[$medidaActual][$articuloVenta->ALMACEN_STOCK]['STOCK'] += $articuloVenta->STOCK;
                        array_push($acumuladoMedidas[$medidaActual][$articuloVenta->ALMACEN_STOCK]['STK'] , [$articuloVenta->CODIGO ." -- ". $articuloVenta->STOCK] );
                        // echo $articuloVenta->ALMACEN_STOCK.' -- '.$articuloVenta->CODIGO.'  ----  '.$acumuladoMedidas[$medidaActual][$articuloVenta->ALMACEN_STOCK]['STOCK'] ."<br>";
                    }
                    
                } else {
                    $medidaComprobar = explode( " ", $articuloVenta->DESCRIPCION );
                    $medidaComprobar = $medidaComprobar[0];

                    if ( isset($acumuladoMedidas[$medidaActual][$articuloVenta->ALMACEN_STOCK]['VENDIDOS'] ) ) {
                        if ( $articuloVenta->ALMACEN == $articuloVenta->ALMACEN_STOCK  ) {
                            $acumuladoMedidas[$medidaActual][$articuloVenta->ALMACEN_STOCK]['VENDIDOS'] += $articuloVenta->CANTIDAD;
                        }
                    }else{
                        if ( $articuloVenta->ALMACEN == $articuloVenta->ALMACEN_STOCK  ) {
                            $acumuladoMedidas[$medidaActual][$articuloVenta->ALMACEN_STOCK]['VENDIDOS'] = $articuloVenta->CANTIDAD;
                        }
                    }
                    
                    if ( $medidaComprobar != $medidaActual) {
                        continue;
                    }
                    if ( !isset ( $acumuladoMedidas[$medidaActual][$articuloVenta->ALMACEN_STOCK]['CODIGOS']) ) {
                        $acumuladoMedidas[$medidaActual][$articuloVenta->ALMACEN_STOCK]['CODIGOS'] = [$articuloVenta->CODIGO];
                        $acumuladoMedidas[$medidaActual][$articuloVenta->ALMACEN_STOCK]['STOCK'] = $articuloVenta->STOCK;
                        $acumuladoMedidas[$medidaActual][$articuloVenta->ALMACEN_STOCK]['STK'] = [$articuloVenta->CODIGO ." -- ". $articuloVenta->STOCK];
                    }elseif ( !in_array( $articuloVenta->CODIGO, $acumuladoMedidas[$medidaActual][$articuloVenta->ALMACEN_STOCK]['CODIGOS']) ) {
                        array_push($acumuladoMedidas[$medidaActual][$articuloVenta->ALMACEN_STOCK]['CODIGOS'] , $articuloVenta->CODIGO );
                        $acumuladoMedidas[$medidaActual][$articuloVenta->ALMACEN_STOCK]['STOCK'] += $articuloVenta->STOCK;
                        array_push($acumuladoMedidas[$medidaActual][$articuloVenta->ALMACEN_STOCK]['STK'] , [$articuloVenta->CODIGO ." -- ". $articuloVenta->STOCK] );
                        // echo $articuloVenta->ALMACEN_STOCK.' -- '.$articuloVenta->CODIGO.'  ----  '.$acumuladoMedidas[$medidaActual][$articuloVenta->ALMACEN_STOCK]['STOCK'] ."<br>";
                    }
                    
                }

        }

        $fechaPrimeraVentaLLanta = $modeloVentas->getFechaPrimeraVentaFamilia( "LLANTA" );
        return ['acumulados' => $acumuladoMedidas , 'sucursales' => $sucursales, 'primeraVenta' => $fechaPrimeraVentaLLanta[0]->FECHA ];
    }

    public function createExcelBook()
    {
        $acumuladoSucursales = $this->prepareData();
        $sucursales = $acumuladoSucursales['sucursales'];
        $acumulados = $acumuladoSucursales['acumulados'];
        $fecha1stVenta = new DateTime( $acumuladoSucursales['primeraVenta'] );
        $fechaActual = new DateTime( date("Y-m-d") );
        $cantSucursales = sizeof( $sucursales );

        $this->creaEmptySheet( "LLANTAS" , 0 );

        $mesesIntervalo = $fecha1stVenta->diff( $fechaActual );
        $mesesTranscurridos = ( $mesesIntervalo->y * 12 ) + $mesesIntervalo->m;
        
        $this->libro->getActiveSheet()->setCellValue("A10", "MEDIDA");
        $this->libro->getActiveSheet()->setCellValue($this->columnasTotales[0]."10", "TOTAL PROM. VENTA");
        $this->libro->getActiveSheet()->setCellValue($this->columnasTotales[1]."10",'TOTAL STOCK');

        $sumaVentasPromedio = 0;
        $sumaStocks = 0;

        foreach ( $sucursales as $i => $sucursal) {
            $this->libro->getActiveSheet()->mergeCells($this->columnasReservadas[$i][0]."9:".$this->columnasReservadas[$i][1]."9");
            $this->libro->getActiveSheet()->setCellValue($this->columnasReservadas[$i][0]."9",  $sucursal );
            $this->libro->getActiveSheet()->getStyle($this->columnasReservadas[$i][0]."9")->getAlignment()->setTextRotation(90);
            $this->libro->getActiveSheet()->getRowDimension("9")->setRowHeight(80);
            $this->libro->getActiveSheet()->setCellValue($this->columnasReservadas[$i][0]."10",  "V.P.");
            $this->libro->getActiveSheet()->setCellValue($this->columnasReservadas[$i][1]."10",  "STK");            

             $this->libro->getActiveSheet()->getStyle("A9:".$this->columnasTotales[1]."9")->applyFromArray( $this->labelBold);
             $this->libro->getActiveSheet()->getStyle("A9:".$this->columnasTotales[1]."9")->applyFromArray( $this->centrarTexto );
             $this->libro->getActiveSheet()->getStyle("A9:".$this->columnasTotales[1]."9")->getFill()->applyFromArray( $this->setColorFill("DF013A") );
             $this->libro->getActiveSheet()->getStyle("A9:".$this->columnasTotales[1]."9")->applyFromArray( $this->setColorText("ffffff",12) );

             $this->libro->getActiveSheet()->getStyle("A10:".$this->columnasTotales[1]."10")->applyFromArray( $this->labelBold);
             $this->libro->getActiveSheet()->getStyle("A10:".$this->columnasTotales[1]."10")->applyFromArray( $this->centrarTexto );
             $this->libro->getActiveSheet()->getStyle("A10:".$this->columnasTotales[1]."10")->getFill()->applyFromArray( $this->setColorFill("37474f") );
             $this->libro->getActiveSheet()->getStyle("A10:".$this->columnasTotales[1]."10")->applyFromArray( $this->setColorText("ffffff",12) );             

            $this->libro->getActiveSheet()->getColumnDimension($this->columnasReservadas[$i][0])->setAutoSize(false);
            $this->libro->getActiveSheet()->getColumnDimension($this->columnasReservadas[$i][0])->setWidth("5");
            $this->libro->getActiveSheet()->getColumnDimension($this->columnasReservadas[$i][1])->setAutoSize(false);
            $this->libro->getActiveSheet()->getColumnDimension($this->columnasReservadas[$i][1])->setWidth("5");            
            
        }

        $fila= 11;
        foreach ($acumulados as $medida => $infoSucursal) {
            $this->libro->getActiveSheet()->setCellValue("A$fila",  $medida );
            $this->libro->getActiveSheet()->mergeCells("A$fila:"."B$fila");
            $this->libro->getActiveSheet()->getStyle("A$fila")->applyFromArray( $this->labelBold); 

            $indexSucursal = 0;
            foreach ($infoSucursal as $sucursal => $valores) {
                $valores['VENDIDOS'] = isset( $valores['VENDIDOS'] ) ? $valores['VENDIDOS'] : 0;
                $promedio = ceil( ($valores['VENDIDOS'] / $mesesTranscurridos)  );

                $this->libro->getActiveSheet()->setCellValue($this->columnasReservadas[$indexSucursal][0]."$fila",  $promedio );
                $this->libro->getActiveSheet()->setCellValue($this->columnasReservadas[$indexSucursal][1]."$fila",  $valores['STOCK']);
                $sumaStocks += $valores['STOCK'];
                $sumaVentasPromedio += $promedio;
                $indexSucursal++;
            }
                $this->libro->getActiveSheet()->setCellValue($this->columnasTotales[0]."$fila",  ceil($sumaVentasPromedio /  $cantSucursales ) ) ;
                $this->libro->getActiveSheet()->setCellValue($this->columnasTotales[1]."$fila",  $sumaStocks);
            $sumaStocks = 0;
            $sumaVentasPromedio = 0;
            $fila++;
        }

        foreach ($sucursales as $i => $sucursal) {
            $this->libro->getActiveSheet()->setCellValue($this->columnasReservadas[$i][0].($fila),  "=SUM(".$this->columnasReservadas[$i][0].'11:'.$this->columnasReservadas[$i][0].($fila-1).")" );
            $this->libro->getActiveSheet()->setCellValue($this->columnasReservadas[$i][1].($fila),  "=SUM(".$this->columnasReservadas[$i][1].'11:'.$this->columnasReservadas[$i][1].($fila-1).")" );
            $this->libro->getActiveSheet()->getStyle($this->columnasReservadas[$i][0]."$fila:".$this->columnasReservadas[$i][1]."$fila")->applyFromArray( $this->labelBold);
        }

        $this->libro->getActiveSheet()->setCellValue($this->columnasTotales[0].($fila),  "=SUM(".$this->columnasTotales[0].'11:'.$this->columnasTotales[0].($fila-1).")" );
        $this->libro->getActiveSheet()->setCellValue($this->columnasTotales[1].($fila),  "=SUM(".$this->columnasTotales[1].'11:'.$this->columnasTotales[1].($fila-1).")" );
        $this->libro->getActiveSheet()->getStyle($this->columnasTotales[0]."10:".$this->columnasTotales[1]."$fila")->applyFromArray( $this->labelBold);
        $this->libro->getActiveSheet()->getStyle("A9:".$this->columnasTotales[1].($fila-1) )->applyFromArray( $this->bordes );

        $this->libro->getActiveSheet()->freezePane('A10');
        $this->libro->getActiveSheet()->freezePane('A11');

        $this->putLogo("K1", 150,100);
        $this->libro->getActiveSheet()->mergeCells("G4:T4");
        $this->libro->getActiveSheet()->setCellValue("G4","HISTORICO ACUMULADO DE LLANTAS POR MEDIDAS");
        $this->libro->getActiveSheet()->getStyle("G4")->applyFromArray( $this->labelBold);   
        $this->libro->getActiveSheet()->getStyle("G4")->applyFromArray( $this->centrarTexto );        

        $reporteTerminado = new \PHPExcel_Writer_Excel2007( $this->libro);
        $reporteTerminado->setPreCalculateFormulas(true);
        $reporteTerminado->setIncludeCharts(TRUE);
         $reporteTerminado->save($_SERVER['DOCUMENT_ROOT']."/intranet/controladores/reportes/Acumulados/acumulado_llantas.xlsx");
        $ubicacion = "http://matrix.com.mx/intranet/controladores/reportes/Acumulados/acumulado_llantas.xlsx";
        echo "<a href='$ubicacion'>Descargar</a>";        
    }

}

$reporte = new HistoricoVentasLlantasMedidas;
$reporte->createExcelBook();