<?php
$requests = $_GET;
$hmac = $_GET['hmac'];
$serializeArray = serialize($requests);
$requests = array_diff_key($requests, array('hmac' => '' ));
ksort($requests);

//////////////////////////////////////
//consultar json
$leerjson = file_get_contents('inc/perseo.json');
$datosjson = json_decode($leerjson,true);
//var_dump($datosjson);
?>
<!DOCTYPE html>
<html lang="es-EC" >
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <style>
    * {
      box-sizing:border-box;
    }
    
    .left {
      
      padding:20px;
      float:left;
      width:20%; /* The width is 20%, by default */
    }
    
    .main {
      background-color:#f1f1f1;
      padding:20px;
      float:left;
      width:60%; /* The width is 60%, by default */
    }
    
    .right {
      
      padding:20px;
      float:left;
      width:20%; /* The width is 20%, by default */
    }
    
    /* Use a media query to add a break point at 800px: */
    @media screen and (max-width:800px) {
      .left, .main, .right {
        width:100%; /* The width is 100%, when the viewport is 800px or smaller */
      }
    }
    </style>
</head>
<body>
    
<div class="left">
  <p></p>
</div>
      <h2>Perseo Software</h2>

<!--/////////////////////////
    //configuracion/////////-->  
<div class="main">
  <form action ="inc/procesar.php" method="POST">
      
    <input id="store_url" class="form-control" name="store_url" type="hidden" value="<?php  if (!empty($datosjson['perseo']['store_url'])){ echo $datosjson['perseo']['store_url']; } ?>">
	<input id="access_token" name="access_token" type="hidden" value="<?php if (!empty($datosjson['perseo']['access_token'])){ echo $datosjson['perseo']['access_token']; }?>">
	<input id="fecha_instalacion" name="fecha_instalacion" type="hidden" value="<?php echo date('Y-m-d H:i:s'); ?>">
	
    <div class="form-group">
    <label for="sel1">Conexion Software:</label>
    <select name="Conexion_Software" class="form-control" id="sel1" >
        <option <?php if (!empty($datosjson['perseo']['perseo_conexion'])){ if ($datosjson['perseo']['perseo_conexion']==="PC"){echo "selected";} } ?> value="PC">Perseo PC</option>
        <option <?php if (!empty($datosjson['perseo']['perseo_conexion'])){ if ($datosjson['perseo']['perseo_conexion']==="WEB"){echo "selected";} } ?> value="WEB">Perseo WEB</option>
    </select>
    </div>

    <div class="form-group">
    <label for="sel1">Certificado:</label>
    <select name="Certificado" class="form-control" id="sel1" >
        <option <?php if (!empty($datosjson['perseo']['perseo_certificado'])){ if ($datosjson['perseo']['perseo_certificado']==="HTTP"){echo "selected";} } ?> value="HTTP">HTTP</option>
        <option <?php if (!empty($datosjson['perseo']['perseo_certificado'])){ if ($datosjson['perseo']['perseo_certificado']==="HTTPS"){echo "selected";} } ?> value="HTTPS">HTTPS</option>
    </select>
    </div>

    <div class="form-group">
      <label for="usr">IP/Dominio:</label>
      <input type="text" name= "Dominio" class="form-control" id="Dominio" value="<?php if (!empty($datosjson['perseo']['perseo_ip'])){ echo $datosjson['perseo']['perseo_ip']; } ?>" >
    </div>

    <div class="form-group">
      <label for="usr">Token:</label>
      <input type="text" name= "token" class="form-control" id="token" value="<?php if (!empty($datosjson['perseo']['perseo_token'])){ echo $datosjson['perseo']['perseo_token']; } ?>" >
    </div>

<!--/////////////////////////
    //parametros/////////-->  

<div  style="<?php if (empty($datosjson['perseo']['perseo_token'])){ echo "visibility:hidden;"; } ?> ">
    <br>
    <p>
        Seccion parametrizacion sincronizacion con Perseo Software
    </p>
    
    
        <div class="form-group">
        <label for="sel1">Sincronizacion Producto:</label>
        <select name="Sincronizacion_Producto" class="form-control" id="Sincronizacion_Producto" >
            <option  <?php if (!empty($datosjson['perseo']['Sincronizacion_Producto'])){ if ($datosjson['perseo']['Sincronizacion_Producto']==="SI"){echo "selected";} } ?>  value="SI">SI</option>
            <option  <?php if (!empty($datosjson['perseo']['Sincronizacion_Producto'])){ if ($datosjson['perseo']['Sincronizacion_Producto']==="NO"){echo "selected";} } ?> value="NO">NO</option>
        </select>
        </div>
        
        <div class="form-group">
        <label for="sel1">Sincronizacion Imagenes Productos:</label>
        <select name="Sincronizacion_Imagenes" class="form-control" id="Sincronizacion_Imagenes" >
            <option <?php if (!empty($datosjson['perseo']['sincronizar_ima_prod'])){ if ($datosjson['perseo']['sincronizar_ima_prod']==="SI"){echo "selected";} } ?> value="SI">SI</option>
            <option <?php if (!empty($datosjson['perseo']['sincronizar_ima_prod'])){ if ($datosjson['perseo']['sincronizar_ima_prod']==="NO"){echo "selected";} } ?> value="NO">NO</option>
        </select>
        </div>
        
        <div class="form-group">
        <label for="sel1">Sincronizacion Clientes:</label>
        <select name="Sincronizacion_Clientes" class="form-control" id="Sincronizacion_Clientes" >
            <option  <?php if (!empty($datosjson['perseo']['sincronizar_clientes'])){ if ($datosjson['perseo']['sincronizar_clientes']==="SI"){echo "selected";} } ?> value="SI">SI</option>
            <option <?php if (!empty($datosjson['perseo']['sincronizar_clientes'])){ if ($datosjson['perseo']['sincronizar_clientes']==="NO"){echo "selected";} } ?> value="NO">NO</option>
        </select>
        </div>
        
        <div class="form-group">
        <label for="sel1">Sincronizacion Pedidos:</label>
        <select name="Sincronizacion_Pedidos" class="form-control" id="Sincronizacion_Pedidos" >
            <option  <?php if (!empty($datosjson['perseo']['sincronizar_pedidos'])){ if ($datosjson['perseo']['sincronizar_pedidos']==="SI"){echo "selected";} } ?> value="SI">SI</option>
            <option <?php if (!empty($datosjson['perseo']['sincronizar_pedidos'])){ if ($datosjson['perseo']['sincronizar_pedidos']==="NO"){echo "selected";} } ?> value="NO">NO</option>
        </select>
        </div>
        
        <div class="form-group">
        <label for="sel1">Origen de datos categoria:</label>
        <select name="Origen_datos" class="form-control" id="Origen_datos" >
            <option <?php if (!empty($datosjson['perseo']['origen_datos_categoria'])){ if ($datosjson['perseo']['origen_datos_categoria']==="/api/productos_lineas_consulta"){echo "selected";} } ?> value="/api/productos_lineas_consulta">LINEAS</option>
            <option <?php if (!empty($datosjson['perseo']['origen_datos_categoria'])){ if ($datosjson['perseo']['origen_datos_categoria']==="/api/productos_categorias_consulta"){echo "selected";} } ?> value="/api/productos_categorias_consulta">CATEGORIAS</option>
            <option <?php if (!empty($datosjson['perseo']['origen_datos_categoria'])){ if ($datosjson['perseo']['origen_datos_categoria']==="/api/productos_subcategorias_consulta"){echo "selected";} } ?> value="/api/productos_subcategorias_consulta">SUBCATEGORIAS</option>
            <option <?php if (!empty($datosjson['perseo']['origen_datos_categoria'])){ if ($datosjson['perseo']['origen_datos_categoria']==="/api/productos_subgrupos_consulta"){echo "selected";} } ?> value="/api/productos_subgrupos_consulta">SUBGRUPOS</option>
        </select>
        </div>
        
        <div class="form-group">
        <label for="sel1">Producto Existencias:</label>
        <select name="Producto_Existencias" class="form-control" id="Producto_Existencias" >
            <option <?php if (!empty($datosjson['perseo']['productos_existencias'])){ if ($datosjson['perseo']['productos_existencias']==="1"){echo "selected";} } ?> value="1">CON EXISTENCIAS</option>
            <option <?php if (!empty($datosjson['perseo']['productos_existencias'])){ if ($datosjson['perseo']['productos_existencias']==="0"){echo "selected";} } ?> value="0">TODOS</option>
        </select>
        </div>
        
        <div class="form-group">
      <label for="usr">Sincronizacion:</label>
      <input type="text" name= "tiempo_sincronizar" class="form-control" id="tiempo_sincronizar" value="<?php if (!empty($datosjson['perseo']['tiempo_sincronizar'])){ echo $datosjson['perseo']['tiempo_sincronizar']; } ?>" >
    </div>
        
         <div class="form-group">
      <label for="usr">tarifa venta:</label>
      <input type="text" name= "tarifa_venta" class="form-control" id="tarifa_venta" value="<?php if (!empty($datosjson['perseo']['tarifa_venta'])){ echo $datosjson['perseo']['tarifa_venta']; } ?>" >
    </div>
   
</div>

    <button type="submit" class="btn btn-default" name="submit" id="submit" >Guardar / Actualizar </button>

  </form>
</div>

<div class="right">
  <p></p>
</div>

</body>
</html>

