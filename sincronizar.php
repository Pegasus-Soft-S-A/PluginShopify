<?php
require_once("inc/functions.php");
$shopifyvers = "api/2020-07";

//////////////////////////////////////
/////consultar json
$leerjson = file_get_contents("perseo.json");
$datosjson = json_decode($leerjson,true);

$token  = $datosjson['perseo']['access_token'];
$shop  =  substr($datosjson['perseo']['store_url'],0,strpos($datosjson['perseo']['store_url'],'.'));

////////////////////////////////////////////////////////
/////////////////llamar el api de clientes////////////
//////////////////////////////////////////////////////
 
 //Verificar si esta activo en parametros 
 if ($datosjson['perseo']['sincronizar_clientes']=='SI'){
    //Verificar pc o web
  if ($datosjson['perseo']['perseo_conexion']=='WEB'){
     $perseo_urlcliente ='https://www.perseo.app/api/clientes_consulta';
     }else{
      $perseo_urlcliente  =$datosjson['perseo']['perseo_certificado'].'://'.$datosjson['perseo']['perseo_ip'].'/api/clientes_consulta';
   };
   $perseo_bodycliente= '{"clientes":[{"clientes":{"clienteid":"","clientescodigo":"","identificacion":"","contenido":""}}]}';
      
    $curl_clientes = curl_init();
    $headers_clientes = array();
    $headers_clientes[] = "Usuario: Perseo";
    $headers_clientes[] = "Clave: Perseo1232*";
    $headers_clientes[] = "DBDatos: " .$datosjson['perseo']['perseo_token'];
    $headers_clientes[] = "Content-Type: application/json";

    curl_setopt($curl_clientes, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl_clientes, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl_clientes, CURLOPT_HTTPHEADER, $headers_clientes);
    curl_setopt($curl_clientes, CURLOPT_HEADER, 0);
    curl_setopt($curl_clientes, CURLOPT_URL, $perseo_urlcliente);
    curl_setopt($curl_clientes, CURLOPT_FAILONERROR, 1);
    curl_setopt($curl_clientes, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($curl_clientes, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl_clientes, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($curl_clientes, CURLOPT_POSTFIELDS, $perseo_bodycliente);
    curl_setopt($curl_clientes, CURLOPT_TIMEOUT, 1800);
    
    $resultcliente = curl_exec($curl_clientes);
    //var_dump($resultcliente);   
    
     if (empty(curl_error($curl_clientes))) {
            //Si resultado de la API tiene informacion
                if (!empty($resultcliente)) {
                    $perseo_datos_clientes = json_decode($resultcliente, true);
                    
                    foreach($perseo_datos_clientes['clientes'] as $cliente){
                        ///no ingresar consumidor final
                        if ($cliente['clientescodigo']<>'C000000001'  ){
                            
                        /////////////////////////////////////////////////////////
                        //ingreso del cliente
                        $primermail = preg_split("/[\s,]+/", $cliente['email']);
                        $perseo_nombreCliente = explode(" ", $cliente['razonsocial']);
                             $script_cliente = array(
                                'customer' => array(
                                    'first_name'        => $perseo_nombreCliente[0],
                                    'last_name'         => $perseo_nombreCliente[1],
                                    'email'             => $primermail[0],
                                    'note'              => $cliente['clientesid'].'-'.$cliente['clientescodigo'],
                                    'created_at'        =>  $cliente['fechamodificacion'],
                                    'verified_email'    => true,
                                    'send_email_invite' => true
                                    )
                                );
                            //var_dump($script_cliente);
                            //echo "<br>";
                            //echo "<br>";
                             
                            $scriptclientes = shopify_call($token, $shop, "/admin/{$shopifyvers}/customers.json", $script_cliente , 'POST');
                            $scriptclientes = json_decode($scriptclientes['response'], TRUE);
                           // var_dump($scriptclientes);
                           // echo "<br>";
                           // echo "<br>";
                        }
                    }
                }
         
     };
};

////////////////////////////////////////////////////////
/////////////////llamar el api de categorias////////////
//////////////////////////////////////////////////////

//Verificar si esta activo en parametros 
 if ($datosjson['perseo']['sincronizar_producto']=='SI'){
 //Verificar pc o web
  if ($datosjson['perseo']['perseo_conexion']=='WEB'){
        $perseo_urlorigen ='https://www.perseo.app'.$datosjson['perseo']['origen_datos_categoria'];
     }else{
      $perseo_urlorigen  =$datosjson['perseo']['perseo_certificado'].'://'.$datosjson['perseo']['perseo_ip'].''.$datosjson['perseo']['origen_datos_categoria'];
   };
      
    $curl_origen = curl_init();
    $headers_origen = array();
    $headers_origen[] = "Usuario: Perseo";
    $headers_origen[] = "Clave: Perseo1232*";
    $headers_origen[] = "DBDatos: " .$datosjson['perseo']['perseo_token'];
    $headers_origen[] = "Content-Type: application/json";

    curl_setopt($curl_origen, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl_origen, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl_origen, CURLOPT_HTTPHEADER, $headers_origen);
    curl_setopt($curl_origen, CURLOPT_HEADER, 0);
    curl_setopt($curl_origen, CURLOPT_URL, $perseo_urlorigen);
    curl_setopt($curl_origen, CURLOPT_FAILONERROR, 1);
    curl_setopt($curl_origen, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($curl_origen, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl_origen, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($curl_origen, CURLOPT_POSTFIELDS, '');
    curl_setopt($curl_origen, CURLOPT_TIMEOUT, 1800);
    
    $resultorigen = curl_exec($curl_origen);
    
     if (empty(curl_error($curl_origen))) {
            //Si resultado de la API tiene informacion
                if (!empty($resultorigen)) {
                    $perseo_datos_origen = json_decode($resultorigen, true);
                    if ($row_datos['origen_datos_categoria']=='/api/productos_lineas_consulta'){
                        $arrayorigen=$perseo_datos_origen['lineas'];
                    }; 
                    if ($row_datos['origen_datos_categoria']=='/api/productos_categorias_consulta'){
                        $arrayorigen=$perseo_datos_origen['categorias'];
                    };
                    
                    
                    foreach($arrayorigen as $origen){
                        
                         if ($row_datos['origen_datos_categoria']=='/api/productos_lineas_consulta'){
                            $origenid=  $origen['productos_lineasid'] ;
                         }; 
                          if ($row_datos['origen_datos_categoria']=='/api/productos_categorias_consulta'){
                            $origenid=  $origen['productos_categoriasid'] ;
                         };
                        $script_origen = array(
                                'custom_collection' => array(
                                    'title'         =>  $origen['descripcion'],
                                    'handle'        =>  'perseo-'.$origenid,
                                    'body_html'     => $origen['descripcion']
                                    )
                                );
                             
                            $scriptorigen = shopify_call($token, $shop, "/admin/{$shopifyvers}/custom_collections.json", $script_origen , 'POST');
                            $scriptorigen = json_decode($scriptorigen['response'], TRUE);
                     
                    }
                }
     }
};


////////////////////////////////////////////////////////
/////////////////llamar el api de productos////////////
//////////////////////////////////////////////////////

//Verificar si esta activo en parametros 
 if ($datosjson['perseo']['sincronizar_producto']=='SI'){
     
            //Verificar pc o web
            if ($datosjson['perseo']['perseo_conexion']=='WEB'){
                $perseo_urlproducto ='https://www.perseo.app/api/productos_consulta';
                }else{
                $perseo_urlproducto  =$datosjson['perseo']['perseo_certificado'].'://'.$datosjson['perseo']['perseo_ip'].'/api/productos_consulta';
            };
            
            $perseo_bodyproducto= '{"registros":[ {"productos":{"productosid":null,"codigoproductos":null,"barras":null,"contenido":null}}]}';
            
            $curl = curl_init();
            $headers = array();
            $headers[] = "Usuario: Perseo";
            $headers[] = "Clave: Perseo1232*";
            $headers[] = "DBDatos: " .$datosjson['perseo']['perseo_token'];
            $headers[] = "Content-Type: application/json";

            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_URL, $perseo_urlproducto);
            curl_setopt($curl, CURLOPT_FAILONERROR, 1);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $perseo_bodyproducto);
            curl_setopt($curl, CURLOPT_TIMEOUT, 1800);
    
            $result = curl_exec($curl);
            //var_dump($result);
            if (empty(curl_error($curl))) {
            //Si resultado de la API tiene informacion
                if (!empty($result)) {
                    $perseo_datos_productos = json_decode($result, true);
                    //var_dump($perseo_datos_productos);
                    
                    //unset($listadoproducto);
                    foreach($perseo_datos_productos['productos'] as $producto){
                        
                        ////////////////////////////////////////////////////
                        //SI es producto esta activo                
                        if ($producto['venta']==" " && $producto['estado']==1 && $producto['existenciastotales']>=$row_datos['productos_existencias'] )
                        {
                            $shopifyproductiid=0;
                            $busqueda = $producto['productosid']."-".$producto['productocodigo'];
                            
                            ///////////////////////////////////////////////////////////////////////////
                            //Verificar si los productos ya estan ingresados  , consultamos productos
                            $listadoproducto = shopify_call($token, $shop, "/admin/".$shopifyvers."/products.json", array(), 'GET');
                            $listadoproducto = json_decode($listadoproducto['response'], TRUE);
                            
                            foreach($listadoproducto['products'] as $shopifyproducto ){
                                foreach($shopifyproducto['variants'] as $shopifysku){
                                    if($shopifysku['sku'] == $busqueda){
                                        $shopifyproductiid=$shopifysku['product_id'];
                                        //print_r($shopifysku);
                                        //echo $shopifyproductiid;
                                        //echo "<br>";
                                    }
                                
                                }
                               
                            }
                            
                            if($shopifyproductiid==""){
                                $productoperseo=$listadoproducto['products'];
                                //echo "Nuevo producto";
                                //echo "<br>";
                                       
                                            /////////////////////////////////////////////////////////
                                            //ingreso del producto
                                             $script_producto = array(
                                                'product' => array(
                                                    'title'         => $producto['descripcion'],
                                                    'body_html'     => '<strong>'.$producto['fichatecnica'].'</strong>',
                                                    'vendor'        => 'perseo-'.$producto['productosid'].'-'.$producto['productocodigo'],
                                                    'product_type'  => '',
                                                    'created_at'    => $producto['fechamodificacion'],
                                                    'handle'        => $producto['descripcion'],
                                                    'published_scope' => 'web',
                                                     'variant' => array(
                                                         'inventory_management' => 'shopify'
                                                         )
                                                    )
                                                );
                                            $scriptTag = shopify_call($token, $shop, "/admin/{$shopifyvers}/products.json", $script_producto , 'POST');
                                            $scriptTag = json_decode($scriptTag['response'], TRUE);
                		                    
                		                    //////////////////////////////////////
                                            //verificacion del precio
                                            $perseo_iva= ($producto['porcentajeiva']/100)+1;
                                             foreach($producto['tarifas'] as $tarifa)
                                              { 
                                                if ($tarifa['tarifasid']==$row_datos['tarifa_venta']){
                                            	    $precio =number_format(round($tarifa['precio'],2)* $perseo_iva,2,'.','');
                                            	} 
                                              }
                                              
                                            ///////////////////////////////////////
                                            //id de location consultar
                                            $location_id = shopify_call($token, $shop, "/admin/" .$shopifyvers."/locations.json", array(), 'GET');
                		                    $location_id = json_decode($location_id['response'], TRUE);
                		                    $arraylocation_id=$location_id['locations'][0]['id'];
                		                    
                		                    //crea el inventory_item_id aunq con error
                                            $scriptstock = shopify_call($token, $shop, "/admin/{$shopifyvers}/inventory_levels/adjust.json", array() , 'POST');
                                            $scriptstock = json_decode($scriptstock['response'], TRUE);
                		                 
                                            /// obtenemos inventory_item_id
                                            $inventory_item_id = shopify_call($token, $shop, "/admin/" .$shopifyvers."/inventory_levels.json?location_ids={$arraylocation_id}", array(), 'GET');
                    		                $inventory_item_id = json_decode($inventory_item_id['response'], TRUE);
                    		                $arrayinventory_item_id=$inventory_item_id;
                    		                
                    		                 //////////obtener el final
                    		                 $finalinventory_item_id=end($arrayinventory_item_id['inventory_levels']);
                		                  
                                            //////////////////////////////////////
                                            //Actualizar stock
                                            //insertamos nuevo stock 
                                                    $stock_update=array(
                                                        'location_id'               => $arraylocation_id,
                                                        'inventory_item_id'         => $finalinventory_item_id['inventory_item_id'],
                                                        'available_adjustment'      => $producto['existenciastotales']
                                                        );
                                            
                                            $stock_update = shopify_call($token, $shop, "/admin/{$shopifyvers}/inventory_levels/adjust.json", $stock_update , 'POST');
                                            $stock_update = json_decode($stock_update['response'], TRUE);
                                            
                                            ////////////////////////////////////////
                                            //Enlazamos a origen  categoria
                                            
                                                //verificamos tipo
                                                if ($row_datos['origen_datos_categoria']=='/api/productos_lineas_consulta'){
                                                   $codigoorigendatos ='perseo-'.$producto['productos_lineasid'];
                                                };
                                                if ($row_datos['origen_datos_categoria']=='/api/productos_categorias_consulta'){
                                                   $codigoorigendatos ='perseo-'.$producto['productos_categoriasid'];
                                                };
                                                if ($row_datos['origen_datos_categoria']=='/api/productos_subcategorias_consulta'){
                                                   $codigoorigendatos ='perseo-'.$producto['productos_subcategoriasid'];
                                                };
                                                if ($row_datos['origen_datos_categoria']=='/api/productos_subgrupos_consulta'){
                                                   $codigoorigendatos ='perseo-'.$producto['productos_subgruposid'];
                                                };
                                                
                                                //Consultamos el custom collection
                                                $consulta_idccollection = shopify_call($token, $shop, "/admin/{$shopifyvers}/custom_collections.json", $arrayidcollection , 'GET');
                                                $consulta_idccollection = json_decode($consulta_idccollection['response'], TRUE);
                                                
                                                //comparamos con el id de categoria 
                                                 foreach($consulta_idccollection['custom_collections'] as $custom_collections)
                                                  { 
                                                    if ($custom_collections['handle']==$codigoorigendatos){
                                                        $idccoll = $custom_collections['id'];
                                                	} 
                                                  }
                                                  
                                                
                                                //////////////////////////////////////////
                                                /////////////inserta colleccion///////////
                                                //insercion del producto y la categoria
                                                    $arrayidcollection = array(
                                                        'collect'=> array(
                                                            'collection_id'  => $idccoll,
                                                            'product_id'     => $scriptTag['product']['variants'][0]['product_id']
                                                            )
                                                        );
                                                        
                                                $add_categoria = shopify_call($token, $shop, "/admin/{$shopifyvers}/collects.json", $arrayidcollection , 'POST');
                                                $add_categoria = json_decode($add_categoria['response'], TRUE);
                                     
                                            ////////////////////////////////////////
                                            ///Update de lo demas del producto  
                                            $script_actproducto = array(
                                                             'variant' => array(
                                                                   'id'            => $scriptTag['product']['variants'][0]['id'],
                                                                   'product_id'     =>$scriptTag['product']['id'],
                                                                   'title'          => $producto['descripcion'],
                                                                   'price'          => $precio,
                                                                   'sku'            => $producto['productosid'].'-'.$producto['productocodigo'],
                                                                   'inventory_policy'=>'continue',
                                                                   'compare_at_price' => null,
                                                                   'fulfillment_service' => 'manual',
                                                                   'option1'        =>  $producto['descripcion'],
                                                                   'option2'        => null,
                                                                   'option3'        => null,
                                                                   'taxable'        => true,
                                                                   'barcode'        => $producto['barras'],
                                                                   'requires_shipping'      => true
                                                    )
                                                );
                                          
                                            $scriptUpdate = shopify_call($token, $shop, "/admin/{$shopifyvers}/variants/{$scriptTag['product']['variants'][0]['id']}.json", $script_actproducto, 'PUT');
                                            $scriptUpdate = json_decode($scriptUpdate['response'], TRUE);
                                            
                                            
                                            ////////////////////////////////////////
                                            //ingresasmos la imagen
                                            $Remplazamos=preg_replace('([^A-Za-z0-9])', '', $producto['descripcion']);
                                            $perseo_nombreimagen = $producto['productosid'].''.substr($Remplazamos,0,15);
                                            $perseo_num=2;
                                            
                                             foreach($producto['imagenes'] as $imag){
                                             
                                                $baseFromJavascript = "data:image/jpeg;base64,{$imag["imagen"]}";
                                                $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $baseFromJavascript));
                                                /////////////////////////////
                                                //verificar imagen inicial
                                                //echo '<br>';
                                                //echo $imag['primera'];
                                                //echo '<br>';
                                                if ($imag['ecommerce']==1){
                                                    ////////////////////////////
                                                    //saber si es la principal
                                                    if ($imag['primera']==1){
                                                        $imagenproducto =array(
                                                        'image'         =>array(
                                                                        'product_id'    => $scriptTag['product']['id'],
                                                                        'position'      => 1,
                                                                        'attachment'    => $imag["imagen"],
                                                                        'filename'      => $perseo_num.''.$perseo_nombreimagen.'.png'
                                                                        ) 
                                                    );
                                                    }else {
                                                        $imagenproducto =array(
                                                        'image'         =>array(
                                                                        'product_id'    => $scriptTag['product']['id'],
                                                                        'position'      => $perseo_num,
                                                                        'attachment'    => $imag["imagen"],
                                                                        'filename'      => $perseo_num.''.$perseo_nombreimagen.'.png'
                                                                        ) 
                                                    );
                                                    };
                                                    
                                                  $scriptimag = shopify_call($token, $shop, "/admin/" .$shopifyvers."/products/".$scriptTag['product']['id']."/images.json", $imagenproducto , 'POST');
                                                  $scriptimag = json_decode($scriptimag['response'], TRUE);
                                                   
                                                };
                                               
                                                $perseo_num++; 
                                             }
                                        
                                
                            } else {
                               //Consultamos producto
                                $updproducto = shopify_call($token, $shop, "/admin/".$shopifyvers."/products/{$shopifyproductiid}.json", array(), 'GET');
                                $updproducto = json_decode($updproducto['response'], TRUE);
                                
                                ///////////////////////////////////////////////////
                                //comparamos la fecha para hacer update al producto 
                                //print_r($updproducto['product']['updated_at']);
                                echo "<br>";     
                                $fechashopify   = date_format(date_create($updproducto['product']['updated_at']),'Y-m-d H:i:s');
                                $fechaproducto  = date_format(date_create($producto['fechamodificacion']),'Y-m-d H:i:s');
                                //echo $fechaproducto .' > '.$fechashopify;
                                //echo "<br>";
                                        if($fechaproducto >= $fechashopify ){
                                            
                                            //echo "Actualizar producto";
                                            //echo "<br>";
                                            
                                            ///////////////////////////////////////
                                            //verificar si tiene imagnes y eliminar
                                            $consultaproductoimg = shopify_call($token, $shop, "/admin/{$shopifyvers}/products/{$updproducto['product']['id']}/images.json", array() , 'GET');
                                            $consultaproductoimg = json_decode($consultaproductoimg['response'], TRUE);
                                            
                                            foreach($consultaproductoimg['images'] as $imagen){
                                                
                                                $deleteproductoimg = shopify_call($token, $shop, "/admin/{$shopifyvers}/products/{$updproducto['product']['id']}/images/{$imagen['id']}.json", array() , 'DELETE');
                                                $deleteproductoimg = json_decode($deleteproductoimg['response'], TRUE);
                                                ;
                                            }
                                            
                                            //////////////////////////////////////
                                            //actualizacion del precio
                                            $perseo_iva= ($producto['porcentajeiva']/100)+1;
                                             foreach($producto['tarifas'] as $tarifa)
                                              { 
                                                if ($tarifa['tarifasid']==$row_datos['tarifa_venta']){
                                            	    $precio =number_format(round($tarifa['precio'],2)* $perseo_iva,2,'.','');
                                            	} 
                                              }
                                              
                                            /////////////////////////////////////////////////////////
                                            //actualizacion del producto
                                             $UPD_producto = array(
                                                'product' => array(
                                                    'id'            => $shopifyproductiid,
                                                    'title'         => $producto['descripcion'],
                                                    'body_html'     => '<strong>'.$producto['fichatecnica'].'</strong>',
                                                    'vendor'        => 'perseo-'.$producto['productosid'].'-'.$producto['productocodigo'],
                                                    'handle'        => $producto['descripcion'],
                                                    'variant' => array(
                                                                   'title'          => $producto['descripcion'],
                                                                   'price'          => $precio,
                                                                   'sku'            => $producto['productosid'].'-'.$producto['productocodigo'],
                                                                   'inventory_policy'=>'continue',
                                                                   'compare_at_price' => null,
                                                                   'fulfillment_service' => 'manual',
                                                                   'option1'        =>  $producto['descripcion'],
                                                                   'option2'        => null,
                                                                   'option3'        => null,
                                                                   'taxable'        => true,
                                                                   'barcode'        => $producto['barras'],
                                                                   'requires_shipping'      => true
                                                                    )
                                                                )
                                                );
                                            
                                            $updateproducto = shopify_call($token, $shop, "/admin/{$shopifyvers}/products/{$updproducto['product']['id']}.json", $UPD_producto, 'PUT');
                                            $updateproducto = json_decode($updateproducto['response'], TRUE);
                                            
                                            //var_dump($updateproducto);
                                            //echo "<br>";
                                            
                                            
                                            ////////////////////////////////////////
                                            //ingresamos la imagen
                                            $Remplazamos=preg_replace('([^A-Za-z0-9])', '', $producto['descripcion']);
                                            $perseo_nombreimagen = $producto['productosid'].''.substr($Remplazamos,0,15);
                                            $perseo_num=2;
                                            
                                             foreach($producto['imagenes'] as $imag){
                                             
                                                $baseFromJavascript = "data:image/jpeg;base64,{$imag["imagen"]}";
                                                $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $baseFromJavascript));
                                                /////////////////////////////
                                                //verificar imagen inicial
                                                if ($imag['ecommerce']==1){
                                                    ////////////////////////////
                                                    //saber si es la principal
                                                    if ($imag['primera']==1){
                                                        $imagenproducto =array(
                                                        'image'         =>array(
                                                                        'product_id'    => $updproducto['product']['id'],
                                                                        'position'      => 1,
                                                                        'attachment'    => $imag["imagen"],
                                                                        'filename'      => $perseo_num.''.$perseo_nombreimagen.'.png'
                                                                        ) 
                                                    );
                                                    }else {
                                                        $imagenproducto =array(
                                                        'image'         =>array(
                                                                        'product_id'    => $updproducto['product']['id'],
                                                                        'position'      => $perseo_num,
                                                                        'attachment'    => $imag["imagen"],
                                                                        'filename'      => $perseo_num.''.$perseo_nombreimagen.'.png'
                                                                        ) 
                                                    );
                                                    };
                                                    
                                                  $scriptimag = shopify_call($token, $shop, "/admin/" .$shopifyvers."/products/".$updproducto['product']['id']."/images.json", $imagenproducto , 'POST');
                                                  $scriptimag = json_decode($scriptimag['response'], TRUE);
                                                   
                                                };
                                               
                                                $perseo_num++; 
                                             }
                                            
                                            
                                           
                                        }
                               
                               
                           }
                                                   
                        } 
                    }     
                }
            }
    
 }; 
            
//////////////////////////////////////////////////////
///////////Actualizar stock///////////////////////////
/////////////////////////////////////////////////////

//Verificar si esta activo en parametros 
if ($datosjson['perseo']['sincronizar_producto']=='SI'){
    ///////////////////////////////////////
    //id de location consultar
    $location_id = shopify_call($token, $shop, "/admin/" .$shopifyvers."/locations.json", array(), 'GET');
    $location_id = json_decode($location_id['response'], TRUE);
    $stocklocation_id=intval($location_id['locations'][0]['id']);
    
    ///////////////////////////////////////////////////////////////////////////
    //Verificar si los productos ya estan ingresados  , consultamos productos
    $listadoproducto = shopify_call($token, $shop, "/admin/".$shopifyvers."/products.json", array(), 'GET');
    $listadoproducto = json_decode($listadoproducto['response'], TRUE);
    
    foreach($listadoproducto['products'] as $shopifyproducto ){
        foreach($shopifyproducto['variants'] as $shopifysku){
          
            ///saber id para eliminar y volver a insertar el stock
            $stockidprodud      = $shopifysku['product_id'];
            $stockinventoryitem = $shopifysku['inventory_item_id'];
            $stockanterior      = $shopifysku['inventory_quantity'];
            
            //vacia el stock a 0
            $stock_delete=array(
                'location_id'               => $stocklocation_id,
                'inventory_item_id'         => $stockinventoryitem,
                'available_adjustment'      => -$stockanterior
            );
                    
            $stockdelete = shopify_call($token, $shop, "/admin/{$shopifyvers}/inventory_levels/adjust.json", $stock_delete , 'POST');
            $stockdelete = json_decode($stockdelete['response'], TRUE);
            
            //codigo producto 
            $idpro=explode("-",$shopifysku['sku']);
           
            
            //////////////////////////////
            ////Verificar pc o web
            if ($datosjson['perseo']['perseo_conexion']=='WEB'){
                $perseo_urlstock ='https://www.perseo.app/api/existencia_producto';
                }else{
                $perseo_urlstock  =$datosjson['perseo']['perseo_certificado'].'://'.$datosjson['perseo']['perseo_ip'].'/api/existencia_producto';
            };
            
            $perseo_bodystock= '{ "productos":[ { "productosid":'.$idpro[0].' } ] }';
            
            $curl = curl_init();
            $headers = array();
            $headers[] = "Usuario: Perseo";
            $headers[] = "Clave: Perseo1232*";
            $headers[] = "DBDatos: " .$datosjson['perseo']['perseo_token'];
            $headers[] = "Content-Type: application/json";

            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_URL, $perseo_urlstock);
            curl_setopt($curl, CURLOPT_FAILONERROR, 1);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $perseo_bodystock);
            curl_setopt($curl, CURLOPT_TIMEOUT, 1800);
            $result_stock = curl_exec($curl);
            //var_dump($result_stock);
            if (empty(curl_error($curl))) {
            //Si resultado de la API tiene informacion
                if (!empty($result_stock)) {
                    
                    $perseo_datos_productos = json_decode($result_stock, true);
                    $perseo_totalstock= 0;
                    foreach($perseo_datos_productos['existencias'] as $stock)
                        {                              
                            $perseo_totalstock+=$stock['existencias'];
                        }
                    ///////////////////////////
                    //Actualizar stock producto
                    //insertamos nuevo stock 
                    $stock_update=array(
                        'location_id'               => $stocklocation_id,
                        'inventory_item_id'         => $stockinventoryitem,
                        'available_adjustment'      => $perseo_totalstock
                        );
                    
                    $stock_update = shopify_call($token, $shop, "/admin/{$shopifyvers}/inventory_levels/adjust.json", $stock_update , 'POST');
                    $stock_update = json_decode($stock_update['response'], TRUE);
                }
            }
       
                                
        }
                               
    }
    
    
         
}




