<?php

namespace Wpextend\Cli\Controllers;

use Wpextend\Cli\Helpers\Render;

class Init extends ControllerBase {

    public function __construct() {
        
        parent::__construct();

        Render::output( PHP_EOL . '-------------------- WPExtend CLI - Welcome 👋 --------------------' . PHP_EOL . PHP_EOL, 'heading', false);

        $this->check_wpe_config();
        $this->check_empty_project();
        $this->load_boilerplate();
    }

    public function check_wpe_config() {

        if( ! file_exists( $this->get_config()->get_config_file_path() ) ) {
            $this->get_config()->set_data( 'created_at', date('c') );
        }
    }

    public function check_empty_project() {

        $files_dir = scandir( $this->get_config()->getCurrentWorkingDir() );
        if(
            ( count($files_dir) == 2 && in_array('.', $files_dir) && in_array('..', $files_dir) ) ||
            ( count($files_dir) == 3 && in_array('.', $files_dir) && in_array('..', $files_dir) && in_array($this->get_config()->getDockerDir(), $files_dir) ) ||
            ( count($files_dir) == 3 && in_array('.', $files_dir) && in_array('..', $files_dir) && in_array($this->get_config()->getConfigJsonFilename(), $files_dir) ) ||
            ( count($files_dir) == 4 && in_array('.', $files_dir) && in_array('..', $files_dir) && in_array($this->get_config()->getDockerDir(), $files_dir) && in_array($this->get_config()->getConfigJsonFilename(), $files_dir) )
        ) {
            $this->display_menu_empty_project();
        }
    }

    public function load_boilerplate() {

        $boilerplate = $this->get_config()->get( 'boilerplate' );
        if( is_null($boilerplate) || ! in_array($boilerplate, Boilerplate::$type_available) ) {

            do{
                $select_options = [
                    'Bedrock | WordPress Boilerplate',
                    'Basic Wordpress'
                ];
                $response = Main::getInstance()->shellController->select( 'What\'s the structure of the project?', $select_options );
                switch( $response ) {
    
                    case 1:
                        $boilerplate = 'Bedrock';
                        break;
                    
                    case 2:
                        $boilerplate = 'Vanilla';
                        break; 
                }
    
            } while( is_null($boilerplate) || ! in_array($boilerplate, Boilerplate::$type_available) );

            $this->get_config()->set_data( 'boilerplate', $boilerplate );
        }

        Main::getInstance()->boilerplateController = new Boilerplate( $boilerplate );
    }

    public function display_menu_empty_project() {
        
        Render::output( PHP_EOL . 'Empty project...' , 'info', false);
        $select_options = [
            'Clone existing project (Bitbucket, Github, ...)',
            'Create a new Wordpress installation (feature in development...)',
        ];
        $response = Main::getInstance()->shellController->select( 'What do you want to do?', $select_options );
        switch( $response ) {

            case 1:
                Main::getInstance()->gitController->clone();
                Main::getInstance()->gitController->branch_choice();
                break;

            case 2:
                Render::output( PHP_EOL . 'Feature in development... Please use existing project.' , 'error' );
                exit;
                // shell_exec( 'sh ' . $this->get_config()->getDockerDir() . '/new/init.sh' );
                break;
        }
    }

}