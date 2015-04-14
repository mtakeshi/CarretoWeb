<html>
<head>
	<title>C A R R E T O   W E B</title>

	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<meta http-equiv="cache-control" content="no-store" /> 

	<link rel="stylesheet" type="text/css" href="css/cw.css" />
</head>

<body>
	<?php include 'menusup.php' ?>
	
	<div class="container">
	<?php
	$con = mysql_connect("localhost","root","clicklib");
	mysql_set_charset('latin1',$con);
	
	if (!$con) {
		die('Nao foi possivel conectar: ' . mysql_error());
	}

	mysql_select_db("carretoweb", $con);

	$oid = $_GET['oid'];
	$query1 = "select * from pedido where oid = " . $oid;
	$result1 = mysql_query($query1);

	while($row1 = mysql_fetch_array($result1)) {
		echo "<div>";
		echo "<div class=\"column\" style=\"width:200px;\">" . $row1['assunto'] . "</div>";
		echo "<div class=\"column\" style=\"width:200px\">" . $row1['dt_solic'] . "</div>";
		echo "<div class=\"column\" style=\"width:50px\">" . $row1['status'] . "</div>";
		echo "</div>";
		$pedidoid = $row1['oid'];
	}
	
	$query2 = "select * from ponto where pedido_id = " . $pedidoid . " order by pid";
	$result2 = mysql_query($query2);
	while($row2 = mysql_fetch_array($result2)) {
		echo "<div>";
		echo "<div class=\"column\" style=\"width:50px;\"></div>";
		echo "<div class=\"column\" style=\"width:200px;\">";
		if ($row2['origdest'] == 1) {
			echo "origem";
		} else if ($row2['origdest'] == 2) {
			echo "destino";
		}
		echo "</div>";
		echo "<div class=\"column\" style=\"width:500px\">" . $row2['endereco'] . "</div>";
		echo "</div>";
	}

	mysql_close($con);
	?>
	
	<input type="button" value="aprovar" />
	</div>

</body>
</html>