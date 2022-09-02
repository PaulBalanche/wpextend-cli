<?php

namespace Wpextend\Cli\Controllers;

use Wpextend\Cli\Singleton\Config;

class ControllerBase {
    
    private $config;

    function __construct() {
        $this->config = Config::getInstance();
    }

    public function get_config() {
        return $this->config;
    }

}