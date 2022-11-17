<?php

require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/DB.php";

class Insumos extends DB 
{
    
    
    public function getInsumos()
    {
        $queryInsumos = "SELECT id,descripcion as name
                                        FROM cinsumos
                                        WHERE status = 1";

        return$this->select( $queryInsumos );
    }

    public function setRequisicion( $params )
    {
        

        $queryRequisicion = "INSERT INTO requisiciones(sucursal_id) VALUES($params) ";;

        return $this->insert( $queryRequisicion );
    }

    public function setDetalleRequisicion( $values)
    {
        $queryRequisicionDetail = "INSERT INTO requisicion_details(idrequisicion,idarticulo,cantidad_solicitado) VALUES  $values ";

        return $this->insert( $queryRequisicionDetail );
    }

    public function getRequisicion( $idRequisicion )
    {
      $queryRequisicion = "SELECT  fecha_solicitado,fecha_entregado,cantidad_solicitado,cantidad_entregada,cinsumos.descripcion AS item,csucursal.descripcion AS sucursal,cinsumos.id as idinsumo, requisiciones.id
                                        FROM requisiciones
                                        INNER join requisicion_details AS rd ON rd.idrequisicion = requisiciones.id
                                        INNER JOIN cinsumos ON cinsumos.id = rd.idarticulo
                                        INNER JOIN csucursal ON csucursal.id = requisiciones.sucursal_id
                                        WHERE requisiciones.id= $idRequisicion";


      return $this->select( $queryRequisicion )  ;
    }

    public function getRequisicionesSolicitado(  $entregado )
    {
        $mesActual = date('m') -1;
        $anioActual = date("Y");
        $estadoEntrega = $entregado == false ? "IS NULL" : "IS NOT NULL ";
        $querySolicitud = "SELECT  fecha_solicitado,fecha_entregado,csucursal.descripcion AS sucursal,requisiciones.id
                                        FROM requisiciones
                                        INNER JOIN csucursal ON csucursal.id = requisiciones.sucursal_id
                                        WHERE fecha_entregado $estadoEntrega AND ( (month(fecha_entregado) >= $mesActual and ( year(fecha_entregado) = $anioActual  OR year(fecha_entregado) = 2020)) OR  fecha_entregado is null ) 
                                        ORDER BY ID DESC ";
        return $this->select( $querySolicitud );
    }


    public function setSurtidoRequisicion(  $idRequisicion, $itemValues )
    {
       
       $queryUpdate = "UPDATE requisicion_details SET cantidad_entregada= '$itemValues->surtido' where idrequisicion= $idRequisicion AND idarticulo = $itemValues->id  ";

       return $this->update( $queryUpdate );

    }
    
    public function setFechaSurtido( $idRequisicion , $fecha)
    {
        $updateEntrega = "UPDATE requisiciones SET  fecha_entregado='$fecha' WHERE id=$idRequisicion   ";

        return $this->update( $updateEntrega );
    }
    
}


