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

    public function get_data( $ids, $message = '', $default_value = null ) {

        $ids = ( ! is_array($ids) ) ? [ $ids ] : $ids;
        $value = $this->get( $ids );

        if( ! is_null($value) ) {
            Render::output( implode( ' > ', $ids ) . ' retrieved from WPE config file', 'normal' );
        }
        else {

            $id_last = $ids[count($ids) - 1];
            if( $_ENV && is_array($_ENV) && isset($_ENV[$id_last]) ) {
                Render::output( PHP_EOL . "We found $id_last into environment variables ($_ENV[$id_last])", 'normal', false );
                $response = Terminal::readline( 'Do you want to use it ? (y/n) ', false );
                if( $response == 'y' ) {
                    $value = $_ENV[$id_last];
                }
            }

            if( ! isset($value) ) {
                $message = ( ! empty($message) ) ? $message : $id_last;
                if( ! is_null($default_value) ) { $message .= " [$default_value]"; }
                $value = Terminal::readline( $message . ' : ', false );
                if( empty($value) && ! is_null($default_value) ) { $value = $default_value; }
            }

            $this->set_data( $ids, $value );
        }

        return $value;
    }

    public function get( $ids ) {
        
        $json_data = ( file_exists( $this->get_config_file_path() ) ) ? json_decode( file_get_contents( $this->get_config_file_path() ), true ) : [];

        $ids = ( ! is_array($ids) ) ? [ $ids ] : $ids;
        return $this->get_recursive_data( $ids, $json_data );
    }

    public function set_data( $ids, $value ) {
        
        $json_data = ( file_exists( $this->get_config_file_path() ) ) ? json_decode( file_get_contents( $this->get_config_file_path() ), true ) : [];
        
        $ids = ( ! is_array($ids) ) ? [ $ids ] : $ids;
        $this->set_recursive_data( $ids, $json_data, $value );

        file_put_contents( $this->get_config_file_path(), json_encode($json_data, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) );
    }

    public function get_recursive_data( $ids, $array ) {

        if( is_array($array) && is_array($ids) && count($ids) > 0 ) {

            $id = array_shift($ids);
            if( isset($array[$id]) ) {

                return ( count($ids) == 0 ) ? $array[$id] : $this->get_recursive_data( $ids, $array[$id] );
            }
        }
        
        return null;
    }

    public function set_recursive_data( $ids, &$array, $value ) {

        if( ! is_array($array) ) {
            $array = [];
        }
        if( count($ids) > 1 ) {

            $id = array_shift($ids);
            $this->set_recursive_data( $ids, $array[$id], $value );
        }
        else if ( count($ids) == 1 ) {
            $array[$ids[0]] = $value;
        }
    }

}