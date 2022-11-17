<?php


require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/CuentasPorPagar.php";


$cxp = new CuentasPorPagar;
$listadoFacturas_pagos = $cxp->getTodasFacturas();
$mesAnio = explode( "/" , $_GET['periodo']);
$mes = $mesAnio[0];
$anio = $mesAnio[1];

$listaFacurasDocumentadas  =  [];
foreach ( $listadoFacturas_pagos as $i => $factura) {
    $numeroDocumento = explode( '-/1' , $factura->NUMFACT );
            $movimientoAFactura = $cxp->getPagosAplicacdos( $factura->IMP_FACTU_HISTORCP , $factura->IMP_COBRO_HISTORCP );
            
        if( !isset($movimientoAFactura[0]->FK1MCRM_PROSPECTOS ) ){
            continue;
        }
            // echo "$factura->IMP_FACTU_HISTORCP , $factura->IMP_COBRO_HISTORCP <br>";
            if ( isset( $listaFacurasDocumentadas[ $movimientoAFactura[0]->FK1MCRM_PROSPECTOS][ $numeroDocumento[0] ][0]) ) {
                //anexando 
                array_push( $listaFacurasDocumentadas[ $movimientoAFactura[0]->FK1MCRM_PROSPECTOS][ $numeroDocumento[0] ]  , $movimientoAFactura );
            } else {
                $listaFacurasDocumentadas[ $movimientoAFactura[0]->FK1MCRM_PROSPECTOS][ $numeroDocumento[0]]  = $movimientoAFactura;
            }
            
            // $listaFacurasDocumentadas[ $movimientoAFactura[0]->FK1MCRM_PROSPECTOS][ $factura->IMP_COBRO_HISTORCP ]  = $movimientoAFactura;
            $listaFacurasDocumentadas[ $movimientoAFactura[0]->FK1MCRM_PROSPECTOS][ $numeroDocumento[0] ]['pagado'] = $factura->REC_PAGADO;
            foreach ($movimientoAFactura    as $i => $movtos ) {
                $movimientoAFactura[$i]->CONCEPTO= utf8_encode( $movtos->CONCEPTO );
                $movimientoAFactura[$i]->NDOCUMENTO = utf8_encode( $movtos->NDOCUMENTO );
                $movimientoAFactura[$i]->PYM_NOMBRE = utf8_encode( $movtos->PYM_NOMBRE );
            }
            $listaFacurasDocumentadas[ $movimientoAFactura[0]->FK1MCRM_PROSPECTOS][ $numeroDocumento[0] ]['proveedor'] = utf8_encode( $movimientoAFactura[0]->PYM_NOMBRE ); 
            $listaFacurasDocumentadas[ $movimientoAFactura[0]->FK1MCRM_PROSPECTOS][ $numeroDocumento[0] ]['monto_factura'] = utf8_encode( $factura->IMPORTE ); 
            
            // $listaFacurasDocumentadas[ $factura->FK1MCRM_PROSPECTOS][ $numeroDocumento[0] ]['monto'] = $factura->IMPORTEFACTU != 0 ? -$factura->IMPORTEFACTU : $factura->IMPORTECOBRO;
        
    // $extractFecha = explode("-", $factura->FECHAMOVI );
    // $extractFecha[1] /= 1;
    // if ( $extractFecha[0]  <= $anio) {
    //     if ( ( $extractFecha[1]  <= $mes && $extractFecha[0] == $anio )  || ( $extractFecha[0] < $anio  )  ) {
    //     }
    // }

}
//  echo json_encode( $listaFacurasDocumentadas );
//  exit();

// recorriendo el arreglo con los valores agrupados y obtener  unicamente los pagos que fueron aplicados 
foreach ( $listaFacurasDocumentadas as $proveedorID => $facturasProveedor) {
    foreach ($facturasProveedor as $numFactura => $factura) {
        $cantMovimientos = sizeof($factura) ;
        foreach ($factura as $i => $movimiento) {
            // var_dump( $movimiento );
            // echo "<br><br>";
            //verifica si el item contiene un array en la posición 0, quiere decir que es un movimiento completo
            if (  is_object(  $movimiento) ) { //Hay un solo item y se procede a la comparación
                if ( !isset($movimiento->FECHAMOVI ) ) {
                continue;
                 }

                 $extractFecha = explode( "-", $movimiento->FECHAMOVI );
                    if ( $extractFecha[0] <= $anio) {
                        if ( ( $extractFecha[0] < $anio )  || ( $extractFecha[0] == $anio && $extractFecha[1] <= $mes ) ) {
                            if ( !isset( $listaFacurasDocumentadas[ $proveedorID][ $numFactura ]['monto_factura'] ) ) {
                                if ( isset(  $listaFacurasDocumentadas[ $proveedorID][ $numFactura ] ) ) {
                                    
                                    if ( $listaFacurasDocumentadas[ $proveedorID][ $numFactura ]['pagado']  == 'T'  ) {
                                        if (  $extractFecha[0] < $anio ) {
                                            unset( $listaFacurasDocumentadas[ $proveedorID][ $numFactura ]);
                                        }else if( $extractFecha[1] <= $mes ){
                                            unset( $listaFacurasDocumentadas[ $proveedorID][ $numFactura ]);
                                        }

                                        if( empty($listaFacurasDocumentadas[ $proveedorID] ) ){
                                            unset( $listaFacurasDocumentadas[ $proveedorID] );
                                        } else if( !isset($listaFacurasDocumentadas[ $proveedorID][0]) ){
                                            unset( $listaFacurasDocumentadas[ $proveedorID] );
                                        }                           
                                    }                            
                                }
                            } else {

                                 if( ! isset($listaFacurasDocumentadas[ $proveedorID][ $numFactura ]['pagado']  ) ){
                                    //  echo $numFactura."<br>";
                                     unset( $listaFacurasDocumentadas[ $proveedorID][ $numFactura ] );
                                    continue;
                                 }

                                if( $listaFacurasDocumentadas[ $proveedorID][ $numFactura ]['monto_factura']  == 0 || $listaFacurasDocumentadas[ $proveedorID][ $numFactura ]['pagado']  == 'T' ){
                                    if (  $extractFecha[0] < $anio ) {
                                        unset( $listaFacurasDocumentadas[ $proveedorID][ $numFactura ]);
                                    }else if( $extractFecha[1] <= $mes ){
                                        // if ( $i == $cantMovimientos-1 ) { //solo aplica 
                                            unset( $listaFacurasDocumentadas[ $proveedorID][ $numFactura ]);
                                        // }
                                        
                                    }

                                    if( empty($listaFacurasDocumentadas[ $proveedorID] ) ){
                                        unset( $listaFacurasDocumentadas[ $proveedorID] );
                                    }
                                }
                            }

                                 if( ! isset($listaFacurasDocumentadas[ $proveedorID][ $numFactura ]['pagado']  ) ){
                                    //  echo $numFactura."<br>";
                                     unset( $listaFacurasDocumentadas[ $proveedorID][ $numFactura ] );
                                    continue;
                                 }

                            if ( $movimiento->IMPORTEFACTU != 0  && $listaFacurasDocumentadas[ $proveedorID][ $numFactura ]['pagado'] != 'F'  ) {
                                $listaFacurasDocumentadas[ $proveedorID][ $numFactura ]['monto_factura']  = $movimiento->IMPORTEFACTU;
                                unset( $listaFacurasDocumentadas[ $proveedorID][ $numFactura ][$i] );
                                if( ! isset( $listaFacurasDocumentadas[ $proveedorID][ $numFactura ] ) ){
                                    unset( $listaFacurasDocumentadas[ $proveedorID][ $numFactura ] );
                                }
                            }else{
                                if ( $movimiento->IMPORTEFACTU != 0 ) {
                                    $listaFacurasDocumentadas[ $proveedorID][ $numFactura ]['monto_factura']  = $movimiento->IMPORTEFACTU;
                                }
                            }
                        }else{
                            unset( $listaFacurasDocumentadas[ $proveedorID][ $numFactura ][$i]);
                            if ( !isset( $listaFacurasDocumentadas[ $proveedorID][ $numFactura ]['monto_factura'] ) ) {

                                unset( $listaFacurasDocumentadas[ $proveedorID][ $numFactura ]);
                            }elseif( !isset( $listaFacurasDocumentadas[ $proveedorID][ $numFactura ][0] )  ){
                                unset( $listaFacurasDocumentadas[ $proveedorID][ $numFactura ]);
                            }
                        }
                    }else{
                        unset( $listaFacurasDocumentadas[ $proveedorID] );
                    }       
            }else{

                if ( is_array( $movimiento) ) {
                    foreach ( $movimiento as $x => $mvtoFactuCobro) {
                        //comprobando que no sea el dato repetido del  valor de la factura a la cual se está abonando
                        if ( $mvtoFactuCobro->IMPORTECOBRO == 0) {
                            unset( $listaFacurasDocumentadas[ $proveedorID ][$numFactura][$i][$x] );
                        }
                    }
                    if ( empty( $listaFacurasDocumentadas[ $proveedorID ][$numFactura]) ) {
                        unset(  $listaFacurasDocumentadas[ $proveedorID ][$numFactura]  );
                    }
                }

            }


            
        }
        if ( empty( $listaFacurasDocumentadas[ $proveedorID ] )  ) {
            unset( $listaFacurasDocumentadas[ $proveedorID ] );
        }
    }
}





//DEPURANDO LOS VALORES 

foreach ( $listaFacurasDocumentadas  as $proveedorID => $facturas) {
    foreach ( $facturas as $nfactura => $movimientos) {
        foreach ($movimientos as $i => $movimiento) {
            if ( empty( $listaFacurasDocumentadas[$proveedorID][$nfactura][$i] ) ) {
                unset( $listaFacurasDocumentadas[$proveedorID][$nfactura][$i]  );
            } else if( is_array( $listaFacurasDocumentadas[$proveedorID][$nfactura][$i] ) ) { //es un arreglo de valores
                // echo "<br><br>";
                // var_dump( $listaFacurasDocumentadas[$proveedorID][$nfactura][$i] );
                foreach ($listaFacurasDocumentadas[$proveedorID][$nfactura][$i] as $x => $itemMovimiento) {
                    array_push( $listaFacurasDocumentadas[$proveedorID][$nfactura] , $itemMovimiento);
                }
                unset( $listaFacurasDocumentadas[$proveedorID][$nfactura][$i]  );
            }
            
        }
    }
}

echo json_encode( $listaFacurasDocumentadas );