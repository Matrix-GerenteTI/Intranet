<?php
if(!isset($_SESSION)){ 
	session_start(); 
}
require_once "C:\\wamp\\www\\intranet\config.ini.php";
class CMySQLi{
	protected $db;
	protected $dbEshop;
	
    public function __construct(){
		$this->db=new mysqli(SERVER, USUARIO, CONTRA, DB);
        $this->db->query("SET NAMES 'utf8'");
		$this->dbEshop=new mysqli(SERVER, USUARIO, CONTRA, 'matrix_eshop');
        $this->dbEshop->query("SET NAMES 'utf8'");
	}
	
    public function insert($post,$tabla, $bd='dbnomina'){
		$query = "INSERT INTO ".$tabla." (";
		foreach($post as $idx => $val){
			if($idx!='id')
				$query.= $idx.",";
		}
		$query = substr($query,0,-1);
		$query.= ") VALUES (";
		foreach($post as $idx => $val){
			if($idx!='id'){
				if($idx=='fecha' || $idx=='hora')
					$query.= "NOW(),";
				else
					$query.= "'".$val."',";
			}
		}
		$query = substr($query,0,-1);
		$query.= ")";
		if($bd=='dbnomina')
			$consulta=$this->db->query($query);
		else
			$consulta=$this->dbEshop->query($query);
		
		if(!$consulta)
			return 0;
		else
			return $this->db->insert_id;
    }
	
	public function update($post,$tabla,$where, $bd='dbnomina'){
		$query = "UPDATE ".$tabla." SET ";
		foreach($post as $idx => $val){
			if($idx!='id')
				$query.= $idx."='".$val."',";
		}
		$query = substr($query,0,-1);
		$query.= " WHERE ".$where;
		
        if($bd=='dbnomina')
			$consulta=$this->db->query($query);
		else
			$consulta=$this->dbEshop->query($query);
		
		if(!$consulta)
			return false;
		else
			return true;
		
    }
	
	public function eliminar($where,$tabla, $bd='dbnomina'){
		$query = "DELETE FROM ".$tabla." WHERE ".$where;
        if($bd=='dbnomina')
			$consulta=$this->db->query($query);
		else
			$consulta=$this->dbEshop->query($query);
		
		if(!$consulta)
			return false;
		else
			return true;
		
    }
	
	public function select($query, $bd='dbnomina'){
		$array = array();
		if($bd=='dbnomina')
			$consulta=$this->db->query($query);
		else
			$consulta=$this->dbEshop->query($query);
		//echo $query;
		//die();
		if(mysqli_num_rows($consulta) ){
			while($row = $consulta->fetch_assoc()){
				$array[] = $row;
			}
		}
        return $array;
    }
	
	public function selectU($query, $bd='dbnomina'){
		$array = array();
		if($bd=='dbnomina')
			$consulta=$this->db->query($query);
		else
			$consulta=$this->dbEshop->query($query);
		
		return $row = $consulta->fetch_assoc();
	}
	
	public function insertDefined($query, $bd='dbnomina'){
		if($bd=='dbnomina')
			return $consulta=$this->db->query($query);
		else
			return $consulta=$this->dbEshop->query($query);
		
	}

	public function updateTable( $query )
	{
		   $this->db->query($query );
			return $this->db->affected_rows;
    }
    
    public function dateFormatDB( $date )
    {
        $explodeDate = explode("/", $date);
        
        return $explodeDate[2]."-".$explodeDate[0]."-".$explodeDate[1];
    }

}

?>