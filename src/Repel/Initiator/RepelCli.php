<?php

namespace Repel\Initiator;

class RepelCli {

    static $DOT_FILL    = 30;
    static $HEADER_FILL = 30;
    protected $show_output;
    protected $results; // cli output

    public function __construct($show_output = true) {
        $this->show_output = $show_output;
    }

    public function append($text) {
        $this->results[] = $text;
    }

    public function show($text) {
        $this->append($text);
        echo $text;
    }

    public function output($text) {
        if ($this->show_output) {
            $this->show($text);
        } else {
            $this->append($text);
        }
    }

    public function getResults() {
        return implode("", $this->results);
    }

    public function getPlainResults($nl2br = false) {
        $results = implode("", $this->results);

        $find = array(
            "[0;32m",
            "[0m",
            "[1;37m[42m",
            "[0;31m",
            "[1;37m[41m",
        );

        if ($nl2br) {
            return nl2br(str_replace($find, "", $results));
        } else {
            return str_replace($find, "", $results);
        }
    }

    public function getResultsArray() {
        return $this->results;
    }

}
