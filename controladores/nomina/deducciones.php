<?php

require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/nomina/incidencias.php";

class DeduccionesController 
{
    
    protected $modeloIncidencia;

    public function __construct()
    {
        $this->modeloIncidencia = new Incidencias;
    }

    public function getCatalogo()
    {
        return $this->modeloIncidencia->getCatalogoDeducciones();
    }

    public function getTipoIncidencia()
    {
        return $this->modeloIncidencia->getTipoIncidencia();
    }

    public function getIncidencias($tipoIndcidencia)
    {
        return $this->modeloIncidencia->getIncidencias($tipoIndcidencia);
    }

    public function delIncidencia($id)
    {
        return $this->modeloIncidencia->delIncidencia($id);
    }

    public function addIncidencia($idempleado,$idtipoincidencia,$idincidencia,$fechaaplicacion,$fechadescuento,$monto,$numpagos,$observaciones)
    {
        return $this->modeloIncidencia->addIncidencia($idempleado,$idtipoincidencia,$idincidencia,$fechaaplicacion,$fechadescuento,$monto,$numpagos,$observaciones);
    }

    public function autIncidencia($id,$monto,$numpagos,$sms)
    {
        extract($params);
        $result = $this->modeloIncidencia->autIncidencia($id,$monto,$numpagos);
        //die($id.','.$monto.','.$numpagos);
        if(is_array($result)){
            if($sms==1){
                $info = $result[0];
                $nombre = explode(' ',$info['nombre']);
                $this->sendSMS($info['celular'],'[MATRIXNOMINA] '.$nombre[0].' se le aplico una '.$info['tipoincidencia'].' por '.substr($info['incidencia'],0,40).'. Inf. al 9613590650');
            }
            return 'OK';
        }else{
            return $result;
        }
        //$this->sendSMS($numero,$mensaje)
        //return $res
    }

    public function getNomina($nivelUsuario)
    {
        $arrReturn = array();
        $arrchck = array();
        $arrNomina = $this->modeloIncidencia->getNomina($nivelUsuario);
        foreach($arrNomina as $val){
            if(!in_array($val['nip'],$arrchck)){
                $arrchck[] = $val['nip'];
                $arrReturn[$val['nip']] = array('nip'=>$val['nip'],'nombre'=>$val['empleado'],'sucursal'=>$val['sucursal'],'quincena'=>$val['quincena'],'totdeducciones'=>0,'totpercepciones'=>0,'tototal'=>0,'incidencias'=>array());
                $arrReturn[$val['nip']]['incidencias'][] = array('id'=>$val['id'],'idtipoincidencia'=>$val['idtipoincidencia'],'incidencia'=>utf8_encode($val['incidencia']),'monto'=>$val['monto'],'status'=>$val['statusincidencia'],'numpagos'=>$val['numpagos'],'observaciones'=>utf8_encode($val['observaciones']));
            }else{
                $arrReturn[$val['nip']]['incidencias'][] = array('id'=>$val['id'],'idtipoincidencia'=>$val['idtipoincidencia'],'incidencia'=>utf8_encode($val['incidencia']),'monto'=>$val['monto'],'status'=>$val['statusincidencia'],'numpagos'=>$val['numpagos'],'observaciones'=>utf8_encode($val['observaciones']));
            }
        }

        foreach($arrReturn as $key => $row){
            $totpercepciones = 0;
            $totdeducciones = 0;
            foreach($row as $idx => $item){
                if($idx == 'incidencias'){
                    foreach($item as $incidencia){
                        $incidencia['idtipoincidencia']==1?$totdeducciones+=$incidencia['monto']:$totpercepciones+=$incidencia['monto'];
                        //var_dump($incidencia);
                        
                    }
                }
            }
            $arrReturn[$key]['totdeducciones'] = $totdeducciones;
            $arrReturn[$key]['totpercepciones'] = $totpercepciones;
            $arrReturn[$key]['tototal'] = $row['quincena'] - $totdeducciones + $totpercepciones;
        }

        foreach ($arrReturn as $key => $row) {
            $aux[$key] = $row['nombre'];
        }
        array_multisort($aux, SORT_ASC, $arrReturn);

        return $arrReturn;
    }

    public function sendSMS($numero,$mensaje){
        $params = array(
            "message" => $mensaje,
            "numbers" => $numero,
            "country_code" => 52
          );
          $headers = array(
            "apikey: 924d006378fd5bea41bf43618b5ef2a81d095661"
          );
          curl_setopt_array($ch = curl_init(), array(
            CURLOPT_URL => "https://api.smsmasivos.com.mx/sms/send",
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_HEADER => 0,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => http_build_query($params),
            CURLOPT_RETURNTRANSFER => 1
          ));
          $response = curl_exec($ch);
          curl_close($ch);
          
          return json_encode($response);
    }

}
