<?php

require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/Ventas/vendedores.php";


class VendedoresController  
{
    protected $modeloVendedor;

    public function __construct()
    {
        $this->modeloVendedor = new Vendedores;
    }
    public function getAll()
    {
        $listaVendedores = $this->modeloVendedor->getAll();
        $vendedores = [];
        foreach ($listaVendedores as $i => $vendedor) {
            //Quitando lo que está antes de los :: 
            $correctNombre = explode("::", $vendedor->NOMBREVENDEDOR );
            array_push( $vendedores , ['name' => utf8_encode( isset( $correctNombre[1] ) ? $correctNombre[1]  : $correctNombre[0] ) , 'id' => $vendedor->ID  ] );
          
        }

        return $vendedores;
    }

    public function getComisiones( $mes , $anio)
    {
        $listaVentasVendedores = $this->modeloVendedor->getVentasPorVendedor($mes , $anio );
        $listaComisiones = [];
        $metaVendedor = 500000;
        foreach ($listaVentasVendedores as $i => $venta) {

            //se verififica que las ventas son sin firma
            $precioVenta = number_format( $venta->PRECIO, 2,".","");
            $precioMinimo = floatval( filter_var( $venta->PVP3, FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION) );
            
            $comisionConFirma = 0;
            $comisionSinFirma = 0;
            $hasValvula = true;

            if ( $precioVenta >= $precioMinimo) {
                $comisionSinFirma = $venta->TOTAL * 0.01;
            } else {
                    //validando que no use firmas
                    if($venta->SUBFAMILIA !='VALVULA'){
                        $hasValvula = false;
                    }
            }
            


            if ( isset( $listaComisiones[$venta->ID ] ) ) {
                $listaComisiones[ $venta->ID]['vendido'] += round($venta->TOTAL,2);
                $listaComisiones[ $venta->ID]['comisionReal'] += round(($comisionSinFirma));
                $listaComisiones[ $venta->ID ]['comisionConFirma'] += round( ($venta->TOTAL * 0.01)  / 1, 2);
                $listaComisiones[ $venta->ID ]['nFirmas'] += !$hasValvula ?  1 : 0;
                $listaComisiones[ $venta->ID ]['progreso'] = round( $listaComisiones[ $venta->ID]['vendido'] /$metaVendedor,2);
                
            } else {
                
                $listaComisiones[ $venta->ID]['vendedor'] = mb_convert_encoding( $venta->VENDEDOR , "UTF-8");
                $listaComisiones[ $venta->ID]['vendido'] = round($venta->TOTAL,2);
                $listaComisiones[  $venta->ID]['comisionReal'] =  round($comisionSinFirma,2);
                $listaComisiones[ $venta->ID ]['nFirmas'] = !$hasValvula ?  1 : 0;
                $listaComisiones[ $venta->ID ]['comisionConFirma'] =  round( ($venta->TOTAL * 0.01)  / 1, 2);
                $listaComisiones[ $venta->ID ]['progreso'] = round( $listaComisiones[ $venta->ID]['vendido'] /$metaVendedor,2);
            }
            
        }

        $vendedores = [];
        foreach ($listaComisiones as $i => $vendedor) {
            array_push( $vendedores , $vendedor );
        }
        
        

        usort($vendedores, [$this,'cmp']); 
        
        echo "<pre>";
        echo var_dump($vendedores);
        echo "</pre>";
    }

    


        public function cmp($a, $b)
        {
            if ($a['vendido'] == $b['vendido']) {
                return 0;
            }
            return ($a['vendido'] > $b['vendido']) ? -1 : 1;
        }

}

$test = new VendedoresController();
$test->getComisiones(7,2020);