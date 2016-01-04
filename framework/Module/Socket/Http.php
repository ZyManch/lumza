<?php
/**
 * Created by PhpStorm.
 * User: Елена
 * Date: 01.01.2016
 * Time: 16:28
 */
namespace LZ\Module\Socket;

use \LZ\Exception;

class Http {

    protected $_socket;

    public $closed = false;

    public $writable;

    public $body;

    public function __construct($socket){
        socket_set_block($socket);
        $this->_socket = $socket;
        $this->updateStatus();
    }

    public function updateStatus() {
        if ($this->closed) {
            return;
        }
        $read = array($this->_socket);
        $write = array($this->_socket);
        if (!socket_select($read, $write, $null, null)) {
            throw new Exception\Socket(socket_strerror(socket_last_error($this->_socket)));
        }
        $this->writable = sizeof($write)>0;
        $this->body = null;
        if (sizeof($read)>0) {
            $this->_read();
        }
    }

    public function write($body){
        $this->updateStatus();
        if ($this->writable) {
            socket_write($this->_socket, $body, strlen($body));
        }
    }

    protected function _read(){
        print "Start read socket\n";
        $empty
        while($buf = socket_read($this->_socket, 1024, PHP_NORMAL_READ)) {
            print $buf."|\n";
            if ($buf === false) {
                throw new Exception\Listener(socket_strerror(socket_last_error($this->_socket)));
            } else if ($buf) {
                switch ($buf) {
                    case 'quit':
                        print "Close connection\n";
                        socket_close($this->_socket);
                        break;
                    case 'shutdown':
                        throw new Exception\Listener('Shutdown detected');
                    default:
                        $this->body.=$buf;
                }
            }
        }
        print "Finish read socket\n";
    }

    public function close() {
        if (!$this->closed) {
            $this->closed = true;
            if ($this->_socket) {
                socket_close($this->_socket);
                $this->_socket = null;
            }
        }
    }

    public function __destruct(){
        $this->close();
    }
}