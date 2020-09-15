<?php

// Get our helper functions
require_once("inc/functions.php");

// Set variables for our request
$api_key = "faacea581d4a8bb9637ec0c187f6a7bf";
$shared_secret = "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";
$params = $_GET; // Retrieve all request parameters
$hmac = $_GET['hmac']; // Retrieve HMAC request parameter

$shop_url=$params['shop'];

$params = array_diff_key($params, array('hmac' => '')); // Remove hmac from params
ksort($params); // Sort params lexographically

$computed_hmac = hash_hmac('sha256', http_build_query($params), $shared_secret);

// Use hmac data to check that the response is from Shopify or not
if (hash_equals($hmac, $computed_hmac)) {

	// Set variables for our request
	$query = array(
	    "Content-type" => "application/json", 
		"client_id" => $api_key, // Your API key
		"client_secret" => $shared_secret, // Your app credentials (secret key)
		"code" => $params['code'] // Grab the access key from the URL
	);

	// Generate access token URL
	$access_token_url = "https://" . $params['shop'] . "/admin/oauth/access_token";

	// Configure curl client and execute request
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_URL, $access_token_url);
	curl_setopt($ch, CURLOPT_POST, count($query));
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($query));
	$result = curl_exec($ch);
	curl_close($ch);

	// Store the access token
	$result = json_decode($result, true);
	$access_token = $result['access_token'];

	// Show the access token (don't do this in production!)
	
	//generar json 
    $perseo_datosconexion = array(
        'perseo'=> array(
            'store_url'             => $params['shop'],
            'access_token'          => $access_token,
            'fecha_instalacion'     => NOW(),
            'perseo_conexion'       => 'WEB',
            'perseo_certificado'    => 'HTTPS',
            'perseo_ip'             => '',
            'perseo_token'          => '',
            'sincronizar_producto'  => 'NO',
            'sincronizar_ima_prod'  => 'NO',
            'sincronizar_clientes'  => 'NO',
            'sincronizar_pedidos'   => 'NO',
            'origen_datos_categoria'=> '',
            'productos_existencias' => '1',
            'tiempo_sincronizar'    => '1',
            'tarifa_venta'          => '1'
            )
        );
        
    $json = json_encode($perseo_datosconexion);
    //var_dump($json);
    file_put_contents("perseo.json", $json) or die ("no se escribio los datos");
    header('Location: https://'.$params['shop'].'/admin/apps');


} else {
	// Someone is trying to be shady!
	die('This request is NOT from Shopify!');
}