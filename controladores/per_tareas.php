<?php
	if(!isset($_SESSION)){ 
		session_start(); 
	}
	require_once(dirname(__DIR__)."/controladores/sesiones.php");
	require_once(dirname(__DIR__)."/modelos/per_tareas.php");
	$sesion = new Sesion();
	//$sesion->validate();
	
	$tareas = new Tareas();
	$opcion = $_GET['opc'];
	switch($opcion){
		case 'lista':{
			$arreglo = $tareas->lista();
			echo json_encode($arreglo);
			break;
		}
		
		case 'listaModal':{
			$arreglo = $tareas->listaUsuarios();
			echo json_encode($arreglo);
			break;
		}
				
		case 'tareasUsuario':{
			$arreglo = $tareas->tareasUsuario();			
			echo json_encode($arreglo);
			break;
		}
				
		case 'checkTarea':{
			$array = array();
			$array['id'] = 0;
			$array['observaciones'] = $_POST['observaciones'];
			$array['idrtarea'] = $_POST['id'];
			$res = $tareas->guardar($array,'ptarea');
			echo $res;
			break;
		}
		
		case 'eliminaCheck':{
			$res = $tareas->elimina("idrtarea=".$_POST['id'],'ptarea');
			echo $res;
			break;
		}
		
		case 'updFotoTarea':{
			$array = array();
			$array['id'] = $_POST['id'];
			$array['foto'] = $_SESSION['archivo'];
			$res = $tareas->guardar($array,'ptarea');
			echo $res;
			break;
		}
		
		case 'cargaDatos':{
			$arreglo = $tareas->cargaDatos($_POST['id']);
			echo json_encode($arreglo);
			break;
		}
		
		case 'guarda':{
			$arrtarea = array('id','descripcion','fechainicio','horainicio','fechafin','horafin','programacion','dias','diasemana','diames');
			$arrayT = array();
			$arrayR = array();
			foreach($_POST as $idx => $val){
				if(in_array($idx,$arrtarea)){
					if($idx == 'fechainicio' || $idx == 'fechafin'){
						if($val == "")
							$val = "0000/00/00";
						$xpl = explode("/",$val);
						$arrayT[$idx] = $xpl[2]."-".$xpl[1]."-".$xpl[0];
					}else{
						$arrayT[$idx] = $val;
					}
				}
			}
			$arrayT['nip'] = $_POST['idempleado']; //$_SESSION['nip'];
			//print_r($arrayT);
			//die();
			$res1 = $tareas->guardar($arrayT,'ctarea');
			
			$arrayR['id'] = "";
			$arrayR['idtarea'] = $res1;
			$arrayR['iddepartamento'] = $_POST['departamento'];
			$arrayR['idpuesto'] = $_POST['puesto'];
			$arrayR['nip'] = $_POST['idempleado'];
			$res2 = $tareas->guardar($arrayR,'rtarea');
			
			$res = $res1 * $res2;
			echo $res;
			break;
		}
	}
?>