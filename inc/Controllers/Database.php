<?php

namespace Wpextend\Cli\Controllers;

use Wpextend\Cli\Helpers\Render;
use Wpextend\Cli\Services\Database as DatabaseService;

class Database extends ControllerBase {

    private $databaseService;

    public function __construct() {
        
        parent::__construct();

        $this->databaseService = new DatabaseService();

    }

    public function up() {
        Render::output( 'Database init...', 'info');
        shell_exec( "cd docker && make database-up &>/dev/null" );
    }

    public function healthcheck() {
        return shell_exec( "cd docker && make -si database-healthcheck" );
    }

    public function import() {

        $db_sql_file_to_import = readline( Render::output( 'Database file ? (relative to "docker" dir)', 'heading', true, false ) );

        Render::output( 'Import database...' , 'info' );
        shell_exec( "cd docker && make mysql-import $db_sql_file_to_import" );
        Render::output( 'Done!' , 'success' );
    }

}