<?php

class Vendedores
{
    private $conexionIbase ;

	public function __construct()
	{
		$host = "172.16.0.70:/var/lib/firebird/3.0/data/PREDICTION.FDB";
		$user="SYSDBA";
		$pass="masterkey";
		$this->conexionIbase = @ibase_pconnect($host,$user,$pass) or die("Error al conectarse a la base de datos: ".ibase_errmsg());
		return $this->conexionIbase;
    }
    
    public function ventasPorVendedor($fecha)
    {
        $ventas = array();
        $splitFecha = explode('-',$fecha);
        $mes = $splitFecha[1];
        $anio = $splitFecha[0];
        $queryVentas = " SELECT 
                                    IMPORTELINEA,DETIVA, FAMILIA,
                                    SUBFAMILIA, NOMBREVENDEDOR,sum(cantidad) as vendido
                                FROM VENTAS r
                                where extract(year from fecha) = $anio and extract(month from fecha) = '$mes'
                                group by importelinea,detiva,familia,subfamilia,nombrevendedor
                                order by vendido desc";
            $exeVentas = ibase_query( $this->conexionIbase ,$queryVentas);
               
            $ventas = $this->saveRetrievedRows($exeVentas);

            return $ventas;
    }

    public function saveRetrievedRows($executedQuery)
    {
        $registros = array();
        while ( $registro = ibase_fetch_object($executedQuery)) {
            $registro->NOMBREVENDEDOR = utf8_encode($registro->NOMBREVENDEDOR);
            $registro->SUBFAMILIA = utf8_encode($registro->SUBFAMILIA );
            $registro->FAMILIA = utf8_encode($registro->FAMILIA );
            array_push($registros,$registro);
        }
        return $registros;
    }
}