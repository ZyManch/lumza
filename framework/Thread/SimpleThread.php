<?php
/**
 * Created by PhpStorm.
 * User: ZyManch
 * Date: 23.12.2015
 * Time: 11:20
 */
namespace LZ\Thread;

use \LZ\App;


class SimpleThread  extends Base {

    protected $_id;

    public function run() {
        $pid = $this->getId();
        $this->_manager[$pid] = $this;
        return $pid;
    }

    public function addApplication(App\Base $app) {
        parent::addApplication($app);
        $app->run();
        $this->stop();
    }

    public function getId() {
        if (is_null($this->_id)) {
            $this->_id = uniqid();
        }
        return $this->_id;
    }

}