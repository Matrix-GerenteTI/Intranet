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
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/con_vendedores.php";
require_once $_SERVER['DOCUMENT_ROOT'].'/intranet/lib/phpmailer/class.phpmailer.php';

class Vendedores
{
    private $reporte;

    private $labelBold = array(  'font' => 
                                                        array( 'bold' => true,
                                                                    'size' => 16,
                                                                    'name' => 'Arial'),
        'alignment' => array(
            'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER
        ) );

        private $bordes = array( 'borders' => array(
                         'allborders' => array(
                                        'style' => \PHPExcel_Style_Border::BORDER_THIN
                                    )
                                )
                            );
        private $hiddenData = array(
                     'font'=>array(
                         'color' => array('rgb' => 'FFFFFF')
                     )
    );                             
        public function __construct()
    {
        $this->reporte = new \PHPExcel();
        $this->reporte->getProperties()->setCreator("Intranet");
        $this->reporte->getProperties()->setLastModifiedBy("Intranet");
        $this->reporte->getProperties()->setTitle("REPORTE DE VENDEDORES");
        $this->reporte->setActiveSheetIndex(0);
        $this->reporte->getActiveSheet()->setTitle("Ventas_x_Vendedores");
    }

    public function preparaDatos($fecha = "")
    {
        $fecha = date("Y-m-d");
        $vendedores = new \Vendedores;
        $listaVentas = ($vendedores->ventasPorVendedor($fecha) );
        $this->seccionaPorVendedor($listaVentas);
    }

    public function seccionaPorVendedor($listaVentas)
    {
        $vendedores = array();
        foreach ($listaVentas as $venta) {
            $splitNombre = explode( "::" , $venta->NOMBREVENDEDOR );
            if ( isset($splitNombre[1]) ) {
                if ( strlen( trim($splitNombre[1]) ) == 0 ) {
                    $vendedores  = $this->agruparArray($vendedores,$venta->NOMBREVENDEDOR,$venta);
                } else {
                    $vendedores  = $this->agruparArray($vendedores,$splitNombre[1],$venta);
                }
            } else {
                 $vendedores  = $this->agruparArray($vendedores,$venta->NOMBREVENDEDOR,$venta);
            }
        }
        $this->crearPlantillaHoja($vendedores);
    }

    function crearPlantillaHoja($vendedores){
        $i = 0;
        $cont= 0;
        $contFam = 0;
        $inicioValores = 3;
        $iniValSub = 3;
        $lastRowFam = 3;
        $chartInicio = 4;
        $chartFin = 24;
        $columnas['ACCESORIO'] = array('J','P');
        var_dump($columnas["ACCESORIO"]);
        $columnas['COLISION'] = array('R','AA');
        $columnas['LLANTA'] = array('AC','AJ');
        $columnas['RIN'] = array('AL','AT');
        $columnas['SERVICIO'] = array('AV','BD');
        $columnas['REFACCION'] = array('BF','BM');
		$columnas['OTRAS'] = array('BO','BW');

        $colFamilias['ACCESORIO'] = array('J','K','L','M','N','O');
        $colFamilias['COLISION'] =   array('R','S','T','U','V','W');
        $colFamilias['LLANTA'] = array('AC','AD','AE','AF','AG','AH');
        $colFamilias['RIN'] = array('AL','AM','AN','AO','AP','AQ');
        $colFamilias ['SERVICIO']= array('AV','AW','AX','AY','AZ','BA');
        $colFamilias ['REFACCION']= array('BF','BG','BH','BI','BJ','BK');
		$colFamilias['OTRAS'] = array('BO','BP','BQ','BR','BS','BT');
        $headerSub = array('SUBFAMILIA','CANTIDAD','TOTAL');
        $contadorHeader = 0;
        $contMerge = 0;                                                                                          
        $iCol = 0;
        $arrayAxis = array();
        $l = 0;

        $this->reporte->getActiveSheet()->setCellValue("ZA1","Familia");
        $this->reporte->getActiveSheet()->setCellValue("ZB1","CantidadFamilia");
        $this->reporte->getActiveSheet()->setCellValue("ZC1","totalFamilia");
        $this->reporte->getActiveSheet()->setCellValue("ZD1","Subfamilia");
        $this->reporte->getActiveSheet()->setCellValue("ZE1","cantSubFam");
        $this->reporte->getActiveSheet()->setCellValue("ZF1","totalSubFam");
        $this->reporte->getActiveSheet()->getStyle("ZA1:ZF1")->applyFromArray($this->hiddenData);

        $etiquetasFam = array( new \PHPExcel_Chart_DataSeriesValues('String', 'Ventas_x_Vendedores!$ZA$1', NULL, 1),
                            new \PHPExcel_Chart_DataSeriesValues('String', 'Ventas_x_Vendedores!$B$1', NULL, 1));

        $etiquetasSub = array( new \PHPExcel_Chart_DataSeriesValues('String', 'Ventas_x_Vendedores!$ZD$1', NULL, 1),
                    new \PHPExcel_Chart_DataSeriesValues('String', 'Ventas_x_Vendedores!$ZE$1', NULL, 1),
                    new \PHPExcel_Chart_DataSeriesValues('String', 'Ventas_x_Vendedores!$ZF$1', NULL, 1)
                );

        $arrayCantidades = array();
        foreach ($vendedores as $key => $vendedor) {
            $arrayCantidades[$key]  = ($vendedor['TOTAL']) ;
        }
        \arsort($arrayCantidades);
        foreach ($arrayCantidades as $vendedor => $totalVendido) {
            $arrayCantidades[$vendedor] = $vendedores[$vendedor];
        }
        $vendedores = $arrayCantidades;
        foreach ($vendedores as $vendedor => $stats) {
            $this->reporte->getActiveSheet()->mergeCells("H".($chartInicio-1).":R".($chartInicio-1));
            $this->reporte->getActiveSheet()->setCellValue("H".($chartInicio-1),$vendedor);
            $this->reporte->getActiveSheet()->getStyle("H".($chartInicio-1))->applyFromArray( $this->labelBold);

            $contFam = 0;
            $iCol = 0;
            $i  = 0;
           
            
            //ENCABEZADO DE TABLA DE CONTROL DE VENTAS POR VENDEDOR
            $this->reporte->getActiveSheet()->setCellValue("B".($chartFin+2),"FAMILIA");
            $this->reporte->getActiveSheet()->mergeCells("B".($chartFin+2).":C".($chartFin+2));

            $this->reporte->getActiveSheet()->setCellValue("D".($chartFin+2),"CANTIDAD VENIDA");
            $this->reporte->getActiveSheet()->mergeCells("D".($chartFin+2).":E".($chartFin+2));

            $this->reporte->getActiveSheet()->setCellValue("F".($chartFin+2),"TOTAL DE VENTAS");
            $this->reporte->getActiveSheet()->mergeCells("F".($chartFin+2).":G".($chartFin+2));
            $this->reporte->getActiveSheet()->getStyle("B".($chartFin+2).":G".($chartFin+2) )->applyFromArray( $this->bordes);

            ksort($stats['FAMILIA']);
            foreach ($stats['FAMILIA'] as $familia => $contenido) {
                    $idTabSub = $contadorHeader = 0;
                    $cont = 0;
                     $cuetaProductos = 0;
                     $l = 0;
                 array_push($arrayAxis,'Ventas_x_Vendedores!$ZA$'.$inicioValores.':$ZA$'.$inicioValores );

                 //CREANDO TABLA DE TOP 10 DE VENTAS PARA CADA SUBFAMILIA
				 
                 $columnaFamilas = $colFamilias[$familia];
				 var_dump($familia); echo"<br>";
                 foreach ($columnaFamilas as $idx => $columna) {
                     if ( $contMerge == 1) {
                         $this->reporte->getActiveSheet()->mergeCells($columnaFamilas[$idx-1].''.($chartFin+2).':'.$columna.''.($chartFin+2) );
                         $this->reporte->getActiveSheet()->setCellValue($columnaFamilas[$idx-1].''.($chartFin+2), $headerSub[$contadorHeader]);
                         
                         $contMerge = 0;
                         $contadorHeader++;
                     } else {
                         $contMerge++;
                     }   
                 }
                 $this->reporte->getActiveSheet()->getStyle($columnaFamilas[0].''.($chartFin + 2 ).':'.$columnaFamilas[5].''.($chartFin + 2 )  )->applyFromArray( $this->bordes);

                array_multisort($contenido['SUBFAMILIA'],SORT_DESC);
                $this->reporte->getActiveSheet()->setCellValue("ZA$inicioValores",$familia);
                $this->reporte->getActiveSheet()->setCellValue("ZB$inicioValores",$contenido['CANTIDAD']);
                $this->reporte->getActiveSheet()->setCellValue("ZC$inicioValores",$contenido['TOTAL']);
                 $this->reporte->getActiveSheet()->getStyle("ZA".$inicioValores.":ZF".$inicioValores)->applyFromArray($this->hiddenData);


                foreach ($contenido['SUBFAMILIA'] as $subfamilia => $info) {
                    if ( $cuetaProductos <= 10) {
                        $this->reporte->getActiveSheet()->setCellValue("ZD$iniValSub",$subfamilia);
                        $this->reporte->getActiveSheet()->setCellValue("ZE$iniValSub",$info['CANTIDAD']);
                        $this->reporte->getActiveSheet()->setCellValue("ZF$iniValSub",$info['TOTAL']);
                         $this->reporte->getActiveSheet()->getStyle("ZD".$iniValSub.":ZF".$iniValSub)->applyFromArray($this->hiddenData);
                        //  $this->reporte->getActiveSheet()->setCellValue($letraColumna[$l]."".($chartFin+3),$info['CANTIDAD']);

                        //AGREGANDO VALORES A LAS TABLAS TOP 10 DE SUBFAMILIAS
                        $columnaFamilas = $colFamilias[$familia];

                                $this->reporte->getActiveSheet()->mergeCells($columnaFamilas[0].''.($chartFin + 3 + $cuetaProductos).':'.$columnaFamilas[1].''.($chartFin + 3 + $cuetaProductos) );
                                $this->reporte->getActiveSheet()->setCellValue($columnaFamilas[0].''.($chartFin + 3 + $cuetaProductos), $subfamilia);

                                $this->reporte->getActiveSheet()->setCellValue($columnaFamilas[2].''.($chartFin + 3 + $cuetaProductos), $info['CANTIDAD']);
                                $this->reporte->getActiveSheet()->mergeCells($columnaFamilas[2].''.($chartFin + 3 + $cuetaProductos).':'.$columnaFamilas[3].''.($chartFin + 3 + $cuetaProductos) );

                                $this->reporte->getActiveSheet()->setCellValue($columnaFamilas[4].''.($chartFin + 3 + $cuetaProductos), $info['TOTAL']);
                                $this->reporte->getActiveSheet()->mergeCells($columnaFamilas[4].''.($chartFin + 3 + $cuetaProductos).':'.$columnaFamilas[5].''.($chartFin + 3 + $cuetaProductos) );    
                        $iniValSub++;
                        $cont++;
                        $cuetaProductos++;
                        $l++;
                    }
                    $this->reporte->getActiveSheet()->getStyle($columnaFamilas[4].''.($chartFin + 3 ).':'.$columnaFamilas[5].''.($chartFin + 2 + $cuetaProductos))->getNumberFormat()->setFormatCode("$#,##0;-$#,##0");
                    $this->reporte->getActiveSheet()->getStyle($columnaFamilas[0].''.($chartFin + 3 ).':'.$columnaFamilas[5].''.($chartFin + 2 + $cuetaProductos)  )->applyFromArray( $this->bordes);
                }

            //DE ACUERDO A LAS FAMILIAS
            $init = $iniValSub - $cont;
            $fin = $iniValSub -1;

            $xAxis = array( new \PHPExcel_Chart_DataSeriesValues('String', 'Ventas_x_Vendedores!$ZD$'.($init).':$ZD$'.($iniValSub-1), NULL, 100 ));         
            $valores = array( new \PHPExcel_Chart_DataSeriesValues('Number', 'Ventas_x_Vendedores!$ZE$'.($init).':$ZE$'.($iniValSub-1), NULL, ( $cont) ));

            $dataSeriesChart =new \PHPExcel_Chart_DataSeries(
                \PHPExcel_Chart_DataSeries::TYPE_BARCHART,
                \PHPExcel_Chart_DataSeries::GROUPING_CLUSTERED,
                range(0, count($valores)-1),
                $etiquetasSub,
                $xAxis,
                $valores
                );

                $dataSeriesChart->setPlotDirection(\PHPExcel_Chart_DataSeries::DIRECTION_HORIZONTAL);
                $layout1 = new \PHPExcel_Chart_Layout();
                $layout1->setShowVal(TRUE);
                $layout1->setShowPercent(TRUE);

                $plotArea = new \PHPExcel_Chart_PlotArea(null, array($dataSeriesChart));
                $legend=new \PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);
                $title = new \PHPExcel_Chart_Title($familia);
                $grafica= new \PHPExcel_Chart(
                            'VENTASFAM',
                            $title,
                            $legend,
                            $plotArea,
                            true,
                            0,
                            NULL, 
                            NULL);
                    
                $grafica->setTopLeftPosition($columnas[$familia][0]."".$chartInicio);
                $grafica->setBottomRightPosition($columnas[$familia][1]."".$chartFin);
                $this->reporte->getActiveSheet()->addChart( $grafica);
                
                
                //AGREGANDO LOS VALORES A LA TABLA DE VENTAS POR VENDEDOR
                $this->reporte->getActiveSheet()->mergeCells("B".($chartFin+ 3 + $i).":C".($chartFin+ 3 + $i));
                $this->reporte->getActiveSheet()->setCellValue("B".($chartFin+ 3 + $i),$familia);
                $this->reporte->getActiveSheet()->getStyle("B".($chartFin+ 3 + $i))->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

                $this->reporte->getActiveSheet()->mergeCells("D".($chartFin+ 3 + $i).":E".($chartFin+ 3 + $i));
                $this->reporte->getActiveSheet()->setCellValue("D".($chartFin+ 3 + $i),$contenido['CANTIDAD']);
                $this->reporte->getActiveSheet()->getStyle("D".($chartFin+ 3 + $i))->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

                $this->reporte->getActiveSheet()->mergeCells("F".($chartFin+ 3 + $i).":G".($chartFin+ 3 + $i));
                $this->reporte->getActiveSheet()->setCellValue("F".($chartFin+ 3 + $i),$contenido['TOTAL']);
                $this->reporte->getActiveSheet()->getStyle("F".($chartFin+ 3 + $i))->getNumberFormat()->setFormatCode("$#,##0;-$#,##0");
                $this->reporte->getActiveSheet()->getStyle("B".($chartFin+ 3 + $i).":G".($chartFin+ 3 + $i) )->applyFromArray( $this->bordes);

                $i++;
               $inicioValores++;
               $contFam++;
            }


            $xAxis = array( new \PHPExcel_Chart_DataSeriesValues('String', 'Ventas_x_Vendedores!$ZA$'.($inicioValores-$contFam).':$ZA$'.($inicioValores-1), NULL, 5 ));         
            $valores = array( new \PHPExcel_Chart_DataSeriesValues('Number', 'Ventas_x_Vendedores!$ZB$'.($inicioValores-$contFam).':$ZB$'.($inicioValores-1), NULL, ( $contFam) ));
            
            $dataSeriesChart =new \PHPExcel_Chart_DataSeries(
                \PHPExcel_Chart_DataSeries::TYPE_PIECHART,
                NULL,
                range(0, count($valores)-1),
                $etiquetasFam,
                $xAxis,
                $valores
                );

                $layout1 = new \PHPExcel_Chart_Layout();
                // $layout1->setShowVal(TRUE);
                $layout1->setShowPercent(TRUE);

            $plotArea = new \PHPExcel_Chart_PlotArea($layout1, array($dataSeriesChart));
            $legend=new \PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);
            $title = new \PHPExcel_Chart_Title('Ventas');
            $grafica= new \PHPExcel_Chart(
                        'VENTASFAM',
                        $title,
                        $legend,
                        $plotArea,
                        true,
                        0,
                        NULL, 
                        NULL);
            $grafica->setTopLeftPosition("B".$chartInicio);
            $grafica->setBottomRightPosition("H".$chartFin);
            $this->reporte->getActiveSheet()->addChart( $grafica);
            
            $this->reporte->getActiveSheet()->setCellValue("E".($chartFin+ 4 + $i),"TOTAL");
            $this->reporte->getActiveSheet()->mergeCells("F".($chartFin+ 4 + $i).":G".($chartFin+ 4 + $i));
            $this->reporte->getActiveSheet()->setCellValue("F".($chartFin+ 4 + $i),"=SUM(F".($chartFin+ -3 + $i).":F".($chartFin+ 2 + $i).")");
            $this->reporte->getActiveSheet()->getStyle("F".($chartFin+ 4 + $i))->getNumberFormat()->setFormatCode("$#,##0;-$#,##0");
            $chartInicio = $chartFin +17;
            $chartFin = $chartInicio+20;
        }
        $reporteTerminado = new \PHPExcel_Writer_Excel2007( $this->reporte);
        $reporteTerminado->setIncludeCharts(TRUE);
        
        $reporteTerminado->save("ReporteVendedores.xlsx");
    }

    public function agruparArray( $vendedores,$rootName, $venta)
    {
		if( $venta->FAMILIA != "ANTICIPOS" AND $venta->FAMILIA != ''){
        if ( isset($vendedores[$rootName] ) ) {//Se busca al vendedor
			
				if ( isset($vendedores[$rootName]['FAMILIA'][$venta->FAMILIA] ) ) { //Se busca a la familia
					$vendedores[$rootName]['FAMILIA'][$venta->FAMILIA]['CANTIDAD'] += $venta->VENDIDO;
					$vendedores[$rootName]['FAMILIA'][$venta->FAMILIA]['TOTAL'] += ($venta->IMPORTELINEA + $venta->DETIVA);
					$vendedores[$rootName]['TOTAL'] += ($venta->IMPORTELINEA + $venta->DETIVA);
					if( isset($vendedores[$rootName]['FAMILIA'][$venta->FAMILIA]['SUBFAMILIA'][$venta->SUBFAMILIA]) ){
						$vendedores[$rootName]['FAMILIA'][$venta->FAMILIA]['SUBFAMILIA'][$venta->SUBFAMILIA]['CANTIDAD'] += $venta->VENDIDO;
						$vendedores[$rootName]['FAMILIA'][$venta->FAMILIA]['SUBFAMILIA'][$venta->SUBFAMILIA]['TOTAL'] += ($venta->IMPORTELINEA + $venta->DETIVA);
						
					}
					else{
						$vendedores[$rootName]['FAMILIA'][$venta->FAMILIA]['SUBFAMILIA'][$venta->SUBFAMILIA]['CANTIDAD'] = $venta->VENDIDO;
						$vendedores[$rootName]['FAMILIA'][$venta->FAMILIA]['SUBFAMILIA'][$venta->SUBFAMILIA]['TOTAL'] = ($venta->IMPORTELINEA + $venta->DETIVA);
					}
				}else{
					$vendedores[$rootName]['FAMILIA'][$venta->FAMILIA]['CANTIDAD'] = $venta->VENDIDO;
					$vendedores[$rootName]['FAMILIA'][$venta->FAMILIA]['TOTAL'] = ($venta->IMPORTELINEA + $venta->DETIVA);
					$vendedores[$rootName]['FAMILIA'][$venta->FAMILIA]['SUBFAMILIA'][$venta->SUBFAMILIA]['CANTIDAD'] = $venta->VENDIDO;
					$vendedores[$rootName]['FAMILIA'][$venta->FAMILIA]['SUBFAMILIA'][$venta->SUBFAMILIA]['TOTAL'] = ($venta->IMPORTELINEA + $venta->DETIVA);
					$vendedores[$rootName]['TOTAL'] += ($venta->IMPORTELINEA + $venta->DETIVA);
				}
			   
		}	
			else{
					$vendedores[$rootName]['FAMILIA'][$venta->FAMILIA]['CANTIDAD'] = $venta->VENDIDO;
					$vendedores[$rootName]['FAMILIA'][$venta->FAMILIA]['TOTAL'] = ($venta->IMPORTELINEA + $venta->DETIVA);
					$vendedores[$rootName]['FAMILIA'][$venta->FAMILIA]['SUBFAMILIA'][$venta->SUBFAMILIA]['CANTIDAD'] = $venta->VENDIDO;
					$vendedores[$rootName]['FAMILIA'][$venta->FAMILIA]['SUBFAMILIA'][$venta->SUBFAMILIA]['TOTAL'] = ($venta->IMPORTELINEA + $venta->DETIVA);
					$vendedores[$rootName]['TOTAL'] = ($venta->IMPORTELINEA + $venta->DETIVA);
			}
        }
        return $vendedores;
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
        $emailsender->FromName = "Reporte de Vendedores";

        $emailsender->Subject ="Reporte de Vendedores";
        $emailsender->Body = "<p>...</p>";

        $emailsender->AltBody = "...";

        if ( is_file("ReporteVendedores.xlsx") ) {
            $emailsender->AddAttachment("ReporteVendedores.xlsx");
        }
        //sestrada
        $emailsender->AddAddress("sestrada@matrix.com.mx");
		$emailsender->AddAddress("raulmatrixxx@hotmail.com");
		
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

$vendedores = new \Reportes\Vendedores;
$vendedores->preparaDatos();
$vendedores->enviarReporte();