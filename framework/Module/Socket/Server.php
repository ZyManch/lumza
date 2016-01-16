<?php
/**
 * Created by PhpStorm.
 * User: Елена
 * Date: 01.01.2016
 * Time: 16:23
 */
namespace LZ\Module\Socket;

use \LZ\Exception;

class Server {

    protected $_socket;

    public function __construct($dns, $maxClients = 1024) {
        $sock = \socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if (!$sock) {
            throw new Exception\Socket('Error start server');
        }
        $dns = $this->_getParsedDns($dns);
        if (!socket_bind($sock, $dns['domain'], $dns['port'])) {
            throw new Exception\Socket('Error bind socket to '.$dns['domain'].':'.$dns['port']);
        }
        if (!socket_listen($sock, $maxClients)) {
            throw new Exception\Socket('Error listed port');
        }
        $this->_socket = $sock;
    }


    protected function _getParsedDns($dns) {
        $dns = explode(':',$dns,2);
        if (!isset($dns[1])) {
            $dns[1] = 80;
        }
        return array(
            'domain' => $dns[0],
            'port' => $dns[1]
        );
    }

    /**
     * @return Http|null
     * @throws Exception\Socket
     */
    public function checkNewSocket() {
        $null = null;
        $read = array($this->_socket);
        if (!socket_select($read, $null, $null, null)) {
            return null;
        }
        $socketNew = socket_accept($this->_socket);
        if ($socketNew === false) {
            throw new Exception\Socket(socket_strerror(socket_last_error($this->_socket)));
        }
        return new Http($socketNew);
    }

    public function __destruct() {
        socket_close($this->_socket);
    }
}