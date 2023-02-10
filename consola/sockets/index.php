<?php

    include 'funcs/funcs.php';


?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8" />
		<link rel="stylesheet" type="text/css" href="css/styles.css" />
        <link rel="stylesheet" type="text/css" href="css/header.css" />
		<title>SGCE</title>
		<script src="js/funcs.js"></script>
	</head>
	<body onload="socket()">
		<?php include 'mods/header.php'; ?>
		<div id="pag">
			<div id="barra-lateral">
				<div id="barra-lateral-cont">
				</div>
			</div>
			<div id="escena">
			</div>
		</div>
		<?php include 'mods/footer.php'; ?>
	</body>
</html>