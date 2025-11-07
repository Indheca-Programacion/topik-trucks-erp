<?php use App\Route; ?>

<!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-light-red elevation-4">
    <!-- Brand Logo -->
    <a href="inicio" class="brand-link navbar-red">
      <img src="<?php echo Route::rutaServidor(); ?>vistas/adminlte/dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text text-white font-weight-light">CMP 1.0</span>
    </a>


    <?php
    $foto = is_null($usuarioAutenticado->foto) ? "vistas/img/usuarios/default/anonymous.png" : $usuarioAutenticado->foto;
    ?>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="<?php echo Route::rutaServidor().$foto; ?>" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <!-- <a href="<?php echo Route::names('usuarios.edit', usuarioAutenticado()["id"]); ?>" class="d-block text-capitalize"><?=mb_strtolower(fString($usuarioAutenticado->nombreCompleto))?></a> -->
           
          <a href="<?php echo Route::routes('perfil'); ?>" class="d-block text-capitalize"><?=mb_strtolower(fString($usuarioAutenticado->nombreCompleto))?></a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->               

        <?php if ( usuarioAutenticado() ): ?>

          <li class="nav-item">
            <!-- <a href="inicio" class="nav-link active"> -->
            <a href="<?php echo Route::routes('inicio'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "inicio") ? 'active' : '' ); ?>">
              <i class="nav-icon fas fa-home"></i>
              <p>Inicio</p>
            </a>
          </li>

          <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("maquinarias") || $usuarioAutenticado->checkPermiso("servicios") || $usuarioAutenticado->checkPermiso("informacion-tecnica") || $usuarioAutenticado->checkPermiso("generadores") ): ?>

          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-truck-moving"></i>
              <p>
                Operaciones
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("maquinarias") ): ?>
              <li class="nav-item">
                <a href="maquinarias" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Maquinarias</p>
                </a>
              </li>
              <?php endif ?>
              <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("servicios") ): ?>
              <li class="nav-item">
                <a href="servicios" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Servicios</p>
                </a>
              </li>
              <?php endif ?>
              <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("informacion-tecnica") ): ?>
              <li class="nav-item">
                <a href="informacion-tecnica" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Información Técnica</p>
                </a>
              </li>
              <?php endif ?>
              <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("generadores") ): ?>
              <li class="nav-item">
                <a href="generadores" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Generadores</p>
                </a>
              </li>
              <?php endif ?>
            </ul>
          </li>

          <?php endif ?>

          <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("maquinaria-tipos") || $usuarioAutenticado->checkPermiso("marcas") || $usuarioAutenticado->checkPermiso("modelos") || $usuarioAutenticado->checkPermiso("colores") || $usuarioAutenticado->checkPermiso("estatus") ||  $usuarioAutenticado->checkPermiso("ubicaciones") || $usuarioAutenticado->checkPermiso("maquinarias") || $usuarioAutenticado->checkPermiso("checklist-maquinarias") || $usuarioAutenticado->checkPermiso("kit-mantenimiento") || $usuarioAutenticado->checkPermiso("obras") ): ?>

            <li class="nav-item">
              <a href="#" class="nav-link <?php echo ( (Route::getRoute() == "maquinaria-tipos" || Route::getRoute() == "marcas" || Route::getRoute() == "modelos" || Route::getRoute() == "colores" || Route::getRoute() == "estatus" || Route::getRoute() == "ubicaciones" || Route::getRoute() == "maquinarias" || Route::getRoute() == "checklist-maquinarias" || Route::getRoute() == "kit-mantenimiento" || Route::getRoute() == "obras" ) ? 'active"' : '' ); ?>">
                <i class="nav-icon fas fa-truck"></i>
                <p>
                  Maquinaria
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("maquinaria-tipos") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('maquinaria-tipos.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "maquinaria-tipos") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Tipos de Maquinaria</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("marcas") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('marcas.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "marcas") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Marcas</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("modelos") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('modelos.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "modelos") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Modelos</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("colores") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('colores.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "colores") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Colores</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("estatus") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('estatus.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "estatus") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Estatus</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("ubicaciones") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('ubicaciones.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "ubicaciones") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Ubicaciones</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("obras") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('obras.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "obras") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Obras</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("maquinarias") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('maquinarias.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "maquinarias") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Maquinarias</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("checklist-maquinaria") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('checklist-maquinarias.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "checklist-maquinarias") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Checklist Maquinarias</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("kit-mantenimiento") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('kit-mantenimiento.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "kit-mantenimiento") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Kit Mantenimiento</p>
                  </a>
                </li>
                <?php endif ?>
              </ul>
            </li>

          <?php endif ?>

          <!-- INVENTARIO -->
          <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("inventarios") || $usuarioAutenticado->checkPermiso("resguardos") || $usuarioAutenticado->checkPermiso("almacenes")  ): ?>

            <li class="nav-item">
              <a href="#" class="nav-link <?php echo ( (Route::getRoute() == "inventarios" || Route::getRoute() == "resguardos" || Route::getRoute() == "almacenes" ) ? ' active"' : '' ); ?>">
                <i class="nav-icon fas fa-boxes"></i>
                <p>
                  Inventarios
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("inventarios") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('inventarios.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "inventarios") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Inventarios</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("resguardos") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('resguardos.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "resguardos") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Resguardos</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("almacenes") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('almacenes.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "almacenes") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Almacenes</p>
                  </a>
                </li>
                <?php endif ?>
              </ul>
            </li>

          <?php endif ?>

          <!-- COSTOS -->
          <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("resumen-costos") || $usuarioAutenticado->checkPermiso("requisiciones") || $usuarioAutenticado->checkPermiso("requisicion-gastos") || $usuarioAutenticado->checkPermiso("gastos") || $usuarioAutenticado->checkPermiso("OrdenCompra") || $usuarioAutenticado->checkPermiso("comprobacion-gastos") ): ?>
            <li class="nav-item">
              <a href="#" class="nav-link <?php echo ( (Route::getRoute() == "resumen-costos" || Route::getRoute() == "requisiciones" || Route::getRoute() == "requisicion-gastos" || Route::getRoute() == "gastos" || Route::getRoute() == "orden-compra" || Route::getRoute() == "comprobacion-gastos") ? 'active"' : '' ); ?>">
                <i class="nav-icon 	fas fa-dollar-sign"></i>
                <p>
                  Costos
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("resumen-costos") ): ?>
                  <li class="nav-item">
                    <a href="<?php echo Route::names('resumen-costos.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "resumen-costos") ? 'active' : '' ); ?>">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Resumen</p>
                    </a>
                  </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("requisiciones") ): ?>
                  <li class="nav-item">
                    <a href="<?php echo Route::names('requisiciones.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "requisiciones") ? 'active' : '' ); ?>">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Requisiciones</p>
                    </a>
                  </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("requisicion-gastos") ): ?>
                  <li class="nav-item">
                    <a href="<?php echo Route::names('requisicion-gastos.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "requisicion-gastos") ? 'active' : '' ); ?>">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Requisición de Gastos</p>
                    </a>
                  </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("gastos") ): ?>
                  <li class="nav-item">
                    <a href="<?php echo Route::names('gastos.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "gastos") ? 'active' : '' ); ?>">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Gastos</p>
                    </a>
                  </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("OrdenCompra") ): ?>
                  <li class="nav-item">
                    <a href="<?php echo Route::names('orden-compra.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "orden-compra") ? 'active' : '' ); ?>">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Órdenes de Compra</p>
                    </a>
                  </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("comprobacion-gastos") ): ?>
                  <li class="nav-item">
                    <a href="<?php echo Route::names('comprobacion-gastos.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "comprobacion-gastos") ? 'active' : '' ); ?>">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Comprobación de Gastos</p>
                    </a>
                  </li>
                <?php endif ?>

              </ul>
            </li>

          <?php endif ?>

          <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("servicio-centros") || $usuarioAutenticado->checkPermiso("estatus-orden-compra") || $usuarioAutenticado->checkPermiso("unidades") || $usuarioAutenticado->checkPermiso("mantenimiento-tipos") || $usuarioAutenticado->checkPermiso("servicio-tipos") ||  $usuarioAutenticado->checkPermiso("solicitud-tipos") || $usuarioAutenticado->checkPermiso("servicios") ||  $usuarioAutenticado->checkPermiso("actividad-semanal") || $usuarioAutenticado->checkPermiso("tareas") || $usuarioAutenticado->checkPermiso("gastos") || $usuarioAutenticado->checkPermiso("obras") || $usuarioAutenticado->checkPermiso("traslados") || $usuarioAutenticado->checkPermiso("OrdenCompra") || $usuarioAutenticado->checkPermiso("estimaciones") || $usuarioAutenticado->checkPermiso("generadores") || $usuarioAutenticado->checkPermiso("alertas") ): ?>

            <li class="nav-item">
              <a href="#" class="nav-link <?php echo ( (Route::getRoute() == "servicio-centros" || Route::getRoute() == "unidades" || Route::getRoute() == "mantenimiento-tipos" || Route::getRoute() == "servicio-tipos" || Route::getRoute() == "solicitud-tipos" || Route::getRoute() == "servicios" || Route::getRoute() == "actividad-semanal" || Route::getRoute() == "tareas" || Route::getRoute() == "generadores" || Route::getRoute() == "alertas" || Route::getRoute() == "traslados" || Route::getRoute() == "estimaciones" )  ? 'active"' : '' ); ?>">
                <i class="nav-icon fas fa-tools"></i>
                <p>
                  Servicios
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("servicio-centros") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('servicio-centros.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "servicio-centros") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Centros de Servicio</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("unidades") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('unidades.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "unidades") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Unidades</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("mantenimiento-tipos") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('mantenimiento-tipos.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "mantenimiento-tipos") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Tipos de Mantenimiento</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("servicio-tipos") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('servicio-tipos.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "servicio-tipos") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Tipos de Servicio</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("solicitud-tipos") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('solicitud-tipos.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "solicitud-tipos") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Tipos de Solicitud</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("servicios") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('servicios.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "servicios") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Servicios</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("actividad-semanal") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('actividad-semanal.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "actividad-semanal") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Actividad Semanal</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("tareas") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('tareas.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "tareas") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Tareas</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("generadores") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('generadores.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "generadores") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Generadores</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("estimaciones") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('estimaciones.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "estimaciones") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Estimaciones</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("alertas") ): ?>
                <li class="nav-item">
                  <a href="alertas" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Alertas de Programacion</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("traslados") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('traslados.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "traslados") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Traslados</p>
                  </a>
                </li>
                <?php endif ?>
              </ul>
            </li>

          <?php endif ?>

          <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("info-tecnica-tags") || $usuarioAutenticado->checkPermiso("informacion-tecnica")  ): ?>

            <li class="nav-item">
              <a href="#" class="nav-link <?php echo ( (Route::getRoute() == "informacion-tecnica-tags" || Route::getRoute() == "informacion-tecnica" ) ? 'active"' : '' ); ?>">
                <i class="nav-icon fas fa-info"></i>
                <p>
                  Información Técnica
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("info-tecnica-tags") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('informacion-tecnica-tags.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "informacion-tecnica-tags") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Tags</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("informacion-tecnica") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('informacion-tecnica.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "informacion-tecnica") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Información Técnica</p>
                  </a>
                </li>
                <?php endif ?>
              </ul>
            </li>

          <?php endif ?>

          <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("combust-cargas") || $usuarioAutenticado->checkPermiso("combust-rendimiento") || $usuarioAutenticado->checkPermiso("programacion") ): ?>

            <li class="nav-item">
              <a href="#" class="nav-link <?php echo ( (Route::getRoute() == "combustible-cargas" || Route::getRoute() == "combustible-rendimiento" || Route::getRoute() == "programacion" ) ? 'active"' : '' ); ?>">
                <i class="nav-icon fas fa-flag"></i>
                <p>
                  Predictivo
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("combust-cargas") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('combustible-cargas.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "combustible-cargas") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Cargas de Combustible</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("combust-rendimiento") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('combustible-rendimiento.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "combustible-rendimiento") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Rendimiento de Combustible</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("programacion") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('programacion.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "programacion") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Programacion</p>
                  </a>
                </li>
                <?php endif ?>
              </ul>
            </li>

          <?php endif ?>

          <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("empleados") || $usuarioAutenticado->checkPermiso("proveedores") || $usuarioAutenticado->checkPermiso("servicio-estatus") || $usuarioAutenticado->checkPermiso("estatus-orden-compra") ): ?>

            <li class="nav-item">
              <a href="#" class="nav-link <?php echo ( (Route::getRoute() == "empleados" || Route::getRoute() == "proveedores" || Route::getRoute() == "servicio-estatus" || Route::getRoute() == "estatus-orden-compra" ) ? 'active"' : '' ); ?>">
                <i class="nav-icon fas fa-list-alt"></i>
                <p>
                  Catálogos
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("empleados") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('empleados.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "empleados") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Empleados</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("proveedores") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('proveedores.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "proveedores") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Proveedores</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("servicio-estatus") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('servicio-estatus.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "servicio-estatus") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Estatus</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("estatus-orden-compra") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('estatus-orden-compra.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "estatus-orden-compra") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Estatus Orden Compra</p>
                  </a>
                </li>
                <?php endif ?>
              </ul>
            </li>

          <?php endif ?>

          <!----------------------
          | PROVEEDORES
          ------------------------>

          <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("cat-proveedores") || $usuarioAutenticado->checkPermiso("cat-per-proveedor") || $usuarioAutenticado->checkPermiso("per-proveedor") || $usuarioAutenticado->checkPermiso("proveedores")  || $usuarioAutenticado->checkPermiso("soli-proveedor")  ): ?>

            <li class="nav-item">
              <a href="#" class="nav-link <?php echo ( (Route::getRoute() == "categoria-proveedores" || Route::getRoute() == "categoria-permiso-proveedor" || Route::getRoute() == "permiso-proveedor"  || Route::getRoute() == "proveedores" || Route::getRoute() == "solicitud-proveedor"  ) ? ' active"' : '' ); ?>">
                <i class="nav-icon fas fa-dolly"></i>
                <p>
                  Proveedores
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">

                <!----------------------
                | PROVEEDORES
                ------------------------>

                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("proveedores") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('proveedores.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "proveedores") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Proveedores</p>
                  </a>
                </li>
                <?php endif ?>

                <!----------------------
                | SOLICITUD PROVEEDOR
                ------------------------>

                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("soli-proveedor") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('solicitud-proveedor.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "solicitud-proveedor") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Solicitud Proveedor</p>
                  </a>
                </li>
                <?php endif ?>

                <!----------------------
                | CATEGORIAS
                ------------------------>

                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("cat-proveedores") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('categoria-proveedores.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "categoria-proveedores") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Categorias</p>
                  </a>
                </li>
                <?php endif ?>

                <!----------------------
                | REQUERIMIENTOS
                ------------------------>

                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("per-proveedor") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('permiso-proveedor.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "permiso-proveedor") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Requerimientos</p>
                  </a>
                </li>
                <?php endif ?>

                <!----------------------
                | CONFIRGURACIÓN REQUERIMIENTOS
                ------------------------>

                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("cat-per-proveedor") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('categoria-permiso-proveedor.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "categoria-permiso-proveedor") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Configuración Requerimientos</p>
                  </a>
                </li>
                <?php endif ?>

              </ul>
            </li>

          <?php endif ?>


          <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("conf-requisiciones") || $usuarioAutenticado->checkPermiso("conf-correo") || $usuarioAutenticado->checkPermiso("conf-programacion")  || $usuarioAutenticado->checkPermiso("conf-puesto-tipo")  ): ?>

            <li class="nav-item">
              <a href="#" class="nav-link <?php echo ( (Route::getRoute() == "configuracion-requisiciones" || Route::getRoute() == "configuracion-correo-electronico" || Route::getRoute() == "conf-programacion" ) ? 'active"' : '' ); ?>">
                <i class="nav-icon fas fa-cog"></i>
                <p>
                  Configuración
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("conf-requisiciones") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::routes('configuracion-requisiciones'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "configuracion-requisiciones") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Requisiciones</p>
                  </a>
                </li>
                <?php endif ?>


                  <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("conf-ordenes-compra") ): ?>
                  <li class="nav-item">
                    <a href="<?php echo Route::routes('configuracion-ordenes-compra'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "configuracion-ordenes-compra") ? 'active' : '' ); ?>">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Ordenes Compra</p>
                    </a>
                  </li>
                  <?php endif ?>
                  
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("conf-puesto-tipo") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::routes('configuracion-puesto-tipo'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "configuracion-puesto-tipo") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Puesto Tipo Mantenimiento</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("conf-correo") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::routes('configuracion-correo-electronico'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "configuracion-correo-electronico") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Correo Electrónico</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("conf-programacion") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::routes('configuracion-programacion'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "conf-programacion") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Programación</p>
                  </a>
                </li>
                <?php endif ?>
              </ul>
            </li>

          <?php endif ?>

          <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("empresas") || $usuarioAutenticado->checkPermiso("sucursales") ): ?>

            <li class="nav-item">
              <a href="#" class="nav-link <?php echo ( (Route::getRoute() == "empresas" || Route::getRoute() == "sucursales" ) ? 'active"' : '' ); ?>">
                <i class="nav-icon fas fa-building"></i>
                <p>
                  Empresas
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("empresas") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('empresas.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "empresas") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Empresas</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("sucursales") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('sucursales.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "sucursales") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Sucursales</p>
                  </a>
                </li>
                <?php endif ?>
              </ul>
            </li>

          <?php endif ?>

          <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("usuarios") || $usuarioAutenticado->checkPermiso("perfiles") || $usuarioAutenticado->checkPermiso("permisos") || $usuarioAutenticado->checkPermiso("puestos") ): ?>

            <li class="nav-item">
              <a href="#" class="nav-link <?php echo ( (Route::getRoute() == "usuarios" || Route::getRoute() == "perfiles" || Route::getRoute() == "permisos") ? ' active"' : '' ); ?>">
                <i class="nav-icon fas fa-user"></i>
                <p>
                  Usuarios
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("usuarios") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('usuarios.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "usuarios") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Usuarios</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("perfiles") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('perfiles.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "perfiles") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Perfiles</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("permisos") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('permisos.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "permisos") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Permisos</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("puestos") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('puestos.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "puestos") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Puestos</p>
                  </a>
                </li>
                <?php endif ?>
              </ul>
            </li>

          <?php endif ?>

        <?php endif ?> <!-- if ( usuarioAutenticado() ) -->

        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>