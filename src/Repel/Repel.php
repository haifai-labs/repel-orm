<?php

namespace Repel;

use Repel\Framework;

class Repel {

    private static $serviceContainer = null;

    public static function getServiceContainer() {
        if (null === self::$serviceContainer) {
            self::$serviceContainer = new Framework\RServiceContainer();
        }

        return self::$serviceContainer;
    }

    public static function setServiceContainer(Framework\RServiceContainer $serviceContainer) {
        self::$serviceContainer = $serviceContainer;
    }

}
