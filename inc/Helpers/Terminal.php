<?php

namespace Wpextend\Cli\Helpers;


class Terminal {

    static public function readline( $prompt, $newLine = true ) {

        Render::output( PHP_EOL . $prompt, 'heading', $newLine );
        return readline();
    }

    static public function read_password( $prompt, $newLine = true ) {

        Render::output( PHP_EOL . $prompt, 'heading', $newLine );
        $password = shell_exec( 'sh docker/bash/read_password.sh' );
        Render::output( PHP_EOL );
        return trim( $password, PHP_EOL );
    }

}