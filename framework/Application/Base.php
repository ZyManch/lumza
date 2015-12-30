<?php
/**
 * Created by PhpStorm.
 * User: ZyManch
 * Date: 30.12.2015
 * Time: 15:06
 */
namespace LZ\Application;

class Base  {

    protected $_request;

    public function __construct(\LZ\Request\Base $request) {
        $this->_request = $request;
    }


    public function run() {

    }

}