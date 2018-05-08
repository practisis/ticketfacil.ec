<style type="text/css">
	
	.on-order{
		background-color: #18B8EB !important;
		color:white !important;
	}
	.padding-0{
	    padding-right:0 !important;
	    padding-left:0 !important;
	}
	.btn {
	  font-size: 11px !important;
	  color:white !important;
	  background-color: #18B8EB !important;
	}
	.btn-arrow-right,
	.btn-arrow-left {
	   position: relative;
	   padding-left: 18px;
	   padding-right: 18px;
	}

	.btn-arrow-right {
	   padding-left: 36px;
	}

	.btn-arrow-left {
	   padding-right: 36px;
	}

	.btn-arrow-right:before,
	.btn-arrow-right:after,
	.btn-arrow-left:before,
	.btn-arrow-left:after {
	   /* make two squares (before and after), looking similar to the button */
	   
	   content: "";
	   position: absolute;
	   top: 5px;
	   /* move it down because of rounded corners */
	   
	   width: 22px;
	   /* same as height */
	   
	   height: 22px;
	   /* button_outer_height / sqrt(2) */
	   
	   background: inherit;
	   /* use parent background */
	   
	   border: inherit;
	   /* use parent border */
	   
	   border-left-color: transparent;
	   /* hide left border */
	   
	   border-bottom-color: transparent;
	   /* hide bottom border */
	   
	   border-radius: 0px 4px 0px 0px;
	   /* round arrow corner, the shorthand property doesn't accept "inherit" so it is set to 4px */
	   
	   -webkit-border-radius: 0px 4px 0px 0px;
	   -moz-border-radius: 0px 4px 0px 0px;
	}

	.btn-arrow-right:before,
	.btn-arrow-right:after {
	   transform: rotate(45deg);
	   /* rotate right arrow squares 45 deg to point right */
	   
	   -webkit-transform: rotate(45deg);
	   -moz-transform: rotate(45deg);
	   -o-transform: rotate(45deg);
	   -ms-transform: rotate(45deg);
	}

	.btn-arrow-left:before,
	.btn-arrow-left:after {
	   transform: rotate(225deg);
	   /* rotate left arrow squares 225 deg to point left */
	   
	   -webkit-transform: rotate(225deg);
	   -moz-transform: rotate(225deg);
	   -o-transform: rotate(225deg);
	   -ms-transform: rotate(225deg);
	}

	.btn-arrow-right:before,
	.btn-arrow-left:before {
	   /* align the "before" square to the left */
	   
	   left: -11px;
	}

	.btn-arrow-right:after,
	.btn-arrow-left:after {
	   /* align the "after" square to the right */
	   
	   right: -11px;
	}

	.btn-arrow-right:after,
	.btn-arrow-left:before {
	   /* bring arrow pointers to front */
	   
	   z-index: 1;
	}

	.btn-arrow-right:before,
	.btn-arrow-left:after {
	   /* hide arrow tails background */
	   
	   background-color: white;
	}
</style>
<?php
	$con = $_REQUEST['con'];
	$sqlC = 'select * from Concierto where idConcierto = "'.$con.'" ';
	$resC = mysql_query($sqlC) or die(mysql_error());
	$rowC = mysql_fetch_array($resC);
	$img = $rowC['strImagen'];
	$ruta = 'http://ticketfacil.ec/ticket2/spadmin/';
	$r = $ruta.$img;
	
	
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
?>
	<div class="row">
		<div class="col-md-1"></div>
		<div class="col-md-10">
			<div id="order-localidad" class="col-md-2"><button type="button" class="btn btn-info btn-arrow-right">LOCALIDAD</button></div>
			<div id="order-asiento" class="col-md-2"><button type="button" class="btn btn-info btn-arrow-right">ASIENTO</button></div>
			<div id="order-identificacion" class="col-md-2"><button type="button" class="btn btn-info btn-arrow-right">IDENTIFICATE</button></div>
			<div id="order-resumen" class="col-md-2"><button type="button" class="btn btn-info btn-arrow-right">RESUMEN</button></div>
			<div id="order-pago" class="col-md-2"><button type="button" class="btn btn-info btn-arrow-right">PAGO</button></div>
			<div id="order-confirmacion" class="col-md-2"><button type="button" class="btn btn-info btn-arrow-right">CONFIRMACIÓN</button></div>
		</div>
		<div class="col-md-1"></div>
	</div>
	<div class="row">
		<div class="col-md-1"></div>
			<div class="col-md-10">
				<div class="row">
				<div class="col-md-5">
					<h1 style="color: #18B8EB; font-weight: bold;">COMPRA EN LÍNEA:</h1>
				</div>
				<div class="col-md-7">
					<p style="margin-top: 17px !important; font-size: 13px !important;">Selecciona tu localidad, elije tu asiento favorito y paga con tarjeta de credito, PayPal, transferencia, deposito bancario o acércate a nuestros puntos de venta. Entregamos tus <span style="font-weight: bold; color:#18B8EB !important;">TICKETS A DOMICILIO</span> en todo el pais y tambien los puedes retirar el dia del evento o en nuestros puntos de venta.
					</p>
				</div>
				</div>
				<div class = 'row'>
					<div class = 'col-md-6'>
						<img src="<?php echo $r; ?>" alt="<?php echo $rowC['strEvento'];?>" class="img-rounded">
					</div>
					<div class = 'col-md-6'>
						<h2 class="padding-top-1x text-normal" style = 'text-transform:capitalize;'><?php echo $rowC['strEvento'];?></h2>
						<span style = 'color:#009FE3;'><i class="fa fa-calendar" aria-hidden="true"></i></span> <?php echo obtenerFechaEnLetra($rowC["dateFecha"]);?><br>
						<span style = 'color:#009FE3;'><i class="fa fa-clock-o" aria-hidden="true"></i></span> <?php echo $rowC["timeHora"];?><br>
						<span style = 'color:#009FE3;'><i class="fa fa-globe" aria-hidden="true"></i></span> <?php echo $rowC["strLugar"];?><br>
							
						<br>
						<?php 
							echo $rowC['strDescripcion'];
						?>
					</div>
				</div>
			</div>
		<div class="col-md-1"></div>
	</div>
	<br>
	<div class="breadcrumb" style="width:100%;text-align:left;background-color:#fff;">
		<div class="row">
			<div class="col-md-1"></div>
			<div class="col-md-10">
				<div class="row">
					<div class="col-md-12">
						<div style="max-width: 1052px !important; font-weight: bold; background-color: #18B8EB !important; color:white !important; text-align: center !important; border-radius: 0px !important; border-color: #18B8EB !important;" class="panel panel-primary">SELECCIONA TU LOCALIDAD EN EL MAPA O EN LA TABLA DE PRECIOS</div>
					</div>
				</div>
		<div class="row">
			<div class="col-md-5" style="margin-right: -42px !important;">
				<?php
					$dir = 'spadmin/';
					$imagen = $rowC['strMapaC'];
					$ruta_mapa = $dir.$imagen;
				?>
				<div class="row">
					<div class="col-md-12">
						<div style="font-weight: bold; background-color: white !important; color:black !important; text-align: center !important; font-size: 14px !important; border-color: #18B8EB; border-radius: 0px !important;" class="panel panel-primary">MAPA DE LOCALIDADES</div>
					</div>
				</div>
				<div id="mapster_wrap_0" style="display: block; position: relative; padding: 0px; width: 456px; height: 355px;">
					<img class="mapster_el" style="width: 456px; height: 355px; opacity: 1;" src="http://ticketfacil.ec/ticket2/<?php echo $ruta_mapa;?>">
					<img class="mapster_el" style="display: none;" src="spadmin/undefined">
					<canvas width="550" height="415" class="mapster_el" style="position: absolute; left: 0px; top: 0px; padding: 0px; border: 0px none;"></canvas>
					<canvas width="550" height="415" class="mapster_el" style="position: absolute; left: 0px; top: 0px; padding: 0px; border: 0px none;"></canvas>
					<img id="localmapa" src="<?php echo $ruta_mapa;?>" alt="localmapa" usemap="#localmapa" style="width: 456px; height: 355px; opacity: 0; position: absolute; left: 0px; top: 0px; padding: 0px; border: 0px none;">
				</div>
				<map name="localmapa" width='300px'>
					<?php 
						$selectAreas = '
											SELECT strCoordenadasB, datestateL, datafullL, intLocalB, intConcB, idButaca 
											FROM Butaca 
											WHERE intConcB = "'.$con.'"
											AND strEstado = "A" 
											and createbyB > 0 
										';
						$resultSelectAreas = mysql_query($selectAreas) or die (mysql_error());
						while($rowArea = mysql_fetch_array($resultSelectAreas)){
					?>
						<area 
							name="" 
							data-state="<?php echo $rowArea['datestateL'];?>" 
							data-full="<?php echo $rowArea['datafullL'];?>" 
							shape="poly" 
							coords="<?php echo $rowArea['strCoordenadasB'];?>" 
							onclick="irLocalidad('<?php echo $rowArea['intLocalB'];?>','<?php echo $rowArea['intConcB'];?>')" 
							alt = "<?php echo $rowArea['datafullL'];?>"
						/>
					<?php 
						}
					?>
				</map>
			</div>
			<div class="col-md-2"></div>
			<div class="col-md-5" style="color:#fff; ">
				<div class="row">
					<div class="col-md-12">
						<div style="font-weight: bold; background-color: white !important; color:black !important; text-align: center !important; font-size: 14px !important; border-color: #18B8EB; border-radius: 0px !important;" class="panel panel-primary">TABLA DE PRECIOS</div>
					</div>
				</div>
				<input id="concierto" name="concierto" value="<?php echo $con;?>" type="hidden">
				<table style = 'color:#000;border: 1px solid #000; font-size: 12px !important;' width = '100%'>
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
						
						$sqlLo = 'select * from Localidad where idConc = "'.$con.'"';
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
								<span class="label label-success" style="cursor:pointer;background-color:#18B8EB;color:#fff;" onclick="irLocalidad('<?php echo $rowLo['idLocalidad'];?>','<?php echo $con;?>');">Abrir</span>
							</td>
						</tr>
					<?php
						}
						
					?>
				</table>
			</div>
		</div>
			</div>
			<div class="col-md-1"></div>
		</div>
		
	</div>
		</div>
	<div class = 'container'>
		<br><br>
		<br>
	</div>
	<hr />
				<div class = 'row'>
				<?php
					$sqlart = 'SELECT * FROM Artista WHERE intIdConciertoA = "'.$con.'"';
					$resArt = mysql_query($sqlart) or die (mysql_error());
					while($rowart = mysql_fetch_array($resArt)){
				?>
					<div class="col-md-4">
						<div class="row">
							<div class="container">
								<div class="col-md-1" style="margin-right: -60px !important;" onclick="window.open('<?php echo $rowart['strFacebookA']; ?>','_blank');">
									<br><span style = 'color:#009FE3;'><i class="fa fa-facebook" aria-hidden="true"></i></span>
								</div>
								<div class="col-md-1" style="margin-right: -60px !important;" onclick="window.open('<?php echo $rowart['strTwitterA']; ?>','_blank');">
									<br><span style = 'color:#009FE3;'><i class="fa fa-twitter" aria-hidden="true"></i></span>
								</div>
								<div class="col-md-1" style="margin-right: -60px !important;" onclick="window.open('<?php echo $rowart['strYoutubeA']; ?>','_blank');">
									<br><span style = 'color:#009FE3;'><i class="fa fa-youtube" aria-hidden="true"></i></span>
								</div>
								<div class="col-md-1" style="margin-right: -60px !important;" onclick="window.open('<?php echo $rowart['strInstagramA']; ?>','_blank');">
									<br><span style = 'color:#009FE3;'><i class="fa fa-instagram" aria-hidden="true"></i></span>
								</div>
								<div class="col-md-2" style="margin-right: -60px !important;">
									<h2 style="font-weight: bold; text-transform:capitalize; font-size: 1.4em"><?php echo $rowart['strNombreA'];?></h2>
								</div>
							</div>
						</div>
					</div>
				<?php
					}
				?>
				</div>
				<br>
	</div>
		<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
		<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
		<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
		<script language="Javascript"  type="text/javascript" src="js/clockCountdown.js"></script>
		<script src="js/bootstrap.js"></script>
		<script src="js/jquery.imagemapster.js"></script>
		<link rel="stylesheet" href="css/bootstrap.css">
		<script src="js/jquery.easing-1.3.js"></script>
		<script src="js/jquery.mousewheel-3.1.12.js"></script>
		<script src="js/jquery.jcarousellite.js"></script>
		<script type="text/javascript" src="https://www.google.com/jsapi"></script>
	<script>
		
		function irLocalidad(idLoc , idCon){
			window.location = '?modulo=localidad&idLoc='+idLoc+'&idCon='+idCon;
		}
		
		$(document).ready(function(){
			var url = window.location.href;
			var last = url.lastIndexOf('?');
			var actualUrl = url.substring(47, 65);
			if (actualUrl == '?modulo=evento&con') {
				$('#order-localidad').html('<button type="button" style="background-color:white!important; color:#46b8da !important;" class="btn btn-info btn-arrow-right">LOCALIDAD</button>');
			}else{
				alert('No');
			}
			$('#localmapa').mapster({
				singleSelect: true,
				render_highlight: {altImage: ruta},
				render_select: false,
				mapkey: 'data-state',
				fill: true,
			});
			
		});
	</script>