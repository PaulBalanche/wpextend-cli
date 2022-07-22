<?php

namespace Wpextend\Cli\Services;

use Wpextend\Cli\Singleton\Config;

class ServiceBase {
    
    private $config;

    function __construct() {
        $this->config = Config::getInstance();
    }

    public function get_config() {
        return $this->config;
    }

}