<?php
return array(

    /*
    |--------------------------------------------------------------------------
    | Public key file for password encrypt and decrypt.
    |--------------------------------------------------------------------------
    |
    | The password that user filled should be encrypted by the public key and
    | sent to the backend. The backend decrypts it and get the plaintext, then
    | use the Hash:check() to check if the password is correct.
    |
     */
    'public_key_file'  => config_path() . '/rsa_public_key.pem',

    /*
    |--------------------------------------------------------------------------
    | Private key file for password encrypt and decrypt.
    |--------------------------------------------------------------------------
    |
    | The password that user filled should be encrypted by the public key and
    | sent to the backend. The backend decrypts it and get the plaintext, then
    | use the Hash:check() to check if the password is correct.
    |
     */
    'private_key_file' => config_path() . '/rsa_private_key.pem',

);
