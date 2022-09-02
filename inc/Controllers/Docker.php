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

        if( ! file_exists( $this->get_config()->getCurrentWorkingDir() . '/docker' ) ) {
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

        if( ! file_exists( $this->get_config()->getCurrentWorkingDir() . '/docker/.env' ) ) {

            Render::output( '-- Local environnement is not yet configured.', 'heading');
            $this->dockerService->setup();
        }
    }

    public function up () {

        Render::output( PHP_EOL . 'Starting docker...' . PHP_EOL, 'info' );
        echo shell_exec( "cd docker && make" );
        Render::output( PHP_EOL . 'Your website is up and running at:' . PHP_EOL . PHP_EOL . '  👉 ' . $this->get_config()->get_data( 'WP_HOME' ) . PHP_EOL, 'success' );
    }
    
    public function down () {
        echo shell_exec( "cd docker && make down" );
    }

}