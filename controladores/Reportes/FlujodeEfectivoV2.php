<?php
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/Reportes/prepareExcel.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/saldos_bancarios.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/con_egresos.php";

class FlujoEfectivo extends PrepareExcel
{
    public function preparaReporte()
    {
        $this->creaEmptySheet('Flujo de Efectivo');

        $rowBanco = $rowGasto = $rowCaja = 11;

        $movimientos = new Egresos;
        $movimientosBancos= new SaldosBancarios;
        $movimientosCargo = $movimientos->getMovimientoCargoAbonoGroupBy(date('m'),1);
        $movimientosAbono = $movimientos->getMovimientoCargoAbonoGroupBy(date('m'),2);
        $listaMovientosBancos = $movimientosBancos->getSaldoByMes( date('m'));
        $bancos = $movimientosBancos->getMovimientoBancosDiarioGroupBy(date('m'));
        $movimientosBancosPostFecha = $movimientosBancos->ingresosEgresosDespuesDeFecha(date('m'));
        $saldoCajaChica = $movimientosBancos->getSaldoCajaChica();
        $ingresosEgresosTotales = $movimientosBancos->ingresosEgresosMes(date('m'));
        $cuentasBancarias = $movimientosBancos->getAllcuentas();

        $totalEgresosBanco = 0;
        $totalIngresosBanco = 0;
        $saldoBancos = 0;
        $salcoCajaChica = 0;
        $totalEgresoCaja = 0;
        $totalIngresoCaja = 0;
        foreach ($ingresosEgresosTotales as $i => $ingresoEgreso) {
            $totalEgresosBanco += $ingresoEgreso['egresos'];
            $totalIngresosBanco += $ingresoEgreso['ingresos'];
        }

        foreach ($cuentasBancarias as $i => $cuenta) {
            if ( $cuenta['banco'] != 'CAJA CHICA') {
                $saldoBancos += $cuenta['saldo'];
            }else{
                $saldoCajaChica =  $cuenta['saldo'];
            }
        }
        $saldoBancos = $saldoBancos + $totalEgresosBanco - $totalIngresosBanco;

        $this->libro->getActiveSheet()->setCellValue('A10',"FECHA");
        $this->libro->getActiveSheet()->mergeCells("A10:B10"); 

        $this->libro->getActiveSheet()->setCellValue('C8',"BANCOS");
        $this->libro->getActiveSheet()->mergeCells("C8:F8");  
        $this->libro->getActiveSheet()->setCellValue("G8",'GASTOS');
        $this->libro->getActiveSheet()->mergeCells("G8:M8");
        $this->libro->getActiveSheet()->setCellValue("N8","CAJA CHICA");
        $this->libro->getActiveSheet()->mergeCells("N8:Q8");
        $this->libro->getActiveSheet()->setCellValue('C9',"SALDO INICIAL");
        $this->libro->getActiveSheet()->mergeCells("C9:D9");              
        $this->libro->getActiveSheet()->setCellValue('C10',"ABONOS");
        $this->libro->getActiveSheet()->mergeCells("C10:D10");
        $this->libro->getActiveSheet()->setCellValue('E9',"$saldoBancos");
        $this->libro->getActiveSheet()->getStyle("E9")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00");
        $this->libro->getActiveSheet()->mergeCells("E9:F9");              
        $this->libro->getActiveSheet()->setCellValue('E10',"CARGOS");
  

        $this->libro->getActiveSheet()->setCellValue('G10',"DESCRIPCION");
        $this->libro->getActiveSheet()->mergeCells("G10:I10");
        $this->libro->getActiveSheet()->setCellValue('J10',"MONTO");
        $this->libro->getActiveSheet()->mergeCells("J10:M10");
        $this->libro->getActiveSheet()->getStyle("G10:M10")->applyFromArray( $this->labelBold);
        $this->libro->getActiveSheet()->getStyle("G10:M10")->applyFromArray( $this->centrarTexto);

        $this->libro->getActiveSheet()->setCellValue('H9',"CARGOS");
        $this->libro->getActiveSheet()->getStyle("H9")->applyFromArray( $this->labelBold);
        $this->libro->getActiveSheet()->getStyle("H9")->applyFromArray( $this->centrarTexto);
        $this->libro->getActiveSheet()->mergeCells("H9:L9");    
        $this->libro->getActiveSheet()->setCellValue('N9',"SALDO INICIAL");
        $this->libro->getActiveSheet()->mergeCells("N9:O9");                
        $this->libro->getActiveSheet()->setCellValue('N10',"ABONOS");
        $this->libro->getActiveSheet()->mergeCells("N10:O10");
        $this->libro->getActiveSheet()->setCellValue('P10',"CARGOS");
        $this->libro->getActiveSheet()->mergeCells("P10:Q10");        
        $this->libro->getActiveSheet()->getStyle("A8:N8")->applyFromArray( $this->labelBold);
        $this->libro->getActiveSheet()->getStyle("C8:Q8")->applyFromArray( $this->bordes);
        $this->libro->getActiveSheet()->getStyle("A10:Q10")->applyFromArray( $this->labelBold);
        $this->libro->getActiveSheet()->getStyle("A8:Q8")->applyFromArray( $this->centrarTexto);
        $this->libro->getActiveSheet()->getStyle("A10:Q10")->applyFromArray( $this->centrarTexto);
        $this->libro->getActiveSheet()->getStyle("A10:Q10")->applyFromArray( $this->borderBottom);
        $this->libro->getActiveSheet()->getStyle("C9")->applyFromArray( $this->labelBold);
        $this->libro->getActiveSheet()->getStyle("C9")->applyFromArray( $this->centrarTexto);
        $this->libro->getActiveSheet()->getStyle("N9")->applyFromArray( $this->labelBold);
        $this->libro->getActiveSheet()->getStyle("N9")->applyFromArray( $this->centrarTexto);

        $this->libro->getActiveSheet()->getStyle("F9:F10")->applyFromArray( $this->setBorder('right'));
        $this->libro->getActiveSheet()->getStyle("M9:M10")->applyFromArray( $this->setBorder('right'));
        $this->libro->getActiveSheet()->getStyle("Q9:Q10")->applyFromArray( $this->setBorder('right'));

        $flujoEfectivo = $this->fillArrayFlujoEfectivo( array(
                                        'tipo' => 'bancos',
                                        'flujoEfectivo' => array(),
                                        'movimientos' => $bancos
                                            ));
        $flujoEfectivo = $this->fillArrayFlujoEfectivo( array(
                                'tipo' => 'caja',
                                'flujoEfectivo' => $flujoEfectivo,
                                'movimientos' =>$movimientosCargo
                                    ));                                            
        $flujoEfectivo = $this->fillArrayFlujoEfectivo( array(
                        'tipo' => 'caja',
                        'flujoEfectivo' => $flujoEfectivo,
                        'movimientos' =>$movimientosAbono
                            ));              
        sort($flujoEfectivo);

        $fila = 0;
        foreach ($flujoEfectivo as $i => $movimiento) {
            if ($movimiento['tipo'] == 'bancos') {
                if ( $movimiento['egresos'] > 0) {
                    $this->libro->getActiveSheet()->setCellValue("E".($i+11), $movimiento['egresos']);
                    $this->libro->getActiveSheet()->mergeCells("E".($i+11).":F".($i+11));
                    $this->libro->getActiveSheet()->getStyle("E".($i+11))->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00");
                    $this->libro->getActiveSheet()->getStyle("E".($i+11))->applyFromArray( $this->centrarTexto);

                    $this->libro->getActiveSheet()->setCellValue("G".($i+11), $movimiento['beneficiario']);
                    $this->libro->getActiveSheet()->mergeCells("G".($i+11).":I".($i+11));
                    $this->libro->getActiveSheet()->setCellValue("J".($i+11), $movimiento['egresos']);
                    $this->libro->getActiveSheet()->getStyle("J".($i+11))->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00");
                    $this->libro->getActiveSheet()->getStyle("J".($i+11))->applyFromArray( $this->centrarTexto);
                    
                    $this->libro->getActiveSheet()->mergeCells("J".($i+11).":M".($i+11));
                    $rowGasto++;
                } else {
                    $this->libro->getActiveSheet()->setCellValue("C".($i+11), $movimiento['ingresos']);
                    $this->libro->getActiveSheet()->mergeCells("C".($i+11).":D".($i+11));
                    $this->libro->getActiveSheet()->getStyle("C".($i+11))->applyFromArray( $this->centrarTexto);
                    $this->libro->getActiveSheet()->getStyle("C".($i+11))->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00");
                }
                $rowBanco++;
            }
            else{
                if ( $movimiento['egresos'] > 0) {
                    $this->libro->getActiveSheet()->setCellValue("P".($i+11), $movimiento['egresos']);
                    $this->libro->getActiveSheet()->getStyle("P".($i+11))->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00");
                    $this->libro->getActiveSheet()->getStyle("P".($i+11))->applyFromArray( $this->centrarTexto);

                    $this->libro->getActiveSheet()->mergeCells("P".($i+11).":Q".($i+11));
                    $this->libro->getActiveSheet()->setCellValue("G".($i+11), $movimiento['beneficiario']);
                    $this->libro->getActiveSheet()->mergeCells("G".($i+11).":I".($i+11));
                    $this->libro->getActiveSheet()->setCellValue("J".($i+11), $movimiento['egresos']);
                    $this->libro->getActiveSheet()->getStyle("J".($i+11))->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00");
                    $this->libro->getActiveSheet()->getStyle("J".($i+11))->applyFromArray( $this->centrarTexto);

                    $this->libro->getActiveSheet()->mergeCells("J".($i+11).":M".($i+11));
                    $totalEgresoCaja += $movimiento['egresos'];
                    $rowCaja++;  
                    $rowGasto++;
                } else {
                    $this->libro->getActiveSheet()->setCellValue("C".($i+11), $movimiento['ingresos']);
                    $this->libro->getActiveSheet()->getStyle("C".($i+11))->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00");
                    $this->libro->getActiveSheet()->getStyle("C".($i+11))->applyFromArray( $this->centrarTexto);
                    $totalIngresoCaja += $movimiento['ingresos'];
                    $rowCaja++;  
                }     
                         
            }
            $fila = $i + 11;
            $this->libro->getActiveSheet()->setCellValue("A".($i+11), str_replace('-','/',$movimiento['fecha']));
            $this->libro->getActiveSheet()->mergeCells("A".($i+11).":B".($i+11));
            $this->libro->getActiveSheet()->getStyle("B".($i+11))->applyFromArray( $this->setBorder('right'));
            $this->libro->getActiveSheet()->getStyle("A".($i+11))->applyFromArray( $this->centrarTexto);
        }
        $salcoCajaChica = $salcoCajaChica + $totalEgresoCaja - $totalIngresoCaja;
        $this->libro->getActiveSheet()->getStyle("D11:D$fila")->applyFromArray( $this->setBorder('right'));
        $this->libro->getActiveSheet()->getStyle("F11:F$fila")->applyFromArray( $this->setBorder('right'));
        $this->libro->getActiveSheet()->getStyle("I11:I$fila")->applyFromArray( $this->setBorder('right'));
        $this->libro->getActiveSheet()->getStyle("M11:M$fila")->applyFromArray( $this->setBorder('right'));

        $this->libro->getActiveSheet()->setCellValue("U7",'ABONOS');
        $this->libro->getActiveSheet()->setCellValue("V7",'CARGOS');
        $this->libro->getActiveSheet()->setCellValue("W7",'SALDO FINAL');
        $this->libro->getActiveSheet()->getStyle("U7:W7")->applyFromArray( $this->labelBold);
        $this->libro->getActiveSheet()->setCellValue('T8',"BANCOS");
        $this->libro->getActiveSheet()->setCellValue('U8',"=SUM(C11:C$fila)");
        $this->libro->getActiveSheet()->setCellValue('V8',"=SUM(E11:E$fila)");
        $this->libro->getActiveSheet()->setCellValue('W8',"=E9+U8-V8");
        $this->libro->getActiveSheet()->setCellValue('T9',"CAJA CHICA");
        $this->libro->getActiveSheet()->setCellValue('U9',"=SUM(N11:N$fila)");
        $this->libro->getActiveSheet()->setCellValue('V9',"=SUM(P11:P$fila)");
        $this->libro->getActiveSheet()->setCellValue('W9',"=P9+U9-V9");
        $this->libro->getActiveSheet()->getStyle("T7:W9")->applyFromArray( $this->bordes);
        $this->libro->getActiveSheet()->setCellValue('V10',"CAPITAL");
        $this->libro->getActiveSheet()->getStyle("V10")->applyFromArray( $this->labelBold);
        $this->libro->getActiveSheet()->setCellValue('W10',"=W8+W9");
        $this->libro->getActiveSheet()->getStyle("T8:T9")->applyFromArray( $this->labelBold);

        $this->libro->getActiveSheet()->getStyle("U8:W10")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00");
         $this->libro->getActiveSheet()->getStyle("W11")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00");

        $this->libro->getActiveSheet()->getColumnDimension('T')->setAutoSize(false);
        $this->libro->getActiveSheet()->getColumnDimension('T')->setWidth("15");
        $this->libro->getActiveSheet()->getColumnDimension('U')->setAutoSize(false);
        $this->libro->getActiveSheet()->getColumnDimension('U')->setWidth("15");        
        $this->libro->getActiveSheet()->getColumnDimension('V')->setAutoSize(false);
        $this->libro->getActiveSheet()->getColumnDimension('V')->setWidth("15");        
        $this->libro->getActiveSheet()->getColumnDimension('W')->setAutoSize(false);
        $this->libro->getActiveSheet()->getColumnDimension('W')->setWidth("15");        

        $this->libro->getActiveSheet()->setCellValue('P9',"$salcoCajaChica");
        $this->libro->getActiveSheet()->getStyle("P9")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00");
        $this->libro->getActiveSheet()->mergeCells("P9:Q9");   

        $this->putLogo("E1",400,200);
        $this->libro->getActiveSheet()->mergeCells("D5:N5");   
        $this->libro->getActiveSheet()->setCellValue("D5", "REPORTE DE FLUJO DE EFECTIVO DEL MES DE ".$this->getMesAsString(date('m')/1)." DE ".date('Y'));
        $this->libro->getActiveSheet()->getStyle("D5")->applyFromArray( $this->labelBold);
        $this->libro->getActiveSheet()->getStyle("D5")->applyFromArray( $this->centrarTexto);
        $reporteTerminado = new \PHPExcel_Writer_Excel2007( $this->libro);
        // ob_end_clean();
        $reporteTerminado->setPreCalculateFormulas(true);
        $reporteTerminado->save("FlujoEfectivo.xlsx");
   
    }
    public function fillArrayFlujoEfectivo( $params )
    {
        extract( $params );
        foreach ($movimientos as $i => $movimiento) {
            $cargos = array(
                'fecha' => $movimiento['fecha'],
                'tipo' => $tipo
            );
            if ( isset($movimiento['total'])) {
               $cargos['beneficiario'] = $movimiento['descripcion'];
               $cargos['referencia'] = $movimiento['emisor'];
               if ( $movimiento['tipo_movimiento'] == 1) {
                    $cargos['egresos'] = $movimiento['total'];
                    $cargos['ingresos'] = 0;
               }else{
                    $cargos['egresos'] = 0;
                    $cargos['ingresos'] = $movimiento['total'];
               }

            }else{
               $cargos['beneficiario'] = $movimiento['beneficiario'];
               $cargos['referencia'] = $movimiento['referencia'];
                $cargos['egresos'] = $movimiento['egresos'];
                $cargos['ingresos'] = $movimiento['ingresos'];                
            }
            array_push($flujoEfectivo, $cargos );
        }       

        return $flujoEfectivo;
    }    
}


$flujoEfectivo= new FlujoEfectivo;
$flujoEfectivo->preparaReporte();

$configCorreo = array("descripcionDestinatario" => "Flujo de Efectivo",
                                       "mensaje" => "...",
                                       "pathFile" => "FlujoEfectivo.xlsx",
                                       "subject" => "Flujo de Efectivo",
                                       "correos" => array('sestrada@matrix.com.mx'/*, "raulmatrixxx@hotmail.com"*/)
                                     );
$flujoEfectivo->enviarReporte( $configCorreo);

