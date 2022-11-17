<?php
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/recursos_materiales/insumos.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/Reportes/recursos_materiales/recepcionInsumos.php";

class InsumosController  
{
    protected $modeloInsumos;

    public function __construct() {
        $this->modeloInsumos = new Insumos;
        
    }

    public function getInsumos()
    {
        
        $listaInsumos = $this->modeloInsumos->getInsumos();


        foreach ($listaInsumos as $i => $insumo) {
            $listaInsumos[$i]['value'] = mb_convert_encoding( $insumo['name'], "UTF-8");
        }

        return $listaInsumos;
    }

    public function solicitarRequisicion( $params)
    {
        
        $idRequisicion = $this->modeloInsumos->setRequisicion( $params->sucursal );
        //gerenado la sentencia para insertar la lista de insumos solicitados en una sola query
        $queryWishList = "";
        foreach ($params->wishList as $i => $insumo) {
            $queryWishList .= "(".$idRequisicion.",".$insumo->idarticulo.",".$insumo->cantidad."),";
        }

        $queryWishList = rtrim( $queryWishList , ",");

        $this->modeloInsumos->setDetalleRequisicion( $queryWishList );

        if ( $idRequisicion == -1 ) {
            return 0;
        }

        return $idRequisicion;

    }

    public function getRequisicionesSolicitudes(  $entregado = false )
    {
        $requisicionesPendientes = $this->modeloInsumos->getRequisicionesSolicitado( $entregado );

        foreach ( $requisicionesPendientes as $i => $requisicion) {
            $requisicionesPendientes[$i]['fecha_solicitado'] = str_replace("-","/", $requisicion['fecha_solicitado'] );
        }        

        return $requisicionesPendientes;
    }

    public function getInsumosParaRequisicion( $idRequisicion )
    {
        $insumosSolicitados = $this->modeloInsumos->getRequisicion( $idRequisicion );

        
        foreach ($insumosSolicitados as $i => $insumo) {
            $insumosSolicitados[$i]['item'] = mb_convert_encoding( $insumo['item'] , 'UTF-8');
        }

        return $insumosSolicitados;

    }

    public function setSurtidoRequisicion( $requisicion , $surtido )
    {
        $surtido = json_decode( $surtido );

        foreach ($surtido as $i => $item) {
            $this->modeloInsumos->setSurtidoRequisicion( $requisicion , $item );

        }

        //estableciendo la fecha en la que fue realizada la requisicón
        $actualizado = $this->modeloInsumos->setFechaSurtido( $requisicion, date("Y-m-d H:i:s") );
        if ( $actualizado > 0 ) {
            $recibo = new  RecepcionDeInsumos;
            
            return $recibo->generaRecibo( $requisicion );
        }else{
            return "404";
        }

    }

    public function reimpresion( $idRequisicion)
    {
        $recibo = new  RecepcionDeInsumos;
            
        return $recibo->generaRecibo( $idRequisicion );
    }
}
