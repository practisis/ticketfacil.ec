<?php
	include '../../conexion.php';
	$idConcierto = $_REQUEST['idConcierto'];
	
	$sqlC = 'select * from Concierto where idConcierto = "'.$idConcierto.'" ';
	$resC = mysql_query($sqlC) or die (mysql_error());
	$rowC = mysql_fetch_array($resC);
	
	$sql = 'SELECT * 
			FROM Boleto 
			WHERE idCon = "'.$idConcierto.'" 
			and colB = "1"
			group by idLocB
			ORDER BY idLocB DESC ';
	//echo $sql;class='table table-border'
?>
	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<style>
		th , td {
			text-align:center;
			
		}
		table , tr , th  , td{
			border:1px solid #fff ;
			vertical-align:top;
			font-size:11px;
			color :#fff;
		}
	</style>
	<table  style='font-size:12px;position:relative' width ='100%' border ='1'>
		<tr>
			<th colspan = '11' style='background-color:#171A1B;color:#fff;'>
				VENTAS PAGINA WEB : <?php echo $rowC['strEvento'];?>
			</th>
		</tr>
		<tr>
			<th>
				<table>
					<tr>
						<td>
							
						</td>
					</tr>
					<tr>
						<td>
							LOCALIDAD
						</td>
					</tr>
				</table>
			</th>
			<th>
				<table>
					<tr>
						<td>
							
						</td>
					</tr>
					<tr>
						<td>
							PRECIO
						</td>
					</tr>
				</table>
			</th>
			<th>
				<table width = '100%' border = '1' >
					<tr>
						<td colspan='3' style='text-align:center;'>
							EFECTIVO
						</td>
					</tr>
					<tr>
						<td  width = '33.33%'>
							Tickets
						</td>
						<td  width = '33.33%'>
							Tickets <br/>50%
						</td>
						<td  width = '33.33%'>
							Total
						</td>
					</tr>
				</table>
			</th>
			<th>
				<table width ='100%' border ='1'>
					<tr>
						<td colspan='3' style='text-align:center;'>
							TARJETA
						</td>
					</tr>
					<tr>
						<td  width = '33.33%'>
							Tickets
						</td>
						<td  width = '33.33%'>
							Tickets <br/>50%
						</td>
						<td  width = '33.33%'>
							Total
						</td>
					</tr>
				</table>
			</th>
			
			<th>
				<table width ='100%' border ='1'>
					<tr>
						<td colspan='3' style='text-align:center;font-size:12px;'>
							EF PREV
						</td>
					</tr>
					<tr>
						<td  width = '33.33%'>
							Tickets
						</td>
						<td  width = '33.33%'>
							Tickets <br/>50%
						</td>
						<td  width = '33.33%'>
							Total
						</td>
					</tr>
				</table>
			</th>
			
			<th>
				<table width ='100%' border ='1'>
					<tr>
						<td colspan='3' style='text-align:center;font-size:12px;'>
							TAR PREV
						</td>
					</tr>
					<tr>
						<td  width = '33.33%'>
							Tickets
						</td>
						<td  width = '33.33%'>
							Tickets <br/>50%
						</td>
						<td  width = '33.33%'>
							Total
						</td>
					</tr>
				</table>
			</th>
			
			
			<th>
				TOTAL TICKETS
			</th>
			<th>
				TOTAL COBRADO
			</th>
			<th>
				COMISIÓN TARJETA
			</th>
			<th>
				RETENCIÓN
			</th>
			<!--<th>
				COMISIÓN VENTAS
			</th>-->
			<th>
				TOTAL A CANCELAR
			</th>
		</tr>
<?php
	$res = mysql_query($sql) or die (mysql_error());
	while($row = mysql_fetch_array($res)){
		$sqlL = 'select * from Localidad where idLocalidad = "'.$row['idLocB'].'" order by idLocalidad DESC';
		$resL = mysql_query($sqlL) or die (mysql_error());
		$rowL = mysql_fetch_array($resL);
?>
	<tr>
			<td>
				<?php echo $rowL['strDescripcionL'];?>
			</td>
			<td>
				<?php echo number_format(($rowL['doublePrecioL']),2);?>
			</td>
			<td>
				<table width='100%' border ="1">
					<tr>
						<td width = '33.33%'>
							<?php 
								$sqlEf = 'SELECT count(idBoleto) as idBol
										FROM Boleto 
										WHERE idCon = "'.$idConcierto.'" 
										and colB = "1" 
										and rowB = "1"
										and tercera = 0
										and preventa = 0
										and idLocB = "'.$row['idLocB'].'"
										ORDER BY idLocB DESC 
										';
								//echo $sqlEf;
								$resEF = mysql_query($sqlEf) or die (mysql_error());
								$rowEF = mysql_fetch_array($resEF);
								$numeroEfectivo = $rowEF['idBol'];
								echo $numeroEfectivo;
								$sumaNumeroEfectivo += $numeroEfectivo;
							?>
						</td>
						<td width = '33.33%'>
							<?php 
								$sqlEfT = 'SELECT count(idBoleto) as idBol
										FROM Boleto 
										WHERE idCon = "'.$idConcierto.'" 
										and colB = "1" 
										and rowB = "1"
										and tercera = 1
										and preventa = 0
										and idLocB = "'.$row['idLocB'].'"
										ORDER BY idLocB DESC 
										';
								//echo $sqlEfT;
								$resEFT = mysql_query($sqlEfT) or die (mysql_error());
								$rowEFT = mysql_fetch_array($resEFT);
								$numeroEfectivoTer = $rowEFT['idBol'];
								
								
								//$numeroEfectivoTer = 1;
								echo $numeroEfectivoTer;
								$totaltotalnumeroEfectivoTer += $numeroEfectivoTer;
							?>
						</td>
						<td width = '33.33%'>
							<?php 
								$sumaTotalEfectivo =0;
								$totalEfectivo = (($numeroEfectivo + ($numeroEfectivoTer * 0.5))*$rowL['doublePrecioL']);
								echo $totalEfectivo;
								$sumaTotalEfectivo += $totalEfectivo;
							?>
						</td>
					</tr>
				</table>
			</td>
			<td>
				<table width='100%' width = '100%' border ='1' >
					<tr>
						<td width = '33.33%'>
							<?php
								$sqlTar = 'SELECT count(idBoleto) as idBol
										FROM Boleto 
										WHERE idCon = "'.$idConcierto.'" 
										and colB = "1" 
										and rowB = "2"
										and tercera = 0
										and preventa = 0
										and idLocB = "'.$row['idLocB'].'"
										ORDER BY idLocB DESC 
										';
								//echo $sqlEf;
								$resTar = mysql_query($sqlTar) or die (mysql_error());
								$rowTar = mysql_fetch_array($resTar);
								$numeroTar = $rowTar['idBol'];
								echo $numeroTar;
								$sumaNumeroTar += $numeroTar;
							?>
						</td>
						<td width = '33.33%'>
							<?php 
								// $terceraEdadTar = 0;
								// echo $terceraEdadTar;
								$sqlEfT1 = 'SELECT count(idBoleto) as idBol
										FROM Boleto 
										WHERE idCon = "'.$idConcierto.'" 
										and colB = "1" 
										and rowB = "2"
										and tercera = 1
										and preventa = 0
										and idLocB = "'.$row['idLocB'].'"
										ORDER BY idLocB DESC 
										';
								//echo $sqlEfT;
								$resEFT1 = mysql_query($sqlEfT1) or die (mysql_error());
								$rowEFT1 = mysql_fetch_array($resEFT1);
								$numeroEfectivoTer1 = $rowEFT1['idBol'];
								$totalnumeroEfectivoTer1 += $numeroEfectivoTer1;
								
								//$numeroEfectivoTer = 1;
								echo $numeroEfectivoTer1;
							?>
						</td>
						<td width = '33.33%'>
							<?php 
								$totalTar = (($numeroTar + ($numeroEfectivoTer1 * 0.5))*$rowL['doublePrecioL']);
								echo $totalTar;//."dd";
								$sumaTotalTar += $totalTar;
							?>
						</td>
					</tr>
				</table>
			</td>
			<td>
				<table width='100%' border ="1">
					<tr>
						<td width = '33.33%'>
							<?php 
								$sqlEfPREV = '
										SELECT count(idBoleto) as idBol
										FROM Boleto 
										WHERE idCon = "'.$idConcierto.'" 
										and colB = "1" 
										and rowB = "1"
										and tercera = 0
										and preventa = 1
										and idLocB = "'.$row['idLocB'].'"
										ORDER BY idLocB DESC 
										';
								//echo $sqlEfPREV;
								$resEFPREV = mysql_query($sqlEfPREV) or die (mysql_error());
								$rowEFPREV = mysql_fetch_array($resEFPREV);
								$numeroEfectivoPREV = $rowEFPREV['idBol'];
								echo $numeroEfectivoPREV;
								$sumaNumeroEfectivoPREV += $numeroEfectivoPREV;
								
								
							?>
						</td>
						<td width = '33.33%'>
							<?php 
								$sqlEfTPREV = 'SELECT count(idBoleto) as idBol
										FROM Boleto 
										WHERE idCon = "'.$idConcierto.'" 
										and colB = "1" 
										and rowB = "1"
										and tercera = 1
										and preventa = 1
										and idLocB = "'.$row['idLocB'].'"
										ORDER BY idLocB DESC 
										';
								//echo $sqlEfTPREV;
								$resEFTPREV = mysql_query($sqlEfTPREV) or die (mysql_error());
								$rowEFTPREV = mysql_fetch_array($resEFTPREV);
								$numeroEfectivoTerPREV = $rowEFTPREV['idBol'];
								
								
								//$numeroEfectivoTerPREV = 1;
								echo $numeroEfectivoTerPREV;
								$totaltotalnumeroEfectivoTerPREV += $numeroEfectivoTerPREV;
							?>
						</td>
						<td width = '33.33%'>
							<?php 
								$sumaTotalEfectivoPREVTAR =0;
								$totalEfectivoPREV = (($numeroEfectivoPREV + ($numeroEfectivoTerPREV * 0.5))*$rowL['doublePrecioPreventa']);
								echo $totalEfectivoPREV;
								$sumaTotalEfectivoPREVTAR += $totalEfectivoPREV;
							?>
						</td>
					</tr>
				</table>
			</td>
			<td>
				<table width='100%' border ="1">
					<tr>
						<td width = '33.33%'>
							<?php 
								$sqlEfPREVT = 'SELECT count(idBoleto) as idBol
										FROM Boleto 
										WHERE idCon = "'.$idConcierto.'" 
										and colB = "1" 
										and rowB = "2"
										and tercera = 0
										and preventa = 1
										and idLocB = "'.$row['idLocB'].'"
										ORDER BY idLocB DESC 
										';
								//echo $sqlEfPREVT;
								$resEFPREVT = mysql_query($sqlEfPREVT) or die (mysql_error());
								$rowEFPREVT = mysql_fetch_array($resEFPREVT);
								$numeroEfectivoPREVT = $rowEFPREVT['idBol'];
								echo $numeroEfectivoPREVT;//."rr";
								$sumaNumeroEfectivoPREVT += $numeroEfectivoPREVT;
							?>
						</td>
						<td width = '33.33%'>
							<?php 
								$sqlEfTPREV = 'SELECT count(idBoleto) as idBol
										FROM Boleto 
										WHERE idCon = "'.$idConcierto.'" 
										and colB = "1" 
										and rowB = "2"
										and tercera = 1
										and preventa = 1
										and idLocB = "'.$row['idLocB'].'"
										ORDER BY idLocB DESC 
										';
								//echo $sqlEfTPREV;
								$resEFTPREV = mysql_query($sqlEfTPREV) or die (mysql_error());
								$rowEFTPREV = mysql_fetch_array($resEFTPREV);
								$numeroEfectivoTerPREV22 = $rowEFTPREV['idBol'];
								
								
								//$numeroEfectivoTerPREV = 1;
								echo $numeroEfectivoTerPREV22;//."tt";
								$totaltotalnumeroEfectivoTerPREVTERCERA += $numeroEfectivoTerPREV22;
							?>
						</td>
						<td width = '33.33%'>
							<?php 
								$totalEfectivoPREV2 = (($numeroEfectivoPREVT + ($numeroEfectivoTerPREV22 * 0.5))*$rowL['doublePrecioPreventa']);
								echo $totalEfectivoPREV2;//."aa";
								$sumaTotalEfectivoTAREFEPREV += $totalEfectivoPREV2;
							?>
						</td>
					</tr>
				</table>
			</td>
			<td>
				<?php
					$globalTickets = (($numeroEfectivo + $numeroEfectivoTer)+($numeroTar + $numeroEfectivoTer1) + ($numeroEfectivoPREV+$numeroEfectivoTerPREV) + ($numeroEfectivoTerPREV22 + $numeroEfectivoPREVT));
					echo $globalTickets;
					$sumaGlobalTickets += $globalTickets;
					
				?>
			</td>
			<td>
				<?php
					$globalCobrado = ($totalEfectivo + $totalTar + $totalEfectivoPREV + $totalEfectivoPREV2);
					echo $globalCobrado;
					$sumaGlobalCogrado += $globalCobrado;
					
				?>
			</td>
			<td>
				<?php
					$sqlComWeb ='SELECT * FROM `comi_ret` WHERE `id_con` =  "'.$idConcierto.'" and detalle = "PAGINA WEB" ';
					//echo $sqlComWeb;
					$resComWeb = mysql_query($sqlComWeb) or die (mysql_error());
					$rowComWeb = mysql_fetch_array($resComWeb);
					
					$comi_tar = $rowComWeb['comi_tar'];
					$ret_renta = $rowComWeb['ret_renta'];
					$ret_iva = $rowComWeb['ret_iva'];
					$comisionTarjeta = (($comi_tar * ($totalTar + $totalEfectivoPREV2))/100);
					
					$retencionTarjeta = number_format((((($totalTar + $totalEfectivoPREV2) / 1.14)*($ret_renta/100))+((($totalTar + $totalEfectivoPREV2) - (($totalTar + $totalEfectivoPREV2) / 1.14))*($ret_iva/100))),2);
					
					
					
					echo $comisionTarjeta;
					$sumacomisionTarjeta += $comisionTarjeta;
					
				?>
			</td>
			<td>
				<?php
					//$retencionTarjeta = (($totalTar * $retencionTarjeta)/100);
				//	echo "(".($totalTar + $totalEfectivoPREV2)."*".$ret_renta."/100)+(".($totalTar + $totalEfectivoPREV2)."-".($totalTar + $totalEfectivoPREV2)."/ 1.14) * (".$ret_iva." /100)<br>";
					echo $retencionTarjeta;//."fff";
					$sumaretencionTarjeta += $retencionTarjeta;
				?>
			</td>
			<!--<td>
				<?php
					$comisionVentas = ($totalTar * 0.00);
					echo $comisionVentas;
					$sumacomisionVentas += $comisionVentas;
				?>
			</td>-->
			<td>
				<?php
					$totalAPagar = ($globalCobrado - $comisionTarjeta - $retencionTarjeta - $comisionVentas);
					echo $totalAPagar;
					$sumatotalAPagar += $totalAPagar;
				?>
			</td>
			
	</tr>
<?php
	}
?>
	<tr>
			<td>
				TOTAL
			</td>
			<td>
				<?php 
					echo $totalBoletos."";
				?>
			</td>
			<td>
				<table width='100%'>
					<tr>
						<td width = '33.33%'>
							<?php
								
								echo $sumaNumeroEfectivo;
								
							?>
						</td>
						<td width = '33.33%'>
							<?php 
								//$terceraEdadEf = 0;
								echo $totaltotalnumeroEfectivoTer;
							?>
						</td>
						<td width = '33.33%'>
							<?php 
								if($sumaTotalEfectivo == ''){
									$sumaTotalEfectivo = 0;
								}
								echo $sumaTotalEfectivo."ee";
								echo "<input type = 'hidden' id = 'efec_web' value = '".$sumaTotalEfectivo."' />";
							?>
						</td>
					</tr>
				</table>
			</td>
			<td>
				<table width='100%'>
					<tr>
						<td width = '33.33%'>
							<?php
								echo $sumaNumeroTar;
							?>
						</td>
						<td width = '33.33%'>
							<?php 
								//$terceraEdadTar = 0;
								echo $totalnumeroEfectivoTer1;
							?>
						</td>
						<td width = '33.33%'>
							<?php 
								if($sumaTotalTar == ''){
									$sumaTotalTar = 0;
								}
								echo $sumaTotalTar."tt";
								echo "<input type = 'hidden' id = 'tar_web' value = '".$sumaTotalTar."' />";
							?>
						</td>
					</tr>
				</table>
			</td>
			<td>
				<table width='100%'>
					<tr>
						<td width = '33.33%'>
							<?php echo $sumaNumeroEfectivoPREV;?>
						</td>
						<td width = '33.33%'>
							<?php 
								//$terceraEdadTar = 0;
								echo $totaltotalnumeroEfectivoTerPREV;
							?>
						</td>
						<td width = '33.33%'>
							<?php 
								if($sumaTotalEfectivoPREVTAR == ''){
									$sumaTotalEfectivoPREVTAR =0;
								}
								echo $sumaTotalEfectivoPREVTAR."eee";
								echo "<input type = 'hidden' id = 'efec_web_prev' value = '".$sumaTotalEfectivoPREVTAR."' />";
							?>
						</td>
					</tr>
				</table>
				
			</td>
			<td>
				<table width='100%'>
					<tr>
						<td width = '33.33%'>
							<?php echo $sumaNumeroEfectivoPREVT;?>
						</td>
						<td width = '33.33%'>
							<?php 
								$terceraEdadTar = 0;
								echo $totaltotalnumeroEfectivoTerPREVTERCERA;
							?>
						</td>
						<td width = '33.33%'>
							<?php 
								if($sumaTotalEfectivoTAREFEPREV == ''){
									$sumaTotalEfectivoTAREFEPREV = 0;
								}
								echo $sumaTotalEfectivoTAREFEPREV."ttt";
								echo "<input type = 'hidden' id = 'tar_web_prev' value = '".$sumaTotalEfectivoTAREFEPREV."' />";
							?>
						</td>
					</tr>
				</table>
			</td>
			<td>
				<?php
					echo $sumaGlobalTickets;
				?>
			</td>
			<td>
				<?php
					echo $sumaGlobalCogrado;
				?>
			</td>
			<td>
				<?php
					echo $sumacomisionTarjeta;
				?>
			</td>
			<td>
				<?php
					echo $sumaretencionTarjeta;
				?>
			</td>
			<!--<td>
				<?php
					echo $sumacomisionVentas;
				?>
			</td>-->
			<td>
				<?php
					echo $sumatotalAPagar;
				?>
			</td>
			
			
		</tr>
	</table>
	<br/>
	
	<!--  cadena-->
	<?php	
	$sql1 = 'SELECT * 
			FROM Boleto 
			WHERE idCon = "'.$idConcierto.'" 
			and colB = "2"
			group by idLocB
			ORDER BY idLocB DESC ';
	//echo $sql1;
?>
	<table  style='font-size:12px;position:relative' width ='100%' border ='1' id = 'cobrosCCC'>
		<tr>
			<th colspan = '11' style='background-color:#171A1B;color:#fff;'>
				VENTAS CADENA COMERCIAL : <?php echo $rowC['strEvento'];?>
			</th>
		</tr>
		<tr>
			<th>
				<table>
					<tr>
						<td>
							
						</td>
					</tr>
					<tr>
						<td>
							LOCALIDAD
						</td>
					</tr>
				</table>
			</th>
			<th>
				<table>
					<tr>
						<td>
							
						</td>
					</tr>
					<tr>
						<td>
							PRECIO
						</td>
					</tr>
				</table>
			</th>
			<th>
				<table width = '100%' border = '1' >
					<tr>
						<td colspan='3' style='text-align:center;'>
							EFECTIVO
						</td>
					</tr>
					<tr>
						<td  width = '33.33%'>
							Tickets
						</td>
						<td  width = '33.33%'>
							Tickets <br/>50%
						</td>
						<td  width = '33.33%'>
							Total
						</td>
					</tr>
				</table>
			</th>
			<th>
				<table width ='100%' border ='1'>
					<tr>
						<td colspan='3' style='text-align:center;'>
							TARJETA
						</td>
					</tr>
					<tr>
						<td  width = '33.33%'>
							Tickets
						</td>
						<td  width = '33.33%'>
							Tickets <br/>50%
						</td>
						<td  width = '33.33%'>
							Total
						</td>
					</tr>
				</table>
			</th>
			
			<th>
				<table width ='100%' border ='1'>
					<tr>
						<td colspan='3' style='text-align:center;font-size:12px;'>
							EF PREV
						</td>
					</tr>
					<tr>
						<td  width = '33.33%'>
							Tickets
						</td>
						<td  width = '33.33%'>
							Tickets <br/>50%
						</td>
						<td  width = '33.33%'>
							Total
						</td>
					</tr>
				</table>
			</th>
			
			<th>
				<table width ='100%' border ='1'>
					<tr>
						<td colspan='3' style='text-align:center;font-size:12px;'>
							TAR PREV
						</td>
					</tr>
					<tr>
						<td  width = '33.33%'>
							Tickets
						</td>
						<td  width = '33.33%'>
							Tickets <br/>50%
						</td>
						<td  width = '33.33%'>
							Total
						</td>
					</tr>
				</table>
			</th>
			<th>
				TOTAL TICKETS
			</th>
			<th>
				TOTAL COBRADO
			</th>
			<th>
				COMISIÓN TARJETA
			</th>
			<th>
				RETENCIÓN
			</th>
			<!--<th>
				COMISIÓN VENTAS
			</th>-->
			<th>
				TOTAL A CANCELAR
			</th>
		</tr>
<?php
	$res1 = mysql_query($sql1) or die (mysql_error());
	while($row1 = mysql_fetch_array($res1)){
		$sqlL1 = 'select * from Localidad where idLocalidad = "'.$row1['idLocB'].'" order by idLocalidad DESC ';
		$resL1 = mysql_query($sqlL1) or die (mysql_error());
		$rowL1 = mysql_fetch_array($resL1);
?>
	<tr>
			<td>
				<?php echo $rowL1['strDescripcionL'];?>
			</td>
			<td>
				<?php echo number_format(($rowL1['doublePrecioL']),2);?>
			</td>
			<td>
				<table width='100%' border ="1">
					<tr>
						<td width = '33.33%'>
							<?php 
								$sqlEf1 = 'SELECT count(idBoleto) as idBol
										FROM Boleto 
										WHERE idCon = "'.$idConcierto.'" 
										and rowB = "1"
										and colB = "2" 
										and tercera = 0
										and preventa = 0
										and idLocB = "'.$row1['idLocB'].'"
										ORDER BY idLocB DESC 
										';
								//echo $sqlEf1;
								$resEF1 = mysql_query($sqlEf1) or die (mysql_error());
								$rowEF1 = mysql_fetch_array($resEF1);
								$numeroEfectivo1 = $rowEF1['idBol'];
								echo $numeroEfectivo1;
								$sumaNumeroEfectivo1 += $numeroEfectivo1;
							?>
						</td>
						<td width = '33.33%'>
							<?php 
								$sqlEfT1 = 'SELECT count(idBoleto) as idBol
										FROM Boleto 
										WHERE idCon = "'.$idConcierto.'" 
										and rowB = "1"
										and colB = "2" 
										and tercera = 1
										and preventa = 0
										and idLocB = "'.$row1['idLocB'].'"
										ORDER BY idLocB DESC 
										';
								//echo $sqlEfT1;
								$resEFT1 = mysql_query($sqlEfT1) or die (mysql_error());
								$rowEFT1 = mysql_fetch_array($resEFT1);
								$numeroEfectivoTer1 = $rowEFT1['idBol'];
								
								
								//$numeroEfectivoTer = 1;
								echo $numeroEfectivoTer1;
								$totaltotalnumeroEfectivoTer1 += $numeroEfectivoTer1;
							?>
						</td>
						<td width = '33.33%'>
							<?php 
								
								$totalEfectivo1 = (($numeroEfectivo1 + ($numeroEfectivoTer1 * 0.5))*$rowL1['doublePrecioL']);
								echo $totalEfectivo1."ggg";
								$sumaTotalEfectivo1 += $totalEfectivo1;
							?>
						</td>
					</tr>
				</table>
			</td>
			<td>
				<table width='100%' width = '100%' border ='1' >
					<tr>
						<td width = '33.33%'>
							<?php
								$sqlTar1 = 'SELECT count(idBoleto) as idBol
										FROM Boleto 
										WHERE idCon = "'.$idConcierto.'" 
										and rowB = "2"
										and colB = "2" 
										and tercera = 0
										and preventa = 0
										and idLocB = "'.$row1['idLocB'].'"
										ORDER BY idLocB DESC 
										';
								//echo $sqlEf;
								$resTar1 = mysql_query($sqlTar1) or die (mysql_error());
								$rowTar1 = mysql_fetch_array($resTar1);
								$numeroTar1 = $rowTar1['idBol'];
								echo $numeroTar1;
								$sumaNumeroTar1 += $numeroTar1;
							?>
						</td>
						<td width = '33.33%'>
							<?php 
								// $terceraEdadTar = 0;
								// echo $terceraEdadTar;
								$sqlEfT11 = 'SELECT count(idBoleto) as idBol
										FROM Boleto 
										WHERE idCon = "'.$idConcierto.'" 
										and rowB = "2"
										and colB = "2" 
										and tercera = 1
										and preventa = 0
										and idLocB = "'.$row1['idLocB'].'"
										ORDER BY idLocB DESC 
										';
								//echo $sqlEfT;
								$resEFT11 = mysql_query($sqlEfT11) or die (mysql_error());
								$rowEFT11 = mysql_fetch_array($resEFT11);
								$numeroEfectivoTer11 = $rowEFT11['idBol'];
								$totalnumeroEfectivoTer11 += $numeroEfectivoTer11;
								
								//$numeroEfectivoTer = 1;
								echo $numeroEfectivoTer11;
							?>
						</td>
						<td width = '33.33%'>
							<?php 
								$totalTar1 = (($numeroTar1 + ($numeroEfectivoTer11 * 0.5))*$rowL1['doublePrecioL']);
								echo $totalTar1;//."hhh";
								$sumaTotalTar1 += $totalTar1;
							?>
						</td>
					</tr>
				</table>
			</td>
			
			<td>
				<table width='100%' border ="1">
					<tr>
						<td width = '33.33%'>
							<?php 
								$sqlEfPREV1 = '
										SELECT count(idBoleto) as idBol
										FROM Boleto 
										WHERE idCon = "'.$idConcierto.'" 
										and rowB = "1"
										and colB = "2" 
										and tercera = 0
										and preventa = 1
										and idLocB = "'.$row1['idLocB'].'"
										ORDER BY idLocB DESC 
										';
								//echo $sqlEfPREV1;
								$resEFPREV1 = mysql_query($sqlEfPREV1) or die (mysql_error());
								$rowEFPREV1 = mysql_fetch_array($resEFPREV1);
								$numeroEfectivoPREV1 = $rowEFPREV1['idBol'];
								echo $numeroEfectivoPREV1;//."gg";
								$sumaNumeroEfectivoPREV1 += $numeroEfectivoPREV1;
								
								
							?>
						</td>
						<td width = '33.33%'>
							<?php 
								$sqlEfTPREV1 = 'SELECT count(idBoleto) as idBol
										FROM Boleto 
										WHERE idCon = "'.$idConcierto.'" 
										and rowB = "1"
										and colB = "2" 
										and tercera = 1
										and preventa = 1
										and idLocB = "'.$row1['idLocB'].'"
										ORDER BY idLocB DESC 
										';
								//echo $sqlEfTPREV1;
								$resEFTPREV1 = mysql_query($sqlEfTPREV1) or die (mysql_error());
								$rowEFTPREV1 = mysql_fetch_array($resEFTPREV1);
								$numeroEfectivoTerPREV1 = $rowEFTPREV1['idBol'];
								
								
								//$numeroEfectivoTerPREV1 = 1;
								echo $numeroEfectivoTerPREV1;
								$totaltotalnumeroEfectivoTerPREV1 += $numeroEfectivoTerPREV1;
							?>
						</td>
						<td width = '33.33%'>
							<?php 
								//echo $rowL['doublePrecioPreventa']."prev";
								
								$totalEfectivoPREV1 = (($numeroEfectivoPREV1 + ($numeroEfectivoTerPREV1 * 0.5))*$rowL1['doublePrecioPreventa']);
								echo $totalEfectivoPREV1;
								$sumaTotalEfectivoPRE1 += $totalEfectivoPREV1;
							?>
						</td>
					</tr>
				</table>
			</td>
			
			<td>
				<table width='100%' border ="1">
					<tr>
						<td width = '33.33%'>
							<?php 
								$sqlEfPREV2 = '
										SELECT count(idBoleto) as idBol
										FROM Boleto 
										WHERE idCon = "'.$idConcierto.'" 
										and rowB = "2"
										and colB = "2" 
										and tercera = 0
										and preventa = 1
										and idLocB = "'.$row1['idLocB'].'"
										ORDER BY idLocB DESC 
										';
								//echo $sqlEfPREV2;
								$resEFPREV2 = mysql_query($sqlEfPREV2) or die (mysql_error());
								$rowEFPREV2 = mysql_fetch_array($resEFPREV2);
								$numeroEfectivoPREV2 = $rowEFPREV2['idBol'];
								echo $numeroEfectivoPREV2;
								$sumaNumeroEfectivoPREV2 += $numeroEfectivoPREV2;
								
								
							?>
						</td>
						<td width = '33.33%'>
							<?php 
								$sqlEfTPREV2 = 'SELECT count(idBoleto) as idBol
										FROM Boleto 
										WHERE idCon = "'.$idConcierto.'" 
										and rowB = "2"
										and colB = "2" 
										and tercera = 1
										and preventa = 1
										and idLocB = "'.$row1['idLocB'].'"
										ORDER BY idLocB DESC 
										';
								//echo $sqlEfTPREV2;
								$resEFTPREV2 = mysql_query($sqlEfTPREV2) or die (mysql_error());
								$rowEFTPREV2 = mysql_fetch_array($resEFTPREV2);
								$numeroEfectivoTerPREV2 = $rowEFTPREV2['idBol'];
								
								
								//$numeroEfectivoTerPREV2 = 1;
								echo $numeroEfectivoTerPREV2;
								$totaltotalnumeroEfectivoTerPREV2 += $numeroEfectivoTerPREV2;
							?>
						</td>
						<td width = '33.33%'>
							<?php 
								$totalEfectivoPREV2 = (($numeroEfectivoPREV2 + ($numeroEfectivoTerPREV2 * 0.5))*$rowL1['doublePrecioPreventa']);
								echo $totalEfectivoPREV2;//."ggg";
								$sumaTotalEfectivoPREV2 += $totalEfectivoPREV2;
							?>
						</td>
					</tr>
				</table>
			</td>
			<td>
				<?php
					$globalTickets1 = (($numeroEfectivo1 + $numeroEfectivoTer1)+($numeroTar1 + $numeroEfectivoTer11) + ($numeroEfectivoPREV1 + $numeroEfectivoTerPREV1) + ($numeroEfectivoPREV2 + $numeroEfectivoTerPREV2) );
					echo $globalTickets1;
					$sumaGlobalTickets1 += $globalTickets1;
					
				?>
			</td>
			<td>
				<?php
					//echo $totalEfectivo1."<br>".$totalTar1."<br>".$totalEfectivoPREV2."<br>".$totalEfectivoPREV1."<br><br/>";
					$globalCobrado1 = ($totalEfectivo1 + $totalTar1 + $totalEfectivoPREV2 + $totalEfectivoPREV1);
					echo $globalCobrado1;//."zz";
					$sumaGlobalCogrado1 += $globalCobrado1;
					
				?>
			</td>
			<td>
				<?php
					$sqlComWeb1 ='SELECT * FROM `comi_ret` WHERE `id_con` =  "'.$idConcierto.'" and detalle = "cadena comercial" ';
				//	echo $sqlComWeb1;
					$resComWeb1 = mysql_query($sqlComWeb1) or die (mysql_error());
					$rowComWeb1 = mysql_fetch_array($resComWeb1);
					
					$comi_tar1 = $rowComWeb1['comi_tar'];
					$ret_renta1 = $rowComWeb1['ret_renta'];
					$ret_iva1 = $rowComWeb1['ret_iva'];
					$comisionTarjeta1 = (($comi_tar1 * ($totalTar1 + $totalEfectivoPREV2))/100);
					$retencionTarjeta1 = number_format((((($totalTar1 + $totalEfectivoPREV2) / 1.14)*($ret_renta1/100))+((($totalTar1 + $totalEfectivoPREV2) - (($totalTar1 + $totalEfectivoPREV2) / 1.14))*($ret_iva1/100))),2);
					
					
					
					echo $comisionTarjeta1;
					$sumacomisionTarjeta1 += $comisionTarjeta1;
					
				?>
			</td>
			<td>
				<?php
					//$retencionTarjeta = (($totalTar * $retencionTarjeta)/100);
					//echo "(".($totalTar1 + $totalEfectivoPREV2)."*".$ret_renta1."/100)+(".($totalTar1 + $totalEfectivoPREV2)."-".($totalTar1 + $totalEfectivoPREV2)."/ 1.14) * (".$ret_iva1." /100)<br>";
					echo $retencionTarjeta1;
					$sumaretencionTarjeta1 += $retencionTarjeta1;
				?>
			</td>
			<!--<td>
				<?php
					$comisionVentas1 = ($totalTar1 * 0.00);
					echo $comisionVentas1;
					$sumacomisionVentas1 += $comisionVentas1;
				?>
			</td>-->
			<td>
				<?php
					$totalAPagar1 = ($globalCobrado1 - $comisionTarjeta1 - $retencionTarjeta1 - $comisionVentas1);
					echo $totalAPagar1;
					$sumatotalAPagar1 += $totalAPagar1;
				?>
			</td>
			
	</tr>
<?php
	}
?>
	<tr>
			<td>
				TOTAL
			</td>
			<td>
				<?php 
					echo $totalBoletos1;
				?>
			</td>
			<td>
				<table width='100%'>
					<tr>
						<td width = '33.33%'>
							<?php
								
								echo $sumaNumeroEfectivo1;
								
							?>
						</td>
						<td width = '33.33%'>
							<?php 
								
								echo $totaltotalnumeroEfectivoTer1;
							?>
						</td>
						<td width = '33.33%'>
							<?php 
								if($sumaTotalEfectivo1 == ''){
									$sumaTotalEfectivo1 = 0;
								}
								echo $sumaTotalEfectivo1."ee1";
								echo "<input type = 'hidden' id = 'efec_cad_com' value = '".$sumaTotalEfectivo1."' />";
							?>
						</td>
					</tr>
				</table>
			</td>
			<td>
				<table width='100%'>
					<tr>
						<td width = '33.33%'>
							<?php
								echo $sumaNumeroTar1;
							?>
						</td>
						<td width = '33.33%'>
							<?php 
								//$terceraEdadTar1 = 0;
								echo $totalnumeroEfectivoTer11;
							?>
						</td>
						<td width = '33.33%'>
							<?php 
								if($sumaTotalTar1 == ''){
									$sumaTotalTar1 = 0;
								}
								echo $sumaTotalTar1."tt1";
								echo "<input type = 'hidden' id = 'tar_cad_com' value = '".$sumaTotalTar1."' />";
							?>
						</td>
					</tr>
				</table>
			</td>
			
			
			<td>
				<table width='100%'>
					<tr>
						<td width = '33.33%'>

							<?php
								echo $sumaNumeroEfectivoPREV1;
							?>
						</td>
						<td width = '33.33%'>
							<?php 
								//$terceraEdadTar1 = 0;
								echo $totaltotalnumeroEfectivoTerPREV1;
							?>
						</td>
						<td width = '33.33%'>
							<?php 
								if($sumaTotalEfectivoPRE1 == ''){
									$sumaTotalEfectivoPRE1 = 0;
								}
								echo $sumaTotalEfectivoPRE1."eee1";
								echo "<input type = 'hidden' id = 'efec_cad_com_pre' value = '".$sumaTotalEfectivoPRE1."' />";
							?>
						</td>
					</tr>
				</table>
			</td>
			
			
			<td>
				<table width='100%'>
					<tr>
						<td width = '33.33%'>
							<?php
								echo $sumaNumeroEfectivoPREV2;
							?>
						</td>
						<td width = '33.33%'>
							<?php 
								//$terceraEdadTar1 = 0;
								echo $totaltotalnumeroEfectivoTerPREV2;//'aqui';
							?>
						</td>
						<td width = '33.33%'>
							<?php 
								if($sumaTotalEfectivoPREV2 == ''){
									$sumaTotalEfectivoPREV2 = 0;
								}
								echo $sumaTotalEfectivoPREV2."ttt1";
								echo "<input type = 'hidden' id = 'tar_cad_com_prev' value = '".$sumaTotalEfectivoPREV2."' />";
							?>
						</td>
					</tr>
				</table>
			</td>
			
			
			<td>
				<?php
					echo $sumaGlobalTickets1;
				?>
			</td>
			<td>
				<?php
					echo $sumaGlobalCogrado1;
				?>
			</td>
			<td>
				<?php
					echo $sumacomisionTarjeta1;
				?>
			</td>
			<td>
				<?php
					echo $sumaretencionTarjeta1;
				?>
			</td>
			<!--<td>
				<?php
					echo $sumacomisionVentas1;
				?>
			</td>-->
			<td>
				<?php
					echo $sumatotalAPagar1;
				?>
			</td>
			
			
		</tr>
	</table>
	<br/>
	
	<br/>
	
	<script>
		$( document ).ready(function() {
			console.log( "ready!" );
			var ancho = $('#contRepAjax').width()+40;
			var alto = ($('#cobrosCCC').height())+320;
			console.log('ancho : '+ ancho + ' alto : ' + alto );
			$('.ifr22').css('width',ancho);
			$('.ifr22').css('height',alto);
		});
		
	</script>