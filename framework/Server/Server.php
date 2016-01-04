<?php
/**
 * Created by PhpStorm.
 * User: ZyManch
 * Date: 23.12.2015
 * Time: 11:19
 */
namespace LZ\Server;

use LZ\App\Base;
use LZ\Listener;
use LZ\Thread\Manager;

class Server {

    protected $_listener;

    protected $_threadManager;

    public function __construct(Listener\Base $listener, Manager $threadManager) {
        set_time_limit(0);
        ob_implicit_flush();
        $this->_listener = $listener;
        $this->_threadManager = $threadManager;
    }

    public function run() {
        while(true) {
            if ($request = $this->_listener->getNewRequest()) {
                $app = new Base($request);
                $thread = $this->_threadManager->getFreeThread();
                $thread->addApplication($app);
            }
        }
    }



}