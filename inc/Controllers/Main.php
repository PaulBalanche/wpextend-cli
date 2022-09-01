<?php

namespace Wpextend\Cli\Controllers;

use Wpextend\Cli\Helpers\Render;

class Main {

    private static $_instance = null;

    public $initController,
        $dockerController,
        $databaseController,
        $contentController,
        $gitController;

    private function __construct() {

        $this->initController = new Init();
        $this->dockerController = new Docker();
        $this->databaseController = new Database();
        $this->contentController = new Content();
        $this->gitController = new Git();
    }

    /**
     * Utility method to retrieve the main instance of the class.
     * The instance will be created if it does not exist yet.
     * 
     */
    public static function getInstance() {

        if( is_null(self::$_instance) ) {
            self::$_instance = new Main();
        }
        return self::$_instance;
    }


    public function init() {

        $this->dockerController->checkDockerExists();
        $this->dockerController->checkDockerSetup();

        $this->initController->checkProject();

        $this->display_main_menu();
    }

    public function display_main_menu() {

        Render::output( PHP_EOL . '-- What do you want to do?' . PHP_EOL, 'heading');
        $select_options = [
            'Start',
            'Stop',
            'Database operations',
            'Download remote uploads'
        ];
        $response = shell_exec( 'sh docker/bash/select.sh "' . implode('" "', $select_options) . '"' );
        switch( $response ) {

            case 1:
                $this->dockerController->up();
                break;
            
            case 2:
                $this->dockerController->down();
                break; 

            case 3:
                $this->databaseController->display_main_menu();
                break;
            
            case 4:
                $this->contentController->downloadUploads();
                break;
        }
    }

}