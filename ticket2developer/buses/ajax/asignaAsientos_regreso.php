<?php
	session_start();
	include '../enoc.php';
	$id_horario = $_REQUEST['id_horario'];
	$id = $_REQUEST['id_horario'];
	$asiento = $_REQUEST['asiento'];
	$id_bus = $_REQUEST['id_bus'];
	$fecha = $_REQUEST['fecha'];
	$tipo_destino = $_REQUEST['tipo_destino'];
	
	$sql = 'insert into ocupadas (id , id_ruta , asiento , fecha_salida , estado) values (null , "'.$id_horario.'" , "'.$asiento.'" , "'.$fecha.'" , "s"  ) ';
	$res = mysql_query($sql) or die (mysql_error());
	$product_id = mysql_insert_id();
	if ( isset($_SESSION['carrito_regreso']) || isset($_POST['nombre'])){
		if(isset ($_SESSION['carrito_regreso'])){
			
			$compras_regreso=$_SESSION['carrito_regreso'];
			if(isset($_POST['nombre'])){
				$nombre=$_POST['nombre'];
				$precio=$_POST['precio'];
				$cantidad=$_POST['cantidad'];
				$tipo_destino=$_POST['tipo_destino'];
				//$product_id=$_POST['product_id'];
				$duplicado=-1;
				for($i=0;$i<=count($compras_regreso)-1;$i++){
					if($nombre==$compras_regreso[$i]['nombre']){
						$duplicado=$i;
					}
				}

				if($duplicado != -1){
					$cantidad_nueva = $compras_regreso[$duplicado]['cantidad'] + $cantidad;
						$compras_regreso[$duplicado]=array("nombre"=>$nombre,"precio"=>$precio,"cantidad"=>$cantidad_nueva,"product_id"=>$product_id,"tipo_destino"=>$tipo_destino);
				}else {
						$compras_regreso[]=array("nombre"=>$nombre,"precio"=>$precio,"cantidad"=>$cantidad,"product_id"=>$product_id,"tipo_destino"=>$tipo_destino);
				}
			}
		}else{
			$nombre=$_POST['nombre'];
			$precio=$_POST['precio'];
			$cantidad=$_POST['cantidad'];
			$tipo_destino=$_POST['tipo_destino'];
			//$product_id=$_POST['product_id'];
			$compras_regreso[]=array("nombre"=>$nombre,"precio"=>$precio,"cantidad"=>$cantidad ,"product_id"=>$product_id,"tipo_destino"=>$tipo_destino);

		}
		$cuantoscarrito=$_SESSION['$cuantoscarrito']+$_POST['cantidad'];//contador
		if(isset($_POST['cantidadactualizada'])){
			$id=$_POST['id'];
			$contador_cant=$_POST['cantidadactualizada'];
			if($contador_cant<1){
				$compras_regreso[$id]=NULL;
			}else{
				$compras_regreso[$id]['cantidad']=$contador_cant;
				}
		}
		if(isset($_POST['id2'])){
			$id=$_POST['id2'];
			$compras_regreso[$id]=NULL;
		}
		$_SESSION['carrito_regreso']=$compras_regreso;
		//print_r($_SESSION['carrito_regreso']);
		
		$_SESSION['$cuantoscarrito']=$cuantoscarrito;//asigna contador
		$carro = count($_SESSION['carrito_regreso']);
	
	}
	
	for($i=0;$i<=count($compras_regreso)-1;$i++){
		echo "<br/><br/><br/>ID:".$compras_regreso[$i]['product_id']."<br/>";
		echo "Nombre:".$compras_regreso[$i]['nombre']."<br/>";
		echo "Cantidad:".$compras_regreso[$i]['cantidad']."<br/><br/>";
		echo "Precio:".$compras_regreso[$i]['precio']."<br/><br/>";
		echo "tipo de destino".$compras_regreso[$i]['tipo_destino']."<br/><br/>";
	}
	
?>
	