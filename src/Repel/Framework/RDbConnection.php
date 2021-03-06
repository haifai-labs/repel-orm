<?php

namespace Repel\Framework;

class RDbConnection {

    private static $singleton;
    private $_driver;
    public $PDOInstance;

    private function __construct($driver, $user, $password) {
        $this->_driver = $driver;
        $this->PDOInstance = new \PDO($driver, $user, $password);
        $this->PDOInstance->query("SET client_encoding = 'UTF8';");
        $this->PDOInstance->query("SET NAMES 'UTF8';");
        $this->PDOInstance->query("SET TIME ZONE 'Europe/Warsaw';");
        $this->PDOInstance->query("SET bytea_output = 'escape';");
        //$this->PDOInstance->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
    }

    public static function instance($driver = null, $user = null, $password = null) {
        if (!(self::$singleton instanceof self) || self::$singleton->_driver !== $driver) {
            if ($driver === null || $user === null || $password === null){
                return null;
            }
            self::$singleton = new self($driver, $user, $password);
        }
        return self::$singleton;
    }

    public static function get() {
        return self::instance()->PDOInstance;
    }

}
