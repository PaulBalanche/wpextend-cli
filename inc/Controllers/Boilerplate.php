<?php

namespace Wpextend\Cli\Controllers;

class Boilerplate extends ControllerBase {

    public static $type_available = [ 'Bedrock', 'Vanilla' ];

    private $typeInstance;

    public function __construct( $type ) {
        
        parent::__construct();

        $fully_qualified_class_name = "Wpextend\Cli\Controllers\\$type";
        $this->typeInstance = new $fully_qualified_class_name;
    }

    public function check_before_run() {

        $this->typeInstance->check_before_run();
    }
}