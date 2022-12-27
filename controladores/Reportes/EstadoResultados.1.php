<?php

require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/con_edosfinancieros.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/Reportes/prepareExcel.php";
require_once $_SERVER['DOCUMENT_ROOT'].'/intranet/lib/phpmailer/class.phpmailer.php';
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/sesiones.php";

if(!isset($_SESSION)){ 
    session_start(); 
}

class FlujoEfectivo extends prepareExcel
{

    public $centrarTexto = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER_CONTINUOUS,
        )
    );

    public $colorPadre =     array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => array(
                        'rgb' => "DF013A"
                    ) );

      /*private $labelBold = array(  'font' => 
                                                        array( 'bold' => true,
                                                                    'size' => 11,
                                                                    'name' => 'Arial'), ); */

    public $coloTextPadre = array(
                        'font'  => array(
                            'color' => array('rgb' => 'FFFFFF'),
                            'size'  => 11,
                            'name'  => 'Verdana'
                        ));             
    const GASTO_OP_ROW = 10 ;                        

    public function __construct()
    {
        parent::__construct();
        $this->libro->getProperties()->setTitle('ESTADO DE RESULTADOS'); 
    }
  public static function getDatosARellenar(){
      $estadoFinanciero = new EdoFinancieros;
      $sucursales = $estadoFinanciero->getSucursal();
      $cuentas = $estadoFinanciero->getCuentas();
      $cuetasSucursales = array('sucursales' => $sucursales, 'cuentas' => $cuentas);
      return $cuetasSucursales;
  }

        public function isCuentaPadre( $cuentas, $cuenta)
        {
            foreach ($cuentas as $cuentaIndividual ) {
                if ( $cuenta == $cuentaIndividual['padre']) {
                    return $cuenta;
                }
            }
            return null;
        }
    public function preparaExcel($fechaInicio, $fechaFin){
        $cuentasPadre =  array();

        $infoSucursalCuentas = $this->getDatosARellenar();
        $cuentas = $infoSucursalCuentas['cuentas'];
        $sucursales = $infoSucursalCuentas['sucursales'];
        $this->creaEmptySheet("Estado Resultados");
        
        $this->libro->getActiveSheet()->setCellValue("A6","CUENTA");
        $this->libro->getActiveSheet()->mergeCells("A6:C6");
        
        // $arrayColumnas = array( array('D','E'),array('F','G'),array('H','I'),array('J','K'),array('L','M'),array('N','O'),
        //                                         array('P','Q'),array('R','S'),array('T','U'),array('V','W'),array('X','Y'),array('Z','AA'),
        //                                         array('AB','AC'), array('AD','AE') ,array('AF','AG'), array('AL','AM')
        //                                                 );            
        
            $arrayColumnas = [
                ['D','E','F'],['G','H','I'],['J','K','L'],['M','N','O'],['P','Q','R'],['S','T','U'],['V','W','X'],
                ['Y','Z','AA'],['AB','AC','AD'],['AE','AF','AG'],['AH','AI','AJ'],['AK','AL','AM'],
            /*['AN','AO','AP']*/];
           $columnasTotal = ['AN','AO'];

        $this->putLogo("N1", 300,180);
        /*$fechaInicio = date('Y')."-".date('m')."-1";
        $fechaFin = date('Y-m-d');*/
        $this->libro->getActiveSheet()->mergeCells("J4:W4");
        $this->libro->getActiveSheet()->setCellValue("J4","Reporte de Estado de Resultados del $fechaInicio al $fechaFin ");
        $this->libro->getActiveSheet()->getStyle("J4:W4")->applyFromArray( $this->centrarTexto );
        $this->libro->getActiveSheet()->getStyle("J4:W4")->applyFromArray( $this->labelBold );

        $i = 7;
        $filaCXP = 0;
        
        $estadoFinanciero = new EdoFinancieros;      
        //Obteniendo las cuentas por pagar registradas en la base de datos de firebird         
        $cuentasXpagar = $estadoFinanciero->getTotalCXP($fechaInicio, $fechaFin);
        $cuentasProveedores =  array();
        foreach ($cuentasXpagar as $proveedor) {
            if ( !isset( $cuentasProveedores[utf8_decode( $proveedor['CF1'] )]) ) {
                $cuentasProveedores[utf8_decode( $proveedor['CF1'] )] = $proveedor['IMPORTECOBRO'];
            }else{
                $cuentasProveedores[utf8_decode( $proveedor['CF1'] )] += $proveedor['IMPORTECOBRO'];
            }
        }
        
        $cambio  = 0;
        $ultimaFila = 0;
        foreach ($cuentas as $index => $cuenta) {
            
            if ( $cuenta['padre'] != null) {
                
                $this->libro->getActiveSheet()->mergeCells("A$i:C$i");
                if ( $cuenta['nombre'] == 'GASTOS') {
                    $this->libro->getActiveSheet()->setCellValue("A$i", 'COSTOS');
                    $this->libro->getActiveSheet()->mergeCells("A$i:C$i");
                    $i++;
                    $this->libro->getActiveSheet()->setCellValue("A$i", 'GASTOS DE OPERACION');
                    $this->libro->getActiveSheet()->mergeCells("A$i:C$i");
                }
                elseif ( $cuenta['nombre'] == 'VENTAS') {
                    $this->libro->getActiveSheet()->setCellValue("A$i", 'UTILIDAD BRUTA');
                    
                }elseif( $cuenta['nombre'] == 'COMPRAS'){
                    // $this->libro->getActiveSheet()->setCellValue("A$i", "CXP");
                    // $filaCXP = $i;
                    $i--; // Se decrementa porque  haría un salto de linea innecesario
                    }elseif ($cuenta['padre'] != '601-02-000') {
                    $this->libro->getActiveSheet()->setCellValue("A$i", $cuenta['nombre']);
                    
                    
                }else{ //SE agregan las filas de cuentas por pagar
                    // echo $cuenta['nombre'].'<br>';
                    // foreach ($cuentasProveedores as $proveedor => $monto) {
                    //     $this->libro->getActiveSheet()->setCellValue("A$i", $proveedor);
                    //     $i++;
                    // }
                    
                    $i--; //eliminando el ultimo incremento innecesario

                }
                if ( $this->isCuentaPadre($cuentas, $cuenta['cuenta'])  != null ) {
                    if ( $cuenta['nombre'] != 'COMPRAS') {
                        array_push($cuentasPadre, $cuenta['cuenta']);
                        
                        $this->libro->getActiveSheet()->getStyle("A$i")->getFill()->applyFromArray( $this->colorPadre );
                        $this->libro->getActiveSheet()->getStyle("A$i")->applyFromArray( $this->coloTextPadre );
                    }
                 }
                $i++;                
                }
                $ultimaFila = $i;
            }
        
        $this->libro->getActiveSheet()->getStyle("A6:".$columnasTotal[1].($i-1) )->applyFromArray( $this->bordes );
        $this->libro->getActiveSheet()->getStyle("B6:".$columnasTotal[1].($i-1) )->applyFromArray( $this->centrarTexto );
        $this->libro->getActiveSheet()->getStyle("A6:A".($i-1) )->applyFromArray( $this->labelBold );

        //HACIENDO LA SUMA DE LOS VALORES INDIVIDUALES
        $this->libro->getActiveSheet()->setCellValue($columnasTotal[0].'6','GENERAL');
        $this->libro->getActiveSheet()->mergeCells("$columnasTotal[0]6:$columnasTotal[1]6");
        $this->libro->getActiveSheet()->getStyle("$columnasTotal[0]6" )->applyFromArray( $this->labelBold );

        $this->libro->getActiveSheet()->setCellValue("$columnasTotal[0]7","=SUM(D7:AAL7)");
        $this->libro->getActiveSheet()->mergeCells("$columnasTotal[0]7:$columnasTotal[0]7");
        $this->libro->getActiveSheet()->getStyle("$columnasTotal[0]7")->getFill()->applyFromArray( $this->colorPadre );
        $this->libro->getActiveSheet()->getStyle("$columnasTotal[0]7")->applyFromArray( $this->coloTextPadre );

        $this->libro->getActiveSheet()->setCellValue("$columnasTotal[0]8","=SUM(D8:AL8)");
        $this->libro->getActiveSheet()->mergeCells("$columnasTotal[1]8:$columnasTotal[1]8");
        $this->libro->getActiveSheet()->setCellValue("$columnasTotal[0]9","=SUM(D9:AL9)");
        $this->libro->getActiveSheet()->mergeCells("$columnasTotal[0]9:$columnasTotal[1]9");
        $this->libro->getActiveSheet()->setCellValue("$columnasTotal[0]10","=SUM(D10:AL10)");
        $this->libro->getActiveSheet()->mergeCells("$columnasTotal[0]10:$columnasTotal[1]10");
        $this->libro->getActiveSheet()->getStyle("$columnasTotal[0]10")->getFill()->applyFromArray( $this->colorPadre );
        $this->libro->getActiveSheet()->getStyle("$columnasTotal[0]10")->applyFromArray( $this->coloTextPadre );
        


        $idxSucursal = -1;
        $filaGastoFinanciero = 0;
        foreach ($sucursales  as $index => $sucursal) {
            if ( $sucursal['idprediction'] ==  0 || is_null($sucursal['idprediction']) ) {
                continue;
            }
            $filaGastoFinanciero = 0;
            $idxSucursal ++;
            
            $this->libro->getActiveSheet()->mergeCells($arrayColumnas[$idxSucursal][0]."6:".$arrayColumnas[$idxSucursal][1]."6");
            $this->libro->getActiveSheet()->setCellValue($arrayColumnas[$idxSucursal][0]."6", $sucursal['descripcion']);
            $this->libro->getActiveSheet()->getStyle($arrayColumnas[$idxSucursal][0]."6" )->applyFromArray( $this->labelBold );
            $this->libro->getActiveSheet()->setCellValue($arrayColumnas[$idxSucursal][2]."6", "%");
            $this->libro->getActiveSheet()->getStyle($arrayColumnas[$idxSucursal][2]."6")->applyFromArray($this->centrarTexto);
            $this->libro->getActiveSheet()->getStyle($arrayColumnas[$idxSucursal][2]."6")->applyFromArray($this->labelBold);            

            $gastosSucursal = $estadoFinanciero->getGastosCuentasSucursal($sucursal['id'], $fechaInicio, $fechaFin);
            $i = 7;
            $contCXP = 0;
            $banderaCompras = 0;
            foreach ($gastosSucursal as $idx => $cuenta) {
                $this->libro->getActiveSheet()->mergeCells($arrayColumnas[$idxSucursal][0]."$i:".$arrayColumnas[$idxSucursal][1]."$i");
                
                if (  is_numeric( $cuenta['monto']) ){
                    
                    if ( strpos($cuenta['cuenta'] , '601-02') !== false  ) {
                        // $i =  $filaCXP +1; // Comienza a colocar los valores de los proveedores un fila despues del encabezado de CXP
                        
                        // foreach ($cuentasProveedores as $proveedor => $monto) {
                        //     $this->libro->getActiveSheet()->setCellValue($arrayColumnas[$idxSucursal][0]."$i", ($monto/12) );
                        //     $this->libro->getActiveSheet()->getStyle($arrayColumnas[$idxSucursal][0]."$i")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00");
                        //     $contCXP++;
                        //     $i++;
                        // }
                        // //HACIENDO LAS SUMAS DE LOS VALORES DE CUENTAS POR PAGAR
                        // $this->libro->getActiveSheet()->setCellValue($arrayColumnas[$idxSucursal][0]."$filaCXP", "=(SUM(".$arrayColumnas[$idxSucursal][0].($filaCXP+1).":".$arrayColumnas[$idxSucursal][0].($i-1).")) ");
                        // $i--; //Eliminando ultimo incremento innecesario
                        //echo "<br><br> SALIO CON   ----".$sucursal['descripcion'];
                        $banderaCompras ++;
                        
                    }
                    
                    if ( $banderaCompras == 1 && strpos($cuenta['cuenta'] , '601-02')  !== false) {
                        $i--;  //Eliminando valor innecesario
                        // echo$cuenta['cuenta']. "  ---- ". $sucursal['idprediction']."<br>";
                    }
                    else {
                        $this->libro->getActiveSheet()->setCellValue($arrayColumnas[$idxSucursal][0]."$i", $cuenta['monto']);
                        //obteniendo el detalle de los gastos y se agregan como comentario a la celda
                        $detalleGastos = $estadoFinanciero->getDetalleGastoCuenta($cuenta['cuenta'], $fechaInicio, $fechaFin); 
                        $contentComentario = '';
                        foreach ($detalleGastos as $gasto) {
                            $contentComentario .= $gasto['descripcion']."$".number_format($gasto['total'],2,'.',',')."\n";
                        }
                        
                        $this->libro->getActiveSheet()->getComment($arrayColumnas[$idxSucursal][0]."$i")->getText()->createTextRun( $contentComentario );


                        if (  $filaGastoFinanciero  === 0) {
                            $this->libro->getActiveSheet()->setCellValue($arrayColumnas[$idxSucursal][2]."$i", "=($".$arrayColumnas[$idxSucursal][0]."$7-SUM(".$arrayColumnas[$idxSucursal][0]."11:".$arrayColumnas[$idxSucursal][0]."$i".") )/$".$arrayColumnas[$idxSucursal][0]."$8");
                        }else{
                            $this->libro->getActiveSheet()->setCellValue($arrayColumnas[$idxSucursal][2]."$i", "=($".$arrayColumnas[$idxSucursal][0]."$7-".$arrayColumnas[$idxSucursal][0].self::GASTO_OP_ROW."-SUM(".$arrayColumnas[$idxSucursal][0].($filaGastoFinanciero+1).":".$arrayColumnas[$idxSucursal][0]."$i".") )/$".$arrayColumnas[$idxSucursal][0]."$8");
                        }
                        $this->libro->getActiveSheet()->getStyle($arrayColumnas[$idxSucursal][2]."$i" )
                                    ->getNumberFormat()->applyFromArray( 
                                        array( 
                                            'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE
                                        )
                                    );
                                            
                    }
                    
                } else {
                    if ( $cuenta['cuenta'] == '601-02-001' ) {
                        $i--;
                    }
                    $this->libro->getActiveSheet()->setCellValue($arrayColumnas[$idxSucursal][0]."$i", 0);
                }
                
                if ( in_array( $cuenta['cuenta'], $cuentasPadre ) ){
                    $this->libro->getActiveSheet()->getStyle($arrayColumnas[$idxSucursal][0]."$i")->getFill()->applyFromArray( $this->colorPadre );
                    if(  !is_numeric( $cuenta['monto']) )
                        if ( $i == 7) { // Es la fila de encabezado de utilidad bruta?
                            $this->libro->getActiveSheet()->setCellValue($arrayColumnas[$idxSucursal][0]."$i", "=".$arrayColumnas[$idxSucursal][0].($i+1)."-".$arrayColumnas[$idxSucursal][0].($i+2));
                        }else{
                            if( $cuenta['cuenta'] == '601-03-000'){ //ES GASTOS FINANCIEROS
                                $filaGastoFinanciero  = $i;
                                $this->libro->getActiveSheet()->setCellValue($arrayColumnas[$idxSucursal][0]."$i", 0);
                            }elseif( $cuenta['cuenta'] == '601-01-000'){ //ES GASTO DE OPERACION
                                    $this->libro->getActiveSheet()->setCellValue($arrayColumnas[$idxSucursal][0]."$i", "=SUM(".$arrayColumnas[$idxSucursal][0].($i+1).":".$arrayColumnas[$idxSucursal][0].($i+44).")");
                            }else{ //ES COMPRAS(SUSTITUIDO POR CUENTAS POR PAGAR)
 
                                    // $this->libro->getActiveSheet()->setCellValue($arrayColumnas[$idxSucursal][0]."$i", 0);
                            }
                            
                        }

                        
                    $this->libro->getActiveSheet()->getStyle($arrayColumnas[$idxSucursal][0]."$i")->applyFromArray( $this->coloTextPadre );
                }          
                else{
                     if( $cuenta['cuenta'] == '601-02-000'  ){
                         $i--;
                     }else{
                         
                     }
                }      
                //haciendo el llenado de las celdas de compras y ventas
                // if ( $cuenta['cuenta'] == '601-02-001' ) { //Obttienen las compras
                //     $compras = $estadoFinanciero->getTotalCompras($sucursal['id'], $fechaInicio, $fechaFin);
                //     $importeCompras = 0;
                //     foreach ($compras as  $compra) {
                //         $importeCompras += $compra['IMPORTETOTAL'] ;
                //     }
                //     $this->libro->getActiveSheet()->setCellValue($arrayColumnas[$idxSucursal][0]."$i", $importeCompras);
                // }
            
                if ( $cuenta['nombre'] == 'VENTAS EN GENERAL' ) { //Obttienen las ventas
                    
                    $sucursal['idprediction'] = $sucursal['idprediction'] != NULL ? $sucursal['idprediction'] : 0;
                    $ventas = $estadoFinanciero->getVentasDiariasInDateRange($sucursal['idprediction'], $fechaInicio, $fechaFin);
                    $costosVentas = $estadoFinanciero->getCostosVentas(array(
                                                    'fechaInicio' => $fechaInicio,
                                                    'fechaFin' => $fechaFin,
                                                    'almacen' => $sucursal['idprediction']
                    ));
                    // $compras = $estadoFinanciero->getTotalCompras($sucursal['idprediction'], $fechaInicio, $fechaFin);
                    $cobrosVentas = $estadoFinanciero->getCobrosInDateRange($sucursal['idprediction'], $fechaInicio, $fechaFin);
                    $costo = 0;
                    $importeVenta = 0;
                    foreach ($costosVentas as $costoIndividual) {
                        // $importeVenta = $costoIndividual['VENTAS'];
                        $costo = $costoIndividual['COSTOS'];
                    }

                    
                    // foreach ($compras as $compra) {
                    //     $costo += $compra['IMPORTETOTAL'];
                    // }
                    // echo $sucursal['idprediction']."    ".$sucursal['descripcion']."     ".$costo.'<br>';
                    
                    foreach ($ventas as  $venta) {
                        $importeVenta += $venta['IMPORTE'] ;
                        // $costo += $venta['IMPORTE']*0.70;
                    }
                    $cobros = 0;
                    foreach ($cobrosVentas as $cobro) {
                        $cobros += $cobro['IMPORTE'];
                    }
                    $importeVenta += $cobros;

                    $this->libro->getActiveSheet()->setCellValue($arrayColumnas[$idxSucursal][0]."$i", $importeVenta);
                    $this->libro->getActiveSheet()->getStyle($arrayColumnas[$idxSucursal][0]."$i")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00");
                    $i++;
                    $this->libro->getActiveSheet()->setCellValue($arrayColumnas[$idxSucursal][0]."$i", $costo);
                }                
                $this->libro->getActiveSheet()->getStyle($arrayColumnas[$idxSucursal][0]."$i")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00");
                $i++;
            }
        //estableciendo el la suma de gastos financieros
            $this->libro->getActiveSheet()->setCellValue($arrayColumnas[$idxSucursal][0]."$filaGastoFinanciero","=SUM(".$arrayColumnas[$idxSucursal][0].($filaGastoFinanciero+1).":".$arrayColumnas[$idxSucursal][0].($ultimaFila-1).")");
            //echo $arrayColumnas[$idxSucursal][0]."$filaGastoFinanciero<br>";            
        }

        $this->libro->getActiveSheet()->setCellValue("A$i", 'UTILIDAD/PÉRDIDA');
        $this->libro->getActiveSheet()->mergeCells("A$ultimaFila:C$ultimaFila");
        $this->libro->getActiveSheet()->getStyle("A$ultimaFila" )->applyFromArray( $this->labelBold );

        $idxSucursal = -1;
        foreach ($sucursales  as $index => $sucursal) {
            if ( $sucursal['idprediction'] ==  0 || is_null($sucursal['idprediction']) ) {
                continue;
            }
            $idxSucursal++;
            $this->libro->getActiveSheet()->setCellValue($arrayColumnas[$idxSucursal][0]."$i", "=(".$arrayColumnas[$idxSucursal][0]."7-".$arrayColumnas[$idxSucursal][0]."10-".$arrayColumnas[$idxSucursal][0].($filaGastoFinanciero).")");
            $this->libro->getActiveSheet()->mergeCells($arrayColumnas[$idxSucursal][0]."$ultimaFila:".$arrayColumnas[$idxSucursal][1]."$ultimaFila");
            //Obteniendo el resultado total para aplicar un cambio de color al texto si es perdida o utilidad
            $monto = $this->libro->getActiveSheet()->getCell($arrayColumnas[$idxSucursal][0].$ultimaFila)->getCalculatedValue();
            if ( $monto < 0) {
                $this->libro->getActiveSheet()->getStyle($arrayColumnas[$idxSucursal][0].$ultimaFila)->applyFromArray($this->setColorText('e60000'));
            }else{
                $this->libro->getActiveSheet()->getStyle($arrayColumnas[$idxSucursal][0].$ultimaFila)->applyFromArray($this->setColorText('009900'));
            }
            $this->libro->getActiveSheet()->getStyle($arrayColumnas[$idxSucursal][0]."$ultimaFila")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00");
        }

        for ($i=11; $i <= $ultimaFila ; $i++) { 
            $this->libro->getActiveSheet()->setCellValue("$columnasTotal[0]$i","=SUM(D$i:AL$i)");
            $this->libro->getActiveSheet()->mergeCells("$columnasTotal[0]$i:$columnasTotal[1]$i");
        }
        //Obteniendo el resultado total para aplicar un cambio de color al texto si es perdida o utilidad
        $monto = $this->libro->getActiveSheet()->getCell("$columnasTotal[0]$ultimaFila")->getCalculatedValue();
        if ( $monto < 0) {
            $this->libro->getActiveSheet()->getStyle("$columnasTotal[0]$ultimaFila")->applyFromArray($this->setColorText('e60000'));
        }else{
            $this->libro->getActiveSheet()->getStyle("$columnasTotal[0]$ultimaFila")->applyFromArray($this->setColorText('009900'));
        }

        $this->libro->getActiveSheet()->getStyle("$columnasTotal[0]7:$columnasTotal[0]$ultimaFila")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00");
        // $this->libro->getActiveSheet()->getStyle("AB$filaCXP")->getFill()->applyFromArray( $this->colorPadre );
        // $this->libro->getActiveSheet()->getStyle("AB$filaCXP")->applyFromArray( $this->coloTextPadre );

        $this->libro->getActiveSheet()->getStyle("$columnasTotal[0]$filaGastoFinanciero")->getFill()->applyFromArray( $this->colorPadre );
        $this->libro->getActiveSheet()->getStyle("$columnasTotal[0]$filaGastoFinanciero")->applyFromArray( $this->coloTextPadre );



        // CREANDO LA HOJA  DONDE SE LISTA LAS VENTAS Y  LOS GASTOS  PARA GENERAR LAS GRAFICAS
        $this->listarVentasGastos($fechaInicio, $fechaFin);

        
        $reporteTerminado = new \PHPExcel_Writer_Excel2007( $this->libro);
        $reporteTerminado->setPreCalculateFormulas(true);
        $reporteTerminado->setIncludeCharts(TRUE);
        $reporteTerminado->save("Estado de Resultados.xlsx");
		
		return "controladores/reportes/Estado de Resultados.xlsx";
  }

  public function listarVentasGastos($fechaInicio, $fechaFin)
  {
      $dataInicial =  $this->getDatosARellenar() ;
      $sucursales = $dataInicial['sucursales'];
      $estadoFinanciero = new EdoFinancieros;
      $listadoMovimientos = array();
      $columnaVentasGastos = array(
            array('A','B','C'),array('E','F','G'),array('I','J','K'),array('M','N','O'),
            array('Q','R','S'), array('U','V','W'),array('Y','Z','AA'),array('AC','AD','AE'),
            array('AG','AH','AI'),array('AK','AL','AM'),array('AO','AP','AQ'),array('AS','AT','AU'),
      array('AW','AX','AY'),  array('BA','BB','BC'), array('BE','BF','BG'), array('BI','BJ','BK') );


    //Relllenando el array de  con las fechas incluidas en el intervalo dado
        $intervaloInicio = explode('-',$fechaInicio);
        $intervaloFin = explode('-',$fechaFin);
        if ( $intervaloInicio[1] == $intervaloFin[1]) {
            $diaInicio = $intervaloInicio[2] / 1;
            $diaFin = $intervaloFin[2] / 1;
            for ($i=$diaInicio; $i <= $diaFin ; $i++) { 
                $dia = $i < 10 ? "0$i" : $i;
                $listadoMovimientos['gastos'][$intervaloInicio[0].'-'.$intervaloInicio[1].'-'. $dia] = 0;
                $listadoMovimientos['ventas'][$intervaloInicio[0].'-'.$intervaloInicio[1].'-'. $dia] = 0;
            }
        }elseif ( $intervaloInicio[0] == $intervaloFin[0]) { //Son del mismo año pero diferentes meses
            $mesInicio = $intervaloInicio[1] / 1;
            $mesFin = $intervaloFin[1] / 1;
            $numeroDias = 0;
            $diaInicio = 0;
            for ($i=$mesInicio; $i <= $mesFin ; $i++) { 
                $numeroDias =cal_days_in_month(CAL_GREGORIAN, $mesInicio, $intervaloInicio[0]);
                if ( $i == $mesInicio) {
                    $diaInicio = $intervaloInicio[2];
                }elseif( $i < $mesFin){
                    $diaInicio = 1;
                }else{
                    $diaInicio =1;
                    $numeroDias = $intervaloFin[2] /1;
                }
                //LLenando el array con las fechas
                for ($j= $diaInicio ; $j <= $numeroDias ; $j++) { 
                    $dia = $j < 10 ? "0$j" : $j;
                    $mes = $i < 10 ? "0$i" : $i;
                    $listadoMovimientos['gastos'][$intervaloInicio[0].'-'.$mes.'-'. $dia] = 0;
                    $listadoMovimientos['ventas'][$intervaloInicio[0].'-'.$mes.'-'. $dia] = 0;
                }
            }
        }

    $idxSucursal = -1;
    
        $listadoMovimientosGral =  $listadoMovimientos;

    //obteniendo la cantidad de fillas insertadas en la hoja 1
    $rowLastdata =  $this->libro->getActiveSheet()->getHighestRow() + 5;
    $rowChartGeneral = $rowLastdata;

    $this->libro->createSheet(1);
    $this->libro->setActiveSheetIndex(1);
    $this->libro->getActiveSheet()->setTitle('Data');

    $columnasGraficas = array(array('A','H'), array('J','Q'), array('S','Z') );
    $countIndexChart = 1;
    $bandGeneral = true;

      foreach ($sucursales as $index =>   $sucursal) {
          if ( $sucursal['idprediction'] == 0 || is_null($sucursal['idprediction'])  ) {
              continue;
          }else{
              $idxSucursal++;
              //obteniendo las ventas diarías en el intervalo de tiempo establecido y a la sucursal en concurrencia
              $ventas = $estadoFinanciero->getVentasDiariasInDateRange($sucursal['idprediction'],$fechaInicio, $fechaFin);
            //   Obteniendo los gastos generados por la sucursal
             $movimientosGastos = $estadoFinanciero->getMovimientos($sucursal['id'], $fechaInicio, $fechaFin);
            
                foreach ($movimientosGastos as $movimiento) {
                    if ( isset( $listadoMovimientos['gastos'][$movimiento['docfecha']] ) ) {
                        $listadoMovimientos['gastos'][$movimiento['docfecha']] += $movimiento['total'];
                        $listadoMovimientosGral['gastos'][$movimiento['docfecha']] += $movimiento['total'];
                    } else {
                        $listadoMovimientos['gastos'][$movimiento['docfecha']] = $movimiento['total'];
                        $listadoMovimientosGral['gastos'][$movimiento['docfecha']] += $movimiento['total'];
                    }
                    
                }

                foreach ($ventas as $venta) {
                    
                    if ( isset( $listadoMovimientos['ventas'][$venta['FECHA']] ) ) {
                        $listadoMovimientos['ventas'][$venta['FECHA'] ] += $venta['IMPORTE'];
                        $listadoMovimientosGral['ventas'][$venta['FECHA'] ] += $venta['IMPORTE'];

                        $listadoMovimientos['gastos'][$venta['FECHA'] ] += ($venta['IMPORTE'] * 0.70);   
                        $listadoMovimientosGral['gastos'][$venta['FECHA'] ] += ($venta['IMPORTE'] * 0.70);   
                    } 
                    
                }


                //Agregando los valores a la hoja dos para genear las gráficas
                $acumuladoVentas = $acumuladoGastos = 0;
                $g = 3 ; //contador de filas para gastos
                $v = 3;  // Contador de filas para ventas
                $puntoEquilibrio = array();

                $this->libro->getActiveSheet()->setCellValue($columnaVentasGastos[$idxSucursal][0].'2', "Fecha");
                $this->libro->getActiveSheet()->setCellValue($columnaVentasGastos[$idxSucursal][1].'2', "Ventas");
                $this->libro->getActiveSheet()->setCellValue($columnaVentasGastos[$idxSucursal][2].'2', "Gastos");

                foreach ($listadoMovimientos['ventas'] as $fecha => $monto) {
                    $acumuladoVentas += $monto;
                    $this->libro->getActiveSheet()->setCellValue($columnaVentasGastos[$idxSucursal][0].$v, $fecha);
                    $this->libro->getActiveSheet()->setCellValue($columnaVentasGastos[$idxSucursal][1].$v, $acumuladoVentas);
                    $this->libro->getActiveSheet()->getStyle($columnaVentasGastos[$idxSucursal][1].$v)->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00");
                    $v++;
                }
                $acumuladoVentas = 0;
                foreach ($listadoMovimientos['gastos'] as $fecha => $monto) {
                    $acumuladoGastos += $monto;
                    $acumuladoVentas += $listadoMovimientos['ventas'][$fecha];

                    $this->libro->getActiveSheet()->setCellValue($columnaVentasGastos[$idxSucursal][2].$g, $acumuladoGastos);
                    $this->libro->getActiveSheet()->getStyle($columnaVentasGastos[$idxSucursal][2].$g)->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00");

                    if( $acumuladoGastos < $acumuladoVentas && $acumuladoGastos !=0 &&   sizeof($puntoEquilibrio) === 0){
                        $puntoEquilibrio= array('fecha' => $fecha, 'monto' => $acumuladoVentas);
                    }else{
						if( $acumuladoGastos > $acumuladoVentas){
							$puntoEquilibrio = array();
						}							
                    }
                    $g++;
                }    
                
                if ( $sucursal['id'] == 15 ) { //Es la sucursal de Boulevard
                    
                    $gV = $gG = 3;
                    $puntoEquilibrioGral = array();
                    $acumuladoVentasGral = $acumuladoGastosGral = 0;
                    $this->libro->getActiveSheet()->setCellValue($columnaVentasGastos[( sizeof($columnaVentasGastos)-1 )][0].'2', "Fecha");
                    $this->libro->getActiveSheet()->setCellValue($columnaVentasGastos[( sizeof($columnaVentasGastos)-1 )][1].'2', "Ventas");
                    $this->libro->getActiveSheet()->setCellValue($columnaVentasGastos[( sizeof($columnaVentasGastos)-1 )][2].'2', "Gastos");

                    foreach ($listadoMovimientosGral['ventas'] as $fecha => $monto) {
                        $acumuladoVentasGral += $monto;
                        $this->libro->getActiveSheet()->setCellValue($columnaVentasGastos[( sizeof($columnaVentasGastos)-1 )][0].$gV, $fecha);
                        $this->libro->getActiveSheet()->setCellValue($columnaVentasGastos[( sizeof($columnaVentasGastos)-1 )][1].$gV, $acumuladoVentasGral);
                        $gV++;
                    }
                    $acumuladoVentasGral = 0;
                    foreach ($listadoMovimientosGral['gastos'] as $fecha => $monto) {
                        $acumuladoGastosGral += $monto;
                        $acumuladoVentasGral += $listadoMovimientosGral['ventas'][$fecha];
                        $this->libro->getActiveSheet()->setCellValue($columnaVentasGastos[( sizeof($columnaVentasGastos)-1 )][2].$gG, $acumuladoGastosGral);
                        if( $acumuladoGastosGral < $acumuladoVentasGral && $acumuladoGastosGral !=0 &&   sizeof($puntoEquilibrioGral) === 0){
                            $puntoEquilibrioGral= array('fecha' => $fecha, 'monto' => $acumuladoVentasGral);
                         }else{
                             if( $acumuladoGastosGral > $acumuladoVentasGral){
								 $puntoEquilibrioGral = array();
							 }
                         }
                        $gG++;
                    }   
                    
                    //Agregando las graficas a la hoja principal
                    $this->libro->setActiveSheetIndex(0);
                    //var_dump( $puntoEquilibrioGral);
                    if (  sizeof($puntoEquilibrioGral) !==  0 ) {
                        
                        $this->libro->getActiveSheet()->setCellValue("A".($rowChartGeneral +17), "Fecha P.E. :");

                        $this->libro->getActiveSheet()->setCellValue("B".($rowChartGeneral +17), $puntoEquilibrioGral['fecha']);

                        $this->libro->getActiveSheet()->setCellValue("E".($rowChartGeneral +17), "Monto P.E. : ");
                        $this->libro->getActiveSheet()->setCellValue("F".($rowChartGeneral +17),  $puntoEquilibrioGral['monto']);
                        
                        $this->libro->getActiveSheet()->getStyle("F".($rowChartGeneral +17))->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00");

                        
                    }else{
                        $this->libro->getActiveSheet()->setCellValue("A".($rowChartGeneral +17), "Fecha P.E. :");

                        $this->libro->getActiveSheet()->setCellValue("B".($rowChartGeneral +17), "Indefinido");

                        $this->libro->getActiveSheet()->setCellValue("E".($rowChartGeneral +17), "Monto P.E. : ");
                        $this->libro->getActiveSheet()->setCellValue("F".($rowChartGeneral +17),  "Indefinido");
                    
                    }
                    $this->libro->getActiveSheet()->getStyle("E".($rowChartGeneral +17))->applyFromArray( $this->labelBold);
                    $this->libro->getActiveSheet()->getStyle("A".($rowChartGeneral + 17) )->applyFromArray( $this->labelBold);
                    $this->libro->getActiveSheet()->getColumnDimension("F")->setAutoSize(true);
                    $this->libro->getActiveSheet()->getColumnDimension("A" )->setAutoSize(true);


                $etiquetas = array( 
                                    new \PHPExcel_Chart_DataSeriesValues('String', 'Data!$'.$columnaVentasGastos[( sizeof($columnaVentasGastos)-1 )][1].'$2', NULL, 1),
                                new \PHPExcel_Chart_DataSeriesValues('String', 'Data!$'.$columnaVentasGastos[( sizeof($columnaVentasGastos)-1 )][2].'$2', NULL, 1));
                                    
                $xAxis = array(new \PHPExcel_Chart_DataSeriesValues('String', 'Data!$'.$columnaVentasGastos[( sizeof($columnaVentasGastos)-1 )][0].'$3:$'.$columnaVentasGastos[( sizeof($columnaVentasGastos)-1 )][0].'$'.($v-1), NULL, ( $v-3) ) );         
                $valores = array( new \PHPExcel_Chart_DataSeriesValues('Number', 'Data!$'.$columnaVentasGastos[( sizeof($columnaVentasGastos)-1 )][1].'$3:$'.$columnaVentasGastos[( sizeof($columnaVentasGastos)-1 )][1].'$'.( $v-1), NULL, ( $v-3) ),
                                            new \PHPExcel_Chart_DataSeriesValues('Number', 'Data!$'.$columnaVentasGastos[( sizeof($columnaVentasGastos)-1 )][2].'$3:$'.$columnaVentasGastos[( sizeof($columnaVentasGastos)-1 )][2].'$'.( $v-1), NULL, ( $v-3) ));                        
                
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
                $title = new \PHPExcel_Chart_Title('Punto de Equilibrio General');
                $grafica= new \PHPExcel_Chart(
                            'PUNTOEQUILIBRIO',
                            $title,
                            $legend,
                            $plotArea,
                            true,
                            0,
                            NULL, 
                            NULL);

                
                    $grafica->setTopLeftPosition( $columnasGraficas[0][0].$rowChartGeneral);
                    $grafica->setBottomRightPosition($columnasGraficas[0][1].($rowChartGeneral + 15) );
                    $this->libro->getActiveSheet()->addChart( $grafica);


                }          
                
                
                $etiquetas = array( 
                                        new \PHPExcel_Chart_DataSeriesValues('String', 'Data!$'.$columnaVentasGastos[$idxSucursal][1].'$2', NULL, 1),
                                    new \PHPExcel_Chart_DataSeriesValues('String', 'Data!$'.$columnaVentasGastos[$idxSucursal][2].'$2', NULL, 1));
                                    
                $xAxis = array(new \PHPExcel_Chart_DataSeriesValues('String', 'Data!$'.$columnaVentasGastos[$idxSucursal][0].'$3:$'.$columnaVentasGastos[$idxSucursal][0].'$'.($v-1), NULL, ( $v-3) ) );         
                $valores = array( new \PHPExcel_Chart_DataSeriesValues('Number', 'Data!$'.$columnaVentasGastos[$idxSucursal][1].'$3:$'.$columnaVentasGastos[$idxSucursal][1].'$'.( $v-1), NULL, ( $v-3) ),
                                            new \PHPExcel_Chart_DataSeriesValues('Number', 'Data!$'.$columnaVentasGastos[$idxSucursal][2].'$3:$'.$columnaVentasGastos[$idxSucursal][2].'$'.( $v-1), NULL, ( $v-3) ));                        
                
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
                $title = new \PHPExcel_Chart_Title('Punto de Equilibrio '.$sucursal['descripcion']);
                $grafica= new \PHPExcel_Chart(
                            'PUNTOEQUILIBRIO',
                            $title,
                            $legend,
                            $plotArea,
                            true,
                            0,
                            NULL, 
                            NULL);

                //Agregando las graficas a la hoja principal
                $this->libro->setActiveSheetIndex(0);

                        // AGREGANDO LA FECHA EN QUE SE ALCANZÓ EL PUNTO DE EQUILIBRIO
                
                    //Letra consecutiva a la del inicio
                    $consecutiva = $columnasGraficas[$countIndexChart][0] ;
                    $consecutiva++;
                    //Letra anterior a la del fin
                    $anterior = $columnasGraficas[$countIndexChart][1] ;
                    $anterior1 = chr(  ord( $columnasGraficas[$countIndexChart][1] )- 1  );
                    $anterior2 = chr(  ord( $columnasGraficas[$countIndexChart][1] )- 2  );
                    

                    if (  sizeof($puntoEquilibrio) !==  0 ) {
                        
                        $this->libro->getActiveSheet()->setCellValue($columnasGraficas[$countIndexChart][0].($rowLastdata +17), "Fecha P.E. :");

                        $this->libro->getActiveSheet()->setCellValue($consecutiva.($rowLastdata +17), $puntoEquilibrio['fecha']);

                        $this->libro->getActiveSheet()->setCellValue($anterior2.($rowLastdata +17), "Monto P.E. : ");
                        $this->libro->getActiveSheet()->setCellValue($anterior1.($rowLastdata +17),  $puntoEquilibrio['monto']);
                        
                        $this->libro->getActiveSheet()->getStyle($anterior1.($rowLastdata +17))->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00");

                        
                    }else{
                        $this->libro->getActiveSheet()->setCellValue($columnasGraficas[$countIndexChart][0].($rowLastdata +17), "Fecha P.E. :");

                        $this->libro->getActiveSheet()->setCellValue($consecutiva.($rowLastdata +17), "Indefinido");

                        $this->libro->getActiveSheet()->setCellValue($anterior2.($rowLastdata +17), "Monto P.E. : ");
                        $this->libro->getActiveSheet()->setCellValue($anterior1.($rowLastdata +17),  "Indefinido");
                    
                    }
                    $this->libro->getActiveSheet()->getStyle($anterior2.($rowLastdata +17))->applyFromArray( $this->labelBold);
                    $this->libro->getActiveSheet()->getStyle($columnasGraficas[$countIndexChart][0].($rowLastdata + 17) )->applyFromArray( $this->labelBold);
                    $this->libro->getActiveSheet()->getColumnDimension($anterior1)->setAutoSize(true);
                    $this->libro->getActiveSheet()->getColumnDimension($columnasGraficas[$countIndexChart][0] )->setAutoSize(true);
                    
        

                if ( $countIndexChart < 3) {
                    $grafica->setTopLeftPosition( $columnasGraficas[$countIndexChart][0].$rowLastdata);
                    $grafica->setBottomRightPosition($columnasGraficas[$countIndexChart][1].($rowLastdata + 15) );
                    $countIndexChart++;
                }

                if( $countIndexChart >= 3 ){
                    $countIndexChart = 0;
                    $rowLastdata += 20;
                }
                $this->libro->getActiveSheet()->addChart( $grafica);



                $this->libro->setActiveSheetIndex(1);
                foreach ($listadoMovimientos['ventas'] as $fecha => $monto) {
                    $listadoMovimientos['ventas'] [$fecha] = 0;
                    $listadoMovimientos['gastos'] [$fecha] = 0;
                }
                

          }
      }
      $this->libro->setActiveSheetIndex(0);
      return $listadoMovimientos;
  }

}

$estadoResultados = new FlujoEfectivo;


if ( isset($_GET['fInicio']) && isset($_GET['fFin']) ) {
    
    $fecini = $_GET['fInicio'];
    $fecfin = $_GET['fFin'];

    $checkedDate = explode('-', $fecini);

    $sesion = new \Sesion();
    if ( sizeof( $checkedDate) < 2 ) {
        $fecini = $sesion->formateaFecha($fecini,'d2g',1);
        $fecfin = $sesion->formateaFecha($fecfin,'d2g',1);
    }


    
   echo $estadoResultados->preparaExcel($fecini, $fecfin);
} else {
    
	$fecini = date ('Y-m-01');
    $fecfin = date ('Y-m-d');

    $estadoResultados->preparaExcel($fecini, $fecfin);
   
    // echo json_encode( $reporteFlujo->listarVentasGastos(date('Y-m-01'),date('Y-m-d') ) );
    $configCorreo = array("descripcionDestinatario" => "Reporte de Estado de Resultados",
                                        "mensaje" => "...",
                                        "pathFile" => "Estado de Resultados.xlsx",
                                        "subject" => "Reporte de Estado de Resultados",
                                        "correos" => array( "sestrada@matrix.com.mx","raulmatrixxx@hotmail.com","luisimatrix@matrix.com")
                                        //"correos" => array( "sestrada@matrix.com.mx","auxsistemas@matrix.com.mx")
                                        );
    // $estadoResultados->enviarReporte( $configCorreo);
	
}
