<?php
/**
 * Created by PhpStorm.
 * User: ZyManch
 * Date: 30.12.2015
 * Time: 15:06
 */
namespace LZ\App;

use LZ\Request;

class Base  {

    /** @var Request\Base\Request  */
    protected $_request;

    public function __construct(Request\Base\Request $request) {
        $this->_request = $request;
    }


    public function run() {
        $executor = function() {

        };
        sleep(1);
        $this->_request->sendBody(var_export($this->_request->server,1));
        $this->_request->finish();
    }

}