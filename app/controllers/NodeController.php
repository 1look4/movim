<?php

use Movim\Controller\Base;

class NodeController extends Base
{
    public function load()
    {
        $this->session_only = false;
        $this->public = true;
    }

    public function dispatch()
    {
        $this->page->setTitle(__('page.communities'));
        $this->page->disableJavascriptCheck();
    }
}
