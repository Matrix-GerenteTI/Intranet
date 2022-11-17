<?php
$return = array('ok'=>TRUE);

$marca = $_POST['marca'];
$modelo = $_POST['modelo'];
$linea = $_POST['linea'];
$sucursal = $_POST['sucursal'];
$arrSuc = array(1=>'MATRIX_MATRIZ',
				2=>'LEON_VILLAFLORES',
				3=>'LEON_5A',
				4=>'MATRIX_PALMERAS',
				5=>'MATRIX_LIBRAMIENTO',
				6=>'MATRIX_9A',
				7=>'MATRIX_5A',
				8=>'MATRIX_MERC_ALTOS',
				9=>'MATRIX_SAN_RAMON',
				10=>'LEON_9A',
				15=>'MATRIX_LAURELES',
				18=>'MATRIX_AMBAR',
				'TODOS'=>'TODOS',
				'null'=>null);

if(!file_exists("../../Fotos_Articulos/".$marca))
	mkdir("../../Fotos_Articulos/".$marca, 0777, true);
if(!file_exists("../../Fotos_Articulos/".$marca."/".$linea))
	mkdir("../../Fotos_Articulos/".$marca."/".$linea, 0777, true);
if(!file_exists("../../".$marca."/".$linea."/".$modelo))
	mkdir("../../Fotos_Articulos/".$marca."/".$linea."/".$modelo, 0777, true);

$upload_folder ="../../Fotos_Articulos/".$marca."/".$linea."/".$modelo;
$nombre_archivo = $_FILES['archivo']['name'];
$tipo_archivo = $_FILES['archivo']['type'];
$tamano_archivo = $_FILES['archivo']['size'];
$tmp_archivo = $_FILES['archivo']['tmp_name'];

$fecha = date("Ydm");
$hora = date("hms");

$extencion = explode('.',$nombre_archivo);
foreach($extencion as $ext)

$nombre = $arrSuc[$sucursal].'-'.$fecha.$hora.".".$ext;

$archivador = $upload_folder . '/' . $nombre;

if (!move_uploaded_file($tmp_archivo, $archivador)) {
	$return = array('ok' => FALSE, 'msg' => 'Ocurrio un error al subir el archivo. No pudo guardarse.', 'status' => 'error');
}

echo json_encode($return);
?>