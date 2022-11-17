<?php


require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/Reportes/prepareExcel.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/Ventas/ventas.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/Almacenes/Almacen.php";


class MatchAlineaciones extends PrepareExcel
{   
    
    protected $modeloVenta;
    protected $modeloAlmacen;

    public function __construct()
    {
        parent::__construct();
        $this->libro->getProperties()->setTitle('Alineaciones'); 
        $this->modeloVenta = new Ventas;
        $this->modeloAlmacen = new Almacen;
    }

    public function generaReporte(  $anio = 2018 )
    {
        $this->creaEmptySheet( "Alinaciones" );


        //OBTENIENDO LAS IDS DE LOS SUCURSALES QUE TIENEN UN SENSOR
        $idsSucursales = $this->modeloVenta->getSucursalesConSensores();

    
        $columnasMes = ['',['D','E'],['F','G'],['H','I'],['J','K'],['L','M'],['N','O'],['P','Q'],['R','S'],['T','U'],['V','W'],['X','Y'],['Z','AA'],['AB','AC'] ];

        $j = 9 ;

        $this->putLogo("K1", '200',"100");

        $this->libro->getActiveSheet()->setCellValue("G5", "REPORTE DE ALINEACIONES EN VENTAS VS SENSORES CONTADORES DE ALINEACIONES");
        $this->libro->getActiveSheet()->mergeCells("G5:U5");
        $this->libro->getActiveSheet()->getStyle("G5")->applyFromArray( $this->centrarTexto );
        $this->libro->getActiveSheet()->getStyle("G5")->applyFromArray( $this->labelBold);
        

        $this->libro->getActiveSheet()->mergeCells("A7:C8");
        $this->libro->getActiveSheet()->getStyle("A7:".$columnasMes[12][1]."8")->applyFromArray( $this->labelBold);
        $this->libro->getActiveSheet()->getStyle("A7:".$columnasMes[12][1]."8")->applyFromArray( $this->centrarTexto );
        $this->libro->getActiveSheet()->getStyle("A7:".$columnasMes[12][1]."8")->getFill()->applyFromArray( $this->setColorFill("DF013A") );
        $this->libro->getActiveSheet()->getStyle("A7:".$columnasMes[12][1]."8")->applyFromArray( $this->setColorText("ffffff",12) );

        foreach ($idsSucursales as $sucursal) {
            
            $this->libro->getActiveSheet()->setCellValue("A7", "SUCURSALES");


            $descripcionSucursal = $this->modeloAlmacen->getSucursalesById( $sucursal['idsucursal'])[0];

            $this->libro->getActiveSheet()->setCellValue("A$j",  $descripcionSucursal->DESCRIPCION);
            $this->libro->getActiveSheet()->getStyle("A$j")->applyFromArray( $this->labelBold);
            $this->libro->getActiveSheet()->mergeCells("A$j:C$j");


            for ($i=1; $i <= 12 ; $i++) { 
                $listaAlineacionesVentas = $this->modeloVenta->getAlineacionesBySucursal( $sucursal['idsucursal'], $i ,$anio);
                $listaAlineacionesSensor = $this->modeloVenta->getAlineacionesPorSensores( $sucursal['idsucursal'], $i , $anio);

                $cantSensor = sizeof( $listaAlineacionesSensor ) ;
                $cantVentas = sizeof( $listaAlineacionesVentas );

                $this->libro->getActiveSheet()->mergeCells($columnasMes[$i][0]."7:".$columnasMes[$i][1]."7");
                $this->libro->getActiveSheet()->setCellValue($columnasMes[$i][0]."7", $this->getMesAsString( $i ) );


                $this->libro->getActiveSheet()->setCellValue($columnasMes[$i][0]."8", "A.VENTA");
                $this->libro->getActiveSheet()->setCellValue($columnasMes[$i][1]."8", "A. SENSOR");
                if (  $cantVentas > 0) {
                    $this->libro->getActiveSheet()->setCellValue($columnasMes[$i][0]."$j", $cantVentas);
                    $this->libro->getActiveSheet()->setCellValue($columnasMes[$i][1]."$j", $cantVentas);
                }else{
                    $this->libro->getActiveSheet()->setCellValue($columnasMes[$i][0]."$j", 0);
                    $this->libro->getActiveSheet()->setCellValue($columnasMes[$i][1]."$j", 0);
                }


                if ( $cantSensor ) {
                    $this->libro->getActiveSheet()->setCellValue($columnasMes[$i][0]."$j", $cantVentas);
                    $this->libro->getActiveSheet()->setCellValue($columnasMes[$i][1]."$j", $cantSensor);
                } else {
                    $this->libro->getActiveSheet()->setCellValue($columnasMes[$i][0]."$j", 0);
                    $this->libro->getActiveSheet()->setCellValue($columnasMes[$i][1]."$j", 0);
                }

                $this->libro->getActiveSheet()->getStyle($columnasMes[$i][0]."$j:".$columnasMes[$i][1]."$j")->applyFromArray( $this->centrarTexto );
                $this->libro->getActiveSheet()->getStyle($columnasMes[$i][0]."$j:".$columnasMes[$i][1]."$j")->applyFromArray( $this->labelBold );
                
            }


            echo $j."<br>";
            $j++;

        }
        $this->libro->getActiveSheet()->getStyle("A7:".$columnasMes[12][1].($j-1) )->applyFromArray( $this->bordes );

        $reporteTerminado = new \PHPExcel_Writer_Excel2007( $this->libro);
        $reporteTerminado->setPreCalculateFormulas(true);
        $reporteTerminado->setIncludeCharts(TRUE);
         $reporteTerminado->save($_SERVER['DOCUMENT_ROOT']."/intranet/controladores/ventas/reportes/alineacion.xlsx");
        $ubicacion = "http://servermatrixxxb.ddns.net:8181/intranet/controladores/ventas/reportes/alineacion.xlsx";
        echo "<a href='$ubicacion'>Descargar</a>";
    }


    

}


$alineacion = new MatchAlineaciones;
$alineacion->generaReporte(2019);


$configCorreo = array("descripcionDestinatario" => "Acumulado de Ventas por Familia",
"mensaje" => "SITEX",
"pathFile" => $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/ventas/reportes/alineacion.xlsx",
"subject" => "Alineaciones Vs Sensores",
//"correos" => array( "sestrada@matrix.com.mx")
"correos" => array( "sestrada@matrix.com.mx","gerenteadministrativo@matrix.com.mx","raulmatrixxx@hotmail.com",'direccionestrategica@matrix.com.mx','software@matrix.com.mx')
);
$alineacion->enviarReporte( $configCorreo);









