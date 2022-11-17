<?php 
//require_once("satxmlsv.php"); 
$conn = new mysqli("matrix.com.mx", "sestrada", "M@tr1x2017", "matrixerp");
date_default_timezone_set('America/Mexico_City');

$emre = array('Emitidas','Recibidas');
foreach($emre as $folder){
	$path = "./RUVL810825597/".$folder."/2018/01/";
	$dir = opendir($path);
	$files = array();
	while ($current = readdir($dir)){
		if( $current != "." && $current != "..") {
			if(!is_dir($path.$current)) {
				$ext = explode('.',$current);
				$extension = "";
				foreach($ext as $val){
					$extension = $val;
				}
				if($extension == "xml"){
					$carga_xml = simplexml_load_file($path.$current);
					$ns = $carga_xml->getNamespaces(true);
					
					$carga_xml->registerXPathNamespace('c', $ns['cfdi']);
					$carga_xml->registerXPathNamespace('t', $ns['tfd']);
					$comprobante = $carga_xml->xpath('//c:Comprobante');
					if(isset($comprobante[0]['version']))
						$version = "3.2";
					else
						$version = "3.3";
						
					if($version=="3.2"){							
						
						$fecha = $comprobante[0]['fecha'];
						$fecha = explode("T",$fecha);
						$horacfdi = $fecha[1];
						$fecha = $fecha[0];
						$seriefolio = $comprobante[0]['serie']."".$comprobante[0]['folio'];
						$descuento = (float) $comprobante[0]['descuento'];
						$subtotal = (float) $comprobante[0]['subTotal'] - $descuento;
						$total = (float) $comprobante[0]['total'];
						
						if((float) $comprobante[0]['SubTotal']>0)
							$desc = ($descuento * 100)/(float) $comprobante[0]['SubTotal'];
						else
							$desc = 0;
						
						$hora = date("h:i:s");
						
						$emisor = $carga_xml->xpath('//c:Emisor');
						$rfc = $emisor[0]['rfc'];
						$nombreemisor = $emisor[0]['nombre'];
						
						$receptor = $carga_xml->xpath('//c:Receptor');
						$rfcR = $receptor[0]['rfc'];
						$nombrereceptor = $receptor[0]['nombre'];
						
						$porciva = 0;
						$iva = 0;
						foreach ($carga_xml->xpath('//c:Traslado') as $traslado) {
							if($traslado['impuesto']=='IVA'){
								$porciva = $traslado['tasa'];						
								$iva = $iva + (float) $traslado['importe'];
							}
						}
						
						$timbre = $carga_xml->xpath('//t:TimbreFiscalDigital');
						$uuid = $timbre[0]['UUID'];
						
						if($folder == 'Recibidas'){
							$descripcion = "COMPRA FACTURA ".$seriefolio." ".$rfc." del ".$fecha;
							$tipoCFDI = 2;
							$concuenta = 8;
						}else{
							$descripcion = "VENTA FACTURA ".$seriefolio." del ".$fecha;
							$tipoCFDI = 1;
							$concuenta = 9;
						}
						$query1 = "INSERT INTO con_facturassat (rfcemisor,
															   rfcreceptor,
															   subtotal,
															   total,
															   nombreemisor,
															   nombrereceptor,
															   uuid,
															   fecha,
															   hora,
															   serie,
															   folio,
															   nocuenta,
															   tipo,
															   nombredoc) 
													VALUES 	  ('".$rfc."',
															   '".$rfcR."',
															   ".$subtotal.",
															   ".$total.",
															   '".$nombreemisor."',
															   '".$nombrereceptor."',
															   '".$uuid."',
															   '".$fecha."',
															   '".$hora."',
															   '".$comprobante[0]['serie']."',
															   '".$comprobante[0]['folio']."',
															   '',
															   ".$tipoCFDI.",
															   '".$path.$current."')";
						$sql1 = $conn->query($query1);
						
						
						$query = "INSERT INTO con_movimientos (descripcion,
															   fecha,
															   hora,
															   docfecha,
															   dochora,
															   docuuid,
															   subtotal,
															   iva,
															   total,
															   idcon_cuentas,
															   tipo,
															   financiero,
															   recurrente,
															   status,
															   idcudn) 
													VALUES 	  ('".$descripcion."',
															   NOW(),
															   NOW(),
															   '".$fecha."',
															   '".$horacfdi."',
															   '".$uuid."',
															   ".$subtotal.",
															   ".($subtotal * .16).",
															   ".$total.",
															   ".$concuenta.",
															   ".$tipoCFDI.",
															   0,
															   0,
															   1,
															   1)";
						$sql = $conn->query($query);
						//$sentence2 = ibase_query($ibasetransG,$query02);
						
					}
					if($version=="3.3"){
						
						$fecha = $comprobante[0]['Fecha'];
						$fecha = explode("T",$fecha);
						$horacfdi = $fecha[1];
						$fecha = $fecha[0];
						$seriefolio = $comprobante[0]['Serie']."".$comprobante[0]['Folio'];
						$descuento = (float) $comprobante[0]['Descuento'];
						$subtotal = (float) $comprobante[0]['SubTotal'] - $descuento;
						$total = (float) $comprobante[0]['Total'];
						
						if((float) $comprobante[0]['SubTotal']>0)
							$desc = ($descuento * 100)/(float) $comprobante[0]['SubTotal'];
						else
							$desc = 0;
						
						$hora = date("h:i:s");
						
						$emisor = $carga_xml->xpath('//c:Emisor');
						$rfc = $emisor[0]['Rfc'];
						$nombreemisor = $emisor[0]['Nombre'];
						
						$receptor = $carga_xml->xpath('//c:Receptor');
						$rfcR = $receptor[0]['Rfc'];
						$nombrereceptor = $receptor[0]['Nombre'];
						
						$porciva = 0;
						$iva = 0;
						foreach ($carga_xml->xpath('//c:Traslado') as $traslado) {
							if(isset($traslado['Base'])){
								if($traslado['Impuesto']=='002'){
									$porciva = $traslado['TasaOCuota'];						
									$iva = $iva + (float) $traslado['Importe'];
								}
							}
						}
						
						$timbre = $carga_xml->xpath('//t:TimbreFiscalDigital');
						$uuid = $timbre[0]['UUID'];
						
						if($folder == 'Recibidas'){
							$descripcion = "COMPRA FACTURA ".$seriefolio." ".$rfc." del ".$fecha;
							$tipoCFDI = 2;
							$concuenta = 8;
						}else{
							$descripcion = "VENTA FACTURA ".$seriefolio." del ".$fecha;
							$tipoCFDI = 1;
							$concuenta = 9;
						}
						$query1 = "INSERT INTO con_facturassat (rfcemisor,
															   rfcreceptor,
															   subtotal,
															   total,
															   nombreemisor,
															   nombrereceptor,
															   uuid,
															   fecha,
															   hora,
															   serie,
															   folio,
															   nocuenta,
															   tipo,
															   nombredoc) 
													VALUES 	  ('".$rfc."',
															   '".$rfcR."',
															   ".$subtotal.",
															   ".$total.",
															   '".$nombreemisor."',
															   '".$nombrereceptor."',
															   '".$uuid."',
															   '".$fecha."',
															   '".$hora."',
															   '".$comprobante[0]['Serie']."',
															   '".$comprobante[0]['Folio']."',
															   '',
															   ".$tipoCFDI.",
															   '".$path.$current."')";
						$sql1 = $conn->query($query1);
						
						$query = "INSERT INTO con_movimientos (descripcion,
															   fecha,
															   hora,
															   docfecha,
															   dochora,
															   docserie,
															   docfolio,
															   docuuid,
															   subtotal,
															   iva,
															   total,
															   idcon_cuentas,
															   tipo,
															   financiero,
															   recurrente,
															   status,
															   idcudn) 
													VALUES 	  ('".$descripcion."',
															   NOW(),
															   NOW(),
															   '".$fecha."',
															   '".$horacfdi."',
															   '".$comprobante[0]['Serie']."',
															   '".$comprobante[0]['Folio']."',
															   '".$uuid."',
															   ".$subtotal.",
															   ".($subtotal * .16).",
															   ".$total.",
															   ".$concuenta.",
															   ".$tipoCFDI.",
															   0,
															   0,
															   1,
															   1)";
						$sql = $conn->query($query);
					}			
				}
			}
		}
	}
}
//echo "Done"; 
/////////////////////////////////////////////////////////////////////////////


//   Valores guardados en un arreglo para ser usado por las funciones
/////////////////////////////////////////////////////////////////////////////




// {{{ Valida este XML en el servidor del SAT 
// ftp://ftp2.sat.gob.mx/asistencia_servicio_ftp/publicaciones/cfdi/WS_ConsultaCFDI.pdf
function valida_en_sat($data) {
    $url = "https://consultaqr.facturaelectronica.sat.gob.mx/consultacfdiservice.svc?wsdl";
    $soapclient = new SoapClient($url);
    $rfc_emisor = utf8_encode($data['rfc']);
    $rfc_receptor = utf8_encode($data['rfc_receptor']);
    $impo = (double)$data['total'];
    $impo=sprintf("%.6f", $impo);
    $impo = str_pad($impo,17,"0",STR_PAD_LEFT);
    $uuid = strtoupper($data['uuid']);
    $factura = "?re=$rfc_emisor&rr=$rfc_receptor&tt=$impo&id=$uuid";
    //echo "<h3>$factura</h3>";
    $prm = array('expresionImpresa'=>$factura);
    $buscar=$soapclient->Consulta($prm);
    //echo "<h3>El portal del SAT reporta</h3>";
    //echo "El codigo: ".$buscar->ConsultaResult->CodigoEstatus."<br>";
    return $buscar->ConsultaResult->Estado;

}

function formateaFecha($fecha){
		$newFecha = explode("-",$fecha);
		return $newFecha[2].".".$newFecha[1].".".$newFecha[0];
	}
	