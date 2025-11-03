<?php

namespace App\Controllers;

require_once "app/Controllers/Autorizacion.php";
require_once "app/Models/Proveedor.php";
require_once "app/Models/Cotizacion.php";

require_once "app/Models/OrdenCompra.php";
require_once "app/Models/RequisicionArchivo.php";
require_once "app/Models/CategoriaPermiso.php";


use App\Route;
use App\Conexion;
use App\Models\Proveedor;
use App\Models\Cotizacion;
use App\Models\OrdenCompra;
use App\Models\RequisicionArchivo;
use App\Models\CategoriaPermiso;


class HomeProveedorController
{
    public function index()
    {
        if ( !usuarioAutenticadoProveedor() ) {
            include "vistas/modulos/plantilla_proveedores.php"; // plantilla.php redireccionará a la página de ingreso
            return;
        }

        // Validar Autorizacion
        $proveedor = New Proveedor;

        $proveedor->consultar("id", usuarioAutenticadoProveedor()["id"]);

        if ( !$proveedor->consultar("id", usuarioAutenticadoProveedor()["id"]) ) {
            include "vistas/modulos/plantilla_proveedores.php"; // plantilla.php redireccionará a la página de ingreso
            return;
        }

        $ordenCompra = New OrdenCompra;
        $ordenCompra->id = usuarioAutenticadoProveedor()["id"];
        $ordenCompras = $ordenCompra->consultarOrdenCompraProveedor();

        $requisionArchivo = New RequisicionArchivo;
        $requisionArchivo->proveedorId = $proveedor->id;
        $ordenesFacturaFaltante = $requisionArchivo->obtenerOrdenesCompraFaltanFactura();
        $ordenesComprobanteFaltante = $requisionArchivo->obtenerOrdenesCompraFaltanComprobantes();

        $cotizacion = New Cotizacion;
        $cotizaciones = $cotizacion->consultarPorProveedor(usuarioAutenticadoProveedor()["id"]);

        $vendedores = $proveedor->obtenerVendedores();

        $cantidadOrdenes = count($ordenCompras);
        $cantidadVendedores = count($vendedores);
        $cantidadPropuestas = count($cotizaciones);

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/dashboard_proveedor.php');
        include "vistas/modulos/plantilla_proveedores.php";
    }
}
