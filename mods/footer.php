<?php
?>
<div id="footer">
    <div id="cont_footer">
        <?php echo $_SESSION['user']->nombre.' '.$_SESSION['user']->apellido; ?>
        <input type="submit" onclick="cerrar_sesion()" value="Cerrar sesión">
        <?php include 'chat.php'; ?>
    </div>
</div>