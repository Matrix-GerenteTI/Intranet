<?php

require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/Ventas/vendedores.php";

ini_set('precision', 10);
ini_set('serialize_precision', 10);

class VendedoresController  
{
    protected $modeloVendedor;

    public function __construct()
    {
        $this->modeloVendedor = new Vendedores;
    }
    public function getAll()
    {
        $listaVendedores = $this->modeloVendedor->getAll();
        $vendedores = [];
        foreach ($listaVendedores as $i => $vendedor) {
            //Quitando lo que está antes de los :: 
            $correctNombre = explode("::", $vendedor->NOMBREVENDEDOR );
            array_push( $vendedores , ['name' => utf8_encode( isset( $correctNombre[1] ) ? $correctNombre[1]  : $correctNombre[0] ) , 'id' => $vendedor->ID  ] );
          
        }

        return $vendedores;
    } 

    public function getMetasVendedores( $mes, $anio)
    {
        $listaVentasVendedores = $this->modeloVendedor->getVentasMetasVendedor($mes , $anio );
        $arrVolumen = array();
        foreach ($listaVentasVendedores as $i0 => $venta0) {
            if (!isset( $arrVolumen[$venta0->CODIGOVENDEDOR ] ) ) { 
                $arrVolumen[$venta0->CODIGOVENDEDOR]['vendedor'] = $venta0->VENDEDOR;
                $arrVolumen[$venta0->CODIGOVENDEDOR]['importenoservicio'] = $venta0->TOTALNOSERVICIO;
                $arrVolumen[$venta0->CODIGOVENDEDOR]['importealineaciones'] = $venta0->TOTALALINEACION;
                $arrVolumen[$venta0->CODIGOVENDEDOR]['metaimporte'] = $venta0->METAIMPORTE;
                $arrVolumen[$venta0->CODIGOVENDEDOR]['metaalineaciones'] = $venta0->METAALINEACIONES;
                $arrVolumen[$venta0->CODIGOVENDEDOR]['bonoimporte'] = $venta0->BONOIMPORTE;
                $arrVolumen[$venta0->CODIGOVENDEDOR]['bonoalineaciones'] = $venta0->BONOALINEACIONES;
                $arrVolumen[$venta0->CODIGOVENDEDOR]['total'] = $venta0->TOTALNOSERVICIO + $venta0->TOTALALINEACION;
            }else{
                $arrVolumen[$venta0->CODIGOVENDEDOR]['importenoservicio']+= $venta0->TOTALNOSERVICIO;
                $arrVolumen[$venta0->CODIGOVENDEDOR]['importealineaciones']+= $venta0->TOTALALINEACION;
                $arrVolumen[$venta0->CODIGOVENDEDOR]['total']+= $venta0->TOTALNOSERVICIO + $venta0->TOTALALINEACION;
            }
        }
        //agregando la proyeccion de ventas en el mes
        foreach ($arrVolumen as $i => $vendedor) {
            $diaActual = $mes== date('m') && date('Y') == $anio ? date('d') : cal_days_in_month( CAL_GREGORIAN, $mes, $anio);
            $arrVolumen[$i]['proyeccionimporte'] =round( ($vendedor['importenoservicio'] /date('d')) * cal_days_in_month( CAL_GREGORIAN, $mes, $anio) , 2);
            $arrVolumen[$i]['proyeccionalineaciones'] =round( ($vendedor['importealineaciones'] /date('d')) * cal_days_in_month( CAL_GREGORIAN, $mes, $anio) , 2);
            if($arrVolumen[$i]['metaimporte']>0){
                $porcentajeimporte = round( $arrVolumen[$i]['importenoservicio'] /  $arrVolumen[$i]['metaimporte'] , 2 );
                $arrVolumen[$i]['progresoimporte'] = $porcentajeimporte;
                $arrVolumen[$i]['progresoProyectadoImporte'] = round( $arrVolumen[$i]['proyeccionimporte'] /  $arrVolumen[$i]['metaimporte'] , 2 );
            }else{
                $arrVolumen[$i]['progresoimporte'] = 0;
                $arrVolumen[$i]['progresoProyectadoImporte'] = 0;
            }
            if($arrVolumen[$i]['metaalineaciones']>0){
                $porcentajealineaciones = round( $arrVolumen[$i]['importealineaciones'] /  $arrVolumen[$i]['metaalineaciones'] , 2 );
                $arrVolumen[$i]['progresoalineaciones'] = $porcentajealineaciones;
                $arrVolumen[$i]['progresoProyectadoAlineaciones'] = round( $arrVolumen[$i]['proyeccionalineaciones'] /  $arrVolumen[$i]['metaalineaciones'] , 2 );
            }else{
                $arrVolumen[$i]['progresoalineaciones'] = 0;
                $arrVolumen[$i]['progresoProyectadoAlineaciones'] = 0;
            }
        }
        return $arrVolumen;
    }

    public function getMetasVendedoresTabla( $mes, $anio)
    {
        $listaVentasVendedores = $this->modeloVendedor->getVentasPorVendedorFam(0, $mes , $anio );
        $metas = $this->modeloVendedor->getMetaVentasVendedor();
        //CREAMOS EL ARREGLO DE METAS POR VENDEDOR
        $arrMetas = array();
        foreach($metas as $rmeta){
            $arrMetas[$rmeta->IDVENDEDOR][$rmeta->FAMILIA]['VDIARIO'] = $rmeta->VDIARIO>0?$rmeta->VDIARIO:'NA';
            $arrMetas[$rmeta->IDVENDEDOR][$rmeta->FAMILIA]['VMENSUAL'] = $rmeta->VMENSUAL>0?$rmeta->VMENSUAL:'NA';
            $arrMetas[$rmeta->IDVENDEDOR][$rmeta->FAMILIA]['IDIARIO'] = $rmeta->IDIARIO>0?$rmeta->IDIARIO:'NA';
            $arrMetas[$rmeta->IDVENDEDOR][$rmeta->FAMILIA]['IMENSUAL'] = $rmeta->IMENSUAL>0?$rmeta->IMENSUAL:'NA';
        }


        $listaComisiones = [];
        $metaVendedor = 500000;
        $arrVolumen = array();
        $arrVtasVend = array();
        $arrFamilias = array('LLANTA','RIN','ACCESORIO','SERVICIO');

        foreach($listaVentasVendedores as $valV){
            if(in_array($valV->FAMILIA,$arrFamilias)){
                if ( isset( $arrVtasVend[$valV->IDVENDEDOR] ) ) { 
                    if(isset( $arrVtasVend[$valV->IDVENDEDOR][$valV->FAMILIA])){  
                        if($valV->FAMILIA == 'SERVICIO'){                     
                            if($valV->SUBFAMILIA == 'ALINEACION')
                                $arrVtasVend[$valV->IDVENDEDOR][$valV->FAMILIA]['CANTIDAD']+= $valV->CANTIDAD;
                        }else
                            $arrVtasVend[$valV->IDVENDEDOR][$valV->FAMILIA]['CANTIDAD']+= $valV->TOTAL;                    
                    }else{
                        if($valV->FAMILIA == 'SERVICIO'){     
                            if($valV->SUBFAMILIA == 'ALINEACION')
                                $arrVtasVend[$valV->IDVENDEDOR][$valV->FAMILIA]['CANTIDAD'] = $valV->CANTIDAD;
                        }else
                            $arrVtasVend[$valV->IDVENDEDOR][$valV->FAMILIA]['CANTIDAD'] = $valV->TOTAL;
                    }
                }else{                    
                    $arrVtasVend[$valV->IDVENDEDOR]['VENDEDOR'] = utf8_encode($valV->VENDEDOR);
                    if($valV->FAMILIA == 'SERVICIO'){     
                        if($valV->SUBFAMILIA == 'ALINEACION')
                            $arrVtasVend[$valV->IDVENDEDOR][$valV->FAMILIA]['CANTIDAD'] = $valV->CANTIDAD;
                    }else
                        $arrVtasVend[$valV->IDVENDEDOR][$valV->FAMILIA]['CANTIDAD'] = $valV->TOTAL;
                }  
            } 
        }

        foreach($arrVtasVend as $idxvend => $valvend){
            foreach($arrFamilias as $fam){
                if(!isset($arrVtasVend[$idxvend][$fam])){
                    $arrVtasVend[$idxvend][$fam]['CANTIDAD'] = 0;
                }
            }
        }

        

        //Generamos el arreglo para los ROWS y solo para el mes seleccionado
        if($mes>0){
            $mesSelected = $mes;
        }else{
            $mesSelected = date('m')*1;            
        }
        //Creamos la cabecera
        $arrHeader = array('');
        $arrFooter = array();
        $arrWidth = array(130,130,130);        
        $arrHeaderWidth = array(130,130,130);  
        $arrFooterWidth = array(390); 

        foreach($arrVtasVend as $idxVendedor => $familiaData){  
            $arrVtasVend[$idxVendedor]['headerWidth'] = array(240,120,120);
            $arrVtasVend[$idxVendedor]['footerWidth'] = array(360,120);
            $arrVtasVend[$idxVendedor]['header'] = array('FAMILIA','VENTA','COMISION');
            $totalFooter = 0;
            foreach($arrVtasVend[$idxVendedor] as $idxFamilia => $cantidad){ 
                if($idxFamilia == 'RIN'){             
                    $arrTMP = array();
                    $arrTMP[] = $idxFamilia.'ES (1%)';                        
                    $arrTMP[] = '$'.number_format($cantidad['CANTIDAD'],2,'.',',');           
                    $arrTMP[] = '$'.number_format(($cantidad['CANTIDAD'] * 0.01),2,'.',',');
                    $totalFooter = $totalFooter + ($cantidad['CANTIDAD'] * 0.01);
                    $arrVtasVend[$idxVendedor]['rows'][] = $arrTMP;
                }
                if($idxFamilia == 'LLANTA'){             
                    $arrTMP = array();
                    $arrTMP[] = $idxFamilia.'S (1%)';                        
                    $arrTMP[] = '$'.number_format($cantidad['CANTIDAD'],2,'.',',');           
                    $arrTMP[] = '$'.number_format(($cantidad['CANTIDAD'] * 0.01),2,'.',',');
                    $totalFooter = $totalFooter + ($cantidad['CANTIDAD'] * 0.01);
                    $arrVtasVend[$idxVendedor]['rows'][] = $arrTMP;
                }
                if($idxFamilia == 'SERVICIO'){             
                    $arrTMP = array();
                    $arrTMP[] = 'ALINEACIONES (META '.$arrMetas[$idxVendedor][$idxFamilia]['VMENSUAL'].')';                
                    $arrTMP[] = $cantidad['CANTIDAD'];
                    if($cantidad['CANTIDAD'] >= $arrMetas[$idxVendedor][$idxFamilia]['VMENSUAL'] && $arrMetas[$idxVendedor][$idxFamilia]['VMENSUAL']>1)  {                 
                        $arrTMP[] = '$'.number_format(1000,2,'.',',');
                        $totalFooter = $totalFooter + 1000;
                    }else{
                        $arrTMP[] = '$'.number_format(0,2,'.',',');
                    }
                    $arrVtasVend[$idxVendedor]['rows'][] = $arrTMP;
                }                
                if($idxFamilia == 'ACCESORIO'){             
                    $arrTMP = array();
                    $arrTMP[] = $idxFamilia.'S (META $'.number_format($arrMetas[$idxVendedor][$idxFamilia]['IMENSUAL'],2,'.',',').')';                
                    $arrTMP[] = '$'.number_format($cantidad['CANTIDAD'],2,'.',',');
                    if($cantidad['CANTIDAD'] >= $arrMetas[$idxVendedor][$idxFamilia]['IMENSUAL'] && $arrMetas[$idxVendedor][$idxFamilia]['IMENSUAL']>1)  {                 
                        $arrTMP[] = '$'.number_format(4000,2,'.',',');
                        $totalFooter = $totalFooter + 4000;
                    }else{
                        $arrTMP[] = '$'.number_format(0,2,'.',',');
                    }
                    $arrVtasVend[$idxVendedor]['rows'][] = $arrTMP;
                }
            }          
            rsort($arrVtasVend[$idxVendedor]['rows']); 
            $arrVtasVend[$idxVendedor]['footer'] = array('TOTAL COMISION','$ '.number_format($totalFooter,2,'.',','));
        }

        $vendedores = [];
        foreach ($arrVtasVend as $i => $vendedor) {
            array_push( $vendedores , $vendedor );
        }               

        usort($vendedores, [$this,'VENDEDOR',]); 
        
        return $vendedores;
    }

    public function getComisiones($dia, $mes , $anio)
    {
        $listaVentasVendedores = $this->modeloVendedor->getVentasPorVendedor($dia, $mes , $anio );
        $listaComisiones = [];
        $metaVendedor = 500000;
        $arrVolumen = array();
        foreach ($listaVentasVendedores as $i0 => $venta0) {
            if ( isset( $arrVolumen[$venta0->ID ] ) ) { 
                if(isset( $arrVolumen[$venta0->ID ][$venta0->FAMILIA]))
                    $arrVolumen[$venta0->ID][$venta0->FAMILIA]+= $venta0->CANTIDAD;
                else
                    $arrVolumen[$venta0->ID][$venta0->FAMILIA] = $venta0->CANTIDAD;
            }else{
                $arrVolumen[$venta0->ID][$venta0->FAMILIA] = $venta0->CANTIDAD;
            }
        }

        $arrVolumenF = array();
        foreach($arrVolumen as $idx0 => $rw0){
            foreach($rw0 as $idxFam => $valFam){
                $arrVolumenF[$idx0]['labels'][] = substr($idxFam,0,4);
                $arrVolumenF[$idx0]['datas'][] = $valFam;
            }
        }

        foreach ($listaVentasVendedores as $i => $venta) {

            //se verififica que las ventas son sin firma
            $precioVenta = number_format( $venta->PRECIO, 2,".","");
            $precioMinimo = floatval( filter_var( $venta->PVP3, FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION) );
            
            $comisionConFirma = 0;
            $comisionSinFirma = 0;
            $hasValvula = true;

            if ( $precioVenta >= $precioMinimo) {
                $comisionSinFirma = $venta->TOTAL * 0.01;
            } else {
                    //validando que no use firmas
                    if($venta->SUBFAMILIA !='VALVULA'){
                        $hasValvula = false;
                    }
            }
            


            if ( isset( $listaComisiones[$venta->ID ] ) ) { 
                $progreso = round( $listaComisiones[ $venta->ID]['vendido'] /$metaVendedor,2);
                $listaComisiones[ $venta->ID]['vendido'] += round($venta->TOTAL,2);
                $listaComisiones[ $venta->ID]['comisionReal'] += round( floatval($comisionSinFirma),2) ;
                $listaComisiones[ $venta->ID ]['comisionConFirma'] += round( ($venta->TOTAL * 0.01)  / 1, 2);
                $listaComisiones[ $venta->ID ]['nFirmas'] += !$hasValvula ?  1 : 0;
                $listaComisiones[ $venta->ID ]['progreso'] = $progreso > 1 ? 1 : $progreso;              
            } else {
                $progreso = round( $listaComisiones[ $venta->ID]['vendido'] /$metaVendedor,2);
                $listaComisiones[ $venta->ID]['vendedor'] = mb_convert_encoding( $venta->VENDEDOR , "UTF-8");
                $listaComisiones[ $venta->ID]['vendido'] = round($venta->TOTAL,2);
                $listaComisiones[  $venta->ID]['comisionReal'] =  round($comisionSinFirma,2);
                $listaComisiones[ $venta->ID ]['nFirmas'] = !$hasValvula ?  1 : 0;
                $listaComisiones[ $venta->ID ]['comisionConFirma'] =  round( ($venta->TOTAL * 0.01)  / 1, 2);
                $listaComisiones[ $venta->ID ]['progreso'] = $progreso > 1 ? 1 : $progreso;
                $listaComisiones[ $venta->ID ]['labels'] = $arrVolumenF[$venta->ID]['labels'];
                $listaComisiones[ $venta->ID ]['datas'] = $arrVolumenF[$venta->ID]['datas'];
            }
            
        }

        $vendedores = [];
        foreach ($listaComisiones as $i => $vendedor) {
            array_push( $vendedores , $vendedor );
        }
        
        

        usort($vendedores, [$this,'cmp']); 
        
        return $vendedores;
    }

    public function getComisionesFam($dia, $mes , $anio)
    {
        $listaVentasVendedores = $this->modeloVendedor->getVentasPorVendedorFam($dia, $mes , $anio );
        $metas = $this->modeloVendedor->getMetaVentasVendedor();
        //CREAMOS EL ARREGLO DE METAS POR VENDEDOR
        $arrMetas = array();
        foreach($metas as $rmeta){
            $arrMetas[$rmeta->IDVENDEDOR][$rmeta->FAMILIA]['VDIARIO'] = $rmeta->VDIARIO>0?$rmeta->VDIARIO:'NA';
            $arrMetas[$rmeta->IDVENDEDOR][$rmeta->FAMILIA]['VMENSUAL'] = $rmeta->VMENSUAL>0?$rmeta->VMENSUAL:'NA';
            $arrMetas[$rmeta->IDVENDEDOR][$rmeta->FAMILIA]['IDIARIO'] = $rmeta->IDIARIO>0?$rmeta->IDIARIO:'NA';
            $arrMetas[$rmeta->IDVENDEDOR][$rmeta->FAMILIA]['IMENSUAL'] = $rmeta->IMENSUAL>0?$rmeta->IMENSUAL:'NA';
        }


        $listaComisiones = [];
        $metaVendedor = 500000;
        $arrVolumen = array();
        $arrVtasVend = array();
        $arrFamilias = array('LLANTA','RIN','ACCESORIO','SERVICIO');

        foreach($listaVentasVendedores as $valV){
            if(in_array($valV->FAMILIA,$arrFamilias)){
                if ( isset( $arrVtasVend[$valV->IDVENDEDOR] ) ) { 
                    if(isset( $arrVtasVend[$valV->IDVENDEDOR][$valV->FAMILIA])){
                        if(isset( $arrVtasVend[$valV->IDVENDEDOR][$valV->ANIO])){
                            if(isset( $arrVtasVend[$valV->IDVENDEDOR][$valV->ANIO][$valV->MES])){
                                if(isset( $arrVtasVend[$valV->IDVENDEDOR][$valV->ANIO][$valV->MES][$valV->DIA])){
                                    if($valV->FAMILIA!='ACCESORIO')
                                        $arrVtasVend[$valV->IDVENDEDOR][$valV->FAMILIA][$valV->ANIO][$valV->MES][$valV->DIA]+= $valV->CANTIDAD;
                                    else
                                        $arrVtasVend[$valV->IDVENDEDOR][$valV->FAMILIA][$valV->ANIO][$valV->MES][$valV->DIA]+= $valV->TOTAL;
                                }else{
                                    if($valV->FAMILIA!='ACCESORIO')
                                        $arrVtasVend[$valV->IDVENDEDOR][$valV->FAMILIA][$valV->ANIO][$valV->MES][$valV->DIA] = $valV->CANTIDAD;
                                    else
                                        $arrVtasVend[$valV->IDVENDEDOR][$valV->FAMILIA][$valV->ANIO][$valV->MES][$valV->DIA] = $valV->TOTAL;
                                }
                            }else{
                                if($valV->FAMILIA!='ACCESORIO')
                                    $arrVtasVend[$valV->IDVENDEDOR][$valV->FAMILIA][$valV->ANIO][$valV->MES][$valV->DIA] = $valV->CANTIDAD;
                                else
                                    $arrVtasVend[$valV->IDVENDEDOR][$valV->FAMILIA][$valV->ANIO][$valV->MES][$valV->DIA] = $valV->TOTAL;
                            }
                        }else{
                            if($valV->FAMILIA!='ACCESORIO')
                                $arrVtasVend[$valV->IDVENDEDOR][$valV->FAMILIA][$valV->ANIO][$valV->MES][$valV->DIA] = $valV->CANTIDAD;
                            else
                                $arrVtasVend[$valV->IDVENDEDOR][$valV->FAMILIA][$valV->ANIO][$valV->MES][$valV->DIA] = $valV->TOTAL;
                        }
                    }else{
                        if($valV->FAMILIA!='ACCESORIO')
                            $arrVtasVend[$valV->IDVENDEDOR][$valV->FAMILIA][$valV->ANIO][$valV->MES][$valV->DIA] = $valV->CANTIDAD;
                        else
                            $arrVtasVend[$valV->IDVENDEDOR][$valV->FAMILIA][$valV->ANIO][$valV->MES][$valV->DIA] = $valV->TOTAL;
                    }
                }else{
                    if($valV->FAMILIA!='ACCESORIO')
                        $arrVtasVend[$valV->IDVENDEDOR][$valV->FAMILIA][$valV->ANIO][$valV->MES][$valV->DIA] = $valV->CANTIDAD;
                    else
                        $arrVtasVend[$valV->IDVENDEDOR][$valV->FAMILIA][$valV->ANIO][$valV->MES][$valV->DIA] = $valV->TOTAL;
                }  
            } 
        }

        $arrVtasVendF = array();
        foreach( $arrVtasVend as $idVendedor => $rwVendedor){
            foreach($arrFamilias as $familia){
                if(isset($arrVtasVend[$idVendedor][$familia])){
                    foreach($rwVendedor as $idxFamilia => $rwFamilia){
                        foreach($rwFamilia as $idxAnio => $rwAnio){
                            foreach($rwAnio as $idxMes => $rwMes){
                                $L = new DateTime( $idxAnio.'-'.$idxMes.'-01' ); 
                                $diaFinal = $L->format( 't' );
                                for($ini=1;$ini<=$diaFinal;$ini++){
                                    if(!isset($rwMes[$ini])){
                                        $arrVtasVend[$idVendedor][$idxFamilia][$idxAnio][$idxMes][$ini] = 0;
                                    }
                                }                                              
                            }
                        }
                    }    
                }else{
                    if($mes>0){
                        $L = new DateTime( $anio.'-'.$mes.'-01' ); 
                        $diaFinal = $L->format( 't' );
                        for($ini=1;$ini<=$diaFinal;$ini++){
                            $arrVtasVend[$idVendedor][$familia][$anio][$mes][$ini] = 0;
                        } 
                    }else{
                        for($iMes=1;$iMes<=12;$iMes++){
                            $L = new DateTime( $anio.'-'.$iMes.'-01' ); 
                            $diaFinal = $L->format( 't' );
                            for($ini=1;$ini<=$diaFinal;$ini++){
                                $arrVtasVend[$idVendedor][$familia][$anio][$iMes][$ini] = 0;
                            }            
                        }
                    }
                }            
            }
        }
        foreach ($listaVentasVendedores as $i0 => $venta0) {
            if ( isset( $arrVolumen[$venta0->ID ] ) ) { 
                if(isset( $arrVolumen[$venta0->ID ][$venta0->FAMILIA]))
                    $arrVolumen[$venta0->ID][$venta0->FAMILIA]+= $venta0->CANTIDAD;
                else
                    $arrVolumen[$venta0->ID][$venta0->FAMILIA] = $venta0->CANTIDAD;
            }else{
                $arrVolumen[$venta0->ID][$venta0->FAMILIA] = $venta0->CANTIDAD;
            }
        }

        //Generamos el arreglo para los ROWS y solo para el mes seleccionado
        if($mes>0){
            $mesSelected = $mes;
        }else{
            $mesSelected = date('m')*1;            
        }
        //Creamos la cabecera
        $arrHeader = array('');
        $arrSubHeader = array('FAMILIA');
        $arrWidth = array(120);        
        $arrHeaderWidth = array(120); 
        $L = new DateTime( $anio.'-'.$mesSelected.'-01' ); 
        $diaFinal = $L->format( 't' );
        for($ini=1;$ini<=$diaFinal;$ini++){
            $arrHeader[] = $ini;            
            $arrHeaderWidth[] = 140;
            $arrSubHeader[] = 'R';
            $arrSubHeader[] = 'M';
            $arrWidth[] = 70;
            $arrWidth[] = 70;
        } 
        $arrHeader[] = 'TOTAL';        
        $arrSubHeader[] = 'R';
        $arrSubHeader[] = 'M';       
        $arrHeaderWidth[] = 160;
        $arrWidth[] = 80;
        $arrWidth[] = 80;

        $arrRows = array();

        foreach($arrVtasVend as $idvend => $vend){
            foreach($vend as $idx9 => $familia9){
                $arrTMP = array($idx9);
                for($ini=1;$ini<=$diaFinal;$ini++){ 
                    if(isset($arrVtasVend[$idvend][$idx9][$anio][$mesSelected][$ini])){
                        array_push($arrTMP,$arrVtasVend[$idvend][$idx9][$anio][$mesSelected][$ini]);
                    }else{
                        array_push($arrTMP,0);
                    }
                }  
                $arrRows[$idvend][] = $arrTMP;               
            }
        }


        foreach ($listaVentasVendedores as $i => $venta) {

            //se verififica que las ventas son sin firma
            $precioVenta = number_format( $venta->PRECIO, 2,".","");
            $precioMinimo = floatval( filter_var( $venta->PVP3, FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION) );
            
            $comisionConFirma = 0;
            $comisionSinFirma = 0;
            $hasValvula = true;

            if ( $precioVenta >= $precioMinimo) {
                $comisionSinFirma = $venta->TOTAL * 0.01;
            } else {
                    //validando que no use firmas
                    if($venta->SUBFAMILIA !='VALVULA'){
                        $hasValvula = false;
                    }
            }

            $newRows = array();
            foreach($arrRows[$venta->IDVENDEDOR] as $rowsIDX => $rowsFamilia){
                $newRows[$rowsIDX] = array();
                $ntn = 0;
                $realv = 0;
                $reali = 0;
                $familiaActual = '';
                foreach($rowsFamilia as $rowsVal){  
                    if($ntn==0){
                        $familiaActual = $rowsVal;
                        $newRows[$rowsIDX][] = $rowsVal;
                    }else{
                        if($arrMetas[$venta->IDVENDEDOR][$familiaActual]['VDIARIO']>0 || $arrMetas[$venta->IDVENDEDOR][$familiaActual]['IDIARIO']>0){
                            if($familiaActual!='ACCESORIO'){
                                $newRows[$rowsIDX][] = $rowsVal;
                                $newRows[$rowsIDX][] = $arrMetas[$venta->IDVENDEDOR][$familiaActual]['VDIARIO'];
                                $realv = $familiaActual=='RIN'?$realv+($rowsVal/4):$realv+$rowsVal;
                            }else{
                                $newRows[$rowsIDX][] = '$'.number_format($rowsVal,0,'.',',');
                                $newRows[$rowsIDX][] = '$'.number_format($arrMetas[$venta->IDVENDEDOR][$familiaActual]['IDIARIO'],0,'.',',');
                                $reali+=$rowsVal;
                            }
                        }else{
                            if($familiaActual!='ACCESORIO'){
                                $newRows[$rowsIDX][] = $rowsVal;
                                $newRows[$rowsIDX][] = 'NA';
                                $realv = $familiaActual=='RIN'?$realv+($rowsVal/4):$realv+$rowsVal;
                            }else{
                                $newRows[$rowsIDX][] = '$'.number_format($rowsVal,0,'.',',');
                                $newRows[$rowsIDX][] = 'NA';
                                $reali+=$rowsVal;
                            }
                        }                        
                    }
                    $ntn++;
                }
                if($arrRows[$venta->IDVENDEDOR][$rowsIDX][0]=='ACCESORIO'){
                    $newRows[$rowsIDX][] = '$'.number_format($reali,0,'.',',');
                    $newRows[$rowsIDX][] = '$'.number_format($arrMetas[$venta->IDVENDEDOR][$arrRows[$venta->IDVENDEDOR][$rowsIDX][0]]['IMENSUAL'],0,'.',',');
                }else{
                    $newRows[$rowsIDX][] = $realv;
                    $newRows[$rowsIDX][] = $arrMetas[$venta->IDVENDEDOR][$arrRows[$venta->IDVENDEDOR][$rowsIDX][0]]['VMENSUAL'];
                }
            }
            foreach ($newRows as $key => $rrr) {
                $aux[$key] = $rrr[0];
            }
            array_multisort($aux, SORT_ASC, $newRows);

            if ( isset( $listaComisiones[$venta->ID ] ) ) { 
                $progreso = round( $listaComisiones[ $venta->ID]['vendido'] /$metaVendedor,2);
                $listaComisiones[ $venta->ID]['vendido'] += round($venta->TOTAL,2);
                $listaComisiones[ $venta->ID]['comisionReal'] += round( floatval($comisionSinFirma),2) ;
                $listaComisiones[ $venta->ID ]['comisionConFirma'] += round( ($venta->TOTAL * 0.01)  / 1, 2);
                $listaComisiones[ $venta->ID ]['nFirmas'] += !$hasValvula ?  1 : 0;
            } else {
                $progreso = round( $listaComisiones[ $venta->ID]['vendido'] /$metaVendedor,2);
                $listaComisiones[ $venta->ID]['vendedor'] = mb_convert_encoding( $venta->VENDEDOR , "UTF-8");
                $listaComisiones[ $venta->ID]['vendido'] = round($venta->TOTAL,2);
                $listaComisiones[ $venta->ID]['comisionReal'] =  round($comisionSinFirma,2);
                $listaComisiones[ $venta->ID ]['nFirmas'] = !$hasValvula ?  1 : 0;
                $listaComisiones[ $venta->ID ]['comisionConFirma'] =  round( ($venta->TOTAL * 0.01)  / 1, 2);
                $listaComisiones[ $venta->ID ]['progreso'] = $progreso > 1 ? 1 : $progreso;
                $listaComisiones[ $venta->ID ]['header'] = $arrHeader;
                $listaComisiones[ $venta->ID ]['rows'] = $newRows;
                $listaComisiones[ $venta->ID ]['widths'] = $arrWidth;
                $listaComisiones[ $venta->ID ]['widthsh'] = $arrHeaderWidth;
                $listaComisiones[ $venta->ID ]['subheader'] = $arrSubHeader;
            }
            
        }

        $vendedores = [];
        foreach ($listaComisiones as $i => $vendedor) {
            array_push( $vendedores , $vendedor );
        }
        
        

        usort($vendedores, [$this,'cmp']); 
        
        return $vendedores;
    }

    


        public function cmp($a, $b)
        {
            if ($a['vendido'] == $b['vendido']) {
                return 0;
            }
            return ($a['vendido'] > $b['vendido']) ? -1 : 1;
        }

}

// $test = new VendedoresController();
// echo json_encode($test->getMetasVendedores(9,2022));