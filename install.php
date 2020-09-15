<?php

// Set variables for our request
$shop = $_GET['shop'];
$api_key = "faacea581d4a8bb9637ec0c187f6a7bf";
$scopes = "read_customers,write_customers,read_orders,write_orders,read_inventory,write_inventory, write_products,read_products,read_script_tags,write_script_tags";
$redirect_uri = "https://contafacil.ec/app/generate_token.php";

// Build install/approval URL to redirect to
$install_url = "https://" . $shop . ".myshopify.com/admin/oauth/authorize?client_id=" . $api_key . "&scope=" . $scopes . "&redirect_uri=" . urlencode($redirect_uri);

// Redirect
header("Location: " . $install_url);
die();