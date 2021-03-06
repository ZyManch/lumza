<?php
/**
 * Created by PhpStorm.
 * User: ZyManch
 * Date: 30.12.2015
 * Time: 14:42
 */
namespace LZ\Thread;

use \LZ\App;

abstract class Base {

    protected $_manager;

    protected $_stopped = false;

    /** @var App\Base[] */
    protected $_applications = array();

    public function __construct(Manager $manager) {
        $this->_manager = $manager;
    }

    public function stop() {
        unset($this->_manager[$this->getId()]);
        $this->_stopped = true;
    }

    public function addApplication(App\Base $app) {
        $this->_applications[] = $app;
    }

    public function getActiveApplicationCount() {
        return sizeof($this->_applications);
    }

    abstract public function run();


    abstract function getId();

}