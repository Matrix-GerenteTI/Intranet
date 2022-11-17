<?php
if(!isset($_SESSION)){ 
	session_start(); 
}
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/MySQL.php";

class Sesiones extends CMySQLi{
	
    public function get_usuario(){
		$query = "SELECT 	COUNT(*) as registros,
							p.nip as nip,
							p.nombre as nombre,
							u.tipo as nivel,
							t.descripcion as niveltxt,
							p.idsucursal as sucursal,
							pro.familia as familia,
							pro.precios as precios,
							pro.paginaini as paginaini
				  FROM 		pusuarios u
				  INNER JOIN pempleado p ON u.idempleado=p.nip
				  INNER JOIN ctipousuario t ON u.tipo=t.id
				  LEFT JOIN pusuarioproductos pro ON u.idempleado=pro.idempleado
				  WHERE 	u.username='".$_POST['usuario']."' 
				  AND 		u.password='".$_POST['password']."'";
        return $this->selectU($query);
    }

	public function get_modulos(){
		$query = "SELECT 	mod.*
				  FROM 		pusuarios u
				  INNER JOIN pusuariomodulos mod ON u.idempleado=mod.idempleado
				  WHERE 	u.idempleado='".$_SESSION['nip']."'";
        return $this->selectU($query);
    }

    public function getUser( $user , $pass){
		$query = "SELECT 	COUNT(*) as registros,
							p.nip as nip,
							p.nombre as nombre,
							u.tipo as nivel,
							t.descripcion as niveltxt,
							p.idsucursal as sucursal,
							u.username as username
				  FROM 		pusuarios u
				  INNER JOIN pempleado p ON u.idempleado=p.nip
				  INNER JOIN ctipousuario t ON u.tipo=t.id
				  WHERE 	u.username='$user'
				  AND 		u.password='$pass' ";
        return $this->selectU($query);
    }
	
} 
?>