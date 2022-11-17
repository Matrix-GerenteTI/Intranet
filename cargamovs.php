<?php
	$conn = new mysqli("matrix.com.mx", "sestrada", "M@tr1x2017", "matrixerp");
	
	$file = file("movimientos.csv");
	foreach($file as $lineas){
		$linea = explode(",",$lineas);
		/*
		[0] -> fecha
		[1] -> tipo EGRESO/INGRESO
		[2] -> consecutivo
		[3] -> concepto
		[4] -> tipo NOTA/FACTURA
		[5] -> monto
		[6] -> segmento
		[7] -> cuenta
		*/
		var_dump($linea);
		$q1 = "SELECT * FROM con_cuentas WHERE cuenta='".trim($linea[7])."'";
		$s1 = $conn->query($q1);
		$r1 = $s1->fetch_assoc();
		$id_cuenta = $r1['id'];
		if($linea[4] == 'FACTURA'){
			$uuid = '000000-0000-00000-00000000';
			$subtotal = $linea[5]/1.16;
			$iva = $linea[5] - $subtotal;
		}else{
			$uuid = '';
			$subtotal = $linea[5];
			$iva = 0;
		}
		
		$query = "INSERT INTO con_movimientos (descripcion,
											   fecha,
											   hora,
											   docfecha,
											   dochora,
											   docuuid,
											   subtotal,
											   iva,
											   total,
											   idcon_cuentas,
											   tipo,
											   financiero,
											   recurrente,
											   status,
											   idcudn) 
									VALUES 	  ('".$linea[3]."',
											   '".$linea[0]."',
											   NOW(),
											   '".$linea[0]."',
											   NOW(),
											   '".$uuid."',
											   ".$subtotal.",
											   ".$iva.",
											   ".$linea[5].",
											   '".$id_cuenta."',
											   2,
											   0,
											   0,
											   1,
											   '".$linea[6]."')";
		$sql = $conn->query($query);
		
	}
?>