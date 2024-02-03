<?php

$privateKeyResource = openssl_pkey_get_private('cert/saml.crt');

if ($privateKeyResource === false) {
    die("Invalid private key format: " . openssl_error_string());
}

// Continue with signing using $privateKeyResource
echo "Private Key: " . $privateKey . "\n";
echo "Data to Sign: " . $dataToSign . "\n";
