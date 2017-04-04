<?php

namespace Repel\Framework;

class RFormatter {

    public $_record;
    public $_formatters       = array();
    private $_base_formatters = array(
        "date", "bool", "unserialize"
    );

    public function __construct($record = null) {
        $this->_record = $record;
    }

    public function getFormatters() {
        $serviceContainer = \Repel\Repel::getServiceContainer();
        return $serviceContainer->getFormatters();
    }

    public function format($attributte, array $formatter) {
        $attributte = str_replace("^", "", $attributte);
        $attributte = str_replace("*", "", $attributte);
        $keys       = array_keys($formatter);
        $key        = explode(":", $keys[0]);

        if (count($key) === 2) {
            $type = $key[1];

            if (in_array($type, $this->_base_formatters)) {
                return $this->formatBase($attributte, $formatter);
            }

            $formatters = $this->getFormatters();

            foreach ($formatters as $f) {
                if (in_array($type, $f->_formatters)) {
                    $f->_record = $this->_record;
                    return $f->format($attributte, $formatter);
                }
            }
            return array($key[0], $this->_record->$attributte);
        } else {
            return array("*" . $attributte, $this->_record->$attributte);
        }
    }

    private function formatBase($attributte, array $formatter) {
        $keys = array_keys($formatter);
        $key  = explode(":", $keys[0]);

        if (count($key) === 2) {
            $new_attributte = $key[0];
            $type           = $key[1];
            $parameters     = $formatter[$keys[0]];

            switch ($type) {
                case "date":
                    return array($new_attributte, $this->formatDate($this->_record->$attributte, $parameters));
                case "bool":
                    return array($new_attributte, $this->formatBool($this->_record->$attributte, $parameters));
                case "unserialize":
                    return array($new_attributte, $this->formatUnserialize($attributte));
                default:
                    return $this->_record->$attributte;
            }
        } else {
            return $this->_record->$attributte;
        }
    }

    protected function formatDate($date, $format) {
        if (strlen($format === 0)) {
            $format = 'Y-m-d H:i:s';
        }

        $time = strtotime($date);

        if ($time) {
            return date($format, $time);
        } else {
            return $date;
        }
    }

    protected function formatBool($value, $toString) {
        if ($toString) {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN) . "";
        } else {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }
    }

    protected function formatUnserialize($attribute) {
        if (empty($this->_record->{$attribute})) {
            return null;
        }
        return unserialize($this->_record->{$attribute});
    }

}
