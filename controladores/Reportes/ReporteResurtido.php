<?php
set_time_limit(0);
ini_set('memory_limit', '20000M');

require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/Almacenes/Articulos.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/Almacenes/Almacen.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/Reportes/prepareExcel.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/Reportes/Reportes.php";

class ReporteResurtido extends PrepareExcel
{
    public function __construct()
    {
        parent::__construct();
        $this->libro->getProperties()->setTitle('REPORTE DE RESURTIDO'); 
    }
    
    public function preparaExcel( $familias, $sucursales)
    {
        //obteniendo información para la busqueda  de productos y el envío de los reportes
        $reportes = new Reportes;
        $resurtido = new Articulos;
        $resurtidoStock = $resurtido;
        $almacen = new Almacen;
        $condicionesResurtido = $reportes->getReporte('RESURTIDO');
        $contadorHojas = 0;
        $familiasNombreReporte = "";
        if ( sizeof($condicionesResurtido) > 0) {
            // foreach ($condicionesResurtido as $itemResurtido) {
                //se extraen  las sucursales que aplicana la condicion establecida en la tabla de reportes
                $familiasNombreReporte = $familias;
                $explodedFamilia = explode(',', $familias);
                foreach ($explodedFamilia as $k => $familia) {
                    $explodedFamilia[$k] = "'$familia'";
                }
                $familias = implode(',',$explodedFamilia);
                
                $arraySucursales = explode(',',$sucursales);
                foreach ($arraySucursales as $sucursal) {
                    for ($i=1; $i <= 8 ; $i++) { 
                        $this->libro->getActiveSheet()->freezePane('A'.$i);
                    }   
                    $i = 8;
                    $infoSucursal = $almacen->getSucursalesById($sucursal);
                    $this->creaEmptySheet($infoSucursal[0]->DESCRIPCION, $contadorHojas);

                    //obteniendo el resurtido de la sucursal
                    $articulos = $resurtido->getResurtidoArticulos($sucursal,$familias);
                    //agrupando los productos que tienen mismo codigo
                    $articulosAgrupados = array();


                    foreach ($articulos as $k => $articulo) {
                        if ( !isset($articulosAgrupados[$articulo->CODIGOARTICULO]) ) {
                            $articulosAgrupados[$articulo->CODIGOARTICULO]['codigo'] = $articulo->CODIGOARTICULO;
                            $articulosAgrupados[$articulo->CODIGOARTICULO]['almacen'] = $articulo->ALMACEN;
                            $articulosAgrupados[$articulo->CODIGOARTICULO]['articulo'] = utf8_decode( $articulo->DESCRIPCION );
                            $articulosAgrupados[$articulo->CODIGOARTICULO]['familia'] = $articulo->FAMILIA;
                            $articulosAgrupados[$articulo->CODIGOARTICULO]['subfamilia'] = $articulo->SUBFAMILIA;
                            $articulosAgrupados[$articulo->CODIGOARTICULO]['costo'] = $articulo->CTOPROMEDIO;
                            $articulosAgrupados[$articulo->CODIGOARTICULO]['stock'] = $articulo->STOCK;
                        } else {
                            $articulosAgrupados[$articulo->CODIGOARTICULO]['almacen'] .= "/$articulo->ALMACEN";
                            $articulosAgrupados[$articulo->CODIGOARTICULO]['stock'] .="/$articulo->STOCK";
                        }
                        
                    }
                    $this->putLogo("B1",300,200);
                    $this->libro->getActiveSheet()->setCellValue("A4","Reporte de resurtido para ".$infoSucursal[0]->DESCRIPCION);
                    $this->libro->getActiveSheet()->mergeCells("A4:D4");
                    $this->libro->getActiveSheet()->getStyle("A4")->applyFromArray( $this->labelBold );
                    $this->libro->getActiveSheet()->getStyle("A4")->applyFromArray( $this->centrarTexto );
                    $this->libro->getActiveSheet()->setCellValue("A7","CODIGO");
                    $this->libro->getActiveSheet()->setCellValue("B7","DESCRIPCION");
                    $this->libro->getActiveSheet()->setCellValue("C7","FAMILIA");
                    $this->libro->getActiveSheet()->setCellValue("D7","SUBFAMILIA");
                    $this->libro->getActiveSheet()->setCellValue("E7","COSTO");
                    $this->libro->getActiveSheet()->setCellValue("F7", "ORIGEN");
                    $this->libro->getActiveSheet()->setCellValue("G7", "STOCK ALM. CENTRAL");
                    //$this->libro->getactiveSheet()->setCellValue("H7","STOCK ALM. CENTRAL");
                    $this->libro->getActiveSheet()->setAutoFilter("A7:F7");
                    
                    $this->libro->getActiveSheet()->getStyle("A7:H7")->applyFromArray($this->setColorText("FFFFFF") );
                    $this->libro->getActiveSheet()->getStyle("A7:H7")->applyFromArray($this->labelBold);
                    $this->libro->getActiveSheet()->getStyle("A7:H7")->applyFromArray($this->centrarTexto);
                    $this->libro->getActiveSheet()->getStyle("A7:H7")->getFill()->applyFromArray( $this->setColorFill("DF013A") );
                    $this->libro->getActiveSheet()->getStyle("G7:H7")->applyFromArray($this->centrarTexto);
                    foreach ($articulosAgrupados as  $articulo) {
                        $this->libro->getActiveSheet()->setCellValue("A$i", $articulo['codigo']);
                        $this->libro->getActiveSheet()->setCellValue("B$i", $articulo['articulo']);
                        $this->libro->getActiveSheet()->setCellValue("C$i", $articulo['familia']);
                        $this->libro->getActiveSheet()->setCellValue("D$i", $articulo['subfamilia']);
                        $this->libro->getActiveSheet()->setCellValue("E$i", $articulo['costo']);
                        $this->libro->getActiveSheet()->setCellValue("F$i", $articulo['almacen']);
                        $this->libro->getActiveSheet()->setCellValue("G$i", $articulo['stock']);
                        // $stockAlmCentral = $resurtidoStock->getStockAlmacen($articulo['codigo']);
                        //$stockCentralAlmacen = isset( $stockAlmCentral[0]->STOCK ) ? $stockAlmCentral[0]->STOCK : 0;
                        //$this->libro->getActiveSheet()->setCellValue("H$i", $stockCentralAlmacen);

                        
                        
                        $i++;
                    }
                    $this->libro->getActiveSheet()->setCellValue("B$i", "Total Articulos:");
                    $this->libro->getActiveSheet()->setCellValue("C$i", "=SUBTOTAL(3,C8:C".($i-1).")");
                    $this->libro->getActiveSheet()->getStyle("B$i:C$i")->applyFromArray($this->labelBold);
                    $this->libro->getActiveSheet()->getStyle("B$i:C$i")->applyFromArray($this->centrarTexto);
                    

                    $this->libro->getActiveSheet()->getStyle("A7:A$i")->applyFromArray($this->centrarTexto);
                    $this->libro->getActiveSheet()->getStyle("F7:F$i")->applyFromArray($this->centrarTexto);
                $this->libro->getActiveSheet()->getColumnDimension('A')->setAutoSize(false);
                $this->libro->getActiveSheet()->getColumnDimension('A')->setWidth("20");
                $this->libro->getActiveSheet()->getColumnDimension('B')->setAutoSize(false);
                $this->libro->getActiveSheet()->getColumnDimension('B')->setWidth("60");
                $this->libro->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
                $this->libro->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);           
                $this->libro->getActiveSheet()->getColumnDimension('F')->setAutoSize(false);
                $this->libro->getActiveSheet()->getColumnDimension('F')->setWidth("30");    
                $this->libro->getActiveSheet()->getColumnDimension('G')->setAutoSize(false);
                $this->libro->getActiveSheet()->getColumnDimension('G')->setWidth("20");                       
                $this->libro->getActiveSheet()->getColumnDimension('H')->setAutoSize(false);
                $this->libro->getActiveSheet()->getColumnDimension('H')->setWidth("20");                                           
                $this->libro->getActiveSheet()->getStyle("E7:E$i")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00");    
                $this->libro->getActiveSheet()->getStyle("D7:H$i")->applyFromArray($this->centrarTexto);        
                    $contadorHojas++;
                }
            //}
        } else {
            # code...
        }
        
        $reporteTermindao = new PHPExcel_Writer_Excel2007( $this->libro );
        $reporteTermindao->setPreCalculateFormulas(true);
        $reporteTermindao->save("Reporte Resurtido $familiasNombreReporte.xlsx");
    }
}


$reportes = new Reportes;
$infoCorreos = $reportes->getReporte("RESURTIDO");
// var_dump( $infoCorreos );
$arrayCorreos = array();
$familia = "";
foreach ($infoCorreos as $i => $itemReporte) {

    $explodeCorreo = explode(',', $itemReporte['correos']);
    foreach ($explodeCorreo as $correo) {
        if ( ! in_array($correo, $arrayCorreos)) {
            array_push( $arrayCorreos, $correo);
        }
    }
    $resurtidoReporte = new ReporteResurtido;
    $familia = $itemReporte['familias'];
    echo "<br>".$itemReporte['familias'];
    $resurtidoReporte->preparaExcel($familia, $itemReporte['almacenes']);
    $configCorreo = array("descripcionDestinatario" => "SITEX",
                                        "mensaje" => "...",
                                        "pathFile" => "Reporte Resurtido $familia.xlsx",
                                        "subject" => "Reporte de Resurtido",
                                        "correos" => array( "dispersion@matrix.com.mx","raulmatrixxx@hotmail.com","gtealmacen@matrix.com.mx","gerentecomercial@matrix.com.mx"),
                                        // "correos" => array( "sestrada@matrix.com.mx","auxsistemas@matrix.com.mx")
                                        "correos" => $arrayCorreos
                                        );
    $resurtidoReporte->enviarReporte( $configCorreo);                                        
}


    
    