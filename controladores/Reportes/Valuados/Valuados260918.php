<?php
// ini_set('memory_limit', '-1');
set_time_limit(0);
ini_set('memory_limit', '20000M');
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/almacenes/Articulos.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/Reportes/Reportes.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/Reportes/prepareExcel.php";


 $cm = \PHPExcel_CachedObjectStorageFactory::cache_in_memory_gzip;
  \PHPExcel_Settings::setCacheStorageMethod($cm);
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
                // $listaArticulosAgrupados[$articulo->CODIGOART] = $articulo;
                //obteniendo los precios de venta del articulo
                $precios = $articulos->getPrecioByArticulo( $item->IDARTICULO );

				$item->DESCRIP =utf8_decode( $item->DESCRIP );
                $item->MARCA = $item->ADIC1;
                $item->ADIC1 = !isset($precios[0]->PVP1) ? 0 : str_replace(',','', str_replace('$','',$precios[0]->PVP1) ) ;
                $item->ADIC2 =  !isset($precios[0]->PVP2) ? 0 : str_replace(',','', str_replace('$','',$precios[0]->PVP2) ) ;
                $item->ADIC3 =  !isset($precios[0]->PVP3) ? 0 : str_replace(',','', str_replace('$','',$precios[0]->PVP3) ) ;
                $item->ADIC4 =  !isset($precios[0]->PVP4) ? 0 : str_replace(',','', str_replace('$','',$precios[0]->PVP4) ) ;
                $precio5 = (($item->PREMECOS * 1.16)*(1.15) );
                $precio6 = (($item->PREMECOS * 1.16)*(1.10) );
                $item->ADIC6 =  $precio6;
                if ( !isset($precios[0]->PVP5) || $precios[0]->PVP5 == '' || $precios[0]->PVP5 === 0 ) {
                    $item->ADIC5 =  $precio5 ;
                    
                } else {
                     $item->ADIC5 =  str_replace(',','', str_replace('$','',$precios[0]->PVP5) ) ;
                     if ($item->ADIC5  == round(($item->PREMECOS * 1.16), 2 ) ) {
                         $item->ADIC5 = $precio5;
                     }elseif( is_numeric( $item )  ){
                         if ( abs( ($item->ADIC5 / ($item->PREMECOS * 1.16 ) - 1 ) ) < 1 ) {
                             $item->ADIC5 = $precio5;
                         }  
                     }else{
                         $item->ADIC5 = $precio5;
                     }
                }
        return $item;
    }

    public function preparaReporte($data)
    {
        extract($data);
		$arrPVPS = explode(',',$pvps);
		
        $articulos = new Articulos;
        $listaArticulosDesagrupados = $articulos->getProductosFamilia( $familia, $almacen) ;
        $listaArticulosAgrupados = array();
        if ( $group == 0) {
            foreach($listaArticulosDesagrupados as $i => $articulo){
                $listaArticulosDesagrupados[$i] = $this->configuraPrecios($articulo);
            }
        } else {
            foreach ($listaArticulosDesagrupados as $i => $articulo) {
                if ( ! isset( $listaArticulosAgrupados[$articulo->CODIGOART]->STOCK)  ){
                    $listaArticulosAgrupados[$articulo->CODIGOART] = $this->configuraPrecios( $articulo);
                }else{
                    $listaArticulosAgrupados[$articulo->CODIGOART]->STOCK += $articulo->STOCK;
                }
            }
            $listaArticulosDesagrupados = $listaArticulosAgrupados;
        }
        

        // foreach ($listaArticulosDesagrupados as $i => $articulo ) {

        //     // if ( ! isset( $listaArticulosAgrupados[$articulo->CODIGOART]->STOCK)  ){

                
                
        //     //     //  echo $listaArticulosAgrupados[$articulo->CODIGOART]->ADIC5.' ----- '.$precio5 ."<br>";
        //     // } else {
        //     //     $listaArticulosAgrupados[$articulo->CODIGOART]->STOCK += $articulo->STOCK;

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
        $libroEdicion->setAutoFilter("A7:X7");
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
		if ( $displayStock == 1) {
			$libroEdicion->setCellValue("H7",'STOCK');
		}
		if ( $costo == 1) {
			$libroEdicion->setCellValue("I7",'CTO. PROM.');
			$libroEdicion->setCellValue("J7",'CTO. PROM. C\IVA');
			$libroEdicion->setCellValue("K7",'TOT. CTO. PROM.');
		}
		if ( $displayAlmacen == 1) {
			$libroEdicion->setCellValue("L7",'ALMACEN');
		}
        $libroEdicion->setCellValue("M7",'PVP1');
        $libroEdicion->setCellValue("N7",'%');
        $libroEdicion->setCellValue("O7",'PVP2');
        $libroEdicion->setCellValue("P7",'%');
        $libroEdicion->setCellValue("Q7",'PVP3');
        $libroEdicion->setCellValue("R7",'%');
        $libroEdicion->setCellValue("S7",'PVP4');
        $libroEdicion->setCellValue("T7",'%');
        $libroEdicion->setCellValue("U7",'PVP5');
        $libroEdicion->setCellValue("V7",'%');
        $libroEdicion->setCellValue("W7",'PVP6');
        $libroEdicion->setCellValue("X7",'%');

        $libroEdicion->getStyle("A7:X7")->applyFromArray( $this->labelBold);
        $libroEdicion->getStyle("A7:X7")->applyFromArray( $this->centrarTexto);
        $libroEdicion->getStyle("A7:X7")->applyFromArray( $this->setColorText('ffffff', 11));
        $libroEdicion->getStyle("A7:X7")->getFill()->applyFromArray( $this->setColorFill("cc0000")  );
        
        for ($i=1; $i <= 8 ; $i++) { 
            $libroEdicion->freezePane('A'.$i);
        }        
        

        $i = 8;
        $colorRow = false;
        foreach ($listaArticulosDesagrupados as $codigo => $articulo) {

            if ( $colorRow) {
                $libroEdicion->getStyle("A$i:X$i")->getFill()->applyFromArray( $this->setColorFill("f0f5f5")  );
            }
			$libroEdicion->getStyle("A$i:X$i")->getAlignment()->applyFromArray(array('vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,));
            $libroEdicion->setCellValue("A$i", $articulo->CODIGOART);
            $libroEdicion->setCellValue("B$i", utf8_decode($articulo->DESCRIP));
            $libroEdicion->setCellValue("C$i",$articulo->FAM);
            $libroEdicion->setCellValue("D$i",$articulo->SUBFAMILIA);

            if ( $familia  == 'LLANTA' ) {
					$descripcionSplitted = explode(' ',utf8_decode($articulo->DESCRIP));
					$libroEdicion->setCellValue("E$i", $descripcionSplitted[0]);
                    $libroEdicion->setCellValue("F$i",$articulo->MARCA);
                    $libroEdicion->setCellValue("G$i",$articulo->SUBFAM3);
            }else{
                    $libroEdicion->setCellValue("E$i",utf8_decode($articulo->SUBFAM2));
                    $libroEdicion->setCellValue("F$i",$articulo->MARCA);
                    $libroEdicion->setCellValue("G$i",$articulo->SUBFAM4);
            }
			if ( $displayStock == 1) {
				$libroEdicion->setCellValue("H$i",$articulo->STOCK);
				$libroEdicion->getStyle("H$i")->applyFromArray( $this->centrarTexto);
			}
			
			if($costo==1){
				$libroEdicion->setCellValue("I$i", $articulo->PREMECOS);
				$libroEdicion->setCellValue("J$i","=I$i*1.16");
				$libroEdicion->setCellValue("K$i","=H$i*I$i");
			}
			if ( $displayAlmacen == 1) {
				$libroEdicion->setCellValue("L$i", $articulo->ALMACEN);
			}
            
            $arrayColumnPVP = array('',array('M','N'),array('O','P'),array('Q','R'),array('S','T'),array('U','V'),array('W','X'));
			if(in_array('1',$arrPVPS)){
				$libroEdicion->setCellValue("M$i",$articulo->ADIC1);
				if($utilidad==1){
					$libroEdicion->setCellValue("N$i","=(M$i/J$i) - 1");
				}
			}
			if(in_array('2',$arrPVPS)){
				$libroEdicion->setCellValue("O$i",$articulo->ADIC2);
				if($utilidad==1)
					$libroEdicion->setCellValue("P$i","=(O$i/J$i) - 1");
			}
			if(in_array('3',$arrPVPS)){
				$libroEdicion->setCellValue("Q$i",$articulo->ADIC3);
				if($utilidad==1)
					$libroEdicion->setCellValue("R$i","=(Q$i/J$i) - 1");
			}
			if(in_array('4',$arrPVPS)){
				$libroEdicion->setCellValue("S$i",$articulo->ADIC4);
				if($utilidad==1)
					$libroEdicion->setCellValue("T$i","=ABS((S$i/J$i) - 1)");
			}
			if(in_array('5',$arrPVPS)){
				$libroEdicion->setCellValue("U$i",$articulo->ADIC5);
				if($utilidad==1)
					$libroEdicion->setCellValue("V$i","=ABS((U$i/J$i) - 1)");      
            }
            if ( in_array('6',$arrPVPS) ) {
                
                    $libroEdicion->setCellValue("W$i", $articulo->ADIC6);
                if ( $utilidad == 1) {
                    $libroEdicion->setCellValue("X$i", "=ABS((W$i/J$i) - 1)");
                }
            }
        

            $libroEdicion->getStyle("N$i")->getNumberFormat()->setFormatCode('0%;[Red]-0%');   
            $libroEdicion->getStyle("P$i")->getNumberFormat()->setFormatCode('0%;[Red]-0%');   
            $libroEdicion->getStyle("R$i")->getNumberFormat()->setFormatCode('0%;[Red]-0%');   
            $libroEdicion->getStyle("T$i")->getNumberFormat()->setFormatCode('0%;[Red]-0%');   
            $libroEdicion->getStyle("V$i")->getNumberFormat()->setFormatCode('0%;[Red]-0%');  
            $libroEdicion->getStyle("X$i")->getNumberFormat()->setFormatCode('0%;[Red]-0%');  
            $libroEdicion->getStyle("I$i:K$i")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00"); 
            $libroEdicion->getStyle("M$i")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00"); 
            $libroEdicion->getStyle("O$i")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00"); 
            $libroEdicion->getStyle("Q$i")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00"); 
            $libroEdicion->getStyle("S$i")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00"); 
            $libroEdicion->getStyle("U$i")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00"); 
            $libroEdicion->getStyle("W$i")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00");
            $libroEdicion->getRowDimension($i)->setRowHeight(30);

            if ( !$colorRow) {
                $colorRow = true;
            }else{
                $colorRow = false;
            }
            $i++;   
        }
        //Haciendo la suma del Stock y de los costos
        $libroEdicion->setCellValue("H$i","=SUM(H5:H$i)");
        $libroEdicion->setCellValue("I$i","=SUM(I5:I$i)");
        $libroEdicion->setCellValue("J$i","=SUM(J5:J$i)");
		$libroEdicion->setCellValue("K$i","=SUM(K5:K$i)");

        $libroEdicion->getStyle("H$i:J$i")->applyFromArray( $this->setColorText("ff0000"));
        $libroEdicion->getStyle("H$i")->getNumberFormat()->setFormatCode("#,##0;-#,##0");
        $libroEdicion->getStyle("I$i:J$i")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00"); 

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
        $libroEdicion->getColumnDimension('I')->setWidth("17");          
        $libroEdicion->getColumnDimension('J')->setAutoSize(false);
        $libroEdicion->getColumnDimension('J')->setWidth("17");
        $libroEdicion->getColumnDimension('K')->setAutoSize(false);
        $libroEdicion->getColumnDimension('K')->setWidth("12");        
        $libroEdicion->getColumnDimension('L')->setAutoSize(false);
        $libroEdicion->getColumnDimension('L')->setWidth("14");
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
        for ($i=1; $i <= 6; $i++) { 
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
            $libroEdicion->getColumnDimension("L")->setVisible(false);
        }
		
		
		if ( $costo == 0) {
			//$libroEdicion->removeColumn("K");
            $libroEdicion->getColumnDimension("K")->setVisible(false);
			//$libroEdicion->removeColumn("J");
            $libroEdicion->getColumnDimension("J")->setVisible(false);
            //$libroEdicion->removeColumn("I");
            $libroEdicion->getColumnDimension("I")->setVisible(false);
        }

        if ( $displayStock == 0) {
           // $libroEdicion->removeColumn("H");
            $libroEdicion->getColumnDimension("H")->setVisible(false);
        }
		
		
        $libroEdicion->setShowGridlines(false);
        $valuadoTerminado = new PHPExcel_Writer_Excel2007( $this->libro );
        $valuadoTerminado->setPreCalculateFormulas(true);
        $valuadoTerminado->save("VALUADO $familia $id.xlsx");

        $articulos->close();
        unset($articulos);
        
    }
	
	public function enviarReportes( $lista, $correos, $id, $titulo )
    {
        $emailsender = new \phpmailer;
        $emailsender->isSMTP();
        $emailsender->SMTPDebug = 1;
        $emailsender->SMTPAuth = true;
        $emailsender->Port = 587;

        $emailsender->Host = 'mail.matrix.com.mx';
        $emailsender->Username = "no-responder@matrix.com.mx";
        $emailsender->Password = "M@tr1x2017";

        $emailsender->From ="no-responder@matrix.com.mx";
        $emailsender->FromName = "SITEX";

        $emailsender->Subject = $titulo." del ".date("d-m-Y");
        $emailsender->Body = "<p>...</p>";

        $emailsender->AltBody = "...";

		foreach($lista as $fam){
			if ( is_file("VALUADO ".$fam.".xlsx") ) {
				$emailsender->AddAttachment("VALUADO ".$fam." $id.xlsx");
			}
		}
		/**/
		
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
		// $reportes = new Reportes;
		// $listReporte = $reportes->getReporte('valuado');
        
		//Recorremos los registros que tengan en el campo "nombre" lo indicado en el parametro enviado a getReporte, por cada registro se llamará a la función enviarReporte()
		// foreach($listReporte as $rw){
            // var_dump( $rw);
            // echo "<br><br>";
			$arrfamilias = explode(',',$familias);
			$arrcorreos = explode(',',$correos);
			foreach($arrfamilias as $rwfamilias){
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
			$this->enviarReportes($arrfamilias, $arrcorreos, $id, $titulo);
		// }
		
        
    }
}

$valuados = new Valuados;
// $valuados->generaReporte();


if ( isset($_GET['opc'])) {
    switch($_GET['opc']){
        case 'getIdsValuado':{
            $reportes = new Reportes;
            echo json_encode( $reportes->getReporte('valuado'));
            break;
        }
        case 'genValuado':{
                $reportes = new Reportes;
                $detalleReporte = $reportes->getDetalleReporte($_GET['id']);
                $valuados->generaReporte($detalleReporte[0]);
            break;
        }
    }
} else {
?>
    <script src="/intranet/assets/js/jquery-1.11.2.min.js"></script>
    <script>
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