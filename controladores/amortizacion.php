<?php
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/lib/PHPExcel/IOFactory.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/amortizacion.php";

if (  (!isset($_FILES['tabla'])  || $_FILES['tabla'] == null) && !isset( $_POST['cuenta'] ) ) {
} else {
    $cuentas = $_POST['cuenta'];
    $cols = array() ;
                                $cols =  array('startRow' => 9 ,
                            'fecha'  => 'A',
                            'capital' => 'B',
                            'interes' => 'C',
                            'pago' => 'E',
                            'saldo' => 'F',
                            'descripcion' => 'ZZ');
    switch ($cuentas) {
        case '35':
                            $cols =  array('startRow' => 10 ,
                            'fecha'  => 'B',
                            'capital' => 'F',
                            'interes' => 'D',
                            'pago' => 'J',
                            'saldo' => 'C',
                            'descripcion' => 'ZZ');
            break;
        case '36':
            $cols =  array('startRow' => 4 ,
                            'fecha'  => 'B',
                            'capital' => 'C',
                            'interes' => 'D',
                            'pago' => 'E',
                            'saldo' => 'F',
                            'descripcion' => 'ZZ');
            break;
        case '37':
                $cols =  array('startRow' => 9 ,
                            'fecha'  => 'A',
                            'capital' => 'B',
                            'interes' => 'C',
                            'pago' => 'E',
                            'saldo' => 'F',
                            'descripcion' => 'ZZ');
            break;                    
        case '38':
                            $cols =  array('startRow' => 9 ,
                            'fecha'  => 'A',
                            'capital' => 'B',
                            'interes' => 'C',
                            'pago' => 'E',
                            'saldo' => 'F',
                            'descripcion' => 'ZZ');
            break;            
        case '42':
            $cols =  array('startRow' => 7 ,
                            'fecha'  => 'B',
                            'capital' => 'C',
                            'interes' => 'D',
                            'pago' => 'E',
                            'saldo' => 'F',
                            'descripcion' => 'ZZ');
            break;            
        case '27':
           $cols =  array('startRow' => 4,
                            'fecha'  => 'C',
                            'capital' => 'F',
                            'interes' => 'M',//vacio
                            'pago' => 'F',
                            'saldo' => 'F',
                            'descripcion' => 'B');
            break;            
        default:
            # code...
            break;
    }

    if ( isset( $_FILES['tabla'])) {
        $amortizacion = new AmortizacionControler;
        $amortizacion->setExcelReader( $_FILES['tabla']['tmp_name']);


        $pagos = $amortizacion->extractValuesExcelTable($cols);
        $amortizacion->registraAmortizacion( $cuentas , $pagos);
        $amortizacion->registrarCtaContable();
        if( $cuentas == 27) {$amortizacion->registrarRentas(); }
      }
    }


class AmortizacionControler
{
    private $libroExcel;
    private $numRowsSheet;

    public function setExcelReader($urlArchivo , $hojaActiva = 0){
        if( $hojaActiva == 0){
            $this->libroExcel = PHPExcel_IOFactory::load($urlArchivo);
        }
        $this->libroExcel->setActiveSheetIndex($hojaActiva);
        $this->numRowsSheet = $this->libroExcel->setActiveSheetIndex($hojaActiva)->getHighestRow();
            echo $this->numRowsSheet;
    }
    
    public function extractValuesExcelTable($columnas){
        $rowPagos = array();
         
        for ($i=  $columnas['startRow'] ; $i < $this->numRowsSheet ; $i++) { 

            $pago['fecha'] = $this->libroExcel->getActiveSheet()->getCell($columnas['fecha']."$i")->getValue();

             if( $pago['fecha'] == NULL ) { break;}
            
             $unix_date = ($pago['fecha'] - 25569) * 86400;
             $pago['fecha'] = 25569 + ($unix_date / 86400);
             $unix_date = ($pago['fecha'] - 25569) * 86400;
            $pago['fecha'] = gmdate("Y-m-d", $unix_date);

            $pago['capital'] = $this->libroExcel->getActiveSheet()->getCell($columnas['capital']."$i")->getValue();
            if( $pago['capital'] == NULL){
                $pago['capital'] = 0;
            }
            $pago['interes'] = $this->libroExcel->getActiveSheet()->getCell($columnas['interes']."$i")->getValue();
            $pago['pago'] = $this->libroExcel->getActiveSheet()->getCell($columnas['pago']."$i")->getCalculatedValue();
            $pago['saldo'] = $this->libroExcel->getActiveSheet()->getCell($columnas['saldo']."$i")->getCalculatedValue();
            $pago['descripcion'] = $this->libroExcel->getActiveSheet()->getCell($columnas['descripcion']."$i")->getCalculatedValue();

            
             array_push($rowPagos,$pago);
             
        }
        
        return $rowPagos;
        
    }

    public function registraAmortizacion( $cuenta , $pagos)
    {
        
        $dbAmortizacion = new Amortizacion;
        $dbAmortizacion->insertAmortizacion($cuenta,$pagos);
    }

    public function registrarCtaContable()
    {
        $dbAmortizacion = new Amortizacion;
        $fechaActual = $this->checkCurrentDate();

        
        $mesPago = $dbAmortizacion->getMonthlyPayment($fechaActual['m'] , $fechaActual['a']);


        if ( sizeof( $mesPago ) ) { //Existe al menos una cuenta por pagrar en el mes actual?
                foreach ($mesPago as $amortizacion) {
                    //se comprueba que no haya pagos a cuentas por registrar en el mes
                    $movimientos = $dbAmortizacion->checkDailyPayment( $amortizacion['id_con_cuentas'] , $fechaActual['m'] , $fechaActual['a'] );
                    if ( sizeof( $movimientos) ) { //Existe movimientos pendiente para la cuenta
                            $movimientos = array();
                    }
                    else{
                        if( $fechaActual['d'] >= 5 && $amortizacion['id_con_cuentas'] != 27) //Verifica que sea 5 del mes actual
                        {
                            $dbAmortizacion->insertaCtaContable($amortizacion);
                        }
                    }
                }
        }
    }

    public function registrarRentas()
    {
        $dbAmortizacion = new Amortizacion;
        $rentas = $dbAmortizacion->getRentasByIdCuenta(27);
        $fecha = $this->checkCurrentDate();
        foreach ($rentas as $renta) {
            if ( $fecha['d'] >= 5) {

                //Cambiando la fecha de renta al del mes y año de en curso
                $mesBDRenta = substr( $renta['fechaPago'] ,4,3);
                
                $anioDbRenta = substr( $renta['fechaPago'] , 0,4);
                $badDate= array( $anioDbRenta, $mesBDRenta);
                $anio = $fecha['a'] ."";
                $currentDate = array( $anio, "-".$fecha['m']);
                $renta['fechaPago'] = str_replace( $badDate , $currentDate , $renta['fechaPago']);

                $movimientos = $dbAmortizacion->checkDailyPayment( $renta['id_con_cuentas'] , $fecha['m'] , $fecha['a'] );

                if ( sizeof($movimientos) == 0) { //No se ha registrado cargos de renta?
                    $dbAmortizacion->insertRentaMovimiento( $renta );
                }
                else{
                    $bandera = 0;
                    foreach ($movimientos as $movimiento) {
                        if ( ($movimiento['idcon_cuentas'] == $renta['id_con_cuentas']) &&  ($movimiento['total'] == $renta['total'])  && ($movimiento['fecha'] == $renta['fechaPago']) ) {
                            $bandera = 1;
                        } 
                        else {
                            
                        }
                        
                    }                    
                    if( ! $bandera){
                        $dbAmortizacion->insertRentaMovimiento( $renta );
                    }
                }
            }
        }
    }
    
    public function checkCurrentDate()
    {
        return array('d' => date('d') , 'm' => date('m'),'a' => date('Y') );
    }
}
