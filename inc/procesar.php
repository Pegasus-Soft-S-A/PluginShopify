<?php
    /////////////////////////////
    //insertaren json
    $perseo_datosconexion = array(
            'perseo'=> array(
                'store_url'             => $_POST['store_url'],
                'access_token'          => $_POST['access_token'],
                'fecha_instalacion'     => $_POST['fecha_instalacion'],
                'perseo_conexion'       => $_POST['Conexion_Software'],
                'perseo_certificado'    => $_POST['Certificado'],
                'perseo_ip'             => $_POST['Dominio'],
                'perseo_token'          => $_POST['token'],
                'sincronizar_producto'  => $_POST['Sincronizacion_Producto'],
                'sincronizar_ima_prod'  => $_POST['Sincronizacion_Imagenes'],
                'sincronizar_clientes'  => $_POST['Sincronizacion_Clientes'],
                'sincronizar_pedidos'   => $_POST['Sincronizacion_Pedidos'],
                'origen_datos_categoria'=> $_POST['Origen_datos'],
                'productos_existencias' => $_POST['Producto_Existencias'],
                'tiempo_sincronizar'    => $_POST['tiempo_sincronizar'],
                'tarifa_venta'          => $_POST['tarifa_venta']
                )
            );
    $json = json_encode($perseo_datosconexion);
   //var_dump($json);
    file_put_contents("perseo.json", $json) or die ("no se escribio los datos"); 
if ($_POST['Sincronizacion_Producto']==""){
    require_once("../index.php");  
    
    } else {
    require_once("../sincronizar.php");
    }

