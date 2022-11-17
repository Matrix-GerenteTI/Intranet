--query para  buscar las facturas y anticipos por id de crm_prospecto
select R.id,R.numero,R.fechavto,R.fechaemision,R.importe,R.observaciones,R.tipocliente,R.imp_factu_historcp,H.porciva,R.numfact,R.conceptoreal,H.fk11cfg_cabdoctos,
H.concepto,H.comentario,H.fk1mcrm_prospectos 
from cxp_recibos R 
inner join cxp_historcp H on H.id=R.imp_factu_historcp where H.documentado='S' and (R.rec_pagado='F' or (R.numero='ANTICIPO' and R.tipocobro<>'CANCELADO')) and H.fk1mcrm_prospectos=4676 order by R.fechaemision,R.numero


-- busca los anticipos / facturas por pagar cuando se introduce la factura del proveedor
select R.id,R.numero,R.fechavto,R.fechaemision,R.importe,R.observaciones,R.tipocliente,R.imp_factu_historcc,H.porciva,
R.numfact,R.conceptoreal,H.fk11cfg_cabdoctos,H.documentado,M.campo1,H.concepto,H.comentario,PYM.pym_nombre,PYM.id_prospecto,D.wsuuid
 from cxc_recibos R 
 inner join cxc_historcc H on H.id=R.imp_factu_historcc left outer join cfg_masdatos M on M.fk11padid=H.fk11pedidoid left outer join cfg_doctos D on D.id=H.fk11cfg_cabdoctos inner join crm_prospectos PYM on PYM.id_prospecto=H.fk1mcrm_prospectos where R.importe>0 and H.documentado='S' and (R.rec_pagado='F' and R.numero<>'ANTICIPO') and H.numdocumento like '%"%' and (M.origen='REF_PEDIDOSPRESUP' or M.origen is null) order by R.fechaemision,R.numero


 --Tabla contador para hacer increment en las tablas
 update CONTADOR set id=id+1 where tabla=''
 --obtiene el  id que se va a ingresar
 select *  from CONTADOR where tabla='CRM_PROSPECTOS'
 --SE OBTIENE EL ULTIMO ID DE LA TABLA EN LA QUE QUERMOS INSERTAR
 select max(ID_PROSPECTO) from CRM_PROSPECTOS
 --SE INSERTA EL ID QUE VA SER INSERTADO EN LA TABLA
 insert into contador values (1+1,'tabla')