<?php

namespace Wpextend\Cli\Controllers;

use Wpextend\Cli\Helpers\Render;

class Init extends ControllerBase {

    public function checkProject() {

        $files_dir = scandir( $this->get_config()->getCurrentWorkingDir() );
        if( count($files_dir) == 3 && in_array('.', $files_dir) && in_array('..', $files_dir) && in_array('docker', $files_dir) ) {
            $this->display_main_menu();
        }
    }

    public function display_main_menu() {

        Render::output( "Empty project" , 'warning' );
        Render::output( PHP_EOL . '-- What do you want to do?' . PHP_EOL, 'heading');
        $select_options = [
            'Run existing project (Bitbucket)',
            'Init Bedrock (new WP from scratch)',
        ];
        $response = shell_exec( 'sh docker/bash/select.sh "' . implode('" "', $select_options) . '"' );
        switch( $response ) {

            case 1:
                Main::getInstance()->gitController->pull_from_bitbucket();
                Main::getInstance()->gitController->branch_choice();
                break;

            case 2:
                // shell_exec( 'sh docker/new/init.sh' );
                break;
        }
    }

}