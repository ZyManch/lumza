<?php
/**
 * Created by PhpStorm.
 * User: ZyManch
 * Date: 23.12.2015
 * Time: 13:13
 */
namespace LZ\Request\Http;

use LZ\Exception;
use LZ\Request\Base;
use LZ\Module\Socket;

class Provider extends Base\Provider {

    /** @var  Socket\Server */
    protected $_socket;

    protected $_read;

    protected $_clients = array();

    /** @var Request[]  */
    protected $_requests = array();

    public function init() {
        $this->_socket = new Socket\Server($this->_dns, $this->_maxClients);
    }

    public function getSocket() {
        return $this->_socket;
    }

    public function getNewRequest() {
        $socket = $this->_socket->checkNewSocket();
        if (!$socket) {
            return null;
        }
        print "New request obtained\n";
        $request = new Request($socket->body);
        $request->setSocket($socket);
        return $request;
    }

    protected function _checkFinishedRequests(){
        foreach ($this->_requests as $key => $request) {
            if ($request->finished) {
                printf("Found closed request %d: %f\n",$key, microtime(true));
                socket_close($this->_clients[$key]);
                unset($this->_clients[$key]);
                unset($this->_requests[$key]);
            }
        }
        $this->_read = array_merge(array($this->_socket),$this->_clients);
    }

    protected function _checkNewRequest() {
        $null = null;
        if (!socket_select($this->_read, $null, $null, null)) {
            return null;
        }
        if (!in_array($this->_socket, $this->_read)) {
            return null;
        }
        printf("Accept new connection: %f\n",microtime(true));
        $socketNew = socket_accept($this->_socket);
        if ($socketNew === false) {
            throw new Exception\Listener(socket_strerror(socket_last_error($this->_socket)));
        }
        $requestBody = socket_read($socketNew, 1024);
        $this->_clients[] = $socketNew;
        end($this->_clients);
        $index = key($this->_clients);
        printf("Create new request %d: %f\n",$index, microtime(true));
        $request = new Request($requestBody);
        $request->setSocket($socketNew);
        $this->_requests[$index] = $request;
        return $request;
    }

    protected function _checkClosedRequests() {
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
                            print "Close connection\n";
                            unset($this->_clients[$key]);
                            $this->_requests[$key]->finished = true;
                            unset($this->_requests[$key]);
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


}