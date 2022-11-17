<?php
// date_default_timezone_set ('America/Mexico_City');
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/lib/PHPExcel.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/saldos_bancarios.php";


$libro =$_FILES['documentoArrastre']['tmp_name'];
$cuentaIngresada = $_POST['hoja'];
// $inicio = $_POST['inicio'];
// $fin = $_POST['fin'];
$momivimentos = fopen( $libro, 'r');
$saltosLinea = fread ( $momivimentos, $_FILES['documentoArrastre']['size']);

$arrayMovimientos =( explode("\n",$saltosLinea ) );

$contador = 0;
$saldos = new SaldosBancarios;

 function insertaMovimientosBancomer($cuentaIngresada, $arrayMovimientos)
{
    $contador = 0;
    $saldos = new SaldosBancarios;
              $momivimentos = registraMovimientosBancarios(  array(
                    'cargo' => 2,
                    'abono' => 3,
                    'referencia' => 1,
                    'fecha' => 0,
                    'saldo' => 4,
                    'delimitador' => "\t",
                    'cuentaIngresada' => $cuentaIngresada,
                    'arrayMovimientos' => $arrayMovimientos
            ) );
        $cantidadMovimientos = sizeof( $momivimentos );

            $arrayMovimientosVerificados = [];
            for ($i= ($cantidadMovimientos -1 ) ; $i >= 0 ; $i--) { 
                // comprobando que la referencia no haya cambiado en ese caso ya no se guarda 
                $movimientosRegistrados = $saldos->getMovimientoSimilares( $momivimentos[$i] );
                $cantCoincidencias = sizeof( $movimientosRegistrados );
                if ( $cantCoincidencias == 0) { //Esos montos aún no se encuentran registrador?
                    if($saldos->insertaSaldo( $momivimentos[$i]) > 0 ){
                        $contador ++;
                    }  
                    
                }else{
                    foreach ($movimientosRegistrados as $movimientoRegistrado) {
                        if ( trim( strpos(trim( $momivimentos[$i]['referencia'] ), trim( $movimientoRegistrado['referencia'] ) ) ) !== false )  {
                            $diferenciasReferencia = trim( str_replace($movimientoRegistrado['referencia'],'',$momivimentos[$i]['referencia']) );

                        }else{
                            if ( strpos( trim($movimientoRegistrado['referencia'] ),trim( $momivimentos[$i]['referencia'] ) ) !== false ) {
                                $diferenciasReferencia = trim( str_replace($movimientoRegistrado['referencia'],'',$momivimentos[$i]['referencia']) );

                            }else{
                                // verificando que sea un concepto que contenga  Concepto/mesDia con Concepto/numeroMesDia
                                $explodeConceptoRegistrado = explode("/", $movimientoRegistrado['referencia']);
                                $explodeConceptoArchivo = explode("/", $momivimentos[$i]['referencia']);
                                if ( sizeof( $explodeConceptoRegistrado ) == 2 && sizeof( $explodeConceptoArchivo ) ) {
                                    $difenciaConceptos = str_ireplace( trim( $explodeConceptoRegistrado[2] ), trim( $explodeConceptoArchivo[2] )  );
                                    if (! is_numeric( trim( $difenciaConceptos ) ) ) {
                                        if($saldos->insertaSaldo( $momivimentos[$i]) > 0 ){
                                            $contador ++;
                                        }                                         
                                    }
                                } else {
                                    if($saldos->insertaSaldo( $momivimentos[$i]) > 0 ){
                                        $contador ++;
                                    }     
                                }

                        
                            }                     
                        }
                    }

                }

            }
    echo $contador;            
}

switch ( $cuentaIngresada) {
    case '1':
            insertaMovimientosBancomer($cuentaIngresada, $arrayMovimientos);
        break;

     case '2':
        insertaMovimientosBancomer($cuentaIngresada, $arrayMovimientos);
     break;
    case '5':
        $csvBanco = fopen( $libro, 'r');
        $csvMovimientos = fread( $csvBanco, $_FILES['documentoArrastre']['size'] );
        $explodeMovimientos = explode("\n",$csvMovimientos);

        //var_dump( $explodeMovimientos );
        
        $arrayMovimientos = [];
        // se extraen unicamente los movimientos 

        

        foreach ($explodeMovimientos as  $idx => $movimiento ) {            
            $explodeMovimientos[$idx] = str_replace('",',',',$explodeMovimientos[$idx]);
            for($an=1;$an<=2;$an++){
                $pos = strpos($explodeMovimientos[$idx], '"');
                if ($pos !== false){
                $bit = substr($explodeMovimientos[$idx],$pos,9);
                $formated = str_replace(",","",$bit);               
                $formated = str_replace('"','',$formated);       
                //echo $bit." to ".$formated."\n";
                $explodeMovimientos[$idx] = str_replace($bit,$formated,$explodeMovimientos[$idx]);
                }
            }

            //echo $explodeMovimientos[$idx]."\n";
            $explodeMovimientos[$idx] = str_replace(',',"|", $explodeMovimientos[$idx]);
            $explodeMovimientos[$idx] = str_replace('"','', $explodeMovimientos[$idx]);
            //echo $movimiento."\t";
            $contenidoMovto = explode("|", $explodeMovimientos[$idx ]);
            
            if ( validateDate( $contenidoMovto[0] ) ) {
                $fechaExplode = explode("/" , $contenidoMovto[0] );
                $contenidoMovto[0] = $fechaExplode[0]."-".$fechaExplode[1]."-".$fechaExplode[2];
                $contenidoMovto[1] = utf8_encode( $contenidoMovto[1] );
                $explodeMovimientos[$idx] = implode("|", $contenidoMovto );
                array_push($arrayMovimientos, $explodeMovimientos[$idx] );
            }
        }

        //var_dump( $arrayMovimientos );

              $momivimentos = registraMovimientosBancarios(  array(
                    'cargo' => 3,
                    'abono' => 2,
                    'referencia' => 1,
                    'fecha' => 0,
                    'delimitador' => "|",
                    'cuentaIngresada' => $cuentaIngresada,
                    'arrayMovimientos' => $arrayMovimientos
            ) );

            $cantidadMovimientos = sizeof( $momivimentos );
            //echo json_encode( $momivimentos );
            //exit();
            for ($i= ($cantidadMovimientos -1) ; $i  >= 0; $i-- ){ 
                    if($saldos->insertaSaldo( $momivimentos[$i]) > 0 ){
                        $contador ++;
                    }  
            }
            
            //actualizando el saldo de banamex
            $saldoAdepurar = explode('$', $explodeMovimientos[7]);
            $saldo = str_replace("+",'', $saldoAdepurar[1]);
            $saldo = str_replace("-",'', $saldo);
            $saldo = str_replace(" ",'', $saldo);
            $saldo = str_replace("+",'', $saldo);
            $saldo = str_replace(",",'', $saldo);
            
                $saldos->actualizaTablaCuentasSaldos( 5, $saldo);

            echo $contador;           
    break;


    case '3':
            $csvBanco = fopen( $libro, 'r');;
            $csvMovimientos = fread( $csvBanco, $_FILES['documentoArrastre']['size'] );
            $explodeMovimientos = explode("\n",$csvMovimientos);
            $arrayMovimientos = array();
            // $sizeMovimientos = sizeof( $csvMovimientos );
            foreach ($explodeMovimientos  as $movimiento) {
                $explodeMovimiento = explode(",",$movimiento);
                $numeroCuenta = str_replace('"','',$explodeMovimiento[0]);
                if ( is_numeric( trim($numeroCuenta) ) ) {
                    
                    $fecha = trim( str_replace('"','',$explodeMovimiento[1] ) );
                    $dia = substr($fecha, 1,2);
                    $mes = substr($fecha,3,2);
                    $anio = substr($fecha,5,4);
                    $fecha = "$dia-$mes-$anio";
                    $referencia = trim( str_replace('"','', $explodeMovimiento[4]) )." ".trim( str_replace('"','', $explodeMovimiento[9]) );
                    $importe =  trim( str_replace('"','', $explodeMovimiento[6]) );
                    $tipo = trim( str_replace('"','', $explodeMovimiento[5]) );
                    $saldo = trim( str_replace('"', '',$explodeMovimiento[7]) );
                    
                    
                    if ( $tipo == '-') {
                        array_push($arrayMovimientos, "$fecha\t$referencia\t$importe\t-\t$saldo");
                        
                    }else{
                        array_push($arrayMovimientos, "$fecha\t$referencia\t-\t$importe\t$saldo");
                    }
                    
                }
                
            }
            
            $momivimentos = registraMovimientosBancarios ( array(
                    'cargo' => 2,
                    'abono' => 3,
                    'referencia' => 1,
                    'fecha' => 0,
                    'saldo' =>4,
                    'delimitador' => "\t",
                    'cuentaIngresada' => $cuentaIngresada  ,
                    'arrayMovimientos' => $arrayMovimientos
            ) );
            $cantidadMovimientos = sizeof( $momivimentos );

        for ($i= 0; $i <=  $cantidadMovimientos-1 ; $i++) { 
                if($saldos->insertaSaldo( $momivimentos[$i]) > 0 ){
                    $contador ++;
                }  
        }
        echo $contador;                
        
        break;

    case '4':
            $csvBanco = fopen( $libro, 'r');;
            $csvMovimientos = fread( $csvBanco, $_FILES['documentoArrastre']['size'] );
            

            $explodeMovimientos = preg_split('/\R/',$csvMovimientos);     
            // $csvMovimientos = fgetcsv( $csvBanco,4000, ',' );
            $sizeMovimientos = sizeof( $explodeMovimientos );
            $arrayMovimientos = array();
            $expresionDinero =  '/\,?\$((\d{1,3}(,\d{3})*)|(\d+))(\.\d{2})?\,?$/';
            foreach ($explodeMovimientos as $i => $movimiento) {

                if ( $i == 0) { //es el encabezado del archivo
                    unset( $explodeMovimientos[$i]);
                    continue; 
                }
                $movimiento = trim( str_replace('"','', $movimiento ) );
                
                // Eliminando las comas del string donde hay dinero
                $contentMovimiento = $movimiento;
                $afectados = 0;
                do{
                    $contentMovimiento = preg_replace ($expresionDinero,'', $contentMovimiento, -1, $afectados );
                    
                }while( $afectados != 0);
                
                //obteniendo la diferencia entre las cadenas de texto
                if ($contentMovimiento != $movimiento) {
                    
                     $valores =  substr($movimiento, strlen($contentMovimiento), strlen($movimiento))."<br>";
                     $valores = str_replace(",,","\t\t", $valores);
                     $valores = str_replace(",$","\t$", $valores);
                     $valores = str_replace(",","", $valores);
                    //  Separando el dato de la transferencia para luego se reagrupada con tabulaciones
                    
                     $contentMovimiento = preg_replace('/\s+/',' ',$contentMovimiento);
                    $contentMovimiento = str_replace( ", "," ", $contentMovimiento ); //elimina comas dentro del texto
                    $contentMovimiento = explode( ",", $contentMovimiento ); //elimina comas delimitadoras
                    
                    $contentMovimiento = implode("\t", $contentMovimiento);
                     $explodeMovimientos[$i] =$contentMovimiento.$valores;
                     
                    }

            }
            
            // var_dump( $explodeMovimientos);
            // for ($i= 7; $i <= ($sizeMovimientos-8) ; $i = $i+7) { 
            //     $csvMovimientos[$i+4] = str_replace('$','',$csvMovimientos[$i+4]);
            //     $csvMovimientos[$i+4] = str_replace(',','',$csvMovimientos[$i+4]);
            //     $csvMovimientos[$i+4] =  strlen( trim($csvMovimientos[$i+4]) ) > 0 ? $csvMovimientos[$i+4] : '-';
            //     $csvMovimientos[$i+5] = str_replace(',','',$csvMovimientos[$i+5]);
            //     $csvMovimientos[$i+5] = str_replace('$','',$csvMovimientos[$i+5]);
            //     $csvMovimientos[$i+5] =  strlen( trim($csvMovimientos[$i+5]) ) > 0 ? $csvMovimientos[$i+5] : '-';
            //     $csvMovimientos[$i+3] = str_replace("\t",' ',$csvMovimientos[$i+3]);

            //     // Eliminando la palabra cheque de inicio
            //     if( strpos($csvMovimientos[$i], "Cheque") === 0){
            //         $csvMovimientos[$i] = str_replace('Cheque','',$csvMovimientos[$i] );
            //     }
            //     array_push($arrayMovimientos,str_replace('"','', "$csvMovimientos[$i]\t".$csvMovimientos[$i+3]."\t".$csvMovimientos[$i+4]." \t".$csvMovimientos[$i+5]."\t".$csvMovimientos[$i+1]."\t".$csvMovimientos[$i+6] ) );
            // }

            // krsort( $explodeMovimientos );
            $momivimentos = registraMovimientosBancarios ( array(
                    'cargo' => 4,
                    'abono' => 5,
                    'referencia' => 3,
                    'fecha' => 0,
                    'saldo' => 6,
                    'delimitador' => "\t",
                    'movimientoId' => 1,
                    'cuentaIngresada' => $cuentaIngresada  ,
                    'arrayMovimientos' => $explodeMovimientos
            ) );

            
        $cantidadMovimientos = sizeof( $momivimentos );


        for ($i= 0; $i <=  $cantidadMovimientos-1 ; $i++) { 
                if($saldos->insertaSaldo( $momivimentos[$i]) > 0 ){
                    $contador ++;
                }  
        }
        echo $contador;                
        break;
}

 function registraMovimientosBancarios( $dataCondicion)
{
    $listMovimientos = array();
    $saldos = new SaldosBancarios;
    extract( $dataCondicion );

    foreach ($arrayMovimientos as $movimientoBanco) {
        $movimientoExplode = explode( $delimitador, $movimientoBanco);
        
        $cuentaNominalCorrecta = false;
        $arraySetMovimiento = array();

        if ( !isset($movimientoExplode[$cargo])  && !isset($movimientoExplode[$abono])) {
            continue;
        }
        $movimientoExplode[$cargo] = str_replace("$","", $movimientoExplode[$cargo]);
        $movimientoExplode[$abono] = str_replace("$","", $movimientoExplode[$abono]);

        $movimientoExplode[$cargo] = str_replace(',','',$movimientoExplode[$cargo] );
        $movimientoExplode[$abono] = str_replace(',','',$movimientoExplode[$abono] );

        if ( is_numeric(trim($movimientoExplode[$cargo])) && trim($movimientoExplode[$cargo]) != '') { //Cargo
            $cuentaNominalCorrecta = true;
            $arraySetMovimiento['egresos'] = str_replace(',','',trim($movimientoExplode[$cargo])) / 1;
            $arraySetMovimiento['ingresos'] = 0;
        }elseif ( is_numeric(trim($movimientoExplode[$abono])) && trim($movimientoExplode[$abono]) != '' ) { //Abono
            $cuentaNominalCorrecta = true;
            $arraySetMovimiento['ingresos'] = str_replace(',','',trim($movimientoExplode[$abono]) ) / 1;
            $arraySetMovimiento['egresos'] = 0;
        }
        
        $sucursales = $saldos->getAfiliacionesTerminal_sucursal( $cuentaIngresada );
        $beneficiario = '';
        foreach ($sucursales as $i => $sucursal) {
            if ( strpos($movimientoExplode[$referencia], $sucursal['numero']) !== false ) {
                $beneficiario = $sucursal['descripcion'];
            }
        }

        // comprobando que el array no pertenezca a los movimientos de banamex
        $campoSaldo = "-";
        // var_dump( $saldo);
        
        if ( isset($saldo) ) {
                $campoSaldo = str_replace(',','',$movimientoExplode[$saldo]);
                $campoSaldo = str_replace('$','',$campoSaldo);
        }

        $arraySetMovimiento['beneficiario'] = $beneficiario;
        $arraySetMovimiento['referencia'] = $movimientoExplode[$referencia];
        $arraySetMovimiento['cuentaId'] = $cuentaIngresada;
        $arraySetMovimiento['sucursal'] = 0;
        $arraySetMovimiento['tipoMov'] = "NULL";
        $arraySetMovimiento['saldo'] = $campoSaldo;
        $arraySetMovimiento['movimientoId'] = isset($movimientoId) ? "'".$movimientoExplode[$movimientoId]."'" : "''";

        
        if ( $cuentaNominalCorrecta) {
            //Exploding date
            
            $arraySetMovimiento['fecha'] = fechaConverter( $movimientoExplode[$fecha]);
            

            array_push($listMovimientos, $arraySetMovimiento);
            
        }
    }
    return $listMovimientos;
}


function validateDate($date, $format = 'd/m/Y')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

function fechaConverter( $fecha)
{
    $meses = array('Ene' => 1 ,'Feb' => 2,'Mar' => 3,'Abr' => 4,'May' => 5,'Jun' => 6,'Jul' =>7,'Ago' => 8,'Sep' => 9,'Oct' =>10,'Nov' => 11,'Dic' => 12);
    $fechaExplode = explode('-', $fecha);
    if ( sizeof($fechaExplode) > 1 ) {
        return $fechaExplode[2]."-".$fechaExplode[1]."-".$fechaExplode[0];
    }else{
        $fecha = substr($fecha,-12,-1) .$fecha[strlen($fecha)-1];
        
        $fechaExplode = explode('/', $fecha);
        if ( sizeof( $fechaExplode) > 1 ) {
            
            if ( ! is_numeric($fechaExplode[1]) ) {
                $mes = '';
                
                foreach ($meses as $mesAbreviado => $numeroMes) {
                    
                    if ( strpos( $fechaExplode[1], $mesAbreviado) !== false ) {
                        return $fechaExplode[2].'-'.$numeroMes.'-'.$fechaExplode[0];
                    }
                }
            }
        }
    }

}


// $inputFileTypr = PHPExcel_IOFactory::identify($libro);
// $reader = PHPExcel_IOFactory::createReader( $inputFileTypr);
// $phpExcel = $reader->load($libro);
// $hoja = $phpExcel->getSheet( $hojaIgresada);
// $highestRow = $hoja->getHighestRow();




// for ($i= $inicio; $i <= $fin ; $i++) { 
//     // $movimientosExcel = array('fecha'=> date($format = "Y-m-d", PHPExcel_Shared_Date::ExcelToPHP( $hoja->getCell("A$i")->getValue() ))
//     //                 ,'beneficiario'=> $hoja->getCell("B$i")->getValue(),
//     //                 'referencia'=> $hoja->getCell("C$i")->getValue(),
//     //                 'egresos'=>  $hoja->getCell("D$i")->getValue() != NULL ? $hoja->getCell("D$i")->getValue() : 0 ,
//     //                 'ingresos'=> $hoja->getCell("E$i")->getValue()  != NULL ? $hoja->getCell("E$i")->getValue() : 0 ,
//     //                 'cuentaId' => $hojaIgresada);
//     // $saldos = new SaldosBancarios;
//     // if($saldos->insertaSaldo( $movimientosExcel) > 0 ){
//     //     $contador ++;
//     // }                 
// }

// echo $contador;