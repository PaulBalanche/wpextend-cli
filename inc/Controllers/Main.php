<?php

namespace Wpextend\Cli\Controllers;

use Wpextend\Cli\Helpers\Render;

class Main extends ControllerBase {

    private static $_instance;

    private $argv,
            $dockerController,
            $databaseController,
            $contentController;

    public function __construct( $args = [] ) {

        $this->argv = !empty($args) ? $args : $GLOBALS['argv'];

        $this->dockerController = new Docker();
        $this->databaseController = new Database();
        $this->contentController = new Database();

        if( is_array($this->argv) && count($this->argv) == 1 ) {
            $this->display_main_menu();
        }
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
                $this->databaseController = new Database();
                $this->databaseController->display_main_menu();
                break;
            
            case 4:
                $this->contentController = new Content();
                $this->contentController->downloadUploads();
                break;
        }
    }

}