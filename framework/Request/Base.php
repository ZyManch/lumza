<?php
/**
 * Created by PhpStorm.
 * User: ZyManch
 * Date: 23.12.2015
 * Time: 15:50
 */
namespace LZ\Request;

abstract class Base {

    public $path;
    public $params = array();

    protected $_body;

    public function __construct($requestBody) {
        $this->_body = $this->_parseBody($requestBody);
    }

    abstract protected function _parseBody($requestBody);

    abstract public function finish();

}