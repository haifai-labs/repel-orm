<?php

namespace Repel\Adapter\Fetcher;

use Repel\Adapter\Fetcher\FetcherInterface;
use Repel\Adapter\Classes;

class PostgreSQLFetcher implements FetcherInterface {

    protected $config  = null;
    protected $adapter = null;
    protected $connection_name;

    public function __construct($connection_name = null) {
        if ($connection_name) {
            $this->connection_name = $connection_name;
        } else {
            $this->connection_name = 'primary';
        }
    }

    public function setAdapter($adapter) {
        if (get_class($adapter) === 'Repel\Adapter\Adapter') {
            $this->adapter = $adapter;
        } else {
            throw new \Exception('Fetcher PostgreSQLFetcher wrong adapter instance given.');
        }
    }

    protected function addTable($row) {
        if (!isset($row) || !isset($row['table_name']) || !isset($row['table_type'])) {
            throw new \Exception('(addTable) Wrong row format: ' . print_r($row, true));
        }
        $this->adapter->addTable($row['table_name'], $row['table_type']);
    }

    protected function addColumn($row) {
        if (!isset($row) || !isset($row['table_name']) || !isset($row['column_name'])) {
            throw new Exception('(addColumn) Wrong row format: ' . print_r($row, true));
        }
        $new_column                 = new Classes\Column();
        $new_column->name           = $row['column_name'];
        $new_column->type           = $row['data_type'];
        $new_column->default        = $row['column_default'];
        $new_column->is_primary_key = $row['constraint_type'] === 'PRIMARY KEY' ? 1 : 0;
        $new_column->is_null        = $row['is_nullable'] === 'YES' ? 1 : 0;

        if ($row['constraint_type'] === 'FOREIGN KEY') {
            $new_column->foreign_key = new Classes\ForeignKey($row['referenced_table'], $row['referenced_column']);
        }
        $this->adapter->addColumn($row['table_name'], $new_column);
    }

    protected function tableExists($row) {
        if (!isset($row) || !isset($row['table_name'])) {
            throw new Exception('(tableExists) Wrong row format: ' . print_r($row, true));
        }
        return $this->adapter->tableExists($row['table_name']);
    }

    public function fetch() {

        if (!is_array($this->adapter->config)) {
            throw new \Exception('Fetcher PostgreSQLFetcher needs a config file.');
        }
        if (!is_array($this->adapter->config[$this->connection_name])) {
            throw new \Exception('Fetcher PostgreSQLFetcher invalid config for connection: ' . $this->connection . '.');
        }

        $this->config = $this->adapter->config[$this->connection_name];
        if (isset($this->config['public_schema'])) {
            $this->schema = $this->config['public_schema'];
        } else {
            $this->schema = 'public';
        }


        $sql = "select columns.table_name,tables.table_type,columns.column_name,columns.column_default,columns.is_nullable,columns.data_type,constraints.constraint_type,constraints.referenced_table,constraints.referenced_column
FROM information_schema.tables tables JOIN information_schema.columns ON columns.table_name = tables.table_name 
AND columns.table_schema = tables.table_schema
 
left join (
   SELECT tc.constraint_name,
          tc.constraint_type,
          tc.table_name AS constraint_table_name,
          kcu.column_name AS constraint_column_name,
          ccu.table_name AS referenced_table,
          ccu.column_name AS referenced_column
          
 
     FROM information_schema.table_constraints tc
LEFT JOIN information_schema.key_column_usage kcu

       ON tc.constraint_catalog = kcu.constraint_catalog
      AND tc.constraint_schema = kcu.constraint_schema
      AND tc.constraint_name = kcu.constraint_name
      
LEFT JOIN information_schema.referential_constraints rc

       ON tc.constraint_catalog = rc.constraint_catalog
      AND tc.constraint_schema = rc.constraint_schema
      AND tc.constraint_name = rc.constraint_name
      
LEFT JOIN information_schema.constraint_column_usage ccu

       ON rc.unique_constraint_catalog = ccu.constraint_catalog
      AND rc.unique_constraint_schema = ccu.constraint_schema
      AND rc.unique_constraint_name = ccu.constraint_name


   WHERE tc.constraint_schema = '{$this->schema}') constraints ON constraints.constraint_column_name = columns.column_name AND constraints.constraint_table_name = columns.table_name
   WHERE columns.table_schema = '{$this->schema}'
   
   ORDER BY columns.table_name,columns.column_name";

        $this->db = new \PDO($this->config['driver'], $this->config['username'], $this->config['password']);
        return $this->buildStructure($this->db->query($sql));
    }

    protected function buildStructure($results) {
        foreach ($results as $row) {
            if (!$this->tableExists($row)) {
                $this->addTable($row);
            }
            $this->addColumn($row);
        }
    }

}
