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

        $this->checkDockerExists();
        $this->checkDockerSetup();
    }

    public function checkDockerExists() {

        if( ! file_exists( $this->get_config()->getCurrentWorkingDir() . '/docker' ) ) {
            $answer = Terminal::readline( '-- Your project does not have yet Docker instance. Add it? (y/n)' );
            if( strtolower($answer) == 'y' ) {
                $this->dockerService->downloadDockerFiles();
            }
            else {
                Render::output( 'Sorry but for now WP Extend CLI needs its own docker instance to work...', 'error' );
            }
        }
    }

    public function checkDockerSetup() {

        if( ! file_exists( $this->get_config()->getCurrentWorkingDir() . '/docker/.env' ) ) {

            Render::output( 'Local environnement is not yet configured.' . PHP_EOL . '-- What do you want to do?', 'heading');
            $select_options = [
                'Run existing project',
                'Init Bedrock (new WP from scratch)'
            ];
            $response = shell_exec( 'sh docker/bash/select.sh "' . implode('" "', $select_options) . '"' );
            switch( $response ) {

                case 1:
                    $this->dockerService->setup();
                    break;

                case 2:
                    Render::output( '/new/init.sh' );
                    break;
            }
        }
    }

    public function up () {

        echo shell_exec( "cd docker && make" );
    }

}