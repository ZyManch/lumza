<?php
/**
 * Created by PhpStorm.
 * User: ZyManch
 * Date: 23.12.2015
 * Time: 15:50
 */
namespace LZ\Request;

use LZ\Module\Socket;
class Http extends Base {

    /** @var  Socket\Http */
    protected $_socket;

    public $method;
    public $server = array();

    protected $_responseCode = 200;



    public function setSocket($socket) {
        $this->_socket = $socket;
    }

    protected function _parseBody($requestBody) {
        $parts = explode("\n",$requestBody);
        $mainHeader = explode(' ',array_shift($parts));
        $this->method = strtoupper($mainHeader[0]);
        $this->path = ltrim(parse_url($mainHeader[1],PHP_URL_PATH),'/');
        $args = parse_url($mainHeader[1],PHP_URL_QUERY);
        parse_str($args, $this->get);
        foreach ($parts as $part) {
            $part = trim($part);
            $part = explode(':',$part,2);
            $this->server[strtolower(trim($part[0]))] = trim($part[1]);
        }
    }

    protected function _sendHeaders() {
        if (!$this->_canSendHeader || $this->finished) {
            return false;
        }
        $this->_canSendHeader = false;
        $code = $this->_responseCode;
        $codeDescription = (isset($this->_codes[$code]) ? $this->_codes[$code] : '');
        $headers = array(
            'HTTP/1.1 '.$code.' '.$codeDescription,
        );
        if (isset($this->server['host'])) {
            $headers[] = 'Host: '.$this->server['host'];
        }
        foreach ($this->_headers as $header) {
            $headers[] = $header[0].': '.$header[1];
        }
        $headers[] = 'Connection: Close';
        $headers = implode("\r\n",$headers)."\r\n\r\n";
        $this->_checkSocket();
        $this->_socket->write($headers);
        return true;
    }

    public function sendBody($body) {
        if (!$this->finished) {
            $this->_sendHeaders();
            $this->_checkSocket();
            $this->_socket->write($body);
        }
    }

    protected function _checkSocket() {
        $this->_socket->updateStatus();
        if ($this->_socket->closed) {
            $this->finished = true;
        }
    }


    public function finish() {
        if (!$this->finished) {
            $this->_sendHeaders();
            $this->finished = true;
        }
    }

}