<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/intranet/modelos/DB.php';

class Organigrama extends DB
{
    public   function estructuraOrganigrama( $abstraccion, $idNodo = null)
        {
            $condicionWhere = $this->setCondicionAbstraccion($abstraccion, $idNodo);

            $queryOrganigrama = "SELECT porganigrama.idhijo as id,porganigrama.idpadre as parentId,porganigrama.iddepa_hijo,porganigrama.iddepa_padre,cpuesto.descripcion
                                    from porganigrama
                                    inner join cpuesto ON porganigrama.idhijo = cpuesto.id  
                                    left join cpuesto as puesto On puesto.id = porganigrama.idpadre 
                                    where $condicionWhere
                                    order by parentId";
                    // echo $queryOrganigrama;
            $exeOrganigrama = $this->conexion()->query( $queryOrganigrama);
            return $exeOrganigrama->fetch_all(MYSQLI_ASSOC);
        }

        public function setCondicionAbstraccion( $abstraccion, $idNodo)
        {
            $condicionWhere = ' 1= 1';
                if ( $abstraccion == 'Dir') {
                    $condicionWhere = " abstraccion LIKE '%$abstraccion%' ";
                }elseif ( $abstraccion == 'Jef' || $abstraccion == 'Ger' ) {
                    if ( $idNodo != '') {
                        $condicionWhere = "abstraccion like '%$abstraccion%' AND (porganigrama.idhijo = $idNodo || porganigrama.idpadre = $idNodo) ";
                    }
                }
            return $condicionWhere;
        }

        public function getHijosSubNodosPosibles($params)
        {
            extract( $params);

            $wherePadre = " AND porganigrama.idpadre= $padre AND porganigrama.iddepa_padre=$depaPadre";
            if ( $padre == NULL) {
                $wherePadre = " AND porganigrama.idpadre IS NULL  AND porganigrama.iddepa_padre IS NULL";
            }

            $abstraccion = '';
            if ( $abstraccion  != 1) {
                $abstraccion = "porganigrama.abstraccion like '%$abstraccion%'  and ";
            }
            $queryNodos = " SELECT porganigrama.idhijo as id,porganigrama.idpadre as parentId, cpuesto.descripcion,pcontrato.id contrato,pcontrato.fechainiciolab,pempleado.nip, 
                            pempleado.nombre,(pcontrato.salariobase*15) AS sueldo,pempleado.curp,pempleado.nss, csucursal.descripcion as sucursal,csucursal.id as idSucursal,pcontrato.iddepartamento,pempleado.foto,
                            porganigrama.iddepa_padre,porganigrama.iddepa_hijo
                                        from porganigrama
                                        inner join cpuesto ON porganigrama.idhijo = cpuesto.id
                                        left join cpuesto as puesto On puesto.id = porganigrama.idpadre 
                                        inner join pcontrato ON pcontrato.idpuesto = porganigrama.idhijo
                                        inner join pempleado ON pempleado.nip = pcontrato.nip
                                        inner join csucursal ON csucursal.id = pempleado.idsucursal
                                        WHERE  $abstraccion pempleado.status = 1 AND  porganigrama.idhijo = $hijo AND  porganigrama.iddepa_hijo=$depaHijo $wherePadre
                                        order by parentId";
                                        // echo $queryNodos.'<br><br>';
            $exeNodos = $this->conexion()->query($queryNodos);
            return $exeNodos->fetch_all(MYSQLI_ASSOC);
        }
    
        public function getJerarquizacionPuestos( $tipoOrganigrama)
        {
            $queryNiveles = "SELECT porganigrama.idhijo,porganigrama.iddepa_hijo,cpuesto.descripcion
                                            from porganigrama
                                            INNER JOIN cpuesto on cpuesto.id = porganigrama.idhijo
                                            WHERE porganigrama.abstraccion LIKE '%$tipoOrganigrama%'  ";
            $exeNiveles = $this->conexion()->query( $queryNiveles);
            return $exeNiveles->fetch_all(MYSQLI_ASSOC);
        }

        public function getJefePuestos($puesto)
        {
            $queryPuestos = "SELECT pempleado.nombre,pempleado.nip,cpuesto.id,pcontrato.iddepartamento FROM pempleado
                                                INNER JOIN pcontrato on pcontrato.nip = pempleado.nip
                                                inner join cpuesto on cpuesto.id = pcontrato.idpuesto
                                                WHERE pempleado.status =1 AND cpuesto.id LIKE '$puesto' ";
            $exePuestos = $this->conexion()->query( $queryPuestos );                                                
            return $exePuestos->fetch_all(MYSQLI_ASSOC);
        }

        public function personalPadreSucursal($idPadre, $idDepaPadre,$statusEmpleado, $sucursalHijo = null)
        {
            $condicionSucursal = "";
            if ( $sucursalHijo != null) {
                
                $condicionSucursal = " AND csucursal.id = $sucursalHijo";
            }
          $wherePadre = " AND porganigrama.idhijo= $idPadre AND porganigrama.iddepa_hijo=$idDepaPadre";
            if ( $idPadre == NULL) {
                $wherePadre = " AND porganigrama.idhijo IS  NULL  AND porganigrama.iddepa_hijo IS  NULL";
            }

            $queryPersonalNodo= "SELECT porganigrama.idhijo as id,porganigrama.idpadre as parentId, cpuesto.descripcion,pcontrato.id contrato,pcontrato.fechainiciolab,pempleado.nip, 
                            pempleado.nombre,(pcontrato.salariobase*15) AS sueldo,pempleado.curp,pempleado.nss, csucursal.descripcion as sucursal,csucursal.id as idSucursal,pcontrato.iddepartamento,pempleado.foto,
                            porganigrama.iddepa_padre,porganigrama.iddepa_hijo
                                        from porganigrama
                                        inner join cpuesto ON porganigrama.idhijo = cpuesto.id
                                        left join cpuesto as puesto On puesto.id = porganigrama.idpadre 
                                        inner join pcontrato ON pcontrato.idpuesto = porganigrama.idhijo
                                        inner join pempleado ON pempleado.nip = pcontrato.nip
                                        inner join csucursal ON csucursal.id = pempleado.idsucursal
                                        WHERE    pempleado.status =$statusEmpleado $wherePadre $condicionSucursal
                                        order by parentId";
                                        
            $exePersonalNodo = $this->conexion()->query($queryPersonalNodo);
            return $exePersonalNodo->fetch_all(MYSQLI_ASSOC);            
        }

        public function getNodoOrganigrama( $padre, $hijo, $nip = NULL)
        {
            $wherePadre = "porganigrama.idpadre= $padre";
            $whereInfoTrabajador ="";
            
            if ($padre == 'null' || $padre == '') {
                $wherePadre = "porganigrama.idpadre is NULL";
            }
            if ( $nip != NULL) {
                $whereInfoTrabajador =" AND pempleado.nip = $nip";
            }
            $queryOrganigrama = "SELECT porganigrama.idhijo as id,porganigrama.idpadre as parentId, cpuesto.descripcion,pcontrato.id contrato,pcontrato.fechainiciolab,pempleado.nip, 
                                        pempleado.nombre,(pcontrato.salariobase*15) AS sueldo,pempleado.curp,pempleado.nss, csucursal.descripcion as sucursal,csucursal.id as idSucursal,pcontrato.iddepartamento,pempleado.foto
                                                    from porganigrama
                                                    inner join cpuesto ON porganigrama.idhijo = cpuesto.id
                                                    left join cpuesto as puesto On puesto.id = porganigrama.idpadre 
                                                    inner join pcontrato ON pcontrato.idpuesto = porganigrama.idhijo
                                                    inner join pempleado ON pempleado.nip = pcontrato.nip
                                                    inner join csucursal ON csucursal.id = pempleado.idsucursal
                                                    WHERE   pempleado.status = 1 AND  porganigrama.idhijo = $hijo AND $wherePadre $whereInfoTrabajador
                                                    order by parentId";

                                             
            $exeOrganigrama = $this->conexion()->query( $queryOrganigrama);
            return $exeOrganigrama->fetch_all(MYSQLI_ASSOC);
        }

                public function getNodoOrganigramaSucursal( $padre, $sucursal)
        {

            $queryOrganigrama = "SELECT porganigrama.idhijo as id,porganigrama.idpadre as parentId, cpuesto.descripcion,pcontrato.id contrato,pcontrato.fechainiciolab,pempleado.nip, 
                                        pempleado.nombre,(pcontrato.salariobase*15) AS sueldo,pempleado.curp,pempleado.nss, csucursal.descripcion as sucursal,csucursal.id as idSucursal,pcontrato.iddepartamento,pempleado.foto
                                                    from porganigrama
                                                    inner join cpuesto ON porganigrama.idhijo = cpuesto.id
                                                    left join cpuesto as puesto On puesto.id = porganigrama.idpadre 
                                                    inner join pcontrato ON pcontrato.idpuesto = porganigrama.idhijo
                                                    inner join pempleado ON pempleado.nip = pcontrato.nip
                                                    inner join csucursal ON csucursal.id = pempleado.idsucursal
                                                    WHERE  pempleado.status = 1 AND  porganigrama.idhijo = $hijo  AND pempleado.idsucursal = $sucursal
                                                    order by parentId";

                                             
            $exeOrganigrama = $this->conexion()->query( $queryOrganigrama);
            return $exeOrganigrama->fetch_all(MYSQLI_ASSOC);
        }

        public function getOrganigramaByDepartamento($departamento = NULL)
        {
             $queryOrganigrama = "SELECT porganigrama.idhijo as id,porganigrama.idpadre as parentId, cpuesto.descripcion as puestoHijo,puestoP.descripcion as puestoPadre,
                                    cdepartamento.id as idDepaHijo,cdepartamento.descripcion as depaHijo,departamento.id as idDepaPadre,departamento.descripcion as depaPadre,
                                        porganigrama.idsucursal, porganigrama.abstraccion
                                                        FROM porganigrama
                                                        LEFT JOIN cpuesto on cpuesto.id = porganigrama.idhijo
                                                        LEFT JOIN cpuesto as puestoP ON puestoP.id = porganigrama.idpadre
                                                        LEFT JOIN cdepartamento ON cdepartamento.id = cpuesto.iddepartamento
                                                        LEFT JOIN cdepartamento AS departamento ON departamento.id = puestoP.iddepartamento";
                                                        // echo $queryOrganigrama;
            $exeOrganigrama = $this->conexion()->query( $queryOrganigrama );
            return $exeOrganigrama->fetch_all( MYSQLI_ASSOC );
        }
}

