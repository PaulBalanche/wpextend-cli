#!/usr/local/bin/php
<?php
define( 'WPE_CLI_SCRIPT_DIR', dirname(__FILE__) );

if( ! file_exists('vendor') ) {
    shell_exec( "composer install --working-dir=" . WPE_CLI_SCRIPT_DIR );
}
require WPE_CLI_SCRIPT_DIR . '/vendor/autoload.php';

Wpextend\Cli\Controllers\Main::getInstance()->init();