<?php
require_once dirname(__DIR__)."/modelos/saldos_bancarios.php";
require_once dirname(__DIR__)."/controladores/Reportes/Egresos/movimientos_bancos.php";

class SaldosBancariosController
{

    public static function insertaSaldo( $parametros)
    {
        $contadorRegistrado = 0;
        $parametros['egresos']  = $parametros['egresos']  == "" ? 0 : $parametros['egresos'] ;
        $parametros['ingresos']  = $parametros['ingresos']  == "" ? 0 : $parametros['ingresos'] ;
        $saldosBancarios = new SaldosBancarios;
        if ( $parametros['sucursal'] == 'zc') {
                $sucursales = array(
                'MATRIZ' =>1,
                'VILLAFLORES'=>2,
                'LEON 5TA' =>3,
                'PALMERAS' => 4,
                'LIB SUR' => 5,
                'LLANTERA 9NA' => 6,
                'LLANTERA 5TA' => 7,
                'LEON 9NA' => 10,
                'BOULEVARD' => 15           
            );
            $parametros['egresos']  =  $parametros['egresos'] / sizeof( $sucursales );
            $parametros['ingresos'] =  $parametros['ingresos']  / sizeof( $sucursales );
            foreach ($sucursales as $sucursalId) {
                $parametros['sucursal'] = $sucursalId;
                if ($saldosBancarios->insertaSaldo( $parametros ) ) {
                    $contadorRegistrado++;
                }
            }
            return $contadorRegistrado;
        } elseif( $parametros['sucursal'] == 'za') {
                $sucursales = array(
                'MERCALTOS' => 8,
                'SAN RAMON' => 9,
                'LEON SCLC' => 13 ,    
            );
            $parametros['egresos']  =  $parametros['egresos'] / sizeof( $sucursales );
            $parametros['ingresos'] =  $parametros['ingresos']  / sizeof( $sucursales );
            foreach ($sucursales as $sucursalId) {
                $parametros['sucursal'] = $sucursalId;
                if ($saldosBancarios->insertaSaldo( $parametros ) ) {
                    $contadorRegistrado++;
                }
            }            
            return $contadorRegistrado;       
        }elseif ( $parametros['sucursal'] == 'all') {
                        $sucursales = array(
                'MATRIZ' =>1,
                'VILLAFLORES'=>2,
                'LEON 5TA' =>3,
                'PALMERAS' => 4,
                'LIB SUR' => 5,
                'LLANTERA 9NA' => 6,
                'LLANTERA 5TA' => 7,
                'MERCALTOS' => 8,
                'SAN RAMON' => 9,
                'LEON 9NA' => 10,
                'LEON SCLC' => 13 ,    
                'BOULEVARD' => 15           
            );
            $parametros['egresos']  =  $parametros['egresos'] / sizeof( $sucursales );
            $parametros['ingresos'] =  $parametros['ingresos']  / sizeof( $sucursales );
            foreach ($sucursales as $sucursalId) {
                $parametros['sucursal'] = $sucursalId;
                if ($saldosBancarios->insertaSaldo( $parametros ) ) {
                    $contadorRegistrado++;
                }
            }
            return $contadorRegistrado;
        }else{
            return $saldosBancarios->insertaSaldo( $parametros );
        }
        
        
    }

    public  static function getSaldosRegistrados( $data = NULL,$pagination = NULL)
    {
        $saldosBancarios = new SaldosBancarios;
        $cuentasActivas = isset($data['cuenta'])  !=  '%'? $saldosBancarios->getCuentasSaldo($data['cuenta']): $saldosBancarios->getAllcuentas();
    
        $saldosMovimientos = array();
        $j= 0;
        $saldosOrdenados = array();
        $dataTemp = $data;

        if ( $data == NULL) {
            if( $pagination == null){
                $pagination = 0;
            }
            
            $saldos = $saldosBancarios->getSaldosSinFiltro($pagination);
            $saldosMovimientos = array();
            foreach ($saldos as $i => $saldo) {
                // $saldoInicial =  $saldos[$i]['saldo'];
                $saldosMovimientos[$saldo['id']]['id'] = $saldo['id'];
                $saldosMovimientos[$saldo['id']]['fecha'] = $saldo['fecha'];
                $saldosMovimientos[$saldo['id']]['banco'] = $saldo['banco'];
                $saldosMovimientos[$saldo['id']]['beneficiario'] = $saldo['beneficiario'];
                $saldosMovimientos[$saldo['id']]['referencia'] = $saldo['referencia'];
                $saldosMovimientos[$saldo['id']]['egresos'] = $saldo['egresos'];
                $saldosMovimientos[$saldo['id']]['ingresos'] = $saldo['ingresos'];
                $saldosMovimientos[$saldo['id']]['ingresos'] = $saldo['ingresos'];
                $saldosMovimientos[$saldo['id']]['sucursal'] = $saldo['sucursal_id'];
                $saldosMovimientos[$saldo['id']]['saldo'] =  0;//$saldoInicial;     
                // $saldoInicial= $saldoInicial - $saldo['egresos'] + $saldo['ingresos'];           
                $j++;
            }           
            krsort( $saldosMovimientos);

            foreach ($saldosMovimientos as $id => $saldo) {
                array_push($saldosOrdenados, $saldo);
            }
            
            return $saldosOrdenados;             
        }

        foreach ($cuentasActivas as $i => $cuenta) {
            
            //si la cuenta no se seleccionó debee mostrar todas la coincidencias 
            if ( $data['cuenta'] ==  '%') {
                $dataTemp['cuenta'] = $cuenta['id'];
            }elseif ($data['cuenta'] != $cuenta['id']){
                continue;
            }
            $saldos = $saldosBancarios->getSaldoDesgloce( $cuenta['id'],'%', $dataTemp, $pagination);
            
            $saldosTotales = $saldosBancarios->getSaldosCuenta( $cuenta['id']);
            
            $saldoInicial = $saldosTotales[0]['totalEgresos']- $saldosTotales[0]['totalIngresos'] + $cuenta['saldo'];
            foreach ($saldos as $i => $saldo) {
                
                $saldos[$i]['saldo'] = $saldoInicial - $saldo['egresos'] + $saldo['ingresos'];
                $saldoInicial =  $saldos[$i]['saldo'];
                $saldosMovimientos[$saldo['id']]['id'] = $saldo['id'];
                $saldosMovimientos[$saldo['id']]['fecha'] = $saldo['fecha'];
                if( !isset($saldosMovimientos[$saldo['id']]['banco']) ){
                        $saldosMovimientos[$saldo['id']]['banco'] = $cuenta['banco'];
                }
                
                $saldosMovimientos[$saldo['id']]['beneficiario'] = $saldo['beneficiario'];
                $saldosMovimientos[$saldo['id']]['referencia'] = $saldo['referencia'];
                $saldosMovimientos[$saldo['id']]['egresos'] = $saldo['egresos'];
                $saldosMovimientos[$saldo['id']]['ingresos'] = $saldo['ingresos'];
                $saldosMovimientos[$saldo['id']]['saldo'] = $saldoInicial;     
                $saldoInicial= $saldoInicial - $saldo['egresos'] + $saldo['ingresos'];           
                $saldosMovimientos[$saldo['id']]['sucursal'] = $saldo['sucursal_id'];
                $j++;
            }

        }
        krsort( $saldosMovimientos);

        foreach ($saldosMovimientos as $id => $saldo) {
            array_push($saldosOrdenados, $saldo);
        }
        echo json_encode( $saldosOrdenados );
        exit();
        return $saldosOrdenados;
    }

    public static function deleteSaldo( $id)
    {
        $saldosBancarios = new SaldosBancarios;
        return $saldosBancarios->deleteSaldo( $id);
    }

    public static function getAllCuentas(Type $var = null)
    {
        $saldosBancarios = new SaldosBancarios;
        return $saldosBancarios->getAllcuentas( );
    }

    public function getTipoCuentas()
    {
        $saldosBancarios = new SaldosBancarios;
        return $saldosBancarios->getTipoCuentas();
    }
    public function getTipoOperacionMovimiento( $params)
    {
        $saldosBancarios = new SaldosBancarios;
        return $saldosBancarios->getTipoOperacionMovimiento($params);
    }
    public function getIfIngresoEgresoMovimiento( $idMovimiento)
    {
        $saldosBancarios = new SaldosBancarios;
        return $saldosBancarios->getIfCargoAbonoMovimiento( $idMovimiento);
    }
    public function actualizarSaldoManual($id, $saldo)
    {
        $saldosBancarios = new SaldosBancarios;
        return $saldosBancarios->actualizarSaldoManual($id, $saldo);
    }

    public function registraSaldoProrrateado($ingreso,$egreso, $sucursales, $movimiento,$beneficiario = null)
    {
        $saldosBancarios = new SaldosBancarios;
        $contadorRegistrado = 0 ;
        
        foreach ($sucursales as $sucursal => $sucursalId) {
            
            $parametros = array(
                'fecha' =>  $movimiento['fecha'],
                'beneficiario' => $beneficiario,
                'referencia' => $movimiento['referencia'],
                'egresos' => $egreso,
                'ingresos' => $ingreso,
                'cuentaId' => $movimiento['cuenta_bancaria_id'],
                'sucursal' => $sucursalId,
                'movimientoId' => "NULL",
                'tipoMov' => "NULL"                
            );
            
            if ( $saldosBancarios->insertaSaldo( $parametros ) > 0) {
                $contadorRegistrado++;
            }
        }
        return $contadorRegistrado;
    }

    public function setBeneficiario($movimiento, $beneficiario, $sucursal )
    {
        $saldosBancarios = new SaldosBancarios;
        $descripcionMovimiento = $saldosBancarios->getMovimientoById( $movimiento);
        $descripcionMovimiento = $descripcionMovimiento[0];
        $contadorRegistrado = 0;

        if ( $sucursal === 'zc') { //Prorratear entre las sucursales de tuxtla

            $zonaCentro = array(
                'MATRIZ' =>1,
                'VILLAFLORES'=>2,
                'LEON 5TA' =>3,
                'PALMERAS' => 4,
                'LIB SUR' => 5,
                'LLANTERA 9NA' => 6,
                'LLANTERA 5TA' => 7,
                'LEON 9NA' => 10,
                'BOULEVARD' => 15
            );
            $egresoProrrateado =  $descripcionMovimiento['egresos'] / sizeof( $zonaCentro );
            $ingresoProrrateado =  $descripcionMovimiento['ingresos']  / sizeof( $zonaCentro );
            $contadorRegistrado = self::registraSaldoProrrateado($ingresoProrrateado,$egresoProrrateado,$zonaCentro,$descripcionMovimiento,$beneficiario);
            echo $contadorRegistrado;
            $saldosBancarios->deleteSaldo($movimiento);
        }elseif ( $sucursal === 'za') {
            $zonaAltos = array(
                    'MERCALTOS' => 8,
                    'SAN RAMON' => 9,
                    'LEON SCLC' => 13
            );
            $egresoProrrateado =  $descripcionMovimiento['egresos'] / sizeof( $zonaAltos );
            $ingresoProrrateado =  $descripcionMovimiento['ingresos']  / sizeof( $zonaAltos );
            $contadorRegistrado = self::registraSaldoProrrateado($ingresoProrrateado,$egresoProrrateado,$zonaAltos,$descripcionMovimiento,$beneficiario);
            echo $contadorRegistrado;
            $saldosBancarios->deleteSaldo($movimiento);
            return $contadorRegistrado;
            
        }elseif( $sucursal === 'all'){
            $sucursales = array(
                'MATRIZ' =>1,
                'VILLAFLORES'=>2,
                'LEON 5TA' =>3,
                'PALMERAS' => 4,
                'LIB SUR' => 5,
                'LLANTERA 9NA' => 6,
                'LLANTERA 5TA' => 7,
                'MERCALTOS' => 8,
                'SAN RAMON' => 9,
                'LEON 9NA' => 10,
                'LEON SCLC' => 13 ,    
                'BOULEVARD' => 15           
            );
            $egresoProrrateado =  $descripcionMovimiento['egresos'] / sizeof( $sucursales );
            $ingresoProrrateado =  $descripcionMovimiento['ingresos']  / sizeof( $sucursales );
            $contadorRegistrado = self::registraSaldoProrrateado($ingresoProrrateado,$egresoProrrateado,$sucursales,$descripcionMovimiento,$beneficiario);
            echo $contadorRegistrado;
            $saldosBancarios->deleteSaldo($movimiento);            
        }else{
            return $saldosBancarios->setBeneficiario( $movimiento, $beneficiario,$sucursal);
        }
        
    }
}




    if( isset($_GET['opc']) ){
        switch ( $_GET['opc'] ) {
            case 'getAllCuentas':
                echo json_encode( SaldosBancariosController::getAllCuentas() );
                break;
            case 'allSaldos':
                if ( ( trim($_GET['cuenta']) ) > 0 || strlen( trim($_GET['beneficiario']) ) > 0 || strlen( trim($_GET['fecha']) ) > 0 ) {
                    $filtro = array('cuenta' => $_GET['cuenta'] < 0 ? '%' : $_GET['cuenta'],
                                            'beneficiario' => strlen($_GET['beneficiario']) > 0 ? "%".$_GET['beneficiario']."%" :  '%',
                                            'referencia' => strlen( $_GET['referencia'] ) > 0 ? '%'.$_GET['referencia'].'%' : '%');
                    $explodeFecha = explode('/', $_GET['fecha']);
                    $explodeFechaFin = explode("/", $_GET['fechaFin']);
                    if (strlen( trim($_GET['fecha']) ) > 0 && strlen( trim($_GET['fechaFin']) ) > 0) {
                        $filtro['fecha'] =  " fecha >= '".$explodeFecha[2]."-".$explodeFecha[1]."-".$explodeFecha[0]."' AND fecha <= '" .$explodeFechaFin[2]."-".$explodeFechaFin[1]."-".$explodeFechaFin[0]."'";
                    }elseif( strlen( trim($_GET['fecha']) ) > 0 && strlen( trim($_GET['fechaFin']) ) == 0 ){
                        $filtro['fecha'] =  " fecha >= '".$explodeFecha[2]."-".$explodeFecha[1]."-".$explodeFecha[0]."'";
                    }elseif(  strlen( trim($_GET['fecha']) ) == 0 && strlen( trim($_GET['fechaFin']) ) > 0 ) {
                        $filtro['fecha'] =  " fecha >= '".$explodeFechaFin[2]."-".$explodeFechaFin[1]."-".$explodeFechaFin[0]."'";
                    } else{
                        $filtro['fecha'] = "fecha like '%' ";
                    }
                    
                    echo json_encode( SaldosBancariosController::getSaldosRegistrados($filtro, $_GET['pag']) );
                }else{
                    
                    echo json_encode( SaldosBancariosController::getSaldosRegistrados(null, $_GET['pag']) );
                }
                
                break;
            case 'delSaldo':
                echo SaldosBancariosController::deleteSaldo( $_GET['id']);
                break;
            case 'actManual':
                echo SaldosBancariosController::actualizarSaldoManual( $_POST['id'], $_POST['saldo']);
                break;
            case 'filtro':
            
                $filtro = array('cuenta' => $_GET['cuenta'] < 0 ? '%' : $_GET['cuenta'],
                                        'beneficiario' => strlen($_GET['beneficiario']) > 0 ? "%".$_GET['beneficiario']."%" :  '%',
                                        'referencia' => strlen( $_GET['referencia'] ) > 0 ? '%'.$_GET['referencia'].'%' : '%' );
                    $explodeFecha = explode('/', $_GET['fecha']);
                    $explodeFechaFin = explode("/", $_GET['fechaFin']);
                    if (strlen( trim($_GET['fecha']) ) > 0 && strlen( trim($_GET['fechaFin']) ) > 0) {
                        $filtro['fecha'] =  " fecha >= '".$explodeFecha[2]."-".$explodeFecha[1]."-".$explodeFecha[0]."' AND fecha <= '" .$explodeFechaFin[2]."-".$explodeFechaFin[1]."-".$explodeFechaFin[0]."'";
                    }elseif( strlen( trim($_GET['fecha']) ) > 0 && strlen( trim($_GET['fechaFin']) ) == 0 ){
                        $filtro['fecha'] =  " fecha >= '".$explodeFecha[2]."-".$explodeFecha[1]."-".$explodeFecha[0]."'";
                    }elseif(  strlen( trim($_GET['fecha']) ) == 0 && strlen( trim($_GET['fechaFin']) ) > 0 ) {
                        $filtro['fecha'] =  " fecha >= '".$explodeFechaFin[2]."-".$explodeFechaFin[1]."-".$explodeFechaFin[0]."'";
                    } else{
                        $filtro['fecha'] = "fecha like '%' ";
                    }
                echo json_encode( SaldosBancariosController::getSaldosRegistrados($filtro) );
                break;
            case 'getTipoCuentas':
                        echo json_encode( array('tipoCuenta' => SaldosBancariosController::getTipoCuentas(),
                                                                    'cargoAbono'  => SaldosBancariosController::getIfIngresoEgresoMovimiento($_GET['movId'])) );
                break;
            case 'getTipoOperacionMov':
                    echo json_encode(SaldosBancariosController::getTipoOperacionMovimiento($_GET) );
                break;
            case 'reporteMovimientos':
                        
                break;
            default:
                # code...
                break;
        }
    
    }else{
        
        if( isset($_POST['opc'])){
            echo SaldosBancariosController::setBeneficiario($_POST['movimiento'], $_POST['beneficiario'], $_POST['sucursal']);
        }else{
            
            echo  SaldosBancariosController::insertaSaldo( $_POST );
        }   
    }

