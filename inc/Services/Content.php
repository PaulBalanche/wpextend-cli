<?php

namespace Wpextend\Cli\Services;

use Wpextend\Cli\Helpers\Render;
use Wpextend\Cli\Helpers\Terminal;

class Content extends ServiceBase {

    public function downloadUploads() {

        $remote_ssh_host = Terminal::readline( 'Remote SSH HOST: ', false );
        $remote_ssh_user = Terminal::readline( 'Remote SSH USER: ', false );

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