<?php

namespace Wpextend\Cli\Controllers;

use Wpextend\Cli\Helpers\Render;
use Wpextend\Cli\Helpers\Terminal;

class Main extends ControllerBase {

    private static $_instance;

    private $argv;

    public $initController,
        $dockerController,
        $databaseController,
        $contentController,
        $gitController;

    public function __construct( $args = [] ) {

        parent::__construct();

        $this->argv = !empty($args) ? $args : $GLOBALS['argv'];

        $this->checkDockerExists();

        $this->initController = new Init();
        $this->dockerController = new Docker();
        $this->databaseController = new Database();
        $this->contentController = new Content();
        $this->gitController = new Git();

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

    public function checkDockerExists() {

        if( ! file_exists( $this->get_config()->getCurrentWorkingDir() . '/docker' ) ) {
            $answer = Terminal::readline( '-- Your project does not have yet Docker instance. Add it? (y/n)' );
            if( strtolower($answer) == 'y' ) {
                $this->downloadDockerFiles();
            }
            else {
                Render::output( 'Sorry but for now WP Extend CLI needs its own docker instance to work...', 'error' );
            }
        }
    }

    public function downloadDockerFiles() {
        
        Render::output( 'Copying docker files...', 'info' );

        $src = $this->get_config()->getScriptDir() . '/docker';
        $dest = $this->get_config()->getCurrentWorkingDir() . '/docker';
        
        shell_exec( "cp -r $src $dest" );

        if( ! file_exists( $this->get_config()->getCurrentWorkingDir() . '/docker' ) ) {
            Render::output( 'Sorry an error occurs while copying files...' , 'error' );
            exit;
        }
        
        Render::output( 'Files successfully copied.' , 'success' );
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