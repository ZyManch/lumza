<?php
/**
 * Created by PhpStorm.
 * User: ZyManch
 * Date: 23.12.2015
 * Time: 11:20
 */
namespace LZ\Thread;

class PcntlThread extends Base {

    protected $_id;

    public function run() {
        $pid = pcntl_fork();
        switch ($pid) {
            case -1:
                return null;
            case 0:
                $pid = getmypid();
                unset($this->_manager[$pid]);
                exit($pid);
                break;
            default:
                $this->_id = $pid;
                $this->_manager[$pid] = $this;
                return $pid;
        }
    }


    public function getId() {
        return $this->_id;
    }

}