<?php

namespace Wpextend\Cli\Controllers;

use Wpextend\Cli\Helpers\Terminal;

class Vanilla extends ControllerBase {

    public $name = 'Basic Wordpress',
        $preferedServerDocumentRoot = '/';

    public function check_before_run() {

        $this->check_wp_config();
    }

    public function check_wp_config() {

        if( ! file_exists( $this->get_config()->getCurrentWorkingDir() . '/wp-config.php' ) ) {
            $answer = Terminal::readline( '-- ' . $this->render_prefix() . ' wp-config.php file\'s missing. Continue anyway? (y/n) ', false );
            if( strtolower($answer) != 'y' ) {
                exit;
            }
        }
    }

    public function render_prefix() {

        return '[Basic Wordpress notice]';
    }
}