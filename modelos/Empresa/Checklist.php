<?php

require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/DB.php";

class CheckList extends DB 
{
    
    public function getAllPlanAccionConPrioridad( )
    {
        $queyPlanesAccion = "SELECT prespuesta.id,plan_accion,prioridad,cumplimiento, cpregunta.descripcion AS pevaluada,csucursal.descripcion AS sucursal, cseccion.descripcion AS seccion,prespuesta.autorizado,prespuesta.csucursal_id
                                                FROM prespuesta 
                                                INNER JOIN cpregunta ON cpregunta.id = prespuesta.cpregunta_id
                                                INNER JOIN csucursal ON csucursal.id = prespuesta.csucursal_id
                                                INNER JOIN cseccion ON cseccion.id = cpregunta.cseccion_id
                                                WHERE prioridad IS NOT NULL &&  prioridad != '' ORDER BY cumplimiento desc";
        return $this->select( $queyPlanesAccion );
    }


    public function getPlanAccionFiltrado( $params )
    {
        extract( $params );
        $condicionFecha = '';
        if ( $dia != '' && $mes != '-1' && $anio != ''  ) {
            $condicionFecha = "AND cumplimiento = '$dia-$mes-$anio'";
        }else if( $dia == '' && $mes != '-1' && $anio != '' ){
            $ultimoDiaMes = cal_days_in_month(CAL_GREGORIAN , $mes , $anio);
            $condicionFecha = "AND (cumplimiento >= '01-$mes-$anio' AND cumplimiento <= '$ultimoDiaMes-$mes-$dia') ";
        }else if( $dia == '' && $mes == '-1' && $anio != ''){
            $condicionFecha = " AND year(cumplimiento) = $anio";
        }

        $sucursal = $sucursal == -1 ? "%" : $sucursal;
        
        $queyPlanesAccion = "SELECT prespuesta.id,plan_accion,prioridad,cumplimiento, cpregunta.descripcion AS pevaluada,csucursal.descripcion AS sucursal, cseccion.descripcion AS seccion,prespuesta.autorizado,prespuesta.csucursal_id
                                                FROM prespuesta 
                                                INNER JOIN cpregunta ON cpregunta.id = prespuesta.cpregunta_id
                                                INNER JOIN csucursal ON csucursal.id = prespuesta.csucursal_id
                                                INNER JOIN cseccion ON cseccion.id = cpregunta.cseccion_id
                                                WHERE csucursal.id like '$sucursal' AND prespuesta.prioridad like '$prioridad'  AND (prespuesta.prioridad is not null  AND prespuesta.prioridad != '' ) $condicionFecha
                                                ORDER BY cumplimiento desc";
        return $this->select( $queyPlanesAccion );
    }


    public function getIdpadreItemChecklist( $iditem )
    {
        $queryIds = "SELECT * FROM prespuesta WHERE  id = $iditem ";

        return $this->select( $queryIds );
    }

    public function getDetallePregunta( $preguntaId )
    {
         $queryInfo = "SELECT * FROM cpregunta WHERE  id= $preguntaId ";

         return $this->select( $queryInfo );
    }

    public function getEvidenciaPregunta( $idchecklist , $idpregunta , $idSeccion )
    {
         $queryEvidencia = "SELECT * FROM pimagenrespuesta WHERE prespuesta_id ='preg".$idSeccion."_". $idpregunta."b' and idchecklist=$idchecklist ";

         return $this->select( $queryEvidencia );
    }

    public function autorizacionPlanAccion( $id , $estado , $fecha = '' , $observaciones = '')
    {

        $updateFecha = '';
        $upobservaciones = "";

        if ( $fecha != '' ) {
            $updateFecha  = " ,cumplimiento = '$fecha' ";
        }
        if ( $observaciones != '' ) {
          $observaciones  = ",plan_accion= CONCAT(plan_accion,'@_$observaciones') ";
        }
        $queryAutorizacion = "UPDATE  prespuesta set  autorizado = $estado  $updateFecha $observaciones where id= $id ";

        return $this->update( $queryAutorizacion );
    }
}
