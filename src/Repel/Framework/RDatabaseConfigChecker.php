<?php

namespace Repel\Framework;

class RDatabaseConfigChecker {

    public static function checkConfig($config) {
        if (!key_exists('primary', $config)) {
            echo "No 'primary' database definition!";
            return false;
        }

        foreach ($config as $key => $c) {
            if (!key_exists('username', $c)) {
                echo "No 'username' definition in database {$key}";
                return false;
            }
            if (!key_exists('password', $c)) {
                echo "No 'password' definition in database {$key}";
                return false;
            }
            if (!key_exists('driver', $c)) {
                echo "No 'driver' definition in database {$key}";
                return false;
            }
            if (!key_exists('adapter', $c)) {
                echo "No 'adapter' definition in database {$key}";
                return false;
            }
            return true;
        }
    }

}
