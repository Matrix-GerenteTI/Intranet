<?php
date_default_timezone_set("America/Mexico_City");

if(!isset($_SESSION)){ 
	session_start(); 
}
 require_once dirname(__DIR__)."/modelos/MySQL.php";

class SaldosBancarios extends CMySQLi
{
    public function getFiliaciones($sucursal = '%')
    {
        $condicion = " id like '%' ";
        if ($sucursal != '%') {
            $condicion ="id ='$sucursal' or filiacion= $sucursal";
        }
        $queryFiliaciones = "SELECT id,filiacion,descripcion FROM csucursal WHERE $condicion AND filiacion is not null";
        
        $exeFiliaciones = $this->db->query( $queryFiliaciones);
        return $exeFiliaciones->fetch_all(MYSQLI_ASSOC);
    }

    public function getAfiliacionesTerminal_sucursal( $cuentaId )
    {

        $queryFiliaciones = "SELECT *
                                            FROM csucursal 
                                            INNER JOIN pafiliaciones on pafiliaciones.sucursal = csucursal.id
                                            WHERE pafiliaciones.banco = $cuentaId";
        
        $exeFiliaciones = $this->db->query( $queryFiliaciones);
        return $exeFiliaciones->fetch_all(MYSQLI_ASSOC);
    }

    public function getEgresoPorFilicacion( $filiacion, $fechaInicio, $fechaFin)
    {
        $queryEgreso = "SELECT  sum(egresos) as egresos from saldos_bancarios where fecha between '$fechaInicio' and '$fechaFin' and beneficiario like '%$filiacion%' and egresos > 0";
        
        $exeEgreso = $this->db->query($queryEgreso);
        return $exeEgreso->fetch_all(MYSQLI_ASSOC);
    }

    public function getMovimientoSimilares( $parametros )
    {
        extract( $parametros );
        $queryMovimientosSimilares = "SELECT * FROM saldos_bancarios 
                            WHERE fecha ='$fecha' AND egresos = '$egresos' AND ingresos ='$ingresos' AND  saldoMovimiento = '$saldo' 
                                        AND cuenta_bancaria_id = '$cuentaId'  "   ;

         $exeMovimientosSimilares = $this->db->query( $queryMovimientosSimilares);
         return $exeMovimientosSimilares->fetch_all(MYSQLI_ASSOC);
    }

    public function insertaSaldo( $parametros )
    {
        extract( $parametros );
            $queryInsertaSaldo = "INSERT INTO saldos_bancarios VALUES('','$fecha',$movimientoId,'$beneficiario','$referencia','$egresos','$ingresos','$saldo',$cuentaId,$sucursal,$tipoMov)";
                
        if ( ($ingresos !==  "" && $ingresos >= 0)  || ($egresos !== "" && $egresos >= 0)) {

            if( $saldo == '-'){
                $saldo = 0;
            }

            
            $queryInsertaSaldo = "INSERT INTO saldos_bancarios (fecha,movimiento_id,beneficiario,referencia,egresos,ingresos,saldoMovimiento,cuenta_bancaria_id)
            VALUES('$fecha',$movimientoId,'$beneficiario','$referencia','$egresos','$ingresos','$saldo',$cuentaId)";
        
            
                $exeInsertaSaldo = $this->insertDefined( $queryInsertaSaldo);

                $idMovimiento = $this->db->insert_id;

                if ($exeInsertaSaldo ) {
                    $this->actualizaTablaCuentasSaldos( $cuentaId, $saldo );
                    
                }
                
                // if ( $ingresos != "" && $ingresos > 0 && $exeInsertaSaldo === true ) {
                //     $this->actualizaSaldo( $idMovimiento, 'ingresos', $ingresos);
                // }elseif( $egresos != "" && $egresos > 0 && $exeInsertaSaldo === true){

                //     $this->actualizaSaldo( $idMovimiento, 'egresos', $egresos);
                // }
                // return $this->db->affected_rows;
                return $idMovimiento;
            }
            echo $queryInsertaSaldo;
            // echo $queryInsertaSaldo.'<br><br>';
            // echo $this->db->error." error <br>";
            return 0;
    }

    // public function verificaSaldoIngresadoBanamex($fecha, $cuenta)
    // {
    //     $queryMovtosBanamex = "SELECT  MAX(ID) FROM saldos_bancarios WHERE  fecha = '$fecha' and cuenta_bancaria_id = '$cuenta'  ";

    //     $exeMovtosBanamex = $this->db->query($queryMovtosBanamex);
    //     return $exeMovtosBanamex->fetch_all(MYSQLI_ASSOC);
    // }

    public function actualizarSaldoManual($id, $saldo)
    {
        $queryActualizaSaldo = "UPDATE cuentas_bancarias  set saldo = saldo+$saldo where id=$id";
        $exeActualiza = $this->db->query( $queryActualizaSaldo);
        return $this->db->affected_rows;
    }

    public function actualizaTablaCuentasSaldos($cuenta, $saldo)
    {
        $queryActualizaSaldo = "UPDATE cuentas_bancarias  set saldo = $saldo where id=$cuenta";
        
        $exeActualiza = $this->db->query( $queryActualizaSaldo);
        return $this->db->affected_rows;        
    }
    public function actualizaSaldo($cuentaId, $cuentaNominal , $importe)
    {
        $nuevoSaldo = 0;
        $movimientos = $this->getMovimientoById( $cuentaId);
        
        $saldo = $this->getCuentasSaldo( $movimientos[0]['cuenta_bancaria_id']);
            if ( $cuentaNominal == "egresos") {
                $nuevoSaldo = $saldo[0]['saldo'] - $importe;
            } else {
                $nuevoSaldo = $saldo[0]['saldo'] + $importe;
            }

            // echo $cuentaNominal;
            // echo  $saldo[0]['saldo'] ."       $importe <br>";
        $queryCambiaSaldo = "UPDATE cuentas_bancarias  SET saldo ='$nuevoSaldo' where id=".$saldo[0]['id'];
        // echo "$queryCambiaSaldo<br>";
        
        $exeCambio = $this->insertDefined( $queryCambiaSaldo);
         return $this->db->affected_rows;
    }

  
    public function getSaldosRegistrados()
    {

        $queryGetAllSaldos = "SELECT  saldos_bancarios.*, cuentas_bancarias.banco,
                                                CASE 
                                                WHEN saldos_bancarios.egresos > 0  THEN (cuentas_bancarias.saldo - saldos_bancarios.egresos) 
                                                ELSE  (cuentas_bancarias.saldo + saldos_bancarios.ingresos)
                                                END as saldo
        FROM saldos_bancarios 
        INNER JOIN cuentas_bancarias ON cuentas_bancarias.id = saldos_bancarios.cuenta_bancaria_id
        order by saldos_bancarios. id desc ";
        return $exeGetAllSaldos = $this->select( $queryGetAllSaldos);

    }

    public function getMovimientoBancosDiario( $mes)
    {
            $anio = date("Y");
            $queryVariable = "SET @saldo = 0";
            $exeVariable= $this->db->query( $queryVariable);
            $queryGetAllMovimiento = "SELECT  saldos_bancarios.fecha, saldos_bancarios.beneficiario,saldos_bancarios.referencia,saldos_bancarios.egresos,saldos_bancarios.ingresos,
                                                                    @saldo:=saldo, cuentas_bancarias.banco,
                                                                    CASE 
                                                                    WHEN saldos_bancarios.egresos > 0  THEN 
                                                                                        CASE WHEN @saldo > 0 then (@saldo - saldos_bancarios.egresos) 
                                                                                        else (cuentas_bancarias.saldo - saldos_bancarios.egresos)
                                                                                        END
                                                                    ELSE  
                                                                                        CASE WHEN @saldo > 0 THEN (@saldo + saldos_bancarios.ingresos)
                                                                                            ELSE (cuentas_bancarias.saldo + saldos_bancarios.ingresos)
                                                                                        END
                                                                    END as saldo
                            FROM saldos_bancarios 
                            INNER JOIN cuentas_bancarias ON cuentas_bancarias.id = saldos_bancarios.cuenta_bancaria_id
                            Where month(fecha) =  '$mes' and year(fecha) = $anio
                            order by saldos_bancarios. id,fecha desc";
         $exeGetAllSaldos = $this->db->query( $queryGetAllMovimiento);
         return $exeGetAllSaldos->fetch_all(MYSQLI_ASSOC);
    }
	
	public function getMovimientoBancosDiarioGroupBy( $mes)
    {
            $anio = date("Y");
            $queryVariable = "SET @saldo = 0";
            $exeVariable= $this->db->query( $queryVariable);
            $queryGetAllMovimiento = "SELECT  fecha, sum(egresos) as egresos, sum(ingresos) as ingresos
                            FROM saldos_bancarios 
                            Where month(fecha) =  '$mes' and year(fecha) = $anio
							GROUP BY fecha
                            order by fecha ASC";
         $exeGetAllSaldos = $this->db->query( $queryGetAllMovimiento);
         return $exeGetAllSaldos->fetch_all(MYSQLI_ASSOC);
    }

    public function ingresosEgresosDespuesDeFecha($mes)
    {
        $anio = date("Y");
        $queryEgresosIngresos = "SELECT sum(egresos) as egresos, sum(ingresos) as ingresos,cuentas_bancarias.banco
                                FROM saldos_bancarios
                                INNER JOIN cuentas_bancarias on cuentas_bancarias.id = saldos_bancarios.cuenta_bancaria_id
                                WHERE  month(fecha) > '$mes' and year(fecha) = $anio
                                GROUP BY saldos_bancarios.cuenta_bancaria_id";
        return $this->select( $queryEgresosIngresos);
    }

    public function ingresosEgresosMes( $mes)
    {
        $anio = date("Y");
        $queryEgresosIngresos = "SELECT sum(egresos) as egresos, sum(ingresos) as ingresos,cuentas_bancarias.banco
                                FROM saldos_bancarios
                                INNER JOIN cuentas_bancarias on cuentas_bancarias.id = saldos_bancarios.cuenta_bancaria_id
                                WHERE  month(fecha) = '$mes' and year(fecha) = $anio
                                GROUP BY saldos_bancarios.cuenta_bancaria_id";
        return $this->select( $queryEgresosIngresos);
    }


    public function setBeneficiario( $movimiento, $beneficiario , $sucursal)
    {
        $queryActualizaBeneficiario = "UPDATE saldos_bancarios SET beneficiario = '$beneficiario' , sucursal_id = '$sucursal'  where id = $movimiento";
        $exeActualizacion = $this->db->query($queryActualizaBeneficiario);
        return $this->db->affected_rows;
    }

    public function getSaldoCajaChica()
    {
                $queryEgresosIngresos = "SELECT saldo,cuentas_bancarias.banco
                                FROM cuentas_bancarias
                                WHERE id=6";
        return $this->selectU( $queryEgresosIngresos);
    }
    public function deleteSaldo( $id)
    {
        $movimiento = $this->getMovimientoById( $id );
        $cuentaNominal = "egresos";
        $importe;
        // if ($movimiento[0]['egresos'] > 0) { //al elminiar  el movimiento hay que volver a tener el control del importe modificado en el saldo, por ende si era ingreso pasará a ser egreso y si era egreso será ingreso
        //     $cuentaNominal = "ingresos";
        //     $importe = $movimiento[0]['egresos'];
        // }else{
        //     $importe = $movimiento[0]['ingresos'];
        // }
        // $this->actualizaSaldo( $id  , $cuentaNominal, $importe);

        $queryDeleteSaldo = "DELETE FROM saldos_bancarios WHERE  id=$id ";
         $this->insertDefined( $queryDeleteSaldo);
         return $this->db->affected_rows;
    }

    public function getAllcuentas()
    {
        $queryGetAll = "SELECT * FROM cuentas_bancarias where status=1";
        return $this->select( $queryGetAll);
    }

    public function getTipoCuentas()
    {
        $queryTipoCuenta = "SELECT tipocuenta FROM ctipocuenta GROUP BY tipocuenta";
        return $this->select( $queryTipoCuenta );
    }

    public function getAllTipoCuentas()
    {
        $queryTipoCuentas = "SELECT * FROM ctipocuenta";
        return $this->select( $queryTipoCuentas );
    }

    public function getIfCargoAbonoMovimiento( $idMovimiento)
    {
        //creando una variable que devuelve si en el movimento se aplicó un egreso
        $this->db->query("SET @ingresos = (SELECT ingresos from saldos_bancarios where id = $idMovimiento )");
        return $this->select("SELECT CASE WHEN  @ingresos > 0 then 'Ingresos' else 'Egresos' END as tipoMovimiento");
    }

    public function getTipoOperacionMovimiento( $condiciones)
    {
        extract( $condiciones );
        $queryTipoOperacion ="SELECT  * FROM ctipocuenta WHERE  tipocuenta like '$tipoCuenta' AND tipomovimiento like '$tipoMov' ";
        return $this->select( $queryTipoOperacion);
    }

    public function getSaldosByTipoCuentasSucursal($condiciones)
    {
        extract( $condiciones);
        $querySaldosTipos = "SELECT ctipocuenta.tipocuenta,ctipocuenta.tipomovimiento, ctipocuenta.operacion, saldos_bancarios.ingresos, saldos_bancarios.egresos
                                                FROM ctipocuenta
                                                INNER JOIN saldos_bancarios ON saldos_bancarios.ctipocuenta_id = ctipocuenta.id
                                                WHERE month(saldos_bancarios.fecha) = $mes AND year(saldos_bancarios.fecha) = $anio AND saldos_bancarios.sucursal_id LIKE '$sucursal'
                                                GROUP BY saldos_bancarios.sucursal_id,ctipocuenta.id";
        return $this->select( $querySaldosTipos);
    }

    public function getMovimientoById( $id)
    {
        $queryGetSaldos = "SELECT * FROM saldos_bancarios WHERE id=$id  ";
        return  $this->select( $queryGetSaldos );        
    }
    public function getSaldosCuenta( $id, $mes='%')
    {


        $queryGetSaldos = "SELECT *,SUM(egresos) as totalEgresos, SUM(ingresos) as totalIngresos 
        FROM saldos_bancarios WHERE cuenta_bancaria_id=$id  and month(fecha) like '$mes' ORDER BY fecha asc ";
        
        return  $this->select( $queryGetSaldos );
    }

    public function getSaldoDesgloce( $id, $mes='%', $filtro = null,$init = null)
    {

        $limitQuery = '';
        if ( $init != null ) {
            $limitQuery = "ORDER BY id DESC LIMIT  $init, 20";
        }else{
            $limitQuery = "ORDER BY fecha ASC";
        }

        $condicion = "cuenta_bancaria_id=$id AND month(fecha) = '".date('m')."'";
        if ( $filtro != NULL ) {
            extract( $filtro );
            $condicion = "cuenta_bancaria_id like '$cuenta' AND $fecha AND beneficiario like '$beneficiario' AND referencia like '$referencia' ";
        }
        if ( $filtro != NULL) {
            if ( $init == null) {
                $init = 0;
            }
            if ( $filtro['cuenta'] != '%') {
                $limitQuery = "ORDER BY fecha DESC LIMIT  $init, 20";

            }else{
                $limitQuery = "ORDER BY id DESC LIMIT  $init, 20";
            }
            
        }
        $queryGetSaldos = "SELECT *FROM saldos_bancarios WHERE  $condicion  $limitQuery";
        
        return  $this->select( $queryGetSaldos );
    }

    public function getSaldosSinFiltro($init, $condicion = null)
    {
        
        if ( $condicion == null) {
            $condicion ="month(fecha) = '".date('m')."' AND year(fecha) = ".date('Y');
        }
        $querySaldos = "SELECT saldos_bancarios.*,cuentas_bancarias.banco
         FROM saldos_bancarios 
        INNER JOIN  cuentas_bancarias on cuentas_bancarias.id = saldos_bancarios.cuenta_bancaria_id
         WHERE  $condicion ORDER BY id DESC LIMIT  $init,20";
        
        return $this->select( $querySaldos );
    }

    public function getCuentasSaldo( $id)
    {
        $queryGetSaldos = "SELECT * FROM cuentas_bancarias WHERE id like '$id' ";
        return  $this->select( $queryGetSaldos );
    }
    
    public function getSaldoByMes($fecha)
    {
        $querySaldosByDay ="SELECT * from saldos_bancarios WHERE  month(fecha) ='$fecha' ";
        return $this->select( $querySaldosByDay);
    }

    public function getMovimientosByTipoCuenta( $condicion )
    {
        extract( $condicion );
        $queryMovimiento = "SELECT ctipocuenta.tipocuenta,ctipocuenta.tipomovimiento, ctipocuenta.operacion,con_movimientos.id,con_movimientos.descripcion,con_movimientos.total
                                                FROM ctipocuenta
                                                INNER JOIN con_movimientos ON con_movimientos.tipoCuenta = ctipocuenta.id
                                                INNER JOIN csucursal on csucursal.id = con_movimientos.idcudn
                                                WHERE month(con_movimientos.docfecha) = $mes and year(con_movimientos.docfecha) = $anio AND csucursal.id = $sucursal
                                                -- GROUP BY con_movimientos.idcudn,ctipocuenta.id";
        return $this->select( $queryMovimiento);
    }

    public function gteSaldosBancariosByTipoCuenta( $condicion){
        extract( $condicion );
        $querySaldo = "SELECT ctipocuenta.tipocuenta,ctipocuenta.tipomovimiento, ctipocuenta.operacion,saldos_bancarios.id,saldos_bancarios.referencia,
                                            saldos_bancarios.ingresos, saldos_bancarios.egresos
                                        FROM ctipocuenta
                                    INNER JOIN saldos_bancarios ON saldos_bancarios.ctipocuenta_id = ctipocuenta.id
                                    INNER JOIN csucursal on csucursal.id = saldos_bancarios.sucursal_id
                                    WHERE month(saldos_bancarios.fecha) = $mes and year(saldos_bancarios.fecha) = $anio AND  csucursal.id = $sucursal
                                    -- GROUP BY saldos_bancarios.sucursal_id,ctipocuenta.id";
        return $this->select( $querySaldo );
    }
	
    public function getMovimientosByPeriodo( $parametros )
    {
        extract( $parametros );

        $queryMovimientos = "SELECT * 
                                            FROM saldos_bancarios
                                            INNER JOIN cuentas_bancarias on cuentas_bancarias.id = saldos_bancarios.cuenta_bancaria_id 
                                            WHERE (saldos_bancarios.fecha >=  '$fechaInicio' AND saldos_bancarios.fecha <=  '$fechaFin' )
                                                    AND saldos_bancarios.egresos = 0
                                            ORDER BY fecha desc" ;

                                            // echo $queryMovimientos;
        return $this->select( $queryMovimientos );
    }
}
