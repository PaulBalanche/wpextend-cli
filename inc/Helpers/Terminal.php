<?php

namespace Wpextend\Cli\Helpers;

use Wpextend\Cli\Controllers\Main;

class Terminal {

    static public function readline( $prompt, $newLine = true ) {

        Render::output( PHP_EOL . $prompt, 'heading', $newLine );
        return readline();
    }

    static public function read_password( $prompt, $newLine = true ) {

        Render::output( PHP_EOL . $prompt, 'heading', $newLine );
        $password = Main::getInstance()->shellController->read_password();
        Render::output( PHP_EOL );
        return trim( $password, PHP_EOL );
    }

}