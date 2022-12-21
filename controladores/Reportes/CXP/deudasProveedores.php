<?php


require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/Reportes/prepareExcel.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/CuentasPorPagar.php";
header('Content-type: application/vnd.ms-excel');


header('Content-Disposition: attachment; filename="cxp.xlsx"');

class DeudasProveedores extends PrepareExcel
{

    public function __construct( )
    {
        parent::__construct();
    }

    public function getFacturasPorSaldar( $mes , $anio)
    {
     
        $modeloCxP = new CuentasPorPagar;

        $listaFacturas = $modeloCxP->getFacturasPorSaldar();
        $listaProveedores = [];
        foreach ($listaFacturas as $i => $factura) {
            $factura->CF1 = utf8_encode( $factura->CF1);
            $listaProveedores[ $factura->CODIGO ]['proveedor'] = $factura->CF1;
            $listaProveedores[ $factura->CODIGO ]['id'] = $factura->ID;
            if ( $factura->NUMFACT != -1 ) {
                    $fechaEmision = new DateTime( $factura->FECHAEMISION);
                    $fechaVencimiento = new DateTime( $factura->FECHAVTO);                    
                    $fechaActual = new DateTime( date("Y-m-d") ) ;         
                    $diasDeCredito = $fechaVencimiento->diff( $fechaEmision )->format("%a");
                    $diasTranscurridos = $fechaActual->diff( $fechaEmision )->format("%a")   ;
            // agregando los dias de vencimiento y el estado de la factura                    
                    $diasVencimiento = $diasTranscurridos - $diasDeCredito;
                    $factura->DIASVENCIMIENTO = $diasVencimiento;
                    $factura->STATUS = $diasVencimiento < 0 ? "S/VENCER" : "VENCIDO";

                if( !isset( $listaProveedores[$factura->CODIGO]['total_docto'] ) ){
                    $listaProveedores[$factura->CODIGO]['total_docto'] = $factura->IMPORTE;
                    $listaProveedores[ $factura->CODIGO ]['facturas'] = [ $factura ];
                }else{
                    $listaProveedores[$factura->CODIGO]['total_docto'] += $factura->IMPORTE;
                    array_push( $listaProveedores[ $factura->CODIGO ]['facturas'], $factura );
                }
            }
        }

        $listaProveedores =  $this->sortImportesYVencimientos( $listaProveedores );
        $listaProveedores = $this->setPagosAProveedor( $listaProveedores, $mes, $anio );

        // echo json_encode( $listaProveedores );
        return ( $listaProveedores );


    }

    public function setPagosAProveedor( $proveedores , $mes , $anio )
    {
        $modeloCxP = new CuentasPorPagar;
        $pagos = $modeloCxP->getPagosAProveedores( $mes , $anio);

        foreach ($pagos as $i => $abono) {
            $pagos[$i]->PYM_NOMBRE = utf8_encode( $abono->PYM_NOMBRE );
            $pagos[$i]->COMENTARIO = utf8_encode( $abono->COMENTARIO );
            if (!isset( $proveedores[$abono->ID_PROSPECTO."_"]['total_pagos'] ) ) {
                $proveedores[$abono->ID_PROSPECTO."_"]['total_pagos'] = $abono->IMPORTECOBRO;
                $proveedores[$abono->ID_PROSPECTO."_"]['abonos'] = [ $abono ];
                // echo ""
            }else{
                $proveedores[$abono->ID_PROSPECTO."_"]['total_pagos'] += $abono->IMPORTECOBRO;
                array_push( $proveedores[$abono->ID_PROSPECTO."_"]['abonos'] , $abono);
            }
            if (!isset($proveedores[$abono->ID_PROSPECTO."_"]['facturas'] ) ) {
                // echo "mames";
                $proveedores[$abono->ID_PROSPECTO."_"]['facturas'] = [];
                $proveedores[$abono->ID_PROSPECTO."_"]['total_docto'] = 0;
                $proveedores[$abono->ID_PROSPECTO."_"]['proveedor'] = $pagos[$i]->PYM_NOMBRE;
            }
        }
        
        return $proveedores;
    }
    public function sortImportesYVencimientos( $data )
    {
        $proveedorAnt = [];
        $index =[];
        $proveedores = [];

        foreach ($data as  $proveedor) {
            array_push(  $proveedores , $proveedor );
        }

        for($i = 0 ;$i< sizeof( $proveedores ) ; $i++ ) {
            for($j = 0 ; $j< sizeof( $proveedores ) ; $j++ ) {
                // if ( in_array( $j , $index )) {
                    
                //     continue;
                // }
                
                if ( $proveedores[$i]['total_docto'] > $proveedores[$j]['total_docto']) {
                    $proveedorAnt = $proveedores[$i];
                    $proveedores[$i] = $proveedores[$j];
                    $proveedores[$j] = $proveedorAnt;

                }
            }
        }
        // reacomodando los indices
        $newData = [];

        foreach ($proveedores as $i => $proveedor) {
            $newData[$proveedor['id']."_" ] = $proveedor;
            // echo "<br>".$newData[$proveedor['id'] ]['total_docto']." -----  ".$proveedor['total_docto'];
        }
        // $data = null;
        $proveedores = $newData;
        
        //ordenando las facturas de menor dia de vencimiento al mayor
        $factAnterior = [];
        foreach ($proveedores as $i => $proveedor) {
            foreach ($proveedores[$i]['facturas'] as $j => $factura) {
                foreach ($proveedores[$i]['facturas'] as $k => $facturaIterador) {
                    if ( $factura->DIASVENCIMIENTO < $facturaIterador->DIASVENCIMIENTO) {
                        $factAnterior = $proveedores[$i]['facturas'][$j];
                        $proveedores[$i]['facturas'][$j] = $facturaIterador;
                        $proveedores[$i]['facturas'][$k] = $factAnterior;
                    }
                }
            }
        }
        //ordenando facturas por fecha de vencimiento más proxima
        $factAnterior = [];
        foreach ($proveedores as $i => $proveedor) {
            foreach ($proveedores[$i]['facturas'] as $j => $factura) {
                foreach ($proveedores[$i]['facturas'] as $k => $facturaIterador) {
                    if ( ( $factura->DIASVENCIMIENTO > $facturaIterador->DIASVENCIMIENTO) && $factura->DIASVENCIMIENTO  < 0  ) {
                        $factAnterior = $proveedores[$i]['facturas'][$j];
                        $proveedores[$i]['facturas'][$j] = $facturaIterador;
                        $proveedores[$i]['facturas'][$k] = $factAnterior;
                    }
                }
            }
        }        

        return $proveedores;
    }

    public function generaReporte( $mes , $anio )
    {
        $this->creaEmptySheet("CXP", 0 );
        $row = 9;
        $listaFacturas = $this->getFacturasPorSaldar( $mes , $anio );


        $this->putLogo( "B2", 240,300);
        $this->libro->getActiveSheet()->setCellValue("B6","REPORTE DE CUENTAS POR PAGAR");
        $this->libro->getActiveSheet()->mergeCells("B6:E6");
        $this->libro->getActiveSheet()->getStyle("B6")->applyFromArray( $this->centrarTexto );
        $this->libro->getActiveSheet()->getStyle("B6")->applyFromArray( $this->labelBold);
        $this->libro->getActiveSheet()->getStyle("B6")->applyFromArray( $this->setColorText("000000",14) );

        $this->libro->getActiveSheet()->setCellValue("A7","PROVEEDORES");
        $this->libro->getActiveSheet()->getStyle("A7")->applyFromArray( $this->labelBold);
        $this->libro->getActiveSheet()->getStyle("A7")->applyFromArray( $this->setColorText("000000",12) );
        $this->libro->getActiveSheet()->setCellValue("A8","FACTURA");
        $this->libro->getActiveSheet()->setCellValue("B8","FEC. EMISION");
        $this->libro->getActiveSheet()->setCellValue("C8","TFEC. VTO.");        
        $this->libro->getActiveSheet()->setCellValue("D8","DIAS VENC.");
        $this->libro->getActiveSheet()->setCellValue("E8","TOTAL DOC.");
        $this->libro->getActiveSheet()->setCellValue("F8","ESTATUS");
        $this->libro->getActiveSheet()->getStyle("A8:F8")->applyFromArray( $this->labelBold);
        $this->libro->getActiveSheet()->getStyle("A8:F8")->applyFromArray( $this->centrarTexto );
        $this->libro->getActiveSheet()->getStyle("A8:F8")->getFill()->applyFromArray( $this->setColorFill("DF013A") );
        $this->libro->getActiveSheet()->getStyle("A8:F8")->applyFromArray( $this->setColorText("ffffff",12) );

        $proveedorAnt = 0;
        foreach ( $listaFacturas as $i => $proveedor) {

            $this->libro->getActiveSheet()->setCellValue("A$row", $proveedor['proveedor']);
            $this->libro->getActiveSheet()->setCellValue("E$row", $proveedor['total_docto']);
            $this->libro->getActiveSheet()->getStyle("E$row")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00");
            $this->libro->getActiveSheet()->getStyle("A$row:F$row")->applyFromArray( $this->labelBold);
            $this->libro->getActiveSheet()->getStyle("B$row:F$row")->applyFromArray( $this->centrarTexto );
            $this->libro->getActiveSheet()->getStyle("A$row:F$row")->getFill()->applyFromArray( $this->setColorFill("455a64") );
            $this->libro->getActiveSheet()->getStyle("A$row:F$row")->applyFromArray( $this->setColorText("ffffff",12) );            
            $row++;
            
            foreach ($proveedor['facturas'] as $j => $factura) {
                $this->libro->getActiveSheet()->setCellValue("A$row", $factura->NUMFACT);
                $this->libro->getActiveSheet()->setCellValue("B$row", date("d/m/Y", strtotime($factura->FECHAEMISION) ) );
                $this->libro->getActiveSheet()->setCellValue("C$row", date("d/m/Y", strtotime( $factura->FECHAVTO) ) );
                $this->libro->getActiveSheet()->setCellValue("D$row", $factura->DIASVENCIMIENTO);
                $this->libro->getActiveSheet()->setCellValue("E$row", $factura->IMPORTE);
                $this->libro->getActiveSheet()->getStyle("E$row")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00");
                $this->libro->getActiveSheet()->setCellValue("F$row", $factura->STATUS);
                $row++;
            }
        }
            $this->libro->getActiveSheet()->getColumnDimension("A")->setAutoSize( true );
            $this->libro->getActiveSheet()->getColumnDimension("B")->setAutoSize( true );
            $this->libro->getActiveSheet()->getColumnDimension("C")->setAutoSize( true );
            $this->libro->getActiveSheet()->getColumnDimension("D")->setAutoSize( true );
            $this->libro->getActiveSheet()->getColumnDimension("E")->setAutoSize( true );

        $this->creaEmptySheet("Pagos", 1);
        $row =9;
        foreach ($listaFacturas as $i => $proveedor) {
            $this->libro->getActiveSheet()->setCellValue("A$row", $proveedor['proveedor']);
            $this->libro->getActiveSheet()->setCellValue("E$row", isset( $proveedor['total_pagos'] ) ? $proveedor['total_pagos'] : 0 );
            $this->libro->getActiveSheet()->getStyle("E$row")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00");
            $this->libro->getActiveSheet()->getStyle("A$row:F$row")->applyFromArray( $this->labelBold);
            $this->libro->getActiveSheet()->getStyle("B$row:F$row")->applyFromArray( $this->centrarTexto );
            $this->libro->getActiveSheet()->getStyle("A$row:F$row")->getFill()->applyFromArray( $this->setColorFill("455a64") );
            $this->libro->getActiveSheet()->getStyle("A$row:F$row")->applyFromArray( $this->setColorText("ffffff",12) );     
            $row++;
            
            if ( !isset( $proveedor['abonos'] ) ) {
                continue;
            }
            foreach ($proveedor['abonos'] as $j => $abono) {
                $this->libro->getActiveSheet()->setCellValue("A$row", $abono->COMENTARIO);
                $this->libro->getActiveSheet()->setCellValue("E$row", date("d/m/Y", strtotime($abono->FECHAMOVI) ) );
                $this->libro->getActiveSheet()->setCellValue("F$row", $abono->IMPORTECOBRO);                
                $this->libro->getActiveSheet()->getStyle("F$row")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00");
                $row++;
            }                               
        }
         $reporteTerminado = new \PHPExcel_Writer_Excel2007( $this->libro);
        $reporteTerminado->setPreCalculateFormulas(true);
        $reporteTerminado->setIncludeCharts(TRUE);
         $reporteTerminado->save("php://output");
        $ubicacion = "http://matrix.com.mx/intranet/controladores/reportes/cxp.xlsx";
        echo "<a href='$ubicacion'>Descargar</a>";        
    }
}


$cxp = new DeudasProveedores;
$cxp->generaReporte( date('m') , date('Y') );