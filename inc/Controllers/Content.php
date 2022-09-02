<?php

namespace Wpextend\Cli\Controllers;

use Wpextend\Cli\Helpers\Render;
use Wpextend\Cli\Services\Content as contentService;

class Content extends ControllerBase {

    private $contentService;

    public function __construct() {
        
        parent::__construct();

        $this->contentService = new contentService();

    }

    public function downloadUploads() {

        $this->contentService->downloadUploads();
    }

}