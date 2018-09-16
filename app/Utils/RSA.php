<?php
namespace App\Utils;

use Illuminate\Support\Facades\File;

/**
 * The utility class of encapsulated RSA cipher. It uses
 * a pair of public key and private key to do the cipher.
 * It uses the private key to sign and decrypt, and
 * uses the public key to verify and encrypt.
 *
 * @author Benjamin Cao(caojianghui@carnetmotor.com)
 */
class RSA
{
    /**
     * Get the signature of the data.
     *
     * @param   string $data
     * @param   string $code
     * @return  string
     */
    public function sign($data, $code = 'base64')
    {
        $signature = '';
        if (openssl_sign($data, $signature, $this->getPriKey(config('rsa.private_key_file')))) {
            return $this->encode($signature, $code);
        }

        return $signature;
    }

    /**
     * Verify the signature is correct or not.
     *
     * @param   string  $data
     * @param   string  $sign
     * @param   string  $code
     * @return  boolean
     */
    public function verify($data, $sign, $code = 'base64')
    {
        $result = false;

        $sign = $this->decode($sign, $code);
        if ($sign !== false) {
            switch (openssl_verify($data, $sign, $this->getPubKey(config('rsa.public_key_file')))) {
                case 1:
                    $result = true;
                    break;
                case 0:
                case -1:
                default:
                    $result = false;
                    break;
            }
        }

        return $result;
    }

    /**
     * Encrypt the data using the public key and encode the result to the special format.
     *
     * @param   string  $data
     * @param   string  $code
     *
     * @return  string
     */
    public function encrypt($data, $code = 'base64')
    {
        $result = '';
        if (openssl_public_encrypt($data, $result, $this->getPubKey(config('rsa.public_key_file')))) {
            return $this->encode($result, $code);
        }

        return '';
    }

    /**
     * Decrypt the specially formated encrypted data using the private key.
     *
     * @param   string  $data
     * @param   string  $code
     * @return  string
     */
    public function decrypt($data, $code = 'base64')
    {
        $result = '';
        $data   = $this->decode($data, $code);
        if (openssl_private_decrypt($data, $result, $this->getPriKey(config('rsa.private_key_file')))) {
            return $result;
        }

        return '';
    }

    /**
     * Get the public key from the public key file.
     *
     * @param   string  $publicKeyFile
     * @return  string|null
     */
    private function getPubKey($publicKeyFile)
    {
        $content = File::get($publicKeyFile);
        if ($content) {
            return openssl_get_publickey($content);
        }

        return null;
    }

    /**
     * Get the private key from the private key file.
     *
     * @param   string  $privateKeyFile
     * @return  string|null
     */
    private function getPriKey($privateKeyFile)
    {
        $content = File::get($privateKeyFile);
        if ($content) {
            return openssl_get_privatekey($content);
        }

        return null;
    }

    /**
     * Encode the data to a special format.
     *
     * @param   string  $data
     * @param   string  code
     * @return  string
     */
    public function encode($data, $code = 'base64')
    {
        switch (strtolower($code)) {
            case 'base64':
                $data = base64_encode('' . $data);
                break;
            case 'hex':
                $data = bin2hex($data);
                break;
            case 'bin':
            default:
                break;
        }

        return $data;
    }

    /**
     * Decode the specially formated data.
     *
     * @param   string  $data
     * @param   string  $code
     * @return  string
     */
    public function decode($data, $code = 'base64')
    {
        switch (strtolower($code)) {
            case 'base64':
                $data = base64_decode($data);
                break;
            case 'hex':
                $data = $this->hex2bin($data);
                break;
            case 'bin':
            default:
                break;
        }

        return $data;
    }

    /**
     * Convert the hex string to binary string.
     *
     * @param   string  $data
     * @return  string|false    False if the data is not illegal.
     */
    private function hex2bin($data)
    {
        return $data != false && preg_match('/^[0-9a-fA-F]+$/i', $data) ? pack("H*", $data) : false;
    }
}
