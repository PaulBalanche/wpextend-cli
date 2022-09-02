<?php

namespace Wpextend\Cli\Controllers;

class Shell extends ControllerBase {

    public function select( $options ) {
        return shell_exec( 'sh ' . $this->get_config()->getScriptDir() . '/bash/select.sh "' . implode('" "', $options) . '"' );
    }

    public function read_password() {
        return shell_exec( 'sh ' . $this->get_config()->getScriptDir() . '/bash/read_password.sh' );
    }

}