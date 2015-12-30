<?php
/**
 * Created by PhpStorm.
 * User: ZyManch
 * Date: 23.12.2015
 * Time: 13:13
 */
namespace SF\Listener;

abstract class Base {

    protected $_dns;

    protected $_maxClients;

    public function __construct($dns, $maxClients = 128) {
        $this->_dns = $dns;
        $this->_maxClients = $maxClients;
        $this->init();
    }

    abstract function init();

    abstract function getNewRequest();

    abstract function checkClosedRequests();

}