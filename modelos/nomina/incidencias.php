<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/intranet/modelos/DB.php';

class Incidencias extends Db
{
    public function setIncidencia( $params )
    {
        extract( $params );

        $setIncidencias = "INSERT INTO `pdeducciones` (`idtipodeduccion`, `importe`, `status`, `idcontrato`, `fechaCargo`, `descripcion`) VALUES ('$tipoDeduccion', $monto, 1, $contratoId, '$fechaAplicacion',  '$observaciones')";

        return $this->insert( $setIncidencias );
    }

    public function actualizaImporte( $idIncidencia , $importe)
    {
        $updateImporte = "UPDATE pdeducciones SET importe= '$importe' WHERE id= $idIncidencia ";

        
        return $this->update( $updateImporte );
    }

    public function quitaIncidencia( $idIncidencia )
    {
        $updateImporte = "UPDATE pdeducciones SET status= '99' WHERE id= $idIncidencia ";

        
        return $this->update( $updateImporte );
    }

    public function getDetalleIncidencia( $idIncidencia)
    {
         $queryDetalle = "SELECT * FROM pdeducciones WHERE id= $idIncidencia ";

         return $this->select( $queryDetalle );
    }

    public function getDeducciones( $contratoId, $mes)
    {
        $queryDeducciones = "SELECT importe,fechaCargo,descripcion 
                        from pdeducciones
                        right join ctipodeduccion ON ctipodeduccion.id = pdeducciones.idtipodeduccion
                        where pdeducciones.idcontrato = $contratoId and month(fechaCargo) = $mes and pdeducciones.status =1";
        return $this->select( $queryDeducciones );
    }

    public function getTipoIncidencia()
    {
        $queryTipoIncidencia = "SELECT *,descripcion as name  
                        from ctipoincidencia
                        where status =1";
        return $this->selectnew( $queryTipoIncidencia );
    }

    public function getIncidencias($tipo)
    {
        $tipo==1?$tabla='ctipodeduccion':$tabla='ctipopercepcion';
        $queryIncidencias = "SELECT *,descripcion as name  
                        from dbnomina_new.".$tabla."
                        where status =1";
        return $this->selectnew( $queryIncidencias );
    }

    public function delIncidencia($id)
    {
        $query = "UPDATE sitexcloud.pincidenciasusuario SET status=99 WHERE id=".$id;
        return $this->update( $query );
    }

    public function autIncidencia($id,$monto,$numpagos)
    {
        $query = "UPDATE sitexcloud.pincidenciasusuario SET status=1,monto=".$monto.",numpagos=".$numpagos." WHERE id=".$id;
        $result = $this->update( $query );
        if($result>0){
            $q0 = "SELECT * FROM sitexcloud.pincidenciasusuario WHERE id=".$id;
            $s0 = $this->selectnew($q0);
            foreach($s0 as $rw){
                $quincena = $rw['fechaAplicar'];
                for($i=0; $i<$rw['numpagos']; $i++){   
                    $L = new DateTime($quincena);
                    $fechaComoEntero = strtotime($quincena);
                    $dia = date("d", $fechaComoEntero);
                    $parcialidad = $rw['monto']/$rw['numpagos'];
                    //Insert
                    $q1 = "INSERT INTO ppagosdeducciones (idpincidenciasusuario,
                                                          monto,
                                                          fechadepago,
                                                          status) 
                                                VALUES   (".$id.",
                                                          ".$parcialidad.",
                                                          '".$quincena."',
                                                          0)";
                    //die($q1);
                    $this->insertnew($q1);

                    if($dia == 15)
                    {
                        $quincena = $L->format( 'Y-m-t' );
                    }
                    else if($quincena == $L->format( 'Y-m-t' ))
                    {
                        $quincena = date("Y-m-d",strtotime($L->format( 'Y-m-t' )."+ 15 days"));
                    }
                }
            }

            $qry = "SELECT  iu.id as id,
                            emp.nip as nip,
                            emp.nombre as nombre,
                            suc.descripcion as sucursal,
                            emp.celular as celular,
                            IFNULL(iu.monto,0) as monto,
                            IFNULL(iu.idtipoincidencia,1) as idtipoincidencia,
                            tip.descripcion as tipoincidencia,
                            CASE WHEN iu.idtipoincidencia=1 
                                THEN (SELECT descripcion FROM sitexcloud.ctipodeduccion WHERE id=iu.idincidencia ) 
                                ELSE (SELECT descripcion FROM sitexcloud.ctipopercepcion WHERE id=iu.idincidencia ) 
                            END as incidencia,
                            IFNULL(iu.status,0) as statusincidencia,
                            (con.salariodiario * 15) as quincena
                    FROM    sitexcloud.pincidenciasusuario iu
                    INNER JOIN dbnomina.pempleado emp ON iu.idempleado=emp.nip
                    INNER JOIN dbnomina.csucursal suc ON emp.idsucursal=suc.id 
                    INNER JOIN dbnomina.pcontrato con ON con.nip=emp.nip 
                    INNER JOIN sitexcloud.ctipoincidencia tip ON iu.idtipoincidencia=tip.id
                    WHERE   emp.status<>99 
                    AND     iu.id=".$id;
            return $this->selectnew( $qry );

        }else{
            return 0;
        }
        
    }

    
    public function addIncidencia($idempleado,$idtipoincidencia,$idincidencia,$fechaaplicacion,$fechadescuento,$monto,$numpagos,$observaciones)
    {
        $fechaA = substr($fechaaplicacion,6,4).'-'.substr($fechaaplicacion,3,2).'-'.substr($fechaaplicacion,0,2);
        $fechaD = substr($fechadescuento,6,4).'-'.substr($fechadescuento,3,2).'-'.substr($fechadescuento,0,2);
        $q0 = "SELECT * FROM dbnomina_new.pcontrato WHERE nip=".$idempleado;
        $s0 = $this->selectnew($q0);
        $contrato = 0;
        foreach($s0 as $r0){
            $contrato = $r0['id'];
        }

        if($idtipoincidencia==1){
            $q2 = "INSERT INTO dbnomina_new.pdeducciones (idtipodeduccion,
                                                importe,
                                                status,
                                                idcontrato,
                                                fechaCargo,
                                                descripcion) 
                                        VALUES   ('".$idincidencia."',
                                                '".$monto."',
                                                1,
                                                '".$contrato."',
                                                '".$fechaD."',
                                                '".$observaciones."')";
        }else{
            $q2 = "INSERT INTO dbnomina_new.ppercepciones (idtipopercepcion,
                                                gravado,
                                                excento,
                                                valormercado,
                                                preciootrgarse,
                                                status,
                                                idcontrato) 
                                        VALUES   ('".$idincidencia."',
                                                '".$monto."',
                                                0,
                                                0,
                                                0,
                                                '".$contrato."')";
        }
        $s2 = $this->insertnew( $q2 );
        if($s2){
            $q1 = "INSERT INTO pincidenciasusuario (idempleado,
                                                idtipoincidencia,
                                                idincidencia,
                                                fecha,
                                                monto,
                                                numpagos,
                                                fechaAplicar,
                                                observaciones,
                                                status) 
                                        VALUES   (".$idempleado.",
                                                '".$idtipoincidencia."',
                                                '".$idincidencia."',
                                                '".$fechaA."',
                                                ".$monto.",
                                                ".$numpagos.",
                                                '".$fechaD."',
                                                '".$observaciones."',
                                                0)";
            return $this->insertnew( $q1 );
        }else{
            0;
        }

    }

    public function getNomina($nivelUsuario)
    {   
        //Aquí controlamos los permisos
        $nivelUsuario==155?$in='0,1,2':$in='0,1';
        $queryNomina = "SELECT  IFNULL(iu.id,0) as id,
                                emp.nip as nip,
                                emp.nombre as empleado,
                                suc.descripcion as sucursal,
                                IFNULL(iu.monto,0) as monto,
                                IFNULL(iu.numpagos,0) as numpagos,
                                IFNULL(iu.idtipoincidencia,1) as idtipoincidencia,
                                CASE WHEN iu.idtipoincidencia=1 
                                    THEN (SELECT descripcion FROM dbnomina_new.ctipodeduccion WHERE id=iu.idincidencia ) 
                                    ELSE (SELECT descripcion FROM dbnomina_new.ctipopercepcion WHERE id=iu.idincidencia ) 
                                END as incidencia,
                                IFNULL(iu.status,0) as statusincidencia,
                                IFNULL(iu.observaciones,'') as observaciones,
                                (con.salariodiario * 15) as quincena
                        FROM    dbnomina.pempleado emp
                        INNER JOIN dbnomina.csucursal suc ON emp.idsucursal=suc.id 
                        INNER JOIN dbnomina.pcontrato con ON con.nip=emp.nip 
                        LEFT JOIN sitexcloud.pincidenciasusuario iu ON iu.idempleado=emp.nip AND iu.status<>99
                        WHERE   emp.status<>99 
                        ORDER BY emp.nombre";
        return $this->selectnew( $queryNomina );
    }

    public function getRetardosEinasistencias($trabajadorId, $mes, $anio=2018)
    {

        $condicionMes = $mes == -1 ? " month(timecheck) <=  12" : " month(timecheck) <=  $mes";
        $queryAsistencia = "SELECT '' as fecha,	e.nip as nip,
                                e.nombre as nombre,
                                d.descripcion as departamento,
                                p.descripcion as puesto,
                                c.id as idcontrato,
                                r.timecheck,
                                YEAR(r.timecheck) as anio,
                                MONTH(r.timecheck) as mes,
                                DAY(r.timecheck) as dia,
                                HOUR(r.timecheck) as hora,
                                MINUTE(r.timecheck) as minuto,
                                CASE WHEN TIMEDIFF(TIME(r.timecheck),pa.entrada)>'00:00:59' THEN CASE WHEN(HOUR(TIMEDIFF(TIME(r.timecheck),pa.entrada))*60)+MINUTE(TIMEDIFF(TIME(r.timecheck),pa.entrada))<6 THEN 50 ELSE IF(MINUTE(TIMEDIFF(TIME(r.timecheck),pa.entrada))<11,100,c.salariodiario) END ELSE 0 END AS RETARDO,
                                pa.entrada,
                                pa.entradai,
                                pa.salidai,
                                pa.salida,
                                pa.tolerancia,
                                pa.retardospfalta,
                                pa.corrido,
                                pa.faltaspdescuento,
                                s.descripcion as sucursal,
                                c.salariodiario,
                                e.fecha_baja
                                FROM 		pempleado e
                                    LEFT JOIN pregistros r ON r.idempleado=e.nip 
                                    INNER JOIN pcontrato c ON e.nip=c.nip 
                                    INNER JOIN cpuesto p ON c.idpuesto=p.id 
                                    INNER JOIN cdepartamento d ON c.iddepartamento=d.id  
                                    INNER JOIN cparametrosasistencia pa ON p.id=pa.idpuesto 
                                    INNER JOIN csucursal s ON e.idsucursal=s.id
                            WHERE $condicionMes and idempleado=$trabajadorId and year(timecheck) =$anio ";
            return $this->select( $queryAsistencia);
    }


    public function getCatalogoDeducciones()
    {
        $queryDeducciones = "SELECT *, descripcion as name
                                                FROM ctipodeduccion
                                                WHERE status = 1 ";
        return $this->select( $queryDeducciones );
    }
}
