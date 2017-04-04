<?php

namespace Repel\Framework;

class ROperator {

    const EQUAL         = "eq";
    const NOT_EQUAL     = "neq";
    const GREATER_THAN  = "gt";
    const LESS_THAN     = "lt";
    const GREATER_EQUAL = "geq";
    const LESS_EQUAL    = "leq";
    const NOT           = "not";
    const IS_NULL       = "is_null";
    const IS_NOT_NULL   = "is_not_null";

    public static $OPERATORS = array(
        "pgsql"   => array(
            self::EQUAL         => "=",
            self::NOT_EQUAL     => "!=",
            self::GREATER_THAN  => ">",
            self::GREATER_EQUAL => ">=",
            self::LESS_THAN     => "<",
            self::LESS_EQUAL    => "<=",
            self::IS_NULL       => "IS NULL",
            self::IS_NOT_NULL   => "IS NOT NULL"
        ),
        "default" => array(
            self::EQUAL         => "=",
            self::NOT_EQUAL     => "<>",
            self::GREATER_THAN  => ">",
            self::GREATER_EQUAL => ">=",
            self::LESS_THAN     => "<",
            self::LESS_EQUAL    => "<=",
            self::IS_NULL       => "IS NULL",
            self::IS_NOT_NULL   => "IS NOT NULL"
        )
    );

}
