<?php

namespace Wpextend\Cli\Controllers;

use Wpextend\Cli\Helpers\Render;
use Wpextend\Cli\Helpers\Terminal;

class Git extends ControllerBase {


    public function __construct() {
        
        parent::__construct();
    }

    public function pull_from_bitbucket() {

        $remote_ssh_host = Terminal::readline( 'Bitbucket SSH URL ?' );
        system( "git clone $remote_ssh_host ./git_clone" );

        $files_git_clone = scandir( $this->get_config()->getCurrentWorkingDir() . '/git_clone' );
        foreach( $files_git_clone as $file ) {
            if( ! in_array( $file, [ '.', '..', 'docker' ] ) ) {
                shell_exec( 'mv git_clone/' . $file . ' ./' . $file );
            }
            
        }
        shell_exec( 'rm -r git_clone' );
    }

    public function branch_choice() {

        Render::output( PHP_EOL . 'Available branches :' . PHP_EOL, 'heading');

        $branches = shell_exec( 'git branch -a' );
        $branches = explode( PHP_EOL, $branches);
        foreach( $branches as $branche) {
            if( strpos($branche, 'remotes/origin/') !== false && strpos($branche, 'HEAD') === false ) {
                Render::output( str_replace('remotes/origin/', '', $branche) , 'info' );
            }
        }
        $branch_to_clone = Terminal::readline( 'Which branch ?', 'heading' );
        shell_exec( 'git checkout ' . $branch_to_clone );
        shell_exec( 'git pull origin ' . $branch_to_clone );
    }

}