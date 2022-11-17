<?php
if(!isset($_SESSION)){ 
	session_start(); 
}
require_once(dirname(__DIR__)."/modelos/MySQL.php");
class General extends CMySQLi{
    public function cmbCatalogo($tabla,$where, $bd='dbnomina'){
		$array = array();
		
		if($tabla=='csucursal'){
			$q1 = "SELECT 	*
				  FROM 		pusuariosucursal
				  WHERE		username='".$_SESSION['usuario']."' 
				  AND		status=1";
			$arr = $this->select($q1,$bd);
			$in = "0";
			foreach($arr as $row){
				$in.= ",".$row['idcsucursal'];
			}
			$where = "id IN (".$in.") ";
		}
		
		if($where=="")
			$query="SELECT * FROM ".$tabla;
		else
			$query="SELECT * FROM ".$tabla." WHERE status=1 AND ".$where;
		//echo $query;
		//die();
		return $this->select($query,$bd);
	}
	
	function getSucursales($user = '' ){
		$querySucursal = "SELECT csucursal.id,csucursal.descripcion 
										FROM csucursal
										INNER JOIN pusuariosucursal as puc ON puc.idcsucursal = csucursal.id
										WHERE puc.username LIKE '%$user%'
										AND   csucursal.status=1
										GROUP BY csucursal.id ";
										
		return $this->select($querySucursal);
	}	
	
} 
?>