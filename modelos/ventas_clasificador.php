<?php
if(!isset($_SESSION)){ 
	session_start(); 
}
require_once(dirname(__DIR__)."/modelos/MySQL.php");
class General extends CMySQLi{
	
	function getSucursales($user){
		$echo = "";
		$ruta = ""/Fotos_Articulos"";
		// abrir un directorio y listarlo recursivo 
		if (is_dir($ruta)) { 
		  if ($dh = opendir($ruta)) { 
			 while (($file = readdir($dh)) !== false) { 
				if (is_dir($ruta . $file) && $file!="." && $file!=".."){ 
				   //solo si el archivo es un directorio, distinto que "." y ".." 
				   $echo.= "<br>Directorio: $ruta$file"; 
				} 
			 } 
		  closedir($dh); 
		  } 
		}else 
		  $echo.="<br>No es ruta valida"; 
										
		echo $echo;
	}
	
} 
?>