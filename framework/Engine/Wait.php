<?php
/**
 * Created by PhpStorm.
 * User: ZyManch
 * Date: 16.01.2016
 * Time: 13:39
 */
namespace LZ\Engine;

use LZ\Exception;

class Wait {

    protected $_checkFunc;
    protected $_continueFunc;

    protected $_breakAfterTime;

    public function __construct($checkFunc, $continueFunc, $breakAfterMs) {
        $this->_checkFunc = $checkFunc;
        $this->_continueFunc = $continueFunc;
        $this->_breakAfterTime = microtime(true)+$breakAfterMs/1000;
        yield $this;
    }

    public function next() {
        $checkFunc = $this->_checkFunc;
        if ($checkFunc()) {
            $continueFunc = $this->_continueFunc;
            $continueFunc();
        } else if (microtime(true) > $this->_breakAfterTime) {
            throw new Exception\Wait('Time outed');
        }
    }

}