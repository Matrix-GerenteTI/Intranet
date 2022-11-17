<?php
	if(!isset($_SESSION)){ 
		session_start(); 
	}
	require_once("sesiones.php");
	$sesion = new Sesion();
	$sesion->validate();
	
	$opcion = $_GET['opc'];
	switch($opcion){
		case 'galeria':{
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
			$marca = $_POST['marca'];
			$linea = $_POST['linea'];
			$modelo = $_POST['modelo'];
			$sucursal = $arrSuc[$_POST['sucursal']];
			
			
			$echo = "";
			$ruta = "../../Fotos_Articulos/";
			// abrir un directorio y listarlo recursivo 
			//var_dump($_SESSION);
			$directorios = listdir($ruta,'dir');
			foreach($directorios as $directorio){
				$subdirectorios = listdir($directorio."/",'dir');
				foreach($subdirectorios as $subdirectorio){
					$subsubdirectorios = listdir($subdirectorio."/",'dir');
					foreach($subsubdirectorios as $subsubdirectorio){
						//$echo.= "<br>".$subsubdirectorio;
						$archivos = listdir($subsubdirectorio."/",'file');
						foreach($archivos as $archivo){
							if(($marca==extraeNombre($directorio,'auto') || $marca=='TODOS') && ($linea==extraeNombre($subdirectorio,'auto') || $linea=='TODOS') && ($modelo==extraeNombre($subsubdirectorio,'auto') || $modelo=='TODOS') && ($sucursal==extraeNombre($archivo,'sucursal') || $sucursal=='TODOS')){
								//$echo.= "<br>".$archivo;
								$echo.= '<div style="padding:10px; font-size: 10px; width: 150px; height:240px; margin:5px; display: inline-block; background: #FFF; border: #AAA 1px solid; text-align:center">';
								$echo.= '	<a href="'.$archivo.'" target="blank" >';
								$echo.= '		<img src="'.$archivo.'" style="height:90px; max-width: 130px; max-height:90px" border="0" />';
								$echo.= '	</a>';
								$echo.= '	<br>';
								$echo.= '	<b>MARCA:</b> '.extraeNombre($directorio,'auto').'</b></br>';
								$echo.= '	<b>LINEA:</b> '.extraeNombre($subdirectorio,'auto').'</b></br>';
								$echo.= '	<b>MODELO:</b> '.extraeNombre($subsubdirectorio,'auto').'</b></br>';
								$echo.= '	<b>'.extraeNombre($archivo,'sucursal').'</b></br>';
								$echo.= '	<b>'.date("d/m/Y", filemtime($archivo)).'</b>';
								$echo.= '	<div style="text-align:right"><a href="javascript:delImg(\''.$archivo.'\')">';
								if($_SESSION['sucursal']==$_POST['sucursal'] || $_SESSION['nivelT']=='ADMINISTRADOR')
									$echo.= '		<img src="assets/images/delete.png" height="15px" width="13" />';
								else
									$echo.= '		&nbsp;';
								$echo.= '	</a></div>';
								$echo.= '</div>';
							}
						}
					}
				}
			}
											
			echo 	$echo;
			break;
		}
		
		case 'delimagen':{
			$imagen = $_POST['imagen'];
			unlink($imagen);
			break;
		}
		
	}
	
	function extraeNombre($ruta,$tipo){
		$arr = explode("/",$ruta);
		if($tipo=='sucursal'){
			$arr2 = explode("-",$arr[count($arr)-1]);
			return $arr2[0];
		}else{
			return $arr[count($arr)-1];
		}
	}
	
	function listdir($ruta,$tipo){
		$arrFiles = array();
		if (is_dir($ruta)) { 
			if ($dh = opendir($ruta)) { 
				while (($subdir = readdir($dh)) !== false) { 
					$tmpruta = $ruta . $subdir;
					if($tipo=='dir'){
						if (is_dir($tmpruta) && $subdir!="." && $subdir!=".."){ 
						   //solo si el archivo es un directorio, distinto que "." y ".." 
						   $arrFiles[] = $tmpruta; 
						}
					}else{
						if (is_file($tmpruta) && $subdir!="." && $subdir!=".."){ 
						   //solo si el archivo es un directorio, distinto que "." y ".." 
						   $arrFiles[] = $tmpruta; 
						}
					}
				} 
				closedir($dh); 
			}
		}
		
		return $arrFiles;
	}
?>