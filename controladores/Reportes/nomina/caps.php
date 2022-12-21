<?php

ini_set('default_charset', 'UTF-8');

require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/Reportes/prepareExcel.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/nomina/trabajadores.php";


class ReporteCaps extends PrepareExcel 
{

    protected $trabajadorController;

    public function __construct()
    {
        parent::__construct();
        $this->creaEmptySheet("CAPs",1);
        $this->trabajadorController  = new TrabajadorController;
    }


    public function generaReporte( $fecha )
    {
        $listaTrabajadores = $this->trabajadorController->getHistorialCaps(  $fecha );

        $this->putLogo("B1",300, 200 );
        $this->libro->getActiveSheet()->mergeCells("A6:E6");
        $this->libro->getActiveSheet()->setCellValue("A6","Listado de Rotación de Trabajadores");
        $this->libro->getActiveSheet()->getStyle("A6")->applyFromArray($this->centrarTexto);
        $this->libro->getActiveSheet()->getStyle("A6")->applyFromArray($this->labelBold);        

        $i = 9;

        
        $this->libro->getActiveSheet()->setCellValue("A8","EMPLEADO");
        
        $this->libro->getActiveSheet()->setCellValue("C8","PUESTO");
        $this->libro->getActiveSheet()->setCellValue("B8","SUCURSAL");
        $this->libro->getActiveSheet()->setCellValue("D8","PUESTO ANTERIOR");
        $this->libro->getActiveSheet()->setCellValue("E8","SUCURSAL ANTERIOR");        
        $this->libro->getActiveSheet()->setCellValue("F8","FECHA ULT. CAMBIO");
        $this->libro->getActiveSheet()->setCellValue("G8","MOVIMIENTO");


        $this->libro->getActiveSheet()->setAutoFilter("A8:G8");
        $this->libro->getActiveSheet()->getStyle("A8:G8")->applyFromArray($this->centrarTexto);

        $this->libro->getActiveSheet()->getStyle("A8:G8")->getFill()->applyFromArray( $this->setColorFill("cc0000")  );   
        $this->libro->getActiveSheet()->getStyle("A8:G8")->applyFromArray($this->labelBold);
        $this->libro->getActiveSheet()->getStyle("A8:G8") ->getAlignment()->setWrapText(true); 
        $this->libro->getActiveSheet()->getStyle("A8:G8")->applyFromArray($this->setColorText('ffffff',11));

        $nHombres = 0;
        $nMujeres = 0;
        
        foreach ( $listaTrabajadores as $empleado ) {


            
            $this->libro->getActiveSheet()->setCellValue("A$i", $empleado['nombre'] );
            $this->libro->getActiveSheet()->setCellValue("B$i", $empleado['destino'] );
            $this->libro->getActiveSheet()->setCellValue("C$i", $empleado['nvoPuesto'] ); 
            $this->libro->getActiveSheet()->setCellValue("D$i", $empleado['origen'] );
            
            $this->libro->getActiveSheet()->setCellValue("E$i", $empleado['viejoPuesto'] );
            $this->libro->getActiveSheet()->setCellValue("F$i", $empleado['fecha'] != '' ? PHPExcel_Shared_Date::PHPToExcel( $empleado['fecha']  )   : "-" );
            $this->libro->getActiveSheet()->setCellValue("G$i", $empleado['tipo_movto'] );
            $this->libro->getActiveSheet()->getStyle("G$i:H$i")->applyFromArray($this->setColorText('000000',11));
            
            $this->libro->getActiveSheet()
                ->getStyle("F$i")->getNumberFormat()
            ->setFormatCode('dd/MM/yyyy');;


            
           
            $this->libro->getActiveSheet()->getStyle("A$i")->applyFromArray($this->centrarTexto);
            $this->libro->getActiveSheet()->getStyle("B$i:F$i")->applyFromArray($this->centrarTexto);

            // $this->libro->getActiveSheet()->getStyle("E$i")->applyFromArray($this->labelBold);
            // $this->libro->getActiveSheet()->getStyle("E$i")->applyFromArray($this->centrarTexto);
            $this->libro->getActiveSheet()->getStyle("E$i:V$i")->applyFromArray($this->centrarTexto);
            $this->libro->getActiveSheet()->getStyle("J$i")->applyFromArray($this->labelBold);
            $this->libro->getActiveSheet()->getStyle("K$i")->applyFromArray($this->labelBold);
            $this->libro->getActiveSheet()->getStyle("L$i")->applyFromArray($this->labelBold);
            // $this->libro->getActiveSheet()->getStyle("I$i")->applyFromArray($this->labelBold);
            // $this->libro->getActiveSheet()->getStyle("I$i")->applyFromArray($this->centrarTexto);
            
            $i++;
        }
     

        $this->libro->getActiveSheet()->getStyle("A8:H".( $i-1)  )->applyFromArray( $this->bordes );

        $this->libro->getActiveSheet()->getColumnDimension("B")->setAutoSize(false );
        $this->libro->getActiveSheet()->getColumnDimension('B')->setWidth( 20);
        $this->libro->getActiveSheet()->getColumnDimension("C")->setAutoSize(false );
        $this->libro->getActiveSheet()->getColumnDimension('C')->setWidth( 25);
        $this->libro->getActiveSheet()->getColumnDimension("D")->setAutoSize(false );
        $this->libro->getActiveSheet()->getColumnDimension('D')->setWidth( 25);
        $this->libro->getActiveSheet()->getColumnDimension("E")->setAutoSize(false );
        $this->libro->getActiveSheet()->getColumnDimension('E')->setWidth( 20);
        $this->libro->getActiveSheet()->getColumnDimension("F")->setAutoSize(false );
        $this->libro->getActiveSheet()->getColumnDimension('F')->setWidth( 20);

        $this->libro->getActiveSheet()->getColumnDimension("A")->setAutoSize(false );
        $this->libro->getActiveSheet()->getColumnDimension('A')->setWidth( 45);
        $this->libro->getActiveSheet()->getColumnDimension("H")->setAutoSize(false );
        $this->libro->getActiveSheet()->getColumnDimension('H')->setWidth( 15);
        $this->libro->getActiveSheet()->getColumnDimension("I")->setAutoSize(false );
        $this->libro->getActiveSheet()->getColumnDimension('I')->setWidth( 15 );
        $this->libro->getActiveSheet()->getColumnDimension("J")->setAutoSize(false );
        $this->libro->getActiveSheet()->getColumnDimension('J')->setWidth( 15);

        $this->libro->getActiveSheet()->getColumnDimension("K")->setAutoSize(false );
        $this->libro->getActiveSheet()->getColumnDimension('K')->setWidth( 20);
        $this->libro->getActiveSheet()->getColumnDimension("L")->setAutoSize(false );
        $this->libro->getActiveSheet()->getColumnDimension('L')->setWidth( 20);
        $this->libro->getActiveSheet()->getColumnDimension("M")->setAutoSize(false );
        $this->libro->getActiveSheet()->getColumnDimension('M')->setWidth( 20);
        $this->libro->getActiveSheet()->getColumnDimension("N")->setAutoSize(false );
        $this->libro->getActiveSheet()->getColumnDimension('N')->setWidth( 20);
        $this->libro->getActiveSheet()->getColumnDimension("O")->setAutoSize(false );
        $this->libro->getActiveSheet()->getColumnDimension('O')->setWidth( 20);
        $this->libro->getActiveSheet()->getColumnDimension("P")->setAutoSize(false );
        $this->libro->getActiveSheet()->getColumnDimension('P')->setWidth( 20);
        $this->libro->getActiveSheet()->getColumnDimension("Q")->setAutoSize(false );
        $this->libro->getActiveSheet()->getColumnDimension('Q')->setWidth( 20);
        $this->libro->getActiveSheet()->getColumnDimension("R")->setAutoSize(false );
        $this->libro->getActiveSheet()->getColumnDimension('R')->setWidth( 20);
        $this->libro->getActiveSheet()->getColumnDimension("S")->setAutoSize(false );
        $this->libro->getActiveSheet()->getColumnDimension('S')->setWidth( 20);
        $this->libro->getActiveSheet()->getColumnDimension("T")->setAutoSize(false );
        $this->libro->getActiveSheet()->getColumnDimension('T')->setWidth( 20);
        $this->libro->getActiveSheet()->getColumnDimension('U')->setAutoSize( FALSE);
        $this->libro->getActiveSheet()->getColumnDimension('U')->setWidth( 20);
        $this->libro->getActiveSheet()->getColumnDimension('V')->setAutoSize( FALSE);
        $this->libro->getActiveSheet()->getColumnDimension('V')->setWidth( 20);
        $this->libro->getActiveSheet()->getColumnDimension('W')->setAutoSize( FALSE);        
        $this->libro->getActiveSheet()->getColumnDimension('W')->setWidth( 20);
        $this->libro->getActiveSheet()->getColumnDimension('X')->setAutoSize( FALSE); 
        $this->libro->getActiveSheet()->getColumnDimension('X')->setWidth( 20);
        $this->libro->getActiveSheet()->getColumnDimension('Y')->setAutoSize( FALSE); 
        $this->libro->getActiveSheet()->getColumnDimension('Y')->setWidth( 20);
        $this->libro->getActiveSheet()->getColumnDimension('X')->setAutoSize( FALSE);         


        $reporteTerminado = new PHPExcel_Writer_Excel2007( $this->libro);
        $time = (strtotime(date("Y-m-d H:i:s")) * 1000);
        $file = $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/Reportes/nomina/caps".$time.'.xlsx';
        $reporteTerminado->save(  $file);
        echo 'caps'.$time.".xlsx";

    }
}


