<?php

namespace Wpextend\Cli\Controllers;

use Wpextend\Cli\Helpers\Render;
use Wpextend\Cli\Helpers\Terminal;

class Bedrock extends ControllerBase {

    public function check_before_run() {

        $this->check_env();
        $this->check_composer();
    }

    public function check_env() {

        if( ! file_exists( $this->get_config()->getCurrentWorkingDir() . '/.env' ) ) {
            Render::output( PHP_EOL . $this->render_prefix() . ' .env file\'s missing.' . PHP_EOL, 'warning' );
            die;
        }
    }
    
    public function check_composer() {

        if( ! file_exists( $this->get_config()->getCurrentWorkingDir() . '/vendor' ) ) {

           $answer = Terminal::readline( '-- ' . $this->render_prefix() . ' Vendor directory\'s missing. Do you want load Composer dependencies? (y/n) ', false );
           if( strtolower($answer) == 'y' ) {

               shell_exec( "cd docker && make php-up &>/dev/null" );
               shell_exec( "cd docker && make composer-install" );
           }
       }
    }

    public function render_prefix() {

        return '[Bedrock notice]';
    }

}