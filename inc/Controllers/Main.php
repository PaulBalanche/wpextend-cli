<?php

namespace Wpextend\Cli\Controllers;

use Wpextend\Cli\Singleton\Config;
use Wpextend\Cli\Helpers\Render;

class Main extends ControllerBase {

    private static $_instance;

    private $argv,
            $dockerController,
            $databaseController;

    public function __construct( $args = [] ) {

        $this->argv = !empty($args) ? $args : $GLOBALS['argv'];

        $this->dockerController = new Docker();
        $this->databaseController = new Database();
        
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
            'Database operations'
        ];
        $response = shell_exec( 'sh docker/bash/select.sh "' . implode('" "', $select_options) . '"' );
        switch( $response ) {

            case 1:
                $this->dockerController->up();
                break;

            case 2:
                $this->databaseController = new Database();
                $this->databaseController->display_main_menu();
                break;
        }
    }

}