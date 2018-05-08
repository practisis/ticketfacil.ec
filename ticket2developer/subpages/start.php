<?php
	session_start();
	$estado = 'Activo';
	$hoy = date('Y-m-d');
	include 'conexion.php';
	
	$sqlU = 'update Concierto set es_publi = 0 where dateFecha < "'.$hoy.'"';
	 // echo $sqlU."<br><br>";
	$resU = mysql_query($sqlU) or die (mysql_error());
	
	
	
	$sqlU1 = 'update Concierto set es_publi = 1 where dateFecha > "'.$hoy.'"';
	 // echo $sqlU1."<br><br>";
	$resU1 = mysql_query($sqlU1) or die (mysql_error());
	
	
	
	
	echo '<input type="hidden" id="data" value="1" />';
	if($_SESSION['autentica']== 'tFDiS759'){
		include 'distribuidor/distribuidorindex.php';
	}else{

	$selectSliderStart1 = "SELECT c.idConcierto, c.strImagen , c.es_publi , c.strEvento
				FROM Concierto as c , banner as b
				WHERE c.idConcierto = b.id_con
				ORDER BY dateFecha DESC";
	// echo $selectSliderStart1;
	$resultSlideStar1 = $gbd -> prepare($selectSliderStart1);
	$resultSlideStar1 -> execute();
	$num_imagenes1 = $resultSlideStar1 -> rowCount();
?>
<div class="bs-example">
    <div id="myCarousel" class="carousel slide" data-interval="3000" data-ride="carousel">
    	<!-- Carousel indicators -->
        <ol class="carousel-indicators">
            <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
			
			<?php for($i = 1; $i < $num_imagenes1; $i++){?>
            <li data-target="#myCarousel" data-slide-to="<?php echo $i;?>"></li>
			<?php }?>
        </ol>   
		
        <div class="carousel-inner" style = 'height: 350px' >
			<?php 
				$counter = 1; 
				while($rowSlide = $resultSlideStar1 -> fetch(PDO::FETCH_ASSOC)){
				$img = $rowSlide['strImagen'];
				$ruta = 'https://www.ticketfacil.ec/ticket2/spadmin/';
				$ruta = $ruta.$img;
				$es_publi = $rowSlide['es_publi'];
				
				if($es_publi == 2){
					$envioRuta = 'des_pub';
				}else{
					$envioRuta = 'des_concierto';
				}
				if($counter == 1){
					echo '<div class="active item">
							<a href="?modulo='.$envioRuta.'&con='.$rowSlide['idConcierto'].'">
								<img src="'.$ruta.'" style="width:100%; overflow:hidden;" alt = "'.$rowSlide['strEvento'].'" title = "asiste al evento : '.$rowSlide['strEvento'].',  que hemos preparado para ti!"/>
							</a>
						</div>';
				}else{
					echo '<div class="item">
							<a href="?modulo='.$envioRuta.'&con='.$rowSlide['idConcierto'].'">
								<img src="'.$ruta.'" style="width:100%; overflow:hidden;" alt = "'.$rowSlide['strEvento'].'"/>
							</a>
						</div>';
				}
				$counter++;
			}?>
        </div>
        <a class="carousel-control left" href="#myCarousel" data-slide="prev">
            <span class="glyphicon glyphicon-chevron-left"></span>
        </a>
        <a class="carousel-control right" href="#myCarousel" data-slide="next">
            <span class="glyphicon glyphicon-chevron-right"></span>
        </a>
    </div>
</div>
<div style="width:100%; position:relative; margin-top:-5px;">
	<div id="jcl">
		<div class="custom-container default" style="margin:0px 0px 20px;">
			<a href="#" class="prev" style="text-decoration:none; cursor:pointer;">&lsaquo;</a>
			<div style="width:100%; overflow:hidden;">
				<div class="carousel" style="margin-left:20px;">
					<ul>
						<?php 
							while($rowtriple = $resulttriple -> fetch(PDO::FETCH_ASSOC)){
								$es_publi = $rowtriple['es_publi'];
								if($es_publi == 2){
									$envioRuta = 'des_pub';
								}else{
									$envioRuta = 'des_concierto';
								}
								$imgtriple = $rowtriple['strImagen'];
								$rutatriple = 'https://www.ticketfacil.ec/ticket2/spadmin/';
								$rutatriple = $rutatriple.$imgtriple;	
						?>
								<li>
									<a href="?modulo=<?php echo $envioRuta;?>&con=<?php echo $rowtriple['idConcierto'];?>">
										<img src="<?php echo $rutatriple;?>" alt = '<?php echo "Evento : ".$rowtriple['strEvento'];?>' title = 'asiste al evento : <?php echo $rowtriple['strEvento'];?> , que hemos creado para ti' >
									</a>
								</li>
						<?php 
							}
						?>
					</ul>
				</div>
			</div>
			<a href="#" class="next">&rsaquo;</a>
			<div class="clear" style="text-decoration:none; cursor:pointer;"></div>
		</div>
	</div>
</div>
<div class="proximosConciertos">
	<p>Pr&oacute;ximos Conciertos</p>
</div>    
<?php
	
	while($rowConcierto = $resultDatosConciertoStart1 -> fetch(PDO::FETCH_ASSOC)){
		$es_publi = $rowConcierto['es_publi'];
		//echo $es_publi."hola";
		if($es_publi == 2){
			$envioRuta = 'des_pub';
		}else{
			$envioRuta = 'des_concierto';
		}
		$imgCon = $rowConcierto['strImagen'];
		$rutaCon = 'https://www.ticketfacil.ec/ticket2/spadmin/';
		$rutaCon = $rutaCon.$imgCon;
		echo '<div class="col-sm-3" style ="height:260px">
				<div style="background-color:#fff">
					<div style="height:150px; overflow:hidden; background-color:#000; text-align:center; position:relative;">
						<a style="text-decoration:none;" href="?modulo='.$envioRuta.'&con='.$rowConcierto['idConcierto'].'">
							<img src="'.$rutaCon.'" style="height:100%; overflow:hidden;" alt = "'.$rowConcierto['strEvento'].'"  title = "Asiste al evento :  '.$rowConcierto['strEvento'].' , que hemos creado para ti!" />
						</a>
						<div style="position:absolute; background-color:#050808; margin-top:-60px; margin-left:10px; color:#fff; padding:10px 15px;">
							<strong>'.$rowConcierto['strEvento'].'</strong>
						</div>
					</div>
					<div style="font-size:12px; padding:10px 15px; color:#808284;">
						<p>
							'.$rowConcierto['dateFecha'].'<br>
							'.$rowConcierto['strLugar'].'
						</p>
					</div>
					<div style="margin-top:-20px; padding:10px;">
						<table style="width:100%; color:#808284;">
							<tr>
								<td>
									<a style="text-decoration:none;" href="?modulo='.$envioRuta.'&con='.$rowConcierto['idConcierto'].'">
										<img src="imagenes/masinfo.png" style="max-width:25px;" />&nbsp;m&aacute;s info
									</a>
								</td> 
								<td style="text-align:right;">
									<img src="imagenes/carrito.png" style="max-width:30px;" />
								</td>
							</tr>
						</table>
					</div>
				</div>
			</div>';
	}
	echo '</div>';
?>
	<div style = 'background-color:#282b2d;' class = 'row'>
		<br/><br/>
		<div class='proximosConciertos'>
			<p>Eventos Realizados</p>
		</div>    
		
		<br/>
		
		<?php
			
			$sql = 'SELECT * FROM Concierto WHERE dateFecha < "'.$hoy.'" AND strEstado = "'.$estado.'" and costoenvioC > 0 ORDER BY dateFecha DESC';
			//echo $sql;
			$res = mysql_query($sql) or die (mysql_error());
			while($row = mysql_fetch_array($res)){
				$es_publi = $row['es_publi'];
				if($es_publi == 2){
					$envioRuta = 'des_pub';
				}else{
					$envioRuta = 'des_concierto';
				}
				$imgCon = $row['strImagen'];
				$rutaCon = 'https://www.ticketfacil.ec/ticket2/spadmin/';
				$rutaCon = $rutaCon.$imgCon;
				echo'
					<div class="col-sm-3" style ="height:260px">
					<div style="background-color:#fff">
						<div style="height:150px; overflow:hidden; background-color:#000; text-align:center; position:relative;">
							<a style="text-decoration:none;" href="?modulo='.$envioRuta.'&con='.$row['idConcierto'].'">
								<img src="'.$rutaCon.'" style="height:100%; overflow:hidden;" alt = "'.$row['strEvento'].'"/>
							</a>
							<div style="position:absolute; background-color:#050808; margin-top:-60px; margin-left:10px; color:#fff; padding:10px 15px;">
								<strong>'.$row['strEvento'].'</strong>
							</div>
						</div>
						<div style="font-size:12px; padding:10px 15px; color:#808284;">
							<p>
								'.$row['dateFecha'].'<br>
								'.$row['strLugar'].'
							</p>
						</div>
						<div style="margin-top:-20px; padding:10px;">
							<table style="width:100%; color:#808284;">
								<tr>
									<td>
										<a style="text-decoration:none;" href="?modulo='.$envioRuta.'&con='.$row['idConcierto'].'">
											<img src="imagenes/masinfo.png" style="max-width:25px;" />&nbsp;m&aacute;s info
										</a>
									</td> 
									<td style="text-align:right;">
										<img src="imagenes/carrito.png" style="max-width:30px;" />
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
				';
			}
		?>
	</div>
<script type="text/javascript">
	$(function() {
		$(".default .carousel").jCarouselLite({
			btnNext: ".default .next",
			btnPrev: ".default .prev"
		});
	});
</script>	               
<?php
}
?>