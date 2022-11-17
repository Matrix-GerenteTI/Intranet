<?php
session_start();
$return = array('ok'=>TRUE);

$upload_folder ='adjuntos';

$nombre_archivo = $_FILES['archivo']['name'];
$tipo_archivo = $_FILES['archivo']['type'];
$tamano_archivo = $_FILES['archivo']['size'];
$tmp_archivo = $_FILES['archivo']['tmp_name'];

$fecha = date("Ydm");
$hora = date("hms");

$extencion = explode('.',$nombre_archivo);
foreach($extencion as $ext)

$nombre = $fecha.$hora.".".$ext;

$archivador = $upload_folder . '/' . $nombre;
$_SESSION['archivo'] = $nombre;

if (!move_uploaded_file($tmp_archivo, $archivador)) {
	$return = array('ok' => FALSE, 'msg' => 'Ocurrio un error al subir el archivo. No pudo guardarse.', 'status' => 'error');
}

echo json_encode($return);
?>