<?php
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/CuentasPorPagar.php";

class CXPController  
{
        public function getFacturasPorSaldar( )
    {
     
        $modeloCxP = new CuentasPorPagar;

        $listaFacturas = $modeloCxP->getFacturasPorSaldar();
        $listaProveedores = [];
        foreach ($listaFacturas as $i => $factura) {
            $factura->CF1 = utf8_encode( $factura->CF1);
            $listaProveedores[ $factura->CODIGO ]['proveedor'] = $factura->CF1;
            $listaProveedores[ $factura->CODIGO ]['id'] = $factura->ID;
            if ( $factura->NUMFACT != -1 ) {
                    $fechaEmision = new DateTime( $factura->FECHAEMISION);
                    $fechaVencimiento = new DateTime( $factura->FECHAVTO);                    
                    $fechaActual = new DateTime( date("Y-m-d") ) ;         
                    $diasDeCredito = $fechaVencimiento->diff( $fechaEmision )->format("%a");
                    $diasTranscurridos = $fechaActual->diff( $fechaEmision )->format("%a")   ;
            // agregando los dias de vencimiento y el estado de la factura                    
                    $diasVencimiento = $diasTranscurridos - $diasDeCredito;
                    $factura->DIASVENCIMIENTO = $diasVencimiento;
                    $factura->STATUS = $diasVencimiento < 0 ? "S/VENCER" : "VENCIDO";

                if( !isset( $listaProveedores[$factura->CODIGO]['total_docto'] ) ){
                    $listaProveedores[$factura->CODIGO]['total_docto'] = $factura->IMPORTE;
                    $listaProveedores[ $factura->CODIGO ]['facturas'] = [ $factura ];
                }else{
                    $listaProveedores[$factura->CODIGO]['total_docto'] += $factura->IMPORTE;
                    array_push( $listaProveedores[ $factura->CODIGO ]['facturas'], $factura );
                }
            }
        }

        $listaProveedores =  $this->sortImportesYVencimientos( $listaProveedores );

        return ( $listaProveedores );

    }

    public function sortImportesYVencimientos( $data )
    {
        $proveedorAnt = [];
        $index =[];
        $proveedores = [];

        foreach ($data as  $proveedor) {
            array_push(  $proveedores , $proveedor );
        }

        for($i = 0 ;$i< sizeof( $proveedores ) ; $i++ ) {
            for($j = 0 ; $j< sizeof( $proveedores ) ; $j++ ) {
                // if ( in_array( $j , $index )) {
                    
                //     continue;
                // }
                
                if ( $proveedores[$i]['total_docto'] > $proveedores[$j]['total_docto']) {
                    $proveedorAnt = $proveedores[$i];
                    $proveedores[$i] = $proveedores[$j];
                    $proveedores[$j] = $proveedorAnt;

                }
            }
        }
        // reacomodando los indices
        $newData = [];

        foreach ($proveedores as $i => $proveedor) {
            $newData[$proveedor['id']."_" ] = $proveedor;
            // echo "<br>".$newData[$proveedor['id'] ]['total_docto']." -----  ".$proveedor['total_docto'];
        }
        // $data = null;
        $proveedores = $newData;
        
        //ordenando las facturas de menor dia de vencimiento al mayor
        $factAnterior = [];
        foreach ($proveedores as $i => $proveedor) {
            foreach ($proveedores[$i]['facturas'] as $j => $factura) {
                foreach ($proveedores[$i]['facturas'] as $k => $facturaIterador) {
                    if ( $factura->DIASVENCIMIENTO < $facturaIterador->DIASVENCIMIENTO) {
                        $factAnterior = $proveedores[$i]['facturas'][$j];
                        $proveedores[$i]['facturas'][$j] = $facturaIterador;
                        $proveedores[$i]['facturas'][$k] = $factAnterior;
                    }
                }
            }
        }
        //ordenando facturas por fecha de vencimiento más proxima
        $factAnterior = [];
        foreach ($proveedores as $i => $proveedor) {
            foreach ($proveedores[$i]['facturas'] as $j => $factura) {
                foreach ($proveedores[$i]['facturas'] as $k => $facturaIterador) {
                    if ( ( $factura->DIASVENCIMIENTO > $facturaIterador->DIASVENCIMIENTO) && $factura->DIASVENCIMIENTO  < 0  ) {
                        $factAnterior = $proveedores[$i]['facturas'][$j];
                        $proveedores[$i]['facturas'][$j] = $facturaIterador;
                        $proveedores[$i]['facturas'][$k] = $factAnterior;
                    }
                }
            }
        }        

        return $proveedores;
    }    
}


$cxpController = new CXPController;
$listaDeudas = $cxpController->getFacturasPorSaldar();

echo "<table>
                <tr>
                    <th>PROVEEDOR</th>
                    <th>FACTURA</th>
                    <th>FECHA_MOVIMIENTO</th>
                    <th>FECHA_VTO<th>
                    <th>DIAS_VENCIMIENTO</th>
                    <th>IMPORTE</th>
                    <th>STATUS</th>";
foreach ($listaDeudas as $cod => $proveedor) {
    foreach ($proveedor['facturas'] as $factura) {
        echo "<tr>
                        <td>".$proveedor['proveedor']."</td>
                        <td>".$factura->NUMFACT."</td>
                        <td>".$factura->FECHAEMISION."</td>
                        <td>".$factura->FECHAVTO."</td>
                        <td>".$factura->DIASVENCIMIENTO."</td>
                        <td>".$factura->IMPORTE."</td>
                        <td>".$factura->STATUS."</td>
                    </tr>";
    }
}