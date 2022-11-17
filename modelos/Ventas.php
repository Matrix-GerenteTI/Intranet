<?php
set_time_limit(-1);
require_once 'FirebirDB.php';

class Ventas
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

    public function getVentasSemanal( $semana="" )
    {
        $date = strtotime(date("Y-m-d"));

        $first = strtotime('last Monday -7 days');
        $last = strtotime('next Sunday -7 days');
        $first = date("Y-m-d",$first);
        $last = date("Y-m-d",$last);


        $queryColision = "SELECT MARCA,IMPORTELINEA,DETIVA
                                        FROM VENTAS
                                        WHERE fecha BETWEEN '$first' AND '$last'  AND familia = 'COLISION'
                                        GROUP BY  marca,IMPORTELINEA,DETIVA";
        $exeColision = ibase_query( $this->conexionIbase , $queryColision);
        $ventasColision = $this->saveRetrievedRows($exeColision);

        return ($ventasColision);
    }

    public function saveRetrievedRows($executedQuery)
    {
        $registros = array();
        while ( $registro = ibase_fetch_object($executedQuery)) {
            $registro->NOMBREVENDEDOR = utf8_encode($registro->MARCA);
            array_push($registros,$registro);
        }
        return $registros;
    }
}


class VentasExtended extends FirebirdDB
{

    public function getMensualFamilia( $fecha, $familia)
    {
        extract( $fecha );
        $queryVentas = "SELECT CFG_ARTICULOS.CODIGOARTICULO,VENTAS.DESCRIPCION,VENTAS.SUBFAMILIA,CFG_ARTICULOS.PVP4,CFG_ARTICULOS.PVP2,
                            CFG_ARTICULOS.PVP3, VENTAS.CANTIDAD,extract(DAY FROM VENTAS.FECHA ) as DIA,VENTAS.FECHA,VENTAS.MARCA,VENTAS.ZONA
                    FROM CFG_ARTICULOS
                    INNER JOIN VENTAS ON VENTAS.CODIGO = CFG_ARTICULOS.CODIGOARTICULO
                    WHERE  VENTAS.FAMILIA IN ('$familia')
                                AND VENTAS.STATUS IN ('PEDIDO EMITIDO','PEDIDO FACTURADO') AND EXTRACT(YEAR FROM  FECHA) = $anio AND EXTRACT( MONTH FROM FECHA )= $mes
                ORDER BY VENTAS.SUBFAMILIA,VENTAS.FECHA ASC
        ";

        return $this->fireSelect( $queryVentas );
    }

	public function getMensualZona($desc,$fecha,$zona,$familia)
	{
		extract($fecha);
		$query = "SELECT CANTIDAD,FECHA,DESCRIPCION,STATUS FROM VENTAS WHERE FAMILIA IN('$familia') AND DESCRIPCION LIKE '%$desc%' AND ZONA='$zona' 
		AND EXTRACT(YEAR FROM FECHA) = $anio AND EXTRACT(MONTH FROM FECHA)=$mes AND STATUS IN ('PEDIDO EMITIDO','PEDIDO FACTURADO') ORDER BY SUBFAMILIA,FECHA ASC";
		echo $this->fireSelect($query);
		return $this->fireSelect($query);
	}

	
    public function getMensualFamiliaAltos( $fecha, $familia)
    {
        extract( $fecha);
        $queryVentas = "SELECT CFG_ARTICULOS.CODIGOARTICULO,VENTAS.DESCRIPCION,VENTAS.SUBFAMILIA,CFG_ARTICULOS.PVP4,CFG_ARTICULOS.PVP2,VENTAS.ID,
                            CFG_ARTICULOS.PVP3, VENTAS.CANTIDAD,extract(DAY FROM VENTAS.FECHA ) as DIA,VENTAS.FECHA,VENTAS.MARCA
                    FROM CFG_ARTICULOS
                    INNER JOIN VENTAS ON VENTAS.CODIGO = CFG_ARTICULOS.CODIGOARTICULO
                    INNER JOIN REF_ARTXALMACEN ON REF_ARTXALMACEN.FK1MCFG_ARTICULOS = CFG_ARTICULOS.ID
                    INNER JOIN CFG_ALMACENES ON CFG_ALMACENES.ID = REF_ARTXALMACEN.FK1MCFG_ALMACENES 
                    WHERE extract(MONTH FROM VENTAS.FECHA) = $mes AND extract(YEAR FROM VENTAS.FECHA)= $anio AND VENTAS.FAMILIA IN ('$familia')
                                AND VENTAS.STATUS IN ('PEDIDO EMITIDO','PEDIDO FACTURADO') AND CFG_ALMACENES.ZONA IN ('ALTOS')
                group by CFG_ARTICULOS.CODIGOARTICULO,VENTAS.ID,VENTAS.DESCRIPCION,VENTAS.SUBFAMILIA,CFG_ARTICULOS.PVP4,CFG_ARTICULOS.PVP2,CFG_ARTICULOS.PVP3, VENTAS.CANTIDAD,VENTAS.FECHA,DIA ,VENTAS.MARCA
                ORDER BY VENTAS.SUBFAMILIA,VENTAS.FECHA ASC";

        return $this->fireSelect( $queryVentas );
    }


	public function getFechaPrimeraVentaFamilia( $familia )
	{
		$queryfecha = "SELECT min(fecha) as FECHA  from ventas where familia = '$familia'";

		return $this->fireSelect( $queryfecha );
	}

	public function historicoVentaLlantasYStockActual()
	{
		$queryHistorico = "SELECT ventas.fecha, ventas.NUMDOCTO,ventas.CANTIDAD,VENTAS.CODIGO,ventas.DESCRIPCION,ventas.ALMACEN,ventas.IDALMACEN, raxa.FK1MCFG_ALMACENES AS IDALMACEN, 
ALM.DESCRIPCION AS ALMACEN_STOCK,(RAXA.EXISTOTAL-(RAXA.EXISPEDIDOS+RAXA.EXISPROCESO )) AS STOCK
                    FROM CFG_ARTICULOS
                    inner join REF_ARTXALMACEN AS RAXA on RAXA.FK1MCFG_ARTICULOS = CFG_ARTICULOS.ID
                    INNER JOIN ventas on ventas.CODIGO =  CFG_ARTICULOS.CODIGOARTICULO
                    INNER JOIN CFG_ALMACENES AS ALM ON ALM.ID = RAXA.FK1MCFG_ALMACENES
                    WHERE CFG_ARTICULOS.FAMILIA= 'LLANTA'  AND ( ALM.DESCRIPCION NOT LIKE '%APARTADOS%' AND ALM.DESCRIPCION NOT LIKE '%HERR.%' 
						AND ALM.DESCRIPCION NOT LIKE '%AJUSTES%' AND ALM.DESCRIPCION NOT LIKE '%PRESTAMOS%' AND ALM.DESCRIPCION NOT LIKE '%MERMAS%'
						AND ALM.DESCRIPCION NOT LIKE '%LEON SAN%' AND ALM.DESCRIPCION NOT LIKE '%ALMACEN VI%' AND ALM.DESCRIPCION NOT LIKE '%CEDIM%'
						AND ALM.DESCRIPCION NOT LIKE '%LLANTERA L%' AND ALM.DESCRIPCION NOT LIKE '%VENTASO%')";
		return $this->fireSelect( $queryHistorico );
	}

    public function formaPagoDeVentas( $periodo)
    {
        extract( $periodo )        ;
        /*$queryVentaFormaPago = "SELECT REF_PEDIDOSPRESUPFORMSPAGS.FORMAPAGO, REF_PEDIDOSPRESUPFORMSPAGS.REFERENCIA, 
                                        ventas.FECHA,REF_PEDIDOSPRESUPFORMSPAGS.IMPORTE,ventas.ID,VENTAS.ALMACEN
                            from ventas
                            inner join REF_PEDIDOSPRESUPFORMSPAGS on REF_PEDIDOSPRESUPFORMSPAGS.FK1MREF_PEDIDOSPRESUP = ventas.ID
                            where ventas.FECHA >= '$fechaInicio' AND VENTAS.FECHA <= '$fechaFin' AND referencia not like '%CAMBIO%'
                            group by ventas.ID,VENTAS.ALMACEN, ventas.FECHA, REF_PEDIDOSPRESUPFORMSPAGS.FORMAPAGO,REF_PEDIDOSPRESUPFORMSPAGS.IMPORTE,REF_PEDIDOSPRESUPFORMSPAGS.REFERENCIA
                            ORDER BY VENTAS.FECHA";
		*/
		
		$queryVentaFormaPago = "SELECT  PAG.formapago,
										PAG.referencia,
										P.fecha,
										sum(PAG.importe) as importe,
										
										P.ID,
										CFG_ALMACENES.DESCRIPCION AS ALMACEN
								FROM ref_pedidospresup P 
								inner join ref_pedidospresupformspags PAG on P.id=PAG.fk1mref_pedidospresup 
								INNER JOIN CFG_ALMACENES ON CFG_ALMACENES.ID = P.fk1mcfg_almacenes
								where P.status in ('PEDIDO EMITIDO','PEDIDO FACTURADO') 
								and P.fecha>='".$fechaInicio."' AND P.fecha<='".$fechaFin."'
								and (PAG.formapago like '%01%' or PAG.formapago like '%EFECTIVO%' or PAG.formapago like '%02%' or PAG.formapago like '%03%' or PAG.formapago like '%04%' or PAG.formapago like '%28%') 
								group by CFG_ALMACENES.DESCRIPCION,PAG.formapago,PAG.referencia,P.fecha,p.id 
								order by CFG_ALMACENES.DESCRIPCION,PAG.formapago,PAG.referencia";
		$resVtas = $this->fireSelect( $queryVentaFormaPago );
								
		$queryCobranzaFormaPago = "SELECT  RP.formapago,
										'COBRANZA' as referencia,
										H.fechamovi as fecha,
										sum(RP.importe) as importe,
										'' as ID,
										CFG_ALMACENES.DESCRIPCION AS ALMACEN							
								From (cxc_historcc H 
								inner join cxc_recibosformspags RP on H.id=RP.fk1mcxc_historcc 
								inner join cxc_recibos R on R.imp_cobro_historcc=H.id 
								inner join cxc_historcc HO on R.imp_factu_historcc=HO.id 
								inner join cfg_doctos D on D.id=HO.fk11cfg_cabdoctos) 
								INNER JOIN CFG_ALMACENES ON CFG_ALMACENES.ID = D.fk1mcfg_almacenes
								Where (H.concepto like '%INGXPAGPARCIAL%' OR H.concepto like '%INGXANTICIPO%') 
								and H.fechamovi>='".$fechaInicio."' AND H.fechamovi<='".$fechaFin."' 
								group by CFG_ALMACENES.DESCRIPCION,RP.formapago,H.fechamovi 
								order by CFG_ALMACENES.DESCRIPCION,RP.formapago";
								
		$resCobr = $this->fireSelect( $queryCobranzaFormaPago );
		$res = array();
		foreach($resVtas as $rowVtas)
			$res[] = $rowVtas;
		foreach($resCobr as $rowCobr)
			$res[] = $rowCobr;
			
        return $res;
    }
	
	public function ingresosDiarios( $dia, $mes, $anio)
    {
        $ventas = 0;
		$cobranza = 0;
		$queryVentas = "select  P.fk1mcfg_almacenes,
										PAG.formapago,
										PAG.referencia,
										sum(PAG.importe) as importe 
								from ref_pedidospresup P 
								inner join ref_pedidospresupformspags PAG on P.id=PAG.fk1mref_pedidospresup 
								where P.status in ('PEDIDO EMITIDO','PEDIDO FACTURADO') 
								and P.fecha='".$mes."/".$dia."/".$anio."' 
								and (PAG.formapago like '%01%' or PAG.formapago like '%EFECTIVO%' or PAG.formapago like '%02%' or PAG.formapago like '%03%' or PAG.formapago like '%04%' or PAG.formapago like '%28%') 
								group by P.fk1mcfg_almacenes,PAG.formapago,PAG.referencia 
								order by P.fk1mcfg_almacenes,PAG.formapago,PAG.referencia";
		foreach($this->fireSelect( $queryVentas ) as $row1){
			$ventas = $ventas + $row1->IMPORTE;
		}
		
		$queryCobranza = "Select  D.fk1mcfg_almacenes,
									RP.formapago,
									'' as referencia,
									sum(RP.importe) as importe 
							From (cxc_historcc H 
							inner join cxc_recibosformspags RP on H.id=RP.fk1mcxc_historcc 
							inner join cxc_recibos R on R.imp_cobro_historcc=H.id 
							inner join cxc_historcc HO on R.imp_factu_historcc=HO.id 
							inner join cfg_doctos D on D.id=HO.fk11cfg_cabdoctos) 
							Where (H.concepto like '%INGXPAGPARCIAL%' OR H.concepto like '%INGXANTICIPO%') 
							and H.fechamovi='".$mes."/".$dia."/".$anio."' 
							group by D.fk1mcfg_almacenes,RP.formapago 
							order by D.fk1mcfg_almacenes,RP.formapago";
		foreach($this->fireSelect( $queryCobranza ) as $row2){
			$cobranza = $cobranza + $row2->IMPORTE;
		}
		
		$ingresos = $ventas + $cobranza;
		
        return $ingresos;
    }
}
