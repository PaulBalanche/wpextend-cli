<?php

namespace Wpextend\Cli\Controllers;

use Wpextend\Cli\Singleton\Config;

class Main {

    private static $_instance;

    private $argv,
            $config;

    public function __construct( $args = [] ) {

        $this->argv = !empty($args) ? $args : $GLOBALS['argv'];
        $this->config = Config::getInstance();

        new Docker();
    }

    /**
     * Utility method to retrieve the main instance of the class.
     * The instance will be created if it does not exist yet.
     * 
     */
    public static function getInstance() {

        if( is_null(self::$_instance) ) {
            self::$_instance = new Main();
        }
        return self::$_instance;
    }
}