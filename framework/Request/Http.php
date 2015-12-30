<?php
/**
 * Created by PhpStorm.
 * User: ZyManch
 * Date: 23.12.2015
 * Time: 15:50
 */
namespace SF\Request;

class Http extends Base {

    protected $_socket;

    public $method;
    public $server = array();

    protected $_responseCode = 200;

    protected $_canSendHeader = true;
    protected $_headers = array();

    protected $_codes = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        105 => 'Name Not Resolved',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        226 => 'IM Used',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        434 => 'Requested host unavailable',
        449 => 'Retry With',
        451 => 'Unavailable For Legal Reasons',
        456 => 'Unrecoverable Error',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        509 => 'Bandwidth Limit Exceeded',
        510 => 'Not Extended',
        511 => 'Network Authentication Required'
    );

    public function setSocket($socket) {
        $this->_socket = $socket;
    }

    protected function _parseBody($requestBody) {
        $parts = explode("\n",$requestBody);
        $mainHeader = explode(' ',array_shift($parts));
        $this->method = strtoupper($mainHeader[0]);
        $this->path = ltrim(parse_url($mainHeader[1],PHP_URL_PATH),'/');
        $args = parse_url($mainHeader[1],PHP_URL_QUERY);
        parse_str($args, $this->params);
        foreach ($parts as $part) {
            $part = trim($part);
            $part = explode(':',$part,2);
            $this->server[strtolower(trim($part[0]))] = trim($part[1]);
        }
    }

    protected function _sendHeaders() {
        if (!$this->_canSendHeader) {
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
        socket_write($this->_socket, $headers, strlen($headers));
        return true;
    }

    public function returnHeader($key, $value) {
        $this->_headers[] = array($key, $value);
    }

    public function returnBody($body) {
        $this->_sendHeaders();
        socket_write($this->_socket, $body, strlen($body));
    }


    public function finish() {
        $this->_sendHeaders();
    }

}