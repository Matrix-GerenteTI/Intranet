<?php
require_once 'DB.php';
class Amortizacion extends DB
{
    public function insertAmortizacion($cuentaId,$pagos)
    {
        $multipleInsert = "";

        foreach ($pagos as $pago ) {
           if( strlen( $multipleInsert ) ){
               $multipleInsert .=",";
           }
               $multipleInsert .="('$cuentaId',NULL,'".$pago['fecha']."','".$pago['capital']."','".$pago['interes']."','".$pago['pago']."','".$pago['saldo']."','".$pago['descripcion']."')";
        }

        $queryInsert = "INSERT INTO amortizaciones(`id_con_cuentas`, `numeroPago`, `fechaPago`, `capital`, `interes`, `total`, `saldo`,`descripcion`) VALUES $multipleInsert ";
        
        $cantRegistrada = $this->insert($queryInsert);

        return $cantRegistrada;
    }

    public function insertaCtaContable($amortizacion)
    {
            //obteniendo informacion de la cuenta
            $cuenta = $this->getCuentas( $amortizacion['id_con_cuentas'] );
            //OBTENIENDO EL TOTAL DIVIDIDO ENTRE LAS 11 SUCURSALES
            $total = $amortizacion['total'] / 12;

            //Se registran los pagos a las sucursales de id del 1 al 10 y 13
            for ($udn=1; $udn <= 13 ; $udn++) { 
                if ( $udn<= 10 || $udn== 13 || $udn == 15) {
                    //preparando query de insercion a con_movimientos
                      $queryConMovimiento = "INSERT INTO `con_movimientos`
                                                            (`id`,`emisor`,`rfc`,`descripcion`,`fecha`,
                                                            `hora`,`docfecha`,`dochora`,`docserie`,
                                                            `docfolio`,`docuuid`,`subtotal`,`iva`,`ivaretenido`,
                                                            `isr`,`ieps`,`iepsretenido`,`total`,`idcbanco`,`cuenta`,
                                                            `idcon_cuentas`,`tipoCuenta`,`tipo`,`financiero`,`recurrente`,`status`,`timestamp`,
                                                            `idcudn`,`usuario`)
                                                        VALUES ('',NULL,NULL,'".$cuenta[0]['nombre']."','".$amortizacion['fechaPago']."',NOW(),'".$amortizacion['fechaPago']."',
                                                        NULL,NULL,'',NULL,'$total','0',NULL,NULL,NULL,NULL,'$total','','','".$amortizacion['id_con_cuentas']."',7,'2','1','0','1',NOW(),'$udn','admin')";    
                                                        // echo $queryConMovimiento;
                                                        // echo "<br><br>";
                        $this->insert($queryConMovimiento);
                }
            }
 
    }

    public function insertRentaMovimiento( $renta )
    {
        //obteniendo el udn de local que se le asignara el cargo de renta
        $sucursal = $this->getSucursales( $renta['descripcion'] );
        $total = $renta['total'];

        if ( sizeof( $sucursal)  ) {
            $udn = $sucursal[0]['id'];
            $queryConMovimiento = "INSERT INTO `con_movimientos`
                                    (`id`,`emisor`,`rfc`,`descripcion`,`fecha`,
                                    `hora`,`docfecha`,`dochora`,`docserie`,
                                    `docfolio`,`docuuid`,`subtotal`,`iva`,`ivaretenido`,
                                    `isr`,`ieps`,`iepsretenido`,`total`,`idcbanco`,`cuenta`,
                                    `idcon_cuentas`,`tipoCuenta`,`tipo`,`financiero`,`recurrente`,`status`,`timestamp`,
                                    `idcudn`,`usuario`)
                                VALUES ('',NULL,NULL, 'Renta ".$renta['descripcion']."','".$renta['fechaPago']."',NOW(),'".$renta['fechaPago']."',
                                NULL,NULL,'',NULL,'$total','0',NULL,NULL,NULL,NULL,'$total','','','".$renta['id_con_cuentas']."',3,'2','1','0','1',NOW(),'$udn','admin')";    

            $this->insert( $queryConMovimiento );
                                // echo $queryConMovimiento;
        } else {
            # code...
        }
        
    }

    public function getSucursales( $local )
    {
        $querySucursal = "SELECT * FROM csucursal WHERE  INSTR('$local',descripcion)";
        $sucursal = $this->select( $querySucursal);
        return $sucursal;
    }
    public function getCuentas($cuentaId)
    {
        $queryCuentas = "SELECT * from con_cuentas
                                        WHERE id = $cuentaId ";
        $listaCuenta = $this->select($queryCuentas);

        return $listaCuenta;
    }

    public function getRentasByIdCuenta($cuentaId)
    {
        $queryRenta = "SELECT * FROM amortizaciones WHERE id_con_cuentas ='$cuentaId' ";
        $rentas = $this->select( $queryRenta );
        return $rentas;
    }
    public function getMonthlyPayment($mesAct,$anioAct)
    {
        $queryPayment = "SELECT * FROM amortizaciones where MONTH(fechaPago) = $mesAct AND YEAR(fechaPago) = $anioAct";
    
        $setPayment = $this->select($queryPayment);;

        return $setPayment;
    }

    public function checkDailyPayment($cuentaId,$mes,$anio)
    {
        $queryPayment = "SELECT * FROM  con_movimientos WHERE MONTH(docfecha) = $mes AND YEAR(docfecha) = $anio AND idcon_cuentas = $cuentaId";
        $pagosRegistrados = $this->select( $queryPayment );

        return $pagosRegistrados;
    }
}
