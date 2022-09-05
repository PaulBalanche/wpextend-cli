<?php

namespace Wpextend\Cli\Controllers;

use Wpextend\Cli\Helpers\Terminal;

class Bedrock extends ControllerBase {

    public $name = 'Bedrock',
        $preferedServerDocumentRoot = 'web';

    public function check_before_run() {

        $this->check_env();
        $this->check_composer();
    }

    public function check_env() {

        if( ! file_exists( $this->get_config()->getCurrentWorkingDir() . '/.env' ) ) {
            $answer = Terminal::readline( '-- ' . $this->render_prefix() . ' .env file\'s missing. Continue anyway? (y/n) ', false );
            if( strtolower($answer) != 'y' ) {
                exit;
            }
        }
    }
    
    public function check_composer() {

        if( ! file_exists( $this->get_config()->getCurrentWorkingDir() . '/vendor' ) ) {

           $answer = Terminal::readline( '-- ' . $this->render_prefix() . ' Vendor directory\'s missing. Do you want load Composer dependencies? (y/n) ', false );
           if( strtolower($answer) == 'y' ) {

               shell_exec( "cd " . $this->get_config()->getDockerDir() . " && make php-up &>/dev/null" );
               shell_exec( "cd " . $this->get_config()->getDockerDir() . " && make composer-install" );
           }
       }
    }

    public function render_prefix() {

        return '[Bedrock notice]';
    }

}