<?php

require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/Almacenes/Articulos.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/Reportes/prepareExcel.php";


class InventarioVsValuado  extends PrepareExcel
{
    protected $articulosInventariados;
	protected $articulosValuado;	

    public function __construct() {
        parent::__construct();
        $this->libro->getProperties()->setTitle('REPORTE DE INVENTARIO'); 
    }

    public function generaReporte( $fecha , $sucursal, $usuario )
    {
        

        $modeloArticulo = new Articulos;
        $articulos = $modeloArticulo->getValuadoSucursal( $sucursal );
        $this->articulosValuado = $articulos;
        $this->articulosInventariados = $modeloArticulo->getInventarioEnSucursal( $sucursal , $fecha, $usuario );
        
        $noInventariado = [];

        //la hoja de las coincidencias de inventario con el valuado

        $this->creaEmptySheet( "Valuado Vs Inventario", 0 );

        $this->libro->getActiveSheet()->setAutoFilter("A8:F8");
        $this->putLogo("B1", 100,200);
        $this->libro->getActiveSheet()->mergeCells("B4:D4");
        $this->libro->getActiveSheet()->setCellValue("B4","Reporte de inventario Vs Valuado de la sucursal ");
       $this->libro->getActiveSheet()->getStyle("B4")->applyFromArray( $this->labelBold);   
        $this->libro->getActiveSheet()->getStyle("B4")->applyFromArray( $this->centrarTexto );

        $this->libro->getActiveSheet()->mergeCells("B5:D5");
    //    $this->libro->getActiveSheet()->setCellValue("B5", $this->getMesAsString($mes)." de ".$anio );
       $this->libro->getActiveSheet()->getStyle("B5")->applyFromArray( $this->labelBold);   
        $this->libro->getActiveSheet()->getStyle("B5")->applyFromArray( $this->centrarTexto );



        $this->libro->getActiveSheet()->setCellValue("A8", "CODIGO");
        $this->libro->getActiveSheet()->setCellValue("B8", "DESCRIPCION");
        $this->libro->getActiveSheet()->setCellValue("C8", "FAMILIA");
        $this->libro->getActiveSheet()->setCellValue("D8", "SUBFAMILIA");
        $this->libro->getActiveSheet()->setCellValue("E8", "STOCK VALUADO");
        $this->libro->getActiveSheet()->setCellValue("F8", "FISICO");

        $this->libro->getActiveSheet()->setCellValue("G8", "DIFERENCIA");

        $this->libro->getActiveSheet()->getStyle("A8:G8")->applyFromArray( $this->labelBold );
        $this->libro->getActiveSheet()->getStyle("A8:G8")->applyFromArray( $this->centrarTexto );
        $this->libro->getActiveSheet()->getStyle("A8:G8")->getFill()->applyFromArray( $this->setColorFill("DF013A") );
        $this->libro->getActiveSheet()->getStyle("A8:G8")->applyFromArray( $this->setColorText("ffffff",12) );

        $j = 9;
        foreach ($articulos as $i => $articulo) {
            $cantidadInventariada = $this->estaInventariado( $articulo->CODIGOARTICULO );
            if ( !$cantidadInventariada ) {
				array_push( $noInventariado , $articulo );
            }else{
                $this->libro->getActiveSheet()->setCellValue("A$j", $articulo->CODIGOARTICULO );
                $this->libro->getActiveSheet()->getStyle("A$j")->applyFromArray( $this->labelBold );
                $this->libro->getActiveSheet()->setCellValue("B$j", $articulo->DESCRIPCION );
                $this->libro->getActiveSheet()->setCellValue("C$j", $articulo->FAMILIA );
                $this->libro->getActiveSheet()->setCellValue("D$j", $articulo->SUBFAMILIA );
                $this->libro->getActiveSheet()->setCellValue("E$j", $articulo->STOCK );
                $this->libro->getActiveSheet()->setCellValue("F$j", $cantidadInventariada );
                
                $this->libro->getActiveSheet()->setCellValue("G$j", "=F$j-E$j");
                $this->libro->getActiveSheet()->getStyle("E$j:G$j")->applyFromArray( $this->centrarTexto );
                $this->libro->getActiveSheet()->getStyle("E$j:G$j")->applyFromArray( $this->labelBold );
                $this->libro->getActiveSheet()->getStyle("G$j")->applyFromArray( $this->setColorText("DF013A",12) );
                $this->libro->getActiveSheet()->getRowDimension($j)->setRowHeight(25);
                $j++;
            }
        }
        $this->libro->getActiveSheet()->getColumnDimension("A")->setAutoSize(true);
        $this->libro->getActiveSheet()->getColumnDimension("B")->setAutoSize(false);
        $this->libro->getActiveSheet()->getColumnDimension("B")->setWidth("40");
        $this->libro->getActiveSheet()->getColumnDimension("C")->setAutoSize(true);
        $this->libro->getActiveSheet()->getColumnDimension("D")->setAutoSize(true);
        $this->libro->getActiveSheet()->getColumnDimension("E")->setAutoSize(true);
        $this->libro->getActiveSheet()->getColumnDimension("F")->setAutoSize(true);
        $this->libro->getActiveSheet()->getColumnDimension("G")->setAutoSize(true);
        $this->libro->getActiveSheet()->getColumnDimension("I")->setAutoSize(true);
        $this->libro->getActiveSheet()->getColumnDimension("J")->setAutoSize(true);
        $this->libro->getActiveSheet()->getStyle("A8:G".($j-1) )->applyFromArray( $this->bordes );

        //Hoja para lo que esta en el valuado pero no en el inventario
        $this->creaEmptySheet( "No inventariado", 1 );

        $this->libro->getActiveSheet()->setAutoFilter("A8:F8");
        $this->putLogo("B1", 100,200);
        $this->libro->getActiveSheet()->mergeCells("B4:D4");
        $this->libro->getActiveSheet()->setCellValue("B4","Reporte de articulos en valuado que no fueron inventariados");
       $this->libro->getActiveSheet()->getStyle("B4")->applyFromArray( $this->labelBold);   
        $this->libro->getActiveSheet()->getStyle("B4")->applyFromArray( $this->centrarTexto );

        $this->libro->getActiveSheet()->mergeCells("B5:D5");
    //    $this->libro->getActiveSheet()->setCellValue("B5", $this->getMesAsString($mes)." de ".$anio );
       $this->libro->getActiveSheet()->getStyle("B5")->applyFromArray( $this->labelBold);   
        $this->libro->getActiveSheet()->getStyle("B5")->applyFromArray( $this->centrarTexto );



        $this->libro->getActiveSheet()->setCellValue("A8", "CODIGO");
        $this->libro->getActiveSheet()->setCellValue("B8", "DESCRIPCION");
        $this->libro->getActiveSheet()->setCellValue("C8", "FAMILIA");
        $this->libro->getActiveSheet()->setCellValue("D8", "SUBFAMILIA");
        $this->libro->getActiveSheet()->setCellValue("E8", "STOCK VALUADO");
        $this->libro->getActiveSheet()->getStyle("A8:E8")->applyFromArray( $this->labelBold);
        $this->libro->getActiveSheet()->getStyle("A8:E8")->applyFromArray( $this->centrarTexto );
        $this->libro->getActiveSheet()->getStyle("A8:E8")->getFill()->applyFromArray( $this->setColorFill("DF013A") );
        $this->libro->getActiveSheet()->getStyle("A8:E8")->applyFromArray( $this->setColorText("ffffff",12) );
        
        $j= 9;
        foreach ($noInventariado as $i => $articulo) {
            $this->libro->getActiveSheet()->setCellValue("A$j", $articulo->CODIGOARTICULO );
            $this->libro->getActiveSheet()->getStyle("A$j")->applyFromArray( $this->labelBold );
            $this->libro->getActiveSheet()->setCellValue("B$j", $articulo->DESCRIPCION );
            $this->libro->getActiveSheet()->setCellValue("C$j", $articulo->FAMILIA );
            $this->libro->getActiveSheet()->setCellValue("D$j", $articulo->SUBFAMILIA );
            $this->libro->getActiveSheet()->setCellValue("E$j", $articulo->STOCK );
            $this->libro->getActiveSheet()->getStyle("E$j:F$j")->applyFromArray( $this->centrarTexto );
            $this->libro->getActiveSheet()->getStyle("E$j:G$j")->applyFromArray( $this->labelBold );            

            $this->libro->getActiveSheet()->getRowDimension($j)->setRowHeight(25);
            $j++;
        }
        $this->libro->getActiveSheet()->getColumnDimension("A")->setAutoSize(true);
        $this->libro->getActiveSheet()->getColumnDimension("B")->setAutoSize(false);
        $this->libro->getActiveSheet()->getColumnDimension("B")->setWidth("40");
        $this->libro->getActiveSheet()->getColumnDimension("C")->setAutoSize(true);
        $this->libro->getActiveSheet()->getColumnDimension("D")->setAutoSize(true);
        $this->libro->getActiveSheet()->getColumnDimension("E")->setAutoSize(true);
        $this->libro->getActiveSheet()->getStyle("A8:E".($j-1) )->applyFromArray( $this->bordes );

        // Inventariado pero no está en el valuado
        $this->creaEmptySheet( "No presentes en Valuado Inicial", 2 );

        $this->libro->getActiveSheet()->setAutoFilter("A8:F8");
        $this->putLogo("B1", 100,200);
        $this->libro->getActiveSheet()->mergeCells("B4:D4");
        $this->libro->getActiveSheet()->setCellValue("B4","Reporte de articulos inventariados pero no en el valuado de la sucursal");
       $this->libro->getActiveSheet()->getStyle("B4")->applyFromArray( $this->labelBold);   
        $this->libro->getActiveSheet()->getStyle("B4")->applyFromArray( $this->centrarTexto );

        $this->libro->getActiveSheet()->mergeCells("B5:D5");
    //    $this->libro->getActiveSheet()->setCellValue("B5", $this->getMesAsString($mes)." de ".$anio );
       $this->libro->getActiveSheet()->getStyle("B5")->applyFromArray( $this->labelBold);   
        $this->libro->getActiveSheet()->getStyle("B5")->applyFromArray( $this->centrarTexto );



        $this->libro->getActiveSheet()->setCellValue("A8", "CODIGO");
        $this->libro->getActiveSheet()->setCellValue("B8", "DESCRIPCION");
        $this->libro->getActiveSheet()->setCellValue("C8", "FAMILIA");
        $this->libro->getActiveSheet()->setCellValue("D8", "SUBFAMILIA");
        $this->libro->getActiveSheet()->setCellValue("E8", "CANT. INV.");
        $this->libro->getActiveSheet()->getStyle("A8:E8")->applyFromArray( $this->labelBold);
        $this->libro->getActiveSheet()->getStyle("A8:E8")->applyFromArray( $this->centrarTexto );
        $this->libro->getActiveSheet()->getStyle("A8:E8")->getFill()->applyFromArray( $this->setColorFill("DF013A") );
        $this->libro->getActiveSheet()->getStyle("A8:E8")->applyFromArray( $this->setColorText("ffffff",12) );
        
        
        $j= 9;
        foreach ($this->articulosInventariados as $i => $articulo) {
			if($this->estaDeMas($articulo['codigo'])){
				if($articulo['fisico']>0){
					$this->libro->getActiveSheet()->setCellValue("A$j", $articulo['codigo']);
					$this->libro->getActiveSheet()->getStyle("A$j")->applyFromArray( $this->labelBold );
					$this->libro->getActiveSheet()->setCellValue("B$j", $articulo['descripcion'] );
					$this->libro->getActiveSheet()->setCellValue("C$j", $articulo['familia'] );
					$this->libro->getActiveSheet()->setCellValue("D$j", $articulo['subfamilia']);
					$this->libro->getActiveSheet()->setCellValue("E$j", $articulo['fisico'] );
					$this->libro->getActiveSheet()->getStyle("E$j:F$j")->applyFromArray( $this->centrarTexto );
					$this->libro->getActiveSheet()->getStyle("E$j:G$j")->applyFromArray( $this->labelBold );
					$this->libro->getActiveSheet()->getRowDimension($j)->setRowHeight(25);
					$j++;
				}
			}
        }

        $this->libro->getActiveSheet()->getColumnDimension("A")->setAutoSize(true);
        $this->libro->getActiveSheet()->getColumnDimension("B")->setAutoSize(false);
        $this->libro->getActiveSheet()->getColumnDimension("B")->setWidth("40");
        $this->libro->getActiveSheet()->getColumnDimension("C")->setAutoSize(false);
        $this->libro->getActiveSheet()->getColumnDimension("C")->setWidth("50");
        $this->libro->getActiveSheet()->getColumnDimension("D")->setAutoSize(false);
        $this->libro->getActiveSheet()->getColumnDimension("D")->setWidth("50");
        $this->libro->getActiveSheet()->getColumnDimension("E")->setAutoSize(false);
        $this->libro->getActiveSheet()->getColumnDimension("E")->setWidth("50");

        $this->libro->getActiveSheet()->getStyle("A8:E".($j-1) )->applyFromArray( $this->bordes );

        $reporteTerminado = new \PHPExcel_Writer_Excel2007( $this->libro);
        $reporteTerminado->setPreCalculateFormulas(true);
        $reporteTerminado->setIncludeCharts(TRUE);
         $reporteTerminado->save($_SERVER['DOCUMENT_ROOT']."/intranet/controladores/reportes/Valuados/inv_vs_valuado.xlsx");
        $ubicacion = "http://servermatrixxxb.ddns.net:8181/intranet/controladores/reportes/Valuados/inv_vs_valuado.xlsx";
        echo "<a href='$ubicacion'>Descargar</a>";
    }

    public function estaInventariado($codigo)
    {
        foreach ($this->articulosInventariados as $i  => $articulo) {
            
            if ( $articulo['codigo'] == $codigo) {
                return $articulo['fisico'];
				/*
                unset( $this->articulosInventariados[$i] );

                if ( $articulo['fisico2'] != '' && $articulo['fisico3'] != '') {
                    return ['fisico' => $articulo['fisico3'], 'fisico2' => $articulo['fisico3'] ,'fisico3' => $articulo['fisico3'] ];
                }else if( $articulo['fisico2'] != '' && $articulo['fisico3'] == '' ){
                     return ['fisico' => $articulo['fisico'], 'fisico2' => $articulo['fisico2'] ,'fisico3' => null];

                }else if( $articulo['fisico2'] == '' && $articulo['fisico3'] == ''){
                    return ['fisico' => $articulo['fisico'], 'fisico2' => $articulo['fisico2'] ,'fisico3' => null];
                }
                */
            }
        }
        return false;
    }
	
    public function estaDeMas($codigo)
    {
        foreach($this->articulosValuado as $item){
			if($item->CODIGOARTICULO==$codigo){
				return false;
			}
		}
        return true;
    }
}


$invvsvaluado = new InventarioVsValuado;
$invvsvaluado->generaReporte(date("Y-m-d") , $_GET['sucursal'], $_GET['usuario'] );