<?php

use App\Route;

if ( !isset($_SESSION[CONST_SESSION_APP]["ingreso"]) || $_SESSION[CONST_SESSION_APP]["ingreso"]["validarSesion"] != "ok" ) {

  echo '<script>

    window.location = "'.Route::rutaServidor().'ingreso";

  </script>';

}

require_once "app/Models/Usuario.php";
$usuarioAutenticado = New App\Models\Usuario;
if ( usuarioAutenticado() ) {

    $usuarioAutenticado->consultar("usuario", usuarioAutenticado()["usuario"]);
    $usuarioAutenticado->consultarPerfiles();
    $usuarioAutenticado->consultarPermisos();
    
}

?>

<!DOCTYPE html>
<html>
<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <title><?php echo CONST_APP_NAME; ?></title>

  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

  <link rel="icon" href="<?php echo Route::rutaServidor(); ?>vistas/img/plantilla/icono-negro.png">

  <!-- <link rel="stylesheet" href="/vistas/css/estilos.css"> -->

   <!--=====================================
  PLUGINS DE CSS
  ======================================-->

  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="<?php echo Route::rutaServidor(); ?>vistas/bower_components/bootstrap/dist/css/bootstrap.min.css">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo Route::rutaServidor(); ?>vistas/bower_components/font-awesome/css/font-awesome.min.css">

  <!-- Ionicons -->
  <link rel="stylesheet" href="<?php echo Route::rutaServidor(); ?>vistas/bower_components/Ionicons/css/ionicons.min.css">

  <!-- Select2 -->
  <link rel="stylesheet" href="<?php echo Route::rutaServidor(); ?>vistas/bower_components/select2/dist/css/select2.min.css">

  <!-- bootstrap datepicker -->
  <link rel="stylesheet" href="<?php echo Route::rutaServidor(); ?>vistas/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css">

  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo Route::rutaServidor(); ?>vistas/dist/css/AdminLTE.css">
  
  <!-- AdminLTE Skins -->
  <link rel="stylesheet" href="<?php echo Route::rutaServidor(); ?>vistas/dist/css/skins/_all-skins.min.css">

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

   <!-- DataTables -->
  <link rel="stylesheet" href="<?php echo Route::rutaServidor(); ?>vistas/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo Route::rutaServidor(); ?>vistas/bower_components/datatables.net-bs/css/responsive.bootstrap.min.css">

  <!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="<?php echo Route::rutaServidor(); ?>vistas/plugins/iCheck/all.css">

  <!-- Daterange picker -->
  <link rel="stylesheet" href="<?php echo Route::rutaServidor(); ?>vistas/bower_components/bootstrap-daterangepicker/daterangepicker.css">

  <!-- Morris chart -->
  <link rel="stylesheet" href="<?php echo Route::rutaServidor(); ?>vistas/bower_components/morris.js/morris.css">

  <!-- Dropzone -->
  <link rel="stylesheet" href="<?php echo Route::rutaServidor(); ?>vistas/plugins/dropzone/dropzone.css">

  <link rel="stylesheet" href="<?php echo Route::rutaServidor(); ?>vistas/css/estilos.css">

  <!--=====================================
  PLUGINS DE JAVASCRIPT
  ======================================-->

  <!-- jQuery 3 -->
  <script src="<?php echo Route::rutaServidor(); ?>vistas/bower_components/jquery/dist/jquery.min.js"></script>
  
  <!-- Bootstrap 3.3.7 -->
  <script src="<?php echo Route::rutaServidor(); ?>vistas/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

  <!-- FastClick -->
  <script src="<?php echo Route::rutaServidor(); ?>vistas/bower_components/fastclick/lib/fastclick.js"></script>
  
  <!-- AdminLTE App -->
  <script src="<?php echo Route::rutaServidor(); ?>vistas/dist/js/adminlte.min.js"></script>

  <!-- DataTables -->
  <script src="<?php echo Route::rutaServidor(); ?>vistas/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
  <script src="<?php echo Route::rutaServidor(); ?>vistas/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
  <script src="<?php echo Route::rutaServidor(); ?>vistas/bower_components/datatables.net-bs/js/dataTables.responsive.min.js"></script>
  <script src="<?php echo Route::rutaServidor(); ?>vistas/bower_components/datatables.net-bs/js/responsive.bootstrap.min.js"></script>

   <!-- By default SweetAlert2 doesn't support IE. To enable IE 11 support, include Promise polyfill:-->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/core-js/2.4.1/core.js"></script>
  <!-- <script src="<?php echo Route::rutaServidor(); ?>vistas/plugins/sweetalert2/core.js"></script> -->

  <!-- SweetAlert 2 -->
  <script src="<?php echo Route::rutaServidor(); ?>vistas/plugins/sweetalert2/sweetalert2.all.js"></script>

  <!-- iCheck 1.0.1 -->
  <script src="<?php echo Route::rutaServidor(); ?>vistas/plugins/iCheck/icheck.min.js"></script>

  <!-- InputMask -->
  <script src="<?php echo Route::rutaServidor(); ?>vistas/plugins/input-mask/jquery.inputmask.js"></script>
  <script src="<?php echo Route::rutaServidor(); ?>vistas/plugins/input-mask/jquery.inputmask.date.extensions.js"></script>
  <script src="<?php echo Route::rutaServidor(); ?>vistas/plugins/input-mask/jquery.inputmask.extensions.js"></script>

  <!-- jQuery Number -->
  <script src="<?php echo Route::rutaServidor(); ?>vistas/plugins/jqueryNumber/jquerynumber.min.js"></script>

  <!-- daterangepicker http://www.daterangepicker.com/-->
  <script src="<?php echo Route::rutaServidor(); ?>vistas/bower_components/moment/min/moment.min.js"></script>
  <script src="<?php echo Route::rutaServidor(); ?>vistas/bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>

  <!-- Morris.js charts http://morrisjs.github.io/morris.js/-->
  <script src="<?php echo Route::rutaServidor(); ?>vistas/bower_components/raphael/raphael.min.js"></script>
  <script src="<?php echo Route::rutaServidor(); ?>vistas/bower_components/morris.js/morris.min.js"></script>

  <!-- ChartJS http://www.chartjs.org/-->
  <script src="<?php echo Route::rutaServidor(); ?>vistas/bower_components/chart.js/2.7.3/Chart.js"></script>
  
</head>

<!--=====================================
CUERPO DOCUMENTO
======================================-->

<body class="hold-transition skin-blue sidebar-collapse sidebar-mini login-page">
 
  <?php

  if (isset($_SESSION[CONST_SESSION_APP]["ingreso"]) && $_SESSION[CONST_SESSION_APP]["ingreso"]["validarSesion"] == "ok") {

   echo '<div class="wrapper">';

    /*=============================================
    CABEZOTE
    =============================================*/

    include "vistas/modulos/cabezote.php";

    /*=============================================
    MENU
    =============================================*/

    include "vistas/modulos/menu.php";

  }
