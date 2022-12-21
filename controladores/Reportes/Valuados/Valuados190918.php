<?php
ini_set('memory_limit', '-1');

require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/almacenes/Articulos.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/Reportes/Reportes.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/Reportes/prepareExcel.php";

class Valuados extends prepareExcel
{
    public function __construct()
    {
        parent::__construct();
        $this->libro->getProperties()->setTitle('VALUADO DE PRODUCTOS'); 
    }

    public function preparaReporte($familia, $almacen = "%", $utilidad, $costo, $pvps)
    {
		$arrPVPS = explode(',',$pvps);
		
        $articulos = new Articulos;
        $listaArticulosDesagrupados = $articulos->getProductosFamilia( $familia, $almacen) ;
		
        $listaArticulosAgrupados = array();
        foreach ($listaArticulosDesagrupados as $i => $articulo ) {
            // if ( ! isset( $listaArticulosAgrupados[$articulo->CODIGOART]->STOCK)  ){
                $listaArticulosAgrupados[$articulo->CODIGOART] = $articulo;
                //obteniendo los precios de venta del articulo
                $precios = $articulos->getPrecioByArticulo( $articulo->IDARTICULO );
				
                $listaArticulosDesagrupados[$i]->MARCA = $articulo->ADIC1;
                $listaArticulosDesagrupados[$i]->ADIC1 = !isset($precios[0]->PVP1) ? 0 : str_replace(',','', str_replace('$','',$precios[0]->PVP1) ) ;
                $listaArticulosDesagrupados[$i]->ADIC2 =  !isset($precios[0]->PVP2) ? 0 : str_replace(',','', str_replace('$','',$precios[0]->PVP2) ) ;
                $listaArticulosDesagrupados[$i]->ADIC3 =  !isset($precios[0]->PVP3) ? 0 : str_replace(',','', str_replace('$','',$precios[0]->PVP3) ) ;
                $listaArticulosDesagrupados[$i]->ADIC4 =  !isset($precios[0]->PVP4) ? 0 : str_replace(',','', str_replace('$','',$precios[0]->PVP4) ) ;
                $precio5 = (($articulo->PREMECOS * 1.16)*(1.12) );
                if ( !isset($precios[0]->PVP5) || $precios[0]->PVP5 == '' || $precios[0]->PVP5 === 0 ) {
                    $listaArticulosDesagrupados[$i]->ADIC5 =  $precio5 ;
                    
                } else {
                     $listaArticulosDesagrupados[$i]->ADIC5 =  str_replace(',','', str_replace('$','',$precios[0]->PVP5) ) ;
                     if ($listaArticulosDesagrupados[$i]->ADIC5  == round(($articulo->PREMECOS * 1.16), 2 ) ) {
                         $listaArticulosDesagrupados[$i]->ADIC5 = $precio5;
                     }elseif( is_numeric( $listaArticulosDesagrupados[$i] )  ){
                         if ( abs( ($listaArticulosDesagrupados[$i]->ADIC5 / ($articulo->PREMECOS * 1.16 ) - 1 ) ) < 1 ) {
                             $listaArticulosDesagrupados[$i]->ADIC5 = $precio5;
                         }  
                     }else{
                         $listaArticulosDesagrupados[$i]->ADIC5 = $precio5;
                     }
                }
                
                
            //     //  echo $listaArticulosAgrupados[$articulo->CODIGOART]->ADIC5.' ----- '.$precio5 ."<br>";
            // } else {
            //     $listaArticulosAgrupados[$articulo->CODIGOART]->STOCK += $articulo->STOCK;

            // }
            
        }

        $this->creaEmptySheet("VALUADO $familia");
        $this->libro->getActiveSheet()->setCellValue('L1','PRECIOS CON IVA');
        $this->libro->getActiveSheet()->mergeCells("L1:V1");
        $this->libro->getActiveSheet()->getStyle("L1")->applyFromArray($this->centrarTexto);
        $this->libro->getActiveSheet()->getStyle("L1")->applyFromArray($this->labelBold);

        if ( $almacen == '%' ) {
            $almacen = "Matrixxx Alm.";
        }else{
            $infoAlmacen = $articulos->getAlmacenes($almacen);
            $almacen = $infoAlmacen[0]->DESCRIPCION;
        }
        $this->libro->getActiveSheet()->setCellValue("A2","Empresa");
        $this->libro->getActiveSheet()->setCellValue("B2","$almacen.");
        $this->libro->getActiveSheet()->getStyle("A2")->applyFromArray($this->centrarTexto);
        $this->libro->getActiveSheet()->getStyle("A2")->applyFromArray($this->labelBold);

        $this->libro->getActiveSheet()->setAutoFilter("A4:U4");
        $this->libro->getActiveSheet()->setCellValue("L3","P.LISTA");
        $this->libro->getActiveSheet()->setCellValue("N3","MED. MAY.");
        $this->libro->getActiveSheet()->setCellValue("P3","MAYOREO");
        $this->libro->getActiveSheet()->setCellValue("R3","MED. MAY.");

        $this->libro->getActiveSheet()->setCellValue("A4",'CODIGO');
        $this->libro->getActiveSheet()->setCellValue("B4",'DESCRIPCION');
        $this->libro->getActiveSheet()->setCellValue("C4",'FAMILIA');
        $this->libro->getActiveSheet()->setCellValue("D4",'SUBFAMILIA');
        switch ($familia) {
            case 'LLANTA':
                $this->libro->getActiveSheet()->setCellValue("E4","MEDIDA");
                $this->libro->getActiveSheet()->setCellValue("F4",'MARCA');
                $this->libro->getActiveSheet()->setCellValue("G4",'MODELO');
                break;
            case 'RIN':
                $this->libro->getActiveSheet()->setCellValue("E4","TIPO");
                $this->libro->getActiveSheet()->setCellValue("F4",'DIAMETRO');
                $this->libro->getActiveSheet()->setCellValue("G4",'BARRENACION');                
                break;
            case 'ACCESORIO':
                $this->libro->getActiveSheet()->setCellValue("E4","MARCA");
                $this->libro->getActiveSheet()->setCellValue("F4",'MODELO');
                $this->libro->getActiveSheet()->setCellValue("G4",'AÑO');             
                break;
            case 'COLISION':
                $this->libro->getActiveSheet()->setCellValue("E4","MARCA");
                $this->libro->getActiveSheet()->setCellValue("F4",'MODELO');
                $this->libro->getActiveSheet()->setCellValue("G4",'AÑO');           
                break;
        }
        $this->libro->getActiveSheet()->setCellValue("H4",'STOCK');
        $this->libro->getActiveSheet()->setCellValue("I4",'CTO. PROM.');
        $this->libro->getActiveSheet()->setCellValue("J4",'CTO. PROM. C\IVA');
        $this->libro->getActiveSheet()->setCellValue("K4",'TOT. CTO. PROM.');
        $this->libro->getActiveSheet()->setCellValue("L4",'ALMACEN');
        $this->libro->getActiveSheet()->setCellValue("M4",'PVP1');
        $this->libro->getActiveSheet()->setCellValue("N4",'%');
        $this->libro->getActiveSheet()->setCellValue("O4",'PVP2');
        $this->libro->getActiveSheet()->setCellValue("P4",'%');
        $this->libro->getActiveSheet()->setCellValue("Q4",'PVP3');
        $this->libro->getActiveSheet()->setCellValue("R4",'%');
        $this->libro->getActiveSheet()->setCellValue("S4",'PVP4');
        $this->libro->getActiveSheet()->setCellValue("T4",'%');
        $this->libro->getActiveSheet()->setCellValue("U4",'PVP5');
        $this->libro->getActiveSheet()->setCellValue("V4",'%');

        $this->libro->getActiveSheet()->getStyle("A4:V4")->applyFromArray( $this->labelBold);
        $this->libro->getActiveSheet()->getStyle("A4:V4")->applyFromArray( $this->centrarTexto);
        $this->libro->getActiveSheet()->getStyle("A4:V4")->getFill()->applyFromArray( $this->setColorFill("d8d8d8")  );
        
        for ($i=1; $i <= 5 ; $i++) { 
            $this->libro->getActiveSheet()->freezePane('A'.$i);
        }        
        

        $i = 5;
        foreach ($listaArticulosDesagrupados as $codigo => $articulo) {
            $this->libro->getActiveSheet()->setCellValue("A$i", $articulo->CODIGOART);
            $this->libro->getActiveSheet()->setCellValue("B$i", utf8_decode($articulo->DESCRIP));
            $this->libro->getActiveSheet()->setCellValue("C$i",$articulo->FAM);
            $this->libro->getActiveSheet()->setCellValue("D$i",$articulo->SUBFAMILIA);

            if ( $familia  == 'LLANTA' ) {
                    $this->libro->getActiveSheet()->setCellValue("E$i",utf8_decode($articulo->SUBFAM2));
                    $this->libro->getActiveSheet()->setCellValue("F$i",$articulo->MARCA);
                    $this->libro->getActiveSheet()->setCellValue("G$i",$articulo->SUBFAM3);
            }else{
                    $this->libro->getActiveSheet()->setCellValue("E$i",utf8_decode($articulo->SUBFAM2));
                    $this->libro->getActiveSheet()->setCellValue("F$i",$articulo->MARCA);
                    $this->libro->getActiveSheet()->setCellValue("G$i",$articulo->SUBFAM4);
            }

            $this->libro->getActiveSheet()->setCellValue("H$i",$articulo->STOCK);
			
			if($costo==1){
				$this->libro->getActiveSheet()->setCellValue("I$i", $articulo->PREMECOS);
				$this->libro->getActiveSheet()->setCellValue("J$i","=I$i*1.16");
				$this->libro->getActiveSheet()->setCellValue("K$i","=H$i*I$i");
			}
			$this->libro->getActiveSheet()->setCellValue("L$i", $articulo->ALMACEN);
			if(in_array('1',$arrPVPS)){
				$this->libro->getActiveSheet()->setCellValue("M$i",$articulo->ADIC1);
				if($utilidad==1){
					$this->libro->getActiveSheet()->setCellValue("N$i","=(L$i/J$i) - 1");
				}
			}
			if(in_array('2',$arrPVPS)){
				$this->libro->getActiveSheet()->setCellValue("O$i",$articulo->ADIC2);
				if($utilidad==1)
					$this->libro->getActiveSheet()->setCellValue("P$i","=(N$i/J$i) - 1");
			}
			if(in_array('3',$arrPVPS)){
				$this->libro->getActiveSheet()->setCellValue("Q$i",$articulo->ADIC3);
				if($utilidad==1)
					$this->libro->getActiveSheet()->setCellValue("R$i","=(P$i/J$i) - 1");
			}
			if(in_array('4',$arrPVPS)){
				$this->libro->getActiveSheet()->setCellValue("S$i",$articulo->ADIC4);
				if($utilidad==1)
					$this->libro->getActiveSheet()->setCellValue("T$i","=ABS((R$i/J$i) - 1)");
			}
			if(in_array('5',$arrPVPS)){
				$this->libro->getActiveSheet()->setCellValue("U$i",$articulo->ADIC5);
				if($utilidad==1)
					$this->libro->getActiveSheet()->setCellValue("V$i","=ABS((T$i/J$i) - 1)");      
			}
            $this->libro->getActiveSheet()->getStyle("N$i")->getNumberFormat()->setFormatCode('0%;[Red]-0%');   
            $this->libro->getActiveSheet()->getStyle("P$i")->getNumberFormat()->setFormatCode('0%;[Red]-0%');   
            $this->libro->getActiveSheet()->getStyle("R$i")->getNumberFormat()->setFormatCode('0%;[Red]-0%');   
            $this->libro->getActiveSheet()->getStyle("T$i")->getNumberFormat()->setFormatCode('0%;[Red]-0%');   
            $this->libro->getActiveSheet()->getStyle("V$i")->getNumberFormat()->setFormatCode('0%;[Red]-0%');  
            $this->libro->getActiveSheet()->getStyle("I$i:K$i")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00"); 
            $this->libro->getActiveSheet()->getStyle("O$i")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00"); 
            $this->libro->getActiveSheet()->getStyle("R$i")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00"); 
            $this->libro->getActiveSheet()->getStyle("S$i")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00"); 
            $this->libro->getActiveSheet()->getStyle("U$i")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00"); 
            $i++;   
        }
        //Haciendo la suma del Stock y de los costos
        $this->libro->getActiveSheet()->setCellValue("H$i","=SUM(H5:H$i)");
        $this->libro->getActiveSheet()->setCellValue("I$i","=SUM(I5:I$i)");
        $this->libro->getActiveSheet()->setCellValue("J$i","=SUM(J5:J$i)");

        $this->libro->getActiveSheet()->getStyle("H$i:J$i")->applyFromArray( $this->setColorText("ff0000"));
        $this->libro->getActiveSheet()->getStyle("H$i")->getNumberFormat()->setFormatCode("#,##0;-#,##0");
        $this->libro->getActiveSheet()->getStyle("I$i:J$i")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00"); 

        $this->libro->getActiveSheet()->getColumnDimension('A')->setAutoSize(false);
        $this->libro->getActiveSheet()->getColumnDimension('A')->setWidth("15");
        $this->libro->getActiveSheet()->getColumnDimension('B')->setAutoSize(false);
        $this->libro->getActiveSheet()->getColumnDimension('B')->setWidth("40");        
        $this->libro->getActiveSheet()->getColumnDimension('C')->setAutoSize(false);
        $this->libro->getActiveSheet()->getColumnDimension('C')->setWidth("10");                
        $this->libro->getActiveSheet()->getColumnDimension('D')->setAutoSize(false);
        $this->libro->getActiveSheet()->getColumnDimension('D')->setWidth("15");              
        $this->libro->getActiveSheet()->getColumnDimension('E')->setAutoSize(false);
        $this->libro->getActiveSheet()->getColumnDimension('E')->setWidth("12");                  
        $this->libro->getActiveSheet()->getColumnDimension('F')->setAutoSize(false);
        $this->libro->getActiveSheet()->getColumnDimension('F')->setWidth("12");     
        $this->libro->getActiveSheet()->getColumnDimension('G')->setAutoSize(false);
        $this->libro->getActiveSheet()->getColumnDimension('G')->setWidth("12");    
        $this->libro->getActiveSheet()->getColumnDimension('H')->setAutoSize(false);
        $this->libro->getActiveSheet()->getColumnDimension('H')->setWidth("17");     
        $this->libro->getActiveSheet()->getColumnDimension('I')->setAutoSize(false);
        $this->libro->getActiveSheet()->getColumnDimension('I')->setWidth("17");          
        $this->libro->getActiveSheet()->getColumnDimension('J')->setAutoSize(false);
        $this->libro->getActiveSheet()->getColumnDimension('J')->setWidth("17");
        $this->libro->getActiveSheet()->getColumnDimension('K')->setAutoSize(false);
        $this->libro->getActiveSheet()->getColumnDimension('K')->setWidth("12");        
        $this->libro->getActiveSheet()->getColumnDimension('L')->setAutoSize(false);
        $this->libro->getActiveSheet()->getColumnDimension('L')->setWidth("14");                                                     

        $this->libro->getActiveSheet()->setShowGridlines(false);
        $valuadoTerminado = new PHPExcel_Writer_Excel2007( $this->libro );
        $valuadoTerminado->setPreCalculateFormulas(true);
        $valuadoTerminado->save("VALUADO $familia.xlsx");
    }
	
	public function enviarReporte( $lista, $correos )
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

        $emailsender->Subject ="Valuado del ".date("d-m-Y");
        $emailsender->Body = "<p>...</p>";

        $emailsender->AltBody = "...";

		foreach($lista as $fam){
			if ( is_file("VALUADO ".$fam.".xlsx") ) {
				$emailsender->AddAttachment("VALUADO ".$fam.".xlsx");
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
	
	public function generaReporte( )
    {
		$reportes = new Reportes;
		$listReporte = $reportes->getReporte('valuado');
		
		//Recorremos los registros que tengan en el campo "nombre" lo indicado en el parametro enviado a getReporte, por cada registro se llamará a la función enviarReporte()
		foreach($listReporte as $rw){
			$arrfamilias = explode(',',$rw['familias']);
			$arrcorreos = explode(',',$rw['correos']);
			foreach($arrfamilias as $rwfamilias){
				$this->preparaReporte($rwfamilias,$rw['almacenes'],$rw['utilidad'],$rw['costo'],$rw['pvps']);
				$this->libro = null;
				$this->__construct();
			}
			$this->enviarReporte($arrfamilias,$arrcorreos);
		}
		
        
    }
}

$valuados = new Valuados;
$valuados->generaReporte();
