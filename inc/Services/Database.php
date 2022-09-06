<?php

namespace Wpextend\Cli\Services;

use Wpextend\Cli\Helpers\Render;
use Wpextend\Cli\Helpers\Terminal;
use Wpextend\Cli\Controllers\Main;

class Database extends ServiceBase {

    public function up() {
        Render::output( PHP_EOL . 'PHP & Database init...', 'info');
        shell_exec( "cd " . $this->get_config()->getDockerDir() . " && make php-up &>/dev/null" );
        shell_exec( "cd " . $this->get_config()->getDockerDir() . " && make database-up &>/dev/null" );
    }

    public function healthcheck() {
        return shell_exec( "cd " . $this->get_config()->getDockerDir() . " && make -si database-healthcheck" );
    }

    public function import_local_file() {

        do{
            $db_sql_file_to_import = Terminal::readline( 'Database file ? (full local filename, for example ~/Desktop/dump.sql)' );
            if( ! file_exists($db_sql_file_to_import) ) {
                Render::output( 'File not found...' , 'warning' );
                continue;
            }

            $sql_filename = 'dump';

            // Create TMP dir and copy SQL file
            if ( ! file_exists( $this->get_config()->getCurrentWorkingDir() . '/' . $this->get_config()->getDockerDir() . '/tmp' ) ) {
                mkdir( $this->get_config()->getCurrentWorkingDir() . '/' . $this->get_config()->getDockerDir() . '/tmp' );
            }
            copy( $db_sql_file_to_import, $this->get_config()->getCurrentWorkingDir() . '/' . $this->get_config()->getDockerDir() . '/tmp/' . $sql_filename );

            $this->import_command( 'tmp/' . $sql_filename );

            // Remove SQL file
            unlink( $this->get_config()->getCurrentWorkingDir() . '/' . $this->get_config()->getDockerDir() . '/tmp/' . $sql_filename );

            break;

        } while(true);
    }

    public function import_remote_file() {

        $remote_environments = Main::getInstance()->environmentsController->get_remote_environments();
        if( ! is_null($remote_environments) ) {
            $remote_environments[] = 'Add a new environment...';

            $response = Main::getInstance()->shellController->select( 'Please select the environment?', $remote_environments );

            if( $response == count($remote_environments) ) {
                $new_environment = Main::getInstance()->environmentsController->add_remote_environment();
                $remote_environments[ count($remote_environments) - 1 ] = $new_environment;
            }
        }

        $remote_db_host = $this->get_config()->get_data( [ 'env', 'remote', $remote_environments[$response-1], 'database', 'db_host' ], '[' . $remote_environments[$response-1] . '] Database HOST: ' );
        $remote_db_name = $this->get_config()->get_data( [ 'env', 'remote', $remote_environments[$response-1], 'database', 'db_name' ], '[' . $remote_environments[$response-1] . '] Database NAME: ' );
        $remote_db_user = $this->get_config()->get_data( [ 'env', 'remote', $remote_environments[$response-1], 'database', 'db_user' ], '[' . $remote_environments[$response-1] . '] Database USER: ' );
        $remote_db_password = Terminal::read_password( '[' . $remote_environments[$response-1] . '] Database PASSWORD: ', false);

        if( empty($remote_db_host) || empty($remote_db_name) || empty($remote_db_user) || empty($remote_db_password) ) {

            Render::output( 'Some information seems empty... Please provide correct database credentials.' , 'error' );
            exit;
        }

        $sql_filename = $remote_db_name . '_' . date('c');

        // Create TMP dir and copy SQL file
        if ( ! file_exists( $this->get_config()->getCurrentWorkingDir() . '/' . $this->get_config()->getDockerDir() . '/tmp' ) ) {
            mkdir( $this->get_config()->getCurrentWorkingDir() . '/' . $this->get_config()->getDockerDir() . '/tmp' );
        }

        Render::output( 'Downloading database...' , 'info' );
        shell_exec( "cd " . $this->get_config()->getDockerDir() . " && make php-up &>/dev/null" );
        $remote_db_download = shell_exec( "cd " . $this->get_config()->getDockerDir() . " && make -si remote-mysqldump REMOTE_DB_HOST=$remote_db_host REMOTE_DB_USER=$remote_db_user REMOTE_DB_PASSWORD=$remote_db_password REMOTE_DB_NAME=$remote_db_name SQL_FILE=" . $this->get_config()->getDockerDir() . "/tmp/$sql_filename" );
        if( ! $remote_db_download || strpos( $remote_db_download, 'Error' ) !== false || strpos( $remote_db_download, 'Unknown database' ) !== false ) {
            Render::output( 'An error occurs while downloading remote database... Try to import local database instead.' , 'error' );
            exit;
        }
        
        Render::output( 'Database!' , 'success' );
        $this->import_command( 'tmp/' . $sql_filename );
    }

    public function import_command( $filename ) {

        $this->up();
        
        do {    
            $db_healthcheck = $this->healthcheck();
            if( $db_healthcheck && strpos( $db_healthcheck, 'Error' ) === false && strpos( $db_healthcheck, 'Unknown database' ) === false ) {
                Render::output( PHP_EOL . 'Database\'s ready!', 'success');
                break;
            }
            $x = ( isset($x) ) ? $x + 1 : 0;
            if( $x >= 9 ) {
                Render::output( 'An error occurs with the database...', 'error');
                exit;
                break;
            }
            sleep(1);
        } while( true );

        Render::output( 'Database import in progress...' , 'info' );
        shell_exec( "cd " . $this->get_config()->getDockerDir() . " && make mysql-import $filename" );
        Render::output( 'Database successfully imported 🎉' , 'success' );
    }
}