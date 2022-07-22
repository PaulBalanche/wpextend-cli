<?php

namespace Wpextend\Cli\Controllers;

use \Wpextend\Cli\Helpers\Render;
use Wpextend\Cli\Services\Docker as DockerService;

class Docker extends ControllerBase {

    private $dockerService;

    public function __construct() {
        
        parent::__construct();

        $this->dockerService = new DockerService();


        if( ! file_exists( $this->get_config()->getCurrentWorkingDir() . '/docker' ) ) {
            $answer = readline( Render::output( '-- Your project does not have yet Docker instance. Add it? (y/n) ', 'heading', true, false ) );
            if( strtolower($answer) == 'y' ) {
                $this->dockerService->downloadDockerFiles();
            }
            else {
                Render::output( 'Sorry but for now WP Extend CLI needs its own docker instance to work...', 'error' );
            }
        }



        if( ! file_exists( $this->get_config()->getCurrentWorkingDir() . '/docker/.env' ) ) {
            Render::output( 'Local environnement is not yet configured.' . PHP_EOL . '-- What do you want to do?', 'heading');
            Render::output( '1. Run existing project' );
            Render::output( '2. Init Bedrock (new WP from scratch)' );
            switch( readline() ) {

                case 1:
                    $this->dockerService->setup();
                    break;
                
                case 2:
                    Render::output( '/new/init.sh' );
                    break;
            }
        }


    }

}