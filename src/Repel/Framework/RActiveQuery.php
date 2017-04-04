<?php

namespace Repel\Framework;

class RActiveQuery {

    protected $_record;
    protected $_recordClass;
    protected $_table;
    private $_where;
    private $_order_by;
    private $_limit;

    public function __construct($record) {
        if (!isset($record)) {
            throw new Exception('Trying to construct a query, without its parent record');
        }
        $this->_recordClass = $record;
        $this->_record      = new $record();
        $this->_table       = $this->_record->TABLE;
        $this->_where       = array();
        $this->_order_by    = array();
        $this->_limit       = null;
    }

    static public function create() {
        $class_query  = get_called_class();
        $class_record = substr($class_query, 0, strlen($class_query) - 5);
        return new $class_query($class_record);
    }

    public function findByPK($key) {
        return $this->findOneByColumn($this->_record->PRIMARY_KEYS[0], $key);
    }

    public function findByPKs($keys) {
        return $this->findByColumn($this->_record->PRIMARY_KEYS[0], $keys);
    }

    public function findOne($criteria = null, $parameters = array()) {
        if ($criteria !== null) {
            if (!$criteria instanceof RActiveRecordCriteria) {
                $criteria = new RActiveRecordCriteria($criteria, $parameters);
            }
        } else {
            $criteria     = new RActiveRecordCriteria($this->_where);
            $this->_where = array();
        }

        $criteria->Limit = 1;

        return RExecutor::instance($this->_record)->find($criteria, false);
    }

    public function find($criteria = null, $parameters = array()) {
        if ($criteria !== null) {
            if (!$criteria instanceof RActiveRecordCriteria) {
                $criteria = new RActiveRecordCriteria($criteria, $parameters);
            }
        } else {
            $criteria     = new RActiveRecordCriteria($this->_where, $this->_order_by, $this->_limit);
            $this->_where = array();
        }

        return RExecutor::instance($this->_record)->find($criteria, true);
    }

    public function limit($limit) {
        $this->_limit = (int) $limit;

        return $this;
    }

    public function findByColumn($column, $value) {
        $criteria = new RActiveRecordCriteria();

        if (is_array($value)) {
            $i         = 0;
            $in_values = "";

            foreach ($value as $v) {
                $in_values .= ":{$column}{$i}, ";
                $criteria->Parameters[":{$column}{$i}"] = $v;
                $i++;
            }

            $criteria->Condition = "{$this->_table}.{$column} IN ( " . substr($in_values, 0, strlen($in_values) - 2) . " )";
        } else {
            $criteria->Condition                = "{$this->_table}.{$column} = :{$column}";
            if (is_bool($value)){
                if ($value){
                    $criteria->Parameters[":{$column}"] = 'true';
                } else {
                    $criteria->Parameters[":{$column}"] = 'false';
                }
            } else {
                $criteria->Parameters[":{$column}"] = $value;
            }
        }

        return RExecutor::instance($this->_record)->find($criteria, true);
    }

    public function findOneByColumn($column, $value) {
        $criteria = new RActiveRecordCriteria();

        if (is_array($value)) {
            $i         = 0;
            $in_values = "";

            foreach ($value as $v) {
                $in_values .= ":{$column}{$i}, ";
                $criteria->Parameters[":{$column}{$i}"] = $v;
                $i++;
            }

            $criteria->Condition = "{$this->_table}.{$column} IN ( " . substr($in_values, 0, strlen($in_values) - 2) . " )";
        } else {
            $criteria->Condition                = "{$this->_table}.{$column} = :{$column}";
            if (is_bool($value)){
                if ($value){
                    $criteria->Parameters[":{$column}"] = 'true';
                } else {
                    $criteria->Parameters[":{$column}"] = 'false';
                }
            } else {
                $criteria->Parameters[":{$column}"] = $value;
            }
        }

        return RExecutor::instance($this->_record)->find($criteria, false);
    }

    public function filterByColumn($column, $value, $operator = ROperator::EQUAL) {
        $count = count($this->_where);

        if (is_array($value)) {
            $i         = $count;
            $in_values = "";

            $parameters = array();

            foreach ($value as $v) {
                $in_values .= ":{$column}{$i}, ";
                $parameters[":{$column}{$i}"] = $v;
                $i++;
            }

            if ($operator === ROperator::NOT) {
                $this->_where[] = array(
                    "condition"  => "{$this->_table}.{$column} NOT IN ( " . substr($in_values, 0, strlen($in_values) - 2) . " )",
                    "parameters" => $parameters
                );
            } else {
                $this->_where[] = array(
                    "condition"  => "{$this->_table}.{$column} IN ( " . substr($in_values, 0, strlen($in_values) - 2) . " )",
                    "parameters" => $parameters
                );
            }
        } else {
            $repel_operator = ROperator::$OPERATORS[$this->_record->ADAPTER][$operator];
            if (is_bool($value)){
                if ($value){
                    $this->_where[] = array(
                        "condition"  => "{$this->_table}.{$column} {$repel_operator} :{$column}{$count}",
                        "parameters" => array(":{$column}{$count}" => 'true')
                    );
                } else {
                    $this->_where[] = array(
                        "condition"  => "{$this->_table}.{$column} {$repel_operator} :{$column}{$count}",
                        "parameters" => array(":{$column}{$count}" => 'false')
                    );
                }
            } else {
				if ($value === null ) {
					$repel_operator = ROperator::$OPERATORS[$this->_record->ADAPTER][ROperator::IS_NULL];
					if ($operator === ROperator::NOT_EQUAL){
						$repel_operator = ROperator::$OPERATORS[$this->_record->ADAPTER][ROperator::IS_NOT_NULL];
					}
					$this->_where[] = array(
						"condition"  => "{$this->_table}.{$column} {$repel_operator}",
					);
				}else{
					$this->_where[] = array(
	                    "condition"  => "{$this->_table}.{$column} {$repel_operator} :{$column}{$count}",
	                    "parameters" => array(":{$column}{$count}" => $value)
	                );
				}

            }

        }
        return $this;
    }

    public function orderByColumn($column, $order = "asc") {
        if (in_array($order, array("asc", "desc"))) {
            $this->_order_by[$column] = $order;
        }
        return $this;
    }

    public function count($criteria = null, $parameters = array()) {
        if ($criteria !== null) {
            if (!$criteria instanceof RActiveRecordCriteria) {
                $criteria = new RActiveRecordCriteria($criteria, $parameters);
            }
        } else {
            $criteria     = new RActiveRecordCriteria($this->_where);
            $this->_where = array();
        }

        return RExecutor::instance($this->_record)->count($criteria);
    }

    public function findBySql($statement, $parameters = array()) {
        return RExecutor::instance($this->_record)->findBySql($statement, $parameters, true);
    }

    public function findOneBySql($statement, $parameters = array()) {
        return RExecutor::instance($this->_record)->findBySql($statement, $parameters, false);
    }

}
