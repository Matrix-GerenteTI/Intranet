<?php
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/DB.php";
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/FirebirDB.php";


class Vendedores extends DB
{
    protected $firebird;

    public function __construct() {
        $this->firebird = new FirebirdDB;
        parent::__construct();    
    }

    public function getAll( )
    {
        $queryVendedores = "SELECT * 
                                                FROM CFG_VENDEDORES
                                                WHERE ACTIVO = 'S'
                                                ORDER BY NOMBREVENDEDOR";
        return $this->firebird->fireSelect( $queryVendedores );
    }

    public function getMetaVentasVendedor(){
        $query = "SELECT    *
                  FROM      CFG_METASVENDEDOR MV";
        return $this->firebird->fireSelect( $query );
    }

    public function getVentasPorVendedor( $dia, $mes , $anio)
    {
        $mes==''?$mes=date('m'):$mes=$mes;
        if($mes!=0)
            $dia==''?$dia=date('d'):$dia=$dia;
        $queryVentas = "SELECT 	CFGV.ID as ID,
                                            ART.DESCRIPCION as DESCRIPCION,
                                            ART.FAMILIA as FAMILIA,
                                            ART.SUBFAMILIA as SUBFAMILIA,
                                            DET.CANTIDAD as CANTIDAD,
                                            CFGV.NOMBREVENDEDOR as VENDEDOR,
                                            (DET.IMPORTELINEA+DET.IMPIVA) AS TOTAL, 
                                            DET.PRECIO + (DET.PRECIO * (DET.PORCIVA/100)) as PRECIO,
                                            PRE.PVP3 as PVP3,
                                            'DOCTOS.' AS FORMLISTA 
                                    From 		ref_pedidospresup CFGD 
                                    inner join cfg_vendedores CFGV on CFGV.id=CFGD.fk1mcfg_vendedores 
                                    inner join REF_DETPEDIDOSPRESUP DET on CFGD.ID=DET.FKPADREF_PEDIDOSPRESUP 
                                    inner join CFG_ARTICULOS ART on DET.CODIGO=ART.CODIGOARTICULO 
                                    inner join CFG_PRECIOSXALMACENES PRE on PRE.FK1MCFG_ARTICULOS=ART.ID and PRE.FK1MCFG_ALMACENES=10754
                                    Where 		EXTRACT(YEAR FROM CFGD.FECHA)=$anio";
        if($mes>0)
            $queryVentas.= "        AND 	EXTRACT(MONTH FROM CFGD.FECHA)=$mes"; 
        if($dia>0)
            $queryVentas.= "        AND 		EXTRACT(DAY FROM CFGD.FECHA)=$dia";
        $queryVentas.= "            AND NOT CFGD.ESPEDLIQAPARTADO='AA' 
                                    AND 		CFGD.SERDOCTO<>'CREDITO' 
                                    AND 		ART.FAMILIA NOT IN ('OXIFUEL','CONTRAPESO','CONTRAPESO')
                                    AND 		CFGD.DOCTOESPECIAL='' 
                                    AND 		CFGD.STATUS IN ('PEDIDO EMITIDO','PEDIDO FACTURADO')
                                    Order by CFGV.NOMBREVENDEDOR,TOTAL DESC";

        return $this->firebird->fireSelect( $queryVentas );
    }

    public function getVentasPorVendedorFam( $dia, $mes , $anio)
    {
        $mes==''?$mes=date('m')*1:$mes=$mes;
        // if($mes!=0)
        //     $dia==''?$dia=date('d')*1:$dia=$dia;
        $queryVentas = "SELECT 	CFGV.ID as ID,
                                            ART.DESCRIPCION as DESCRIPCION,
                                            ART.FAMILIA as FAMILIA,
                                            ART.SUBFAMILIA as SUBFAMILIA,
                                            DET.CANTIDAD as CANTIDAD,
                                            CFGV.NOMBREVENDEDOR as VENDEDOR,
                                            CFGV.ID as IDVENDEDOR,
                                            CASE WHEN ART.FAMILIA='SERVICIO' THEN 
                                                CASE WHEN ART.SUBFAMILIA='ALINEACION' THEN (DET.IMPORTELINEA+DET.IMPIVA) ELSE 0 END
                                            ELSE 
                                                (DET.IMPORTELINEA+DET.IMPIVA)
                                            END AS TOTAL, 
                                            DET.PRECIO + (DET.PRECIO * (DET.PORCIVA/100)) as PRECIO,
                                            PRE.PVP3 as PVP3,
                                            'DOCTOS.' AS FORMLISTA,
                                            EXTRACT(DAY FROM CFGD.FECHA) as DIA,
                                            EXTRACT(MONTH FROM CFGD.FECHA) as MES,
                                            EXTRACT(YEAR FROM CFGD.FECHA) as ANIO
                                    From 		ref_pedidospresup CFGD 
                                    inner join cfg_vendedores CFGV on CFGV.id=CFGD.fk1mcfg_vendedores 
                                    inner join REF_DETPEDIDOSPRESUP DET on CFGD.ID=DET.FKPADREF_PEDIDOSPRESUP 
                                    inner join CFG_ARTICULOS ART on DET.CODIGO=ART.CODIGOARTICULO 
                                    inner join CFG_PRECIOSXALMACENES PRE on PRE.FK1MCFG_ARTICULOS=ART.ID and PRE.FK1MCFG_ALMACENES=10754
                                    Where 		EXTRACT(YEAR FROM CFGD.FECHA)=$anio ";
        if($mes>0)
            $queryVentas.= "        AND 	EXTRACT(MONTH FROM CFGD.FECHA)=$mes "; 
        if($dia>0)
            $queryVentas.= "        AND 		EXTRACT(DAY FROM CFGD.FECHA)=$dia ";
        $queryVentas.= "            AND NOT CFGD.ESPEDLIQAPARTADO='AA' 
                                    AND 		CFGD.SERDOCTO<>'CREDITO' 
                                    AND 		ART.FAMILIA NOT IN ('OXIFUEL','CONTRAPESO','CONTRAPESO')
                                    AND 		CFGD.DOCTOESPECIAL='' 
                                    AND 		CFGD.STATUS IN ('PEDIDO EMITIDO','PEDIDO FACTURADO')
                                    Order by CFGV.NOMBREVENDEDOR,TOTAL DESC";
        //die($queryVentas);
        return $this->firebird->fireSelect( $queryVentas );
    }
    

    public function getVentasMetasVendedor( $mes , $anio)
    {
        $mes==''?$mes=date('m')*1:$mes=$mes;
        // if($mes!=0)
        //     $dia==''?$dia=date('d')*1:$dia=$dia;
        $queryVentas = "SELECT 	CFGV.ID as ID,
                                            ART.DESCRIPCION as DESCRIPCION,
                                            ART.FAMILIA as FAMILIA,
                                            ART.SUBFAMILIA as SUBFAMILIA,
                                            DET.CANTIDAD as CANTIDAD,
                                            CFGV.NOMBREVENDEDOR as VENDEDOR,
                                            CFGV.ID as IDVENDEDOR,
                                            CASE WHEN ART.FAMILIA='SERVICIO' THEN 
                                                CASE WHEN ART.SUBFAMILIA='ALINEACION' THEN (DET.CANTIDAD) ELSE 0 END
                                            ELSE 
                                                0
                                            END AS TOTALALINEACION, 
                                            CASE WHEN ART.FAMILIA<>'SERVICIO' THEN 
                                                (DET.IMPORTELINEA+DET.IMPIVA)
                                            ELSE 
                                                0
                                            END AS TOTALNOSERVICIO, 
                                            DET.PRECIO + (DET.PRECIO * (DET.PORCIVA/100)) as PRECIO,
                                            PRE.PVP3 as PVP3,
                                            'DOCTOS.' AS FORMLISTA,
                                            EXTRACT(DAY FROM CFGD.FECHA) as DIA,
                                            EXTRACT(MONTH FROM CFGD.FECHA) as MES,
                                            EXTRACT(YEAR FROM CFGD.FECHA) as ANIO,
                                            MVM.IMPORTE as METAIMPORTE,
                                            MVM.ALINEACIONES as METAALINEACIONES,
                                            MVM.BONOIMPORTE as BONOIMPORTE,
                                            MVM.BONOALINEACIONES as BONOALINEACIONES,
                                            CFGV.CODPUNTODEVENTA as CODIGOVENDEDOR
                                    From 		ref_pedidospresup CFGD 
                                    inner join cfg_vendedores CFGV on CFGV.id=CFGD.fk1mcfg_vendedores 
                                    inner join REF_DETPEDIDOSPRESUP DET on CFGD.ID=DET.FKPADREF_PEDIDOSPRESUP 
                                    inner join CFG_ARTICULOS ART on DET.CODIGO=ART.CODIGOARTICULO 
                                    inner join CFG_PRECIOSXALMACENES PRE on PRE.FK1MCFG_ARTICULOS=ART.ID and PRE.FK1MCFG_ALMACENES=10754
                                    left join CFG_METASVENDEDORMENSUAL MVM on CFGV.CODPUNTODEVENTA=MVM.CODIGOVENDEDOR
                                    Where 		EXTRACT(YEAR FROM CFGD.FECHA)=$anio
                                    AND 	EXTRACT(MONTH FROM CFGD.FECHA)=$mes
                                    AND NOT CFGD.ESPEDLIQAPARTADO='AA' 
                                    AND 		CFGD.SERDOCTO<>'CREDITO' 
                                    AND 		ART.FAMILIA NOT IN ('OXIFUEL','CONTRAPESO','CONTRAPESO')
                                    AND 		CFGD.DOCTOESPECIAL='' 
                                    AND 		CFGD.STATUS IN ('PEDIDO EMITIDO','PEDIDO FACTURADO')
                                    Order by CFGV.NOMBREVENDEDOR,TOTAL DESC";
        //die($queryVentas);
        return $this->firebird->fireSelect( $queryVentas );
    }
}
