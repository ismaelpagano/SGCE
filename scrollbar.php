<?php

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8" />
		<link rel="stylesheet" type="text/css" href="css/styles.css" />
		<link rel="stylesheet" type="text/css" href="css/header.css" />
		<link rel="stylesheet" type="text/css" href="css/visor_llamados.css" />
		<link rel="stylesheet" type="text/css" href="css/filtros.css" />
		<link rel="stylesheet" type="text/css" href="css/chat.css" />
		<title>SGCE</title>
		<script src="js/scrollbar.js"></script>
		<script src="js/funcs.js"></script>
	</head>
	<body>
    <body>
		<?php include 'mods/header.php'; ?>
		<div id="pag">
			<div id="barra-lateral">
				<div id="barra-lateral-cont">
					<div id="navbar-llamados">
					</div>
				</div>
			</div>
			<div id="escena">
                <div id="scrolleable_cont">
                    <div id="cont_monitor">
                        <div id="cont_cant_llamados">
                            <div id="cant_resultados"></div>
                        </div>
                        <div id="visor_llamados"></div>
                    </div>
                </div>
                <div id="scrollbar_monitor">
                    <div id="scroll_up"></div>
                    <div id="scrollbar_slider">
                        <div id="puntero_scrollbar"></div>
                    </div>
                    <div id="scroll_down"></div>
                </div>
			</div>
		</div>
		<?php include 'mods/footer.php'; ?>
	</body>
	</body>
</html>