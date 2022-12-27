<?php 
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/Almacenes/Articulos.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/Almacenes/Almacen.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/Reportes/prepareExcel.php";


class AcumuladoVentasFamilias extends PrepareExcel
{
        public function __construct()
    {
        parent::__construct();
        $this->libro->getProperties()->setTitle('ACUMULADOS POR FAMILIA'); 
    }
    
    public function getVentasAgrupadasMes( $mes , $anio)
    {
        $modeloArticulos =  new Articulos;
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
        $columnasReservadas = ['B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF'];
        $columnaTotal = "AG";
        $listadoVentas = self::getVentasAgrupadasMes($mes, $anio);
        $modeloAlmacen = new Almacen;
        $sucursales = $modeloAlmacen->getSucursales();
        $cantSucursales = sizeof( $sucursales );
        $i = 9;
        $hoja = 0;
        $rowTotal = $i + $cantSucursales;
        
        foreach ($listadoVentas as $familia => $fechas) {
            
             $this->creaEmptySheet( $familia , $hoja );
             $hoja++;
             $dias = 1;
            //Reiniciando los valores de i 
             $i = 9;
             $this->libro->getActiveSheet()->setCellValue("A8", "SUCURSALES");
             $this->libro->getActiveSheet()->setCellValue("$columnaTotal"."8", "TOTAL");
             $this->libro->getActiveSheet()->getStyle("A8:".$columnaTotal."8")->applyFromArray( $this->labelBold);
             $this->libro->getActiveSheet()->getStyle("A8:".$columnaTotal."8")->applyFromArray( $this->centrarTexto );
             $this->libro->getActiveSheet()->getStyle("A8:".$columnaTotal."8")->getFill()->applyFromArray( $this->setColorFill("DF013A") );
             $this->libro->getActiveSheet()->getStyle("A8:".$columnaTotal."8")->applyFromArray( $this->setColorText("ffffff",12) );

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
                 $this->libro->getActiveSheet()->setCellValue($columna."8", $dias);
                 $dias++;
                 
             }
             

            foreach ($fechas as $fecha => $almacenes) {
                $fechaSplit = explode("-", $fecha);
                $indiceColumna = $fechaSplit[2] -1;
                $columna = $columnasReservadas[ $indiceColumna ];
                foreach ($almacenes as $almacen => $cantidad) {
                    $index = $this->buscaSucursal( $almacen );
                    $this->libro->getActiveSheet()->setCellValue($columna.($i+$index),  $cantidad['cant'] );
                    $this->libro->getActiveSheet()->getStyle($columna.($i+$index))->applyFromArray( $this->centrarTexto );
                }
                //Haciendo los totales del dia 
                $this->libro->getActiveSheet()->setCellValue($columna.$rowTotal, "=SUM(".$columna."9:$columna".$rowTotal.")");
                $this->libro->getActiveSheet()->getStyle($columna.$rowTotal)->applyFromArray( $this->labelBold);

            }
            //Agregando ceros  a los campos que quedaron vacíos
            $dias = 1;
             $j= 9;
             foreach ($columnasReservadas as $columna) {
                 foreach ($sucursales as $index => $sucursal) {
                     $valor = $this->libro->getActiveSheet()->getCell($columna.($j+$index) )->getValue();
                     if ( $valor == '' || $valor == NULL) {
                         $this->libro->getActiveSheet()->setCellValue($columna.($j+$index),  0 );
                         $this->libro->getActiveSheet()->getStyle($columna.($j+$index))->applyFromArray( $this->centrarTexto );
                     }
                 }
                 $dias++;
                 
             }

             $i = 9;
             $this->libro->getActiveSheet()->setCellValue("A$rowTotal", "TOTAL");
             $this->libro->getActiveSheet()->getStyle("A$rowTotal")->applyFromArray( $this->labelBold);
            //Rellenado las filas con los nombres de las sucursales
             foreach ($sucursales as $index => $sucursal) {
                 
                 $this->libro->getActiveSheet()->setCellValue("A".($i+$index),  $sucursal->DESCRIPCION );
                 $this->libro->getActiveSheet()->setCellValue($columnaTotal.($i+$index), "=SUM(".$columnasReservadas[0].($i+$index).":$columnaTotal".($i+$index).")" );
                 $this->libro->getActiveSheet()->getStyle($columnaTotal.($i+$index))->applyFromArray( $this->labelBold);
             }
             $this->libro->getActiveSheet()->getColumnDimension("A" )->setAutoSize(true);
             $this->libro->getActiveSheet()->getStyle("A8:".$columnasReservadas[30].($rowTotal-1) )->applyFromArray( $this->bordes );
        }
        
         $reporteTerminado = new \PHPExcel_Writer_Excel2007( $this->libro);
        // $reporteTerminado->setPreCalculateFormulas(true);
        // $reporteTerminado->setIncludeCharts(TRUE);
         $reporteTerminado->save($_SERVER['DOCUMENT_ROOT']."/intranet/controladores/reportes/acumulado_ventas.xlsx");
        $ubicacion = "http://matrix.com.mx/intranet/controladores/reportes/AcumuladoVentas.xlsx";
        echo "<a href='$ubicacion'>Descargar</a>";
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
$reporte->generaReporteAcumulados(date('m'), date('Y'));
    // $configCorreo = array("descripcionDestinatario" => "Acumulado de Ventas por Familia",
    //                                     "mensaje" => "SITEX",
    //                                     "pathFile" => "AcumuladoVentas.xlsx",
    //                                     "subject" => "Acumulado de Ventas por Familia",
    //                                     "correos" => array( "sestrada@matrix.com.mx","raulmatrixxx@hotmail.com","luisimatrix@matrix.com")
    //                                     // "correos" => array( "sestrada@matrix.com.mx","auxsistemas@matrix.com.mx")
    //                                     );
    // $reporte->enviarReporte( $configCorreo);