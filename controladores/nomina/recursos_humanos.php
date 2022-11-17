<?php
if(!isset($_SESSION)){ 
	session_start(); 
}

require_once $_SERVER['DOCUMENT_ROOT'].'/intranet/modelos/nomina/trabajadores.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/intranet/modelos/nomina/incidencias.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/intranet/modelos/Clases/sucursales.php';

class RecursosHumanosController
{
    protected static $meses = ['','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
    private static $basePath;

    public function getPersonalSubordinado( $jefeSucursalId )
    {
        $modeloTrabajador = new Trabajador;
        $esJefeDeSucursal = $modeloTrabajador->verificaJefeDeSucursal( $jefeSucursalId );
        
        if ( sizeof( $esJefeDeSucursal ) > 0 ) {
            $listaTrabajadores = [];
            if ( $jefeSucursalId == 214 || $jefeSucursalId == 39) { //Luis avenaño o Luis Lopez
                $listaTrabajadores = $modeloTrabajador -> getPersonalSubordinado( $esJefeDeSucursal[0]['idsucursal'] , 1);
            }else{
                $listaTrabajadores = $modeloTrabajador -> getPersonalSubordinado( $esJefeDeSucursal[0]['idsucursal'] );
            }
            // var_dump( $listaTrabajadores );
            foreach ($listaTrabajadores as $i => $trabajador) {
                $listaTrabajadores[$i]['nombre'] = utf8_encode( $trabajador['nombre']);
            }
            return $listaTrabajadores;
        }
        
    }

    public function getAltasTrabajadores( $mes, $anio)
    {
        $modeloTrabjador = new Trabajador;
        $listaAltas = $modeloTrabjador->altasTrabajadores( $mes, $anio );
        foreach ($listaAltas as $i => $trabajador) {
            $listaAltas[$i]['nombre'] = utf8_encode( $trabajador['nombre']);
        }
        return $listaAltas;
    }

    public function getTrabajadoresBajas($mes, $anio)
    {
        $modeloTrabajador = new Trabajador;
        $listaBajas = $modeloTrabajador->getTrabajadoresBaja( $mes, $anio);
        foreach ($listaBajas as $i => $trabajador) {
            $listaBajas[$i]['nombre'] = utf8_encode( $trabajador['nombre']);
        }
        
        return $listaBajas;
    }

    public function getTotalesAltasBajas( $listadoTrabajadores )
    {
        $listadoTotalesAltas = [];
        foreach ( $listadoTrabajadores as $trabajador) {
            $fechaExplode = !isset($trabajador['fecha_baja']) ? explode('-', $trabajador['fechainiciolab'] ) :   explode('-', $trabajador['fecha_baja'] );
            $mes = $fechaExplode[1] / 1;
            if ( !isset( $listadoTotalesAltas[ self::$meses[$mes] ]) ) {
                $listadoTotalesAltas[ self::$meses[$mes ] ] = 1;
            } else {
                $listadoTotalesAltas[ self::$meses[$mes ] ] += 1;
            }
            
        }
        
        return $listadoTotalesAltas;
    }

    public function getTrabajadoresRetardos( $mes, $anio)
    {
        $modeloIncidencias = new Incidencias;
        $modeloTrabajador = new Trabajador;
        $listadoTrabajadores = $modeloTrabajador->getAllTrabajadoresRegistrados();
        $listaRetardos = [];
        foreach ($listadoTrabajadores as $trabajador) {
            $asistenciasTrabajador = $modeloIncidencias->getRetardosEinasistencias( $trabajador['nip'],$mes, $anio);
            
            foreach ( $asistenciasTrabajador as $asistencia) {
                
                if ( $asistencia['RETARDO'] > 0 && $asistencia['hora']  <= 10 ) {
                    if ( !isset( $listaRetardos[self::$meses[$asistencia['mes']] ]) ) {
                        $listaRetardos[self::$meses[$asistencia['mes']] ] = 1;
                    } else {
                        $listaRetardos[self::$meses[$asistencia['mes']] ] += 1;
                    }
                    
                }
            }
        }
        
        return $listaRetardos;
    }

    public function getFaltasTrabajadores( $mes, $anio)
    {
        $modeloIncidencias = new Incidencias;
        $modeloTrabajador = new Trabajador;
        $listadoTrabajadores = $modeloTrabajador->getAllTrabajadoresRegistrados();
        $listaFaltas = [];
        //En este ciclo se cuenta la cantidad de dias checados para trabajador
        foreach ($listadoTrabajadores as $t=> $trabajador) {
            $asistenciasTrabajador = $modeloIncidencias->getRetardosEinasistencias( $trabajador['nip'],$mes, $anio);
            
            $listadoTrabajadores[$t]['nombre'] = utf8_encode( $trabajador['nombre']);
            $listadoTrabajadores[$t]['noAsistencia'] = 0;
            $diasAsistidos =[];
            
            //obteniendo el total de asistencias en el mes del trabajador, ademas de un historico del acumulado del año en caso de que sea requerido

            foreach ( $asistenciasTrabajador as $index => $asistencia) {
                
                if ( !in_array( $asistencia['anio']."-".$asistencia['mes']."-".$asistencia['dia'],  $diasAsistidos ) ) {
                    if ( !isset( $listadoTrabajadores[$t]['asistenciaLista'][$asistencia['mes']] ) ) {

                        $listadoTrabajadores[$t]['asistenciaLista'][$asistencia['mes']] =1;
                        // $listadoTrabajadores[$t]['asistenciaLista'][$asistencia['mes']][$asistencia['dia']] =1;
                        $listadoTrabajadores[$t]['noAsistencia'] = 1;
                    } else {
                        if ( !isset($listadoTrabajadores[$t]['asistenciaLista'][$asistencia['mes']][$asistencia['dia']] ) ) {
                            // $listadoTrabajadores[$t]['asistenciaLista'][$asistencia['mes']][$asistencia['dia']] = 1;
                            $listadoTrabajadores[$t]['asistenciaLista'][$asistencia['mes']] +=1;
                            $listadoTrabajadores[$t]['noAsistencia'] += 1;
                        }
                        
                    }
                    array_push( $diasAsistidos , $asistencia['anio']."-".$asistencia['mes']."-".$asistencia['dia']);
                }
                
                    
            }
            

            //verificando el mes que o los meses que se van a evaluar
            if ( $mes != -1) {
                
            } else {
                for( $i= 1;  $i <= 12; $i++){
                    
                    $cantDiasMes = cal_days_in_month( CAL_GREGORIAN, $i, $anio );
                    $inicioLaborar = $trabajador['fechainiciolab'];
                    $splittedInicoLaborar = explode('-', $inicioLaborar);
                    $fechaBajaEmpleado = $trabajador['fecha_baja'];
                    $splittedBajaLaboral = explode( '-', $fechaBajaEmpleado );
                    $cantDiasHabilesMes = self::verificaDiaHabil($i, $anio);
                    $cantDiasLaborados = 0;

                    if ( $anio == date('Y') ) {
                        $currentMonth = (int)date('m');
                        if ( $i == $currentMonth ) {
                            $cantDiasMes = date('d');
                        }else if( $i > $currentMonth){
                            continue;
                        }
                    }

                    if ( $splittedInicoLaborar[0]  ==  $anio) {                        
                        if ( $splittedInicoLaborar[1] == $i) {
                            $diaInicio = $splittedInicoLaborar[2];

                            //obteniendo la cantidad de dias laborables realizados por el trabajador
                            for ($d= $diaInicio; $d <= $cantDiasMes  ; $d++) {  
                                $diaDelaSemana = date("N", strtotime("$anio-$mes-$d") );
                                if ( !isset( $splittedBajaLaboral[1] ) ) {
                                    $cantDiasLaborados++;
                                    continue;
                                }
                                if ( $splittedInicoLaborar[1] == $splittedBajaLaboral[1]) { //Fue dado de baja el mismo mes en que se le contrató?
                                    if ( $d <= $splittedBajaLaboral[2]) {
                                        if ( $diaDelaSemana != 7) {
                                            $cantDiasLaborados++;
                                        }
                                    }else{
                                        if ( $diaDelaSemana != 7) {
                                            $cantDiasLaborados++;
                                        }                                        
                                    }
                                }

                            }
                            // $cantDiasHabilesMes = self::verificaDiaHabil($i, $anio);
                            // $cantDiasNoLaborados = $cantDiasHabilesMes -$cantDiasLaborados;
                            // if( !isset( $listaFaltas[self::$meses[$i]] ) ){
                            //     $listaFaltas[self::$meses[$i]] = $cantDiasNoLaborados;
                            // }else{
                            //     $listaFaltas[self::$meses[$i]] += $cantDiasNoLaborados;
                            // }
//------------------------------------------------------------------ CONTINUAR COMPRENSIÓN DE CODIGO DESDE ACÁ--------------------------------------
                        }else if( $i > $splittedInicoLaborar[1] ){
                            $diaDelaSemana = date("N", strtotime("$anio-$mes-$d") );
                            if ( !isset( $listadoTrabajadores[$t]['asistenciaLista']) ) {
                                continue;
                            }
                            if ( isset ( $splittedBajaLaboral[1] ) ) {
                                if ( $i == $splittedBajaLaboral[1] ) { //Fue dado de baja el mismo mes en que se le contrató?
                                    if ( $d <= $splittedBajaLaboral[2]) {
                                        if ( $diaDelaSemana != 7) {
                                            $cantDiasLaborados++;
                                        }
                                    }   
                                }else{
                                    if ( $trabajador['fecha_baja'] == '' || $trabajador['fecha_baja'] == NULL ) {
                                        if ( $diaDelaSemana != 7) {
                                            $cantDiasLaborados++;
                                        }
                                    }                                    
                                }                   
                            }else{
                                if ( $trabajador['fecha_baja'] == '' || $trabajador['fecha_baja'] == NULL ) {
                                    if ( $diaDelaSemana != 7) {
                                        $cantDiasLaborados++;
                                    }
                                }
                            }
                        }                        
                    }else if( $anio > $splittedInicoLaborar[0]) {
                        if ( $splittedInicoLaborar[1] == $i) {
                            if ( !isset( $listadoTrabajadores[$t]['asistenciaLista']) ) {
                                continue;
                            }                            
                            $diasLaborados = $splittedInicoLaborar[2];
                            //obteniendo la cantidad de dias laborables realizados por el cliente
                            for ($d= 1; $d <= $cantDiasMes ; $d++) { 
                                if ( $d > date('d') ) { //No debe contabilizar  los dias que sobrepasan la fecha actual
                                    continue;
                                }
                                $diaDelaSemana = date("N", strtotime("$anio-$mes-$d") );
                                $date = "$anio-$mes-$d";
                                
                                if ( $diaDelaSemana != 7) {
                                    // echo "<br>Dia de la semana: $diaDelaSemana    $anio-$mes-$d<br>";
                                    $cantDiasLaborados++;
                                    // echo $cantDiasLaborados."<br><br>";
                                }
                            }
                            
                            $cantDiasNoLaborados = $cantDiasHabilesMes -$cantDiasLaborados;
                        }
                    }

                    if ( isset( $listadoTrabajadores[$t]['asistenciaLista'][$i] ) ) {
                        
                        if ( !isset( $listaFaltas[self::$meses[$i]] ) ) {
                            $listaFaltas[self::$meses[$i]]  =abs( $cantDiasLaborados - $listadoTrabajadores[$t]['asistenciaLista'][$i] );
                            // echo abs( $cantDiasLaborados - $listadoTrabajadores[$t]['asistenciaLista'][$i] )."<br>";
                        } else {
                            $listaFaltas[self::$meses[$i]]  +=abs( $cantDiasLaborados - $listadoTrabajadores[$t]['asistenciaLista'][$i] );
                            // echo abs( $cantDiasLaborados - $listadoTrabajadores[$t]['asistenciaLista'][$i] )."<br>";
                        }
                        
                        
                    }
                    
                }
            }            
        }

        
        // echo json_encode( $listaFaltas );
        echo json_encode( $listadoTrabajadores  );
        // var_dump( $listaFaltas );
        return $listaFaltas;        
    }

    public function getCambiosAdscripcion( $mes , $anio, $sucursal)
    {
      $trabajadorModelo = new Trabajador;
      $listadoCambios = $trabajadorModelo->getCambiosAdscripcion( $sucursal, $mes, $anio);   
      $listaCambios = [];
      foreach ( $listadoCambios as $cambioAdscripcion) {
            
        if ( !isset( $listaCambios['sale'][$cambioAdscripcion['salida']][self::$meses[$cambioAdscripcion['mes']]]) ) {
            $listaCambios['sale'][$cambioAdscripcion['salida']][self::$meses[$cambioAdscripcion['mes']]] = 1;
            $listaCambios['entra'][$cambioAdscripcion['llegada']][self::$meses[$cambioAdscripcion['mes']]] = 1;
        } else {
            $listaCambios['sale'][$cambioAdscripcion['salida']][self::$meses[$cambioAdscripcion['mes']]] += 1;
            $listaCambios['entra'][$cambioAdscripcion['llegada']][self::$meses[$cambioAdscripcion['mes']]] += 1;
        }
      }

      return $listaCambios;
    }

    public function verificaDiaHabil($mes, $anio)
    {
        //obtiene la cantidad de dias que tiene el mes sin contar los domingos , ya que no son hábiles para la empresa
        $cantDiasMes = cal_days_in_month( CAL_GREGORIAN, $mes, $anio );
        $cantDias = 0;
        for ($i=1; $i <= $cantDiasMes; $i++) { 
            $diaDelaSemana = date("N", strtotime("$anio-$mes-$i") );
            if ( $diaDelaSemana != 7) {
                $cantDias++;
            }
        }
        return $cantDias;
    }

    public function getTrabajadoresEnDepartamentos( $departamentos, $trabajadoresSeleccionados )
    {
        $departamentosIds = explode("#" ,$departamentos );
        $inContent = '';
        foreach ($departamentosIds as $i => $departamentoId) {
            $inContent .="'$departamentoId',";
        }
        $inContent = substr( $inContent,0, -1 );
        $modeloTrabajador = new Trabajador;
        $listadoTrabajadores = $modeloTrabajador->getTrabajadoresEnDepartamentos( $inContent );
        foreach ( $listadoTrabajadores as $i => $trabajador) {
            $listadoTrabajadores[$i]['nombre'] = utf8_encode( $trabajador['nombre'] );
            $listadoTrabajadores[$i]['check'] = !in_array( $trabajador['nip'], $trabajadoresSeleccionados ) ? "checked" : "";
        }

        return $listadoTrabajadores;
    }

    public function setRecursos( $params )
    {
        $params['path'] = self::setBasePath_folder($params['root'], $params['subDirectorios']);
        $cantidadAsignados = 0;
        if( $params['carpetaNueva'] == '' ){
            foreach ($params['archivo']['name'] as $i => $filename) {
                $targetFile = $params['path']."/".basename( $filename );

                
                if ( move_uploaded_file( $params['archivo']['tmp_name'][$i], $targetFile ) ) {
                    //Agregando en la base de datos 
                    $multipleInsert = '';
                    foreach ($params['trabajadores'] as $trabajadorId) {
                        $multipleInsert .="('".$targetFile."',".$trabajadorId."),";
                    }
                    $multipleInsert = substr($multipleInsert,0,-1);
                    $trabajadorModelo = new Trabajador;
                    $cantidadAsignados += $trabajadorModelo->setRecursos( $multipleInsert );
                }

            }
            return 2 + $cantidadAsignados;
        }else{
            if ( !is_dir($params['path']."/".$params['carpetaNueva']  ) ) {
                if ( mkdir( $params['path']."/".$params['carpetaNueva'] ) ) {
                    return 1;
                }
                return -1;
            }
            return -2;
        }
        
    }

        public function setBasePath_folder($root, $subFolders)
    {
        self::$basePath = $_SERVER['DOCUMENT_ROOT']."/intranet/Empresa/Recursos"; 
        $extractDepartamento = explode( '_', $root);
        self::$basePath .= "/".$extractDepartamento[0];
        $root_subFolders = $extractDepartamento[0];
        if ( $subFolders != '' ) {
            $extractFolders = explode('>', $subFolders);
            $canSubFolder = sizeof( $extractFolders );
            for ($i=0; $i < $canSubFolder; $i++) { 
                if ( $i < $canSubFolder) {
                    self::$basePath .= "/".$extractFolders[$i];
                    $root_subFolders .= "/".$extractFolders[$i];
                }
            }
        }
        
        return self::$basePath;
    }

    public function registraAsistenciaManual( $params )
    {
        $modeloTrabajador = new Trabajador;

        return $modeloTrabajador->registraAsistenciaManual( $params );
    }

    public function guardaCambioSucursal( $empleadoId , $sucursalId, $fecha)
    {
        $modeloTrabajador = new Trabajador;
        // Obteniendo los datos de contratación del trabajador para guardar el historial de movimientos de éste en las sucursales
        $contratoEmpleado = $modeloTrabajador->getDatosContratoEmpleado( $empleadoId );
        $contratoEmpleado = $contratoEmpleado[0];
        // Registrando el cambio en el historial
        $data =[
            'empleado' => $empleadoId,
            'destino' => $sucursalId,
            'origen' => $contratoEmpleado['idsucursal'],
            'puestoOrigen' => $contratoEmpleado['idpuesto'],
            'puestoDtno' => $contratoEmpleado['idpuesto'],
            'fecha' => self::dateFormat( $fecha )
        ];
        $modeloTrabajador->sethistorialAdscripcionTrabajador( $data );
        return $modeloTrabajador->cambiaSucursalDeAdscripcion( $empleadoId , $sucursalId );
    }

    public function AplicaAccionCorrectiva( $params)
    {
        $params['fechaIncidencia'] = trim( self::dateFormat( $params['fechaIncidencia'] ) );
        if ( isset( $params['fechaDescuento'] ) ) {
            $params['fechaDescuento'] = trim(self::dateFormat( $params['fechaDescuento'] ) );
        }

        if( !isset( $params['tipodeduccion'] ) ){
            $params['tipodeduccion'] = '002';
        }

        $modeloTrabajador = new Trabajador;

        
        if ( $params['esAcumulable'] == 1 || $params['esAcumulable']  == true ) {
            //obteniendo los días  que tiene el mes
            $explodeFechaSancion = explode("-" , $params['fechaIncidencia'] );
            
            $diasMes = cal_days_in_month( CAL_GREGORIAN , ( $explodeFechaSancion[1] ) , trim($explodeFechaSancion[0] ) );
            if ( $explodeFechaSancion[2] > 15 ) {
                $params['fechaDescuento'] =trim($explodeFechaSancion[0])."-".$explodeFechaSancion[1]."-".$diasMes;
            }else{
                $params['fechaDescuento'] =trim($explodeFechaSancion[0])."-".$explodeFechaSancion[1]."-15";
            }
            //OBTENIENDO EL ID DE LA INSERCION
            $modeloTrabajador->aplicaAccionCorrectivaAcumulable( $params );

            // var_dump( $params );
            return $modeloTrabajador->getUltimaAcorrectivaAcumulable( $params['empleado'] )['id'];
        }

        return $modeloTrabajador->aplicaAccionCorrectiva( $params );
    }

    public function aplicarPermiso( $params )
    {
        $params['fecha'] = self::dateFormat( $params['fecha'] );
        $modeloTrabajador = new Trabajador;

        return $modeloTrabajador->aplicarPermiso( $params );
    }

    public function dateFormat(  $date )
    {
        $dateSplited = explode( "/", $date );

        return trim($dateSplited[2])."-".trim($dateSplited[1]).'-'.trim($dateSplited[0]);
    }
}

if ( isset($_GET['opc']) ) {
    switch ($_GET['opc']) {
        case 'cantidadAltas':
                $listaAltas = RecursosHumanosController::getAltasTrabajadores( $_GET['mes'], $_GET['anio']) ;
                echo json_encode( RecursosHumanosController::getTotalesAltasBajas( $listaAltas ) );
            break;
        case 'getRetardos':
           echo json_encode( RecursosHumanosController::getTrabajadoresRetardos($_GET['mes'], $_GET['anio']) );
            break;
        case 'cantidadBajas':
                $listaBajas = RecursosHumanosController::getTrabajadoresBajas( $_GET['mes'], $_GET['anio']) ;
                echo json_encode( RecursosHumanosController::getTotalesAltasBajas( $listaBajas ) );
            break;
        case 'cantidadFaltas':
            RecursosHumanosController::getFaltasTrabajadores( $_GET['mes'], $_GET['anio'] );
            break;
        case 'getCambiosAds':
            echo json_encode( RecursosHumanosController::getCambiosAdscripcion( $_GET['mes'], $_GET['anio'], $_GET['sucursal']) );
            break;
        case 'getSucursales':
            $sucursalModelo = new Sucursales;
            echo json_encode( $sucursalModelo->getSucursales() );
            break;
        case 'asistenciaDePersonal':
            echo json_encode( RecursosHumanosController::getPersonalSubordinado( isset( $_SESSION['nip']) ? $_SESSION['nip'] : 1 ) );
            break;
            case 'aplicarSancion':

                    echo RecursosHumanosController::AplicaAccionCorrectiva( $_GET );
                break;            
        default:
            # code...
            break;
    }
}else if( isset($_POST['opc']) ){
    switch ($_POST['opc']) {
        case 'getTrabajadorForResources':
            $trabajadoresSeleccionados = isset( $_POST['trabajadores'] ) ? $_POST['trabajadores'] : [] ;
            echo json_encode( RecursosHumanosController::getTrabajadoresEnDepartamentos($_POST['departamentos'], $trabajadoresSeleccionados )  );
            break;
        case 'setRecursosTrabajador':
            $params = [
                'root' => $_POST['folder'],
                'subDirectorios'  => $_POST['path'],
                'archivo' => isset( $_FILES['nuevoArchivo'] ) ? $_FILES['nuevoArchivo'] : [],
                'carpetaNueva' =>  isset( $_POST['nuevoFolder']) ?str_replace(" ",'_', $_POST['nuevoFolder']) : '',
                'trabajadores' => isset($_POST['trabajadores']) ? explode( ',',$_POST['trabajadores'] ) : []
            ];
            echo RecursosHumanosController::setRecursos( $params );
            break;
        case 'registraAsistencia':
            echo RecursosHumanosController::registraAsistenciaManual( $_POST );
            break;
        case 'cambiarAdscripcion':
                echo RecursosHumanosController::guardaCambioSucursal( $_POST['empleado'] , $_POST['sucursal'], $_POST['fecha']);
            break;
        case 'aplicarSancion':

                echo RecursosHumanosController::AplicaAccionCorrectiva( $_POST );
            break;
        case 'aplicarPermiso':
                echo RecursosHumanosController::aplicarPermiso( $_POST );
            break;
        default:
            
            break;
    }
}
?>