<input type="hidden" id="data" value="21" />
<div style="margin:10px -10px;">
<script type="text/javascript" src="js/jquery.numeric.js"></script>
	<body onload = '$("#recibe_code_barras").focus()'>
		<div style="background-color:#171A1B;"  >
			<div class = 'row'>
				<div class = 'col-md-12'>
					<h3 style = 'padding-top: 10px;padding-bottom: 10px;padding-left: 10px;padding-right: 10px;background-color: #ED1568;color: #fff'>
						DAR DE BAJA BOLETOS
					</h3>
				</div>
			</div>
			<div class = 'row'>
				<div class = 'col-xs-3'></div>
				<div class = 'col-xs-5' style = 'color:#fff;'>
					<div class="content">
						<input type = 'text' id = 'recibe_code_barras' class = 'form-control entero' placeholder = 'Escanee el codigo del boleto!!' />
						<br>
						<?php
							include 'conexion.php';
							$sqlCl = 'select * from claves where id_con = 40';
							$resCl = mysql_query($sqlCl) or die (mysql_error());
							$rowCl = mysql_fetch_array($resCl);
							
							$clave = $rowCl['clave2'];
							
							echo "<input type = 'hidden' id = 'clave2' value = '".$clave."' />";
						?>
						<input type = 'password' id = 'recibe_clave2' class = 'form-control' placeholder = 'Ingrese clave' />
						<br>
						<center><button type="button" class="btn btn-primary" onclick = 'recibeBoletoBaja()' >Enviar</button></center>
					</div>
				</div>
				<div class = 'col-xs-3'></div>
			</div>
			
		</div>
	</body>
</div>
<script type="text/javascript">
	function recibeBoletoBaja(){
		var boleto = $("#recibe_code_barras").val();
		var ident = 1;
		var clave2 = $('#clave2').val();
		var recibe_clave2 = $('#recibe_clave2').val();
		if(clave2 != recibe_clave2){
			alert('la clave ingresada es incorrecta');
		}else{
			$.post("distribuidor/recibeBoletoBaja.php",{ 
				boleto : boleto , ident : ident
			}).done(function(data){
				alert(data);
				window.location ='';
			});
		}
	}
	$('.entero').numeric();
	/*
 *
	 * Copyright (c) 2006-2010 Sam Collett (http://www.texotela.co.uk)
	 * Dual licensed under the MIT (http://www.opensource.org/licenses/mit-license.php)
	 * and GPL (http://www.opensource.org/licenses/gpl-license.php) licenses.
	 * 
	 * Version 1.2
	 * Demo: http://www.texotela.co.uk/code/jquery/numeric/
	 *
	 */
	(function($) {
	/*
	 * Allows only valid characters to be entered into input boxes.
	 * Note: does not validate that the final text is a valid number
	 * (that could be done by another script, or server-side)
	 *
	 * @name     numeric
	 * @param    decimal      Decimal separator (e.g. '.' or ',' - default is '.'). Pass false for integers
	 * @param    callback     A function that runs if the number is not valid (fires onblur)
	 * @author   Sam Collett (http://www.texotela.co.uk)
	 * @example  $(".numeric").numeric();
	 * @example  $(".numeric").numeric(",");
	 * @example  $(".numeric").numeric(null, callback);
	 *
	 */
	$.fn.numeric = function(decimal, callback)
	{
		decimal = (decimal === false) ? "" : decimal || ".";
		callback = typeof callback == "function" ? callback : function(){};
		return this.data("numeric.decimal", decimal).data("numeric.callback", callback).keypress($.fn.numeric.keypress).blur($.fn.numeric.blur);
	}

	$.fn.numeric.keypress = function(e)
	{
		var decimal = $.data(this, "numeric.decimal");
		var key = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0;
		// allow enter/return key (only when in an input box)
		if(key == 13 && this.nodeName.toLowerCase() == "input")
		{
			return true;
		}
		else if(key == 13)
		{
			return false;
		}
		var allow = false;
		// allow Ctrl+A
		if((e.ctrlKey && key == 97 /* firefox */) || (e.ctrlKey && key == 65) /* opera */) return true;
		// allow Ctrl+X (cut)
		if((e.ctrlKey && key == 120 /* firefox */) || (e.ctrlKey && key == 88) /* opera */) return true;
		// allow Ctrl+C (copy)
		if((e.ctrlKey && key == 99 /* firefox */) || (e.ctrlKey && key == 67) /* opera */) return true;
		// allow Ctrl+Z (undo)
		if((e.ctrlKey && key == 122 /* firefox */) || (e.ctrlKey && key == 90) /* opera */) return true;
		// allow or deny Ctrl+V (paste), Shift+Ins
		if((e.ctrlKey && key == 118 /* firefox */) || (e.ctrlKey && key == 86) /* opera */
		|| (e.shiftKey && key == 45)) return true;
		// if a number was not pressed
		if(key < 48 || key > 57)
		{
			/* '-' only allowed at start */
			if(key == 45 && this.value.length == 0) return true;
			/* only one decimal separator allowed */
			if(decimal && key == decimal.charCodeAt(0) && this.value.indexOf(decimal) != -1)
			{
				allow = false;
			}
			// check for other keys that have special purposes
			if(
				key != 8 /* backspace */ &&
				key != 9 /* tab */ &&
				key != 13 /* enter */ &&
				key != 35 /* end */ &&
				key != 36 /* home */ &&
				key != 37 /* left */ &&
				key != 39 /* right */ &&
				key != 46 /* del */
			)
			{
				allow = false;
			}
			else
			{
				// for detecting special keys (listed above)
				// IE does not support 'charCode' and ignores them in keypress anyway
				if(typeof e.charCode != "undefined")
				{
					// special keys have 'keyCode' and 'which' the same (e.g. backspace)
					if(e.keyCode == e.which && e.which != 0)
					{
						allow = true;
						// . and delete share the same code, don't allow . (will be set to true later if it is the decimal point)
						if(e.which == 46) allow = false;
					}
					// or keyCode != 0 and 'charCode'/'which' = 0
					else if(e.keyCode != 0 && e.charCode == 0 && e.which == 0)
					{
						allow = true;
					}
				}
			}
			// if key pressed is the decimal and it is not already in the field
			if(decimal && key == decimal.charCodeAt(0))
			{
				if(this.value.indexOf(decimal) == -1)
				{
					allow = true;
				}
				else
				{
					allow = false;
				}
			}
		}
		else
		{
			allow = true;
		}
		return allow;
	}

	$.fn.numeric.blur = function()
	{
		var decimal = $.data(this, "numeric.decimal");
		var callback = $.data(this, "numeric.callback");
		var val = $(this).val();
		if(val != "")
		{
			var re = new RegExp("^\\d+$|\\d*" + decimal + "\\d+");
			if(!re.exec(val))
			{
				callback.apply(this);
			}
		}
	}

	$.fn.removeNumeric = function()
	{
		return this.data("numeric.decimal", null).data("numeric.callback", null).unbind("keypress", $.fn.numeric.keypress).unbind("blur", $.fn.numeric.blur);
	}

	})(jQuery);
</script>
