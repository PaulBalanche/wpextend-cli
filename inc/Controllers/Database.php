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

        shell_exec( "cd docker && make database-up" );
    }

}