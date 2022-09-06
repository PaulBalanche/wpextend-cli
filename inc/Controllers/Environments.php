<?php

namespace Wpextend\Cli\Controllers;

use Wpextend\Cli\Helpers\Terminal;

class Environments extends ControllerBase {

    public function choose_remote_environment() {

        $remote_environments = $this->get_remote_environments();
        if( is_null($remote_environments) ) {

            $answer = Terminal::readline( '-- There is no environment configured yet. Do you want to add it? (y/n) ', false );
            if( strtolower($answer) == 'y' ) {
                $remote_environment_selected = $this->add_remote_environment();
            }
        }
        else {
            $remote_environments[] = 'Add a new environment...';
            $response = Main::getInstance()->shellController->select( 'Please select the environment?', $remote_environments );
            $remote_environment_selected = ( $response == count($remote_environments) ) ? $this->add_remote_environment() : $remote_environments[ $response - 1 ];
        }

        return $remote_environment_selected;
    }

    public function get_remote_environments() {
        
        $remote_environments = $this->get_config()->get( [ 'env', 'remote' ] );
        if( is_array($remote_environments) && count($remote_environments) > 0 ) {
            return array_keys($remote_environments);
        }
        
        return null;
    }

    public function add_remote_environment() {

        $new_environment = Terminal::readline( 'Name of the environment to add: ', false);
        $this->get_config()->set_data( [ 'env', 'remote', $new_environment ], [] );
        return $new_environment;
    }

}