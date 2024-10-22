<?php


namespace App\Core {
    const methodi = 'aes-256-cbc';
    class Crypt {

        public static function encrypt($salt,$string) {
            $encrypted = null;
            $key = substr(hash('sha256','__'.$salt.'!_', true), 0, 16);
            $iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
            $encrypted = base64_encode(openssl_encrypt($string, methodi, $key, OPENSSL_RAW_DATA, $iv));
            return $encrypted;

        }
        public static function decrypt($salt,$string) {
            $decrypted = null;
            $key = substr(hash('sha256', '__'.$salt.'!_', true), 0, 16);
            $iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
            $decrypted = openssl_decrypt(base64_decode($string), methodi, $key, OPENSSL_RAW_DATA, $iv);
            return $decrypted;
        }

        public static function getKeyCashlez() {
            return "C@5hl3zAgt";
        }
    }
}
