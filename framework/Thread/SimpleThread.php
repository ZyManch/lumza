<?php
/**
 * Created by PhpStorm.
 * User: ZyManch
 * Date: 23.12.2015
 * Time: 11:20
 */
namespace LZ\Thread;

class SimpleThread  extends Base {


    public function run() {
        $pid = pcntl_fork();
        switch ($pid) {
            case -1:
                return null;
            case 0:
                $pid = $this->getId();
                unset($this->_manager[$pid]);
                exit($pid);
                break;
            default:
                $this->_manager[$pid] = $this;
                return $pid;
        }
    }


    public function getId() {
        return getmypid();
    }

}