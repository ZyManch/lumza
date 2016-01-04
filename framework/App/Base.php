<?php
/**
 * Created by PhpStorm.
 * User: ZyManch
 * Date: 30.12.2015
 * Time: 15:06
 */
namespace LZ\App;

class Base  {

    /** @var \LZ\Request\Base  */
    protected $_request;

    public function __construct(\LZ\Request\Base $request) {
        $this->_request = $request;
    }


    public function run() {
        sleep(1);
        $this->_request->sendBody(var_export($this->_request->server,1));
        $this->_request->finish();
    }

}