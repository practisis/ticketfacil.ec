<?php 
	require_once 'PHPM/PHPM/class.phpmailer.php';
	require_once 'PHPM/PHPM/class.smtp.php';
	session_start();
	// echo $_SESSION['usermail']." <<>> ".$_SESSION['userdoc']." >><< ".$_SESSION['id'];
	
	$gbd = new DBConn();
	
	$con = $_GET['con'];
	
	
	$query = "SELECT * FROM Concierto WHERE idConcierto = ?";
	$result = $gbd -> prepare($query);
	$result -> execute(array($con));
	
	
	include 'conexion.php';
	$aplica_descuento_socio = 0;
	$sqlSd = 'SELECT count(id) as cuantos FROM `socio_descuento` where cedula_cli = "'.$_SESSION['userdoc'].'" ';
	$resSd = mysql_query($sqlSd) or die (mysql_error());
	$rowSd = mysql_fetch_array($resSd);
	if($rowSd['cuantos'] == 0){
		$aplica_descuento_socio = 0;
	}else{
		$aplica_descuento_socio = 1;
	}
	
	echo '<input type="hidden" value="'.$con.'" id="idConcierto"/>';
	
	echo '<input type="hidden" id="data" value="3" />';
?>
<div style="background-color:#282B2D; margin:10px 20px 0px 20px; text-align:center;">
	<div class="breadcrumb">
		<a id="chooseseat" onclick="chooseasientos()">Escoge tu asiento</a>
		<a id="identification" href="#" onclick="identity()">Identificate</a>
		<a class="active" id="buy" href="#" onclick="security()">Resumen de Compra</a>
		<a id="pay" href="#" onclick="security()">Pagar</a>
		<a id="confirmation" href="#" onclick="security()">Confirmaci&oacute;n</a>
	</div>
</div>
<div style="margin:-10px;">
	<div style="background-color:#171A1B; padding:20px;">
		<div style="border: 2px solid #00AEEF; margin:20px;">
			<div class="row">
				
				<h4 style="color:#fff; text-align:center; font-size:22px;">!HOLA <?php echo ($_SESSION['username']);?></h4>
				
			</div>
			<form id="datos" method="post" action="">
				<div style="font-size:30px; color:#00ADEF; margin:15px;">
					<strong>Confirma tu compra</strong>
				</div>
				<div style="margin:20px;">
					<table style="width:100%; color:#fff;">
						
					</table>
				</div>
				<div style="background-color:#00ADEF; margin-left:40px; margin-top:20px; margin-right:-42px; position:relative;">
					<table class="table_resumen_compra">
						<tr>
							<td style="text-align:left; border-right:2px solid #004C6C; width:50%;">
								<strong>Cliente:</strong>
								<?php echo ($_SESSION['username']);?>
							</td>
							<td>
								<strong>Documento de Identidad:</strong>
								<?php echo $_SESSION['userdoc'];?>
							</td>
						</tr>
						<tr>
							<td style="border-right:2px solid #004C6C;">
								<strong>E-mail:</strong>
								<?php echo $_SESSION['usermail'];?>
							</td>
							<td>
								<strong>Celular:</strong>
								<?php echo $_SESSION['usertel'];?>
							</td>
						</tr>
						<?php
							include 'conexion.php';
							$sqlC = 'select envio from Concierto where idConcierto = "'.$_REQUEST['con'].'" ';
							$resC = mysql_query($sqlC) or die (mysql_error());
							$rowC = mysql_fetch_array($resC);
							if($rowC['envio'] == ''){
								$envio = 0;
							}else{
								$envio = $rowC['envio'];
							}
						
							
							if(isset($_POST['codigo'])){
								// echo $aplica_descuento_socio." hola <br>";
								if($aplica_descuento_socio == 0){
									$contador=0;
									
									
									foreach($_POST['codigo'] as $key=>$cod_loc){
										$id_localidad = 0;
										$suma += $_POST['tot'][$key];
										$totalpagar2 = number_format($suma, 2,'.','');
										$contador++;
									}
									$limite = $contador;
								}else{
									$contador=0;
									$limite = 2;
									foreach($_POST['codigo'] as $key=>$cod_loc){
										$contador++;
										if($contador == $limite){
											break;
										}
										$id_localidad = $cod_loc;
									}
									
									
									$sqlD = 'SELECT * FROM `descuentos` WHERE `idloc` = "'.$id_localidad.'" AND `nom` LIKE "%socio%" ORDER BY `idloc` ASC ';
									// echo $sqlD."<br>";
									$resD = mysql_query($sqlD) or die (mysql_error());
									$rowD = mysql_fetch_array($resD);
									
									$totalpagar2 = number_format(($rowD['val'] * $contador), 2,'.','');
								}
								
								// echo $id_localidad." <<>> ".$contador." >><< ".$totalpagar2." xx ".$limite."<br>";
							}
							?>
						
						
						
					</table>
					<div class="tra_azul"></div>
					<div class="par_azul"></div>
				</div>
				<script>
					function aceptaCostoEnvio(valor_envio , totalpagar2){
						var envio = valor_envio.toFixed(2);
						var total_pagar = totalpagar2.toFixed(2)
						var pagoFinal = total_pagar;
						
						var porce_transfer = $('#porce_transfer').val();
						var comis_transfere = $('#comis_transfere').val();
						var pagarComision = 0;
						if( $('#envio_check').is(':checked') ) {
							var PrecioEnvio = (parseFloat(envio) + parseFloat(total_pagar));
							
							pagarComision = (((parseFloat(PrecioEnvio) * parseFloat(porce_transfer))/100) + parseFloat(comis_transfere)).toFixed(2);
							$('#texto_comision').html("(" +((parseFloat(PrecioEnvio) * parseFloat(porce_transfer))/100)+ " %) + " + "( "+parseFloat(comis_transfere)+" ctvs)" )
							// alert('Seleccionado');
							$('#precio_envio').html(envio);
							$('#ident1').val(1);
							$('#modalCostoEnvio').modal('show');
							pagoFinal = (parseFloat(pagoFinal) + parseFloat(envio)).toFixed(2);
							$('#pagoFinal').html(pagoFinal);
							$('#total_pagar').val(pagoFinal);
							$('#valor_compra_transferencia').html(pagoFinal);
							
							$('#comis_transfer').html(pagarComision);
							var comis_transfer = $('#comis_transfer').text();
							
							$('#total_transferencia').html(parseFloat(pagoFinal) + parseFloat(comis_transfer));
							
						}else{
							
							var PrecioEnvio = total_pagar;
							
							pagarComision = (((parseFloat(PrecioEnvio) * parseFloat(porce_transfer))/100) + parseFloat(comis_transfere)).toFixed(2);
							$('#texto_comision').html("(" +((parseFloat(PrecioEnvio) * parseFloat(porce_transfer))/100)+ " %) + " + "( "+parseFloat(comis_transfere)+" ctvs)" )
							var pagoFinal = total_pagar;
							$('#modalCostoEnvio').modal('hide');
							$('#precio_envio').html('0.00');
							$('#pagoFinal').html(pagoFinal);
							
							$('#comis_transfer').html(pagarComision);
							var comis_transfer = $('#comis_transfer').text();
							
							
							$('#total_transferencia').html(parseFloat(pagoFinal) + parseFloat(comis_transfer));
							$('#total_pagar').val(pagoFinal);
							$('#valor_compra_transferencia').html(pagoFinal);
							$('#ident1').val(0);
						}
					}
				</script>
				<div style="margin:50px 20px 20px 20px;">
					<table style="border: 2px solid #00ADEF; width:100%; color:#fff; font-size:20px; border-collapse:separate; border-spacing:0 20px;">
						<tr style="border-bottom:2px solid #00ADEF;">
								<td style="text-align:center">
									<strong>Asientos #</strong>
								</td>
								<td style="text-align:center">
									<strong>Descripci&oacute;n</strong>
								</td>
								<td style="text-align:center">
									<strong># de Boletos</strong>
								</td>
								<td style="text-align:center">
									<strong>Precio Unitario</strong>
								</td>
								<td style="text-align:center">
									<strong>Precio Total</strong>
								</td>
							</tr>
							<?php
						//	echo $_SESSION['valida_ocupadas']."ocupadas";
							$suma = 0;
							$cant_bol = 0;
							// var_dump($_POST['codigo']);
							if(isset($_POST['codigo'])){
								$contador1 = 0;
								// echo $contador." mmm ".$limite;
								// foreach($_POST['codigo'] as $key=>$cod_loc){
								for($key=0;$key<=$limite;$key++){
									$contador1++;
									// echo $key." nn ".$limite."<br>";
									echo'<tr>
										<td style="text-align:center">
											<input type="hidden" id="codigo" name="codigo[]" value="'.$_POST['codigo'][$key].'" class="added resumen" readonly="readonly" size="2" />
											<input type="hidden" name="row[]" class="added" value="'.$_POST['row'][$key].'" />
											<input type="hidden" name="col[]" class="added" value="'.$_POST['col'][$key].'" />
										<input type="text" id="chair" name="chair[]" value="'.$_POST['chair'][$key].'" class="added resumen" readonly="readonly" size="15" /></td>
										<td style="text-align:center"><input type="text" id="des" name="des[]" value="'.$_POST['des'][$key].'" class="added resumen" readonly="readonly" size="15" /></td>
										<td style="text-align:center"><input type="text" id="num" name="num[]" value="'.$_POST['num'][$key].'" class="added resumen" readonly="readonly" size="2" /></td>
										<td style="text-align:center">';
										if($aplica_descuento_socio == 0){
											$precio_ = $_POST['pre'][$key];
											$tot_ = $_POST['tot'][$key];
											echo'<input type="text" id="pre" name="pre[]" value="'.$_POST['pre'][$key].'" class="added resumen" readonly="readonly" size="5" />';
											echo'<input type="hidden" id="val_desc" name="val_desc[]" value="'.$_POST['val_desc'][$key].'" class="added resumen" readonly="readonly" size="5" />';
										}else{
											$precio_ = $rowD['val'];
											$tot_ = $rowD['val'];
											echo'<input type="text" id="pre" name="pre[]" value="'.$rowD['val'].'" class="added resumen" readonly="readonly" size="5" />';
											echo'<input type="hidden" id="val_desc" name="val_desc[]" value="'.$rowD['val'].'" class="added resumen" readonly="readonly" size="5" />';
										}
									echo'<input type="hidden" id="id_desc" name="id_desc[]" value="'.$_POST['id_desc'][$key].'" class="added resumen" readonly="readonly" size="5" />
											
										</td>';
										if($aplica_descuento_socio == 0){
											echo '<td style="text-align:center" id="filas"><input type="text" id="tot" name="tot[]" value="'.$_POST['tot'][$key].'" class="added resumen" readonly="readonly" size="5" /></td> ';
										}else{
											echo '<td style="text-align:center" id="filas"><input type="text" id="tot" name="tot[]" value="'.$rowD['val'].'" class="added resumen" readonly="readonly" size="5" /></td> ';
										}
									echo'</tr>';
									$suma += $_POST['tot'][$key];
									$totalpagar = number_format($suma, 2,'.','');
									$cant_bol +=  $_POST['num'][$key];
									$compras[]=array("codigo"=>$cod_loc,"row"=>$_POST['row'][$key],"col"=>$_POST['col'][$key] ,"chair"=>$_POST['chair'][$key],"des"=>$_POST['des'][$key],"num"=>$_POST['num'][$key],"pre"=>$precio_,"tot"=>$tot_,"val_desc"=>$precio_,"id_desc"=>$_POST['id_desc'][$key]);
									if($contador1 == $limite){
										break;
									}
								}
								$_SESSION['boletos_asignados'] = ($compras);
								// print_r($_SESSION['boletos_asignados']);
							}
							?>
					</table>
				</div>
				<div style="margin:10px 20px;">
					<table style="width:100%; color:#fff; font-size:20px;">
						<tr> 
							<td rowspan="4" style="width:50%; vertical-align:middle;">
								<div class="tiempo_compra">
									<div class="reloj">
										<img src="imagenes/manecillas.png" style="width:100%; max-width:50px; margin-left:20px; margin-top:20px;">
									</div>
									<div style="text-align:center; padding-bottom:15px; padding-left:30px; font-size:17px; margin-top:-90px;">
										Tiempo para realizar la compra<br>
										<div id="CuentaAtras" style="text-align:center; font-size:18px;"></div>
										<div style="text-align:center;">hour min seg</div>
									</div>
								</div>
							</td>
						</tr>
						<tr>
							<td style="text-align:right; width:20%;">
								<p><strong>SUBTOTAL</strong></p>
							</td>
							<td style="text-align:right; padding-right:40px;" id="total">
								<?php echo $totalpagar2;?>
							</td>
						</tr>
						<tr>
							<td style="text-align:right;">
								<strong style = 'font-size:13px;' >Desea aceptar el costo de envio USD$<?php echo number_format(($envio),2);?> ? </strong>
								<input type = 'checkbox' id = 'envio_check' onclick = 'aceptaCostoEnvio(<?php echo $envio;?> , <?php echo $totalpagar2;?>)' />
							</td>
							<td style="text-align:right; padding-right:40px;">
								<span id = 'precio_envio' >0.00</span>
								<?php 
									$totalpagar = $totalpagar + $costoenvio;
									$totalpagar = number_format($totalpagar,2);
								?>
							</td>
						</tr>
						<tr>
							<td style="text-align:right; font-size:20px;">
								<p><strong>TOTAL</strong></p>
							</td>
							<td style="text-align:right; font-size:25px; padding-right:40px;">
								<strong><span id = 'pagoFinal' ><?php echo $totalpagar2;?></span></strong>
								<input type="hidden" id="total_pagar" name="total_pagar" value="<?php echo $totalpagar2;?>" class="added resumen" readonly="readonly" size="7"/>
								<br/>
								<input type="hidden" id="dir1" value = '<?php echo $_SESSION['userdir'];?>' name="dir1" style = 'color:#000;' />
								<input type="hidden" id="tel1" value = '<?php echo $_SESSION['usertelf'];?>' name="tel1" style = 'color:#000;'/>
								<input type="hidden" id="cel1" value = '<?php echo $_SESSION['usertel'];?>' name="cel1" style = 'color:#000;'/>
								
								<input type="hidden" id="ident1" name="ident1" style = 'color:#000;'/>
								
								<input type="hidden" id="num_boletos" name="num_boletos" value="<?php echo $cant_bol;?>" class="added" readonly="readonly" />
							</td>
						</tr>
					</table>
				</div>
				<div style="background-color:#00ADEF; margin-right:40px; margin-top:20px; margin-left:-42px; position:relative;">
					<div class="row">
						<?php $rowc = $result -> fetch(PDO::FETCH_ASSOC);
							$img = $rowc['strImagen'];
							$ruta = 'spadmin/';
							$r = $ruta.$img;
						?>
						<div class="col-lg-5" style="color:#fff;">
							<p>
								<div style="margin:0px 60px 20px 80px; border:1px solid #fff; font-size:30px; padding: 20px">
									<strong><?php echo $rowc['strEvento'];?></strong>
								</div>
							</p>
							<p>
								<div style="margin: 20px 80px; font-size:20px">
									<strong>Fecha: </strong><?php echo $rowc['dateFecha'];?><br>
									<strong>Hora: </strong><?php echo $rowc['timeHora'];?><br>
									<strong>Lugar: </strong><?php echo $rowc['strLugar'];?>
								</div>
							</p>
						</div>
						<div class="col-lg-6">
							<img src="<?php echo $r;?>" id="image" style="width:100%; overflow:hidden;"/>
						</div>
					</div>
					<div class="tra_video_concierto"></div>
					<div class="par_video_concierto"></div>
				</div>
				<div style="margin:20px; border: 2px solid #00ADEF;">
					<table style="width:100%; color:#fff; margin-top:20px; border-collapse:separate; border-spacing:25px; font-size:25px;">
						<tr>
							<td style="text-align:center" colspan="2">
								<center>
									<strong style="font-size:20px;">Forma de pago:&nbsp;<strong>
									<select name="cmbpago" id="cmbpago" class="inputlogin">
										<option value="0">Seleccione</option>
										<option value="3">Punto de Venta</option>
										<option value="1">Tarjeta de Crédito</option>
										<option value="4">Paypal</option>
										<option value="2">Depósito Bancario</option>
									</select>
								</center>
							</td>
						</tr>
						<tr>
							<td colspan="2" style="font-size:15px;">
								<center><p><I>Al hacer click en el bot&oacute;n comprar acepta los T&eacute;rminos, Condiciones y Declaraci&oacute;n de
								Privacidad emitidos por TICKETFACIL.</I></p></center>
							</td>
						</tr>
						<tr>
							<td style="text-align:center; font-size:18px;" colspan="2">
								<label for="check"><input type="checkbox" name="check" id="check"/>
								<I><strong>Acepto los <a href="javascript:op();" style="text-decoration:none;">T&eacute;rminos y Condiciones</a></strong></I></label>
							</td>
						</tr>
						<tr>
							<td colspan="2" align="center">
								<img src="imagenes/tarjetas.png" style="max-width:270px;"/>&nbsp;
								<img src="imagenes/dinners.png" style="max-width:70px; margin-left:-15px; margin-top:-7px" />&nbsp;
								<img src="imagenes/Pacificard.gif" style="max-width:180px; margin-left:-13px; margin-top:-1px;" />
							</td> 
						</tr>
					</table>
				</div>
				<!--<div class="divbtncompra">-->
					<center>
						<!--<a class="btndegradate"  ><img src="imagenes/mano_comprar.png" alt="" style="margin:0 10px 0 -20px;"/></a>-->
						<button type="button" id="aceptar" name="aceptar" style="cursor:pointer;" class="btn btn-primary"> <i class="fa fa-money" aria-hidden="true"></i>  <strong style = 'margin-top-5px'>  PAGAR  </strong></button>
					</center><br><br><br>
				<!--</div>-->
			</form>
		</div>
		<div data-keyboard="false" data-backdrop="static" class="modal fade" id="aviso" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						
						<h4 class="modal-title" id="myModalLabel">Alerta...!</h4>
					</div>
					<div class="modal-body">
						<h4 id="alerta1" class="alertas" style="display:none;">
							<div class="alert alert-info" role="alert">
								<span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
									&nbsp;&nbsp;<strong>Aviso!</strong>&nbsp;Los asientos seleccionados se han perdido, seleccionalos nuevamente.
							</div>
						</h4>
						<h4 id="alerta2" class="alertas" style="display:none;">
							<div class="alert alert-info" role="alert"><span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
								&nbsp;&nbsp;<strong>Aviso!</strong>&nbsp;El tiempo de la transacción ha terminado.
							</div>
						</h4>
						<h4 id="alerta3" class="alertas" style="display:none;">
							<div class="alert alert-info" role="alert"><span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
								&nbsp;&nbsp;<strong>Aviso!</strong>&nbsp;Debes volver a seleccionar tus asientos.
							</div>
						</h4>
						<h4 id="alerta4" class="alertas" style="display:none;">
							<div class="alert alert-info" role="alert"><span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
								&nbsp;&nbsp;<strong>Aviso!</strong>&nbsp;Para modificar tus datos da click en "MODIFICAR".
							</div>
						</h4>
						<h4 id="alerta5" class="alertas" style="display:none;">
							<div class="alert alert-info" role="alert"><span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
								&nbsp;&nbsp;<strong>Aviso!</strong>&nbsp;Acepta los terminos de uso y da click en "PAGAR".
							</div>
						</h4>
						<h4 id="alerta6" class="alertas" style="display:none;">
							<div class="alert alert-info" role="alert"><span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
								&nbsp;&nbsp;<strong>Aviso!</strong>&nbsp;Se abrira su perfil para modificar sus datos.
							</div>
						</h4>
						<h4 id="alerta7" class="alertas" style="display:none;">
							<div class="alert alert-info" role="alert" style = 'padding:10px !important;margin:0px !important;'>
								<span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
								Te recordamos que tienes 
								<?php echo $rowc['tiempopagoC'];?> para registrar tu Deposito o Transferencia Bancaria , por el valor de: <br/><br/>
								
								<blockquote>
									 
									<table class = 'table table-bordered' style = 'color:#31708f;font-weight:bold;'>
										<tr>
											<td>
												Valor de Compra :  
												<input type = 'hidden' id = 'porce_transfer' value = '<?php echo $rowc['porce_transfer'];?>' />
												<input type = 'hidden' id = 'comis_transfere' value = '<?php echo $rowc['comis_transfer'];?>' />
											</td>
											<td style ='text-align:right;' id = 'valor_compra_transferencia'>
												<?php echo $totalpagar;?>
												
											
												
											</td>
										</tr>
										
										<tr>
											<td>
												Comisión de Transacción : 
												
												<div id = 'texto_comision'></div>
											</td>
											<td style ='text-align:right;' id = 'comis_transfer'>
												<?php 
													$comis_transfer = ((($totalpagar * $rowc['porce_transfer'])/100) + $rowc['comis_transfer']);
													echo $comis_transfer;
												?>
											</td>
										</tr>
										<tr>
											<tr>
											<td>
												Total a Pagar : 
											</td>
											<td style ='text-align:right;border-top:2px solid #31708f;' id = 'total_transferencia'>
												<?php echo $totalpagar+$comis_transfer;?>
											</td>
										</tr>
										</tr>
									</table>
								</blockquote>
								Para realizar tu deposito o transferencia bancaria debes hacerlo en la siguiente cuenta : 
								<?php
									include 'conexion.php';
									$sql = 'select * from cuenta ';
									$res = mysql_query($sql) or die (mysql_error());
									if(mysql_num_rows($res)>0){
										$row = mysql_fetch_array($res);
										echo '<blockquote>';
											echo "Banco : ".$row['bco']."<br/>";
											echo "Cuenta : ".$row['tipo']."<br/>";
											echo "Num Cta : ".$row['num']."<br/>";
											echo "Titular : ".$row['nom']."<br/>";
											echo "Cedula : ".$row['ced']."<br/>";
											
											
										echo '</blockquote>';
										echo '<br> Esta información será enviada a tu correo electrónico';
									}
								?>
								
							</div>
						</h4>
						<h4 id="alerta8" class="alertas" style="display:none;">
							<div class="alert alert-info" role="alert"><span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
								
								<blockquote>
									<?php
										
										echo $rowc['dirPago']."<br/>";
										
									?>
									<br><br>
									Te recordamos que tienes <?php echo $rowc['tiempopagoC'];?> para realizar tu Pago.
								</blockquote>
							</div>
							<div class="row" style="text-align:center;">
								<!--<img src="imagenes/logo_Fybeca.gif" />-->
							</div>
						</h4>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-primary" data-dismiss="modal" onclick="aceptarModal()">Aceptar</button>
					</div>
				</div>
			</div>
		</div>
		<div class="modal fade" id="modalCostoEnvio" style = 'display:none;' tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-keyboard="false" data-backdrop="static">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="myModalLabel">INGRESE SU DIRECCION Y TELEFONO FIJO PARA ENVIAR SU(S) TICKET(S)</h4>
					</div>
					<div class="modal-body" style="text-align:left;">
						<textarea class = 'form-control' id = 'direccion_envio'  placeholder = 'Ingrese aqui su dirección' style = 'resize:none;' ><?php echo $_SESSION['userdir'];?></textarea>
						<br>
						<input type= 'number' value = '<?php echo $_SESSION['usertelf'];?>' class = 'form-control' id = 'fijo_envio'  placeholder = 'Ingrese aqui su telefono fijo' /><br>
						<input type= 'number' value = '<?php echo $_SESSION['usertel'];?>'  class = 'form-control' id = 'mobil_envio'  placeholder = 'Ingrese aqui su telefono celular' /><br>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-primary" data-dismiss="modal">Aceptar</button>
					</div>
				</div>
			</div>
		</div>
		<div class="modal fade" id="terminosdeuso" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-keyboard="false" data-backdrop="static">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="myModalLabel">Terminos y Condiciones TicketFacil</h4>
					</div>
					<div class="modal-body" style="text-align:left;">
						<p style = 'text-align:left;color:#444;' >
							COMPRA WEB TICKETFACIL<br/>
							Al adquirir el Ticket,  el cliente acepta Los términos y condiciones en él descritos.<br>
							TÉRMINOS Y CONDICIONES DE COMPRA DE TICKETS, BOLETOS O ENTRADAS MEDIANTE EL SISTEMA WEB DE TICKETFACIL Y SUS PUNTOS DE VENTA.<br>
							El CLIENTE al realizar la compra acepta irrevocablemente los siguientes Términos y condiciones de compra de tiquetes por medio del sistema TICKETFACIL y sus puntos de Venta que se detallan a continuación .<br/>
						</p>
						<style>
						#lista3 {
							counter-reset: li; 
							list-style: none; 
							*list-style: decimal; 
							font: 15px 'trebuchet MS', 'lucida sans';
							padding: 0;
							margin-bottom: 4em;
							text-shadow: 0 1px 0 rgba(255,255,255,.5);
						}

						#lista3 ol {
							margin: 0 0 0 2em; 
						}

						#lista3 li{
							position: relative;
							display: block;
							padding: .4em .4em .4em .8em;
							*padding: .4em;
							margin: .5em 0 .5em 2.5em;
							background: #ddd;
							color: #444;
							text-decoration: none;
							transition: all .3s ease-out;   
						}

						#lista3 li:hover{
							background: #eee;
						}   

						#lista3 li:before{
							content: counter(li);
							counter-increment: li;
							position: absolute; 
							left: -2.5em;
							top: 50%;
							margin-top: -1em;
							background: #fa8072;
							height: 2em;
							width: 2em;
							line-height: 2em;
							text-align: center;
							font-weight: bold;
						}

						#lista3 li:after{
							position: absolute; 
							content: '';
							border: .5em solid transparent;
							left: -1em;
							top: 50%;
							margin-top: -.5em;
							transition: all .3s ease-out;               
						}

						#lista3 li:hover:after{
							left: -.5em;
							border-left-color: #fa8072;             
						}
						</style>
							<ol id="lista3">
							<li>Primero: TICKETFACIL, es una marca registrada cuyo objeto social, para efectos de las presentes condiciones, es la impresión y comercialización de entradas para eventos o espectáculos.</li>
							<li>Segunda:No existen cambios, reembolsos ni cancelaciones de compra de tickets.</li>
							<li>Tercera: EL CLIENTE es el usuario, que adquiere los Tickets y está previamente registrado en la página web www.ticketfacil.ec o en el sistema de facturación de los puntos de venta de Ticketfacil quien acepta haber ingresado toda la información real personal requerida, y es él único y exclusivamente responsable por la información registrada. TICKETFACIL no se hace responsable por tiquetes falsos o adulterados o adquiridos en lugares no autorizados. Quien suministre información o use su(s)entrada(s) para falsificaciones o adulteraciones será responsable ante las entidades legales pertinentes, esto puede dar lugar a RESPONSABILIDAD PENAL</li>
							<li>Cuarta: EL CLIENTE acepta que toda la información de las entradas seleccionadas han sido verificadas. El CLIENTE reconoce que la realización de cualquier evento o espectáculo, del que adquiera las entradas mediante los puntos de venta de TICKET FACIAL  o por el sistema de www.ticketfacil.ec no depende de TICKETFACIL,  quien no se responsabiliza por por errores de fechas, horas, valores registrados, nombre de eventos, calidad de los espectáculos, condiciones de seguridad, organizacion, contenido o en general causas ajenas a responsabilidades propias. Este ticket es válido sólo por el día,hora y lugar del evento en el especificado, el ingreso después de la hora señalada está sujeta a las reglas del lugar donde se realiza el evento.Una vez adquirido el tiquete no se aceptan cambios, devoluciones o reintegros, salvo en aquellos casos en que el empresario, promotor del evento o la ley lo establezca y en las condiciones que los mismos lo dispongan. TICKETFACIL  no está obligado hacer devoluciones de dinero, el empresario es la única persona legalmente responsable del evento, información impresa en la parte frontal del presente ticket.</li> 
							<li>Quinta:  TICKET FACIL no se hace responsable por la pérdida o robo del presente ticket, no existe obligación alguna de emitir un nuevo ticket o permitirle el ingreso aunque logre comprobar la adquisición del ticket,  estos son de entera responsabilidad del CLIENTE. El cliente acepta y conoce que los tiquetes que compre tienen un costo adicional por el servicio ofrecido por el sistema TICKETFACIL y que en ningún caso será reembolsable. Para el ingreso al evento el ticket debe ser presentado completo y sin alteraciones. Únicamente serán válidas para entrar al evento los tickets emitidos por TICKETFACIL.</li> 
							<li>Sexta: EL CLIENTE asepta que los organizadores se reservan el derecho de admisión y  el derecho de agregar, modificar o sustituir artistas, así como de variar los programas, precios, ubicaciones, fechas, capacidad del escenario y ubicación de los mismos. En caso de que el evento sea postergado este ticket será válido para el ingreso en la fecha indicada por el empresario.El cliente no tendrá derecho a la devolución de su dinero.</li> 
							<li>Septima: Al ingreso y durante el evento los asistentes estarán sujetos a las medidas de seguridad y políticas establecidas por el escenario, quien no cumpla los controles respectivos, o  desacate los mismos, se le prohibirá  el ingreso o se le solicitará su retiro, sin lugar a devolución del dinero pagado por el ticket. En las localidades numeradas se deberá respetar el número de silla, de lo contrario el personal de logística podrá reubicarlo en el lugar adquirido o solicitarle el retiro del escenario.</li> 
							<li>Octava: No se permite el ingreso de cámaras de vídeo, fotográficas, bebidas alcohólicas u objetos que pongan en peligro la seguridad del público, pudiendo ser retirados del lugar y destruido su contenido.</li> 
							<li>Novena:El poseedor de este ticket asume todos los riesgos que puedan suscitarse durante el evento y exime a los organizadores por cualquier daño que pueda sufrir, de ser el caso EL CLIENTE se obliga a comunicar su situación de discapacidad, embarazo o cualquier situación que pueda poner en riesgo su integridad o la de los asistentes, bajo su responsabilidad el promotor permitirá el ingreso o un trato especial.</li> 
							<li>Decima: Las compras realizadas por el portal www.ticketfacil.ec están sujetas a la verificación y aceptación de la tarjeta débito o crédito por parte del BANCO o entidad financiera en el transcurso de 24 horas después de haber efectuado la compra.</li> 
							<li>Decima primera: El cliente acepta que la información registrada en el sistema de ticketfacil.ec o sus puntos de ventas, así como de las transacciones efectuadas son de propiedad de Ticketfacil; quien está autorizado para dar usos comerciales sin afectar en ningún caso la intimidad y seguridad de los usuarios.</li> 
							<li>Decima segunda: En caso de cancelación definitiva del evento por cualquier causa, TICKET FACIL es en su condición de simple intermediario para la distribución y venta del ticket, no se hacen responsable por la devolución del dinero pagado por este ticket, siendo única y exclusivamente responsable el empresario, promotor o productor  del evento. El presente ticket es emitido por la imprenta autorizada por mandato del empresario quien es la única persona legalmente responsable del evento, información impresa en la parte frontal del presente ticket. TICKETFACIL gestionará la devolución solo en caso de ser contratado por EL EMPRESARIO para esta gestión. En caso que TICKETFACIL  gestione la devolución del dinero, EL CLIENTE deberá seguir las indicaciones publicadas en el sitio web www.ticketfacil.ec.</li> 
							<li>Decima tercera: EL CLIENTE acepta que los tickets comprados o reservados por la web www.ticketfacil.ec deberán ser recogidos por el usuario registrado en el sistema de TICKETFACIL. o por la persona que el usuario haya indicado en el proceso de la compra, mostrando su documento de identidad original.</li> 
							<li>Decima cuarta: Las compras efectuadas en la página web www.tickefacil.ec se entienden efectuadas en los términos de la legislación ecuatoriana, en particular la ley de Comercio Electrónico y su Reglamento, por lo que la contratación y la firma electrónica se reputa válida y obliga al usuario, en el términos legales y contractuales del negocio jurídico realizado.</li> 
							<li>Decima quinta: Cualquier diferencia surgida entre las partes será dirimida en derecho aplicando la legislación ecuatoriana, ante la justicia ordinaria.</li> 
							</ol>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
var evento = $('#idConcierto').val();
$( document ).ready(function(){
	
	$( "#direccion_envio" ).keyup(function() {
		var direccion_envio = $('#direccion_envio').val();
		$('#dir1').val(direccion_envio)
	});
	
	
	$( "#fijo_envio" ).keyup(function() {
		var fijo_envio = $('#fijo_envio').val();
		$('#tel1').val(fijo_envio)
	});
	
	$( "#mobil_envio" ).keyup(function() {
		var mobil_envio = $('#mobil_envio').val();
		$('#cel1').val(mobil_envio);
	});
	
	
	$('#aceptar').on('click',function(){
		if($('#check').is(':checked')){
			var pago = $('#cmbpago').val();
			if(pago == 0){
				alert('Debes seleccionar una forma de pago')
			}else{
				if(pago == 1){
					var ident1 = $('#ident1').val();
					var dir1 = $('#dir1').val();
					var tel1 = $('#tel1').val();
					var cel1 = $('#cel1').val();
					if(ident1 == 1 && (dir1 == '' || tel1 == '' || cel1 == '')){
						alert('debe ingresar su direccion  , convencional y celular de envio');
						$('#modalCostoEnvio').modal('show');
					}else{
						accion = '?modulo=pagotarjeta&evento='+evento;
						$('#datos').attr('action',accion);
						$('#datos').submit();
					}
					
				}else if(pago == 2){
					$('#alerta7').fadeIn();
					$('#aviso').modal('show');
					accion = 'subpages/Compras/predeposito.php?evento='+evento;
					$('#datos').attr('action', accion);
					$('#datos').submit();
				}else if(pago == 3){
					$('#aceptar').attr('disabled',true);
					$('#aceptar').html('espere , generando su compra');
					
					
					var ident1 = $('#ident1').val();
					var dir1 = $('#dir1').val();
					var tel1 = $('#tel1').val();
					var cel1 = $('#cel1').val();
					if(ident1 == 1 && (dir1 == '' || tel1 == '' || cel1 == '')){
						alert('debe ingresar su direccion  , convencional y celular de envio');
						$('#modalCostoEnvio').modal('show');
					}else{
						$.post('subpages/Compras/puntodeventa.php',{
							evento : evento , ident1 : ident1 , dir1 : dir1 , tel1 : tel1 , cel1 : cel1
						}).done(function(data){
							if($.trim(data)==0){
								$.post('subpages/Compras/vacia_sessiones.php',{}).done(function(data){
									
									alert('Uno o mas de sus boletos ya has sido seleccionados , por favor debe escojer otros');
									window.location = 'http://ticketfacil.ec/ticket2/?modulo=des_concierto&con='+evento;
								});	
							}else if($.trim(data)==1){
								alert('su reserva ha sido generada con exito');
								window.location = 'http://ticketfacil.ec/ticket2/?modulo=preventaok&error=ok';
							}
						});	
					}
				}else if(pago == 4){
					// $('#alerta8').fadeIn();
					// $('#aviso').modal('show');
					
					var ident1 = $('#ident1').val();
					var dir1 = $('#dir1').val();
					var tel1 = $('#tel1').val();
					var cel1 = $('#cel1').val();
					if(ident1 == 1 && (dir1 == '' || tel1 == '')){
						alert('debe ingresar su direccion y telefono de envio');
						$('#modalCostoEnvio').modal('show');
					}else{
						accion = '?modulo=pagoPaypal&evento='+evento;
						$('#datos').attr('action', accion);
						$('#datos').submit();
					}
				}
			}
		}else{
			$('#alerta5').fadeIn();
			$('#aviso').modal('show');
		}
	});
	
	if(!document.getElementById('codigo')){
		$('#alerta1').fadeIn();
		$('#aviso').modal('show');
	}
	
	$('#modificar').on('click',function(){
		$('#alerta6').fadeIn();
		$('#aviso').modal('show');
	});
});

$('#cmbpago').on('change',function(){
	var cmb_pago = $('#cmbpago').val();
	if(cmb_pago == 1){
		// accioncmb = '?modulo=pagotarjeta&evento='+evento;
		// $('#datos').attr('action',accioncmb);
		// $('#datos').submit();
	}else if(cmb_pago == 2){
		$('#alerta8').css('display','none');
		$('#alerta7').fadeIn();
		$('#aviso').modal('show');
		// accioncmb = 'subpages/Compras/predeposito.php?evento='+evento;
		// $('#datos').attr('action',accioncmb);
		// $('#datos').submit();
	}else if(cmb_pago == 3){
		$('#alerta7').css('display','none');
		$('#alerta8').fadeIn();
		$('#aviso').modal('show');
		// accioncmb = 'subpages/Compras/puntodeventa.php?evento='+evento;
		// $('#datos').attr('action',accioncmb);
		// $('#datos').submit();
	}
});

function chooseasientos(){
	$('#alerta3').fadeIn();
	$('#aviso').modal('show');
}

function identity(){
	$('#alerta4').fadeIn();
	$('#aviso').modal('show');
}

function security(){
	$('#alerta5').fadeIn();
	$('#aviso').modal('show');
}

function aceptarModal(){
	if(!$('#alerta1').is(':hidden')){
		window.location = '?modulo=des_concierto&con='+evento;
	}else if(!$('#alerta2').is(':hidden')){
		window.location = '?modulo=des_concierto&con='+evento;
	}else if(!$('#alerta3').is(':hidden')){
		window. location = '?modulo=des_concierto&con='+evento;
	}else if(!$('#alerta6').is(':hidden')){
		$('#datos').attr('action','');
		accionmod = '?modulo=updcliente&con='+evento;
		$('#datos').attr('action',accionmod);
		$('#datos').submit();
	}
	$('.alertas').fadeOut();
	$('#aviso').modal('hide');
}


function op(){
	$('#terminosdeuso').modal('show');
}
	
var totalTiempo=300+300;
var timestampStart = new Date().getTime();
var endTime=timestampStart+(totalTiempo*1000);
var timestampEnd=endTime-new Date().getTime();
var tiempRestante=totalTiempo;

updateReloj();

function updateReloj() {
	var Seconds=tiempRestante;
	
	var Days = Math.floor(Seconds / 86400);
	Seconds -= Days * 86400;

	var Hours = Math.floor(Seconds / 3600);
	Seconds -= Hours * (3600);

	var Minutes = Math.floor(Seconds / 60);
	Seconds -= Minutes * (60);

	var TimeStr = ((Days > 0) ? Days + " dias " : "") + LeadingZero(Hours) + ":" + LeadingZero(Minutes) + ":" + LeadingZero(Seconds);
	
	document.getElementById('CuentaAtras').innerHTML = TimeStr;
	if(endTime<=new Date().getTime())
	{
		document.getElementById('CuentaAtras').innerHTML = "00:00:00";
	}else{
		tiempRestante-=1;
		setTimeout("updateReloj()",1000);
	}
}

function LeadingZero(Time) {
	return (Time < 10) ? "0" + Time : + Time;
}
setTimeout("$('#alerta2').fadeIn(); $('#aviso').modal('show');",600000);
</script>