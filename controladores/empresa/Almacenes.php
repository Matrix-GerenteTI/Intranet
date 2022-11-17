<?php

require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/Almacenes/Almacen.php";

class AlmacenController 
{
    protected $modeloAlmacen;

    public function __construct() {
        $this->modeloAlmacen = new Almacen;
    }
    public function getSucursales()
    {
        $listaSucursales = $this->modeloAlmacen->getSucursalesIntranet();

        return $listaSucursales;
    }

    public function getSucursalesApp()
    {
        $listaSucursales = $this->modeloAlmacen->getSucursalesApp();
        $return = array();
        $n = 0;
        foreach($listaSucursales as $row){
            $return[$n]['id'] = $row->ID;
            $return[$n]['name'] = $row->NAME;
            $n++;
        }

        return $return;
    }

    public function getEntradaSalida( $data )
    {
        $explodeInicio = explode("/",$data['inicio']);
        $explodeFin = explode("/",$data['fin']);
        $data['usuario'] = $data['usuario'] == -1 ? "" : $data['usuario'];

        if( $data ['inicio']== 'Inicio Busq.'){
            $data ['inicio'] = date("Y-m-d");
            
            $data ['fin'] = $data ['fin'] == 'Fin Busq.' ? date("Y-m-d") : $explodeFin[2]."-".$explodeFin[1]."-".$explodeFin[0];
            
        }else{
            
            $data ['inicio']  = $explodeInicio[2]."-".$explodeInicio[1]."-".$explodeInicio[0];
            $data ['fin'] = $data ['fin'] == 'Fin Busq.' ? $explodeInicio[2]."-".$explodeInicio[1]."-".$explodeInicio[0] : $explodeFin[2]."-".$explodeFin[1]."-".$explodeFin[0];
        }

        $listaEntradaSalida = $this->modeloAlmacen->getEntradasSalidas($data );
        
        $entradasSalidas = [];
        $totalArticulosEntrada = 0;
        $totalArticulosSalida = 0;
        foreach ( $listaEntradaSalida as $i => $item) {
            if( !is_numeric( $item->NUMDOCTO) ){ //Es un movimiento cancelado
                $folioCancelado = explode("/", $item->NUMDOCTO );
                $entradasSalidas[ $folioCancelado[1] ]['estado'] = "Cancelado";
                continue;
            }
            if( $item->SECCION == 'ENTRADA X AJUSTE' ){
                $totalArticulosEntrada +=  $item->CANTIDAD;
            }else {
                $totalArticulosSalida +=  $item->CANTIDAD;
            }
            if ( isset( $entradasSalidas[ str_replace(" ","", $item->SECCION) ][ mb_convert_encoding($item->SUCURSAL, "UTF-8")]['articulos'] ) ) {
                array_push( $entradasSalidas[ str_replace(" ","", $item->SECCION) ][ mb_convert_encoding($item->SUCURSAL, "UTF-8")] ['articulos'] , [ 'codigo' => mb_convert_encoding($item->CODIGO,"UTF-8"), 'descripcion' => mb_convert_encoding($item->DESCRIPCION, "UTF-8") ,'cant' => $item->CANTIDAD ] );
                $entradasSalidas[str_replace(" ","", $item->SECCION) ][ mb_convert_encoding($item->SUCURSAL, "UTF-8")]['cantidad'] +=  $item->CANTIDAD;
            } else {
                // $entradasSalidas[str_replace(" ","", $item->SECCION) ][ mb_convert_encoding($item->SUCURSAL, "UTF-8")]['hora'] = $item->HORAMOVTO;
                // $entradasSalidas[str_replace(" ","", $item->SECCION) ][ mb_convert_encoding($item->SUCURSAL, "UTF-8")]['fecha'] = $item->FECHA;
                // $entradasSalidas[str_replace(" ","", $item->SECCION) ][ mb_convert_encoding($item->SUCURSAL, "UTF-8")str_replace(" ","", ]['almacen'] =)    mb_convert_encoding($item->SUCURSAL, "UTF-8");
                $entradasSalidas[str_replace(" ","", $item->SECCION) ][ mb_convert_encoding($item->SUCURSAL, "UTF-8")]['articulos'] = [ [ 'codigo' => mb_convert_encoding($item->CODIGO,"UTF-8"), 'descripcion' =>  mb_convert_encoding($item->DESCRIPCION, "UTF-8"),'cant' => $item->CANTIDAD ]  ];
                $entradasSalidas[str_replace(" ","", $item->SECCION) ][ mb_convert_encoding($item->SUCURSAL, "UTF-8")]['cantidad'] =  $item->CANTIDAD;
                
                // $entradasSalidas[str_replace(" ","", $item->SECCION) ][ mb_convert_encoding($item->SUCURSAL, "UTF-8")]['realizo'] = $item->USU_NOMBRE;
                // $entradasSalidas[str_replace(" ","", $item->SECCION) ][ mb_convert_encoding($item->SUCURSAL, "UTF-8")]['estado'] = "ok";
                // $entradasSalidas[str_replace(" ","", $item->SECCION) ][ mb_convert_encoding($item->SUCURSAL, "UTF-8")]['tipo'] = $item->SECCION ;
                // $entradasSalidas[ str_replace(" ","", $item->SECCION) ][ mb_convert_encoding($item->SUCURSAL, "UTF-8")] ['folio'] = $item->NUMDOCTO;
            }
            
        }

        return [ 'movimientos' => $entradasSalidas , 'totalEntradas' => $totalArticulosEntrada, 'totalSalidas' => $totalArticulosSalida];
    }

    public function getGeneralEntradasSalidas()
    {
        $listaEntradaSalida = $this->modeloAlmacen->getEntradasSalidas([
            'inicio' => date('Y-01-01'),
            'fin' => date('Y-12-31'),
            'usuario' => '',
            'folio' => ''
        ] , TRUE);
        
        $entradasSalidas = [];
        $totalArticulosEntrada = 0;
        $totalArticulosSalida = 0;
        foreach ( $listaEntradaSalida as $i => $item) {
                $listaEntradaSalida[$i]->SUCURSAL = mb_convert_encoding($item->SUCURSAL, "UTF-8");
                $listaEntradaSalida[$i]->DESCRIPCION = mb_convert_encoding($item->DESCRIPCION, "UTF-8");
                $listaEntradaSalida[$i]->OBSERVACIONES = mb_convert_encoding($item->OBSERVACIONES, "UTF-8");
                $listaEntradaSalida[$i]->NUMDOCTO = mb_convert_encoding($item->NUMDOCTO, "UTF-8");
                $listaEntradaSalida[$i]->CODIGO = mb_convert_encoding($item->CODIGO, "UTF-8");
                if( $listaEntradaSalida[$i]->SECCION == "ENTRADA X AJUSTE"){
                    $listaEntradaSalida[$i]->SECCION = "ENTRADA";
                }elseif ( $listaEntradaSalida[$i]->SECCION == "SALIDA X AJUSTE") {
                    $listaEntradaSalida[$i]->SECCION = "SALIDA";
                }else {
                    $listaEntradaSalida[$i]->SECCION = "TRANSITO DEVUELTO";
                }
        }
            
        // var_dump( $listaEntradaSalida );
        return $listaEntradaSalida;
    }

    public function getGeneralEmitidosDevueltos()
    {
        $listaEntradaSalida = $this->modeloAlmacen->getEmitidoDevuelto([                                                                                       
            'inicio' => date('Y-01-01'),
            'fin' => date('Y-12-31'),
            'usuario' => '',
            'folio' => ''
        ] , TRUE);
        
        $entradasSalidas = [];
        foreach ( $listaEntradaSalida as $i => $item) {
            $listaEntradaSalida[$i]->SUCURSAL = mb_convert_encoding($item->SUCURSAL, "UTF-8");
            $listaEntradaSalida[$i]->DESCRIPCION = mb_convert_encoding($item->DESCRIPCION, "UTF-8");
            $listaEntradaSalida[$i]->OBSERVACIONES = mb_convert_encoding($item->OBSERVACIONES, "UTF-8");
            $listaEntradaSalida[$i]->SECCION = mb_convert_encoding($item->SECCION, "UTF-8");
            $listaEntradaSalida[$i]->STATUS = mb_convert_encoding($item->STATUS, "UTF-8");
        }
            

        return $listaEntradaSalida;
    }

    public function getProductoswithoutImg(){

        $listaEntradaSalida = $this->modeloAlmacen->getProductoInventario();
        foreach ( $listaEntradaSalida as $i => $item) {
            $listaEntradaSalida[$i]->ALMACEN = mb_convert_encoding($item->ALMACEN, "UTF-8");
            $listaEntradaSalida[$i]->FAM = mb_convert_encoding($item->FAM, "UTF-8");
            $listaEntradaSalida[$i]->SUBFAM = mb_convert_encoding($item->SUBFAM, "UTF-8");
            $listaEntradaSalida[$i]->COD = mb_convert_encoding($item->COD, "UTF-8");
            $listaEntradaSalida[$i]->DESCRIPCION = mb_convert_encoding($item->DESCRIPCION, "UTF-8");
            $listaEntradaSalida[$i]->REGISTROS = mb_convert_encoding($item->REGISTROS, "UTF-8");

        }
        return $listaEntradaSalida;
    }

    public function getUsuariosMovtosAlmacenes( )
    {
        $listaUsuarios = $this->modeloAlmacen->getUsuariosMovtosAlmacenes();
        $usuarios = [];
        foreach ($listaUsuarios as $i => $usuario) {
            array_push( $usuarios , [ 'id' => $usuario->ID_USUARIO , 'name' => $usuario->USUARIO ] );
        }

        return $usuarios;
    }
    
    
    public function getAlmacenes()
    {
       return  $listaSucursales = $this->modeloAlmacen->getAlmacenes();
    }

    public function getAlmacenesApp()
    {
        $sucursales = $this->modeloAlmacen->getAlmacenes();
        $listaSucursales = [ [ 'id' => "%", 'name' => "TODAS"] ];
        foreach ($sucursales as $i => $sucursal) {
            array_push( $listaSucursales ,  ['id' => mb_convert_encoding( $sucursal->ID , "UTF-8") , "name" => mb_convert_encoding( $sucursal->DESCRIPCION , "UTF-8") ] );
        }
        array_push( $listaSucursales ,  ['id' => 'CENTRO' , "name" => 'ZONA CENTRO' ] );
        array_push( $listaSucursales ,  ['id' => 'ALTOS' , "name" => 'ZONA ALTOS' ] );
        array_push( $listaSucursales ,  ['id' => 'COSTA' , "name" => 'ZONA COSTA' ] );
        return  $listaSucursales;
    }

    public function foliosTraspasos( $data)
    {
        $data['inicio'] = $this->parseFechaMysqlFormat( $data['inicio'] );
        $data['fin'] = $this->parseFechaMysqlFormat( $data['fin'] );

        return $this->modeloAlmacen->getFoliosTraspasos( $data );
    }

    public function parseFechaMysqlFormat($fecha)
	{
		$fechaExplode = explode( '/',$fecha );
		
		return $fechaExplode[2].'-'.$fechaExplode[1]."-".$fechaExplode[0];
	}

    public function setCheckInventario($params){
        return $this->modeloAlmacen->checkInventario( $params );
    }
    
    

    public function getConfirmados( $params )
    {
        $params['fecha'] = $this->parseFechaMysqlFormat( $params['fecha'] );
        $inventarios =  $this->modeloAlmacen->getConfirmados( $params );
        foreach ($inventarios as $i => $inventario) {
            $inventarios[$i]->almacen = mb_convert_encoding( $inventario->almacen , "UTF-8");   
            $inventarios[$i]->familia = mb_convert_encoding( $inventario->familia , "UTF-8");
            $inventarios[$i]->subfamilia = mb_convert_encoding( $inventario->subfamilia , "UTF-8");
            $inventarios[$i]->usuario = mb_convert_encoding( $inventario->usuario , "UTF-8");
        }
        //var_dump( $articulos );
        return $inventarios;
    }
}
