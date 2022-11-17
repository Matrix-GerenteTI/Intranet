<?php

require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/Reportes/prepareExcel.php";
require_once $_SERVER[''];

class ReporteVentas extends PrepareExcel
{

    protected $modeloVentas;


    public function __construct() {
        parent::__construct();
        $this->libro->getProperties()->setTitle('Reporte de ventas en linea'); 
        $this->modeloVentas = new Ventas;
    }

    public function generaReporte( $mes , $anio )
    {
        $listaVentas = $this->modeloVentas->getVentasEnLinea( $mes , $anio);
        
        //Creando e libro de excel para el reporte
        $this->creaEmptySheet( "Lista de ventas" );
        foreach ($listaVentas as $venta) {
            $this->libro->getActiveSheet()->setCellValue("A$i", $venta['folio_venta']);
            $this->libro->getActiveSheet()->setCellValue("B$i", $venta['nombre']." ".$venta['appaterno']." ".$venta['apmaterno'] );

            //Obteniendo la información de la venta en la base de datos de prediction
            $totalesVenta = $this->modeloVentas->getMontoVentaEnlinea( $venta['folio_venta'] );
            //Comprobando que se haya registrado  la venta
            if ( sizeof( $totalesVenta) ) {
                $this->libro->getActiveSheet()->setCellValue("C$i", $totalesVenta[0]->SUBTOT);
            }
            $i++;
        }
    }
}
