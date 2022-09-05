<?php

namespace Wpextend\Cli\Services;

use Wpextend\Cli\Helpers\Render;
use Wpextend\Cli\Helpers\Terminal;
use Wpextend\Cli\Controllers\Main;
use ZipArchive;

class Docker extends ServiceBase {

    public function downloadDockerFiles() {
        
        // Curl download
        Render::output( PHP_EOL. 'Downloading WPE Docker...', 'info' );
        $fh = fopen( $this->get_config()->getCurrentWorkingDir() . '/' . $this->get_config()->getDockerDir() . '.zip', 'w' );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://github.com/PaulBalanche/wpe-docker/archive/refs/heads/master.zip");
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_FILE, $fh);
        curl_exec($ch);
        curl_close($ch);
        fclose($fh);

        // Zip extract archive
        $zip = new ZipArchive;
        if( $zip->open( $this->get_config()->getCurrentWorkingDir() . '/' . $this->get_config()->getDockerDir() . '.zip' ) ){

            $zip->extractTo( $this->get_config()->getCurrentWorkingDir() );
            $zip->close();

            // Rename and remove ZIP archive
            rename( $this->get_config()->getCurrentWorkingDir() . '/wpe-docker-master', $this->get_config()->getCurrentWorkingDir() . '/' . $this->get_config()->getDockerDir() );
            unlink( $this->get_config()->getCurrentWorkingDir() . '/' . $this->get_config()->getDockerDir() . '.zip' );

            if( file_exists( $this->get_config()->getCurrentWorkingDir() . '/' . $this->get_config()->getDockerDir() ) ) {
                Render::output( 'WPE Docker successfully downloaded 🎉' . PHP_EOL, 'success' );
                return;
            }
        }

        Render::output( 'Sorry an error occurs while dowloading WPE-Docker...' , 'error' );
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

        if( property_exists( Main::getInstance()->boilerplateController->getTypeInstance(), 'preferedServerDocumentRoot' ) ) {
            
            Render::output( PHP_EOL . 'You\'re using ' . sprintf("\033[1;33m%s\033[0;33m", Main::getInstance()->boilerplateController->getTypeInstance()->name) . ', and the default document root is ' . sprintf("\033[1;33m%s\033[0;33m", Main::getInstance()->boilerplateController->getTypeInstance()->preferedServerDocumentRoot) . '.', 'info', false );
            $answer = Terminal::readline( '-- Do you want to use this default document root? (y/n) ', false );
            if( strtolower($answer) == 'y' ) {
                $server_document_root = Main::getInstance()->boilerplateController->getTypeInstance()->preferedServerDocumentRoot;
            }
            else {
                $server_document_root  = $this->get_config()->get_data( 'server_document_root', 'Server document root (leave empty if root)', );
            }
        }
        if( ! empty($server_document_root) ) { $server_document_root = '/' . trim( $server_document_root, '/'); }

        $env_content = file_get_contents( $this->get_config()->getCurrentWorkingDir() . '/' . $this->get_config()->getDockerDir() . '/.env.example' );
        $env_content = str_replace( 'PROJECT_NAME=PROJECT_NAME', 'PROJECT_NAME=' . $project_name, $env_content );
        $env_content = str_replace( 'PROJECT_BASE_URL=PROJECT_BASE_URL', 'PROJECT_BASE_URL=' . $project_base_url, $env_content );
        $env_content = str_replace( 'PROJECT_HTTP_PROTOCOL=PROJECT_HTTP_PROTOCOL', 'PROJECT_HTTP_PROTOCOL=' . $http_protocol, $env_content );
        $env_content = str_replace( 'PROJET_PUBLIC_PORT=PROJET_PUBLIC_PORT', 'PROJET_PUBLIC_PORT=' . $public_port, $env_content );
        $env_content = str_replace( 'SERVER_DOCUMENT_ROOT=SERVER_DOCUMENT_ROOT', 'SERVER_DOCUMENT_ROOT=' . $server_document_root, $env_content );
        $env_content = str_replace( 'DB_NAME=DB_NAME', 'DB_NAME=' . $db_name, $env_content );
        $env_content = str_replace( 'DB_USER=DB_USER', 'DB_USER=' . $db_user, $env_content );
        $env_content = str_replace( 'DB_PASSWORD=DB_PASSWORD', 'DB_PASSWORD=' . $db_password, $env_content );
        $env_content = str_replace( 'DB_ROOT_PASSWORD=DB_ROOT_PASSWORD', 'DB_ROOT_PASSWORD=' . $db_password, $env_content );
        $env_content = str_replace( 'DB_HOST=DB_HOST', 'DB_HOST=' . $db_host, $env_content );
        
        file_put_contents( $this->get_config()->getCurrentWorkingDir() . '/' . $this->get_config()->getDockerDir() . '/.env', $env_content );

        Render::output( PHP_EOL . 'Docker\'s ready 🎉' . PHP_EOL, 'success' );
    }

    public function up() {
        
        shell_exec( "cd " . $this->get_config()->getDockerDir() . " && make" );
    }

}