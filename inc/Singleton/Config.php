<?php

namespace Wpextend\Cli\Singleton;

class Config {
    
    private static $_instance;

    private $scriptDir,
            $currentWorkingDir;

    public function __construct() {
    
        $this->scriptDir = WPE_CLI_SCRIPT_DIR;
        $this->currentWorkingDir = getcwd();
    }

    /**
     * Utility method to retrieve the main instance of the class.
     * The instance will be created if it does not exist yet.
     * 
     */
    public static function getInstance() {

        if( is_null(self::$_instance) ) {
            self::$_instance = new Config();
        }
        return self::$_instance;
    }


    public function getScriptDir() {
        return $this->scriptDir;
    }
    
    public function getCurrentWorkingDir() {
        return $this->currentWorkingDir;
    }

}