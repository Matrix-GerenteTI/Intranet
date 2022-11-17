<?php
ini_set('memory_limit', '-1');


class FirebirdDB
{
    protected  $conexionFireBird;

    public function __construct()
    {
        $host = "172.16.0.200:C:\Prediction\BDs\PREDICTION.FDB"; 
		$user="SYSDBA";
		$pass="masterkey";
		$this->conexionFireBird = @ibase_pconnect($host,$user,$pass) or die("Error al conectarse a la base de datos: ".ibase_errmsg());

    }

    public function fireSelect( $query )
    {
        $this->close();
        $this->__construct();


        
        $exeQuery = ibase_query( $this->conexionFireBird, $query);
        //Fetch results
        $datosObtenidos = array();
        while ( $retrievedData = ibase_fetch_object($exeQuery) ) {
        
            array_push( $datosObtenidos, $retrievedData );
            $retrievedData= null;
        }


        return $datosObtenidos;
    }

    public function selectValuado( $query  )
    {

        
        $exeQuery = ibase_query( $this->conexionFireBird, $query);
        //Fetch results
        $datosObtenidos = array();
        $codAnt = '';
        $diasInv = 0;

        while ( $retrievedData = ibase_fetch_object($exeQuery) ) {

            if ( $codAnt != $retrievedData->CODIGOART ) {
                $codAnt = $retrievedData->CODIGOART;
                $diasInv =  Articulos::getUltimaCompra( $retrievedData->CODIGOART ) ;
                $diasInv = sizeof( $diasInv ) > 0 ? $diasInv : 0;
                $retrievedData->DIASINV = $diasInv;
                
            } else {
               
                $retrievedData->DIASINV = $diasInv;
            }

            $retrievedData->CODIGOART = mb_convert_encoding( $retrievedData->CODIGOART, 'UTF-8' );
            $retrievedData->DESCRIP = mb_convert_encoding( $retrievedData->DESCRIP, 'UTF-8' );
            $retrievedData->FAM = mb_convert_encoding( $retrievedData->FAM, 'UTF-8' );
            $retrievedData->SUBFAMILIA = mb_convert_encoding( $retrievedData->SUBFAMILIA, 'UTF-8' );

            array_push( $datosObtenidos, $retrievedData );
            var_dump( $datosObtenidos );
            exit();
        }
        return $datosObtenidos;
    }

    public function insert( $query )
    {
        $exeQuery = ibase_query( $this->conexionFireBird , $query);

        ibase_commit( $this->conexionFireBird);
        return $exeQuery;
    }

    public function close()
    {
        ibase_close($this->conexionFireBird);
    }

    public function getIdsFromContador( $tabla)
    {
        $querySetNextId = "UPDATE CONTADOR SET ID=ID+1 WHERE TABLA='$tabla'  ";

        $exeQuery = ibase_query( $this->conexionFireBird , $querySetNextId);

        $queryIdContador = "SELECT * FROM CONTADOR WHERE TABLA='$tabla' ";
            
        $exeIdContador = ibase_query($this->conexionFireBird, $queryIdContador);
        $registroRecuperado = ibase_fetch_object( $exeIdContador );

        return $registroRecuperado->ID;

    }
}
