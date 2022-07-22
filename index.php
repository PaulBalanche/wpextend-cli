#!/usr/local/bin/php
<?php

define( 'WPE_CLI_SCRIPT_DIR', dirname(__FILE__) );

require WPE_CLI_SCRIPT_DIR . '/vendor/autoload.php';

Wpextend\Cli\Controllers\Main::getInstance();