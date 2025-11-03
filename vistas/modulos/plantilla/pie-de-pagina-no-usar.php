<?php
  use App\Route;
	// solo incluir los js que se requieran

  // if (isset($_SESSION["appGestionEmpresarial"]["ingreso"]) && $_SESSION["appGestionEmpresarial"]["ingreso"]["validarSesion"] == "ok") {
  if (isset($_SESSION[CONST_SESSION_APP]["ingreso"]) && $_SESSION[CONST_SESSION_APP]["ingreso"]["validarSesion"] == "ok") {
    
    /*=============================================
    FOOTER
    =============================================*/

    include "vistas/modulos/footer.php";

    echo '</div>';

  } else {

    // include "modulos/login-resp.php";
    // include "vistas/modulos/login.php";

  }
  
  unset($_SESSION[CONST_SESSION_APP]["flash"]);
  unset($_SESSION[CONST_SESSION_APP]["flashAlertClass"]);
  unset($_SESSION[CONST_SESSION_APP]["old"]);
  unset($_SESSION[CONST_SESSION_APP]["errors"]);

?>

<script src="<?php echo Route::rutaServidor(); ?>vistas/js/plantilla.js"></script>

<?php if ( isset($archivoJS) ) : ?>
  <script src="<?php echo Route::rutaServidor().$archivoJS ?>"></script>
<?php endif; ?>

<?php if ( isset($archivoJS2) ) : ?>
  <script src="<?php echo Route::rutaServidor().$archivoJS2 ?>"></script>
<?php endif; ?>

<?php if ( isset($archivoJS3) ) : ?>
  <script src="<?php echo Route::rutaServidor().$archivoJS3 ?>"></script>
<?php endif; ?>

<?php if ( isset($archivoJS4) ) : ?>
  <script src="<?php echo Route::rutaServidor().$archivoJS4 ?>"></script>
<?php endif; ?>

<?php if ( isset($archivoJS5) ) : ?>
  <script src="<?php echo Route::rutaServidor().$archivoJS5 ?>"></script>
<?php endif; ?>

<?php if ( isset($archivoJS6) ) : ?>
  <script src="<?php echo Route::rutaServidor().$archivoJS6 ?>"></script>
<?php endif; ?>

<?php if ( isset($comandoJS) ) : ?>
  <script><?php echo $comandoJS ?></script>
<?php endif; ?>

</body>
</html>
