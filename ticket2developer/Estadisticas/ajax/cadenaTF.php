<?php	
	session_start();
	include '../../conexion.php';
	$idConcierto = $_REQUEST['idConcierto'];
	
	$sqlC = 'select * from Concierto where idConcierto = "'.$idConcierto.'" '; 
	$resC = mysql_query($sqlC) or die (mysql_error());
	$rowC = mysql_fetch_array($resC);
	
	
	$sql1 = 'SELECT * FROM `Boleto` WHERE `idCon` = "'.$idConcierto.'"  AND `colB` = 2 AND `id_usu_venta` = "'.$_SESSION['iduser'].'" group by idLocB ORDER BY `idBoleto` DESC ';
	echo $sql1;
?>
	<style>
		table{
			font-size:11px !important;
			color:#fff;
		}
		th , td {
			text-align:center;
			
		}
		table , tr , th  , td{
			border: 1px solid #fff;
			vertical-align:top;
		}
	</style>
	<table  style='font-size:12px;position:relative' width ='100%' border ="">
		<tr>
			<th colspan = '11' style='background-color:#171A1B;color:#fff;'>
				VENTAS CADENA COMERCIAL (p.venta Dessire): <?php echo $rowC['strEvento'];?> 
			</th>
		</tr>
		<tr>
			<th>
				<table>
					
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
							PRECIO
						</td>
					</tr>
				</table>
			</th>
			<th>
				<table width = '100%' border = '0' >
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
				<table width ='100%' border ="">
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
				<table width ='100%' border ="">
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
				<table width ='100%' border ="">
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
		$sqlL1 = 'select * from Localidad where idLocalidad = "'.$row1['idLocB'].'" ';
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
				<table width='100%' border ="">
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
										and id_usu_venta = "'.$_SESSION['iduser'].'"
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
										and id_usu_venta = "'.$_SESSION['iduser'].'"
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
								echo $totalEfectivo1;
								$sumaTotalEfectivo1 += $totalEfectivo1;
							?>
						</td>
					</tr>
				</table>
			</td>
			<td>
				<table width='100%' width = '100%' border ="" >
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
										and id_usu_venta = "'.$_SESSION['iduser'].'"
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
										and id_usu_venta = "'.$_SESSION['iduser'].'"
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
								echo $totalTar1;//."mmm";
								$sumaTotalTar1 += $totalTar1;
							?>
						</td>
					</tr>
				</table>
			</td>
			
			<td>
				<table width='100%' border ="">
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
										and id_usu_venta = "'.$_SESSION['iduser'].'"
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
										and id_usu_venta = "'.$_SESSION['iduser'].'"
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
								echo $totalEfectivoPREV1;//."kkk";
								$sumaTotalEfectivoPRE1 += $totalEfectivoPREV1;
							?>
						</td>
					</tr>
				</table>
			</td>
			
			<td>
				<table width='100%' border ="">
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
										and id_usu_venta = "'.$_SESSION['iduser'].'"
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
										and id_usu_venta = "'.$_SESSION['iduser'].'"
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
								echo $totalEfectivoPREV2;//."lll";
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
					$sqlComWeb1 ='SELECT * FROM `comi_ret` WHERE `id_con` =  "'.$idConcierto.'" and detalle = "PUNTOS TICKET FACIL" ';
				//	echo $sqlComWeb1;
					$resComWeb1 = mysql_query($sqlComWeb1) or die (mysql_error());
					$rowComWeb1 = mysql_fetch_array($resComWeb1);
					
					$comi_tar1 = $rowComWeb1['comi_tar'];
					$ret_renta1 = $rowComWeb1['ret_renta'];
					$ret_iva1 = $rowComWeb1['ret_iva'];
					$comisionTarjeta1 = (($comi_tar1 * ($totalTar1 + $totalEfectivoPREV2))/100);
					$retencionTarjeta1 = number_format((((($totalTar1 + $totalEfectivoPREV2)/1.14)*($ret_renta1/100))+((($totalTar1 + $totalEfectivoPREV2) - (($totalTar1 + $totalEfectivoPREV2) / 1.14))*($ret_iva1/100))),2);
					
					
					
					echo $comisionTarjeta1;
					$sumacomisionTarjeta1 += $comisionTarjeta1;
					
				?>
			</td>
			<td>
				<?php
					//$retencionTarjeta = (($totalTar * $retencionTarjeta)/100);
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
								$terceraEdadEf1 = 0;
								echo $totaltotalnumeroEfectivoTer1;
							?>
						</td>
						<td width = '33.33%'>
							<?php 
								
								echo $sumaTotalEfectivo1;
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
								$terceraEdadTar1 = 0;
								echo $totalnumeroEfectivoTer11;
							?>
						</td>
						<td width = '33.33%'>
							<?php 
								echo $sumaTotalTar1;
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
								$terceraEdadTar1 = 0;
								echo $totaltotalnumeroEfectivoTerPREV1;
							?>
						</td>
						<td width = '33.33%'>
							<?php 
								echo $sumaTotalEfectivoPRE1;//."aa";
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
								$terceraEdadTar1 = 0;
								echo $totaltotalnumeroEfectivoTerPREV2;//'aqui';
							?>
						</td>
						<td width = '33.33%'>
							<?php 
								echo $sumaTotalEfectivoPREV2;
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
	<table width = '100%' border = '1' style = 'font-size:12px;' id = '' >
		<tr>
			<td colspan = '9' valign = 'middle' align = 'center' style = 'background-color:#171A1B;color:#fff;' >
				DETALLE DE COMISIÓN POR VENTAS Y COBROS DE CADENA COMERCIAL PARA EL CONCIERTO DE <?php echo $rowC['strEvento'];?>
			</td>
		</tr>
		<tr>
			<td valign = 'middle' align = 'center'>
				LOCALIDAD
			</td>
			<td colspan = '3' valign = 'middle' align = 'center'>
				VENTAS
			</td>
			<td colspan = '3' valign = 'middle' align = 'center'>
				COBROS
			</td>
			<td valign = 'middle' align = 'center'>
				COMISIÓN TOTAL
			</td>
			<td valign = 'middle' align = 'center'>
				TOTAL A PAGAR
			</td>
		</tr>
		<tr>
			<td rowspan = '1' valign = 'middle' align = 'center'>
				
			</td>
			<td valign = 'middle' align = 'center'>
				TICKET
			</td>
			<td valign = 'middle' align = 'center'>
				TOTAL
			</td>
			<td valign = 'middle' align = 'center'>
				COMISIÓN
			</td>
			<td valign = 'middle' align = 'center'>
				TICKET
			</td>
			<td valign = 'middle' align = 'center'>
				TOTAL
			</td>
			<td valign = 'middle' align = 'center'>
				COMISIÓN
			</td>
			<td rowspan = '1' valign = 'middle' align = 'center'>
				
			</td>
			<td rowspan = '1' valign = 'middle' align = 'center'>
				
			</td>
		</tr>
		<?php
			$sqlConCC = 'SELECT * FROM `Localidad` WHERE `idConc` = "'.$idConcierto.'"  ORDER BY `idLocalidad` DESC ';
			//echo $sqlComCiC;
			$resConCC = mysql_query($sqlConCC) or die (mysql_error());
			while($rowConCC = mysql_fetch_array($resConCC)){
				$sqlLoCC = 'select * from Localidad where idLocalidad = "'.$rowConCC['idLocalidad'].'" ';
				$resLoCC = mysql_query($sqlLoCC) or die (mysql_error());
				$rowLoCC = mysql_fetch_array($resLoCC);
				$doublePrecioPreventa = $rowLoCC['doublePrecioPreventa'];
				$doublePrecioL = $rowLoCC['doublePrecioL'];
				
		?>
			<tr>
				<td valign = 'middle' align = 'center'>
					<?php echo $rowLoCC['strDescripcionL'];?>
					<?php //echo $rowLoCC['idLocalidad'];?>
				</td>
				<td valign = 'middle' align = 'center'>
					<?php
						$sqlCcadCom = '	select count(idBoleto) as bol 
										from Boleto 
										where idLocB = "'.$rowLoCC['idLocalidad'].'" 
										and colB = "2"
										and tercera = 0
										and preventa = 0
										and tipo_evento = 2
										and id_usu_venta = "'.$_SESSION['iduser'].'"
									 ';
						//echo $sqlCcadCom."<br/>";
						$resCcadCom = mysql_query($sqlCcadCom) or die (mysql_error());
						$rowCcadCom = mysql_fetch_array($resCcadCom);
						//echo $rowCcadCom['bol']."<<>>".$doublePrecioL."<br/><br/>";
						
						$sqlCRPREVECadena = '
							select count(idBoleto) as bol 
										from Boleto 
										where idLocB = "'.$rowLoCC['idLocalidad'].'" 
										and colB = "2"
										and tercera = 0
										and preventa = 1
										and tipo_evento = 2
										and id_usu_venta = "'.$_SESSION['iduser'].'"
						';
						//echo $sqlCRPREVECadena."<br/>";
						$resCRPREVECadena = mysql_query($sqlCRPREVECadena) or die (mysql_error());
						$rowCRPREVECadena = mysql_fetch_array($resCRPREVECadena);
						//echo $rowCRPREVECadena['bol']."<<>>".$doublePrecioPreventa."<br/><br/>";
						
						$sqlCRPREVECadenaTercera = '
							select count(idBoleto) as bol 
										from Boleto 
										where idLocB = "'.$rowLoCC['idLocalidad'].'" 
										and colB = "2"
										and tercera = 1
										and preventa = 0
										and tipo_evento = 2
										and id_usu_venta = "'.$_SESSION['iduser'].'"
						';
						//echo $sqlCRPREVECadenaTercera."<br/>";
						$resCRPREVECadenaTercera = mysql_query($sqlCRPREVECadenaTercera) or die (mysql_error());
						$rowCRPREVECadenaTercera = mysql_fetch_array($resCRPREVECadenaTercera);
						//echo $rowCRPREVECadenaTercera['bol']."<<>>".$doublePrecioL."<br/><br/>";
						
						
						
						$sqlCRPREVECadenaTercera1 = '
							select count(idBoleto) as bol 
										from Boleto 
										where idLocB = "'.$rowLoCC['idLocalidad'].'" 
										and colB = "2"
										and tercera = 1
										and preventa = 1
										and tipo_evento = 2
										and id_usu_venta = "'.$_SESSION['iduser'].'"
						';
						//echo $sqlCRPREVECadenaTercera1."<br>";
						$resCRPREVECadenaTercera1 = mysql_query($sqlCRPREVECadenaTercera1) or die (mysql_error());
						$rowCRPREVECadenaTercera1 = mysql_fetch_array($resCRPREVECadenaTercera1);
						//echo $rowCRPREVECadenaTercera1['bol']."<<>>".$doublePrecioPreventa."<br/><br/>";
						
						
						
						//echo "normal : ".$rowCcadCom['bol']."<br/>normal preventa : ".$rowCRPREVECadena['bol']."<br/>tercera sin preventa: ".$rowCRPREVECadenaTercera['bol']."<br/>tercera preventa:".$rowCRPREVECadenaTercera1['bol']."<br>";
						$sumaVentasComRet = ($rowCcadCom['bol'] + $rowCRPREVECadena['bol'] + $rowCRPREVECadenaTercera['bol'] + $rowCRPREVECadenaTercera1['bol']);
						echo $sumaVentasComRet;
						$sumRowCcadComGeneral += $sumaVentasComRet;//suma total boletos
					?>
				</td>
				<td valign = 'middle' align = 'center'>
					<?php
						$totalEfecCC = ($rowLoCC['doublePrecioL']*$rowCcadCom['bol']);
						$totalEfecCRPREV1 = ($doublePrecioPreventa*$rowCRPREVECadena['bol']);
						$totalEfecCRPREV2 = (($rowCRPREVECadenaTercera['bol']*$rowLoCC['doublePrecioL'])*0.5);
						$totalEfecCRPREV12 = (($doublePrecioPreventa*$rowCRPREVECadenaTercera1['bol'])*0.5);
						
						
						$sumaTotVentasComRet = ($totalEfecCC + $totalEfecCRPREV1 + $totalEfecCRPREV2 + $totalEfecCRPREV12);
						
						
						
						echo $sumaTotVentasComRet;//.">><<";
						$sumaTotalEfecCC += $sumaTotVentasComRet;
					?>
				</td>
				<td valign = 'middle' align = 'center'>
					<?php
						$sqlComRetCC = 'select * from comi_ret where id_con = "'.$idConcierto.'" and detalle = "PUNTOS TICKET FACIL" ';

						$resComRetCC = mysql_query($sqlComRetCC) or die (mysql_error());
						$rowComRetCC = mysql_fetch_array($resComRetCC);
						$comi_venta =  $rowComRetCC['comi_venta'];
						$comVenEfeCC = (($comi_venta * $sumaTotVentasComRet)/100);
						echo $comVenEfeCC;//."hh";
						$sumComVenEfeCC += $comVenEfeCC;
					?>
				</td>
				<td valign = 'middle' align = 'center'>
					<?php
						$sqlCcadComT = 'select count(idBoleto) as bol 
										from Boleto 
										where idLocB = "'.$rowLoCC['idLocalidad'].'" 
										and colB = "2"
										and tercera = 0
										and preventa = 0
										and tipo_evento = 1
										and id_usu_venta = "'.$_SESSION['iduser'].'"
										';
						//echo $sqlCcadComT."<br>";
						$resCcadComT = mysql_query($sqlCcadComT) or die (mysql_error());
						$rowCcadComT = mysql_fetch_array($resCcadComT);
						
						
						$sqlCRPREVECadena1 = '
							select count(idBoleto) as bol 
										from Boleto 
										where idLocB = "'.$rowLoCC['idLocalidad'].'" 
										and colB = "2"
										and tercera = 0
										and preventa = 1
										and tipo_evento = 1
										and id_usu_venta = "'.$_SESSION['iduser'].'"
						';
						//echo $sqlCRPREVECadena1."<br>";
						$resCRPREVECadena1 = mysql_query($sqlCRPREVECadena1) or die (mysql_error());
						$rowCRPREVECadena1 = mysql_fetch_array($resCRPREVECadena1);
						
						$sqlCRPREVECadenaTercera1 = '
							select count(idBoleto) as bol 
										from Boleto 
										where idLocB = "'.$rowLoCC['idLocalidad'].'" 
										and colB = "2"
										and tercera = 1
										and preventa = 0
										and tipo_evento = 1
										and id_usu_venta = "'.$_SESSION['iduser'].'"
						';
						//echo $sqlCRPREVECadenaTercera1."<br>";
						$resCRPREVECadenaTercera1 = mysql_query($sqlCRPREVECadenaTercera1) or die (mysql_error());
						$rowCRPREVECadenaTercera1 = mysql_fetch_array($resCRPREVECadenaTercera1);
						
						
						
						$sqlCRPREVECadenaTercera11 = '
							select count(idBoleto) as bol 
										from Boleto 
										where idLocB = "'.$rowLoCC['idLocalidad'].'" 
										and colB = "2"
										and tercera = 1
										and preventa = 1
										and tipo_evento = 1
										and id_usu_venta = "'.$_SESSION['iduser'].'"
						';
						
						$resCRPREVECadenaTercera11 = mysql_query($sqlCRPREVECadenaTercera11) or die (mysql_error());
						$rowCRPREVECadenaTercera11= mysql_fetch_array($resCRPREVECadenaTercera11);
						//echo $sqlCRPREVECadenaTercera11."<br>";
						
						
						//echo "normal : ".$rowCcadCom['bol']."preventa : ".$rowCRPREVECadena['idBol1']."preventa tercera : ".$rowCRPREVECadenaTercera['idBol2'];
						$sumaVentasComRet1 = ($rowCcadComT['bol'] + $rowCRPREVECadena1['bol'] + $rowCRPREVECadenaTercera1['bol'] + $rowCRPREVECadenaTercera11['bol']);
						//echo $sumaVentasComRet1;
						$sumRowCcadCom += $sumaVentasComRet1;//suma total boletos
						
						
						echo $sumaVentasComRet1;
						$sumRowCcadComT += $sumaVentasComRet1;
					?>
				</td>
				<td valign = 'middle' align = 'center'>
					<?php
						$totalTarCC = ($rowLoCC['doublePrecioL']*$rowCcadComT['bol']);
						$totalTarCC1 = ($doublePrecioPreventa*$rowCRPREVECadena1['bol']);
						$totalTarCC2 = (($rowLoCC['doublePrecioL']*$rowCRPREVECadenaTercera1['bol'])*0.5);
						$totalTarCC12 = (($doublePrecioPreventa*$rowCRPREVECadenaTercera11['bol'])*0.5);
						
						
						$totalTarCCGlobal = ($totalTarCC + $totalTarCC1 + $totalTarCC2 + $totalTarCC12);
						echo $totalTarCCGlobal;//."<<>>";
						$sumaTotalEfecCC111 += $totalTarCCGlobal;
					?>
				</td>
				<td valign = 'middle' align = 'center'>
					<?php
						$sqlComRetCC1 = 'select * from comi_ret where id_con = "'.$idConcierto.'" and detalle = "PUNTOS TICKET FACIL" ';
						$resComRetCC1 = mysql_query($sqlComRetCC1) or die (mysql_error());
						$rowComRetCC1 = mysql_fetch_array($resComRetCC1);
						$comi_cobro =  $rowComRetCC1['comi_cobro'];
						$comVenEfeCC1 = (($comi_cobro * $totalTarCCGlobal)/100);
						echo $comVenEfeCC1;//."aa";
						$sumComVenEfeCC1 += $comVenEfeCC1;
					?>
				</td>
				<td rowspan = '1' valign = 'middle' align = 'center'>
					<?php
						$sumGComision = ($comVenEfeCC + $comVenEfeCC1);
						echo $sumGComision;
						$sumGlobalComision += $sumGComision;
					?>
				</td>
				<td>
					<?php
						
						$totAPagarFinalComi = (($sumaTotVentasComRet + $totalTarCCGlobal)-$sumGComision);
						//echo $totAPagar;
						//echo "ventas : ".$sumaTotVentasComRet." cobros : ".$totalTarCCGlobal." comision : ".$sumGComision." total a pagar : ".$totAPagarFinalComi;
						echo $totAPagarFinalComi;
						$sumTotAPagar += $totAPagarFinalComi;
					?>
				</td>
			</tr>
		<?php
			}
		?>
		<tr>
			<td rowspan = '1' valign = 'middle' align = 'center'>
				TOTAL
			</td>
			<td valign = 'middle' align = 'center'>
				<?php echo $sumRowCcadComGeneral?>
			</td>
			<td valign = 'middle' align = 'center'>
				<?php echo $sumaTotalEfecCC;?>
			</td>
			<td valign = 'middle' align = 'center'>
				<?php echo $sumComVenEfeCC;?>
			</td>
			<td valign = 'middle' align = 'center'>
				<?php echo $sumRowCcadComT;?>
			</td>
			<td valign = 'middle' align = 'center'>
				<?php echo $sumaTotalEfecCC111;?>
			</td>
			<td valign = 'middle' align = 'center'>
				<?php echo $sumComVenEfeCC1;?>
			</td>
			<td rowspan = '1' valign = 'middle' align = 'center'>
				<?php echo $sumGlobalComision;?>
			</td>
			<td>
				<?php
					echo $sumTotAPagar;
				?>
			</td>
		</tr>
		<tr>
			<td colspan = '3'>
				Total Tickets : <?php echo ($sumRowCcadComGeneral + $sumRowCcadComT);?>
			</td>
			<td colspan = '4' valign = 'middle' align = 'center'>
				Total Cobrado : <?php echo ($sumaTotalEfecCC + $sumaTotalEfecCC111);?>
			</td>
			<td colspan = '2'></td>
		</tr>
	</table>