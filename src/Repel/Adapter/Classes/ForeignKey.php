<?php
namespace Repel\Adapter\Classes;

class ForeignKey {

    public $referenced_table;
    public $referenced_column;

    public function __construct($referenced_table, $referenced_column) {
        $this->referenced_table = $referenced_table;
        $this->referenced_column = $referenced_column;
    }

}