<?php

namespace Wpextend\Cli\Services;

use Wpextend\Cli\Helpers\Render;
use Wpextend\Cli\Helpers\Terminal;
use Wpextend\Cli\Controllers\Main;

class Content extends ServiceBase {

    public function downloadUploads() {

        $remote_environment = Main::getInstance()->environmentsController->choose_remote_environment();

        $remote_ssh_host = $this->get_config()->get_data( [ 'env', 'remote', $remote_environment, 'ssh', 'host' ], '[' . $remote_environment . '] Remote SSH HOST' );
        $remote_ssh_user = $this->get_config()->get_data( [ 'env', 'remote', $remote_environment, 'ssh', 'user' ], '[' . $remote_environment . '] Remote SSH USER' );

        if( empty($remote_ssh_host) || empty($remote_ssh_user) ) {

            Render::output( 'Some information seems empty... Please provide correct SSH credentials.' , 'error' );
            return false;
        }

        $remote_source_path = rtrim( Terminal::readline( 'Source path (remote): ', false), '/' );

        do{
            $local_target_path = Terminal::readline( 'Target path (local) relative to ' . $this->get_config()->getCurrentWorkingDir() . ': ', false);
            
            if( ! file_exists($local_target_path) ) {
                Render::output( "Target path not found..." , 'warning' );
                continue;
            }

            system( "rsync -avz $remote_ssh_user@$remote_ssh_host:$remote_source_path/ $local_target_path" );
            Render::output( 'Done!' , 'success' );

            break;
        } while(true);

    }

}