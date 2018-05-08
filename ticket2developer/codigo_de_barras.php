<?php
	session_start();
	include('Barcode.php');
	$codigo = $_REQUEST['barcode'];
	$im     = imagecreatetruecolor(170, 65);
	$black  = ImageColorAllocate($im,0x00,0x00,0x00);
	$white  = ImageColorAllocate($im,0xff,0xff,0xff);
	imagefilledrectangle($im, 0, 0, 170, 65, $white);
	//Barcode::gd($res, $color, $x, $y, $angle, $type, $datas, $width = null, $height = null);
	$data = Barcode::gd($im, $black, 85, 30, 0, "code128", $codigo, 1, 50);
	header('Content-type: image/png');
	imagepng($im);
	imagedestroy($im);
	
	// $imgbar = 'barcode/'.$codigo.'.png';
	// file_put_contents($imgbar, file_get_contents($urlbar));
?>