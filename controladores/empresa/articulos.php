<?php
error_reporting( E_ERROR  );
 ini_get("precision",2);
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/almacenes/Articulos.php";

class ArticulosController  
{

    const  MONTHS = ['','Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
    protected $modeloArticulo; 

    public function __construct()
    {
        $this->modeloArticulo = new Articulos;    
    }
    public function getValuado()
    {
        $listaArticulos = $this->modeloArticulo->getValuado();

        // $codAnt = '';
        // $diasInv = 0;
        // foreach ( $listaArticulos as $i => $articulo) {
        //     //obteniendo la ultima compra
        //     if ( $codAnt != $articulo->CODIGOART ) {
        //         $codAnt = $articulo->CODIGOART;
        //         $diasInv =  $this->modeloArticulo ->getUltimaCompra( $articulo->CODIGOART )[0]->DIF ;
        //         $listaArticulos[$i]->DIASINV = $diasInv;
                
        //     } else {
               
        //         $listaArticulos[$i]->DIASINV = $diasInv;
        //     }
            
        //     $listaArticulos[$i]->CODIGOART = mb_convert_encoding( $articulo->CODIGOART, 'UTF-8' );
        //     $listaArticulos[$i]->DESCRIP = mb_convert_encoding( $articulo->DESCRIP, 'UTF-8' );
        //     $listaArticulos[$i]->FAM = mb_convert_encoding( $articulo->FAM, 'UTF-8' );
        //     $listaArticulos[$i]->SUBFAMILIA = mb_convert_encoding( $articulo->SUBFAMILIA, 'UTF-8' );

        // }

        return $listaArticulos;
    }

    public function getArticulosConPrecios( $params )
    {
       $articulos =  $this->modeloArticulo->buscaArticulosConPvp( $params );
        $iva = .16;
       $articulosAgrupados = [];
       foreach ($articulos as $i => $articulo) {
           $articulos[$i]->CODIGO = mb_convert_encoding( $articulo->CODIGO , "UTF-8");
           $articulos[$i]->DESCRIPCION = mb_convert_encoding( $articulo->DESCRIPCION , "UTF-8");
           $articulos[$i]->FAMILIA = mb_convert_encoding( $articulo->FAMILIA , "UTF-8");
           $articulos[$i]->SUBFAMILIA = mb_convert_encoding( $articulo->SUBFAMILIA , "UTF-8");
           $costoulitmo = $this->modeloArticulo->buscarCostoUltimo($articulo->CODIGO);
           $costoulitmo = $costoulitmo[0];
        //    $articulos[$i]->COSTO = $costoulitmo->COSTO + ($costoulitmo->COSTO * 0.16);

        //    $articulos[$i]->COSTO = $costoulitmo->COSTO;
            if (  isset( $articulosAgrupados[ $articulo->CODIGO ] ) ) {
                if($articulo->ZONA=='ALTOS')
                    $articulosAgrupados[ $articulo->CODIGO ]['stockaltos'] +=  $articulo->EXISTOTAL;
                if($articulo->ZONA=='CENTRO')
                    $articulosAgrupados[ $articulo->CODIGO ]['stockcentro'] +=  $articulo->EXISTOTAL;
                if($articulo->ZONA=='COSTA')
                    $articulosAgrupados[ $articulo->CODIGO ]['stockcosta'] +=  $articulo->EXISTOTAL;
                $articulosAgrupados[ $articulo->CODIGO ]['stock'] +=  $articulo->EXISTOTAL;
                array_push($articulosAgrupados[ $articulo->CODIGO ]['sucursalStock'] , [ 'sucursal' => $articulo->ALMACEN , 'cantidad' => $articulo->EXISTOTAL]);
            } else {
                $diasInv = 'Inv. Ini';
                if ($articulo->ULTCOMPRA != null ) {
                    $ultCompra = new DateTime( $articulo->ULTCOMPRA  );
                    $dateAct = new DateTime( date("Y-m-d") );
                    $diasInv  = $dateAct->diff($ultCompra)->format('%a');
                }
                //obteniendo la cantidad de items vendidos por mes
                $listaVendidos = $this->modeloArticulo->getCantidadVendid( $articulo->CODIGO  );

                $labels = [];
                $dataSets = [];
                foreach ($listaVendidos as $x => $vendido) {
                    array_push( $labels , self::MONTHS[ $vendido->MES] );
                    array_push(  $dataSets, $vendido->CANT );
                }
                
                $articulosAgrupados[ $articulo->CODIGO ]['stockaltos'] =  0;
                $articulosAgrupados[ $articulo->CODIGO ]['stockcentro'] =  0;
                $articulosAgrupados[ $articulo->CODIGO ]['stockcosta'] =  0;
                if($articulo->ZONA=='ALTOS')
                    $articulosAgrupados[ $articulo->CODIGO ]['stockaltos'] =  $articulo->EXISTOTAL;
                if($articulo->ZONA=='CENTRO')
                    $articulosAgrupados[ $articulo->CODIGO ]['stockcentro'] =  $articulo->EXISTOTAL;
                if($articulo->ZONA=='COSTA')
                    $articulosAgrupados[ $articulo->CODIGO ]['stockcosta'] +=  $articulo->EXISTOTAL;
                $articulosAgrupados[ $articulo->CODIGO ]['stock'] =  $articulo->EXISTOTAL;
                // $articulosAgrupados[ $articulo->CODIGO ]["costo"] = " TESTEO". (round(($articulo->PVP1 - $costoulitmo->COSTO) / $costoulitmo->COSTO, 2)*100);
                $articulosAgrupados[ $articulo->CODIGO ]["costo"] =  round($costoulitmo->COSTO,2);
                $articulosAgrupados[ $articulo->CODIGO ]["diasInv"] =  $diasInv;
                $articulosAgrupados[ $articulo->CODIGO ]["diasInv"] =  $diasInv;
                $articulosAgrupados[ $articulo->CODIGO ]["utilidad1"] =  round( ($articulo->PVP1 - $costoulitmo->COSTO ) /$costoulitmo->COSTO , 2) * 100 ;
                $articulosAgrupados[ $articulo->CODIGO ]["utilidad2"] =  round( ($articulo->PVP2 - $costoulitmo->COSTO ) /$costoulitmo->COSTO , 2) * 100 ;
                $articulosAgrupados[ $articulo->CODIGO ]["utilidad3"] =  round( ($articulo->PVP3 - $costoulitmo->COSTO ) /$costoulitmo->COSTO , 2) * 100 ;
                $articulosAgrupados[ $articulo->CODIGO ]["utilidad4"] =  round( ($articulo->PVP4 - $costoulitmo->COSTO ) /$costoulitmo->COSTO , 2) * 100 ;
                $articulosAgrupados[ $articulo->CODIGO ]["utilidad5"] =  round( ($articulo->PVP5 - $costoulitmo->COSTO ) /$costoulitmo->COSTO , 2) * 100 ;
                $articulosAgrupados[ $articulo->CODIGO ]["utilidad6"] =  round( ($articulo->PVP6 - $costoulitmo->COSTO ) /$costoulitmo->COSTO , 2) * 100 ;
                $articulosAgrupados[ $articulo->CODIGO ]["utilidad7"] =  round( ($articulo->PVP7 - $costoulitmo->COSTO ) /$costoulitmo->COSTO , 2) * 100 ;
                $articulosAgrupados[ $articulo->CODIGO ]['sucursalStock'] = [ ['sucursal' => $articulo->ALMACEN , 'cantidad' =>$articulo->EXISTOTAL ] ];
                //cambiando precio por juego
                if ( $articulo->FAMILIA == 'RIN') {
                    $articulo->PVP1 *= 4;
                    $articulo->PVP2 *= 4;
                    $articulo->PVP3 *= 4;
                    $articulo->PVP4 *= 4;
                    $articulo->PVP5 *= 4;
                    $articulo->PVP6 *= 4;
                    $articulo->PVP7 *= 4;
                    $articulosAgrupados[ $articulo->CODIGO ]["detalle"] = $articulo;
                    // $articulosAgrupados[ $articulo->CODIGO ]["costoJuego"] =  round( ($articulo->COSTO*4),2);
                    $articulosAgrupados[ $articulo->CODIGO ]["costoJuego"] =  round($costoulitmo->COSTO*4, 2);
                }else{
                    $articulosAgrupados[ $articulo->CODIGO ]["detalle"] = $articulo;
                }
                if($articulo->PVP1 < ($articulo->PVP3*1.1182))
                    $articulosAgrupados[ $articulo->CODIGO ]["msi6"] =  round( ($articulo->PVP3 * 1.1182) ,2 );
                else
                    $articulosAgrupados[ $articulo->CODIGO ]["msi6"] =  round( ($articulo->PVP1) ,2 );
                
                if($articulo->PVP1 < ($articulo->PVP3*1.1934))
                    $articulosAgrupados[ $articulo->CODIGO ]["msi12"] =  round( ($articulo->PVP3 * 1.1934) ,2 );
                else
                    $articulosAgrupados[ $articulo->CODIGO ]["msi12"] =  round( ($articulo->PVP1) ,2 );         

                $articulosAgrupados[ $articulo->CODIGO ]["chart"]['labels'] = $labels;
                $articulosAgrupados[ $articulo->CODIGO ]["chart"]['datasets'] = $dataSets;                
            }
            
       }

       function cmp($a, $b) {
                return $b["diasInv"] - $a["diasInv"];
        }
        usort($articulosAgrupados, "cmp");

    //    var_dump( $articulos );
       return $articulosAgrupados;
    }

    public function getArticulosAgrup( $params )
    {
       $articulos =  $this->modeloArticulo->buscaArticulosAgrup( $params );
       //return $articulos;
       foreach ($articulos as $i => $articulo) {
            $articulos[$i]->ALMACEN = mb_convert_encoding( $articulo->ALMACEN , "UTF-8");   
            $articulos[$i]->FAMILIA = mb_convert_encoding( $articulo->FAMILIA , "UTF-8");
            $articulos[$i]->SUBFAMILIA = mb_convert_encoding( $articulo->SUBFAMILIA , "UTF-8");
       }
       //var_dump( $articulos );
       return $articulos;
    }


    public function getFamilias()
    {
        $familias = $this->modeloArticulo->getFamilias();

        $listaFamilas = [  ];
        foreach ($familias as $i => $familia) {
            array_push( $listaFamilas ,  ['id' => mb_convert_encoding( $familia->FAM , "UTF-8") , "name" => mb_convert_encoding( $familia->FAM , "UTF-8") ] );
        }

        return $listaFamilas;
    }

    public function getSubfamilias( $familia )
    {
        $subfamilias = $this->modeloArticulo->getSubfamilias( $familia );

        $listaSubfamilas = [ [ 'id' => "%", 'name' => "TODAS"] ];
        foreach ($subfamilias as $i => $subfamilia) {
            array_push( $listaSubfamilas ,  ['id' => mb_convert_encoding( $subfamilia->SUBFAM , "UTF-8") , "name" => mb_convert_encoding( $subfamilia->SUBFAM , "UTF-8") ] );
        }

        return $listaSubfamilas;
    }

    public function getSubfamiliasAPI( $familia )
    {
        $subfamilias = $this->modeloArticulo->getSubfamiliasAPI( $familia );

        $listaSubfamilas = [];
        foreach ($subfamilias as $i => $subfamilia) {
            array_push( $listaSubfamilas ,  ['id' => mb_convert_encoding( $subfamilia->SUBFAM , "UTF-8") , "name" => mb_convert_encoding( $subfamilia->SUBFAM , "UTF-8") ] );
        }

        return $listaSubfamilas;
    }

    public function getAllSubfamiliasAPI()
    {
        $subfamilias = $this->modeloArticulo->getAllSubfamiliasAPI( $familia );

        // $listaSubfamilas = [];
        // foreach ($subfamilias as $i => $subfamilia) {
        //     array_push( $listaSubfamilas ,  ['id' => mb_convert_encoding( $subfamilia->SUBFAM , "UTF-8") , "name" => mb_convert_encoding( $subfamilia->SUBFAM , "UTF-8") ] );
        // }

        return $subfamilias;
    }

    public function getPvpXFamilia($codArticulo, $descripcion){
        $expusuarioprecios = explode(',',$_SESSION['usuarioprecios']);
        $productos = $this->modeloArticulo->getPvpXCod($codArticulo, $descripcion,$_SESSION['usuariofamilia']);
        //$productos = json_decode(json_encode($productos), true);
        $array = array();
        foreach ($productos as $i => $producto) {
           $array[$i]['CODIGOARTICULO'] = mb_convert_encoding( $producto->CODIGOARTICULO , "UTF-8");
           $array[$i]['DESCRIPCION'] = mb_convert_encoding( $producto->DESCRIPCION , "UTF-8");
           foreach($expusuarioprecios as $precio){
            $array[$i][$precio] = mb_convert_encoding( $producto->$precio , "UTF-8");
           }
           $array[$i]['EXISTOTAL'] = mb_convert_encoding( $producto->EXISTOTAL , "UTF-8");
           $array[$i]['ALMACEN'] = mb_convert_encoding( $producto->ALMACEN , "UTF-8");          
        }
        
        return $array;
    }
}
