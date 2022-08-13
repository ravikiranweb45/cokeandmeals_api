<?php

namespace app\helpers;

use Yii;


class AppHelper
{

    public function getUniqueRandomNum(){
        $randval = mt_rand(1000,9999);
        return $randval;
    }

    public function getDecryptdata($string){

        $privatefile = realpath(Yii::$app->basePath) .'/web/uploads/files/rsa_1024_priv.pem';
	    $fopen_private = fopen($privatefile,"r");
        $private_key = fread($fopen_private,8192);
        fclose($fopen_private);
        $pkey_private = openssl_pkey_get_private($private_key);
        openssl_private_decrypt(base64_decode($string), $decrypted, $pkey_private);

        return $decrypted;
    }

    public function getEncryptdata($string){
        $privatefile = realpath(Yii::$app->basePath) . '/web/uploads/files/rsa_1024_priv.pem';
        $fopen_private = fopen($privatefile,"r");
        $private_key = fread($fopen_private,8192);
        fclose($fopen_private);
        openssl_private_encrypt($string, $encrypted, $private_key);
        #$encrypted = base64_encode($encrypted);
        return $encrypted;
    }

    public function getPublicEncryptdata($string){

        $privatefile = realpath(Yii::$app->basePath) . '/web/uploads/files/rsa_1024_pub.pem';
        $fopen_private = fopen($privatefile,"r");
        $private_key = fread($fopen_private,8192);
        fclose($fopen_private);
        openssl_public_encrypt($string, $encrypted, $private_key);
        $encrypted = base64_encode($encrypted);
        return $encrypted;
    }

    public function getPublicDecryptdata($string){
        $privatefile = realpath(Yii::$app->basePath) . '/web/uploads/files/rsa_1024_pub.pem';
        $fopen_private = fopen($privatefile,"r");
        $private_key = fread($fopen_private,8192);
        fclose($fopen_private);
        $pkey_private = openssl_pkey_get_public($private_key);
        openssl_public_decrypt(base64_decode($string), $decrypted, $pkey_private);
        return $decrypted;
    }

}
