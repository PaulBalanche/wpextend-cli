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

    public function get_tmp_dirname() {
        return 'tmp';
    }

    /**
     * Create TMP dir and copy SQL file
     * 
     */
    public function ensure_tmp_exists() {
        
        if ( ! file_exists( $this->get_config()->getCurrentWorkingDir() . '/' . $this->get_config()->getDockerDir() . '/' . $this->get_tmp_dirname() ) ) {
            mkdir( $this->get_config()->getCurrentWorkingDir() . '/' . $this->get_config()->getDockerDir() . '/' . $this->get_tmp_dirname() );
        }
    }

    public function download_local_file() {

        do{
            $db_sql_file_to_import = trim( Terminal::readline( 'Database file (full local filename, for example ~/Desktop/dump.sql):' ) );
            if( ! $db_sql_file_to_import || ! file_exists($db_sql_file_to_import) ) {
                Render::output( 'File not found...', 'error' );
            }
        } while( ! $db_sql_file_to_import || ! file_exists($db_sql_file_to_import) );

        $sql_filename = 'local_imported_' . date('c');
        $this->ensure_tmp_exists();
        
        if( copy( $db_sql_file_to_import, $this->get_config()->getCurrentWorkingDir() . '/' . $this->get_config()->getDockerDir() . '/' . $this->get_tmp_dirname() . '/' . $sql_filename ) ) {
            return $this->get_tmp_dirname() . '/' . $sql_filename;
        }

        return false;
    }

    public function download_remote_file() {

        $remote_environment = Main::getInstance()->environmentsController->choose_remote_environment();

        $remote_db_host = $this->get_config()->get_data( [ 'env', 'remote', $remote_environment, 'database', 'db_host' ], '[' . $remote_environment . '] Database HOST' );
        $remote_db_name = $this->get_config()->get_data( [ 'env', 'remote', $remote_environment, 'database', 'db_name' ], '[' . $remote_environment . '] Database NAME' );
        $remote_db_user = $this->get_config()->get_data( [ 'env', 'remote', $remote_environment, 'database', 'db_user' ], '[' . $remote_environment . '] Database USER' );
        $remote_db_password = Terminal::read_password( '[' . $remote_environment . '] Database PASSWORD: ', false);

        if( empty($remote_db_host) || empty($remote_db_name) || empty($remote_db_user) || empty($remote_db_password) ) {

            Render::output( 'Some information seems empty... Please provide correct database credentials.' , 'error' );
            return false;
        }

        $sql_filename = $remote_db_name . '_' . date('c');
        $this->ensure_tmp_exists();

        Render::output( 'Downloading database...' , 'info' );
        shell_exec( "cd " . $this->get_config()->getDockerDir() . " && make php-up &>/dev/null" );
        $remote_db_download = shell_exec( "cd " . $this->get_config()->getDockerDir() . " && make -si remote-mysqldump REMOTE_DB_HOST=$remote_db_host REMOTE_DB_USER=$remote_db_user REMOTE_DB_PASSWORD=$remote_db_password REMOTE_DB_NAME=$remote_db_name SQL_FILE=" . $this->get_config()->getDockerDir() . "/" . $this->get_tmp_dirname() . "/$sql_filename" );
        if( ! $remote_db_download || strpos( $remote_db_download, 'Error' ) !== false || strpos( $remote_db_download, 'Unknown database' ) !== false ) {
            Render::output( 'An error occurs while downloading remote database... Try another way to import database.' , 'error' );
            unlink(  $this->get_config()->getDockerDir() . "/" . $this->get_tmp_dirname() . "/" . $sql_filename );
            return false;
        }
        
        Render::output( 'Database downloaded!' , 'success' );
        
        return $this->get_tmp_dirname() . '/' . $sql_filename;
    }

    public function import_command( $filename ) {

        if( file_exists( $this->get_config()->getCurrentWorkingDir() . '/' . $this->get_config()->getDockerDir() . '/' . $filename ) ) {

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
            $mysql_import = shell_exec( "cd " . $this->get_config()->getDockerDir() . " && make mysql-import $filename" );
            if( ! $mysql_import || strpos( $mysql_import, 'Error' ) !== false ) {
                Render::output( 'An error occurs while importing database...' , 'error' );
                exit;
            }

            Render::output( 'Database successfully imported 🎉' , 'success' );
        }
        else {
            Render::output( $this->get_config()->getCurrentWorkingDir() . '/' . $this->get_config()->getDockerDir() . '/' . $filename . ': file not found...' , 'error' );
            exit;
        }
    }

}