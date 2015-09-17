<?php

namespace Repel\Framework;

class ROperator {

    const EQUAL = "eq";
    const NOT_EQUAL = "neq";
    const GREATER_THAN = "gt";
    const LESS_THAN = "lt";
    const GREATER_EQUAL = "geq";
    const LESS_EQUAL = "leq";

    public static $OPERATORS = array(
        "pgsql" => array(
            self::EQUAL => "=",
            self::NOT_EQUAL => "!=",
            self::GREATER_THAN => ">",
            self::GREATER_EQUAL => ">=",
            self::LESS_THAN => "<",
            self::LESS_EQUAL => "<="
        ),
        "default" => array(
            self::EQUAL => "=",
            self::NOT_EQUAL => "<>",
            self::GREATER_THAN => ">",
            self::GREATER_EQUAL => ">=",
            self::LESS_THAN => "<",
            self::LESS_EQUAL => "<="
        )
    );

}
