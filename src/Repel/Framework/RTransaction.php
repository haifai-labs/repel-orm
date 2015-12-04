<?php

namespace Repel\Framework;

class RTransaction {

    protected $transaction_level = 0;
    protected $PDO;

    public function __construct($record = null) {
        $serviceContainer = \Repel\Repel::getServiceContainer();
        if ($record !== null) {
            $key = $record->_DATABASE;
        } else {
            $key = 'primary';
        }

        $connection = $serviceContainer->getConnectionManager($key)->getConnection();

        $this->PDO = $connection->PDOInstance;

        $serviceContainer->setTransaction($connection->getDriver(), $this);
    }

    public function beginTransaction() {
        if ($this->transaction_level === 0) {
            $this->PDO->beginTransaction();
        }

        $this->transaction_level++;
    }

    public function addTransactionLevel() {
        $this->transaction_level++;
    }

    public function commit() {
        $this->transaction_level--;

        if ($this->transaction_level === 0) {
            $this->PDO->commit();
        }
    }

    public function rollBack() {
        $this->transaction_level--;

        if ($this->transaction_level === 0) {
            $this->PDO->rollBack();
        }
    }

}
