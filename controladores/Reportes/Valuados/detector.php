<?php
// ini_set('memory_limit', '-1');

set_time_limit(0);
ini_set('memory_limit', '20000M');

require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/almacenes/Articulos.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/Reportes/Reportes.php";
$host = "172.16.0.70:/var/lib/firebird/3.0/data/PREDICTION.FDB";
$user="SYSDBA";
$pass="masterkey";

$conexion = @ibase_pconnect($host,$user,$pass) or die("Error al conectarse a la base de datos: ".ibase_errmsg());

$fileInput = "";
$queryUPD = '';
$arrItems = array();
$arrProds = array();
$query = "SELECT    ART.CODIGOARTICULO as CODIGO, 
                    ART.DESCRIPCION as DESCRIPCION,
                    (SELECT FIRST 1 c.FECHA FROM REF_COMPRASTRASPREGS c 
                        INNER JOIN REF_DETCOMPRASTRASPREGS d ON c.ID=d.FKPADREF_COMPRASTRASPREGS
                        WHERE c.STATUS IN ('COMPRA EMITIDO','ENTRADA EMITIDO')
                        ORDER BY c.ID DESC) as FECULTCOMP  
          FROM      CFG_PRECIOSXALMACENES PREC 
          INNER JOIN CFG_ARTICULOS ART ON PREC.FK1MCFG_ARTICULOS=ART.ID
          INNER JOIN REF_ARTXALMACEN AXA ON ART.ID=AXA.FK1MCFG_ARTICULOS AND PREC.FK1MCFG_ALMACENES=AXA.FK1MCFG_ALMACENES
          WHERE     (AXA.EXISTOTAL + AXA.EXISPROCESO + AXA.EXISPEDIDOS)>0 
          AND       AXA.FK1MCFG_ALMACENES=10754 
          AND       (PREC.PVP1='0' OR PREC.PVP1='')";
$sentence = ibase_query($conexion,$query);
while($row = ibase_fetch_assoc($sentence)){
    
}

?>