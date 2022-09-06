<?php

namespace Wpextend\Cli\Controllers;

use Wpextend\Cli\Helpers\Render;
use Wpextend\Cli\Helpers\Terminal;
use Wpextend\Cli\Services\Docker as DockerService;

class Docker extends ControllerBase {

    private $dockerService;

    public function __construct() {
        
        parent::__construct();

        $this->dockerService = new DockerService();
    }

    public function checkDockerExists() {

        if( ! file_exists( $this->get_config()->getCurrentWorkingDir() . '/' . $this->get_config()->getDockerDir() ) ) {
            $answer = Terminal::readline( '-- Your project does not have yet Docker instance. Add it? (y/n) ', false );
            if( strtolower($answer) == 'y' ) {
                $this->dockerService->downloadDockerFiles();
            }
            else {
                Render::output( PHP_EOL. 'Sorry but for now WP Extend CLI needs its own docker instance to work...' . PHP_EOL, 'error' );
                exit;
            }
        }
    }

    public function checkDockerSetup() {

        if( ! file_exists( $this->get_config()->getCurrentWorkingDir() . '/' . $this->get_config()->getDockerDir() . '/.env' ) ) {

            Render::output( '-- Docker local environment is not yet configured.', 'heading');
            $this->dockerService->setup();
        }
    }

    public function up () {

        Render::output( PHP_EOL . 'Starting docker...' . PHP_EOL, 'info' );
        echo shell_exec( "cd " . $this->get_config()->getDockerDir() . " && make" );
    }
    
    public function down () {
        echo shell_exec( "cd " . $this->get_config()->getDockerDir() . " && make down" );
    }

}