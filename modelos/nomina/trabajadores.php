<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/intranet/modelos/DB.php';

class Trabajador extends DB
{
    // private $conexion;

    
    public function getEmpleadosActivos( $sucursal, $departamento, $puesto, $nombre)
    {

        $queryEmpleados = "SELECT   pempleado.nip, 
                                    pempleado.nombre as nombre,
                                    cdepartamento.descripcion as departamento,
                                    cpuesto.descripcion as puesto,
                                    csucursal.descripcion as sucursal,
                                    csucursal.id as idsucursal,
                                    cpuesto.id as idpuesto
                           FROM     pempleado
                           INNER JOIN pcontrato on pcontrato.nip = pempleado.nip
                           LEFT JOIN csucursal on csucursal.id = pempleado.idsucursal
                           LEFT JOIN cdepartamento on cdepartamento.id = pcontrato.iddepartamento
                           LEFT JOIN cpuesto on cpuesto.id = pcontrato.idpuesto
                           WHERE   pempleado.status = 1
                           AND     csucursal.id LIKE '$sucursal'
                           AND     cdepartamento.id LIKE '$departamento'
                           AND     cpuesto.id LIKE '$puesto'
                           AND     pempleado.nombre LIKE '%$nombre%' 
                           ORDER BY csucursal.descripcion,cdepartamento.descripcion,cpuesto.descripcion,pempleado.nombre ";

        return $this->select( $queryEmpleados );
    }

    public function getSolicitudesPersonal()
    {

        $query = "SELECT    s.*,
                            s.nip as nip,
                            s.id as id,
                            e.nip as nipsolicitante,
                            TRIM(e.nombre) as solicitante,
                            p.descripcion as puesto,
                            u.descripcion as sucursal,
                            TIMESTAMPDIFF(DAY, s.fecha_solicitud, NOW()) as diasbusqueda
                  FROM      prequisicion_personal s 
                  INNER JOIN pempleado e ON s.solicitante=e.nip
                  INNER JOIN cpuesto p ON s.puesto=p.id 
                  INNER JOIN csucursal u ON s.sucursal=u.id 
                  WHERE     TIMESTAMPDIFF(DAY, s.fecha_solicitud, NOW())<30 
                  AND       s.num_vacantes>0 
                  ORDER BY  s.fecha_solicitud DESC";

        return $this->select( $query );
    }

    public function getUltimoSocioeconomico( $trabajador )
    {
        $querySocioeconomico = "SELECT * FROM psocioeconomico WHERE  idempleado = $trabajador ";
        return $this->select( $querySocioeconomico );
    }


    public function getContrato( $idempleado)
    {
        $queryContratacion = "SELECT 	*,
                                                e.nip as nip,
                                                d.idestado as estado,
                                                a.descripcion as departamento,
                                                p.descripcion as puesto,
                                                e.idsucursal as idsucursal,
                                                csucursal.descripcion as sucursal,
                                                u.username as username,
                                                e.nombre as nombre,
                                                u.password as password,
                                                u.tipo as tipo
                                        FROM 		pempleado e 
                                        INNER JOIN pcontrato c ON c.nip=e.nip
                                        INNER JOIN pdireccion d ON e.nip=d.nip 
                                        INNER JOIN cdepartamento a ON a.id=c.iddepartamento 
                                        INNER JOIN cpuesto p ON p.id=c.idpuesto
                                        LEFT JOIN pusuarios u ON e.nip=u.idempleado
                                        INNER JOIN csucursal on csucursal.id = e.idsucursal
                                        WHERE 	e.nip=$idempleado";

                                        

        return $this->select( $queryContratacion );
    }

    public function getCapsTrabajadores( $fechaInicio )
    {
        $queryCaps = "SELECT origen.descripcion AS origen, destino.descripcion AS destino,  pempleado.nombre,cpuesto.descripcion AS viejoPuesto,nuevoPuesto.descripcion AS nvoPuesto,
                                cambios_adscripcion.tipo_movto, cambios_adscripcion.fecha
                                FROM cambios_adscripcion
                                INNER JOIN csucursal AS origen ON origen.id =cambios_adscripcion.sucursal_salida_id
                                INNER JOIN csucursal AS destino ON destino.id =cambios_adscripcion.sucursal_llegada_id
                                INNER JOIN pempleado ON pempleado.nip = cambios_adscripcion.trabajador_id
                                INNER JOIN cpuesto ON cpuesto.id = cambios_adscripcion.puesto_id
                                INNER JOIN pcontrato ON pcontrato.nip = pempleado.nip
                                INNER JOIN cpuesto AS nuevoPuesto ON nuevoPuesto.id = pcontrato.idpuesto
                                WHERE fecha >= '$fechaInicio'
                                ORDER BY fecha DESC ";
        return $this->select( $queryCaps );
    }

    public function getTrabajadoresInactivos( $nombre)
    {
        $queryTrabajadorBaja = "SELECT * 
                                                        FROM pempleado 
                                                        WHERE STATUS = 99 and nombre like '%$nombre%'
                                                        ORDER BY nombre";
        return $this->select( $queryTrabajadorBaja );
    }

    public function reactivarEmpleado( $idempleado  , $fechaBaja)
    {
        $queryReactivar = "UPDATE pempleado set status = 1 WHERE nip = $idempleado ";

        $actualizado = $this->update( $queryReactivar );

        $actualizaFechaIngreso = "UPDATE pcontrato set fechainiciolab = '$fechaBaja' where nip = $iddepartamento ";
        $actualizado = $this->update( $actualizaFechaIngreso );
        if ( $actualizado > 0 ) {
            //Agregando el reingreso al historial de movimientos 
            $this->sethistorialAdscripcionTrabajador([
                'origen' => -1,
                'destino' => -1,
                'puestoOrigen' => -1,
                'fecha' => $fechaBaja,
                'trabajador' => $idempleado,
                'tipoMovto' => 'reingreso'
            ]);

            return $actualizado;
        }

        return 0;
    }

    public function verificaJefeDeSucursal( $jefeSucursalId)
    {
        $queryJefeSucursal = "SELECT pempleado.nombre,pempleado.nip,MAX(fecha_realizado) as lastAsistencia,pempleado.idsucursal 
                        FROM pempleado
                        INNER JOIN pcontrato on pcontrato.nip = pempleado.nip
                        INNER JOIN cpuesto on cpuesto.id = pcontrato.idpuesto
                        LEFT JOIN asistencia_manual on asistencia_manual.idempleado = pempleado.nip
                        WHERE (cpuesto.descripcion LIKE '%ENCARGADO%' OR cpuesto.descripcion LIKE 'JEFE DE SUCURSAL') AND
                         pempleado.status = 1 AND pempleado.nip = $jefeSucursalId ";
        return $this->select( $queryJefeSucursal );
    }

    public function getStatus( $nip)
    {
        $queryStatus = "SELECT *
                        FROM pempleado
                        WHERE pempleado.nip = $nip ";
        return $this->select( $queryStatus );
    }

    public function getPersonalSubordinado( $sucursalId , $usuario = null)
    {
        $condicion = ' AND pempleado.idsucursal = $sucursalId ';
        if( $usuario != null ){
            $condicion = '';
        }
        
        $querySubordinados = "SELECT pempleado.nombre,pempleado.nip,MAX(fecha_realizado) as lastAsistencia, pempleado.idsucursal as idsucursal
                            from pempleado
                            INNER JOIN pcontrato on pcontrato.nip = pempleado.nip
                            INNER JOIN cpuesto on cpuesto.id = pcontrato.idpuesto
                            LEFT JOIN asistencia_manual on asistencia_manual.idempleado = pempleado.nip
                            WHERE  pempleado.status = 1 -- and pempleado.nip = 1
                            group by nombre";
        return $this->select( $querySubordinados );
    }

    public function getDatosContratoEmpleado( $empleadoId)
    {
        $queryContratacion = "SELECT *  
                                                FROM pempleado
                                                INNER JOIN pcontrato ON pcontrato.nip = pempleado.nip 
                                                WHERE pempleado.nip = $empleadoId AND pempleado.`status` = 1";
        return $this->select( $queryContratacion );
    }

    public function getDatosDePermiso( $permisoId )
    {
        $queryGetPermisos = "SELECT ppermisos.fecha, ppermisos.motivo, pempleado.nombre,csucursal.descripcion as sucursal,cpuesto.descripcion  as puesto,
                            ppermisos.goce_sueldo ,ppermisos.dias 
                                                FROM ppermisos
                                                INNER JOIN pempleado on pempleado.nip = ppermisos.idempleado
                                                INNER JOIN cpuesto on cpuesto.id = ppermisos.puestoId
                                                INNER JOIN csucursal on csucursal.id = ppermisos.sucursalId 
                                                WHERE ppermisos.id = $permisoId";
                                                
        return $this->select( $queryGetPermisos );
    }

    public function getDatosAccionCorrectiva( $accionCorrectivaId )
    {
        $queryGetAccion = "SELECT pempleado.nombre,csucursal.descripcion as sucursal, cpuesto.descripcion as puesto, pacciones_correctivas.fecha_sancion as fecha,
                                                            pacciones_correctivas.motivo, pacciones_correctivas.plan_accion,pacciones_correctivas.monto,pacciones_correctivas.fecha_descuento,pempleado.nip,
                                                            pempleado.idsucursal,cpuesto.id AS idpuesto,Pacciones_correctivas.id as idaccion,Pacciones_correctivas.consecutivo
                                                FROM Pacciones_correctivas
                                                INNER JOIN pempleado on pempleado.nip = Pacciones_correctivas.idempleado
                                                INNER JOIN cpuesto on cpuesto.id = Pacciones_correctivas.puestoId
                                                INNER JOIN csucursal on csucursal.id = Pacciones_correctivas.sucursalId 
                                                WHERE Pacciones_correctivas.id = $accionCorrectivaId ";
        return $this->select( $queryGetAccion );
    }

    public function deleteAccionCorrectiva( $id )
    {
        $queryDeleteAccion = "DELETE FROM Pacciones_correctivas where id = $id ";

        return $this->update( $queryDeleteAccion );
    }

    public function updateAccionCorrectiva( $params )
    {
        extract( $params );

        $queryUpdateCorrectiva = "UPDATE pacciones_correctivas SET idempleado=$sancionado,fecha_sancion='$fechaSancion',motivo='$motivo',plan_accion='$planAccion',fecha_descuento='$fechaDescuento' WHERE";

        return $this->select( $queryUpdateCorrectiva );
    }

    public function setAsistencia( $params ){
        extract( $params );
        $query = "INSERT INTO asistencia (idempleado,foto,latitud,longitud,fecha,hora) VALUES($idempleado, '".$imagen."','".$latitud."','".$longitud."', NOW(), NOW())";   
        $result = $this->insert( $query );                
        //echo $query;
        return $result;
    }

    public function sethistorialAdscripcionTrabajador( $params )
    {
        if ( !isset( $params['sueldo']) ) {
            $params['sueldo'] = -1;
        }
        extract( $params );
        

        $queryHistorialAdscripcion = "INSERT INTO cambios_adscripcion(sucursal_salida_id,sucursal_llegada_id,puesto_id,fecha,trabajador_id,tipo_movto,sueldo) 
                    VALUES($origen, $destino,$puestoOrigen,'$fecha', $trabajador, '$tipoMovto','$sueldo')";

                    
        return $this->insert( $queryHistorialAdscripcion );
    }

     public function setFotoTrabajador( $nip, $uri)
    {
        $querySetPhoto = "UPDATE pempleado SET foto='$uri'  where nip=$nip";
        $exeSetPhoto = $this->conexion()->query( $querySetPhoto);
        return $this->conexion()->affected_rows;
    }

    public function altasTrabajadores( $mes, $anio)
    {
        $condicionAltas = $mes < 0 ? " MONTH(fechainiciolab) <=' ".date('m')."' AND YEAR(fechainiciolab) = $anio " : " MONTH(fechainiciolab) =  $mes AND  YEAR(fechainiciolab) = $anio  ";       
         $queryAltas = "SELECT pempleado.nombre, pempleado.nip,pcontrato.fechainiciolab  
                                    FROM pempleado
                                    INNER JOIN pcontrato ON pcontrato.nip = pempleado.nip
                                    WHERE $condicionAltas AND pempleado.status = 1";
        return $this->select( $queryAltas );
    }

    public function getTrabajadoresBaja( $mes, $anio)
    {
        $condicionAltas = $mes < 0 ? " MONTH(fecha_baja) <=' ".date('m')."' AND YEAR(fecha_baja) = $anio " : " MONTH(fecha_baja) =  $mes AND  YEAR(fecha_baja) = $anio  ";
        $queryAltas = "SELECT pempleado.nombre, pempleado.nip,fecha_baja,pcontrato.fechainiciolab  
                        FROM pempleado
                        INNER JOIN pcontrato ON pcontrato.nip = pempleado.nip
                        WHERE $condicionAltas AND pempleado.status = 99 and  fecha_baja is not NULL";
        return $this->select( $queryAltas );
    }

    public function getCambiosAdscripcion($sucursal, $mes, $anio)
    {
        $condicionMes = $mes == -1 ? " month(fecha) <= 12 " : "month(fecha) = $mes";
        // $condicionTipoCambio = ( $tipoCambio == 1 ) ? "sucursal_salida_id like '$sucursal' " : "sucursal_llegada_id = '$sucursal' ";

        $queryCambiosAdscripcion = "SELECT cambios_adscripcion.*,sucsalida.descripcion as salida , sucllegada.descripcion as llegada, month(fecha) as mes
                                                                FROM cambios_adscripcion
                                                                INNER JOIN csucursal as sucsalida on sucsalida.id = cambios_adscripcion.sucursal_salida_id
                                                                INNER JOIN csucursal as sucllegada on sucllegada.id = cambios_adscripcion.sucursal_llegada_id
                                                                WHERE  $condicionMes AND year(fecha) = $anio";
        return $this->select( $queryCambiosAdscripcion );
    }

    public function getAllTrabajadoresRegistrados()
    {
        $queryTrabajador = "SELECT pempleado.*,pcontrato.id ,pcontrato.fechainiciolab
                            FROM pempleado
                            INNER JOIN pcontrato ON pcontrato.nip = pempleado.nip";
        return $this->select( $queryTrabajador);
    }

    public function getNominaActiva()
    {
        $queryNomina = "SELECT pempleado.nombre,cpuesto.descripcion AS puesto, cdepartamento.descripcion AS departamento,pcontrato.fechainiciolab, csucursal.descripcion AS sucursal,pempleado.fechanac,
                                        tiposangre,nivelestudios,numhijos,religion,alergias,asegurado,pdireccion.cp,edocivil,sexo,pempleado.nip,pempleado.celular,pcontrato.salariobase,pcontrato.id as contratoId,
                                        pempleado.nss as nss,CONCAT(pdireccion.calle,' ',pdireccion.numext,', ',pdireccion.colonia,'. ',pdireccion.municipio) as direccion,pempleado.curp as curp,pempleado.rfc as rfc,pempleado.email as email
                                        FROM pempleado
                                        INNER JOIN pcontrato ON pcontrato.nip = pempleado.nip
                                        LEFT JOIN csucursal ON csucursal.id = pempleado.idsucursal
                                        LEFT JOIN cpuesto ON cpuesto.id = pcontrato.idpuesto
                                        LEFT JOIN cdepartamento ON cdepartamento.id = pcontrato.iddepartamento
                                        LEFT JOIN pdireccion ON pdireccion.nip = pempleado.nip
                                        WHERE pempleado.status = 1";
        return $this->select( $queryNomina );
    }

    public function getHorarioEntrada( $idempleado)
    {
        $queryHoraEntrada = "SELECT *                                         
        FROM pempleado
        INNER JOIN pcontrato ON pcontrato.nip = pempleado.nip
        INNER JOIN cpuesto ON cpuesto.id = pcontrato.idpuesto
        INNER JOIN cdepartamento ON cdepartamento.id = cpuesto.iddepartamento
        INNER JOIN cparametrosasistencia ON cparametrosasistencia.idpuesto = pcontrato.idpuesto
        WHERE pempleado.`status` = 1 AND pempleado.nip = $idempleado ";

        return $this->select( $queryHoraEntrada );
    }

    public function ultimoCambioAdscripcion( $idempleado )
    {
       $queryCambios = "SELECT MAX(fecha) AS fecha , csucursal.descripcion AS sucursal , cpuesto.descripcion AS puesto, cambios_adscripcion.trabajador_id
                                            FROM cambios_adscripcion 
                                            INNER JOIN csucursal ON csucursal.id = cambios_adscripcion.sucursal_salida_id
                                            INNER JOIN cpuesto ON cpuesto.id = cambios_adscripcion.puesto_id
                                            WHERE trabajador_id = $idempleado AND tipo_movto = 'cambioAdscrip'";
        return $this->select( $queryCambios );
    }

    public function getTrabajadoresEnDepartamentos( $inContent )
    {
        $queryTrabajador = "SELECT * 
                            FROM pempleado
                            INNER JOIN pcontrato ON pcontrato.nip = pempleado.nip
                            WHERE iddepartamento in ($inContent) AND pempleado.status = 1";
        return $this->select( $queryTrabajador);
    }    

    public function getVacacionesProgramadas( $idempleado , $anio = '' )
    {
        $whereAnio = '';
        if ( $anio != '' ) {
            $whereAnio = " AND  YEAR(fecha) = $anio";
        }
        $queryVacaciones = "SELECT * 
                                                FROM programacion_vacaciones
                                                WHERE  estado IN ('pendiente','cumplido') AND trabajador_id = $idempleado $whereAnio";


        return $this->select( $queryVacaciones );
    }

    public function agendarVacaciones( $empleado , $fecha , $estado , $periodo )
    {
        $queryVacaciones = "CALL SETPROGRAMACION_VACACIONES( '$fecha', $empleado,'$estado', '$periodo')";

        $exeQuery = $this->conexion->query( $queryVacaciones ) ; 
        
        if( $exeQuery  ){
            return 1;
        }
        return 0;
    }

    public function getUltimaAcorrectivaAcumulable( $idempleado )
    {
        $queryACorrectiva = "SELECT MAX(id) as id  FROM pacciones_correctivas WHERE idempleado = $idempleado AND (monto = -1 OR monto in (100) ) ";

        
        return $this->select( $queryACorrectiva )[0];
    }

    public function actualizarVacaciones( $empleado , $fecha , $estado )
    {
        // $queryCambiaVacaciones = "CALL UPDATE_VACACIONES('$fecha' , $empleado , '$estado')";
        $queryId = " SELECT id from programacion_vacaciones where fecha = '$fecha' AND trabajador_id = $empleado AND estado = '$estado' ";
        $exeId = $this->select( $queryId)[0];

        $queryCambiaVacaciones = "			UPDATE programacion_vacaciones SET estado= concat('cancelado','@',$empleado,'@',now() )
        WHERE id=".$exeId['id'];
        
        $exeQuery = $this->conexion->query( $queryCambiaVacaciones ) ; 
        
        if( $exeQuery  ){
            return 1;
        }
        return 0;
    }


    public function getTrabajadoresConVacacionesProgramadas( $anio )
    {
        $whereAnio = '';
        if ( $anio != '' ) {
            $whereAnio = " AND   YEAR(fecha) = $anio";
        }

        $queryTrabajadorVacacion = "SELECT pempleado.nip,pempleado.nombre AS empleado,csucursal.descripcion AS sucursal, cpuesto.descripcion AS puesto,count(programacion_vacaciones.fecha) AS diasProgramados
                                                                FROM pempleado
                                                                INNER JOIN programacion_vacaciones ON programacion_vacaciones.trabajador_id = pempleado.nip
                                                                INNER JOIN csucursal ON csucursal.id = pempleado.idsucursal
                                                                INNER JOIN pcontrato ON pcontrato.nip = pempleado.nip
                                                                INNER JOIN cpuesto ON cpuesto.id = pcontrato.idpuesto
                                                                WHERE estado in ('pendiente') $whereAnio
                                                                GROUP BY pempleado.nip,cpuesto.id";
        return $this->select( $queryTrabajadorVacacion );
    }

    public function setRecursos($values )
    {
        $querySetRecursos = "INSERT INTO precursos(path,empleado_nip) VALUES $values  ";
        
        return $this->insert( $querySetRecursos );
    }

    public function getRecursos( $path, $user )
    {
        
        $queryRecursos = "SELECT * FROM precursos WHERE path='$path'  AND empleado_nip = $user";

        return $this->select( $queryRecursos );
    }

    public function registraAsistenciaManual( $params )
    {
        extract( $params );
        $queryAsistenciaManual = "INSERT INTO asistencia_manual(idempleado,observaciones,asistencia) VALUES('$empleado', '$observaciones', '$asistencia') ";

        return $this->insert( $queryAsistenciaManual );
    }

    public function cambiaSucursalDeAdscripcion( $empleadoId , $sucursal)
    {
        $queryCambiaSucursal = "UPDATE pempleado set idsucursal = $sucursal where nip = $empleadoId ";

        return $this->update( $queryCambiaSucursal );
    }

    public function cambiaAdscripcionContrato( $params )
    {
        extract( $params );
        $queryUpdateCoontrato = "UPDATE pcontrato SET iddepartamento = $iddepartamento, idpuesto= $nuevoPuesto WHERE nip = $trabajador ";

        return $this->update( $queryUpdateCoontrato );
    }
    

    public function aplicaAccionCorrectiva( $params )
    {
        extract( $params );

        $aplicaSancion = isset( $fechaDescuento ) ? " '$fechaDescuento', '$monto' " : "NULL,0";

        $queryAccionCorrectiva = "INSERT INTO pacciones_correctivas(fecha_sancion,idempleado,motivo,plan_accion,fecha_descuento,monto,sucursalId,puestoId,id_usuario_aplico_sancion) 
                                                                VALUES('$fechaIncidencia',$empleado,'$motivo','$plan',$aplicaSancion,$sucursal,$puesto,". ( isset( $_SESSION['nip'] ) ?  $_SESSION['nip']  : 1 ).")";
                                          
                                          
        return $this->insert( $queryAccionCorrectiva );
    }

    public function aplicaAccionCorrectivaAcumulable( $params )
    {
        extract( $params );
        $queryAcumulable = "CALL ACORRECTIVAS_ACUMULATIVAS($empleado,'$fechaIncidencia','$motivo','$plan','$fechaDescuento',$sucursal,$puesto,".( isset( $_SESSION['nip'] ) ?  $_SESSION['nip']  : 1 ).", '$tipodeduccion' )";


        return $this->conexion()->query( $queryAcumulable );
    }

    public function aplicarPermiso( $params )
    {
        extract( $params );

        $queryPermiso =  "INSERT INTO ppermisos(fecha,idempleado,motivo,goce_sueldo,puestoId,sucursalId,dias) VALUES('$fecha',$empleado,'$motivo',$accionPermiso,$puesto,$sucursal,$dias)";

        return $this->insert( $queryPermiso );
    }

    public function verificarHuella(  )
    {
         $queryHuella= "SELECT huella,idempleado,nombre,  csucursal.idreloj
                                    FROM huellas 
                                    INNER JOIN pempleado ON pempleado.nip = huellas.idempleado
                                    INNER JOIN csucursal ON pempleado.idsucursal = csucursal.id
                                    WHERE estatus= 1 AND pempleado.status = 1";

          return $this->select( $queryHuella );
    }

    public function setHuella( $params)
    {
        extract( $params );
        $queryHuellaRegistro = "INSERT INTO huellas(idempleado,huella) VALUES ( $idempleado , '$huella'  )  ";

        echo $queryHuellaRegistro;
        return $this->insert( $queryHuellaRegistro );
    }

    public function updateAplicacionIncidencia( $empleado , $incidencia , $timecheck , $nuevoIngreso = '' )
    {
        $updateTimecheck = '';
        if ( $nuevoIngreso != '' ) {
            $updateTimecheck = " ,timecheck='$timecheck $nuevoIngreso:00' ";
             $timecheck .= " 00:00:00";
        }
        $updateAplicaIncidencia = "UPDATE pregistros SET aplicaIncidencia = $incidencia $updateTimecheck  WHERE idempleado = $empleado and timecheck = '$timecheck' ";

        // echo $updateAplicaIncidencia;
        return $this->update( $updateAplicaIncidencia );
    }

    public function getFaltaSinIncidenciaAplicada( $idempleado , $datetimeChecado )
    {
        $queryFaltasSinAplicar = " SELECT * FROM pregistros WHERE (timecheck) = '$datetimeChecado' AND idempleado = $idempleado";

        return $this->select( $queryFaltasSinAplicar );
    }

    public function setEntradaSalidaReloj( $params )
    {
        extract( $params );

        $queryRegistraReloj = "INSERT INTO pregistros(idempleado,timecheck,idreloj) VALUES ($idempleado , '$timecheck', $idreloj )";

        return $this->insert( $queryRegistraReloj );
        
    }

    public function setCoutFaltasAsistenciaRetardos( $params)
    {
        extract( $params );
        $queryContadorAsistenica = "INSERT INTO incidencia_asistencia(trabajadorId,inicio_periodo,fin_periodo,asistencias,faltas,retardos)
                                                VALUES($trabajador,'$fechaInicio','$fechaFin',$asistencias,$faltas,$retardos) ";
        return $this->insert( $queryContadorAsistenica );
        
    }

    public function getDatosSocioeconomico( $nip = '%')
    {
        $querySocioeconomico = "SELECT *
        FROM pempleado
        INNER JOIN psocioeconomico ON psocioeconomico.idempleado = pempleado.nip
        INNER JOIN pevaluacion_socioeconomico ON pevaluacion_socioeconomico.idsocioeconomico = psocioeconomico.id
        WHERE psocioeconomico.idempleado LIKE '$nip'";
        return $this->select( $querySocioeconomico );
    }

    public function getAccionesCorrectivas( $params)
    {
        extract( $params );
        
        $queryAccionesCorrectivas = "SELECT motivo,plan_accion,fecha_sancion,monto,pempleado.nombre AS sancionado, usraplicosancion.nombre AS sancionador , pacciones_correctivas.id
                                        FROM pacciones_correctivas
                                        INNER JOIN pempleado ON pempleado.nip = pacciones_correctivas.idempleado
                                        left JOIN pempleado AS usraplicosancion ON usraplicosancion.nip = pacciones_correctivas.id_usuario_aplico_sancion
                                        WHERE fecha_sancion >= '$fechaInicio' AND fecha_sancion <= '$fechaFin' ";

        return $this->select( $queryAccionesCorrectivas );
    }

    public function getDocumentacionAllTrabajadores( )
    {
     
        $queryTrabajoresDocumentacion = "SELECT ctipodoc.id AS iddoct, ctipodoc.descripcion AS documento,pempleado.nombre AS empleado , csucursal.descripcion AS sucursal,pempleado.nip
                                                                        FROM ctipodoc
                                                                        INNER JOIN pdocumentos ON  pdocumentos.idtipo = ctipodoc.id
                                                                        INNER JOIN pempleado ON pempleado.nip = pdocumentos.idempleado
                                                                        INNER JOIN csucursal ON csucursal.id = pempleado.idsucursal                                                                        
                                                                        WHERE pdocumentos.`status` = 1 AND pempleado.`status` = 1
                                                                        ORDER BY sucursal,empleado";
            return $this->select( $queryTrabajoresDocumentacion );
    }

    public function getDocumentacionTrabajador($idempleado)
    {
     
        $queryTrabajoresDocumentacion = "SELECT ctipodoc.id AS iddoct, ctipodoc.descripcion AS documento,pempleado.nombre AS empleado , csucursal.descripcion AS sucursal,pempleado.nip
                                                                        FROM ctipodoc
                                                                        INNER JOIN pdocumentos ON  pdocumentos.idtipo = ctipodoc.id
                                                                        INNER JOIN pempleado ON pempleado.nip = pdocumentos.idempleado
                                                                        INNER JOIN csucursal ON csucursal.id = pempleado.idsucursal                                                                        
                                                                        WHERE pdocumentos.`status` = 1 AND pempleado.`status` = 1 AND pempleado.nip=".$idempleado;
            return $this->select( $queryTrabajoresDocumentacion );
    }

    public function getTipoDocumentos( )
    {
        $queryTipoDocumentos = "SELECT * FROM ctipodoc WHERE status = 1";

        return $this->select( $queryTipoDocumentos );
    }

    // ASISTENCIA DE PERSONAL
    public function getAsistencia( $fecha )
    {
         $queryAsistenciaDiaria = "SELECT  pempleado.nombre,csucursal.descripcion AS sucursal,pempleado.nip,pregistros.timecheck,cpuesto.descripcion AS puesto,time( timecheck) AS checado,
                                                    cparametrosasistencia.entrada,cparametrosasistencia.tolerancia,date(pregistros.timecheck ) as fecha,pcontrato.id as contratoId
                                                    FROM pempleado
                                                    LEFT JOIN pregistros ON pregistros.idempleado = pempleado.nip AND date(pregistros.timecheck ) = '$fecha'
                                                    INNER JOIN csucursal ON csucursal.id = pempleado.idsucursal
                                                    INNER JOIN pcontrato ON pcontrato.nip = pempleado.nip
                                                    INNER JOIN cpuesto ON cpuesto.id = pcontrato.idpuesto
                                                    INNER JOIN cparametrosasistencia ON cparametrosasistencia.idpuesto = pcontrato.idpuesto
                                                    WHERE pempleado.`status` = 1
                                                    ORDER BY sucursal, timecheck";
        return $this->select( $queryAsistenciaDiaria );
    }

    //REQUISICI??N DE PERSONAL
    public function setSolicitudPersonal( $params )
    {
        extract( $params );

        $personalRecomendado = $nipRecomendado == "-1"  ? "NULL,NULL" :  "'$nipRecomendado', '$motivoRecomendacion'" ;
        $queryRequisicion = "INSERT INTO prequisicion_personal (solicitante,fecha_est_contratar,puesto,num_vacantes,cualidades,sucursal,personal_recomendado_nip,motivo_recomendacion)
                                                VALUES( '$solicitante','$fecha','$puesto','$nVacantes','$cualidades',$sucursal,$personalRecomendado) ";
                                            
        return $this->insert( $queryRequisicion );

    }

    public function getRequisicionesPersonal(  $idRequisicion = '')
    {
        $idRequisicion = $idRequisicion == '' ? '' : " WHERE reqper.id=$idRequisicion ";
        $queryRequisicion = "SELECT reqper.id,pempleado.nombre AS solicita,reqper.fecha_solicitud,reqper.fecha_est_contratar,cpuesto.descripcion,reqper.num_vacantes,reqper.cualidades,csucursal.descripcion AS sucursal,
                                                    empleadoRecomendado.nombre AS recomendado, reqper.motivo_recomendacion
                                                FROM prequisicion_personal AS reqper
                                                INNER JOIN pempleado on pempleado.nip = reqper.solicitante
                                                INNER JOIN cpuesto ON cpuesto.id = reqper.puesto
                                                INNER JOIN csucursal ON csucursal.id = reqper.sucursal
                                                LEFT JOIN pempleado AS empleadoRecomendado ON empleadoRecomendado.nip = reqper.personal_recomendado_nip
                                                $idRequisicion
                                                ORDER BY fecha_solicitud DESC";

        return $this->select( $queryRequisicion );
    }

    public function listaCompletaTrabajadores( $mes , $anio )
    {
        $queryTrabajadores = "SELECT pempleado.nip,pempleado.nombre,pcontrato.fechainiciolab,pempleado.status,ca.fecha, ca.tipo_movto,pcontrato.fecha_ingreso,pempleado.fecha_baja
                                                    FROM pempleado
                                                    INNER JOIN pcontrato ON pcontrato.nip = pempleado.nip
                                                    LEFT join cambios_adscripcion AS ca ON pempleado.nip = ca.trabajador_id  AND tipo_movto IN ('baja' ,'reingreso','ingreso')  AND (YEAR(ca.fecha) = $anio AND MONTH( ca.fecha) = $mes)
                                                    WHERE ( ( MONTH(pcontrato.fechainiciolab)<= $mes AND YEAR(pcontrato.fechainiciolab) <= $anio ) OR  ( YEAR(pcontrato.fechainiciolab) < $anio) )";
 
        return $this->select( $queryTrabajadores );
    }
}
