<?php
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/Contabilidad/bancos/chequeras.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/saldos_bancarios.php";

class ChequeraController  
{
    protected $modeloChequera;
    protected $modeloBancos;
    
    public function __construct()
    {
        $this->modeloChequera = new ChequeraBancarias;
        $this->modeloBancos = new SaldosBancarios; 
    }

    public function getCuentasPagoProveedores()
    {
        $listaChequera = $this->modeloChequera->getChequerasPagoProveedor();

        $listado = [ ['id' => 1 , 'name' => 'POR DEFECTO' ] ];
        foreach ($listaChequera as $i => $cuenta) {
            $listaChequera[$i]->VALOR = $cuenta->VALOR;
            $listaChequera[$i]->name = $cuenta->VALOR;
            $listaChequera[$i]->id = $i;
            array_push( $listado , $listaChequera[$i]);
        }

        return $listado;
    }

    public function getEstadoCuenta( $params )
    {
            $params['fechaInicio'] =  $this->modeloBancos->dateFormatDB( $params['fechaInicio' ] );
            $params['fechaFin'] =  $this->modeloBancos->dateFormatDB( $params['fechaFin' ] );
            
            $listamoviemientos = $this->modeloBancos->getMovimientosByPeriodo( $params );

            $listaFacturas = $this->modeloChequera->getFacturados( $params );
            $listaAgrupada = [];
            foreach ( $listamoviemientos as $i => $movimientos) {
                if ( $movimientos['egresos'] > 0 ){
                    unset( $listamoviemientos[$i] );
                    continue;
                }
                $movimientos['fecha'] = date("d/m/Y", strtotime($movimientos['fecha']));  
                if ( stripos( $movimientos['referencia'], "DEB" ) !==  false || stripos( $movimientos['referencia'], "tarje" )   !==  false  || stripos( $movimientos['referencia'], "tc" ) !==  false || stripos( $movimientos['referencia'], "TERMI" ) !==  false  || stripos( $movimientos['referencia'], "tdc" )   !==  false     ) {
                    
                    if ( isset($listaAgrupada['tarjetas'] )  ) {
                        $listaAgrupada['tarjetas']['total'] += $movimientos['ingresos'];
                        array_push( $listaAgrupada['tarjetas']['movimientos'] , $movimientos  );
                    } else {
                        $listaAgrupada['tarjetas'] =  ['total' => $movimientos['ingresos'],'facturado' => 0, 'movimientos'=>[$movimientos] ] ;
                    }
                    
                    
                } else if(  stripos( $movimientos['referencia'], "trans" ) !==  false  ||  stripos( $movimientos['referencia'], "spei" ) !==  false  || stripos( $movimientos['referencia'], "trasp" ) !==  false ||  stripos( $movimientos['referencia'], "interba" ) !==  false ||  stripos( $movimientos['referencia'], "pago" ) !==  false ) {
                    if ( isset($listaAgrupada['transferencia'] )  ) {
                         $listaAgrupada['transferencia']['total'] += $movimientos['ingresos'];
                        
                        array_push( $listaAgrupada['transferencia']['movimientos'] , $movimientos   );
                    } else {
                        $listaAgrupada['transferencia'] =  ['total' => $movimientos['ingresos'],'facturado' => 0, 'movimientos'=>[$movimientos] ] ;
                    }
                }else if(  stripos( $movimientos['referencia'], "dep" )  !== false  && stripos( $movimientos['referencia'], 'DEPOSITO VENTAS ' ) === false) {
                    if ( isset($listaAgrupada['deposito'] )  ) {
                         $listaAgrupada['deposito']['total'] += $movimientos['ingresos'];
                        array_push( $listaAgrupada['deposito']['movimientos'] , $movimientos   );
                    } else {
                        $listaAgrupada['deposito'] =  ['total' => $movimientos['ingresos'],'facturado' => 0, 'movimientos'=>[$movimientos] ] ;
                    }
                }else{
                    if ( isset($listaAgrupada['otros'] )  ) {
                        $listaAgrupada['otros']['total'] += $movimientos['ingresos'];
                        array_push( $listaAgrupada['otros']['movimientos'] , $movimientos   );
                    } else {
                        $listaAgrupada['otros'] =  ['total' => $movimientos['ingresos'],'facturado' => 0, 'movimientos'=>[$movimientos] ] ;
                    }                    
                }
    
            }

            foreach ($listaFacturas as $i => $factura) {
                if ( $factura->METODOPAGO == '01' ) {
                    if ( !isset( $listaAgrupada['deposito'] ) ) {

                        $listaAgrupada['deposito']  = [ 'total' => '-' , 'facturado' =>  $factura->TOTAL , 'movimientos' => [] ];
                        
                    }else{
                        $listaAgrupada['deposito']['facturado'] += $factura->TOTAL;
                    }

                }else if(  $factura->METODOPAGO == '02'  ){
                    if ( !isset( $listaAgrupada['cheque'] ) ) {
                        $listaAgrupada['cheque']  = [ 'total' => '-' , 'facturado' =>  $factura->TOTAL , 'movimientos' => [] ];
                    }else{
                        $listaAgrupada['cheque']['facturado'] += $factura->TOTAL;
                    }                    
                }else if( $factura->METODOPAGO == '03') {
                    if ( !isset( $listaAgrupada['transferencia'] ) ) {
                        $listaAgrupada['transferencia']  = [ 'total' => '-' , 'facturado' =>  $factura->TOTAL , 'movimientos' => [] ];
                    }else{
                        $listaAgrupada['transferencia']['facturado'] += $factura->TOTAL;
                    }                    
                }else if( $factura->METODOPAGO == '04' || $factura->METODOPAGO == '28' ){
                    if ( !isset( $listaAgrupada['tarjetas'] ) ) {
                        $listaAgrupada['tarjetas']  = [ 'total' => '-' , 'facturado' =>  $factura->TOTAL , 'movimientos' => [] ];
                    }else{
                        $listaAgrupada['tarjetas']['facturado'] += $factura->TOTAL;
                    }                     
                }
            }
            
         return $listaAgrupada;   
    }
}
