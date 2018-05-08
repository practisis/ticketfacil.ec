
<?php
	if($_SESSION['autentica'] == 'uzx153'){
		echo '<input type="hidden" id="session" value="ok"/>';
	}else{
		echo '<input type="hidden" id="session" value="error"/>';
	}
	
	$gbd = new DBConn();
	
	$idcon = $_GET['con'];
	echo "<input type = 'hidden' id = 'con' value = '".$idcon."' ";
	$estadoCon = 'Activo';
	$hoy = date("Y-m-d");
	$estadoLoc = 'A';
	//Consulta para obtener todos los datos del concierto
	$sqlcon = "SELECT * FROM Concierto WHERE idConcierto = ? AND strEstado = ?";
	$sqlc = $gbd -> prepare($sqlcon);
	$sqlc -> execute(array($idcon,$estadoCon));
	$row = $sqlc -> fetch(PDO::FETCH_ASSOC);
	if($row['es_publi'] == 1){
		$displayEsPub = 'style="display:block;"';
		$displayEsPub2 = '';
	}else{
		$displayEsPub = 'style="display:none;"';
		$displayEsPub2 = 'display:none;';
	}
	
	
	//consulta para obtener el precio normal de los tickets comparando con la fecha
	$selectPrecio = "SELECT strDescripcionL, doublePrecioL, idLocalidad FROM Localidad JOIN Concierto ON Localidad.idConc = Concierto.idConcierto 
			WHERE dateFecha >= ? AND idConc = ? AND strEstadoL = ? ORDER BY dateFecha ASC";
	$sltprecio = $gbd -> prepare($selectPrecio);
	$sltprecio -> execute(array($hoy,$idcon,$estadoLoc));
	$resultadoPrecio = $sltprecio -> rowCount(); 
	
	//consulta para obtener el precio de reserva de los tickets comparando con la fecha
	$selectReserva = "SELECT strDescripcionL, doublePrecioReserva, idLocalidad FROM Localidad JOIN Concierto ON Localidad.idConc = Concierto.idConcierto 
				WHERE dateFechaReserva >= ? AND idConc = ? AND strEstadoL = ? ORDER BY dateFecha ASC";
	$sltreserva = $gbd -> prepare($selectReserva);
	$sltreserva -> execute(array($hoy,$idcon,$estadoLoc));
	$resultadoReserva = $sltreserva -> rowCount();
	echo '<input type="hidden" id="num_rows_reserva" value="'.$resultadoReserva.'" />';
	
	//consulta para obtener el precio de preventa de los tickets comparando con la fecha
	$selectPreventa = '	SELECT strDescripcionL, doublePrecioPreventa, idLocalidad 
						FROM Localidad 
						JOIN Concierto 
						ON Localidad.idConc = Concierto.idConcierto 
						WHERE dateFechaPreventa >= "'.$hoy.'" 
						AND idConc = "'.$idcon.'" 
						AND strEstadoL = "'.$estadoLoc.'" 
						ORDER BY dateFecha ASC';
	//echo $selectPreventa;
	$sltpreventa = $gbd -> prepare($selectPreventa);
	$sltpreventa -> execute();
	$resultadoPreventa = $sltpreventa -> rowCount();
	echo '<input type="hidden" id="num_rows_preventa" value="'.$resultadoPreventa.'" />';
	
	if(($resultadoReserva > 0) && ($resultadoPreventa == 0)){
		$colst = 'col-xs-3';
		$colsn = 'col-xs-3';
		$colsr = 'col-xs-3';
		$colsp = 'col-xs-0';
		$colsm = 'col-xs-3';
	}else if(($resultadoReserva == 0) && ($resultadoPreventa == 0)){
		$colst = 'col-xs-5';
		$colsn = 'col-xs-5';
		$colsr = 'col-xs-0';
		$colsp = 'col-xs-0';
		$colsm = 'col-xs-2';
	}else if(($resultadoReserva == 0) && ($resultadoPreventa > 0)){
		$colst = 'col-xs-3';
		$colsn = 'col-xs-3';
		$colsr = 'col-xs-0';
		$colsp = 'col-xs-3';
		$colsm = 'col-xs-1';
	}else{
		$colst = 'col-xs-4';
		$colsn = 'col-xs-2';
		$colsr = 'col-xs-2';
		$colsp = 'col-xs-2';
		$colsm = 'col-xs-2';
	}
	//echo $colsm."aqui";
	//obtengo el id de todas las localidades
	$selectIdLocal = "SELECT idLocalidad FROM Localidad WHERE idConc = ? AND strEstadoL = ?";
	$sltidlocal = $gbd -> prepare($selectIdLocal);
	$sltidlocal -> execute(array($idcon,$estadoLoc));
	
	//datos para los elementos del mapa
	$query6 = "SELECT idLocalidad FROM Localidad WHERE idConc = ? AND strEstadoL = ?";
	$stmt6 = $gbd -> prepare($query6);
	$stmt6 -> execute(array($idcon,$estadoLoc));
	
	//datos para botones
	$query7 = "SELECT idLocalidad FROM Localidad WHERE idConc = ? AND strEstadoL = ?";
	$stmt7= $gbd -> prepare($query7);
	$stmt7 -> execute(array($idcon,$estadoLoc));
	
	//selecciono las descripciones de las localidades
	$selectDesLocal = "SELECT strDescripcionL, idLocalidad FROM Localidad WHERE idConc = ? AND strEstadoL = ?";
	$sltdeslocal = $gbd -> prepare($selectDesLocal);
	$sltdeslocal -> execute(array($idcon,$estadoLoc));
	
	//concateno el path de la carpeta de origen para mostrar la imagen
	$img = $row['strImagen'];
	$ruta = 'https://www.ticketfacil.ec/ticket2/spadmin/';
	$r = $ruta.$img;
	
	echo $r." hola";
	
	//Selecciono los artistas dependiendo el concierto
	$estadoArt = 'Activo';
	$sqlart = "SELECT * FROM Artista WHERE intIdConciertoA = ? AND strEstadoA = ?";
	$resart = $gbd -> prepare($sqlart);
	$resart -> execute(array($idcon,$estadoArt));
	
	//Selecciono las butacas dependiendo del concierto
	$estadoBut = 'A';
	$selectButaca = "SELECT * FROM Butaca WHERE intConcB = ? AND strEstado = ?" or die(mysqli_error($mysqli));
	$resultSelectButaca = $gbd -> prepare($selectButaca);
	$resultSelectButaca -> execute(array($idcon,$estadoBut));
	
	//Select para obtener las areas de cada localidad
	$selectAreas = "SELECT strCoordenadasB, datestateL, datafullL, intLocalB, intConcB, idButaca FROM Butaca WHERE intConcB = ? AND strEstado = ? and createbyB > 0 ";
	$resultSelectAreas = $gbd -> prepare($selectAreas);
	$resultSelectAreas -> execute(array($idcon,$estadoBut));
	
	//select id por localidades de las areas 
	$selectId = 'SELECT intAsientosB, idLocalidad, strImgL, strSecuencial FROM Butaca JOIN Localidad ON Butaca.intLocalB = Localidad.idLocalidad WHERE intConcB = "'.$idcon.'" AND strEstado = "'.$estadoBut.'"';
	//echo $selectId;
	
	$resultId = $gbd -> prepare($selectId);
	$resultId -> execute();
	
	
	$selectCronReserva = "SELECT dateFechaReserva, dateFechaPreventa, timeHora FROM Concierto WHERE idConcierto = ? AND strEstado = ?";
	$resultCronReserva = $gbd -> prepare($selectCronReserva);
	$resultCronReserva -> execute(array($idcon,$estadoCon));
	$rowCronReserva = $resultCronReserva -> fetch(PDO::FETCH_ASSOC);
	$fechareserva = $rowCronReserva['dateFechaReserva'];
	$hora = ($rowCronReserva['timeHora']);
	$fechaCreacionC = ($rowCronReserva['fechaCreacionC']);
	// echo $hora."<br/>";
	// $horaMenos = '12:00:00';
	// echo $horaMenos."<br/>";
	
	// $horaFinal = ($hora - $horaMenos);
	
	// echo $horaFinal;
	
	
	
	
	$fechapreventa = $rowCronReserva['dateFechaPreventa'];
	$time = time();
	$fecha1 = new DateTime(date("Y-m-d H:i:s",$time));
	$fecha2 = new DateTime($fechareserva.' '.$hora);
	$fecha = $fecha1->diff($fecha2);
	
	
	$horaActual = date("H:i:s");   
	$explodeHora = explode(":",$horaActual);
	$hora1 = $explodeHora[0];
	$min1 = $explodeHora[1];
	$seg1 = $explodeHora[2];
	
	//echo $hora1."-".$min1."-".$seg1."<br>";

	$horaFin1 = (23 - $hora1);
	$minFin1 = (59 - $min1);
	$segFin1 = (59 - $seg1);
	
	$horaFinPrev = $horaFin1.":".$minFin1.":".$segFin1;
	
	//echo $horaFinPrev;
	echo '<input type="hidden" id="segundos" value="'.$fecha->s.'" />';
	echo '<input type="hidden" id="minutos" value="'.$fecha->i.'" />';
	echo '<input type="hidden" id="horas" value="'.$fecha->h.'" />';
	echo '<input type="hidden" id="dias" value="'.$fecha->d.'" />';
	
	$fecha3 = new DateTime(date("Y-m-d H:i:s",$time));
	$fecha4 = new DateTime($fechapreventa.' '.$fechaCreacionC);
	$fechap = $fecha3->diff($fecha4);
	//echo $fecha3."hola".$fecha4;
	echo '<input type="hidden" id="segundosventa" value="'.$fechap->s.'" />';
	echo '<input type="hidden" id="minutosventa" value="'.$fechap->i.'" />';
	echo '<input type="hidden" id="horasventa" value="'.$fechap->h.'" />';
	echo '<input type="hidden" id="diasventa" value="'.$fechap->d.'" />';
	
	echo '<input type="hidden" id="data" value="3" />';
	
	$anio = date('Y');
	$anio = $anio - 10;
?>
<style>
	.secciones{
		background-color:#f8e316; 
		color:#000; 
		font-size:45px; 
		display:inline-block; 
		height:100px;
		opacity:0.9; 
		border:1px solid #000; 
		cursor:pointer;
	}
	.secciones:hover{
		background-color:#67cdf5;
	}
	.clockCountdownFootItem{
		color:#000;
	}

	.asiento_vasio:hover{
		background-color:#00AEEF;
		color:#fff;
	}
</style>
<?php
	echo $row['es_publi'];
	if($row['es_publi'] == 1){
?>
<div style="background-color:#282d2b; margin:10px 20px -10px 20px; text-align:center;">
	<div class="breadcrumb">
		<a class="active" id="chooseseat" onclick = 'verMapaAbajo();' style = 'cursor:pointer;'>Escoge tu asiento</a>
		<a id="identification" href="#" onclick="security()">Identificate</a>
		<a id="buy" href="#" onclick="security()">Resumen de Compra</a>
		<a id="pay" href="#" onclick="security()">Pagar</a>
		<a id="confirmation" href="#" onclick="security()">Confirmaci&oacute;n</a>
	</div>
</div>
<?php
	}
?>
<div style='text-align:center;' id = 'ocultar'>
	<!--<div class="breadcrumb" style = 'width:81.5%;text-align:left;background-color:#3C3C3B;padding-left:20px;padding-top:10px;padding-bottom:10px'>
		<i class="fa fa-caret-right fa-2x" aria-hidden="true" style = 'color:#FFED00;'></i><span style = 'color:#FFED00;font-size:16.83pt;'>&nbsp;&nbsp;&nbsp;&nbsp;Tu Artista</span>
	</div>-->
	<div class="breadcrumb" >
		<div class="row">
			<div class="col-lg-12" style = 'margin:0px;' >
				<img src="<?php echo $r; ?>" alt = '' class="img-responsive" style = 'width:100%;' />
				<!--<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
					<div class="carousel-inner" role="listbox">
						<div class="item active">
							
						</div>
						<div class="item">
							<iframe class="video embed-responsive-item" width="100%" height = '300px' src="<?php echo $row["strVideoC"];?>" id = 'videoEventoss' frameborder="0"></iframe>
						</div>
					</div>
				</div>-->
			</div>
		</div><br/><br/>
		<div class="row">
			<div class="col-lg-6" style = 'padding-left:20px;margin:0px;text-align:left;'>
				<div style = ''>
					<font color="#3CBBEB" size="6">
						<p style="vertical-align:middle;padding-left:2%;">
							<strong style = 'padding-left: 3px;padding-right: 3px;padding-bottom: 10px;border-bottom: 1px solid #3CBBEB'>
								<?php echo utf8_encode($row["strEvento"]);?>
							</strong>
						</p>
					</font>
				</div>
				<div style="width: 300px;text-align: left;color: #fff;padding-left:3%">
					<span style = 'color:#009FE3;'>Fecha:</span> <?php echo $row["dateFecha"];?><br>
					<span style = 'color:#009FE3;'>Hora:</span> <?php echo $row["timeHora"];?><br>
					<span style = 'color:#009FE3;'>Lugar:</span> <?php echo $row["strLugar"];?><br>
				</div>
				<div class="row">
					<div class = 'col-md-12' >
					<?php while($rowart = $resart -> fetch(PDO::FETCH_ASSOC)){?>
						<div class = 'row' >
							<div class="col-xs-5" style = 'padding-top: 8px'>
								<strong style = 'font-size:19pt;color:#fff;'><?php echo $rowart['strNombreA'];?></strong>
							</div>
							<div class="col-xs-1" style='padding:3px;' onclick="window.open('<?php echo $rowart['strFacebookA']; ?>','_blank');">
								<br><img src="imagenes/faceNuevo.png" style="width:100%; max-width:50px;cursor:pointer;" />
							</div>
							<div class="col-xs-1" style='padding:3px;' onclick="window.open('<?php echo $rowart['strTwitterA']; ?>','_blank');">
								<br><img src="imagenes/twNuevo.png" style="width:100%; max-width:50px;cursor:pointer;" />
							</div>
							<div class="col-xs-1" style='padding:3px;' onclick="window.open('<?php echo $rowart['strYoutubeA']; ?>','_blank');">
								<br><img src="imagenes/ytNuevo.png" style="width:100%; max-width:50px;cursor:pointer;" />
							</div>
							<div class="col-xs-1" style='padding:3px;' onclick="window.open('<?php echo $rowart['strInstagramA']; ?>','_blank');">
								<br><img src="imagenes/inNuevo.png" style="width:100%; max-width:50px;cursor:pointer;" />
							</div>
							<!--<div class="col-xs-1">
								<a href="<?php echo $rowart['strTwitterA'];?>"><img src="imagenes/twitter.jpg" style="width:100%; max-width:50px;" /></a>
							</div>
							<div class="col-xs-1">
								<a href="<?php echo $rowart['strYoutubeA'];?>"><img src="imagenes/youtube.jpg" style="width:100%; max-width:50px;" /></a>
							</div>
							<div class="col-xs-1">
								<a href="<?php echo $rowart['strInstagramA'];?>"><img src="imagenes/instagram.jpg" style="width:100%; max-width:50px;" /></a>
							</div>-->
						</div>
					<?php }?>
					</div>
				</div>
			</div>
			<div class="col-lg-6 sm-12" style = 'padding: 0px;margin: 0px'>
				<p>
					<div style="color: #fff;letter-spacing:0.5px;font-size:10pt;padding-left:20px;text-align:left;">
						<?php 
							function ReemplazarTildes($texto){
								$texto=str_replace("á","&aacute;",$texto);
								$texto=str_replace("é","&eacute;",$texto);
								$texto=str_replace("í","&iacute;",$texto);
								$texto=str_replace("ó","&oacute;",$texto);
								$texto=str_replace("ú","&uacute;",$texto);
								$texto=str_replace("ñ","&ntilde;",$texto);
								$texto=str_replace("Á","&Aacute;",$texto);
								$texto=str_replace("É","&Eacute;",$texto);
								$texto=str_replace("Í","&Iacute;",$texto);
								$texto=str_replace("Ó","&Oacute;",$texto);
								$texto=str_replace("Ú","&Uacute;",$texto);
								$texto=str_replace("Ñ","&Ntilde;",$texto);
								return $texto;
							}
							echo utf8_decode(ReemplazarTildes(nl2br($row['strDescripcion'])));
						?>
					</div>
				</p><br>
				<div class = 'row'>
					<!--<div class = 'col-lg-4'>
						<a href="whatsapp://send?text=https://https://ticketfacil.ec/ticket2/?modulo=des_concierto&con=<?php echo $_REQUEST['con'];?>" data-text="BOTÓN COMPARTIR EN WHATSAPP EN MI PÁGINA WEB" data-action="share/whatsapp/share" class="miestilo" style="border: none; margin: 10px 0; font-size: 16px;">
							<img border="0" src="img/wts.png" width="64px" height="64px"> Compartir
						</a>
					</div>-->
					<div class = 'col-lg-4'>
						<a href="https://twitter.com/share" class="twitter-share-button" data-url="https://ticketfacil.ec/ticket2/?modulo=des_concierto&con=<?php echo $_REQUEST['con'];?>" data-text="Ticketfacil.ec" data-via="ticketfacilec" data-dnt="true">Tweet</a>
						<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^https:/.test(d.location)?'https':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
					</div>
					<div class = 'col-lg-4'>
						<div id="fb-root"></div>
						<script>(function(d, s, id) {
						var js, fjs = d.getElementsByTagName(s)[0];
						if (d.getElementById(id)) return;
						js = d.createElement(s); js.id = id;
						js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1";
						fjs.parentNode.insertBefore(js, fjs);
						}(document, 'script', 'facebook-jssdk'));</script>

						<!-- Your share button code -->
						<div class="fb-share-button" 
						data-href="https://ticketfacil.ec/ticket2/?modulo=des_concierto&con=<?php echo $_REQUEST['con'];?>" 
						data-layout="button_count">
						</div>
					</div>
				</div>
				<?php
					if($row['es_publi'] == 2){
				?>
					<a href ='<?php echo $row['dircanjeC'];?>'> Compra tu boleto aqui</a>
				<?php
					}
				?>
			</div>
		</div>
	</div>
	<div class="breadcrumb" id = 'posicionMapa' style = 'width:81.5%;text-align:left;background-color:#3C3C3B;padding-left:20px;padding-top:10px;padding-bottom:10px;<?php echo $displayEsPub2;?>'>
		<i class="fa fa-caret-right fa-2x" aria-hidden="true" style = 'color:#FFED00;'></i>
		<span style = 'color:#FFED00;font-size:16.83pt;'>
			&nbsp;&nbsp;&nbsp;&nbsp;Escoge tu localidad en el mapa o en la tabla de precios
		</span>
	</div>
	<div class="breadcrumb" style = 'width:100%;text-align:left;background-color:#fff;'>
		<div class="row">
			<?php if(($resultadoReserva > 0) || ($resultadoPreventa > 0)){?>
			<div class="row" <?php echo $displayEsPub;?>>
				
				<div class="col-lg-7" >
					<?php
						$dir = 'https://www.ticketfacil.ec/ticket2/spadmin/';
						$imagen = $row['strMapaC'];
						$ruta_mapa = $dir.$imagen;
					?>
					<img id="localmapa" src="<?php echo $ruta_mapa?>" alt="localmapa" usemap="#localmapa" style="width: 550px; height: 415px; position: absolute; left: 0px; top:0px; padding: 0; opacity: 0.0;" />
					<map name="localmapa" width='300px'>
						<?php while($rowArea = $resultSelectAreas -> fetch(PDO::FETCH_ASSOC)){?>
							<area href="#" data-state="<?php echo $rowArea['datestateL'];?>" data-full="<?php echo $rowArea['datafullL'];?>" shape="poly" coords="<?php echo $rowArea['strCoordenadasB'];?>" onclick="asientos_mapa('<?php echo $rowArea['intLocalB'];?>','<?php echo $rowArea['intConcB'];?>')" alt />
						<?php }?>
					</map>
				</div>
				
		
				<div class="col-lg-4" style="color:#fff;">
					<input type="hidden" id="concierto" name="concierto" value="<?php echo $idcon;?>" />
					
					<div class="row" style="color:#fff;">
						<table style = 'color:#000;border: 1px solid #000;' width = '100%'>
						<tr>
							<td style = 'margin:0px; text-align:left;background-color:#3C3C3B;color:#fff;padding-top:5px;padding-bottom:5px;padding-left: 5px;border-right:1px solid #fff;'>
								<span><strong>LOCALIDADES</strong></span>
							</td>
							<td style = 'margin:0px; text-align:left;background-color:#3C3C3B;color:#fff;padding-top:5px;padding-bottom:5px;padding-left: 5px;border-right:1px solid #fff;'>
								<span><strong>DETALLE</strong></span>
							</td>
							<td style = 'margin:0px; text-align:center;background-color:#3C3C3B;color:#fff;padding-top:5px;padding-bottom:5px;padding-left: 5px;border-right:1px solid #fff;'>
								<span><strong>VALOR</strong></span>
							</td>
							<td style = 'margin:0px; text-align:;background-color:#3C3C3B;color:#fff;padding-top:5px;padding-bottom:5px;'>
								
							</td>
						</tr>
						<?php
							include 'conexion.php';
							$sqlLo = 'select * from Localidad where idConc = "'.$idcon.'" and createbyL > 0';
							//echo $sqlLo;
							$resLo = mysql_query($sqlLo) or die (mysql_error());
							while($rowLo = mysql_fetch_array($resLo)){
						?>
							<tr>
								<td style = 'padding-left: 5px;border: 1px solid #000;'>
									<?php echo $rowLo['strDescripcionL'];?>
									<input type="hidden" id="local<?php echo $rowLo['idLocalidad'];?>" value="<?php  echo $rowLo['strDescripcionL'];?>" />
								</td>
								<td style = 'padding-left: 5px;border: 1px solid #000;'>
									<span style='font-size:10px;'><?php echo $rowLo['strCaracteristicaL'];?></span>
								</td>
								<td style = 'padding-left: 5px;border: 1px solid #000;'>
									<?php 
										$hoy = date("Y-m-d");
										$dateFechaPreventa = $row['dateFechaPreventa'];
										if($hoy <= $dateFechaPreventa){
											echo $rowLo['doublePrecioPreventa'];
										}else{
											echo $rowLo['doublePrecioL'];
										}
										
									?>
								</td>
								<td style = 'padding-left: 5px;border: 1px solid #000;'>
									<span class="label label-success" style="cursor:pointer;background-color:#18B8EB;color:#fff;" onclick="asientos_mapa('<?php echo $rowLo['idLocalidad'];?>','<?php echo $idcon;?>');">Abrir</span>
								</td>
							</tr>
						<?php
							}
							
						?>
						</table>
					</div>
					<!--<div class="row" style=''>
						<?php if($resultadoReserva > 0){?>
						<div class = 'col-lg-12'><h4 style = 'color:#000;'>Tiempo limite para RESERVAR boletos:</h4></div>
						<div id="clock" style = 'position: relative; left: 20px'></div>
						<?php }?>
					</div>-->
					
						<?php
							$hoy = date("Y-m-d");
							$dateFechaPreventa = $row['dateFechaPreventa'];
							echo $hoy."<=".$dateFechaPreventa;
							if($hoy <= $dateFechaPreventa){
								$espreventa = 'display:block;';
							}else{
								$espreventa = 'display:none;';
							}
						?>
						<div class="row" style = '<?php echo $espreventa?>'>
							<div class = 'col-lg-12'><h4 style = 'color:#000;'>Tiempo limite de compra por PREVENTA:</h4></div>
							<div id="clock2" style = 'position: relative; left: 20px'></div>
						</div>
						<?php 
							
						?>
					
				</div>
			</div>
			
			<?php }else{?>
			<div class="row" <?php echo $displayEsPub;?>>
				
				<div class="col-lg-7" >
					<?php
						$dir = 'https://www.ticketfacil.ec/ticket2/spadmin/';
						$imagen = $row['strMapaC'];
						$ruta_mapa = $dir.$imagen;
					?>
					<img id="localmapa" src="<?php echo $ruta_mapa?>" alt="localmapa" usemap="#localmapa" style="width: 550px; height: 415px; position: absolute; left: 0px; top:0px; padding: 0; opacity: 0.0;" />
					<map name="localmapa" width='300px'>
						<?php while($rowArea = $resultSelectAreas -> fetch(PDO::FETCH_ASSOC)){?>
							<area href="#" data-state="<?php echo $rowArea['datestateL'];?>" data-full="<?php echo $rowArea['datafullL'];?>" shape="poly" coords="<?php echo $rowArea['strCoordenadasB'];?>" onclick="asientos_mapa('<?php echo $rowArea['intLocalB'];?>','<?php echo $rowArea['intConcB'];?>')" alt />
						<?php }?>
					</map>
				</div>
				
		
				<div class="col-lg-4" style="color:#fff;">
					<input type="hidden" id="concierto" name="concierto" value="<?php echo $idcon;?>" />
					
					<div class="row" style="color:#fff;">
						<table style = 'color:#000;border: 1px solid #000;' width = '100%'>
							<tr>
								<td style = 'margin:0px; text-align:left;background-color:#3C3C3B;color:#fff;padding-top:5px;padding-bottom:5px;padding-left: 5px;border-right:1px solid #fff;'>
									<span><strong>LOCALIDADES</strong></span>
								</td>
								<td style = 'margin:0px; text-align:;background-color:#3C3C3B;color:#fff;padding-top:5px;padding-bottom:5px;padding-left: 5px;border-right:1px solid #fff;'>
									<span><strong>VALOR</strong></span>
								</td>
								<td style = 'margin:0px; text-align:;background-color:#3C3C3B;color:#fff;padding-top:5px;padding-bottom:5px;'>
									
								</td>
							</tr>
						<?php
							include 'conexion.php';
							$sqlLo = 'select * from Localidad where idConc = "'.$idcon.'" and doublePrecioL > 0 and createbyL > 0 order by strDescripcionL ASC';
							$resLo = mysql_query($sqlLo) or die (mysql_error());
							while($rowLo = mysql_fetch_array($resLo)){
						?>
							<tr>
								<td style = 'padding-left: 5px;border: 1px solid #000;'>
									<?php echo $rowLo['strDescripcionL'];?>
									<input type="hidden" id="local<?php echo $rowLo['idLocalidad'];?>" value="<?php  echo $rowLo['strDescripcionL'];?>" />
								</td>
								<td style ='padding-left: 5px;border: 1px solid #000;'>
									<?php echo $rowLo['doublePrecioL'];?>
								</td>
								<td style = 'padding-left: 5px;border: 1px solid #000;'>
									<span class="label label-success" style="cursor:pointer;background-color:#18B8EB;color:#fff;" onclick="asientos_mapa('<?php echo $rowLo['idLocalidad'];?>','<?php echo $idcon;?>');">Abrir</span>
								</td>
							</tr>
						<?php
							}
							
						?>
						</table>
					</div>
					<div class="row" style=''>
						<?php if($resultadoReserva > 0){?>
						<div class = 'col-lg-12'><h4>Tiempo limite para RESERVAR boletos:</h4></div>
						<div id="clock" style='position: relative; left: 20px'></div>
						<?php }?>
					</div>
					<div class="row" style=''>
						<?php if($resultadoPreventa > 0){?>
						<div class = 'col-lg-12'><h4>Tiempo limite de compra por PREVENTA:</h4></div>
						<div id="clock2" style = 'position: relative; left: 20px'></div>
						<?php }?>
					</div>
				</div>
			</div>
			
			<?php }?>
		</div>
	</div>
</div>
	<div id = 'contieneNoNumerados' style = 'display:none;' >
		<center><h2 id = 'nombrelocaldiadNoNumerados' style = 'padding-top:0px;color:#fff;' ></h2></center>
		<table class = 'table' style = 'width:80%;' align = 'center' >
			<tr>
				<td>
					<label style = 'font-size:1.4em;color:#fff;' >Por favor Seleccione el Numero de Boletos a comprar</label>
					<input type = 'hidden' id = 'precioLocalidadSinNum' />
					<input type = 'hidden' id = 'localidadSinNum' />
				</td>
				<td>
					<select class = 'form-control comboSinNumeracion' onchange='asignaSinNum()' id = 'sinNumeracion' >
						<option value = '0' >Seleccione...</option>
					<?php
						for($k=1;$k<=21;$k++){
							if($k==1){
								$txtOp = '';
							}else{
								$txtOp ='(s)';
							}
					?>
						<option value = '<?php echo $k;?>' ><?php echo $k;?> Boleto <?php echo $txtOp;?></option>
					<?php
						}
					?>
					</select>
				</td>
			</tr>
		</table>
	</div>
<script>
	function asignaSinNum(){
		$('#seleccion').html('');
		var sinNumeracion = $('#sinNumeracion').val();
		//alert(sinNumeracion);
		var precioLocalidadSinNum = $('#precioLocalidadSinNum').val();
		var localidadSinNum = $('#localidadSinNum').val();
		var nomLoc = $('#nomLoc').text();
		var l = 0;
		for(var m = 1; m <= sinNumeracion; m++){
			l++
			$('#seleccion').append('<tr class = "filaSinNumeracion_'+m+' asientosescogidos">\
										<td style="text_align:center; border:none;">\
											<center>\
												<font color="#000">\
													<input name="codigo[]" class="added" value="'+localidadSinNum+'" readonly="readonly" size="5" type="text">\
													<input class="added" name="row[]" value="0" type="hidden">\
													<input class="added" name="col[]" value="0" type="hidden">\
												</font>\
											</center>\
										</td>\
										<td>\
											<center><font color="#000"><input name="chair[]" class="added" value="'+m+'" readonly="readonly" size="15" type="text"></font></center>\
										</td>\
										<td>\
											<center><font color="#000"><input name="des[]" class="added" value="'+nomLoc+'" readonly="readonly" size="10" type="text"></font></center>\
										</td>\
										<td>\
											<center><font color="#000"><input name="num[]" class="added" value="1" readonly="readonly" size="10" type="text"></font></center>\
										</td>\
										<td>\
											<center><font color="#000"><input name="pre[]" class="added" value="'+precioLocalidadSinNum+'" readonly="readonly" size="10" type="text"></font></center>\
											\
											<input name="id_desc[]" class="added" value="0" readonly="readonly" type="hidden"/>\
											<input name="val_desc[]" class="added" value="'+precioLocalidadSinNum+'" readonly="readonly" type="hidden"/>\
										</td>\
										<td>\
											<center><font color="#000"><input name="tot[]" class="added" value="'+precioLocalidadSinNum+'" readonly="readonly" size="10" type="text"></font></center>\
										</td>\
										<td>\
											<center><font color="#000"><button type="button" class="btn_eliminar" id="delete86" onclick="elimanarFila('+m+')">Eliminar</button></font></center>\
										</td>\
									</tr>');
		}
		
	}
	function elimanarFila(id){
		$('.filaSinNumeracion_'+id).remove();
	}
	function redimencionVideo(){
		var widthVideo = $('#contieneVideo').width();
		var heightVideo = $('#contieneVideo').height();
		$('#videoEvento').css('width',widthVideo);
		$('#videoEvento').css('height',heightVideo);
		//alert(heightVideo)
	}
	$( document ).ready(function() {
		console.log( "ready!" );
		redimencionVideo();
	});
	
	//alert($("#descuentos_web").length);
	 
	function descuentos_web_limitados(){
		// alert('hola')
		var nombre = $('#descuentos_web option:selected').attr('nombre');
		var valor = $('#descuentos_web option:selected').attr('valor');
		var id_desc = $('#descuentos_web option:selected').attr('id_desc');
		// alert(nombre);
		$( "input[name='pre[]']" ).val(valor);
		$( "input[name='tot[]']" ).val(valor);
		$( "input[name='id_desc[]']" ).val(id_desc);
		$( "input[name='val_desc[]']" ).val(valor);
	}
	$(window).resize(function(){
		redimencionVideo();
	});
</script>

<script>  
                (function(d,s,id) {  
                    var js, fjs = d.getElementsByTagName(s)[0];  
                    if(d.getElementById(id)) return;  
                    js = d.createElement(s); js.id = id;  
                    js.src = "https://connect.facebook.net/en_US/sdk.js";  
                    fjs.parentNode.insertBefore(js, fjs);  
                }(document, 'script', 'facebook-jssdk'));  
      
				window.fbAsyncInit = function() {
					FB.init({
						appId      : '420099668326698',
						xfbml      : true,
						version    : 'v2.8'
					});
					FB.AppEvents.logPageView();
				};
      
                function ingresar() {  
                    FB.login(function(response){  
                        validarUsuario();  
                    }, {scope: 'email'});  
                }  
                function validarUsuario() {  
                    FB.getLoginStatus(function(response) {  
                        if(response.status == 'connected') {  
                            FB.api(
								'/me',
								'GET',
								{"fields":"id,name,birthday,email,gender,permissions"},
								function(response) {
									//alert(JSON.stringify(response));
									var id_face = response.id;
									var email_face = response.email;
									var nombre = response.name;
									var birthday = response.birthday;
									var gender = response.gender;
									$('#nombreUsuFace').html('!!!  ' + nombre);
									$('#nombreUsuEmail').html('!!!  ' + email_face);
									$('#nombreUsuPass').html('!!!  ' + id_face);
									$('#fecha').html('!!!  ' + birthday);
									$('#sexo').html('!!!  ' + gender);
									$.post("autenticaFb.php",{ 
										id_face : id_face , email_face : email_face , nombre : nombre , birthday : birthday , gender : gender
									}).done(function(data){
										accion ='?modulo=comprar&con='+con;
										$('#form').attr('action',accion);
										$('#form').submit();
									});
								}
							);
                        } else if(response.status == 'not_authorized') {  
                            alert('Debes autorizar la app!');  
                        } else {  
                            alert('Debes ingresar a tu cuenta de Facebook!');  
                        }  
                    });  
               }  
      
            </script>  
			
			
<div style="">
	<!--<div id="ocultar">
		<h3 style="color:#fff; text-align:center;" id="titulo1">Escoge tu localidad en el "MAPA" o en la "Tabla de PRECIOS"</h3>
		<div style="background-color:#00AEEF; margin:0px -32px 50px 0px; position:relative;">
			<?php if(($resultadoReserva > 0) || ($resultadoPreventa > 0)){?>
			<div class="row">
				<div class="col-lg-7" style="margin:0px;">
					<?php
						$dir = 'https://www.ticketfacil.ec/ticket2/spadmin/';
						$imagen = $row['strMapaC'];
						$ruta_mapa = $dir.$imagen;
					?>
					<img id="localmapa" src="<?php echo $ruta_mapa?>" alt="localmapa" usemap="#localmapa" style="width: 550px; height: 415px; position: absolute; left: 0px; top:0px; padding: 0; opacity: 0.0;" />
					<map name="localmapa">
						<?php while($rowArea = $resultSelectAreas -> fetch(PDO::FETCH_ASSOC)){?>
							<area href="#" data-state="<?php echo $rowArea['datestateL'];?>" data-full="<?php echo $rowArea['datafullL'];?>" shape="poly" coords="<?php echo $rowArea['strCoordenadasB'];?>" onclick="asientos_mapa('<?php echo $rowArea['intLocalB'];?>','<?php echo $rowArea['intConcB'];?>')" alt />
						<?php }?>
					</map>
				</div>
				<div class="col-lg-5" style="color:#fff; margin:10% 0px 0px;">
					<div class="row">
						<?php if($resultadoReserva > 0){?>
						<h4><strong>Tiempo limite para RESERVAR boletos:</strong></h4>
						<div id="clock"></div>
						<?php }?>
					</div>
					<div class="row">
						<?php if($resultadoPreventa > 0){?>
						<h4><strong>Tiempo limite de compra por PREVENTA:</strong></h4>
						<div id="clock2"></div>
						<?php }?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-7" style="margin:0px; padding:20px 5px 40px 10px;">
					<input type="hidden" id="concierto" name="concierto" value="<?php echo $idcon;?>" />
					<div class="row">
						<div class="col-xs-12" style="margin:0px; text-align:center; border:1px solid #fff">
							<font size="5"><p style="margin-top:15px; color:#fff;"><strong>LOCALIDADES</strong></p></font>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12" style="margin:0px; text-align:center; border:1px solid #fff; color:#fff;">
							<span><strong>PRECIOS</strong></span>
						</div>
					</div>
					<div class="row" style="color:#fff;">
						<div class="<?php echo $colst;?>" style="margin:0px; border:1px solid #fff">
							<div class="row">
								<div class="col-xs-11">
									<strong>Tis</strong>
								</div>
							</div>
							<?php while($row5 = $sltdeslocal -> fetch(PDO::FETCH_ASSOC)){?>
							<div class="row">
								<div class="col-xs-11">
									<?php echo $row5['strDescripcionL'];?>
									<input type="hidden" id="local<?php echo $row5['idLocalidad'];?>" value="<?php  echo $row5['strDescripcionL'];?>" />
								</div>
							</div>
							<?php }?>
						</div>
						<div class="<?php echo $colsn;?>" style="margin:0px; border:1px solid #fff">
							<div class="row">
								<div class="col-xs-11">
									<strong>$Normal</strong>
								</div>
							</div>
							<?php while($row1 = $sltprecio -> fetch(PDO::FETCH_ASSOC)){?>
							<div class="row">
								<div class="col-xs-11">
									<?php echo $row1['doublePrecioL'];?>
								</div>
							</div>
							<?php }?>
						</div>
						<?php if($resultadoReserva > 1){?>
						<div class="<?php echo $colsr;?>" style="margin:0px; border:1px solid #fff">
							<div class="row">
								<div class="col-xs-11">
									<strong>$Reserva</strong>
								</div>
							</div>
							<?php while($row2 = $sltreserva -> fetch(PDO::FETCH_ASSOC)){?>
							<div class="row">
								<div class="col-xs-11">
									<?php echo $row2['doublePrecioReserva'];?>
								</div>
							</div>
							<?php }?>
						</div>
						<?php }?>
						<?php if($resultadoPreventa > 1){?>
						<div class="<?php echo $colsp;?>" style="margin:0px; border:1px solid #fff">
							<div class="row">
								<div class="col-xs-11">
									<strong>$Preventa</strong>
								</div>
							</div>
							<?php while($row3 = $sltpreventa -> fetch(PDO::FETCH_ASSOC)){?>
							<div class="row">
								<div class="col-xs-11">
									<?php echo $row3['doublePrecioPreventa'];?>
								</div>
							</div>
							<?php }?>
						</div>
						<?php }?>
						<div class="<?php echo $colsm;?>" style="margin:0px; border:1px solid #fff; text-align:center;">
							<div class="row">
								<div class="col-xs-11" style="margin-left:2px;">
									<strong>Ver</strong>
								</div>
							</div>
							<?php while($row4 = $sltidlocal -> fetch(PDO::FETCH_ASSOC)){?>
							<div class="row">
								<div class="col-xs-11" style="margin-left:2px;">
									<span class="label label-success" style="cursor:pointer;" onclick="asientos_mapa('<?php echo $row4['idLocalidad'];?>','<?php echo $idcon;?>');">Abrir</span>
								</div>
							</div>
							<?php }?>
						</div>
					</div>
				</div>
				<div class="col-lg-5" style="margin:0px;">
					<p>
						<div style="border: 1px solid #fff; margin:20px 100px 20px 10px; padding:15px 0px 2px 8px;">
							<font color="#fff" size="6"><p style="vertical-align:middle"><strong><?php echo $row["strEvento"];?></strong></p></font>
						</div>
					</p>
					<p>
						<div style="background-color:#171A1B; margin:0px 100px 20px 10px; padding:10px 0px 10px 10px; color:#fff; font-weight:700;">
							<span>Fecha: <?php echo $row["dateFecha"];?></span><br>
							<span>Hora: <?php echo $row["timeHora"];?></span><br>
							<span>Lugar: <?php echo $row["strLugar"];?></span>
						</div>
					</p>
					<p>
						<div style="padding-left:15px; padding-right:100px; color:#fff; margin-bottom:20px; letter-spacing:0.5px;">
							<?php echo utf8_encode($row["strDescripcion"]);?>
						</div>
					</p>
				</div>
			</div>
			<?php }else{?>
			<div class="row">
				<div class="col-lg-7" style="margin:0px;">
					<?php
						$dir = 'https://www.ticketfacil.ec/ticket2/spadmin/';
						$imagen = $row['strMapaC'];
						$ruta_mapa = $dir.$imagen;
					?>
					<img id="localmapa" src="<?php echo $ruta_mapa?>" alt="localmapa" usemap="#localmapa" style="width: 550px; height: 415px; position: absolute; left: 0px; top:0px; padding: 0; opacity: 0.0;" />
					<map name="localmapa">
						<?php while($rowArea = $resultSelectAreas -> fetch(PDO::FETCH_ASSOC)){?>
							<area href="#" data-state="<?php echo $rowArea['datestateL'];?>" data-full="<?php echo $rowArea['datafullL'];?>" shape="poly" coords="<?php echo $rowArea['strCoordenadasB'];?>" onclick="asientos_mapa('<?php echo $rowArea['intLocalB'];?>','<?php echo $rowArea['intConcB'];?>')" alt />
						<?php }?>
					</map>
				</div>
				<div class="col-lg-5" style="margin:0px;">
					<p>
						<div style="border: 1px solid #fff; margin:20px 100px 20px 10px; padding:15px 0px 2px 8px;">
							<font color="#fff" size="6"><p style="vertical-align:middle"><strong><?php echo $row["strEvento"];?></strong></p></font>
						</div>
					</p>
					<p>
						<div style="background-color:#171A1B; margin:0px 100px 20px 10px; padding:10px 0px 10px 10px; color:#fff; font-weight:700;">
							<span>Fecha: <?php echo $row["dateFecha"];?></span><br>
							<span>Hora: <?php echo $row["timeHora"];?></span><br>
							<span>Lugar: <?php echo $row["strLugar"];?></span>
						</div>
					</p>
					<p>
						<div style="padding-left:15px; padding-right:100px; color:#fff; margin-bottom:20px; letter-spacing:0.5px;">
							<?php echo utf8_encode($row["strDescripcion"]);?>
						</div>
					</p>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-12" style="margin:0px; padding:20px 150px 40px;">
					<input type="hidden" id="concierto" name="concierto" value="<?php echo $idcon;?>" />
					<div class="row">
						<div class="col-xs-12" style="margin:0px; text-align:center; border:1px solid #fff">
							<font size="5"><p style="margin-top:15px; color:#fff;"><strong>LOCALIDADES</strong></p></font>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12" style="margin:0px; text-align:center; border:1px solid #fff; color:#fff;">
							<span><strong>PRECIOS</strong></span>
						</div>
					</div>
					<div class="row" style="color:#fff;">
						<div class="<?php echo $colst;?>" style="margin:0px; border:1px solid #fff">
							<div class="row">
								<div class="col-xs-11" style="text-align:center;">
									<strong>Tipo</strong>
								</div>
							</div>
							<?php while($row5 = $sltdeslocal -> fetch(PDO::FETCH_ASSOC)){?>
							<div class="row">
								<div class="col-xs-11">
									<?php echo $row5['strDescripcionL'];?>
									<input type="hidden" id="local<?php echo $row5['idLocalidad'];?>" value="<?php  echo $row5['strDescripcionL'];?>" />
								</div>
							</div>
							<?php }?>
						</div>
						<div class="<?php echo $colsn;?>" style="margin:0px; border:1px solid #fff">
							<div class="row">
								<div class="col-xs-11" style="text-align:center;">
									<strong>$Normal</strong>
								</div>
							</div>
							<?php while($row1 = $sltprecio -> fetch(PDO::FETCH_ASSOC)){?>
							<div class="row">
								<div class="col-xs-11">
									<?php echo $row1['doublePrecioL'];?>
								</div>
							</div>
							<?php }?>
						</div>
						<?php if($resultadoReserva > 1){?>
						<div class="<?php echo $colsr;?>" style="margin:0px; border:1px solid #fff">
							<div class="row">
								<div class="col-xs-11" style="text-align:center;">
									<strong>$Reserva</strong>
								</div>
							</div>
							<?php while($row2 = $sltreserva -> fetch(PDO::FETCH_ASSOC)){?>
							<div class="row">
								<div class="col-xs-11">
									<?php echo $row2['doublePrecioReserva'];?>
								</div>
							</div>
							<?php }?>
						</div>
						<?php }?>
						<?php if($resultadoPreventa > 1){?>
						<div class="<?php echo $colsp;?>" style="margin:0px; border:1px solid #fff">
							<div class="row">
								<div class="col-xs-11" style="text-align:center;">
									<strong>$Preventa</strong>
								</div>
							</div>
							<?php while($row3 = $sltpreventa -> fetch(PDO::FETCH_ASSOC)){?>
							<div class="row">
								<div class="col-xs-11">
									<?php echo $row3['doublePrecioPreventa'];?>
								</div>
							</div>
							<?php }?>
						</div>
						<?php }?>
						<div class="<?php echo $colsm;?>" style="margin:0px; border:1px solid #fff; text-align:center;">
							<div class="row">
								<div class="col-xs-11" style="margin-left:2px;">
									<strong>Ver</strong>
								</div>
							</div>
							<?php while($row4 = $sltidlocal -> fetch(PDO::FETCH_ASSOC)){?>
							<div class="row">
								<div class="col-xs-11" style="margin-left:2px;">
									<span class="label label-success" style="cursor:pointer;" onclick="asientos_mapa('<?php echo $row4['idLocalidad'];?>','<?php echo $idcon;?>');">Abrir</span>
								</div>
							</div>
							<?php }?>
						</div>
					</div>
				</div>
			</div>
			<?php }?>
			<!--<div class="tra_info_concierto"></div>
			<div class="par_info_concierto"></div>
		</div>
		
		<div style="background-color:#515557; margin:20px; height:3px;"></div>
		<div class="row">
			<div class="col-lg-6" style="background-color:#00AEEF; padding-left:20px; color:#fff; font-size:22px;">
				<span><strong>Redes Sociales de los Artistas</strong></font></span>
			</div>
		</div>
		<div class="row">
			<?php while($rowart = $resart -> fetch(PDO::FETCH_ASSOC)){?>
				<div class="col-xs-3" style="margin:-10px 0 0 40px;">
					<h3 style="color:#fff;"><strong><?php echo $rowart['strNombreA'];?></strong></h3>
				</div>
				<div class="col-xs-1">
					<a href="<?php echo $rowart['strFacebookA'];?>"><img src="imagenes/face.jpg" style="width:100%; max-width:50px;" /></a>
				</div>
				<div class="col-xs-1">
					<a href="<?php echo $rowart['strTwitterA'];?>"><img src="imagenes/twitter.jpg" style="width:100%; max-width:50px;" /></a>
				</div>
				<div class="col-xs-1">
					<a href="<?php echo $rowart['strYoutubeA'];?>"><img src="imagenes/youtube.jpg" style="width:100%; max-width:50px;" /></a>
				</div>
				<div class="col-xs-1">
					<a href="<?php echo $rowart['strInstagramA'];?>"><img src="imagenes/instagram.jpg" style="width:100%; max-width:50px;" /></a>
				</div>
			<?php }?>
		</div>
		<div style="background-color:#515557; margin:20px; height:3px;"></div>
	</div>-->
	<div id="mostrar_mapa">
		<div class="row">
			<div class="col-lg-3" style="text-align:center;">
				<img src="<?php echo $ruta_mapa;?>" style="max-width:250px;" />
			</div>
			<div class="col-lg-7" style="text-align:center;">
				<h2 id="nombrelocaldiad" style="color:#fff;"></h2>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-12" style="padding-right:30px;">
				<h4 class="pull-right"><strong>PRIMER PASO: </strong>Escoge la sección que deseas</h4>
			</div>
		</div>
		<div style="background-color:#272b2a; border:1px solid #00aeef; margin:0 125px; padding:60px 0; position:relative;">
			<h4 style="text-align:center; color:#fff; margin-top:-20px; margin-bottom:40px;"><span style="border:1px solid #00aeef; padding:10px;"><strong>ESCENARIO</strong></span></h4>
			<?php while($rowId = $resultId -> fetch(PDO::FETCH_ASSOC)){
				$idbtnloc = $rowId['idLocalidad'];
				$img = $rowId['strImgL'];
				$dir = 'https://www.ticketfacil.ec/ticket2/spadmin/';
				$ruta_img_local = $dir.$img; 
			?>
			<div id="img_localidad<?php echo $idbtnloc;?>" style="margin: 0 auto; width: 100%; display: none;">
				<center>
					<div id="divisiones_mapa<?php echo $idbtnloc;?>" style="width:700px; height:100px; background-image:url(<?php echo $ruta_img_local;?>); background-repeat:no-repeat; background-size:500px 100px;"></div>
				</center>
				<input type="hidden" id="limite_columnas<?php echo $idbtnloc;?>" value="<?php echo $rowId['intAsientosB'];?>" />
				<input type="hidden" id="secuencial<?php echo $idbtnloc?>" value="<?php echo $rowId['strSecuencial'];?>" />
			</div>
			<?php }?>
			<h4 style="text-align:center; color:#fff; font-size:14px;">SECCION(ES)</h4>
			<div style="position:absolute; right:-100px; top:40%;">
				<img src="imagenes/arrowcontinuo.png" id="arrow1" style="display:none;" class="arrow"/>
				<img src="imagenes/arrowcontinuo.png" id="arrow2" style="display:none;" class="arrow"/>
				<img src="imagenes/arrowcontinuo.png" id="arrow3" style="display:none;" class="arrow"/>
				<img src="imagenes/arrowcontinuo.png" id="arrow4" style="display:none;" class="arrow"/>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-12" style="text-align:center;">
				<h4 id="nombreseccion"></h4>
			</div>
		</div>
		<div id="segundopaso" style="display:none;">
			<div class="row">
				<div class="col-lg-12">
					<h4><strong>SEGUNDO PASO: </strong>Escoge tu(s) asiento(s)</h4>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-2">
					<div style="width:30px; height:30px; background-color:#67cdf5;"></div>
					<span style="margin-left:40px;">Tu Asiento</span>
				</div>
				<div class="col-xs-2">
					<div style="width:30px; height:30px; background-color:#ffffff;"></div>
					<span style="margin-left:40px;">Asientos Disponibles</span>
				</div>
				<div class="col-xs-2">
					<div style="width:30px; height:30px; background-color:#fbed2c;"></div>
					<span style="margin-left:40px;">Asientos Reservados</span>
				</div>
				<div class="col-xs-2">
					<div style="width:30px; height:30px; background-color:red;"></div>
					<span style="margin-left:40px;">Asientos Ocupados</span>
				</div>
				<div class="col-xs-2">
					<div style="width:30px; height:30px; background-color:#000;"></div>
					<span style="margin-left:40px;">Asientos no Disponibles</span>
				</div>
			</div>
			<?php while($row6 = $stmt6 -> fetch(PDO::FETCH_ASSOC)){?>
			<div id="detallesillas<?php echo $row6['idLocalidad'];?>" style="display:none;">
				<div class="row">
					<div class="col-lg-6" style="text-align:center;">
						<h4 style="color:#67cdf5;"><strong><span id="numboletos<?php echo $row6['idLocalidad'];?>">0</span></strong> Boleto(s) seleccionados</h4>
					</div>
					<div class="col-lg-5" style="text-align:center;">
						<h4 style="color:#67cdf5;" id="descripcionsilla<?php echo $row6['idLocalidad'];?>"></h4>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-12">
						<h4 style="color:#fff; text-align:center;"><span style="border:1px solid #00aeef; padding:10px;"><strong>ESCENARIO</strong></span></h4>
					</div>
				</div>
			</div>
			<?php }?>
			<div class="row">
				<div class="col-lg-12" style="text-align:center;">
					<img src="imagenes/loading.gif" id="wait" style="display:none; max-width:80px;" />
				</div>
			</div>
			<div class="row" style="overflow:auto; text-align:center;">
				<div id="localidad_butaca">
					
				</div>
			</div>
		</div>
		<?php while($row7 = $stmt7 -> fetch(PDO::FETCH_ASSOC)){?>
		<div class="row" id="botones<?php echo $row7['idLocalidad'];?>" style="display:none;">
			<div class="col-lg-6" style="text-align:right;">
				<button class="btnceleste" onclick="save_local('<?php echo $row7['idLocalidad'];?>','<?php echo $idcon;?>')">Seguir Seleccionando</button>
			</div>
			<div class="col-lg-5">
				<button class="btnceleste" onclick="cancel_local('<?php echo $row7['idLocalidad'];?>','<?php echo $idcon;?>')">Cancelar Selección</button>
			</div>
		</div>
		<?php }?>
	</div>
	<form id="form" action="" method="post" <?php echo $displayEsPub;?> class = 'posicionForm'>
		<div id="ocultar_mapa" style = 'border: 3px solid #00ADEF;' >
			<div style="margin:20px">
				<table id="seleccion" align="center" class="table-responsive" style="width:100%; color:#fff; border-collapse: separate; border-spacing:0 10px;">
					<tr style="background-color:#00ADEF">
						<td style="text-align:center; border-right:1px solid #fff; padding:5px;">
							<strong>C&oacute;digo de Compra</strong>
						</td>
						<td style="text-align:center; border-right:1px solid #fff;">
							<strong>Asiento #</strong>
						</td>
						<td style="text-align:center; border-right:1px solid #fff;">
							<strong>Descripci&oacute;n</strong>
						</td>
						<td style="text-align:center; border-right:1px solid #fff;">
							<strong># de Boletos</strong>
						</td>
						<td style="text-align:center; border-right:1px solid #fff;">
							<strong>Valor Unitario</strong>
						</td>
						<td style="text-align:center; border-right:1px solid #fff;">
							<strong>Valor Total</strong>
						</td>
						<td style="text-align:center">
							<strong>Eliminar</strong>
						</td>
					</tr>
				</table>
			</div>
			<div class="row" style="text-align:right; padding-right:30px;">
				<div class="col-lg-12">
					<!--<button type="button" class="btndegradate" id="reserva" style="font-size:15px;" onclick="reservarselect()"><img src="imagenes/mano_comprar.png" style="margin-left:-20px;"/><strong>+ RESERVAR SELECCIÓN</strong></button>-->
					<!--<button type="button" class="btndegradate" style="font-size:15px;" ></button>-->
					<div id = 'recibeConsultaDesc' style = 'width:100%;'></div>

				</div>
			</div>
		</div>
	</form>
	<div class="modal fade" id="mostrar_login_compra" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content" style="background-color:#333333; color:#fff;">
				<div class="modal-header" style="border-bottom:none;">
					<button type="button" id="close" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff;"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>
				</div>
				<div class="modal-body">
				<div style="border:2px solid #00ADEF;">
					<div style="background-color:#00ADEF; color:#fff; margin:20px 70% 20px 0px; padding-left:30px; font-size:25px;">
						<strong>Login</strong>
					</div>
					<div style="background-color:#EC1867; margin:10px -18px 10px 50%; font-size:18px; position:relative;">
						<img src="imagenes/mano_comprar.png" alt="" /><button type="button" id="btn_account" onclick="crearcuentacomprar()" class="btn_compra_online"><strong>+ CREAR CUENTA</strong></button>
						<div class="tra_comprar_concierto"></div>
						<div class="par_comprar_concierto"></div>
					</div>
					<div style="background-color:#00ADEF; margin:30px -18px 20px 40px; position:relative;">
						<table style="width:100%; color:#fff; font-size:20px;">
							<tr>
								<td colspan="2" style="padding-bottom:0px; padding-top:10px;">
									<center><I><p>Si ya tienes una cuenta en <strong>TICKETFACIL</strong> ingresa tu <strong>USUARIO</strong> y <strong>CONTRASE&Ntilde;A</strong></p></I></center>
									<center><I><p>o si no <strong>CREA TU CUENTA</strong> y disfruta de una nueva experiencia en linea</p></I></center>
								</td>
							</tr>
							<!--<tr>
								<td style="text-align:center; padding-bottom:20px; padding-top:10px; padding-right:25px;">
									<p style="display:none" class="cuentaOk">Usuario(e-mail): </p>
								</td>
							</tr>-->
							<tr>
								<td style="text-align:left; padding:10px 30px;">
									<h4 class="cuentaOk_compra" style="display:none; color:#fff;">Usuario(E-mail):</h4>
									<input type="text" id="user_compra" class="cuentaOk_compra inputlogin form-control" placeholder="Usuario(E-mail)" style="display:none" autocomplete="off" />
								</td>
							</tr>
							<!--<tr>
								<td style="text-align:center; padding-top:10px; padding-right:25px;">
									<p style="display:none" class="cuentaOk">Contrase&ntilde;a: </p>
								</td>
							</tr>-->
							<tr>
								<td style="text-align:left; padding:10px 30px;">
									<h4 class="cuentaOk_compra" style="display:none; color:#fff;">Contraseña:</h4>
									<input type="password" id="pass_compra" class="cuentaOk_compra inputlogin form-control" placeholder="Contraseña" style="display:none" autocomplete="off" />
								</td>
							</tr>
							
							<tr class="ocultar_text_login_compra" align="center">
								<td>
									<div> 
										<a onclick="ingresar();" class="btn btn-primary social-login-btn social-facebook" name="">
											<i class="fa fa-facebook fa-1x">
												<label style="font-size:15px;font-family:corbel;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Acceder&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
											</i>
										</a><br>
										<a class="g-signin2" data-onsuccess="onSignIn" class="btn btn-danger social-login-btn social-google" name=""><i class="fa fa-google-plus fa-2x"></i></a>
									</div>
									<div style = 'display:none;' >
										<span id = 'nombreUsuFace' >!!!</span>
										<span id = 'nombreUsuEmail' >!!!</span>
										<span id = 'nombreUsuPass' >!!!</span>
										<span id = 'fecha' >!!!</span>
										<span id = 'sexo' >!!!</span>
									</div>
								
							
									<script src="https://apis.google.com/js/platform.js" async defer></script>
									<meta name="google-signin-client_id" content="888800978669-ocjtqig3s763l7erklsp5v58j69b2h0r.apps.googleusercontent.com">
									<br>
									
									<script>
										function onSignIn(googleUser) {
											var profile = googleUser.getBasicProfile();
											var con = $('#con').val();
											// alert(profile)
											$('#ID').html('ID: ' + profile.getId()); // Do not send to your backend! Use an ID token instead.
											$('#Name').html('Name: ' + profile.getName());
											$('#Image').attr('src',profile.getImageUrl());
											$('#Email').html('Email: ' + profile.getEmail()); // This is null if the 'email' scope is not present.
											
											
											var id_gmail = profile.getId();
											var email_gmail =  profile.getEmail();
											var nombre = profile.getName();
											var birthday = '';
											var gender = '';
											
											
											if ($('#seleccion >tbody >tr').length == 1){
												// alert ( "No hay filas en la tabla!!" );
											}else{
												$.post("autenticaGmail.php",{ 
													id_gmail : id_gmail , email_gmail : email_gmail , nombre : nombre , birthday : birthday , gender : gender
												}).done(function(data){
													accion ='?modulo=comprar&con='+con;
													$('#form').attr('action',accion);
													$('#form').submit();
													
												});

											}
										}
									</script>
								</td>
							</tr>
							
							<tr class="ocultar_text_login_compra" align="center">
								<td>
									<button type="" class="btn_login"  onclick="crearcuentacomprar()" >CREAR CUENTA</button>
								</td>
							</tr>
							<tr class="ocultar_text_login_compra" align="center">
								<td>
									<br>
									<button type="button" class="btn_login" onclick="mostrar_login_compra()">INGRESAR</button>
								</td>
							</tr>
							
							
							<tr>
								<td style="text-align:right; padding-right:15px;">
									<h4 class="cuentaOk_compra" style="display:none; cursor:pointer;" onclick="recuperarcontrasena()"><I>Olvido su contraseña?</I></h4>
									<h4 class="cuentaOk_compra" style="display:none; cursor:pointer;" onclick="recuperarnombre()"><I>Olvido su nombre de usuario?</I></h4>
								</td>
							</tr>
							<tr align="center">
								<td colspan="2" style="padding:30px;">
									<button type="button" class="btn_login cuentaOk_compra" style="display:none" onclick="validardatos_compra()"><strong>ACEPTAR</strong></button>
									<img src="imagenes/ajax-loader.gif" alt="" id="imgif" name="imgif" style="display:none" />
								</td>
							</tr>
						</table>
						<div class="tra_azul"></div>
						<div class="par_azul"></div>
					</div>
				</div>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="mostrar_login_reserva" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content" style="background-color:#333333; color:#fff;">
				<div class="modal-header" style="border-bottom:none;">
					<button type="button" id="close" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff;"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>
				</div>
				<div class="modal-body">
				<div style="border:2px solid #00ADEF;">
					<div style="background-color:#00ADEF; color:#fff; margin:20px 70% 20px 0px; padding-left:30px; font-size:25px;">
						<strong>Login</strong>
					</div>
					<div style="background-color:#EC1867; margin:10px -18px 10px 50%; font-size:18px; position:relative;">
						<img src="imagenes/mano_comprar.png" alt="" /><button type="button" id="btn_account" onclick="crearcuentareserva()" class="btn_compra_online"><strong>+ CREAR CUENTA</strong></button>
						<div class="tra_comprar_concierto"></div>
						<div class="par_comprar_concierto"></div>
					</div>
					<div style="background-color:#00ADEF; margin:30px -18px 20px 40px; position:relative;">
						<table style="width:100%; color:#fff; font-size:20px;">
							<tr>
								<td colspan="2" style="padding-bottom:50px; padding-top:40px;">
									<center><I><p>Si ya tienes una cuenta en <strong>TICKETFACIL</strong> ingresa tu <strong>USUARIO</strong> y <strong>CONTRASE&Ntilde;A</strong></p></I></center>
									<center><I><p>o si no <strong>CREA TU CUENTA</strong> y disfruta de una nueva experiencia en linea</p></I></center>
								</td>
							</tr>
							<!--<tr>
								<td style="text-align:center; padding-bottom:20px; padding-top:10px; padding-right:25px;">
									<p style="display:none" class="cuentaOk">Usuario(e-mail): </p>
								</td>
							</tr>-->
							<tr>
								<td style="text-align:left; padding:10px 30px;">
									<h4 class="cuentaOk_reserva" style="display:none; color:#fff;">Usuario(E-mail):</h4>
									<input type="text" id="user_reserva" class="cuentaOk_reserva inputlogin form-control" placeholder="Usuario(E-mail)" style="display:none" autocomplete="off" />
								</td>
							</tr>
							<!--<tr>
								<td style="text-align:center; padding-top:10px; padding-right:25px;">
									<p style="display:none" class="cuentaOk">Contrase&ntilde;a: </p>
								</td>
							</tr>-->
							<tr>
								<td style="text-align:left; padding:10px 30px;">
									<h4 class="cuentaOk_reserva" style="display:none; color:#fff;">Contraseña:</h4>
									<input type="password" id="pass_reserva" class="cuentaOk_reserva inputlogin form-control" placeholder="Contraseña" style="display:none" autocomplete="off" />
								</td>
							</tr>
							<tr class="ocultar_text_login_reserva" align="center">
								<td>
									<button type="" class="btn_login" onclick="crearcuentareserva()" >CREAR CUENTA</button>
								</td>
							</tr>
							<tr class="ocultar_text_login_reserva" align="center">
								<td>
									<br>
									<button type="button" class="btn_login" onclick="mostrar_login_reserva()">INGRESAR</button>
								</td>
							</tr>
							<tr>
								<td style="text-align:right; padding-right:15px;">
									<h4 class="cuentaOk_reserva" style="display:none; cursor:pointer;" onclick="recuperarcontrasena()"><I>Olvido su contraseña?</I></h4>
									<h4 class="cuentaOk_reserva" style="display:none; cursor:pointer;" onclick="recuperarnombre()"><I>Olvido su nombre de usuario?</I></h4>
								</td>
							</tr>
							<tr align="center">
								<td colspan="2" style="padding:30px;">
									<button type="button" class="btn_login cuentaOk_reserva" style="display:none" onclick="validardatos_reserva()"><strong>ACEPTAR</strong></button>
									<img src="imagenes/ajax-loader.gif" alt="" id="imgif" name="imgif" style="display:none" />
								</td>
							</tr>
						</table>
						<div class="tra_azul"></div>
						<div class="par_azul"></div>
					</div>
				</div>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="aviso" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="aceptarModal()"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">Alerta...!</h4>
				</div>
				<div class="modal-body">
					<h4 id="alerta1" class="alertas" style="display:none;">
						<div class="alert alert-danger" role="alert">
							<span class="glyphicon glyphicon-alert" aria-hidden="true"></span>
							&nbsp;&nbsp;<strong>Error...!</strong>&nbsp;Debes seleccionar asientos primero.
						</div>
					</h4>
					<h4 id="alerta2" class="alertas" style="display:none;">
						<div class="alert alert-info" role="alert"><span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
							&nbsp;&nbsp;<strong>Aviso!</strong>&nbsp;Escoge tus asientos para continuar con los pasos.
						</div>
					</h4>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" data-dismiss="modal" onclick="aceptarModal()">Aceptar</button>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="recuperarcontrasena" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-keyboard="false" data-backdrop="static">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="aceptarModal()"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">Envio de email</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-lg-12">
							<h4>Ingrese su correo electrónico</h4>
							<input type="text" id="mailrecuperar" class="form-control" placeholder="Ingrese su correo electrónico" />
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" onclick="aceptarcontrasena()" id="enviarcambio">Enviar</button>
					<img src="imagenes/loading.gif" style="display:none; max-width:50px;" id="waitcambio">
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="nuevacontrasena" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-keyboard="false" data-backdrop="static">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="aceptarModal()"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">Cambio de contraseña</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-lg-12">
							<h4>Ingrese su código</h4>
							<input type="text" id="codigorecuperar" class="form-control" placeholder="Ingrese el código enviado a su correo electrónico" />
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12">
							<h4>Nueva contraseña</h4>
							<input type="password" id="passrecuperar" class="form-control" placeholder="**********" />
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12">
							<h4>Repita la contraseña</h4>
							<input type="password" id="passrecuperar1" class="form-control" placeholder="**********" />
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" onclick="confirmarcambio()" id="btnokcambio">Guardar</button>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="recuperarnombre" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-keyboard="false" data-backdrop="static">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="aceptarModal()"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">Recuperar Nombre de Usuario (E-mail)</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-lg-12">
							<h4>Ingrese su número de documento</h4>
							<input type="text" id="cedularecuperar" class="form-control" placeholder="Ingrese su número de identificación..." />
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12">
							<h4>Ingrese su fecha de nacimiento</h4>
							<div class="row">
								<div class="col-lg-3">
									<strong>Año</strong>
									<select id="aniorecuperar" class="inputlogin form-control">
										<option value="1">Año</option>
										<?php for($i = $anio; $i > 1930; $i--){?>
										<option value="<?php echo $i;?>"><?php echo $i;?></option>
										<?php }?>
									</select>
								</div>
								<div class="col-lg-3">
									<strong>Mes</strong>
									<select id="mesrecuperar" class="inputlogin form-control">
										<option value="0">Mes</option>
										<option value="01">Enero</option>
										<option value="02">Febrero</option>
										<option value="03">Marzo</option>
										<option value="04">Abril</option>
										<option value="05">Mayo</option>
										<option value="06">Junio</option>
										<option value="07">Julio</option>
										<option value="08">Agosto</option>
										<option value="09">Septiembre</option>
										<option value="10">Octubre</option>
										<option value="11">Noviembre</option>
										<option value="12">Diciembre</option>
									</select>
								</div>
								<div class="col-lg-3">
									<strong>Día</strong>
									<select id="diarecuperar" class="inputlogin form-control">
										<option value="0">Día</option>
										<?php for($j = 1; $j <= 31; $j++){?>
										<option value="<?php echo $j;?>"><?php echo $j;?></option>
										<?php }?>
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12">
							<h4>Ingrese su número de celular</h4>
							<input type="text" id="celularrecuperar" class="form-control" placeholder="Ingrese el número de celular ingresado al crear la cuenta..." />
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" onclick="aceptarnombre()" id="enviarnombre">Enviar</button>
					<img src="imagenes/loading.gif" style="display:none; max-width:50px;" id="waitnombre">
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="errorcontrasena" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-keyboard="false" data-backdrop="static">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="aceptarModal()"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">Alerta...!</h4>
				</div>
				<div class="modal-body">
					<div class="alert alert-danger" role="alert">
						<span class="glyphicon glyphicon-alert" aria-hidden="true"></span>
						&nbsp;&nbsp;<strong>Error...!</strong>&nbsp;El correo electrónico ingresado es incorrecto.
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" data-dismiss="modal" onclick="aceptarModal()">Aceptar</button>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="errorvalidacion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-keyboard="false" data-backdrop="static">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="aceptarModal()"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">Alerta...!</h4>
				</div>
				<div class="modal-body">
					<div class="alert alert-danger" role="alert">
						<span class="glyphicon glyphicon-alert" aria-hidden="true"></span>
						&nbsp;&nbsp;<strong>Error...!</strong>&nbsp;Los datos ingresados son incorrectos.
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" data-dismiss="modal" onclick="aceptarModal()">Aceptar</button>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="nombreok" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-keyboard="false" data-backdrop="static">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="aceptarModal()"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">Alerta...!</h4>
				</div>
				<div class="modal-body">
					<div class="alert alert-info" role="alert">
						<span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
						&nbsp;Se ha recuperado exitosamente tu nombre de usuario.
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" data-dismiss="modal" onclick="aceptaroknombre()">Aceptar</button>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="mensajeenviado" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-keyboard="false" data-backdrop="static">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="aceptarModal()"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">Alerta...!</h4>
				</div>
				<div class="modal-body">
					<div class="alert alert-info" role="alert">
						<span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
						&nbsp;Se ha enviado un código a tu correo electrónico, revisalo y sigue los pasos.
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" data-dismiss="modal" onclick="aceptarmensaje()">Aceptar</button>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="cambiook" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-keyboard="false" data-backdrop="static">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="aceptarModal()"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">Alerta...!</h4>
				</div>
				<div class="modal-body">
					<div class="alert alert-info" role="alert">
						<span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
						&nbsp;Tu contraseña se ha modificado con éxito.
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" data-dismiss="modal" onclick="aceptarcambiook()">Aceptar</button>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
	include 'conexion.php';
	$sqlCo = 'SELECT * FROM Concierto WHERE idConcierto = "'.$idcon.'" AND strEstado = "'.$estadoCon.'"';
	// echo $sqlCo;
	$resCo = mysql_query($sqlCo) or die (mysql_error());
	
	$rowCo = mysql_fetch_array($resCo);
	echo '<input type="hidden" id="mapa_opacity" value="'.$rowCo['strMapaFill'].'" />';
?>
<script>
	function verMapaAbajo(){
		var elemento = $("#posicionMapa");
		var posicion = elemento.position();
		//alert( "left: " + posicion.left + ", top: " + posicion.top );
		var aumento = '250';
		var animar = (parseInt(posicion.top)+parseInt(aumento));
		//alert(animar);
		$( "html, body " ).animate({
			scrollTop: animar,
		}, 2000, function() {
			
		});
	}
var dias = $('#dias').val();
var horas = $('#horas').val();
var minutos = $('#minutos').val();
var segundos = $('#segundos').val();
var diasventa = $('#diasventa').val();
var horasventa = $('#horasventa').val();
var minutosventa = $('#minutosventa').val();
var segundosventa = $('#segundosventa').val();
var valor_session = $('#session').val();
var con = $('#concierto').val();
var variables = '';
var dir_map = 'https://www.ticketfacil.ec/ticket2/spadmin/'; 
var mapa_opaco = $('#mapa_opacity').val();
var ruta = dir_map+mapa_opaco;
var num_preventa = $('#num_rows_preventa').val();
var num_reserva = $('#num_rows_reserva').val();

window.onload = function (){
	if(num_reserva > 0){
		r = new clockCountdown('clock',{'days':dias,'hours':horas,'minutes':minutos,'seconds':segundos});
	}
	if(num_preventa > 0){
		s = new clockCountdown('clock2',{'days':diasventa,'hours':horasventa,'minutes':minutosventa,'seconds':segundosventa});
	}
}
var timer2 = 0;
function animaciontitle(){
	var tiempo = 0;
	timer2 = setInterval(function(){
		tiempo = parseInt(tiempo) + parseInt(1);
		if(tiempo <= 1){
			$('#titulo1').css('color','#f8e316');
		}else{
			$('#titulo1').css('color','#fff');
			tiempo = 0;
		}
	},400);
}

$(document).ready(function(){
	clearInterval(timer2);
	animaciontitle();
	
	$(window).resize(function(){
		var width = $(window).width();
		if(width < 1024){
			$('.responsivedesign').fadeOut();
			$('#right1').removeClass('pull-right');
			$('#right2').removeClass('pull-right');
		}else{
			$('.responsivedesign').fadeIn();
			$('#right1').addClass('pull-right');
			$('#right2').addClass('pull-right');
		}
	});
	var width = $(window).width();
	if(width < 1024){
		$('.responsivedesign').fadeOut();
		$('#right1').removeClass('pull-right');
		$('#right2').removeClass('pull-right');
	}else{
		$('.responsivedesign').fadeIn();
		$('#right1').addClass('pull-right');
		$('#right2').addClass('pull-right');
	}
	
	$('#localmapa').mapster({
		singleSelect: true,
		render_highlight: {altImage: ruta},
		render_select: false,
		mapkey: 'data-state',
		fill: true,
		// fillColor: '282B2D',
		//fillOpacity: 1,
	});
	
	// if(num_preventa > 0){
		// $('#reserva').css('display','none');
		// $('.div_reserva').css('display','none');
	// }else{
	if(num_reserva < 1){
		$('#reserva').fadeOut();
	}else{
		$('#reserva').fadeIn();
	}
	// }
});

function comprarselect(){
	if(valor_session == 'ok'){
		if($('.asientosescogidos').length){
			$('#form').attr('action','');
			accion ='?modulo=comprar&con='+con;
			$('#form').attr('action',accion);
			$('#identification').addClass('active');
			$('#chooseseat').removeClass('active');
			$('#form').submit();
		}else{
			$('#alerta1').fadeIn();
			$('#aviso').modal('show');
		}
	}else{
		if($('.asientosescogidos').length){
			$('#form').prop('action','');
			$('#mostrar_login_compra').modal('show');
			
			$('#identification').addClass('active');
			$('#chooseseat').removeClass('active');
			
			$('#close').on('click',function(){
				$('#mostrar_login_compra').modal('hide');
				$('.cuentaOk_compra').css('display','none');
				$('.ocultar_text_login_compra').fadeIn();
				$('#form').prop('action','');
				$('#identification').removeClass('active');
				$('#chooseseat').addClass('active');
			});
			
		}else{
			$('#alerta1').fadeIn();
			$('#aviso').modal('show');
		}
	}
}

function mostrar_login_compra(){
	$('.cuentaOk_compra').fadeIn();
	$('.ocultar_text_login_compra').css('display','none');
}

function reservarselect(){
	if(valor_session == 'ok'){
		if($('.asientosescogidos').length){
			$('#form').attr('action','');
			accion ='?modulo=reservar&con='+con;
			$('#form').attr('action',accion);
			$('#identification').addClass('active');
			$('#chooseseat').removeClass('active');
			$('#form').submit();
		}else{
			$('#alerta1').fadeIn();
			$('#aviso').modal('show');
		}
	}else{
		if($('.asientosescogidos').length){
			$('#form').prop('action','');
			$('#mostrar_login_reserva').modal('show');
			$('#identification').addClass('active');
			$('#chooseseat').removeClass('active');
			$('#close').on('click',function(){
				$('#mostrar_login_reserva').modal('hide');
				$('.cuentaOk_reserva').css('display','none');
				$('.ocultar_text_login_reserva').fadeIn();
				$('#form').prop('action','');
				$('#identification').removeClass('active');
				$('#chooseseat').addClass('active');
			});
		}else{
			$('#alerta1').fadeIn();
			$('#aviso').modal('show');
		}
	}
}

function mostrar_login_reserva(){
	$('.cuentaOk_reserva').css('display','block');
	$('.ocultar_text_login_reserva').css('display','none');
}

function recuperarcontrasena(){
	$('#recuperarcontrasena').modal('show');
}

function recuperarnombre(){
	$('#recuperarnombre').modal('show');
}

function aceptarcontrasena(){
	var mail = $('#mailrecuperar').val();
	$('#enviarcambio').fadeOut('slow');
	$('#waitcambio').delay(600).fadeIn('slow');
	$.post('subpages/Conciertos/recuperarcontrasena.php',{
		mail : mail
	}).done(function(response){
		if($.trim(response) == 'ok'){
			$('#recuperarcontrasena').modal('hide');
			$('#waitcambio').fadeOut('fast');
			$('#enviarcambio').fadeIn('slow');
			$('#mensajeenviado').modal('show');
			$('#mailrecuperar').val('');
		}else if($.trim(response) == 'error'){
			$('#mailrecuperar').val('');
			$('#waitcambio').fadeOut('fast');
			$('#enviarcambio').delay(600).fadeIn('slow');
			$('#errorvalidacion').modal('show');
		}
	});
}

function aceptarmensaje(){
	$('#nuevacontrasena').modal('show');
}

function aceptarnombre(){
	var cedula = $('#cedularecuperar').val();
	var anio = $('#aniorecuperar').val();
	var mes = $('#mesrecuperar').val();
	var dia = $('#diarecuperar').val();
	var celular = $('#celularrecuperar').val();
	$.post('subpages/Conciertos/cambiarnombre.php',{
		cedula : cedula, anio : anio, mes : mes, dia : dia, celular : celular
	}).done(function(response){
		if($.trim(response) != 'error'){
			$('#nombreok').modal('show');
			if($('#mostrar_login_compra').is(':visible')){
				$('#user_compra').val(response);
			}else if($('#mostrar_login_reserva').is(':visible')){
				$('#user_reserva').val(response);
			}
			$('#recuperarnombre').modal('hide');
		}else{
			$('#errorvalidacion').modal('show');
		}
	});
}

function aceptaroknombre(){
	$('#nombreok').modal('hide');
}

function validardatos_compra(){
	var login = $('#user_compra').val();
	var pass = $('#pass_compra').val();
	$.post('subpages/Conciertos/validardatos.php',{
		login : login, pass : pass
	}).done(function(response){
		if($.trim(response) == 'ok'){
			accion ='?modulo=comprar&con='+con;
			$('#form').attr('action',accion);
			$('#form').submit();
		}else{
			$('#user_compra').val('');
			$('#pass_compra').val('');
			$('#errorvalidacion').modal('show');
		}
	});
	
}

function validardatos_reserva(){
	var login = $('#user_reserva').val();
	var pass = $('#pass_reserva').val();
	$.post('subpages/Conciertos/validardatos.php',{
		login : login, pass : pass
	}).done(function(response){
		if($.trim(response) == 'ok'){
			accion = '?modulo=reservar&con='+con;
			$('#form').attr('action',accion);
			$('#form').submit();
		}else{
			$('#user_reserva').val('');
			$('#pass_reserva').val('');
			$('#errorvalidacion').modal('show');
		}
	});
	
}

function confirmarcambio(){
	var codigo = $('#codigorecuperar').val();
	var pass = $('#passrecuperar').val();
	$.post('subpages/Conciertos/cambiarcontrasena.php',{
		codigo : codigo, pass : pass
	}).done(function(response){
		$('#nuevacontrasena').modal('hide');
		if($.trim(response) == 'ok'){
			$('#btnokcambio').fadeOut('slow');
			$('#cambiook').modal('show');
		}else{
			$('#errorvalidacion').modal('show');
		}
	});
}

function aceptarcambiook(){
	$('#cambiook').modal('hide');
}

function crearcuentacomprar(){
	if($('.asientosescogidos').length){
		accion = '?modulo=newaccount&con='+con+'&pos=1';
		$('#form').attr('action',accion);
		$('#form').submit();
	}else{
		$('#alerta1').fadeIn();
		$('#aviso').modal('show');
	}
}

function crearcuentareserva(){
	if($('.asientosescogidos').length){
		accion = '?modulo=registrousuarioreserva&con='+con;
		$('#form').attr('action',accion);
		$('#form').submit();
	}else{
		$('#alerta1').fadeIn();
		$('#aviso').modal('show');
	}
}

function security(){
	$('#alerta2').fadeIn();
	$('#aviso').modal('show');
}

function aceptarModal(){
	$('.alertas').fadeOut();
	$('#aviso').modal('hide');
}

var timer = 0;

// function animaringreso(){
	// var right = -150;
	// timer = setInterval(function(){
		// right = parseInt(right) + parseInt(50);
		// if(right <= 50){
			// right = right+'px';
			// $('#arrow1').animate({
				// 'right':right
			// });
		// }else{
			// right = -150;
		// }
	// },200);
// }

function animaringreso(){
	var mostrar = 0;
	timer = setInterval(function(){
		mostrar = parseInt(mostrar) + parseInt(1);
		if(mostrar <= 4){
			$('#arrow'+mostrar).fadeIn();
		}else{
			$('.arrow').fadeOut();
			mostrar = 0;
		}
	},400);
}

function asientos_mapa(local, concierto){
	
	$.post("subpages/Conciertos/saber_asientos_numerados.php",{ 
		local : local , concierto : concierto
	}).done(function(data){
		var respuesta = data.split('|');
		var valorAsientos = respuesta[0];
		var precio = respuesta[1];
		var local = respuesta[2];
		var options = respuesta[3];
		$('.comboSinNumeracion').html(options);
		
		$('#precioLocalidadSinNum').val(precio);
		$('#localidadSinNum').val(local);
		if(valorAsientos == 0){
			//alert('es asientos no numerados');
			animaringreso();
			var nombrelocal = $('#local'+local).val();
			$('#nombrelocaldiadNoNumerados').html('Estas en <strong id = "nomLoc">'+nombrelocal+'<strong>');
			$('#ocultar').fadeOut('slow');
			$('#contieneNoNumerados').fadeIn('slow');
			$('html, body').animate({ scrollTop: 0 }, 'slow');
		}else{
			animaringreso();
			var nombrelocal = $('#local'+local).val();
			$('#nombrelocaldiad').html('Estas en <strong>'+nombrelocal+'<strong>');
			var numcolumnas = $('#limite_columnas'+local).val();
			var divisiones = parseInt(numcolumnas) / parseInt(30);
			divisiones = Math.ceil(divisiones); 
			var totdivisiones = parseInt(500) / parseInt(divisiones);
			for(var i = 1; i <= divisiones; i++){
				// if(i == 1){
					// $('#divisiones_mapa'+local).append('<div onclick="open_rest_map('+i+','+local+','+concierto+')" id="img_hover'+local+'_'+i+'" class="img_over'+local+'" style="background-color:#67cdf5; color:#000; font-size:20px; display:inline-block; height:100px; width:'+totdivisiones+'px; opacity:0.9; border:1px solid #000; cursor:pointer;">'+i+'</div>');
				// }else{
					$('#divisiones_mapa'+local).append('<div onclick="open_rest_map('+i+','+local+','+concierto+')" id="img_hover'+local+'_'+i+'" style="width:'+totdivisiones+'px;" class="img_over'+local+' secciones">'+i+'</div>');
				// }
			}
			
			if($('#mostrar'+local).length){
				$('#ocultar').fadeOut('slow');
				$('#mostrar_mapa').delay(600).fadeIn();
				$('#img_localidad'+local).fadeIn();
				$('.ocultar'+local).fadeOut();
				$('#detallesillas'+local).fadeIn();
				$('#botones'+local).fadeIn();
				// $('#mostrar'+local).fadeIn();
				// $('#nombreseccion').html('Estas en la sección <strong>1</strong>');
				$('html, body').animate({ scrollTop: 0 }, 'slow');
			}else{
				$('#detallesillas'+local).fadeIn();
				$('#botones'+local).fadeIn();
				// $('#nombreseccion').html('Estas en la sección <strong>1</strong>');
				$('#ocultar').fadeOut('slow');
				$('#mostrar_mapa').delay(600).fadeIn();
				$('#wait').fadeIn();
				$('#img_localidad'+local).fadeIn();
				
				$.ajax({
					type : 'POST',
					url : 'subpages/Conciertos/construir_mapa.php',
					data : 'local='+local +'&concierto='+concierto,
					success : function(response){
						$('#localidad_butaca').append(response);
						$('#wait').fadeOut();
					}
				});
			}
			$('html, body').animate({ scrollTop: 0 }, 'slow');			
		}	
		
		$.post("subpages/Conciertos/saberDescuentosWeb.php",{ 
			local : local 
		}).done(function(data){
			if(data == 0){
				$('#recibeConsultaDesc').html('\
												<button type="button" class="btn btn-primary" onclick="comprarselect()">\
													<strong> + COMPRAR SELECCIÓN</strong>\
												</button>\
											');
			}else{
				$('#recibeConsultaDesc').html('\
												<div class = "row">\
														<div class = "col-md-4" style = "padding-right:20px;color:#fff;" >\
															<label style = "font-size:1vw;">POR FAVOR SELECCIONE EL DESCUENTO</label>\
														</div>\
														<div class = "col-md-4">\
															'+data+'\
														</div>\
														<div class = "col-md-4">\
															<button type="button" class="btn btn-primary" onclick="comprarselect()">\
																<strong> + COMPRAR SELECCIÓN</strong>\
															</button>\
														</div>\
												</div>\
											');
			}
		});

	});
}

// function celeste(i,local){
	// $('#img_hover'+local+'_'+i).css('background-color','#67cdf5');
// }

// function amarillo(i,local){
	// $('#img_hover'+local+'_'+i).css('background-color','#f8e316');
// }

function open_rest_map(id, local, concierto){
	$('html, body').animate({ scrollTop: 800 }, 'slow');
	var nombrelocal = $('#local'+local).val();
	$('#nombrelocaldiad').html('Estas en <strong>'+nombrelocal+'<strong>');
	$('#nombreseccion').html('Estas en la sección <strong>'+id+'</strong>');
	$('.img_over'+local).css('background-color','#f8e316');
	$('#img_hover'+local+'_'+id).css('background-color','#67cdf5');
	$('#segundopaso').fadeIn();
	clearInterval(timer);
	$('.arrow').fadeOut();
	if(id == 1){
		$.post("subpages/Conciertos/creaSessionSeccion.php",{ 
			id_area_mapa : id 
		}).done(function(data){
			//alert(data)			
		});
		$('#mostrar'+local).fadeIn();
		//$('#mostrar_mapa').fadeOut();
		$('#segundopaso').fadeIn();
		$('#nombreseccion').html('Estas en la sección <strong>1</strong>');
		$('.ocultar'+local).fadeOut();
	}else{
		//$('#mostrar_mapa').fadeOut();
		$('#segundopaso').fadeIn();
		$('#mostrar'+local).fadeOut();
		$('.mostrartable'+id).css('display','none');
		$('.ocultar'+local).css('display','none');
		if($('#asientos_local-'+local+'_'+id).length){
			$('#contar_boletos'+local+'_'+id).fadeIn();
		}else{
			$('#wait').fadeIn();
			$.ajax({
				type : 'POST',
				url : 'subpages/Conciertos/continuacion_asientos.php',
				data : 'id='+id +'&local='+local +'&concierto='+concierto,
				success : function(response){
					$('#localidad_butaca').append(response);
					$('#wait').fadeOut();
				}
			});
		}
	}
}

function add_asientos(local, concierto, row, col){
	var numboletos = $('#numboletos'+local).html();
	var color = $('#A-'+local+'-'+row+'-'+col).css('background-color');
	if(color == 'rgb(255, 255, 255)'){
		$('#A-'+local+'-'+row+'-'+col).css('background-color','#67cdf5');
		numboletos = parseInt(numboletos) + parseInt(1);
		$('#numboletos'+local).html(numboletos);
	}else{
		$('#A-'+local+'-'+row+'-'+col).css('background-color','#fff');
		$('#new_selection_'+row+'_'+col+'_'+local).remove();
		numboletos = parseInt(numboletos) - parseInt(1);
		$('#numboletos'+local).html(numboletos);
		$('#descripcionsilla'+local).html('');
		return false;
	}
	
	var secuencial = $('#secuencial'+local).val();
	if($('#new_selection_'+row+'_'+col+'_'+local).length){
		$('#descripcionsilla'+local).html('');
	}else{
		if(secuencial == 0){
			$('#descripcionsilla'+local).html('<p align="center">FILA-'+row+'_SILLA-'+col+'</p>');
		}else if(secuencial == 1){
			if(row == 1){
				$('#descripcionsilla'+local).html('<p align="center">FILA-A_SILLA-'+col+'</p>');
			}
			if(row == 2){
				$('#descripcionsilla'+local).html('<p align="center">FILA-B_SILLA-'+col+'</p>');
			}
			if(row == 3){
				$('#descripcionsilla'+local).html('<p align="center">FILA-C_SILLA-'+col+'</p>');
			}
			if(row == 4){
				$('#descripcionsilla'+local).html('<p align="center">FILA-D_SILLA-'+col+'</p>');
			}
			if(row == 5){
				$('#descripcionsilla'+local).html('<p align="center">FILA-E_SILLA-'+col+'</p>');
			}
			if(row == 6){
				$('#descripcionsilla'+local).html('<p align="center">FILA-F_SILLA-'+col+'</p>');
			}
			if(row == 7){
				$('#descripcionsilla'+local).html('<p align="center">FILA-G_SILLA-'+col+'</p>');
			}
			if(row == 8){
				$('#descripcionsilla'+local).html('<p align="center">FILA-H_SILLA-'+col+'</p>');
			}
			if(row == 9){
				$('#descripcionsilla'+local).html('<p align="center">FILA-I_SILLA-'+col+'</p>');
			}
			if(row == 10){
				$('#descripcionsilla'+local).html('<p align="center">FILA-J_SILLA-'+col+'</p>');
			}
			if(row == 11){
				$('#descripcionsilla'+local).html('<p align="center">FILA-K_SILLA-'+col+'</p>');
			}
			if(row == 12){
				$('#descripcionsilla'+local).html('<p align="center">FILA-L_SILLA-'+col+'</p>');
			}
			if(row == 13){
				$('#descripcionsilla'+local).html('<p align="center">FILA-M_SILLA-'+col+'</p>');
			}
			if(row == 14){
				$('#descripcionsilla'+local).html('<p align="center">FILA-N_SILLA-'+col+'</p>');
			}
			if(row == 15){
				$('#descripcionsilla'+local).html('<p align="center">FILA-O_SILLA-'+col+'</p>');
			}
			if(row == 16){
				$('#descripcionsilla'+local).html('<p align="center">FILA-P_SILLA-'+col+'</p>');
			}
			if(row == 17){
				$('#descripcionsilla'+local).html('<p align="center">FILA-Q_SILLA-'+col+'</p>');
			}
			if(row == 18){
				$('#descripcionsilla'+local).html('<p align="center">FILA-R_SILLA-'+col+'</p>');
			}
			if(row == 19){
				$('#descripcionsilla'+local).html('<p align="center">FILA-S_SILLA-'+col+'</p>');
			}
			if(row == 20){
				$('#descripcionsilla'+local).html('<p align="center">FILA-T_SILLA-'+col+'</p>');
			}
			if(row == 21){
				$('#descripcionsilla'+local).html('<p align="center">FILA-U_SILLA-'+col+'</p>');
			}
			if(row == 22){
				$('#descripcionsilla'+local).html('<p align="center">FILA-V_SILLA-'+col+'</p>');
			}
			if(row == 23){
				$('#descripcionsilla'+local).html('<p align="center">FILA-W_SILLA-'+col+'</p>');
			}
			if(row == 24){
				$('#descripcionsilla'+local).html('<p align="center">FILA-X_SILLA-'+col+'</p>');
			}
			if(row == 25){
				$('#descripcionsilla'+local).html('<p align="center">FILA-Y_SILLA-'+col+'</p>');
			}
		}
		$.ajax({
			type : 'POST',
			url : 'subpages/Conciertos/seleccionarasientos.php',
			data : 'local='+local +'&concierto='+concierto +'&row='+row +'&col='+col,
			success : function(response){
				$('#seleccion').append(response);
			}
		});
	}
}

function elimanarsillas(local, concierto, row, col){
	$('#A-'+local+'-'+row+'-'+col).css('background-color','#fff');
	$('#new_selection_'+row+'_'+col+'_'+local).remove();
}

function save_local(local, concierto){
	$('#img_localidad'+local).css('display','none');
	$('#descripcionsilla'+local).html('');
	$('.img_over'+local).remove();
	$('#detallesillas'+local).fadeOut();
	$('#botones'+local).fadeOut();
	$('.contar_boletos'+local).css('display','none');
	$('#mostrar_mapa').fadeOut('slow');
	$('#segundopaso').fadeOut();
	$('#nombreseccion').html('');
	clearInterval(timer);
	$('#ocultar').delay(600).fadeIn('slow');
	$('html, body').animate({ scrollTop: 75 }, 'slow');
}

function cancel_local(local, concierto){
	$('.file_checked-'+local).remove();
	$('.inputchk'+local).css('background-color','#fff');
	$('#img_localidad'+local).css('display','none');
	$('.contar_boletos'+local).css('display','none');
	$('.img_over'+local).remove();
	$('#numboletos'+local).html(0);
	$('#descripcionsilla'+local).html('');
	$('#detallesillas'+local).fadeOut();
	$('#botones'+local).fadeOut();
	$('#mostrar_mapa').fadeOut('slow');
	$('#segundopaso').fadeOut();
	$('#nombreseccion').html('');
	clearInterval(timer);
	$('#ocultar').delay(600).fadeIn('slow');
}
</script>