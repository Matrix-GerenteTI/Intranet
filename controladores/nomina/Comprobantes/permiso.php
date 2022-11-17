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


    public function getDataPermiso( $permisoId )
    {
        $modeloTrabajador = new Trabajador;
        $infoPermiso = $modeloTrabajador->getDatosDePermiso( $permisoId );
        if ( sizeof( $infoPermiso) ) {
            return $infoPermiso[0];
        }
        return [];
    }

    public function generaSolicitud( $permisoId )
    {
        $this->documento->AddPage(); 
        $this->documento->setPrintHeader(false); 

        $permiso = $this->getDataPermiso( $permisoId );
        if ( empty($permiso) ) {
            
            exit(-1);
        }
        $x = "";
        $empty = '';
      $header = '<div style="width:100%;text-align: center;margin-top:50px;">
                                                <img src="http://servermatrixxxb.ddns.net:8181/intranet/assets/images/logo.png" style="margin: auto;">
                                                <br>
                                                <b style="text-align:center;font-size:0.9em">SOLICITUD DE PERMISO LABORAL</b>
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
                                                    <td colspan="6" style="padding:3px;font-size:0.9em;background-color: #eceff1;color: #000; height:15;">'.$permiso['nombre'].'</td>
                                                    <td></td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td> 
                                            <table>
                                                        <tr>
                                                            <td colspan="6" style="padding:3px;font-size:0.9em;background-color: #eceff1;color: #000; height:15;">'.$permiso['fecha'].'</td>
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
                                                            <td colspan="6" style="padding:3px;font-size:0.9em;background-color: #eceff1;color: #000; height:15;">'.$permiso['puesto'].'</td>
                                                            <td></td>
                                                        </tr>
                                                </table>                                           
                                        </th>
                                        <th>
                                            <table>
                                                        <tr>
                                                            <td colspan="6" style="padding:3px;font-size:0.9em;background-color: #eceff1;color: #000; height:15;">'.$permiso['sucursal'].'</td>
                                                            <td></td>
                                                        </tr>
                                                </table>       
                                                <br>                                    
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>
                                            <table>
                                                    <tr>
                                                        <td colspan="6" style="font-size:1.1em;background-color: #f44336;color: #fff; height:15;">
                                                                Días de permiso
                                                        </td>
                                                        <th style="padding:3px;font-size:0.9em;background-color: #eceff1;color: #000; height:15;text-align:center">'.$permiso['dias'].' día(s)</th>
                                                    </tr>
                                                </table>                                        
                                        </th>
                                        <th>
                                        </th>
                                    </tr>
                                    <tr>
                                        <td><br></td>
                                    </tr>
                                    <tr>    
                                        <td colspan="2" style="font-size:1.1em;background-color: #000;color: #fff; height:18;text-align:center;"> Marque con X: </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <table>
                                                <tr>
                                                    <th colspan="5" style="font-size:1.1em;background-color: #f44336;color: #fff; height:15;">Con goce de sueldo</th>
                                                    <td style="padding:3px;font-size:0.9em;background-color: #eceff1;color: #000; height:15;text-align:center">'.($permiso['goce_sueldo'] == 1 ? "X" : "")  .'</td>
                                                </tr>
                                            </table>
                                        </th>
                                        <th>
                                            <table>
                                                <tr>
                                                    <th colspan="5" style="font-size:1.1em;background-color: #f44336;color: #fff; height:15;">Sin goce de sueldo</th>
                                                    <td style="padding:3px;font-size:0.9em;background-color: #eceff1;color: #000; height:15;text-align:center">'.($permiso['goce_sueldo'] == 0 ? "X" : "") .'</td>
                                                </tr>
                                            </table>    
                                            <br>                                    
                                        </th>
                                    </tr>
                                    <tr>
                                        <th colspan="2">  
                                                        <table>
                                                                <tr>    
                                                                    <td style="font-size:1.1em;background-color: #f44336;color: #fff; height:15;">Motivo</td>
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
                                                        $permiso['motivo']
                                                   . '<br><br><br><br></td>
                                                </tr>
                                            </table>                                              
                                        </td>                                
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
                                                    <td colspan="8" style="border-top:1px solid black;text-align:center">Gerente comercial</td>
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
        $this->documento->Output($_SERVER['DOCUMENT_ROOT'].'/intranet/controladores/reportes/egresos/permiso_'.$name.'.pdf', 'F');
        echo "http://servermatrixxxb.ddns.net:8181/intranet/controladores/reportes/egresos/permiso_$name.pdf";
    }
}

$solicitud = new SolicitudDePermiso;
$solicitud->generaSolicitud( $_POST['permiso']);
 