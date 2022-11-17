<?php
//echo 'EOF 101';
require_once (dirname(__DIR__) ."/tienda/vendor/autoload.php");
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/contabilidad/programacionPagos.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/contabilidad/ControllerEgresos.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/contabilidad/AcreedorController.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/ResguardoController.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/ColaboradoresController.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/AleatorioExcepcionController.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/empresa/checklist.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/nomina/trabajadores.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/nomina/deducciones.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/empresa/Almacenes.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/empresa/articulos.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/nomina/empresa.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/empresa/Compras.php";   
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/empresa/proveedores.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/intranet/controladores/contabilidad/bancos/chequeras.php";
//echo 'EOF 102';
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/contabilidad/proveedores/cxp.php";
//echo 'EOF 103';

require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/Reportes/nomina/caps.php";

require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/recursos_materiales/insumos.php";

require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/Reportes/nomina/acuseAsistencia.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/ventas/vendedores.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/ventas/ventas.php";
require_once dirname(__DIR__)."/eshop/Controladores/Vehiculos.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/users/usuarios.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/empresa/permisos.php";

require_once $_SERVER['DOCUMENT_ROOT']."/intranet/Middleware/recursos/comentarios.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/Middleware/http.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/Middleware/correos.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/Middleware/recursos/FileManager.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/Middleware/apps/agenda/agenda.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/Middleware/notifications/correos.php";

require_once $_SERVER['DOCUMENT_ROOT']."/cavim/ajaxClass.php";


require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/quizz.php";


if( session_status() == PHP_SESSION_NONE ){
    session_start();
}

use League\Plates\Engine as Template;
use Klein\Klein as Route;
use Middlewares\recursos\CommentsHandler as CommentsHandler;

$base  = dirname($_SERVER['PHP_SELF']);

if(ltrim($base, '/')){ 

    $_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], strlen($base));
}

$klein = new \Klein\Klein();

$klein->respond("/", function($request,$response,$service){        
    $response->redirect("index.php", 200);       
});

$klein->respond("POST","/loginapp", function ( $request , $response , $service )
{
    $paramsPost = $request->paramsPost();
    $trabajadorController = new UsuarioController;

    echo json_encode( $trabajadorController->getUser( $paramsPost['user'] , $paramsPost['pass'] ) );
});

$klein->respond("GET","/trabajadores/all", function ( $request , $response , $service )
{
    $trabajadorController = new TrabajadorController;

    echo json_encode( $trabajadorController->getTrabajadoresActivos() );
});

$klein->respond("GET","/trabajadores/getStatus", function ( $request , $response , $service )
{
    $trabajadorController = new TrabajadorController;
    $paramsGet = $request->paramsGet();
    echo json_encode( $trabajadorController->getStatus( $paramsGet['nip']) );
});

// ROUTES PARA CAVIM*

$klein->respond("GET", "/productos/getfamilias",  function ( $request , $response , $service )
{    
    $articulosController = new ArticulosController; 
    echo json_encode( $articulosController->getFamilias() );
});

$klein->respond("GET", "/productos/getsubfamilias/[:familia]",  function ( $request , $response , $service )
{
    //$paramsGet = $request->paramsGet();
    $articulosController = new ArticulosController;
    echo json_encode( $articulosController->getSubfamiliasAPI( $request->familia  ) );
});

$klein->respond("GET", "/productos/getallsubfamilias",  function ( $request , $response , $service )
{
    //$paramsGet = $request->paramsGet();
    $articulosController = new ArticulosController;
    echo json_encode( $articulosController->getAllSubfamiliasAPI() );
});

$klein->respond("GET", "/cavim/getTickets/[:fecha]", function ( $request, $response, $service )
{
    $CavimController = new Cavim;
    if(!isset($request->fecha))
        $fecha = date('Y-m-d');
    else
        $fecha = $request->fecha;

    $arr = array();
    $arr[] = $CavimController->getTickets($fecha);
    echo json_encode($arr);
});

$klein->respond("GET", "/cavim/getTicketsAll/[:fecha]", function ( $request, $response, $service )
{
    $CavimController = new Cavim;
    if(!isset($request->fecha))
        $fecha = date('Y-m-d');
    else
        $fecha = $request->fecha;

    $arr = array();
    $arr[] = $CavimController->getTicketsAll($fecha);
    echo json_encode($arr);
});

//ROUTES PARA FACTURACION
$klein->respond("GET", "/sitexcloud/getClientesActivos/[:nombre]",  function ( $request , $response , $service )
{
    //$paramsGet = $request->paramsGet();
    $ventasController = new VentasController;
    echo json_encode( $ventasController->getClientesActivos($request->nombre) );
});

$klein->respond("GET", "/sitexcloud/facturacion/getTicketsFiltro/[:cliente]/[:almacen]/[:fechainicial]/[:fechafinal]/[:formapago]",  function ( $request , $response , $service )
{    
    //echo $request->cliente.','.$request->almacen.','.$request->fechainicial.','.$request->fechafinal.','.$request->formapago;
    $ventasController = new VentasController;
    echo json_encode( $ventasController->getTicketsFiltro($request->cliente, $request->almacen, $request->fechainicial, $request->fechafinal, $request->formapago) );
});

$klein->respond("GET", "/sitexcloud/facturacion/getAnticiposLiq/[:id]",  function ( $request , $response , $service )
{
    //$paramsGet = $request->paramsGet();
    $ventasController = new VentasController;
    echo json_encode( $ventasController->getAnticiposLiq($request->id) );
});

$klein->respond("GET", "/sitexcloud/facturacion/getFormasPagos",  function ( $request , $response , $service )
{
    //$paramsGet = $request->paramsGet();
    $ventasController = new VentasController;
    echo json_encode( $ventasController->getFormasPagos() );
});

$klein->respond("GET", "/sitexcloud/facturacion/getUsoCFDI",  function ( $request , $response , $service )
{
    //$paramsGet = $request->paramsGet();
    $ventasController = new VentasController;
    echo json_encode( $ventasController->getUsoCFDI() );
});

// ROUTES PARA INTRANET/NOMINA/*
    $klein->respond("GET","/nomina/altas-bajas", function ( $request , $response, $service)
    {
        $paramsGet = $request->paramsGet();
        $trabajadorController = new TrabajadorController;
        echo json_encode( $trabajadorController->listaTrabajadoresAltasBajas( $paramsGet['mes'] , $paramsGet['anio']) );
    });

    $klein->respond("GET","/nomina/solicitudesPersonal", function ( $request , $response, $service)
    {
        $paramsGet = $request->paramsGet();
        $trabajadorController = new TrabajadorController;
        echo json_encode( $trabajadorController->solicitudesPersonal());
    });

    // se usa get para mostrar la vista de incidencias

$klein->respond("GET", "/nomina/catalogo/deducciones", function ( $request , $response , $service)
{
    $deduccionController = new DeduccionesController;

    echo json_encode( $deduccionController->getCatalogo() );
});

$klein->respond("GET","/nomina/getTipoIncidencia", function ( $request , $response , $service )
{
    $paramsGet = $request->paramsGet();
    $incidenciasController = new DeduccionesController;
    echo json_encode( $incidenciasController->getTipoIncidencia() );
});

$klein->respond("GET","/nomina/getIncidencias/[:tipoMovimiento]", function ( $request , $response , $service )
{
    $incidenciasController = new DeduccionesController;
    echo json_encode( $incidenciasController->getIncidencias($request->tipoMovimiento) );
});

$klein->respond("GET","/nomina/getNomina/[:nivelusuario]", function ( $request , $response , $service )
{    
    $incidenciasController = new DeduccionesController;
    echo json_encode( $incidenciasController->getNomina($request->nivelusuario) );
});

$klein->respond("GET","/nomina/delIncidencia/[:id]", function ( $request , $response , $service )
{
    $incidenciasController = new DeduccionesController;
    echo json_encode( $incidenciasController->delIncidencia($request->id) );
});

$klein->respond("POST","/nomina/autIncidencia", function ( $request , $response , $service )
{
    $paramsPOST = $request->paramsPost();
    // echo $paramsPOST['monto'];
    $incidenciasController = new DeduccionesController;
    echo json_encode( $incidenciasController->autIncidencia($paramsPOST['id'],$paramsPOST['monto'],$paramsPOST['numpagos'],$paramsPOST['sms']) );
});

$klein->respond("POST","/nomina/addIncidencia", function ( $request , $response , $service )
{
    $paramsPOST = $request->paramsPost();
    $incidenciasController = new DeduccionesController;
    echo json_encode( $incidenciasController->addIncidencia($paramsPOST['idempleado'],$paramsPOST['idtipoincidencia'],$paramsPOST['idincidencia'],$paramsPOST['fechaaplicacion'],$paramsPOST['fechadescuento'],$paramsPOST['monto'],$paramsPOST['numpagos'],$paramsPOST['observaciones']) );
});

$klein->respond("GET","/nomina/getQuincena", function ( $request , $response , $service )
{    
    //$incidenciasController = new DeduccionesController;
    $arrpagos = array();
    $arrpagos[] = array('id'=>1,'name'=>'1');
    $arrpagos[] = array('id'=>2,'name'=>'2');
    $arrpagos[] = array('id'=>3,'name'=>'3');
    $arrpagos[] = array('id'=>4,'name'=>'4');
    $arrpagos[] = array('id'=>5,'name'=>'5');
    echo json_encode( $arrpagos );
});

$klein->respond("GET","/nomina/sendSMS", function ( $request , $response , $service )
{
    $paramsGet = $request->paramsGet();
    $incidenciasController = new DeduccionesController;
    echo json_encode( $incidenciasController->sendSMS($paramsGet['numero'],$paramsGet['mensaje']) );
});

$klein->respond("GET","/nomina/incidencias", function ( $request , $response , $service )
{
    $templates = new League\Plates\Engine('vistas');
    echo $templates->render("Nomina/incidencias" );
});

    //Se usa post para guardar incidencias
$klein->respond("POST", "/nomina/incidencias", function ( $request , $response , $service)
{
    $params = $request->paramsPost();
    $trabajadorController = new TrabajadorController;
    echo $trabajadorController->setIncidencia( [
        'tipoDeduccion' => $params['tipo'],
        'monto' => $params['monto'],
        'contratoId' => $params['contratoId'],
        'observaciones' => $params['observaciones']
    ]);

});

$klein->respond("/nomina/asistencia", function ( $request , $response , $service)
{
    $trabajadorController = new TrabajadorController;

    echo json_encode( $trabajadorController->getAsistenciaDiaria(  '2020-01-09') );
});

$klein->respond("POST", "/nomina/incidencia/[:retardo]" , function ( $request , $response , $service)
{
    $params = $request->paramsPost();
    $params = $params['incidencias'] ;

    $trabajadorController = new TrabajadorController;
    echo $trabajadorController->actualizaImporteIncidencia( $params );

});

$klein->respond("POST","/nomina/asistencia/[:tipo]", function ( $request , $service , $response)
{
   $params = $request->paramsPost();
   $trabajadorController = new TrabajadorController;

    

    if ( $request->tipo == 'retardo') {
        $responseUpdate =  $trabajadorController->setAplicacionIncidenciaAsistencia( $params['empleado'] , $params['checado'] , 0  );
        $trabajadorController->quitaInicidencia( $params['incidencia'] );
        echo $responseUpdate;
    } else {
        $responseUpdate =  $trabajadorController->setAplicacionIncidenciaAsistencia( $params['empleado'] , $params['checado'] , 0, $params['horaNvoIngreso'] );
         echo $trabajadorController->quitaInicidencia( $params['incidencia'] );
    }
    

});
// FIN ROUTES PARA INTRANET/NOMINA/*


///--------------------------ROUTES PARA REQUISICIÓN DE INSUMOS ----------


$klein->respond("GET","/insumos/requisicion" , function ( $request , $response , $service )
{
    

    $insumosController = new InsumosController ;
    echo json_encode($insumosController->getInsumos( ) , JSON_UNESCAPED_UNICODE );
});

$klein->respond("POST","/insumos/requisicion" , function ( $request , $response , $service )
{
    
    $paramsPost = $request->paramsPost();
    $solicitud = json_decode( $paramsPost['solicitud'] );
    
    $insumosController = new InsumosController ;
    echo ($insumosController->solicitarRequisicion( $solicitud ) );
});


$klein->respond("GET","/requisicion/pendings" , function ( $request , $response , $service )
{
    $insumosController = new InsumosController;

    echo json_encode( $insumosController->getRequisicionesSolicitudes() );
});

$klein->respond("GET","/requisicion/delivered" , function ( $request , $response , $service )
{
    $insumosController = new InsumosController;

    echo json_encode( $insumosController->getRequisicionesSolicitudes( true ) );
});

$klein->respond("GET","/requisicion/pendings/[:ids]" , function ( $request , $response , $service )
{
    $insumosController = new InsumosController;


    echo json_encode( $insumosController->getInsumosParaRequisicion( $request->ids ) );
});

$klein->respond("GET","/requisicion/surtir" , function ( $request , $response , $service )
{
    
    $templates = new League\Plates\Engine('vistas');
    echo $templates->render("RH/recursos_materiales" );
});

$klein->respond("POST","/requisicion/surtir" , function ( $request , $response , $service )
{
    $insumosController = new InsumosController;

    $paramsPost =   ( json_decode(file_get_contents('php://input'), true) );
    
   echo  $insumosController->setSurtidoRequisicion( $paramsPost['requisicion'] , $paramsPost['surtido'] );

});

$klein->respond("POST","/requisicion/reimpresion" , function ( $request , $response , $service )
{
    $insumosController = new InsumosController;

    $paramsPost =   ( json_decode(file_get_contents('php://input'), true) );
    
   echo  $insumosController->reimpresion( $paramsPost['requisicion']  );

});

////-------------------------------------------------- FIN ROUTES INSUMOS-----------------


////------------------ROUTES PARA DIFUNSION DE NOTIFICACION  DE MENSAJES ---------------------------------
$klein->respond("POST", "/notificaciones/cxp" , function ($request , $response , $service)
{
    $notificacion = new Notificaciones;
    $params = $request->paramsGet();
    $notificacion->aplicacionPagos( $params['id'] , $params['abono']);
});

////--------------------------------------------------------ROUTES DE CXP ------------------------------------------
$klein->respond("POST","/valida-usuario/cxp" , function ( $request , $response , $service )
{

    $usuarios = new \CxpController;
    $params = $request->paramsPost(); 
   echo  json_encode( $usuarios->validaUsuario([
       'usuario' => $params['user'] == "Administrador" ? $params['user'] : strtoupper( $params['user'] ),
       'pass' => strtoupper($params['pass'])
   ] ) );

});

$klein->respond("GET","/contabilidad/bancos/cuentas" , function ( $request , $response , $service )
{

    $chequeraController = new ChequeraController;
    
   echo  json_encode( $chequeraController->getCuentasPagoProveedores( ) );

});

$klein->respond("POST","/contabilidad/proveedores/pagos", function ( $request , $response , $service )
{
    $cxpController = new \CxpController;
    $paramsPost = $request->paramsPost();

     echo json_encode( $cxpController->aplicarPagoProveedor([
        'facturas' => $paramsPost['facturas'],
        'fechaAbono' => $paramsPost['fechaPago'],
        'montoAbonado' => $paramsPost['montoAbono'],
        'numcheque' => $paramsPost['numcheque'],
        'comentario'=> $paramsPost['observaciones'],
        'referencias'=> $paramsPost['referencias'],
        'medioPago' => $paramsPost['mediopago'],
        'usuario' => $paramsPost['usuario'],
        'nombreUsuario'=> $paramsPost['nombreUsuario']
    ]) );
});

////-----------------------------------------------------------FIN ROUTE CXP-----------------------------------------
$klein->respond("POST","/asistencia/recibos" , function ( $request , $response , $service )
{
    $paramsPost = $request->paramsPost();

    $acuseAsistencia = new AcuseDeAsistencia;
    $acuseAsistencia->generaHojaAcuse( $paramsPost[ 'asistencia' ] );
});

$klein->respond("POST","/trabajadores/fotografiar/save", function ( $request , $response , $service )
{
    $paramsPost = $request->paramsPost( );
    $trabajadorController = new TrabajadorController;
    $foto = $_FILES['foto'];
    
    echo ( $trabajadorController->saveFotoCredencial( $paramsPost['nip'] , $foto) );
});


$klein->respond("GET","/trabajadores/fotografiar", function ( $request , $response , $service )
{
    $templates = new League\Plates\Engine('vistas');
    echo $templates->render("RH/fotografiar" );

});


$klein->respond("POST","/caps", function ( $request , $response , $service )
{
    $trabajadorController = new TrabajadorController;
    $dataPost = $request->paramsPost();


    echo json_encode( $trabajadorController->setCambioAdscripcion( [ 'trabajador' => $dataPost['nip'],
    'fecha' => $dataPost['fecha'] ,
    'destino' => $dataPost['sucursal'],
    'nuevoPuesto' => $dataPost['puesto'],
    'sueldo' => $dataPost['sueldo ']]  ) );
});

$klein->respond("GET", "/reportes/caps", function ( $request , $response , $service )
{
    $data = $request->paramsGet();

    $reporte = new ReporteCaps;

    $reporte->generaReporte( $data['fecha']);
});


$klein->respond("GET","/sucursales/all", function ( $request , $response , $service )
{
    $sucursalController = new AlmacenController;

    echo json_encode( $sucursalController->getSucursales() );

});

$klein->respond("GET","/sucursales/allapp", function ( $request , $response , $service )
{
    $sucursalController = new AlmacenController;

    echo json_encode( $sucursalController->getSucursalesApp() );

});

$klein->respond("GET","/almacenes/allapp", function ( $request , $response , $service )
{
    $sucursalController = new AlmacenController;

    echo json_encode( $sucursalController->getAlmacenesApp() );

});

$klein->respond("GET","/almacenes/all", function ( $request , $response , $service )
{
    $sucursalController = new AlmacenController;

    echo json_encode( $sucursalController->getAlmacenes() );

});

$klein->respond("GET","/puesto/all", function ( $request , $response , $service )
{
    $empresaController = new EmpresaController;

    echo json_encode( $empresaController->getAllPuestos() );

});

$klein->respond("/contabilidad/facturacion" , function (  $request , $response , $service )
{
    $templates = new League\Plates\Engine('vistas');
    echo $templates->render("Contabilidad/factGrupal" );
});

$klein->respond( "/contabilidad/bancos/movimientos", function ( $request , $response , $service )
{

    $paramsGet = $request->paramsGet();

    $chequeraController = new ChequeraController;

    echo json_encode( $chequeraController->getEstadoCuenta([
        'fechaInicio' => $paramsGet['inicio']  == '' ? date("m/d/Y") : $paramsGet['inicio'] ,
        'fechaFin' => $paramsGet['fin']  == '' ? date("m/d/Y") : $paramsGet['fin'] 
    ]) );
        
});


$klein->respond("/contabilidad/programacion" , function (  $request , $response , $service )
{
    $templates = new League\Plates\Engine('vistas');
    echo $templates->render("Contabilidad/ProgramacionPagos" );
});

$klein->respond("GET","/contabilidad/fetch/programacion", function ( $request , $response , $service)
{
    $controllerProgramacion = new ProgramacionPagosController;

    echo json_encode( $controllerProgramacion->getAll() );
});


$klein->respond("GET", "/checklist/planAccion", function ( $request , $response , $service )
{
    $checklistController = new CheckListController;

    echo json_encode( $checklistController->getAllRubrosConPrioridad() );
});

$klein->respond("GET", "/checklist/filtro/planAccion", function ( $request , $response , $service )
{
    $checklistController = new CheckListController;
    $paramsGet = $request->paramsGet();
    $params = ['sucursal' => $paramsGet['sucursal'] ,
                        'dia' => $paramsGet['dia'],
                        'mes' => $paramsGet['mes'],
                        'anio' => $paramsGet['anio'],
                        'prioridad' => $paramsGet['prioridad'] ];

    echo json_encode( $checklistController->getChecklistPlanAccionFiltro( $params ) );
});


$klein->respond("GET", "/checklist/evidencias", function ( $request , $response , $service )
{
    $checklistController = new CheckListController;
    $params = $request->paramsGet();

    echo json_encode( $checklistController->geUrlImgsEvidencias( $params['id']) );
});

$klein->respond("POST", "/checklist/planAccion/autorizacion", function ( $request , $response , $service )
{
    $checklistController = new CheckListController;
    $paramsPost = $request->paramsPost();

    echo json_encode( $checklistController->setAutorizacionPlanAccion( $paramsPost['id'] , $paramsPost['estado'] , $paramsPost['fecha'] , $paramsPost['observaciones']) );
});


//+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+ ROUTES PARA PARA AREA DE COMPRAS+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+
$klein->respond( "/proveedores/compras", function ( $request , $response , $service )
{

    $templates = new League\Plates\Engine('vistas');
    echo $templates->render("Templates/compras/analisisCompras" );
        
});



$klein->respond('GET',"/proveedores/facturas", function ( $request , $response , $service)
{
    $comprasController  = new ComprasController;
    
    echo json_encode( $comprasController ->getFacturasCompra( ) );
});

$klein->respond('GET',"/proveedores/facturas/listaItems", function ( $request , $response , $service)
{
    $comprasController = new ComprasController;
    $paramsGet = $request->paramsGet();

    echo json_encode ( $comprasController->getListaArticulosCompra( $paramsGet['compra']  )  );
});

$klein->respond('GET',"/articulos/valuado", function ( $request , $response , $service)
{
    $articulosController = new ArticulosController;
    

    echo json_encode ( $articulosController->getValuado(  )  );
});

$klein->respond("GET","/sinregistrar/compras", function ( $request , $response , $service )
{
    $comprasController = new ComprasController;
    $paramsGet = $request->paramsGet();
    echo json_encode( $comprasController->getComprasSinProcesar( $paramsGet['factura'], $paramsGet['proveedor']) );
});

$klein->respond("POST", "/control/compras/recepcion",  function ( $request , $response, $service)
{
    $comprasController = new ComprasController;
    $paramsPost = $request->paramsPost();
    
    echo $comprasController->registraRecepcionMercancía([
        'compraId' => $paramsPost['compraId'],
        'factura' => $paramsPost['numFact'],
        'recepcion' => $paramsPost['fecha'],
        'usuario' => $paramsPost['usuario']
    ]);
});

$klein->respond("POST", "/control/compras/ingreso",  function ( $request , $response, $service)
{
    $comprasController = new ComprasController;
    $paramsPost = $request->paramsPost();
    
    echo $comprasController->registraAltaMercancia([
        'entradaId' => $paramsPost['numEntrada'],
        'ingreso' => $paramsPost['fecha'],
        'usuario' => $paramsPost['usuario'],
        'factura' => $paramsPost['factura']
    ]);
});

//+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+ ROUTES PARA PROVEEDORES+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+

$klein->respond("GET", "/proveedores", function ( $request, $response, $service )
{
    $proveedoresController = new ProveedoresController;

    echo json_encode( $proveedoresController->getProveedores() );
});

$klein->respond("GET", "/proveedores/deuda", function ( $request, $response, $service )
{

    $paramsGet = $request->paramsGet();

    $proveedoresController = new ProveedoresController;

    echo json_encode( $proveedoresController->getDeudaConProveedor( $paramsGet['proveedor'] ) );
});
$klein->respond("GET", "/proveedores/deuda/all", function ( $request, $response, $service )
{

    $proveedoresController = new ProveedoresController;

    echo json_encode( $proveedoresController->getDeudaToral( ) );
});

//+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+ ROUTES PARA MIDDLEWARES+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+
$klein->respond("GET", "/recursos/post/comments" ,function ( $request , $response , $service )
{
   $paramsPost =  $request->paramsPost();
   $postHandler= new CommentsHandler;
    $postHandler->post( $paramsPost );
});

$klein->respond("POST", "/correo", function ( $request , $response , $service )
{
    $middlewareCorreo = new MailSender();
    $paramsPost = $request->paramsPost();

    $middlewareCorreo->send( [
        'descripcionDestinatario' => $paramsPost['descripcionDestinatario'],
        'mensaje' => $paramsPost['mensaje'],
        'pathFile' => $paramsPost['pathFile'],
        'subject' => $paramsPost['subject'],
        'correos' => $paramsPost['correos']
    ]);
    
});

$klein->respond("GET", "/resources/delete", function ( $request , $response , $service )
{

    $paramsGet = $request->paramsGet();

    $middlewareRecursos = new FileManager;

    echo $middlewareRecursos->deleteFiles( $paramsGet['path'] );
    
});

$klein->respond("GET", "/obtenerPagosProgramados", function ( $request , $response , $service )
{
    $paramsGet = $request->paramsGet();

    $controllerEgresos = new ControllerEgresos;

    echo $controllerEgresos->getPagosProgramados( $paramsGet['fechaI'], $paramsGet['fechaF'], $paramsGet['pagination'] );
    
});

$klein->respond("POST", "/applyCreditors", function ( $request , $response , $service )
{
    $paramsPost = $request->paramsPost();

    $acreedorController = new AcreedorController;

    echo $acreedorController->creditorsProcess( $paramsPost['acreedor'], $paramsPost['aliasAcreedor'], $paramsPost['monto'], $paramsPost['plazo'], $paramsPost['interes'], $paramsPost['fecha'] );
    
});

$klein->respond("GET", "/obtenerCreditosAcreedores", function ( $request , $response , $service )
{
    $paramsPost = $request->paramsGet();

    $acreedorController = new AcreedorController;

    echo $acreedorController->creditors(  );
    
});

$klein->respond("GET", "/obtenerDetallePago", function ( $request , $response , $service )
{
    $paramsPost = $request->paramsGet();

    $acreedorController = new AcreedorController;

    echo $acreedorController->detallePagos( $paramsPost['id'] );
    
});


$klein->respond("POST", "/payTo", function ( $request , $response , $service )
{
    $paramsPost = $request->paramsPost();

    $acreedorController = new AcreedorController;

    echo $acreedorController->payTo( $paramsPost['montoAplicado'], $paramsPost['interesGenerado'], $paramsPost['fechaAplicacion'], $paramsPost['id'], $paramsPost['ok'] );
    
});

$klein->respond("POST", "/eliminarCredito", function ( $request , $response , $service )
{
    $paramsPost = $request->paramsPost();

    $acreedorController = new AcreedorController;

    echo $acreedorController->down( $paramsPost['id'] );
    
});

$klein->respond("GET", "/Resguardos", function ( $request , $response , $service )
{
    $templates = new League\Plates\Engine('vistas');
    echo $templates->render("resguardos");
});

$klein->respond("POST", "/obtenerResguardo", function ( $request , $response , $service )
{
    $paramsPost = $request->paramsPost();

    $resguardoController = new ResguardoController;

    echo $resguardoController->getResg( $paramsPost['pagination'] );
});

$klein->respond("POST", "/obtenerResguardosPaginados", function ( $request , $response , $service )
{
    $paramsPost = $request->paramsPost();

    $resguardoController = new ResguardoController;

    echo $resguardoController->getResgPags( $paramsPost['empleado'], $paramsPost['tipoResg'], $paramsPost['fechaResg'], $paramsPost['pagination'] );
});

$klein->respond("POST", "/obtenerResguardosEmpleado", function ( $request , $response , $service )
{
    $paramsPost = $request->paramsPost();

    $resguardoController = new ResguardoController;

    echo $resguardoController->getResgEmp( $paramsPost['tipoResg'], $paramsPost['id'] );
});

$klein->respond("POST", "/guardarResguardoEquipoCel", function ( $request , $response , $service )
{
    $paramsPost = $request->paramsPost();

    $resguardoController = new ResguardoController;

    echo $resguardoController->saveResg( $paramsPost['data'], $paramsPost['chks'], $paramsPost['tipo_resg'] );
});

$klein->respond("POST", "/actualizarResguardo", function ( $request , $response , $service )
{
    $paramsPost = $request->paramsPost();

    $resguardoController = new ResguardoController;

    echo $resguardoController->updateResg( $paramsPost['data'], $paramsPost['chks'], $paramsPost['tipo_resg'] );
});

$klein->respond("POST", "/guardarResguardoEquipoComputo", function ( $request , $response , $service )
{
    $paramsPost = $request->paramsPost();

    $resguardoController = new ResguardoController;

    echo $resguardoController->saveResgEquipoComputo( $paramsPost['data'], $paramsPost['tipo_resg'] );
});

$klein->respond("POST", "/actualizarResguardoEquipoComputo", function ( $request , $response , $service )
{
    $paramsPost = $request->paramsPost();

    $resguardoController = new ResguardoController;

    echo $resguardoController->updateResgEquipoComputo( $paramsPost['data']);
});

$klein->respond("GET", "/obtenerExcepcionesAleatorios", function ( $request , $response , $service )
{
    $paramsGet = $request->paramsGet();

    $aleatorioExcepcionController = new AleatorioExcepcionController;

    echo $aleatorioExcepcionController->getAllExceptios( $paramsGet['data']);
});

$klein->respond("POST", "/guardarExcepcionAleatorio", function ( $request , $response , $service )
{
    $paramsPost = $request->paramsPost();

    $aleatorioExcepcionController = new AleatorioExcepcionController;

    echo $aleatorioExcepcionController->saveNewException( $paramsPost['udnSeleccionada'], $paramsPost['razon'], $paramsPost['fecha']);
});

$klein->respond("GET", "/getBajasColaboradores", function ( $request , $response , $service )
{
    $colaboradoresController = new ColaboradoresController;

    echo $colaboradoresController->index();
});

//+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+ ROUTES PARA APLICACIONES MÓVILES+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+
$klein->respond("POST", "/agenda/dispositivos", function ( $request , $response, $service)
{
    $paramsPost = $request->paramsPost();
    $middleAgenda = new AgendaMiddleware;
    echo  $middleAgenda->registraDispositivo( $paramsPost['query']);
});

$klein->respond("GET", "/agenda/recordatorio", function ( $request , $response, $service)
{
    
    $middleAgenda = new AgendaMiddleware;
    echo  $middleAgenda->enviaRecordatorio(  );
});


$klein->respond("POST", "/nomina/requisicion-personal", function ( $request , $response , $service )
{
    $paramsPost = $request->paramsPost();
    $trabajadorController  = new TrabajadorController;


    echo $trabajadorController->solicitarPersonal([
            'solicitante' => $paramsPost['solicitante'],
            'fecha' => $paramsPost['fecha_contratacion'],
            'puesto' => $paramsPost['puesto'],
            'nVacantes' => $paramsPost['vacantes'],
            'sucursal' => $paramsPost['sucursal'],
            'nipRecomendado' => $paramsPost['recomendadoNip'],
            'cualidades' => $paramsPost['cualidades'],
            'motivoRecomendacion' => $paramsPost['motivoRecomendacion']
    ]);
});

$klein->respond("GET", "/nomina/requisicion-personal", function ( $request , $response , $service )
{
    //$paramsPost = $request->paramsPost();
    $trabajadorController  = new TrabajadorController;

    echo json_encode( $trabajadorController->getRequisicionPersonal() );
});

$klein->respond("GET", "/nomina/requisicion-personal/notifica", function ( $request , $response , $service )
{
    $paramsGet = $request->paramsGet();
    $trabajadorController  = new TrabajadorController;

    echo json_encode( $trabajadorController->setCorreoRequisicion( $paramsGet['requisicion']) );
});


$klein->respond("POST", "/saveAsistencia",  function ( $request , $response , $service )
{
    
    $trabajadorController = new TrabajadorController;
    $paramsPost = $request->paramsPost();
    $data = [
        'idempleado' => $paramsPost['empleado'],
        'latitud' => $paramsPost['latitud'],
        'longitud' => $paramsPost['longitud'],
        'imagen' => $paramsPost['image']
    ];    
    if($trabajadorController->setAsistencia( $data )>0)
        echo 'Registro Correcto';
    else
        echo 'Error. Comunicarse con el area de TI';
});

$klein->respond("GET", "/appmenus", function ( $request , $response , $service )
{   
    $paramsPost = $request->paramsGet();
    $permisosController = new PermisosController;
    echo json_encode( $permisosController->getAppMenu( $paramsPost['usuariotipo'],  $paramsPost['vista'] ));
   
});

$klein->respond("GET", "/ventas/vendedores", function ( $request , $response , $service )
{
    $vendedoresController = new VendedoresController;
    echo json_encode( $vendedoresController->getAll() );
   
});

$klein->respond(["GET","POST"],"/vendedores/comisiones-", function ( $request , $response, $service )
{
   $vendedoresController = new VendedoresController;
    $paramsPost = $request->paramsPost();

   echo json_encode( $vendedoresController->getComisiones( $paramsPost['dia'],  $paramsPost['mes'] , $paramsPost['anio']==''?date("Y"):$paramsPost['anio'] ) );
});

$klein->respond(["GET","POST"],"/vendedores/comisionesfam-", function ( $request , $response, $service )
{
   $vendedoresController = new VendedoresController;
    $paramsPost = $request->paramsGet();

   echo json_encode( $vendedoresController->getComisionesFam( $paramsPost['dia'],  $paramsPost['mes']==''?date("m")*1:$paramsPost['mes'] , $paramsPost['anio']==''?date("Y"):$paramsPost['anio'] ) );
});

$klein->respond("GET", "/ventas/razones-fallidas", function ( $request , $response , $service )
{
    $ventasController = new VentasController;
    echo json_encode( $ventasController->getRazonesVentasFallidas() );
   
});

$klein->respond("POST", "/ventas/control-piso", function ( $request , $response , $service )
{
    $paramsPost = $request->paramsPost();

    $ventasController = new VentasController;
    echo json_encode( $ventasController->setHistorialControlPiso([
        'vendedor' => $paramsPost['vendedor'],
        'vehiculo' => $paramsPost['vehiculo'],
        'cliente' => $paramsPost['cliente'],
        'email' => $paramsPost['email'],
        'producto' => $paramsPost['producto'],
        'razon' => $paramsPost['razon']
    ]) );
   
});

$klein->respond("POST", "/ventas/ingresos-", function ( $request , $response, $service)
{
   $paramsPost = $request->paramsPost();
    $ventasController = new VentasController;

    echo json_encode( $ventasController->getFlujoIngresos( $paramsPost['fecha']) );
});

$klein->respond("POST", "/ventas/utilidad-", function ( $request , $response, $service)
{
   $paramsPost = $request->paramsPost();
    $ventasController = new VentasController;

    echo json_encode( $ventasController->getUtilidadVenta( $paramsPost['fecha']) );
});

$klein->respond("POST", "/ventas/edoRes-", function ( $request , $response, $service)
{
   $paramsPost = $request->paramsPost();
    $ventasController = new VentasController;
    //echo $paramsPost['fecha'];
    //echo json_encode( $ventasController->getEdoRes( '2022-7' ) );
    echo json_encode( $ventasController->getEdoRes( $paramsPost['fecha']) );
});

$klein->respond(["GET","POST"], "/ventas/metas-", function ( $request , $response, $service)
{
   $paramsPost = $request->paramsPost();
    $ventasController = new VentasController;

    echo json_encode( $ventasController->getMetasSucursales( $paramsPost['mes'] , $paramsPost['anio'] == '' ? date("Y") : $paramsPost['anio'] ) );
});

$klein->respond(["GET","POST"], "/ventas/metasVendedores-", function ( $request , $response, $service)
{
   $paramsPost = $request->paramsPost();
    $ventasController = new VendedoresController;

    echo json_encode( $ventasController->getMetasVendedores( $paramsPost['mes'] , $paramsPost['anio'] == '' ? date("Y") : $paramsPost['anio'] ) );
});


$klein->respond("POST", "/ventas/metasVendedoresTabla-", function ( $request , $response, $service)
{
   $paramsPost = $request->paramsPost();
    $ventasController = new VendedoresController;

    echo json_encode( $ventasController->getMetasVendedoresTabla( $paramsPost['mes'] , $paramsPost['anio'] == '' ? date("Y") : $paramsPost['anio'] ) );
});


$klein->respond("GET", "/ventas/ingresos-", function ( $request , $response, $service)
{
   $paramsPost = $request->paramsGet();
    $ventasController = new VentasController;

    echo json_encode( $ventasController->getFlujoIngresos( $paramsPost['fecha']) );
});

$klein->respond("GET", "/ventas/ingresos2", function ( $request , $response, $service)
{
   $paramsPost = $request->paramsGet();
    $ventasController = new VentasController;

    echo json_encode( $ventasController->getFlujoIngresos( $paramsPost['fecha']) );
});


$klein->respond("GET", "/almacen/v/traspasos/",  function ( $request , $response , $service )
{
    $templates = new League\Plates\Engine('vistas');
    echo $templates->render("almacen/traspasos" );
});

$klein->respond("GET", "/almacen/traspasos/",  function ( $request , $response , $service )
{
    $paramsPost = $request->paramsGet();
    $data = [
        'inicio' => $paramsPost['fechaInicio'],
        'fin' => $paramsPost['fechaFin'],
        'sucursal' => $paramsPost['sucursal'],
    ];

    $almacenController = new AlmacenController;
    echo json_encode( $almacenController->foliosTraspasos( $data ) );

});


$klein->respond("POST", "/almacen/entradas-salidas",  function ( $request , $response , $service )
{
    $paramsPost = $request->paramsPost();
    $data = [
        'inicio' => $paramsPost['fechaInicio'],
        'fin' => $paramsPost['fechaFin'],
        'usuario' => $paramsPost['usuario'],
        'folio' => $paramsPost['folio'] 
    ];
    $almacenController = new AlmacenController;
    echo json_encode( $almacenController->getEntradaSalida( $data ) );
});

$klein->respond("GET", "/almacen/gral/entradas-salidas",  function ( $request , $response , $service )
{
    

    $almacenController = new AlmacenController;
    echo json_encode( $almacenController->getGeneralEntradasSalidas(  ) );
});

$klein->respond("GET", "/almacen/traspasos/emitidos-devueltos",  function ( $request , $response , $service )
{
    

    $almacenController = new AlmacenController;
    echo json_encode( $almacenController->getGeneralEmitidosDevueltos(  ) );
});

$klein->respond("GET", "/almacen/inventarios/productoswithoutImg",  function ( $request , $response , $service )
{
    

    $almacenController = new AlmacenController;
    echo json_encode( $almacenController->getProductoswithoutImg(  ) );
});


$klein->respond("GET", "/almacen/entradas-salidas/usuarios",  function ( $request , $response , $service )
{
    
    $almacenController = new AlmacenController;
    echo json_encode( $almacenController->getUsuariosMovtosAlmacenes() );
});

$klein->respond("GET", "/productos",  function ( $request , $response , $service )
{
    
    $articulosController = new ArticulosController;
    $paramsPost = $request->paramsGet();
    $data = [
        'idusuario' => $paramsPost['user'],
        'familia' => strtoupper( $paramsPost['familia'] ),
        'subfamilia' => strtoupper( $paramsPost['subfamilia'] ),
        'codigo' => strtoupper( $paramsPost['codigo'] )
    ];    
    echo json_encode( $articulosController->getArticulosConPrecios( $data ) );
});

$klein->respond("GET", "/productosagrup",  function ( $request , $response , $service )
{
    
    $articulosController = new ArticulosController;
    $paramsPost = $request->paramsGet();
    $data = [
        'familia' => strtoupper( urldecode($paramsPost['familia']) ),
        'subfamilia' => strtoupper( urldecode($paramsPost['subfamilia']) ),
        'almacen' => strtoupper( urldecode($paramsPost['almacenid']) )
    ];    
    echo json_encode( $articulosController->getArticulosAgrup( $data ) );
});

$klein->respond("GET", "/showConfirmado",  function ( $request , $response , $service )
{
    
    $almacenController = new AlmacenController;
    $paramsPost = $request->paramsGet();
    $data = [
        'fecha' => strtoupper( $paramsPost['fecha'] )
    ];    
    echo json_encode( $almacenController->getConfirmados( $data ) );
});


$klein->respond("GET", "/productos/familias",  function ( $request , $response , $service )
{
    
    $articulosController = new ArticulosController;
    echo json_encode( $articulosController->getFamilias() );
});

$klein->respond("GET", "/productos/subfamilias",  function ( $request , $response , $service )
{
    $paramsGet = $request->paramsGet();
    $articulosController = new ArticulosController;
    echo json_encode( $articulosController->getSubfamilias( $paramsGet['familia']  ) );
});

$klein->respond("GET", "/checkinventario",  function ( $request , $response , $service )
{
    
    $almacenController = new AlmacenController;
    $paramsPost = $request->paramsGet();
    $data = [
        'familia' => strtoupper( $paramsPost['familia'] ),
        'subfamilia' => strtoupper( $paramsPost['subfamilia'] ),
        'almacen' => strtoupper( $paramsPost['almacenid'] ),
        'usuario' => strtoupper( $paramsPost['usuario'] )
    ];    
    echo json_encode( $almacenController->setCheckInventario( $data ) );
});




$klein->respond("GET","/autos/lista",  function ($request, $response, $service)
{
    $vehiculoController = new VehiculosController;
    echo json_encode( $vehiculoController->getAll() );
});

$klein->respond("GET","/productosColision",  function ($request, $response, $service)
{
    $paramsGet = $request->paramsGet();
    $articulosController = new ArticulosController;
    echo json_encode( $articulosController->getPvpXFamilia( $paramsGet['codproduct'], $paramsGet['descripcion'] ) );
});

// QUIZZ
$klein->respond("GET","/quizz/getPreguntas/[:idusuario]",  function ($request, $response, $service)
{   
    $quizzController = new QuizzController;
    echo json_encode( $quizzController->getPreguntas( $request->idusuario ) );
});

//--------------------


//+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+ ROUTES PARA QLIK SENSE+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+*+
$klein->respond("GET", "/bi/financiero/recartera" ,function ( $request , $response , $service )
{
    
    require_once $_SERVER['DOCUMENT_ROOT']."/qlik_cxc_recup_cartera.php";
    $recuperacion_cartera = new RecuperacionCartera;
    echo json_encode(  $recuperacion_cartera->setDataDisplay()  );
});


$klein->respond("GET", "/bi/financiero/deudacxp" ,function ( $request , $response , $service )
{
    
    require_once $_SERVER['DOCUMENT_ROOT']."/qlik_cxpdeuda.php";
    $deuda = new Qlik\CXPController;
    echo json_encode( $deuda->qlikData() );
});

$klein->respond("GET", "/bi/financiero/pagoscxp" ,function ( $request , $response , $service )
{
    
    require_once $_SERVER['DOCUMENT_ROOT']."/qlik_cxp.php";
    // $deuda = new CXPController;
    // echo json_encode( $deuda->qlikData() );
});


$klein->respond("GET", "/bi/financiero/cxc" ,function ( $request , $response , $service )
{
    
    require_once $_SERVER['DOCUMENT_ROOT']."/qlik_cxc.php";
    $deuda = new CXCDeudaClientes;
    echo json_encode( $deuda->qlikData() );
});
// $klein->respond("GET", "/bi/financiero/cxc" ,function ( $request , $response , $service )
// {
    
//     require_once $_SERVER['DOCUMENT_ROOT']."/qlik_cxc.php";
//     $deuda = new CXCDeudaClientes;
//     echo json_encode( $deuda->qlikData() );
// });
$klein->respond("GET", "/bi/ventas" ,function ( $request , $response , $service )
{
    
    require_once $_SERVER['DOCUMENT_ROOT']."/tablaqlik.php";
    $deuda = new VentasQlik;
    echo $deuda->qlikData();
});
$klein->respond("GET", "/bi/financiero/gastost" ,function ( $request , $response , $service )
{
    
    require_once $_SERVER['DOCUMENT_ROOT']."/qlik_gastos.php";
    $data = new GastosQlik;
    echo json_encode( $data->qlikData(''));
});
$klein->respond("GET", "/bi/financiero/gastosf" ,function ( $request , $response , $service )
{
    
    require_once $_SERVER['DOCUMENT_ROOT']."/qlik_gastos.php";
    $data = new GastosQlik;
    echo json_encode( $data->qlikData('f'));
});
$klein->respond("GET", "/bi/financiero/gastoso" ,function ( $request , $response , $service )
{
    
    require_once $_SERVER['DOCUMENT_ROOT']."/qlik_gastos.php";
    $data = new GastosQlik;
    echo json_encode( $data->qlikData('o'));
});
$klein->respond("GET", "/bi/compras" ,function ( $request , $response , $service )
{
    
    require_once $_SERVER['DOCUMENT_ROOT']."/qlik_compras.php";
    $data = new ComprasQlik;
    echo json_encode( $data->qlikData() );
});

$klein->respond("GET", "/bi/rh" ,function ( $request , $response , $service )
{
    $trabajadorController = new TrabajadorController;
    echo json_encode( $trabajadorController->trabajadoresActivos() );
});

$klein->respond("GET", "/bi/rh/documentos" ,function ( $request , $response , $service )
{
    $trabajadorController = new TrabajadorController;
	
	$arr = array();
	$result = $trabajadorController->getDocumentacion();
	
	
	foreach($result as $index1 => $val){
		$arr2 = null;
		$arr2 = array();
		foreach($val as $index => $subval){
			if($index=='empleado'){
				$arr1 = array('empleado'=>$subval);
			}else{
				$arr2[$subval['documento']] = $subval['tieneDocto'];
			}			
		}
		$nuevo = array_merge($arr1,$arr2);
		array_push($arr,$nuevo);
	}
    echo json_encode( $arr );
	
	//echo json_encode( $trabajadorController->getDocumentacion() );
});


$klein->dispatch();