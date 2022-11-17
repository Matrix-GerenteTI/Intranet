<?php
if(!isset($_SESSION)){ 
	session_start(); 
} 
$directorio = str_replace("\\","/",str_replace('C:\\wamp\www\\','',getcwd()));
if(!isset($_SESSION['RUTA'])){
	$_SESSION['RUTA'] = $directorio;
}
require_once($_SERVER['DOCUMENT_ROOT']."/intranet/modelos/sesiones.php");
require_once($_SERVER['DOCUMENT_ROOT']."/intranet/controladores/amortizacion.php");

class Sesion {
	function __construct() {
					
	}

	public function set($nombre, $valor) {
		$_SESSION[$nombre] = $valor;
	}

	public function get($nombre){
		if(isset($_SESSION[$nombre])){
			return $_SESSION[$nombre];
		}else{
			return false;
		}
	}
	public function borrar_variable($nombre){
		unset($_SESSION[$nombre]);
	}
	
	public function borrar_sesion(){
		$_SESSION = array();
		session_destroy();
	}
	
	public function check(){
		if(isset($_POST['usuario']) && isset($_POST['password'])){
			$msesion = new Sesiones();
			$rw = $msesion->get_usuario();
			if($rw['registros']==0){
				return false;
			}else{
				$_SESSION['nip'] = $rw['nip'];
				$_SESSION['usuario'] = $_POST['usuario'];
				$_SESSION['nombre'] = $rw['nombre'];
				$_SESSION['nivel'] = $rw['nivel'];
				$_SESSION['nivelT'] = $rw['niveltxt'];
				$_SESSION['sucursal'] = $rw['sucursal'];
				$_SESSION['usuarioprecios'] = $rw['precios'];
				$_SESSION['usuariofamilia'] = $rw['familia'];
				$_SESSION['paginaini'] = $rw['paginaini'];
				//REGISTRANDO ESTADOS FINANCIEROS
				$movimientos  = new AmortizacionControler;
				//$movimientos->registrarCtaContable();
				//$movimientos->registrarRentas();
				return true;
				
			}
		}else{
			if(!isset($_SESSION['usuario'])){
				return false;
			}else{				
				return true;
			}
		}
	}
	
	public function validate(){
		if(!isset($_SESSION['usuario'])){
			$this->borrar_sesion();
			header("Location: ./index.php");
		}
	}
	
	public function formateaFecha($dato,$trans,$invertido){
	//Patron de fecha
		$patronFecha = "/^[[0-3][0-9]\/[0-1][0-9]\/[0-9][0-9][0-9][0-9]/" ;
		if( preg_match($patronFecha , $dato) ){
			if($trans=='d2g' || $trans=='d2d'){
				$f1 = explode("/",$dato);
				if($invertido==0){
					if($trans=='d2g')
						return $f1[0]."-".$f1[1]."-".$f1[2];
					else
						return $f1[0]."/".$f1[1]."/".$f1[2];
				}else{
					if($trans=='d2g')
						return $f1[2]."-".$f1[1]."-".$f1[0];
					else
						return $f1[2]."/".$f1[1]."/".$f1[0];
				}
			}
			if($trans=='g2d' || $trans=='g2g'){
				$f1 = explode("/",$dato);
				if($invertido==0){
					if($trans=='g2g')
						return $f1[0]."-".$f1[1]."-".$f1[2];
					else
						return $f1[0]."/".$f1[1]."/".$f1[2];
				}else{
					if($trans=='g2g')
						return $f1[2]."-".$f1[1]."-".$f1[0];
					else
						return $f1[2]."/".$f1[1]."/".$f1[0];
				}
			}
			if($trans=='d2f'){
				$f1 = explode("/",$dato);
				return $f1[1]."/".$f1[0]."/".$f1[2];
			}
				}
	else{
		exit(0);
	}	
	}
	
}



	
?>