<?php

?>

<form class="actualizador" method="POST" id="form-actualizador">
    <!-- <input type="radio" id="lv" name="tipo-publicacion" value="lv" checked>
    <label for="lv"> Vigentes</label><br>
    <input type="radio" id="l" name="tipo-publicacion" value="l">
    <label for="l"> Todos los llamados </label><br>
    <input type="radio" id="adj" name="tipo-publicacion" value="a">
    <label for="adj"> Adjudicaciones</label><br>
    <br>
    
    <label for="inicio">Fecha inicio:</label>
    <input type="date" id="inicio" name="fecha-inicial" value="<?php echo date('Y-m-d') ?>"><br>
    <label for="fin">Fecha fin:</label>
    <input type="date" id="fin" name="fecha-final"  value="<?php echo date('Y-m-d')?>"><br>		
    <br> -->
    
    <input class="updateButton" type='submit' value="Actualizar BD" onclick="botonActualizar()">
</form>