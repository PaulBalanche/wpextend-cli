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
                $this->display_main_menu();
            }
        }
    }

    public function display_main_menu() {

        do {
            $select_options = [
                'Import local file',
                'Dump/download remote database',
                'Dump/download remote database & import'
            ];
            $response = Main::getInstance()->shellController->select( 'What do you want to do?', $select_options );
            switch( $response ) {

                case 1:
                    $filename = $this->databaseService->download_local_file();
                    break;

                case 2:
                    $filename = $this->databaseService->download_remote_file();
                    Render::output( PHP_EOL . 'Dump is here: ' . $this->get_config()->getDockerDir() . '/' .  $filename, 'success');
                    return;
                    break;
                
                case 3:
                    $filename = $this->databaseService->download_remote_file();
                    break;
            }

        } while( ! $filename );

        $this->databaseService->import_command( $filename );
    }

}