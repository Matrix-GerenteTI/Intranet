<?php
namespace Reportes;

if(!isset($_SESSION)){ 
    session_start(); 
}
//$_SERVER['DOCUMENT_ROOT']."/intranet/controladores/sesiones.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/sesiones.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/lib/PHPExcel.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/lib/PHPExcel/Writer/Excel2007.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/con_edosfinancieros.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/sesiones.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/Ventas.php";
require_once $_SERVER['DOCUMENT_ROOT'].'/intranet/lib/phpmailer/class.phpmailer.php';


class VentasColision
{
    private $reporte;

    private $hiddenData = array(
                'font'=>array(
                    'color' => array('rgb' => 'FFFFFF')
                )
    );    

    private $bordes = array( 'borders' => array(
                    'allborders' => array(
                                'style' => \PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    );

    public function __construct()
    {
        $this->reporte = new \PHPExcel();
        $this->reporte->getProperties()->setCreator("Intranet");
        $this->reporte->getProperties()->setLastModifiedBy("Intranet");
        $this->reporte->getProperties()->setTitle("Reporte de Ventas de Colision");
        $this->reporte->setActiveSheetIndex(0);
        $this->reporte->getActiveSheet()->setTitle("Colision");
    }

    public function preparaDatos()
    {
        $ventas = new \Ventas;
        $colision = $ventas->getVentasSemanal();
        $this->crearGrafica( $this->agruparMarcas($colision) );
    }
    public function crearGrafica( $datos)
    {
        $this->reporte->getActiveSheet()->setCellValue("A1","MARCA");
        $this->reporte->getActiveSheet()->setCellValue("B1","TOTAL");

        $etiquetas= array( new \PHPExcel_Chart_DataSeriesValues('String', 'Colision!$A$1', NULL, 1),
                    new \PHPExcel_Chart_DataSeriesValues('String', 'Colision!$B$1', NULL, 1));

        $i = 2;
        array_multisort($datos,SORT_DESC);
        foreach ($datos as $marca => $monto) {
            $this->reporte->getActiveSheet()->setCellValue("A$i",$marca);
            $this->reporte->getActiveSheet()->setCellValue("B$i", $monto['TOTAL']);
            $this->reporte->getActiveSheet()->getStyle("A$i:C$i")->getNumberFormat()->setFormatCode("$#,##0;-$#,##0");
            $i++;
        }
        $this->reporte->getActiveSheet()->getStyle( "A1:B".($i-1) )->applyFromArray($this->bordes);
         $xAxis = array( new \PHPExcel_Chart_DataSeriesValues('String', 'Colision!$A$2:$A$'.($i-1), NULL, ($i-1) ));         
            $valores = array( new \PHPExcel_Chart_DataSeriesValues('Number', 'Colision!$B$2:$B$'.($i-1), NULL, ( $i-1) ));
            
            $dataSeriesChart =new \PHPExcel_Chart_DataSeries(
                \PHPExcel_Chart_DataSeries::TYPE_BARCHART,
                \PHPExcel_Chart_DataSeries::GROUPING_CLUSTERED,
                range(0, count($valores)-1),
                $etiquetas,
                $xAxis,
                $valores
                );

                $layout1 = new \PHPExcel_Chart_Layout();
                $layout1->setShowVal(TRUE);
                $layout1->setShowPercent(TRUE);

                $plotArea = new \PHPExcel_Chart_PlotArea(null, array($dataSeriesChart));
                $legend=new \PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);
                $title = new \PHPExcel_Chart_Title('Ventas de Colisión');
                $grafica= new \PHPExcel_Chart(
                            'VENTASFAM',
                            $title,
                            $legend,
                            $plotArea,
                            true,
                            0,
                            NULL, 
                            NULL);
                                
                $grafica->setTopLeftPosition("D1");
                $grafica->setBottomRightPosition("L21");
                $this->reporte->getActiveSheet()->addChart( $grafica);
                
        $reporteTerminado = new \PHPExcel_Writer_Excel2007( $this->reporte);
        $reporteTerminado->setIncludeCharts(TRUE);
        
        $reporteTerminado->save("ReporteColision.xlsx");
    }
    public function agruparMarcas( $ventasColision)
    {
        $proveedores = array();

        foreach ($ventasColision as $venta) {
            
            if ( isset($proveedores[$venta->MARCA]) ) {
                $proveedores[$venta->MARCA]['TOTAL'] += ($venta->IMPORTELINEA + $venta->DETIVA);
            } else {
                $proveedores[$venta->MARCA]['TOTAL'] = ($venta->IMPORTELINEA + $venta->DETIVA);
            }
            
        }

        return $proveedores;
    }
	public function enviarReporte( )
    {
        $emailsender = new \phpmailer;
        $emailsender->isSMTP();
        $emailsender->SMTPDebug = 1;
        $emailsender->SMTPAuth = true;
        $emailsender->Port = 587;

        $emailsender->Host = 'mail.matrix.com.mx';
        $emailsender->Username = "no-responder@matrix.com.mx";
        $emailsender->Password = "M@tr1x2017";

        $emailsender->From ="no-responder@matrix.com.mx";
        $emailsender->FromName = "Reporte de Ventas Colision";

        $emailsender->Subject ="Reporte de Ventas Colision";
        $emailsender->Body = "<p>...</p>";

        $emailsender->AltBody = "...";

        if ( is_file("ReporteColision.xlsx") ) {
            $emailsender->AddAttachment("ReporteColision.xlsx");
        }
        //sestrada
        $emailsender->AddAddress("sestrada@matrix.com.mx");
		$emailsender->AddAddress("raulmatrixxx@hotmail.com");
		$emailsender->AddAddress("luisimatrix@hotmail.com");
        $emailsender->AddAddress("director@matrix.com.mx");
        $emailsender->AddAddress("gerente_auditoria@matrix.com.mx");
        $emailsender->AddAddress("gerenteti@matrix.com.mx");
		
        $statusEnvio = $emailsender->Send();

        if ( $emailsender->ErrorInfo == "SMTP Error: Data not accepted") {
            $statusEnvio = true;
        } 

        if ( !$statusEnvio ) {
             echo "[".$emailsender->ErrorInfo."] - Problemas enviando correo electrónico a ";
        } else {
            echo "Enviado";
        }
    }
}

$colision = new \Reportes\VentasColision;

if(date("D") == "Mon") {
	$colision->preparaDatos();
	$colision->enviarReporte();
}