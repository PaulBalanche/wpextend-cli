<?php

namespace Wpextend\Cli\Controllers;

use Wpextend\Cli\Helpers\Render;

class Shell extends ControllerBase {

    public function select( $question, $options ) {

        do {
            Render::output( PHP_EOL . '-- ' . $question . PHP_EOL, 'heading');
            $response = shell_exec( 'sh ' . $this->get_config()->getScriptDir() . '/bash/select.sh "' . implode('" "', $options) . '"' );
        } while( ! is_numeric($response) || ! array_key_exists($response - 1, $options) );

        return $response;
    }

    public function read_password() {
        return shell_exec( 'sh ' . $this->get_config()->getScriptDir() . '/bash/read_password.sh' );
    }

}