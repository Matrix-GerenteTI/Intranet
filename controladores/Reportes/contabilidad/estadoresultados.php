<?php


require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/Reportes/prepareExcel.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/con_edosfinancieros.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/Ventas/ventas.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/Almacenes/Articulos.php";

class EstadoResultados  extends PrepareExcel
{
    protected $modeloVentas;
    protected $modeloFinanciero;
    protected $modeloArticulos;

    public function __construct()
    {
        parent::__construct();
        $this->creaEmptySheet("Estado Resultados");
        $this->modeloVentas = new Ventas;
        $this->modeloFinanciero = new EdoFinancieros;
        $this->modeloArticulos = new Articulos;

    }
    
    public function generarReporte( $fechaInicio , $fechaFin )
    {
            $familias = ['RIN','LLANTA','ACCESORIO','COLISION','OXIFUEL'];
            $totalVentas = $this->modeloVentas->getTotalVentasMes( $fechaInicio , $fechaFin )[0]->TOTAL;
            $totalCompras = $this->modeloVentas->getTotalComprasEdoResultados ( $fechaInicio , $fechaFin )[0]->IMPORTETOTAL;
            $totalOperativo = $this->modeloFinanciero->getTotalGastosOperativos( $fechaInicio , $fechaFin )[0]['operativos'];
            $totalFinanciero= $this->modeloFinanciero->getTotalGastosFinancieros( $fechaInicio , $fechaFin )[0]['financieros'];

            $totalVentasCredito = $this->modeloVentas->getTotalVentasCredito( $fechaInicio , $fechaFin )[0]->TOTAL;


            //Obteniendo los totales pero hasta el mes en curso
            $totalVentasMasEnCurso = $this->modeloVentas->getTotalVentasMes( $fechaInicio , date('Y-m-d') )[0];
            $totalCompraEnCurso = $this->modeloVentas->getTotalComprasEdoResultados( $fechaInicio , date("Y-m-d"));
            $totalVentasCreditoMasEnCurso = $this->modeloVentas->getTotalVentasCredito( $fechaInicio , date("Y-m-d") )[0];
            $costoInventario = 0;
            // foreach ($totalOperativo as $familia ) {
            //     $listadoCostoInventario = $this->modeloArticulos->getCostoInventario( $familia );
            //     foreach ( $listadoCostoInventario as  $item) {
            //         $costoInventario += ( ($item->EXISTOTAL -( $item->EXISPEDIDOS+$item->EXISPROCESO)  )* $item->CTOPROMEDIO) * 1.16;
            //     }
            // }
            
            //Ya que tenemos el total del inventario hasta el día  generación del reporte se procede a agregarle las ventas, restarle las compras
            $costoInventarioInicial =  27349379.39;
            $costoInventarioFinal =  27819021.56;


            $uriLibro = $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/Reportes/contabilidad/edoresultados.xlsx";
            $libro = new PHPExcel();
            $objReader = PHPExcel_IOFactory::createReader('Excel2007');
            $libro = $objReader->load($uriLibro);
            // Indicamos que se pare en la hoja uno del libro
            $libro->setActiveSheetIndex(0);

            $this->libro = $libro;

            // $this->putLogo("B1", 250 , 200);
            $this->libro->getActiveSheet()->setCellValue("C7",$totalVentas);
            $this->libro->getActiveSheet()->setCellValue("C8", $totalVentasCredito );
            $this->libro->getActiveSheet()->setCellValue("C11", $costoInventarioInicial );
            $this->libro->getActiveSheet()->setCellValue("C12", $totalCompras );
            $this->libro->getActiveSheet()->setCellValue("C13", $costoInventarioFinal );
            $this->libro->getActiveSheet()->setCellValue("D16", $totalOperativo );
            $this->libro->getActiveSheet()->setCellValue("C18", $totalFinanciero );            

            $reporteTerminado = PHPExcel_IOFactory::createWriter($this->libro, 'Excel2007');
            $reporteTerminado->setPreCalculateFormulas(true);
            $nombreLibro = strtotime("now");
            $reporteTerminado->save( $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/Reportes/contabilidad/edoresultados$nombreLibro.xlsx");
            echo "edoresultados$nombreLibro.xlsx";
    }
}


$reporte = new EstadoResultados;
$inicio = explode( "/", $_GET['fInicio']); 
$fin = explode( "/", $_GET['fFin']); 
$reporte->generarReporte( $inicio[2]."-".$inicio[1]."-".$inicio[0], $fin[2]."-".$fin[1]."-".$fin[0] );