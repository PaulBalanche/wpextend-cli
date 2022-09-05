<?php

namespace Wpextend\Cli\Controllers;

use Wpextend\Cli\Helpers\Render;
use Wpextend\Cli\Helpers\Terminal;
use Wpextend\Cli\Services\Database as DatabaseService;

class Database extends ControllerBase {

    private $databaseService;

    public function __construct() {
        
        parent::__construct();

        $this->databaseService = new DatabaseService();
    }

    public function check_database_exists() {

        if( file_exists( $this->get_config()->getCurrentWorkingDir() . '/' . $this->get_config()->getDockerDir() ) && ! file_exists( $this->get_config()->getCurrentWorkingDir() . '/' . $this->get_config()->getDockerDir() . '/mariadb' ) ) {

            $answer = Terminal::readline( '-- Missing local database. Do you want to add it? (y/n) ', false );
            if( strtolower($answer) == 'y' ) {
                $this->display_import_menu();
            }
        }
    }

    public function display_main_menu() {

        Render::output( PHP_EOL . '-- What do you want to do?' . PHP_EOL, 'heading');
        $select_options = [
            'Import database'
        ];
        $response = Main::getInstance()->shellController->select($select_options);
        switch( $response ) {

            case 1:
                $this->display_import_menu();
                break;
        }
    }

    public function display_import_menu() {

        Render::output( PHP_EOL . '-- What do you want to do?' . PHP_EOL, 'heading');
        $select_options = [
            'Import local file',
            'Dump and import remote database'
        ];
        $response = Main::getInstance()->shellController->select($select_options);
        switch( $response ) {

            case 1:
                $this->databaseService->import_local_file();
                break;

            case 2:
                $this->databaseService->import_remote_file();
                break;
        }
    }

}