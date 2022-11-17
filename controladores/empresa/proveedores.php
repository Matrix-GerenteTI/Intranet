<?php

require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/Empresa/proveedores.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/CuentasPorPagar.php";


class ProveedoresController  
{
    protected $modeloProveedor;
    

    public function __construct()
    {
        $this->modeloProveedor = new Proveedor;
        
    }
    public function getProveedores( $proveedorId = '%')
    {
        $listProveedoresObtenida = $this->modeloProveedor->getProveedores( $proveedorId );

        $listaProveedores = [];
        foreach ( $listProveedoresObtenida  as $i => $proveedor) {
            array_push( $listaProveedores, [
                'id' => $proveedor->ID_PROSPECTO,
                'name' => mb_convert_encoding( $proveedor->PYM_NOMBRE , "UTF-8")
            ]);
        }

        return $listaProveedores;
    }

    public function getDeudaConProveedor( $proveedorId )
    {
        $deudaConProveedor = $this->modeloProveedor->getFacturasPorSaldar( $proveedorId );
        $fActual = new DateTime( date('Y-m-d') );
        $listaFacturas = [];
        foreach ( $deudaConProveedor as $i => $factura) {
            $fEmision = new DateTime( $factura->FECHAEMISION);
            $fVencimiento = new DateTime($factura->FECHAVTO);
            
            
            $diasVencimiento = $fVencimiento->diff($fEmision)->format("%a");
            $diasTranscurridos = $fActual->diff( $fEmision)->format( "%a" );
            if(round($factura->IMPORTE,1)>0){
                array_push( $listaFacturas ,[
                    'idProveedor' => $factura->CODIGO,
                    'factura' => mb_convert_encoding($factura->NUMFACT, "UTF-8"),
                    'proveedor' => mb_convert_encoding($factura->CF1, "UTF-8"),
                    'origen' => mb_convert_encoding($factura->ORIGEN, "UTF-8") ,
                    'emision' => date("d/m/Y", strtotime( $factura->FECHAEMISION ) ),
                    'vencimiento' => date("d/m/Y", strtotime(  $factura->FECHAVTO ) ),
                    'diasVencimiento' => $diasTranscurridos - $diasVencimiento,
                    'importe' => round($factura->IMPORTE,2),
                    'nAnticipo' => mb_convert_encoding( $factura->NUMANTICIPO, "UTF-8" ),
                    'numRecibo' => $factura->NUMERO,
                    'idfact_deuda' => $factura->IDFACTURA_DEUDA,
                    'idDeuda' => $factura->IDCXP
                ]);
            }
        }

        return $listaFacturas;
    }

    public function getDeudaToral()
    {
        return $this->modeloProveedor->deudaGlobal();
    }
}
