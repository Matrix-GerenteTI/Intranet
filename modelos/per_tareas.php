<?php
if(!isset($_SESSION)){ 
	session_start(); 
}
require_once(dirname(__DIR__)."/modelos/MySQL.php");
class Tareas extends CMySQLi{
	
    public function lista(){
		$query = "SELECT 	*,
							rt.id as id,
							ta.descripcion as tarea,
							IFNULL(de.descripcion,'') as departamento,
							IFNULL(pu.descripcion,'') as puesto,
							IFNULL(em.nombre,'') as empleado
				  FROM 		rtarea rt
				  INNER JOIN ctarea ta ON rt.idtarea=ta.id
				  LEFT JOIN cdepartamento de ON rt.iddepartamento=de.id
				  LEFT JOIN cpuesto pu ON rt.idpuesto=pu.id
				  LEFT JOIN pempleado em ON rt.nip=em.nip
				  WHERE 	rt.status=1";
        return $this->select($query);
    }
	
	public function tareasUsuario(){
		$q1 = "SELECT	c.iddepartamento as departamento,
						c.idpuesto as puesto,
						e.nip as empleado
			   FROM 	pempleado e
			   INNER JOIN pcontrato c ON e.nip=c.nip
			   WHERE 	e.nip=".$_SESSION['nip'];
		$rw = $this->selectU($q1);	   
		$iddepartamento = $rw['departamento'];
		$idpuesto = $rw['puesto'];
		$nip = $rw['empleado'];
		$hoy = date("Y-m-d");
		$diasemhoy = (int)date("w");
		$diameshoy = (int)date("d");
		$meshoy = (int)date("m");
		$meses31 = array(4,6,9,11);
		$meses30 = array(1,3,5,7,8,10,12);
		$query = "SELECT 	*,
							rt.id as id,
							ta.descripcion as descripcion,
							IFNULL(de.descripcion,'') as departamento,
							IFNULL(pu.descripcion,'') as puesto,
							IFNULL(em.nombre,'') as empleado,
							IFNULL(pt.TIMESTAMP,'') AS fechacheck,
							IFNULL(pt.STATUS,0) AS status
				  FROM 		rtarea rt
				  INNER JOIN ctarea ta ON rt.idtarea=ta.id
				  LEFT JOIN cdepartamento de ON rt.iddepartamento=de.id
				  LEFT JOIN cpuesto pu ON rt.idpuesto=pu.id
				  LEFT JOIN pempleado em ON rt.nip=em.nip
				  LEFT JOIN ptarea pt ON pt.idrtarea=rt.id
				  WHERE 	rt.status=1 ";
		if($idpuesto>0)		  
			$query.= "AND		(rt.idpuesto=".$idpuesto." OR rt.nip=".$nip.") ";
		else
			$query.= "AND		(rt.iddepartamento=".$iddepartamento." OR rt.idpuesto=".$idpuesto." OR rt.nip=".$nip.") ";
		$query.= "AND 		ta.fechainicio<='".$hoy."'";
		$array = array();
		$arreglo = $this->select($query);
		foreach($arreglo as $row){
			$bandera = 0;
			if(strlen($row['fechacheck'])>10){
				$xpl2 = explode(" ",$row['fechacheck']);
				$fechacheck = $xpl2[0];
				if($hoy==$fechacheck)
					$row['status'] = 1;
				else
					$row['status'] = 0;
			}
			if($row['programacion']=='diariamente'){
				$array[] = $row;
			}
			if($row['programacion']=='semanalmente'){				
				if($diasemhoy == $row['diasemana']){
					$array[] = $row;
				}
			}
			if($row['programacion']=='quincenalmente'){
				if($diameshoy==15)
					$array[] = $row;
				if($diameshoy==28 && $meshoy==2)
					$array[] = $row;
				if($diameshoy==30 && in_array($meshoy,$meses30))
					$array[] = $row;
				if($diameshoy==31 && in_array($meshoy,$meses31))
					$array[] = $row;
			}
			if($row['programacion']=='mensualmente'){
				if($diameshoy==$row['diames'])
					$array[] = $row;
			}
			if($row['programacion']=='especificos'){
				$xplo = explode(",",$row['dias']);
				foreach($xplo as $v){
					if($diasemhoy == $v){
						$array[] = $row;
					}
				}
			}
		}
        return $array;
    }
	
	 public function listaUsuarios(){
		$query = "SELECT 	*,
							per.nip as id,
							per.nombre as nombre,
							pue.descripcion as puesto
				  FROM 		pcontrato con
				  INNER JOIN pempleado per ON con.nip=per.nip
				  INNER JOIN cpuesto pue ON con.idpuesto=pue.id
				  WHERE 	per.status=1";
        return $this->select($query);
    }
	
	public function cargaDatos($id){
		$query = "SELECT 	*,
							tar.descripcion as descripcion,
							tar.fechainicio as fechainicio,
							tar.horainicio as horainicio,
							tar.fechafin as fechafin,
							tar.horafin as horafin,
							rel.iddepartamento as departamento,
							rel.idpuesto as puesto,
							rel.nip as idempleado,
							IFNULL(emp.nombre,'') as empleado,
							tar.programacion as programacion,
							tar.diasemana as diasemana,
							tar.dias as dias,
							tar.diames as diames
				  FROM 		rtarea rel
				  INNER JOIN ctarea tar ON rel.idtarea=tar.id
				  LEFT JOIN pempleado emp ON rel.nip=emp.nip
				  WHERE 	rel.status=1
				  AND 		rel.id='".$id."'";
        return $this->selectU($query);
    }
	
	public function guardar($post,$tabla){
		$where = "id='".$post['id']."'";
		if($post['id']>0){
			if(!$this->update($post,$tabla,$where))
				return 0;
			else
				return 1;
		}else{
			return $this->insert($post,$tabla);
		}
    }
	
	public function elimina($where,$tabla){
		if(!$this->eliminar($where,$tabla))
			return 0;
		else
			return 1;
    }
	
} 
?>