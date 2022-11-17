<?php
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/Contabilidad/Egresos.php";

class ProgramacionPagosController  
{
    
    protected $modeloEgresos ;

    public function __construct()
    {
        $this->modeloEgresos = new Egresos;
    }
    
    public function getAll(  $anio= '%' )
    {
        $listaProgramada = $this->modeloEgresos->getProgramacionPagos( $anio );
        //decodificando a utf8 el texto de los conceptos y beneficiarios
        $eventParse = [];
        foreach ( $listaProgramada as $i => $actProgramada ) {
            $listaProgramada[$i]['concepto'] = utf8_encode( $actProgramada['concepto'] );
            $listaProgramada[$i]['beneficiario'] = utf8_encode( $actProgramada['beneficiario'] );
            array_push( $eventParse , ["eventName" =>  $listaProgramada[$i]['concepto'] , 'beneficiario' => $listaProgramada[$i]['beneficiario'],'monto' =>$actProgramada['monto'] , 'date' => $actProgramada['fecha_evento'],'calendar' => 'Wok' ,'color' => 'orange'] );
        }

        return $eventParse;
    }
}
