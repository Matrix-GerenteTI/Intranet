<?php
session_start();
require('fpdf/fpdf.php');
require_once $_SERVER['DOCUMENT_ROOT']."/eshop/Controladores/Cotizacion.php";
require_once $_SERVER['DOCUMENT_ROOT']."/eshop/Modelos/Sucursales.php";
require_once dirname(__DIR__)."../../Modelos/Articulos.php";
$GLOBALS['n'] = 0;
$GLOBALS['paginas'] = 1;
$GLOBALS['h'] = 0;
//GENERAR EL PDF YA QUE SI SE TIMBR

class PDF extends FPDF{
	
	var $widths;
	var $heightL;
	var $aligns;
	var $fonts;
	
	var $javascript;
	var $n_js;

	function IncludeJS($script) {
		$this->javascript=$script;
	}

	function _putjavascript() {
		$this->_newobj();
		$this->n_js=$this->n;
		$this->_out('<<');
		$this->_out('/Names [(EmbeddedJS) '.($this->n+1).' 0 R]');
		$this->_out('>>');
		$this->_out('endobj');
		$this->_newobj();
		$this->_out('<<');
		$this->_out('/S /JavaScript');
		$this->_out('/JS '.$this->_textstring($this->javascript));
		$this->_out('>>');
		$this->_out('endobj');
	}

	function _putresources() {
		parent::_putresources();
		if (!empty($this->javascript)) {
			$this->_putjavascript();
		}
	}

	function _putcatalog() {
		parent::_putcatalog();
		if (!empty($this->javascript)) {
			$this->_out('/Names <</JavaScript '.($this->n_js).' 0 R>>');
		}
	}


	function AutoPrint($dialog=false)
	{
		//Open the print dialog or start printing immediately on the standard printer
		$param=($dialog ? 'true' : 'false');
		$script="print($param);";
		$this->IncludeJS($script);
	}
	
	function AutoPrintToPrinter($server, $printer, $dialog=false)
	{
		//Print on a shared printer (requires at least Acrobat 6)
		$script = "var pp = getPrintParams();";
		if($dialog)
			$script .= "pp.interactive = pp.constants.interactionLevel.full;";
		else
			$script .= "pp.interactive = pp.constants.interactionLevel.automatic;";
		$script .= "pp.printerName = '\\\\\\\\".$server."\\\\".$printer."';";
		$script .= "print(pp);";
		$this->IncludeJS($script);
	}
	
	function truncateFloat($number, $digitos)
	{
		$raiz = 10;
		$multiplicador = pow ($raiz,$digitos);
		$resultado = ((int)($number * $multiplicador)) / $multiplicador;
		return number_format($resultado, $digitos);
	 
	}
	
	function num2letras($num, $fem = false, $dec = true) { 
	   $matuni[2]  = "dos"; 
	   $matuni[3]  = "tres"; 
	   $matuni[4]  = "cuatro"; 
	   $matuni[5]  = "cinco"; 
	   $matuni[6]  = "seis"; 
	   $matuni[7]  = "siete"; 
	   $matuni[8]  = "ocho"; 
	   $matuni[9]  = "nueve"; 
	   $matuni[10] = "diez"; 
	   $matuni[11] = "once"; 
	   $matuni[12] = "doce"; 
	   $matuni[13] = "trece"; 
	   $matuni[14] = "catorce"; 
	   $matuni[15] = "quince"; 
	   $matuni[16] = "dieciseis"; 
	   $matuni[17] = "diecisiete"; 
	   $matuni[18] = "dieciocho"; 
	   $matuni[19] = "diecinueve"; 
	   $matuni[20] = "veinte"; 
	   $matunisub[2] = "dos"; 
	   $matunisub[3] = "tres"; 
	   $matunisub[4] = "cuatro"; 
	   $matunisub[5] = "quin"; 
	   $matunisub[6] = "seis"; 
	   $matunisub[7] = "sete"; 
	   $matunisub[8] = "ocho"; 
	   $matunisub[9] = "nove"; 
	
	   $matdec[2] = "veint"; 
	   $matdec[3] = "treinta"; 
	   $matdec[4] = "cuarenta"; 
	   $matdec[5] = "cincuenta"; 
	   $matdec[6] = "sesenta"; 
	   $matdec[7] = "setenta"; 
	   $matdec[8] = "ochenta"; 
	   $matdec[9] = "noventa"; 
	   $matsub[3]  = 'mill'; 
	   $matsub[5]  = 'bill'; 
	   $matsub[7]  = 'mill'; 
	   $matsub[9]  = 'trill'; 
	   $matsub[11] = 'mill'; 
	   $matsub[13] = 'bill'; 
	   $matsub[15] = 'mill'; 
	   $matmil[4]  = 'millones'; 
	   $matmil[6]  = 'billones'; 
	   $matmil[7]  = 'de billones'; 
	   $matmil[8]  = 'millones de billones'; 
	   $matmil[10] = 'trillones'; 
	   $matmil[11] = 'de trillones'; 
	   $matmil[12] = 'millones de trillones'; 
	   $matmil[13] = 'de trillones'; 
	   $matmil[14] = 'billones de trillones'; 
	   $matmil[15] = 'de billones de trillones'; 
	   $matmil[16] = 'millones de billones de trillones'; 
	   
	   //Zi hack
	   $float=explode('.',$num);
	   $num=$float[0];
	
	   $num = trim((string)@$num); 
	   if ($num[0] == '-') { 
		  $neg = 'menos '; 
		  $num = substr($num, 1); 
	   }else 
		  $neg = ''; 
	   while ($num[0] == '0') $num = substr($num, 1); 
	   if ($num[0] < '1' or $num[0] > 9) $num = '0' . $num; 
	   $zeros = true; 
	   $punt = false; 
	   $ent = ''; 
	   $fra = ''; 
	   for ($c = 0; $c < strlen($num); $c++) { 
		  $n = $num[$c]; 
		  if (! (strpos(".,'''", $n) === false)) { 
			 if ($punt) break; 
			 else{ 
				$punt = true; 
				continue; 
			 } 
	
		  }elseif (! (strpos('0123456789', $n) === false)) { 
			 if ($punt) { 
				if ($n != '0') $zeros = false; 
				$fra .= $n; 
			 }else 
	
				$ent .= $n; 
		  }else 
	
			 break; 
	
	   } 
	   $ent = '     ' . $ent; 
	   if ($dec and $fra and ! $zeros) { 
		  $fin = ' coma'; 
		  for ($n = 0; $n < strlen($fra); $n++) { 
			 if (($s = $fra[$n]) == '0') 
				$fin .= ' cero'; 
			 elseif ($s == '1') 
				$fin .= $fem ? ' una' : ' un'; 
			 else 
				$fin .= ' ' . $matuni[$s]; 
		  } 
	   }else 
		  $fin = ''; 
	   if ((int)$ent === 0) return 'Cero ' . $fin; 
	   $tex = ''; 
	   $sub = 0; 
	   $mils = 0; 
	   $neutro = false; 
	   while ( ($num = substr($ent, -3)) != '   ') { 
		  $ent = substr($ent, 0, -3); 
		  if (++$sub < 3 and $fem) { 
			 $matuni[1] = 'una'; 
			 $subcent = 'as'; 
		  }else{ 
			 $matuni[1] = $neutro ? 'un' : 'uno'; 
			 $subcent = 'os'; 
		  } 
		  $t = ''; 
		  $n2 = substr($num, 1); 
		  if ($n2 == '00') { 
		  }elseif ($n2 < 21) 
			 $t = ' ' . $matuni[(int)$n2]; 
		  elseif ($n2 < 30) { 
			 $n3 = $num[2]; 
			 if ($n3 != 0) $t = 'i' . $matuni[$n3]; 
			 $n2 = $num[1]; 
			 $t = ' ' . $matdec[$n2] . $t; 
		  }else{ 
			 $n3 = $num[2]; 
			 if ($n3 != 0) $t = ' y ' . $matuni[$n3]; 
			 $n2 = $num[1]; 
			 $t = ' ' . $matdec[$n2] . $t; 
		  } 
		  $n = $num[0]; 
		  if ($n == 1) { 
			 $t = ' ciento' . $t; 
		  }elseif ($n == 5){ 
			 $t = ' ' . $matunisub[$n] . 'ient' . $subcent . $t; 
		  }elseif ($n != 0){ 
			 $t = ' ' . $matunisub[$n] . 'cient' . $subcent . $t; 
		  } 
		  if ($sub == 1) { 
		  }elseif (! isset($matsub[$sub])) { 
			 if ($num == 1) { 
				$t = ' mil'; 
			 }elseif ($num > 1){ 
				$t .= ' mil'; 
			 } 
		  }elseif ($num == 1) { 
			 $t .= ' ' . $matsub[$sub] . '?n'; 
		  }elseif ($num > 1){ 
			 $t .= ' ' . $matsub[$sub] . 'ones'; 
		  }   
		  if ($num == '000') $mils ++; 
		  elseif ($mils != 0) { 
			 if (isset($matmil[$sub])) $t .= ' ' . $matmil[$sub]; 
			 $mils = 0; 
		  } 
		  $neutro = true; 
		  $tex = $t . $tex; 
	   } 
	   $tex = $neg . substr($tex, 1) . $fin; 
	   //Zi hack --> return ucfirst($tex);
	   $end_num=ucfirst($tex).' pesos '.$float[1].'/100 M.N.';
	   return $end_num; 
	}
	
	
	function SetWidths($w)
	{
		//Set the array of column widths
		$this->widths=$w;
	}
	
	function SetFonts($b)
	{
		//Set the array of column widths
		$this->Ffonts=$b;
	}
	
	function SetBorders($b)
	{
		//Set the array of column widths
		$this->Bborders=$b;
	}
	
	function SetHeights($h)
	{
		//Set the array of column heights
		$this->heightL=$h;
	}
	
	function SetAligns($a)
	{
		//Set the array of column alignments
		$this->aligns=$a;
	}
	
	function Row($data)
	{
		//Calculate the height of the row
		$nb=0;
		for($i=0;$i<count($data);$i++)
			$nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
		$h=5*$nb;
		//Issue a page break first if needed
		$this->CheckPageBreak($h);
		//Draw the cells of the row
		for($i=0;$i<count($data);$i++)
		{
			$w=$this->widths[$i];
			$f=$this->Ffonts[$i];
			//print_r($f);
			$h=$this->heightL;
			$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
			$br=isset($this->Bborders[$i]) ? $this->Bborders[$i] : 0;
			//Save the current position
			$x=$this->GetX();
			$y=$this->GetY();
			//Draw the border
			$this->SetFont((string)$f[0],(string)$f[1],$f[2]);
			//$this->Rect($x,$y,$w,$h);
			$this->MultiCell($w,$h,$data[$i],(string)$br,$a,false);
			//Put the position to the right of the cell
			$this->SetXY($x+$w,$y);
		}
		//Go to the next line
		$this->Ln($h);
	}
	
	function Row2($data)
	{
		//Calculate the height of the row
		$nb=0;
		for($i=0;$i<count($data);$i++)
			$nb=max($nb, $this->NbLines($this->widths[$i], $data[$i]));
		$h=5*$nb;
		//Issue a page break first if needed
		$this->CheckPageBreak($h);
		//Draw the cells of the row
		for($i=0;$i<count($data);$i++)
		{
			$w=$this->widths[$i];
			$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
			//Save the current position
			$x=$this->GetX();
			$y=$this->GetY();
			//Draw the border
			$this->Rect($x, $y, $w, $h);
			//Print the text
			$this->SetFillColor(255,255,255); 
			$this->MultiCell($w, 5, $data[$i], 0, $a, true);
			//Put the position to the right of the cell
			$this->SetXY($x+$w, $y);
		}
		//Go to the next line
		$this->Ln($h);
	}
	
	function CheckPageBreak($h)
	{
		//If the height h would cause an overflow, add a new page immediately
		if($this->GetY()+$h>($this->PageBreakTrigger-83))
		{
			$this->AddPage($this->CurOrientation);
			$this->SetMargins(6,20,20);
			$this->Ln(12);
			$GLOBALS['paginas']++;
		}
	}
	
	function NbLines($w, $txt)
	{
		//Computes the number of lines a MultiCell of width w will take
		$cw=&$this->CurrentFont['cw'];
		if($w==0)
			$w=$this->w-$this->rMargin-$this->x;
		$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
		$s=str_replace("\r", '', $txt);
		$nb=strlen($s);
		if($nb>0 and $s[$nb-1]=="\n")
			$nb--;
		$sep=-1;
		$i=0;
		$j=0;
		$l=0;
		$nl=1;
		while($i<$nb)
		{
			$c=$s[$i];
			if($c=="\n")
			{
				$i++;
				$sep=-1;
				$j=$i;
				$l=0;
				$nl++;
				continue;
			}
			if($c==' ')
				$sep=$i;
			$l+=$cw[$c];
			if($l>$wmax)
			{
				if($sep==-1)
				{
					if($i==$j)
						$i++;
				}
				else
					$i=$sep+1;
				$sep=-1;
				$j=$i;
				$l=0;
				$nl++;
			}
			else
				$i++;
		}
		return $nl;
	}
	
	function mes($m){
		$m = $m*1;
		$mesLetras = array('','Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic');
		return $mesLetras[$m];
	}
	
	function CalculaAntiguedadSAT( $fecha, $fechaFinal ) {
		$fecha1 = new DateTime($fecha);
		$fecha2 = new DateTime($fechaFinal);
		$fecha = $fecha1->diff($fecha2);
		$anio = $fecha->y;
		$mes = $fecha->m;
		$dia = $fecha->d;
		$return = "P";
		if($anio>0)
			$return.= $anio."Y";
		if($mes>0)
			$return.= $mes."M";
		$return.= $dia."D";
		return $return;
	}
	
	function formateaFechaSLASH($fecha){
		$arr = explode("/",$fecha);
		$fechaNueva = $arr[2]."-".$arr[1]."-".$arr[0];
		return $fechaNueva;	
	}
	
	function formateaFecha($fecha){
		$arr = explode("-",$fecha);
		$fechaNueva = $arr[2]."-".$arr[1]."-".$arr[0];
		return $fechaNueva;	
	}
	
	function Header(){
		//INSERTAMOS EL LOGO
		$this->Image('logo.jpg',50,10,50); //LOGO
			
		$this->Ln(-2);
		#DATOS DEL CFDI		
		$this->SetFont('Arial','b',12);
		$this->Cell(50,5,'',0,0,'L');
		$this->Cell(72,5,'COTIZACION',0,0,'L');
		$this->Cell(72,5,date("Ymd-his"),0,0,'R');
		$this->Ln(4);
		$this->SetFont('Arial','',8);
		$this->Cell(50,5,'',0,0,'L');
		$this->Cell(72,5,'LUISA MARIA RUIZ VARGAS',0,0,'L');
		$this->SetFont('Arial','b',8);
		$this->Cell(72,5,'Fecha y Hora de Emision',0,0,'R');
		$this->Ln(4);
		$this->SetFont('Arial','',8);
		$this->Cell(50,5,'',0,0,'L');
		$this->Cell(72,5,'RUVL810825597',0,0,'L');
		$this->Cell(72,5,date("Y-m-d").'T'.date("H:i:s"),0,0,'R');
		$this->Ln(4);
		$this->Cell(50,5,'',0,0,'L');
		$this->Cell(72,5,'AV.9A. SUR ORIENTE 503-A, COL. OBRERA C.P. 29080',0,0,'L');
		$this->Cell(72,5,'',0,0,'R');
		$this->Ln(4);
		$this->Cell(50,5,'',0,0,'L');
		$this->Cell(72,5,'TUXTLA GUTIERREZ, CHIAPAS',0,0,'L');
		$this->Cell(72,5,'',0,0,'R');
		$this->Ln(4);
		$this->Cell(50,5,'',0,0,'L');
		$this->Cell(72,5,'VENDEDOR: '.$vendnombre,0,0,'L');
		$this->Cell(72,5,'',0,0,'R');
		$this->Ln(4);
		$this->Cell(50,5,'',0,0,'L');
		$this->Cell(40,5,'TELEFONO: '.$vendtelefono,0,0,'L');
		$this->Cell(104,5,'EMAIL: '.$vendemail,0,0,'L');
		$this->Ln(4);
		
		$this->Ln(2);
		
		//DATOS DEL EMPLEADO
		$this->SetFont('Arial','b',16);
		$this->MultiCell(194,5,'RECIBO DE DINERO',1,'C');
		$this->SetFont('Arial','b',8);
		$this->Cell(15,5,"Nombre:",0,0,'L');
		$this->SetFont('Arial','',8);
		$this->Cell(179,5,strtoupper(utf8_decode($nombre)).' '.strtoupper(utf8_decode($apellidos)),0,0,'L');
		$this->Ln(4);
		$this->SetFont('Arial','b',8);
		$this->Cell(15,5,"Direccion:",0,0,'L');
		$this->SetFont('Arial','',8);
		$this->Cell(179,5,strtoupper(utf8_decode($direccion)),0,0,'L');
		$this->Ln(4);
		$this->SetFont('Arial','b',8);
		$this->Cell(15,5,"R.F.C:",0,0,'L');
		$this->SetFont('Arial','',8);
		$this->Cell(55,5,trim(strtoupper(utf8_decode($rfc))),0,0,'L');
		$this->SetFont('Arial','b',8);
		$this->Cell(25,5,"Telefono: ".$telefono,0,0,'L');
		$this->SetFont('Arial','',8);
		$this->Cell(37,5,'',0,0,'L');		
		$this->SetFont('Arial','b',8);
		$this->Cell(25,5,"",0,0,'L');
		$this->SetFont('Arial','',8);
		$this->Cell(37,5,'',0,0,'L');
		
	}
	
	function Footer(){
		//PIE DE PÁGINA
	}
	
	}
	
	
	date_default_timezone_set("America/Mexico_City");
	$subtotal = 0;
	$iva = 0;
		$rfc = $nombre = $apellidos= $direccion = $cp = $telefono = $vendnombre= $vendtelefono = $vendemail = $vendcodigo = $sucursal= "";
		$articulos  = array();
		if ( isset( $_POST['cotizacion'] ) ) {
			$cotizaciones = CotizacionController::getDetalleCotizacionesUsuario($_SESSION['credenciales']['usuario']);
			$cotizacionSeleccionada = $cotizaciones[$_POST['cotizacion']];	
			if ( isset($cotizacionSeleccionada) ) {
				$rfc = $cotizacionSeleccionada['rfc'];
				$nombre = $cotizacionSeleccionada['nombre'];
				$apellidos = $cotizacionSeleccionada['apellido'];
				$direccion = $cotizacionSeleccionada['direccion'];
				$cp = $cotizacionSeleccionada['cp'];
				$telefono = $cotizacionSeleccionada['telefono'];
				$vendnombre = $cotizacionSeleccionada['nombreVendedor'];
				$vendtelefono = $cotizacionSeleccionada['telefonoVendedor'];
				$vendemail = $cotizacionSeleccionada['emailVendedor'];
				$articulos = json_decode( json_encode($cotizacionSeleccionada['items']) );
				$modeloSucursal = new Sucursales;
				$detalleSucursal = $modeloSucursal->getSucursal( $cotizacionSeleccionada['sucursal'] );
				if ( isset($detalleSucursal[0]) ) {
					$sucursal = $detalleSucursal[0]->ALMACEN;
				}else{
					echo "404";
				}
			} else {
				echo "404";
			}
			
		}else{
				$articulos = $_POST['items'];
				$articulos =json_decode( json_encode($articulos) );
	
				$rfc = $_POST['rfc'];
				$nombre = $_POST['name'];
				if ( $nombre == "" || isset($_POST['items']) == 0) {
					exit();
				}				
				$apellidos = $_POST['surname'];
				$direccion = $_POST['dir'];
				$telefono = $_POST['phone'];
				$cp = $_POST['zc'];
				$vendcodigo = $_POST['codevendor'];
				$vendnombre = $_POST['namevendor'];
				$vendtelefono = $_POST['mobile'];
				$vendemail = $_POST['email'];
				$sucursal = $_POST['store'];

		}

	
	$items = new Articulos;
	
	ob_start();
	$pdf=new PDF('P','mm','Letter');
	$pdf->Open();
	$pdf->AddPage();
	$pdf->SetMargins(10,10,10);	
	$pdf->SetAutoPageBreak(true);	
	$pdf->Ln(6);
	$pdf->SetFont('Arial','b',8);
	$pdf->Cell(194,5,"CONCEPTOS",1,1,'C');
	$pdf->Cell(20,5,"CODIGO",'L',0,'C');
	$pdf->Cell(94,5,"DESCRIPCION",0,0,'C');
	$pdf->Cell(20,5,"PRECIO",0,0,'C');
	
	$pdf->Cell(20,5,"CANTIDAD",0,0,'C');	
	$pdf->Cell(20,5,"IVA",0,0,'C');	
	$pdf->Cell(20,5,"IMPORTE",'R',1,'C');
	$pdf->SetAligns(array('C','L','R','C','R','R'));
	$itemsarray = array();
	$subtotal = 0;
	foreach($articulos as $articulo){
		
		$item = $items->getInfoArticulo($articulo->codigo);
		
		if ( $_SESSION['credenciales']['type'] === 'ext') {
			$subtotal += ($item[0]->PVP1 /1.16 ) * $articulo->cantidad;
		} else {
			$subtotal += ($articulo->precio /1.16 ) * $articulo->cantidad;
		}
		
		
		$iva += ($articulo->precio -($articulo->precio /1.16  ) ) * $articulo->cantidad;
		$nb=0;	
		$nb = $pdf->NbLines(94, $articulo->descripcion);
		$h=5*$nb;
		$pdf->CheckPageBreak($h);
		//Issue a page break first if needed
		$pdf->CheckPageBreak($h);
		$pdf->SetWidths(array(20,94,20,20,20,20));
		$pdf->SetHeights(5);
		$pdf->SetFonts(array(array('Arial','',8),array('Arial','',8),array('Arial','',8),array('Arial','',8),array('Arial','',8),array('Arial','',8)));
		$pdf->SetBorders(array('L',0,0,0,0,'R'));
		
		if ( $_SESSION['credenciales']['type'] === 'ext') {
			$pdf->Row(array($item[0]->CODIGO,$item[0]->DESCRIPCION,number_format($item[0]->PVP1 /1.16 ,2,".","," ), $articulo->cantidad,number_format(($item[0]->PVP1 -($item[0]->PVP1 /1.16  ) ) * $articulo->cantidad,2,".","," ),number_format(($item[0]->PVP1 /1.16 ) * $articulo->cantidad,2,".",",")));			
			$articulo->precio = $item[0]->PVP1;
			$itemsarray[] = array('codigo'=>$item[0]->CODIGO,
								  'descripcion'=>$item[0]->DESCRIPCION,
								  'precio'=>number_format($item[0]->PVP1 /1.16 ,2,".","," ),
								  'cantidad'=>$articulo->cantidad,
								  'iva'=>number_format(($item[0]->PVP1 -($item[0]->PVP1 /1.16  ) ) * $articulo->cantidad,2,".","," ),
								  'importe'=>number_format(($item[0]->PVP1 /1.16 ) * $articulo->cantidad,2,".",","));
		}
	   else{
			$item = $items->getInfoArticulo($articulo->codigo);
			$pdf->Row(array($articulo->codigo,$articulo->descripcion,number_format($articulo->precio /1.16 ,2,".","," ), $articulo->cantidad,number_format(($articulo->precio -($articulo->precio /1.16  ) ) * $articulo->cantidad,2,".","," ),number_format(($articulo->precio /1.16 ) * $articulo->cantidad,2,".",",")));			
			$itemsarray[] = array('codigo'=>$articulo->codigo,
								  'descripcion'=>$articulo->descripcion,
								  'precio'=>number_format($articulo->precio /1.16 ,2,".","," ),
								  'cantidad'=>$articulo->cantidad,
								  'iva'=>number_format(($articulo->precio -($articulo->precio /1.16  ) ) * $articulo->cantidad,2,".","," ),
								  'importe'=>number_format(($articulo->precio /1.16 ) * $articulo->cantidad,2,".",","));
	   }
	}
	
	if ( !isset( $_POST['cotizacion'] ) ) {
	
		$dataArticulos = array('rfc'=>$rfc,
						'nombre'=>$nombre,
						'apellidos'=>$apellidos,
						'direccion'=>$direccion,
						'telefono'=>$telefono,
						'cp'=>$cp,
						'usuario'=>$_SESSION['credenciales']['usuario'],
						'subtotal'=>$subtotal,
						'iva'=>$iva,
						'total'=>($subtotal + $iva),
						'vendcodigo'=>$vendcodigo,
						'vendnombre'=>$vendnombre,
						'sucursal'=>$sucursal,
						'vendtelefono'=>$vendtelefono,
						'vendemail'=>$vendemail,
						'items'=>$itemsarray);
		$items->insertaCotizacion($dataArticulos);
	}

	
	$pdf->SetFont('Arial','b',8);
	$pdf->Cell(154,5,"Observaciones:",'LTR',0,'L');
	$pdf->Cell(20,5,"Subtotal:",'T',0,'R');	
	$pdf->Cell(20,5,number_format($subtotal,2,'.',','),'TR',0,'R');
	$pdf->Ln(4);
	$pdf->Cell(154,5,"",'LR',0,'R');
	$pdf->Cell(20,5,"Descuento:",'',0,'R');	
	$pdf->Cell(20,5,number_format($iva,2,'.',','),'R',0,'R');
	$pdf->Ln(4);
	$pdf->Cell(154,5,"",'LBR',0,'R');
	$pdf->Cell(20,5,"Total:",'B',0,'R');	
	$pdf->Cell(20,5,number_format($subtotal + $iva,2,".",","),'RB',0,'R');
	$pdf->Ln(6);
	$pdf->Cell(194,5,strtoupper($pdf->num2letras(number_format($subtotal + $iva,2,".",""))),0,0,'L');
		
		
	//$pdf->AutoPrint(true);
	$fechaGenerado = date("d-m-YTH-i-s");
	$nombreRecibo = $rfc."-".$fechaGenerado.".pdf";
	$pdf->Output(dirname(__DIR__)."/Recibos/$nombreRecibo");
	echo "/eshop/Controladores/Recibos/$nombreRecibo";
	
?>