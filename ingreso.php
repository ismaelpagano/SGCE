<?php

	include "funcs/funcs.php";

	if(isset($_SESSION['user'])){
		header("Location: index.php");
	}

	$error = NULL;

	if(count($_SESSION['sistema']->error) > 0){
		$i = count($_SESSION['sistema']->error) - 1;
		$error = $_SESSION['sistema']->error[$i];
	}

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8" />
		<link rel="stylesheet" type="text/css" href="css/styles.css" />
		<link rel="stylesheet" type="text/css" href="css/header.css" />
		<link rel="stylesheet" type="text/css" href="css/visor_llamados.css" />
		<title>SGCE</title>
		<script src="js/funcs.js"></script>
	</head>
	<body>
		<?php if($error!=NULL){ echo $error;} ?>
		<div id="pag">
			<div id="escena">
				<div id="contenedor">
                    <div id="cont_login">
                        <form class="form_login" id="form_login" method="POST" action="verificacion.php">
							<table id="table_login">
								<tr>
									<td class="c1"><label for="username">Nombre de usuario</label></td>
									<td class="c2"><input type="text" id="username" name="username" autofocus placeholder="Usuario..." autocomplete='off'></td>
								</tr>
								<tr>
									<td class="c1"><label for="password">Contraseña</label></td>
									<td class="c2"><input type="password" id="password" name="password" placeholder="Contraseña..."><br></td>
								</tr>
								<tr>
									<td class="cc" colspan="2">
										<div class="cont_subbut">
											<input class="login_subbut" type="submit" value="Ingresar">
										</div>
									</td>
								</tr>
							</table>
                        </form>
                    </div>
				</div>
			</div>
		</div>
		<?php include 'mods/footer.php'; ?>
	</body>
</html>