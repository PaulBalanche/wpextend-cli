<?php

namespace Wpextend\Cli\Services;

use Wpextend\Cli\Helpers\Render;

class Database extends ServiceBase {

    public function up() {
        Render::output( 'PHP & Database init...', 'info');
        shell_exec( "cd docker && make php-up &>/dev/null" );
        shell_exec( "cd docker && make database-up &>/dev/null" );
    }

    public function healthcheck() {
        return shell_exec( "cd docker && make -si database-healthcheck" );
    }

    public function import_local_file() {

        do{
            $db_sql_file_to_import = readline( Render::output( PHP_EOL . 'Database file ? (full local filename, for example ~/Desktop/dump.sql)', 'heading', true, false ) );
            if( ! file_exists($db_sql_file_to_import) ) {
                Render::output( "File not found..." , 'warning' );
                continue;
            }

            // Create TMP dir and copy SQL file
            if ( ! file_exists( $this->get_config()->getCurrentWorkingDir() . '/docker/tmp' ) ) {
                mkdir( $this->get_config()->getCurrentWorkingDir() . '/docker/tmp' );
            }
            copy( $db_sql_file_to_import, $this->get_config()->getCurrentWorkingDir() . '/docker/tmp/dump' );

            $this->import_command();

            // Remove TMP dir and SQL file
            unlink( $this->get_config()->getCurrentWorkingDir() . '/docker/tmp/dump' );
            rmdir( $this->get_config()->getCurrentWorkingDir() . '/docker/tmp' );

            break;

        } while(true);
    }

    public function import_remote_file() {

        // REMOTE_DB_HOST=$(get_config REMOTE_DB_HOST "Remote database host")
        // REMOTE_DB_NAME=$(get_config REMOTE_DB_NAME "Remote database name")
        // REMOTE_DB_USER=$(get_config REMOTE_DB_USER "Remote database user")
        // REMOTE_DB_PASSWORD=$(get_secret REMOTE_DB_PASSWORD "Remote database password")

        // SQL_FILENAME="docker/mariadb-init/"$REMOTE_DB_NAME"_"$(date +"%m_%d_%y_%H%M%S")".sql"

        // cd docker
        // make remote-mysqldump REMOTE_DB_HOST=$REMOTE_DB_HOST REMOTE_DB_USER=$REMOTE_DB_USER REMOTE_DB_PASSWORD=$REMOTE_DB_PASSWORD REMOTE_DB_NAME=$REMOTE_DB_NAME SQL_FILE=$SQL_FILENAME
        // cd ..
    }

    public function import_command() {

        $this->up();
        
        do {    
            $db_healthcheck = $this->healthcheck();
            if( $db_healthcheck && strpos( $db_healthcheck, 'Error' ) === false && strpos( $db_healthcheck, 'Unknown database' ) === false ) {
                Render::output( 'Database\'s ready!', 'success');
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

        Render::output( 'Import database...' , 'info' );
        shell_exec( "cd docker && make mysql-import tmp/dump" );
        Render::output( 'Done!' , 'success' );
    }
}