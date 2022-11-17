<?php 

require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/Apps/agenda/agenda.php";
require_once ($_SERVER['DOCUMENT_ROOT']."/intranet/vendor/autoload.php");

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use  Kreait\Firebase\Messaging\Notification;
use  Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\AndroidConfig;

class AgendaMiddleware  
{
    protected $modeloAgendaApp;

    public function __construct()
    {
        $this->modeloAgendaApp = new AgendaApp;
    
    }
    public function registraDispositivo( $query )
    {
        
        return $this->modeloAgendaApp->registraToken( $query );

    }

    public function enviaRecordatorio()
    {

        $serviceAccount = ServiceAccount::fromJsonFile( dirname(__DIR__)."/agenda/agenda-multiuso-firebase-adminsdk-ewdm4-dc81c3b533.json");
            
 
        $firebase = ( new Factory )
                ->withServiceAccount( $serviceAccount )
                ->create();

        $notificacionHandler = $firebase->getMessaging();  

        $dispositivosConToken = $this->modeloAgendaApp->getToken();

        //recorriendo el listado de equipos con los que puede compartir
        foreach ( $dispositivosConToken as $i => $dispositivo) {
            //obteniendo los tokens de los dispositivos receptores
            $dispositivosReceptores = $this->modeloAgendaApp->tokenReceptor( $dispositivo['dispositivoReceptor']);
            //obtenemos los agendados  que tengan hasta dos días de anticipacion
            $listaARecordar = $this->modeloAgendaApp->eventosANotificar( date("Y-m-d") );
            foreach ($listaARecordar as $j => $evento) {
                //listando los dispositivos a los que se le va a mandar la notificacion
                
                foreach ( $dispositivosReceptores as $k => $receptor) {
                    //vamos a obtener los usuarios que tienen un token y  los usuarios 
                    $config = AndroidConfig::fromArray([
                        'ttl' => '3600s',
                        'priority' => 'high',
                        'data' => [
                            'saluda' => "hola"
                        ],
                        'notification' => [
                            'channel_id' => "agenda_notificacion",
                            'title' => "RECORDATORIO DE: ".$evento['concepto'],
                            'body' => "Tienes ".$evento['retraso']." día(s) para realizarlo ",
                            'color' => '#f45342',
                            "sound" => "notification",
                            //'click_action' => 'BUZON_PEDIDOS_NOTIFICATION',
                            'tag' => $evento['idevt']
                        ],
                    ]); 


                    try {
                        $contentNotificacion = CloudMessage::withTarget('token', $receptor['token'])
                            ->withAndroidConfig( $config );
                        $notificacionHandler->send( $contentNotificacion);       
                    // var_dump( $notificacionHandler );
                    } catch (Firebase\Auth\Token\Exception\IIssuedInTheFuture $e) {
                        
                    }catch( Firebase\Auth\Token\Exception\IInvalidIdToken $e){

                    }catch( Firebase\Auth\Token\Exception\IMessagingException  $e){

                    }catch(Firebase\Auth\Token\Exception\InvalidMessage $e){
                            
                    }                    
                }

                //Se le manda notificacion al creador del evento
                $config = AndroidConfig::fromArray([
                    'ttl' => '3600s',
                    'priority' => 'high',
                    'data' => [
                        'saluda' => "hola"
                    ],
                    'notification' => [
                        // 'android_channel_id' => "notificacionesOreo",
                        'title' => "RECORDATORIO DE: ".$evento['concepto'],
                        'body' => "Tienes ".$evento['retraso']." día(s) para realizarlo ",
                        'color' => '#f45342',
                        "sound" => "notification",
                        //'click_action' => 'BUZON_PEDIDOS_NOTIFICATION',
                        'tag' => $evento['idevt']
                    ],
                ]); 


                try {
                    $contentNotificacion = CloudMessage::withTarget('token', $dispositivo['token'])
                        ->withAndroidConfig( $config );
                    $notificacionHandler->send( $contentNotificacion);       
                // var_dump( $notificacionHandler );
                } catch (Firebase\Auth\Token\Exception\IIssuedInTheFuture $e) {
                    
                }catch( Firebase\Auth\Token\Exception\IInvalidIdToken $e){

                }catch( Firebase\Auth\Token\Exception\IMessagingException  $e){

                }catch(Firebase\Auth\Token\Exception\InvalidMessage $e){
                        
                }                    
            }
                       
        }
        
    }
}
