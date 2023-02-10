<?php

    include 'funcs/funcs.php';
	// $_SESSION['sistema']->get_informe();

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
	<body onload="main()">
		<?php include 'mods/header.php'; ?>
		<div id="pag">
			<div id="barra-lateral">
				<div id="barra-lateral-cont">
					<div id="navbar-llamados">
						<ul id="u_list">
							<li class="nb-llamados-bot" onclick="nueva_busqueda('no_cat', 0)">Nuevos llamados</li>
							<li class="nb-llamados-bot" onclick="nueva_busqueda('guardados', 0)">Guardados</li>
							<li class="nb-llamados-bot" onclick="nueva_busqueda('cotizaciones', 0)">Cotizaciones</li>
							<li class="nb-llamados-bot" onclick="nueva_busqueda('adjudicados', 0)">Adjudicaciones</li>
							<li class="nb-llamados-bot" onclick="nueva_busqueda('descartados', 0)">Descartados</li>
							<li class="nb-llamados-bot" id="filtros_arce"><div id="nb-filtros-label" onclick="filtros_arce()">Buscador</div></li>
						</ul>
					</div>
				</div>
			</div>
			<div id="escena">
				<?php include 'mods/monitor_llamados.php' ?>
			</div>
		</div>
		<?php include 'mods/footer.php'; ?>
	</body>
</html>