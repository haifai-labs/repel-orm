<?php

namespace Repel\Adapter\Generator;

class BaseGenerator {

    public static function singular($word, $delete_underscores = true) {
        // first letter to upper
        $word[0] = strtoupper($word[0]);

        // delete -ies
        if (substr($word, strlen($word) - 3) == "ies") {
            $word = substr($word, 0, strlen($word) - 3);
            $word .= "y";
        }
        // delete -xes
        if (substr($word, strlen($word) - 3) == "xes") {
            $word = substr($word, 0, strlen($word) - 3);
            $word .= "x";
        }

        // delete -es
        if (substr($word, strlen($word) - 2) == "es" && in_array(substr($word, strlen($word) - 3, 1), array("s", "x", "z", "ch", "sh"))) {
            $word = substr($word, 0, strlen($word) - 2);
        }

        // delete -s
        if ($word[strlen($word) - 1] == "s" && substr($word, strlen($word) - 2) !== "ss") {
            $word = substr($word, 0, strlen($word) - 1);
        }

        while (substr_count($word, "ies_")) {
            $index = strpos($word, "ies_");
            if (!$delete_underscores) {
                $word = self::str_replace_limit("ies", "y", $word);
            } else {
                $word             = self::str_replace_limit("ies_", "y", $word);
                $word[$index + 1] = strtoupper($word[$index + 1]);
            }
        }

        $index = 0;
        while (substr_count($word, "s_", $index + 1)) {
            $index = strpos($word, "s_");
            if ($word[$index - 1] !== "s") {
                if (!$delete_underscores) {
                    $word = self::str_replace_limit("s", "", $word);
                } else {
                    $word         = self::str_replace_limit("s_", "", $word);
                    $word[$index] = strtoupper($word[$index]);
                }
            }
        }

        if ($delete_underscores) {
            while (substr_count($word, "_")) {
                $index        = strpos($word, "_");
                $word         = self::str_replace_limit("_", "", $word);
                $word[$index] = strtoupper($word[$index]);
            }
        }
        return $word;
    }

    public static function firstLettersToUpper($word) {
        return str_replace("_", "", mb_convert_case($word, MB_CASE_TITLE, 'UTF-8'));
    }

    public static function str_replace_limit($search, $replace, $string, $limit = 1) {
        if (is_bool($pos = (strpos($string, $search)))) {
            return $string;
        }

        $search_len = strlen($search);

        for ($i = 0; $i < $limit; $i++) {
            $string = substr_replace($string, $replace, $pos, $search_len);

            if (is_bool($pos = (strpos($string, $search)))) {
                break;
            }
        }
        return $string;
    }

    public function generateTable($table) {
        return $table;
    }

    public static function replace_limit($search, $replace, $string, $limit = 1) {
        if (is_bool($pos = (strpos($string, $search)))) {
            return $string;
        }

        $search_len = strlen($search);

        for ($i = 0; $i < $limit; $i++) {
            $string = substr_replace($string, $replace, $pos, $search_len);

            if (is_bool($pos = (strpos($string, $search)))) {
                break;
            }
        }
        return $string;
    }

}
