<?php
 
 require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/Reportes/prepareExcel.php";
 require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/Almacenes/Almacen.php";


 class ContadorMovimeintos extends PrepareExcel
 {
     
        protected $modeloAlmacen ;

        public function __construct( ) {
            parent::__construct();

            $this->modeloAlmacen = new Almacen;

            $this->libro->getProperties()->setTitle('MOVIMIENTOS'); 
        }


        public function prepareExcel( )
        {
            $listaMovimientos = $this->modeloAlmacen->getTraspasosCedimSucursales();

            $agrupados = [];
            $contandoPorFechas= [] ;
            foreach ( $listaMovimientos as $i => $movimiento) {
                if ( !isset( $agrupados[$movimiento->ORIGEN] ) ) {
                    $agrupados[$movimiento->ORIGEN ]= [ $movimiento ];
                } else {
                    array_push( $agrupados[$movimiento->ORIGEN ], $movimiento);
                }

                if (  !isset( $contandoPorFechas[$movimiento->FECHA][ $movimiento->ORIGEN ][$movimiento->DESTINO]  ) ){
                    $contandoPorFechas[$movimiento->FECHA][ $movimiento->ORIGEN ][$movimiento->DESTINO]  = 1;
                }else{
                    $contandoPorFechas[$movimiento->FECHA][ $movimiento->ORIGEN ][$movimiento->DESTINO]  += 1;
                }
               
                //vamos a agurpar 
            }

            $this->creaEmptySheet( "MOVIMIENTOS CEDIM", 0 );

            $i = 9;
            foreach ( $agrupados as $origen => $destinos) {
                
                foreach ($destinos as $j => $movimiento) {
                    $this->libro->getActiveSheet()->setCellValue("A$i", $movimiento->ORIGEN);
                    $this->libro->getActiveSheet()->setCellValue("B$i", $movimiento->DESTINO);
                    $this->libro->getActiveSheet()->setCellValue("C$i", $movimiento->FECHA);
                    $this->libro->getActiveSheet()->setCellValue("D$i", $movimiento->HORAMOVTO);
                    $i++;
                }

                $i = 9;
                foreach ($contandoPorFechas as $fecha => $cedim) {
                    foreach ($cedim as $alm => $sucursales) {
                        foreach ($sucursales as $sucursal => $cantidad) {
                            $this->libro->getActiveSheet()->setCellValue("F$i", $alm);
                            $this->libro->getActiveSheet()->setCellValue("G$i", $sucursal);
                            $this->libro->getActiveSheet()->setCellValue("H$i", $fecha);
                            $this->libro->getActiveSheet()->setCellValue("I$i", $cantidad);
                            $i++;                            
                        }
                    }
                }
                    
            }


            $reporteTermindao = new PHPExcel_Writer_Excel2007( $this->libro );
            $reporteTermindao->setPreCalculateFormulas(true);
            $reporteTermindao->save("traspasos.xlsx");

        }
 }
 

 $reporte = new ContadorMovimeintos;
 $reporte->prepareExcel();