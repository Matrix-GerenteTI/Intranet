<?php
// ini_set('memory_limit', '-1');



set_time_limit(0);
ini_set('memory_limit', '20000M');

require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/almacenes/Articulos.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/Reportes/Reportes.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/Reportes/prepareExcel.php";


//  $cm = \PHPExcel_CachedObjectStorageFactory::cache_in_memory_gzip;
//   \PHPExcel_Settings::setCacheStorageMethod($cm);

class Valuados extends prepareExcel
{
    public function __construct()
    {
        parent::__construct();
        $this->libro->getProperties()->setTitle('VALUADO DE PRODUCTOS'); 
    }

    public function configuraPrecios( $item)
    {
                $articulos = new Articulos;
                $ctoultcompra = 0;
                $item['DIASINV'] = 'INV. INI';
                $item['FECULT'] = 'INV. INI';
                // $listaArticulosAgrupados[$articulo->CODIGOART] = $articulo;
                $ultcomp = $articulos->getUltimaCompra( $item['CODIGOART'] ) ;
                //foreach($ultcomp as $datauc){
                    $item['DIASINV'] = $ultcomp['DIF'];
                    $item['FECULT'] = $ultcomp['FECULTCOMPRA'];
                    $item['PREMECOS'] = $ultcomp['CTOULTCOMPRA'];

                    if($item['DIASINV']<=45){
                        $item['CLASIFICACION']='RAPIDO';
                    }else{
                        if($item['DIASINV']<=90){
                            $item['CLASIFICACION']='REGULAR';
                        }else{
                            if($item['DIASINV']<=180){
                                $item['CLASIFICACION']='LENTO';
                            }else{
                                if($item['DIASINV']<=365){
                                    $item['CLASIFICACION']='RESAGADO';
                                }else{
                                    $item['CLASIFICACION']='OBSOLETO';
                                }
                            }
                        }
                    }
                //}
                
                //$ctouc = floatval($ctoultcompra);
                
                //obteniendo los precios de venta del articulo
                // $precios = $articulos->getPrecioByArticulo( $item['IDARTICULO'] );

                // if(  $item->CODIGOART == "AW2059-BT"){
                //     echo json_encode(( $precios ));
                //     exit();
                // }
                //$item['PREMECOS'] = $ctoultcompra;
                //if($ctouc > 0 && $ctoultcompra != '' && ( $item['PREMECOS'] == 0 || $item['PREMECOS'] == "" )  )
                //{
        
                //        $item['PREMECOS'] = $ctoultcompra;
                //}   
                //else{   
                    // foreach ( $precios  as $x => $precio) {

                    //     if( $item['PREMECOS'] == 0  ){
                            

                    //         if( $precio->CTOPROMEDIO > 0 ){
                    //             echo json_encode( $precios );
                    //             exit();
                    //             $item['PREMECOS'] = $precios[$x]->CTOPROMEDIO;
                    //         } 
                    //     }
                    // }
                //}
            
                    

				$item['DESCRIP'] =utf8_decode( $item['DESCRIP'] );
                $item['MARCA'] = $item['MARCA'];
                /*$item['PVP1'] = !isset($precios[0]->PVP1) ? 0 : str_replace(',','', str_replace('$','',$precios[0]->PVP1) ) ;

                $item['PVP2'] =  !isset($precios[0]->PVP2) ? 0 : str_replace(',','', str_replace('$','',$precios[0]->PVP2) ) ;
                $item['PVP3'] =  !isset($precios[0]->PVP3) ? 0 : str_replace(',','', str_replace('$','',$precios[0]->PVP3) ) ;
                $item['PVP4'] =  !isset($precios[0]->PVP4) ? 0 : str_replace(',','', str_replace('$','',$precios[0]->PVP4) ) ;
                $item['PVP5'] =  !isset($precios[0]->PVP5) ? 0 : str_replace(',','', str_replace('$','',$precios[0]->PVP5) ) ;
*/
                //$precio5 = (($item['PREMECOS'] * 1.16)*(1.10) );
                //$precio6 = (($item['PREMECOS'] * 1.16)*(1.10) );
                // $item['PVP6'] =  $precio6;
				/*
                if ( !isset($precios[0]->PVP5) || $precios[0]->PVP5 == '' || $precios[0]->PVP5 === 0 ) {
                    $item['PVP5'] =  $precio5 ;
                    
                } else {
                     $item['PVP5'] =  str_replace(',','', str_replace('$','',$precios[0]->PVP5) ) ;
                     if ($item['PVP5']  == round(($item['PREMECOS'] * 1.16), 2 ) ) {
                         $item['PVP5'] = $precio5;
                     }elseif( is_numeric( $item )  ){
                         if ( abs( ($item['PVP5'] / ($item['PREMECOS'] * 1.16 ) - 1 ) ) < 1 ) {
                             $item['PVP5'] = $precio5;
                         }  
                     }else{
                         $item['PVP5'] = $precio5;
                     }
                }
				*/
        return $item;
    }

    public function formatEspacios($cadena,$longitud){
        $espacios = "";
        $max = $longitud - strlen($cadena);
        for($tmp=0;$tmp<$max;$tmp++){
            $espacios.=" ";
        }
        return $cadena.$espacios;
    }

    public function preparaReporte($data)
    {
        extract($data);
		$arrPVPS = explode(',',$pvps);
        
        $txtFile = '';
        $arrIDS = array();
        
        $articulos = new Articulos;
        $almacen = "%";
        if( $almacen != "%" ){
            $infoAlmacen = $articulos->getAlmacenes($almacen);
            $almacen = $infoAlmacen[0]->DESCRIPCION;
        }

        $stock = 0;

        $listaArticulosDesagrupados = $articulos->getValuadoSucursalMysql( $familia, $almacen) ;

        $listaArticulosAgrupados = array();
        if ( $group == 0) {
            foreach($listaArticulosDesagrupados as $i => $articulo){
                $listaArticulosDesagrupados[$i] = $this->configuraPrecios($articulo);                
            }
        } else {
            foreach ($listaArticulosDesagrupados as $i => $articulo) {
                $stock += $articulo['STOCK'];
                if ( ! isset( $listaArticulosAgrupados[$articulo['CODIGOART']]['STOCK']  )  ){
                    $listaArticulosAgrupados[$articulo['CODIGOART']] = $this->configuraPrecios( $articulo);
                }else{
                    $listaArticulosAgrupados[$articulo['CODIGOART']]['STOCK']  += $articulo['STOCK'];
                }
                //echo $listaArticulosAgrupados[$articulo->CODIGOART]['DIASINV'];
                //die();
            }
            $listaArticulosDesagrupados = $listaArticulosAgrupados;
        }
        

        // foreach ($listaArticulosDesagrupados as $i => $articulo ) {

        //     // if ( ! isset( $listaArticulosAgrupados[$articulo->CODIGOART]['STOCK'])  ){

                
                
        //     //     //  echo $listaArticulosAgrupados[$articulo->CODIGOART]['PVP5'].' ----- '.$precio5 ."<br>";
        //     // } else {
        //     //     $listaArticulosAgrupados[$articulo->CODIGOART]['STOCK'] += $articulo['STOCK'];

        //     // }
            
            
        // }

        
        $libroEdicion = $this->libro->getActiveSheet();

        $this->creaEmptySheet("VALUADO $familia");
        $libroEdicion->setCellValue('L1','PRECIOS CON IVA');
        $libroEdicion->mergeCells("L1:V1");
        $libroEdicion->getStyle("L1")->applyFromArray($this->centrarTexto);
        $libroEdicion->getStyle("L1")->applyFromArray($this->labelBold);

        if ( $almacen == '%' ) {
            $almacen = "Matrixxx Alm.";
        }else{
            $infoAlmacen = $articulos->getAlmacenes($almacen);
            $almacen = $infoAlmacen[0]->DESCRIPCION;
        }

        $libroEdicion->setCellValue("C5","VALUADO DE  $familia DEL ".date("d-m-Y"));
        $libroEdicion->mergeCells("C5:I5");
        // $libroEdicion->setCellValue("B2","$almacen.");
        $libroEdicion->getStyle("C5")->applyFromArray($this->centrarTexto);
        $libroEdicion->getStyle("C5")->applyFromArray($this->labelBold);

        $this->putLogo("A1",300,200);

        //aqui mandar a los métodos
        if($familia == 'LLANTA'){
            // var_dump("hereee");
            $this->generarLlantas($data);

        }
        if($familia == 'RIN'){
            $this->generarRines($data);

        }

        $libroEdicion->setAutoFilter("A7:AG7");
        $libroEdicion->setCellValue("L3","P.LISTA");
        $libroEdicion->setCellValue("N3","MED. MAY.");
        $libroEdicion->setCellValue("P3","MAYOREO");
        $libroEdicion->setCellValue("R3","MED. MAY.");

        $libroEdicion->setCellValue("A7",'CODIGO');
        $libroEdicion->setCellValue("B7",'DESCRIPCION');
        $libroEdicion->setCellValue("C7",'FAMILIA');
        $libroEdicion->setCellValue("D7",'SUBFAMILIA');
        switch ($familia) {
            // case 'LLANTA':
            //     $this->generarLlantas($data);
            //     // $libroEdicion->setCellValue("E7","MEDIDA");
            //     // $libroEdicion->setCellValue("F7",'MARCA');
            //     // $libroEdicion->setCellValue("G7",'MODELO');
            //     break;
            // case 'RIN':
            //     $this->generarRines($data);
            //     // $libroEdicion->setCellValue("E7","ANCHO");
            //     // $libroEdicion->setCellValue("F7",'DIAMETRO');
            //     // $libroEdicion->setCellValue("G7",'BARRENACION');                
            //     break;
            case 'ACCESORIO':
                $libroEdicion->setCellValue("E7","MARCA");
                $libroEdicion->setCellValue("F7",'MODELO');
                $libroEdicion->setCellValue("G7",'AÑO');             
                break;
            case 'COLISION':
                $libroEdicion->setCellValue("E7","MARCA");
                $libroEdicion->setCellValue("F7",'MODELO');
                $libroEdicion->setCellValue("G7",'AÑO');           
                break;
        }
        $libroEdicion->setCellValue("H7",'PROVEEDOR');
		if ( $displayStock == 1) {
			$libroEdicion->setCellValue("I7",'STOCK');
		}
		if ( $displayAlmacen == 1) {
			$libroEdicion->setCellValue("J7",'ALMACEN');
		}
		if ( $costo == 1) {
			$libroEdicion->setCellValue("K7",'CTO. PROM.');
			$libroEdicion->setCellValue("L7",'CTO. PROM. C\IVA');
            $libroEdicion->setCellValue("M7",'TOT. CTO. PROM.');
            $libroEdicion->setCellValue("N7",'TOT. CTO. PROM. C\IVA');
        }
        /*
        if($familia!='RIN'){
            $libroEdicion->setCellValue("O7",'PVP1');
            $libroEdicion->setCellValue("P7",'%');
            $libroEdicion->setCellValue("Q7",'PVP2');
            $libroEdicion->setCellValue("R7",'%');
            $libroEdicion->setCellValue("S7",'PVP3');
            $libroEdicion->setCellValue("T7",'%');
            $libroEdicion->setCellValue("U7",'PVP4');
            $libroEdicion->setCellValue("V7",'%');
            $libroEdicion->setCellValue("W7",'PVP5');
            $libroEdicion->setCellValue("X7",'%');
            // $libroEdicion->setCellValue("Y7",'DIAS INV.');
            $libroEdicion->setCellValue("Z7",'FEC. ULT.');
            $libroEdicion->setCellValue("AA7",'OBSERVACIONES PRECIOS');
            //$libroEdicion->setCellValue("Z7",'%');

            $libroEdicion->getStyle("A7:AA7")->applyFromArray( $this->labelBold);
            $libroEdicion->getStyle("A7:AA7")->applyFromArray( $this->centrarTexto);
            $libroEdicion->getStyle("A7:AA7")->applyFromArray( $this->setColorText('ffffff', 11));
            $libroEdicion->getStyle("A7:AA7")->getFill()->applyFromArray( $this->setColorFill("cc0000")  );
        }else{
            */
            $libroEdicion->setCellValue("O7",'PVP1');
            $libroEdicion->setCellValue("P7",'PVP1xJGO');
            $libroEdicion->setCellValue("Q7",'%');
            $libroEdicion->setCellValue("R7",'PVP2');
            $libroEdicion->setCellValue("S7",'PVP2xJGO');
            $libroEdicion->setCellValue("T7",'%');
            $libroEdicion->setCellValue("U7",'PVP3');
            $libroEdicion->setCellValue("V7",'PVP3xJGO');
            $libroEdicion->setCellValue("W7",'%');
            $libroEdicion->setCellValue("X7",'PVP4');
            $libroEdicion->setCellValue("Y7",'PVP4xJGO');
            $libroEdicion->setCellValue("Z7",'%');
            $libroEdicion->setCellValue("AA7",'PVP5');
            $libroEdicion->setCellValue("AB7",'PVP5xJGO');
            $libroEdicion->setCellValue("AC7",'%');
            $libroEdicion->setCellValue("AD7",'DIAS INV.');
            $libroEdicion->setCellValue("AE7",'FEC. ULT.');
            //$libroEdicion->setCellValue("AF7",'OBSERVACIONES PRECIOS');
            $libroEdicion->setCellValue("AF7",'CTO ULT COMP');
            $libroEdicion->setCellValue("AG7",'CLASIFICACION');
            //$libroEdicion->setCellValue("Z7",'%');

            $libroEdicion->getStyle("A7:AG7")->applyFromArray( $this->labelBold);
            $libroEdicion->getStyle("A7:AG7")->applyFromArray( $this->centrarTexto);
            $libroEdicion->getStyle("A7:AG7")->applyFromArray( $this->setColorText('ffffff', 11));
            $libroEdicion->getStyle("A7:AG7")->getFill()->applyFromArray( $this->setColorFill("cc0000")  );
        //}
    
        for ($i=1; $i <= 8 ; $i++) { 
            $libroEdicion->freezePane('A'.$i);
        }        
        
        $trend = 0;
        $i = 8;
        $colorRow = false;
        $arrayColumnPVP = array('',array('O','P','Q'),array('R','S','T'),array('U','V','W'),array('X','Y','Z'),array('AA','AB','AC'));
        foreach ($listaArticulosDesagrupados as $codigo => $articulo) {

            if ( $colorRow) {
                $libroEdicion->getStyle("A$i:AA$i")->getFill()->applyFromArray( $this->setColorFill("f0f5f5")  );
            }
			$libroEdicion->getStyle("A$i:AG$i")->getAlignment()->applyFromArray(array('vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,));
            $libroEdicion->setCellValue("A$i", $articulo['CODIGOART']);
            $libroEdicion->setCellValue("B$i", mb_convert_encoding($articulo['DESCRIP'], 'UTF-8', 'ISO-8859-1'));
            $libroEdicion->setCellValue("C$i",$articulo['FAMILIA']);
            $libroEdicion->setCellValue("D$i",$articulo['SUBFAMILIA']);
            $libroEdicion->setCellValue("H$i",$articulo['MARCA']);

            if ( $familia  == 'LLANTA' ) {
                    $descripcionSplitted = explode(" ", utf8_decode($articulo['DESCRIP']));
                    $libroEdicion->setCellValue("E$i", $descripcionSplitted[0]);
                    $libroEdicion->setCellValue("F$i", $articulo['MARCA']);
                    $modelo = '';
                    $nnn = 1;
                    foreach($descripcionSplitted as $txt){
                        if($nnn > 2){
                            $modelo.= $txt;
                        } 
                        $nnn++;
                    }
                    $libroEdicion->setCellValue("G$i", $modelo ); //$articulo['SUBFAM3']);
            }else{
                if ( $familia == 'RIN' ) {
                    $descripcionSplitted = explode(" ", utf8_decode($articulo['DESCRIP']));

                    $anchoExtrac = explode("X", $descripcionSplitted[0])[1]; 
                    $diametroExtract = explode("X", $descripcionSplitted[0])[0];
                    $barrenacionExtract = $descripcionSplitted[1];

                    $libroEdicion->setCellValue("E$i",$anchoExtrac);
                    $libroEdicion->setCellValue("F$i",$diametroExtract);
                    $libroEdicion->setCellValue("G$i",$barrenacionExtract);
                    $libroEdicion->setCellValue("H$i",$descripcionSplitted[2]);
                }else{
                    if ( $familia == 'COLISION' || $familia == 'ACCESORIO') {
                        $libroEdicion->setCellValue("E$i",$articulo['SUBFAM2']);
                        $libroEdicion->setCellValue("F$i",$articulo['SUBFAM3']);
                        $libroEdicion->setCellValue("G$i",$articulo['SUBFAM4']);
                        $libroEdicion->setCellValue("H$i",$articulo['MARCA']);
                    }else{
                        $libroEdicion->setCellValue("E$i",$articulo['MARCA']);
                        $libroEdicion->setCellValue("F$i",$articulo['SUBFAM3']);
                        $libroEdicion->setCellValue("H$i",$articulo['MARCA']);
                    }
                }
                    
            }
			if ( $displayStock == 1) {
				$libroEdicion->setCellValue("I$i",$articulo['STOCK']);
				$libroEdicion->getStyle("I$i")->applyFromArray( $this->centrarTexto);
			}
			
			if($costo==1){
				$libroEdicion->setCellValue("K$i", $articulo['PREMECOS']);
				$libroEdicion->setCellValue("L$i","=K$i*1.16");
                $libroEdicion->setCellValue("M$i","=I$i*K$i");
                $libroEdicion->setCellValue("N$i","=I$i*L$i");
			}
			if ( $displayAlmacen == 1) {
				$libroEdicion->setCellValue("J$i", $articulo['ALMACEN']);
			}
            
            //GENERA ARCHIVO PARA VOLCADO EN FIREBIRD CON PRECIOS ACTUALIZADOS POR LAYOUT
            //Valida en la tabla de precios mysql cual es su configuración de precios
            if($articulo['FAMILIA']=='LLANTA'){
                $expMedida = explode(" ", $articulo['DESCRIP']);
                $configPrecios = $articulos->getPoliticaPrecios($articulo['FAMILIA'],$expMedida[0],'');
            }else{
                $configPrecios = $articulos->getPoliticaPrecios($articulo['FAMILIA'],$articulo['SUBFAMILIA'],'');
            }
            
            //var_dump($configPrecios);
            //die(); 
            if(is_array($configPrecios)){
                if($articulo['FAMILIA']=='LLANTA'){
                    if(!in_array($articulo['IDARTICULO'],$arrIDS)){
                        $arrIDS[] = $articulo['IDARTICULO'];
                        $trend++;
                        //Formateo de espacios
                        $txtFile.= $this->formatEspacios($articulo['IDARTICULO'],20);
                        $txtFile.= $this->formatEspacios(number_format($articulo['PREMECOS'],2,'.',''),15);
                        $txtFile.= $this->formatEspacios(number_format((($articulo['PREMECOS']*1.16) * (1 + ($configPrecios['pvp1']/100))),2,'.',''),15);
                        $txtFile.= $this->formatEspacios(number_format((($articulo['PREMECOS']*1.16) * (1 + ($configPrecios['pvp2']/100))),2,'.',''),15);
                        $txtFile.= $this->formatEspacios(number_format((($articulo['PREMECOS']*1.16) * (1 + ($configPrecios['pvp3']/100))),2,'.',''),15);
                        $txtFile.= $this->formatEspacios(number_format((($articulo['PREMECOS']*1.16) * (1 + ($configPrecios['pvp4']/100))),2,'.',''),15);
                        $txtFile.= $this->formatEspacios(number_format((($articulo['PREMECOS']*1.16) * (1 + ($configPrecios['pvp5']/100))),2,'.',''),15);
                        //$txtFile.= chr(13).chr(10);
                    }
                }

                $preciosVenta = [];

            
                if(in_array('1',$arrPVPS)){
                    $tmpPVP1 = (float) number_format((($articulo['PREMECOS']*1.16) * (1 + ($configPrecios['pvp1']/100))),2,'.','');
                    $libroEdicion->setCellValue("O$i",$tmpPVP1);
                    array_push( $preciosVenta , [ 'precio' => $tmpPVP1 , 'numero' => 1] );
                    if($familia=='RIN'){
                        $libroEdicion->setCellValue("P$i",$tmpPVP1*4);
                    }else{
                        $libroEdicion->getColumnDimension('P')->setVisible(false);
                    }
                    if($utilidad==1){
                        $libroEdicion->setCellValue("Q$i","=ABS((O$i/L$i) - 1)");  
                    }
                }
                if(in_array('2',$arrPVPS)){
                    $tmpPVP2 = (float) number_format((($articulo['PREMECOS']*1.16) * (1 + ($configPrecios['pvp2']/100))),2,'.','');
                    array_push( $preciosVenta , [ 'precio' => $tmpPVP2 , 'numero' => 2] );
                    $libroEdicion->setCellValue("R$i",$tmpPVP2);
                    if($familia=='RIN'){
                        $libroEdicion->setCellValue("S$i",$tmpPVP2*4);
                    }else{
                        $libroEdicion->getColumnDimension('S')->setVisible(false);
                    }
                    if($utilidad==1){
                        $libroEdicion->setCellValue("T$i","=ABS((R$i/L$i) - 1)");  
                    }
                }
                if(in_array('3',$arrPVPS)){
                    $tmpPVP3 = (float) number_format((($articulo['PREMECOS']*1.16) * (1 + ($configPrecios['pvp3']/100))),2,'.','');
                    array_push( $preciosVenta , [ 'precio' => $tmpPVP3 , 'numero' => 3] );
                    $libroEdicion->setCellValue("U$i",$tmpPVP3);
                    if($familia=='RIN'){
                        $libroEdicion->setCellValue("V$i",$tmpPVP3*4);
                    }else{
                        $libroEdicion->getColumnDimension('V')->setVisible(false);
                    }
                    if($utilidad==1){
                        $libroEdicion->setCellValue("W$i","=ABS((U$i/L$i) - 1)");  
                    }
                }
                if(in_array('4',$arrPVPS)){
                    $tmpPVP4 = (float) number_format((($articulo['PREMECOS']*1.16) * (1 + ($configPrecios['pvp4']/100))),2,'.','');
                    array_push( $preciosVenta , [ 'precio' => $tmpPVP4 , 'numero' => 4] );
                    $libroEdicion->setCellValue("X$i",$tmpPVP4);
                    if($familia=='RIN'){
                        //echo $articulo['PVP4']."<br>";
                        $libroEdicion->setCellValue("Y$i",$tmpPVP4*4);
                    }else{
                        $libroEdicion->getColumnDimension('Y')->setVisible(false);
                    }
                    if($utilidad==1){
                    $libroEdicion->setCellValue("Z$i","=ABS((X$i/L$i) - 1)");  
                    }
                }
                if(in_array('5',$arrPVPS)){
                    $tmpPVP6 = (float) number_format((($articulo['PREMECOS']*1.16) * (1 + ($configPrecios['pvp5']/100))),2,'.','');
                    array_push( $preciosVenta , [ 'precio' => $tmpPVP5 , 'numero' => 5] );
                    $libroEdicion->setCellValue("AA$i",$tmpPVP5);
                    if($familia=='RIN'){
                        $libroEdicion->setCellValue("AB$i",$tmpPVP5*4);
                    }else{
                        $libroEdicion->getColumnDimension('AB')->setVisible(false);
                    }
                    if($utilidad==1){
                        $libroEdicion->setCellValue("AC$i","=ABS((AA$i/L$i) - 1)");  
                    }
                }

            }else{

                if(in_array('1',$arrPVPS)){
                    $libroEdicion->setCellValue("O$i",$articulo['PVP1']);
                    array_push( $preciosVenta , [ 'precio' => $articulo['PVP1'] , 'numero' => 1] );
                    if($familia=='RIN'){
                        $libroEdicion->setCellValue("P$i",$articulo['PVP1']*4);
                    }else{
                        $libroEdicion->getColumnDimension('P')->setVisible(false);
                    }
                    if($utilidad==1){
                        $libroEdicion->setCellValue("Q$i","=ABS((O$i/L$i) - 1)");  
                    }
                }
                if(in_array('2',$arrPVPS)){
                    array_push( $preciosVenta , [ 'precio' => $articulo['PVP2'] , 'numero' => 2] );
                    $libroEdicion->setCellValue("R$i",$articulo['PVP2']);
                    if($familia=='RIN'){
                        $libroEdicion->setCellValue("S$i",$articulo['PVP2']*4);
                    }else{
                        $libroEdicion->getColumnDimension('S')->setVisible(false);
                    }
                    if($utilidad==1){
                        $libroEdicion->setCellValue("T$i","=ABS((R$i/L$i) - 1)");  
                    }
                }
                if(in_array('3',$arrPVPS)){
                    array_push( $preciosVenta , [ 'precio' => $articulo['PVP3'] , 'numero' => 3] );
                    $libroEdicion->setCellValue("U$i",$articulo['PVP3']);
                    if($familia=='RIN'){
                        $libroEdicion->setCellValue("V$i",$articulo['PVP3']*4);
                    }else{
                        $libroEdicion->getColumnDimension('V')->setVisible(false);
                    }
                    if($utilidad==1){
                        $libroEdicion->setCellValue("W$i","=ABS((U$i/L$i) - 1)");  
                    }
                }
                if(in_array('4',$arrPVPS)){
                    array_push( $preciosVenta , [ 'precio' => $articulo['PVP4'] , 'numero' => 4] );
                    $libroEdicion->setCellValue("X$i",$articulo['PVP4']);
                    if($familia=='RIN'){
                        echo $articulo['PVP4']."<br>";
                        $libroEdicion->setCellValue("Y$i",$articulo['PVP4']*4);
                    }else{
                        $libroEdicion->getColumnDimension('Y')->setVisible(false);
                    }
                    if($utilidad==1){
                       $libroEdicion->setCellValue("Z$i","=ABS((X$i/L$i) - 1)");  
                    }
                }
                if(in_array('5',$arrPVPS)){
                    array_push( $preciosVenta , [ 'precio' => $articulo['PVP5'] , 'numero' => 5] );
                    $libroEdicion->setCellValue("AA$i",$articulo['PVP5']);
                    if($familia=='RIN'){
                        $libroEdicion->setCellValue("AB$i",$articulo['PVP1']*4);
                    }else{
                        $libroEdicion->getColumnDimension('AB')->setVisible(false);
                    }
                    if($utilidad==1){
                        $libroEdicion->setCellValue("AC$i","=ABS((AA$i/L$i) - 1)");  
                    }
                }

            }
            //var_dump($configPrecios);
            //if($trend==10)
            //    die($txtFile); 
            
            

            /*

			
            */
            
            $libroEdicion->setCellValue("AD$i",$articulo['DIASINV']);     
            /*
            0 DIAS < RAPIDO MOVIMIENTO < 45 DIAS
            45 DIAS < REGULAR < 3 MESES
            3 MESES < LENTO < 6 MESES
            6 < RESAGADO < 1 AÑO
            1 < OBSOLETO
            */
            $libroEdicion->setCellValue("AE$i",$articulo['FECULT']);

            //Ordenando los precios para ver si hay descuadre en los precios de venta
            $articulo['PREMECOS'];
            
            $preciosIncorrectos = '';
            $band = 0;

            foreach ( $preciosVenta as $n => $precio) {
                foreach ( $preciosVenta as $m => $precioIterador) {
                    if ( $m < $band) {
                        continue;
                    }
                    if ( $precioIterador['precio'] >$precio['precio'] ) {
                        $preciosIncorrectos .= " PVP ".$precioIterador['numero'] ." > PVP ". $precio['numero']."," ;
                    }
                }
                $band++;
                
                if ( $precio['precio'] <= ( $articulo['PREMECOS'] * 1.16 ) ) {
                    $preciosIncorrectos .= " COSTO > PVP".$precio['numero']."," ;
                }
            }

            //USAR PARA SABER ULTIMO COSTO DE COMPRA
            $libroEdicion->setCellValue("AF$i", $articulo['PREMECOS']);
            $libroEdicion->setCellValue("AG$i", $articulo['CLASIFICACION']);

            
            //USAR PARA SABER PRECIOS INCORRECTOS
            //$libroEdicion->setCellValue("AF$i", $preciosIncorrectos);

			/*
            if ( in_array('6',$arrPVPS) ) {
                
                    $libroEdicion->setCellValue("Y$i", $articulo->ADIC6);
                if ( $utilidad == 1) {
                    $libroEdicion->setCellValue("Z$i", "=ABS((Y$i/K$i) - 1)");
                }
            }
			*/

            
            $libroEdicion->getStyle("Q$i")->getNumberFormat()->setFormatCode('0%;[Red]-0%');   
            $libroEdicion->getStyle("T$i")->getNumberFormat()->setFormatCode('0%;[Red]-0%');   
            $libroEdicion->getStyle("W$i")->getNumberFormat()->setFormatCode('0%;[Red]-0%');   
            $libroEdicion->getStyle("Z$i")->getNumberFormat()->setFormatCode('0%;[Red]-0%');  
            $libroEdicion->getStyle("AC$i")->getNumberFormat()->setFormatCode('0%;[Red]-0%');    
            $libroEdicion->getStyle("K$i:N$i")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00"); 
            $libroEdicion->getStyle("O$i")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00"); 
            $libroEdicion->getStyle("P$i")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00"); 
            $libroEdicion->getStyle("R$i")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00"); 
            $libroEdicion->getStyle("S$i")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00"); 
            $libroEdicion->getStyle("U$i")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00"); 
            $libroEdicion->getStyle("V$i")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00");
            $libroEdicion->getStyle("X$i")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00"); 
            $libroEdicion->getStyle("Y$i")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00");
            $libroEdicion->getStyle("AA$i")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00");
            $libroEdicion->getStyle("AB$i")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00"); 
            //$libroEdicion->getStyle("Y$i")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00");
            
            $libroEdicion->getRowDimension($i)->setRowHeight(30);

            if ( !$colorRow) {
                $colorRow = true;
            }else{
                $colorRow = false;
            }
            $i++;   
        }

        $fch= fopen('codigosLoad.txt', "a"); // Abres el archivo para escribir en �l
		fwrite($fch, $txtFile); // Grabas
		fclose($fch); // Cierras el archivo.

        //Haciendo la suma del Stock y de los costos
        $libroEdicion->setCellValue("I$i","=SUM(I5:I$i)");
        $libroEdicion->setCellValue("J$i","=SUM(J5:J$i)");
        $libroEdicion->setCellValue("K$i","=SUM(K5:K$i)");
        $libroEdicion->setCellValue("L$i","=SUM(L5:L$i)");
        $libroEdicion->setCellValue("M$i","=SUM(M5:M$i)");
        $libroEdicion->setCellValue("N$i","=SUM(N5:N$i)");

        $libroEdicion->getStyle("I$i:M$i")->applyFromArray( $this->setColorText("ff0000"));
        $libroEdicion->getStyle("I$i")->getNumberFormat()->setFormatCode("#,##0;-#,##0");
        $libroEdicion->getStyle("K$i:M$i")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00"); 

        $libroEdicion->getColumnDimension('A')->setAutoSize(false);
        $libroEdicion->getColumnDimension('A')->setWidth("15");
        $libroEdicion->getColumnDimension('B')->setAutoSize(false);
        $libroEdicion->getColumnDimension('B')->setWidth("40");        
        $libroEdicion->getColumnDimension('C')->setAutoSize(false);
        $libroEdicion->getColumnDimension('C')->setWidth("10");                
        $libroEdicion->getColumnDimension('D')->setAutoSize(false);
        $libroEdicion->getColumnDimension('D')->setWidth("15");              
        $libroEdicion->getColumnDimension('E')->setAutoSize(false);
        $libroEdicion->getColumnDimension('E')->setWidth("12");                  
        $libroEdicion->getColumnDimension('F')->setAutoSize(false);
        $libroEdicion->getColumnDimension('F')->setWidth("12");     
        $libroEdicion->getColumnDimension('G')->setAutoSize(false);
        $libroEdicion->getColumnDimension('G')->setWidth("12");    
        $libroEdicion->getColumnDimension('H')->setAutoSize(false);
        $libroEdicion->getColumnDimension('H')->setWidth("17");     
        $libroEdicion->getColumnDimension('I')->setAutoSize(false);
        $libroEdicion->getColumnDimension('I')->setWidth("22");          
        $libroEdicion->getColumnDimension('J')->setAutoSize(true);
        $libroEdicion->getColumnDimension('K')->setWidth("22");
        $libroEdicion->getColumnDimension('K')->setAutoSize( false);
        $libroEdicion->getColumnDimension('L')->setAutoSize(false);
        $libroEdicion->getColumnDimension('L')->setWidth("22");        
        $libroEdicion->getColumnDimension('M')->setAutoSize(false);
        $libroEdicion->getColumnDimension('M')->setWidth("22");
        $libroEdicion->getColumnDimension('N')->setAutoSize(false);
        $libroEdicion->getColumnDimension('N')->setWidth("22");
        $libroEdicion->getColumnDimension('AD')->setAutoSize(false);
        $libroEdicion->getColumnDimension('AD')->setWidth("20"); 
        $libroEdicion->getColumnDimension('AE')->setAutoSize(false);
        $libroEdicion->getColumnDimension('AE')->setWidth("20");  
        $libroEdicion->getColumnDimension('AF')->setAutoSize(false);
        $libroEdicion->getColumnDimension('AF')->setWidth("20"); 
        $libroEdicion->getColumnDimension('AG')->setAutoSize(false);
        $libroEdicion->getColumnDimension('AG')->setWidth("35");         
		/*        
        $libroEdicion->getColumnDimension('M')->setAutoSize(false);
        $libroEdicion->getColumnDimension('M')->setWidth("14");        
        $libroEdicion->getColumnDimension('O')->setAutoSize(false);
        $libroEdicion->getColumnDimension('O')->setWidth("14");       
        $libroEdicion->getColumnDimension('Q')->setAutoSize(false);
        $libroEdicion->getColumnDimension('Q')->setWidth("14");   
        $libroEdicion->getColumnDimension('S')->setAutoSize(false);
        $libroEdicion->getColumnDimension('S')->setWidth("14");  
        $libroEdicion->getColumnDimension('U')->setAutoSize(false);
        $libroEdicion->getColumnDimension('U')->setWidth("14");  
        $libroEdicion->getColumnDimension('W')->setAutoSize(false);
        $libroEdicion->getColumnDimension('W')->setWidth("14");  
        $libroEdicion->getColumnDimension('Y')->setAutoSize(false);
        $libroEdicion->getColumnDimension('Y')->setWidth("14");     	  
		*/

        //Remomiendo las columnas de PVPs que no se encuentran en el array de PVPs
        for ($i=1; $i <= 5; $i++) { 
            if (! in_array($i, $arrPVPS) ) {
                $libroEdicion->removeColumn( $arrayColumnPVP[$i][0]);
                $libroEdicion->removeColumn( $arrayColumnPVP[$i][1]);
                $libroEdicion->getColumnDimension($arrayColumnPVP[$i][0])->setVisible(false);
                $libroEdicion->getColumnDimension($arrayColumnPVP[$i][1])->setVisible(false);
            }else{
				if ( $utilidad == 0){
					$libroEdicion->removeColumn( $arrayColumnPVP[$i][1]);
					$libroEdicion->getColumnDimension($arrayColumnPVP[$i][1])->setVisible(false);
				}
				$libroEdicion->getColumnDimension($arrayColumnPVP[$i][0])->setAutoSize(false);
				$libroEdicion->getColumnDimension($arrayColumnPVP[$i][0])->setWidth("20"); 
			}
        }
		
		/*if ( $utilidad == 0) {
			$libroEdicion->removeColumn("X");
            $libroEdicion->getColumnDimension("X")->setVisible(false);
			$libroEdicion->removeColumn("V");
            $libroEdicion->getColumnDimension("V")->setVisible(false);
			$libroEdicion->removeColumn("T");
            $libroEdicion->getColumnDimension("T")->setVisible(false);
			$libroEdicion->removeColumn("R");
            $libroEdicion->getColumnDimension("R")->setVisible(false);
			$libroEdicion->removeColumn("P");
            $libroEdicion->getColumnDimension("P")->setVisible(false);
            $libroEdicion->removeColumn("N");
            $libroEdicion->getColumnDimension("N")->setVisible(false);
        }
		*/
        if ( $displayAlmacen == 0) {
            //$libroEdicion->removeColumn("L");
            $libroEdicion->getColumnDimension("N")->setVisible(false);
        }
        $libroEdicion->getColumnDimension("H")->setVisible(false);
        if ( $familia == 'COLISION') {
            $libroEdicion->getColumnDimension("H")->setVisible(true);
        }elseif($familia == 'ACCESORIO' ){
            $libroEdicion->getColumnDimension("H")->setVisible(true);
        }elseif($familia == 'RIN'){
            $libroEdicion->getColumnDimension("H")->setVisible(true);
        }

		
		if ( $costo == 0) {
			//$libroEdicion->removeColumn("K");
            $libroEdicion->getColumnDimension("M")->setVisible(false);
			//$libroEdicion->removeColumn("J");
            $libroEdicion->getColumnDimension("L")->setVisible(false);
            //$libroEdicion->removeColumn("I");
            $libroEdicion->getColumnDimension("K")->setVisible(false);
        }

        if ( $displayStock == 0) {
           // $libroEdicion->removeColumn("H");
            $libroEdicion->getColumnDimension("I")->setVisible(false);
        }
		
		
        $libroEdicion->setShowGridlines(false);
        $valuadoTerminado = new PHPExcel_Writer_Excel2007( $this->libro );
        $valuadoTerminado->setPreCalculateFormulas(true);
        
        // verificar si fam == llanta o rin y descartar esos
        
        // $valuadoTerminado->save("VALUADO $familia $id.xlsx");
        $articulos->close();
 
    }
	
	public function enviarReportes( $lista, $correos, $id, $titulo )
    {
        $emailsender = new \phpmailer;
        $emailsender->IsSMTP(); // habilita SMTP
        $emailsender->SMTPDebug = 1; // debugging: 1 = errores y mensajes, 2 = sólo mensajes
        $emailsender->SMTPAuth = true; // auth habilitada
        $emailsender->Host = "mail.matrix.com.mx";
        $emailsender->Port = 587; // or 587
        $emailsender->IsHTML(true);
        $emailsender->Username = "no-responder@matrix.com.mx";
        $emailsender->Password = "M@tr1x2017";
        $emailsender->FromName = "SITEX";

        $emailsender->Subject = $titulo." del ".date("d-m-Y");
        $emailsender->Body = "<p>...</p>";

        $emailsender->AltBody = "...";

		foreach($lista as $fam){ 
			if ( is_file("VALUADO ".$fam." $id.xlsx") ) {
				$emailsender->AddAttachment("VALUADO ".$fam." $id.xlsx");
			}
		}
		/**/
		//$emailsender->AddAddress('sestrada@matrix.com.mx');

        foreach($correos as $mail){
			$emailsender->AddAddress($mail);
		}
		
        $statusEnvio = $emailsender->Send();

        if ( $emailsender->ErrorInfo == "SMTP Error: Data not accepted") {
            $statusEnvio = true;
        } 

        if ( !$statusEnvio ) {
             echo "[".$emailsender->ErrorInfo."] - Problemas enviando correo electrónico a ";
        } else {
            echo "Enviado";
        }
    }
	
	public function generaReporte( $params )
    {
        extract( $params);
        $fch= fopen('codigosLoad.txt', "w"); // Abres el archivo para escribir en �l
		fwrite($fch, ''); // Grabas
		fclose($fch); // Cierras el archivo.
		// $reportes = new Reportes;
		// $listReporte = $reportes->getReporte('valuado');
        
		//Recorremos los registros que tengan en el campo "nombre" lo indicado en el parametro enviado a getReporte, por cada registro se llamará a la función enviarReporte()
		// foreach($listReporte as $rw){
            // var_dump( $rw);
            // echo "<br><br>";
            $arrfamilias = explode(',',$familias);
            
            $arrcorreos = explode(',',$correos);
            // if( $titulo != 'Valuado Clientes'){
            //     exit();
            // }
			foreach($arrfamilias as $rwfamilias){
                if( $rwfamilias  != 'COLISION' ){
                    //continue;
                }
                
                $parametros = array(
                    'familia' => $rwfamilias,
                    'almacen' => $almacenes,
                    'utilidad' => $utilidad,
                    'costo' => $costo,
                    'pvps' => $pvps,
                    'id' => $id,
                    'group' => $agrupado,
                    'displayStock' => $stock,
                    'displayAlmacen' =>$almacen,
                    'titulo' =>$titulo
                );
				$this->preparaReporte($parametros);
				$this->libro = null;
                $this->__construct();
                
            }
            // exit();
			// $this->enviarReportes($arrfamilias, $arrcorreos, $id, $titulo);
		// }
		
        
    }

    public function generarLlantas($data)
    {
        extract($data);
		$arrPVPS = explode(',',$pvps);
        
        $txtFile = '';
        $arrIDS = array();
        
        $articulos = new Articulos;
        $almacen = "%";
        if( $almacen != "%" ){
            $infoAlmacen = $articulos->getAlmacenes($almacen);
            $almacen = $infoAlmacen[0]->DESCRIPCION;
        }

        $stock = 0;

        $listaArticulosDesagrupados = $articulos->getValuadoSucursalMysql( $familia, $almacen) ;
        // $listaArticulosDesagrupados = $articulos->obtenerValuados( $familia, $almacen) ;

        $listaArticulosAgrupados = array();
        if ( $group == 0) {
            foreach($listaArticulosDesagrupados as $i => $articulo){
                $listaArticulosDesagrupados[$i] = $this->configuraPrecios($articulo);
            }
        } else {
            foreach ($listaArticulosDesagrupados as $i => $articulo) {
                $stock += $articulo['STOCK'];
                if ( ! isset( $listaArticulosAgrupados[$articulo['CODIGOART']]['STOCK']  )  ){
                    $listaArticulosAgrupados[$articulo['CODIGOART']] = $this->configuraPrecios( $articulo);
                }else{
                    $listaArticulosAgrupados[$articulo['CODIGOART']]['STOCK']  += $articulo['STOCK'];
                }
                //echo $listaArticulosAgrupados[$articulo->CODIGOART]['DIASINV'];
                //die();
            }
            $listaArticulosDesagrupados = $listaArticulosAgrupados;
        }


        
        $libroEdicion = $this->libro->getActiveSheet();


        $libroEdicion->setAutoFilter("A7:F7");

        $libroEdicion->setCellValue("A7",'ARTÍCULO'); // CB
        $libroEdicion->setCellValue("B7",'DESCRIPCION');
        $libroEdicion->setCellValue("C7",'MARCA');


        // $libroEdicion->setAutoFilter("D7:F7");

        $libroEdicion->setCellValue("D7","ANCHO");
        $libroEdicion->setCellValue("E7",'ALTO');
        $libroEdicion->setCellValue("F7",'RODADA / RIN');

        $libroEdicion->setCellValue("G7", 'TUXTLA');
        $libroEdicion->setCellValue("N7", 'SAN CRISTOBAL');
        $libroEdicion->setCellValue("P7", 'TAPACHULA');

        $libroEdicion->setCellValue("G8", 'LAURELES');
        $libroEdicion->setCellValue("H8", 'QUINTA NTE');
        $libroEdicion->setCellValue("I8", 'ÁMBAR');
        $libroEdicion->setCellValue("J8", 'GÉNESIS');
        $libroEdicion->setCellValue("K8", 'LIBRAMIENTO');
        $libroEdicion->setCellValue("L8", 'PALMERAS');
        $libroEdicion->setCellValue("M8", 'CEDIM BLVD');

        $libroEdicion->setCellValue("N8", 'SAN RAMÓN');
        $libroEdicion->setCellValue("O8", 'MERCALTOS');

        $libroEdicion->setCellValue("P8", 'CENTRO');
        $libroEdicion->setCellValue("Q8", 'BOULEVARD');
        $libroEdicion->setCellValue("R8", 'CEDIM TAPA');
        
        $libroEdicion->setCellValue("S7", 'TOTAL');
        
        $libroEdicion->setCellValue("T7", 'PVP1');
        $libroEdicion->setCellValue("U7", '%');
        $libroEdicion->setCellValue("V7", 'PVP2');
        $libroEdicion->setCellValue("W7", '%');
        $libroEdicion->setCellValue("X7", 'PVP3');
        $libroEdicion->setCellValue("Y7", '%');
        $libroEdicion->setCellValue("Z7", 'PVP4');
        $libroEdicion->setCellValue("AA7", '%');
        $libroEdicion->setCellValue("AB7", 'PVP5');
        $libroEdicion->setCellValue("AC7", '%');
        $libroEdicion->setCellValue("AD7", 'DIAS INV.');
        $libroEdicion->setCellValue("AE7", 'FECHA ULT');
        $libroEdicion->setCellValue("AF7", 'CTO ULT COMPRA');



        $libroEdicion->mergeCells('G7:M7');
        $libroEdicion->mergeCells('N7:O7');
        $libroEdicion->mergeCells('P7:R7');
        $libroEdicion->mergeCells('S7:S8');

        $libroEdicion->getStyle("A7:AF7")->applyFromArray( $this->labelBold);
        $libroEdicion->getStyle("A7:AF7")->applyFromArray( $this->centrarTexto);
        $libroEdicion->getStyle("A7:AF7")->applyFromArray( $this->setColorText('ffffff', 11));
        $libroEdicion->getStyle("A7:AF7")->getFill()->applyFromArray( $this->setColorFill("073763"));


        $libroEdicion->mergeCells('A7:A8');
        $libroEdicion->mergeCells('B7:B8');
        $libroEdicion->mergeCells('C7:C8');
    

        for ($i=1; $i <= 9 ; $i++) { 
            $libroEdicion->freezePane('A'.$i);
        }        
        
        $trend = 0;
        $i = 9;
        $colorRow = false;
        foreach ($listaArticulosDesagrupados as $codigo => $articulo) {

            $nombreArt[$i] = $articulo['DESCRIP'];
            $codigoArt[$i] = $articulo['CODIGOART'];
            $marcaArt[$i]= $articulo['MARCA'];
            
            if($colorRow){
                $libroEdicion->getStyle("A$i:AF$i")->getFill()->applyFromArray( $this->setColorFill("f0f5f5")  );
            }
			$libroEdicion->getStyle("A$i:AF$i")->getAlignment()->applyFromArray(array('vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,));

            if($nombreArt[$i-1] == $articulo['DESCRIP'] && $codigoArt[$i-1] == $articulo['CODIGOART'] &&  $marcaArt[$i-1] == $articulo['MARCA']){
                $i = $i-1;
                $libroEdicion->setCellValue("A$i", $articulo['CODIGOART']);
                $libroEdicion->setCellValue("B$i", mb_convert_encoding($articulo['DESCRIP'], 'UTF-8', 'ISO-8859-1'));
                $libroEdicion->setCellValue("C$i",$articulo['MARCA']);
                // $libroEdicion->setCellValue("D$i",$articulo['MARCA']);
                // $libroEdicion->setCellValue("H$i",$articulo['MARCA']);
    
                // if ( $familia  == 'LLANTA' ) {
                    $descripcionSplitted = explode(" ", utf8_decode($articulo['DESCRIP']));
                    // $libroEdicion->setCellValue("E$i", $descripcionSplitted[0]);
                    $datosLlanta = explode('/', $descripcionSplitted[0]);
                    $anchoLlanta = $datosLlanta[0];
                    $altoLlanta = $datosLlanta[1];
                    $rinLlanta = $datosLlanta[2];
    
                    $libroEdicion->setCellValue("D$i", $anchoLlanta);
                    $libroEdicion->setCellValue("E$i", $altoLlanta);
                    $libroEdicion->setCellValue("F$i", $rinLlanta);
                    $modelo = '';
                    $nnn = 1;
                    foreach($descripcionSplitted as $txt){
                        if($nnn > 2){
                            $modelo.= $txt;
                        } 
                        $nnn++;
                    }
                // }   
                
                //aqui comparar el primer recorrido, eliminar los campos de stock y almacen
                $stockArt = 0;
                $cantidadArt = [];
                if ($articulo['ALMACEN'] == 'MATRIX LAURELES') {
                    if($articulo['STOCK'] != '' || $articulo['STOCK'] != null){
                        $libroEdicion->setCellValue("G$i", $articulo['STOCK']);
                        array_push($cantidadArt, $articulo['STOCK']);
                    }
                    else {
                        $libroEdicion->setCellValue("G$i", $stockArt);
                    }
                }
                if ($articulo['ALMACEN'] == 'MATRIX 5A') {
                    if($articulo['STOCK'] != '' || $articulo['STOCK'] != null){
                        $libroEdicion->setCellValue("H$i", $articulo['STOCK']);
                        array_push($cantidadArt, $articulo['STOCK']);
                    }
                    else {
                        $libroEdicion->setCellValue("H$i", $stockArt);
                    }
                }
                if ($articulo['ALMACEN'] == 'MATRIX AMBAR') {
                    if($articulo['STOCK'] != '' || $articulo['STOCK'] != null){
                        $libroEdicion->setCellValue("I$i", $articulo['STOCK']);
                        array_push($cantidadArt, $articulo['STOCK']);
                    }
                    else {
                        $libroEdicion->setCellValue("I$i", $stockArt);
                    }
                }
                if ($articulo['ALMACEN'] == 'MATRIX GENESIS') {
                    if($articulo['STOCK'] != '' || $articulo['STOCK'] != null){
                        $libroEdicion->setCellValue("J$i", $articulo['STOCK']);
                        array_push($cantidadArt, $articulo['STOCK']);
                    }
                    else {
                        $libroEdicion->setCellValue("J$i", $stockArt);
                    }
                }
                if ($articulo['ALMACEN'] == 'MATRIX LIBRAMIENTO') {
                    if($articulo['STOCK'] != '' || $articulo['STOCK'] != null){
                        $libroEdicion->setCellValue("K$i", $articulo['STOCK']);
                        array_push($cantidadArt, $articulo['STOCK']);
                    }
                    else {
                        $libroEdicion->setCellValue("K$i", $stockArt);
                    }
                }
                if ($articulo['ALMACEN'] == 'MATRIX PALMERAS') {
                    if($articulo['STOCK'] != '' || $articulo['STOCK'] != null){
                        $libroEdicion->setCellValue("L$i", $articulo['STOCK']);
                        array_push($cantidadArt, $articulo['STOCK']);
                    }
                    else {
                        $libroEdicion->setCellValue("L$i", $stockArt);
                    }
                }
                if ($articulo['ALMACEN'] == 'CEDIM BOULEVARD') {
                    if($articulo['STOCK'] != '' || $articulo['STOCK'] != null){
                        $libroEdicion->setCellValue("M$i", $articulo['STOCK']);
                        array_push($cantidadArt, $articulo['STOCK']);
                    }
                    else {
                        $libroEdicion->setCellValue("M$i", $stockArt);
                    }
                }
                if ($articulo['ALMACEN'] == 'MATRIX SAN RAMON') {
                    if($articulo['STOCK'] != '' || $articulo['STOCK'] != null){
                        $libroEdicion->setCellValue("N$i", $articulo['STOCK']);
                        array_push($cantidadArt, $articulo['STOCK']);
                    }
                    else {
                        $libroEdicion->setCellValue("N$i", $stockArt);
                    }
                }
                if ($articulo['ALMACEN'] == 'MATRIX MERC ALTOS') {
                    if($articulo['STOCK'] != '' || $articulo['STOCK'] != null){
                        $libroEdicion->setCellValue("O$i", $articulo['STOCK']);
                        array_push($cantidadArt, $articulo['STOCK']);
                    }
                    else {
                        $libroEdicion->setCellValue("O$i", $stockArt);
                    }
                }
                if ($articulo['ALMACEN'] == 'MATRIX 2AOTE') {
                    if($articulo['STOCK'] != '' || $articulo['STOCK'] != null){
                        $libroEdicion->setCellValue("P$i", $articulo['STOCK']);
                        array_push($cantidadArt, $articulo['STOCK']);
                    }
                    else {
                        $libroEdicion->setCellValue("P$i", $stockArt);
                    }
                }
                if ($articulo['ALMACEN'] == 'MATRIX BLVD') {
                    if($articulo['STOCK'] != '' || $articulo['STOCK'] != null){
                        $libroEdicion->setCellValue("Q$i", $articulo['STOCK']);
                        array_push($cantidadArt, $articulo['STOCK']);
                    }
                    else {
                        $libroEdicion->setCellValue("Q$i", $stockArt);
                    }
                }
                if ($articulo['ALMACEN'] == 'CEDIM TAPA') {
                    if($articulo['STOCK'] != '' || $articulo['STOCK'] != null){
                        $libroEdicion->setCellValue("R$i", $articulo['STOCK']);
                        array_push($cantidadArt, $articulo['STOCK']);
                    }
                    else {
                        $libroEdicion->setCellValue("R$i", $stockArt);
                    }
                }
                        
                $totalArt = array_sum($cantidadArt);
                $libroEdicion->setCellValue("S$i","=SUM(G$i:R$i)");
    
                $cantidadArt = [];
                
                //STOCK
                // if ( $displayStock == 1) {
                // 	$libroEdicion->setCellValue("I$i",$articulo['STOCK']);
                // 	$libroEdicion->getStyle("I$i")->applyFromArray( $this->centrarTexto);
                // }
    
    
                //
                $expMedida = explode(" ", $articulo['DESCRIP']);
                $configPrecios = $articulos->getPoliticaPrecios($articulo['FAMILIA'],$expMedida[0],'');
    
                if(is_array($configPrecios)){
                    // if($articulo['FAMILIA']=='LLANTA'){
                        if(!in_array($articulo['IDARTICULO'],$arrIDS)){
                            $arrIDS[] = $articulo['IDARTICULO'];
                            $trend++;
                            //Formateo de espacios
                            $txtFile.= $this->formatEspacios($articulo['IDARTICULO'],20);
                            $txtFile.= $this->formatEspacios(number_format($articulo['PREMECOS'],2,'.',''),15);
                            $txtFile.= $this->formatEspacios(number_format((($articulo['PREMECOS']*1.16) * (1 + ($configPrecios['pvp1']/100))),2,'.',''),15);
                            $txtFile.= $this->formatEspacios(number_format((($articulo['PREMECOS']*1.16) * (1 + ($configPrecios['pvp2']/100))),2,'.',''),15);
                            $txtFile.= $this->formatEspacios(number_format((($articulo['PREMECOS']*1.16) * (1 + ($configPrecios['pvp3']/100))),2,'.',''),15);
                            $txtFile.= $this->formatEspacios(number_format((($articulo['PREMECOS']*1.16) * (1 + ($configPrecios['pvp4']/100))),2,'.',''),15);
                            $txtFile.= $this->formatEspacios(number_format((($articulo['PREMECOS']*1.16) * (1 + ($configPrecios['pvp5']/100))),2,'.',''),15);
                            //$txtFile.= chr(13).chr(10);
                        }
                    // }
    
                    $preciosVenta = [];
    
                    $costoPromedioConIva = number_format($articulo['PREMECOS'] * 1.16, 2);
                    
                
                    if(in_array('1',$arrPVPS)){
                        $tmpPVP1 = (float) number_format((($articulo['PREMECOS']*1.16) * (1 + ($configPrecios['pvp1']/100))),2,'.','');
                        $libroEdicion->setCellValue("T$i", $tmpPVP1);
                        array_push( $preciosVenta , [ 'precio' => $tmpPVP1 , 'numero' => 1] );
                        if($utilidad==1){
                            // $libroEdicion->setCellValue("S$i","=ABS((O$i/L$i) - 1)");
                            $costoConIva = round($articulo['PREMECOS'] * 1.16, 2);
                            $resultado = number_format(($tmpPVP1 / $costoConIva) - 1, 2);
                            // $resultado = $resultado * 100;
                            $resultado = abs($resultado);
                            $libroEdicion->setCellValue("U$i", $resultado);
                        }
                    }
                    if(in_array('2',$arrPVPS)){
                        $tmpPVP2 = (float) number_format((($articulo['PREMECOS']*1.16) * (1 + ($configPrecios['pvp2']/100))),2,'.','');
                        array_push( $preciosVenta , [ 'precio' => $tmpPVP2 , 'numero' => 2] );
                        $libroEdicion->setCellValue("V$i", $tmpPVP2);
                        if($utilidad==1){
                            $costoConIva = round($articulo['PREMECOS'] * 1.16, 2);
                            $resultado2 = number_format(($tmpPVP2 / $costoConIva) - 1, 2);
                            // $resultado2 = $resultado2 * 100;
                            $resultado2 = abs($resultado2);
                            $libroEdicion->setCellValue("W$i", $resultado2);
                        }
                    }
                    if(in_array('3',$arrPVPS)){
                        $tmpPVP3 = (float) number_format((($articulo['PREMECOS']*1.16) * (1 + ($configPrecios['pvp3']/100))),2,'.','');
                        array_push( $preciosVenta , [ 'precio' => $tmpPVP3 , 'numero' => 3] );
                        $libroEdicion->setCellValue("X$i", $tmpPVP3);
                        if($utilidad==1){
                            // $libroEdicion->setCellValue("W$i","=ABS((U$i/L$i) - 1)");
                            $costoConIva = round($articulo['PREMECOS'] * 1.16, 2);
                            $resultado3 = number_format(($tmpPVP3 / $costoConIva) - 1, 2);
                            // $resultado3 = $resultado3 * 100;
                            $resultado3 = abs($resultado3);
                            $libroEdicion->setCellValue("Y$i", $resultado3);
                        }
                    }
                    if(in_array('4',$arrPVPS)){
                        $tmpPVP4 = (float) number_format((($articulo['PREMECOS']*1.16) * (1 + ($configPrecios['pvp4']/100))),2,'.','');
                        array_push( $preciosVenta , [ 'precio' => $tmpPVP4 , 'numero' => 4] );
                        $libroEdicion->setCellValue("Z$i", $tmpPVP4);
                        if($utilidad==1){
                        // $libroEdicion->setCellValue("Y$i","=ABS((X$i/L$i) - 1)");
                            $costoConIva = round($articulo['PREMECOS'] * 1.16, 2);
                            $resultado4 = number_format(($tmpPVP4 / $costoConIva) - 1, 2);
                            // $resultado4 = $resultado4 * 100;
                            $resultado4 = abs($resultado4);
                            $libroEdicion->setCellValue("AA$i", $resultado4);
                        }
                    }
                    if(in_array('5',$arrPVPS)){
                        $tmpPVP5 = (float) number_format((($articulo['PREMECOS']*1.16) * (1 + ($configPrecios['pvp5']/100))),2,'.','');
                        array_push( $preciosVenta , [ 'precio' => $tmpPVP5 , 'numero' => 5] );
                        $libroEdicion->setCellValue("AB$i",$tmpPVP5);
                        $libroEdicion->getColumnDimension('AB')->setVisible(false);
                        $libroEdicion->getColumnDimension('AC')->setVisible(false);
                        if($utilidad==1){
                            // $libroEdicion->setCellValue("AA$i","=ABS((AA$i/L$i) - 1)");
                        }
                    }
    
                }
                else{
    
                    if(in_array('1',$arrPVPS)){
                        $libroEdicion->setCellValue("T$i", $articulo['PVP1']);
                        array_push( $preciosVenta , [ 'precio' => $articulo['PVP1'] , 'numero' => 1] );
                        if($utilidad==1){
                            // $libroEdicion->setCellValue("S$i","=ABS((O$i/L$i) - 1)");
                            $costoConIva = round($articulo['PREMECOS'] * 1.16, 2);
                            $resultado = number_format(($articulo['PVP1'] / $costoConIva) - 1, 2);
                            // $resultado = $resultado * 100;
                            $resultado = abs($resultado);
                            $libroEdicion->setCellValue("U$i", $resultado);
                        }
                    }
                    if(in_array('2',$arrPVPS)){
                        array_push( $preciosVenta , [ 'precio' => $articulo['PVP2'] , 'numero' => 2] );
                        $libroEdicion->setCellValue("V$i", $articulo['PVP2']);
                        if($utilidad==1){
                            // $libroEdicion->setCellValue("U$i","=ABS((R$i/L$i) - 1)");
                            $costoConIva = round($articulo['PREMECOS'] * 1.16, 2);
                            $resultado2 = number_format(($$articulo['PVP2'] / $costoConIva) - 1, 2);
                            // $resultado2 = $resultado2 * 100;
                            $resultado2 = abs($resultado2);
                            $libroEdicion->setCellValue("W$i", $resultado2);
                        }
                    }
                    if(in_array('3',$arrPVPS)){
                        array_push( $preciosVenta , [ 'precio' => $articulo['PVP3'] , 'numero' => 3] );
                        $libroEdicion->setCellValue("X$i", $articulo['PVP3']);
                        if($utilidad==1){
                            // $libroEdicion->setCellValue("W$i","=ABS((U$i/L$i) - 1)");
                            $costoConIva = round($articulo['PREMECOS'] * 1.16, 2);
                            $resultado3 = number_format(($articulo['PVP3'] / $costoConIva) - 1, 2);
                            // $resultado3 = $resultado3 * 100;
                            $resultado3 = abs($resultado3);
                            $libroEdicion->setCellValue("Y$i", $resultado3);
                        }
                    }
                    if(in_array('4',$arrPVPS)){
                        array_push( $preciosVenta , [ 'precio' => $articulo['PVP4'] , 'numero' => 4] );
                        $libroEdicion->setCellValue("Z$i",$articulo['PVP4']);   $libroEdicion->getColumnDimension('Y')->setVisible(false);
                        if($utilidad==1){
                        //    $libroEdicion->setCellValue("Y$i","=ABS((X$i/L$i) - 1)");
                        $costoConIva = round($articulo['PREMECOS'] * 1.16, 2);
                            $resultado4 = number_format(($articulo['PVP4'] / $costoConIva) - 1, 2);
                            // $resultado4 = $resultado4 * 100;
                            $resultado4 = abs($resultado4);
                            $libroEdicion->setCellValue("AA$i", $resultado4);
                        }
                    }
                    if(in_array('5',$arrPVPS)){
                        array_push( $preciosVenta , [ 'precio' => $articulo['PVP5'] , 'numero' => 5] );
                        $libroEdicion->setCellValue("AB$i", $articulo['PVP5']);
                        $libroEdicion->getColumnDimension('AB')->setVisible(false);
                        $libroEdicion->getColumnDimension('AC')->setVisible(false);
                        if($utilidad==1){
                            // $libroEdicion->setCellValue("AA$i","=ABS((AA$i/L$i) - 1)");
                        }
                    }  
                }
            }

            ///////////////////
            else{
                $libroEdicion->setCellValue("A$i", $articulo['CODIGOART']);
                $libroEdicion->setCellValue("B$i", mb_convert_encoding($articulo['DESCRIP'], 'UTF-8', 'ISO-8859-1'));
                $libroEdicion->setCellValue("C$i",$articulo['MARCA']);
                // $libroEdicion->setCellValue("D$i",$articulo['MARCA']);
                // $libroEdicion->setCellValue("H$i",$articulo['MARCA']);
    
                // if ( $familia  == 'LLANTA' ) {
                    $descripcionSplitted = explode(" ", utf8_decode($articulo['DESCRIP']));
                    // $libroEdicion->setCellValue("E$i", $descripcionSplitted[0]);
                    $datosLlanta = explode('/', $descripcionSplitted[0]);
                    $anchoLlanta = $datosLlanta[0];
                    $altoLlanta = $datosLlanta[1];
                    $rinLlanta = $datosLlanta[2];
    
                    $libroEdicion->setCellValue("D$i", $anchoLlanta);
                    $libroEdicion->setCellValue("E$i", $altoLlanta);
                    $libroEdicion->setCellValue("F$i", $rinLlanta);
                    $modelo = '';
                    $nnn = 1;
                    foreach($descripcionSplitted as $txt){
                        if($nnn > 2){
                            $modelo.= $txt;
                        } 
                        $nnn++;
                    }
                // }   
                
                //aqui comparar el primer recorrido, eliminar los campos de stock y almacen
                $stockArt = 0;
                $cantidadArt = [];
                if ($articulo['ALMACEN'] == 'MATRIX LAURELES') {
                    if($articulo['STOCK'] != '' || $articulo['STOCK'] != null){
                        $libroEdicion->setCellValue("G$i", $articulo['STOCK']);
                        array_push($cantidadArt, $articulo['STOCK']);
                    }
                    else {
                        $libroEdicion->setCellValue("G$i", $stockArt);
                    }
                }
                if ($articulo['ALMACEN'] == 'MATRIX 5A') {
                    if($articulo['STOCK'] != '' || $articulo['STOCK'] != null){
                        $libroEdicion->setCellValue("H$i", $articulo['STOCK']);
                        array_push($cantidadArt, $articulo['STOCK']);
                    }
                    else {
                        $libroEdicion->setCellValue("H$i", $stockArt);
                    }
                }
                if ($articulo['ALMACEN'] == 'MATRIX AMBAR') {
                    if($articulo['STOCK'] != '' || $articulo['STOCK'] != null){
                        $libroEdicion->setCellValue("I$i", $articulo['STOCK']);
                        array_push($cantidadArt, $articulo['STOCK']);
                    }
                    else {
                        $libroEdicion->setCellValue("I$i", $stockArt);
                    }
                }
                if ($articulo['ALMACEN'] == 'MATRIX GENESIS') {
                    if($articulo['STOCK'] != '' || $articulo['STOCK'] != null){
                        $libroEdicion->setCellValue("J$i", $articulo['STOCK']);
                        array_push($cantidadArt, $articulo['STOCK']);
                    }
                    else {
                        $libroEdicion->setCellValue("J$i", $stockArt);
                    }
                }
                if ($articulo['ALMACEN'] == 'MATRIX LIBRAMIENTO') {
                    if($articulo['STOCK'] != '' || $articulo['STOCK'] != null){
                        $libroEdicion->setCellValue("K$i", $articulo['STOCK']);
                        array_push($cantidadArt, $articulo['STOCK']);
                    }
                    else {
                        $libroEdicion->setCellValue("K$i", $stockArt);
                    }
                }
                if ($articulo['ALMACEN'] == 'MATRIX PALMERAS') {
                    if($articulo['STOCK'] != '' || $articulo['STOCK'] != null){
                        $libroEdicion->setCellValue("L$i", $articulo['STOCK']);
                        array_push($cantidadArt, $articulo['STOCK']);
                    }
                    else {
                        $libroEdicion->setCellValue("L$i", $stockArt);
                    }
                }
                if ($articulo['ALMACEN'] == 'CEDIM BOULEVARD') {
                    if($articulo['STOCK'] != '' || $articulo['STOCK'] != null){
                        $libroEdicion->setCellValue("M$i", $articulo['STOCK']);
                        array_push($cantidadArt, $articulo['STOCK']);
                    }
                    else {
                        $libroEdicion->setCellValue("M$i", $stockArt);
                    }
                }
                if ($articulo['ALMACEN'] == 'MATRIX SAN RAMON') {
                    if($articulo['STOCK'] != '' || $articulo['STOCK'] != null){
                        $libroEdicion->setCellValue("N$i", $articulo['STOCK']);
                        array_push($cantidadArt, $articulo['STOCK']);
                    }
                    else {
                        $libroEdicion->setCellValue("N$i", $stockArt);
                    }
                }
                if ($articulo['ALMACEN'] == 'MATRIX MERC ALTOS') {
                    if($articulo['STOCK'] != '' || $articulo['STOCK'] != null){
                        $libroEdicion->setCellValue("O$i", $articulo['STOCK']);
                        array_push($cantidadArt, $articulo['STOCK']);
                    }
                    else {
                        $libroEdicion->setCellValue("O$i", $stockArt);
                    }
                }
                if ($articulo['ALMACEN'] == 'MATRIX 2AOTE') {
                    if($articulo['STOCK'] != '' || $articulo['STOCK'] != null){
                        $libroEdicion->setCellValue("P$i", $articulo['STOCK']);
                        array_push($cantidadArt, $articulo['STOCK']);
                    }
                    else {
                        $libroEdicion->setCellValue("P$i", $stockArt);
                    }
                }
                if ($articulo['ALMACEN'] == 'MATRIX BLVD') {
                    if($articulo['STOCK'] != '' || $articulo['STOCK'] != null){
                        $libroEdicion->setCellValue("Q$i", $articulo['STOCK']);
                        array_push($cantidadArt, $articulo['STOCK']);
                    }
                    else {
                        $libroEdicion->setCellValue("Q$i", $stockArt);
                    }
                }
                if ($articulo['ALMACEN'] == 'CEDIM TAPA') {
                    if($articulo['STOCK'] != '' || $articulo['STOCK'] != null){
                        $libroEdicion->setCellValue("R$i", $articulo['STOCK']);
                        array_push($cantidadArt, $articulo['STOCK']);
                    }
                    else {
                        $libroEdicion->setCellValue("R$i", $stockArt);
                    }
                }
                        
                $totalArt = array_sum($cantidadArt);
                $libroEdicion->setCellValue("S$i","=SUM(G$i:R$i)");
    
                $cantidadArt = [];
                
                //STOCK
                // if ( $displayStock == 1) {
                // 	$libroEdicion->setCellValue("I$i",$articulo['STOCK']);
                // 	$libroEdicion->getStyle("I$i")->applyFromArray( $this->centrarTexto);
                // }
    
    
                //
                $expMedida = explode(" ", $articulo['DESCRIP']);
                $configPrecios = $articulos->getPoliticaPrecios($articulo['FAMILIA'],$expMedida[0],'');
    
                if(is_array($configPrecios)){
                    // if($articulo['FAMILIA']=='LLANTA'){
                        if(!in_array($articulo['IDARTICULO'],$arrIDS)){
                            $arrIDS[] = $articulo['IDARTICULO'];
                            $trend++;
                            //Formateo de espacios
                            $txtFile.= $this->formatEspacios($articulo['IDARTICULO'],20);
                            $txtFile.= $this->formatEspacios(number_format($articulo['PREMECOS'],2,'.',''),15);
                            $txtFile.= $this->formatEspacios(number_format((($articulo['PREMECOS']*1.16) * (1 + ($configPrecios['pvp1']/100))),2,'.',''),15);
                            $txtFile.= $this->formatEspacios(number_format((($articulo['PREMECOS']*1.16) * (1 + ($configPrecios['pvp2']/100))),2,'.',''),15);
                            $txtFile.= $this->formatEspacios(number_format((($articulo['PREMECOS']*1.16) * (1 + ($configPrecios['pvp3']/100))),2,'.',''),15);
                            $txtFile.= $this->formatEspacios(number_format((($articulo['PREMECOS']*1.16) * (1 + ($configPrecios['pvp4']/100))),2,'.',''),15);
                            $txtFile.= $this->formatEspacios(number_format((($articulo['PREMECOS']*1.16) * (1 + ($configPrecios['pvp5']/100))),2,'.',''),15);
                            //$txtFile.= chr(13).chr(10);
                        }
                    // }
    
                    $preciosVenta = [];
    
                    $costoPromedioConIva = number_format($articulo['PREMECOS'] * 1.16, 2);
                    
                
                    if(in_array('1',$arrPVPS)){
                        $tmpPVP1 = (float) number_format((($articulo['PREMECOS']*1.16) * (1 + ($configPrecios['pvp1']/100))),2,'.','');
                        $libroEdicion->setCellValue("T$i", $tmpPVP1);
                        array_push( $preciosVenta , [ 'precio' => $tmpPVP1 , 'numero' => 1] );
                        if($utilidad==1){
                            // $libroEdicion->setCellValue("S$i","=ABS((O$i/L$i) - 1)");
                            $costoConIva = round($articulo['PREMECOS'] * 1.16, 2);
                            $resultado = number_format(($tmpPVP1 / $costoConIva) - 1, 2);
                            // $resultado = $resultado * 100;
                            $resultado = abs($resultado);
                            $libroEdicion->setCellValue("U$i", $resultado);
                        }
                    }
                    if(in_array('2',$arrPVPS)){
                        $tmpPVP2 = (float) number_format((($articulo['PREMECOS']*1.16) * (1 + ($configPrecios['pvp2']/100))),2,'.','');
                        array_push( $preciosVenta , [ 'precio' => $tmpPVP2 , 'numero' => 2] );
                        $libroEdicion->setCellValue("V$i", $tmpPVP2);
                        if($utilidad==1){
                            $costoConIva = round($articulo['PREMECOS'] * 1.16, 2);
                            $resultado2 = number_format(($tmpPVP2 / $costoConIva) - 1, 2);
                            // $resultado2 = $resultado2 * 100;
                            $resultado2 = abs($resultado2);
                            $libroEdicion->setCellValue("W$i", $resultado2);
                        }
                    }
                    if(in_array('3',$arrPVPS)){
                        $tmpPVP3 = (float) number_format((($articulo['PREMECOS']*1.16) * (1 + ($configPrecios['pvp3']/100))),2,'.','');
                        array_push( $preciosVenta , [ 'precio' => $tmpPVP3 , 'numero' => 3] );
                        $libroEdicion->setCellValue("X$i", $tmpPVP3);
                        if($utilidad==1){
                            // $libroEdicion->setCellValue("W$i","=ABS((U$i/L$i) - 1)");
                            $costoConIva = round($articulo['PREMECOS'] * 1.16, 2);
                            $resultado3 = number_format(($tmpPVP3 / $costoConIva) - 1, 2);
                            // $resultado3 = $resultado3 * 100;
                            $resultado3 = abs($resultado3);
                            $libroEdicion->setCellValue("Y$i", $resultado3);
                        }
                    }
                    if(in_array('4',$arrPVPS)){
                        $tmpPVP4 = (float) number_format((($articulo['PREMECOS']*1.16) * (1 + ($configPrecios['pvp4']/100))),2,'.','');
                        array_push( $preciosVenta , [ 'precio' => $tmpPVP4 , 'numero' => 4] );
                        $libroEdicion->setCellValue("Z$i", $tmpPVP4);
                        if($utilidad==1){
                        // $libroEdicion->setCellValue("Y$i","=ABS((X$i/L$i) - 1)");
                            $costoConIva = round($articulo['PREMECOS'] * 1.16, 2);
                            $resultado4 = number_format(($tmpPVP4 / $costoConIva) - 1, 2);
                            // $resultado4 = $resultado4 * 100;
                            $resultado4 = abs($resultado4);
                            $libroEdicion->setCellValue("AA$i", $resultado4);
                        }
                    }
                    if(in_array('5',$arrPVPS)){
                        $tmpPVP5 = (float) number_format((($articulo['PREMECOS']*1.16) * (1 + ($configPrecios['pvp5']/100))),2,'.','');
                        array_push( $preciosVenta , [ 'precio' => $tmpPVP5 , 'numero' => 5] );
                        $libroEdicion->setCellValue("AB$i",$tmpPVP5);
                        $libroEdicion->getColumnDimension('AB')->setVisible(false);
                        $libroEdicion->getColumnDimension('AC')->setVisible(false);
                        if($utilidad==1){
                            // $libroEdicion->setCellValue("AA$i","=ABS((AA$i/L$i) - 1)");
                        }
                    }
    
                }
                else{
    
                    if(in_array('1',$arrPVPS)){
                        $libroEdicion->setCellValue("T$i", $articulo['PVP1']);
                        array_push( $preciosVenta , [ 'precio' => $articulo['PVP1'] , 'numero' => 1] );
                        if($utilidad==1){
                            // $libroEdicion->setCellValue("S$i","=ABS((O$i/L$i) - 1)");
                            $costoConIva = round($articulo['PREMECOS'] * 1.16, 2);
                            $resultado = number_format(($articulo['PVP1'] / $costoConIva) - 1, 2);
                            // $resultado = $resultado * 100;
                            $resultado = abs($resultado);
                            $libroEdicion->setCellValue("U$i", $resultado);
                        }
                    }
                    if(in_array('2',$arrPVPS)){
                        array_push( $preciosVenta , [ 'precio' => $articulo['PVP2'] , 'numero' => 2] );
                        $libroEdicion->setCellValue("V$i", $articulo['PVP2']);
                        if($utilidad==1){
                            // $libroEdicion->setCellValue("U$i","=ABS((R$i/L$i) - 1)");
                            $costoConIva = round($articulo['PREMECOS'] * 1.16, 2);
                            $resultado2 = number_format(($$articulo['PVP2'] / $costoConIva) - 1, 2);
                            // $resultado2 = $resultado2 * 100;
                            $resultado2 = abs($resultado2);
                            $libroEdicion->setCellValue("W$i", $resultado2);
                        }
                    }
                    if(in_array('3',$arrPVPS)){
                        array_push( $preciosVenta , [ 'precio' => $articulo['PVP3'] , 'numero' => 3] );
                        $libroEdicion->setCellValue("X$i", $articulo['PVP3']);
                        if($utilidad==1){
                            // $libroEdicion->setCellValue("W$i","=ABS((U$i/L$i) - 1)");
                            $costoConIva = round($articulo['PREMECOS'] * 1.16, 2);
                            $resultado3 = number_format(($articulo['PVP3'] / $costoConIva) - 1, 2);
                            // $resultado3 = $resultado3 * 100;
                            $resultado3 = abs($resultado3);
                            $libroEdicion->setCellValue("Y$i", $resultado3);
                        }
                    }
                    if(in_array('4',$arrPVPS)){
                        array_push( $preciosVenta , [ 'precio' => $articulo['PVP4'] , 'numero' => 4] );
                        $libroEdicion->setCellValue("Z$i",$articulo['PVP4']);   $libroEdicion->getColumnDimension('Y')->setVisible(false);
                        if($utilidad==1){
                        //    $libroEdicion->setCellValue("Y$i","=ABS((X$i/L$i) - 1)");
                            $costoConIva = round($articulo['PREMECOS'] * 1.16, 2);
                            $resultado4 = number_format(($articulo['PVP4'] / $costoConIva) - 1, 2);
                            // $resultado4 = $resultado4 * 100;
                            $resultado4 = abs($resultado4);
                            $libroEdicion->setCellValue("AA$i", $resultado4);
                        }
                    }
                    if(in_array('5',$arrPVPS)){
                        array_push( $preciosVenta , [ 'precio' => $articulo['PVP5'] , 'numero' => 5] );
                        $libroEdicion->setCellValue("AB$i", $articulo['PVP5']);
                        $libroEdicion->getColumnDimension('AB')->setVisible(false);
                        $libroEdicion->getColumnDimension('AC')->setVisible(false);
                        if($utilidad==1){
                            // $libroEdicion->setCellValue("AA$i","=ABS((AA$i/L$i) - 1)");
                        }
                    }  
                }
            }

            $libroEdicion->setCellValue("AD$i", $articulo['DIASINV']);
            $libroEdicion->setCellValue("AE$i", $articulo['FECULT']);
            $libroEdicion->setCellValue("AF$i", $articulo['PREMECOS']);


            $libroEdicion->getStyle("T$i")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00");
            $libroEdicion->getStyle("V$i")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00");
            $libroEdicion->getStyle("X$i")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00");
            $libroEdicion->getStyle("Z$i")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00");
            $libroEdicion->getStyle("AB$i")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00");
            $libroEdicion->getStyle("AF$i")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00");

            $libroEdicion->getStyle("U$i")->getNumberFormat()->setFormatCode('0%;[Red]-0%');
            $libroEdicion->getStyle("W$i")->getNumberFormat()->setFormatCode('0%;[Red]-0%');
            $libroEdicion->getStyle("Y$i")->getNumberFormat()->setFormatCode('0%;[Red]-0%');
            $libroEdicion->getStyle("AA$i")->getNumberFormat()->setFormatCode('0%;[Red]-0%');
            $libroEdicion->getStyle("AC$i")->getNumberFormat()->setFormatCode('0%;[Red]-0%');

            $libroEdicion->getRowDimension($i)->setRowHeight(30);

            if ( !$colorRow) {
                $colorRow = true;
            }else{
                $colorRow = false;
            }
            $i++; 
        }


        $libroEdicion->getColumnDimension('A')->setAutoSize(false);
        $libroEdicion->getColumnDimension('A')->setWidth("15");
        $libroEdicion->getColumnDimension('B')->setAutoSize(false);
        $libroEdicion->getColumnDimension('B')->setWidth("60");
        $libroEdicion->getColumnDimension('C')->setAutoSize(false);
        $libroEdicion->getColumnDimension('C')->setWidth("15");
        $libroEdicion->getColumnDimension('D')->setAutoSize(false);
        $libroEdicion->getColumnDimension('D')->setWidth("15");
        $libroEdicion->getColumnDimension('E')->setAutoSize(false);
        $libroEdicion->getColumnDimension('E')->setWidth("15");
        $libroEdicion->getColumnDimension('F')->setAutoSize(false);
        $libroEdicion->getColumnDimension('F')->setWidth("15");
        $libroEdicion->getColumnDimension('G')->setAutoSize(false);
        $libroEdicion->getColumnDimension('G')->setWidth("15");
        $libroEdicion->getColumnDimension('H')->setAutoSize(false);
        $libroEdicion->getColumnDimension('H')->setWidth("15");
        $libroEdicion->getColumnDimension('I')->setAutoSize(false);
        $libroEdicion->getColumnDimension('I')->setWidth("15");
        $libroEdicion->getColumnDimension('J')->setAutoSize(false);
        $libroEdicion->getColumnDimension('J')->setWidth("15");
        $libroEdicion->getColumnDimension('K')->setAutoSize(false);
        $libroEdicion->getColumnDimension('K')->setWidth("15");
        $libroEdicion->getColumnDimension('L')->setAutoSize(false);
        $libroEdicion->getColumnDimension('L')->setWidth("15");
        $libroEdicion->getColumnDimension('M')->setAutoSize(false);
        $libroEdicion->getColumnDimension('M')->setWidth("15");
        $libroEdicion->getColumnDimension('N')->setAutoSize(false);
        $libroEdicion->getColumnDimension('N')->setWidth("15");
        $libroEdicion->getColumnDimension('O')->setAutoSize(false);
        $libroEdicion->getColumnDimension('O')->setWidth("15");
        $libroEdicion->getColumnDimension('P')->setAutoSize(false);
        $libroEdicion->getColumnDimension('P')->setWidth("15");
        $libroEdicion->getColumnDimension('Q')->setAutoSize(false);
        $libroEdicion->getColumnDimension('Q')->setWidth("15");
        $libroEdicion->getColumnDimension('R')->setAutoSize(false);
        $libroEdicion->getColumnDimension('R')->setWidth("15");
        $libroEdicion->getColumnDimension('AD')->setAutoSize(false);
        $libroEdicion->getColumnDimension('AD')->setWidth("15");
        $libroEdicion->getColumnDimension('AE')->setAutoSize(false);
        $libroEdicion->getColumnDimension('AE')->setWidth("20");
        $libroEdicion->getColumnDimension('AF')->setAutoSize(false);
        $libroEdicion->getColumnDimension('AF')->setWidth("20");
        

        $libroEdicion->setShowGridlines(false);
        $valuadoTerminado = new PHPExcel_Writer_Excel2007( $this->libro );
        $valuadoTerminado->setPreCalculateFormulas(true);
        $valuadoTerminado->save("VALUADO TEST $familia $id.xlsx");

        $articulos->close();

    }

    public function generarRines($data)
    {
        extract($data);
		$arrPVPS = explode(',',$pvps);
        
        $txtFile = '';
        $arrIDS = array();
        
        $articulos = new Articulos;
        $almacen = "%";
        if( $almacen != "%" ){
            $infoAlmacen = $articulos->getAlmacenes($almacen);
            $almacen = $infoAlmacen[0]->DESCRIPCION;
        }

        $stock = 0;

        $listaArticulosDesagrupados = $articulos->getValuadoSucursalMysql( $familia, $almacen) ;

        $listaArticulosAgrupados = array();
        if ( $group == 0) {
            foreach($listaArticulosDesagrupados as $i => $articulo){
                $listaArticulosDesagrupados[$i] = $this->configuraPrecios($articulo);                
            }
        } else {
            foreach ($listaArticulosDesagrupados as $i => $articulo) {
                $stock += $articulo['STOCK'];
                if ( ! isset( $listaArticulosAgrupados[$articulo['CODIGOART']]['STOCK']  )  ){
                    $listaArticulosAgrupados[$articulo['CODIGOART']] = $this->configuraPrecios( $articulo);
                }else{
                    $listaArticulosAgrupados[$articulo['CODIGOART']]['STOCK']  += $articulo['STOCK'];
                }
                //echo $listaArticulosAgrupados[$articulo->CODIGOART]['DIASINV'];
                //die();
            }
            $listaArticulosDesagrupados = $listaArticulosAgrupados;
        }


        
        $libroEdicion = $this->libro->getActiveSheet();


        $libroEdicion->setAutoFilter("A7:F7");

        $libroEdicion->setCellValue("A7",'ARTÍCULO'); // CB
        $libroEdicion->setCellValue("B7",'DESCRIPCION');
        $libroEdicion->setCellValue("C7",'MARCA');


        // $libroEdicion->setAutoFilter("D7:F7");

        $libroEdicion->setCellValue("D7","ANCHO");
        $libroEdicion->setCellValue("E7",'DIÁMETRO');
        $libroEdicion->setCellValue("F7",'BARRENACIÓN');

        $libroEdicion->setCellValue("G7", 'TUXTLA');
        $libroEdicion->setCellValue("M7", 'SAN CRISTOBAL');
        $libroEdicion->setCellValue("O7", 'TAPACHULA');

        $libroEdicion->setCellValue("G8", 'LAURELES');
        $libroEdicion->setCellValue("H8", 'QUINTA NTE');
        $libroEdicion->setCellValue("I8", 'ÁMBAR');
        $libroEdicion->setCellValue("J8", 'GÉNESIS');
        $libroEdicion->setCellValue("K8", 'LIBRAMIENTO');
        $libroEdicion->setCellValue("L8", 'PALMERAS');
        $libroEdicion->setCellValue("M8", 'CEDIM BLVD');

        $libroEdicion->setCellValue("N8", 'SAN RAMÓN');
        $libroEdicion->setCellValue("O8", 'MERCALTOS');

        $libroEdicion->setCellValue("P8", 'CENTRO');
        $libroEdicion->setCellValue("Q8", 'BOULEVARD');
        $libroEdicion->setCellValue("R8", 'CEDIM TAPA');
        
        $libroEdicion->setCellValue("S7", 'TOTAL');
        
        $libroEdicion->setCellValue("T7", 'PVP1');
        $libroEdicion->setCellValue("U7", 'PVP1xJGO');
        $libroEdicion->setCellValue("V7", '%');
        $libroEdicion->setCellValue("W7", 'PVP2');
        $libroEdicion->setCellValue("X7", 'PVP2xJGO');
        $libroEdicion->setCellValue("Y7", '%');
        $libroEdicion->setCellValue("Z7", 'PVP3');
        $libroEdicion->setCellValue("AA7", 'PVP3xJGO');
        $libroEdicion->setCellValue("AB7", '%');
        $libroEdicion->setCellValue("AC7", 'PVP4');
        $libroEdicion->setCellValue("AD7", 'PVP4xJGO');
        $libroEdicion->setCellValue("AE7", '%');
        $libroEdicion->setCellValue("AF7", 'PVP5');
        $libroEdicion->setCellValue("AG7", 'PVP5xJGO');
        $libroEdicion->setCellValue("AH7", '%');
        $libroEdicion->setCellValue("AI7", 'DIAS INV.');
        $libroEdicion->setCellValue("AJ7", 'FECHA ULT');
        $libroEdicion->setCellValue("AK7", 'CTO ULT COMPRA');

        $libroEdicion->mergeCells('A7:A8');
        $libroEdicion->mergeCells('B7:B8');
        $libroEdicion->mergeCells('C7:C8');

        $libroEdicion->mergeCells('G7:M7');
        $libroEdicion->mergeCells('N7:O7');
        $libroEdicion->mergeCells('P7:R7');
        $libroEdicion->mergeCells('S7:S8');

        $libroEdicion->getStyle("A7:AK7")->applyFromArray( $this->labelBold);
        $libroEdicion->getStyle("A7:AK7")->applyFromArray( $this->centrarTexto);
        $libroEdicion->getStyle("A7:AK7")->applyFromArray( $this->setColorText('ffffff', 11));
        $libroEdicion->getStyle("A7:AK7")->getFill()->applyFromArray( $this->setColorFill("073763"));


       
    

        for ($i=1; $i <= 9 ; $i++) { 
            $libroEdicion->freezePane('A'.$i);
        }        
        
        $trend = 0;
        $i = 9;
        $colorRow = false;
        foreach ($listaArticulosDesagrupados as $codigo => $articulo) {
            $nombreArt[$i] = $articulo['DESCRIP'];
            $codigoArt[$i] = $articulo['CODIGOART'];
            $marcaArt[$i]= $articulo['MARCA'];


            if ( $colorRow) {
                $libroEdicion->getStyle("A$i:AK$i")->getFill()->applyFromArray( $this->setColorFill("f0f5f5")  );
            }
			$libroEdicion->getStyle("A$i:AK$i")->getAlignment()->applyFromArray(array('vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,));


            if($nombreArt[$i-1] == $articulo['DESCRIP'] && $codigoArt[$i-1] == $articulo['CODIGOART'] &&  $marcaArt[$i-1] == $articulo['MARCA']){
                $i = $i-1;
                $libroEdicion->setCellValue("A$i", $articulo['CODIGOART']);
                $libroEdicion->setCellValue("B$i", mb_convert_encoding($articulo['DESCRIP'], 'UTF-8', 'ISO-8859-1'));
                $libroEdicion->setCellValue("C$i",$articulo['MARCA']);
                // $libroEdicion->setCellValue("D$i",$articulo['MARCA']);
                // $libroEdicion->setCellValue("H$i",$articulo['MARCA']);
    
                // if ( $familia  == 'RIN' ) {
                    $descripcionSplitted = explode(" ", utf8_decode($articulo['DESCRIP']));
    
                    $anchoExtrac = explode("X", $descripcionSplitted[0])[1]; 
                    $diametroExtract = explode("X", $descripcionSplitted[0])[0];
                    $barrenacionExtract = $descripcionSplitted[1];
    
                    $libroEdicion->setCellValue("D$i",$anchoExtrac);
                    $libroEdicion->setCellValue("E$i",$diametroExtract);
                    $libroEdicion->setCellValue("F$i",$barrenacionExtract);
                    // $libroEdicion->setCellValue("H$i",$descripcionSplitted[2]);
                // }                
                
    
                $stockArt = 0;
                $cantidadArt = [];
                if ($articulo['ALMACEN'] == 'MATRIX LAURELES') {
                    if($articulo['STOCK'] != '' || $articulo['STOCK'] != null){
                        $libroEdicion->setCellValue("G$i", $articulo['STOCK']);
                        array_push($cantidadArt, $articulo['STOCK']);
                    }
                    else {
                        $libroEdicion->setCellValue("G$i", $stockArt);
                    }
                }
                if ($articulo['ALMACEN'] == 'MATRIX 5A') {
                    if($articulo['STOCK'] != '' || $articulo['STOCK'] != null){
                        $libroEdicion->setCellValue("H$i", $articulo['STOCK']);
                        array_push($cantidadArt, $articulo['STOCK']);
                    }
                    else {
                        $libroEdicion->setCellValue("H$i", $stockArt);
                    }
                }
                if ($articulo['ALMACEN'] == 'MATRIX AMBAR') {
                    if($articulo['STOCK'] != '' || $articulo['STOCK'] != null){
                        $libroEdicion->setCellValue("I$i", $articulo['STOCK']);
                        array_push($cantidadArt, $articulo['STOCK']);
                    }
                    else {
                        $libroEdicion->setCellValue("I$i", $stockArt);
                    }
                }
                if ($articulo['ALMACEN'] == 'MATRIX GENESIS') {
                    if($articulo['STOCK'] != '' || $articulo['STOCK'] != null){
                        $libroEdicion->setCellValue("J$i", $articulo['STOCK']);
                        array_push($cantidadArt, $articulo['STOCK']);
                    }
                    else {
                        $libroEdicion->setCellValue("J$i", $stockArt);
                    }
                }
                if ($articulo['ALMACEN'] == 'MATRIX LIBRAMIENTO') {
                    if($articulo['STOCK'] != '' || $articulo['STOCK'] != null){
                        $libroEdicion->setCellValue("K$i", $articulo['STOCK']);
                        array_push($cantidadArt, $articulo['STOCK']);
                    }
                    else {
                        $libroEdicion->setCellValue("K$i", $stockArt);
                    }
                }
                if ($articulo['ALMACEN'] == 'MATRIX PALMERAS') {
                    if($articulo['STOCK'] != '' || $articulo['STOCK'] != null){
                        $libroEdicion->setCellValue("L$i", $articulo['STOCK']);
                        array_push($cantidadArt, $articulo['STOCK']);
                    }
                    else {
                        $libroEdicion->setCellValue("L$i", $stockArt);
                    }
                }
                if ($articulo['ALMACEN'] == 'CEDIM BOULEVARD') {
                    if($articulo['STOCK'] != '' || $articulo['STOCK'] != null){
                        $libroEdicion->setCellValue("M$i", $articulo['STOCK']);
                        array_push($cantidadArt, $articulo['STOCK']);
                    }
                    else {
                        $libroEdicion->setCellValue("M$i", $stockArt);
                    }
                }
                if ($articulo['ALMACEN'] == 'MATRIX SAN RAMON') {
                    if($articulo['STOCK'] != '' || $articulo['STOCK'] != null){
                        $libroEdicion->setCellValue("N$i", $articulo['STOCK']);
                        array_push($cantidadArt, $articulo['STOCK']);
                    }
                    else {
                        $libroEdicion->setCellValue("N$i", $stockArt);
                    }
                }
                if ($articulo['ALMACEN'] == 'MATRIX MERC ALTOS') {
                    if($articulo['STOCK'] != '' || $articulo['STOCK'] != null){
                        $libroEdicion->setCellValue("O$i", $articulo['STOCK']);
                        array_push($cantidadArt, $articulo['STOCK']);
                    }
                    else {
                        $libroEdicion->setCellValue("O$i", $stockArt);
                    }
                }
                if ($articulo['ALMACEN'] == 'MATRIX 2AOTE') {
                    if($articulo['STOCK'] != '' || $articulo['STOCK'] != null){
                        $libroEdicion->setCellValue("P$i", $articulo['STOCK']);
                        array_push($cantidadArt, $articulo['STOCK']);
                    }
                    else {
                        $libroEdicion->setCellValue("P$i", $stockArt);
                    }
                }
                if ($articulo['ALMACEN'] == 'MATRIX BLVD') {
                    if($articulo['STOCK'] != '' || $articulo['STOCK'] != null){
                        $libroEdicion->setCellValue("Q$i", $articulo['STOCK']);
                        array_push($cantidadArt, $articulo['STOCK']);
                    }
                    else {
                        $libroEdicion->setCellValue("Q$i", $stockArt);
                    }
                }
                if ($articulo['ALMACEN'] == 'CEDIM TAPA') {
                    if($articulo['STOCK'] != '' || $articulo['STOCK'] != null){
                        $libroEdicion->setCellValue("R$i", $articulo['STOCK']);
                        array_push($cantidadArt, $articulo['STOCK']);
                    }
                    else {
                        $libroEdicion->setCellValue("R$i", $stockArt);
                    }
                }
                
                $totalArt = array_sum($cantidadArt);
                // $libroEdicion->setCellValue("Q$i","=SUMA(G$i:P$i)");
                $libroEdicion->setCellValue("S$i","=SUM(G$i:R$i)");
    
                $cantidadArt = [];
    
    
                $configPrecios = $articulos->getPoliticaPrecios($articulo['FAMILIA'],$articulo['SUBFAMILIA'],'');
    
                if(is_array($configPrecios)){
                    $preciosVenta = [];
                
                    if(in_array('1',$arrPVPS)){
                        $tmpPVP1 = (float) number_format((($articulo['PREMECOS']*1.16) * (1 + ($configPrecios['pvp1']/100))),2,'.','');
                        $libroEdicion->setCellValue("T$i", $tmpPVP1);
                        $libroEdicion->setCellValue("U$i", $tmpPVP1 * 4);
                        array_push( $preciosVenta , [ 'precio' => $tmpPVP1 , 'numero' => 1] );
                        if($utilidad==1){
                            // $libroEdicion->setCellValue("S$i","=ABS((O$i/L$i) - 1)");
                            $costoConIva = round($articulo['PREMECOS'] * 1.16, 2);
                            $resultado = number_format(($tmpPVP1 / $costoConIva) - 1, 2);
                            // $resultado = $resultado * 100;
                            $resultado = abs($resultado);
                            $libroEdicion->setCellValue("V$i", $resultado);
                        }
                    }
                    if(in_array('2',$arrPVPS)){
                        $tmpPVP2 = (float) number_format((($articulo['PREMECOS']*1.16) * (1 + ($configPrecios['pvp2']/100))),2,'.','');
                        array_push( $preciosVenta , [ 'precio' => $tmpPVP2 , 'numero' => 2] );
                        $libroEdicion->setCellValue("W$i", $tmpPVP2);
                        $libroEdicion->setCellValue("X$i", $tmpPVP2 * 4);
                        if($utilidad==1){
                            // $libroEdicion->setCellValue("U$i","=ABS((R$i/L$i) - 1)");
                            $costoConIva = round($articulo['PREMECOS'] * 1.16, 2);
                            $resultado2 = number_format(($tmpPVP2 / $costoConIva) - 1, 2);
                            // $resultado2 = $resultado2 * 100;
                            $resultado2 = abs($resultado2);
                            $libroEdicion->setCellValue("Y$i", $resultado2);
                        }
                    }
                    if(in_array('3',$arrPVPS)){
                        $tmpPVP3 = (float) number_format((($articulo['PREMECOS']*1.16) * (1 + ($configPrecios['pvp3']/100))),2,'.','');
                        array_push( $preciosVenta , [ 'precio' => $tmpPVP3 , 'numero' => 3] );
                        $libroEdicion->setCellValue("Z$i", $tmpPVP3);
                        $libroEdicion->setCellValue("AA$i", $tmpPVP3 * 4);
                        if($utilidad==1){
                            // $libroEdicion->setCellValue("W$i","=ABS((U$i/L$i) - 1)");
                            $costoConIva = round($articulo['PREMECOS'] * 1.16, 2);
                            $resultado3 = number_format(($tmpPVP3 / $costoConIva) - 1, 2);
                            // $resultado3 = $resultado3 * 100;
                            $resultado3 = abs($resultado3);
                            $libroEdicion->setCellValue("AB$i", $resultado3);
                        }
                    }
                    if(in_array('4',$arrPVPS)){
                        $tmpPVP4 = (float) number_format((($articulo['PREMECOS']*1.16) * (1 + ($configPrecios['pvp4']/100))),2,'.','');
                        array_push( $preciosVenta , [ 'precio' => $tmpPVP4 , 'numero' => 4] );
                        $libroEdicion->setCellValue("AC$i", $tmpPVP4);
                        $libroEdicion->setCellValue("AD$i", $tmpPVP4 * 4);
                        if($utilidad==1){
                        // $libroEdicion->setCellValue("Y$i","=ABS((X$i/L$i) - 1)");
                        $costoConIva = round($articulo['PREMECOS'] * 1.16, 2);
                            $resultado4 = number_format(($tmpPVP4 / $costoConIva) - 1, 2);
                            // $resultado4 = $resultado4 * 100;
                            $resultado4 = abs($resultado4);
                            $libroEdicion->setCellValue("AE$i", $resultado4);
                        }
                    }
                    if(in_array('5',$arrPVPS)){
                        $tmpPVP6 = (float) number_format((($articulo['PREMECOS']*1.16) * (1 + ($configPrecios['pvp5']/100))),2,'.','');
                        array_push( $preciosVenta , [ 'precio' => $tmpPVP5 , 'numero' => 5] );
                        $libroEdicion->setCellValue("AF$i", $tmpPVP5);
                        $libroEdicion->setCellValue("AG$i", $tmpPVP5 * 4);
                        $libroEdicion->getColumnDimension('AF')->setVisible(false);
                        $libroEdicion->getColumnDimension('AG')->setVisible(false);
                        $libroEdicion->getColumnDimension('AH')->setVisible(false);
                        if($utilidad==1){
                            // $libroEdicion->setCellValue("AA$i","=ABS((AA$i/L$i) - 1)");
                        }
                    }
    
                }else{
    
                    if(in_array('1',$arrPVPS)){
                        array_push( $preciosVenta , [ 'precio' => $articulo['PVP1'] , 'numero' => 1] );
                        $libroEdicion->setCellValue("T$i", $articulo['PVP1']);
                        $libroEdicion->setCellValue("U$i", $articulo['PVP1'] * 4);
                        if($utilidad==1){
                            // $libroEdicion->setCellValue("S$i","=ABS((O$i/L$i) - 1)");
                            $costoConIva = round($articulo['PREMECOS'] * 1.16, 2);
                            $resultado = number_format(($articulo['PVP1'] / $costoConIva) - 1, 2);
                            // $resultado = $resultado * 100;
                            $resultado = abs($resultado);
                            $libroEdicion->setCellValue("V$i", $resultado);
                        }
                    }
                    if(in_array('2',$arrPVPS)){
                        array_push( $preciosVenta , [ 'precio' => $articulo['PVP2'] , 'numero' => 2] );
                        $libroEdicion->setCellValue("W$i",$articulo['PVP2']);
                        $libroEdicion->setCellValue("X$i",$articulo['PVP2'] * 4);
                        if($utilidad==1){
                            // $libroEdicion->setCellValue("U$i","=ABS((R$i/L$i) - 1)");
                            $costoConIva = round($articulo['PREMECOS'] * 1.16, 2);
                            $resultado2 = number_format(($articulo['PVP2'] / $costoConIva) - 1, 2);
                            // $resultado2 = $resultado2 * 100;
                            $resultado2 = abs($resultado2);
                            $libroEdicion->setCellValue("Y$i", $resultado2);
                        }
                    }
                    if(in_array('3',$arrPVPS)){
                        array_push( $preciosVenta , [ 'precio' => $articulo['PVP3'] , 'numero' => 3] );
                        $libroEdicion->setCellValue("Z$i",$articulo['PVP3']);
                        $libroEdicion->setCellValue("AA$i",$articulo['PVP3'] * 4);
                        if($utilidad==1){
                            // $libroEdicion->setCellValue("W$i","=ABS((U$i/L$i) - 1)");
                            $costoConIva = round($articulo['PREMECOS'] * 1.16, 2);
                            $resultado3 = number_format(($articulo['PVP3'] / $costoConIva) - 1, 2);
                            // $resultado3 = $resultado3 * 100;
                            $resultado3 = abs($resultado3);
                            $libroEdicion->setCellValue("AB$i", $resultado3);
                        }
                    }
                    if(in_array('4',$arrPVPS)){
                        array_push( $preciosVenta , [ 'precio' => $articulo['PVP4'] , 'numero' => 4] );
                        $libroEdicion->setCellValue("AC$i",$articulo['PVP4']);   
                        $libroEdicion->setCellValue("AD$i",$articulo['PVP4'] * 4);   
                        if($utilidad==1){
                        //    $libroEdicion->setCellValue("Y$i","=ABS((X$i/L$i) - 1)");
                        $costoConIva = round($articulo['PREMECOS'] * 1.16, 2);
                            $resultado4 = number_format(($articulo['PVP4'] / $costoConIva) - 1, 2);
                            // $resultado4 = $resultado4 * 100;
                            $resultado4 = abs($resultado4);
                            $libroEdicion->setCellValue("AE$i", $resultado4);
                        }
                    }
                    if(in_array('5',$arrPVPS)){
                        array_push( $preciosVenta , [ 'precio' => $articulo['PVP5'] , 'numero' => 5] );
                        $libroEdicion->setCellValue("AF$i",$articulo['PVP5']);
                        $libroEdicion->setCellValue("AG$i",$articulo['PVP5'] * 4);
                        $libroEdicion->getColumnDimension('AF')->setVisible(false);
                        $libroEdicion->getColumnDimension('AG')->setVisible(false);
                        $libroEdicion->getColumnDimension('AH')->setVisible(false);
                        if($utilidad==1){
                            // $libroEdicion->setCellValue("AA$i","=ABS((AA$i/L$i) - 1)");
                        }
                    }  
                }
            }
            else{
                $libroEdicion->setCellValue("A$i", $articulo['CODIGOART']);
                $libroEdicion->setCellValue("B$i", mb_convert_encoding($articulo['DESCRIP'], 'UTF-8', 'ISO-8859-1'));
                $libroEdicion->setCellValue("C$i",$articulo['MARCA']);
                // $libroEdicion->setCellValue("D$i",$articulo['MARCA']);
                // $libroEdicion->setCellValue("H$i",$articulo['MARCA']);
    
                // if ( $familia  == 'RIN' ) {
                    $descripcionSplitted = explode(" ", utf8_decode($articulo['DESCRIP']));
    
                    $anchoExtrac = explode("X", $descripcionSplitted[0])[1]; 
                    $diametroExtract = explode("X", $descripcionSplitted[0])[0];
                    $barrenacionExtract = $descripcionSplitted[1];
    
                    $libroEdicion->setCellValue("D$i",$anchoExtrac);
                    $libroEdicion->setCellValue("E$i",$diametroExtract);
                    $libroEdicion->setCellValue("F$i",$barrenacionExtract);
                    // $libroEdicion->setCellValue("H$i",$descripcionSplitted[2]);
                // }                
                
    
                $stockArt = 0;
                $cantidadArt = [];
                if ($articulo['ALMACEN'] == 'MATRIX LAURELES') {
                    if($articulo['STOCK'] != '' || $articulo['STOCK'] != null){
                        $libroEdicion->setCellValue("G$i", $articulo['STOCK']);
                        array_push($cantidadArt, $articulo['STOCK']);
                    }
                    else {
                        $libroEdicion->setCellValue("G$i", $stockArt);
                    }
                }
                if ($articulo['ALMACEN'] == 'MATRIX 5A') {
                    if($articulo['STOCK'] != '' || $articulo['STOCK'] != null){
                        $libroEdicion->setCellValue("H$i", $articulo['STOCK']);
                        array_push($cantidadArt, $articulo['STOCK']);
                    }
                    else {
                        $libroEdicion->setCellValue("H$i", $stockArt);
                    }
                }
                if ($articulo['ALMACEN'] == 'MATRIX AMBAR') {
                    if($articulo['STOCK'] != '' || $articulo['STOCK'] != null){
                        $libroEdicion->setCellValue("I$i", $articulo['STOCK']);
                        array_push($cantidadArt, $articulo['STOCK']);
                    }
                    else {
                        $libroEdicion->setCellValue("I$i", $stockArt);
                    }
                }
                if ($articulo['ALMACEN'] == 'MATRIX GENESIS') {
                    if($articulo['STOCK'] != '' || $articulo['STOCK'] != null){
                        $libroEdicion->setCellValue("J$i", $articulo['STOCK']);
                        array_push($cantidadArt, $articulo['STOCK']);
                    }
                    else {
                        $libroEdicion->setCellValue("J$i", $stockArt);
                    }
                }
                if ($articulo['ALMACEN'] == 'MATRIX LIBRAMIENTO') {
                    if($articulo['STOCK'] != '' || $articulo['STOCK'] != null){
                        $libroEdicion->setCellValue("K$i", $articulo['STOCK']);
                        array_push($cantidadArt, $articulo['STOCK']);
                    }
                    else {
                        $libroEdicion->setCellValue("K$i", $stockArt);
                    }
                }
                if ($articulo['ALMACEN'] == 'MATRIX PALMERAS') {
                    if($articulo['STOCK'] != '' || $articulo['STOCK'] != null){
                        $libroEdicion->setCellValue("L$i", $articulo['STOCK']);
                        array_push($cantidadArt, $articulo['STOCK']);
                    }
                    else {
                        $libroEdicion->setCellValue("L$i", $stockArt);
                    }
                }
                if ($articulo['ALMACEN'] == 'CEDIM BOULEVARD') {
                    if($articulo['STOCK'] != '' || $articulo['STOCK'] != null){
                        $libroEdicion->setCellValue("M$i", $articulo['STOCK']);
                        array_push($cantidadArt, $articulo['STOCK']);
                    }
                    else {
                        $libroEdicion->setCellValue("M$i", $stockArt);
                    }
                }
                if ($articulo['ALMACEN'] == 'MATRIX SAN RAMON') {
                    if($articulo['STOCK'] != '' || $articulo['STOCK'] != null){
                        $libroEdicion->setCellValue("N$i", $articulo['STOCK']);
                        array_push($cantidadArt, $articulo['STOCK']);
                    }
                    else {
                        $libroEdicion->setCellValue("N$i", $stockArt);
                    }
                }
                if ($articulo['ALMACEN'] == 'MATRIX MERC ALTOS') {
                    if($articulo['STOCK'] != '' || $articulo['STOCK'] != null){
                        $libroEdicion->setCellValue("O$i", $articulo['STOCK']);
                        array_push($cantidadArt, $articulo['STOCK']);
                    }
                    else {
                        $libroEdicion->setCellValue("O$i", $stockArt);
                    }
                }
                if ($articulo['ALMACEN'] == 'MATRIX 2AOTE') {
                    if($articulo['STOCK'] != '' || $articulo['STOCK'] != null){
                        $libroEdicion->setCellValue("P$i", $articulo['STOCK']);
                        array_push($cantidadArt, $articulo['STOCK']);
                    }
                    else {
                        $libroEdicion->setCellValue("P$i", $stockArt);
                    }
                }
                if ($articulo['ALMACEN'] == 'MATRIX BLVD') {
                    if($articulo['STOCK'] != '' || $articulo['STOCK'] != null){
                        $libroEdicion->setCellValue("Q$i", $articulo['STOCK']);
                        array_push($cantidadArt, $articulo['STOCK']);
                    }
                    else {
                        $libroEdicion->setCellValue("Q$i", $stockArt);
                    }
                }
                if ($articulo['ALMACEN'] == 'CEDIM TAPA') {
                    if($articulo['STOCK'] != '' || $articulo['STOCK'] != null){
                        $libroEdicion->setCellValue("R$i", $articulo['STOCK']);
                        array_push($cantidadArt, $articulo['STOCK']);
                    }
                    else {
                        $libroEdicion->setCellValue("R$i", $stockArt);
                    }
                }
                
                $totalArt = array_sum($cantidadArt);
                // $libroEdicion->setCellValue("Q$i","=SUMA(G$i:P$i)");
                $libroEdicion->setCellValue("S$i","=SUM(G$i:R$i)");
    
                $cantidadArt = [];
    
    
                $configPrecios = $articulos->getPoliticaPrecios($articulo['FAMILIA'],$articulo['SUBFAMILIA'],'');
    
                if(is_array($configPrecios)){
                    $preciosVenta = [];
                
                    if(in_array('1',$arrPVPS)){
                        $tmpPVP1 = (float) number_format((($articulo['PREMECOS']*1.16) * (1 + ($configPrecios['pvp1']/100))),2,'.','');
                        $libroEdicion->setCellValue("T$i", $tmpPVP1);
                        $libroEdicion->setCellValue("U$i", $tmpPVP1 * 4);
                        array_push( $preciosVenta , [ 'precio' => $tmpPVP1 , 'numero' => 1] );
                        if($utilidad==1){
                            // $libroEdicion->setCellValue("S$i","=ABS((O$i/L$i) - 1)");
                            $costoConIva = round($articulo['PREMECOS'] * 1.16, 2);
                            $resultado = number_format(($tmpPVP1 / $costoConIva) - 1, 2);
                            // $resultado = $resultado * 100;
                            $resultado = abs($resultado);
                            $libroEdicion->setCellValue("V$i", $resultado);
                        }
                    }
                    if(in_array('2',$arrPVPS)){
                        $tmpPVP2 = (float) number_format((($articulo['PREMECOS']*1.16) * (1 + ($configPrecios['pvp2']/100))),2,'.','');
                        array_push( $preciosVenta , [ 'precio' => $tmpPVP2 , 'numero' => 2] );
                        $libroEdicion->setCellValue("W$i", $tmpPVP2);
                        $libroEdicion->setCellValue("X$i", $tmpPVP2 * 4);
                        if($utilidad==1){
                            // $libroEdicion->setCellValue("U$i","=ABS((R$i/L$i) - 1)");
                            $costoConIva = round($articulo['PREMECOS'] * 1.16, 2);
                            $resultado2 = number_format(($tmpPVP2 / $costoConIva) - 1, 2);
                            // $resultado2 = $resultado2 * 100;
                            $resultado2 = abs($resultado2);
                            $libroEdicion->setCellValue("Y$i", $resultado2);
                        }
                    }
                    if(in_array('3',$arrPVPS)){
                        $tmpPVP3 = (float) number_format((($articulo['PREMECOS']*1.16) * (1 + ($configPrecios['pvp3']/100))),2,'.','');
                        array_push( $preciosVenta , [ 'precio' => $tmpPVP3 , 'numero' => 3] );
                        $libroEdicion->setCellValue("Z$i", $tmpPVP3);
                        $libroEdicion->setCellValue("AA$i", $tmpPVP3 * 4);
                        if($utilidad==1){
                            // $libroEdicion->setCellValue("W$i","=ABS((U$i/L$i) - 1)");
                            $costoConIva = round($articulo['PREMECOS'] * 1.16, 2);
                            $resultado3 = number_format(($tmpPVP3 / $costoConIva) - 1, 2);
                            // $resultado3 = $resultado3 * 100;
                            $resultado3 = abs($resultado3);
                            $libroEdicion->setCellValue("AB$i", $resultado3);
                        }
                    }
                    if(in_array('4',$arrPVPS)){
                        $tmpPVP4 = (float) number_format((($articulo['PREMECOS']*1.16) * (1 + ($configPrecios['pvp4']/100))),2,'.','');
                        array_push( $preciosVenta , [ 'precio' => $tmpPVP4 , 'numero' => 4] );
                        $libroEdicion->setCellValue("AC$i", $tmpPVP4);
                        $libroEdicion->setCellValue("AD$i", $tmpPVP4 * 4);
                        if($utilidad==1){
                        // $libroEdicion->setCellValue("Y$i","=ABS((X$i/L$i) - 1)");
                        $costoConIva = round($articulo['PREMECOS'] * 1.16, 2);
                            $resultado4 = number_format(($tmpPVP4 / $costoConIva) - 1, 2);
                            // $resultado4 = $resultado4 * 100;
                            $resultado4 = abs($resultado4);
                            $libroEdicion->setCellValue("AE$i", $resultado4);
                        }
                    }
                    if(in_array('5',$arrPVPS)){
                        $tmpPVP6 = (float) number_format((($articulo['PREMECOS']*1.16) * (1 + ($configPrecios['pvp5']/100))),2,'.','');
                        array_push( $preciosVenta , [ 'precio' => $tmpPVP5 , 'numero' => 5] );
                        $libroEdicion->setCellValue("AF$i", $tmpPVP5);
                        $libroEdicion->setCellValue("AG$i", $tmpPVP5 * 4);
                        $libroEdicion->getColumnDimension('AF')->setVisible(false);
                        $libroEdicion->getColumnDimension('AG')->setVisible(false);
                        $libroEdicion->getColumnDimension('AH')->setVisible(false);
                        if($utilidad==1){
                            // $libroEdicion->setCellValue("AA$i","=ABS((AA$i/L$i) - 1)");
                        }
                    }
    
                }else{
    
                    if(in_array('1',$arrPVPS)){
                        array_push( $preciosVenta , [ 'precio' => $articulo['PVP1'] , 'numero' => 1] );
                        $libroEdicion->setCellValue("T$i", $articulo['PVP1']);
                        $libroEdicion->setCellValue("U$i", $articulo['PVP1'] * 4);
                        if($utilidad==1){
                            // $libroEdicion->setCellValue("S$i","=ABS((O$i/L$i) - 1)");
                            $costoConIva = round($articulo['PREMECOS'] * 1.16, 2);
                            $resultado = number_format(($articulo['PVP1'] / $costoConIva) - 1, 2);
                            // $resultado = $resultado * 100;
                            $resultado = abs($resultado);
                            $libroEdicion->setCellValue("V$i", $resultado);
                        }
                    }
                    if(in_array('2',$arrPVPS)){
                        array_push( $preciosVenta , [ 'precio' => $articulo['PVP2'] , 'numero' => 2] );
                        $libroEdicion->setCellValue("W$i",$articulo['PVP2']);
                        $libroEdicion->setCellValue("X$i",$articulo['PVP2'] * 4);
                        if($utilidad==1){
                            // $libroEdicion->setCellValue("U$i","=ABS((R$i/L$i) - 1)");
                            $costoConIva = round($articulo['PREMECOS'] * 1.16, 2);
                            $resultado2 = number_format(($articulo['PVP2'] / $costoConIva) - 1, 2);
                            // $resultado2 = $resultado2 * 100;
                            $resultado2 = abs($resultado2);
                            $libroEdicion->setCellValue("Y$i", $resultado2);
                        }
                    }
                    if(in_array('3',$arrPVPS)){
                        array_push( $preciosVenta , [ 'precio' => $articulo['PVP3'] , 'numero' => 3] );
                        $libroEdicion->setCellValue("Z$i",$articulo['PVP3']);
                        $libroEdicion->setCellValue("AA$i",$articulo['PVP3'] * 4);
                        if($utilidad==1){
                            // $libroEdicion->setCellValue("W$i","=ABS((U$i/L$i) - 1)");
                            $costoConIva = round($articulo['PREMECOS'] * 1.16, 2);
                            $resultado3 = number_format(($articulo['PVP3'] / $costoConIva) - 1, 2);
                            // $resultado3 = $resultado3 * 100;
                            $resultado3 = abs($resultado3);
                            $libroEdicion->setCellValue("AB$i", $resultado3);
                        }
                    }
                    if(in_array('4',$arrPVPS)){
                        array_push( $preciosVenta , [ 'precio' => $articulo['PVP4'] , 'numero' => 4] );
                        $libroEdicion->setCellValue("AC$i",$articulo['PVP4']);
                        $libroEdicion->setCellValue("AD$i",$articulo['PVP4'] * 4);
                        // $libroEdicion->setCellValue("X$i",$articulo['PVP4']);   $libroEdicion->getColumnDimension('Y')->setVisible(false);
                        if($utilidad==1){
                        //    $libroEdicion->setCellValue("Y$i","=ABS((X$i/L$i) - 1)");
                        $costoConIva = round($articulo['PREMECOS'] * 1.16, 2);
                            $resultado4 = number_format(($articulo['PVP4'] / $costoConIva) - 1, 2);
                            // $resultado4 = $resultado4 * 100;
                            $resultado4 = abs($resultado4);
                            $libroEdicion->setCellValue("AE$i", $resultado4);
                        }
                    }
                    if(in_array('5',$arrPVPS)){
                        array_push( $preciosVenta , [ 'precio' => $articulo['PVP5'] , 'numero' => 5] );
                        $libroEdicion->setCellValue("AF$i",$articulo['PVP5']);
                        $libroEdicion->setCellValue("AG$i",$articulo['PVP5'] * 4);
                        $libroEdicion->getColumnDimension('AF')->setVisible(false);
                        $libroEdicion->getColumnDimension('AG')->setVisible(false);
                        $libroEdicion->getColumnDimension('AH')->setVisible(false);
                        if($utilidad==1){
                            // $libroEdicion->setCellValue("AA$i","=ABS((AA$i/L$i) - 1)");
                        }
                    }  
                }

            }
			// if ( $displayStock == 1) {
			// 	$libroEdicion->setCellValue("I$i",$articulo['STOCK']);
			// 	$libroEdicion->getStyle("I$i")->applyFromArray( $this->centrarTexto);
			// }

            $libroEdicion->setCellValue("AI$i", $articulo['DIASINV']);
            $libroEdicion->setCellValue("AJ$i", $articulo['FECULT']);
            $libroEdicion->setCellValue("AK$i", $articulo['PREMECOS']);


            $libroEdicion->getStyle("T$i")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00");
            $libroEdicion->getStyle("U$i")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00");
            $libroEdicion->getStyle("W$i")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00");
            $libroEdicion->getStyle("X$i")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00");
            $libroEdicion->getStyle("Z$i")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00");
            $libroEdicion->getStyle("AA$i")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00");
            $libroEdicion->getStyle("AC$i")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00");
            $libroEdicion->getStyle("AD$i")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00");
            $libroEdicion->getStyle("AG$i")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00");
            $libroEdicion->getStyle("AH$i")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00");
            $libroEdicion->getStyle("AK$i")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00");

            $libroEdicion->getStyle("V$i")->getNumberFormat()->setFormatCode('0%;[Red]-0%');
            $libroEdicion->getStyle("Y$i")->getNumberFormat()->setFormatCode('0%;[Red]-0%');
            $libroEdicion->getStyle("AB$i")->getNumberFormat()->setFormatCode('0%;[Red]-0%');
            $libroEdicion->getStyle("AE$i")->getNumberFormat()->setFormatCode('0%;[Red]-0%');
            $libroEdicion->getStyle("AH$i")->getNumberFormat()->setFormatCode('0%;[Red]-0%');

            $libroEdicion->getRowDimension($i)->setRowHeight(30);

            if ( !$colorRow) {
                $colorRow = true;
            }else{
                $colorRow = false;
            }
            $i++; 
        }



        $libroEdicion->getColumnDimension('A')->setAutoSize(false);
        $libroEdicion->getColumnDimension('A')->setWidth("15");
        $libroEdicion->getColumnDimension('B')->setAutoSize(false);
        $libroEdicion->getColumnDimension('B')->setWidth("60");
        $libroEdicion->getColumnDimension('C')->setAutoSize(false);
        $libroEdicion->getColumnDimension('C')->setWidth("15");
        $libroEdicion->getColumnDimension('D')->setAutoSize(false);
        $libroEdicion->getColumnDimension('D')->setWidth("15");
        $libroEdicion->getColumnDimension('E')->setAutoSize(false);
        $libroEdicion->getColumnDimension('E')->setWidth("15");
        $libroEdicion->getColumnDimension('F')->setAutoSize(false);
        $libroEdicion->getColumnDimension('F')->setWidth("15");
        $libroEdicion->getColumnDimension('G')->setAutoSize(false);
        $libroEdicion->getColumnDimension('G')->setWidth("15");
        $libroEdicion->getColumnDimension('H')->setAutoSize(false);
        $libroEdicion->getColumnDimension('H')->setWidth("15");
        $libroEdicion->getColumnDimension('I')->setAutoSize(false);
        $libroEdicion->getColumnDimension('I')->setWidth("15");
        $libroEdicion->getColumnDimension('J')->setAutoSize(false);
        $libroEdicion->getColumnDimension('J')->setWidth("15");
        $libroEdicion->getColumnDimension('K')->setAutoSize(false);
        $libroEdicion->getColumnDimension('K')->setWidth("15");
        $libroEdicion->getColumnDimension('L')->setAutoSize(false);
        $libroEdicion->getColumnDimension('L')->setWidth("15");
        $libroEdicion->getColumnDimension('M')->setAutoSize(false);
        $libroEdicion->getColumnDimension('M')->setWidth("15");
        $libroEdicion->getColumnDimension('N')->setAutoSize(false);
        $libroEdicion->getColumnDimension('N')->setWidth("15");
        $libroEdicion->getColumnDimension('O')->setAutoSize(false);
        $libroEdicion->getColumnDimension('O')->setWidth("15");
        $libroEdicion->getColumnDimension('P')->setAutoSize(false);
        $libroEdicion->getColumnDimension('P')->setWidth("15");
        $libroEdicion->getColumnDimension('Q')->setAutoSize(false);
        $libroEdicion->getColumnDimension('Q')->setWidth("15");
        $libroEdicion->getColumnDimension('R')->setAutoSize(false);
        $libroEdicion->getColumnDimension('R')->setWidth("15");
        $libroEdicion->getColumnDimension('S')->setAutoSize(false);
        $libroEdicion->getColumnDimension('S')->setWidth("15");
        $libroEdicion->getColumnDimension('T')->setAutoSize(false);
        $libroEdicion->getColumnDimension('T')->setWidth("15");
        $libroEdicion->getColumnDimension('U')->setAutoSize(false);
        $libroEdicion->getColumnDimension('U')->setWidth("15");
        $libroEdicion->getColumnDimension('V')->setAutoSize(false);
        $libroEdicion->getColumnDimension('V')->setWidth("15");
        $libroEdicion->getColumnDimension('W')->setAutoSize(false);
        $libroEdicion->getColumnDimension('W')->setWidth("15");
        $libroEdicion->getColumnDimension('X')->setAutoSize(false);
        $libroEdicion->getColumnDimension('X')->setWidth("15");
        $libroEdicion->getColumnDimension('Y')->setAutoSize(false);
        $libroEdicion->getColumnDimension('Y')->setWidth("15");
        $libroEdicion->getColumnDimension('Z')->setAutoSize(false);
        $libroEdicion->getColumnDimension('Z')->setWidth("15");
        $libroEdicion->getColumnDimension('AA')->setAutoSize(false);
        $libroEdicion->getColumnDimension('AA')->setWidth("15");
        $libroEdicion->getColumnDimension('AB')->setAutoSize(false);
        $libroEdicion->getColumnDimension('AB')->setWidth("15");
        $libroEdicion->getColumnDimension('AC')->setAutoSize(false);
        $libroEdicion->getColumnDimension('AC')->setWidth("15");
        $libroEdicion->getColumnDimension('AD')->setAutoSize(false);
        $libroEdicion->getColumnDimension('AD')->setWidth("15");
        $libroEdicion->getColumnDimension('AE')->setAutoSize(false);
        $libroEdicion->getColumnDimension('AE')->setWidth("15");
        $libroEdicion->getColumnDimension('AF')->setAutoSize(false);
        $libroEdicion->getColumnDimension('AF')->setWidth("15");
        $libroEdicion->getColumnDimension('AG')->setAutoSize(false);
        $libroEdicion->getColumnDimension('AG')->setWidth("15");
        $libroEdicion->getColumnDimension('AH')->setAutoSize(false);
        $libroEdicion->getColumnDimension('AH')->setWidth("15");
        $libroEdicion->getColumnDimension('AI')->setAutoSize(false);
        $libroEdicion->getColumnDimension('AI')->setWidth("15");
        $libroEdicion->getColumnDimension('AJ')->setAutoSize(false);
        $libroEdicion->getColumnDimension('AJ')->setWidth("20");
        $libroEdicion->getColumnDimension('AK')->setAutoSize(false);
        $libroEdicion->getColumnDimension('AK')->setWidth("20");
        

        $libroEdicion->setShowGridlines(false);
        $valuadoTerminado = new PHPExcel_Writer_Excel2007( $this->libro );
        $valuadoTerminado->setPreCalculateFormulas(true);
        $valuadoTerminado->save("VALUADO TEST $familia $id.xlsx");

        $articulos->close();
    }
}



$valuados = new Valuados;
//$valuados->enviarReportes();
//die();

if ( isset($_GET['opc'])) {
    switch($_GET['opc']){
        case 'getIdsValuado':{
            $reportes = new Reportes;
            echo json_encode( $reportes->getReporte('valuado'));
            break;
        }
        case 'genValuado':{
                echo "
                    <script src='/intranet/assets/js/jquery-1.11.2.min.js'></script>
                    <script>
                            $(document).ready(function(){
                                window.close();
                            });
                    </script>
                ";
                $reportes = new Reportes;
                $detalleReporte = $reportes->getDetalleReporte($_GET['id']);
                // if( $_GET['id'] == 13 ){
                    $valuados->generaReporte($detalleReporte[0]);
                // }
                
            break;
        }
    }
}else {

?>
    <script src="/intranet/assets/js/jquery-1.11.2.min.js"></script>
    <script>
        let ventana = [];
        let codigoResponse = [];
        $.get("/intranet/controladores/reportes/valuados/valuados.php", {opc: 'getIdsValuado'},
            function (data, textStatus, jqXHR) {
                $.each(data, function (i, item) { 
                    window.open("/intranet/controladores/reportes/valuados/valuados.php?opc=genValuado&id="+item.id, "", "width=200,height=100");


                });
                
            },
            "json"
        );
        
    </script>

<?php
}
?>