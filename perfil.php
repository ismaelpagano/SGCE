<?php

    include 'funcs/funcs.php';


?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8" />
		<link rel="stylesheet" type="text/css" href="css/styles.css" />
		<link rel="stylesheet" type="text/css" href="css/header.css" />
		<link rel="stylesheet" type="text/css" href="css/visor_llamados.css" />
		<title>SGCE - Perfíl</title>
		<script src="js/funcs.js"></script>
	</head>
	<body>
		<?php include 'mods/header.php'; ?>
		<div id="pag">
			<div id="escena">
				<div id="contenedor">
					<?php 
						$_SESSION['user']->info_usuario(); 
					?>
				</div>
			</div>
		</div>
		<?php include 'mods/footer.php'; ?>
	</body>
</html>