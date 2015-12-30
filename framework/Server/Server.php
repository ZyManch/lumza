<?php
/**
 * Created by PhpStorm.
 * User: ZyManch
 * Date: 23.12.2015
 * Time: 11:19
 */
namespace SF\Server;

use SF\Listener;

class Server {

    protected $_listener;

    protected $_requests = array();

    public function __construct(Listener\Base $listener) {
        set_time_limit(0);
        ob_implicit_flush();
        $this->_listener = $listener;
    }

    public function run() {
        while(true) {
            if ($request = $this->_listener->getNewRequest()) {
                $this->_requests[] = $request;
            }
            $this->_listener->checkClosedRequests();
            sleep(1);
        }
    }



}