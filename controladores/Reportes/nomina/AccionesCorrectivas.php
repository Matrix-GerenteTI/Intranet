<?php

require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/Reportes/prepareExcel.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/nomina/trabajadores.php";


class AccionesCorrectivas  extends PrepareExcel
{
    
    public function __construct() {
        parent::__construct();
        $this->libro->getProperties()->setTitle('ACCIONES CORRECTIVAS'); 
    }

    public function generaReporte( $params )
    {
        
        $fechaInicio = explode( '/', $params['fechaInicio']);
        $fechaFin = explode( '/', $params['fechaFin']);

        $params['fechaInicio'] = $fechaInicio[2]."-".$fechaInicio[1]."-".$fechaInicio[0];
        $params['fechaFin'] = $fechaFin[2]."-".$fechaFin[1]."-".$fechaFin[0];

        $modeloTrabajador = new Trabajador;
        $listaAcciones = $modeloTrabajador->getAccionesCorrectivas( $params );

        $this->creaEmptySheet( "ACCIONES CORRECTIVAS", 0 );

        $this->libro->getActiveSheet()->setAutoFilter("A8:F8");
        $this->putLogo("B1", 100,200);
        $this->libro->getActiveSheet()->mergeCells("B4:D4");
        $this->libro->getActiveSheet()->setCellValue("B4","Reporte de Acciones correctivas");
       $this->libro->getActiveSheet()->getStyle("B4")->applyFromArray( $this->labelBold);   
        $this->libro->getActiveSheet()->getStyle("B4")->applyFromArray( $this->centrarTexto );

        $this->libro->getActiveSheet()->mergeCells("B5:D5");
       $this->libro->getActiveSheet()->setCellValue("B5", str_replace("-","/", $params['fechaInicio']). " A ".str_replace("-","/", $params['fechaFin']) );
       $this->libro->getActiveSheet()->getStyle("B5")->applyFromArray( $this->labelBold);   
        $this->libro->getActiveSheet()->getStyle("B5")->applyFromArray( $this->centrarTexto );


        $this->libro->getActiveSheet()->setCellValue("A8", "EMPLEADO");
        $this->libro->getActiveSheet()->setCellValue("B8", "ACCION CORRECTIVA");
        $this->libro->getActiveSheet()->setCellValue("C8", "PLAN DE ACCIÓN");
        $this->libro->getActiveSheet()->setCellValue("D8", "FECHA");
        $this->libro->getActiveSheet()->setCellValue("E8", "MONTO");
        $this->libro->getActiveSheet()->setCellValue("F8", "APLICÓ");

        $this->libro->getActiveSheet()->getStyle("A8:F8")->applyFromArray( $this->labelBold);
        $this->libro->getActiveSheet()->getStyle("A8:F8")->applyFromArray( $this->centrarTexto );
        $this->libro->getActiveSheet()->getStyle("A8:F8")->getFill()->applyFromArray( $this->setColorFill("DF013A") );
        $this->libro->getActiveSheet()->getStyle("A8:F8")->applyFromArray( $this->setColorText("ffffff",12) );

        $j = 9;

        foreach ( $listaAcciones as $i => $accionCorrectiva) {
            $this->libro->getActiveSheet()->setCellValue("A$j", $accionCorrectiva['sancionado']);
            $this->libro->getActiveSheet()->setCellValue("B$j", $accionCorrectiva['motivo']);
            $this->libro->getActiveSheet()->setCellValue("C$j", $accionCorrectiva['plan_accion']);
            $this->libro->getActiveSheet()->setCellValue("D$j", $accionCorrectiva['fecha_sancion']);
            $this->libro->getActiveSheet()->setCellValue("E$j", $accionCorrectiva['monto']);
            $this->libro->getActiveSheet()->getStyle("E$j")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00");
            $this->libro->getActiveSheet()->setCellValue("F$j", $accionCorrectiva['sancionador']);
            $this->libro->getActiveSheet()->getRowDimension($j)->setRowHeight(25);
            $j++;

        }
        $this->libro->getActiveSheet()->getColumnDimension("A")->setAutoSize(true);
        $this->libro->getActiveSheet()->getColumnDimension("B")->setAutoSize(false);
        $this->libro->getActiveSheet()->getColumnDimension("B")->setWidth("40");
        $this->libro->getActiveSheet()->getColumnDimension("C")->setAutoSize(false);
        $this->libro->getActiveSheet()->getColumnDimension("C")->setWidth("40");
        $this->libro->getActiveSheet()->getColumnDimension("D")->setAutoSize(true);
        $this->libro->getActiveSheet()->getColumnDimension("E")->setAutoSize(true);
        $this->libro->getActiveSheet()->getColumnDimension("F")->setAutoSize(true);
        $this->libro->getActiveSheet()->getStyle("A8:F".($j-1) )->applyFromArray( $this->bordes );

        $reporteTerminado = new \PHPExcel_Writer_Excel2007( $this->libro);
        $reporteTerminado->setPreCalculateFormulas(true);
        $reporteTerminado->setIncludeCharts(TRUE);
         $reporteTerminado->save($_SERVER['DOCUMENT_ROOT']."/intranet/controladores/reportes/nomina/accionCorrectiva.xlsx");
        $ubicacion = "http://servermatrixxxb.ddns.net:8181/intranet/controladores/reportes/nomina/accionCorrectiva.xlsx";
        echo "$ubicacion";
    }

}


$accionCorrectiva = new AccionesCorrectivas;
$data = [];

if ( isset($_GET['fechaInicio'] ) ) {
    $data = $_GET;
}else{
    $data['fechaInicio'] = date('01/m/Y');
    $data['fechaFin'] = date('d/m/Y');
}

$accionCorrectiva->generaReporte( $data ) ;

    $configCorreo = array("descripcionDestinatario" => "Listado de acciones correctivas",
                                        "mensaje" => "SITEX",
                                        "pathFile" => $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/reportes/nomina/accionCorrectiva.xlsx",
                                        "subject" => "Acciones correctivas",
                                        //"correos" => array( "sestrada@matrix.com.mx")
                                        "correos" => array( "sestrada@matrix.com.mx","rh@matrix.com.mx",'compras@matrix.com.mx', 'gtealmacen@matrix.com.mx','direccionestrategica@matrix.com.mx',
                                                            'compras@matrix.com.mx', 'gtealmacen@matrix.com.mx','luisimatrix@hotmail.com','raulmatrixxx@outlook.com.mx','gerenteadministrativo@matrix.com.mx',
                                                            'software@matrix.com.mx',"gerenteti@matrix.com.mx","director@matrix.com.mx")
                                        );
    if ( !isset( $_GET['fechaInicio']) ) {
        $accionCorrectiva->enviarReporte( $configCorreo);
    }
     