<?php

namespace Wpextend\Cli\Controllers;

use Wpextend\Cli\Helpers\Terminal;

class Environments extends ControllerBase {

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