<?php
// ini_set('memory_limit', '-1');



set_time_limit(0);
ini_set('memory_limit', '20000M');

require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/almacenes/ArticulosPruebas.php";
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
        $libroEdicion->setAutoFilter("A7:AA7");
        $libroEdicion->setCellValue("L3","P.LISTA");
        $libroEdicion->setCellValue("N3","MED. MAY.");
        $libroEdicion->setCellValue("P3","MAYOREO");
        $libroEdicion->setCellValue("R3","MED. MAY.");

        $libroEdicion->setCellValue("A7",'CODIGO');
        $libroEdicion->setCellValue("B7",'DESCRIPCION');
        $libroEdicion->setCellValue("C7",'FAMILIA');
        $libroEdicion->setCellValue("D7",'SUBFAMILIA');
        switch ($familia) {
            case 'LLANTA':
                $libroEdicion->setCellValue("E7","MEDIDA");
                $libroEdicion->setCellValue("F7",'MARCA');
                $libroEdicion->setCellValue("G7",'MODELO');
                break;
            case 'RIN':
                $libroEdicion->setCellValue("E7","TIPO");
                $libroEdicion->setCellValue("F7",'DIAMETRO');
                $libroEdicion->setCellValue("G7",'BARRENACION');                
                break;
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
            //$libroEdicion->setCellValue("Z7",'%');

            $libroEdicion->getStyle("A7:AF7")->applyFromArray( $this->labelBold);
            $libroEdicion->getStyle("A7:AF7")->applyFromArray( $this->centrarTexto);
            $libroEdicion->getStyle("A7:AF7")->applyFromArray( $this->setColorText('ffffff', 11));
            $libroEdicion->getStyle("A7:AF7")->getFill()->applyFromArray( $this->setColorFill("cc0000")  );
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
			$libroEdicion->getStyle("A$i:AA$i")->getAlignment()->applyFromArray(array('vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,));
            $libroEdicion->setCellValue("A$i", $articulo['CODIGOART']);
            $libroEdicion->setCellValue("B$i", mb_convert_encoding($articulo['DESCRIP'], 'UTF-8', 'ISO-8859-1'));
            $libroEdicion->setCellValue("C$i",$articulo['FAMILIA']);
            $libroEdicion->setCellValue("D$i",$articulo['SUBFAMILIA']);

            if ( $familia  == 'LLANTA' ) {
                    $descripcionSplitted = explode(" ", utf8_decode($articulo['DESCRIP']));
                    $libroEdicion->setCellValue("E$i", $descripcionSplitted[0]);
                    $libroEdicion->setCellValue("F$i", $descripcionSplitted[1]); //$articulo['MARCA']);
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
                    $libroEdicion->setCellValue("E$i",utf8_decode($articulo['SUBFAM2']));
                    if ( $familia == 'RIN' ) {
                        $descripcionSplitted = explode(" ", utf8_decode($articulo['DESCRIP']));

                        $subFamiliaExtrac = strpos( $descripcionSplitted[0] , "RIN") !== false ? "RIN ".explode("X", $descripcionSplitted[1])[0] :  $articulo['SUBFAMILIA'] ; 
                        $barrenacionExtract = strpos( $descripcionSplitted[0] , "RIN") !== false ? $descripcionSplitted[1] : $descripcionSplitted[0] ;
                        $diametroExtract = strpos( $descripcionSplitted[0] , "RIN")  !== false ? $descripcionSplitted[2] : $descripcionSplitted[1] ;

                        $libroEdicion->setCellValue("D$i",$subFamiliaExtrac);
                        $libroEdicion->setCellValue("F$i",$diametroExtract);
                        $libroEdicion->setCellValue("G$i",$barrenacionExtract);
                        $libroEdicion->setCellValue("H$i",$articulo['MARCA']);
                    }
                    if ( $familia == 'COLISION' || $familia == 'ACCESORIO') {
                        $libroEdicion->setCellValue("F$i",$articulo['SUBFAM3']);
                        $libroEdicion->setCellValue("G$i",$articulo['SUBFAM4']);
                        $libroEdicion->setCellValue("H$i",$articulo['MARCA']);
                    }else{
                        $libroEdicion->setCellValue("F$i",$articulo['MARCA']);
                        $libroEdicion->setCellValue("H$i",$articulo['SUBFAM3']);
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
            
            
            $preciosVenta = [];

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
            
            $libroEdicion->setCellValue("AD$i",$articulo['DIASINV']);            
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

            //GENERA ARCHIVO PARA VOLCADO EN FIREBIRD CON PRECIOS ACTUALIZADOS POR LAYOUT
            //Valida en la tabla de precios mysql cual es su configuración de precios
            $configPrecios = $articulos->getPoliticaPrecios($articulo['FAMILIA'],$articulo['SUBFAMILIA'],'');
            //var_dump($configPrecios);
            //die(); 
            if(is_array($configPrecios)){
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
            //var_dump($configPrecios);
            //if($trend==10)
            //    die($txtFile); 
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
        $libroEdicion->getColumnDimension('AF')->setAutoSize(false);
        $libroEdicion->getColumnDimension('AF')->setWidth("35");        
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
        $valuadoTerminado->save("VALUADO $familia $id.xlsx");

        $articulos->close();
 
    }
	
	public function enviarReportes( $lista, $correos, $id, $titulo )
    {
        $emailsender = new \phpmailer;
        $emailsender->IsSMTP(); // habilita SMTP
        $emailsender->SMTPDebug = 1; // debugging: 1 = errores y mensajes, 2 = sólo mensajes
        $emailsender->SMTPAuth = true; // auth habilitada
        $emailsender->SMTPSecure = 'ssl'; // transferencia segura REQUERIDA para Gmail
        $emailsender->Host = "smtp.gmail.com";
        $emailsender->Port = 465; // or 587
        $emailsender->IsHTML(true);
        $emailsender->Username = "rhmatrix2019@gmail.com";
        $emailsender->Password = "M@tr1x2017";
        $emailsender->SetFrom("rhmatrix2019@gmail.com");
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
                if( $rwfamilias  != 'COLISION'){
                    if( $rwfamilias  != 'RIN'){
                        //continue;
                    }
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
			//$this->enviarReportes($arrfamilias, $arrcorreos, $id, $titulo);
		// }
		
        
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