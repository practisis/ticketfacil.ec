<meta charset="iso-8859-1">
<link href='http://fonts.googleapis.com/css?family=Roboto:300,700,400' rel='stylesheet' type='text/css'>
<style>
	#menu {
		width: 100%;
		margin: 0;
		padding: 5px 0 0;
		list-style: none;  
		background-color: #111;
		background-image: linear-gradient(#444, #111);
		border-radius: 10px;
		box-shadow: 0 2px 1px #9c9c9c;
	}

	#menu li {
		float: left;
		padding: 0 0 10px 0;
		position: relative;
	}

	#menu a {
		float: left;
		height: 25px;
		padding: 0 25px;
		color: #999;
		text-transform: uppercase;
		font: bold 15px/25px Roboto;
		text-decoration: none;
		text-shadow: 0 1px 0 #000;
	}

	#menu li:hover > a {
		color: #fafafa;
	}

	#menu li:hover > ul {
		display: block;
	}
</style>
<div>
	<ul id="menu">
		<!--<li><a href="boleto.php" title="Admission Ticket">Ingreso de Boletos</a></li>-->
		<!--<li><a href="revisardeposito.php" title="Deposits">Dep&oacute;sitos</a></li>
		<li><a href="list_concierto.php" title="My Concerts">Mis Conciertos</a></li>-->
		<li><a href="#" title="Estadistics">Estadisticas</a></li> 
		<li><a href="../controlusuarios/salir.php" title="Exit">Salir</a></li>
		<li></li>
	</ul>
</div>