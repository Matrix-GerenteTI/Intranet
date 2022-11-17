<?php
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/Empresa/Checklist.php";
require_once $_SERVER['DOCUMENT_ROOT'].'/intranet/lib/phpmailer/class.phpmailer.php';

class CheckListController  
{
    
    protected $modeloChecklist;

    public function __construct()
    {
         $this->modeloChecklist = new CheckList;
    }

    public function getAllRubrosConPrioridad( )
    {
        $listaPlanes = $this->modeloChecklist->getAllPlanAccionConPrioridad();
        
        foreach ($listaPlanes as $i => $planAccion) {
            $listaPlanes[$i]['plan_accion'] = mb_convert_encoding( $planAccion['plan_accion'], "UTF-8" ) ;
            $listaPlanes[$i]['pevaluada'] =  mb_convert_encoding( $planAccion['pevaluada']  , "UTF-8" ) ;
        }

        return $listaPlanes;
    }

    public function getChecklistPlanAccionFiltro( $params )
    {
        $listaPlanes = $this->modeloChecklist->getPlanAccionFiltrado( $params );
        
        foreach ($listaPlanes as $i => $planAccion) {
            $listaPlanes[$i]['plan_accion'] = mb_convert_encoding( $planAccion['plan_accion'], "UTF-8" ) ;
            $listaPlanes[$i]['pevaluada'] =  mb_convert_encoding( $planAccion['pevaluada']  , "UTF-8" ) ;
        }

        return $listaPlanes;
    }

    public function geUrlImgsEvidencias( $idElemento )
    {
        //Obteniendo el id del checklist padre
        $infoPregunta = $this->modeloChecklist->getIdpadreItemChecklist( $idElemento )[0];
        $seccionPregunta = $this->modeloChecklist->getDetallePregunta( $infoPregunta['cpregunta_id'] )[0];

        $listaImgs =  $this->modeloChecklist->getEvidenciaPregunta( $infoPregunta['idchecklist'] , $infoPregunta['cpregunta_id'] ,$seccionPregunta['cseccion_id'] );

        $urls = [];
        foreach ( $listaImgs as $i => $img) {
            array_push( $urls , "http://servermatrixxxb.ddns.net/checklist/fotos/".$img['imagen'] );
        }

        return [ 'urls' => $urls ];
    }

    public function setAutorizacionPlanAccion( $id , $estado , $fecha = '' , $observaciones = '' )
    {
        $status = $estado == 's' ? 1: 0;
        if ( $estado == 's') {
            //Obteniendo el plan de accion  y las observaciones
             $datosAutorizado = $this->modeloChecklist->getDetallePregunta( $id )[0];
            //  Obtenienendo las observaciones y el plan de accion
            $planAccionObservaciones = explode("@_" , $datosAutorizado['plan_accion']);


            // $this->enviarReporte( ["descripcionDestinatario" => "Autorizacion Plan Accion Checklist",
            // "mensaje" => "Se ha autorizado la siguiente peticion por parte de la <b>V.P. DE ADMINISTRACIÓN Y FINANZAS</b> para realizar llevar a cabo la siguiente petición: <br>
            //                         ".$planAccionObservaciones[0]. "<br>
            //                         <b>Observaciones de la V.P. DE ADMINISTRACIÓN Y FINANZAS: </b>".$planAccionObservaciones[1],
            // "pathFile" => "",
            // "subject" => "Autorización Plan Accion",
            // //"correos" => array( "sestrada@matrix.com.mx")
            // "correos" => [ "software@matrix.com.mx" ]
            // ] );
        }
        return [ 'actualizado' => $this->modeloChecklist->autorizacionPlanAccion( $id , $status  ,$fecha , $observaciones)];
    }

    public function enviarReporte( $configCorreo)
    {
        extract( $configCorreo );
        $emailsender = new phpmailer;
        $emailsender->isSMTP();
        $emailsender->SMTPDebug = 1;
        $emailsender->SMTPAuth = true;
        $emailsender->Port = 587;

        $emailsender->Host = 'mail.matrix.com.mx';
        $emailsender->Username = "no-responder@matrix.com.mx";
        $emailsender->Password = "M@tr1x2017";

        $emailsender->From ="no-responder@matrix.com.mx";
        $emailsender->FromName = $descripcionDestinatario;

        $emailsender->Subject ="$subject";
        $emailsender->Body = "<p>$mensaje</p>";

        $emailsender->AltBody = "...";

        if ( is_file($pathFile) ) {
            $emailsender->AddAttachment( $pathFile);
        }
        //sestrada
        foreach ($correos as $email) {
            $emailsender->AddAddress( $email );
        }
        /*$emailsender->AddAddress("luisimatrix@matrix.com");
		 $emailsender->AddAddress("sestrada@matrix.com.mx");
		 $emailsender->AddAddress("raulmatrixxx@hotmail.com");*/
		 //$emailsender->AddAddress("auxsistemas@hotmail.com");
		
        $statusEnvio = $emailsender->Send();

        if ( $emailsender->ErrorInfo == "SMTP Error: Data not accepted") {
            $statusEnvio = true;
        } 

        if ( !$statusEnvio ) {
            return "[".$emailsender->ErrorInfo."] - Problemas enviando correo electrónico a ";
        } else {
            return "Enviado";
        }
    }

}
