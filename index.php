<?php
	$directorio = str_replace("\\","/",str_replace('C:\\wamp\www\\','',getcwd()));
	require_once("controladores/sesiones.php");
	$sesion = new Sesion();
	
	$pag = "admision";
	$n = 1;
	foreach($_GET as $key => $val){  
	    if($n==1){
			$pos = strpos($key,"#");
			if($pos !== false){
				$exp = explode('#',$key);
				$pag = $exp[0];
			}else{				
				$pag = $key;
			}
			
			if($pag != 'close')
				$_SESSION['uri'] = $pag;
			else
				$sesion->borrar_sesion();
			
			if($key=='view')
				$pag=$val;
		}
		$n++;
	}  
	
	//echo $sesion->check();
	//die();
	
	if($sesion->check()){
		require_once("vistas/head.php");
		//require_once("modelos/topbar.php");
		require_once("vistas/topbar.php");
		require_once("vistas/menu.php");
		
		if ( $_SESSION['uri'] != 'sgc_principal' && $_SESSION['uri'] != 'view') {
			if($_SESSION['nivel']==101)
				require_once("vistas/".$_SESSION['paginaini'].".php");
			else
				require_once("vistas/".$_SESSION['uri'].".php");
		}else if( $_SESSION['uri'] != 'view' ) {
			require_once($_SERVER['DOCUMENT_ROOT']."/intranet/vistas/SGC/".$_SESSION['uri'].".php");
		}else {
				require_once $_SERVER['DOCUMENT_ROOT']."/intranet/vistas/displayRecursosDepartamentos.php";
		}
		
		require_once("vistas/chatEngine.php");
		require_once("vistas/corejs.php");		
		require_once("vistas/modal.php");
	}else{
		require_once("vistas/login.php");
	}
?>