<?php
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/con_edosfinancieros.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/Reportes/prepareExcel.php";

class ProgramaGastosController extends PrepareExcel
{

    public $celdasDias;

    public $centrarTexto = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER_CONTINUOUS,
        )
    );

    public $bordes = array( 'borders' => array(
                    'allborders' => array(
                                'style' => \PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    );
    public function __construct()
    {
        parent::__construct();
        $this->celdasDias = array('-','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI');
    }

    public function getMes( $mesIn)
    {
        $meses = array('-','ENERO','FEBRERO','MARZO','ABRIL','MAYO','JUNIO','JULIO','AGOSTO','SEPTIEMBRE','OCTUBRE','NOVIEMBRE','DICIEMBRE');

        foreach ($meses as $key => $mes) {
            if ( $key == ($mesIn / 1) ) {
                echo $mes;
                return $mes;
            }
        }
    }
    public function setColumnConceptos($mes, $titulo="", $index= null)
    {
        $i = 9;
        $this->creaEmptySheet($titulo, $index);
        $estadoFinanciero = new EdoFinancieros;
        $dias = cal_days_in_month(CAL_GREGORIAN, date('m'),date('Y'));
        $cuentasPorPagar = $estadoFinanciero->getTotalCXP( date('Y')."-".date('m')."-01",  date("Y-m")."-$dias" );

        $movimientosMensual = $estadoFinanciero->getGasto_conceptoMensual( date('m')  );

        $this->libro->getActiveSheet()->mergeCells("H5:S5");
        $this->libro->getActiveSheet()->setCellValue('H5',"PROGRAMACION DE GASTOS ". $this->getMes(date('m') )." DE ".date('Y'));
        $this->libro->getActiveSheet()->getStyle('H5')->applyFromArray( $this->centrarTexto);
        $this->libro->getActiveSheet()->getStyle('H5')->applyFromArray( $this->labelBold);
        
        $this->libro->getActiveSheet()->setCellValue('A8',"Concepto");
        $this->libro->getActiveSheet()->mergeCells("A8:C8");
        $this->libro->getActiveSheet()->setCellValue('D8',"Fecha");
        $this->libro->getActiveSheet()->setCellValue('E8',"Total");
        $this->libro->getActiveSheet()->getStyle("A8:E8")->applyFromArray( $this->centrarTexto );

        $this->libro->getActiveSheet()->getStyle("A8:E8")->applyFromArray( $this->centrarTexto );
        $this->libro->getActiveSheet()->getStyle("A8:E8")->applyFromArray( $this->labelBold );
        $this->libro->getActiveSheet()->getStyle("A8:E8")->getFill()->applyFromArray( $this->setColorFill("000000")  );
        $this->libro->getActiveSheet()->getStyle("A8:E8")->applyFromArray( $this->setColorText('FFFFFF') );    
    
        $this->libro->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $this->libro->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);

        $fechaAnterior = "";
        $totalAcumulado = 0;
        $flagFilaInicio = 9;
        $dia;
        $descuentaSalto = 1;
        $fechaExplode;
        foreach ($cuentasPorPagar as $index => $movimiento) {

            if( $fechaAnterior != ""){
                $fechaExplode = explode('-', $fechaAnterior);
                $dia = ($fechaExplode[2] /1) ;
                if ( $dia % 7 == 0) {
                    $i++;
                }
            }

            $this->libro->getActiveSheet()->setCellValue("A$i", utf8_decode( $movimiento['CF1']) );
            
            $this->libro->getActiveSheet()->setCellValue("D$i",  str_replace("-","/" ,$movimiento['FECHAMOVI'] ));
            $this->libro->getActiveSheet()->setCellValue("E$i", round($movimiento['IMPORTECOBRO'], 1 ) );
            $this->libro->getActiveSheet()->mergeCells("A$i:C$i");
            $this->libro->getActiveSheet()->getStyle("E$i")->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);


            $fechaExplode = explode('-', $movimiento['FECHAMOVI']);
            if ( $fechaAnterior == "") {
                $fechaAnterior = $movimiento['FECHAMOVI'];
            }
            if ( $fechaAnterior != $movimiento['FECHAMOVI']) {

                    $fechaExplode = explode('-', $fechaAnterior);
                    $dia = ($fechaExplode[2] /1) ;
                
                    $descuentaSalto = $dia % 7 == 0 ? 2 : 1;
                    
                    // var_dump($this->celdasDias[$dia].$flagFilaInicio.":".$this->celdasDias[$dia].($i-1));

                    $this->libro->getActiveSheet()->setCellValue($this->celdasDias[$dia].$flagFilaInicio, $totalAcumulado);
                    $this->libro->getActiveSheet()->mergeCells($this->celdasDias[$dia].$flagFilaInicio.":".$this->celdasDias[$dia].($i-$descuentaSalto) );
                    $this->libro->getActiveSheet()->getStyle( $this->celdasDias[$dia].$flagFilaInicio.":".$this->celdasDias[$dia].($i- $descuentaSalto) )->applyFromArray( $this->labelBold);

                    $this->libro->getActiveSheet()->getStyle($this->celdasDias[$dia].$flagFilaInicio)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $this->libro->getActiveSheet()->getStyle($this->celdasDias[$dia].$flagFilaInicio)->applyFromArray( $this->centrarTexto );
                    $this->libro->getActiveSheet()->getStyle($this->celdasDias[$dia].$flagFilaInicio)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

                    if ( $dia % 7 == 0) {
                        
                        echo "A$i:AI$i  dia: $dia  $$totalAcumulado<br>" ;
                        $this->libro->getActiveSheet()->mergeCells("A".($i-1) .":AI".($i-1) );
                        $this->libro->getActiveSheet()->getStyle("A".($i-1) .":AI".($i-1) )->getFill()->applyFromArray( $this->setColorFill("000000")  );
                        
                    }

                    $fechaAnterior = $movimiento['FECHAMOVI'];
                    $totalAcumulado = $movimiento['IMPORTECOBRO'];
                    $flagFilaInicio = $i;
                } else {
                    $totalAcumulado += $movimiento['IMPORTECOBRO'];
                    
                }
                
                
            $i++;
        }


        //Agregand el valor del ultimo elemento de la la lista
                            $fechaExplode = explode('-', $fechaAnterior);
                    $dia = ($fechaExplode[2] /1) ;
                
                    echo "$dia<br>";
                    // var_dump($this->celdasDias[$dia].$flagFilaInicio.":".$this->celdasDias[$dia].($i-1));

                    $this->libro->getActiveSheet()->setCellValue($this->celdasDias[$dia].$flagFilaInicio, $totalAcumulado);
                    $this->libro->getActiveSheet()->mergeCells($this->celdasDias[$dia].$flagFilaInicio.":".$this->celdasDias[$dia].($i-1) );
                    $this->libro->getActiveSheet()->getStyle( $this->celdasDias[$dia].$flagFilaInicio.":".$this->celdasDias[$dia].($i-1) )->applyFromArray( $this->labelBold);

                    $this->libro->getActiveSheet()->getStyle($this->celdasDias[$dia].$flagFilaInicio)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $this->libro->getActiveSheet()->getStyle($this->celdasDias[$dia].$flagFilaInicio)->applyFromArray( $this->centrarTexto );
                    $this->libro->getActiveSheet()->getStyle($this->celdasDias[$dia].$flagFilaInicio)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);


             
        $calendario = ( $this->getCalendario($mes) );
        foreach ($calendario as $index => $dia) {
            if ( $index > 0) {
                $this->libro->getActiveSheet()->getColumnDimension($this->celdasDias[$index])->setAutoSize(false);
                $this->libro->getActiveSheet()->getColumnDimension($this->celdasDias[$index])->setWidth("12");
                $this->libro->getActiveSheet()->setCellValue($this->celdasDias[$index]."8", $index);

                $this->libro->getActiveSheet()->getStyle($this->celdasDias[$index]."8")->applyFromArray( $this->centrarTexto );
                $this->libro->getActiveSheet()->getStyle($this->celdasDias[$index]."8")->applyFromArray( $this->labelBold );
                $this->libro->getActiveSheet()->getStyle($this->celdasDias[$index]."8")->getFill()->applyFromArray( $this->setColorFill("000000")  );
                $this->libro->getActiveSheet()->getStyle($this->celdasDias[$index]."8")->applyFromArray( $this->setColorText('FFFFFF') );                
                $this->libro->getActiveSheet()->getStyle($this->celdasDias[$index]."8")->applyFromArray( $this->bordes );

                if ( $index % 7 == 0) {
                    $celdaInicio = $this->celdasDias[$index-6];
                    $this->libro->getActiveSheet()->mergeCells($celdaInicio."7:".$this->celdasDias[$index]."7");
                    $this->libro->getActiveSheet()->setCellValue($celdaInicio."7","SEMANA ".($index /7) );
                    $this->libro->getActiveSheet()->getStyle($celdaInicio."7")->applyFromArray( $this->centrarTexto );
                     $this->libro->getActiveSheet()->getStyle($celdaInicio."7")->getFill()->applyFromArray( $this->setColorFill("C42F2F")  );
                    $this->libro->getActiveSheet()->getStyle($celdaInicio."7")->applyFromArray( $this->setColorText('FFFFFF') );                                   
                    $this->libro->getActiveSheet()->getStyle($celdaInicio."7")->applyFromArray( $this->labelBold );
                    if( ($index /7) %2  == 0 ){
                        echo ($index );
                        $this->libro->getActiveSheet()->getStyle($this->celdasDias[$index-6]."9:".$this->celdasDias[$index].($i-1))->getFill()->applyFromArray( $this->setColorFill("FFEAEA")  );
                    }
                    else{
                        
                        $this->libro->getActiveSheet()->getStyle($this->celdasDias[$index-6]."9:".$this->celdasDias[$index].($i-1) )->getFill()->applyFromArray( $this->setColorFill("EBEBEB")  );
                    }
                     
                }    
            }
             $dia = sizeof( $calendario )-1;
            $this->libro->getActiveSheet()->getStyle($this->celdasDias[1]."9:".$this->celdasDias[$dia].($i-1) )->applyFromArray( $this->bordes );
        }

        for ($i=7; $i <= 9 ; $i++) { 
            $this->libro->getActiveSheet()->freezePane('A'.$i);
        }           
        $this->putLogo("K1",400,200)    ;
        $reporteTerminado = new PHPExcel_Writer_Excel2007( $this->libro);
        $reporteTerminado->save("ProgramaGastosFire.xlsx");

    }

    public function getCalendario( $mes )
    {
        $anioActual = date('Y');
        $calendario = array('');
        $dias = cal_days_in_month(CAL_GREGORIAN, $mes, $anioActual);
        for ($i=1; $i <= $dias ; $i++) { 
            $fecha = strtotime("$anioActual-$mes-$i");
            switch ( date('w', $fecha)) {
                case 0:
                     array_push( $calendario, 'Dom');
                    break;
                case 1:
                     array_push( $calendario, 'Lun');
                    break; 
                case 2:
                     array_push( $calendario, 'Mar');
                    break;  
                case 3:
                     array_push( $calendario, 'Mier');
                    break;
                case 4:
                     array_push( $calendario, 'Jue');
                    break; 
                case 5:
                     array_push( $calendario, 'Vier');
                    break;     
                case 6:
                     array_push( $calendario, 'Sab');
                    break;                                                                                       
            }
        }
        return $calendario;
    }

}

$reporte  = new ProgramaGastosController;
$reporte->setColumnConceptos(6,"Junio");