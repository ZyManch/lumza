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
        $this->_socket = $socket;
        socket_set_nonblock($this->_socket);
        $this->updateStatus();
    }

    public function updateStatus() {
        if ($this->closed) {
            return;
        }
        $null = array();
        $write = array($this->_socket);
        print "Checking selects\n";
        if (!socket_select($null, $write, $null, null)) {
            throw new Exception\Socket(socket_strerror(socket_last_error($this->_socket)));
        }
        print "Result ".($write?1:0)."\n";
        $this->writable = sizeof($write)>0;
        $this->_read();
    }

    public function write($body){
        $this->updateStatus();
        if ($this->writable) {
            socket_write($this->_socket, $body, strlen($body));
        }
    }

    protected function _read(){
        $this->body = null;
        do {
            print "Read block:";
            $buf = socket_read($this->_socket, 10, PHP_BINARY_READ);
            switch ($buf) {
                case 'quit':
                    $this->close();
                    break;
                case 'shutdown':
                    throw new Exception\Listener('Shutdown detected');
                default:
                    $this->body.=$buf;
            }
            print strlen($buf).":".$buf."\n";
            if (substr($buf, -4)=="\r\n\r\n") {
                break;
            }
        } while (trim($buf));
    }

    public function close() {
        if (!$this->closed) {
            $this->closed = true;
            $this->writable = false;
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