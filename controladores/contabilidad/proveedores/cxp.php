<?php


// require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/contabilidad/proveedores/cxp.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/Contabilidad/cxp.php";
use cxp\CuentasPorPagar;

class CxpController  
{
    
    protected $modeloCxp;
    private $listFactuasAbonar;

    public function __construct()
    {
        $this->modeloCxp = new  cxp\CuentasPorPagar;;
    }

    public function validaUsuario( $params )
    {
        $usuarioObtenido = $this->modeloCxp->getUsuarioSistema( $params['usuario']);
        
        if( sizeof( $usuarioObtenido) == 1){
            if (  $params['pass'] === $usuarioObtenido[0]->USU_STATUS ) {
                return [ 'verificado' => true , 'userId' => $usuarioObtenido[0]->ID_USUARIO , 'name' =>$usuarioObtenido[0]->USU_NOMBRE." ".$usuarioObtenido[0]->USU_APELPAT  ];
            }
            return [ 'verificado' => false , 'userId' => -1 , 'name' => '' ];
        }

        return [ 'verificado' => false , 'userId' => -1 , 'name' => '' ];
    }

    public function aplicarPagoProveedor( $params )
    {
        //OBTENIENDO ID ID QUE SE GEREARA EN LA TABLA CXP_HISTORCP  PARA QUE  SE REALICE EL(LOS) ABONO(S)
        $this->listFactuasAbonar = json_decode( $params ['facturas'] );
        $idsHistCxpAbonos = [];
        $montoTotal = 0;
        $nfacturas =  sizeof( $this->listFactuasAbonar ) ;
        $duplicadas = [];



        $listFactsDepurada = [];

        foreach ($this->listFactuasAbonar as $i => $factura) {
            if ( !in_array( $factura->factura ,$duplicadas ) ) {
                array_push( $duplicadas , $factura->factura );
                array_push($listFactsDepurada , $factura );
                array_push( $idsHistCxpAbonos , $this->modeloCxp->getIdsFromContador("CXP_HISTORCP") );
                $montoTotal += $factura->importe;
            }

        }
        $this->listFactuasAbonar = $listFactsDepurada;
        //validando y convirtiendo la fecha de la aplicacion a foformato de base de datos
        $parseDate = DateTime::createFromFormat("d/m/Y", $params['fechaAbono']);
        
        if ( $parseDate && $parseDate->format("d/m/Y") === $params['fechaAbono'] ) {
            //cambiamos el formato de la fecha
            $explodeDateAbono = explode("/", $params['fechaAbono'] );
            $params['fechaAbono']= $explodeDateAbono[2]."-".$explodeDateAbono[1]."-".$explodeDateAbono[0];
        }else{

        }


        $estadoAnticipo = [];
        $estadoHistPago = [];
        $estadoAbono = [];


        //die($montoTotal."_".$params['montoAbonado']);
        if( $nfacturas  > 1 ){
            //echo "Es mas de una factura<br>";
            //de acuerdo al prediction cuando se va a pagar mas de una factua en un movimiento es porqu ambas se van a liquidar
                
                $estadoHistPago = $this->setApliacionPagos( $params , $idsHistCxpAbonos, $nfacturas );
                //echo "<br>Result setApliacionPagos:".$estadoHistPago;
                //ahora se hace un update al registro que tenia el registro anterior con el abono que se realizó
                $estadoAbono = $this->actualizaSaldoConAbono( $params , $idsHistCxpAbonos, $nfacturas );                
                //echo "<br>Result actualizaSaldoConAbono:".$estadoAbono;
        }else if ( $nfacturas == 1) {
            //echo "<br>Es una factura";
            if ( $montoTotal >  $params['montoAbonado'] ) { //se le aplicara un abono a la factura
                //echo "<br>Solo es un ABONO";
                 $estadoHistPago = $this->setApliacionPagos( $params , $idsHistCxpAbonos );
                 //echo "<br>Result setApliacionPagos:".json_encode($estadoHistPago);
                 //Como no es el monto total segun prediction hay que hacer el registro del monto final 
                $estadoAnticipo = $this->setReciboAbono( $params , $idsHistCxpAbonos );
                //echo "<br>Result setReciboAbono:".json_encode($estadoAnticipo);
                //ahora se hace un update al registro que tenia el registro anterior con el abono que se realizó
               $estadoAbono =  $this->actualizaSaldoConAbono( $params , $idsHistCxpAbonos );
               //echo "<br>Result actualizaSaldoConAbono:".json_encode($estadoAbono);
            }else if ( $montoTotal == $params['montoAbonado']) { //se liquida el documento
                //echo "<br>Se liquida siendo igual el pago que el documento";
                $estadoHistPago = $this->setApliacionPagos( $params , $idsHistCxpAbonos );
                //echo "<br>Result setApliacionPagos:".json_encode($estadoHistPago);
                //ahora se hace un update al registro que tenia el registro anterior con el abono que se realizó
                $estadoAbono = $this->actualizaSaldoConAbono( $params , $idsHistCxpAbonos );
                //echo "<br>Result actualizaSaldoConAbono:".json_encode($estadoAbono);
            }else if( $montoTotal < $params['montoAbonado' ]){
                //echo "<br>El pago es mayor que el documento (no se que pasa ahí)";
                $estadoHistPago = $this->setApliacionPagos( $params , $idsHistCxpAbonos );
                //echo "<br>Result setApliacionPagos:".json_encode($estadoHistPago);
                //ahora se hace un update al registro que tenia el registro anterior con el abono que se realizó
                $estadoAbono = $this->actualizaSaldoConAbono( $params , $idsHistCxpAbonos );
                //echo "<br>Result actualizaSaldoConAbono:".json_encode($estadoAbono);
            }
        }
        

        $data = [];
        $response = [];
        //Procesando  los estados obtenidos para retornar un json de las facturas que fueron aplicadas
        foreach ($idsHistCxpAbonos as $i => $id) {
            $data['factura']= mb_convert_encoding( $estadoHistPago[$i]['factura'] , "UTF-8") ;
            
            if ( isset( $estadoAnticipo[$i])  ) {
                
                if ( !$estadoAnticipo[$i]['registrado'] ) {
                    $data ['anticipo'] = "fail";
                }else{
                    $data ['anticipo'] = "ok";
                }
            }else{
                $data ['anticipo'] = "none";
            }

            //Verificando  el historico y la actualización del saldo
            if ( $estadoHistPago[$i]['registrado'] ) {
                $data ['historico'] = "ok";
            }else{
                $data ['historico'] = "fail";
            }

            if ( $estadoAbono[$i]['registrado'] ) {
                $data ['abono'] = "ok";
            }else{
                $data ['abono'] = "fail";
            }

            array_push( $response , $data );

        //haciendo la notificiacion via correo
        if( isset( $estadoAnticipo[$i])){

            if ( $estadoAnticipo[$i]['registrado']  && $estadoAbono[$i]['registrado'] ) {
                //envia
                $this->sendNotification([
                    'id' => $estadoHistPago[$i]['id'],
                    'abono' => true
                ]);
            }
        }else {
            if (  $estadoAbono[$i]['registrado'] ) {
                //envia
                $this->sendNotification([
                    'id' => $estadoHistPago[$i]['id'],
                    'abono' => false
                ]);                
            }   
        }

        


        }

        return $response;
    }

    public function sendNotification( $params )
    {
        file_get_contents("http://servermatrixxxb.ddns.net/intranet/notificaciones/cxp", false, 
        HttpRequestParser::preparePostData( [
                    'idHistorico' => $params['id'],
                    'esAbono' =>$params['abono'],
                    ] ) );
    }

    public function setApliacionPagos( $params , $idsHistCxpAbonos, $nfacturas = 1 )
    {
        $xpfec = explode('/',$params['fechaAbono']);
        $xpfec[1]=='010'?$params['fechaAbono']=$xpfec[0].'/10/'.$xpfec[2]:$params['fechaAbono']=$params['fechaAbono'];
        //recorriendo cada uno de los ids historicocxp para hacer la inseción de l abono correspondiente a todas las facturas
        $registrados = [];
        foreach ($idsHistCxpAbonos as $i => $id) {
            $abono = $nfacturas == 1 ? ( $params['montoAbonado']  > $this->listFactuasAbonar[$i]->importe) ?  $this->listFactuasAbonar[$i]->importe : $params['montoAbonado'] : $this->listFactuasAbonar[$i]->importe;
        
            $estadoRegistro = $this->modeloCxp->setHistoricoPagos([
                'id' => $id,
                'fecha' => $params['fechaAbono'],
                'numCheque' => $params['numcheque'],
                'numDocto' => $this->listFactuasAbonar[$i]->numRecibo,
                'importeAbono' => $abono ,
                'usuarioAplico' => $params['usuario'],
                'idProveedor' => $this->listFactuasAbonar[$i]->idProveedor,
                'nombreUsuario' => $params["nombreUsuario"],
                'ivaAplicado' => 16,
                'comentario'  => strtoupper( $params['comentario'] )
            ]);
            //echo '<br>Resultado del modelo setHistoricoPagos'.$estadoRegistro;

       

            array_push( $registrados , [ 'id' => $id , 'registrado'  => $estadoRegistro ,'factura' => $this->listFactuasAbonar[$i]->factura ] );
            
        }

        return $registrados;
    }

    public function setReciboAbono( $params , $idsHistCxpAbonos, $nfacturas = 1 )
    {
        $idsRecibos = [];
        foreach ( $idsHistCxpAbonos as $i => $id) {
            $abono = $nfacturas == 1 ? ( $params['montoAbonado']  > $this->listFactuasAbonar[$i]->importe) ?  $this->listFactuasAbonar[$i]->importe : $params['montoAbonado'] : $this->listFactuasAbonar[$i]->importe;
            $saldofinal =$this->listFactuasAbonar[$i]->importe - $abono;

            if ( $saldofinal >= 0 ) {
                $saldofinal = $saldofinal == 0 ? $this->listFactuasAbonar[$i]->importe : $saldofinal;
                //obteniendo el id que tendra el recibo
                $idRecibo = $this->modeloCxp->getIdsFromContador("CXP_RECIBOS");
                
                ///haciendo la insercion del monto restante de la deuda al proveedor
                $estadoAbono = $this->modeloCxp->setAbonoAcuenta([
                    'idRecibo' => $idRecibo,
                    'numeroMovto' =>  $this->listFactuasAbonar[$i]->numRecibo,
                    'fechaVencimiento' => DateTime::createFromFormat('d/m/Y', $this->listFactuasAbonar[$i]->vencimiento)->format('Y-m-d') ,
                    'fechaEmision' =>DateTime::createFromFormat('d/m/Y', $this->listFactuasAbonar[$i]->emision)->format('Y-m-d') ,
                    'saldoFinal' => $saldofinal,
                    'observaciones' => strtoupper( $params['comentario'] ),
                    'numeroFactura' =>$this->listFactuasAbonar[$i]->factura,
                    'idFacturaAbonada' => $this->listFactuasAbonar[$i]->idfact_deuda,
                    'conceptoReal' => '',
                    'iva' => round( $saldofinal - ( $saldofinal/1.16) , 2),  //obteniendo del iva de la deuda
                    'valorIva' => 16,
                    'itpoCliente' => '',
                ]);

                if ( $estadoAbono ) {
                    $reciboFormaPago = $this->modeloCxp->setDetalleFormaPago([
                        'idRecibo' => $idRecibo,
                        'medioPago' => $params['medioPago'],
                        'referencias' => $params['referencias'],
                        'cheque' => $params['numcheque'],
                        'importePagado' => $params['montoAbonado']
                    ]);     
                }


                array_push( $idsRecibos , [ 'id' => $idRecibo , 'registrado'  => $estadoAbono ,'factura' => $this->listFactuasAbonar[$i]->factura ] );

            }
        }

        return $idsRecibos;
    }

    public function actualizaSaldoConAbono( $params , $idsHistCxpAbonos , $nfacturas = 1)
    {
        $xpfec = explode('/',$params['fechaAbono']);
        $xpfec[1]=='010'?$params['fechaAbono']=$xpfec[0].'/10/'.$xpfec[2]:$params['fechaAbono']=$params['fechaAbono'];
        $registrados = [];
        foreach ($idsHistCxpAbonos as $i => $id) {
            $abono = $nfacturas == 1 ? ( $params['montoAbonado']  >= $this->listFactuasAbonar[$i]->importe) ? 
             $this->listFactuasAbonar[$i]->importe : 
             ( $params['montoAbonado']  < $this->listFactuasAbonar[$i]->importe) ?  $params['montoAbonado']  : -1   : $this->listFactuasAbonar[$i]->importe;
             if( $abono == -1 ){
                array_push( $registrados , [ 'id' => $this->listFactuasAbonar[$i]->idDeuda , 'registrado'  => false ,'factura' => $this->listFactuasAbonar[$i]->factura ] );
                continue;
             }
            $estadoRegistro = $this->modeloCxp->actualizaSaldoDeudaProveedor([
                'importePagado' => $abono,
                'idHistoricoPago' => $id,
                'fechaPago' => $params['fechaAbono'],
                'numCheque' =>  $params['numcheque'],
                'idcuentaChequera' => '',
                'importeIva' => round( $params['montoAbonado'] - ( $params['montoAbonado'] /1.16) , 2),
                'idRecibo' => $this->listFactuasAbonar[$i]->idDeuda
            ]);
            //echo '<br>Resultado de modelo actualizaSaldoDeudaProveedor'.$estadoRegistro;
            //guardando el estado del registro de los datos
            array_push( $registrados , [ 'id' => $this->listFactuasAbonar[$i]->idDeuda , 'registrado'  => $estadoRegistro ,'factura' => $this->listFactuasAbonar[$i]->factura ] );
            
        }

        return $registrados;
    }
}




