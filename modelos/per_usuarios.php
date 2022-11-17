<?php
require_once("/home/respirar/crae.respirart.mx/modelos/MySQL.php");
class Personal extends CMySQLi{
	
    public function lista(){
		$query = "SELECT 	*,
							pe.id as id,
							CONCAT(pe.nombre,' ',pe.paterno,' ',pe.materno) as nombre,
							us.user as usuario,
							pu.descripcion as puesto,
							ti.descripcion as tipo,
							ni.descripcion as nivel
				  FROM 		cpersonal pe
				  INNER JOIN cpuesto pu ON pe.fkcpuesto_id=pu.id 
				  INNER JOIN ctipo ti ON pe.fkctipo_id=ti.id 
				  INNER JOIN cusuario us ON pe.id=us.fkcpersonal_id 
				  INNER JOIN cnivel ni ON us.fkcnivel_id=ni.id 
				  WHERE 	pe.status=1";
        return $this->select($query);
    }
	
	public function cargaDatos($id){
		$query = "SELECT 	*,
							pe.id as id,
							pe.nombre as nombre,
							pe.paterno as paterno,
							pe.materno as materno,
							pe.email as email,
							pe.telefono as telefono,
							pe.sexo as sexo,
							pe.curp as curp,
							us.user as usuario,
							us.password as password,
							pu.id as puesto,
							ti.id as tipo,
							ni.id as nivel
				  FROM 		cpersonal pe
				  INNER JOIN cpuesto pu ON pe.fkcpuesto_id=pu.id 
				  INNER JOIN ctipo ti ON pe.fkctipo_id=ti.id 
				  INNER JOIN cusuario us ON pe.id=us.fkcpersonal_id 
				  INNER JOIN cnivel ni ON us.fkcnivel_id=ni.id 
				  WHERE 	pe.status=1
				  AND 		pe.id='".$id."'";
        return $this->selectU($query);
    }
	
	public function guardar($post,$tabla){
		if($tabla == "cpersonal")
			$where = "id='".$post['id']."'";
		else
			$where = "fkcpersonal_id='".$post['id']."'";
		if($post['id']>0){
			if(!$this->update($post,$tabla,$where))
				return 0;
			else
				return 1;
		}else{
			return $this->insert($post,$tabla);
		}
    }
	
} 
?>