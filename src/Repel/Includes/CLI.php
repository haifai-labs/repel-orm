<?php

namespace Repel\Includes;

define('red', 'red');
define('black', 'black');
define('dark_gray', 'dark_gray');
define('blue', 'blue');
define('light_blue', 'light_blue');
define('green', 'green');
define('light_green', 'light_green');
define('cyan', 'cyan');
define('light_cyan', 'light_cyan');
define('light_red', 'light_red');
define('purple', 'purple');
define('light_purple', 'light_purple');
define('brown', 'brown');
define('yellow', 'yellow');
define('light_gray', 'light_gray');
define('white', 'white');

class CLI {

    public static $foreground_colors = array(
        'black'        => '0;30',
        'dark_gray'    => '1;30',
        'blue'         => '0;34',
        'light_blue'   => '1;34',
        'green'        => '0;32',
        'light_green'  => '1;32',
        'cyan'         => '0;36',
        'light_cyan'   => '1;36',
        'red'          => '0;31',
        'light_red'    => '1;31',
        'purple'       => '0;35',
        'light_purple' => '1;35',
        'brown'        => '0;33',
        'yellow'       => '1;33',
        'light_gray'   => '0;37',
        'white'        => '1;37'
    );
    public static $background_colors = array(
        'black'      => '40',
        'red'        => '41',
        'green'      => '42',
        'yellow'     => '43',
        'blue'       => '44',
        'magenta'    => '45',
        'cyan'       => '46',
        'light_gray' => '47',
    );

    public static function isCLI() {
        return (php_sapi_name() === 'cli');
    }

    public static function out($arg) {
        if (self::isCLI()) {
            echo $arg;
        }
    }

    public static function success() {
        $return = "\n";
        $return .= CLI::color("SUCCESS", 'white', 'green') . "\n";
        $return .="\n";
        return $return;
    }

    public static function failure($ex) {
        if (gettype($ex) === 'string') {
            $text = $ex;
        } else {
            $text = $ex->getMessage();
        }
        $return = CLI::color("failed", red) . "\n";
        $return.= "\n";
        $return.= CLI::color($text, 'white', 'red') . "\n";
        $return.= "\n";
        return $return;
    }

    public static function warning($text) {
        $return = "\n";
        $return.= CLI::color($text, 'white', 'yellow') . "\n";
        $return.= "\n";
        return $return;
    }

// Returns colored string
    public static function color($string, $foreground_color = null, $background_color = null) {
        $colored_string = "";

// Check if given foreground color found
        if (isset(self::$foreground_colors[$foreground_color])) {
            $colored_string .= "\033[" . self::$foreground_colors[$foreground_color] . "m";
        }
// Check if given background color found
        if (isset(self::$background_colors[$background_color])) {
            $colored_string .= "\033[" . self::$background_colors[$background_color] . "m";
        }

// Add string and end coloring
        $colored_string .= $string . "\033[0m";

        return $colored_string;
    }

// Returns all foreground color names
    public function getForegroundColors() {
        return array_keys(self::foreground_colors);
    }

// Returns all background color names
    public function getBackgroundColors() {
        return array_keys(self::background_colors);
    }

    public static function newLine() {
        return "\n";
    }

    public static function dotFill($text, $length) {
        $diff = $length - strlen($text);
        if ($diff > 0) {
            for ($i = 0; $i < $diff; $i++) {
                $text.=".";
            }
        }
        return $text;
    }

    public static function h1($text, $length) {
        $text = strtoupper(trim($text));
        $diff = $length - strlen($text);
        if ($diff < 2) {
            $diff = 2;
        }
        if ($diff % 2 !== 0) {
            $diff++;
        }
        $decoration = '';
        for ($i = 0; $i < $diff / 2; $i++) {
            $decoration.="=";
        }
        $text = "\n" . $decoration . " " . $text . " " . $decoration . "\n";
        return $text;
    }

    public static function h2($text, $length) {
        $text = strtoupper(trim($text));
        $diff = $length - strlen($text);
        if ($diff < 2) {
            $diff = 2;
        }
        if ($diff % 2 !== 0) {
            $diff++;
        }
        $decoration = '';
        for ($i = 0; $i < $diff / 2; $i++) {
            $decoration.="-";
        }
        $text = "\n" . $decoration . " " . $text . " " . $decoration . "\n";
        return $text;
    }

}

?>
