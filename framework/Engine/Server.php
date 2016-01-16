<?php
/**
 * Created by PhpStorm.
 * User: ZyManch
 * Date: 23.12.2015
 * Time: 11:19
 */
namespace LZ\Engine;

use LZ\App;
use LZ\Request\Base;
use LZ\Thread\Manager;

class Server {

    protected $_requestProvider;

    protected $_threadManager;

    public function __construct(Base\Provider $requestProvider, Manager $threadManager) {
        set_time_limit(0);
        ob_implicit_flush();
        $this->_requestProvider = $requestProvider;
        $this->_threadManager = $threadManager;
    }

    public function loop() {
        while(true) {
            $this->run();
        }
    }


    public function run() {
        if ($request = $this->_requestProvider->getNewRequest()) {
            $app = new App\Base($request);
            $thread = $this->_threadManager->getFreeThread();
            $thread->addApplication($app);
        }
        $this->_threadManager->tick();
    }

}