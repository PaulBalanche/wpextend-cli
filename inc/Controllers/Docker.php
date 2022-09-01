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

            Render::output( 'Local environnement is not yet configured.', 'info');
            $this->dockerService->setup();
        }
    }

    public function check_database_exists() {

        if( file_exists( $this->get_config()->getCurrentWorkingDir() . '/docker/' ) && ! file_exists( $this->get_config()->getCurrentWorkingDir() . '/docker/mariadb' ) ) {

            $answer = Terminal::readline( '-- Missing local database. Do you want to add it? (y/n)' );
            if( strtolower($answer) == 'y' ) {
                Main::getInstance()->databaseController->display_import_menu();
            }
        }
    }

    public function check_composer() {

         // Check if composer.json exist, but not vendor yet...
         if( file_exists( $this->get_config()->getCurrentWorkingDir() . '/composer.lock' ) && ! file_exists( $this->get_config()->getCurrentWorkingDir() . '/vendor' ) ) {

            Render::output( PHP_EOL . 'We found Composer file without vendor directory.' . PHP_EOL . 'Composer install in progress...' . PHP_EOL, 'info');

            shell_exec( "cd docker && make php-up &>/dev/null" );
            shell_exec( "cd docker && make composer-install" );

            Render::output( PHP_EOL . 'Composer dependencies are ready!', 'success');
        }
    }

    public function up () {

        $this->check_database_exists();
        $this->check_composer();

        Render::output( PHP_EOL . 'Docker starting...' . PHP_EOL, 'info');
        echo shell_exec( "cd docker && make" );
    }
    
    public function down () {
        echo shell_exec( "cd docker && make down" );
    }

}