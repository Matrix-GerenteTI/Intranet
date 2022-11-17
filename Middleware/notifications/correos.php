<?php
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/Contabilidad/cxp.php";
use cxp\CuentasPorPagar;

class Notificaciones extends MailSender 
{
    protected $modeloCxP;

    public function aplicacionPagos(  $id , $esAbono)
    {
        $this->modeloCxP = new cxp\CuentasPorPagar;
        $detallePago = $this->modeloCxP->getDetallePagoAplicado( $id );

        if ( isset( $detallePago[0]) ) {
            $labelAbono = $esAbono == true ? "Se realiz&oacute;  un abono a la factura: " : "Se liquid&oacute; la factura: ";
            $this->send([
                'descripcionDestinatario' => "Aplicacion de pagos",
                'subject' => "Aplicacion de pago a proveedor",
                'mensaje' => "$labelAbono ".$detallePago[0]->NUMFACT." por un monto de <b>$".number_format ( $detallePago[0]->IMPORTECOBRO)."</b> del proveedor: <b>".$detallePago[0]->PYM_NOMBRE." <br> v&iacute;a: <b>AppSitex</b>",
                'correos' =>['software@matrix.com.mx','ingresos@matrix.com.mx','egresos@matrix.com.mx','luisimatrix@hotmail.com'],
                'pathFile ' => ''
            ]);
        }
    }
}
