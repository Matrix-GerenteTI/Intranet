<?php
error_reporting(E_ALL ^ E_WARNING); 

require_once $_SERVER['DOCUMENT_ROOT']."/intranet/lib/tcpdf/tcpdf.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/lib/tcpdf/tcpdi.php";
require_once $_SERVER['DOCUMENT_ROOT'].'/intranet/modelos/nomina/trabajadores.php';


class SolicitudDePermiso    
{
    protected $documento;

    public function __construct()
    {
        $this->documento  = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $this->documento->SetCreator(PDF_CREATOR);
        $this->documento->SetAuthor('Olaf Lederer');
        $this->documento->SetTitle('TCPDF Example');
        $this->documento->SetSubject('TCPDF Tutorial');
        $this->documento->SetKeywords('TCPDF, PDF, Permisos, tutorial');

    }


    public function getDatosAccionCorrectiva( $permisoId )
    {
        $modeloTrabajador = new Trabajador;
        $infoAccionCorrectiva = $modeloTrabajador->getDatosAccionCorrectiva( $permisoId );
        if ( sizeof( $infoAccionCorrectiva) ) {
            return $infoAccionCorrectiva[0];
        }
        return [];
    }

    public function generaSolicitud( $permisoId )
    {
        $this->documento->AddPage(); 
        $this->documento->setPrintHeader(false); 

        $accionCorrectiva = $this->getDatosAccionCorrectiva( $permisoId );
        if ( empty($accionCorrectiva) ) {
            
            exit(-1);
        }
        $x = "";
        $empty = '';
        $header = ' <div style="margin-top:50px;">
                                    <table>
                                        <tr>
                                            <td><img src="http://servermatrixxxb.ddns.net:8181/intranet/assets/images/logo2.png" style="width:200px;height:auto"></td>
                                            <td></td>
                                            <td><img src="http://servermatrixxxb.ddns.net:8181/intranet/assets/images/logoleon.png" style="width:100px;height:auto"> </td>
                                        </tr>
                                        <tr>
                                            <td><br></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" style="text-align:center"> <b style="text-align:center;font-size:0.9em">ACCIÓN CORRECTIVA</b> </td>
                                        </tr>';

        $header .= $accionCorrectiva['consecutivo'] != null ? '<tr>
                                                                                                            <td></td>
                                                                                                            <td></td>
                                                                                                            <td>Acción correctiva No.:<b>'.$accionCorrectiva['consecutivo'].' de 4</b></td>
                                                                                                    </tr>' : '';
        $header .=       '</table>
                                    
                            </div>';        
        
        $this->documento->writeHTML( $header, true, false, false, false, '');

        $monto = '<br><br><div style="font-size:0.9em;margin-top:-120px;">
                                <table>
                                    <tr>   
                                        <th>
                                            <table>
                                                <tr>    
                                                    <td style="font-size:1.1em;background-color: #f44336;color: #fff; height:15;">Nombre del empleado</td>
                                                    <td></td>
                                                </tr>
                                            </table>
                                        </th>
                                        <th>
                                           <table>
                                                <tr>    
                                                    <td style="font-size:1.1em;background-color: #f44336;color: #fff; height:15;">Fecha</td>
                                                    <td></td>
                                                </tr>
                                            </table>                                        
                                        </th>
                                    </tr>
                                    <tr>
                                        <td >
                                            <table>
                                                <tr>
                                                    <td colspan="6" style="padding:3px;font-size:0.9em;background-color: #eceff1;color: #000; height:15;">'.$accionCorrectiva['nombre'].'</td>
                                                    <td></td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td> 
                                            <table>
                                                        <tr>
                                                            <td colspan="6" style="padding:3px;font-size:0.9em;background-color: #eceff1;color: #000; height:15;">'.$accionCorrectiva['fecha'].'</td>
                                                            <td></td>
                                                        </tr>
                                                </table>     
                                                <br>                                   
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                           <table>
                                                <tr>    
                                                    <td style="font-size:1.1em;background-color: #f44336;color: #fff; height:15;">Puesto</td>
                                                    <td></td>
                                                </tr>
                                            </table>                                        
                                        </th>
                                        <th>
                                           <table>
                                                <tr>    
                                                    <td style="font-size:1.1em;background-color: #f44336;color: #fff; height:15;">Sucursal</td>
                                                    <td></td>
                                                </tr>
                                            </table>                                        
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>
                                            <table>
                                                        <tr>
                                                            <td colspan="6" style="padding:3px;font-size:0.9em;background-color: #eceff1;color: #000; height:15;">'.$accionCorrectiva['puesto'].'</td>
                                                            <td></td>
                                                        </tr>
                                                </table>                                           
                                        </th>
                                        <th>
                                            <table>
                                                        <tr>
                                                            <td colspan="6" style="padding:3px;font-size:0.9em;background-color: #eceff1;color: #000; height:15;">'.$accionCorrectiva['sucursal'].'</td>
                                                            <td></td>
                                                        </tr>
                                                </table>       
                                                <br>                                    
                                        </th>
                                    </tr>
                                    <tr>
                                        <th colspan="2">  
                                                        <table>
                                                                <tr>    
                                                                    <td style="font-size:1.1em;background-color: #f44336;color: #fff; height:15;">Causa de la acción correctiva</td>
                                                                    <td></td>
                                                                </tr>
                                                            </table>                                                                                   
                                        </th>
                                    </tr>
                                    <tr>
                                        <td colspan= "2">
                                            <table>
                                                <tr>
                                                    <td style="padding:3px;font-size:0.9em;background-color: #eceff1;color: #000; height:15;">'.
                                                        $accionCorrectiva['motivo']
                                                   . '<br><br><br><br></td>
                                                </tr>
                                            </table>                                              
                                        </td>                                
                                    </tr>
                                    
                                    <tr>
                                        <th colspan="2">  
                                                        <table>
                                                                <tr>    
                                                                    <td style="font-size:1.1em;background-color: #f44336;color: #fff; height:15;">Plan de acción</td>
                                                                    <td></td>
                                                                </tr>
                                                            </table>                                                                                   
                                        </th>
                                    </tr>
                                    <tr>
                                        <td colspan= "2">
                                            <table>
                                                <tr>
                                                    <td style="padding:3px;font-size:0.9em;background-color: #eceff1;color: #000; height:15;">'.
                                                        ($accionCorrectiva['monto'] > 0 ? $accionCorrectiva['plan_accion'] : $accionCorrectiva['plan_accion'] ." (ACCIÓN CORRECTIVA SIN CARGO AL TRABAJADOR,".$accionCorrectiva['consecutivo']." DE 4 PARA SANCIÓN ENCONÓMICA )" ) 
                                                        
                                                   . '<br><br><br><br></td>
                                                </tr>
                                            </table>                                              
                                        </td>                                
                                    </tr>                                    

                                    <tr>
                                        <td><br></td>
                                    </tr>
                                    <tr>    
                                        <td colspan="2" style="font-size:1.1em;background-color: #000;color: #fff; height:18;text-align:center;"> Marque con X si aplica sanción economica</td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <table>
                                                <tr>
                                                    <th colspan="5" style="font-size:1.1em;background-color: #f44336;color: #fff; height:15;">Si aplica</th>
                                                    <td style="padding:3px;font-size:0.9em;background-color: #eceff1;color: #000; height:15;text-align:center">'.($accionCorrectiva['monto'] > 0 ? "X" : "")  .'</td>
                                                </tr>
                                            </table>
                                        </th>
                                        <th>
                                            <table>
                                                <tr>
                                                    <th colspan="5" style="font-size:1.1em;background-color: #f44336;color: #fff; height:15;">No aplica</th>
                                                    <td style="padding:3px;font-size:0.9em;background-color: #eceff1;color: #000; height:15;text-align:center">'.($accionCorrectiva['monto'] <= 0 ? "X" : "") .'</td>
                                                </tr>
                                            </table>    
                                            <br>                                    
                                        </th>
                                    </tr>
                                    <tr>
                                        <th colspan="1">
                                            <table>
                                                <tr>
                                                    <th colspan="3" style="font-size:1.1em;height:15;">Inidicar el monto</th>
                                                    <td style="padding:3px;font-size:0.9em;background-color: #eceff1;color: #000; height:15;text-align:center">'.($accionCorrectiva['monto'] <= 0 ? " - " : $accionCorrectiva['monto']  ).'</td>
                                                </tr>
                                            </table>
                                        </th>
                                        <th colspan="2">
                                            <table>
                                                <tr>
                                                    <th colspan="2" style="font-size:1.1em;height:15;">Fecha de aplicación de descuento:</th>
                                                    <td style="padding:3px;font-size:0.9em;background-color: #eceff1;color: #000; height:15;text-align:center">'.($accionCorrectiva['monto'] <= 0 ? $accionCorrectiva['consecutivo']."/4" : $accionCorrectiva['fecha_descuento']  )  .'</td>
                                                </tr>
                                            </table>    
                                            <br>                                    
                                        </th>
                                    </tr>                                    
                                </table>
                            </div>';
        $this->documento->writeHTML( $monto, true, false, false, false, '');

        $this->documento->Ln(80);    

        $firmas = '
                            <div>
                                <table> 
                                    <tr>
                                        <td>
                                            <table>
                                                <tr>
                                                    <td></td>
                                                    <td colspan="8" style="border-top:1px solid black;text-align:center">Empleado</td>
                                                    <td></td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td>
                                            <table>
                                                <tr>
                                                    <td></td>
                                                    <td colspan="8" style="border-top:1px solid black;text-align:center">Jefe inmediato</td>
                                                    <td></td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td>
                                            <table>
                                                <tr>
                                                    <td></td>
                                                    <td colspan="8" style="border-top:1px solid black;text-align:center">Área administrativa</td>
                                                    <td></td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </div>'
                        ;
        $this->documento->writeHTML( $firmas, true, false, false, false, '');

        $name = strtotime( date("Y-m-d H:i:s") );
        $this->documento->Output($_SERVER['DOCUMENT_ROOT'].'/intranet/controladores/reportes/egresos/accion_'.$name.'.pdf', 'F');
        echo "http://servermatrixxxb.ddns.net:8181/intranet/controladores/reportes/egresos/accion_$name.pdf ";
    }
}

$solicitud = new SolicitudDePermiso;
$solicitud->generaSolicitud( $_POST['accion']);
 