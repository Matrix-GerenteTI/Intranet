<?php
	if(!isset($_SESSION)){ 
		session_start(); 
	}
	require_once("sesiones.php");
	require_once(dirname(__DIR__)."/modelos/general.php");
	$sesion = new Sesion();
	$sesion->validate();
	
	$general = new General();
	$opcion = $_GET['opc'];
	switch($opcion){
		case 'cmbCatalogo':{
			if(!isset($_POST['db']))
				$db = "dbnomina";
			else
				$db = $_POST['db'];
			
			if(!isset($_POST['where']))
				$where = "";
			else
				$where = $_POST['where'];
			
			
			$echo = '';
			$arreglo = $general->cmbCatalogo($_POST['tabla'],$where,$db);
			foreach($arreglo as $valor){
				$echo.='<option value="'.$valor[$_POST['id']].'">'.$valor[$_POST['descripcion']].'</option>';
			}
			echo $echo;
			break;
		}
		
		case 'cmbCatalogow':{
			if(!isset($_POST['where']))
				$where = "";
			else
				$where = $_POST['where'];
			$echo = '<option value="%">Todos...</option>';
			$arreglo = $general->cmbCatalogo($_POST['tabla'],$where);
			foreach($arreglo as $valor){
				$echo.='<option value="'.$valor[$_POST['id']].'">'.$valor[$_POST['descripcion']].'</option>';
			}
			echo $echo;
			break;
		}

		case 'sucursales':{
			$user = "";
			if( isset($_POST['type'])){
				$user = $_SESSION['usuario'];
			}
			$sucursales = $general->getSucursales($user);
			foreach ($sucursales as $i => $sucursal) {
				$sucursales[$i]['name'] = $sucursal['descripcion'];
			}
			echo json_encode($sucursales);
			break;
		}
	}
?>