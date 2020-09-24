<?php

namespace SilverCart\ORM\Connect;

use SilverStripe\ORM\Connect\MySQLDatabase;
use SilverStripe\ORM\Connect\PDOConnector as SilverStripePDOConnector;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Environment;
use PDO;

/**
 * PDO driver database connector.
 * Extends the original SilverStripe PDO driver by enabling SSL encrypted connections
 * without the need of client key and certificate.
 * To enable the SSL connection, set up the SS_DATABASE_SSL_CA environment configuration 
 * property in your .env file.
 * There is a new, optional environment configuration property
 * SS_DATABASE_SSL_VERIFY_SERVER_CERT (bool) to disable the verification for self 
 * signed server certificates.
 * 
 * @package SilverCart
 * @subpackage ORM\Connect
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 24.09.2020
 * @copyright 2020 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class PDOConnector extends SilverStripePDOConnector
{
    /**
     * Connects to the database.
     * Customized code is marked by comments (CUSTOM SSL CONFIG START / CUSTOM SSL CONFIG END)
     * 
     * @param array $parameters Connection parameters
     * @param bool  $selectDB   Select DB?
     * 
     * @return void
     */
    public function connect($parameters, $selectDB = false) : void
    {
        $this->flushStatements();

        // Build DSN string
        // Note that we don't select the database here until explicitly
        // requested via selectDatabase
        $driver = $parameters['driver'] . ":";
        $dsn = array();

        // Typically this is false, but some drivers will request this
        if ($selectDB) {
            // Specify complete file path immediately following driver (SQLLite3)
            if (!empty($parameters['filepath'])) {
                $dsn[] = $parameters['filepath'];
            } elseif (!empty($parameters['database'])) {
                // Some databases require a selected database at connection (SQLite3, Azure)
                if ($parameters['driver'] === 'sqlsrv') {
                    $dsn[] = "Database={$parameters['database']}";
                } else {
                    $dsn[] = "dbname={$parameters['database']}";
                }
            }
        }

        // Syntax for sql server is slightly different
        if ($parameters['driver'] === 'sqlsrv') {
            $server = $parameters['server'];
            if (!empty($parameters['port'])) {
                $server .= ",{$parameters['port']}";
            }
            $dsn[] = "Server=$server";
        } elseif ($parameters['driver'] === 'dblib') {
            $server = $parameters['server'];
            if (!empty($parameters['port'])) {
                $server .= ":{$parameters['port']}";
            }
            $dsn[] = "host={$server}";
        } else {
            if (!empty($parameters['server'])) {
                // Use Server instead of host for sqlsrv
                $dsn[] = "host={$parameters['server']}";
            }

            if (!empty($parameters['port'])) {
                $dsn[] = "port={$parameters['port']}";
            }
        }

        // Connection charset and collation
        $connCharset = Config::inst()->get(MySQLDatabase::class, 'connection_charset');
        $connCollation = Config::inst()->get(MySQLDatabase::class, 'connection_collation');

        // Set charset if given and not null. Can explicitly set to empty string to omit
        if (!in_array($parameters['driver'], ['sqlsrv', 'pgsql'])) {
            $charset = isset($parameters['charset'])
                    ? $parameters['charset']
                    : $connCharset;
            if (!empty($charset)) {
                $dsn[] = "charset=$charset";
            }
        }

        // Connection commands to be run on every re-connection
        if (!isset($charset)) {
            $charset = $connCharset;
        }

        $options = [];
        if ($parameters['driver'] === 'mysql') {
            $options[PDO::MYSQL_ATTR_INIT_COMMAND] = 'SET NAMES ' . $charset . ' COLLATE ' . $connCollation;
        }

        // Set SSL options if they are defined
        if (array_key_exists('ssl_key', $parameters) &&
            array_key_exists('ssl_cert', $parameters)
        ) {
            $options[PDO::MYSQL_ATTR_SSL_KEY] = $parameters['ssl_key'];
            $options[PDO::MYSQL_ATTR_SSL_CERT] = $parameters['ssl_cert'];
            if (array_key_exists('ssl_ca', $parameters)) {
                $options[PDO::MYSQL_ATTR_SSL_CA] = $parameters['ssl_ca'];
            }
            // use default cipher if not provided
            $options[PDO::MYSQL_ATTR_SSL_CIPHER] = array_key_exists('ssl_cipher', $parameters) ? $parameters['ssl_cipher'] : self::config()->get('ssl_cipher_default');
        }
        /*************************************************************************/
        /*************************************************************************/
        /**                                                                     **/
        /**                ||                                 ||                **/
        /**                ||                                 ||                **/
        /**               _||_    CUSTOM SSL CONFIG START    _||_               **/
        /**               \  /                               \  /               **/
        /**                \/                                 \/                **/
        /**                                                                     **/
        /*************************************************************************/
        /*************************************************************************/
        if (Environment::getEnv('SS_DATABASE_SSL_VERIFY_SERVER_CERT') !== null) {
            $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = (bool) Environment::getEnv('SS_DATABASE_SSL_VERIFY_SERVER_CERT');
        }
        if (Environment::getEnv('SS_DATABASE_SSL_CA') !== null) {
            $options[PDO::MYSQL_ATTR_SSL_CA] = Environment::getEnv('SS_DATABASE_SSL_CA');
        }
        /*************************************************************************/
        /*************************************************************************/
        /**                                                                     **/
        /**                /\                                 /\                **/
        /**               /  \                               /  \               **/
        /**                ||      CUSTOM SSL CONFIG END      ||                **/
        /**                ||                                 ||                **/
        /**                ||                                 ||                **/
        /**                                                                     **/
        /*************************************************************************/
        /*************************************************************************/

        if (self::is_emulate_prepare()) {
            $options[PDO::ATTR_EMULATE_PREPARES] = true;
        }

        // May throw a PDOException if fails
        $this->pdoConnection = new PDO(
            $driver . implode(';', $dsn),
            empty($parameters['username']) ? '' : $parameters['username'],
            empty($parameters['password']) ? '' : $parameters['password'],
            $options
        );

        // Show selected DB if requested
        if ($this->pdoConnection && $selectDB && !empty($parameters['database'])) {
            $this->databaseName = $parameters['database'];
        }
    }
}