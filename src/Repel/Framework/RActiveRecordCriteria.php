<?php

namespace Repel\Framework;

class RActiveRecordCriteria {

    public $Condition;
    public $Parameters;
    public $OrdersBy;
    public $Limit;
    public $Offset;

    public function __construct($condition = null, $parameters = null, $limit = null) {
        if (is_array($condition)) {
            $temp_condition  = array();
            $temp_parameters = array();
            foreach ($condition as $c) {
                $temp_condition[] = $c["condition"];
                foreach ($c["parameters"] as $key => $value) {
                    $temp_parameters[$key] = $value;
                }
            }

            $temp_order_by = array();

            if ($parameters !== null) {
                foreach ($parameters as $column => $order) {
                    $temp_order_by[$column] = $order;
                }
            }

            $this->Condition  = implode(" AND ", $temp_condition);
            $this->Parameters = $temp_parameters;
            $this->OrdersBy   = $temp_order_by;

            if ($limit !== null) {
                $this->Limit = (int) $limit;
            }
        } else {
            $this->Condition = $condition;
            if ($parameters === null) {
                $this->Parameters = array();
            } else {
                $this->Parameters = $parameters;
            }
        }
    }

}
