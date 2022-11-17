<?php 
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/Almacenes/compras.php";

class ComprasController  
{

    protected $modeloCompras;

    public function __construct()
    {
        $this->modeloCompras = new Compras;
    }

    public function getFacturasCompra( )
    {
        $listaFacturas = $this->modeloCompras->getHistoricoFacturasCompras( '%' );

        foreach ( $listaFacturas as $i => $factura) {
            $listaFacturas[$i]->PYM_NOMBRE = utf8_encode( $factura->PYM_NOMBRE );
        }

        return $listaFacturas;
    }
    
    public function getListaArticulosCompra( $idCompras )
    {
        $listaArticulos = $this->modeloCompras->getListaItemsCompra( $idCompras );
        foreach ( $listaArticulos as $i => $articulo) {
            $listaArticulos[$i]->ARTICULO = utf8_encode( $articulo->ARTICULO );
            $listaArticulos[$i]->FAMILIA = utf8_encode( $articulo->FAMILIA );
            $listaArticulos[$i]->SUBFAMILIA = utf8_encode( $articulo->SUBFAMILIA );
        }

        
        return $listaArticulos; 
    }

    public function getProveedoresFactSinIngresar( $compras )
    {
        $repetido = [];
        $proveedorList = [ [ 'id' => -1 , "name" => "TODOS"]];
        foreach( $compras as $i => $compra ){
            if (  !in_array( $compra['proveedor'] , $repetido ) ) {
                array_push(  $repetido , $compra['proveedor']  );
                array_push( $proveedorList , ['name' => $compra['proveedor'], 'id' => $i] );
            }
        }

        return $proveedorList;
    }

    public function getComprasSinProcesar( $factura = '' , $proveedor = '%' )
    {
        $listaCompras = $this->modeloCompras->getComprasSinProcesar( $factura , $proveedor);

        $facturasSinProcesar = [];
        //buscando si el ya se registró el log de entrada o ingreso 
        foreach ($listaCompras as $i => $compra) {
            $data = [
                'proveedor' => mb_convert_encoding( $compra->PROVEEDOR , "UTF-8"),
                'cfdi' => $compra->NUMDOCTO,
                'factura' => $compra->NUMFACTPROV,
                'id' => $compra->ID,
                'iva' => $compra->IMPIVA,
                'subtotal'=> $compra->SUBTOTAL,
                'total' => $compra->TOTAL,
                'checkedLlegada' => false,
                'ceckedEntrada' => false
            ];

            $seguimientoCompra = $this->modeloCompras->getLogCompraSinProcesar( $compra->NUMFACTPROV );
            if ( sizeof( $seguimientoCompra ) ) {
                $data ['llegada'] = $seguimientoCompra[0]['fechaRecepcion'];
                $data['checkedLlegada'] = $data ['llegada'] != null ? true : false;
                $data ['procesado'] = $seguimientoCompra[0]['fechaIngresoFact'];
                $data['ceckedEntrada'] = $data ['procesado'] != null ? true : false;
            }
            
            array_push( $facturasSinProcesar , $data );
        }



        return ( [ 'compras' => $facturasSinProcesar , 'proveedores' => $this->getProveedoresFactSinIngresar( $facturasSinProcesar ) ] );
    }

    public function registraRecepcionMercancía( $params )
    {        
        return $this->modeloCompras->registraRecepcionCompra( $params );
    }
    public function registraAltaMercancia( $params )
    {

        //comprobando que el numero de compra  exista
        if ( sizeof( $this->modeloCompras->validaEntradaCompra( $params['numEntrada'] , $params['factura']) ) ) {
            return $this->modeloCompras->registraAltaMercancia( $params );
        }

        return -2;
        
    }


}
