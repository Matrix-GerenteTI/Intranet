<?php

use Google\Cloud\Core\Batch\Retry;

require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/nomina/trabajadores.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/nomina/incidencias.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/Empresa/Empresa.php";
require_once $_SERVER['DOCUMENT_ROOT'].'/intranet/modelos/nomina/incidencias.php';


class TrabajadorController  
{
    protected $modeloTrabajador;
    protected $modeloEmpresa;
    protected $modeloIncidencias;

    public function __construct( )
    {
        $this->modeloTrabajador = new Trabajador;
        $this->modeloEmpresa = new Empresa;
        $this->modeloIncidencias = new Incidencias;
    }

    public function getContratacion( $idempleado)
    {
        $detalleContrato = $this->modeloTrabajador->getContrato( $idempleado)[0];
                //Calculando la antigueadad laboral del trabajador
        $fechcaInicioLaborar = date_create( $detalleContrato['fechainiciolab']  );
        $fechaActual = date_create( date("Y-m-d")  );
        
        $diferencia = date_diff($fechcaInicioLaborar, $fechaActual);
        $detalleContrato['antiguedad']= "$diferencia->y Año(s),$diferencia->m Mes(es) y $diferencia->d Día(s)";
        $detalleContrato['vacaciones'] = $diferencia->y > 0 ? true : false;
        //Numero de días de vacaciones de acuardo a su antiguedad
        if( $diferencia->y == 0){
            $detalleContrato['diasVacaciones'] = 0;
        }elseif ( $diferencia->y == 1) {
            $detalleContrato['diasVacaciones'] = 6;
        } else if( $diferencia->y == 2) {
            $detalleContrato['diasVacaciones'] = 10;
        }else if ( $diferencia->y == 3) {
            $detalleContrato['diasVacaciones'] = 12;
        }elseif ( $diferencia->y == 4 ) {
            $detalleContrato['diasVacaciones'] = 10;
        }elseif ( $diferencia->y >= 5 && $diferencia->y <= 9 ) {
            $detalleContrato['diasVacaciones'] = 14;
        }elseif ( $diferencia->y >= 10 && $diferencia->y <= 14 ) {
            $detalleContrato['diasVacaciones'] = 16;
        }elseif ( $diferencia->y >= 15 && $diferencia->y <= 19) {
            $detalleContrato['diasVacaciones'] = 18;
        }elseif ( $diferencia->y >= 20 && $diferencia->y <= 24) {
            $detalleContrato['diasVacaciones'] = 20;
        }elseif ( $diferencia->y >= 25 && $diferencia->y <= 29) {
            $detalleContrato['diasVacaciones'] = 22;
        }else{
            $detalleContrato['diasVacaciones'] = 24;
        }
        
        //obtenemos sus vacaciones programadas en el año actual    
        $detalleContrato['listaVacaciones'] = $this->modeloTrabajador->getVacacionesProgramadas(  $idempleado  );
                
        
        return $detalleContrato;
    }

    public function setIncidencia( $params )
    {
        // Obteniendo la fecha de descuento
        $diaActual = date("d");
        $fechaDescuento = $diaActual <= 15? date("Y-m-15") :  date("Y-m")."-".cal_days_in_month(CAL_GREGORIAN, date('m'),date('Y') ); 
        $params['fechaAplicacion'] = $fechaDescuento;
        $aplicado = $this->modeloIncidencias->setIncidencia( $params );

        return $aplicado;
    }

    public function quitaInicidencia( $idIncidencia)
    {
        
        return $this->modeloIncidencias->quitaIncidencia( $idIncidencia );;
    }

    public function verificaAsistenciaIncidencia( $idempleado , $checado)
    {
        
    }

    public function setAplicacionIncidenciaAsistencia( $empleado , $timecheck , $incidencia , $nuevoIngreso = '' )
    {
        $fecha_hora = explode(" ", $timecheck);
        $explodeFecha = explode("/", $fecha_hora[0] );
        
        $timecheck = $explodeFecha[2]."-".$explodeFecha[1]."-".$explodeFecha[0]." ".$fecha_hora[1];

        if ( $nuevoIngreso == '' ) {

            return $this->modeloTrabajador->updateAplicacionIncidencia( $empleado , $incidencia , $timecheck );
        }

        
        return $this->modeloTrabajador->updateAplicacionIncidencia( $empleado , $incidencia , $explodeFecha[2]."-".$explodeFecha[1]."-".$explodeFecha[0] , $nuevoIngreso);
        
    }

    public function actualizaImporteIncidencia( $params )
    {
        $incidencias = json_decode( $params );
        
        foreach ( $incidencias as $i => $incidencia) {
            $this->modeloIncidencias->actualizaImporte( $incidencia->idIncidencia , $incidencia->monto);
        }

        return 1;
    }


    public function solicitarPersonal( $params )
    {
        $internoRecomendado = '';

        $params['fecha'] = $this->dateFormat( trim($params['fecha']) );

        //haciendo la notificacion de la solicitud via correo electronico
        $contratacionTrabajador = $this->modeloTrabajador->getContrato( $params['solicitante'] )[0];
        //Obteniendo el id del departamento del puesto solicitado 

        $departamento = $this->modeloEmpresa->getDepartamentoDelPuesto( $params['puesto'] )[0];

        //Obteniendo el  nombre de la sucursal a la que el trabajador estará laborando
        $sucursalDestino = $this->modeloEmpresa->getSucursal( $params['sucursal'] )[0];

        if ( $params['nipRecomendado'] != -1) {

            $trabajadorRecomendado = $this->modeloTrabajador->getContrato( $params['nipRecomendado'] )[0];

            $internoRecomendado = '<p style="color:#e65100">En caso de no contratar a un nuevo empleado, el solicitante propone a: <b>'.$trabajadorRecomendado['nombre'].'</b> para cumplir con la(s) vacante(s), cuyo motivo de recomendac&oacute;n es: '.$params['motivoRecomendacion'].'</p>';
        }

        //Enviando el cambio de adscripcion del trabajador vía correo electronico
            $configCorreo = array("descripcionDestinatario" => "INTRANET-SITEX",
                                        "mensaje" => "<div style='font-family:Arial, Helvetica, sans-serif '>Buen d&iacute;a, el trabajador: <b>".$contratacionTrabajador['nombre']."</b>  ha realizado una solicitud de contrataci&oacute;n de <b>".$params['nVacantes']."</b> nuevo(s) empleado(s) para la sucursal ".$sucursalDestino['descripcion']
                                                                 ." y desempe&ntilde;ar el puesto de: ".$departamento['puesto'].", las personas que vayan a ser contradad deber&acute;n cumplir con algunas de las siguientes aptitudes: <b>".$params['cualidades'] ."</b> <br>
                                                                    <br><br>".$internoRecomendado."<br><br><b>*NOTA:  FECHA ESTIMADA DE CONTRATACI&Oacute;N: ".$params['fecha']."</b></div>"  ,
                                        "pathFile" => "",
                                        "subject" => "SOLICITUD DE PERSONAL",
                                        "correos" => array( "ti@matrix.com.mx","raulmatrixxx@hotmail.com","luisimatrix@matrix.com","rh@matrix.com.mx","gerenterh@matrix.com.mx","reclutamiento@matrix.com.mx")
                                        // "correos" => array( "software@matrix.com.mx")
                                        );
                                        $dta = HttpRequestParser::preparePostData( $configCorreo ) ; 
                                        
        file_get_contents( "http://servermatrixxxb.ddns.net/intranet/correo" ,false , $dta);


        return $this->modeloTrabajador->setSolicitudPersonal( $params  );
    }

    public function getHistorialCaps( $fechaInicio )
    {
        $listaMovimientos = $this->modeloTrabajador->getCapsTrabajadores( $fechaInicio );

        foreach ($listaMovimientos as $i => $movimiento) {
            switch ($movimiento['tipo_movto']) {
                case 'cambioAdscrip':
                    $listaMovimientos[$i]['tipo_movto'] = "CAMBIO DE ADSCRIPCION";
                    break;
                
                default:
                    # code...
                    break;
            }
        }

        return $listaMovimientos;
    }

    public function getRequisicionPersonal(  $idRequisicion)
    {
        $listaRequisiciones = $this->modeloTrabajador->getRequisicionesPersonal();
        foreach ( $listaRequisiciones as $i => $elemento) {
            $listaRequisiciones[$i]['solicita'] = $elemento['solicita'];
            $listaRequisiciones[$i]['cualidades'] = $elemento['cualidades'];
            $listaRequisiciones[$i]['recomendado'] = $elemento['recomendado'];
            $listaRequisiciones[$i]['motivo_recomendacion'] = $elemento['motivo_recomendacion'];
            $listaRequisiciones[$i]['fecha_solicitud'] = date("d/m/Y h:i:s", strtotime($elemento['fecha_solicitud']) ); 
        }

        return $listaRequisiciones;
    }

    public function setCorreoRequisicion( $idRequisicion)
    {
        $reqPersonal = $this->modeloTrabajador->getRequisicionesPersonal()[0];
        
        //Enviando el cambio de adscripcion del trabajador vía correo electronico
        $configCorreo = array("descripcionDestinatario" => "INTRANET-SITEX",
        "mensaje" => "<div style='font-family:Arial, Helvetica, sans-serif '>Buen d&iacute;a, el trabajador: <b>".$reqPersonal['solicita']."</b>  ha realizado una solicitud de contrataci&oacute;n de <b>".$reqPersonal['num_vacantes']."</b> nuevo(s) empleado(s) para la sucursal ".$reqPersonal['sucursal']
                                 ." y desempe&ntilde;ar el puesto de: ".$reqPersonal['descripcion'].", las personas que vayan a ser contradad deber&acute;n cumplir con algunas de las siguientes aptitudes: <b>".$reqPersonal['cualidades'] ."</b> <br>
                                    <br><br>".$reqPersonal['recomendado']."<br><br><b>*NOTA:  FECHA ESTIMADA DE CONTRATACI&Oacute;N: ".$reqPersonal['fecha_est_contratar']."</b></div>"  ,
        "pathFile" => "",
        "subject" => "SOLICITUD DE PERSONAL",
        "correos" => array( "ti@matrix.com.mx","raulmatrixxx@hotmail.com","luisimatrix@matrix.com","rh@matrix.com.mx","gerenterh@matrix.com.mx","gerenteadministrativo@matrix.com.mx")
        // "correos" => array( "software@matrix.com.mx")
        );
        $dta = HttpRequestParser::preparePostData( $configCorreo ) ; 
        
       echo  file_get_contents( "http://servermatrixxxb.ddns.net/intranet/correo" ,false , $dta);        
    }

    public function setCambioAdscripcion( $params )
    {
        $explodeFecha = explode("/", $params['fecha'] );
        
        $params['fecha'] = $explodeFecha[2]."-".$explodeFecha[1]."-".$explodeFecha[0];
        
        $contratacionTrabajador = $this->modeloTrabajador->getContrato( $params['trabajador'] )[0];
        $params['origen'] = $contratacionTrabajador['idsucursal'];
        $params['puestoOrigen'] = $contratacionTrabajador['idpuesto'];
        $params['tipoMovto'] = "cambioAdscrip";
        //Obteniendo el id del departamento del puesto nuevo
        $departamento = $this->modeloEmpresa->getDepartamentoDelPuesto( $contratacionTrabajador['idpuesto'] )[0];
        $params['iddepartamento'] =  $departamento['iddepartamento'];
        $registrado = $this->modeloTrabajador->sethistorialAdscripcionTrabajador( $params );
        //Cambiando el puesto y el departamento del contrato del trabajador
        $this->modeloTrabajador->cambiaAdscripcionContrato( $params );
        $this->modeloTrabajador->cambiaSucursalDeAdscripcion( $params['trabajador'] , $params['destino']);

        //Obteniendo el  nombre de la sucursal a la que el trabajador estará laborando
        $sucursalDestino = $this->modeloEmpresa->getSucursal( $params['destino'] )[0];
        //Enviando el cambio de adscripcion del trabajador vía correo electronico
            $configCorreo = array("descripcionDestinatario" => "INTRANET-SITEX",
                                        "mensaje" => "<div style='font-family:Arial, Helvetica, sans-serif '>Buen d&iacute;a, el trabajador: <b>".$contratacionTrabajador['nombre']."</b>  deja de ser ".$contratacionTrabajador['puesto']." en la sucursal ".$contratacionTrabajador['sucursal']
                                                                 ." y ahora pasa a ser <b>".$departamento['puesto'] ." en la sucursal de ".$sucursalDestino['descripcion'].", con un sueldo de $".$params['sueldo']."<br>
                                                                    <br><br><br><br><b>*NOTA: Favor de actualizar la documentaci&oacute;n correspondiente del trabajador.</b></div>"  ,
                                        "pathFile" => "",
                                        "subject" => "CAPS",
                                        "correos" => array( "ti@matrix.com.mx","raulmatrixxx@hotmail.com","luisimatrix@matrix.com","rh@matrix.com.mx","gerenterh@matrix.com.mx","gerenteadministrativo@matrix.com.mx")
                                        // "correos" => array( "sestrada@matrix.com.mx","auxsistemas@matrix.com.mx")
                                        );
                                        $dta = HttpRequestParser::preparePostData( $configCorreo ) ; 
                                        
        file_get_contents( "http://servermatrixxxb.ddns.net/intranet/correo" ,false , $dta);

        return ['registrado' => $registrado ];
    }

    public function setAsistencia( $params )
    {
        $insert = $this->modeloTrabajador->setAsistencia( $params ) ; 
        return $insert;
    }

    public function getAsistenciaDiaria( $fecha )
    {
        $listaAsistencia = $this->modeloTrabajador->getAsistencia( $fecha );
        foreach ($listaAsistencia as $i => $asistencia) {
            $listaAsistencia[$i]['nombre'] = utf8_encode( $asistencia['nombre'] );
            if ( $asistencia['timecheck'] != null ) {
                $checado = strtotime($asistencia['timecheck']);
                $entradaDeseada = strtotime($asistencia['fecha']." ".$asistencia['entrada']);
                if( $asistencia['nip'] == 120 or $asistencia['nip'] == 230 ){
                    $tiempoEntrada = round(($checado - $entradaDeseada) / 60);
                    if( $tiempoEntrada > 0){
                        $checado = $entradaDeseada;
                        $listaAsistencia[$i]['timecheck'] = $asistencia['fecha']." ".$asistencia['entrada'];
                    }
                }
                //Diferencia checado con hora de entrada establecida
                $tiempoEntrada = round(($checado - $entradaDeseada) / 60);
                
                
                if ( $tiempoEntrada <= 0) {
                    $listaAsistencia[$i]['estado'] = "Puntual";
                    $listaAsistencia[$i]['aplicaSancion'] = 'n';
                    $listaAsistencia[$i]['tipoDeduccion'] = '-1';
                }else if( $tiempoEntrada <= $asistencia['tolerancia'] ){
                    $listaAsistencia[$i]['estado'] = "Puntual con Tolerancia";
                    $listaAsistencia[$i]['aplicaSancion'] = 'n';
                    $listaAsistencia[$i]['tipoDeduccion'] = '-1';
                }else{
                    $listaAsistencia[$i]['estado'] = "Retardo";
                    $listaAsistencia[$i]['aplicaSancion'] = 's';
                    $listaAsistencia[$i]['tipoDeduccion'] = '0001';
                }
            }else{
                $listaAsistencia[$i]['estado'] = "Falta";
                $listaAsistencia[$i]['aplicaSancion'] = 's';
                $listaAsistencia[$i]['tipoDeduccion'] = '0005';
            }
        }

        return $listaAsistencia;
    }

    public function getTrabajadoresDeBaja( $nombre)
    {
       $listaTrabajadores = $this->modeloTrabajador->getTrabajadoresInactivos( $nombre );

       foreach ($listaTrabajadores as $i => $trabajador) {
           $listaTrabajadores[$i]['nombre'] = utf8_encode( $trabajador['nombre']);
           $listaTrabajadores[$i]['email'] = utf8_encode( $trabajador['email']);
       }

       return $listaTrabajadores;
    }

    public function reactivarEmpleado( $idempleado  ,$fechaBaja)
    {
        return $this->modeloTrabajador->reactivarEmpleado( $idempleado , $fechaBaja);
    }
    
    public function getAccionesCorrectiva( $fechaInicio , $fechaFin  )
    {

        $listadoAccionesCorrectivas = $this->modeloTrabajador->getAccionesCorrectivas([
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin
        ]);
       
        return $listadoAccionesCorrectivas;

    }

    public function getAccionCorrectiva( $id )
    {
        $accionCorrectiva = $this->modeloTrabajador->getDatosAccionCorrectiva( $id )[0];
        foreach ($accionCorrectiva as $indice => $valor) {
            if ( $indice == 'fecha' || $indice == 'fecha_descuento') {
                $valor = str_replace("-", "/", $valor );

            }
            $accionCorrectiva[$indice] = utf8_encode( $valor );
        }

        return $accionCorrectiva;
    }

    public function deleteAccionCorrectiva( $id)
    {
        return $this->modeloTrabajador->deleteAccionCorrectiva( $id );
    }

    public function updateAccionCorrectiva()
    {
        
    }

    public function solicitudesPersonal(){
        return $this->modeloTrabajador->getSolicitudesPersonal();;
    }

    public function listaTrabajadoresAltasBajas( $mes = 8 , $anio = 2020 )
    {
        $listaTrabajadores = $this->modeloTrabajador->listaCompletaTrabajadores( $mes , $anio);
        $listaAltas = [];
        $listaBajas = [];
        $trabadoresActivos = [];
        $idxAntInOut = 0;
        $idTrabajadorAnt = 0;
        foreach ( $listaTrabajadores as $i => $trabajador) {
            if ( $trabajador['status'] == 99 && $trabajador['tipo_movto'] == null ) {
                //son datos viejos y que no entrarán en la estadística
                $splitInicioLab = explode( "-",$trabajador['fechainiciolab']);
                if ( ($splitInicioLab[1] /1) > $mes && $splitInicioLab[0] >= $anio ) {
                    unset( $listaTrabajadores[$i] );
                }else if ( $trabajador['fecha_baja'] == null || $trabajador['fecha_baja'] == '0000-00-00') {

                    unset( $listaTrabajadores[$i] );
                    
                }elseif ( $trabajador['fecha_baja'] != null ) {
                    // $splitBaja = explode( "-",$trabajador['fecha_baja']);
                    if ( $splitBaja[0] <= $anio && $splitBaja[1] <= $mes) {
                        unset( $listaTrabajadores[$i] );
                    }
                }
                
            

            }else{
                $listaTrabajadores[$i]['nombre'] = mb_convert_encoding( $trabajador['nombre'] , "UTF-8");
                //comprobando que los datos recopilados estén ok
                $splitInicioLab = explode("-",$trabajador['fechainiciolab'] );
                
                if ( ( $trabajador['tipo_movto'] == 'ingreso'  || $trabajador['tipo_movto'] == 'reingreso') || ( ($splitInicioLab[1] /1) == $mes && $splitInicioLab[0] == $anio)  ) {
                    
                    $idxAntInOut = $i;
                    $idTrabajadorAnt = $trabajador['nip'];
                    if ( $trabajador['tipo_movto'] == 'baja') { //unico registro cuya entrada fue tambien una baja
                        
                        array_push( $listaBajas, $trabajador );
                        
                        unset( $listaTrabajadores[$i] );
                    }else {
                        if ( $trabajador['tipo_movto'] == null ) {
                            $trabajador['fecha'] = $trabajador['fechainiciolab'];
                            $trabajador['tipo_movto'] = "ingreso";
                        }

                        array_push( $listaAltas, $trabajador );
                    }
                    // echo $trabajador['nip']."<bR>";
                }else if( $trabajador['tipo_movto'] == 'baja' && $trabajador['status'] == 99  ){
                    if ( $idTrabajadorAnt == $trabajador['nip']) {
                        // echo "ok";
                        unset( $listaTrabajadores[$idxAntInOut] );
                    }
                    array_push( $listaBajas, $trabajador );
                    // echo $trabajador['nombre']." ".$trabajador['fechainiciolab']."<br>";
                    unset( $listaTrabajadores[$i] );
                }else{
                    $splitInicioLab = $trabajador['fechainiciolab'];
                    if ( ($splitInicioLab[1] /1) == $mes && $splitInicioLab[0] == $anio) {
                        array_push( $listaAltas, $trabajador );
                    }
                }
            }
            
        }

        //depurando los repeitos y no los cuente varias veces 
        $numAltas = sizeof( $listaAltas);
        $numBajas = sizeof( $listaBajas);
        $idxAnterior = 0;
        $bajas = [];
        $altas= [];

        for ($i=1; $i < $numAltas ; $i++) { 
            if( $listaAltas[$idxAnterior]['nip'] == $listaAltas[$i]['nip']){
                if ( isset(  $listaAltas[$idxAnterior]['reingresos'] ) ) {
                    array_push( $listaAltas[$idxAnterior]['reingresos'] , $listaAltas[$i] );
                }else{
                    
                    $listaAltas[$idxAnterior]['reingresos'] = [  $listaAltas[$i] ];
                }
                unset( $listaAltas[$i]);
            }else{
                array_push( $altas , $listaAltas[$idxAnterior] );
                $idxAnterior = $i ;
            }
        }

        $idxAnterior = 0;
        for ($i=1; $i < $numBajas ; $i++) { 
            if( $listaBajas[$idxAnterior]['nip'] == $listaBajas[$i]['nip']){
                if ( isset(  $listaBajas[$idxAnterior]['bajas'] ) ) {
                    array_push( $listaBajas[$idxAnterior]['bajas'] , $listaBajas[$i] );
                }else{
                    $finDate = $listaBajas[$idxAnterior]['fecha'] != null  ? $listaBajas[$idxAnterior]['fecha']  : $listaBajas[$idxAnterior]['fecha_baja'] ;
                    $inicioDate = $listaBajas[$idxAnterior]['fechainiciolab'];
                    $diferencia=date_diff( date_create( $inicioDate ), date_create( $finDate) );
        
                    $listaBajas[$idxAnterior]['antiguedad'] =  $diferencia->format("%yA,%mM,%d D");
                    $listaBajas[$idxAnterior]['antiguedadDias'] =  $diferencia->format("%a");
                    $listaBajas[$idxAnterior]['bajas'] = [  $listaBajas[$i] ];
                }
                unset( $listaBajas[$i]);
            }else{
                $finDate = $listaBajas[$idxAnterior]['fecha'] != null  ? $listaBajas[$idxAnterior]['fecha']  : $listaBajas[$idxAnterior]['fecha_baja'] ;
                $inicioDate = $listaBajas[$idxAnterior]['fechainiciolab'];
                $diferencia=date_diff( date_create( $inicioDate ), date_create( $finDate) );
    
                $listaBajas[$idxAnterior]['antiguedad'] =  $diferencia->format("%yA,%mM,%dD");      
                $listaBajas[$idxAnterior]['antiguedadDias'] =  $diferencia->format("%a");          
                array_push( $bajas , $listaBajas[$idxAnterior] );
                $idxAnterior = $i ;
            }
        }


        foreach ($listaTrabajadores as $i => $trabajador) {
            $finDate = date('m') == $mes ? $anio."-".$mes."-".date("d")  : $anio."-".$mes."-".cal_days_in_month(CAL_GREGORIAN, $mes,$anio);
            $inicioDate = $trabajador['fechainiciolab'];
            $diferencia=date_diff( date_create( $inicioDate ), date_create( $finDate) );

            $trabajador['antiguedad'] =  $diferencia->format("%yA,%mM,%dD");
            $trabajador['antiguedadDias'] =  $diferencia->format("%a");
            array_push( $trabadoresActivos, $trabajador );
        }

        usort($trabadoresActivos, [$this,'cmp']); 
        usort($bajas, [$this,'cmp']); 
        usort($altas, [$this,'cmp']); 
        return [
            'activos' => $trabadoresActivos,
            'bajas' => $bajas,
            'altas' => $altas
        ];
    }

    public function cmp($a, $b)
    {
        if ($a['antiguedadDias'] == $b['antiguedadDias']) {
            return 0;
        }
        return ($a['antiguedadDias'] > $b['antiguedadDias']) ? -1 : 1;
    }

    public function agendarVacaciones( $idempleado , $fechas, $periodo ,$anio = '')
    {
        $listaFechas = explode( ",", $fechas );
        $cuenta = 0;
        foreach ( $listaFechas as $fecha) {
            
            $status =  $this->modeloTrabajador->agendarVacaciones( $idempleado ,$this->dateFormat( $fecha) , 'pendiente', $periodo );
         
            
            $cuenta = $status > 0 ? $cuenta +1 : $cuenta;
        }


        if( $cuenta > 0   ){
            return $this->modeloTrabajador->getVacacionesProgramadas( $idempleado , $anio != '' ? $anio : '' );
        }else{
            return [];
        }
    }

    public function actualizarVacaciones( $idempleado , $fechas, $estado )
    {
        $listaFechas = explode( ",", $fechas );

        $cuenta = 0;
        foreach ( $listaFechas as $fecha) {
            
            $status =  $this->modeloTrabajador->actualizarVacaciones( $idempleado ,$this->dateFormat( $fecha) , $estado );
         
            
            $cuenta = $status > 0 ? $cuenta +1 : $cuenta;
        }


        if( $cuenta > 0   ){
            return $this->modeloTrabajador->getVacacionesProgramadas( $idempleado , '' );
        }else{
            return [];
        }
    }

    public function getTrabajadoresConVacaciones( $anio = '' )
    {
        return $this->modeloTrabajador->getTrabajadoresConVacacionesProgramadas(  $anio != '' ? $anio : '' );
    }

    public function dateFormat( $fecha )
    {
        $fechaExplode = explode( "/", $fecha );

        return $fechaExplode[2]."-".$fechaExplode[1]."-".$fechaExplode[0];
    }

    public function verificarHuella(   )
    {
         $trabajadoresConHuella = $this->modeloTrabajador->verificarHuella(  );
         foreach ( $trabajadoresConHuella as $i => $trabajador) {
             $trabajadoresConHuella[$i]['nombre'] = utf8_encode( $trabajador['nombre'] );
         }

         return $trabajadoresConHuella;
    }

    public function getStatus( $nip )
    {
         $trabajadorStatus = $this->modeloTrabajador->getStatus( $nip );

         return $trabajadorStatus;
    }

    public function setEntradaSalidaReloj( $params )
    {
        $horaExplode = explode(" ", $params['timecheck'] );
        $fechaExplode = explode("-", $horaExplode[0]);
        $params['timecheck'] = $fechaExplode[2]."-".$fechaExplode[1]."-".$fechaExplode[0]." ".$horaExplode[1];
        return $this->modeloTrabajador->setEntradaSalidaReloj( $params );
    }

    public function setEntradaSalidaReloCSharp( $params)
    {
        return $this->modeloTrabajador->setEntradaSalidaReloj( $params );
    }
    public function setHuella( $params )
    {
        return $this->modeloTrabajador->setHuella( $params );
    }

    public function getTrabajadoresActivos()
    {
         $listaTrabajadores = $this->modeloTrabajador->getNominaActiva();

         foreach ( $listaTrabajadores as $i => $empleado) {
             $listaTrabajadores[$i]['nombre'] = mb_convert_encoding($empleado['nombre'] , "UTF-8" );
             $listaTrabajadores[$i]['religion'] = mb_convert_encoding($empleado['religion'] , "UTF-8" );
             $listaTrabajadores[$i]['name'] = $listaTrabajadores[$i]['nombre'];
             $listaTrabajadores[$i]['id'] = $listaTrabajadores[$i]['nip'];

         }

         return $listaTrabajadores;
    }

    public function saveFotoCredencial( $nip , $foto )
    {
        $file_name = $foto["name"];
        $extension = pathinfo( $foto["name"] );

            $guardado = move_uploaded_file($foto["tmp_name"],$_SERVER['DOCUMENT_ROOT']."/intranet/Empresa/foto_empleado/". $nip.".".$extension['extension']);

            if ( $guardado) {
               return 1;
            }

            return 0;
    }

    public function trabajadoresActivos()
    {
        $listaTrabajadores = $this->modeloTrabajador->getNominaActiva( );

        foreach ( $listaTrabajadores as $j => $empleado ) {
            

            $fechaActual = date_create( date("Y-m-d")  );
            $fechaNacimiento = date_create( $empleado['fechanac'] );
            $edad = date_diff( $fechaNacimiento , $fechaActual );
            $ultimoCambioAscripcion = $this->modeloTrabajador->ultimoCambioAdscripcion( $empleado['nip'] );
            $listaTrabajadores[$j]['nombre'] = mb_convert_encoding($empleado['nombre'] ,  "UTF-8" );
            $listaTrabajadores[$j]['puestoanterior'] = $ultimoCambioAscripcion[0]['fecha'] != ''  > 0 ? mb_convert_encoding($ultimoCambioAscripcion[0]['puesto'] , "UTF-8")  : "-";
            $listaTrabajadores[$j]['sucursalanterior'] = $ultimoCambioAscripcion[0]['fecha'] != '' ? $ultimoCambioAscripcion[0]['sucursal'] : "-";
            $listaTrabajadores[$j]['ultimocambioadscripcion'] = $ultimoCambioAscripcion[0]['fecha'] != '' ? $ultimoCambioAscripcion[0]['fecha']  : "-" ;
            $listaTrabajadores[$j]['edad'] = $edad->y;
            $listaTrabajadores[$j]['religion'] = mb_convert_encoding($empleado['religion'] , "UTF-8" ) ;
            $listaTrabajadores[$j]['tienehijos']  = $empleado['numhijos'] == '0-0' ? "NO" : "SI" ;
            //Obteniendo la cantidad de hijos que tiene
            $explodeHijos = explode("-", $empleado['numhijos'] );
            $listaTrabajadores[$j]['numhijos'] =  $explodeHijos[0] + $explodeHijos[1];
            $listaTrabajadores[$j]['asegurado'] = $empleado['asegurado'] == 'n' ? "NO" : 'Sí' ;
            
            //Obteniendo la antiguead del trabajador
            $fechcaInicioLaborar = date_create( $empleado['fechainiciolab']  );
            $fechaActual = date_create( date("Y-m-d")  );
            
           
            $diferencia = date_diff($fechcaInicioLaborar, $fechaActual);
            $listaTrabajadores[$j]['antigueadaAnio'] = $diferencia->y;
            $listaTrabajadores[$j]['antigueadaMes'] = $diferencia->m;
            $listaTrabajadores[$j]['antigueadaDia'] = $diferencia->d;
        
        }

        return $listaTrabajadores;
        
    }

    public function getDocumentacion( )
    {
        $documentacion = $this->modeloTrabajador->getDocumentacionAllTrabajadores();
        $listaTipoDoctos = $this->modeloTrabajador->getTipoDocumentos();

        $documentos = [];
        foreach ( $listaTipoDoctos as $k => $documen) {

            $documentos[$documen['id']]["tieneDocto"] = "NO";
            $documentos[$documen['id']]['documento'] = $documen['descripcion'];
        }
        $empleadosConDocumentos = [];
        $ultimoEmpleado = '';
        $i = 8;
        $j = 0;
        foreach ( $documentacion as $h => $documento ) {

            if ( $ultimoEmpleado != $documento['empleado'] ) {
                $i++;
                $ultimoEmpleado = $documento['empleado'];
                $empleadosConDocumentos[$documento['nip'] ] = $documentos;
                $empleadosConDocumentos[$documento['nip'] ] ['empleado']= mb_convert_encoding($documento['empleado'], 'UTF-8' );
                $j = 0;
            }

            $empleadosConDocumentos[$documento['nip'] ][$documento['iddoct']]['tieneDocto'] = "SI";
            
            //Verificando si tiene foto de perfil del empleado
            
            $formatos = ['jpg','jpeg'];
            foreach ($formatos as  $formato) {
                if( file_exists( $_SERVER['DOCUMENT_ROOT']."/intranet/Empresa/foto_empleado/".$documento['nip'].".$formato")  ){
                    //$empleadosConDocumentos[$documento['nip'] ][$documento['iddoct']] = "SI";
                    $empleadosConDocumentos[$documento['nip'] ][$documento['iddoct']]['tieneDocto'] = "SI";
                }
            }

        }

        return $empleadosConDocumentos;
    }

}



if ( isset( $_GET['opc'] ) ) {
    $trabajadorController = new TrabajadorController;
    switch ($_GET['opc'] ) {
        case 'getAcciones':
                $fechaInicio = isset( $_POST['fechaInicio'] )  ? $_POST['fechaInicio'] : date('Y-m-1');
                $fechaFin = isset( $_POST['fechaFin'])  ? $_POST['fechaFin'] : date('Y-m-d');
                
                echo json_encode( $trabajadorController->getAccionesCorrectiva( $fechaInicio , $fechaFin )  );
            break;
        case 'getAccionCorrectiva':
            echo json_encode( $trabajadorController->getAccionCorrectiva( $_POST['key']) );
        break;
        case 'delAccionCorrectiva':
        echo json_encode( $trabajadorController->deleteAccionCorrectiva( $_POST['key'] ) );
        break;
        case 'updateAccionCorrectiva':

        break;
        case 'agendarVacaciones':
            echo json_encode( $trabajadorController->agendarVacaciones( $_POST['trabajador'] , $_POST['fechas'], $_POST['periodo'],isset( $_POST['anio'] ) ? $_POST['anio']  : '' ) );
        break;
        case 'listarVacaciones':
                echo json_encode( $trabajadorController->getTrabajadoresConVacaciones( isset( $_POST['anio'] ) ? $_POST['anio'] : ''  ) );
        break;
        case 'actualizarVacaciones':
            
            echo json_encode( $trabajadorController->actualizarVacaciones( $_POST['trabajador'] , $_POST['fechas'] , $_POST['estado'] ) );
        break;
        case 'getTrabajadoresDeBaja':
                echo json_encode( $trabajadorController->getTrabajadoresDeBaja(   $_GET['empleado'] ) );
        break;
        case 'reactivarEmpleado':
            echo $trabajadorController->reactivarEmpleado( $_POST['trabajador'] , $_POST['fecha'] );
        break;
        case 'getHuella':
            echo  json_encode( $trabajadorController->verificarHuella(   ) );
            
        break;
        case 'setChecador':
            echo $trabajadorController->setEntradaSalidaReloj( $_POST );
        break;
        case 'setChecadorSucursales':
            echo $trabajadorController->setEntradaSalidaReloCSharp( $_GET );
        break;        
        case 'setHuella':
            echo $trabajadorController->setHuella( $_POST );
        break;
        default:
            
            break;
    }
}