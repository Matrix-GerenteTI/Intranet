<?php 

require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/Ventas/ventas.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/con_edosfinancieros.php";

class VentasController  
{
    protected $modeloVentas;
    protected $edosfinancieros;

    public function __construct()
    {
        $this->modeloVentas = new Ventas;
        $this->edosfinancieros = new EdoFinancieros();
    }

    public function getRazonesVentasFallidas()
    {
        $listaRazones = $this->modeloVentas->getRazonesVentasFallidas();

        foreach ($listaRazones as $i => $razon) {
            $listaRazones[$i]['descripcion'] = utf8_encode( $razon['descripcion'] );
            $listaRazones[$i]['name'] = utf8_encode( $razon['descripcion'] );
        }

        return $listaRazones;
    }

    public function setHistorialControlPiso( $params )
    {
        
        return $this->modeloVentas->setHistorialControlPiso( $params );
    }

    // public function getFlujoIngresos( $fecha  )
    // {
    //     if( $fecha == 'Haz clic en el calendario'){
    //         $fecha = date("Y-m-d");
    //     }else{
    //         $explodeFecha = explode("/", $fecha);
    //         $fecha = $explodeFecha[2]."-".$explodeFecha[1]."-".$explodeFecha[0];
    //     }

    //     $ingresosPorFormaPago = $this->modeloVentas->getFlujoIngresos( $fecha );
    //     $ingresosOnlineFormaPago = $this->modeloVentas->getFlujoIngresosVentasOnline( $fecha );
    //     $ingresosPorSucursal = [];
    //     foreach ($ingresosPorFormaPago as $i => $ingreso) {
    //         $ingresosPorFormaPago[$i]->REFERENCIA = mb_convert_encoding($ingreso->REFERENCIA, "UTF-8");
    //         $ingresosPorFormaPago[$i]->IMPORTE = round( $ingreso->IMPORTE , 2 );
    //         if( strpos( $ingreso->FORMAPAGO, "#04") !== false || strpos( $ingreso->FORMAPAGO,"#28") !== false  ){
    //             if ( isset( $ingresosPorSucursal[$ingreso->DESCRIPCION]['tarjetas'] ) ) {
    //                 $ingresosPorSucursal[$ingreso->DESCRIPCION]['tarjetas'] += $ingreso->IMPORTE;
    //             } else {
    //                 $ingresosPorSucursal[$ingreso->DESCRIPCION]['tarjetas'] = $ingreso->IMPORTE;
    //             }
                 
    //         }else if( strpos( $ingreso->FORMAPAGO, "#02") !== false){
    //             $ingresosPorSucursal[$ingreso->DESCRIPCION]['cheque'] = $ingreso->IMPORTE;
    //         }else if( strpos( $ingreso->FORMAPAGO, "#01") !== false ){
    //             if ( isset( $ingresosPorSucursal[$ingreso->DESCRIPCION]['efectivo'] ) ) {
    //                 $ingresosPorSucursal[$ingreso->DESCRIPCION]['efectivo'] += $ingreso->IMPORTE;
    //             } else {
    //                 $ingresosPorSucursal[$ingreso->DESCRIPCION]['efectivo'] = $ingreso->IMPORTE;
    //             }
    //         }else if( strpos( $ingreso->FORMAPAGO, "#03") !== false){
    //             $ingresosPorSucursal[$ingreso->DESCRIPCION]['transferencias'] = $ingreso->IMPORTE;
    //         }
    //     }

    //     //para ventas en Linea
    //     if ( sizeof( $ingresosOnlineFormaPago ) ) {
    //         foreach ( $ingresosOnlineFormaPago as $i => $ingreso) {
    //             $ingresosPorFormaPago[$i]->REFERENCIA = mb_convert_encoding($ingreso->REFERENCIA, "UTF-8");
    //             $ingresosPorFormaPago[$i]->IMPORTE = round( $ingreso->IMPORTE , 2 );
    //             if( strpos( $ingreso->FORMAPAGO, "#04") !== false || strpos( $ingreso->FORMAPAGO,"#28") !== false  ){
    //                 if ( isset( $ingresosPorSucursal["VENTAS ONLINE"]['tarjetas'] ) ) {
    //                     $ingresosPorSucursal["VENTAS ONLINE"]['tarjetas'] += $ingreso->IMPORTE;
    //                 } else {
    //                     $ingresosPorSucursal["VENTAS ONLINE"]['tarjetas'] = $ingreso->IMPORTE;
    //                 }
                     
    //             }else if( strpos( $ingreso->FORMAPAGO, "#02") !== false){
    //                 $ingresosPorSucursal["VENTAS ONLINE"]['cheque'] = $ingreso->IMPORTE;
    //             }else if( strpos( $ingreso->FORMAPAGO, "#01") !== false ){
    //                 if ( isset( $ingresosPorSucursal["VENTAS ONLINE"]['efectivo'] ) ) {
    //                     $ingresosPorSucursal["VENTAS ONLINE"]['efectivo'] += $ingreso->IMPORTE;
    //                 } else {
    //                     $ingresosPorSucursal["VENTAS ONLINE"]['efectivo'] = $ingreso->IMPORTE;
    //                 }
    //             }else if( strpos( $ingreso->FORMAPAGO, "#03") !== false){
    //                 $ingresosPorSucursal["VENTAS ONLINE"]['transferencias'] = $ingreso->IMPORTE;
    //             }            
    //         }
    //     }else{
    //         $ingresosPorSucursal["VENTAS ONLINE"]['cheque'] = 0;
    //         $ingresosPorSucursal["VENTAS ONLINE"]['transferencias'] = 0;
    //         $ingresosPorSucursal["VENTAS ONLINE"]['efectivo'] = 0;
    //         $ingresosPorSucursal["VENTAS ONLINE"]['tarjetas'] = 0;
    //     }


    //     //creando el arreglo procesado para que muestre el desgloce generl
    //     $ingresos =[];
    //     $totalGlobal =0;
    //     foreach ($ingresosPorSucursal as $i => $montos) {
    //         $total = 0;
    //         $total +=  isset( $montos['tarjetas'] ) ? $montos['tarjetas'] : 0;
    //         $total += isset( $montos['cheque']) ? $montos['cheque'] : 0;
    //         $total += isset( $montos['efectivo']) ? $montos['efectivo'] : 0;
    //         $total += isset( $montos['transferencias']) ? $montos['transferencias'] : 0;
    //         $totalGlobal += $total;
    //         array_push( $ingresos , [
    //             'sucursal' => $i,
    //             'total' => $nombre_format_francais = number_format($total, 2, '.', ','),
    //             'desgloce' => $montos
    //         ]);
    //     }

    //     return [ round( $totalGlobal,2) , $ingresos];
    // }

    public function getFlujoIngresos( $fecha  )
    {
        if( $fecha == 'Haz clic en el calendario'){
            $fecha = date("Y-m-d");
        }else{
            $explodeFecha = explode("/", $fecha);
            $fecha = $explodeFecha[2]."-".($explodeFecha[1]/1)."-".$explodeFecha[0];
        }

        $ingresosPorFormaPago = $this->modeloVentas->getFlujoIngresos( $fecha );
        $ingresosOnlineFormaPago = $this->modeloVentas->getFlujoIngresosVentasOnline( $fecha );
        $ingresosPorSucursal = [];
        $ingresosTotales = [];
        foreach ($ingresosPorFormaPago as $i => $ingreso) {
            $ingresosPorFormaPago[$i]->REFERENCIA = mb_convert_encoding($ingreso->REFERENCIA, "UTF-8");
            $ingresosPorFormaPago[$i]->IMPORTE = round( $ingreso->IMPORTE , 2 );
            if( strpos( $ingreso->FORMAPAGO, "#04") !== false || strpos( $ingreso->FORMAPAGO,"#28") !== false  ){
                $ingresosTotales['tarjetas'] = isset( $ingresosTotales['tarjetas']) ? $ingresosTotales['tarjetas'] +$ingreso->IMPORTE : $ingreso->IMPORTE;
                if ( isset( $ingresosPorSucursal[$ingreso->DESCRIPCION]['tarjetas'] ) ) {
                    $ingresosPorSucursal[$ingreso->DESCRIPCION]['tarjetas'] += $ingreso->IMPORTE;
             
                } else {
                    $ingresosPorSucursal[$ingreso->DESCRIPCION]['tarjetas'] = $ingreso->IMPORTE;
    
                }
                 
            }else if( strpos( $ingreso->FORMAPAGO, "#02") !== false){
                $ingresosPorSucursal[$ingreso->DESCRIPCION]['cheque'] = $ingreso->IMPORTE;
                $ingresosTotales['cheque'] = isset( $ingresosTotales['cheque']) ? $ingresosTotales['cheque'] +$ingreso->IMPORTE : $ingreso->IMPORTE;
            }else if( strpos( $ingreso->FORMAPAGO, "#01") !== false ){
                $ingresosTotales['efectivo'] = isset( $ingresosTotales['efectivo']) ? $ingresosTotales['efectivo'] +$ingreso->IMPORTE : $ingreso->IMPORTE;
                if ( isset( $ingresosPorSucursal[$ingreso->DESCRIPCION]['efectivo'] ) ) {
                    $ingresosPorSucursal[$ingreso->DESCRIPCION]['efectivo'] += $ingreso->IMPORTE;
                } else {
                    $ingresosPorSucursal[$ingreso->DESCRIPCION]['efectivo'] = $ingreso->IMPORTE;
                }
            }else if( strpos( $ingreso->FORMAPAGO, "#03") !== false){
                $ingresosTotales['transferencias'] = isset( $ingresosTotales['transferencias']) ? $ingresosTotales['transferencias'] +$ingreso->IMPORTE : $ingreso->IMPORTE;
                $ingresosPorSucursal[$ingreso->DESCRIPCION]['transferencias'] = $ingreso->IMPORTE;
            }
        }

        //para ventas en Linea
        if ( sizeof( $ingresosOnlineFormaPago ) ) {
            foreach ( $ingresosOnlineFormaPago as $i => $ingreso) {
                $ingresosPorFormaPago[$i]->REFERENCIA = mb_convert_encoding($ingreso->REFERENCIA, "UTF-8");
                $ingresosPorFormaPago[$i]->IMPORTE = round( $ingreso->IMPORTE , 2 );
                if( strpos( $ingreso->FORMAPAGO, "#04") !== false || strpos( $ingreso->FORMAPAGO,"#28") !== false  ){
                    $ingresosTotales['tarjetas'] = isset( $ingresosTotales['tarjetas']) ? $ingresosTotales['tarjetas'] +$ingreso->IMPORTE : $ingreso->IMPORTE;
                    if ( isset( $ingresosPorSucursal["VENTAS ONLINE"]['tarjetas'] ) ) {
                        $ingresosPorSucursal["VENTAS ONLINE"]['tarjetas'] += $ingreso->IMPORTE;
                    } else {
                        $ingresosPorSucursal["VENTAS ONLINE"]['tarjetas'] = $ingreso->IMPORTE;
                    }
                     
                }else if( strpos( $ingreso->FORMAPAGO, "#02") !== false){
                    $ingresosTotales['cheque'] = isset( $ingresosTotales['cheque']) ? $ingresosTotales['cheque'] +$ingreso->IMPORTE : $ingreso->IMPORTE;
                    $ingresosPorSucursal["VENTAS ONLINE"]['cheque'] = $ingreso->IMPORTE;
                }else if( strpos( $ingreso->FORMAPAGO, "#01") !== false ){
                    $ingresosTotales['efectivo'] = isset( $ingresosTotales['efectivo']) ? $ingresosTotales['efectivo'] +$ingreso->IMPORTE : $ingreso->IMPORTE;
                    if ( isset( $ingresosPorSucursal["VENTAS ONLINE"]['efectivo'] ) ) {
                        $ingresosPorSucursal["VENTAS ONLINE"]['efectivo'] += $ingreso->IMPORTE;
                    } else {
                        $ingresosPorSucursal["VENTAS ONLINE"]['efectivo'] = $ingreso->IMPORTE;
                    }
                }else if( strpos( $ingreso->FORMAPAGO, "#03") !== false){
                    $ingresosTotales['transferencias'] = isset( $ingresosTotales['transferencias']) ? $ingresosTotales['transferencias'] +$ingreso->IMPORTE : $ingreso->IMPORTE;
                    $ingresosPorSucursal["VENTAS ONLINE"]['transferencias'] = $ingreso->IMPORTE;
                }            
            }
        }else{
            $ingresosPorSucursal["VENTAS ONLINE"]['cheque'] = 0;
            $ingresosPorSucursal["VENTAS ONLINE"]['transferencias'] = 0;
            $ingresosPorSucursal["VENTAS ONLINE"]['efectivo'] = 0;
            $ingresosPorSucursal["VENTAS ONLINE"]['tarjetas'] = 0;
        }


        //creando el arreglo procesado para que muestre el desgloce generl
        $ingresos =[];
        $totalGlobal =0;
        foreach ($ingresosPorSucursal as $i => $montos) {
            $total = 0;
            $total +=  isset( $montos['tarjetas'] ) ? $montos['tarjetas'] : 0;
            $total += isset( $montos['cheque']) ? $montos['cheque'] : 0;
            $total += isset( $montos['efectivo']) ? $montos['efectivo'] : 0;
            $total += isset( $montos['transferencias']) ? $montos['transferencias'] : 0;
            $totalGlobal += $total;
            array_push( $ingresos , [
                'sucursal' => $i,
                'totalValue' => $total,
                'total' => number_format($total, 2, '.', ','),
                'desgloce' => $montos
            ]);
        }

        $keys = array_column($ingresos, 'totalValue');

        array_multisort($keys, SORT_DESC, $ingresos);

        
        return [ round( $totalGlobal,2) , $ingresos, $ingresosTotales];
    }

    public function getUtilidadVenta( $mes )
    {

        $listaVentasUtilidad = $this->modeloVentas->getUtilidadVenta( $mes );

        
        $listResultUtilidaddes = [];
        $utilidadTotal = 0;
        foreach ($listaVentasUtilidad as $i => $utilidad) {

            $utilidadTotal += $utilidad->VENTA - ( $utilidad->COSTO *1.16  );
            $porcentajeGasto =  round( ($utilidad->COSTO * 1.16 ) / $utilidad->VENTA , 2)*100;
            array_push( $listResultUtilidaddes , ['venta' => round( $utilidad->VENTA,2) , 'porcentajeGasto' => $porcentajeGasto,'porcentajeUtilidad' => 100 - $porcentajeGasto  ,'costo' => round( ($utilidad->COSTO * 1.16 ), 2),'almacen' => $utilidad->ALMACEN, 'utilidad_bruta'=> round( $utilidad->VENTA - ( $utilidad->COSTO *1.16  ) ) ] );

        $keys = array_column($listResultUtilidaddes, 'utilidad_bruta');

        }

        array_multisort($keys, SORT_DESC, $listResultUtilidaddes); 
        return ['utilidadXsucursal' =>$listResultUtilidaddes, 'utilidadTotal' => round( $utilidadTotal,2) ];
    }

    public function formateaFecha($dato,$trans,$invertido){
        //Patron de fecha
            $patronFecha = "/^[[0-3][0-9]\/[0-1][0-9]\/[0-9][0-9][0-9][0-9]/" ;
            if( preg_match($patronFecha , $dato) ){
                if($trans=='d2g' || $trans=='d2d'){
                    $f1 = explode("/",$dato);
                    if($invertido==0){
                        if($trans=='d2g')
                            return $f1[0]."-".$f1[1]."-".$f1[2];
                        else
                            return $f1[0]."/".$f1[1]."/".$f1[2];
                    }else{
                        if($trans=='d2g')
                            return $f1[2]."-".$f1[1]."-".$f1[0];
                        else
                            return $f1[2]."/".$f1[1]."/".$f1[0];
                    }
                }
                if($trans=='g2d' || $trans=='g2g'){
                    $f1 = explode("/",$dato);
                    if($invertido==0){
                        if($trans=='g2g')
                            return $f1[0]."-".$f1[1]."-".$f1[2];
                        else
                            return $f1[0]."/".$f1[1]."/".$f1[2];
                    }else{
                        if($trans=='g2g')
                            return $f1[2]."-".$f1[1]."-".$f1[0];
                        else
                            return $f1[2]."/".$f1[1]."/".$f1[0];
                    }
                }
                if($trans=='d2f'){
                    $f1 = explode("/",$dato);
                    return $f1[1]."/".$f1[0]."/".$f1[2];
                }
                    }
        else{
            exit(0);
        }	
    }

    public function getEdoRes($mes){      
        $edosfinancieros = new EdoFinancieros();
        $arregloCuentas = $edosfinancieros->getCuentas();
        $mesAnioSplit = explode("-", $mes );
        $ianio = $mesAnioSplit[0];
        $imes = $mesAnioSplit[1];
        if($imes<10){
            $imes = $imes * 1;
            $imes = '0'.$imes;
        }
        $numDiasMes = cal_days_in_month(CAL_GREGORIAN, $mesAnioSplit[1], $mesAnioSplit[0]);
        $fechaI = '01/'.$imes.'/'.$ianio;
        $fechaF = $numDiasMes.'/'.$imes.'/'.$ianio;
        $fecini = $this->formateaFecha($fechaI,'d2g',1);
        $fecfin = $this->formateaFecha($fechaF,'d2g',1);
        $fecinif = $this->formateaFecha($fechaI,'d2f',0);
        $fecfinf = $this->formateaFecha($fechaF,'d2f',0);
        $arrGlobal = array();
        $arrSucursales = array();
        $sucursalesVenta = $edosfinancieros->getSucursalesConVenta();
        //return $sucursalesVenta;
        foreach($sucursalesVenta as $suc){
            if(!in_array($suc['id'],$arrSucursales)){
                $arrSucursales[] = $suc['id'];
                $arrGlobal[$suc['id']] = array('almacen'=>$suc['descripcion'],'ventas'=>0,'costo'=>0,'porccosto'=>0,'utilidadbruta'=>0,'porcutilidadbruta'=>0,'gastosop'=>0,'porcgastosop'=>0,'utilidadneta'=>0,'porcutilidadneta'=>0);
            }
            
			$totalesventas = $edosfinancieros->getTotalVentas($suc['id'],$fecinif,$fecfinf);
            //return 'DATOS: '.$totalesventas['ventas'];
            // $sucursalFiliacion = $saldosBancarios->getFiliaciones( $udn );
            // $totalEgresoBancos = 0;

            // if ($udn != '%') {
            //     foreach ($sucursalFiliacion as $i => $sucursal) {
            //         $egresosBancarios = $saldosBancarios->getEgresoPorFilicacion($sucursal['filiacion'], $fecini, $fecfin);
            //         $totalEgresoBancos += $egresosBancarios[0]['egresos'];
            //     }				
            // }else{
            //         $egresosBancarios = $saldosBancarios->getEgresoPorFilicacion('%', $fecini, $fecfin);
            //         $totalEgresoBancos += $egresosBancarios[0]['egresos'];				
            // }

            $arrayCtas = array();
            $arrIN = array();
            $totventas = $totalesventas['ventas'];
            $totcostoventas = $totalesventas['costos'];

            $arrGlobal[$suc['id']]['ventas'] = number_format($totventas,0,'.','');
            $arrGlobal[$suc['id']]['costo'] = number_format($totcostoventas,0,'.','');

            $echo = "";
            $arrArbol = array();
            $arrCuentas = array();
            foreach($arregloCuentas as $cuenta){
                $xta = explode("-",$cuenta['cuenta']);
                if((int)$xta[1]==0 && (int)$xta[2]==0)
                    $arrArbol[$xta[0].'-00-000'] = array();
                
                if((int)$xta[1]>0 && (int)$xta[2]==0)
                    $arrArbol[$xta[0].'-00-000'][$xta[0].'-'.$xta[1].'-000'] = array();
                
                if((int)$xta[1]>0 && (int)$xta[2]>0){
                    $arrArbol[$xta[0].'-00-000'][$xta[0].'-'.$xta[1].'-000'][$cuenta['cuenta']]['naturaleza'] = $cuenta['naturaleza'];
                    $arrArbol[$xta[0].'-00-000'][$xta[0].'-'.$xta[1].'-000'][$cuenta['cuenta']]['nombre'] = $cuenta['nombre'];
                    $arrArbol[$xta[0].'-00-000'][$xta[0].'-'.$xta[1].'-000'][$cuenta['cuenta']]['subtotal'] = $edosfinancieros->getMovimientosCuentaTotal($suc['id'],$fecini,$fecfin,$cuenta['cuenta']);
                    if($arrArbol[$xta[0].'-00-000'][$xta[0].'-'.$xta[1].'-000'][$cuenta['cuenta']]['subtotal']>0){
                        if(!in_array($xta[0].'-'.$xta[1].'-000',$arrIN)){
                            $arrayCtas[$cuenta['cuenta']] = $arrArbol[$xta[0].'-00-000'][$xta[0].'-'.$xta[1].'-000'][$cuenta['cuenta']]['subtotal'];
                            $arrIN[] = $cuenta['cuenta'];
                        }else{
                            $arrayCtas[$cuenta['cuenta']]+= $arrArbol[$xta[0].'-00-000'][$xta[0].'-'.$xta[1].'-000'][$cuenta['cuenta']]['subtotal'];
                        }
                    }
                }
            }
            $utilidadbruta = $totventas - $totcostoventas;
            
            $arrGlobal[$suc['id']]['utilidadbruta'] = number_format($utilidadbruta,0,'.','');

            if($totventas>0){
                $prcutilb = ($utilidadbruta*100) / $totventas;
                $prcctob = ($totcostoventas*100) / $totventas;
            }else{
                $prcutilb = 0;
                $prcctob = 0;
            }
            
            $arrGlobal[$suc['id']]['porccosto'] = number_format( $prcctob,0,'.','');
            $arrGlobal[$suc['id']]['porcutilidadbruta'] = number_format( $prcutilb,0,'.','');

            //CABECERA DE COMPRAS
            $importeTotalCompra = 0;
            foreach ($totalCompras as $compra) {
                $importeTotalCompra +=  $compra['IMPORTETOTAL'];
            }


            //CABECERA GASTOS DE OPERACION
            $totcta = 0;
            foreach($arrArbol as $idx => $val){
                foreach($val as $idx1 => $val1){
                    $totsubcta = 0;
                    if($idx1 == '601-01-000'){
                        foreach($val1 as $idx2=> $val2){
                            $importe = $edosfinancieros->getMovimientosCuentaTotal($suc['id'],$fecini,$fecfin,$idx2);

                            if($val2['naturaleza'] == 1)
                                $totsubcta = $totsubcta + $importe;
                            else
                                $totsubcta = $totsubcta - $importe;
                        }
                    }

                    $totcta = $totcta + $totsubcta;		
                }
            }
            $utilidadoperacion = $utilidadbruta - abs($totcta);
            
            $porcOperativo = number_format(($totcta/$utilidadbruta)*100,0,'.','');
            
            $arrGlobal[$suc['id']]['gastosop'] = number_format($totcta,0,'.','');
            $arrGlobal[$suc['id']]['porcgastosop'] = $porcOperativo;

            $totcta = 0;
            foreach($arrArbol as $idx => $val){
                
                foreach($val as $idx1 => $val1){
                    $totsubcta = 0;
                    //if($idx1 == '601-01-000'){
                        foreach($val1 as $idx2=> $val2){
                            $importe = $edosfinancieros->getMovimientosCuentaTotal($suc['id'],$fecini,$fecfin,$idx2);
                            if($val2['naturaleza'] == 1)
                                $totsubcta = $totsubcta + $importe;
                            else
                                $totsubcta = $totsubcta - $importe;
                        }
                    //}
                    $totcta = $totcta + $totsubcta;
                }
            }

            //CABECERA GASTOS FINANCIEROS
            $gastosFinancieros = 0;
            foreach($arrArbol as $idx => $val){
                
                foreach($val as $idx1 => $val1){
                    $totsubcta = 0;
                    if($idx1 == '601-03-000'){
                        foreach($val1 as $idx2=> $val2){
                            $importe = $edosfinancieros->getMovimientosCuentaTotal($suc['id'],$fecini,$fecfin,$idx2);
                            if($val2['naturaleza'] == 1)
                                $totsubcta = $totsubcta + $importe;
                            else
                                $totsubcta = $totsubcta - $importe;
                        }
                    }
                    $gastosFinancieros = $gastosFinancieros + $totsubcta;
                }
            }
            
            $porcFinancieros = number_format(($gastosFinancieros/$utilidadbruta)*100,0,'.','');
            //CABECERA UTILIDAD O PERDIDA
            //$utilidad = $utilidadbruta - abs($gastosFinancieros)- abs( $gastosOperativos );
            $utilidad = $utilidadbruta - abs( $arrGlobal[$suc['id']]['gastosop'] );
            //var_dump( $utilidadoperacion );
            if($utilidad<0){
                $txtcolor = 'red';
                $txtutilperd = 'P&Eacute;RDIDA';
            }else{
                $txtcolor = 'green';
                $txtutilperd = 'UTILIDAD';
            }

            $porcUtilidad = number_format(($utilidad/$utilidadbruta)*100,0,'.','');
            
            $arrGlobal[$suc['id']]['utilidadneta'] = number_format($utilidad,0,'.','');
            $arrGlobal[$suc['id']]['porcutilidadneta'] = $porcUtilidad;
        } //Termina foreach de sucursales


        $arrFinal = array('utilidadXsucursal'=>array(),'ventasTotal'=>0,'costoTotal'=>0,'porcCostoTotal'=>0,'utilidadbrutaTotal'=>0,'porcUtilidadbrutaTotal'=>0,'gastosTotal'=>0,'porcGastosTotal'=>0,'utilidadTotal'=>0,'porcUtilidadTotal'=>0);
        $VentasTotal = 0;
        $CostoTotal = 0;
        $UtilidadBrutaTotal = 0;
        $GastosTotal = 0;
        $UtilidadTotal = 0;
        foreach($arrGlobal as $item){
            $arrFinal['utilidadXsucursal'][] = $item;
            $VentasTotal = $VentasTotal + $item['ventas'];
            $CostoTotal = $CostoTotal + $item['costo'];
            $UtilidadBrutaTotal = $UtilidadBrutaTotal + $item['utilidadbruta'];
            $GastosTotal = $GastosTotal + $item['gastosop'];
            $UtilidadTotal = $UtilidadTotal + $item['utilidadneta'];
        }

        $porcCostoTotal = number_format(($CostoTotal/$VentasTotal)*100,0,'.','');
        $porcUtilidadBrutaTotal = number_format(($UtilidadBrutaTotal/$VentasTotal)*100,0,'.','');
        $porcGastosTotal = number_format(($GastosTotal/$UtilidadBrutaTotal)*100,0,'.','');
        $porcUtilidadTotal = number_format(($UtilidadTotal/$UtilidadBrutaTotal)*100,0,'.','');
        $arrFinal['ventasTotal']= number_format($VentasTotal,0,'.','');
        $arrFinal['costoTotal']= number_format($CostoTotal,0,'.','');
        $arrFinal['porcCostoTotal']= $porcCostoTotal;
        $arrFinal['utilidadbrutaTotal']= number_format($UtilidadBrutaTotal,0,'.','');
        $arrFinal['porcUtilidadbrutaTotal']= $porcUtilidadBrutaTotal;
        $arrFinal['gastosTotal']= number_format($GastosTotal,0,'.','');
        $arrFinal['porcGastosTotal']= $porcGastosTotal;
        $arrFinal['utilidadTotal']= number_format($UtilidadTotal,0,'.','');
        $arrFinal['porcUtilidadTotal']= $porcUtilidadTotal;
        return $arrFinal;
    }

    public function buscaIndex ( $arrList , $key , $value)
    {
        foreach ($arrList as $i => $item) {
            if ( $item[$key] == $value) {
                return $i;
            }
        }

        return -1;
    }
    public function joinVentasCobrosMeta( $ventas , $ingresosCobranza, $online = false)
    {

        $ventasAcumuladas = [];
        $idsAlmacenes = [];
        //Obteniendo las metas de las sucursales
        $listaMetas = $this->modeloVentas->getMetasSucursales();
        foreach ($listaMetas as $i => $meta) {
            array_push( $idsAlmacenes , trim($meta['idAlmacen']) );
        }

        
            $importeOnline = 0;
            $idxMeta = -1;
            foreach ($ventas as $j => $ventasSucursal) {

                if (in_array( $ventasSucursal->FK1MCFG_ALMACENES  , $idsAlmacenes ) && !$online) {
                    
                    $idxMeta = $this->buscaIndex( $listaMetas, 'idAlmacen',$ventasSucursal->FK1MCFG_ALMACENES);
                    array_push( $ventasAcumuladas ,[
                        'id' =>   !$online ?  $ventasSucursal->FK1MCFG_ALMACENES : 10791,
                        'almacen' =>  !$online ? $ventasSucursal->DESCRIPCION : "ONLINE",
                        'importe' => $ventas [$j]->IMPORTE ,
                        'meta' => $idxMeta >= 0 ? $listaMetas[$idxMeta]['cantidad'] : -1
                    ] );
                    unset( $idsAlmacenes[ array_search( $ventasSucursal->FK1MCFG_ALMACENES ,$idsAlmacenes) ]);
                }else{
                    $importeOnline += $ventas [$j]->IMPORTE ;
                }

            }      
            
            if ( $online) {
                $idxMeta = $this->buscaIndex( $listaMetas, 'idAlmacen',10791);
                $ventasAcumuladas = [
                    'id' => 10791,
                    'almacen' => "ONLINE",
                    'importe' => $importeOnline,
                    'meta' => $idxMeta >= 0 ? $listaMetas[$idxMeta]['cantidad'] : -1
                ];
            }      
            foreach ($ingresosCobranza as $i => $cobranza) {
                if (!$online) {
                    $idxSucursal = $this->buscaIndex( $ventasAcumuladas, "id", $cobranza->FK1MCFG_ALMACENES);
                    if ( $idxSucursal >= 0 && !$online) {
                        // echo "ddp";
                        $ventasAcumuladas[ $idxSucursal ]['importe'] += $cobranza->IMPORTE;
                       unset( $ingresosCobranza[$i]);
                    }
                }else{
                    $ventasAcumuladas['importe'] += $cobranza->IMPORTE;
                }
                
            }
            

        return $ventasAcumuladas;
    }

    public function getMetasSucursales( $mes, $anio)
    {
        $ventasFisicas = $this->modeloVentas->getTotalVentasSucursal( $mes, $anio);
        $cobranzaVendedoresLocales = $this->modeloVentas->getIngresosCXC( $mes, $anio);

        //agregando los montos de cobranza 
        $ventasFisicas = $this->joinVentasCobrosMeta( $ventasFisicas , $cobranzaVendedoresLocales );


        $ventasOnline = $this->modeloVentas->getTotalVentasSucursal( $mes, $anio, true);
        $cobranzaOnline = $this->modeloVentas->getIngresosCXC( $mes, $anio, true);

        $ventasOnline =  $this->joinVentasCobrosMeta( $ventasOnline , $cobranzaOnline, true );

        array_push( $ventasFisicas , $ventasOnline);


        //agregando la proyeccion de ventas en el mes
        foreach ($ventasFisicas as $i => $sucursal) {
            $diaActual = $mes== date('m') && date('Y') == $anio ? date('d') : cal_days_in_month( CAL_GREGORIAN, $mes, $anio);
            $ventasFisicas[$i]['proyeccion'] =round( ($sucursal['importe'] /date('d')) * cal_days_in_month( CAL_GREGORIAN, $mes, $anio) , 2);
            $porcentaje = round( $ventasFisicas[$i]['importe'] /  $ventasFisicas[$i]['meta'] , 2 );
            // echo  $ventasFisicas[$i]['importe'] ."/.".  $ventasFisicas[$i]['proyeccion']."<br>";
            $ventasFisicas[$i]['progreso'] = $porcentaje;
            $ventasFisicas[$i]['progresoProyectado'] = $porcentaje = round( $ventasFisicas[$i]['proyeccion'] /  $ventasFisicas[$i]['meta'] , 2 );
        }
        return $ventasFisicas;
    }

    public function getClientesActivos($nombre){
        $arr = array();
        $result = $this->modeloVentas->getClientesActivos( $nombre );
        $n=0;
        foreach($result as $rw){
            $arr[$n]['ID'] = $rw->ID;
            $arr[$n]['NOMBRE'] = utf8_encode($rw->NOMBRE);
            $n++;
        }
        return $arr;
    }

    public function getFormasPagos(){
        return $this->modeloVentas->getFormasPagos();
    }

    public function getUsoCFDI(){
        return $this->modeloVentas->getUsoCFDI();
    }

    public function getTicketsFiltro($cliente, $almacen, $fechainicial, $fechafinal, $formapago){
        return $this->modeloVentas->getTicketsFiltro( $cliente, $almacen, $fechainicial, $fechafinal, $formapago );
    }

    public function getAnticiposLiq($id){
        return $this->modeloVentas->getAnticiposLiq( $id );
    }

}
