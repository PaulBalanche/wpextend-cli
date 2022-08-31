<?php

namespace Wpextend\Cli\Controllers;

use Wpextend\Cli\Helpers\Render;
use Wpextend\Cli\Services\Docker as DockerService;

class Docker extends ControllerBase {

    private $dockerService;

    public function __construct() {
        
        parent::__construct();

        $this->dockerService = new DockerService();
        $this->checkDockerSetup();
    }

    public function checkDockerSetup() {

        if( ! file_exists( $this->get_config()->getCurrentWorkingDir() . '/docker/.env' ) ) {

            Render::output( 'Local environnement is not yet configured.', 'info');
            $this->dockerService->setup();
        }
    }

    public function up () {

        // Check if composer.json exist, but not vendor yet...
        if( file_exists( $this->get_config()->getCurrentWorkingDir() . '/composer.lock' ) && ! file_exists( $this->get_config()->getCurrentWorkingDir() . '/vendor' ) ) {

            Render::output( PHP_EOL . 'We found Composer file without vendor directory.' . PHP_EOL . 'Composer install in progress...' . PHP_EOL, 'info');

            shell_exec( "cd docker && make php-up &>/dev/null" );
            shell_exec( "cd docker && make composer-install" );

            Render::output( PHP_EOL . 'Composer dependencies are ready!', 'success');
        }

        Render::output( PHP_EOL . 'Docker starting...' . PHP_EOL, 'info');
        echo shell_exec( "cd docker && make" );
    }
    
    public function down () {
        echo shell_exec( "cd docker && make down" );
    }

}