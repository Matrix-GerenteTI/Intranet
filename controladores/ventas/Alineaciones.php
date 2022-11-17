<?php

require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/Ventas/ventas.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/Almacenes/Almacen.php";


class Alineaciones  
{
    protected $modeloVentas;
    protected $modeloAlmacen;

    public function __construct() {
        $this->modeloAlmacen = new Almacen;
        $this->modeloVentas = new Ventas;

    }

    public function getAlineacionesVentas()
    {
        //Arreglo que contiene la relacion entre alineaciones en ventas vs sensores
        $listaVsAlineaciones = [];

                //OBTENIENDO LAS IDS DE LOS SUCURSALES QUE TIENEN UN SENSOR
                $idsSucursales = $this->modeloVentas->getSucursalesConSensores();
                foreach( $idsSucursales as $sucursal){
                    $listaAlineacionesVentas = $this->modeloVentas->getTodasAlineaciones( $sucursal['idsucursal'] );

                    $listaVsAlineaciones[ $sucursal['idsucursal'] ]= $listaAlineacionesVentas;
                    
                    // continue
                }
               
        return $listaVsAlineaciones;
    }
    

    public function getAlineacionesSensor()
    {
        //Arreglo que contiene la relacion entre alineaciones en ventas vs sensores
        $listaVsAlineaciones = [];

                //OBTENIENDO LAS IDS DE LOS SUCURSALES QUE TIENEN UN SENSOR
                $idsSucursales = $this->modeloVentas->getSucursalesConSensores();
                foreach( $idsSucursales as $sucursal){
                    $listaAlineacionesSensor = $this->modeloVentas->getTodasAlineacionesPorSensores( $sucursal['idsucursal'] );

                    $listaVsAlineaciones[ $sucursal['idsucursal'] ]= $listaAlineacionesSensor;
                    // continue
                }
               
        return $listaVsAlineaciones;
    }


}


