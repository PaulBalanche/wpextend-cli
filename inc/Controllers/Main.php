<?php

namespace Wpextend\Cli\Controllers;

use Wpextend\Cli\Helpers\Render;

class Main extends ControllerBase {

    private static $_instance = null;

    public $dockerController,
        $databaseController,
        $contentController,
        $gitController,
        $shellController,
        $boilerplateController,
        $environmentsController;

    private function __construct() {

        parent::__construct();

        $this->dockerController = new Docker();
        $this->databaseController = new Database();
        $this->contentController = new Content();
        $this->gitController = new Git();
        $this->shellController = new Shell();
        $this->environmentsController = new Environments();
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

        new Init();

        $this->dockerController->checkDockerExists();
        $this->dockerController->checkDockerSetup();

        $this->display_main_menu();
    }

    public function display_main_menu() {

        $select_options = [
            'Start docker',
            'Stop docker',
            'Database operations',
            'Download remote uploads'
        ];
        $response = $this->shellController->select( 'What do you want to do?', $select_options );
        switch( $response ) {

            case 1:
                $this->databaseController->check_database_exists();
                $this->boilerplateController->check_before_run();
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