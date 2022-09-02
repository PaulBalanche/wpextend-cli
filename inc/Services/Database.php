<?php

namespace Wpextend\Cli\Services;

use Wpextend\Cli\Helpers\Render;
use Wpextend\Cli\Helpers\Terminal;

class Database extends ServiceBase {

    public function up() {
        Render::output( PHP_EOL . 'PHP & Database init...', 'info');
        shell_exec( "cd docker && make php-up &>/dev/null" );
        shell_exec( "cd docker && make database-up &>/dev/null" );
    }

    public function healthcheck() {
        return shell_exec( "cd docker && make -si database-healthcheck" );
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
            if ( ! file_exists( $this->get_config()->getCurrentWorkingDir() . '/docker/tmp' ) ) {
                mkdir( $this->get_config()->getCurrentWorkingDir() . '/docker/tmp' );
            }
            copy( $db_sql_file_to_import, $this->get_config()->getCurrentWorkingDir() . '/docker/tmp/' . $sql_filename );

            $this->import_command( 'tmp/' . $sql_filename );

            // Remove SQL file
            unlink( $this->get_config()->getCurrentWorkingDir() . '/docker/tmp/' . $sql_filename );

            break;

        } while(true);
    }

    public function import_remote_file() {

        $remote_db_host = Terminal::readline( 'Remote database HOST: ', false );
        $remote_db_name = Terminal::readline( 'Remote database NAME: ', false);
        $remote_db_user = Terminal::readline( 'Remote database USER: ', false );
        $remote_db_password = Terminal::read_password( 'Remote database PASSWORD: ', false);

        $sql_filename = $remote_db_name . '_' . date('c');

        // Create TMP dir and copy SQL file
        if ( ! file_exists( $this->get_config()->getCurrentWorkingDir() . '/docker/tmp' ) ) {
            mkdir( $this->get_config()->getCurrentWorkingDir() . '/docker/tmp' );
        }

        Render::output( 'Downloading database...' , 'info' );
        shell_exec( "cd docker && make php-up &>/dev/null" );
        shell_exec( "cd docker && make remote-mysqldump REMOTE_DB_HOST=$remote_db_host REMOTE_DB_USER=$remote_db_user REMOTE_DB_PASSWORD=$remote_db_password REMOTE_DB_NAME=$remote_db_name SQL_FILE=docker/tmp/$sql_filename" );
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
        shell_exec( "cd docker && make mysql-import $filename" );
        Render::output( 'Database successfully imported 🎉' , 'success' );
    }
}