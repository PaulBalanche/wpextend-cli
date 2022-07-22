<?php

namespace Wpextend\Cli\Services;

use Wpextend\Cli\Helpers\Render;

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

        $project_name           = basename( $this->get_config()->getCurrentWorkingDir() );

        $wp_home                = $this->get_config()->get_data( 'WP_HOME', 'Site URL', "http://$project_name.local.buzzbrothers.ch:8000");

        $parsed_url = parse_url($wp_home);
        $project_base_url       = $parsed_url['host'];
        $http_protocol          = $parsed_url['scheme'];
        $public_port            = $parsed_url['port'];

        $db_name                = $this->get_config()->get_data( 'DB_NAME', 'DB name', 'wordpress' );
        $db_user                = $this->get_config()->get_data( 'DB_USER', 'DB user', 'wordpress' );
        $db_password            = $this->get_config()->get_data( 'DB_PASSWORD', 'DB password', 'wordpress' );
        $db_host                = $this->get_config()->get_data( 'DB_HOST', 'DB host', 'mariadb');
        $db_prefix              = $this->get_config()->get_data( 'DB_PREFIX', 'DB prefix', 'wp_');

        $server_document_root   = $this->get_config()->get_data( 'server_document_root', 'Server document root (leave empty if root)', );
        if( ! empty($server_document_root) ) { $server_document_root = '/' . trim( $server_document_root, '/'); }

        $env_content = file_get_contents( $this->get_config()->getCurrentWorkingDir() . '/docker/.env.example' );
        $env_content = str_replace( 'PROJECT_NAME=PROJECT_NAME', 'PROJECT_NAME=' . $project_name, $env_content );
        $env_content = str_replace( 'PROJECT_BASE_URL=PROJECT_BASE_URL', 'PROJECT_BASE_URL=' . $wp_home, $env_content );
        $env_content = str_replace( 'PROJECT_HTTP_PROTOCOL=PROJECT_HTTP_PROTOCOL', 'PROJECT_HTTP_PROTOCOL=' . $http_protocol, $env_content );
        $env_content = str_replace( 'PROJET_PUBLIC_PORT=PROJET_PUBLIC_PORT', 'PROJET_PUBLIC_PORT=' . $public_port, $env_content );
        $env_content = str_replace( 'SERVER_DOCUMENT_ROOT=SERVER_DOCUMENT_ROOT', 'SERVER_DOCUMENT_ROOT=' . $server_document_root, $env_content );
        $env_content = str_replace( 'DB_NAME=DB_NAME', 'DB_NAME=' . $db_name, $env_content );
        $env_content = str_replace( 'DB_USER=DB_USER', 'DB_USER=' . $db_user, $env_content );
        $env_content = str_replace( 'DB_PASSWORD=DB_PASSWORD', 'DB_PASSWORD=' . $db_password, $env_content );
        $env_content = str_replace( 'DB_ROOT_PASSWORD=DB_ROOT_PASSWORD', 'DB_ROOT_PASSWORD=' . $db_password, $env_content );
        $env_content = str_replace( 'DB_HOST=DB_HOST', 'DB_HOST=' . $db_host, $env_content );
        
        file_put_contents( $this->get_config()->getCurrentWorkingDir() . '/docker/.env', $env_content );
    }

    public function up() {
        shell_exec( "cd docker && make" );
    }

}