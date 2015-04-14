<html>
<head>
	<title>C A R R E T O   W E B</title>

	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<meta http-equiv="cache-control" content="no-store" /> 

	<link rel="stylesheet" type="text/css" href="css/cw.css" />
</head>

	<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false"></script>
	<script type="text/javascript" src="js/jquery-1.6.2.min.js"></script>
	<script type="text/javascript">
		$().ready( function(){
			
			// handler de perda de foco dos inputs text
			$('input[class=ponto]').blur( function(){
				geocoding($(this));
			});
			
			// handler do botao limpar
			$('input[class=botaoCln]').click( function(){
				$('div[class=origem]:first').siblings('[class=origem]').each( function(){
					$(this).remove();
				});
				$('div[class=destino]:first').siblings('[class=destino]').each( function(){
					$(this).remove();
				});
				$('input[type=text]:not(:disabled)').each( function(){
					$(this).val('');
				});
				$('div[class=distancia]').each( function(){
					$(this).html('');
				});
			});
			
			// handler do botao distancia
			$('input[class=botaoCalcula]').click( function(){
				$('div[class=totalkm]').html(0);
				$('input[type=text]').each( function(){
					$(this).blur();
				});
				
				var pontos = $('input[class=ponto]');
				for (i=0; i<pontos.length; i++) {
					distance($(pontos[i]), $(pontos[i+1]));
				}
			});
			
			// handler do botao gravar
			$('input[class=botaoGrava]').click( function(){
				$('input[name=orgqtde]').val($('input[name^=orgtxt]').length);
				$('input[name=dstqtde]').val($('input[name^=dsttxt]').length);
				$('form').submit();
			});
			
			// handler do botao de excluir ponto (bind)
			bind();
			
			$('img[id=del]').each( function() {
				$(this).css('display','none');
			});

		});
		
		function bind() {
			$('img').click( function(event) {
				var id = $(this).attr('id');
				
				if (id == 'add') {
					var tipo = $(this).parent().parent().attr('class');
					var obj = $('div[class='+tipo+']:last');
					$(obj).after(obj.clone(true));
					var novo = $('div[class='+tipo+']:last');
					$(novo).children('input').val('');
					var nome = $(novo).children('input').attr('name');
					var novonome = nome.substring(0,6) + (parseInt(nome.substring(6))+1);
					$(novo).children('input').attr('name',novonome);
					$(novo).children('div[class=distancia]').html('');
					$(novo).find('img[id=add]').css('display','none');
					$(novo).find('img[id=del]').css('display','block');
				}
				
				if (id == 'del') {
					$(this).parent().parent().remove();
				}
			});
		}
		
		function soma(destino){
			var conteudo = destino.html();
			var km = conteudo.substring(0, conteudo.indexOf('km')).trim();
			var atual = $('div[class=totalkm]').html().trim();
			km = parseFloat(km) + parseFloat(atual);
			$('div[class=totalkm]').html(km);
		};
		
		function geocoding(obj) {
			var geocoder = new google.maps.Geocoder();

			if (geocoder) {
			  geocoder.geocode({ 'address': obj.val() }, function(results, status) {
				 if (status == google.maps.GeocoderStatus.OK) {
					obj.val(results[0].formatted_address);
				 } else {
					console.log("Geocoding failed: " + status);
				 }
			  });
			}
		};
		
		function distance(origem, destino) {
			var service = new google.maps.DistanceMatrixService();

			if (destino.val() == undefined) {
				destino = $('input[class=ponto]:first');
			}
			service.getDistanceMatrix({ 'origins': [origem.val()] ,
				'destinations': [destino.val()],
				travelMode: google.maps.TravelMode.DRIVING,
				avoidHighways: false,
				avoidTolls: false
				}, function (response, status) {
					if (status == google.maps.DistanceMatrixStatus.OK) {
						var origins = response.originAddresses;
						var destinations = response.destinationAddresses;
						
						var element = response.rows[0].elements[0];
						destino.next('div').html(element.distance.text + " | " + element.duration.text);

						soma(destino.next('div'));
					} else {
						console.log("Distance failed: " + status);
					}
				});
		};

	</script>

<body>
	<?php include 'menusup.php' ?>

	<div class="container">

	<div style="width:900px; height:50px;">
		<div class="fieldName" style="color:gray; font-weight:bold; font-size:15px;">Or&ccedil;amentos</div>
	</div>
	
	<?php
	$con = mysql_connect("localhost","root","clicklib");
	mysql_set_charset('latin1',$con);
	
	if (!$con) {
		die('Nao foi possivel conectar: ' . mysql_error());
	}

	mysql_select_db("carretoweb", $con);
	
	$id = $_GET['id'];
	if (empty($id)) {
		$id = $_POST['id'];
	}
	
	if (!empty($_POST['assuntotxt'])) {	
		$assuntotxt = $_POST['assuntotxt'];
		$insert = "insert into pedido(cliente_id, assunto, dt_solic, status) values (" . $id . ",'" . $assuntotxt . "', now(), 'C')";
		$result = mysql_query($insert);
		$oid = mysql_insert_id();
		
		$orgqtde = $_POST['orgqtde'];
		for ($i=0; $i<$orgqtde; $i++) {
			$insert_org = "insert into ponto(pedido_id, origdest, endereco) values (" . $oid . ", 1, '" . $_POST['orgtxt'.$i] . "')";
			mysql_query($insert_org);
		}
		
		$dstqtde = $_POST['dstqtde'];
		for ($i=0; $i<$dstqtde; $i++) {
			$insert_dst = "insert into ponto(pedido_id, origdest, endereco) values (" . $oid . ", 2, '" . $_POST['dsttxt'.$i] . "')";
			mysql_query($insert_dst);
		}
		
	}
	
	$query = "select * from pedido where cliente_id = " . $id;

	$result = mysql_query($query);

	if (mysql_num_rows($result) == 0) {
		echo "<div class=\"column\"></div>";
		echo "<div class=\"column\">sem resultados</div>";
	}
	
	while($row = mysql_fetch_array($result)) {
		echo "<div>";
		echo "<div class=\"column\" style=\"width:200px;\"><a href=\"pedido.php?oid=" . $row['oid'] . "\">" . $row['assunto'] . "</a></div>";
		echo "<div class=\"column\" style=\"width:200px\">" . $row['dt_solic'] . "</div>";
		echo "<div class=\"column\" style=\"width:50px\">" . $row['status'] . "</div>";
		echo "</div>";
	}

	mysql_close($con);
	?>
	
	<form action="rota.php" method="post">
	<div style="width:900px; height:50px; padding-top:50px;">
		<div class="fieldName" style="color:gray; font-weight:bold; font-size:15px;">Rota</div>
	</div>
	<?php
	echo "<input type=\"hidden\" name=\"id\" value=\"" . $id . "\" />"
	?>
	<input type="hidden" name="orgqtde" value="" />
	<input type="hidden" name="dstqtde" value="" />
	
	<div class="garagem"> 
		<div class="icons"></div>
		<div class="labtexto" style="color:gray">assunto:</div>
		<input type="text" name="assuntotxt" value="" class="assunto" />
	</div>
	
	<div class="garagem"> 
		<input type="hidden" name="garagemtxt" value="Av. Itaborai, 424 - Saude, Sao Paulo - Sao Paulo, 04135-000, Brazil" class="ponto" />
		<div class="distancia"></div>
	</div>

	<div class="origem">
		<div class="icons"><img src="img/icon-del.gif" id="del"/><img src="img/icon-add.gif" id="add"/></div>
		<div class="labtexto">origem:</div>
		<input type="text" name="orgtxt0" value="" class="ponto" />
		<div class="distancia"></div>
	</div>
	<div class="destino">
		<div class="icons"><img src="img/icon-del.gif" id="del"/><img src="img/icon-add.gif" id="add"/></div>
		<div class="labtexto">destino:</div>
		<input type="text" name="dsttxt0" value="" class="ponto" />
		<div class="distancia"></div>
	</div>
	
	<div class="total">
		<div class="totalkm"></div>
	</div>
	
	<div style="text-align:center;">
		<input type="button" class="botaoCln" style="color:red;" id="limpa" value="limpar" />
		<input type="button" class="botaoCalcula" id="calcula" value="calcular" />
		<input type="button" class="botaoGrava" id="grava" value="gravar" />
	</div>
	</form>
	
	</div>

</body>
</html>