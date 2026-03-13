<?php
// encryption_utils.php

// Define a secure key and cipher method
// IMPORTANT: In a production environment, this key should be stored in an environment variable or a protected config file.
define('ENCRYPTION_KEY', 'BTC_SYSTEM_SECRET_KEY_2026_@!!');
define('CIPHER_METHOD', 'AES-256-CBC');

/**
 * Encrypts data using AES-256-CBC
 */
function encryptData($data)
{
    if (empty($data))
        return $data;

    $iv_length = openssl_cipher_iv_length(CIPHER_METHOD);
    $iv = openssl_random_pseudo_bytes($iv_length);

    $encrypted = openssl_encrypt($data, CIPHER_METHOD, ENCRYPTION_KEY, 0, $iv);

    // Return IV + Encrypted data (base64 encoded)
    return base64_encode($iv . $encrypted);
}

/**
 * Decrypts data using AES-256-CBC
 */
function decryptData($data)
{
    if (empty($data))
        return $data;

    // Use strict mode to check if it's potentially valid base64
    $decoded = base64_decode($data, true);
    if ($decoded === false) {
        return $data; // Not base64, return original
    }

    $iv_length = openssl_cipher_iv_length(CIPHER_METHOD);
    if (strlen($decoded) <= $iv_length) {
        return $data; // Too short to be IV + ciphertext
    }

    $iv = substr($decoded, 0, $iv_length);
    $encrypted = substr($decoded, $iv_length);

    $decrypted = openssl_decrypt($encrypted, CIPHER_METHOD, ENCRYPTION_KEY, 0, $iv);

    // If decryption fails, it might be binary data that happened to look like base64
    return $decrypted === false ? $data : $decrypted;
}
?>
