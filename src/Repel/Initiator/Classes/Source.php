<?php

namespace Repel\Initiator\Classes;

class Source {

    public $file_path;
    public $file_content;

    public function __construct($file) {
        $this->file_path = $file;
        if (file_exists($file)) {
            $this->file_content = file_get_contents($file);
        }
    }

}
