<?php 
	include("../controlusuarios/seguridadSA.php");
	require_once '../classes/private.db.php';
	
	$gbd = new DBConn();
	
	$id = $_REQUEST['id'];
	$ini = $_REQUEST['seciniinfo'];
	if((strlen($ini) == 1)){
		$inicial = '00000000'.$ini;
	}else{
		if((strlen($ini) == 2)){
			$inicial = '0000000'.$ini;
		}else{
			if((strlen($ini) == 3)){
				$inicial = '000000'.$ini;
			}else{
				if((strlen($ini) == 4)){
					$inicial = '00000'.$ini;
				}else{
					if((strlen($ini) == 5)){
						$inicial = '0000'.$ini;
					}else{
						if((strlen($ini) == 6)){
							$inicial = '000'.$ini;
						}else{
							if((strlen($ini) == 7)){
								$inicial = '00'.$ini;
							}else{
								if((strlen($ini) == 8)){
									$inicial = '0'.$ini;
								}else{
									if((strlen($ini) >= 9)){
										$inicial = $ini;
									}
								}
							}
						}
					}
				}
			}
		}
	}
	$fin = $_REQUEST['secfininfo'];
	if((strlen($fin) == 1)){
		$final = '00000000'.$fin;
	}else{
		if((strlen($fin) == 2)){
			$final = '0000000'.$fin;
		}else{
			if((strlen($fin) == 3)){
				$final = '000000'.$fin;
			}else{
				if((strlen($fin) == 4)){
					$final = '00000'.$fin;
				}else{
					if((strlen($fin) == 5)){
						$final = '0000'.$fin;
					}else{
						if((strlen($fin) == 6)){
							$final = '000'.$fin;
						}else{
							if((strlen($fin) == 7)){
								$final = '00'.$fin;
							}else{
								if((strlen($fin) == 8)){
									$final = '0'.$fin;
								}else{
									if((strlen($fin) >= 9)){
										$final = $fin;
									}
								}
							}
						}
					}
				}
			}
		}
	}
	
	try{
		$update = "UPDATE reimpresiones SET secuinicialRS = ?, secufinalRS = ? WHERE idReimpresiones = ?";
		$upd = $gbd -> prepare($update);
		$upd -> execute(array($inicial,$final,$id));
	}catch(PDOEXception $e){
		print_r($e);
	}
?>