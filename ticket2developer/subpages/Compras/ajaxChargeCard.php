<?php
	session_start();
	date_default_timezone_set('America/Guayaquil');
	ini_set('display_startup_errors',1);
	ini_set('display_errors',1);
	error_reporting(-1);
	
	require_once('../../classes/private.db.php');
	$hoy = date("Y-m-d H:i:s");
	$gbd = new DBConn();
	
	require_once('../../../stripeconDora/lib/Stripe.php');
	Stripe::setApiKey('sk_live_LhXwe4EcfHj0hVSnN5WiWhbt');//anterior
	// Stripe::setApiKey('sk_live_gCWu5c8j5joFo3UGzONi7aTt');
	// Stripe::setApiKey('sk_test_6GSRONySlv39WktvlN4ZloaH');

	$quien_vendio_boleto = 1;
	$valorPago = 2;
	
	
	$token = $_REQUEST['token'];
	$concert_id = (int)$_REQUEST['concert_id'];
	// $loc_id = (int)$_REQUEST['idLocal'];
	$total = number_format(($_REQUEST['total']),2);
	$cantidad = $_REQUEST['cant'];
	// $idcli = 59; //$_SESSION['id'];
	$idcli = $_SESSION['id'];
	
	
	
	$select1 = 'SELECT * FROM Cliente WHERE idCliente = "'.$idcli.'" ';
	// echo $select1;
	$slt1 = $gbd -> prepare($select1);
	$slt1 -> execute();
	$row1 = $slt1 -> fetch(PDO::FETCH_ASSOC);
	$envio = $row1['strEnvioC'];
	$dir = $row1['strDireccionC'];
	$strDocumentoC = $row1['strDocumentoC'];
	
	$sql = "SELECT * FROM Concierto WHERE idConcierto = ?";
	$res = $gbd -> prepare($sql);
	$res -> execute(array($concert_id));
	$row = $res -> fetch(PDO::FETCH_ASSOC);
	$preciocents = ($total * 100);
	$evento = $row['strEvento'];
	$fecha = $row['dateFecha'];
	$lugar = $row['strLugar'];
	$hora = $row['timeHora'];
	$pagopor = 1;
	$tiene_permisos = $row['tiene_permisos'];
	
	$cobroExitoso = false;

	try{
		$charge = Stripe_Charge::create(array(
			'amount' => $preciocents,
			'currency' => 'usd',
			'card' => $token,
			'description' => $evento
			));

		$cobroExitoso = true;
		$purchaseID = $charge->id;
		// echo 'purchase id token : '.$purchaseID;
		
	}
	catch(Exception $e){
		$error = $e->getMessage();
		// echo $error;
		$cobroExitoso = false;
		// exit;
	}
	include '../../conexion.php';
	
	$dir1 = $_REQUEST['dir1'];
	$tel1 = $_REQUEST['tel1'];
	$cel1 = $_REQUEST['cel1'];
	$ident1 = $_REQUEST['ident1'];
	// $cobroExitoso = true;
	
	
	if($cobroExitoso === true){
		if($ident1 == 1){
			$sqlUC = 'update Cliente set strDireccionC = "'.$dir1.'" , strTelefonoC  = "'.$tel1.'", intTelefonoMovC = "'.$cel1.'" where idCliente = "'.$idcli.'" ';
			// echo $sqlUC;
			$resUC = mysql_query($sqlUC) or die (mysql_error());
		}
		$status_boleto = 1;
		$fechahoy = date('Y-m-d H:i:s');
		$fecha = date('Y-m-d');
		$selectpreventa = "SELECT * FROM Concierto WHERE dateFechaPreventa >= ? AND idConcierto = ?";
		$resSelectpreventa = $gbd -> prepare($selectpreventa);
		$resSelectpreventa -> execute(array($fechahoy,$concert_id));
		$numpreventa = $resSelectpreventa -> rowCount();
		if($numpreventa > 0){
			$descompra = 1;
		}else{
			$descompra = 2;
		}
		$counter = 1;
		$idboletoVendido = '';
		foreach(explode('@',$_REQUEST['valFor']) as $valor){	
			$exVal = explode('|',$valor);
		}
		
		$sqlFa = '	INSERT INTO factura (id, tipo, rand, id_cli, idConc , localidad , valor , estadoPV , estadopagoPV , ndepo , fecha ) 
					VALUES (NULL, "4", "", "'.$idcli.'" , "'.$concert_id.'" , "'.$exVal[0].'" , "'.$total.'" , "stripe" , "pagado" , "'.$ident1.'" , "'.$hoy.'")';
		
		$resFa = mysql_query($sqlFa) or die (mysql_error());
		$idFactura = mysql_insert_id();
		
		// $valor2 = explode('@',$_REQUEST['valFor']);
		$valor = explode('@',$_REQUEST['valFor']);
		
		// print_r($valor);
		function obtenerFechaEnLetra($fecha){

			$dia= conocerDiaSemanaFecha($fecha);

			$num = date("j", strtotime($fecha));

			$anno = date("Y", strtotime($fecha));

			$mes = array('enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre');

			$mes = $mes[(date('m', strtotime($fecha))*1)-1];

			return $dia.', '.$num.' de '.$mes.' del '.$anno;

		}

		 

		function conocerDiaSemanaFecha($fecha) {

			$dias = array('Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado');

			$dia = $dias[date('w', strtotime($fecha))];

			return $dia;

		}
		$hora = date('H:i:s');
		for($i=0;$i<=count($valor)-1;$i++){
			//echo $i."<br>";
			$exVal = explode('|',$valor[$i]);
			// print_r($exVal)." los datos <br/>";
			$localidad = $exVal[0];
			// echo $localidad."<br>";
			
			$sqlB = 'select max(CAST(serie AS INTEGER)) as serieB from Boleto where idCon = "'.$concert_id.'"  order by idBoleto DESC limit 1';
			// echo $sqlB."<br>";
			$resB = mysql_query($sqlB) or die (mysql_error());
			$rowB = mysql_fetch_array($resB);
			
			if($rowB['serieB'] == null || $rowB['serieB'] == '' ){
				$numeroSerie = 1;
			}else{
				$numeroSerie = ($rowB['serieB'] + 1);
			}
			
			$sqlB1 = 'select max(CAST(serie_localidad AS INTEGER)) as serieB from Boleto where idCon = "'.$concert_id.'"  and idLocB = "'.$exVal[0].'" order by idBoleto DESC limit 1';
			// echo $sqlB1."<br/>";
			$resB1 = mysql_query($sqlB1) or die (mysql_error());
			$rowB1 = mysql_fetch_array($resB1);
			
			if($rowB1['serieB'] == null || $rowB1['serieB'] == '' ){
				$numeroSerie_localidad = 1;
			}else{
				$numeroSerie_localidad = ($rowB1['serieB'] + 1);
			}
		
		
		
		
			$sqlLo = 'select * from Localidad where idLocalidad = "'.$exVal[0].'" ';
			// echo $sqlLo."<br>";
			
			
			
			$resLo = mysql_query($sqlLo) or die (mysql_error());
			$rowLo = mysql_fetch_array($resLo);
			$strCaracteristicaL = $rowLo['strCaracteristicaL'];
			
			
			// echo 'hola'.$strCaracteristicaL;
			if($strCaracteristicaL == 'Asientos no numerados'){
				$valorAsientos = 0;
				$asientoss = "Asientos no Numerados";
				$asientoss2 = $exVal[2]."Asientos no Numerados";
			}elseif($strCaracteristicaL == 'Asientos numerados'){
				$valorAsientos = 1;
				$asientoss = "Fila-".$exVal[6]."-Silla".$exVal[7];
				$asientoss2 = $exVal[2]."Fila-".$exVal[6]."-Silla".$exVal[7];
			}
			
			// echo $valorAsientos."<br>";
			
			if($valorAsientos == 1){
				$boletosok = 'SELECT * FROM ocupadas WHERE row = "'.$exVal[6].'" AND col = "'.$exVal[7].'" AND local = "'.$exVal[0].'" AND concierto = "'.$concert_id.'"';
				// echo $boletosok;
				$bolok = $gbd -> prepare($boletosok);
				$bolok -> execute();
				$numbolok = $bolok -> rowCount();
				if($numbolok > 0){
					echo 'error_ocupadas';
					return false;
				}
			
				
				$insertBol = 'INSERT INTO ocupadas VALUES ("NULL" , "'.$exVal[6].'" , "'.$exVal[7].'" , "'.$status_boleto.'" , "'.$exVal[0].'" , "'.$concert_id.'" , "'.$pagopor.'" , "'.$descompra.'")';
				// echo $insertBol;
				$res3 = mysql_query($insertBol) or die (mysql_error());
			}	
				// $resultInsertBol = $gbd -> prepare($insertBol);
				// $resultInsertBol -> execute(array('NULL',$exVal[6],$exVal[7],$status_boleto,$exVal[0],$concert_id,$pagopor,$descompra));
				
				$sqlCod_bar = 'SELECT * FROM `codigo_barras` WHERE id_con = "'.$concert_id.'" and utilizado = 0 and id_loc = "'.$exVal[0].'" order by id ASc ';
				$resCod_bar = mysql_query($sqlCod_bar) or die (mysql_error());
				$rowCod_bar = mysql_fetch_array($resCod_bar);
				$code = $rowCod_bar['codigo'];
				// echo $code."  este es el codigo q se insertara .<br><br>";
				
				if($tiene_permisos == 0){
					$identComprador = 1;
				}elseif($tiene_permisos > 0){
					$identComprador = 2;
					
				}
				
				
				
				$hoy = date("Y-m-d");
				$dateFechaPreventa = $row['dateFechaPreventa'];
				if($hoy <= $dateFechaPreventa){
					$espreventa = 1;
				}else{
					$espreventa = 0;
				}
				
				if(isset($_SESSION['id_area_mapa'])){
					$seccion = $_SESSION['id_area_mapa'];
				}else{
					$seccion = 1;
				}
				$query1 = 'INSERT INTO Boleto VALUES ("NULL","'.$seccion.'","'.$code.'",'.$idcli.','.$concert_id.','.$exVal[0].',"'.$valorPago.'","'.$quien_vendio_boleto.'", "0", "'.$espreventa.'", "0" , "","'.$strDocumentoC.'","A","S","'.$numeroSerie.'", "'.$numeroSerie_localidad.'", "'.$identComprador.'" , "0" , "'.$exVal[8].'" , "'.$exVal[9].'" , "'.$fecha.'" , "'.$hora.'" , "11" , "1" )';
				// echo $query1."<<  >>".$i."<br><br>"; 
				$res1 = mysql_query($query1) or die (mysql_error());
				$idboleto = mysql_insert_id();
				// echo $idboleto."<<>><br>";
				
				
				if($exVal[10] == 1){
					$sqlCM = 'INSERT INTO `compras_membresias` (`id`, `id_cli`, `id_loc`, `id_con`, `valor`, `id_desc`, `canti`, `fecha` , `id_boleto` , `tipo` ) 
						  VALUES (NULL, "'.$idcli.'" , "'.$exVal[0].'" , "'.$concert_id.'" , "'.$exVal[9].'" , "'.$exVal[8].'" , "1" , "'.$hoy.'" , "'.$idboleto.'" , "'.$exVal[11].'")';
					$resCM = mysql_query($sqlCM) or die(mysql_error());
				}
				
				
				if($ident1 == 1){
					$sqlDo = '	insert into domicilio (rowD , colD , statusD , localD , conciertoD , clienteD , boletoD , pagoporD , domicilioHISD , nombreHISD , documentoHISD) 
								values ("'.$exVal[6].'" , "'.$exVal[7].'",  "'.$hoy.'" , "'.$exVal[0].'" , "'.$concert_id.'" , "'.$idcli.'" , "'.$idboleto.'" , "stripe" , "'.$dir1.'" , "'.$tel1.'" , "'.$idFactura.'")';
					$resDo = mysql_query($sqlDo) or die (mysql_error());
				}
				
				$sqlUpCodBar = 'update codigo_barras set utilizado = "1" where codigo = "'.$code.'" ';
				$resUpCodBar = mysql_query($sqlUpCodBar);
			
			
				$hoy = date("Y-m-d");
				
				
				$urlbar = 'http://ticketfacil.ec/ticket2/codigo_de_barras.php?barcode=$code';
				$imgbar = 'barcodeStrype/'.$code.'.png';
				file_put_contents($imgbar, file_get_contents($urlbar));
				include '../../conexion.php';
				
				
				if($tiene_permisos == 0){
					$identComprador = 1;
				}elseif($tiene_permisos > 0){
					$identComprador = 2;
					$hoy = date("Y-m-d H:i:s");
					
				}
				
				
				
				$sqlL = 'select * from Localidad where idLocalidad = "'.$exVal[0].'" ';
				$resL = mysql_query($sqlL) or die (mysql_error());
				$rowL = mysql_fetch_array($resL);
				
				
				$detalleBoleto = 'INSERT INTO detalle_boleto (idBoleto, localidad, asientos, precio) VALUES ("'.$idboleto.'" , "'.$rowL['strDescripcionL'].'" , "'.$asientoss.'" , "'.$exVal[9].'")';
				// echo $detalleBoleto."<br><br>";
				$res = mysql_query($detalleBoleto) or die (mysql_error());
				
				$idboletoVendido .= $idboleto."|";
				//echo $idboletoVendido;
				
				$fecha = date('Y-m-d');
				$hora = date('H:i:s');
				
				$sqlDT = '	INSERT INTO `detalle_tarjetas` (`idcon`, `idloc`, `idbol`, `idcli`, `fecha`, `hora`, `tipo`, `valor`, `id_fact`) 
							VALUES ("'.$concert_id.'", "'.$exVal[0].'", "'.$idboleto.'", "'.$idcli.'", "'.$fecha.'", "'.$hora.'", "stripe|token:'.$token.'|purchaseID:'.$purchaseID.'" , "'.$exVal[9].'" , "'.$idFactura.'")';
				// echo $sqlDT."<br><br>";
				$resDT = mysql_query($sqlDT) or die (mysql_error());
		
				//echo $idboletoVendido;
				
				$content = '
						<page>
							<div style="border:1px solid #ccc;border-radius:10px;width:500px;margin:0 auto;">
							<table align="center" style="width:100%; border-collapse:separate; border-spacing:15px 5px;font-size:11px;">
								<tr>
									<td style="text-align:center;">
										<img src="http://www.lcodigo.com/ticket/imagenes/ticketfacilnegro.png" width="120px"/>
									</td>
								</tr>
								<tr>
									<td style="text-align:center;">
										Estimado <strong style ="text-transform:uppercase;">'.utf8_decode($row1['strNombresC']).'</strong> 
										<br/>Esto es un comprobante de compra en l&iacute;nea :
									</td>
								</tr>
								<tr>
									<td>
										* Sus asientos estan Pagados<br>
										Ud pago mediante  <strong>Stripe ( tarjeta de credito )</strong>, <br>
										
										el dia : '.obtenerFechaEnLetra($fecha).', a las : '.$hora.'
									</td>
								</tr>';
							if($ident1 != 1){	
								$content .= '		
										<tr>
											<td>
												* Para el evento de :<center><h3><strong>'.$evento.'</strong><h3></center>
												<br>
												En la localidad de : '.$asientoss2.'
												<br/><br/>
												
												
												'.$row['dircanjeC'].'
												
												<br><br>
												
												Por favor debe portar este documento impreso , y su documento de identidad al momento del canje<br/>
											</td>
										</tr>';
							}	
							else{
								$content .= '	
									<tr>
										<td>
											* Para el evento de :<center><h3><strong>'.$evento.'</strong><h3></center>
											En la localidad de : '.$asientoss2.'<br><br>
											Sus tickets seran enviados a la siguente direccion : "'.$dir1.'"<br>
											mediante el sistema de envios de servientrega <br>
											Su numero de contacto ingresado es : "'.$cel1.'"<br>
											nosotros le notificaremos a su numero de celular : '.$tel1.' <br>
											cuando el envio se haya realizado.
										</td>
									</tr>';

							}
							$content .= '	
								<tr>
									<td valign="middle" align="center">
										* Su c&oacute;digo de compra es el siguiente:<br/>
										<img src="http://ticketfacil.ec/ticket2/subpages/Compras/barcodeStrype/'.$code.'.png" /><br/>
										 <span style="color:#EC1867;"><strong>'.$code.'</strong></span>
									</tr>
								</tr>

								<tr>
									<td style="text-align:center;">
										<strong>Gracias por Preferirnos</strong>
										<br>
										<strong>TICKETFACIL <I>"La mejor experiencia de compra En L&iacute;nea"</I></strong>
									</td>
								</tr>
							</table>
							</div>
						</page>';
				// echo $content."<br>";
				
				require_once '../../../PHPM/PHPM/class.phpmailer.php';
				require_once '../../PHPM/PHPM/class.smtp.php';
				
				$email = $row1['strMailC'];//$_SESSION['usermail'];
				// echo $email."destinatario";
				
				
				
				$ownerEmail = 'info@ticketfacil.ec';
				$subject = 'TICKET para '.$evento.'';
				$mail = new PHPMailer();
				$mail->IsSMTP();
				$mail->SMTPAuth = true;
				$mail->SMTPSecure = "ssl";
				$mail->Host = "smtp.gmail.com"; 
				$mail->Port = 465;
				$mail->Username = "info@ticketfacil.ec";
				$mail->Password = "ticketfacil2012";
				$mail->AddReplyTo($ownerEmail,'TICKETFACIL');
				$mail->SetFrom($ownerEmail,'TICKETFACIL');
				$mail->AddCC("fabricio@practisis.com", "copia de compra desde punto de venta");
				$mail->From = $ownerEmail;
                $mail->AddAddress($email,'Cliente');

				$mail->AddAddress($ownerEmail,'TICKETFACIL');     // Add a recipient
				$mail->FromName = 'TICKETFACIL';
				$mail->Subject = $subject;
				$mail->MsgHTML($content);
				//$mail->AddAttachment('pdf/'.$timeRightNow.'.pdf'); // attachment
			
				if(!$mail->Send()){
					// echo "Mailer Error: " . $mail->ErrorInfo;
				}
				else{
					
				}
			
		}
		echo 'ok';
		
	}else{
		echo'no_se_cobro';
	}
?>