<?php

namespace Wpextend\Cli\Singleton;

use Wpextend\Cli\Helpers\Render;
use Wpextend\Cli\Helpers\Terminal;
use Dotenv\Dotenv;

class Config {
    
    private static $_instance;

    private $scriptDir,
            $currentWorkingDir,
            $config_json_filename = 'wpe.conf.json',
            $docker_dirname = 'wpe-docker';

    private function __construct() {
    
        $this->scriptDir = WPE_CLI_SCRIPT_DIR;
        $this->currentWorkingDir = getcwd();

        if( file_exists( $this->getCurrentWorkingDir() . '/.env' ) ) {
            $dotenv = Dotenv::createImmutable( [ $this->getCurrentWorkingDir(), $this->getCurrentWorkingDir() . '/' . $this->getDockerDir() ] );
            $dotenv->load();
        }
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

    public function getDockerDir() {
        return $this->docker_dirname;
    }

    public function get_config_file_path() {
        return $this->getCurrentWorkingDir() . '/' . $this->config_json_filename;
    }

    public function get_data( $id, $message = '', $default_value = null ) {

        $value = $this->get( $id );

        if( ! is_null($value) ) {
            Render::output( $id . ' get from WPE config file', 'info' );
        }
        else {

            if( $_ENV && is_array($_ENV) && isset($_ENV[$id]) ) {
                Render::output( "We found $id into environment variables ($_ENV[$id])", 'info' );
                $response = Terminal::readline( 'Do you want to use it ? (y/n) ', false );
                if( $response == 'y' ) {
                    $value = $_ENV[$id];
                }
            }

            if( ! isset($value) ) {
                $message = ( ! empty($message) ) ? $message : $id;
                if( ! is_null($default_value) ) { $message .= " [$default_value]"; }
                $value = Terminal::readline( $message . ' : ', false );
                if( empty($value) && ! is_null($default_value) ) { $value = $default_value; }
            }

            $this->set_data( $id, $value );
        }

        return $value;
    }

    public function get( $id ) {
        
        $json_data = ( file_exists( $this->get_config_file_path() ) ) ? json_decode( file_get_contents( $this->get_config_file_path() ), true ) : [];
        return ( isset( $json_data[$id] ) ) ? $json_data[$id] : null;
    }

    public function set_data( $id, $value ) {
        
        $json_data = ( file_exists( $this->get_config_file_path() ) ) ? json_decode( file_get_contents( $this->get_config_file_path() ), true ) : [];
        $json_data[ $id ] = $value;

        file_put_contents( $this->get_config_file_path(), json_encode($json_data, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) );
    }

}