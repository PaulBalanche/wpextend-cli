<?php

namespace Wpextend\Cli\Controllers;

use Wpextend\Cli\Helpers\Render;

class Vanilla extends ControllerBase {

    public function __construct() {
        
        parent::__construct();

        Render::output( PHP_EOL . 'Feature in development... Please use existing project.' , 'error' );
        exit;
    }
}