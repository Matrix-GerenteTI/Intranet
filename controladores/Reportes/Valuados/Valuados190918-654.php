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

    public function preparaReporte($familia, $almacen = "%", $utilidad, $costo, $pvps, $id)
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

				$listaArticulosDesagrupados[$i]->DESCRIP =utf8_decode( $articulo->DESCRIP );
                $listaArticulosDesagrupados[$i]->MARCA = $articulo->ADIC1;
                $listaArticulosDesagrupados[$i]->ADIC1 = !isset($precios[0]->PVP1) ? 0 : str_replace(',','', str_replace('$','',$precios[0]->PVP1) ) ;
                $listaArticulosDesagrupados[$i]->ADIC2 =  !isset($precios[0]->PVP2) ? 0 : str_replace(',','', str_replace('$','',$precios[0]->PVP2) ) ;
                $listaArticulosDesagrupados[$i]->ADIC3 =  !isset($precios[0]->PVP3) ? 0 : str_replace(',','', str_replace('$','',$precios[0]->PVP3) ) ;
                $listaArticulosDesagrupados[$i]->ADIC4 =  !isset($precios[0]->PVP4) ? 0 : str_replace(',','', str_replace('$','',$precios[0]->PVP4) ) ;
                $precio5 = (($articulo->PREMECOS * 1.16)*(1.15) );
                $precio6 = (($articulo->PREMECOS * 1.16)*(1.10) );
                $listaArticulosDesagrupados[$i]->ADIC6 =  $precio6;
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
        $libroEdicion->setCellValue("A2","Empresa");
        $libroEdicion->setCellValue("B2","$almacen.");
        $libroEdicion->getStyle("A2")->applyFromArray($this->centrarTexto);
        $libroEdicion->getStyle("A2")->applyFromArray($this->labelBold);

        $libroEdicion->setAutoFilter("A4:U4");
        $libroEdicion->setCellValue("L3","P.LISTA");
        $libroEdicion->setCellValue("N3","MED. MAY.");
        $libroEdicion->setCellValue("P3","MAYOREO");
        $libroEdicion->setCellValue("R3","MED. MAY.");

        $libroEdicion->setCellValue("A4",'CODIGO');
        $libroEdicion->setCellValue("B4",'DESCRIPCION');
        $libroEdicion->setCellValue("C4",'FAMILIA');
        $libroEdicion->setCellValue("D4",'SUBFAMILIA');
        switch ($familia) {
            case 'LLANTA':
                $libroEdicion->setCellValue("E4","MEDIDA");
                $libroEdicion->setCellValue("F4",'MARCA');
                $libroEdicion->setCellValue("G4",'MODELO');
                break;
            case 'RIN':
                $libroEdicion->setCellValue("E4","TIPO");
                $libroEdicion->setCellValue("F4",'DIAMETRO');
                $libroEdicion->setCellValue("G4",'BARRENACION');                
                break;
            case 'ACCESORIO':
                $libroEdicion->setCellValue("E4","MARCA");
                $libroEdicion->setCellValue("F4",'MODELO');
                $libroEdicion->setCellValue("G4",'AÑO');             
                break;
            case 'COLISION':
                $libroEdicion->setCellValue("E4","MARCA");
                $libroEdicion->setCellValue("F4",'MODELO');
                $libroEdicion->setCellValue("G4",'AÑO');           
                break;
        }
        $libroEdicion->setCellValue("H4",'STOCK');
        $libroEdicion->setCellValue("I4",'CTO. PROM.');
        $libroEdicion->setCellValue("J4",'CTO. PROM. C\IVA');
        $libroEdicion->setCellValue("K4",'TOT. CTO. PROM.');
        $libroEdicion->setCellValue("L4",'ALMACEN');
        $libroEdicion->setCellValue("M4",'PVP1');
        $libroEdicion->setCellValue("N4",'%');
        $libroEdicion->setCellValue("O4",'PVP2');
        $libroEdicion->setCellValue("P4",'%');
        $libroEdicion->setCellValue("Q4",'PVP3');
        $libroEdicion->setCellValue("R4",'%');
        $libroEdicion->setCellValue("S4",'PVP4');
        $libroEdicion->setCellValue("T4",'%');
        $libroEdicion->setCellValue("U4",'PVP5');
        $libroEdicion->setCellValue("V4",'%');
        $libroEdicion->setCellValue("W4",'PVP6');
        $libroEdicion->setCellValue("X4",'%');

        $libroEdicion->getStyle("A4:X4")->applyFromArray( $this->labelBold);
        $libroEdicion->getStyle("A4:X4")->applyFromArray( $this->centrarTexto);
        $libroEdicion->getStyle("A4:X4")->getFill()->applyFromArray( $this->setColorFill("d8d8d8")  );
        
        for ($i=1; $i <= 5 ; $i++) { 
            $libroEdicion->freezePane('A'.$i);
        }        
        

        $i = 5;
        foreach ($listaArticulosDesagrupados as $codigo => $articulo) {
            $libroEdicion->setCellValue("A$i", $articulo->CODIGOART);
            $libroEdicion->setCellValue("B$i", utf8_decode($articulo->DESCRIP));
            $libroEdicion->setCellValue("C$i",$articulo->FAM);
            $libroEdicion->setCellValue("D$i",$articulo->SUBFAMILIA);

            if ( $familia  == 'LLANTA' ) {
                    $libroEdicion->setCellValue("E$i",utf8_decode($articulo->SUBFAM2));
                    $libroEdicion->setCellValue("F$i",$articulo->MARCA);
                    $libroEdicion->setCellValue("G$i",$articulo->SUBFAM3);
            }else{
                    $libroEdicion->setCellValue("E$i",utf8_decode($articulo->SUBFAM2));
                    $libroEdicion->setCellValue("F$i",$articulo->MARCA);
                    $libroEdicion->setCellValue("G$i",$articulo->SUBFAM4);
            }

            $libroEdicion->setCellValue("H$i",$articulo->STOCK);
			
			if($costo==1){
				$libroEdicion->setCellValue("I$i", $articulo->PREMECOS);
				$libroEdicion->setCellValue("J$i","=I$i*1.16");
				$libroEdicion->setCellValue("K$i","=H$i*I$i");
			}
			$libroEdicion->setCellValue("L$i", $articulo->ALMACEN);
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
            $libroEdicion->getStyle("O$i")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00"); 
            $libroEdicion->getStyle("R$i")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00"); 
            $libroEdicion->getStyle("S$i")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00"); 
            $libroEdicion->getStyle("U$i")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00"); 
            $libroEdicion->getStyle("W$i")->getNumberFormat()->setFormatCode("$#,##0.00;-$#,##0.00");
            $i++;   
        }
        //Haciendo la suma del Stock y de los costos
        $libroEdicion->setCellValue("H$i","=SUM(H5:H$i)");
        $libroEdicion->setCellValue("I$i","=SUM(I5:I$i)");
        $libroEdicion->setCellValue("J$i","=SUM(J5:J$i)");

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

        $libroEdicion->setShowGridlines(false);
        $valuadoTerminado = new PHPExcel_Writer_Excel2007( $this->libro );
        $valuadoTerminado->setPreCalculateFormulas(true);
        $valuadoTerminado->save("VALUADO $familia $id.xlsx");

        $articulos->close();
        unset($articulos);
    }
	
	public function enviarReportes( $lista, $correos, $id )
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
				$this->preparaReporte($rwfamilias,$almacenes,$utilidad,$costo,$pvps,$id);
				$this->libro = null;
                $this->__construct();
                
            }
            // exit();
			$this->enviarReportes($arrfamilias, $arrcorreos, $id);
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