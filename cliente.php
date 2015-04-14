<html>
<head>
	<title>CW - cadastro de clientes</title>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<meta http-equiv="cache-control" content="no-store" />
	
	<link rel="stylesheet" type="text/css" href="css/cw.css" />

	<script type="text/javascript" src="js/jquery-1.6.2.min.js"></script>
	<script type="text/javascript">
		$().ready( function(){
			// handler do botao limpar
			$('input[class=botaoCln]').click( function(){
				$('input[class=fieldValue]').each( function(){
					$(this).val('');
				});
			});
			
			// handler do botao gravar
			$('input[class=botaoCadastra]').click( function(){
				$('form').submit();
			});
			
		});
		
	</script>

</head>

<body>
	<div style="width:900px; height:50px;">
		<div class="fieldName" style="color:gray; font-weight:bold; font-size:15px;">Clientes</div>
	</div>
	
	<?php
	$con = mysql_connect("localhost","root","clicklib");
	mysql_set_charset('latin1',$con);

	if (!$con) {
		die('Nao foi possivel conectar: ' . mysql_error());
	}

	mysql_select_db("carretoweb", $con);
	
	if (!empty($_POST['nome'])) {
		$insert = "insert into cliente(nome, email, endereco, telefone) values
			( '$_POST[nome]','$_POST[email]','$_POST[endereco]','$_POST[telefone]') ";

		if (!mysql_query($insert, $con)) {
			die('Error:' . mysql_error());
		}
	}

	$query = "select * from cliente order by nome";

	$result = mysql_query($query);

	if (mysql_num_rows($result) == 0) {
		echo "<div class=\"column\"></div>";
		echo "<div class=\"column\">sem resultados</div>";
	}
	
	while($row = mysql_fetch_array($result)) {
		echo "<div>";
		echo "<div class=\"column\" style=\"width:200px;\"><a href=\"rota.php?id=" . $row['id'] . "\">" . $row['nome'] . "</a></div>";
		echo "<div class=\"column\" style=\"width:200px\">" . $row['email'] . "</div>";
		echo "<div class=\"column\" style=\"width:250px\">" . $row['endereco'] . "</div>";
		echo "<div class=\"column\">" . $row['telefone'] . "</div>";
		echo "</div>";
	}

	mysql_close($con);
	?>
	
	<form action="cliente.php" method="post">
	<div style="width:900px; height:50px; padding-top:50px;">
		<div class="fieldName" style="color:gray; font-weight:bold; font-size:15px;">Cadastro</div>
	</div>
	<div> 
		<div class="fieldName" style="color:gray">nome:</div>
		<input class="fieldValue" type="text" name="nome" value="" maxlength="40" />
	</div>
	<div> 
		<div class="fieldName" style="color:gray">email:</div>
		<input class="fieldValue" type="text" name="email" value="" maxlength="50" />
	</div>
	<div> 
		<div class="fieldName" style="color:gray">endere&ccedil;o:</div>
		<input class="fieldValue" type="text" name="endereco" value="" maxlength="60" />
	</div>
	<div> 
		<div class="fieldName" style="color:gray">telefone:</div>
		<input class="fieldValue" type="text" name="telefone" value="" maxlength="13" />
	</div>
	
	<div></div>
	
	<div style="text-align:center;">
		<input type="button" class="botaoCln" style="color:red;" id="limpa" value="limpar" />
		<input type="submit" class="botaoCadastra" id="cadastra" value="cadastrar" />
	</div>
	</form>

</body>
</html>