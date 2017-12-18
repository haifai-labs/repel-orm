<?php

namespace Repel\Framework;

class RTransaction extends \PDO {

    protected static $savepointTransactions = array("pgsql", "mysql");
    // The current transaction level.
    protected static $transLevel = 0;
    private static $singleton;
    protected $PDO;

    public function __construct($record = null) {
        $serviceContainer = \Repel\Repel::getServiceContainer();
        if ($record !== null) {
            if (is_string($record)){
                $key = $record;
            } else{
                $key = $record->_DATABASE;
            }
        } else {
            $key = 'primary';
        }

        $connection = $serviceContainer->getConnectionManager($key)->getConnection();

        $this->PDO = $connection->PDOInstance;
    }

    public static function instance(RActiveRecord $record) {
        if (!(self::$singleton instanceof self) || self::$singleton->_record !== $record) {
            self::$singleton = new self($record);
        }
        return self::$singleton;
    }

    protected function nestable() {
        return in_array($this->PDO->getAttribute(\PDO::ATTR_DRIVER_NAME), self::$savepointTransactions);
    }

    public function beginTransaction() {
        if ( self::$transLevel > 0) {
            if ($this->nestable() ) {
                $this->PDO->exec("SAVEPOINT LEVEL".self::$transLevel);
            }
        } else {
            $this->PDO->beginTransaction();
        }

        self::$transLevel++;
    }

    public function commit() {
        self::$transLevel--;

        if (self::$transLevel > 0) {
            if ($this->nestable()) {
				$this->PDO->exec("RELEASE SAVEPOINT LEVEL".self::$transLevel);
            }
        } else {
            $this->PDO->commit();
        }

    }

    public function rollBack() {
        self::$transLevel--;

        if (self::$transLevel > 0) {
            if ($this->nestable()) {
                $this->PDO->exec("ROLLBACK TO SAVEPOINT LEVEL".self::$transLevel);
            }
        } else {
            $this->PDO->rollBack();
        }
    }

}
