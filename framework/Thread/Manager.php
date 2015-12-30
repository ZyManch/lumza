<?php
/**
 * Created by PhpStorm.
 * User: ZyManch
 * Date: 30.12.2015
 * Time: 14:03
 */
namespace LZ\Thread;

class Manager implements \Countable, \ArrayAccess {

    protected $_maxThreadCount;

    protected $_isThreadIsSupported;

    /** @var Base[]  */
    protected $_threads = array();

    protected $_processesPerThread;

    public function __construct($processesPerThread, $maxThreadCount = 1000) {
        $this->_maxThreadCount = $maxThreadCount;
        $this->_processesPerThread = $processesPerThread;
        $this->_isThreadIsSupported = PHP_OS != 'WINNT';
    }


    public function addThread() {
        if (sizeof($this->_threads) >= $this->_maxThreadCount) {
            return null;
        }
        if ($this->_isThreadIsSupported) {
            $thread = new PcntlThread($this);
        } else {
            $thread = new SimpleThread($this);
        }
        $thread->run();
        return $thread;
    }

    public function stopThread($id) {
        $thread = $this[$id];
        if ($thread) {
            $thread->stop();
        }
    }

    public function stopThreads() {
        foreach ($this->_threads as $thread) {
            $thread->stop();
        }
    }

    public function getFreeThread() {
        $tree = array();
        if (!$this->_threads) {
            return $this->addThread();
        }
        foreach ($this->_threads as $key => $thread) {
            $tree[$thread->getActiveApplicationCount()][] = $thread;
        }
        ksort($tree);
        $threads = reset($tree);
        $activeProcess = key($tree);
        if ($activeProcess >= $this->_processesPerThread) {
            return $this->addThread();
        }
        $rand = rand(0,sizeof($threads)-1);
        return $threads[$rand];
    }

    public function count() {
        return count($this->_threads);
    }

    public function offsetExists($offset) {
        return isset($this->_threads[$offset]);
    }

    public function offsetGet($offset) {
        return $this->offsetExists($offset) ? $this->_threads[$offset] : null;
    }

    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->_threads[] = $value;
        } else {
            $this->_threads[$offset] = $value;
        }
    }

    public function offsetUnset($offset) {
        unset($this->_threads[$offset]);
    }
}