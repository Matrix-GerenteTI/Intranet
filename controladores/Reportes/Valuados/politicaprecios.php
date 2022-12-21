<?php
// ini_set('memory_limit', '-1');

set_time_limit(0);
ini_set('memory_limit', '20000M');

require_once "C:\\wamp\\www\\intranet\\modelos\\almacenes\\ArticulosPolitica.php";
require_once "C:\\wamp\\www\\intranet\\modelos\\Reportes\\ReportesPolitica.php";
$host = "172.16.0.70:/var/lib/firebird/3.0/data/PREDICTION.FDB";
$user="SYSDBA";
$pass="masterkey";

$conexion = @ibase_pconnect($host,$user,$pass) or die("Error al conectarse a la base de datos: ".ibase_errmsg());

$fileInput = "";
//$articulos = new Articulos;
if(isset($_GET['fecha']))
    $fecha = $_GET['fecha'];
else
    $fecha = date('d.m.Y');
//$fecha = '22.02.2022';
$queryUPD = '';
$arrItems = array();
$arrProds = array();
$query = "SELECT  d.CODIGO as CODIGO,d.FK1MREF_ARTXALMACEN as FK1MREF_ARTXALMACEN,d.COSTO as COSTO
            FROM    REF_COMPRASTRASPREGS c
            INNER JOIN REF_DETCOMPRASTRASPREGS d ON c.ID=d.FKPADREF_COMPRASTRASPREGS
            WHERE   c.FECHA='".$fecha."'
            AND     c.STATUS IN ('COMPRA EMITIDO','ENTRADA EMITIDO')
            ORDER BY c.ID DESC";
$sentence = ibase_query($conexion,$query);
while($row = ibase_fetch_assoc($sentence)){
    if(!in_array($row['CODIGO'],$arrItems)){
        $arrItems[] = $row['CODIGO'];
        $query2 = "SELECT  a.MARCA as MARCA,a.FAMILIA as FAMILIA,a.SUBFAMILIA as SUBFAMILIA,a.DESCRIPCION as DESCRIPCION,a.ID as IDARTICULO
                    FROM    REF_ARTXALMACEN axa
                    INNER JOIN CFG_ARTICULOS a ON a.ID=axa.FK1MCFG_ARTICULOS
                    WHERE   axa.ID=".$row['FK1MREF_ARTXALMACEN'];
        $sentence2 = ibase_query($conexion,$query2);
        while($row2 = ibase_fetch_assoc($sentence2)){
            //Valida en la tabla de precios mysql cual es su configuración de precios
            if($row2['FAMILIA']=='LLANTA'){
                $expMedida = explode(" ", $row2['DESCRIPCION']);
                $configPrecios = getPoliticaPrecios($row2['FAMILIA'],$expMedida[0],'','');
            }else{
                $configPrecios = getPoliticaPrecios($row2['FAMILIA'],$row2['SUBFAMILIA'],'',$row2['MARCA']);
            } 
 
            if(is_array($configPrecios)){
                $queryUPD.= "UPDATE REF_ARTXALMACEN SET CTOPROMEDIO=".number_format($row['COSTO'],2,'.','')." WHERE FK1MCFG_ARTICULOS=".$row2['IDARTICULO'].";</br>";
                $queryUPD1 = "UPDATE REF_ARTXALMACEN SET CTOPROMEDIO=".number_format($row['COSTO'],2,'.','')." WHERE FK1MCFG_ARTICULOS=".$row2['IDARTICULO'];
                ibase_query($conexion,$queryUPD1);
                $pvp1 = number_format((($row['COSTO']*1.16) * (1 + ($configPrecios['pvp1']/100))),2,'.','');
                $pvp2 = number_format((($row['COSTO']*1.16) * (1 + ($configPrecios['pvp2']/100))),2,'.','');
                $pvp3 = number_format((($row['COSTO']*1.16) * (1 + ($configPrecios['pvp3']/100))),2,'.','');
                $pvp4 = number_format((($row['COSTO']*1.16) * (1 + ($configPrecios['pvp4']/100))),2,'.','');
                $pvp5 = number_format((($row['COSTO']*1.16) * (1 + ($configPrecios['pvp5']/100))),2,'.','');
                $queryUPD.= "UPDATE CFG_PRECIOSXALMACENES SET PVP1='".$pvp1."',PVP2='".$pvp2."',PVP3='".$pvp3."',PVP4='".$pvp4."',PVP5='".$pvp5."',PVP10='".$pvp3."' WHERE FK1MCFG_ARTICULOS=".$row2['IDARTICULO'].";</br>";
                $queryUPD2 = "UPDATE CFG_PRECIOSXALMACENES SET PVP1='".$pvp1."',PVP2='".$pvp2."',PVP3='".$pvp3."',PVP4='".$pvp4."',PVP5='".$pvp5."',PVP10='".$pvp3."' WHERE FK1MCFG_ARTICULOS=".$row2['IDARTICULO'];
                ibase_query($conexion,$queryUPD2);
            }
        }
    }    
}

$fch= fopen('updprecios.txt', "w"); // Abres el archivo para escribir en �l
fwrite($fch, $queryUPD); // Grabas
fclose($fch); // Cierras el archivo.
echo $queryUPD;

function getPoliticaPrecios( $familia , $subfamilia, $llanta, $marca ){
    $conexion = new mysqli('127.0.0.1','sestrada','M@tr1x2017','dbnomina');
    mysqli_query($conexion,"SET NAMES 'utf8");
    mysqli_set_charset( $conexion ,"utf8");

    $array = array();
    $b1 = 0;
    $m1 = 0;
    if($marca != '')
    {
        $querymarca = "SELECT * FROM prediction.politica_precios WHERE marca='".$marca."'";
        $sqlmarca = $conexion->query($querymarca);
        while($rowm = $sqlmarca->fetch_assoc())
        {
            $m1++;
            $array[] = $rowm;
        }
        if ($m1 > 0) {
            return $array[0];
        }
    }
    $queryPolitica = "SELECT * FROM  prediction.politica_precios 
                    WHERE familia='".$familia."' AND subfamilia='".$subfamilia."'";
    $sql = $conexion->query($queryPolitica);
    while($row = $sql->fetch_assoc()){
        $b1++;
        $array[] = $row;
    }
    if($b1==0){            
        $b2 = 0;
        $queryPolitica2 = "SELECT * FROM  prediction.politica_precios WHERE familia='".$familia."' AND subfamilia='ALL'";
        $sql2 = $conexion->query($queryPolitica2);
        while($row2 = $sql2->fetch_assoc()){
            $b2++;
            $array[] = $row2;
        }
        if($b2 == 0){
            return 0;
        }else{
            return $array[0];
        }
    }else{
        return $array[0];
    }
}
?>