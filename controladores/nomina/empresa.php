<?php

require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/Empresa/Empresa.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/Clases/sucursales.php";

class EmpresaController  
{
    public function getAllDepartamentos()
    {
        $modeloEmpresa = new Empresa;
        return $modeloEmpresa->getAllDepartamentos();
    }
    
    public function getAllSucursales()
    {
        $modeloSucursal = new Sucursales;

        return $modeloSucursal->getSucursales();
    }

    public function getAllPuestos()
    {
        $modeloEmpresa = new Empresa;

         $listaPuestos = $modeloEmpresa->getAllPuestos();
            foreach ( $listaPuestos as $i => $puesto) {
                $listaPuestos[$i]['name'] = mb_convert_encoding($puesto['descripcion'] , "UTF-8" ) ;
            }
         return $listaPuestos;
    }
}

if ( isset($_GET['opc'] ) ) {
    switch ($_GET['opc'] ) {
        case 'getAllDepartamentos':
                echo json_encode( EmpresaController::getAllDepartamentos() );
            break;
        case 'getAllSucursales':
                echo json_encode( EmpresaController::getAllSucursales() );
            break;
        case 'getAllPuestos':
            echo json_encode( EmpresaController::getAllPuestos() );
            break;
        default:
            
            break;
    }
}
