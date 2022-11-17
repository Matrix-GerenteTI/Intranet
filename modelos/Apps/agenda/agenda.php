<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/intranet/modelos/DB.php';


class AgendaApp extends DB 
{
    
    public function registraToken( $query )
    {
        return $this->insert( $query );
    }

    public function getToken( $dispositivoId = "%")
    {
        $queryToken = "SELECT disp.dispositivoId, disp.token, demisor.dispositivoReceptor
                                        FROM cal_dispositivos_app AS disp
                                        LEFT JOIN dipositivos_compartir_eventos_app AS demisor ON demisor.dispositivoEmisor = disp.dispositivoId
                                        WHERE disp.dispositivoId like '$dispositivoId' 
                                        GROUP BY disp.dispositivoId" ;
        return $this->select( $queryToken )                                        ;
    }

    public function tokenReceptor( $dispositivoId )
    {
        $queryToken = "SELECT disp.token, dreceptor.dispositivoReceptor
                                            FROM cal_dispositivos_app AS disp
                                            INNER jOIN dipositivos_compartir_eventos_app AS dreceptor ON dreceptor.dispositivoReceptor= disp.dispositivoId
                                            WHERE dispdispositivoId = '$dispositivoId'
                                            GROUP BY disp.dispositivoId";
        return $this->select( $queryToken );
    }

    public function eventosANotificar( $fecha )
    {
        $queryDispersion = "SELECT * ,DATEDIFF(evt.fecha_evento,'$fecha')  as retraso,evt.id AS idevt
                                            FROM cal_pagos_app AS evt
                                            INNER JOIN cal_dispositivos_app AS disp ON disp.dispositivoId = evt.dispositivo
                                            WHERE DATEDIFF(evt.fecha_evento,'$fecha')  >= -2 AND DATEDIFF(evt.fecha_evento,'$fecha') <= 7 AND evt.`status`= 0";
        return $this->select( $queryDispersion );

    }
}
