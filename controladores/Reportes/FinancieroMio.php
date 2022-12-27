<?php 

namespace Reportes;

if(!isset($_SESSION)){ 
    session_start(); 
}

require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/sesiones.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/lib/PHPExcel.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/lib/PHPExcel/Writer/Excel2007.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/con_edosfinancieros.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/sesiones.php";

if ( isset($_POST['fInicio']) && isset($_POST['fFin']) ) {
    $fecini = $_POST['fInicio'];
    $fecfin = $_POST['fFin'];

    $sesion = new \Sesion();
    $fecini = $sesion->formateaFecha($fecini,'d2g',1);
    $fecfin = $sesion->formateaFecha($fecfin,'d2g',1);

    $reporteFinanciero = new \Reportes\Financiero;
   echo $reporteFinanciero->preparaReporte($fecini, $fecfin);
} else {
    echo "0";
    exit();
}

class Financiero 
{
    private $reporte;
    const CUENTA_GASTOS_OPERACION = '601-01-000' ;
    const CUENTA_GASTOS_FINANCIERO= '601-03-000' ;

    private $labelBold = array(  'font' => 
                                                            array( 'bold' => true,
                                                                        'size' => 12,
                                                                        'name' => 'Arial'
                                        ) );
    private $hiddenData = array(
                     'font'=>array(
                         'color' => array('rgb' => 'FFFFFF')
                     )
    );                                        
    private $bordeInferior = array(
                    'borders' =>  array(
                        'bottom' => array(
                            'style' => \PHPExcel_Style_Border::BORDER_MEDIUM
                        ) )
    );                                        

    public function __construct()
    {
        $this->reporte = new \PHPExcel();
        $this->reporte->getProperties()->setCreator("Intranet");
        $this->reporte->getProperties()->setLastModifiedBy("Intranet");
        $this->reporte->getProperties()->setTitle("REPORTE FINANCIERO");
    }

    public function templateBookPage($sucursal = array() , $edoFinanciero , $ventasDetalle = array() , $gastosDetalle = array() )
    {
        
        $sheetIndex = 0;
         $costos;
        $titlePag = "GENERAL";

        if ( sizeof( $sucursal )) {
            $this->reporte->createSheet( $sucursal['id']);
            $sheetIndex = $sucursal['id'];
            $costos = number_format($edoFinanciero[0]['costos'] ,2,".","" );
            $titlePag = $sucursal['descripcion'];
        }
        else{
            $costos = number_format($edoFinanciero[1] ,2,".","" );
        }
        $titlePag = \trim( $titlePag);
        $titlePag = str_replace(" ","",$titlePag);

        $this->reporte->setActiveSheetIndex($sheetIndex);
        $this->reporte->getActiveSheet()->setTitle($titlePag);

        $this->reporte->getActiveSheet()->mergeCells("A1:F1");
        $this->reporte->getActiveSheet()->setCellValue("A1","REPORTE FINANCIERO");
        $this->reporte->getActiveSheet()->getStyle("A1")->applyFromArray( $this->labelBold);

        $this->reporte->getActiveSheet()->mergeCells("A3:C3");
        $this->reporte->getActiveSheet()->setCellValue("A3","Ventas");
         $this->reporte->getActiveSheet()->getStyle("A3")->applyFromArray( $this->labelBold);
        $this->reporte->getActiveSheet()->mergeCells("D3:F3");
        $this->reporte->getActiveSheet()->setCellValue('D3',$edoFinanciero[0]['ventas']);
        $this->reporte->getActiveSheet()->getStyle('D3')->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        $this->reporte->getActiveSheet()->mergeCells("A5:C5");
        $this->reporte->getActiveSheet()->setCellValue("A5","Costo");
        $this->reporte->getActiveSheet()->getStyle("A5")->applyFromArray( $this->labelBold);
        $this->reporte->getActiveSheet()->mergeCells("D5:F5");
        $this->reporte->getActiveSheet()->setCellValue('D5', $costos);
        $this->reporte->getActiveSheet()->getStyle('D5')->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $this->reporte->getActiveSheet()->getStyle("A5:F5")->applyFromArray( $this->bordeInferior);

        $this->reporte->getActiveSheet()->mergeCells("A6:C6");
        $this->reporte->getActiveSheet()->setCellValue("A6","Utilidad Bruta");
        $this->reporte->getActiveSheet()->getStyle("A6")->applyFromArray( $this->labelBold);
        $this->reporte->getActiveSheet()->mergeCells("D6:F6");
        $this->reporte->getActiveSheet()->setCellValue('D6',"=D3-D5");
        $this->reporte->getActiveSheet()->getStyle('D6')->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        $this->reporte->getActiveSheet()->mergeCells("A8:C8");
        $this->reporte->getActiveSheet()->setCellValue("A8","Gastos de Operacion");
        $this->reporte->getActiveSheet()->getStyle("A8")->applyFromArray( $this->labelBold);
        $this->reporte->getActiveSheet()->mergeCells("D8:F8");
        $this->reporte->getActiveSheet()->setCellValue('D8', number_format( abs($edoFinanciero['gastoOperacion'] ) ,2,".","") );
        $this->reporte->getActiveSheet()->getStyle('D8')->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        $this->reporte->getActiveSheet()->mergeCells("A10:C10");
        $this->reporte->getActiveSheet()->setCellValue("A10","Gastos Financieros");
        $this->reporte->getActiveSheet()->getStyle("A10")->applyFromArray( $this->labelBold);
        $this->reporte->getActiveSheet()->mergeCells("D10:F10");
        $this->reporte->getActiveSheet()->setCellValue('D10', number_format( abs($edoFinanciero['gastoFinanciero'] ),2,".","") );
        $this->reporte->getActiveSheet()->getStyle('D10')->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $this->reporte->getActiveSheet()->getStyle("A10:F10")->applyFromArray( $this->bordeInferior);
        
        $this->reporte->getActiveSheet()->mergeCells("A11:C11");
        $this->reporte->getActiveSheet()->setCellValue("A11","Gastos");
        $this->reporte->getActiveSheet()->getStyle("A11")->applyFromArray( $this->labelBold);
        $this->reporte->getActiveSheet()->mergeCells("D11:F11");
        $this->reporte->getActiveSheet()->setCellValue('D11',"=D8+D10");   
        $this->reporte->getActiveSheet()->getStyle('D11')->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        
        $utilidad = ($edoFinanciero[0]['ventas'] - $costos) -( abs($edoFinanciero['gastoOperacion'] ) +abs($edoFinanciero['gastoFinanciero']) );
        $labelUtilidad = "Utilidad";
        if ( $utilidad < 0) {
            $labelUtilidad ="Pérdida";
        }

        $this->reporte->getActiveSheet()->mergeCells("A13:C13");
        $this->reporte->getActiveSheet()->setCellValue("A13", $labelUtilidad);
        $this->reporte->getActiveSheet()->getStyle("A13")->applyFromArray( $this->labelBold);
        $this->reporte->getActiveSheet()->mergeCells("D13:F13");
        $this->reporte->getActiveSheet()->setCellValue('D13',"=D6-D11");   
        $this->reporte->getActiveSheet()->getStyle('D13')->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        $rentabilidad = 0;
        if( $edoFinanciero[0]['ventas'] != 0){
            $rentabilidad = ( $utilidad / $edoFinanciero[0]['ventas'] ) * 100 ;
        }
    
        $this->reporte->getActiveSheet()->mergeCells("A15:C15");
        $this->reporte->getActiveSheet()->setCellValue("A15", "Rentabilidad");
        $this->reporte->getActiveSheet()->mergeCells("D15:F15");
        $this->reporte->getActiveSheet()->setCellValue('D15',"=D13/D3");   
        $this->reporte->getActiveSheet()->getStyle('D15')->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
        $this->reporte->getActiveSheet()->getStyle("A15")->applyFromArray( $this->labelBold);

        $i = 3;

        
        $this->reporte->getActiveSheet()->setCellValue("H2", "Fecha");
        
        $this->reporte->getActiveSheet()->setCellValue("I2", "Ventas");
        
        $this->reporte->getActiveSheet()->setCellValue("J2", "Gastos");

        $this->reporte->getActiveSheet()->getColumnDimension('H')->setWidth("1");
        $this->reporte->getActiveSheet()->getColumnDimension('I')->setWidth("1");
        $this->reporte->getActiveSheet()->getColumnDimension('J')->setWidth("1");
        $this->reporte->getActiveSheet()->getColumnDimension('G')->setWidth("1");

        $ventaDiaria = array();
        $ventaDiariaAcumulada = array(); //almacena el acumulado de dias anteriores de ventas
        $banderaPE = 0;
        $puntoEquilibrio = 0;
        $fechaPE = "Indefinido";
        if ( sizeof( $ventasDetalle) ) {
            $cantAnteriorVentas = 0;
            foreach ($ventasDetalle as $fecha => $importe) {
                $this->reporte->getActiveSheet()->setCellValue("H$i", $fecha);
                $cantAnteriorVentas += $importe;
                $this->reporte->getActiveSheet()->setCellValue("I$i", $cantAnteriorVentas);
                array_push( $ventaDiaria , $importe );
                array_push( $ventaDiariaAcumulada , $cantAnteriorVentas);
                $i++;
            }
        }
                    //REINICIAMOS EL VALOR DE $i A 1 PARA RECORRER EL ARRAY DE GASTOS
        $i = 3;
        if ( sizeof($gastosDetalle) ) {
                $cantAnteriorGastos = 0;
                foreach ($gastosDetalle as $fecha => $total) {
                    $plusVentas = $ventaDiaria[ $i-3] ;
                    if( $total != 0){
                         $cantAnteriorGastos =$cantAnteriorGastos + ($total + ($plusVentas * 0.70) );
                    }else{
                        $cantAnteriorGastos = $cantAnteriorGastos + (($plusVentas * 0.70) );
                    }
                    $this->reporte->getActiveSheet()->setCellValue("J$i", $cantAnteriorGastos);

                    if( $cantAnteriorGastos <= $ventaDiariaAcumulada[$i-3] && $cantAnteriorGastos !=0 && $banderaPE == 0){
                        $banderaPE = 1;
                        $puntoEquilibrio = $ventaDiariaAcumulada[$i-3];
                        $fechaPE = $fecha;
                    }
                    $i++;
                }
        }
        $this->reporte->getActiveSheet()->mergeCells("A22:C22");
        $this->reporte->getActiveSheet()->setCellValue("A22", "Punto de Equilibrio");
        $this->reporte->getActiveSheet()->getStyle("A22")->applyFromArray($this->labelBold);

        $this->reporte->getActiveSheet()->mergeCells("D22:E22");
        $this->reporte->getActiveSheet()->setCellValue("D22", "$puntoEquilibrio");
         $this->reporte->getActiveSheet()->getStyle('D22')->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        $this->reporte->getActiveSheet()->mergeCells("K22:L22");
        $this->reporte->getActiveSheet()->setCellValue("K22", "Fecha");
        $this->reporte->getActiveSheet()->getStyle("K22")->applyFromArray($this->labelBold);
        
        $this->reporte->getActiveSheet()->mergeCells("M22:N22");
        $this->reporte->getActiveSheet()->setCellValue("M22", "$fechaPE");
        

        $this->reporte->getActiveSheet()->getStyle("H1:J$i")->applyFromArray($this->hiddenData);

        $etiquetas = array( new \PHPExcel_Chart_DataSeriesValues('String', $titlePag.'!$I$2', NULL, 1),
                                 new \PHPExcel_Chart_DataSeriesValues('String', $titlePag.'!$J$2', NULL, 1));
        $xAxis = array(new \PHPExcel_Chart_DataSeriesValues('String', $titlePag.'!$H$3:$H$'.($i-1), NULL, ( $i-3) ) );         
        $valores = array( new \PHPExcel_Chart_DataSeriesValues('Number', $titlePag.'!$I$3:$I$'.( $i-1), NULL, ( $i-3) ),
                                    new \PHPExcel_Chart_DataSeriesValues('Number', $titlePag.'!$J$3:$J$'.( $i-1), NULL, ( $i-3) ));

        $dataSeriesChart =new \PHPExcel_Chart_DataSeries(
                    \PHPExcel_Chart_DataSeries::TYPE_LINECHART,
                    \PHPExcel_Chart_DataSeries::GROUPING_STANDARD,
                    range(0, count($valores)-1),
                    $etiquetas,
                    $xAxis,
                    $valores
                    );
        $plotArea = new \PHPExcel_Chart_PlotArea(NULL, array($dataSeriesChart));
        $legend=new \PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);
        $title = new \PHPExcel_Chart_Title('Punto de Equilibrio');
        $grafica= new \PHPExcel_Chart(
                    'PUNTOEQUILIBRIO',
                    $title,
                    $legend,
                    $plotArea,
                    true,
                    0,
                    NULL, 
                    NULL);
        $grafica->setTopLeftPosition("K1");
        $grafica->setBottomRightPosition("S21");
        $this->reporte->getActiveSheet()->addChart( $grafica);

    }

    public function getArrayVentasGastosDiarios($fechaInicio,$fechaFin, $ventaDiaria, $gastosDiarios)
    {
            $arrayFechaInicio = explode("-",$fechaInicio);
            $arrayFechaFin = \explode("-",$fechaFin);
            $ventasDiarias = array();
            $gastoDiario = array();
            if ( $arrayFechaInicio[0] == $arrayFechaFin[0] ) {
                if ( $arrayFechaInicio[1] == $arrayFechaFin[1] ) {
                    for ($i= $arrayFechaInicio[2]; $i <= $arrayFechaFin[2] ; $i++) { 
                        $dia = $i;
                        if(  $i < 10){
                            $i /=1;
                            $dia = "0$i";
                        }
                        $ventasDiarias ["$arrayFechaFin[0]-$arrayFechaInicio[1]-$dia"] = 0;
                        $gastoDiario ["$arrayFechaFin[0]-$arrayFechaInicio[1]-$dia"] = 0;

                    }
     
                } else {
                    for ($i= $arrayFechaInicio[1]; $i <= $arrayFechaFin[1] ; $i++) { 
                        //obteneniendo la cantidad de dias que tiene el mes de la fecha de inicio
                        $diasInLoop = cal_days_in_month(CAL_GREGORIAN ,$i,$arrayFechaFin[0]);
                        if ( $i == $arrayFechaFin[1]) { //Esta en el mes de tope de busqueda
                            $diasInLoop = $arrayFechaFin[2];
                        }
                        
                        $diaNum = 1; //primer dia del mes
                        if ( $i == $arrayFechaInicio[1]) { //Es igual el mes al de inicio de busqueda?
                            $diaNum = $arrayFechaInicio[2];
                        }
                        $mes = $i;
                        if ( $i < 10) {
                            $i /= 1;
                            $mes = "0$i";
                        }
                        for ($j= $diaNum; $j <= $diasInLoop ; $j++) { 
                            $dia = $j;
                            if(  $j < 10){
                                $j /=1;
                                $dia = "0$j";
                            }

                            $ventasDiarias ["$arrayFechaFin[0]-$mes-$dia"] = 0;
                            $gastoDiario ["$arrayFechaFin[0]-$mes-$dia"] = 0;
                        }
                    }
                }
                foreach ($ventaDiaria as $venta ) {
                    $ventasDiarias[$venta['FECHA']] += $venta['IMPORTE'];
                }
                foreach ($gastosDiarios as $gastoPorDia ) { 
                    $gastoDiario[$gastoPorDia['docfecha']] += $gastoPorDia['total'];
                }
            } else {
                # code...
            }

            return array('ventas' =>$ventasDiarias,'gastos' => $gastoDiario);
    }
    public function preparaReporte( $fechaInicio , $fechaFin)
    {
        $edoFinanciero = new \EdoFinancieros;
        $cuentas = $edoFinanciero->getCuentas() ;
        $edoFinancieroGral = $this->setReporteGeneral( $fechaInicio , $fechaFin , $cuentas);
        $edoFinancieroSucursal = $this->setValuesReporteIndividual( '%', $fechaInicio , $fechaFin , $cuentas);
        $ventaDiaria = $edoFinanciero->getVentasDiariasInDateRange( '%',$fechaInicio, $fechaFin);
        $gastosDiarios = $edoFinanciero->getMovimientos( '%', $fechaInicio , $fechaFin);

        $gastoFinaOPDiario = $edoFinanciero->getMvtosDiariosByOpF('%' ,$fechaInicio , $fechaFin, self::CUENTA_GASTOS_FINANCIERO,self::CUENTA_GASTOS_OPERACION);
        $infoVentaGasto = $this->getArrayVentasGastosDiarios( $fechaInicio,$fechaFin,$ventaDiaria,$gastoFinaOPDiario);


         $this->templateBookPage($sucursal = array() , $edoFinancieroGral , $infoVentaGasto['ventas'], $infoVentaGasto['gastos']);


         $sucursales = $edoFinanciero->getSucursal();
         $gastoFinaOPDiario = "";
        $idTemporalSucursal = 1;
         foreach ($sucursales as $sucursal) {
            $edoFinancieroSucursal = $this->setValuesReporteIndividual( $sucursal['id'] , $fechaInicio , $fechaFin , $cuentas);
            $ventaDiaria = $edoFinanciero->getVentasDiariasInDateRange( $sucursal['idprediction'] ,$fechaInicio, $fechaFin);
            $gastosDiarios = $edoFinanciero->getMovimientos( $sucursal['id'] , $fechaInicio , $fechaFin);
            

            $gastoFinaOPDiario = $edoFinanciero->getMvtosDiariosByOpF( $sucursal['id'] ,$fechaInicio , $fechaFin, self::CUENTA_GASTOS_FINANCIERO,self::CUENTA_GASTOS_OPERACION);
           
            $infoVentaGasto = $this->getArrayVentasGastosDiarios( $fechaInicio,$fechaFin,$ventaDiaria,$gastoFinaOPDiario);
            //Se agrupan las las cantidades por fecha

            if($sucursal['idprediction'] != 0){
                $sucursal['id'] = $idTemporalSucursal;
                $this->templateBookPage($sucursal  , $edoFinancieroSucursal , $infoVentaGasto['ventas'], $infoVentaGasto['gastos']);
                $idTemporalSucursal++;
            }
             
         }
        $reporteTerminado = new \PHPExcel_Writer_Excel2007( $this->reporte);
        $reporteTerminado->setIncludeCharts(TRUE);
        
        $reporteTerminado->save("ReporteFinanciero.xlsx");
        
         return "controladores/ReporteFinanciero.xlsx";
    }

    public function setValuesReporteIndividual( $idSucursal , $fechaInicio , $fechaFin , $cuentas)
    {
        $edoFinanciero = new \EdoFinancieros;
        $totalesVenta = $edoFinanciero->getTotalVentas( $idSucursal , $fechaInicio , $fechaFin);
        $totalGastos = $this->calculaGastos( $edoFinanciero , $cuentas , $idSucursal , $fechaInicio , $fechaFin);
        array_push( $totalGastos , $totalesVenta );
        unset($edoFinanciero);

        return $totalGastos;
    }
    public function setReporteGeneral( $fechaInicio , $fechaFin ,$cuentas)
    {
        $edoFinanciero = new \EdoFinancieros;

        $totalesVenta = $edoFinanciero->getTotalVentas( '%' , $fechaInicio ,  $fechaFin);
        $ventastotales = $totalesVenta['ventas'];
        $costosTotales = $totalesVenta['costos'];

        $cxp = $edoFinanciero->getTotalCXP( $fechaInicio , $fechaFin );
        $cxpTotal = 0;
        foreach ($cxp as $cxpIndividual) {
            $cxpTotal += $cxpIndividual['IMPORTECOBRO'];
        }
        $utilidadBruta = $ventastotales - $cxpTotal;

        $totalGastos = $this->calculaGastos( $edoFinanciero , $cuentas , '%' , $fechaInicio , $fechaFin);

        array_push( $totalGastos , $totalesVenta );
        array_push( $totalGastos , $cxpTotal);

        return $totalGastos; 
    }

    public function calculaGastos($edoFinanciero, $cuentas , $idSucursal , $fechaInicio , $fechaFin)
    {
        $totalGastoOperacion = 0;
        $totalGastoFinanciero = 0;
       foreach ($cuentas as $cuenta) {
            $infoCuenta = array('idSucursal' => $idSucursal,
                                    'fechaInicio' => $fechaInicio,
                                    'fechaFin' => $fechaFin,
                                    'cuenta' =>$cuenta['cuenta']);
            $totalSubCuenta = 0;                                     
            if ( $cuenta['padre'] == self::CUENTA_GASTOS_OPERACION) {

                $totalSubCuenta = $this->getSubTotalGasto($edoFinanciero , $infoCuenta );
                $totalGastoOperacion = $this->calculaTotalSubCuenta( $cuenta['naturaleza'] , $totalSubCuenta , $totalGastoOperacion);
            }
            else{
                if ( $cuenta['padre'] == self::CUENTA_GASTOS_FINANCIERO ) {

                    $totalSubCuenta = $this->getSubTotalGasto($edoFinanciero , $infoCuenta);
                   $totalGastoFinanciero = $this->calculaTotalSubCuenta( $cuenta['naturaleza'] , $totalSubCuenta , $totalGastoFinanciero );
                } 
            }
       }

       return array("gastoOperacion" => $totalGastoOperacion,"gastoFinanciero" => $totalGastoFinanciero);
    }
    public function calculaTotalSubCuenta( $naturaleza , $importe ,$cuentaAcumulado)
    {
        if ( $naturaleza == 1) {
             $cuentaAcumulado += $importe;
        } else {
            $cuentaAcumulado -= $importe;
        }
        
        return $cuentaAcumulado;
    }
    public function getSubTotalGasto($objEdofinanciero , $cuetaParams)
    {
        extract($cuetaParams);

        $subtotal =$objEdofinanciero->getMovimientosCuentaTotal( $idSucursal ,  $fechaInicio , $fechaFin , $cuenta);

        return $subtotal;
    }
}

?>