<?php

$uuid = $_POST['uuid'];
$fecha = $_POST['fecha'];
$folio = $_POST['folio'];

$rutaXml = $_SERVER['DOCUMENT_ROOT']."/intranet/xmlSAT/";

$directorio = opendir($rutaXml);

$resultados = array();
while ( $archivo= readdir($directorio) ) {
            
        if ( preg_match("/$uuid/" , $archivo) ) {
            
            if ( strlen($archivo) > 2) {
                
                $xml = simplexml_load_file($rutaXml."".$archivo);
                $dateXmlFormat = explode( "T", $xml->attributes()->{'Fecha'} );
                $fechaXml = $dateXmlFormat[0];
                $folioXml =  $xml->attributes()->{'Folio'}; 
                $archivo = str_replace(".xml" , "" ,$archivo);
                
                if (  $fecha != "" &&  $folio !="") { //Busca en los Xml la fecha y la hora
                    if ( $fechaXml == $fecha && $folioXml == $folio ) { //array push a coincidencias leyendo los valores necesarios del xml
                        array_push($resultados , getDatosFactura($xml, $archivo) );
                    } else {
                        # code...
                    }
                    

                }elseif ( $fecha != "" && $folio == "" ) { //Busca en los archivos las coincidencias por fecha
                    if ( $fecha == $fechaXml) {//array push a coincidencias leyendo los valores necesarios del xml
                        array_push($resultados , getDatosFactura($xml, $archivo) );
                    } else {
                        # code...
                    }
                    
                }elseif( $fecha == "" && $folio != ""){ //Muestra todos los archivos que cumplan con la coincidencia del nombre
                    if ( $folio == $folioXml) {
                        array_push($resultados , getDatosFactura($xml, $archivo) );
                    } else {
                    }  
                }else{
                    array_push($resultados , getDatosFactura($xml, $archivo) );
                }
             }
        } else {
            
        }
    
        
}
echo  json_encode( $resultados);
function getDatosFactura($xml , $uuid){
    
    $folioXml =  $xml->attributes()->{'Folio'}; 
    $fecha =  $xml->attributes()->{'Folio'}; 
    $dateXmlFormat = explode( "T", $xml->attributes()->{'Fecha'} );
    $fechaXml = $dateXmlFormat[0];
    $subTotal = $xml->attributes()->{'SubTotal'}; 
    $total = $xml->attributes()->{'Total'}; 
    //DATOS DEL EMISOR
    $emisorPath = $xml->xpath("/cfdi:Comprobante/cfdi:Emisor");
    
    $rfcEmisor =$emisorPath[0]->attributes()->{'Rfc'};
    
    $emisorNombre=$emisorPath[0]->attributes()->{'Nombre'};
    //OBTENIENDO CONCEPTO DE PAGO
    $conceptoXml = $xml->xpath("//cfdi:Comprobante//cfdi:Concepto");
    
    $concepto = $conceptoXml[0]->attributes()->{'Descripcion'};

    $factura = array(
        'uuid' => $uuid,
        'folio' => $folioXml,
        'fecha' => $fechaXml,
        'emisor' => $emisorNombre,
        'rfcEmisor' => $rfcEmisor,
        'concepto' => $concepto,
        'subtotal' => $subTotal,
        'total' => $total
    );

    return $factura;
}

// $x = $facturas->getDocNamespaces();

// $componente = $facturas->children();

// $propiedad =  $componente->children();

// $atributos = $propiedad->attributes()->name;

// var_dump($facturas);
// var_dump( $facturas->attributes()->{'Fecha'} );