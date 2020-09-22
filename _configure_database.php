<?php

/**
 * Adds SSL support to the database connection.
 * Uses the SS environment parameters:
 *  SS_DATABASE_SSL_KEY
 *  SS_DATABASE_SSL_CERT
 *  SS_DATABASE_SSL_CA
 *  SS_DATABASE_SSL_CIPHER
 */

use SilverStripe\Core\Environment;

if (!array_key_exists('ssl_key', $databaseConfig)) {
    $sslKey    = Environment::getEnv('SS_DATABASE_SSL_KEY');
    $sslCert   = Environment::getEnv('SS_DATABASE_SSL_CERT');
    $sslCa     = Environment::getEnv('SS_DATABASE_SSL_CA');
    $sslCipher = Environment::getEnv('SS_DATABASE_SSL_CIPHER');

    if ($sslKey !== false
     && $sslCert !== false
    ) {
        $sslConfig = [
            'ssl_key'  => $sslKey,
            'ssl_cert' => $sslCert,
        ];
        if ($sslCa !== false) {
            $sslConfig['ssl_ca'] = $sslCa;
        }
        if ($sslCipher !== false) {
            $sslConfig['ssl_cipher'] = $sslCipher;
        }
        $databaseConfig = array_merge($databaseConfig, $sslConfig);
    }
}