<?php

require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/Reportes/prepareExcel.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/saldos_bancarios.php";

 header( "Content-type: application/vnd.ms-excel; charset=UTF-8" );
 header("Content-Disposition: attachment; filename=ReporteaArrastreBancario.xlsx");
 header("Pragma: no-cache");
 header("Expires: 0");

class ArrastreBancario extends PrepareExcel 
{
    
    public function preparaReporte()
    {
        $saldosBancarios = new SaldosBancarios;
        $cuentasActivas = $saldosBancarios->getAllcuentas();

        $hoja = 0;
        foreach ($cuentasActivas as $index => $cuenta) {
            $saldos = $saldosBancarios->getSaldoDesgloce( $cuenta['id'], (date('m')/1) );
            $saldosTotales = $saldosBancarios->getSaldosCuenta( $cuenta['id'], (date('m')/1) );
            $i= 11;
            if ( sizeof($saldos) >0 ) {
                
                $this->creaEmptySheet(str_replace(" ","",$cuenta['banco']), $hoja );
                
                $hoja++;

                $this->libro->getActiveSheet()->setCellValue("A8","Número de Cuenta:");
                $this->libro->getActiveSheet()->setCellValue("B8", $cuenta['numero_cuenta']);
                $this->libro->getActiveSheet()->getStyle("A8:B8")->applyFromArray( $this->setColorText('000000', 11) );

                $this->libro->getActiveSheet()->setCellValue('F10', $saldosTotales[0]['totalEgresos']- $saldosTotales[0]['totalIngresos'] + $cuenta['saldo']);
                $this->libro->getActiveSheet()->getStyle("F10")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00");
                $this->libro->getActiveSheet()->getStyle("F10")->applyFromArray( $this->centrarTexto);
                for ($i=1; $i <= 10 ; $i++) { 
                    $this->libro->getActiveSheet()->freezePane('A'.$i);
                }


                $this->libro->getActiveSheet()->setCellValue('A9','FECHA');
                $this->libro->getActiveSheet()->setCellValue('B9','BENEFICIARIO');
                $this->libro->getActiveSheet()->setCellValue('C9','REFERENCIA');
                $this->libro->getActiveSheet()->setCellValue('D9','EGRESO');
                $this->libro->getActiveSheet()->setCellValue('E9','INGRESO');
                $this->libro->getActiveSheet()->setCellValue('F9','SALDO');

                $this->libro->getActiveSheet()->getStyle("A9:F9")->applyFromArray( $this->centrarTexto);
                $this->libro->getActiveSheet()->getStyle("A9:F9")->applyFromArray( $this->labelBold);
                $this->libro->getActiveSheet()->getStyle("A9:F9")->getFill()->applyFromArray( $this->setColorFill("B3E0F0")  );

                $this->libro->getActiveSheet()->mergeCells("A7:F7");
                $this->libro->getActiveSheet()->setCellValue('A7', "SALDO BANCARIO ".$cuenta['banco']);
                $this->libro->getActiveSheet()->getStyle("A7:F7")->getFill()->applyFromArray( $this->setColorFill("000000")  );
                $this->libro->getActiveSheet()->getStyle("A7:F7")->applyFromArray( $this->setColorText('FFFFFF') );
                $this->libro->getActiveSheet()->getStyle("A7:F7")->applyFromArray( $this->centrarTexto);

                $this->putLogo('B1',600,290);
                
                foreach ($saldos  as  $saldo) {
                    $this->libro->getActiveSheet()->setCellValue('A'.$i, $saldo['fecha']);
                    $this->libro->getActiveSheet()->getStyle('A'.$i)->applyFromArray( $this->centrarTexto);
                    $this->libro->getActiveSheet()->setCellValue('B'.$i, strtoupper( $saldo['beneficiario']) );
                    $this->libro->getActiveSheet()->setCellValue('C'.$i, strtoupper( $saldo['referencia']) );
                    $this->libro->getActiveSheet()->setCellValue('D'.$i, $saldo['egresos']);
                    $this->libro->getActiveSheet()->getStyle('D'.$i)->applyFromArray( $this->centrarTexto);
                    $this->libro->getActiveSheet()->setCellValue('E'.$i, $saldo['ingresos']);
                    $this->libro->getActiveSheet()->getStyle('E'.$i)->applyFromArray( $this->centrarTexto);
                    $this->libro->getActiveSheet()->setCellValue('F'.$i, "=SUM(F".($i-1)."-D$i+E$i)" );    
                    $this->libro->getActiveSheet()->getStyle('F'.$i)->applyFromArray( $this->centrarTexto);
                    $this->libro->getActiveSheet()->getStyle('A'.$i.":F$i")->applyFromArray( $this->setColorText('000000', 12) );
                    $this->libro->getActiveSheet()->getStyle("D$i:F$i")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00");
                    $i++;                
                }

            }
            if ( $hoja >= 1) {
                $this->libro->getActiveSheet()->getColumnDimension('A')->setAutoSize(false);
                $this->libro->getActiveSheet()->getColumnDimension('A')->setWidth("30");

                $this->libro->getActiveSheet()->getColumnDimension('B')->setAutoSize(false);
                $this->libro->getActiveSheet()->getColumnDimension('B')->setWidth("60");

                $this->libro->getActiveSheet()->getColumnDimension('C')->setAutoSize(false);
                $this->libro->getActiveSheet()->getColumnDimension('C')->setWidth("60");

                $this->libro->getActiveSheet()->getColumnDimension('D')->setAutoSize(false);
                $this->libro->getActiveSheet()->getColumnDimension('D')->setWidth("20");

                $this->libro->getActiveSheet()->getColumnDimension('E')->setAutoSize(false);
                $this->libro->getActiveSheet()->getColumnDimension('E')->setWidth("20");

                $this->libro->getActiveSheet()->getColumnDimension('F')->setAutoSize(false);
                $this->libro->getActiveSheet()->getColumnDimension('F')->setWidth("20");



                $this->libro->getActiveSheet()->getStyle("A9:F".($i-1) )->applyFromArray( $this->bordes);
            } 
            
        }
        $reporteTerminado = new \PHPExcel_Writer_Excel2007( $this->libro);
        // ob_end_clean();
        $reporteTerminado->setPreCalculateFormulas(true);
        $reporteTerminado->save("ReporteaArrastreBancario.xlsx");
        $reporteTerminado->save("php://output");
    }
}

$arrastre = new ArrastreBancario;
$arrastre->preparaReporte();