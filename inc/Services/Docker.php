<?php

namespace Wpextend\Cli\Services;

use \Wpextend\Cli\Helpers\Render;

class Docker extends ServiceBase {

    public function downloadDockerFiles() {
        
        Render::output( 'Copying docker files...', 'info' );

        $src = $this->get_config()->getScriptDir() . '/docker';
        $dest = $this->get_config()->getCurrentWorkingDir() . '/docker';
        
        shell_exec( "cp -r $src $dest" );

        if( ! file_exists( $this->get_config()->getCurrentWorkingDir() . '/docker' ) ) {
            Render::output( 'Sorry an error occurs while copying files...' , 'error' );
            exit;
        }
        
        Render::output( 'Files successfully copied.' , 'success' );
    }

    public function setup() {

        $PROJECT_NAME = 'test1';
        $WP_HOME = 'test2';
        $HTTP_PROTOCOL = 'test3';
        $PUBLIC_PORT = 'test4';
        $SERVER_DOCUMENT_ROOT = 'test5';
        $DB_NAME = 'test6';
        $DB_USER = 'test7';
        $DB_PASSWORD = 'test8';
        $DB_HOST = 'test9';

        $env_content = file_get_contents( $this->get_config()->getCurrentWorkingDir() . '/docker/.env.example' );
        $env_content = str_replace( 'PROJECT_NAME=PROJECT_NAME', 'PROJECT_NAME=' . $PROJECT_NAME, $env_content );
        $env_content = str_replace( 'PROJECT_BASE_URL=PROJECT_BASE_URL', 'PROJECT_BASE_URL=' . $WP_HOME, $env_content );
        $env_content = str_replace( 'PROJECT_HTTP_PROTOCOL=PROJECT_HTTP_PROTOCOL', 'PROJECT_HTTP_PROTOCOL=' . $HTTP_PROTOCOL, $env_content );
        $env_content = str_replace( 'PROJET_PUBLIC_PORT=PROJET_PUBLIC_PORT', 'PROJET_PUBLIC_PORT=' . $PUBLIC_PORT, $env_content );
        $env_content = str_replace( 'SERVER_DOCUMENT_ROOT=SERVER_DOCUMENT_ROOT', 'SERVER_DOCUMENT_ROOT=' . $SERVER_DOCUMENT_ROOT, $env_content );
        $env_content = str_replace( 'DB_NAME=DB_NAME', 'DB_NAME=' . $DB_NAME, $env_content );
        $env_content = str_replace( 'DB_USER=DB_USER', 'DB_USER=' . $DB_USER, $env_content );
        $env_content = str_replace( 'DB_PASSWORD=DB_PASSWORD', 'DB_PASSWORD=' . $DB_PASSWORD, $env_content );
        $env_content = str_replace( 'DB_ROOT_PASSWORD=DB_ROOT_PASSWORD', 'DB_ROOT_PASSWORD=' . $DB_PASSWORD, $env_content );
        $env_content = str_replace( 'DB_HOST=DB_HOST', 'DB_HOST=' . $DB_HOST, $env_content );
        
        file_put_contents( $this->get_config()->getCurrentWorkingDir() . '/docker/.env', $env_content );
    }

}