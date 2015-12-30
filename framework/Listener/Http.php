<?php
/**
 * Created by PhpStorm.
 * User: ZyManch
 * Date: 23.12.2015
 * Time: 13:13
 */
namespace LZ\Listener;

use LZ\Exception;
use LZ\Request;

class Http extends Base {

    protected $_socket;

    protected $_read;

    protected $_clients = array();
    protected $_request = array();

    public function init() {
        $sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($sock < 0) {
            throw new Exception\Listener('Error start server');
        }
        $dns = $this->_getParsedDns();
        if (!socket_bind($sock, $dns['domain'], $dns['port'])) {
            throw new Exception\Listener('Error bind socket to '.$dns['domain'].':'.$dns['port']);
        }
        if (!socket_listen($sock, $this->_maxClients)) {
            throw new Exception\Listener('Error listed port');
        }
        $this->_socket = $sock;
    }

    protected function _getParsedDns() {
        $dns = explode(':',$this->_dns,2);
        if (!isset($dns[1])) {
            $dns[1] = 80;
        }
        return array(
            'domain' => $dns[0],
            'port' => $dns[1]
        );
    }

    public function getNewRequest() {
        $this->_read = array_merge(array($this->_socket),$this->_clients);
        $null = null;
        if (socket_select($this->_read, $null, $null, null) < 1) {
            return null;
        }
        if (!in_array($this->_socket, $this->_read)) {
            return null;
        }
        $socketNew = socket_accept($this->_socket);
        if ($socketNew === false) {
            throw new Exception\Listener(socket_strerror(socket_last_error($this->_socket)));
        }
        $requestBody = socket_read($socketNew, 1024);
        $this->_clients[] = $socketNew;


        $in = "HTTP/1.1 200 OK\r\n";
        $in .= "Host: www.example.com\r\n";
        $in .= "Connection: Close\r\n\r\n";

        socket_write($socketNew, $in, strlen($in));
        socket_write($socketNew, $requestBody, strlen($requestBody));
        socket_close($socketNew);


        $request = new Request\Http($requestBody);
        $request->setSocket($socketNew);
        return $request;
    }

    public function checkClosedRequests() {
        foreach ($this->_clients as $key => $client) {
            if (in_array($client, $this->_read)) {
                $buf = @socket_read($client, 1024, PHP_NORMAL_READ);
                if ($buf === false) {
                    throw new Exception\Listener(socket_strerror(socket_last_error($client)));
                }
                $buf = trim($buf);
                if ($buf) {
                    switch ($buf) {
                        case 'quit':
                            unset($this->_clients[$key]);
                            socket_close($client);
                            break;
                        case 'shutdown':
                            throw new Exception\Listener('Shutdown detected');
                    }
                }
            }
        }
        return true;
    }

    public function __destruct() {
        foreach($this->_clients as $client) {
            socket_close($client);
        }
        socket_close($this->_socket);
    }


}