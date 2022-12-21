<?php

require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/Reportes/prepareExcel.php";


class AcuseDeAsistencia extends PrepareExcel 
{
    
    public function __construct()
    {
        parent::__construct();
        $this->libro->getProperties()->setTitle('Acuse de asistencia'); 
    }

    public function generaHojaAcuse( $empleadoAsistencia )
    {
        
        //Verificando que si es un arreglo de asistencias o un unico trbajador , en caso de que sea un item se ingresa dentro de un arreglo para que éste sea el  1er elemento
        $empleadoAsistencia = isset( $empleadoAsistencia[0] )  ? $empleadoAsistencia : [ $empleadoAsistencia ];

        foreach ($empleadoAsistencia as $i => $empleado) {
            $this->creaEmptySheet( substr( $empleado['nombre'], 0 ,12) , $i );

            $this->putLogo("B1", 200,200);

            $this->libro->getActiveSheet()->setCellValue("B12", 'FECHA');
            $this->libro->getActiveSheet()->setCellValue("C12", 'TIPO ACCION');
            $this->libro->getActiveSheet()->setCellValue("D12", 'H. ENTRADA');
            $this->libro->getActiveSheet()->setCellValue("F12", 'SANCIÓN');
            $this->libro->getActiveSheet()->mergeCells("D12:E12");
            
            //AGRUPAMOS LOS DATOS  DE LAS FECHAS Y LAS ORDENAMOS
            $historialAsistencia = [];
            //SE OBTIENEN LAS ASISTENCIAS 
            foreach ($empleado['registroAsistencia'] as $j => $asistencia) {
                $historialAsistencia[ $asistencia['fecha'] ] = $asistencia['hora'];
            }
            //Obteniendo las faltas
            if ( isset($empleado['diasFaltas'] ) ) {
                foreach ( $empleado['diasFaltas'] as $j => $falta) {
                    $historialAsistencia[ $falta['fecha'] ] = "FALTA&".$falta['monto'];
                }
            }

            if ( isset( $empleado['diasRetardo'] ) ) {
                //Agregando el indicador de retardo al arreglo
                foreach ($empleado['diasRetardo'] as $j => $retardo) {
                    $historialAsistencia[ $retardo['fecha'] ] .=  "@RETARDO&".$retardo['monto'] ;
                }
            }

            //AGREGANDO LOS DATOS DEL TRABAJADOR COMO  ENCABEZADO
            $this->libro->getActiveSheet()->setCellValue("A9", "NOMBRE");
            $this->libro->getActiveSheet()->setCellValue("B9", $empleado['nombre']);
            $this->libro->getActiveSheet()->mergeCells("B9:E9");
            $this->libro->getActiveSheet()->getStyle("A9:A10")->applyFromArray( $this->labelBold);   

            $this->libro->getActiveSheet()->setCellValue("A10","PUESTO:");
            $this->libro->getActiveSheet()->setCellValue("B10", $empleado['puesto']);

            $this->libro->getActiveSheet()->setCellValue("D10","SUCURSAL:");
            $this->libro->getActiveSheet()->setCellValue("E10", $empleado['sucursal']);
            $this->libro->getActiveSheet()->getStyle("D10")->applyFromArray( $this->labelBold);   

            //ENCABEZADOS PARA LA TABLA DE LA ASISTENCIA
            $this->libro->getActiveSheet()->getStyle("B12")->applyFromArray( $this->labelBold);   
            $this->libro->getActiveSheet()->getStyle("C12")->applyFromArray( $this->labelBold);   
            $this->libro->getActiveSheet()->getStyle("D12")->applyFromArray( $this->labelBold);   
            $this->libro->getActiveSheet()->getStyle("B12:F12")->applyFromArray( $this->labelBold);   
            $this->libro->getActiveSheet()->getStyle("B12:F12")->applyFromArray( $this->centrarTexto);   
            $this->libro->getActiveSheet()->getStyle("B12:F12")->getFill()->applyFromArray( $this->setColorFill("DF013A") );
            


            //ORDERNANDO EL ARREGLO 
            ksort($historialAsistencia) ;
            $fila = 13;
            foreach ( $historialAsistencia as $fecha => $detalleAsistencia) {
                //extraemos los datos para ir agrendo las filas
                $splitRetardo = explode("@" ,$detalleAsistencia );
                $this->libro->getActiveSheet()->setCellValue("B$fila" , $fecha);
                $this->libro->getActiveSheet()->setCellValue("C$fila" , "ASISTENCIA");
                $this->libro->getActiveSheet()->setCellValue("F$fila" , "-" );

                if ( isset( $splitRetardo[1] ) ) { /// Tiene un retardo
                    $this->libro->getActiveSheet()->setCellValue("C$fila" , $splitRetardo[1] );
                    $this->libro->getActiveSheet()->setCellValue("F$fila" , explode("&" , $splitRetardo[1])[1] );
                }else if( strpos( $detalleAsistencia , "FALTA")  !== false){
                    $this->libro->getActiveSheet()->setCellValue("C$fila" , "FALTA");
                    $this->libro->getActiveSheet()->setCellValue("F$fila" , explode("&" , $splitRetardo[0])[1] );
                }
                $this->libro->getActiveSheet()->setCellValue("D$fila", strpos( $detalleAsistencia , "FALTA") !== false ? "-" : $splitRetardo[0] );
                $this->libro->getActiveSheet()->getRowDimension($fila)->setRowHeight(25);
                $this->libro->getActiveSheet()->getStyle("B$fila:F$fila")->applyFromArray( $this->centrarTexto);   

                $this->libro->getActiveSheet()->getStyle("F$fila")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00");

                $fila++;
            }

            $this->libro->getActiveSheet()->setCellValue("F$fila" , "=SUM(F13:F".($fila-1).")");
            $this->libro->getActiveSheet()->getStyle("F$fila")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00");

            $this->libro->getActiveSheet()->getStyle("B12:F".($fila-1) )->applyFromArray( $this->bordes );
            $this->libro->getActiveSheet()->getColumnDimension("B")->setAutoSize(false);
            $this->libro->getActiveSheet()->getColumnDimension("B")->setWidth("15");
            $this->libro->getActiveSheet()->getColumnDimension("C")->setAutoSize(false);
            $this->libro->getActiveSheet()->getColumnDimension("C")->setWidth("15");
            $this->libro->getActiveSheet()->getColumnDimension("D")->setAutoSize(false);
            $this->libro->getActiveSheet()->getColumnDimension("D")->setWidth("15");

            //Firma de consentimiento
            $this->libro->getActiveSheet()->getStyle("B".($fila+4).":E".($fila+4) )->applyFromArray( $this->borderBottom );

            $this->libro->getActiveSheet()->setCellValue("B".($fila+5), 'NOMBRE Y FIRMA');
            $this->libro->getActiveSheet()->mergeCells("B".($fila+5 ).":F".($fila+5 ));
            $this->libro->getActiveSheet()->getStyle("B".($fila+5 ))->applyFromArray( $this->centrarTexto);   
        }

        $this->libro->getActiveSheet()->getStyle("A9:E".($fila-1))->applyFromArray( $this->setColorText("000000",10) );
        $this->libro->getActiveSheet()->getStyle("B12:F12")->applyFromArray( $this->setColorText("ffffff",10) );

        $reporteTerminado = new \PHPExcel_Writer_Excel2007( $this->libro);
        $reporteTerminado->setPreCalculateFormulas(true);
        $reporteTerminado->setIncludeCharts(TRUE);
         $reporteTerminado->save($_SERVER['DOCUMENT_ROOT']."/intranet/controladores/reportes/nomina/AcuseDeAsistencia.xlsx");
        $ubicacion = "http://servermatrixxxb.ddns.net:8181/intranet/controladores/reportes/nomina/AcuseDeAsistencia.xlsx";
        echo "$ubicacion";        
    }

}

